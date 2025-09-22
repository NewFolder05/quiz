<?php
require_once "db_config.php";
$admin = require_admin();

header("Content-Type: application/json; charset=UTF-8");

$event_id = intval($_GET['event_id'] ?? 0);
if ($event_id <= 0) {
    echo json_encode(["status" => "error", "message" => "event_id required"]);
    exit;
}

try {
    // Get Event Title
    $eventStmt = $conn->prepare("SELECT name FROM events WHERE id=?");
    $eventStmt->bind_param("i", $event_id);
    $eventStmt->execute();
    $eventRes = $eventStmt->get_result();
    $eventRow = $eventRes->fetch_assoc();
    $eventTitle = $eventRow['name'] ?? 'Selected Event';
    $eventStmt->close();

    // Event Leaderboard (Sum of scores across all exams for a specific event)
    $eventLeaderboardStmt = $conn->prepare("
        SELECT u.name, SUM(ur.score) AS total_score
        FROM users u
        JOIN user_results ur ON u.id = ur.user_id
        JOIN exams e ON ur.exam_id = e.id
        WHERE e.event_id = ?
        GROUP BY u.id
        ORDER BY total_score DESC
        LIMIT 10
    ");
    $eventLeaderboardStmt->bind_param("i", $event_id);
    $eventLeaderboardStmt->execute();
    $eventRes = $eventLeaderboardStmt->get_result();
    $eventLeaderboard = [];
    while ($row = $eventRes->fetch_assoc()) {
        $eventLeaderboard[] = $row;
    }
    $eventLeaderboardStmt->close();

    // Individual Exam Leaderboards for the selected event
    $examsStmt = $conn->prepare("SELECT id, title FROM exams WHERE event_id=?");
    $examsStmt->bind_param("i", $event_id);
    $examsStmt->execute();
    $examsRes = $examsStmt->get_result();
    $examLeaderboards = [];
    while ($exam = $examsRes->fetch_assoc()) {
        $examId = $exam['id'];
        $examTitle = $exam['title'];

        $scoresStmt = $conn->prepare("
            SELECT u.name, ur.score
            FROM user_results ur
            JOIN users u ON ur.user_id = u.id
            WHERE ur.exam_id = ?
            ORDER BY ur.score DESC
            LIMIT 5
        ");
        $scoresStmt->bind_param("i", $examId);
        $scoresStmt->execute();
        $scoresRes = $scoresStmt->get_result();
        $scores = [];
        while ($scoreRow = $scoresRes->fetch_assoc()) {
            $scores[] = $scoreRow;
        }
        $scoresStmt->close();

        $examLeaderboards[$examId] = [
            'exam_title' => $examTitle,
            'scores' => $scores
        ];
    }
    
    echo json_encode(["status" => "success", "event_title" => $eventTitle, "event_leaderboard" => $eventLeaderboard, "exam_leaderboards" => $examLeaderboards]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Server error: " . $e->getMessage()]);
}

$conn->close();
?>