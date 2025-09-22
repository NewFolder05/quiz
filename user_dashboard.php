<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* General styles from admin dashboard for consistency */
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
        
        #join-event-form {
            background: #f8f9ff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 40px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            display: flex;
            gap: 15px;
            align-items: center;
        }

        #join-event-form input {
            flex-grow: 1;
            padding: 12px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            font-size: 14px;
        }

        #join-event-form button {
            background: #28a745;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        #join-event-form button:hover {
            background: #218838;
            transform: translateY(-2px);
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

        .exam-btn {
            background: #4a6cf7;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
        .exam-btn:hover {
            background: #3955d1;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>NEW FOLDER 5</h1>
        <button class="logout-btn" onclick="window.location.href='logout.php';">Logout</button>
    </header>
    
    <main class="main-content">
        <h2>Join Event</h2>
        <form id="join-event-form">
            <input type="text" id="unique-code" placeholder="Enter 6-digit Event Code" required>
            <button type="submit">Join</button>
        </form>

        <h2>My Exams</h2>
        <table id="exams-table">
            <thead>
                <tr>
                    <th>Exam Title</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Score</th> <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                </tbody>
        </table>
    </main>

    <script>
    document.getElementById('join-event-form').addEventListener('submit', async e => {
        e.preventDefault();
        const code = document.getElementById('unique-code').value.trim();
        if (code.length !== 6) {
            alert('Please enter a valid 6-digit code.');
            return;
        }

        const fd = new FormData();
        fd.append('code', code);

        try {
            const res = await fetch('join_event.php', { method: 'POST', body: fd });
            const data = await res.json();

            if (data.status === 'success') {
                alert('Successfully joined event!');
                document.getElementById('unique-code').value = '';
                loadExams();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (err) {
            console.error('Failed to fetch from join_event.php:', err);
            alert('A network or server error occurred. Please check the browser console.');
        }
    });

    async function loadExams() {
        const tb = document.querySelector('#exams-table tbody');
        tb.innerHTML = '<tr><td colspan="5" style="text-align:center;">Loading exams...</td></tr>'; // colspan is 5 now
        
        try {
            const res = await fetch('list_user_exams.php');
            const data = await res.json();
            tb.innerHTML = '';

            if (data.status === 'success' && data.exams.length > 0) {
                data.exams.forEach(exam => {
                    const tr = document.createElement('tr');
                    let actionButton = '';
                    let scoreDisplay = 'N/A'; // Default score display
                    const now = new Date();
                    const examDateTime = new Date(`${exam.exam_date}T${exam.start_time}`);
                    const endDateTime = new Date(`${exam.exam_date}T${exam.end_time}`);

                    if (exam.score !== null) { // Check if a score exists
                        scoreDisplay = exam.score;
                        actionButton = '<button class="exam-btn" disabled>Submitted</button>';
                    } else if (now < examDateTime) {
                        actionButton = '<button class="exam-btn" disabled>Upcoming</button>';
                    } else if (now >= examDateTime && now <= endDateTime) {
                        actionButton = `<button class="exam-btn" onclick="startExam(${exam.id})">Start Exam</button>`;
                    } else {
                        actionButton = '<button class="exam-btn" disabled>Expired</button>';
                    }

                    tr.innerHTML = `
                        <td>${exam.title} (${exam.event_name})</td>
                        <td>${exam.exam_date}</td>
                        <td>${exam.start_time.substring(0, 5)} - ${exam.end_time.substring(0, 5)}</td>
                        <td>${scoreDisplay}</td>
                        <td>${actionButton}</td>
                    `;
                    tb.appendChild(tr);
                });
            } else {
                tb.innerHTML = '<tr><td colspan="5" style="text-align:center;">No exams found. Join an event with a code.</td></tr>';
            }
        } catch (err) {
            console.error('Failed to load exams:', err);
            tb.innerHTML = '<tr><td colspan="5" style="text-align:center; color:red;">Error loading exams. Please try again.</td></tr>';
        }
    }

    function startExam(examId) {
        window.location.href = `take_exam.php?exam_id=${examId}`;
    }

    loadExams();
    </script>
</body>
</html>