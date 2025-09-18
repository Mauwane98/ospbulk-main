<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

require_once 'includes/header.php';
require_once 'config/db.php';
require_once 'admin/includes/functions.php';

$message = '';
$isSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message_content = trim($_POST['message']);

    // Validate inputs
    if (empty($name) || empty($email) || empty($subject) || empty($message_content)) {
        $message = 'Please fill in all the fields.';
        $isSuccess = false;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Invalid email format.';
        $isSuccess = false;
    } else {
        // Prepare and execute the query
        $stmt = $conn->prepare("INSERT INTO inquiries (name, email, subject, message) VALUES (?, ?, ?, ?)");
        if ($stmt === false) {
            $message = 'Database error: ' . $conn->error;
            $isSuccess = false;
        } else {
            $stmt->bind_param("ssss", $name, $email, $subject, $message_content);
            if ($stmt->execute()) {
                $message = 'Thank you for your inquiry! We will get back to you shortly.';
                $isSuccess = true;

                // Send email notification using PHPMailer
                $mail = new PHPMailer(true); // Passing true enables exceptions

                try {
                    //Server settings
                    $mail->isSMTP();                                            // Send using SMTP
                    $mail->Host       = 'ospbulk.co.za';                        // Set the SMTP server to send through
                    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                    $mail->Username   = 'info@ospbulk.co.za';                   // SMTP username
                    // WARNING: Hardcoding passwords in code is a security risk.
                    // Please move this password to a secure environment variable or a configuration file outside the web root.
                    $mail->Password   = '20178@OSP';                            // SMTP password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // Enable implicit TLS encryption
                    $mail->Port       = 465;                                    // TCP port to connect to; use 587 if you set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                    //Recipients
                    $mail->setFrom('no-reply@ospbulk.co.za', 'OSP Bulk Website');
                    $mail->addAddress('info@ospbulk.co.za');                    // Add a recipient
                    $mail->addReplyTo($email, $name);

                    //Content
                    $mail->isHTML(false);                                       // Set email format to plain text
                    $mail->Subject = 'New Inquiry from OSP Bulk Website';
                    $mail->Body    = "You have received a new inquiry from your website contact form.\n\n".
                                     "Here are the details:\n\n".
                                     "Name: $name\n".
                                     "Email: $email\n".
                                     "Subject: $subject\n".
                                     "Message:\n$message_content\n";

                    $mail->send();
                    // $message .= ' Email sent successfully!'; // Optional: Add to success message
                } catch (Exception $e) {
                    $error_message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    $isSuccess = false; // Mark as failure if email sending fails
                }

            } else {
                $message = 'Something went wrong. Please try again later.';
                $isSuccess = false;
            }
            $stmt->close();
        }
    }
}
?>

<!-- Hero Section for Contact Page -->
<section class="relative bg-cover bg-center h-[50vh] flex items-center" style="background-image: url('assets/img/logistics.jpg');">
    <div class="absolute inset-0 bg-black opacity-60"></div>
    <div class="container mx-auto px-6 z-10 text-center text-white">
        <h1 class="text-4xl md:text-6xl font-bold mb-4">Contact Us</h1>
        <p class="text-lg md:text-xl max-w-3xl mx-auto">
            Get in touch with our team to discuss your needs and how we can assist you.
        </p>
    </div>
</section>

<!-- Contact Content Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div class="bg-[#f5f5f0] p-8 rounded-lg shadow-lg">
                <h2 class="text-3xl font-bold text-deep-charcoal mb-6">Send Us a Message</h2>
                <?php if ($message): ?>
                    <div class="p-4 mb-4 rounded-lg <?php echo $isSuccess ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                <form action="contact.php" method="POST" class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" id="name" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-burnt-orange focus:ring focus:ring-burnt-orange focus:ring-opacity-50">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" id="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-burnt-orange focus:ring focus:ring-burnt-orange focus:ring-opacity-50">
                    </div>
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                        <input type="text" id="subject" name="subject" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-burnt-orange focus:ring focus:ring-burnt-orange focus:ring-opacity-50">
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700">Your Message</label>
                        <textarea id="message" name="message" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-burnt-orange focus:ring focus:ring-burnt-orange focus:ring-opacity-50"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-burnt-orange text-white py-3 px-4 rounded-full font-semibold hover:bg-[#e26a0a] transition-colors duration-300 transform hover:scale-105">
                        Send Message
                    </button>
                </form>
            </div>
            <!-- Company Info & Map -->
            <div class="space-y-8">
                <div>
                    <h3 class="text-2xl font-bold text-deep-charcoal mb-4">Our Office</h3>
                    <p class="text-gray-700">6 Ivy Street, Arcadia, Pretoria</p>
                    <p class="text-gray-700">Phone: +27 11 123 4567</p>
                    <p class="text-gray-700">Email: info@ospbulk.co.za</p>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-deep-charcoal mb-4">Location</h3>
                    <div class="rounded-lg overflow-hidden shadow-lg">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3593.533143422521!2d28.2093423!3d-25.7529669!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1e956212c17c6855%3A0x79166473370cf508!2s6%20Ivy%20St%2C%20Arcadia%2C%20Pretoria%2C%200083%2C%20South%20Africa!5e0!3m2!1sen!2sus!4v1726502772753!5m2!1sen!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>
