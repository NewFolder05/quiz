<?php
require_once "db_config.php";
$admin = require_admin();

$sql = "SELECT e.*, a.name AS created_by_name 
        FROM events e 
        LEFT JOIN admins a ON e.created_by=a.id 
        ORDER BY start_date DESC"; // ✅ Correct column name

$res = $conn->query($sql);
$events=[];
while($r=$res->fetch_assoc()) $events[]=$r;
echo json_encode(["status"=>"success","events"=>$events]);
?>