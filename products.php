<?php
// Includes our new header, which handles session and shared HTML/CSS
require_once 'includes/header.php';
// Include database connection and functions
require_once 'config/db.php';
require_once 'admin/includes/functions.php';

// Check for a valid database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all products
$stmt = $conn->prepare("SELECT id, name, description, image, price FROM products");
if ($stmt === false) {
    die('Failed to prepare product query: ' . $conn->error);
}
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!-- Hero Section for Products Page -->
<section class="relative bg-cover bg-center h-[50vh] flex items-center" style="background-image: url('assets/img/coffeeproducts.jpg');">
    <div class="absolute inset-0 bg-black opacity-60"></div>
    <div class="container mx-auto px-6 z-10 text-center text-white">
        <h1 class="text-4xl md:text-6xl font-bold mb-4">Our Products</h1>
        <p class="text-lg md:text-xl max-w-3xl mx-auto">
            Discover our wide range of high-quality agricultural products, sustainably sourced from South African farms.
        </p>
    </div>
</section>

<!-- Products Grid Section -->
<section class="py-16 bg-[#f5f5f0]">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center text-deep-charcoal mb-12">Browse Our Offerings</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <!-- Product Card -->
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200 transform hover:scale-105 transition-transform duration-300">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-56 object-cover">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-deep-charcoal mb-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="text-sm text-gray-500 mb-4"><?php echo htmlspecialchars(substr($product['description'], 0, 75)) . '...'; ?></p>
                            <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="inline-block bg-burnt-orange text-white py-2 px-4 rounded-full text-sm font-medium hover:bg-[#e26a0a] transition-colors duration-300">View Product</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-10">
                    <p class="text-xl text-gray-500">No products found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="py-16 bg-golden-yellow text-center text-deep-charcoal">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold mb-4">Can't find what you're looking for?</h2>
        <p class="text-lg mb-8">
            Let us know your specific needs, and we'll help you find the right solution.
        </p>
        <a href="contact.php" class="bg-burnt-orange text-white font-bold py-3 px-8 rounded-full shadow-lg hover:bg-[#e26a0a] transition-colors duration-300 transform hover:scale-105">
            Contact Us
        </a>
    </div>
</section>

<?php
// Includes the new footer
require_once 'includes/footer.php';
?>
