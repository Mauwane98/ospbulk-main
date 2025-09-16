<?php
require_once 'includes/header.php';
require_once '../config/db.php';
require_once 'includes/functions.php'; // Include the functions file

// Fetch categories for the dropdown
$categories_result = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row;
}

$errors = [];
$success_message = '';
$upload_dir = '../assets/uploads/';

$product_id = $_GET['id'] ?? null;
if (!$product_id) {
    header("location: products.php");
    exit;
}

// Fetch the product details before processing POST (to get current image name)
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    header("location: products.php");
    exit;
}

// Handle form submission for updating a product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $errors[] = "Invalid CSRF token.";
    }

    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $is_service = isset($_POST['is_service']) ? 1 : 0;
    $current_image = $product['image']; // Get current image from fetched data
    $new_image_name = $current_image; // Assume no change unless new file uploaded

    // Input Validation
    if (empty($name)) {
        $errors[] = "Name is required.";
    } elseif (strlen($name) > 255) {
        $errors[] = "Name cannot exceed 255 characters.";
    }

    if (empty($description)) {
        $errors[] = "Description is required.";
    }

    // Handle image upload if a new file is provided
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $image_file = $_FILES['product_image'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5 MB

        if (!in_array($image_file['type'], $allowed_types)) {
            $errors[] = "Invalid file type. Only JPG, PNG, GIF, WEBP images are allowed.";
        }
        if ($image_file['size'] > $max_size) {
            $errors[] = "File size exceeds the maximum limit of 5MB.";
        }

        if (empty($errors)) {
            $new_image_name = uniqid() . "_" . basename($image_file["name"]); // Generate unique filename
            $target_file = $upload_dir . $new_image_name;

            if (move_uploaded_file($image_file["tmp_name"], $target_file)) {
                // Delete old image if a new one is uploaded and old one exists
                if ($current_image && file_exists($upload_dir . $current_image)) {
                    unlink($upload_dir . $current_image);
                }
            } else {
                $errors[] = "Error moving uploaded file.";
                $new_image_name = $current_image; // Revert to current image name if move fails
            }
        }
    } elseif (isset($_FILES['product_image']) && $_FILES['product_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $errors[] = "File upload error: " . $_FILES['product_image']['error'];
    }

    // If no validation errors, proceed with update
    if (empty($errors)) {
        // Sanitize inputs before updating DB
        $sanitized_name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $sanitized_description = $description; // Keep HTML from TinyMCE

        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, is_service = ?, image = ?, category_id = ? WHERE id = ?");
        $stmt->bind_param("ssisii", $sanitized_name, $sanitized_description, $is_service, $new_image_name, $category_id, $product_id);
        
        if ($stmt->execute()) {
            $success_message = "Product/Service updated successfully!";
        } else {
            $errors[] = "Error updating product/service: " . $stmt->error;
        }
        $stmt->close();

        // Re-fetch product details to display updated values after successful update
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();
    }
}

?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Product or Service</h1>
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

<div class="card" data-animation-class="animate__fadeInUp">
    <div class="card-body">
        <form action="edit_product.php?id=<?php echo $product_id; ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="mb-3 form-floating">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" placeholder="Name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>


            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_service" name="is_service" <?php echo $product['is_service'] ? 'checked' : ''; ?>>
                <label class="form-check-label" for="is_service">Is this a service?</label>
            </div>
            <div class="mb-3">
                <label for="productImage" class="form-label">Product Image</label>
                <?php if ($product['image']): ?>
                    <div class="mb-2">
                        <img src="<?php echo $upload_dir . htmlspecialchars($product['image']); ?>" alt="Current Image" style="max-width: 200px; height: auto;">
                    </div>
                <?php endif; ?>
                <input type="file" class="form-control" id="productImage" name="product_image" accept="image/*">
                <small class="form-text text-muted">Leave blank to keep current image.</small>
            </div>
            <button type="submit" name="update_product" class="btn btn-primary">Update Product/Service</button>
            <a href="products.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: '#description',
    plugins: 'advlist autolink lists link image charmap print preview anchor',
    toolbar_mode: 'floating',
  });
</script>

<?php
require_once 'includes/footer.php';
$conn->close();
?>