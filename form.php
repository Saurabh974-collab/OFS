<?php
require 'config.php';

$formId = $_GET['form_id'] ?? null;
if (!$formId) {
    header("Location: index.php");
    exit;
}

// Get form details
$form = $db->prepare("SELECT * FROM forms WHERE id = ?");
$form->execute([$formId]);
$form = $form->fetch();

if (!$form) {
    header("Location: index.php");
    exit;
}

// Get questions
$questions = $db->prepare("SELECT * FROM questions WHERE form_id = ?");
$questions->execute([$formId]);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answers = [];
    
    foreach ($questions as $q) {
        $key = 'q'.$q['id'];
        
        if ($q['type'] === 'checkbox') {
            $answers[$key] = $_POST[$key] ?? [];
        } else {
            $answers[$key] = $_POST[$key] ?? null;
        }
    }
    
    try {
        $stmt = $db->prepare("INSERT INTO responses (form_id, user_id, answers) VALUES (?, ?, ?)");
        $stmt->execute([$formId, $_SESSION['user']['id'] ?? null, json_encode($answers)]);
        
        $success = "Thank you for your feedback!";
    } catch (Exception $e) {
        $error = "Error submitting form: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($form['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 800px;
            margin: 40px auto;
        }
        .star-rating {
            display: flex;
            gap: 5px;
            margin: 10px 0;
        }
        .star-rating input { display: none; }
        .star-rating label { 
            font-size: 24px; 
            color: #ddd;
            cursor: pointer;
        }
        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #ffc107;
        }
    </style>
</head>
<body>
    <div class="container form-container">
        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <?= $success ?>
                <a href="form.php?form_id=<?= $formId ?>" class="alert-link">Submit another response</a>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-header">
                    <h2><?= htmlspecialchars($form['title']) ?></h2>
                    <p class="mb-0"><?= htmlspecialchars($form['description']) ?></p>
                </div>
                
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <?php foreach ($questions as $q): ?>
                            <div class="mb-4">
                                <label class="form-label">
                                    <?= htmlspecialchars($q['title']) ?>
                                    <?php if ($q['is_required']): ?>
                                        <span class="text-danger">*</span>
                                    <?php endif; ?>
                                </label>
                                
                                <?php if ($q['type'] === 'text'): ?>
                                    <input type="text" name="q<?= $q['id'] ?>" 
                                           class="form-control" <?= $q['is_required'] ? 'required' : '' ?>>
                                
                                <?php elseif ($q['type'] === 'textarea'): ?>
                                    <textarea name="q<?= $q['id'] ?>" class="form-control" 
                                              rows="3" <?= $q['is_required'] ? 'required' : '' ?>></textarea>
                                
                                <?php elseif ($q['type'] === 'radio'): ?>
                                    <?php foreach (json_decode($q['options']) as $opt): ?>
                                        <div class="form-check">
                                            <input type="radio" name="q<?= $q['id'] ?>" 
                                                   value="<?= htmlspecialchars($opt) ?>" 
                                                   class="form-check-input" <?= $q['is_required'] ? 'required' : '' ?>>
                                            <label class="form-check-label"><?= htmlspecialchars($opt) ?></label>
                                        </div>
                                    <?php endforeach; ?>
                                
                                <?php elseif ($q['type'] === 'checkbox'): ?>
                                    <?php foreach (json_decode($q['options']) as $opt): ?>
                                        <div class="form-check">
                                            <input type="checkbox" name="q<?= $q['id'] ?>[]" 
                                                   value="<?= htmlspecialchars($opt) ?>" class="form-check-input">
                                            <label class="form-check-label"><?= htmlspecialchars($opt) ?></label>
                                        </div>
                                    <?php endforeach; ?>
                                
                                <?php elseif ($q['type'] === 'dropdown'): ?>
                                    <select name="q<?= $q['id'] ?>" class="form-select" 
                                            <?= $q['is_required'] ? 'required' : '' ?>>
                                        <option value="">Select an option</option>
                                        <?php foreach (json_decode($q['options']) as $opt): ?>
                                            <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                
                                <?php elseif ($q['type'] === 'star'): ?>
                                    <div class="star-rating">
                                        <?php for ($i=5; $i>=1; $i--): ?>
                                            <input type="radio" name="q<?= $q['id'] ?>" 
                                                   value="<?= $i ?>" id="star<?= $q['id'] ?>-<?= $i ?>" 
                                                   <?= $q['is_required'] ? 'required' : '' ?>>
                                            <label for="star<?= $q['id'] ?>-<?= $i ?>">★</label>
                                        <?php endfor; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                        
                        <button type="submit" class="btn btn-primary btn-lg">Submit</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>