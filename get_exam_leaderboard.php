<?php
require_once "db_config.php";
$admin = require_admin();

header("Content-Type: application/json; charset=UTF-8");

$exam_id = intval($_GET['exam_id'] ?? 0);
if ($exam_id <= 0) {
    echo json_encode(["status" => "error", "message" => "exam_id required"]);
    exit;
}

try {
    $scoresStmt = $conn->prepare("
        SELECT u.name, ur.score
        FROM user_results ur
        JOIN users u ON ur.user_id = u.id
        WHERE ur.exam_id = ?
        ORDER BY ur.score DESC
    ");
    $scoresStmt->bind_param("i", $exam_id);
    $scoresStmt->execute();
    $scoresRes = $scoresStmt->get_result();
    $scores = [];
    while ($scoreRow = $scoresRes->fetch_assoc()) {
        $scores[] = $scoreRow;
    }
    $scoresStmt->close();

    echo json_encode(["status" => "success", "scores" => $scores]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Server error: " . $e->getMessage()]);
}

$conn->close();
?>