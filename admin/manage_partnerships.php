<?php
session_start();
require_once '../config/db.php';
require_once 'includes/header.php';
require_once 'includes/functions.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = '';
$edit_partnership = null;
$upload_dir = '../assets/uploads/';

// Handle Add/Edit Partnership
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $partner_name = trim($_POST['partner_name']);
    $description = trim($_POST['description']);
    $impact_details = trim($_POST['impact_details']);
    $partnership_id = $_POST['partnership_id'] ?? null;

    // Logo upload handling
    $logo_name = null;
    if (isset($_FILES['partner_logo']) && $_FILES['partner_logo']['error'] === UPLOAD_ERR_OK) {
        $logo_file = $_FILES['partner_logo'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5 MB

        if (!in_array($logo_file['type'], $allowed_types)) {
            $message = '<div class="alert alert-danger">Invalid file type. Only JPG, PNG, GIF, WEBP images are allowed.</div>';
            $logo_name = null;
        } elseif ($logo_file['size'] > $max_size) {
            $message = '<div class="alert alert-danger">Sorry, your file is too large.</div>';
            $logo_name = null;
        } else {
            $logo_name = uniqid() . "_" . basename($logo_file["name"]); // Generate unique filename
            $target_file = $upload_dir . $logo_name;

            if (!move_uploaded_file($logo_file["tmp_name"], $target_file)) {
                $message = '<div class="alert alert-danger">Sorry, there was an error uploading your file.</div>';
                $logo_name = null;
            }
        }
    } elseif ($partnership_id && empty($_FILES['partner_logo']['name'])) {
        // If editing and no new logo is uploaded, retain existing logo
        $stmt_get_logo = $conn->prepare("SELECT logo FROM partnerships WHERE id = ?");
        $stmt_get_logo->bind_param("i", $partnership_id);
        $stmt_get_logo->execute();
        $result_get_logo = $stmt_get_logo->get_result();
        $existing_logo = $result_get_logo->fetch_assoc();
        $logo_name = $existing_logo['logo'] ?? null;
        $stmt_get_logo->close();
    }

    if (empty($partner_name) || empty($description)) {
        $message = '<div class="alert alert-danger">Partner name and description cannot be empty.</div>';
    } else {
        if ($partnership_id) {
            // Update existing partnership
            if ($logo_name) {
                $stmt = $conn->prepare("UPDATE partnerships SET partner_name = ?, description = ?, logo = ?, impact_details = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $partner_name, $description, $logo_name, $impact_details, $partnership_id);
            } else {
                $stmt = $conn->prepare("UPDATE partnerships SET partner_name = ?, description = ?, impact_details = ? WHERE id = ?");
                $stmt->bind_param("sssi", $partner_name, $description, $impact_details, $partnership_id);
            }
            if ($stmt->execute()) {
                $message = '<div class="alert alert-success">Partnership updated successfully!</div>';
            } else {
                $message = '<div class="alert alert-danger">Error updating partnership: ' . $stmt->error . '</div>';
            }
        } else {
            // Add new partnership
            $stmt = $conn->prepare("INSERT INTO partnerships (partner_name, description, logo, impact_details) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $partner_name, $description, $logo_name, $impact_details);
            if ($stmt->execute()) {
                $message = '<div class="alert alert-success">Partnership added successfully!</div>';
            } else {
                $message = '<div class="alert alert-danger">Error adding partnership: ' . $stmt->error . '</div>';
            }
        }
        $stmt->close();
    }
}

// Handle Edit request
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $partnership_id_to_edit = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM partnerships WHERE id = ?");
    $stmt->bind_param("i", $partnership_id_to_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $edit_partnership = $result->fetch_assoc();
    } else {
        $message = '<div class="alert alert-danger">Partnership not found.</div>';
    }
    $stmt->close();
}

// Handle Delete request
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $partnership_id_to_delete = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM partnerships WHERE id = ?");
    $stmt->bind_param("i", $partnership_id_to_delete);
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">Partnership deleted successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger">Error deleting partnership: ' . $stmt->error . '</div>';
    }
    $stmt->close();
}

// Fetch all partnerships for display
$partnerships_result = $conn->query("SELECT * FROM partnerships ORDER BY created_at DESC");

?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; // Assuming you have a sidebar for admin navigation ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <h1 class="mt-4">Manage Partnerships</h1>
            <?php echo $message; ?>

            <div class="card mb-4">
                <div class="card-header">
                    <?php echo $edit_partnership ? 'Edit Partnership' : 'Add New Partnership'; ?>
                </div>
                <div class="card-body">
                    <form action="manage_partnerships.php" method="POST" enctype="multipart/form-data">
                        <?php if ($edit_partnership): ?>
                            <input type="hidden" name="partnership_id" value="<?php echo $edit_partnership['id']; ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label for="partner_name" class="form-label">Partner Name</label>
                            <input type="text" class="form-control" id="partner_name" name="partner_name" value="<?php echo htmlspecialchars($edit_partnership['partner_name'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="5" required><?php echo htmlspecialchars($edit_partnership['description'] ?? ''); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="impact_details" class="form-label">Impact Details (Optional)</label>
                            <textarea class="form-control" id="impact_details" name="impact_details" rows="5"><?php echo htmlspecialchars($edit_partnership['impact_details'] ?? ''); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="partner_logo" class="form-label">Partner Logo</label>
                            <input type="file" class="form-control" id="partner_logo" name="partner_logo" accept="image/*">
                            <?php if ($edit_partnership && $edit_partnership['logo']): ?>
                                <small class="form-text text-muted">Current Logo: <a href="../assets/uploads/<?php echo htmlspecialchars($edit_partnership['logo']); ?>" target="_blank"><?php echo htmlspecialchars($edit_partnership['logo']); ?></a></small>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary"><?php echo $edit_partnership ? 'Update Partnership' : 'Add Partnership'; ?></button>
                        <?php if ($edit_partnership): ?>
                            <a href="manage_partnerships.php" class="btn btn-secondary">Cancel Edit</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    All Partnerships
                </div>
                <div class="card-body">
                    <?php if ($partnerships_result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Partner Name</th>
                                        <th>Description</th>
                                        <th>Logo</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($partnership = $partnerships_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $partnership['id']; ?></td>
                                            <td><?php echo htmlspecialchars($partnership['partner_name']); ?></td>
                                            <td><?php echo htmlspecialchars(substr($partnership['description'], 0, 100)); ?>...</td>
                                            <td>
                                                <?php if (!empty($partnership['logo'])): ?>
                                                    <img src="../assets/uploads/<?php echo htmlspecialchars($partnership['logo']); ?>" alt="<?php echo htmlspecialchars($partnership['partner_name']); ?>" width="50">
                                                <?php else: ?>
                                                    No Logo
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="manage_partnerships.php?action=edit&id=<?php echo $partnership['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                                <a href="manage_partnerships.php?action=delete&id=<?php echo $partnership['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this partnership?');">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>No partnerships found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>