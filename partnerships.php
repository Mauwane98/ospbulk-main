<?php
require_once 'includes/header.php';
require_once 'config/db.php';
require_once 'admin/includes/functions.php';

// Fetch partners if needed, but for now, we'll use a static content approach
// $stmt = $conn->prepare("SELECT id, name, description, logo_url FROM partnerships");
// ...
?>

<!-- Hero Section for Partnerships Page -->
<section class="relative bg-cover bg-center h-[50vh] flex items-center" style="background-image: url('assets/img/community-development.png');">
    <div class="absolute inset-0 bg-black opacity-60"></div>
    <div class="container mx-auto px-6 z-10 text-center text-white">
        <h1 class="text-4xl md:text-6xl font-bold mb-4">Our Partnerships</h1>
        <p class="text-lg md:text-xl max-w-3xl mx-auto">
            Building strong, sustainable relationships is at the heart of our success.
        </p>
    </div>
</section>

<!-- Partnerships Value Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl font-bold text-deep-charcoal mb-12">Why Partner With Us?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Value Prop 1 -->
            <div class="bg-[#f5f5f0] p-8 rounded-lg shadow-md transform hover:scale-105 transition-transform duration-300">
                <div class="text-5xl text-burnt-orange mb-4">
                    <i class="fas fa-handshake"></i> <!-- Placeholder for a handshake icon -->
                </div>
                <h3 class="text-xl font-semibold text-deep-charcoal mb-3">Mutual Growth</h3>
                <p class="text-gray-600">
                    We believe in a symbiotic relationship where our success is tied to yours. We invest in long-term partnerships that drive mutual growth.
                </p>
            </div>
            <!-- Value Prop 2 -->
            <div class="bg-[#f5f5f0] p-8 rounded-lg shadow-md transform hover:scale-105 transition-transform duration-300">
                <div class="text-5xl text-burnt-orange mb-4">
                    <i class="fas fa-seedling"></i> <!-- Placeholder for a seedling icon -->
                </div>
                <h3 class="text-xl font-semibold text-deep-charcoal mb-3">Sustainable Impact</h3>
                <p class="text-gray-600">
                    Our partnerships are built on a foundation of sustainability, ensuring we make a positive impact on both the environment and local communities.
                </p>
            </div>
            <!-- Value Prop 3 -->
            <div class="bg-[#f5f5f0] p-8 rounded-lg shadow-md transform hover:scale-105 transition-transform duration-300">
                <div class="text-5xl text-burnt-orange mb-4">
                    <i class="fas fa-chart-line"></i> <!-- Placeholder for a chart-line icon -->
                </div>
                <h3 class="text-xl font-semibold text-deep-charcoal mb-3">Market Access</h3>
                <p class="text-gray-600">
                    Benefit from our extensive network and logistics expertise, providing you with seamless access to new and international markets.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-16 bg-golden-yellow text-center text-deep-charcoal">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold mb-4">Ready to Grow with Us?</h2>
        <p class="text-lg mb-8">
            Join our network of trusted partners and help us build a better future together.
        </p>
        <a href="contact.php" class="bg-burnt-orange text-white font-bold py-3 px-8 rounded-full shadow-lg hover:bg-[#e26a0a] transition-colors duration-300 transform hover:scale-105">
            Become a Partner
        </a>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>
