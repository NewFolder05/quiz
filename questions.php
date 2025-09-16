<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.html"); // redirect to login page
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
                <li><a href="events.php"><i class="fas fa-calendar-alt"></i> Manage Events</a></li>
                <li><a href="questions.php" class="active"><i class="fas fa-question-circle"></i> Manage Questions</a></li>
                <li><a href="analytics.html"><i class="fas fa-chart-bar"></i> View Analytics</a></li>
                <li><a href="profile.html"><i class="fas fa-user-edit"></i> Edit Profile</a></li>
            </ul>
        </nav>
        
        <main class="main-content">
            <h2>Manage Questions</h2>
            <form id="create-question-form">
                <input type="hidden" id="exam-id">
              
                <div class="form-group">
                  <label for="q-type">Question Type:</label>
                  <select id="q-type" required>
                    <option value="mcq">MCQ</option>
                    <option value="truefalse">True/False</option>
                    <option value="short">Short Answer</option>
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

                <div id="short-block" class="hidden">
                    <div class="form-group">
                        <label for="short-answer">Correct Answer:</label>
                        <input type="text" id="short-answer">
                    </div>
                </div>
              
                <button type="submit">Add Question</button>
            </form>
        </main>
    </div>

<script>
const params = new URLSearchParams(location.search);
document.getElementById('exam-id').value = params.get('exam_id');

document.getElementById('q-type').addEventListener('change', e => {
    // Hide all specific question type blocks first
    document.getElementById('options-block').classList.add('hidden');
    document.getElementById('truefalse-block').classList.add('hidden');
    document.getElementById('short-block').classList.add('hidden');

    const selectedType = e.target.value;
    if (['mcq', 'audio', 'video', 'image'].includes(selectedType)) {
        document.getElementById('options-block').classList.remove('hidden');
    } else if (selectedType === 'truefalse') {
        document.getElementById('truefalse-block').classList.remove('hidden');
    } else if (selectedType === 'short') {
        document.getElementById('short-block').classList.remove('hidden');
    }
});

document.getElementById('create-question-form').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = new FormData();
    const qType = document.getElementById('q-type').value;

    fd.append('exam_id', document.getElementById('exam-id').value);
    fd.append('q_type', qType);
    fd.append('question_text', document.getElementById('question-text').value);
    fd.append('media_url', document.getElementById('media-url').value);

    if (['mcq', 'audio', 'video', 'image'].includes(qType)) {
        fd.append('correct_option', document.getElementById('correct-option').value);
        const opts = [
            document.getElementById('opt1').value,
            document.getElementById('opt2').value,
            document.getElementById('opt3').value,
            document.getElementById('opt4').value
        ];
        opts.forEach((o, i) => fd.append(`options[${i}]`, o));
    } else if (qType === 'truefalse') {
        // The correct option for true/false will be '1' for true and '0' for false
        fd.append('correct_option', document.getElementById('truefalse-option').value);
    } else if (qType === 'short') {
        // For short answer, we send the answer text itself in the options array
        fd.append('options[]', document.getElementById('short-answer').value);
    }

    const res = await fetch('create_question.php', { method: 'POST', body: fd });
    const data = await res.json();
    if(data.status === 'success'){
        alert("Question Added!");
    } else {
        alert("Error: " + data.message);
    }
});

// Run the change event once on page load to set the initial state
document.getElementById('q-type').dispatchEvent(new Event('change'));
</script>

</body>
</html>