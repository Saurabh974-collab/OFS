<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get user info
$user = $pdo->prepare("SELECT username FROM users WHERE id = ?")->fetch([$_SESSION['user_id']]);

// Get all feedback
$feedback = $pdo->query("SELECT f.*, u.username 
                        FROM feedback f 
                        JOIN users u ON f.user_id = u.id
                        ORDER BY f.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h1>Welcome, <?= htmlspecialchars($user['username']) ?></h1>
    <a href="feedback.php">Submit Feedback</a>
    <h2>Recent Feedback</h2>
    <?php foreach ($feedback as $item): ?>
        <div>
            <h3><?= htmlspecialchars($item['username']) ?> (Rating: <?= $item['rating'] ?>/5)</h3>
            <p><?= nl2br(htmlspecialchars($item['message'])) ?></p>
            <small><?= $item['created_at'] ?></small>
        </div>
    <?php endforeach; ?>
</body>
</html>