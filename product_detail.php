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

// Get the product ID from the URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id === 0) {
    echo '<div class="container mx-auto px-6 py-16 text-center">
            <h1 class="text-4xl font-bold text-deep-charcoal mb-4">Product Not Found</h1>
            <p class="text-lg text-gray-600">The product you are looking for does not exist. Please check our <a href="products.php" class="text-burnt-orange hover:underline">product list</a>.</p>
          </div>';
    require_once 'includes/footer.php';
    exit;
}

// Prepare and execute a statement to get a single product
$stmt = $conn->prepare("SELECT id, name, description, image, price FROM products WHERE id = ?");
if ($stmt === false) {
    die('Failed to prepare product query: ' . $conn->error);
}
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

// Check if a product was found
if (!$product) {
    echo '<div class="container mx-auto px-6 py-16 text-center">
            <h1 class="text-4xl font-bold text-deep-charcoal mb-4">Product Not Found</h1>
            <p class="text-lg text-gray-600">The product you are looking for does not exist. Please check our <a href="products.php" class="text-burnt-orange hover:underline">product list</a>.</p>
          </div>';
    require_once 'includes/footer.php';
    exit;
}
?>

<main class="min-h-screen bg-white">
    <div class="container mx-auto px-6 py-16">
        <nav class="text-sm mb-6" aria-label="Breadcrumb">
            <ol class="list-none p-0 inline-flex">
                <li class="flex items-center">
                    <a href="index.php" class="text-gray-500 hover:text-burnt-orange">Home</a>
                    <span class="text-gray-500 mx-2">/</span>
                </li>
                <li class="flex items-center">
                    <a href="products.php" class="text-gray-500 hover:text-burnt-orange">Products</a>
                    <span class="text-gray-500 mx-2">/</span>
                </li>
                <li class="flex items-center text-burnt-orange font-semibold">
                    <span><?php echo htmlspecialchars($product['name']); ?></span>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
            <!-- Product Image Section -->
            <div class="rounded-lg overflow-hidden shadow-xl transform hover:scale-105 transition-transform duration-300">
                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-auto object-cover">
            </div>

            <!-- Product Details Section -->
            <div class="space-y-6">
                <h1 class="text-4xl md:text-5xl font-bold text-deep-charcoal"><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="text-2xl font-semibold text-burnt-orange">
                    R <?php echo number_format(htmlspecialchars($product['price']), 2); ?> per kg
                </p>
                
                <h2 class="text-2xl font-bold text-deep-charcoal mt-8">Description</h2>
                <p class="text-gray-700 leading-relaxed text-lg">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </p>

                <a href="contact.php" class="inline-block mt-8 bg-burnt-orange text-white font-bold py-3 px-8 rounded-full shadow-lg hover:bg-[#e26a0a] transition-colors duration-300 transform hover:scale-105">
                    Contact Us for a Quote
                </a>

                <div class="mt-8 pt-6 border-t border-gray-200">
                    <a href="products.php" class="text-gray-600 hover:text-burnt-orange flex items-center transition-colors duration-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to all products
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
// Includes the new footer
require_once 'includes/footer.php';
?>
