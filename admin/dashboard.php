<?php
require_once 'includes/header.php';
require_once '../config/db.php';

// Fetch counts for dashboard widgets

// Total Products
$products_result = $conn->query("SELECT COUNT(id) as count FROM products WHERE is_service = 0");
$products_count = $products_result->fetch_assoc()['count'];

// Total Services
$services_result = $conn->query("SELECT COUNT(id) as count FROM products WHERE is_service = 1");
$services_count = $services_result->fetch_assoc()['count'];

// Total Events
$events_result = $conn->query("SELECT COUNT(id) as count FROM events");
$events_count = $events_result->fetch_assoc()['count'];

// Gallery Images
$gallery_result = $conn->query("SELECT COUNT(id) as count FROM gallery");
$gallery_count = $gallery_result->fetch_assoc()['count'];

// Total Posts
$posts_result = $conn->query("SELECT COUNT(id) as count FROM posts");
$posts_count = $posts_result->fetch_assoc()['count'];

// Total Inquiries
$inquiries_result = $conn->query("SELECT COUNT(id) as count FROM inquiries");
$inquiries_count = $inquiries_result->fetch_assoc()['count'];

// Total Subscribers
$subscribers_result = $conn->query("SELECT COUNT(id) as count FROM subscribers");
$subscribers_count = $subscribers_result->fetch_assoc()['count'];

// Total Categories
$categories_result = $conn->query("SELECT COUNT(id) as count FROM categories");
$categories_count = $categories_result->fetch_assoc()['count'];

// Total Testimonials
$testimonials_result = $conn->query("SELECT COUNT(id) as count FROM testimonials");
$testimonials_count = $testimonials_result->fetch_assoc()['count'];

// Total Partnerships
$partnerships_result = $conn->query("SELECT COUNT(id) as count FROM partnerships");
$partnerships_count = $partnerships_result->fetch_assoc()['count'];

// Total Users
$users_result = $conn->query("SELECT COUNT(id) as count FROM users");
$users_count = $users_result->fetch_assoc()['count'];

?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<div class="row">
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-primary" data-animation-class="animate__fadeInUp">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title"><?php echo $products_count; ?></h3>
                        <p class="card-text">Products</p>
                    </div>
                    <i class="bi bi-box-seam fs-1"></i>
                </div>
                <a href="products.php" class="text-white stretched-link">View Details</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-success" data-animation-class="animate__fadeInUp animate__delay-0-1s">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title"><?php echo $services_count; ?></h3>
                        <p class="card-text">Services</p>
                    </div>
                    <i class="bi bi-gear-wide-connected fs-1"></i>
                </div>
                <a href="products.php?filter=services" class="text-white stretched-link">View Details</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-warning" data-animation-class="animate__fadeInUp animate__delay-0-2s">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title"><?php echo $events_count; ?></h3>
                        <p class="card-text">Events</p>
                    </div>
                    <i class="bi bi-calendar-event fs-1"></i>
                </div>
                <a href="events.php" class="text-white stretched-link">View Details</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-danger" data-animation-class="animate__fadeInUp animate__delay-0-3s">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title"><?php echo $gallery_count; ?></h3>
                        <p class="card-text">Gallery Images</p>
                    </div>
                    <i class="bi bi-images fs-1"></i>
                </div>
                <a href="gallery.php" class="text-white stretched-link">View Details</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-info" data-animation-class="animate__fadeInUp animate__delay-0-4s">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title"><?php echo $posts_count; ?></h3>
                        <p class="card-text">Posts</p>
                    </div>
                    <i class="bi bi-file-earmark-text fs-1"></i>
                </div>
                <a href="manage_posts.php" class="text-white stretched-link">View Details</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-secondary" data-animation-class="animate__fadeInUp animate__delay-0-5s">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title"><?php echo $inquiries_count; ?></h3>
                        <p class="card-text">Inquiries</p>
                    </div>
                    <i class="bi bi-envelope fs-1"></i>
                </div>
                <a href="manage_inquiries.php" class="text-white stretched-link">View Details</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-dark" data-animation-class="animate__fadeInUp animate__delay-0-6s">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title"><?php echo $subscribers_count; ?></h3>
                        <p class="card-text">Subscribers</p>
                    </div>
                    <i class="bi bi-people fs-1"></i>
                </div>
                <a href="manage_subscribers.php" class="text-white stretched-link">View Details</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-primary" data-animation-class="animate__fadeInUp animate__delay-0-7s">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title"><?php echo $categories_count; ?></h3>
                        <p class="card-text">Categories</p>
                    </div>
                    <i class="bi bi-tags fs-1"></i>
                </div>
                <a href="manage_categories.php" class="text-white stretched-link">View Details</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-success" data-animation-class="animate__fadeInUp animate__delay-0-8s">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title"><?php echo $testimonials_count; ?></h3>
                        <p class="card-text">Testimonials</p>
                    </div>
                    <i class="bi bi-chat-quote fs-1"></i>
                </div>
                <a href="manage_testimonials.php" class="text-white stretched-link">View Details</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-warning" data-animation-class="animate__fadeInUp animate__delay-0-9s">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title"><?php echo $partnerships_count; ?></h3>
                        <p class="card-text">Partnerships</p>
                    </div>
                    <i class="bi bi-handshake fs-1"></i>
                </div>
                <a href="manage_partnerships.php" class="text-white stretched-link">View Details</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-info" data-animation-class="animate__fadeInUp animate__delay-1s">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title"><?php echo $users_count; ?></h3>
                        <p class="card-text">Users</p>
                    </div>
                    <i class="bi bi-people fs-1"></i>
                </div>
                <a href="manage_users.php" class="text-white stretched-link">View Details</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                Quick Actions
            </div>
            <div class="card-body">
                <a href="products.php?action=add" class="btn btn-primary">Add New Product</a>
                <a href="events.php?action=add" class="btn btn-secondary">Add New Event</a>
                <a href="gallery.php?action=add" class="btn btn-info">Add to Gallery</a>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>