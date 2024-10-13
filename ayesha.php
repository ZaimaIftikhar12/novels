<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zy";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Check if form is submitted via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL to prevent SQL injection
    $sql = "SELECT * FROM z1 WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if ($password == $user['password']) {
            // Password is correct, set session and return success
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            echo json_encode(['success' => true]);
        } else {
            // Invalid password
            echo json_encode(['success' => false, 'message' => 'Incorrect email or password.']);
        }
    } else {
        // User not found
        echo json_encode(['success' => false, 'message' => 'Incorrect email or password.']);
    }

    $stmt->close();
}

$conn->close();
exit();
