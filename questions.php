<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Manage Questions</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* (CSS code as previously provided) */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7ff, #ffffff);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .header {
            background: linear-gradient(135deg, #4a6cf7, #667eea);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .header h1 {
            font-size: 28px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .logout-btn {
            background: white;
            color: #4a6cf7;
            border: none;
            padding: 12px 25px;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74,108,247,0.3);
        }
        
        .dashboard {
            display: flex;
            flex: 1;
        }
        
        .sidebar {
            width: 250px;
            background: white;
            border-right: 1px solid #e9ecef;
            padding: 30px 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
        }
        
        .sidebar ul {
            list-style: none;
        }
        
        .sidebar li {
            margin-bottom: 15px;
        }
        
        .sidebar a {
            text-decoration: none;
            color: #495057;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 15px;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .sidebar a:hover,
        .sidebar a.active {
            background: #4a6cf7;
            color: white;
            box-shadow: 0 5px 15px rgba(74,108,247,0.2);
        }
        
        .main-content {
            flex: 1;
            padding: 40px;
            background: white;
        }
        
        h2 {
            margin-bottom: 30px;
            color: #4a6cf7;
            font-size: 32px;
            position: relative;
        }
        
        h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 50px;
            height: 3px;
            background: #4a6cf7;
        }
        
        .search-bar {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        
        .search-bar input {
            flex: 1;
            padding: 12px;
            border: 1px solid #dee2e6;
            border-radius: 10px;
        }
        
        .search-bar button {
            background: #4a6cf7;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            cursor: pointer;
        }
        
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            margin-bottom: 30px;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        th {
            background: #f8f9ff;
            font-weight: 600;
            color: #495057;
        }
        
        td {
            border-top: 1px solid #e9ecef;
            border-bottom: 1px solid #e9ecef;
        }
        
        td:first-child {
            border-left: 1px solid #e9ecef;
            border-radius: 10px 0 0 10px;
        }
        
        td:last-child {
            border-right: 1px solid #e9ecef;
            border-radius: 0 10px 10px 0;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .activate-btn {
            background: #28a745;
            color: white;
        }
        
        .deactivate-btn {
            background: #dc3545;
            color: white;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        #create-question-form {
            background: #f8f9ff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 40px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            display: grid;
            gap: 15px;
        }

        #create-question-form input,
        #create-question-form select,
        #create-question-form textarea {
            padding: 12px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
            width: 100%;
        }

        #create-question-form input:focus,
        #create-question-form select:focus,
        #create-question-form textarea:focus {
            outline: none;
            border-color: #4a6cf7;
            box-shadow: 0 0 0 3px rgba(74,108,247,0.1);
        }

        #create-question-form .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        #create-question-form .form-group label {
            font-weight: 600;
            color: #495057;
        }

        #options-block {
            display: grid;
            gap: 15px;
        }

        #options-block .option-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .hidden {
            display: none;
        }

        #create-question-form button[type="submit"] {
            background: #4a6cf7;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
            width: auto;
            margin-top: 10px;
        }

        #create-question-form button[type="submit"]:hover {
            background: #3955d1;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74,108,247,0.3);
        }

        @media (max-width: 768px) {
            .dashboard {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                padding: 20px;
            }
            
            .sidebar ul {
                display: flex;
                overflow-x: auto;
                gap: 10px;
            }
            
            .sidebar li {
                margin-bottom: 0;
            }
            
            .main-content {
                padding: 20px;
            }
        }
        button, .btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }

        button:hover, .btn:hover {
            background-color: #45a049;
        }

        button.deactivate-btn {
            background-color: #f44336;
        }
        button.deactivate-btn:hover {
            background-color: #d32f2f;
        }
        #questions-list {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        .question-card {
            background: #f8f9ff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .question-card p {
            font-weight: 600;
            color: #4a6cf7;
            margin-bottom: 10px;
        }
        .question-card ol {
            list-style-type: decimal;
            padding-left: 20px;
        }
        .question-card li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1><i class="fas fa-dashboard"></i> Admin Dashboard</h1>
        <button class="logout-btn" onclick="window.location.href='logout.php';">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </header>
    
    <div class="dashboard">
        <nav class="sidebar">
            <ul>
                <li><a href="admin_dashboard.php"><i class="fas fa-users"></i> Manage Users</a></li>
                <li><a href="events.php" class="active"><i class="fas fa-calendar-alt"></i> Manage Events</a></li>
                <li><a href="analytics.html"><i class="fas fa-chart-bar"></i> View Analytics</a></li>
                <li><a href="profile.html"><i class="fas fa-user-edit"></i> Edit Profile</a></li>
            </ul>
        </nav>
        
        <main class="main-content">
            <h2>Manage Questions</h2>
            <form id="create-question-form">
                <input type="hidden" id="exam-id">
                <input type="hidden" id="question-id">
              
                <div class="form-group">
                  <label for="q-type">Question Type:</label>
                  <select id="q-type" required>
                    <option value="mcq">MCQ</option>
                    <option value="truefalse">True/False</option>
                    <option value="audio">Audio Question</option>
                    <option value="video">Video Question</option>
                    <option value="image">Image Question</option>
                  </select>
                </div>
              
                <div class="form-group">
                  <label for="question-text">Question:</label>
                  <textarea id="question-text" required></textarea>
                </div>
              
                <div class="form-group">
                  <label for="media-url">Media (optional URL or file):</label>
                  <input type="text" id="media-url" placeholder="https://...">
                </div>
              
                <div id="options-block" class="hidden">
                  <div class="option-group">
                    <label for="opt1">Option 1:</label>
                    <input type="text" id="opt1">
                  </div>
                  <div class="option-group">
                    <label for="opt2">Option 2:</label>
                    <input type="text" id="opt2">
                  </div>
                  <div class="option-group">
                    <label for="opt3">Option 3:</label>
                    <input type="text" id="opt3">
                  </div>
                  <div class="option-group">
                    <label for="opt4">Option 4:</label>
                    <input type="text" id="opt4">
                  </div>
                  <div class="form-group">
                    <label for="correct-option">Correct Option:</label>
                    <select id="correct-option">
                      <option value="1">Option 1</option>
                      <option value="2">Option 2</option>
                      <option value="3">Option 3</option>
                      <option value="4">Option 4</option>
                    </select>
                  </div>
                </div>

                <div id="truefalse-block" class="hidden">
                    <div class="form-group">
                        <label for="truefalse-option">Correct Answer:</label>
                        <select id="truefalse-option">
                            <option value="1">True</option>
                            <option value="0">False</option>
                        </select>
                    </div>
                </div>
              
                <button type="submit">Add Question</button>
            </form>

            <div id="questions-list">
                <h3>Added Questions</h3>
            </div>
        </main>
    </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const qTypeSelect = document.getElementById('q-type');
    const optionsBlock = document.getElementById('options-block');
    const truefalseBlock = document.getElementById('truefalse-block');
    const createQuestionForm = document.getElementById('create-question-form');
    const examIdInput = document.getElementById('exam-id');
    const questionIdInput = document.getElementById('question-id');
    const questionsListDiv = document.getElementById('questions-list');
    const addQuestionButton = createQuestionForm.querySelector('button[type="submit"]');

    // This is the key line that fixes the issue.
    // It listens for changes to the dropdown and updates the form accordingly.
    qTypeSelect.addEventListener('change', handleQuestionTypeChange);

    const params = new URLSearchParams(location.search);
    const examId = params.get('exam_id');
    if (examId) {
        examIdInput.value = examId;
    }

    function handleQuestionTypeChange() {
        const optionInputs = document.querySelectorAll('#options-block input');
        const correctOptionSelect = document.getElementById('correct-option');
        
        // Always start by hiding both sections
        optionsBlock.classList.add('hidden');
        truefalseBlock.classList.add('hidden');
        
        // And disable the MCQ options to prevent them from being submitted
        optionInputs.forEach(input => {
            input.disabled = true;
            input.required = false; // Prevents validation errors on hidden fields
        });
        if (correctOptionSelect) {
            correctOptionSelect.disabled = true;
        }

        const selectedType = qTypeSelect.value;
        
        // Show the MCQ options block if the type matches
        if (['mcq', 'audio', 'video', 'image'].includes(selectedType)) {
            optionsBlock.classList.remove('hidden');
            optionInputs.forEach(input => {
                input.disabled = false;
                input.required = true; // Make them required again when visible
            });
            if (correctOptionSelect) {
                correctOptionSelect.disabled = false;
            }
        // Show the True/False block if that type is selected
        } else if (selectedType === 'truefalse') {
            truefalseBlock.classList.remove('hidden');
        }
    }

    function loadQuestionForEdit(question) {
        questionIdInput.value = question.id;
        qTypeSelect.value = question.q_type;
        document.getElementById('question-text').value = question.question_text;
        document.getElementById('media-url').value = question.media_url || '';

        if (['mcq', 'audio', 'video', 'image'].includes(question.q_type)) {
            document.getElementById('opt1').value = question.options[0]?.option_text || '';
            document.getElementById('opt2').value = question.options[1]?.option_text || '';
            document.getElementById('opt3').value = question.options[2]?.option_text || '';
            document.getElementById('opt4').value = question.options[3]?.option_text || '';
            document.getElementById('correct-option').value = question.correct_option;
        } else if (question.q_type === 'truefalse') {
            document.getElementById('truefalse-option').value = question.correct_option;
        }
        addQuestionButton.textContent = 'Update Question';
        handleQuestionTypeChange(); // Update the form view after loading data
    }

    async function loadQuestions() {
        if (!examIdInput.value) {
            questionsListDiv.innerHTML = '<p style="text-align:center;">Exam ID is missing. Please navigate from the Events page.</p>';
            return;
        }

        const res = await fetch(`list_questions.php?exam_id=${examIdInput.value}`);
        const data = await res.json();
        
        questionsListDiv.innerHTML = '<h3>Added Questions</h3>';
        
        if (data.status === 'success' && data.questions.length > 0) {
            data.questions.forEach(q => {
                const card = document.createElement('div');
                card.className = 'question-card';
                let optionsHtml = '';
                
                if (['mcq', 'audio', 'video', 'image'].includes(q.q_type) && q.options) {
                    optionsHtml = `<ol>${q.options.map(opt => `<li>${opt.option_text}</li>`).join('')}</ol>`;
                } else if (q.q_type === 'truefalse') {
                    optionsHtml = `<p>Correct Answer: <strong>${q.correct_option == 1 ? 'True' : 'False'}</strong></p>`;
                }

                const editButton = document.createElement('button');
                editButton.textContent = 'Edit';
                editButton.onclick = () => loadQuestionForEdit(q);

                card.innerHTML = `<p><strong>Q:</strong> ${q.question_text}</p>
                                  ${q.media_url ? `<p>Media: <a href="${q.media_url}" target="_blank">View Media</a></p>` : ''}
                                  ${optionsHtml}`;
                card.appendChild(editButton);
                questionsListDiv.appendChild(card);
            });
        } else {
            questionsListDiv.innerHTML += '<p style="text-align:center;">No questions added yet.</p>';
        }
    }

    createQuestionForm.addEventListener('submit', async e => {
        e.preventDefault();
        const fd = new FormData();
        const qType = qTypeSelect.value;
        const questionId = questionIdInput.value;
        
        const questionText = document.getElementById('question-text').value.trim();
        if (!examIdInput.value || questionText === '') {
            alert('Error: Please ensure you have entered a question.');
            return;
        }
        
        fd.append('exam_id', examIdInput.value);
        fd.append('q_type', qType);
        fd.append('question_text', questionText);
        fd.append('media_url', document.getElementById('media-url').value.trim());
        if (questionId) {
            fd.append('question_id', questionId);
        }

        if (['mcq', 'audio', 'video', 'image'].includes(qType)) {
            fd.append('correct_option', document.getElementById('correct-option').value);
            const opts = [
                document.getElementById('opt1').value,
                document.getElementById('opt2').value,
                document.getElementById('opt3').value,
                document.getElementById('opt4').value
            ];
            opts.forEach((o, i) => fd.append(`options[${i}]`, o.trim()));
        } else if (qType === 'truefalse') {
            fd.append('correct_option', document.getElementById('truefalse-option').value);
        }
        
        const endpoint = questionId ? 'update_question.php' : 'create_question.php';

        try {
            const res = await fetch(endpoint, { method: 'POST', body: fd });
            const data = await res.json();
            if(data.status === 'success'){
                alert("Question saved!");
                createQuestionForm.reset();
                questionIdInput.value = '';
                addQuestionButton.textContent = 'Add Question';
                handleQuestionTypeChange();
                loadQuestions();
            } else {
                alert("Error: " + (data.message || 'Unknown error'));
            }
        } catch (err) {
            console.error(err);
            alert('A network or server error occurred. Please check the console for details.');
        }
    });

    // Initial setup when the page loads
    if (qTypeSelect) {
        handleQuestionTypeChange();
        loadQuestions();
    }
});
</script>
</body>
</html>