<?php
require_once 'includes/header.php';
require_once 'config/db.php';

$post_id = $_GET['id'] ?? null;
$post = null;

if ($post_id) {
    // Fetch single post
    $stmt = $conn->prepare("SELECT p.*, u.username FROM posts p JOIN users u ON p.user_id = u.id WHERE p.id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    $stmt->close();
} else {
    // Fetch all posts
    $posts_result = $conn->query("SELECT p.*, u.username FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.published_at DESC");
}

?>

<header class="hero-section" style="background-image: url('assets/img/hero3.jpg');">
    <div class="container text-center fade-in">
        <h1 class="display-2 fw-bold text-white"><?php echo $post ? htmlspecialchars($post['title']) : 'Latest News & Updates'; ?></h1>
        <p class="lead fs-4 text-white-50 mb-4"><?php echo $post ? 'By ' . htmlspecialchars($post['username']) . ' on ' . date("F j, Y", strtotime($post['published_at'])) : 'Stay informed with our latest articles and announcements.'; ?></p>
    </div>
</header>

<main class="container my-5">
    <?php if ($post): // Display single post ?>
        <div class="row justify-content-center">
            <div class="col-lg-8" data-animation-class="animate__fadeInUp">
                <?php if (!empty($post['image'])): ?>
                    <img src="assets/uploads/<?php echo htmlspecialchars($post['image']); ?>" class="img-fluid rounded mb-4" alt="<?php echo htmlspecialchars($post['title']); ?>">
                <?php endif; ?>
                <div class="post-content">
                    <?php echo $post['content']; // Assuming content is HTML and safe ?>
                </div>
                <a href="news.php" class="btn btn-outline-primary mt-4">Back to News</a>
            </div>
        </div>
    <?php else: // Display list of posts ?>
        <div class="row g-4">
            <?php if ($posts_result->num_rows > 0): ?>
                <?php while ($row = $posts_result->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4" data-animation-class="animate__fadeInUp">
                        <div class="card h-100 shadow-sm">
                            <?php if (!empty($row['image'])): ?>
                                <img src="assets/uploads/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['title']); ?>" style="height: 200px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold"><?php echo htmlspecialchars($row['title']); ?></h5>
                                <p class="card-text text-muted flex-grow-1"><?php echo htmlspecialchars(substr($row['content'], 0, 150)) . (strlen($row['content']) > 150 ? '...' : ''); ?></p>
                                <p class="card-text"><small class="text-muted">By <?php echo htmlspecialchars($row['username']); ?> on <?php echo date("F j, Y", strtotime($row['published_at'])); ?></small></p>
                                <a href="news.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary mt-3 align-self-start">Read More</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="lead">No news or updates available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</main>

<?php
require_once 'includes/footer.php';
?>