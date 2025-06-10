<?php include 'header.php'; ?>
<?php include 'config.php'; ?>

<?php

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];
    

    $errors = [];
    if (empty($title)) $errors[] = "Title is required";
    if (empty($content)) $errors[] = "Content is required";
    
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $title, $content);
        
        if ($stmt->execute()) {
            $post_id = $stmt->insert_id;
            header("Location: post.php?id=$post_id");
            exit();
        } else {
            echo '<p class="error">Error creating post: ' . $stmt->error . '</p>';
        }
        $stmt->close();
    } else {
        foreach ($errors as $error) {
            echo '<p class="error">' . $error . '</p>';
        }
    }
}
?>

<div class="container">
    <h1>Create New Post</h1>
    
    <form method="POST" action="create_post.php">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>
        
        <label for="content">Content:</label>
        <textarea id="content" name="content" required></textarea>
        
        <input type="submit" value="Publish Post">
    </form>
</div>

<?php include 'footer.php'; ?>