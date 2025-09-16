<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");

$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "quiz_platform";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status"=>"error","message"=>"DB connection failed"]);
    exit;
}
$conn->set_charset("utf8mb4");

function require_admin() {
    global $conn;
    if (!isset($_SESSION['admin_id'])) {
        http_response_code(401);
        echo json_encode(["status"=>"error","message"=>"Unauthorized"]);
        exit;
    }
    $stmt = $conn->prepare("SELECT id, name, role FROM admins WHERE id=?");
    $stmt->bind_param("i", $_SESSION['admin_id']);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    if (!$row) {
        http_response_code(401);
        echo json_encode(["status"=>"error","message"=>"Unauthorized"]);
        exit;
    }
    return $row;
}
?>
