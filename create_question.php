<?php
require_once "db_config.php";
$admin = require_admin();

$exam_id = intval($_POST['exam_id'] ?? 0);
$q_type = $_POST['q_type'] ?? '';
$question_text = trim($_POST['question_text'] ?? '');
$media_url = trim($_POST['media_url'] ?? '');
$correct_option = intval($_POST['correct_option'] ?? 0);
$options = $_POST['options'] ?? [];

if ($exam_id <= 0 || !in_array($q_type, ['mcq','truefalse','audio','video','image']) || $question_text === '') {
    echo json_encode(["status"=>"error","message"=>"Invalid input"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO questions (exam_id,q_type,question_text,media_url,correct_option,created_by) 
                        VALUES (?,?,?,?,?,?)");
$stmt->bind_param("isssii", $exam_id, $q_type, $question_text, $media_url, $correct_option, $_SESSION['admin_id']);

if ($stmt->execute()) {
    $qid = $stmt->insert_id;

    // This block now correctly handles all question types that need options
    if (in_array($q_type, ['mcq', 'truefalse', 'audio', 'video', 'image']) && is_array($options)) {
        $optStmt = $conn->prepare("INSERT INTO question_options (question_id,option_index,option_text) VALUES (?,?,?)");
        foreach ($options as $i => $opt) {
            $idx = $i + 1;
            $optStmt->bind_param("iis", $qid, $idx, $opt);
            $optStmt->execute();
        }
        $optStmt->close();
    }

    echo json_encode(["status"=>"success","question_id"=>$qid]);
} else {
    echo json_encode(["status"=>"error","message"=>"Failed"]);
}
?>