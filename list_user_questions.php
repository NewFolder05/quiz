<?php
session_start();
require_once "db_config.php";

header("Content-Type: application/json; charset=UTF-8");

$user_id = $_SESSION['user_id'] ?? 0;
$exam_id = intval($_GET['exam_id'] ?? 0);

// Check if user is logged in
if ($user_id <= 0) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

// Check if exam_id is provided
if ($exam_id <= 0) {
    echo json_encode(["status" => "error", "message" => "exam_id required"]);
    exit;
}

try {
    // Check if the user is authorized to take this exam
    $stmt = $conn->prepare("
        SELECT ue.user_id FROM user_events ue
        JOIN exams e ON ue.event_id = e.event_id
        WHERE ue.user_id = ? AND e.id = ?
    ");
    $stmt->bind_param("ii", $user_id, $exam_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        http_response_code(403);
        echo json_encode(["status" => "error", "message" => "Forbidden: You are not authorized to view this exam."]);
        exit;
    }
    $stmt->close();

    // Fetch the questions for the exam
    $questions = [];
    $stmt = $conn->prepare("SELECT * FROM questions WHERE exam_id = ? ORDER BY created_at ASC");
    $stmt->bind_param("i", $exam_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($q = $result->fetch_assoc()) {
        $q_type = $q['q_type'];

        if (in_array($q_type, ['mcq', 'audio', 'video', 'image'])) {
            $optStmt = $conn->prepare("SELECT option_index, option_text FROM question_options WHERE question_id = ? ORDER BY option_index");
            $optStmt->bind_param("i", $q['id']);
            $optStmt->execute();
            $options = $optStmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $q['options'] = $options;
            $optStmt->close();
        } elseif ($q_type === 'truefalse') {
            $q['options'] = [
                ['option_index' => 1, 'option_text' => 'True'],
                ['option_index' => 0, 'option_text' => 'False']
            ];
        } else {
            $q['options'] = [];
        }
        $questions[] = $q;
    }
    
    $stmt->close();
    echo json_encode(["status" => "success", "questions" => $questions]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}

$conn->close();
?>