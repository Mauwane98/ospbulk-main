<?php
require_once 'includes/header.php';
require_once 'config/db.php';

// Fetch all partnerships
$partnerships_result = $conn->query("SELECT * FROM partnerships ORDER BY created_at DESC");

?>

<header class="hero-section" style="background-image: url('assets/img/farming-systems.jpg');">
    <div class="container text-center">
        <h1 class="display-2 fw-bold text-white">Our Partnerships</h1>
        <p class="lead fs-4 text-white-50 mb-4">Collaborating for a Sustainable Future</p>
    </div>
</header>

<main class="container my-5">
    <div class="row g-4">
        <?php if ($partnerships_result->num_rows > 0): ?>
            <?php while ($partnership = $partnerships_result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4" data-animation-class="animate__fadeInUp">
                    <div class="card h-100 shadow-sm border-0 rounded-3 p-4">
                        <?php if (!empty($partnership['logo'])): ?>
                            <img src="assets/uploads/<?php echo htmlspecialchars($partnership['logo']); ?>" class="card-img-top mx-auto mb-3" alt="<?php echo htmlspecialchars($partnership['partner_name']); ?>" style="max-width: 150px; height: auto; object-fit: contain;">
                        <?php endif; ?>
                        <h5 class="card-title fw-bold text-center mb-3"><?php echo htmlspecialchars($partnership['partner_name']); ?></h5>
                        <p class="card-text text-muted flex-grow-1"><?php echo htmlspecialchars($partnership['description']); ?></p>
                        <?php if (!empty($partnership['impact_details'])): ?>
                            <div class="mt-3">
                                <h6 class="fw-bold">Impact:</h6>
                                <p class="text-muted"><?php echo htmlspecialchars($partnership['impact_details']); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <p class="lead">No partnerships found at the moment.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
require_once 'includes/footer.php';
?>