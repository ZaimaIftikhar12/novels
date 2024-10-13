<?php
// Connection details
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "zy"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string(trim($_POST['email']));
    $newPassword = $conn->real_escape_string(trim($_POST['new_password']));
    $confirmPassword = $conn->real_escape_string(trim($_POST['confirm_password']));

    // Check if new password and confirm password match
    if ($newPassword !== $confirmPassword) {
        $response['success'] = false;
        $response['message'] = "Passwords do not match.";
        echo json_encode($response);
        exit();
    }

    // Check if email exists
    $sql = "SELECT * FROM z1 WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update the password if email exists
        $updateSql = "UPDATE z1 SET password = ? WHERE email = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ss", $newPassword, $email);

        if ($updateStmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Password reset successfully.";
        } else {
            $response['success'] = false;
            $response['message'] = "Error updating password. Please try again.";
        }
    } else {
        // Email not found
        $response['success'] = false;
        $response['message'] = "Email not found.";
    }

    $stmt->close();
    $updateStmt->close();
    $conn->close();

    echo json_encode($response);
}
?>
