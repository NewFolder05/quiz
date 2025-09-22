<?php
session_start();
require_once "db_config.php";

header("Content-Type: application/json; charset=UTF-8");

$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id <= 0) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT section, class FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();
    $stmt->close();

    if (!$user) {
        echo json_encode(["status" => "error", "message" => "User not found"]);
        exit;
    }

    $stmt = $conn->prepare("
        SELECT 
            e.id, e.title, e.exam_date, e.start_time, e.end_time, v.name AS event_name,
            ur.score AS score
        FROM exams e
        JOIN user_events ue ON e.event_id = ue.event_id
        JOIN events v ON e.event_id = v.id
        LEFT JOIN user_results ur ON e.id = ur.exam_id AND ur.user_id = ?
        WHERE ue.user_id = ?
          AND v.section = ?
          AND ? BETWEEN v.class_from AND v.class_to
        ORDER BY e.exam_date ASC, e.start_time ASC
    ");
    $stmt->bind_param("iisi", $user_id, $user_id, $user['section'], $user['class']);
    $stmt->execute();
    $res = $stmt->get_result();
    $exams = [];
    while ($row = $res->fetch_assoc()) {
        $exams[] = $row;
    }
    echo json_encode(["status" => "success", "exams" => $exams]);
    $stmt->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Server error: " . $e->getMessage()]);
}

$conn->close();
?>