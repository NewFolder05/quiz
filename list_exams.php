<?php
require_once "db_config.php";
$admin = require_admin();

$event_id = intval($_GET['event_id'] ?? 0);
if ($event_id <= 0) {
    echo json_encode(["status" => "error", "message" => "event_id required"]);
    exit;
}

$stmt = $conn->prepare("SELECT id, title, exam_date, start_time, end_time FROM exams WHERE event_id=? ORDER BY exam_date DESC");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$res = $stmt->get_result();
$exams = [];
while ($row = $res->fetch_assoc()) {
    $exams[] = $row;
}
echo json_encode(["status" => "success", "exams" => $exams]);
?>