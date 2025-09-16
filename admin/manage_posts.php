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
$edit_post = null;

// Handle Add/Edit Post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $post_id = $_POST['post_id'] ?? null;
    $user_id = $_SESSION['user_id']; // The logged-in admin is the author

    // Image upload handling
    $image_name = null;
    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../assets/uploads/";
        $image_name = basename($_FILES['post_image']['name']);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES['post_image']['tmp_name']);
        if ($check === false) {
            $message = '<div class="alert alert-danger">File is not an image.</div>';
            $image_name = null;
        } elseif ($_FILES['post_image']['size'] > 5000000) { // 5MB limit
            $message = '<div class="alert alert-danger">Sorry, your file is too large.</div>';
            $image_name = null;
        } elseif (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
            $message = '<div class="alert alert-danger">Sorry, only JPG, JPEG, PNG & GIF files are allowed.</div>';
            $image_name = null;
        } else {
            if (!move_uploaded_file($_FILES['post_image']['tmp_name'], $target_file)) {
                $message = '<div class="alert alert-danger">Sorry, there was an error uploading your file.</div>';
                $image_name = null;
            }
        }
    }

    if (empty($title) || empty($content)) {
        $message = '<div class="alert alert-danger">Title and content cannot be empty.</div>';
    } else {
        if ($post_id) {
            // Update existing post
            if ($image_name) {
                $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, image = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
                $stmt->bind_param("sssii", $title, $content, $image_name, $post_id, $user_id);
            } else {
                $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
                $stmt->bind_param("ssii", $title, $content, $post_id, $user_id);
            }
            if ($stmt->execute()) {
                $message = '<div class="alert alert-success">Post updated successfully!</div>';
            } else {
                $message = '<div class="alert alert-danger">Error updating post: ' . $stmt->error . '</div>';
            }
        } else {
            // Add new post
            $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content, image) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $user_id, $title, $content, $image_name);
            if ($stmt->execute()) {
                $message = '<div class="alert alert-success">Post added successfully!</div>';
            } else {
                $message = '<div class="alert alert-danger">Error adding post: ' . $stmt->error . '</div>';
            }
        }
        $stmt->close();
    }
}

// Handle Edit request
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $post_id_to_edit = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id_to_edit, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $edit_post = $result->fetch_assoc();
    } else {
        $message = '<div class="alert alert-danger">Post not found or you do not have permission to edit it.</div>';
    }
    $stmt->close();
}

// Handle Delete request
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $post_id_to_delete = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id_to_delete, $_SESSION['user_id']);
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">Post deleted successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger">Error deleting post: ' . $stmt->error . '</div>';
    }
    $stmt->close();
}

// Fetch all posts for display
$posts_result = $conn->query("SELECT p.*, u.username FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC");

?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; // Assuming you have a sidebar for admin navigation ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <h1 class="mt-4">Manage Posts</h1>
            <?php echo $message; ?>

            <div class="card mb-4">
                <div class="card-header">
                    <?php echo $edit_post ? 'Edit Post' : 'Add New Post'; ?>
                </div>
                <div class="card-body">
                    <form action="manage_posts.php" method="POST" enctype="multipart/form-data">
                        <?php if ($edit_post): ?>
                            <input type="hidden" name="post_id" value="<?php echo $edit_post['id']; ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($edit_post['title'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea class="form-control" id="content" name="content" rows="10" required><?php echo htmlspecialchars($edit_post['content'] ?? ''); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="post_image" class="form-label">Featured Image</label>
                            <input type="file" class="form-control" id="post_image" name="post_image" accept="image/*">
                            <?php if ($edit_post && $edit_post['image']): ?>
                                <small class="form-text text-muted">Current Image: <a href="../assets/uploads/<?php echo htmlspecialchars($edit_post['image']); ?>" target="_blank"><?php echo htmlspecialchars($edit_post['image']); ?></a></small>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary"><?php echo $edit_post ? 'Update Post' : 'Add Post'; ?></button>
                        <?php if ($edit_post): ?>
                            <a href="manage_posts.php" class="btn btn-secondary">Cancel Edit</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    All Posts
                </div>
                <div class="card-body">
                    <?php if ($posts_result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Image</th>
                                        <th>Published Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($post = $posts_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $post['id']; ?></td>
                                            <td><?php echo htmlspecialchars($post['title']); ?></td>
                                            <td><?php echo htmlspecialchars($post['username']); ?></td>
                                            <td>
                                                <?php if (!empty($post['image'])): ?>
                                                    <img src="../assets/uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" width="50">
                                                <?php else: ?>
                                                    No Image
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date("Y-m-d H:i", strtotime($post['published_at'])); ?></td>
                                            <td>
                                                <a href="manage_posts.php?action=edit&id=<?php echo $post['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                                <a href="manage_posts.php?action=delete&id=<?php echo $post['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>No posts found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>