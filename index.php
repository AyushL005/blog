<?php include 'header.php'; ?>
<?php include 'config.php'; ?>

<div class="container">
    <h1>Welcome to Our Blog</h1>
    
    <?php
    $sql = "SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY created_at DESC";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo '<div class="post">';
            echo '<h2><a href="post.php?id=' . $row['id'] . '">' . htmlspecialchars($row['title']) . '</a></h2>';
            echo '<p class="meta">Posted by ' . htmlspecialchars($row['username']) . ' on ' . date('F j, Y', strtotime($row['created_at'])) . '</p>';
            echo '<p>' . substr(htmlspecialchars($row['content']), 0, 200) . '...</p>';
            echo '<a href="post.php?id=' . $row['id'] . '" class="read-more">Read More</a>';
            echo '</div>';
        }
    } else {
        echo '<p>No posts found.</p>';
    }
    ?>
    
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="new-post">
            <a href="create_post.php" class="btn">Create New Post</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>