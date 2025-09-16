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
    <title>Admin Dashboard - Manage Exams</title>
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
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
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
            box-shadow: 0 5px 15px rgba(74, 108, 247, 0.3);
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
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
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
            box-shadow: 0 5px 15px rgba(74, 108, 247, 0.2);
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

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            margin-bottom: 30px;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
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

        button,
        .btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }

        button:hover,
        .btn:hover {
            background-color: #45a049;
        }

        #create-exam-form {
            background: #f8f9ff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 40px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            display: grid;
            gap: 15px;
        }

        #create-exam-form input {
            padding: 12px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        #create-exam-form input:focus {
            outline: none;
            border-color: #4a6cf7;
            box-shadow: 0 0 0 3px rgba(74, 108, 247, 0.1);
        }

        #create-exam-form button[type="submit"] {
            background: #4a6cf7;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
        }

        #create-exam-form button[type="submit"]:hover {
            background: #3955d1;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 108, 247, 0.2);
        }
        #create-exam-form .form-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
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
                <li><a href="questions.php"><i class="fas fa-question-circle"></i> Manage Questions</a></li>
                <li><a href="analytics.html"><i class="fas fa-chart-bar"></i> View Analytics</a></li>
                <li><a href="profile.html"><i class="fas fa-user-edit"></i> Edit Profile</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <h2>Manage Exams</h2>
            <form id="create-exam-form">
  <input type="hidden" id="event-id">
  <div class="form-group">
    <label for="exam-title">Exam Title:</label>
    <input type="text" id="exam-title" required>
  </div>
  <div class="form-group">
    <label for="exam-date">Date:</label>
    <input type="date" id="exam-date" required>
  </div>
  <div class="form-group">
    <label for="start-time">Start Time:</label>
    <input type="time" id="start-time" required>
  </div>
  <div class="form-group">
    <label for="end-time">End Time:</label>
    <input type="time" id="end-time" required>
  </div>
  <button type="submit">Add Exam</button>
</form>

            <table>
                <thead>
                    <tr>
                        <th>Exam</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </main>
    </div>

<script>
    const params = new URLSearchParams(location.search);
    document.getElementById('event-id').value = params.get('event_id');

    document.getElementById('create-exam-form').addEventListener('submit', async e => {
        e.preventDefault();
        const fd = new FormData();
        fd.append('title', document.getElementById('exam-title').value);
        fd.append('event_id', document.getElementById('event-id').value);
        fd.append('exam_date', document.getElementById('exam-date').value);
        fd.append('start_time', document.getElementById('start-time').value);
        fd.append('end_time', document.getElementById('end-time').value);
        
        const res = await fetch('create_exam.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.status === 'success') {
            alert('Exam added');
            loadExams();
        } else {
            alert("Error: " + data.message);
        }
    });

    async function loadExams() {
        const eventId = document.getElementById('event-id').value;
        const res = await fetch('list_exams.php?event_id=' + eventId);
        const data = await res.json();
        const tb = document.querySelector('tbody');
        tb.innerHTML = '';
        if (data.status === 'success') {
            data.exams.forEach(ex => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${ex.title}</td><td>${ex.exam_date}</td><td>${ex.start_time.substring(0, 5)} - ${ex.end_time.substring(0, 5)}</td>
                  <td><button onclick="openQuestions(${ex.id})">Set Questions</button></td>`;
                tb.appendChild(tr);
            });
        } else {
             // Handle the case where no exams are returned or an error occurs
             tb.innerHTML = '<tr><td colspan="4" style="text-align:center;">No exams found for this event.</td></tr>';
        }
    }

    function openQuestions(examId) {
        window.location = `questions.php?exam_id=${examId}`;
    }

    loadExams();
</script>

</body>
</html>