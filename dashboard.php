<?php include 'header.php'; ?>
<?php include 'config.php'; ?>

<?php

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<div class="container">
    <h1>Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
    
    <h2>Your Posts</h2>
    
    <?php
    $stmt = $conn->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $posts = $stmt->get_result();
    
    if ($posts->num_rows > 0) {
        while($post = $posts->fetch_assoc()) {
            echo '<div class="post">';
            echo '<h3><a href="post.php?id=' . $post['id'] . '">' . htmlspecialchars($post['title']) . '</a></h3>';
            echo '<p class="meta">Posted on ' . date('F j, Y', strtotime($post['created_at'])) . '</p>';
            echo '<p>' . substr(htmlspecialchars($post['content']), 0, 200) . '...</p>';
            echo '<div class="post-actions">';
            echo '<a href="edit_post.php?id=' . $post['id'] . '" class="btn">Edit</a> ';
            echo '<a href="delete_post.php?id=' . $post['id'] . '" class="btn" onclick="return confirm(\'Are you sure you want to delete this post?\')">Delete</a>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p>You have no posts yet. <a href="create_post.php">Create your first post</a></p>';
    }
    $stmt->close();
    ?>
    
    <div class="new-post">
        <a href="create_post.php" class="btn">Create New Post</a>
    </div>
</div>

<?php include 'footer.php'; ?>