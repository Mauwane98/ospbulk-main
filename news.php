<?php
require_once 'includes/header.php';
require_once 'config/db.php';
require_once 'admin/includes/functions.php';

// Check for a valid database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all posts
$stmt = $conn->prepare("SELECT id, title, content, created_at, image FROM posts ORDER BY created_at DESC");
if ($stmt === false) {
    die('Failed to prepare post query: ' . $conn->error);
}
$stmt->execute();
$posts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!-- Hero Section for News Page -->
<section class="relative bg-cover bg-center h-[50vh] flex items-center" style="background-image: url('assets/img/crop-farming.jpg');">
    <div class="absolute inset-0 bg-black opacity-60"></div>
    <div class="container mx-auto px-6 z-10 text-center text-white">
        <h1 class="text-4xl md:text-6xl font-bold mb-4">Latest News</h1>
        <p class="text-lg md:text-xl max-w-3xl mx-auto">
            Stay informed with the latest updates from our farms and community.
        </p>
    </div>
</section>

<!-- News Grid Section -->
<section class="py-16 bg-[#f5f5f0]">
    <div class="container mx-auto px-6">
        <?php if (!empty($posts)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($posts as $post): ?>
                    <!-- News Card -->
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                        <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-full h-56 object-cover">
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-deep-charcoal mb-2"><?php echo htmlspecialchars($post['title']); ?></h3>
                            <p class="text-sm text-gray-500 mb-4">
                                <i class="fas fa-calendar-alt text-burnt-orange"></i> <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                            </p>
                            <p class="text-gray-700 mb-4"><?php echo htmlspecialchars(substr($post['content'], 0, 100)) . '...'; ?></p>
                            <a href="#" class="inline-block bg-burnt-orange text-white py-2 px-4 rounded-full text-sm font-medium hover:bg-[#e26a0a] transition-colors duration-300">Read More</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-10">
                <p class="text-xl text-gray-500">No news articles found at this time.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>
