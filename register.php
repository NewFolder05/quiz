<?php
header("Content-Type: application/json; charset=UTF-8");

// Database config
$host = "localhost";
$user = "root";
$pass = "";
$db   = "quiz_platform";

// Connect to DB with error handling
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize inputs
    $name     = trim($_POST['name'] ?? "");
    $school   = trim($_POST['school'] ?? "");
    $section  = trim($_POST['section'] ?? "");
    $class    = trim($_POST['class'] ?? "");
    $email    = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";
    $confirm  = $_POST['confirmPassword'] ?? "";

    // Basic validation
    if (empty($name) || empty($school) || empty($section) || empty($class) || empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email format"]);
        exit;
    }

    if ($password !== $confirm) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match"]);
        exit;
    }

    if (strlen($password) < 6) {
        echo json_encode(["status" => "error", "message" => "Password must be at least 6 characters long"]);
        exit;
    }

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email already registered"]);
        $check->close();
        exit;
    }
    $check->close();

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user securely with prepared statement
    $stmt = $conn->prepare("INSERT INTO users (name, school, section, class, email, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $school, $section, $class, $email, $hashedPassword);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Registration successful"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Server error, please try again later"]);
    }

    $stmt->close();
}

$conn->close();
?>
