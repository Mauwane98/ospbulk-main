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
$edit_testimonial = null;
$upload_dir = '../assets/uploads/';

// Handle Add/Edit Testimonial
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $author_name = trim($_POST['author_name']);
    $author_title = trim($_POST['author_title']);
    $content = trim($_POST['content']);
    $testimonial_id = $_POST['testimonial_id'] ?? null;

    // Image upload handling
    $image_name = null;
    if (isset($_FILES['author_image']) && $_FILES['author_image']['error'] === UPLOAD_ERR_OK) {
        $image_file = $_FILES['author_image'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5 MB

        if (!in_array($image_file['type'], $allowed_types)) {
            $message = '<div class="alert alert-danger">Invalid file type. Only JPG, PNG, GIF, WEBP images are allowed.</div>';
            $image_name = null;
        } elseif ($image_file['size'] > $max_size) {
            $message = '<div class="alert alert-danger">Sorry, your file is too large.</div>';
            $image_name = null;
        } else {
            $image_name = uniqid() . "_" . basename($image_file["name"]); // Generate unique filename
            $target_file = $upload_dir . $image_name;

            if (!move_uploaded_file($image_file["tmp_name"], $target_file)) {
                $message = '<div class="alert alert-danger">Sorry, there was an error uploading your file.</div>';
                $image_name = null;
            }
        }
    } elseif ($testimonial_id && empty($_FILES['author_image']['name'])) {
        // If editing and no new image is uploaded, retain existing image
        $stmt_get_image = $conn->prepare("SELECT image FROM testimonials WHERE id = ?");
        $stmt_get_image->bind_param("i", $testimonial_id);
        $stmt_get_image->execute();
        $result_get_image = $stmt_get_image->get_result();
        $existing_image = $result_get_image->fetch_assoc();
        $image_name = $existing_image['image'] ?? null;
        $stmt_get_image->close();
    }

    if (empty($author_name) || empty($content)) {
        $message = '<div class="alert alert-danger">Author name and content cannot be empty.</div>';
    } else {
        if ($testimonial_id) {
            // Update existing testimonial
            if ($image_name) {
                $stmt = $conn->prepare("UPDATE testimonials SET author_name = ?, author_title = ?, content = ?, image = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $author_name, $author_title, $content, $image_name, $testimonial_id);
            } else {
                $stmt = $conn->prepare("UPDATE testimonials SET author_name = ?, author_title = ?, content = ? WHERE id = ?");
                $stmt->bind_param("sssi", $author_name, $author_title, $content, $testimonial_id);
            }
            if ($stmt->execute()) {
                $message = '<div class="alert alert-success">Testimonial updated successfully!</div>';
            } else {
                $message = '<div class="alert alert-danger">Error updating testimonial: ' . $stmt->error . '</div>';
            }
        } else {
            // Add new testimonial
            $stmt = $conn->prepare("INSERT INTO testimonials (author_name, author_title, content, image) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $author_name, $author_title, $content, $image_name);
            if ($stmt->execute()) {
                $message = '<div class="alert alert-success">Testimonial added successfully!</div>';
            } else {
                $message = '<div class="alert alert-danger">Error adding testimonial: ' . $stmt->error . '</div>';
            }
        }
        $stmt->close();
    }
}

// Handle Edit request
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $testimonial_id_to_edit = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM testimonials WHERE id = ?");
    $stmt->bind_param("i", $testimonial_id_to_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $edit_testimonial = $result->fetch_assoc();
    } else {
        $message = '<div class="alert alert-danger">Testimonial not found.</div>';
    }
    $stmt->close();
}

// Handle Delete request
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $testimonial_id_to_delete = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM testimonials WHERE id = ?");
    $stmt->bind_param("i", $testimonial_id_to_delete);
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">Testimonial deleted successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger">Error deleting testimonial: ' . $stmt->error . '</div>';
    }
    $stmt->close();
}

// Fetch all testimonials for display
$testimonials_result = $conn->query("SELECT * FROM testimonials ORDER BY created_at DESC");

?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; // Assuming you have a sidebar for admin navigation ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <h1 class="mt-4">Manage Testimonials</h1>
            <?php echo $message; ?>

            <div class="card mb-4">
                <div class="card-header">
                    <?php echo $edit_testimonial ? 'Edit Testimonial' : 'Add New Testimonial'; ?>
                </div>
                <div class="card-body">
                    <form action="manage_testimonials.php" method="POST" enctype="multipart/form-data">
                        <?php if ($edit_testimonial): ?>
                            <input type="hidden" name="testimonial_id" value="<?php echo $edit_testimonial['id']; ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label for="author_name" class="form-label">Author Name</label>
                            <input type="text" class="form-control" id="author_name" name="author_name" value="<?php echo htmlspecialchars($edit_testimonial['author_name'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="author_title" class="form-label">Author Title (e.g., Farmer, Partner NGO)</label>
                            <input type="text" class="form-control" id="author_title" name="author_title" value="<?php echo htmlspecialchars($edit_testimonial['author_title'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea class="form-control" id="content" name="content" rows="5" required><?php echo htmlspecialchars($edit_testimonial['content'] ?? ''); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="author_image" class="form-label">Author Image</label>
                            <input type="file" class="form-control" id="author_image" name="author_image" accept="image/*">
                            <?php if ($edit_testimonial && $edit_testimonial['image']): ?>
                                <small class="form-text text-muted">Current Image: <a href="../assets/uploads/<?php echo htmlspecialchars($edit_testimonial['image']); ?>" target="_blank"><?php echo htmlspecialchars($edit_testimonial['image']); ?></a></small>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary"><?php echo $edit_testimonial ? 'Update Testimonial' : 'Add Testimonial'; ?></button>
                        <?php if ($edit_testimonial): ?>
                            <a href="manage_testimonials.php" class="btn btn-secondary">Cancel Edit</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    All Testimonials
                </div>
                <div class="card-body">
                    <?php if ($testimonials_result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Author</th>
                                        <th>Title</th>
                                        <th>Content</th>
                                        <th>Image</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($testimonial = $testimonials_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $testimonial['id']; ?></td>
                                            <td><?php echo htmlspecialchars($testimonial['author_name']); ?></td>
                                            <td><?php echo htmlspecialchars($testimonial['author_title']); ?></td>
                                            <td><?php echo htmlspecialchars(substr($testimonial['content'], 0, 100)); ?>...</td>
                                            <td>
                                                <?php if (!empty($testimonial['image'])): ?>
                                                    <img src="../assets/uploads/<?php echo htmlspecialchars($testimonial['image']); ?>" alt="<?php echo htmlspecialchars($testimonial['author_name']); ?>" width="50">
                                                <?php else: ?>
                                                    No Image
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="manage_testimonials.php?action=edit&id=<?php echo $testimonial['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                                <a href="manage_testimonials.php?action=delete&id=<?php echo $testimonial['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this testimonial?');">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>No testimonials found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>