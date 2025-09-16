<?php
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'super_admin') {
    header("Location: index.html");
    exit;
}
?>
<h1>Welcome Super Admin <?php echo htmlspecialchars($_SESSION['admin_name']); ?> 🎉</h1>
<p>You can manage all users and admins here.</p>
