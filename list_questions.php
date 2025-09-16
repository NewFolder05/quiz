<?php
require_once "db_config.php";
$admin = require_admin();

$exam_id=intval($_GET['exam_id']??0); // Changed to exam_id
if ($exam_id<=0) {
    echo json_encode(["status"=>"error","message"=>"exam_id required"]);
    exit;
}

$stmt=$conn->prepare("SELECT * FROM questions WHERE exam_id=? ORDER BY created_at DESC"); // Changed to exam_id
$stmt->bind_param("i",$exam_id); // Changed to exam_id
$stmt->execute();
$res=$stmt->get_result();
$qs=[];
while($q=$res->fetch_assoc()){
    // This logic needs to be updated to handle all question types
    if(in_array($q['q_type'], ['mcq', 'truefalse', 'short', 'audio', 'video', 'image'])){
        $optStmt=$conn->prepare("SELECT option_index,option_text FROM question_options WHERE question_id=? ORDER BY option_index");
        $optStmt->bind_param("i",$q['id']);
        $optStmt->execute();
        $opts=$optStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $q['options']=$opts;
        $optStmt->close();
    }
    $qs[]=$q;
}
echo json_encode(["status"=>"success","questions"=>$qs]);
?>