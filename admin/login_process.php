<?php
session_start();

require_once '../config/db.php';
require_once 'includes/functions.php'; // Include the functions file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        // Invalid CSRF token, redirect to login with an error
        header("location: index.php?error=csrf");
        exit();
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct, so start a new session
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            // Redirect to admin dashboard
            header("location: dashboard.php");
        } else {
            // Password is not valid
            header("location: index.php?error=1");
        }
    } else {
        // Username doesn't exist
        header("location: index.php?error=1");
    }

    $stmt->close();
    $conn->close();
} else {
    // Not a POST request
    header("location: index.php");
    exit();
}
?>
