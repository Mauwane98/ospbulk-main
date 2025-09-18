<?php
require_once 'includes/header.php';
require_once 'config/db.php';
require_once 'admin/includes/functions.php';

$message = '';
$isSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = 'Please fill in all the required fields.';
        $isSuccess = false;
    } elseif ($password !== $confirm_password) {
        $message = 'Passwords do not match.';
        $isSuccess = false;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Invalid email format.';
        $isSuccess = false;
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare and execute the query
        $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        if ($stmt === false) {
            $message = 'Database error: ' . $conn->error;
            $isSuccess = false;
        } else {
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            if ($stmt->execute()) {
                $message = 'Registration successful! You can now <a href="login.php" class="font-bold underline">log in</a>.';
                $isSuccess = true;
            } else {
                $message = 'Registration failed. The email may already be in use.';
                $isSuccess = false;
            }
            $stmt->close();
        }
    }
}
?>

<div class="flex items-center justify-center min-h-screen bg-[#f5f5f0]">
    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-lg">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-deep-charcoal">Register Account</h1>
            <p class="text-gray-500">Create a new account</p>
        </div>
        <?php if ($message): ?>
            <div class="p-4 mb-4 text-sm rounded-lg <?php echo $isSuccess ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100'; ?>" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form action="register.php" method="POST" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" id="username" name="username" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-burnt-orange focus:ring focus:ring-burnt-orange focus:ring-opacity-50">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" id="email" name="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-burnt-orange focus:ring focus:ring-burnt-orange focus:ring-opacity-50">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-burnt-orange focus:ring focus:ring-burnt-orange focus:ring-opacity-50">
            </div>
            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-burnt-orange focus:ring focus:ring-burnt-orange focus:ring-opacity-50">
            </div>
            <button type="submit" class="w-full bg-burnt-orange text-white py-3 px-4 rounded-full font-semibold hover:bg-[#e26a0a] transition-colors duration-300">
                Register
            </button>
        </form>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
