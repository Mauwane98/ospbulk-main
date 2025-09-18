document.addEventListener("DOMContentLoaded", function() {

    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.onclick = function() {
            mobileMenu.classList.toggle('hidden');
        };
    }

    // Dynamic Logo Size and Navbar shrink on Scroll for Homepage
    const body = document.body;
    const navbar = document.querySelector('.navbar');

    // Check if it's the homepage and the navbar exists
    if (body.classList.contains('body-homepage') && navbar) {
        const handleScroll = () => {
            // Add 'scrolled' class after scrolling 50px, else remove it
            if (window.scrollY > 50) { 
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        };

        window.addEventListener('scroll', handleScroll);
        // Run on page load in case the page is already scrolled
        handleScroll();
    }
    
    // Fade-in animation for elements with the .fade-in class
    const faders = document.querySelectorAll('.fade-in');
    if (faders.length > 0) {
        const appearOptions = {
            threshold: 0.1, // Trigger when 10% of the element is visible
            rootMargin: "0px 0px -50px 0px" // Start animation a bit sooner
        };

        const appearOnScroll = new IntersectionObserver(function(entries, appearOnScroll) {
            entries.forEach(entry => {
                if (!entry.isIntersecting) {
                    return;
                } else {
                    // Add a class to trigger the animation
                    entry.target.classList.add('animated'); 
                    // Stop observing the element once it has animated
                    appearOnScroll.unobserve(entry.target); 
                }
            });
        }, appearOptions);

        faders.forEach(fader => {
            appearOnScroll.observe(fader);
        });
    }
});
