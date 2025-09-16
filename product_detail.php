<?php
require_once 'includes/header.php';
require_once 'config/db.php';

$product_id = $_GET['id'] ?? null;
$product = null;

if ($product_id) {
    $stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
}

?>

<header class="hero-section text-center d-flex align-items-center justify-content-center text-white" style="background: url('assets/img/coffeeproducts.jpg') no-repeat center center/cover; height: 40vh;">
    <div class="container" data-animation-class="animate__fadeIn">
        <h1 class="display-3 fw-bold mb-3"><?php echo $product ? htmlspecialchars($product['name']) : 'Product Details'; ?></h1>
    </div>
</header>

<main class="container my-5">
    <?php if ($product): ?>
        <div class="row">
            <div class="col-md-6" data-animation-class="animate__fadeInLeft">
                <img src="assets/uploads/<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid rounded shadow-sm" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="col-md-6" data-animation-class="animate__fadeInRight">
                <h2 class="display-4 fw-bold mb-4 text-dark"><?php echo htmlspecialchars($product['name']); ?></h2>
                <?php if (!empty($product['category_name'])): ?>
                    <p class="lead text-muted">Category: <?php echo htmlspecialchars($product['category_name']); ?></p>
                <?php endif; ?>
                <p class="lead"><?php echo $product['description']; ?></p>
                <?php if (!$product['is_service']): // Only show for products, not services ?>
                    <h3 class="fw-bold mt-4">Price: RXXX.XX</h3> <!-- Placeholder for price -->
                    <button class="btn btn-outline-primary btn-lg mt-3">Add to Cart</button> <!-- Placeholder for Add to Cart -->
                <?php endif; ?>
                <a href="products.php" class="btn btn-outline-secondary mt-3">Back to Products</a>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center" role="alert">
            Product not found.
        </div>
    <?php endif; ?>
</main>

<?php
require_once 'includes/footer.php';

?>
