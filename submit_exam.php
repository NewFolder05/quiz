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

$exam_id = intval($_POST['exam_id'] ?? 0);
$answers = json_decode($_POST['answers'] ?? '{}', true);

if ($exam_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid exam ID"]);
    exit;
}

$score = 0;
try {
    foreach ($answers as $question_id => $user_answer) {
        $stmt = $conn->prepare("SELECT correct_option FROM questions WHERE id = ? AND exam_id = ?");
        $stmt->bind_param("ii", $question_id, $exam_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $q = $res->fetch_assoc();
        $stmt->close();
        
        if ($q && $q['correct_option'] == $user_answer) {
            $score++;
        }
    }

    // Save the result to the database
    $stmt = $conn->prepare("INSERT INTO user_results (user_id, exam_id, score) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $exam_id, $score);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "score" => $score, "message" => "Exam submitted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to save results"]);
    }
    $stmt->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Server error: " . $e->getMessage()]);
}

$conn->close();
?>