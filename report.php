<?php
/**
 * Report Fake License Page
 * FSSAI License Verification System
 */

$pageTitle = 'Report Fake License';
require_once 'includes/header.php';

// Pre-fill license number from URL
$licenseNumber = $_GET['license'] ?? '';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="text-center mb-4">
                <h1 class="display-6 fw-bold text-danger">
                    <i class="bi bi-flag-fill"></i>
                    Report Suspicious License
                </h1>
                <p class="text-muted">Help protect consumers by reporting fake or fraudulent FSSAI licenses</p>
            </div>

            <!-- Success Message (shown after submission) -->
            <div id="successMessage" style="display: none;" class="mb-4">
                <!-- Populated after successful submission -->
            </div>

            <!-- Report Form -->
            <div class="card border-0 shadow" id="reportFormCard">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Report Form</h5>
                </div>
                <div class="card-body p-4">
                    <form id="reportForm" enctype="multipart/form-data">
                        <!-- License Number -->
                        <div class="mb-4">
                            <label for="licenseNumber" class="form-label">
                                <span class="text-danger">*</span> Suspicious License Number
                            </label>
                            <input type="text" class="form-control form-control-lg" id="licenseNumber"
                                   name="license_number"
                                   value="<?= htmlspecialchars($licenseNumber) ?>"
                                   placeholder="Enter 14-digit license number"
                                   pattern="[a-zA-Z0-9]{14}" maxlength="14"
                                   required>
                            <div class="form-text">
                                The license number you want to report as fake/suspicious
                            </div>
                            <?php if (!empty($licenseNumber)): ?>
                            <div class="alert alert-warning mt-2">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                You're reporting license <strong><?= htmlspecialchars($licenseNumber) ?></strong>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Reporter Information -->
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-person-circle me-2"></i>Your Information (Optional)
                        </h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="reporterName" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="reporterName" name="reporter_name"
                                       placeholder="Your name">
                            </div>
                            <div class="col-md-6">
                                <label for="reporterEmail" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="reporterEmail" name="reporter_email"
                                       placeholder="your@email.com">
                            </div>
                            <div class="col-md-12">
                                <label for="reporterPhone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="reporterPhone" name="reporter_phone"
                                       placeholder="+91 XXXXX XXXXX" pattern="[0-9+\-\s]{10,15}">
                            </div>
                        </div>

                        <!-- Location Details -->
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-geo-alt-fill me-2"></i>Location Details
                        </h6>
                        <div class="mb-3">
                            <label for="location" class="form-label">
                                <span class="text-danger">*</span> Where did you see this license?
                            </label>
                            <input type="text" class="form-control" id="location" name="location"
                                   placeholder="e.g., Shop name, market area, landmark" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city"
                                       placeholder="City name">
                            </div>
                            <div class="col-md-6">
                                <label for="state" class="form-label">State</label>
                                <select class="form-select" id="state" name="state">
                                    <option value="">Select State</option>
                                    <option value="Andhra Pradesh">Andhra Pradesh</option>
                                    <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                                    <option value="Assam">Assam</option>
                                    <option value="Bihar">Bihar</option>
                                    <option value="Chhattisgarh">Chhattisgarh</option>
                                    <option value="Delhi">Delhi</option>
                                    <option value="Goa">Goa</option>
                                    <option value="Gujarat">Gujarat</option>
                                    <option value="Haryana">Haryana</option>
                                    <option value="Himachal Pradesh">Himachal Pradesh</option>
                                    <option value="Jharkhand">Jharkhand</option>
                                    <option value="Karnataka">Karnataka</option>
                                    <option value="Kerala">Kerala</option>
                                    <option value="Madhya Pradesh">Madhya Pradesh</option>
                                    <option value="Maharashtra">Maharashtra</option>
                                    <option value="Manipur">Manipur</option>
                                    <option value="Meghalaya">Meghalaya</option>
                                    <option value="Mizoram">Mizoram</option>
                                    <option value="Nagaland">Nagaland</option>
                                    <option value="Odisha">Odisha</option>
                                    <option value="Punjab">Punjab</option>
                                    <option value="Rajasthan">Rajasthan</option>
                                    <option value="Tamil Nadu">Tamil Nadu</option>
                                    <option value="Telangana">Telangana</option>
                                    <option value="Tripura">Tripura</option>
                                    <option value="Uttar Pradesh">Uttar Pradesh</option>
                                    <option value="Uttarakhand">Uttarakhand</option>
                                    <option value="West Bengal">West Bengal</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Complete Address (Optional)</label>
                            <textarea class="form-control" id="address" name="address" rows="2"
                                      placeholder="Street address, building number, etc."></textarea>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label">
                                <span class="text-danger">*</span> Description
                            </label>
                            <textarea class="form-control" id="description" name="description" rows="4"
                                      placeholder="Please describe how you encountered this suspicious license. Include any details about the business, products, or why you believe this license may be fraudulent."
                                      required></textarea>
                            <div class="form-text">
                                Provide as much detail as possible to help with the investigation
                            </div>
                        </div>

                        <!-- Evidence Upload -->
                        <div class="mb-4">
                            <label for="evidenceImage" class="form-label">
                                Evidence Image (Optional)
                            </label>
                            <input type="file" class="form-control" id="evidenceImage" name="evidence_image"
                                   accept="image/*">
                            <div class="form-text">
                                Upload a photo of the license, product packaging, or premises
                                (Max 5MB, JPG/PNG/GIF)
                            </div>
                            <div id="imagePreview" class="mt-2"></div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger btn-lg" id="submitBtn">
                                <i class="bi bi-flag-fill me-2"></i>Submit Report
                            </button>
                        </div>

                        <hr>

                        <div class="alert alert-info mb-0">
                            <strong><i class="bi bi-shield-check me-2"></i>Your report is confidential</strong>
                            <ul class="mb-0 mt-2">
                                <li>Your information (if provided) will not be shared publicly</li>
                                <li>Reports are reviewed by FSSAI officials</li>
                                <li>You may be contacted for additional information</li>
                                <li>False reports may have legal consequences</li>
                            </ul>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Image preview
document.getElementById('evidenceImage')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');

    if (!file) {
        preview.innerHTML = '';
        return;
    }

    // Check file size (5MB limit)
    if (file.size > 5 * 1024 * 1024) {
        preview.innerHTML = '<div class="alert alert-danger py-2">File size must be less than 5MB</div>';
        e.target.value = '';
        return;
    }

    // Check file type
    if (!file.type.startsWith('image/')) {
        preview.innerHTML = '<div class="alert alert-danger py-2">Please select an image file</div>';
        e.target.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        preview.innerHTML = `
            <div class="text-center">
                <img src="${e.target.result}" class="img-fluid rounded border" style="max-height: 200px;">
                <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="removeImage()">
                    <i class="bi bi-trash me-1"></i>Remove
                </button>
            </div>
        `;
    };
    reader.readAsDataURL(file);
});

function removeImage() {
    document.getElementById('evidenceImage').value = '';
    document.getElementById('imagePreview').innerHTML = '';
}

// Form submission
document.getElementById('reportForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const submitBtn = document.getElementById('submitBtn');
    const formData = new FormData(this);

    // Validate license number
    const licenseNumber = formData.get('license_number').trim().toUpperCase().replace(/[^A-Z0-9]/g, '');
    if (licenseNumber.length !== 14) {
        alert('Please enter a valid 14-digit license number');
        return;
    }

    // Validate required fields
    if (!formData.get('location')) {
        alert('Please provide the location where you saw this license');
        return;
    }

    if (!formData.get('description')) {
        alert('Please provide a description of the incident');
        return;
    }

    // Handle image as base64
    const imageFile = document.getElementById('evidenceImage').files[0];
    let imageData = null;

    if (imageFile) {
        imageData = await new Promise((resolve) => {
            const reader = new FileReader();
            reader.onload = (e) => resolve(e.target.result);
            reader.readAsDataURL(imageFile);
        });
    }

    // Prepare data
    const data = {
        license_number: licenseNumber,
        reporter_name: formData.get('reporter_name'),
        reporter_email: formData.get('reporter_email'),
        reporter_phone: formData.get('reporter_phone'),
        location: formData.get('location'),
        city: formData.get('city'),
        state: formData.get('state'),
        address: formData.get('address'),
        description: formData.get('description'),
        evidence_image_data: imageData
    };

    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';

    // Submit to API
    fetch('api/submit-report.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Show success message
            document.getElementById('successMessage').innerHTML = `
                <div class="card border-0 shadow border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-check-circle-fill me-2"></i>Report Submitted Successfully</h5>
                    </div>
                    <div class="card-body">
                        <p class="lead">Thank you for helping protect consumers!</p>
                        <p>Your report has been submitted for review.</p>
                        <div class="alert alert-light">
                            <strong>Reference Number:</strong>
                            <code class="fs-5">${result.reference_number}</code>
                        </div>
                        <p class="small text-muted">
                            Please save this reference number for future correspondence.
                            Our team will investigate within 5-7 business days.
                        </p>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                            <a href="verify.php" class="btn btn-outline-primary">
                                <i class="bi bi-search me-1"></i>Verify Another License
                            </a>
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="bi bi-house me-1"></i>Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('successMessage').style.display = 'block';
            document.getElementById('reportFormCard').style.display = 'none';
            document.getElementById('reportForm').reset();
            document.getElementById('imagePreview').innerHTML = '';
        } else {
            alert('Error: ' + (result.error || 'Failed to submit report'));
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-flag-fill me-2"></i>Submit Report';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while submitting your report. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-flag-fill me-2"></i>Submit Report';
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
