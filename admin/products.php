<?php
require_once 'includes/header.php';
require_once '../config/db.php';
require_once 'includes/functions.php'; // Include the functions file

$errors = [];
$success_message = '';
$upload_dir = '../assets/uploads/';

// Fetch categories for the dropdown
$categories_result = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row;
}

// Handle form submission for adding a new product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $errors[] = "Invalid CSRF token.";
    }

    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $is_service = isset($_POST['is_service']) ? 1 : 0;
    $category_id = $_POST['category_id'] ?? null; // New: Get category ID
    $image_name = null; // Initialize image name

    // Input Validation
    if (empty($name)) {
        $errors[] = "Name is required.";
    } elseif (strlen($name) > 255) {
        $errors[] = "Name cannot exceed 255 characters.";
    }

    if (empty($description)) {
        $errors[] = "Description is required.";
    }

    // Handle image upload if a file is provided
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
            $image_name = uniqid() . "_" . basename($image_file["name"]); // Generate unique filename
            $target_file = $upload_dir . $image_name;

            if (!move_uploaded_file($image_file["tmp_name"], $target_file)) {
                $errors[] = "Error moving uploaded file.";
                $image_name = null; // Reset image name if move fails
            }
        }
    } elseif (isset($_FILES['product_image']) && $_FILES['product_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $errors[] = "File upload error: " . $_FILES['product_image']['error'];
    }

    // If no validation errors, proceed with insertion
    if (empty($errors)) {
        // Sanitize inputs before inserting into DB
        $sanitized_name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $sanitized_description = $description; // Keep HTML from TinyMCE

        $stmt = $conn->prepare("INSERT INTO products (name, description, is_service, image, category_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisi", $sanitized_name, $sanitized_description, $is_service, $image_name, $category_id);
        
        if ($stmt->execute()) {
            $success_message = "Product/Service added successfully!";
        } else {
            $errors[] = "Error adding product/service: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Handle deletion of a product
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Basic validation for ID
    if (filter_var($id, FILTER_VALIDATE_INT)) {
        // First, get the image filename to delete the file
        $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product_to_delete = $result->fetch_assoc();
        $stmt->close();

        if ($product_to_delete && $product_to_delete['image']) {
            // Delete the file from the uploads directory
            if (file_exists($upload_dir . $product_to_delete['image'])) {
                unlink($upload_dir . $product_to_delete['image']);
            }
        }

        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $success_message = "Product/Service deleted successfully!";
        } else {
            $errors[] = "Error deleting product/service: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errors[] = "Invalid product/service ID for deletion.";
    }
    // Redirect to clear GET parameters
    header("location: products.php");
    exit;
}

// Pagination setup
$products_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $products_per_page;

// Get total number of products
$total_products_result = $conn->query("SELECT COUNT(*) as total FROM products");
$total_products = $total_products_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $products_per_page);

// Fetch products for current page (and join with categories to display category name)
$result = $conn->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.is_service, p.name LIMIT $products_per_page OFFSET $offset");

?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Products & Services</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="bi bi-plus-circle"></i> Add New Product/Service
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

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Type</th>
                <th>Category</th> <!-- New Column -->
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars(substr($row['description'], 0, 100)) . (strlen($row['description']) > 100 ? '...' : ''); ?></td>
                    <td><?php echo $row['is_service'] ? 'Service' : 'Product'; ?></td>
                    <td><?php echo htmlspecialchars($row['category_name'] ?? 'N/A'); ?></td> <!-- Display Category -->
                    <td>
                        <?php if ($row['image']): ?>
                            <img src="<?php echo $upload_dir . htmlspecialchars($row['image']); ?>" alt="Product Image" style="width: 50px; height: 50px; object-fit: cover;">
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i> Edit</a>
                        <a href="products.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this item?');"><i class="bi bi-trash"></i> Delete</a>
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

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Add New Product or Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="products.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="productName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="productName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="productDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="productDescription" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="productCategory" class="form-label">Category</label>
                        <select class="form-select" id="productCategory" name="category_id">
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="isService" name="is_service">
                        <label class="form-check-label" for="isService">Is this a service?</label>
                    </div>
                    <div class="mb-3">
                        <label for="productImage" class="form-label">Product Image</label>
                        <input type="file" class="form-control" id="productImage" name="product_image" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add_product" class="btn btn-primary">Add Product/Service</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: '#productDescription',
    plugins: 'advlist autolink lists link image charmap print preview anchor',
    toolbar_mode: 'floating',
  });
</script>

<?php
require_once 'includes/footer.php';
$conn->close();
?>
