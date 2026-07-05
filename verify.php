<?php
/**
 * License Verification Page
 * FSSAI License Verification System
 */

$pageTitle = 'Verify License';
require_once 'includes/header.php';

// Get license number from URL
$licenseNumber = $_GET['license'] ?? '';
$licenseNumber = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $licenseNumber));
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="text-center mb-4">
                <h1 class="display-6 fw-bold">
                    <i class="bi bi-search text-primary"></i>
                    License Verification
                </h1>
                <p class="text-muted">Enter a 14-digit FSSAI license number to verify authenticity</p>
            </div>

            <!-- Verification Form -->
            <div class="card border-0 shadow mb-4">
                <div class="card-body p-4">
                    <form id="verifyForm" class="row g-3">
                        <div class="col-md-9">
                            <label for="licenseNumber" class="form-label">FSSAI License Number</label>
                            <input type="text" class="form-control form-control-lg" id="licenseNumber"
                                   placeholder="Enter 14-digit license number"
                                   value="<?= htmlspecialchars($licenseNumber) ?>"
                                   pattern="[a-zA-Z0-9]{14}" maxlength="14"
                                   autofocus required>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                License numbers are 14 digits (e.g., 10012021000001)
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-search me-1"></i> Verify
                            </button>
                        </div>
                    </form>

                    <hr>

                    <div class="text-center">
                        <span class="text-muted me-2">Or scan from image:</span>
                        <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#qrScanModal">
                            <i class="bi bi-qr-code-scan me-1"></i>Scan QR Code
                        </button>
                    </div>
                </div>
            </div>

            <!-- Results Container -->
            <div id="resultsContainer" style="display: none;">
                <!-- Results will be loaded here -->
            </div>

            <!-- Initial State / Instructions -->
            <?php if (empty($licenseNumber)): ?>
            <div class="card border-0 shadow-sm bg-light" id="instructionsCard">
                <div class="card-body text-center py-5">
                    <i class="bi bi-shield-check text-primary" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Enter a License Number to Verify</h5>
                    <p class="text-muted">
                        The license number can be found on food product packaging,<br>
                        restaurant certificates, or food business premises.
                    </p>
                    <div class="row justify-content-center mt-4">
                        <div class="col-md-8">
                            <div class="alert alert-info text-start">
                                <strong><i class="bi bi-lightbulb me-2"></i>Tip:</strong>
                                FSSAI license numbers follow this format:
                                <code class="d-block mt-2">1 0012 0210 0001</code>
                                <small class="text-muted">
                                    (State Code) (Year) (License Type) (Serial Number)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- QR Scan Modal -->
<div class="modal fade" id="qrScanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-qr-code-scan me-2"></i>Scan License</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="qr-reader" class="mb-3"></div>
                <button class="btn btn-primary w-100" onclick="startQrScanner()">
                    <i class="bi bi-camera-video me-1"></i>Start Camera
                </button>
                <hr>
                <p class="text-muted text-center small">
                    Point your camera at the QR code on the FSSAI license
                </p>
            </div>
        </div>
    </div>
</div>

<script>
let qrScanner = null;

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up event listeners');
    
    // Auto-verify if license number is provided
    <?php if (!empty($licenseNumber)): ?>
    verifyLicense('<?= $licenseNumber ?>');
    <?php endif; ?>

    // Form submission
    const verifyForm = document.getElementById('verifyForm');
    if (verifyForm) {
        console.log('verifyForm found, attaching event listener');
        verifyForm.addEventListener('submit', function(e) {
            console.log('✓ SUBMIT EVENT TRIGGERED');
            e.preventDefault();
            console.log('✓ preventDefault() called');
            
            const licenseField = document.getElementById('licenseNumber');
            console.log('✓ License field found:', licenseField);
            
            const licenseNumber = licenseField.value.trim();
            console.log('✓ License number value:', licenseNumber);
            console.log('✓ License length:', licenseNumber.length);

            if (licenseNumber.length !== 14 || !/^[a-zA-Z0-9]{14}$/.test(licenseNumber)) {
                console.error('✗ Validation failed - invalid format');
                alert('Please enter a valid 14-digit alphanumeric license number');
                return;
            }

            console.log('✓ Validation passed');
            console.log('✓ Calling verifyLicense()');
            
            // Disable button during processing
            const submitBtn = e.target.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;
            
            verifyLicense(licenseNumber);
        });
    } else {
        console.error('✗ verifyForm element not found!');
    }
});

function verifyLicense(licenseNumber) {
    console.log('████ verifyLicense ENTERED');
    const container = document.getElementById('resultsContainer');
    const instructions = document.getElementById('instructionsCard');

    container.innerHTML = '<div class="card border-0 shadow"><div class="card-body text-center py-5"><div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"><span class="visually-hidden">Loading...</span></div><p class="mt-3 text-muted">Verifying license number...</p></div></div>';
    console.log('✓ Set loading spinner');
    
    if (instructions) instructions.style.display = 'none';
    container.style.display = 'block';
    console.log('✓ Container shown');

    const apiUrl = window.location.origin + '/api/verify-license.php';
    console.log('✓ API URL:', apiUrl);
    console.log('→ Starting fetch...');

    fetch(apiUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            license_number: licenseNumber,
            input_method: 'Manual'
        })
    })
    .then(response => {
        console.log('✓ Got response:', response.status);
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('✓ Got JSON:', data);
        displayResults(data);
    })
    .catch(error => {
        console.error('✗ Fetch error:', error.message);
        container.innerHTML = '<div class="alert alert-danger">Error: ' + error.message + '</div>';
    });
}

function displayResults(data) {
    console.log('→ displayResults() STARTED');
    const container = document.getElementById('resultsContainer');
    
    if (!data.success) {
        console.log('✗ API error');
        container.innerHTML = '<div class="alert alert-danger">Error: ' + (data.error || 'Unknown error') + '</div>';
        return;
    }

    if (!data.found) {
        console.log('✗ License not found');
        container.innerHTML = '<div class="card border-0 shadow border-danger"><div class="card-header bg-danger text-white"><h5>License Not Found</h5></div><div class="card-body"><p>License was not found in FSSAI database</p></div></div>';
        container.scrollIntoView({behavior: 'smooth'});
        return;
    }

    console.log('✓ License found');
    const license = data.license;
    const isExpired = data.expiry_status?.is_expired || false;

    container.innerHTML = '<div class="card border-0 shadow"><div class="card-header bg-success text-white"><h5 class="mb-0">Valid License</h5></div><div class="card-body"><h4>' + escapeHtml(license.business_name) + '</h4><p><strong>License:</strong> ' + escapeHtml(license.license_number) + '</p><p><strong>Owner:</strong> ' + escapeHtml(license.owner_name || 'N/A') + '</p><p><strong>Status:</strong> ' + license.status + '</p><p><strong>Expires:</strong> ' + formatDate(license.expiry_date) + '</p><address>' + escapeHtml(license.address) + '<br>' + escapeHtml(license.city) + ', ' + escapeHtml(license.state) + ' ' + escapeHtml(license.pincode) + '</address></div></div>';
    
    console.log('✓ Display updated');
    container.scrollIntoView({behavior: 'smooth'});
    
    // Re-enable verify button
    const submitBtn = document.querySelector('button[type="submit"]');
    if (submitBtn) submitBtn.disabled = false;
    
    console.log('✓ displayResults COMPLETE');
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-IN', {year: 'numeric', month: 'long', day: 'numeric'});
}

function startQrScanner() {
    if (qrScanner) qrScanner.stop();
    qrScanner = new Html5Qrcode("qr-reader");
    qrScanner.start(
        {facingMode: "environment"},
        {fps: 10, qrbox: {width: 250, height: 250}},
        (decodedText) => {
            const licenseMatch = decodedText.match(/[a-zA-Z0-9]{14}/);
            if (licenseMatch) {
                qrScanner.stop();
                bootstrap.Modal.getInstance(document.getElementById('qrScanModal')).hide();
                document.getElementById('licenseNumber').value = licenseMatch[0];
                verifyLicense(licenseMatch[0]);
            }
        },
        (error) => { }
    ).catch(err => {
        alert('Error starting camera: ' + err);
    });
}
</script>

<?php require_once 'includes/footer.php'; ?>
