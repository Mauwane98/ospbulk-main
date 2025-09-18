<?php
// Start a new session or resume an existing one
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the current page is the homepage
$is_homepage = (basename($_SERVER['PHP_SELF']) == 'index.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OSP Bulk</title>
    <!-- Tailwind CSS CDN for modern styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f5f0;
            color: #1a1a1a;
        }
        .text-burnt-orange {
            color: #e87c0e;
        }
        .bg-burnt-orange {
            background-color: #e87c0e;
        }
        .text-earthy-green {
            color: #4a7b5a;
        }
        .bg-earthy-green {
            background-color: #4a7b5a;
        }
        .text-golden-yellow {
            color: #fdb813;
        }
        .bg-golden-yellow {
            background-color: #fdb813;
        }
        .bg-deep-charcoal {
            background-color: #1a1a1a;
        }
        .border-golden-yellow {
            border-color: #fdb813;
        }
        /* Custom mobile menu styling */
        .mobile-menu {
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
        }
        .mobile-menu.active {
            transform: translateX(0);
        }

        /* Initial and scrolled header styles */
        .header-scrolled {
            background-color: white;
            color: #1a1a1a;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease-in-out;
        }
        .header-scrolled .nav-link {
            color: #1a1a1a;
        }
        .header-scrolled .logo-img {
            height: 3rem; /* Normal size on scroll */
        }
        .initial-header .logo-img {
            height: 5rem; /* Larger logo for homepage hero section */
        }
        @media (min-width: 1024px) {
             .initial-header .logo-img {
                height: 3.5rem;
             }
             .header-scrolled .logo-img {
                height: 3rem;
             }
        }
    </style>
</head>
<body class="bg-[#f5f5f0] text-[#1a1a1a]">

    <!-- Header -->
    <header id="main-header" class="bg-white text-[#1a1a1a] shadow-md sticky top-0 z-50 transition-all duration-300
    <?php echo $is_homepage ? 'initial-header' : ''; ?>">
        <nav class="container mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <!-- Logo -->
                <a href="index.php" class="flex-shrink-0">
                    <img src="assets/img/logo.png" alt="OSP Bulk Logo" class="logo-img h-12 lg:h-10 transition-all duration-300">
                </a>
                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center space-x-6">
                    <a href="index.php" class="nav-link hover:text-burnt-orange transition-colors duration-300">Home</a>
                    <a href="about.php" class="nav-link hover:text-burnt-orange transition-colors duration-300">About Us</a>
                    <a href="products.php" class="nav-link hover:text-burnt-orange transition-colors duration-300">Products</a>
                    <a href="news.php" class="nav-link hover:text-burnt-orange transition-colors duration-300">News</a>
                    <a href="gallery.php" class="nav-link hover:text-burnt-orange transition-colors duration-300">Gallery</a>
                    <a href="partnerships.php" class="nav-link hover:text-burnt-orange transition-colors duration-300">Partnerships</a>
                    <a href="events.php" class="nav-link hover:text-burnt-orange transition-colors duration-300">Events</a>
                    <a href="contact.php" class="nav-link hover:text-burnt-orange transition-colors duration-300">Contact Us</a>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <!-- User/Admin Links -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="admin/dashboard.php" class="nav-link hover:text-burnt-orange transition-colors duration-300">Dashboard</a>
                    <a href="logout.php" class="nav-link hover:text-burnt-orange transition-colors duration-300">Logout</a>
                <?php endif; ?>

                <!-- Shop button -->
                <a href="shop.php" class="hidden md:inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-full shadow-sm text-white bg-burnt-orange hover:bg-[#e26a0a] transition-colors duration-300">
                    <i class="fas fa-shopping-bag mr-2"></i> Shop Now
                </a>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" class="lg:hidden text-[#1a1a1a] focus:outline-none">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </nav>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="mobile-menu fixed top-0 right-0 h-full w-64 bg-white z-50 shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <a href="index.php" class="flex-shrink-0">
                    <img src="assets/img/logo.png" alt="OSP Bulk Logo" class="h-12 lg:h-10">
                </a>
                <button id="close-mobile-menu" class="text-[#1a1a1a] focus:outline-none">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <nav class="flex flex-col space-y-4 text-[#1a1a1a]">
                <a href="index.php" class="hover:text-burnt-orange transition-colors duration-300">Home</a>
                <a href="about.php" class="hover:text-burnt-orange transition-colors duration-300">About Us</a>
                <a href="products.php" class="hover:text-burnt-orange transition-colors duration-300">Products</a>
                <a href="news.php" class="hover:text-burnt-orange transition-colors duration-300">News</a>
                <a href="gallery.php" class="hover:text-burnt-orange transition-colors duration-300">Gallery</a>
                <a href="partnerships.php" class="hover:text-burnt-orange transition-colors duration-300">Partnerships</a>
                <a href="events.php" class="hover:text-burnt-orange transition-colors duration-300">Events</a>
                <a href="contact.php" class="hover:text-burnt-orange transition-colors duration-300">Contact Us</a>
                <a href="shop.php" class="mt-4 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-full shadow-sm text-white bg-burnt-orange hover:bg-[#e26a0a] transition-colors duration-300">
                    <i class="fas fa-shopping-bag mr-2"></i> Shop Now
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="admin/dashboard.php" class="mt-2 hover:text-burnt-orange transition-colors duration-300">Dashboard</a>
                    <a href="logout.php" class="mt-2 hover:text-burnt-orange transition-colors duration-300">Logout</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- JavaScript for Mobile Menu & Scroll Behavior -->
    <script>
        document.getElementById('mobile-menu-button').addEventListener('click', () => {
            document.getElementById('mobile-menu').classList.toggle('active');
        });
        document.getElementById('close-mobile-menu').addEventListener('click', () => {
            document.getElementById('mobile-menu').classList.remove('active');
        });

        // Scroll behavior for logo and header
        <?php if ($is_homepage): ?>
        window.addEventListener('scroll', () => {
            const header = document.getElementById('main-header');
            const scrollPosition = window.scrollY;
            if (scrollPosition > 50) {
                header.classList.add('header-scrolled');
                header.classList.remove('initial-header');
            } else {
                header.classList.remove('header-scrolled');
                header.classList.add('initial-header');
            }
        });
        <?php endif; ?>
    </script>
    <main class="container mx-auto px-6 py-8">
