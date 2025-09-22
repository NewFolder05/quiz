<?php
require_once "db_config.php";
$admin = require_admin();

$input = $_POST;
$name = trim($input['name'] ?? '');
$start_date = trim($input['start_date'] ?? '');
$end_date = trim($input['end_date'] ?? '');
$section = trim($input['section'] ?? '');
$class_from = intval($input['class_from'] ?? 0);
$class_to = intval($input['class_to'] ?? 0);

if ($name==='' || $start_date==='' || $end_date==='' || !in_array($section,['lp','up','hs'])) {
    echo json_encode(["status"=>"error","message"=>"Invalid input"]);
    exit;
}

function generateUniqueCode($conn) {
    do {
        $code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $stmt = $conn->prepare("SELECT id FROM events WHERE unique_code = ?");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();
    } while ($result->num_rows > 0);
    return $code;
}

$unique_code = generateUniqueCode($conn);

$stmt = $conn->prepare("INSERT INTO events (name, start_date, end_date, section, class_from, class_to, created_by, unique_code) VALUES (?,?,?,?,?,?,?,?)");
$stmt->bind_param("ssssiisi", $name, $start_date, $end_date, $section, $class_from, $class_to, $_SESSION['admin_id'], $unique_code);

if ($stmt->execute()) {
    echo json_encode(["status"=>"success","event_id"=>$stmt->insert_id, "unique_code" => $unique_code]);
} else {
    echo json_encode(["status"=>"error","message"=>"Failed"]);
}
?>