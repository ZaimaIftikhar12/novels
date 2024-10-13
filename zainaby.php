<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "zy"; // Your database name
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Check if form is submitted via AJAX
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajax'])) {
    $firstname = $conn->real_escape_string(trim($_POST['firstname']));
    $lastname = $conn->real_escape_string(trim($_POST['lastname']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $conn->real_escape_string(trim($_POST['password']));
    $confirmPassword = $conn->real_escape_string(trim($_POST['confirm_password']));

    // Check if passwords match
    if ($password !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
        exit();
    } elseif (strlen($password) > 10) {
        echo json_encode(['success' => false, 'message' => 'Password exceeds the limit of 10 characters.']);
        exit();
    } else {
        $checkEmailQuery = "SELECT * FROM z1 WHERE email = '$email'";
        $result = $conn->query($checkEmailQuery);

        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Email already registered!']);
            exit();
        } else {
            $sql = "INSERT INTO z1 (firstName, lastName, email, password) 
                    VALUES ('$firstname', '$lastname', '$email', '$password')";

            if ($conn->query($sql) === TRUE) {
                echo json_encode(['success' => true, 'message' => 'Registration successful!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
            }
        }
    }
}

$conn->close();
exit();
