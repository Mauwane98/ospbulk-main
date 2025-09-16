<?php
require_once 'includes/header.php';
require_once '../config/db.php';
require_once 'includes/functions.php'; // Include the functions file

$message = '';
$error_message = '';

// Fetch existing settings
$settings = [];
$setting_keys = ['company_name', 'slogan', 'contact_email', 'contact_phone', 'address'];
foreach ($setting_keys as $key) {
    $settings[$key] = get_setting($conn, $key, '');
}

// Handle form submission for updating settings
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $error_message = "Invalid CSRF token.";
    } else {
        $company_name = trim($_POST['company_name']);
        $slogan = trim($_POST['slogan']);
        $contact_email = trim($_POST['contact_email']);
        $contact_phone = trim($_POST['contact_phone']);
        $address = trim($_POST['address']);

        // Input Validation for Settings
        if (empty($company_name)) {
            $error_message = "Company Name is required.";
        } elseif (strlen($company_name) > 255) {
            $error_message = "Company Name cannot exceed 255 characters.";
        } elseif (!empty($slogan) && strlen($slogan) > 255) {
            $error_message = "Slogan cannot exceed 255 characters.";
        } elseif (empty($contact_email) || !filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Valid Contact Email is required.";
        } else {
            // Sanitize and update settings in the database
            set_setting($conn, 'company_name', htmlspecialchars($company_name, ENT_QUOTES, 'UTF-8'));
            set_setting($conn, 'slogan', htmlspecialchars($slogan, ENT_QUOTES, 'UTF-8'));
            set_setting($conn, 'contact_email', htmlspecialchars($contact_email, ENT_QUOTES, 'UTF-8'));
            set_setting($conn, 'contact_phone', htmlspecialchars($contact_phone, ENT_QUOTES, 'UTF-8'));
            set_setting($conn, 'address', htmlspecialchars($address, ENT_QUOTES, 'UTF-8'));

            $message = "Settings updated successfully!";
            // Re-fetch settings to display updated values
            foreach ($setting_keys as $key) {
                $settings[$key] = get_setting($conn, $key, '');
            }
        }
    }
}

// Handle form submission for changing password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $error_message = "Invalid CSRF token.";
    } else {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_new_password = $_POST['confirm_new_password'];

        // Input Validation for Password Change
        if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
            $error_message = "All password fields are required.";
        } elseif ($new_password !== $confirm_new_password) {
            $error_message = "New password and confirm password do not match.";
        } elseif (strlen($new_password) < 8) {
            $error_message = "New password must be at least 8 characters long.";
        } elseif (!preg_match("/[A-Z]/", $new_password) || !preg_match("/[a-z]/", $new_password) || !preg_match("/[0-9]/", $new_password) || !preg_match("/\W/", $new_password)) {
            $error_message = "New password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.";
        } else {
            // Fetch user's current hashed password from the database
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $_SESSION['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if ($user && password_verify($current_password, $user['password'])) {
                if (change_user_password($conn, $_SESSION['id'], $new_password)) {
                    $message = "Password changed successfully!";
                } else {
                    $error_message = "Error changing password.";
                }
            } else {
                $error_message = "Current password is incorrect.";
            }
        }
    }
}

?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Site Settings</h1>
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

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                Company Information
            </div>
            <div class="card-body">
                <form action="settings.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="companyName" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="companyName" name="company_name" value="<?php echo htmlspecialchars($settings['company_name']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="slogan" class="form-label">Slogan</label>
                        <input type="text" class="form-control" id="slogan" name="slogan" value="<?php echo htmlspecialchars($settings['slogan']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="contactEmail" class="form-label">Contact Email</label>
                        <input type="email" class="form-control" id="contactEmail" name="contact_email" value="<?php echo htmlspecialchars($settings['contact_email']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="contactPhone" class="form-label">Contact Phone</label>
                        <input type="text" class="form-control" id="contactPhone" name="contact_phone" value="<?php echo htmlspecialchars($settings['contact_phone']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($settings['address']); ?></textarea>
                    </div>
                    <button type="submit" name="update_settings" class="btn btn-primary">Update Settings</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                Admin User Management
            </div>
            <div class="card-body">
                <p>Manage administrator accounts for the panel.</p>
                <a href="manage_users.php" class="btn btn-success mb-3">Manage Admin Users</a>
                
                <h5>Change Your Password</h5>
                <form action="settings.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="newPassword" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmNewPassword" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirmNewPassword" name="confirm_new_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-warning">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
$conn->close();
?>
