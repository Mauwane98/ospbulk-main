<?php
require_once 'includes/header.php';
require_once 'config/db.php';

// Fetch all gallery images
$result = $conn->query("SELECT * FROM gallery ORDER BY created_at DESC");

?>

<header class="hero-section text-center d-flex align-items-center justify-content-center text-white" style="background: url('assets/img/gallery.jpg') no-repeat center center/cover; height: 60vh;">
    <div class="container" data-animation-class="animate__fadeIn">
        <h1 class="display-3 fw-bold mb-3">Our Gallery</h1>
        <p class="lead fs-4 mb-4">A Glimpse into Our World</p>
    </div>
</header>

<main>
    <section class="py-5">
        <div class="container">
            <h2 class="text-center display-4 fw-bold mb-5 text-dark">Our Journey in Pictures</h2>
            <div class="row g-4">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="col-md-4 mb-4" data-animation-class="animate__fadeInUp">
                            <div class="card h-100 shadow-sm border-0 rounded-3 gallery-item">
                                <a href="assets/uploads/<?php echo htmlspecialchars($row['image']); ?>" data-bs-toggle="modal" data-bs-target="#galleryModal" data-bs-image-title="<?php echo htmlspecialchars($row['title']); ?>" data-bs-image-description="<?php echo htmlspecialchars($row['description']); ?>">
                                    <img src="assets/uploads/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top rounded-top-3" alt="<?php echo htmlspecialchars($row['title']); ?>" style="height: 280px; object-fit: cover;">
                                </a>
                                <div class="card-body">
                                    <h5 class="card-title fw-bold"><?php echo htmlspecialchars($row['title']); ?></h5>
                                    <p class="card-text text-muted"><?php echo htmlspecialchars(substr($row['description'], 0, 100)) . (strlen($row['description']) > 100 ? '...' : ''); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="lead">No images in the gallery at the moment.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="galleryModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" class="img-fluid rounded" id="modalImage" alt="">
                    <p id="modalImageDescription" class="mt-3 text-muted"></p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
require_once 'includes/footer.php';

?>

<script>
    const galleryModal = document.getElementById('galleryModal');
    galleryModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const imageUrl = button.getAttribute('href');
        const imageTitle = button.getAttribute('data-bs-image-title');
        const imageDescription = button.getAttribute('data-bs-image-description');

        const modalImage = galleryModal.querySelector('#modalImage');
        const modalTitle = galleryModal.querySelector('#galleryModalLabel');
        const modalDescription = galleryModal.querySelector('#modalImageDescription');

        modalImage.setAttribute('src', imageUrl);
        modalTitle.textContent = imageTitle;
        modalDescription.textContent = imageDescription;
    });
</script>