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

// Prepare a statement to get products
$stmt = $conn->prepare("SELECT id, name, description, image, price FROM products LIMIT 4");
if ($stmt === false) {
    die('Failed to prepare product query: ' . $conn->error);
}
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Prepare a statement to get news posts
$stmt = $conn->prepare("SELECT id, title, content, created_at, image FROM posts ORDER BY created_at DESC LIMIT 3");
if ($stmt === false) {
    die('Failed to prepare news query: ' . $conn->error);
}
$stmt->execute();
$news_posts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Prepare a statement to get testimonials
$stmt = $conn->prepare("SELECT author_name, author_title, content, image FROM testimonials ORDER BY created_at DESC LIMIT 2");
if ($stmt === false) {
    die('Failed to prepare testimonials query: ' . $conn->error);
}
$stmt->execute();
$testimonials = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>

<!-- Hero Section -->
<section class="relative bg-cover bg-center h-screen" style="background-image: url('assets/img/hero1.jpg');">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div class="relative z-10 flex flex-col items-center justify-center h-full text-center text-white p-6">
        <h1 class="text-4xl md:text-6xl font-bold mb-4 animate-fade-in-up">Connecting South African Farmers to the World</h1>
        <p class="text-lg md:text-xl max-w-2xl mb-8 animate-fade-in-up delay-100">
            OSP Bulk is your partner in sustainable agriculture, providing high-quality produce and empowering local communities.
        </p>
        <a href="about.php" class="bg-golden-yellow text-deep-charcoal font-bold py-3 px-8 rounded-full shadow-lg hover:bg-burnt-orange transition-colors duration-300 transform hover:scale-105 animate-fade-in-up delay-200">
            Learn More
        </a>
    </div>
</section>

<!-- What We Do Section -->
<section class="py-16 bg-[#f5f5f0]">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center mb-12 text-deep-charcoal">What We Do</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Service Card 1 -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                <img src="assets/img/crop-farming.jpg" alt="Crop Farming" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-2">Crop Farming</h3>
                    <p class="text-gray-600">Sustainable and efficient crop farming methods to ensure high-quality produce.</p>
                </div>
            </div>
            <!-- Service Card 2 -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                <img src="assets/img/logistics.jpg" alt="Logistics" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-2">Logistics & Supply Chain</h3>
                    <p class="text-gray-600">Seamless logistics from farm to market, ensuring freshness and quality.</p>
                </div>
            </div>
            <!-- Service Card 3 -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                <img src="assets/img/manufacturing.jpg" alt="Manufacturing" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-2">Manufacturing & Processing</h3>
                    <p class="text-gray-600">Advanced processing facilities that turn raw produce into quality goods.</p>
                </div>
            </div>
            <!-- Service Card 4 -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                <img src="assets/img/community-development.png" alt="Community Development" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-2">Community Empowerment</h3>
                    <p class="text-gray-600">Investing in local communities to foster growth and economic stability.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Products Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center mb-12 text-deep-charcoal">Our Featured Products</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
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
        </div>
        <div class="text-center mt-12">
            <a href="products.php" class="bg-earthy-green text-white font-bold py-3 px-8 rounded-full shadow-lg hover:bg-golden-yellow transition-colors duration-300">
                View All Products
            </a>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-16 bg-[#f5f5f0]">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center mb-12 text-deep-charcoal">What Our Partners Say</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            <?php if (!empty($testimonials)): ?>
                <?php foreach ($testimonials as $testimonial): ?>
                    <div class="bg-white rounded-lg shadow-md p-8 transform hover:scale-105 transition-transform duration-300">
                        <p class="text-lg italic text-gray-700 mb-6">"<?php echo htmlspecialchars($testimonial['content']); ?>"</p>
                        <div class="flex items-center">
                            <?php if (!empty($testimonial['image'])): ?>
                                <img src="assets/uploads/<?php echo htmlspecialchars($testimonial['image']); ?>" alt="<?php echo htmlspecialchars($testimonial['author_name']); ?>'s profile picture" class="w-16 h-16 rounded-full object-cover mr-4 border-2 border-golden-yellow">
                            <?php endif; ?>
                            <div>
                                <p class="font-semibold text-lg text-deep-charcoal"><?php echo htmlspecialchars($testimonial['author_name']); ?></p>
                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($testimonial['author_title']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-10">
                    <p class="text-xl text-gray-500">No testimonials have been shared yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Latest News Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center mb-12 text-deep-charcoal">Latest News</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($news_posts as $post): ?>
            <!-- News Article Card -->
            <div class="bg-[#f5f5f0] rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-transform duration-300">
                <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="News image" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-deep-charcoal mb-2"><?php echo htmlspecialchars($post['title']); ?></h3>
                    <p class="text-sm text-gray-500 mb-4"><?php echo date('F j, Y', strtotime($post['created_at'])); ?></p>
                    <p class="text-gray-600"><?php echo htmlspecialchars(substr(strip_tags($post['content']), 0, 100)) . '...'; ?></p>
                    <a href="news.php?id=<?php echo $post['id']; ?>" class="mt-4 text-burnt-orange hover:underline block">Read more</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-12">
            <a href="news.php" class="bg-earthy-green text-white font-bold py-3 px-8 rounded-full shadow-lg hover:bg-golden-yellow transition-colors duration-300">
                View All News
            </a>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="py-16 bg-golden-yellow text-center text-deep-charcoal">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold mb-4">Partner with Us Today</h2>
        <p class="text-lg mb-8">
            Join the OSP Bulk family and contribute to a sustainable and prosperous future for South Africa.
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
