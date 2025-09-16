<?php
session_start();
require_once 'includes/header.php';
require_once 'config/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = trim($_POST['username_or_email']);
    $password = $_POST['password'];

    if (empty($username_or_email) || empty($password)) {
        $message = '<div class="alert alert-danger">Please enter both username/email and password.</div>';
    } else {
        // Prepare a statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, username, password, email, role FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username_or_email, $username_or_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password'])) {
                // Password is correct, start a new session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                // Redirect user based on role
                if ($user['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: index.php"); // Redirect regular users to homepage or a user dashboard
                }
                exit();
            } else {
                $message = '<div class="alert alert-danger">Invalid username/email or password.</div>';
            }
        } else {
            $message = '<div class="alert alert-danger">Invalid username/email or password.</div>';
        }
        $stmt->close();
    }
}
?>

<main class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 rounded-lg mt-5" data-animation-class="animate__fadeInUp">
                <div class="card-header"><h3 class="text-center font-weight-light my-4">Login</h3></div>
                <div class="card-body">
                    <?php echo $message; ?>
                    <form action="login.php" method="POST">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="username_or_email" name="username_or_email" placeholder="Username or Email" required>
                            <label for="username_or_email">Username or Email</label>
                        </div>
                        <div class="form-floating mb-3 position-relative">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                            <label for="password">Password</label>
                            <span class="position-absolute end-0 top-50 translate-middle-y pe-3" style="cursor: pointer;" onclick="togglePasswordVisibility('password', 'togglePasswordIcon')">
                                <i class="far fa-eye" id="togglePasswordIcon"></i>
                            </span>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Login</button>
                        </div>
                        <div class="mt-3 text-center">
                            <a href="forgot_password.php">Forgot Password?</a>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3">
                    <div class="small">Don't have an account? <a href="register.php">Sign up!</a></div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
require_once 'includes/footer.php';
?>