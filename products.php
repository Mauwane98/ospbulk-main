<?php
require_once 'includes/header.php';
require_once 'config/db.php';

$search_query = $_GET['q'] ?? '';
$category_id = $_GET['category_id'] ?? '';

$sanitized_search_query = '%' . $conn->real_escape_string($search_query) . '%';

// Fetch categories for the filter dropdown
$categories_result = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row;
}

// Base SQL for products and services
$products_sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_service = 0";
$services_sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_service = 1";

// Add search query to SQL
if (!empty($search_query)) {
    $products_sql .= " AND (p.name LIKE '" . $sanitized_search_query . "' OR p.description LIKE '" . $sanitized_search_query . "')";
    $services_sql .= " AND (p.name LIKE '" . $sanitized_search_query . "' OR p.description LIKE '" . $sanitized_search_query . "')";
}

// Add category filter to SQL
if (!empty($category_id)) {
    $products_sql .= " AND p.category_id = " . (int)$category_id;
    $services_sql .= " AND p.category_id = " . (int)$category_id;
}

$products_sql .= " ORDER BY p.name ASC";
$services_sql .= " ORDER BY p.name ASC";

$products_result = $conn->query($products_sql);
$services_result = $conn->query($services_sql);

?>

<header class="hero-section" style="background-image: url('assets/img/foodbeverage.jpg');">
    <div class="container text-center">
        <h1 class="display-2 fw-bold text-white">Our Products & Services</h1>
        <p class="lead fs-4 text-white-50 mb-4">Quality and Sustainability in Everything We Do</p>
    </div>
</header>

<main>
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center mb-4">
                <div class="col-md-8">
                    <form action="products.php" method="GET">
                        <div class="row g-2 mb-3">
                            <div class="col-sm-6">
                                <input class="form-control" type="search" placeholder="Search products or services..." aria-label="Search" name="q" value="<?php echo htmlspecialchars($search_query); ?>">
                            </div>
                            <div class="col-sm-4">
                                <select class="form-select" name="category_id">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" <?php echo ($category_id == $category['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($category['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <button class="btn btn-primary w-100" type="submit">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <ul class="nav nav-pills justify-content-center mb-5" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button" role="tab" aria-controls="products" aria-selected="true">Products</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="services-tab" data-bs-toggle="tab" data-bs-target="#services" type="button" role="tab" aria-controls="services" aria-selected="false">Services</button>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="products" role="tabpanel" aria-labelledby="products-tab">
                    <div class="section-title">
                        <h2>Our Products</h2>
                        <p>Discover our range of premium organic products.</p>
                    </div>
                    <div class="row g-4">
                        <?php if ($products_result->num_rows > 0): ?>
                            <?php while ($row = $products_result->fetch_assoc()): ?>
                                <div class="col-md-4 mb-4" data-animation-class="animate__fadeInUp">
                                    <div class="card h-100">
                                        <?php if (!empty($row['image'])): ?>
                                            <img src="assets/uploads/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['name']); ?>">
                                        <?php else: ?>
                                            <img src="assets/img/coffeeproducts.jpg" class="card-img-top" alt="Default Product Image">
                                        <?php endif; ?>
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                                            <p class="card-text text-muted flex-grow-1"><?php echo htmlspecialchars(substr($row['description'], 0, 150)) . (strlen($row['description']) > 150 ? '...' : ''); ?></p>
                                            <?php if (!empty($row['category_name'])): ?>
                                                <p class="card-text"><small class="text-muted">Category: <?php echo htmlspecialchars($row['category_name']); ?></small></p>
                                            <?php endif; ?>
                                            <a href="product_detail.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary mt-3 align-self-start">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12 text-center">
                                <p class="lead">No products available at the moment.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="tab-pane fade" id="services" role="tabpanel" aria-labelledby="services-tab">
                    <div class="section-title">
                        <h2>Our Services</h2>
                        <p>Explore the services we offer to support sustainable agriculture.</p>
                    </div>
                    <div class="row g-4">
                        <?php if ($services_result->num_rows > 0): ?>
                            <?php while ($row = $services_result->fetch_assoc()): ?>
                                <div class="col-md-4 mb-4" data-animation-class="animate__fadeInUp">
                                    <div class="card h-100">
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                                            <p class="card-text text-muted flex-grow-1"><?php echo htmlspecialchars(substr($row['description'], 0, 150)) . (strlen($row['description']) > 150 ? '...' : ''); ?></p>
                                            <?php if (!empty($row['category_name'])): ?>
                                                <p class="card-text"><small class="text-muted">Category: <?php echo htmlspecialchars($row['category_name']); ?></small></p>
                                            <?php endif; ?>
                                            <a href="product_detail.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary mt-3 align-self-start">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12 text-center">
                                <p class="lead">No services available at the moment.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
require_once 'includes/footer.php';

?>