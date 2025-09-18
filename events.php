<?php
require_once 'includes/header.php';
require_once 'config/db.php';
require_once 'admin/includes/functions.php';

// Check for a valid database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all events
$stmt = $conn->prepare("SELECT id, title, description, event_date, image FROM events ORDER BY event_date DESC");
if ($stmt === false) {
    die('Failed to prepare event query: ' . $conn->error);
}
$stmt->execute();
$events = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!-- Hero Section for Events Page -->
<section class="relative bg-cover bg-center h-[50vh] flex items-center" style="background-image: url('assets/img/hero3.jpg');">
    <div class="absolute inset-0 bg-black opacity-60"></div>
    <div class="container mx-auto px-6 z-10 text-center text-white">
        <h1 class="text-4xl md:text-6xl font-bold mb-4">Our Events</h1>
        <p class="text-lg md:text-xl max-w-3xl mx-auto">
            Stay up-to-date with our community gatherings, farming workshops, and industry events.
        </p>
    </div>
</section>

<!-- Events Grid Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <?php if (!empty($events)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($events as $event): ?>
                    <!-- Event Card -->
                    <div class="bg-[#f5f5f0] rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                        <img src="<?php echo htmlspecialchars($event['image']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="w-full h-56 object-cover">
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-deep-charcoal mb-2"><?php echo htmlspecialchars($event['title']); ?></h3>
                            <p class="text-sm text-gray-500 mb-2">
                                <i class="fas fa-calendar-alt text-burnt-orange"></i> <?php echo date('F j, Y', strtotime($event['event_date'])); ?>
                            </p>
                            <p class="text-gray-700"><?php echo htmlspecialchars(substr($event['description'], 0, 100)) . '...'; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-10">
                <p class="text-xl text-gray-500">No events found at this time.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>
