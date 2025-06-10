<?php include 'header.php'; ?>
<?php include 'config.php'; ?>

<div class="container">
    <h1>Login</h1>
    
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        

        $errors = [];
        if (empty($username)) $errors[] = "Username is required";
        if (empty($password)) $errors[] = "Password is required";
        
        if (empty($errors)) {
            $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    header("Location: index.php");
                    exit();
                } else {
                    $errors[] = "Invalid password";
                }
            } else {
                $errors[] = "User not found";
            }
            $stmt->close();
        }
        
        foreach ($errors as $error) {
            echo '<p class="error">' . $error . '</p>';
        }
    }
    ?>
    
    <form method="POST" action="login.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        
        <input type="submit" value="Login">
    </form>
    
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>

<?php include 'footer.php'; ?>