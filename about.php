<?php
// Includes our new header, which handles session and shared HTML/CSS
require_once 'includes/header.php';
// We don't need a database connection here as the content is static.
?>

<!-- Hero Section for About Page -->
<section class="relative bg-cover bg-center h-[50vh] flex items-center" style="background-image: url('assets/img/hero2.jpg');">
    <div class="absolute inset-0 bg-black opacity-60"></div>
    <div class="container mx-auto px-6 z-10 text-center text-white">
        <h1 class="text-4xl md:text-6xl font-bold mb-4">About Us</h1>
        <p class="text-lg md:text-xl max-w-3xl mx-auto">
            Our journey is rooted in the rich soil of South Africa, driven by a passion for agriculture and community.
        </p>
    </div>
</section>

<!-- Our Story Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <div class="flex flex-col lg:flex-row items-center gap-12">
            <div class="lg:w-1/2">
                <img src="assets/img/farming-systems.jpg" alt="Our Story" class="rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300">
            </div>
            <div class="lg:w-1/2">
                <h2 class="text-3xl font-bold text-deep-charcoal mb-4">Our Story</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    The company was founded in 2023 by Mr Mbongeni Phiri. Mr Phiri currently holds a certificate of competence as a Barista through Ciro SA Coffee Academy.
                </p>
                <p class="text-gray-700 leading-relaxed mb-4">
                    The company is based on the premise that as South Africans we are gifted with more natural resources than we can imagine and as a 100% Black Owned Company, what better way than to cultivate this notion using our expertise, experience and innate talent.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- A Profile in Sustainable Growth Section -->
<section class="py-16 bg-[#f5f5f0]">
    <div class="container mx-auto px-6">
        <div class="flex flex-col lg:flex-row-reverse items-center gap-12">
            <div class="lg:w-1/2">
                <img src="assets/img/hero1.jpg" alt="Our Profile" class="rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300">
            </div>
            <div class="lg:w-1/2">
                <h2 class="text-3xl font-bold text-deep-charcoal mb-4">A Profile in Sustainable Growth</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    OSP Bulk (Pty) Ltd is a forward-thinking agricultural enterprise dedicated to pioneering a new era of profit-driven, organic farming in Africa. Founded on the principle of Ubuntu—that we are all interconnected—the company's core mission is to empower rural communities through the cultivation of high-quality specialty coffee and essential food crops, thereby fostering both economic prosperity and regional food security. We believe that sustainable practices and strong community partnerships are not just ethical choices, but the foundation for a resilient and profitable business model.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Mission and Vision Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl font-bold text-deep-charcoal mb-4">Our Mission & Vision</h2>
        <div class="flex flex-col md:flex-row gap-8 mt-8">
            <div class="md:w-1/2 bg-[#f5f5f0] rounded-lg shadow-md p-8 transform hover:scale-105 transition-transform duration-300">
                <h3 class="text-xl font-semibold mb-3 text-burnt-orange">Vision Statement</h3>
                <p class="text-gray-600">
                    To cultivate a leading global reputation for premium, ethically sourced organic coffee, while simultaneously establishing a sustainable, community-driven agricultural ecosystem that ensures long-term food security and economic empowerment for rural farmers across the continent.
                </p>
            </div>
            <div class="md:w-1/2 bg-[#f5f5f0] rounded-lg shadow-md p-8 transform hover:scale-105 transition-transform duration-300">
                <h3 class="text-xl font-semibold mb-3 text-burnt-orange">Mission Statement</h3>
                <p class="text-gray-600">
                    To mobilize and equip rural farmers through comprehensive vocational training and collaborative partnerships, transforming them into a network of proficient organic producers. By focusing on the high-value specialty coffee market and diversifying into staple food crops, we will generate significant returns for our investors, create sustainable employment, and build a more equitable agricultural future.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Core Business Strategy Section -->
<section class="py-16 bg-[#f5f5f0]">
    <div class="container mx-auto px-6">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-deep-charcoal mb-4">Core Business Strategy</h2>
            <p class="text-gray-700 leading-relaxed max-w-3xl mx-auto">
                Our strategy is built on a dual-pronged approach that leverages the high-value specialty coffee market to fund and sustain broader agricultural and social initiatives.
            </p>
        </div>
    </div>
</section>

<!-- Meet Our Team Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center text-deep-charcoal mb-12">Meet Our Team</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Team Member 1 -->
            <div class="flex flex-col items-center text-center">
                <img src="assets/img/mbongeni.jpg" alt="Mbongeni Phiri" class="w-40 h-40 rounded-full object-cover mb-4 border-4 border-golden-yellow">
                <h3 class="text-xl font-semibold text-deep-charcoal">Mbongeni Phiri</h3>
                <p class="text-burnt-orange">1st Director</p>
                <p class="text-gray-600 mt-2">Contact: +2772 346 4667</p>
                <p class="text-gray-600">Email: mbongeni.phiri@ospbulk.co.za</p>
            </div>
            <!-- Team Member 2 -->
            <div class="flex flex-col items-center text-center">
                <img src="assets/img/thabisile_ncube.jpg" alt="Thabisile Ncube" class="w-40 h-40 rounded-full object-cover mb-4 border-4 border-golden-yellow">
                <h3 class="text-xl font-semibold text-deep-charcoal">Thabisile Ncube</h3>
                <p class="text-burnt-orange">2nd Director – Non Executive</p>
                <p class="text-gray-600 mt-2">Contact: +2765 332 1771</p>
                <p class="text-gray-600">Email: thabisile.ncube@ospbulk.co.za</p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="py-16 bg-golden-yellow text-center text-deep-charcoal">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold mb-4">Join Our Journey</h2>
        <p class="text-lg mb-8">
            Learn more about our mission and how we're making a difference.
        </p>
        <a href="contact.php" class="bg-burnt-orange text-white font-bold py-3 px-8 rounded-full shadow-lg hover:bg-[#e26a0a] transition-colors duration-300 transform hover:scale-105">
            Contact Us
        </a>
    </div>
</section>

<?php
// Includes the new footer
require_once 'includes/footer.php';
?>
