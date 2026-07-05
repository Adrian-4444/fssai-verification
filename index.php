<?php
/**
 * Landing Page
 * FSSAI License Verification System
 */

$pageTitle = 'Home';
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section bg-gradient-primary text-white py-5">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-5 fw-bold mb-3">
                    <i class="bi bi-shield-check me-2"></i>
                    Verify FSSAI License Authenticity
                </h1>
                <p class="lead mb-4">
                    Protect yourself from fraudulent food businesses. Verify any FSSAI license
                    instantly and report suspicious licenses to help keep consumers safe.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="verify.php" class="btn btn-light btn-lg px-4">
                        <i class="bi bi-search me-2"></i>Verify Now
                    </a>
                    <a href="report.php" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-flag me-2"></i>Report Fake
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center mt-4 mt-lg-0">
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 300'%3E%3Crect fill='%23ffffff20' width='400' height='300'/%3E%3Ctext x='50%25' y='50%25' font-size='80' text-anchor='middle' dominant-baseline='middle'%3E🛡️%3C/text%3E%3C/svg%3E"
                     alt="FSSAI Verification" class="img-fluid" style="max-width: 400px;">
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">How It Works</h2>
            <p class="text-muted">Three simple steps to verify any FSSAI license</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm text-center">
                    <div class="card-body py-5">
                        <div class="step-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 80px; height: 80px; font-size: 2rem;">
                            <i class="bi bi-keyboard"></i>
                        </div>
                        <h5 class="card-title">Step 1: Input</h5>
                        <p class="card-text text-muted">
                            Enter the 14-digit FSSAI license number manually or upload an image
                            with the license for automatic reading.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm text-center">
                    <div class="card-body py-5">
                        <div class="step-icon bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 80px; height: 80px; font-size: 2rem;">
                            <i class="bi bi-database-check"></i>
                        </div>
                        <h5 class="card-title">Step 2: Database Match</h5>
                        <p class="card-text text-muted">
                            Our system instantly checks the license against the official
                            FSSAI database of registered food businesses.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm text-center">
                    <div class="card-body py-5">
                        <div class="step-icon bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 80px; height: 80px; font-size: 2rem;">
                            <i class="bi bi-file-earmark-check"></i>
                        </div>
                        <h5 class="card-title">Step 3: Results</h5>
                        <p class="card-text text-muted">
                            Get instant verification results with business details,
                            or report suspicious licenses for investigation.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Verification Form -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-primary text-white py-3">
                        <h4 class="mb-0"><i class="bi bi-search me-2"></i>Quick Verification</h4>
                    </div>
                    <div class="card-body p-4">
                        <form id="quickVerifyForm" class="row g-3">
                            <div class="col-md-8">
                                <label for="quickLicense" class="form-label">FSSAI License Number</label>
                                <input type="text" class="form-control form-control-lg" id="quickLicense"
                                       placeholder="Enter 14-digit license number"
                                       pattern="[a-zA-Z0-9]{14}" maxlength="14" required>
                                <div class="form-text">Example: 10012021000001</div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-search me-2"></i>Verify
                                </button>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="text-muted mb-2">Or scan from an image</p>
                            <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#qrScanModal">
                                <i class="bi bi-qr-code-scan me-2"></i>Scan QR / Upload Image
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-3">
                <div class="display-4 fw-bold text-primary">15+</div>
                <div class="text-muted">Verified Licenses</div>
            </div>
            <div class="col-md-3">
                <div class="display-4 fw-bold text-success">100%</div>
                <div class="text-muted">Accuracy Rate</div>
            </div>
            <div class="col-md-3">
                <div class="display-4 fw-bold text-info">24/7</div>
                <div class="text-muted">Available</div>
            </div>
            <div class="col-md-3">
                <div class="display-4 fw-bold text-warning">Instant</div>
                <div class="text-muted">Verification</div>
            </div>
        </div>
    </div>
</section>

<!-- Alert Banner -->
<section class="py-4 bg-warning">
    <div class="container">
        <div class="d-flex align-items-center justify-content-center">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
            <span class="fw-medium">
                Found a suspicious license?
                <a href="report.php" class="alert-link">Report it now</a> to help protect other consumers.
            </span>
        </div>
    </div>
</section>

<!-- QR Scan Modal -->
<div class="modal fade" id="qrScanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-qr-code-scan me-2"></i>Scan License</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#upload-tab">
                            <i class="bi bi-upload me-1"></i>Upload Image
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#camera-tab">
                            <i class="bi bi-camera me-1"></i>Camera Scan
                        </button>
                    </li>
                </ul>
                <div class="tab-content mt-3">
                    <div class="tab-pane fade show active" id="upload-tab">
                        <div class="text-center py-4">
                            <input type="file" class="form-control" id="licenseImage" accept="image/*">
                            <p class="text-muted mt-2">Upload an image containing the FSSAI license number</p>
                            <button class="btn btn-primary mt-2" onclick="processImage()">
                                <i class="bi bi-magic me-1"></i>Extract License Number
                            </button>
                        </div>
                        <div id="imagePreview" class="mt-3 text-center"></div>
                    </div>
                    <div class="tab-pane fade" id="camera-tab">
                        <div id="reader" class="mb-3"></div>
                        <button class="btn btn-primary w-100" onclick="startCamera()">
                            <i class="bi bi-camera-video me-1"></i>Start Camera
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Quick verify form submission
document.getElementById('quickVerifyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const licenseNumber = document.getElementById('quickLicense').value.trim();
    if (licenseNumber.length === 14) {
        window.location.href = 'verify.php?license=' + encodeURIComponent(licenseNumber);
    } else {
        alert('Please enter a valid 14-digit license number');
    }
});

// Image preview
document.getElementById('licenseImage')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').innerHTML =
                '<img src="' + e.target.result + '" class="img-fluid rounded" style="max-height: 200px;">';
        };
        reader.readAsDataURL(file);
    }
});

function processImage() {
    // Simulated OCR - in production, this would use Tesseract.js or server-side OCR
    alert('In production, this would extract the license number from the image using OCR. For now, please enter the license number manually.');
}

function startCamera() {
    // Initialize QR code scanner
    const html5QrCode = new Html5Qrcode("reader");
    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        (decodedText) => {
            // Extract license number from QR content
            const licenseMatch = decodedText.match(/[a-zA-Z0-9]{14}/);
            if (licenseMatch) {
                window.location.href = 'verify.php?license=' + licenseMatch[0];
            } else {
                alert('No valid license number found in QR code');
            }
        },
        (error) => { /* Ignore scan errors */ }
    ).catch(err => {
        alert('Error starting camera: ' + err);
    });
}
</script>

<?php require_once 'includes/footer.php'; ?>
