<?php
require_once '../config/db.php';

$message = '';
$error_message = '';

// Check if any admin users already exist before processing POST or showing form
$result = $conn->query("SELECT COUNT(*) as count FROM users");
$row = $result->fetch_assoc();
$admin_exists = ($row['count'] > 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($admin_exists) {
        $error_message = "An admin user already exists. Initial setup cannot be performed again.";
    } else {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $email = trim($_POST['email']);

        // Input Validation
        if (empty($username)) {
            $error_message = "Username is required.";
        } elseif (strlen($username) > 255) {
            $error_message = "Username cannot exceed 255 characters.";
        } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Valid Email is required.";
        } elseif (empty($password)) {
            $error_message = "Password is required.";
        } elseif (strlen($password) < 8) {
            $error_message = "Password must be at least 8 characters long.";
        } elseif (!preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[0-9]/", $password) || !preg_match("/\W/", $password)) {
            $error_message = "Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.";
        } else {
            // Check if username or email already exists (shouldn't happen if $admin_exists is false, but good for robustness)
            $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt_check->bind_param("ss", $username, $email);
            $stmt_check->execute();
            $stmt_check->store_result();
            if ($stmt_check->num_rows > 0) {
                $error_message = "Username or Email already exists.";
            }
            $stmt_check->close();

            if (empty($error_message)) {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Prepare and bind
                $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $hashed_password, $email);

                // Execute the statement
                if ($stmt->execute()) {
                    $message = "Initial admin user '" . htmlspecialchars($username) . "' created successfully. You can now log in.";
                    $admin_exists = true; // Update status after successful creation
                } else {
                    $error_message = "Error creating initial admin user: " . $stmt->error;
                }

                $stmt->close();
            }
        }
    }
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Initial Admin Setup - OSP Bulk (Pty) Ltd</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title text-center">Initial Admin Setup</h3>
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!$admin_exists): // Only show form if no admin exists ?>
                            <form action="initial_setup.php" method="post">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Create Initial Admin</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-info" role="alert">
                                An admin user already exists. Please log in to the <a href="index.php">admin panel</a>.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>