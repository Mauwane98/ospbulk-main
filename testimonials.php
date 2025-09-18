<?php
require_once 'includes/header.php';
require_once 'config/db.php';
require_once 'admin/includes/functions.php';

// Check for a valid database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all testimonials
$stmt = $conn->prepare("SELECT id, author, testimonial_text FROM testimonials ORDER BY created_at DESC");
if ($stmt === false) {
    die('Failed to prepare testimonial query: ' . $conn->error);
}
$stmt->execute();
$testimonials = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!-- Hero Section for Testimonials Page -->
<section class="relative bg-cover bg-center h-[50vh] flex items-center" style="background-image: url('assets/img/hero1.jpg');">
    <div class="absolute inset-0 bg-black opacity-60"></div>
    <div class="container mx-auto px-6 z-10 text-center text-white">
        <h1 class="text-4xl md:text-6xl font-bold mb-4">What Our Clients Say</h1>
        <p class="text-lg md:text-xl max-w-3xl mx-auto">
            Hear from our satisfied partners and clients about their experience working with us.
        </p>
    </div>
</section>

<!-- Testimonials Grid Section -->
<section class="py-16 bg-[#f5f5f0]">
    <div class="container mx-auto px-6">
        <?php if (!empty($testimonials)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($testimonials as $testimonial): ?>
                    <!-- Testimonial Card -->
                    <div class="bg-white p-8 rounded-lg shadow-lg border-l-4 border-burnt-orange">
                        <p class="text-gray-700 text-lg italic mb-4">"<?php echo htmlspecialchars($testimonial['testimonial_text']); ?>"</p>
                        <div class="flex items-center">
                            <div class="flex-grow">
                                <p class="font-semibold text-deep-charcoal"><?php echo htmlspecialchars($testimonial['author']); ?></p>
                                <div class="flex text-golden-yellow">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-10">
                <p class="text-xl text-gray-500">No testimonials found at this time.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Call to Action -->
<section class="py-16 bg-golden-yellow text-center text-deep-charcoal">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold mb-4">Share Your Experience</h2>
        <p class="text-lg mb-8">
            Have a great experience with us? We'd love to hear from you!
        </p>
        <a href="contact.php" class="bg-burnt-orange text-white font-bold py-3 px-8 rounded-full shadow-lg hover:bg-[#e26a0a] transition-colors duration-300 transform hover:scale-105">
            Submit a Testimonial
        </a>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>
