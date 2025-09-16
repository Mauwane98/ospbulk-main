<?php
require_once 'includes/header.php';
require_once '../config/db.php';
require_once 'includes/functions.php'; // Include the functions file

$errors = [];
$success_message = '';
$upload_dir = '../assets/uploads/';

// Handle form submission for adding a new event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $errors[] = "Invalid CSRF token.";
    }

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date = trim($_POST['event_date']);
    $image_name = null; // Initialize image name

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

    // Handle image upload if a file is provided
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
            $image_name = uniqid() . "_" . basename($image_file["name"]); // Generate unique filename
            $target_file = $upload_dir . $image_name;

            if (!move_uploaded_file($image_file["tmp_name"], $target_file)) {
                $errors[] = "Error moving uploaded file.";
                $image_name = null; // Reset image name if move fails
            }
        }
    } elseif (isset($_FILES['event_image']) && $_FILES['event_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $errors[] = "File upload error: " . $_FILES['event_image']['error'];
    }

    // If no validation errors, proceed with insertion
    if (empty($errors)) {
        // Sanitize inputs before inserting into DB
        $sanitized_title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $sanitized_description = $description; // Keep HTML from TinyMCE
        $sanitized_event_date = htmlspecialchars($event_date, ENT_QUOTES, 'UTF-8');

        $stmt = $conn->prepare("INSERT INTO events (title, description, event_date, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $sanitized_title, $sanitized_description, $sanitized_event_date, $image_name);
        
        if ($stmt->execute()) {
            $success_message = "Event added successfully!";
        } else {
            $errors[] = "Error adding event: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Handle deletion of an event
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Basic validation for ID
    if (filter_var($id, FILTER_VALIDATE_INT)) {
        // First, get the image filename to delete the file
        $stmt = $conn->prepare("SELECT image FROM events WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $event_to_delete = $result->fetch_assoc();
        $stmt->close();

        if ($event_to_delete && $event_to_delete['image']) {
            // Delete the file from the uploads directory
            if (file_exists($upload_dir . $event_to_delete['image'])) {
                unlink($upload_dir . $event_to_delete['image']);
            }
        }

        // Delete the record from the database
        $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $success_message = "Event deleted successfully!";
        } else {
            $errors[] = "Error deleting event: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errors[] = "Invalid event ID for deletion.";
    }
    // Redirect to clear GET parameters
    header("location: events.php");
    exit;
}

// Pagination setup
$events_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $events_per_page;

// Get total number of events
$total_events_result = $conn->query("SELECT COUNT(*) as total FROM events");
$total_events = $total_events_result->fetch_assoc()['total'];
$total_pages = ceil($total_events / $events_per_page);

// Fetch events for current page
$result = $conn->query("SELECT * FROM events ORDER BY event_date DESC LIMIT $events_per_page OFFSET $offset");

?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Events</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
            <i class="bi bi-plus-circle"></i> Add New Event
        </button>
    </div>
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

<div class="table-responsive" data-animation-class="animate__fadeInUp">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Date</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars(substr($row['description'], 0, 100)) . (strlen($row['description']) > 100 ? '...' : ''); ?></td>
                    <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                    <td>
                        <?php if ($row['image']): ?>
                            <img src="<?php echo $upload_dir . htmlspecialchars($row['image']); ?>" alt="Event Image" style="width: 50px; height: 50px; object-fit: cover;">
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_event.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i> Edit</a>
                        <a href="events.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this event?');"><i class="bi bi-trash"></i> Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<nav aria-label="Page navigation example">
  <ul class="pagination justify-content-center">
    <li class="page-item <?php if($current_page <= 1){ echo 'disabled'; } ?>">
      <a class="page-link" href="<?php if($current_page <= 1){ echo '#'; } else { echo "?page=".($current_page - 1); } ?>">Previous</a>
    </li>
    <?php for($i = 1; $i <= $total_pages; $i++): ?>
      <li class="page-item <?php if($current_page == $i){ echo 'active'; } ?>"><a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
    <?php endfor; ?>
    <li class="page-item <?php if($current_page >= $total_pages){ echo 'disabled'; } ?>">
      <a class="page-link" href="<?php if($current_page >= $total_pages){ echo '#'; } else { echo "?page=".($current_page + 1); } ?>">Next</a>
    </li>
  </ul>
</nav>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEventModalLabel">Add New Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="events.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="modal-body">
                    <div class="mb-3 form-floating">
                        <input type="text" class="form-control" id="eventTitle" name="title" placeholder="Title" required>
                        <label for="eventTitle">Title</label>
                    </div>
                    <div class="mb-3">
                        <label for="eventDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="eventDescription" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3 form-floating">
                        <input type="date" class="form-control" id="eventDate" name="event_date" placeholder="Event Date" required>
                        <label for="eventDate">Event Date</label>
                    </div>
                    <div class="mb-3">
                        <label for="eventImage" class="form-label">Event Image</label>
                        <input type="file" class="form-control" id="eventImage" name="event_image" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add_event" class="btn btn-primary">Add Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: '#eventDescription',
    plugins: 'advlist autolink lists link image charmap print preview anchor',
    toolbar_mode: 'floating',
  });
</script>

<?php
require_once 'includes/footer.php';
$conn->close();
?>
