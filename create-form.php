<?php
require 'config.php';
require 'auth.php';
$auth->requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Save form metadata
    $stmt = $db->prepare("INSERT INTO forms (id, user_id, title, description) VALUES (?, ?, ?, ?)");
    $formId = uniqid();
    $stmt->execute([$formId, $_SESSION['user']['id'], $_POST['title'], $_POST['description']]);
    
    // 2. Save questions
    foreach ($_POST['questions'] as $question) {
        $options = json_encode($question['options'] ?? []);
        $stmt = $db->prepare("INSERT INTO questions (form_id, title, type, options) VALUES (?, ?, ?, ?)");
        $stmt->execute([$formId, $question['title'], $question['type'], $options]);
    }
    
    header("Location: index.php?created=1");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create New Form</title>
    <link href="main.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container my-5">
        <form method="POST">
            <!-- Form Title -->
            <div class="mb-3">
                <label class="form-label">Form Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            
            <!-- Description -->
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control"></textarea>
            </div>
            
            <!-- Questions Container -->
            <div id="questions-container">
                <!-- Question 1 (Default) -->
                <div class="question-card mb-3 p-3 border">
                    <div class="mb-3">
                        <label class="form-label">Question 1</label>
                        <input type="text" name="questions[0][title]" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Question Type</label>
                        <select name="questions[0][type]" class="form-select">
                            <option value="text">Short Answer</option>
                            <option value="textarea">Paragraph</option>
                            <option value="radio">Multiple Choice</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Create Form</button>
        </form>
    </div>
</body>
</html>