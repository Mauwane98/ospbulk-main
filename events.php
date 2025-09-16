<?php
require_once 'includes/header.php';
require_once 'config/db.php';

// Fetch all events
$result = $conn->query("SELECT * FROM events ORDER BY event_date DESC");

?>

<header class="hero-section" style="background-image: url('assets/img/hero2.jpg');">
    <div class="container text-center">
        <h1 class="display-2 fw-bold text-white">Our Events</h1>
        <p class="lead fs-4 text-white-50 mb-4">Connecting Communities, Sharing Experiences</p>
    </div>
</header>

<main>
    <section class="py-5">
        <div class="container">
            <div class="section-title">
                <h2>Upcoming Events</h2>
                <p>Join us to learn more about our work and the coffee we love.</p>
            </div>
            <div class="row g-4">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="col-md-6 col-lg-4 mb-4" data-animation-class="animate__fadeInUp">
                            <div class="card h-100">
                                <?php if (!empty($row['image'])): ?>
                                    <img src="assets/uploads/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['title']); ?>">
                                <?php else: ?>
                                    <img src="assets/img/hero3.jpg" class="card-img-top" alt="Default Event Image">
                                <?php endif; ?>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                                    <p class="card-text text-muted flex-grow-1"><?php echo htmlspecialchars(substr($row['description'], 0, 150)) . (strlen($row['description']) > 150 ? '...' : ''); ?></p>
                                    <p class="card-text"><small class="text-muted"><i class="bi bi-calendar-event me-2"></i>Date: <?php echo date("F j, Y", strtotime($row['event_date'])); ?></small></p>
                                    <a href="#" class="btn btn-outline-primary mt-3 align-self-start">Read More</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="lead">No upcoming events at the moment. Please check back later.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php
require_once 'includes/footer.php';

?>