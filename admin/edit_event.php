<?php
require_once 'includes/header.php';
require_once '../config/db.php';
require_once 'includes/functions.php'; // Include the functions file

$errors = [];
$success_message = '';
$upload_dir = '../assets/uploads/';

$event_id = $_GET['id'] ?? null;
if (!$event_id) {
    header("location: events.php");
    exit;
}

// Fetch the event details before processing POST (to get current image name)
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();
$stmt->close();

if (!$event) {
    header("location: events.php");
    exit;
}

// Handle form submission for updating an event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_event'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $errors[] = "Invalid CSRF token.";
    }

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date = trim($_POST['event_date']);
    $current_image = $event['image']; // Get current image from fetched data
    $new_image_name = $current_image; // Assume no change unless new file uploaded

    // Input Validation
    if (empty($title)) {
        $errors[] = "Title is required.";
    } elseif (strlen($title) > 255) {
        $errors[] = "Title cannot exceed 255 characters.";
    }

    if (empty($description)) {
        $errors[] = "Description is required.";
    }

    if (empty($event_date)) {
        $errors[] = "Event date is required.";
    } elseif (!strtotime($event_date)) {
        $errors[] = "Invalid event date format.";
    } elseif (new DateTime($event_date) < new DateTime(date('Y-m-d'))) {
        // $errors[] = "Event date cannot be in the past."; // Uncomment if past dates are not allowed
    }

    // Handle image upload if a new file is provided
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
        $image_file = $_FILES['event_image'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5 MB

        if (!in_array($image_file['type'], $allowed_types)) {
            $errors[] = "Invalid file type. Only JPG, PNG, GIF, WEBP images are allowed.";
        }
        if ($image_file['size'] > $max_size) {
            $errors[] = "File size exceeds the maximum limit of 5MB.";
        }

        if (empty($errors)) {
            $new_image_name = uniqid() . "_" . basename($image_file["name"]); // Generate unique filename
            $target_file = $upload_dir . $new_image_name;

            if (move_uploaded_file($image_file["tmp_name"], $target_file)) {
                // Delete old image if a new one is uploaded and old one exists
                if ($current_image && file_exists($upload_dir . $current_image)) {
                    unlink($upload_dir . $current_image);
                }
            } else {
                $errors[] = "Error moving uploaded file.";
                $new_image_name = $current_image; // Revert to current image name if move fails
            }
        }
    } elseif (isset($_FILES['event_image']) && $_FILES['event_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $errors[] = "File upload error: " . $_FILES['event_image']['error'];
    }

    // If no validation errors, proceed with update
    if (empty($errors)) {
        // Sanitize inputs before updating DB
        $sanitized_title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $sanitized_description = $description; // Keep HTML from TinyMCE
        $sanitized_event_date = htmlspecialchars($event_date, ENT_QUOTES, 'UTF-8');

        $stmt = $conn->prepare("UPDATE events SET title = ?, description = ?, event_date = ?, image = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $sanitized_title, $sanitized_description, $sanitized_event_date, $new_image_name, $event_id);
        
        if ($stmt->execute()) {
            $success_message = "Event updated successfully!";
        } else {
            $errors[] = "Error updating event: " . $stmt->error;
        }
        $stmt->close();

        // Re-fetch event details to display updated values after successful update
        $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $event = $result->fetch_assoc();
        $stmt->close();
    }
}

?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Event</h1>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger" role="alert">
        <?php foreach ($errors as $error): ?>
            <p class="mb-0"><?php echo $error; ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($success_message)): ?>
    <div class="alert alert-success" role="alert">
        <?php echo $success_message; ?>
    </div>
<?php endif; ?>

<div class="card" data-animation-class="animate__fadeInUp">
    <div class="card-body">
        <form action="edit_event.php?id=<?php echo $event_id; ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="mb-3 form-floating">
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" placeholder="Title" required>
                <label for="title">Title</label>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($event['description']); ?></textarea>
            </div>
            <div class="mb-3 form-floating">
                <input type="date" class="form-control" id="event_date" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" placeholder="Event Date" required>
                <label for="event_date">Event Date</label>
            </div>
            <div class="mb-3">
                <label for="eventImage" class="form-label">Event Image</label>
                <?php if ($event['image']): ?>
                    <div class="mb-2">
                        <img src="<?php echo $upload_dir . htmlspecialchars($event['image']); ?>" alt="Current Image" style="max-width: 200px; height: auto;">
                    </div>
                <?php endif; ?>
                <input type="file" class="form-control" id="eventImage" name="event_image" accept="image/*">
                <small class="form-text text-muted">Leave blank to keep current image.</small>
            </div>
            <button type="submit" name="update_event" class="btn btn-primary">Update Event</button>
            <a href="events.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: '#description',
    plugins: 'advlist autolink lists link image charmap print preview anchor',
    toolbar_mode: 'floating',
  });
</script>

<?php
require_once 'includes/footer.php';
$conn->close();
?>