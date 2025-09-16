<?php
session_start();
require_once 'includes/functions.php';
generate_csrf_token();
require_once 'includes/header.php';
?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-4">
            <div class="card" data-animation-class="animate__fadeInUp">
                <div class="card-body">
                    <h3 class="card-title text-center">Admin Login</h3>
                    <form action="login_process.php" method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <div class="mb-3 form-floating">
                            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                            <label for="username">Username</label>
                        </div>
                        <div class="mb-3 form-floating position-relative">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                            <label for="password">Password</label>
                            <span class="position-absolute end-0 top-50 translate-middle-y pe-3" style="cursor: pointer;" onclick="togglePasswordVisibility('password', 'togglePasswordIconAdminLogin')">
                                <i class="far fa-eye" id="togglePasswordIconAdminLogin"></i>
                            </span>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php
require_once 'includes/footer.php';
?>