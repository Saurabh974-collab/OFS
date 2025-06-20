<?php
require 'config.php';
require 'auth.php';
$auth->requireAuth();

$formId = $_GET['form_id'] ?? null;
if (!$formId) {
    header("Location: form-builder.php");
    exit;
}

// Get form details
$form = $db->prepare("SELECT * FROM forms WHERE id = ?");
$form->execute([$formId]);
$form = $form->fetch();

if (!$form || $form['user_id'] != $_SESSION['user']['id']) {
    header("Location: form-builder.php");
    exit;
}

// Get questions and responses
$questions = $db->prepare("SELECT * FROM questions WHERE form_id = ?");
$questions->execute([$formId]);

$responses = $db->prepare("SELECT * FROM responses WHERE form_id = ? ORDER BY created_at DESC");
$responses->execute([$formId]);

// Calculate stats
$responseCount = $db->query("SELECT COUNT(*) FROM responses WHERE form_id = '$formId'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Responses - <?= htmlspecialchars($form['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .response-card {
            border-left: 4px solid #0d6efd;
            margin-bottom: 20px;
        }
        .analytics-card canvas {
            max-height: 300px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><?= htmlspecialchars($form['title']) ?></h1>
                <p class="lead"><?= htmlspecialchars($form['description']) ?></p>
            </div>
            <a href="form-builder.php" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i> Back to Forms
            </a>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <h3><?= $responseCount ?></h3>
                        <p class="text-muted">Total Responses</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5>Questions</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <?php foreach ($questions as $index => $question): ?>
                                <a href="#question-<?= $question['id'] ?>" 
                                   class="list-group-item list-group-item-action">
                                    <?= ($index+1) ?>. <?= htmlspecialchars($question['title']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <?php foreach ($questions as $question): ?>
                    <?php if (in_array($question['type'], ['radio', 'checkbox', 'dropdown', 'star'])): ?>
                        <div class="card mb-4 analytics-card" id="question-<?= $question['id'] ?>">
                            <div class="card-header">
                                <h5><?= htmlspecialchars($question['title']) ?></h5>
                            </div>
                            <div class="card-body">
                                <canvas id="chart-<?= $question['id'] ?>"></canvas>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h5>All Responses</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($responses as $response): ?>
                            <div class="card mb-3 response-card">
                                <div class="card-body">
                                    <div class="text-muted small mb-2">
                                        <?= date('M j, Y g:i a', strtotime($response['created_at'])) ?>
                                    </div>
                                    
                                    <?php 
                                    $answers = json_decode($response['answers'], true);
                                    foreach ($questions as $q): 
                                    ?>
                                        <div class="mb-3">
                                            <strong><?= htmlspecialchars($q['title']) ?></strong>
                                            <div>
                                                <?php if (isset($answers['q'.$q['id']])): ?>
                                                    <?php if (is_array($answers['q'.$q['id']])): ?>
                                                        <?= implode(', ', array_map('htmlspecialchars', $answers['q'.$q['id']])) ?>
                                                    <?php else: ?>
                                                        <?= htmlspecialchars($answers['q'.$q['id']]) ?>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <em>No response</em>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Generate charts for each question
        <?php foreach ($questions as $q): ?>
            <?php if (in_array($q['type'], ['radio', 'checkbox', 'dropdown', 'star'])): ?>
                <?php
                $options = $q['options'] ? json_decode($q['options']) : [];
                $counts = [];
                
                foreach ($options as $opt) {
                    $count = $db->query("
                        SELECT COUNT(*) 
                        FROM responses 
                        WHERE form_id = '$formId' 
                        AND JSON_CONTAINS(answers->'$.q{$q['id']}', '\"$opt\"')
                    ")->fetchColumn();
                    $counts[] = $count;
                }
                ?>
                
                new Chart(
                    document.getElementById('chart-<?= $q['id'] ?>'),
                    {
                        type: 'bar',
                        data: {
                            labels: <?= json_encode($options) ?>,
                            datasets: [{
                                label: 'Responses',
                                data: <?= json_encode($counts) ?>,
                                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: { beginAtZero: true }
                            }
                        }
                    }
                );
            <?php endif; ?>
        <?php endforeach; ?>
    </script>
</body>
</html>