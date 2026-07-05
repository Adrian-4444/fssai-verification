<?php
/**
 * View Report Details (AJAX endpoint)
 * FSSAI License Verification System
 */

session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    echo 'Access denied';
    exit();
}

define('APP_NAME', 'FSSAI_Verification');
require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'] ?? 0;

if ($id <= 0) {
    echo '<p class="text-danger">Invalid report ID</p>';
    exit();
}

try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM reported_fake_licenses WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $report = $stmt->fetch();

    if (!$report) {
        echo '<p class="text-danger">Report not found</p>';
        exit();
    }

    // Check if license exists in valid database
    $stmt = $db->prepare("SELECT * FROM valid_licenses WHERE license_number = :license");
    $stmt->execute([':license' => $report['license_number']]);
    $validLicense = $stmt->fetch();
} catch (PDOException $e) {
    echo '<p class="text-danger">Error loading report</p>';
    exit();
}
?>

<div class="row">
    <div class="col-md-6">
        <h6 class="text-primary">License Information</h6>
        <table class="table table-sm table-borderless">
            <tr>
                <th width="40%">License Number:</th>
                <td><code class="fs-6"><?= htmlspecialchars($report['license_number']) ?></code></td>
            </tr>
            <tr>
                <th>Reporter Name:</th>
                <td><?= htmlspecialchars($report['reporter_name'] ?: 'Anonymous') ?></td>
            </tr>
            <tr>
                <th>Email:</th>
                <td><?= htmlspecialchars($report['reporter_email']) ?: '-' ?></td>
            </tr>
            <tr>
                <th>Phone:</th>
                <td><?= htmlspecialchars($report['reporter_phone']) ?: '-' ?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <h6 class="text-primary">Location Details</h6>
        <table class="table table-sm table-borderless">
            <tr>
                <th width="40%">Location:</th>
                <td><?= htmlspecialchars($report['location']) ?></td>
            </tr>
            <tr>
                <th>City:</th>
                <td><?= htmlspecialchars($report['city'] ?: '-') ?></td>
            </tr>
            <tr>
                <th>State:</th>
                <td><?= htmlspecialchars($report['state'] ?: '-') ?></td>
            </tr>
            <tr>
                <th>Address:</th>
                <td><?= nl2br(htmlspecialchars($report['address'] ?: 'Not provided')) ?></td>
            </tr>
        </table>
    </div>
</div>

<div class="mt-3">
    <h6 class="text-primary">Description</h6>
    <div class="p-3 bg-light rounded">
        <?= nl2br(htmlspecialchars($report['description'] ?: 'No description provided')) ?>
    </div>
</div>

<?php if ($report['evidence_image_path']): ?>
<div class="mt-3">
    <h6 class="text-primary">Evidence Image</h6>
    <div class="text-center">
        <img src="../<?= htmlspecialchars($report['evidence_image_path']) ?>"
             alt="Evidence"
             class="img-fluid rounded border"
             style="max-height: 300px;"
             onclick="this.requestFullscreen ? this.requestFullscreen() : null">
        <p class="text-muted mt-2 small"><i class="bi bi-fullscreen me-1"></i>Click image to fullscreen</p>
    </div>
</div>
<?php endif; ?>

<?php if ($validLicense): ?>
<div class="mt-3 alert alert-warning">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong>Warning:</strong> This license number exists in the valid licenses database.
    <br><small>The report may be about a different issue (e.g., misuse of valid license, expired license being used, etc.)</small>
</div>
<?php endif; ?>

<div class="mt-4">
    <h6 class="text-primary">Update Status</h6>
    <form method="POST" action="dashboard.php" class="row g-3">
        <input type="hidden" name="report_id" value="<?= $report['id'] ?>">
        <div class="col-md-4">
            <select class="form-select" name="action" required>
                <option value="">Select Action</option>
                <option value="under_review" <?= $report['status'] === 'Under Review' ? 'selected' : '' ?>>Mark Under Review</option>
                <option value="verified_fake" <?= $report['status'] === 'Verified Fake' ? 'selected' : '' ?>>Mark as Verified Fake</option>
                <option value="dismissed" <?= $report['status'] === 'Dismissed' ? 'selected' : '' ?>>Dismiss Report</option>
                <option value="pending" <?= $report['status'] === 'Pending' ? 'selected' : '' ?>>Return to Pending</option>
            </select>
        </div>
        <div class="col-md-6">
            <input type="text" class="form-control" name="admin_notes"
                   placeholder="Admin notes (optional)"
                   value="<?= htmlspecialchars($report['admin_notes'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Update</button>
        </div>
    </form>
</div>

<div class="mt-3 border-top pt-3">
    <small class="text-muted">
        <strong>Reported:</strong> <?= date('F d, Y \a\t g:i A', strtotime($report['reported_at'])) ?><br>
        <?php if ($report['reviewed_at']): ?>
        <strong>Reviewed:</strong> <?= date('F d, Y \a\t g:i A', strtotime($report['reviewed_at'])) ?> by <?= htmlspecialchars($report['reviewed_by']) ?>
        <?php endif; ?>
    </small>
</div>
