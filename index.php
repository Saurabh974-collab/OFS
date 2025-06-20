<?php
// index.php - Exact replica of your screenshot
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Builder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 700px;
            margin: 20px auto;
            padding: 0 20px;
            color: #333;
        }
        .form-header {
            margin-bottom: 30px;
        }
        .form-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .question-container {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .question-row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .question-label {
            width: 80px;
            font-weight: bold;
        }
        .question-type {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: white;
        }
        .option-row {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .option-input {
            flex-grow: 1;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .add-option {
            color: #1a73e8;
            cursor: pointer;
            margin: 10px 0;
            display: inline-block;
        }
        .question-footer {
            display: flex;
            align-items: center;
            margin-top: 15px;
        }
        .add-question-btn {
            background: #f1f3f4;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            margin: 20px 0;
        }
        .share-section {
            border-top: 1px solid #e0e0e0;
            padding-top: 20px;
            margin-top: 30px;
        }
        .share-link {
            font-family: monospace;
            color: #1a73e8;
            margin: 10px 0;
            word-break: break-all;
        }
        .share-options {
            display: flex;
            gap: 20px;
            margin: 15px 0;
        }
        .create-form-btn {
            background: #1a73e8;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="form-header">
        <div class="form-title" contenteditable="true">Form Title</div>
        <div contenteditable="true">Form Description</div>
    </div>

    <div class="question-container">
        <div class="question-row">
            <div class="question-label">Question</div>
            <select class="question-type">
                <option selected>Multiple choice</option>
                <option>Checkboxes</option>
                <option>Dropdown</option>
                <option>Star rating</option>
                <option>Short answer</option>
                <option>Paragraph</option>
                <option>File upload</option>
                <option>Date</option>
                <option>Time</option>
                <option>Date and time</option>
            </select>
        </div>

        <div class="option-row">
            <input type="text" class="option-input" placeholder="Option">
        </div>
        <div class="option-row">
            <input type="text" class="option-input" placeholder="Option">
        </div>

        <div class="add-option">+ Add option</div>

        <div class="question-footer">
            <input type="checkbox" id="required">
            <label for="required">Required</label>
            <span style="margin: 0 10px">|</span>
            <span style="color: #d93025; cursor: pointer">Remove</span>
        </div>
    </div>

    <button class="add-question-btn">Add Question</button>

    <div class="share-section">
        <h3>Shareable Link</h3>
        <div class="share-link">http://localhost/feedback_system/</div>
        
        <div class="share-options">
            <label><input type="checkbox"> Email</label>
            <label><input type="checkbox"> WhatsApp</label>
            <label><input type="checkbox"> QR Code</label>
            <label><input type="checkbox"> Share</label>
        </div>

        <button class="create-form-btn">Create Form</button>
    </div>

    <script>
        // Add option functionality
        document.querySelectorAll('.add-option').forEach(btn => {
            btn.addEventListener('click', function() {
                const newOption = document.createElement('div');
                newOption.className = 'option-row';
                newOption.innerHTML = `<input type="text" class="option-input" placeholder="Option">`;
                this.parentNode.insertBefore(newOption, this);
            });
        });

        // Add question functionality
        document.querySelector('.add-question-btn').addEventListener('click', function() {
            const newQuestion = document.querySelector('.question-container').cloneNode(true);
            document.querySelector('.add-question-btn').before(newQuestion);
        });

        // Remove question functionality
        document.addEventListener('click', function(e) {
            if (e.target.textContent === 'Remove') {
                e.target.closest('.question-container').remove();
            }
        });
    </script>
</body>
</html>