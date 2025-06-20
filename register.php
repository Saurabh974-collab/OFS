<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $password]);
        echo "User registered successfully!";
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>  
<form method="POST">
    <input type="email" name="username" placeholder="Username" pattern=".{3,}" title="Minimum 3 characters" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Register</button>
</form>