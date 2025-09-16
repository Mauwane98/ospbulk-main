<?php
require_once 'config/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $message = '<div class="alert alert-danger">Email cannot be empty.</div>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<div class="alert alert-danger">Invalid email format.</div>';
    } else {
        // Check if email already subscribed
        $stmt = $conn->prepare("SELECT id FROM subscribers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = '<div class="alert alert-warning">This email is already subscribed.</div>';
        } else {
            // Insert new subscriber
            $stmt = $conn->prepare("INSERT INTO subscribers (email) VALUES (?)");
            $stmt->bind_param("s", $email);

            if ($stmt->execute()) {
                $message = '<div class="alert alert-success">Thank you for subscribing!</div>';
            } else {
                $message = '<div class="alert alert-danger">Error subscribing: ' . $stmt->error . '</div>';
            }
        }
        $stmt->close();
    }
}

// Redirect back to the page where the form was submitted, or a dedicated success page
// For simplicity, we'll redirect to index.php and pass the message via session or GET param
// Using session for better practice
session_start();
$_SESSION['subscription_message'] = $message;
header("Location: index.php"); // Redirect to homepage
exit();
?>