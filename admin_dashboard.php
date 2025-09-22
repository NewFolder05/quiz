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
    <title>Admin Dashboard - Leaderboards</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* (Your existing CSS here) */
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
        .leaderboards-container {
            display: grid;
            gap: 20px;
            margin-top: 20px;
        }
        .leaderboard-card {
            background: #f8f9ff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        .leaderboard-card h3 {
            margin-bottom: 15px;
            color: #4a6cf7;
            font-size: 20px;
        }
        .exam-selection-area {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .event-exam-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .event-exam-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .event-exam-item button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }
        #event-select {
            padding: 12px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            width: 250px;
        }
        #event-select:focus {
            outline: none;
            border-color: #4a6cf7;
            box-shadow: 0 0 0 3px rgba(74,108,247,0.1);
        }
        .individual-exam-leaderboard {
            margin-top: 20px;
            border-top: 1px solid #e9ecef;
            padding-top: 20px;
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
                <li><a href="admin_dashboard.php" class="active"><i class="fas fa-users"></i> Manage Users</a></li>
                <li><a href="events.php"><i class="fas fa-calendar-alt"></i> Manage Events</a></li>
                <li><a href="analytics.html"><i class="fas fa-chart-bar"></i> View Analytics</a></li>
                <li><a href="profile.html"><i class="fas fa-user-edit"></i> Edit Profile</a></li>
            </ul>
        </nav>
        
        <main class="main-content">
            <h2>Leaderboards</h2>
            <div class="leaderboards-container">
                <div class="leaderboard-card">
                    <h3>Select an Event</h3>
                    <div class="exam-selection-area">
                        <select id="event-select">
                            <option value="">-- Select Event --</option>
                        </select>
                    </div>
                </div>

                <div class="leaderboard-card hidden" id="event-leaderboard-card">
                    <h3 id="event-leaderboard-title">Global Leaderboard</h3>
                    <table class="leaderboard-table">
                        <thead><tr><th>Rank</th><th>User</th><th>Total Score</th></tr></thead>
                        <tbody id="event-leaderboard-body"></tbody>
                    </table>
                </div>

                <div class="leaderboard-card hidden" id="exam-leaderboards-card">
                    <h3 id="exam-leaderboards-title">Exams in this Event</h3>
                    <div id="exam-leaderboard-list" class="event-exam-list"></div>
                </div>
                
                <div id="individual-exam-details" class="hidden">
                    </div>
            </div>
        </main>
    </div>

    <script>
        async function loadEventsForDropdown() {
            try {
                const res = await fetch('list_events.php');
                const data = await res.json();
                const select = document.getElementById('event-select');
                if (data.status === 'success' && data.events.length > 0) {
                    data.events.forEach(event => {
                        const option = document.createElement('option');
                        option.value = event.id;
                        option.textContent = event.name;
                        select.appendChild(option);
                    });
                }
            } catch (err) {
                console.error('Error loading events:', err);
            }
        }

        async function loadLeaderboards(eventId) {
            try {
                const res = await fetch(`get_leaderboards.php?event_id=${eventId}`);
                const data = await res.json();
                const eventLeaderboardCard = document.getElementById('event-leaderboard-card');
                const examLeaderboardsCard = document.getElementById('exam-leaderboards-card');
                
                if (data.status === 'success') {
                    // Populate Event-based Leaderboard
                    const globalTb = document.getElementById('event-leaderboard-body');
                    globalTb.innerHTML = '';
                    if (data.event_leaderboard.length > 0) {
                        document.getElementById('event-leaderboard-title').textContent = `Leaderboard for Event: ${data.event_title}`;
                        data.event_leaderboard.forEach((entry, index) => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `<td>${index + 1}</td><td>${entry.name}</td><td>${entry.total_score}</td>`;
                            globalTb.appendChild(tr);
                        });
                        eventLeaderboardCard.classList.remove('hidden');
                    } else {
                        eventLeaderboardCard.classList.remove('hidden');
                        document.getElementById('event-leaderboard-title').textContent = `Leaderboard for Event: ${data.event_title}`;
                        globalTb.innerHTML = '<tr><td colspan="3" style="text-align:center;">No scores found for this event.</td></tr>';
                    }

                    // Populate Individual Exam Leaderboards
                    const examListDiv = document.getElementById('exam-leaderboard-list');
                    examListDiv.innerHTML = '';
                    if (data.exam_leaderboards && Object.keys(data.exam_leaderboards).length > 0) {
                        for (const examId in data.exam_leaderboards) {
                            const leaderboard = data.exam_leaderboards[examId];
                            const examTitle = leaderboard.exam_title;
                            
                            const examItem = document.createElement('div');
                            examItem.className = 'event-exam-item';
                            examItem.innerHTML = `<span>${examTitle}</span>`;
                            const button = document.createElement('button');
                            button.textContent = 'View Leaderboard';
                            button.onclick = () => viewExamLeaderboard(examId, examTitle, leaderboard.scores);
                            examItem.appendChild(button);
                            examListDiv.appendChild(examItem);
                        }
                        examLeaderboardsCard.classList.remove('hidden');
                    } else {
                        examLeaderboardsCard.classList.remove('hidden');
                        examListDiv.innerHTML = '<p>No exams with scores yet for this event.</p>';
                    }
                }
            } catch (err) {
                console.error('Error loading leaderboards:', err);
                alert('Failed to load leaderboards.');
            }
        }
        
        function viewExamLeaderboard(examId, examTitle, scores) {
            const individualLeaderboardDiv = document.getElementById('individual-exam-details');
            individualLeaderboardDiv.innerHTML = ''; // Clear previous leaderboard
            
            let tableHtml = `
                <div class="leaderboard-card exam-leaderboard-container">
                    <h3>${examTitle} Leaderboard</h3>
                    <table class="leaderboard-table">
                        <thead><tr><th>Rank</th><th>User</th><th>Score</th></tr></thead>
                        <tbody>
            `;
            if (scores.length > 0) {
                scores.forEach((entry, index) => {
                    tableHtml += `<tr><td>${index + 1}</td><td>${entry.name}</td><td>${entry.score}</td></tr>`;
                });
            } else {
                tableHtml += '<tr><td colspan="3" style="text-align:center;">No scores yet.</td></tr>';
            }
            tableHtml += `</tbody></table></div>`;
            
            individualLeaderboardDiv.innerHTML = tableHtml;
            individualLeaderboardDiv.classList.remove('hidden'); // Show the new leaderboard
        }

        document.getElementById('event-select').addEventListener('change', (e) => {
            const eventId = e.target.value;
            if (eventId) {
                loadLeaderboards(eventId);
            } else {
                document.getElementById('event-leaderboard-card').classList.add('hidden');
                document.getElementById('exam-leaderboards-card').classList.add('hidden');
                document.getElementById('individual-exam-details').classList.add('hidden'); // Hide exam details on new event select
            }
        });

        loadEventsForDropdown();
    </script>
</body>
</html>