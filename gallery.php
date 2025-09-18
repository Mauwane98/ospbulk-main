<?php
require_once 'includes/header.php';
require_once 'config/db.php';
require_once 'admin/includes/functions.php';

// Check for a valid database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all gallery images
$stmt = $conn->prepare("SELECT id, title, image FROM gallery ORDER BY created_at DESC");
if ($stmt === false) {
    die('Failed to prepare gallery query: ' . $conn->error);
}
$stmt->execute();
$gallery_images = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!-- Hero Section for Gallery Page -->
<section class="relative bg-cover bg-center h-[50vh] flex items-center" style="background-image: url('assets/img/fresh-produce.jpg');">
    <div class="absolute inset-0 bg-black opacity-60"></div>
    <div class="container mx-auto px-6 z-10 text-center text-white">
        <h1 class="text-4xl md:text-6xl font-bold mb-4">Our Gallery</h1>
        <p class="text-lg md:text-xl max-w-3xl mx-auto">
            A visual journey of our farming, community, and fresh produce.
        </p>
    </div>
</section>

<!-- Gallery Grid Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <?php if (!empty($gallery_images)): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <?php foreach ($gallery_images as $image): ?>
                    <div class="relative overflow-hidden rounded-lg shadow-lg group">
                        <img src="<?php echo htmlspecialchars($image['image']); ?>" alt="<?php echo htmlspecialchars($image['title']); ?>" class="w-full h-72 object-cover transform group-hover:scale-110 transition-transform duration-300">
                        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <p class="text-white text-lg font-semibold"><?php echo htmlspecialchars($image['title']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-10">
                <p class="text-xl text-gray-500">No images found in the gallery.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>
