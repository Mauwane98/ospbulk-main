<?php
session_start();

require_once 'includes/functions.php'; // Include the new functions file

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true){
    header("location: index.php");
    exit;
}

// Generate CSRF token for forms
generate_csrf_token();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - OSP Bulk (Pty) Ltd</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>
</head>
<body>

<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <nav class="bg-dark text-white" id="sidebar-wrapper">
        <div class="sidebar-heading p-3">
            <a href="dashboard.php" class="text-white text-decoration-none">
                <span class="fs-4">OSP Bulk Admin</span>
            </a>
        </div>
        <hr class="sidebar-divider">
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link text-white" aria-current="page">
                    <i class="bi bi-grid"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="events.php" class="nav-link text-white">
                    <i class="bi bi-calendar-event"></i>
                    Events
                </a>
            </li>
            <li>
                <a href="products.php" class="nav-link text-white">
                    <i class="bi bi-box-seam"></i>
                    Products
                </a>
            </li>
            <li>
                <a href="manage_categories.php" class="nav-link text-white">
                    <i class="bi bi-tags"></i>
                    Categories
                </a>
            </li>
            <li>
                <a href="manage_posts.php" class="nav-link text-white">
                    <i class="bi bi-file-earmark-text"></i>
                    Posts
                </a>
            </li>
            <li>
                <a href="manage_inquiries.php" class="nav-link text-white">
                    <i class="bi bi-envelope"></i>
                    Inquiries
                </a>
            </li>
            <li>
                <a href="manage_subscribers.php" class="nav-link text-white">
                    <i class="bi bi-people"></i>
                    Subscribers
                </a>
            </li>
            <li>
                <a href="manage_testimonials.php" class="nav-link text-white">
                    <i class="bi bi-chat-quote"></i>
                    Testimonials
                </a>
            </li>
            <li>
                <a href="manage_partnerships.php" class="nav-link text-white">
                    <i class="bi bi-handshake"></i>
                    Partnerships
                </a>
            </li>
            <li>
                <a href="gallery.php" class="nav-link text-white">
                    <i class="bi bi-images"></i>
                    Gallery
                </a>
            </li>
            <li>
                <a href="manage_users.php" class="nav-link text-white">
                    <i class="bi bi-people"></i>
                    Users
                </a>
            </li>
            <li>
                <a href="settings.php" class="nav-link text-white">
                    <i class="bi bi-gear"></i>
                    Settings
                </a>
            </li>
        </ul>
        <hr class="sidebar-divider">
        <div class="dropdown p-3">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="https://github.com/mdo.png" alt="" width="32" height="32" class="rounded-circle me-2">
                <strong><?php echo htmlspecialchars($_SESSION["username"]); ?></strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                <li><a class="dropdown-item" href="settings.php">Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="logout.php">Sign out</a></li>
            </ul>
        </div>
    </nav>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="page-content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
            <div class="container-fluid">
                <button class="btn btn-primary" id="sidebarToggle">Toggle Menu</button>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Optional: Add top right nav items here if needed -->
                </div>
            </div>
        </nav>
        <main class="container-fluid p-4">
