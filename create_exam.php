<?php
require_once "db_config.php";
$admin = require_admin();

$title = trim($_POST['title'] ?? '');
$event_id = intval($_POST['event_id'] ?? 0);
$exam_date = trim($_POST['exam_date'] ?? '');
$start_time = trim($_POST['start_time'] ?? ''); // New
$end_time = trim($_POST['end_time'] ?? ''); // New

if ($title === '' || $event_id <= 0 || $exam_date === '' || $start_time === '' || $end_time === '') {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO exams (event_id, title, exam_date, start_time, end_time, created_by) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issssi", $event_id, $title, $exam_date, $start_time, $end_time, $_SESSION['admin_id']);
if ($stmt->execute()) {
    echo json_encode(["status" => "success", "exam_id" => $stmt->insert_id]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed"]);
}
?>