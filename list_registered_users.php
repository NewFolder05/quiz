<?php
require_once "db_config.php";
$admin = require_admin();

header("Content-Type: application/json; charset=UTF-8");

try {
    $stmt = $conn->prepare("
        SELECT u.id, u.name, u.email, COUNT(ue.event_id) AS events_joined
        FROM users u
        LEFT JOIN user_events ue ON u.id = ue.user_id
        GROUP BY u.id
        ORDER BY u.name ASC
    ");
    $stmt->execute();
    $res = $stmt->get_result();
    $users = [];
    while ($row = $res->fetch_assoc()) {
        $users[] = $row;
    }
    $stmt->close();
    echo json_encode(["status" => "success", "users" => $users]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Server error: " . $e->getMessage()]);
}

$conn->close();
?>