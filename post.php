<?php include 'header.php'; ?>
<?php include 'config.php'; ?>

<div class="container">
    <?php
    if (isset($_GET['id'])) {
        $post_id = $_GET['id'];
        
        $stmt = $conn->prepare("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $post = $result->fetch_assoc();
            
            echo '<div class="post">';
            echo '<h2>' . htmlspecialchars($post['title']) . '</h2>';
            echo '<p class="meta">Posted by ' . htmlspecialchars($post['username']) . ' on ' . date('F j, Y', strtotime($post['created_at'])) . '</p>';
            echo '<p>' . nl2br(htmlspecialchars($post['content'])) . '</p>';
            
            
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']) {
                echo '<div class="post-actions">';
                echo '<a href="edit_post.php?id=' . $post['id'] . '" class="btn">Edit</a> ';
                echo '<a href="delete_post.php?id=' . $post['id'] . '" class="btn" onclick="return confirm(\'Are you sure you want to delete this post?\')">Delete</a>';
                echo '</div>';
            }
            
            echo '</div>';

            echo '<div class="comments">';
            echo '<h3>Comments</h3>';
            
            
            $stmt = $conn->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE post_id = ? ORDER BY created_at DESC");
            $stmt->bind_param("i", $post_id);
            $stmt->execute();
            $comments = $stmt->get_result();
            
            if ($comments->num_rows > 0) {
                while($comment = $comments->fetch_assoc()) {
                    echo '<div class="comment">';
                    echo '<p class="meta">' . htmlspecialchars($comment['username']) . ' on ' . date('F j, Y', strtotime($comment['created_at'])) . '</p>';
                    echo '<p>' . nl2br(htmlspecialchars($comment['content'])) . '</p>';
                    echo '</div>';
                }
            } else {
                echo '<p>No comments yet.</p>';
            }
            
        
            if (isset($_SESSION['user_id'])) {
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $content = $_POST['content'];
                    
                    if (!empty($content)) {
                        $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
                        $stmt->bind_param("iis", $post_id, $_SESSION['user_id'], $content);
                        
                        if ($stmt->execute()) {
                            echo '<p class="success">Comment added successfully!</p>';
                        
                            header("Location: post.php?id=$post_id");
                            exit();
                        } else {
                            echo '<p class="error">Error adding comment: ' . $stmt->error . '</p>';
                        }
                        $stmt->close();
                    } else {
                        echo '<p class="error">Comment cannot be empty</p>';
                    }
                }
                
                echo '<form method="POST" action="post.php?id=' . $post_id . '">';
                echo '<label for="content">Add Comment:</label>';
                echo '<textarea id="content" name="content" required></textarea>';
                echo '<input type="submit" value="Submit Comment">';
                echo '</form>';
            } else {
                echo '<p><a href="login.php">Login</a> to post a comment.</p>';
            }
            
            echo '</div>'; 
        } else {
            echo '<p>Post not found.</p>';
        }
        $stmt->close();
    } else {
        echo '<p>No post specified.</p>';
    }
    ?>
</div>

<?php include 'footer.php'; ?>