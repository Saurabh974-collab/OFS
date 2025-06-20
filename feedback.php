<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = htmlspecialchars(trim($_POST['message']));
    $rating = (int)$_POST['rating'];
    
    $stmt = $pdo->prepare("INSERT INTO feedback (user_id, message, rating) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $message, $rating]);
    $success = "Feedback submitted!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Submit Feedback</title>
</head>
<body>
    <h1>Submit Feedback</h1>
    <?php if (isset($success)) echo "<p>$success</p>"; ?>
    <form method="POST">
        <textarea name="message" placeholder="Your feedback" required></textarea>
        <select name="rating" required>
            <option value="">Select rating</option>
            <option value="1">1 - Poor</option>
            <option value="2">2 - Fair</option>
            <option value="3">3 - Good</option>
            <option value="4">4 - Very Good</option>
            <option value="5">5 - Excellent</option>
        </select>
        <button type="submit">Submit</button>
    </form>
</body>
</html>