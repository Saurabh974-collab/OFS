<?php if (isset($_SESSION['user'])): ?>
<nav class="navbar navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="index.php">FeedbackHub</a>
        <div>
            <span class="me-3">Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?></span>
            <a href="responses.php" class="btn btn-outline-primary me-2">Responses</a>
            <a href="logout.php" class="btn btn-outline-danger">Logout</a>
        </div>
    </div>
</nav>
<?php endif; ?>