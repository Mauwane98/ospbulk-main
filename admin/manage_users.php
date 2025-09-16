<?php
require_once 'includes/header.php';
require_once '../config/db.php';
require_once 'includes/functions.php'; // Include the functions file

$errors = [];
$success_message = '';

// Handle user deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Prevent self-deletion
    if ($id == $_SESSION['id']) {
        $errors[] = "You cannot delete your own account.";
    } elseif (filter_var($id, FILTER_VALIDATE_INT)) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $success_message = "User deleted successfully!";
        } else {
            $errors[] = "Error deleting user: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errors[] = "Invalid user ID for deletion.";
    }
    // Redirect to clear GET parameters
    header("location: manage_users.php");
    exit;
}

// Pagination setup
$users_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $users_per_page;

// Get total number of users
$total_users_result = $conn->query("SELECT COUNT(*) as total FROM users");
$total_users = $total_users_result->fetch_assoc()['total'];
$total_pages = ceil($total_users / $users_per_page);

// Fetch users for current page
$result = $conn->query("SELECT id, username, email, created_at FROM users ORDER BY created_at DESC LIMIT $users_per_page OFFSET $offset");

?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Admin Users</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="create_admin.php" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Admin User
        </a>
    </div>
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

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td>
                        <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i> Edit</a>
                        <?php if ($row['id'] != $_SESSION['id']): // Prevent self-deletion ?>
                            <a href="manage_users.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');"><i class="bi bi-trash"></i> Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<nav aria-label="Page navigation example">
  <ul class="pagination justify-content-center">
    <li class="page-item <?php if($current_page <= 1){ echo 'disabled'; } ?>">
      <a class="page-link" href="<?php if($current_page <= 1){ echo '#'; } else { echo "?page=".($current_page - 1); } ?>">Previous</a>
    </li>
    <?php for($i = 1; $i <= $total_pages; $i++): ?>
      <li class="page-item <?php if($current_page == $i){ echo 'active'; } ?>"><a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
    <?php endfor; ?>
    <li class="page-item <?php if($current_page >= $total_pages){ echo 'disabled'; } ?>">
      <a class="page-link" href="<?php if($current_page >= $total_pages){ echo '#'; } else { echo "?page=".($current_page + 1); } ?>">Next</a>
    </li>
  </ul>
</nav>

<?php
require_once 'includes/footer.php';
$conn->close();
?>
