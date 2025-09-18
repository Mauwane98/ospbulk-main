<?php
require_once 'includes/header.php';
require_once 'config/db.php';
require_once 'admin/includes/functions.php';

session_start();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate inputs
    if (empty($email) || empty($password)) {
        $message = 'Email and password are required.';
    } else {
        $stmt = $conn->prepare("SELECT id, password FROM admins WHERE email = ?");
        if ($stmt === false) {
            $message = 'Database error: ' . $conn->error;
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $admin = $result->fetch_assoc();
            $stmt->close();

            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                header("Location: admin/dashboard.php");
                exit;
            } else {
                $message = 'Invalid email or password.';
            }
        }
    }
}
?>

<div class="flex items-center justify-center min-h-screen bg-[#f5f5f0]">
    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-lg">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-deep-charcoal">Admin Login</h1>
            <p class="text-gray-500">Sign in to your account</p>
        </div>
        <?php if ($message): ?>
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <form action="login.php" method="POST" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" id="email" name="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-burnt-orange focus:ring focus:ring-burnt-orange focus:ring-opacity-50">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-burnt-orange focus:ring focus:ring-burnt-orange focus:ring-opacity-50">
            </div>
            <button type="submit" class="w-full bg-burnt-orange text-white py-3 px-4 rounded-full font-semibold hover:bg-[#e26a0a] transition-colors duration-300">
                Log In
            </button>
        </form>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
