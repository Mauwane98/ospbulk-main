<?php
require_once 'includes/header.php';
require_once '../config/db.php';
require_once 'includes/functions.php'; // Include the functions file

// Check if the user is logged in and is an admin (you might want more robust role checking here)
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true){
    header("location: index.php");
    exit;
}

$message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $error_message = "Error: Invalid CSRF token.";
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
            // Check if username or email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $error_message = "Username or Email already exists.";
            }
            $stmt->close();

            if (empty($error_message)) {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Prepare and bind
                $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $hashed_password, $email);

                // Execute the statement
                if ($stmt->execute()) {
                    $message = "Admin user '" . htmlspecialchars($username) . "' created successfully.";
                } else {
                    $error_message = "Error creating admin user: " . $stmt->error;
                }

                $stmt->close();
            }
        }
    }
}

?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Create New Admin User</h1>
</div>

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

<div class="card" data-animation-class="animate__fadeInUp">
    <div class="card-body">
        <form action="create_admin.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="mb-3 form-floating">
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                <label for="username">Username</label>
            </div>
            <div class="mb-3 form-floating">
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                <label for="email">Email</label>
            </div>
            <div class="mb-3 form-floating position-relative">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <label for="password">Password</label>
                <span class="position-absolute end-0 top-50 translate-middle-y pe-3" style="cursor: pointer;" onclick="togglePasswordVisibility('password', 'togglePasswordIconAdmin')">
                    <i class="far fa-eye" id="togglePasswordIconAdmin"></i>
                </span>
            </div>
            <button type="submit" class="btn btn-primary">Create Admin</button>
        </form>
    </div>
</div>

<?php
require_once 'includes/footer.php';
$conn->close();
?>
