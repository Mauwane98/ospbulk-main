<?php
session_start();
require_once '../config/db.php';
require_once 'includes/header.php';
require_once 'includes/functions.php';
generate_csrf_token(); // Generate CSRF token

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = '';

// Handle inquiry deletion
if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $message = '<div class="alert alert-danger">Error: Invalid CSRF token.</div>';
    } else {
        $inquiry_id_to_delete = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM inquiries WHERE id = ?");
        $stmt->bind_param("i", $inquiry_id_to_delete);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Inquiry deleted successfully!</div>';
        } else {
            $message = '<div class="alert alert-danger">Error deleting inquiry: ' . $stmt->error . '</div>';
        }
        $stmt->close();
    }
}

// Fetch all inquiries for display
$inquiries_result = $conn->query("SELECT * FROM inquiries ORDER BY created_at DESC");

?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; // Assuming you have a sidebar for admin navigation ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <h1 class="mt-4">Manage Inquiries</h1>
            <?php echo $message; ?>

            <div class="card" data-animation-class="animate__fadeInUp">
                <div class="card-header">
                    All Inquiries
                </div>
                <div class="card-body">
                    <?php if ($inquiries_result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Subject</th>
                                        <th>Message</th>
                                        <th>Received At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($inquiry = $inquiries_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $inquiry['id']; ?></td>
                                            <td><?php echo htmlspecialchars($inquiry['name']); ?></td>
                                            <td><?php echo htmlspecialchars($inquiry['email']); ?></td>
                                            <td><?php echo htmlspecialchars($inquiry['subject']); ?></td>
                                            <td><?php echo htmlspecialchars(substr($inquiry['message'], 0, 100)); ?>...</td>
                                            <td><?php echo date("Y-m-d H:i", strtotime($inquiry['created_at'])); ?></td>
                                            <td>
                                                <form action="manage_inquiries.php" method="POST" style="display:inline;">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $inquiry['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this inquiry?');"><i class="bi bi-trash"></i> Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>No inquiries found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>