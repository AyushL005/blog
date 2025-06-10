<?php include 'header.php'; ?>
<?php include 'config.php'; ?>

<?php

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $post_id = $_GET['id'];
    
    
    $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows != 1) {
     
        header("Location: index.php");
        exit();
    }
    
    $post = $result->fetch_assoc();
    $stmt->close();
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = $_POST['title'];
        $content = $_POST['content'];
        
       
        $errors = [];
        if (empty($title)) $errors[] = "Title is required";
        if (empty($content)) $errors[] = "Content is required";
        
        if (empty($errors)) {
            $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
            $stmt->bind_param("ssi", $title, $content, $post_id);
            
            if ($stmt->execute()) {
                header("Location: post.php?id=$post_id");
                exit();
            } else {
                echo '<p class="error">Error updating post: ' . $stmt->error . '</p>';
            }
            $stmt->close();
        } else {
            foreach ($errors as $error) {
                echo '<p class="error">' . $error . '</p>';
            }
        }
    }
} else {
    header("Location: index.php");
    exit();
}
?>

<div class="container">
    <h1>Edit Post</h1>
    
    <form method="POST" action="edit_post.php?id=<?php echo $post_id; ?>">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
        
        <label for="content">Content:</label>
        <textarea id="content" name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>
        
        <input type="submit" value="Update Post">
    </form>
</div>

<?php include 'footer.php'; ?>