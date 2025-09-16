<?php
require_once 'includes/header.php';
require_once '../config/db.php';
require_once 'includes/functions.php'; // Include the functions file

$errors = [];
$success_message = '';

$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    header("location: manage_users.php");
    exit;
}

// Handle form submission for updating a user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $errors[] = "Invalid CSRF token.";
    }

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    // Input Validation
    if (empty($username)) {
        $errors[] = "Username is required.";
    } elseif (strlen($username) > 255) {
        $errors[] = "Username cannot exceed 255 characters.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid Email is required.";
    }

    // Check if username or email already exists for another user
    $stmt_check = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
    $stmt_check->bind_param("ssi", $username, $email, $user_id);
    $stmt_check->execute();
    $stmt_check->store_result();
    if ($stmt_check->num_rows > 0) {
        $errors[] = "Username or Email already exists for another user.";
    }
    $stmt_check->close();

    // If no validation errors, proceed with update
    if (empty($errors)) {
        // Sanitize inputs before updating DB
        $sanitized_username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
        $sanitized_email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $sanitized_username, $sanitized_email, $user_id);
        
        if ($stmt->execute()) {
            $success_message = "User updated successfully!";
        } else {
            $errors[] = "Error updating user: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch the user details (after potential update)
$stmt = $conn->prepare("SELECT id, username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    header("location: manage_users.php");
    exit;
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit User</h1>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger" role="alert">
        <?php foreach ($errors as $error): ?>
            <p class="mb-0"><?php echo $error; ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($success_message)): ?>
    <div class="alert alert-success" role="alert">
        <?php echo $success_message; ?>
    </div>
<?php endif; ?>

<div class="card" data-animation-class="animate__fadeInUp">
    <div class="card-body">
        <form action="edit_user.php?id=<?php echo $user_id; ?>" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="mb-3 form-floating">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" placeholder="Username" required>
            </div>
            <div class="mb-3 form-floating">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" placeholder="Email" required>
            </div>
            <button type="submit" name="update_user" class="btn btn-primary">Update User</button>
            <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php
require_once 'includes/footer.php';
$conn->close();
?>
