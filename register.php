<?php include 'header.php'; ?>
<?php include 'config.php'; ?>

<div class="container">
    <h1>Register</h1>
    
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
      
        $errors = [];
        if (empty($username)) $errors[] = "Username is required";
        if (empty($email)) $errors[] = "Email is required";
        if (empty($_POST['password'])) $errors[] = "Password is required";
        
        
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $errors[] = "Username or email already exists";
        }
        $stmt->close();
        
        if (empty($errors)) {
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password);
            
            if ($stmt->execute()) {
                echo '<p class="success">Registration successful! <a href="login.php">Login here</a></p>';
            } else {
                echo '<p class="error">Error: ' . $stmt->error . '</p>';
            }
            $stmt->close();
        } else {
            foreach ($errors as $error) {
                echo '<p class="error">' . $error . '</p>';
            }
        }
    }
    ?>
    
    <form method="POST" action="register.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        
        <input type="submit" value="Register">
    </form>
    
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>

<?php include 'footer.php'; ?>