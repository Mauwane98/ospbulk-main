<?php
require_once 'includes/header.php';
require_once 'config/db.php'; // Include database connection

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $user_message = trim($_POST['message']); // Renamed to avoid conflict with $message variable

    // Server-side validation
    if (empty($name) || empty($email) || empty($user_message)) {
        $message = '<div class="alert alert-danger">Please fill in all required fields (Name, Email, Message).</div>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<div class="alert alert-danger">Invalid email format.</div>';
    } else {
        // Store inquiry in database
        $stmt = $conn->prepare("INSERT INTO inquiries (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $user_message);

        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Your message has been sent successfully! We will get back to you soon.</div>';

            // --- Email Notification (Placeholder) ---
            // In a real application, you would use a library like PHPMailer
            // to send an email to the administrator.
            $to = "admin@ospbulk.co.za"; // Replace with actual admin email
            $email_subject = "New Contact Form Submission: " . $subject;
            $email_body = "Name: $name\n" .
                          "Email: $email\n" .
                          "Subject: $subject\n" .
                          "Message:\n$user_message";
            $headers = "From: no-reply@ospbulk.co.za\r\n" .
                       "Reply-To: $email\r\n" .
                       "X-Mailer: PHP/" . phpversion();

            // mail($to, $email_subject, $email_body, $headers); // Uncomment to enable email sending
            // ----------------------------------------

        } else {
            $message = '<div class="alert alert-danger">There was an error sending your message: ' . $stmt->error . '</div>';
        }
        $stmt->close();
    }
}
?>


<header class="hero-section text-center d-flex align-items-center justify-content-center text-white" style="background: url('assets/img/logistics.jpg') no-repeat center center/cover; height: 60vh;">
    <div class="container" data-animation-class="animate__fadeIn">
        <h1 class="display-3 fw-bold mb-3">Contact Us</h1>
        <p class="lead fs-4 mb-4">We'd Love to Hear From You</p>
    </div>
</header>

<main>
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-4" data-animation-class="animate__fadeInLeft">
                    <div class="card h-100 shadow-sm border-0 rounded-3 p-4">
                        <h2 class="mb-4 fw-bold text-dark">Get in Touch</h2>
                        <?php if ($message_sent): ?>
                            <div class="alert alert-success" role="alert">
                                Your message has been sent successfully! We will get back to you soon.
                            </div>
                        <?php endif; ?>
                        <form action="contact.php" method="post">
                            <div class="mb-3">
                                <label for="name" class="form-label">Your Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Your Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject">
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-outline-primary btn-lg">Send Message</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-6 mb-4" data-animation-class="animate__fadeInRight">
                    <div class="card h-100 shadow-sm border-0 rounded-3 p-4 bg-light">
                        <h2 class="mb-4 fw-bold text-dark">Contact Information</h2>
                        <ul class="list-unstyled fs-5">
                            <li class="mb-3"><i class="bi bi-phone-fill me-3 text-dark"></i><strong>Phone:</strong> +2772 346 4667</li>
                            <li class="mb-3"><i class="bi bi-envelope-fill me-3 text-dark"></i><strong>Email:</strong> mbongeniphiri0@ospbulk.co.za</li>
                            <li class="mb-3"><i class="bi bi-geo-alt-fill me-3 text-dark"></i><strong>Address:</strong> 6 Ivy Street, Arcadia, Pretoria</li>
                        </ul>

                        
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <h2 class="text-center display-4 fw-bold mb-5 text-dark">Find Us on the Map</h2>
            <div class="row justify-content-center" data-animation-class="animate__fadeInUp">
                <div class="col-lg-10">
                    <!-- Placeholder for Google Map or static image -->
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3589.7000000000004!2d28.220000000000002!3d-25.740000000000002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1e95620000000001%3A0x1e95620000000001!2s6%20Ivy%20St%2C%20Arcadia%2C%20Pretoria%2C%200083%2C%20South%20Africa!5e0!3m2!1sen!2sus!4v1678888888888!5m2!1sen!2sus" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <p class="text-center text-muted mt-3">Our office is located at 6 Ivy Street, Arcadia, Pretoria.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
require_once 'includes/footer.php';
?>