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
$edit_category = null;

// Handle Add/Edit Category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $category_id = $_POST['category_id'] ?? null;

    if (empty($name)) {
        $message = '<div class="alert alert-danger">Category name cannot be empty.</div>';
    } else {
        if ($category_id) {
            // Update existing category
            $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $description, $category_id);
            if ($stmt->execute()) {
                $message = '<div class="alert alert-success">Category updated successfully!</div>';
            } else {
                $message = '<div class="alert alert-danger">Error updating category: ' . $stmt->error . '</div>';
            }
        } else {
            // Add new category
            $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $description);
            if ($stmt->execute()) {
                $message = '<div class="alert alert-success">Category added successfully!</div>';
            } else {
                $message = '<div class="alert alert-danger">Error adding category: ' . $stmt->error . '</div>';
            }
        }
        $stmt->close();
    }
}

// Handle Edit request
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $category_id_to_edit = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("i", $category_id_to_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $edit_category = $result->fetch_assoc();
    } else {
        $message = '<div class="alert alert-danger">Category not found.</div>';
    }
    $stmt->close();
}

// Handle Delete request
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $category_id_to_delete = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $category_id_to_delete);
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">Category deleted successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger">Error deleting category: ' . $stmt->error . '</div>';
    }
    $stmt->close();
}

// Fetch all categories for display
$categories_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");

?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; // Assuming you have a sidebar for admin navigation ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <h1 class="mt-4">Manage Categories</h1>
            <?php echo $message; ?>

            <div class="card mb-4" data-animation-class="animate__fadeInUp">
                <div class="card-header">
                    <?php echo $edit_category ? 'Edit Category' : 'Add New Category'; ?>
                </div>
                <div class="card-body">
                    <form action="manage_categories.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <?php if ($edit_category): ?>
                            <input type="hidden" name="category_id" value="<?php echo $edit_category['id']; ?>">
                        <?php endif; ?>
                        <div class="mb-3 form-floating">
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($edit_category['name'] ?? ''); ?>" placeholder="Category Name" required>
                            <label for="name">Category Name</label>
                        </div>
                        <div class="mb-3 form-floating">
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Description"><?php echo htmlspecialchars($edit_category['description'] ?? ''); ?></textarea>
                            <label for="description">Description</label>
                        </div>
                        <button type="submit" class="btn btn-primary"><?php echo $edit_category ? 'Update Category' : 'Add Category'; ?></button>
                        <?php if ($edit_category): ?>
                            <a href="manage_categories.php" class="btn btn-secondary">Cancel Edit</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="card" data-animation-class="animate__fadeInUp">
                <div class="card-header">
                    All Categories
                </div>
                <div class="card-body">
                    <?php if ($categories_result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($category = $categories_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $category['id']; ?></td>
                                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                                            <td><?php echo htmlspecialchars(substr($category['description'], 0, 100)); ?>...</td>
                                            <td>
                                                <a href="manage_categories.php?action=edit&id=<?php echo $category['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                                <a href="manage_categories.php?action=delete&id=<?php echo $category['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this category? Products in this category will have their category set to NULL.');">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>No categories found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>