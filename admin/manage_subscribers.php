<?php
session_start();
require_once '../config/db.php';
require_once 'includes/header.php';
require_once 'includes/functions.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = '';

// Handle subscriber deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $subscriber_id_to_delete = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM subscribers WHERE id = ?");
    $stmt->bind_param("i", $subscriber_id_to_delete);
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">Subscriber deleted successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger">Error deleting subscriber: ' . $stmt->error . '</div>';
    }
    $stmt->close();
}

// Fetch all subscribers for display
$subscribers_result = $conn->query("SELECT * FROM subscribers ORDER BY subscribed_at DESC");

?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; // Assuming you have a sidebar for admin navigation ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <h1 class="mt-4">Manage Subscribers</h1>
            <?php echo $message; ?>

            <div class="card">
                <div class="card-header">
                    All Subscribers
                </div>
                <div class="card-body">
                    <?php if ($subscribers_result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Email</th>
                                        <th>Subscribed At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($subscriber = $subscribers_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $subscriber['id']; ?></td>
                                            <td><?php echo htmlspecialchars($subscriber['email']); ?></td>
                                            <td><?php echo date("Y-m-d H:i", strtotime($subscriber['subscribed_at'])); ?></td>
                                            <td>
                                                <a href="manage_subscribers.php?action=delete&id=<?php echo $subscriber['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this subscriber?');">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>No subscribers found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>