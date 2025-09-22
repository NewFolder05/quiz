<?php
require_once "db_config.php";
$admin = require_admin();

$question_id = intval($_POST['question_id'] ?? 0);
$exam_id = intval($_POST['exam_id'] ?? 0);
$q_type = $_POST['q_type'] ?? '';
$question_text = trim($_POST['question_text'] ?? '');
$media_url = trim($_POST['media_url'] ?? '');
$correct_option = intval($_POST['correct_option'] ?? 0);
$options = $_POST['options'] ?? [];

if ($question_id <= 0 || $exam_id <= 0 || $question_text === '') {
    echo json_encode(["status" => "error", "message" => "Invalid input for update."]);
    exit;
}

$conn->begin_transaction();

try {
    // Update the question itself
    $stmt = $conn->prepare("UPDATE questions SET q_type=?, question_text=?, media_url=?, correct_option=? WHERE id=? AND exam_id=?");
    $stmt->bind_param("sssiis", $q_type, $question_text, $media_url, $correct_option, $question_id, $exam_id);
    $stmt->execute();
    $stmt->close();

    // Delete old options and insert new ones
    $conn->query("DELETE FROM question_options WHERE question_id=$question_id");

    if (in_array($q_type, ['mcq', 'truefalse', 'audio', 'video', 'image']) && is_array($options)) {
        $optStmt = $conn->prepare("INSERT INTO question_options (question_id,option_index,option_text) VALUES (?,?,?)");
        foreach ($options as $i => $opt) {
            $idx = $i + 1;
            $optStmt->bind_param("iis", $question_id, $idx, $opt);
            $optStmt->execute();
        }
        $optStmt->close();
    }

    $conn->commit();
    echo json_encode(["status" => "success", "question_id" => $question_id]);
} catch (mysqli_sql_exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    exit;
}
?>