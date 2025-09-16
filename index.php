<?php
require_once 'includes/header.php';
require_once 'config/db.php';

// Fetch categories
$categories_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");

// Fetch featured products
$products_result = $conn->query("SELECT * FROM products WHERE is_service = 0 ORDER BY created_at DESC LIMIT 3");

// Fetch featured services
$services_result = $conn->query("SELECT * FROM products WHERE is_service = 1 ORDER BY created_at DESC LIMIT 3");

// Fetch latest news
$news_result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC LIMIT 3");

?>

<header class="hero-section vh-100 d-flex align-items-center justify-content-center text-white">
    <video playsinline autoplay muted loop poster="assets/img/hero1.jpg" id="bgvid">
        <source src="assets/videos/hero-video.mp4" type="video/mp4">
    </video>
    <div class="container text-center fade-in">
        <h1 class="display-1 fw-bold mb-4">OSP Bulk (Pty) Ltd</h1>
        <p class="lead fs-3 mb-5">A Cup Full of Life</p>
        <a href="products.php" class="btn btn-primary btn-lg px-5 py-3 me-3">Explore Products</a>
        <a href="about.php" class="btn btn-outline-light btn-lg px-5 py-3">Learn More</a>
    </div>
</header>

<main>
    

    <section class="bg-light py-5">
        <div class="container">
            <div class="section-title">
                <h2>Our Featured Products</h2>
            </div>
            <div id="featuredProductsCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php
                    $i = 0;
                    $products_result->data_seek(0); // Reset pointer
                    while ($row = $products_result->fetch_assoc()):
                        if ($i % 3 == 0): // Start a new carousel item every 3 products
                    ?>
                            <div class="carousel-item <?php echo ($i == 0) ? 'active' : ''; ?>">
                                <div class="row justify-content-center">
                        <?php endif; ?>
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <div class="card h-100 text-center">
                                            <img src="assets/uploads/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['name']); ?>">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                                                <p class="card-text text-muted"><?php echo htmlspecialchars(substr($row['description'], 0, 100)) . (strlen($row['description']) > 100 ? '...' : ''); ?></p>
                                                <a href="product_detail.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary mt-3">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                        <?php
                        $i++;
                        if ($i % 3 == 0 || $i == $products_result->num_rows): // Close carousel item
                        ?>
                                </div>
                            </div>
                        <?php
                        endif;
                    endwhile;
                    ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#featuredProductsCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#featuredProductsCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="section-title">
                <h2>Our Featured Services</h2>
            </div>
            <div id="featuredServicesCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php
                    $i = 0;
                    $services_result->data_seek(0); // Reset pointer
                    while ($row = $services_result->fetch_assoc()):
                        if ($i % 3 == 0): // Start a new carousel item every 3 services
                    ?>
                            <div class="carousel-item <?php echo ($i == 0) ? 'active' : ''; ?>">
                                <div class="row justify-content-center">
                        <?php endif; ?>
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <div class="card h-100 text-center">
                                            <img src="assets/uploads/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['name']); ?>">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                                                <p class="card-text text-muted"><?php echo htmlspecialchars(substr($row['description'], 0, 100)) . (strlen($row['description']) > 100 ? '...' : ''); ?></p>
                                                <a href="product_detail.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary mt-3">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                        <?php
                        $i++;
                        if ($i % 3 == 0 || $i == $services_result->num_rows): // Close carousel item
                        ?>
                                </div>
                            </div>
                        <?php
                        endif;
                    endwhile;
                    ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#featuredServicesCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#featuredServicesCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </section>

    <section class="bg-light py-5">
        <div class="container">
            <div class="section-title">
                <h2>Latest News</h2>
            </div>
            <div id="latestNewsCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php
                    $i = 0;
                    $news_result->data_seek(0); // Reset pointer
                    while ($row = $news_result->fetch_assoc()):
                        if ($i % 3 == 0): // Start a new carousel item every 3 news articles
                    ?>
                            <div class="carousel-item <?php echo ($i == 0) ? 'active' : ''; ?>">
                                <div class="row justify-content-center">
                        <?php endif; ?>
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <div class="card h-100">
                                            <img src="assets/uploads/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['title']); ?>">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                                                <p class="card-text text-muted"><?php echo htmlspecialchars(substr($row['content'], 0, 100)) . (strlen($row['content']) > 100 ? '...' : ''); ?></p>
                                                <a href="news.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary mt-3">Read More</a>
                                            </div>
                                        </div>
                                    </div>
                        <?php
                        $i++;
                        if ($i % 3 == 0 || $i == $news_result->num_rows): // Close carousel item
                        ?>
                                </div>
                            </div>
                        <?php
                        endif;
                    endwhile;
                    ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#latestNewsCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#latestNewsCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </section>

    <section class="py-5 text-center bg-primary text-white">
        <div class="container">
            <h2 class="display-4 fw-bold mb-3">Exciting News! Our Digital Store is Coming Soon!</h2>
            <p class="lead mb-4">Get ready to explore a wider range of products and services online. We're working hard to bring you a seamless shopping experience.</p>
            <a href="subscribe.php" class="btn btn-light btn-lg">Stay Updated - Subscribe Now!</a>
        </div>
    </section>
</main>

<?php
require_once 'includes/footer.php';
?>