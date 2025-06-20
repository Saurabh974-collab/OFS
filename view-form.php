<?php
require_once 'config.php';

$formId = $_GET['id'] ?? null;
$form = $db->query("SELECT * FROM forms WHERE id = '$formId'")->fetch(PDO::FETCH_ASSOC);
$questions = $db->query("SELECT * FROM questions WHERE form_id = '$formId'")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission
    $answers = [];
    foreach ($questions as $q) {
        $answers['q'.$q['id']] = $_POST['q'.$q['id']] ?? '';
    }
    
    $stmt = $db->prepare("INSERT INTO responses (form_id, answers) VALUES (?, ?)");
    $stmt->execute([$formId, json_encode($answers)]);
    
    echo '<div class="alert alert-success">Thank you for your feedback!</div>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($form['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1><?= htmlspecialchars($form['title']) ?></h1>
        <p><?= htmlspecialchars($form['description']) ?></p>
        
        <form method="POST" action="view_form.php?id=<?= $formId ?>">
            <?php foreach ($questions as $q): ?>
                <div class="mb-4">
                    <label class="form-label"><?= htmlspecialchars($q['title']) ?></label>
                    
                    <?php if ($q['type'] === 'text'): ?>
                        <input type="text" name="q<?= $q['id'] ?>" class="form-control">
                    
                    <?php elseif ($q['type'] === 'textarea'): ?>
                        <textarea name="q<?= $q['id'] ?>" class="form-control"></textarea>
                    
                    <?php elseif ($q['type'] === 'radio'): ?>
                        <?php $options = json_decode($q['options'], true); ?>
                        <?php foreach ($options as $opt): ?>
                            <div class="form-check">
                                <input type="radio" name="q<?= $q['id'] ?>" value="<?= htmlspecialchars($opt) ?>" class="form-check-input">
                                <label class="form-check-label"><?= htmlspecialchars($opt) ?></label>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</body>
</html>