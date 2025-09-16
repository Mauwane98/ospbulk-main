document.addEventListener("DOMContentLoaded", function() {
    var featuredProductsCarousel = document.querySelector('#featuredProductsCarousel');
    if (featuredProductsCarousel) {
        new bootstrap.Carousel(featuredProductsCarousel, {
            interval: 5000,
            wrap: true
        });
    }

    var featuredServicesCarousel = document.querySelector('#featuredServicesCarousel');
    if (featuredServicesCarousel) {
        new bootstrap.Carousel(featuredServicesCarousel, {
            interval: 5000,
            wrap: true
        });
    }

    var latestNewsCarousel = document.querySelector('#latestNewsCarousel');
    if (latestNewsCarousel) {
        new bootstrap.Carousel(latestNewsCarousel, {
            interval: 5000,
            wrap: true
        });
    }

    

    // Back to top button
    const backToTop = document.querySelector('.back-to-top');
    if (backToTop) {
        const toggleBackToTop = () => {
            if (window.scrollY > 100) {
                backToTop.classList.add('active');
            } else {
                backToTop.classList.remove('active');
            }
        };
        window.addEventListener('load', toggleBackToTop);
        document.addEventListener('scroll', toggleBackToTop);
        backToTop.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();

            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Password visibility toggle
    window.togglePasswordVisibility = function(fieldId, iconId) {
        const passwordField = document.getElementById(fieldId);
        const toggleIcon = document.getElementById(iconId);
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    };

    // Fade-in animation on scroll
    const faders = document.querySelectorAll('.fade-in');
    const appearOptions = {
        threshold: 0.1 // Trigger when 10% of the element is visible
    };

    const appearOnScroll = new IntersectionObserver(function(entries, appearOnScroll) {
        entries.forEach(entry => {
            if (!entry.isIntersecting) {
                return;
            } else {
                entry.target.classList.add('animated');
                appearOnScroll.unobserve(entry.target);
            }
        });
    }, appearOptions);

    faders.forEach(fader => {
        appearOnScroll.observe(fader);
    });

    // Animate.css animations on scroll
    const animatedElements = document.querySelectorAll('[data-animation-class]');
    const animateOptions = {
        threshold: 0.1
    };

    const animateOnScroll = new IntersectionObserver(function(entries, animateOnScroll) {
        entries.forEach(entry => {
            if (!entry.isIntersecting) {
                return;
            } else {
                const animationClass = entry.target.dataset.animationClass;
                if (animationClass) {
                    entry.target.classList.add('animate__animated', ...animationClass.split(' '));
                }
                animateOnScroll.unobserve(entry.target);
            }
        });
    }, animateOptions);

    animatedElements.forEach(element => {
        animateOnScroll.observe(element);
    });

    // Dynamic Logo Size on Scroll (Mobile Only)
    const body = document.body;
    const navbar = document.querySelector('.navbar');

    if (body.classList.contains('body-homepage') && navbar) {
        const handleScroll = () => {
            if (window.innerWidth <= 768) { // Apply only on mobile
                if (window.scrollY > 50) { // Scroll threshold
                    navbar.classList.add('navbar-scroll-shrink');
                } else {
                    navbar.classList.remove('navbar-scroll-shrink');
                }
            }
        };

        window.addEventListener('scroll', handleScroll);
        window.addEventListener('resize', handleScroll); // Re-evaluate on resize
        handleScroll(); // Initial check
    }
});