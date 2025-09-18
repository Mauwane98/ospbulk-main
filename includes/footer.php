    </main>
    <!-- Footer -->
    <footer class="bg-[#1a1a1a] text-[#f5f5f0] mt-auto py-8">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Logo & Description -->
                <div class="col-span-1">
                    <a href="index.php" class="flex-shrink-0 mb-4">
                        <img src="https://placehold.co/150x50/1a1a1a/f5f5f0?text=OSP+Bulk" alt="OSP Bulk Logo" class="h-10">
                    </a>
                    <p class="mt-4 text-sm text-gray-400">
                        OSP Bulk is a leading South African provider of high-quality agricultural products and community development initiatives.
                    </p>
                </div>
                <!-- Quick Links -->
                <div class="col-span-1">
                    <h3 class="text-xl font-semibold mb-4 text-golden-yellow">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="about.php" class="hover:text-burnt-orange transition-colors duration-300">About Us</a></li>
                        <li><a href="products.php" class="hover:text-burnt-orange transition-colors duration-300">Products</a></li>
                        <li><a href="news.php" class="hover:text-burnt-orange transition-colors duration-300">News</a></li>
                        <li><a href="gallery.php" class="hover:text-burnt-orange transition-colors duration-300">Gallery</a></li>
                        <li><a href="contact.php" class="hover:text-burnt-orange transition-colors duration-300">Contact Us</a></li>
                    </ul>
                </div>
                <!-- Contact Info -->
                <div class="col-span-1">
                    <h3 class="text-xl font-semibold mb-4 text-golden-yellow">Contact Us</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><i class="fas fa-map-marker-alt text-burnt-orange mr-2"></i> 123 Farming Lane, Johannesburg, South Africa</li>
                        <li><i class="fas fa-phone-alt text-burnt-orange mr-2"></i> +27 11 123 4567</li>
                        <li><i class="fas fa-envelope text-burnt-orange mr-2"></i> info@ospbulk.co.za</li>
                    </ul>
                </div>
                <!-- Newsletter -->
                <div class="col-span-1">
                    <h3 class="text-xl font-semibold mb-4 text-golden-yellow">Newsletter</h3>
                    <p class="text-sm text-gray-400">Subscribe to our newsletter for the latest news and updates.</p>
                    <form action="subscribe.php" method="POST" class="mt-4">
                        <input type="email" name="email" placeholder="Enter your email" class="w-full p-2 rounded-md bg-gray-800 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-burnt-orange">
                        <button type="submit" class="mt-2 w-full bg-burnt-orange text-white py-2 rounded-md hover:bg-[#e26a0a] transition-colors duration-300">Subscribe</button>
                    </form>
                </div>
            </div>

            <!-- Copyright and Social Media -->
            <div class="mt-8 pt-6 border-t border-gray-700 flex flex-col md:flex-row items-center justify-between text-center text-sm text-gray-400">
                <p>&copy; <?php echo date("Y"); ?> OSP Bulk. All rights reserved.</p>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <a href="#" class="text-golden-yellow hover:text-burnt-orange"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-golden-yellow hover:text-burnt-orange"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-golden-yellow hover:text-burnt-orange"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-golden-yellow hover:text-burnt-orange"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
