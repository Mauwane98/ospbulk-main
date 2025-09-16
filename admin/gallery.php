<?php
require_once 'includes/header.php';
require_once '../config/db.php';
require_once 'includes/functions.php'; // Include the functions file

$upload_dir = '../assets/uploads/';
$errors = [];
$success_message = '';

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['gallery_image'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $errors[] = "Invalid CSRF token.";
    }

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $image_file = $_FILES['gallery_image'];

    // Input Validation
    if (empty($title)) {
        $errors[] = "Title is required.";
    } elseif (strlen($title) > 255) {
        $errors[] = "Title cannot exceed 255 characters.";
    }

    // File Upload Validation
    if ($image_file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "File upload error: " . $image_file['error'];
    } else {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5 MB

        if (!in_array($image_file['type'], $allowed_types)) {
            $errors[] = "Invalid file type. Only JPG, PNG, GIF, WEBP images are allowed.";
        }
        if ($image_file['size'] > $max_size) {
            $errors[] = "File size exceeds the maximum limit of 5MB.";
        }
    }

    // If no validation errors, proceed with upload and insertion
    if (empty($errors)) {
        $image_name = uniqid() . "_" . basename($image_file["name"]); // Generate unique filename
        $target_file = $upload_dir . $image_name;

        if (move_uploaded_file($image_file["tmp_name"], $target_file)) {
            // Sanitize inputs before inserting into DB
            $sanitized_title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
            $sanitized_description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');

            $stmt = $conn->prepare("INSERT INTO gallery (title, description, image) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $sanitized_title, $sanitized_description, $image_name);
            
            if ($stmt->execute()) {
                $success_message = "Image uploaded successfully!";
            } else {
                $errors[] = "Error uploading image to database: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $errors[] = "Error moving uploaded file.";
        }
    }
}

// Handle image deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Basic validation for ID
    if (filter_var($id, FILTER_VALIDATE_INT)) {
        // First, get the image filename to delete the file
        $stmt = $conn->prepare("SELECT image FROM gallery WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $image = $result->fetch_assoc();
        $stmt->close();

        if ($image) {
            // Delete the file
            if (file_exists($upload_dir . $image['image'])) {
                unlink($upload_dir . $image['image']);
            }

            // Delete the record from the database
            $stmt = $conn->prepare("DELETE FROM gallery WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $success_message = "Image deleted successfully!";
            } else {
                $errors[] = "Error deleting image from database: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $errors[] = "Invalid image ID for deletion.";
    }
    // Redirect to clear GET parameters
    header("location: gallery.php");
    exit;
}

// Pagination setup
$images_per_page = 9; // Display 9 images per page for a 3-column grid
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $images_per_page;

// Get total number of images
$total_images_result = $conn->query("SELECT COUNT(*) as total FROM gallery");
$total_images = $total_images_result->fetch_assoc()['total'];
$total_pages = ceil($total_images / $images_per_page);

// Fetch images for current page
$result = $conn->query("SELECT * FROM gallery ORDER BY created_at DESC LIMIT $images_per_page OFFSET $offset");

?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Gallery</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadImageModal">
            <i class="bi bi-plus-circle"></i> Upload New Image
        </button>
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

<div class="row" data-animation-class="animate__fadeInUp">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 rounded-3">
                <img src="<?php echo $upload_dir . htmlspecialchars($row['image']); ?>" class="card-img-top rounded-top-3" alt="<?php echo htmlspecialchars($row['title']); ?>" style="height: 200px; object-fit: cover;">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold"><?php echo htmlspecialchars($row['title']); ?></h5>
                    <p class="card-text flex-grow-1"><?php echo htmlspecialchars($row['description']); ?></p>
                    <div class="mt-auto">
                        <a href="gallery.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this image?');"><i class="bi bi-trash"></i> Delete</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
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

<!-- Upload Image Modal -->
<div class="modal fade" id="uploadImageModal" tabindex="-1" aria-labelledby="uploadImageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadImageModalLabel">Upload New Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="gallery.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="modal-body">
                    <div class="mb-3 form-floating">
                        <input type="text" class="form-control" id="imageTitle" name="title" placeholder="Title" required>
                        <label for="imageTitle">Title</label>
                    </div>
                    <div class="mb-3 form-floating">
                        <label for="imageDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="imageDescription" name="description" rows="3" placeholder="Description"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="galleryImage" class="form-label">Image</label>
                        <input type="file" class="form-control" id="galleryImage" name="gallery_image" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload Image</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
$conn->close();
?>
