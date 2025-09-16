<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");

$host = "localhost";
$user = "root";
$pass = "";
$db   = "quiz_platform";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";

    if (empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Email and password are required"]);
        exit;
    }

    // 🔹 First check in admins table
    $stmt = $conn->prepare("SELECT id, name, password, role FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $name, $hashedPassword, $role);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['admin_id'] = $id;
            $_SESSION['admin_name'] = $name;
            $_SESSION['admin_role'] = $role;

            echo json_encode([
                "status" => "success",
                "message" => "Admin login successful",
                "role" => $role
            ]);
            exit;
        }
    }
    $stmt->close();

    // 🔹 If not found in admins, check users table
    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $name, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;

            echo json_encode([
                "status" => "success",
                "message" => "User login successful",
                "role" => "user"
            ]);
            exit;
        }
    }

    echo json_encode(["status" => "error", "message" => "Invalid email or password"]);
}
$conn->close();
?>
