<?php
require_once 'includes/header.php';
require_once 'config/db.php';

// Fetch all testimonials
$testimonials_result = $conn->query("SELECT * FROM testimonials ORDER BY created_at DESC");

?>

<header class="hero-section" style="background-image: url('assets/img/community-development.png');">
    <div class="container text-center">
        <h1 class="display-2 fw-bold text-white">What Our Partners Say</h1>
        <p class="lead fs-4 text-white-50 mb-4">Hear from those who have experienced our impact firsthand.</p>
    </div>
</header>

<main class="container my-5">
    <div class="row g-4">
        <?php if ($testimonials_result->num_rows > 0): ?>
            <?php while ($testimonial = $testimonials_result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4" data-animation-class="animate__fadeInUp">
                    <div class="card h-100 shadow-sm border-0 rounded-3 text-center p-4">
                        <?php if (!empty($testimonial['image'])): ?>
                            <img src="assets/uploads/<?php echo htmlspecialchars($testimonial['image']); ?>" class="rounded-circle mx-auto mb-3" alt="<?php echo htmlspecialchars($testimonial['author_name']); ?>" style="width: 100px; height: 100px; object-fit: cover;">
                        <?php endif; ?>
                        <blockquote class="blockquote mb-0">
                            <p class="mb-0">"<?php echo htmlspecialchars($testimonial['content']); ?>"</p>
                            <footer class="blockquote-footer mt-2">
                                <?php echo htmlspecialchars($testimonial['author_name']); ?>
                                <?php if (!empty($testimonial['author_title'])): ?>
                                    <cite title="Source Title"><?php echo htmlspecialchars($testimonial['author_title']); ?></cite>
                                <?php endif; ?>
                            </footer>
                        </blockquote>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <p class="lead">No testimonials available at the moment.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
require_once 'includes/footer.php';
?>