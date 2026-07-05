<?php
/**
 * Header Include
 * FSSAI License Verification System
 */

if (!defined('APP_NAME')) {
    define('APP_NAME', 'FSSAI_Verification');
}

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Verify FSSAI license authenticity and report fake food licenses in India">
    <title><?= $pageTitle ?? 'FSSAI License Verification' ?> - Food Safety Compliance Portal</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- HTML5-QRCode for QR scanning -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🛡️</text></svg>">
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-shield-check me-2"></i>
                FSSAI Verification
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'index' ? 'active' : '' ?>" href="index.php">
                            <i class="bi bi-house-door me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'verify' ? 'active' : '' ?>" href="verify.php">
                            <i class="bi bi-search me-1"></i>Verify License
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'report' ? 'active' : '' ?>" href="report.php">
                            <i class="bi bi-flag me-1"></i>Report Fake
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'admin' || strpos($currentPage, 'admin/') === 0 ? 'active' : '' ?>" href="admin/login.php">
                            <i class="bi bi-gear me-1"></i>Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Wrapper -->
    <main class="flex-grow-1">
