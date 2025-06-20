<?php
require 'config.php';
require 'auth.php';
$auth->requireAuth();

// Form creation handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formId = uniqid();
    $db->beginTransaction();
    
    try {
        // Save form metadata
        $stmt = $db->prepare("INSERT INTO forms (id, user_id, title, description) VALUES (?, ?, ?, ?)");
        $stmt->execute([$formId, $_SESSION['user']['id'], $_POST['title'], $_POST['description']]);
        
        // Save questions
        foreach ($_POST['questions'] as $q) {
            $options = isset($q['options']) ? json_encode(explode("\n", $q['options'])) : null;
            $stmt = $db->prepare("INSERT INTO questions (form_id, title, type, options, is_required) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$formId, $q['title'], $q['type'], $options, isset($q['required'])]); 
        }
        
        $db->commit();
        $_SESSION['success'] = "Form created successfully!";
        header("Location: form-builder.php?created=$formId");
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        $error = "Error creating form: " . $e->getMessage();
    }
}

// Get user's forms
$forms = $db->prepare("SELECT * FROM forms WHERE user_id = ? ORDER BY created_at DESC");
$forms->execute([$_SESSION['user']['id']]);
$userForms = $forms->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form Builder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="main.css" rel="stylesheet">
    <style>
        .question-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: white;
        }
        .option-input {
            margin-bottom: 8px;
        }
        .share-link-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container my-5">
        <?php if (isset($_GET['created'])): ?>
            <div class="alert alert-success">
                Form created successfully! 
                <a href="responses.php?form_id=<?= $_GET['created'] ?>" class="alert-link">
                    View Responses
                </a>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Your Forms</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($userForms) > 0): ?>
                            <div class="list-group">
                                <?php foreach ($userForms as $form): ?>
                                    <a href="responses.php?form_id=<?= $form['id'] ?>" 
                                       class="list-group-item list-group-item-action">
                                        <?= htmlspecialchars($form['title']) ?>
                                        <span class="badge bg-primary float-end">
                                            <?= $db->query("SELECT COUNT(*) FROM responses WHERE form_id = '{$form['id']}'")->fetchColumn() ?>
                                        </span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No forms yet</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Create New Form</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="formBuilder">
                            <div class="mb-3">
                                <label class="form-label">Form Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="2"></textarea>
                            </div>
                            
                            <div id="questionsContainer">
                                <!-- Question template will be cloned here -->
                                <div class="question-card" data-question-id="1">
                                    <div class="mb-3">
                                        <label class="form-label">Question</label>
                                        <input type="text" name="questions[1][title]" class="form-control" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Question Type</label>
                                        <select name="questions[1][type]" class="form-select question-type" onchange="updateQuestionOptions(this)">
                                            <option value="text">Short Answer</option>
                                            <option value="textarea">Paragraph</option>
                                            <option value="radio">Multiple Choice</option>
                                            <option value="checkbox">Checkboxes</option>
                                            <option value="dropdown">Dropdown</option>
                                            <option value="star">Star Rating</option>
                                        </select>
                                    </div>
                                    
                                    <div class="options-container mb-3" id="optionsContainer1">
                                        <!-- Options will appear here for multiple choice questions -->
                                    </div>
                                    
                                    <div class="form-check mb-3">
                                        <input type="checkbox" name="questions[1][required]" class="form-check-input">
                                        <label class="form-check-label">Required</label>
                                    </div>
                                    
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeQuestion(this)">
                                        <i class="bi bi-trash"></i> Remove Question
                                    </button>
                                </div>
                            </div>
                            
                            <button type="button" class="btn btn-primary mt-3" onclick="addQuestion()">
                                <i class="bi bi-plus-circle"></i> Add Question
                            </button>
                            
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Create Form
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let questionCount = 1;
        
        function addQuestion() {
            questionCount++;
            const newQuestion = document.querySelector('.question-card').cloneNode(true);
            newQuestion.dataset.questionId = questionCount;
            newQuestion.innerHTML = newQuestion.innerHTML.replace(/questions\[1\]/g, `questions[${questionCount}]`);
            newQuestion.querySelector('.options-container').id = `optionsContainer${questionCount}`;
            document.getElementById('questionsContainer').appendChild(newQuestion);
        }
        
        function removeQuestion(button) {
            if (document.querySelectorAll('.question-card').length > 1) {
                button.closest('.question-card').remove();
            } else {
                alert("You must have at least one question");
            }
        }
        
        function updateQuestionOptions(select) {
            const questionId = select.closest('.question-card').dataset.questionId;
            const optionsContainer = document.getElementById(`optionsContainer${questionId}`);
            const questionType = select.value;
            
            if (['radio', 'checkbox', 'dropdown'].includes(questionType)) {
                optionsContainer.innerHTML = `
                    <label class="form-label">Options (one per line)</label>
                    <textarea name="questions[${questionId}][options]" class="form-control" rows="3"></textarea>
                    <small class="text-muted">Enter each option on a new line</small>
                `;
            } else {
                optionsContainer.innerHTML = '';
            }
        }
    </script>
</body>
</html>
