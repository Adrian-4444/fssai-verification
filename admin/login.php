<?php
/**
 * Admin Login Page
 * FSSAI License Verification System
 */

session_start();

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

define('APP_NAME', 'FSSAI_Verification');
require_once __DIR__ . '/../config/database.php';

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        try {
            $db = getDB();
            $stmt = $db->prepare("
                SELECT id, username, password_hash, email
                FROM admin_users
                WHERE username = :username
            ");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                // Login successful
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];

                // Update last login
                $stmt = $db->prepare("
                    UPDATE admin_users
                    SET last_login = NOW()
                    WHERE id = :id
                ");
                $stmt->execute([':id' => $user['id']]);

                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid username or password';
            }
        } catch (PDOException $e) {
            error_log("Login Error: " . $e->getMessage());
            $error = 'An error occurred. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - FSSAI Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-shield-lock-fill text-primary" style="font-size: 3rem;"></i>
                            <h4 class="mt-3">Admin Login</h4>
                            <p class="text-muted">FSSAI Verification System</p>
                        </div>

                        <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" id="username" name="username"
                                           placeholder="Enter username" required autofocus>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password"
                                           placeholder="Enter password" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                            </button>
                        </form>

                        <div class="mt-4 text-center">
                            <a href="../index.php" class="text-muted text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i>Back to Home
                            </a>
                        </div>

                        <div class="mt-3 p-3 bg-light rounded">
                            <small class="text-muted">
                                <strong>Demo Credentials:</strong><br>
                                Username: <code>admin</code><br>
                                Password: <code>Admin123!</code>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
