<!-- dashboard.html (Main Dashboard - Manage Users) -->
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
    <title>Admin Dashboard - Manage Users</title>
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
                <li><a href="questions.php"><i class="fas fa-question-circle"></i> Manage Questions</a></li>
                <li><a href="analytics.html"><i class="fas fa-chart-bar"></i> View Analytics</a></li>
                <li><a href="profile.html"><i class="fas fa-user-edit"></i> Edit Profile</a></li>
            </ul>
        </nav>
        
        <main class="main-content">
            <h2>Manage Users</h2>
            <div class="search-bar">
                <input type="text" placeholder="Search users...">
                <button><i class="fas fa-search"></i> Search</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Joined Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>user1</td>
                        <td>user1@example.com</td>
                        <td>Active</td>
                        <td>2024-01-15</td>
                        <td>
                            <button class="btn deactivate-btn" onclick="deactivateUser('user1')">Deactivate</button>
                        </td>
                    </tr>
                    <tr>
                        <td>user2</td>
                        <td>user2@example.com</td>
                        <td>Inactive</td>
                        <td>2024-02-20</td>
                        <td>
                            <button class="btn activate-btn" onclick="activateUser('user2')">Activate</button>
                        </td>
                    </tr>
                    <tr>
                        <td>user3</td>
                        <td>user3@example.com</td>
                        <td>Active</td>
                        <td>2024-03-10</td>
                        <td>
                            <button class="btn deactivate-btn" onclick="deactivateUser('user3')">Deactivate</button>
                        </td>
                    </tr>
                    <tr>
                        <td>user4</td>
                        <td>user4@example.com</td>
                        <td>Active</td>
                        <td>2024-04-05</td>
                        <td>
                            <button class="btn deactivate-btn" onclick="deactivateUser('user4')">Deactivate</button>
                        </td>
                    </tr>
                    <!-- Add more users -->
                </tbody>
            </table>
            <button class="submit-btn" style="margin-top: 20px;"><i class="fas fa-plus"></i> Add New User</button>
        </main>
    </div>

    <script>
        function activateUser(username) {
            alert(`Activated user: ${username}`);
        }
        
        function deactivateUser(username) {
            alert(`Deactivated user: ${username}`);
        }
    </script>
</body>
</html>