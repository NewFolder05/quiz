<?php
session_start();
require_once "db_config.php";

header("Content-Type: application/json; charset=UTF-8");

$user_id = $_SESSION['user_id'] ?? 0;
$code = trim($_POST['code'] ?? '');

if ($user_id <= 0) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

if (strlen($code) !== 6) {
    echo json_encode(["status" => "error", "message" => "Invalid code format"]);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT id, section, class_from, class_to FROM events WHERE unique_code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $res = $stmt->get_result();
    $event = $res->fetch_assoc();
    $stmt->close();

    if (!$event) {
        echo json_encode(["status" => "error", "message" => "Event not found with this code"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT section, class FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();
    $stmt->close();

    if (!$user || $user['section'] !== $event['section'] || $user['class'] < $event['class_from'] || $user['class'] > $event['class_to']) {
        echo json_encode(["status" => "error", "message" => "You are not eligible to join this event"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT id FROM user_events WHERE user_id = ? AND event_id = ?");
    $stmt->bind_param("ii", $user_id, $event['id']);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "You have already joined this event"]);
        exit;
    }
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO user_events (user_id, event_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $event['id']);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Joined event successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to join event"]);
    }
    $stmt->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Server error: " . $e->getMessage()]);
}

$conn->close();
?>