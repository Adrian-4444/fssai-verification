<?php
/**
 * Admin Dashboard
 * FSSAI License Verification System
 *
 * View and manage reported fake licenses
 */

session_start();

// Check authentication
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

define('APP_NAME', 'FSSAI_Verification');
require_once __DIR__ . '/../config/database.php';

$db = getDB();
$adminUsername = $_SESSION['admin_username'];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $reportId = $_POST['report_id'] ?? 0;
    $action = $_POST['action'];
    $adminNotes = $_POST['admin_notes'] ?? '';

    $statusMap = [
        'under_review' => 'Under Review',
        'verified_fake' => 'Verified Fake',
        'dismissed' => 'Dismissed',
        'pending' => 'Pending'
    ];

    if (isset($statusMap[$action]) && $reportId > 0) {
        try {
            $stmt = $db->prepare("
                UPDATE reported_fake_licenses
                SET status = :status,
                    admin_notes = :admin_notes,
                    reviewed_at = NOW(),
                    reviewed_by = :reviewed_by
                WHERE id = :id
            ");
            $stmt->execute([
                ':status' => $statusMap[$action],
                ':admin_notes' => $adminNotes,
                ':reviewed_by' => $adminUsername,
                ':id' => $reportId
            ]);
            $success = 'Report status updated successfully';
        } catch (PDOException $e) {
            $error = 'Failed to update report status';
            error_log("Update Error: " . $e->getMessage());
        }
    }
}

// Get statistics
$stats = $db->query("
    SELECT
        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'Under Review' THEN 1 ELSE 0 END) as under_review,
        SUM(CASE WHEN status = 'Verified Fake' THEN 1 ELSE 0 END) as verified_fake,
        SUM(CASE WHEN status = 'Dismissed' THEN 1 ELSE 0 END) as dismissed,
        COUNT(*) as total
    FROM reported_fake_licenses
")->fetch();

// Get filter
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';

// Build query
$whereClause = '1=1';
$params = [];

if ($filter !== 'all') {
    $whereClause .= " AND r.status = :status";
    $params[':status'] = ucfirst(str_replace('_', ' ', $filter));
}

if (!empty($search)) {
    $whereClause .= " AND (r.license_number LIKE :search OR r.location LIKE :search OR r.reporter_name LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

// Get reports with pagination
$page = max(1, $_GET['page'] ?? 1);
$perPage = 10;
$offset = ($page - 1) * $perPage;

$stmt = $db->prepare("
    SELECT
        r.id,
        r.license_number,
        r.reporter_name,
        r.reporter_email,
        r.reporter_phone,
        r.location,
        r.city,
        r.state,
        r.description,
        r.evidence_image,
        r.evidence_image_path,
        r.status,
        r.admin_notes,
        r.reported_at,
        r.reviewed_at,
        r.reviewed_by,
        (SELECT COUNT(*) FROM verification_logs v WHERE v.license_number = r.license_number) as verification_count
    FROM reported_fake_licenses r
    WHERE $whereClause
    ORDER BY r.reported_at DESC
    LIMIT :limit OFFSET :offset
");

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$reports = $stmt->fetchAll();

// Get total count for pagination
$countStmt = $db->prepare("SELECT COUNT(*) as count FROM reported_fake_licenses r WHERE $whereClause");
foreach ($params as $key => $value) {
    $countStmt->bindValue($key, $value);
}
$countStmt->execute();
$totalReports = $countStmt->fetch()['count'];
$totalPages = ceil($totalReports / $perPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FSSAI Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-shield-check me-2"></i>FSSAI Admin Dashboard
            </a>
            <div class="navbar-nav ms-auto">
                <span class="nav-item nav-link text-light me-3">
                    <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($adminUsername) ?>
                </span>
                <a class="btn btn-outline-light btn-sm" href="logout.php">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="dashboard.php?filter=all" class="list-group-item list-group-item-action <?= $filter === 'all' ? 'active' : '' ?>">
                                <i class="bi bi-inbox me-2"></i>All Reports
                            </a>
                            <a href="dashboard.php?filter=pending" class="list-group-item list-group-item-action <?= $filter === 'pending' ? 'active' : '' ?>">
                                <i class="bi bi-clock me-2"></i>Pending
                            </a>
                            <a href="dashboard.php?filter=under_review" class="list-group-item list-group-item-action <?= $filter === 'under_review' ? 'active' : '' ?>">
                                <i class="bi bi-eye me-2"></i>Under Review
                            </a>
                            <a href="dashboard.php?filter=verified_fake" class="list-group-item list-group-item-action <?= $filter === 'verified_fake' ? 'active' : '' ?>">
                                <i class="bi bi-x-circle me-2"></i>Verified Fake
                            </a>
                            <a href="dashboard.php?filter=dismissed" class="list-group-item list-group-item-action <?= $filter === 'dismissed' ? 'active' : '' ?>">
                                <i class="bi bi-check-circle me-2"></i>Dismissed
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10">
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-2">
                        <div class="card border-0 shadow-sm bg-primary text-white">
                            <div class="card-body text-center">
                                <h3><?= $stats['total'] ?? 0 ?></h3>
                                <small>Total Reports</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-0 shadow-sm bg-warning text-dark">
                            <div class="card-body text-center">
                                <h3><?= $stats['pending'] ?? 0 ?></h3>
                                <small>Pending</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-0 shadow-sm bg-info text-white">
                            <div class="card-body text-center">
                                <h3><?= $stats['under_review'] ?? 0 ?></h3>
                                <small>Under Review</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-0 shadow-sm bg-danger text-white">
                            <div class="card-body text-center">
                                <h3><?= $stats['verified_fake'] ?? 0 ?></h3>
                                <small>Verified Fake</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-0 shadow-sm bg-secondary text-white">
                            <div class="card-body text-center">
                                <h3><?= $stats['dismissed'] ?? 0 ?></h3>
                                <small>Dismissed</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <a href="../index.php" class="card border-0 shadow-sm text-decoration-none">
                            <div class="card-body text-center text-dark">
                                <i class="bi bi-house-door" style="font-size: 1.5rem;"></i>
                                <div class="mt-1"><small>Go to Site</small></div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Alerts -->
                <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Search and Filter -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="search" placeholder="Search by license, location, or reporter" value="<?= htmlspecialchars($search) ?>">
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" name="filter">
                                    <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All Status</option>
                                    <option value="pending" <?= $filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="under_review" <?= $filter === 'under_review' ? 'selected' : '' ?>>Under Review</option>
                                    <option value="verified_fake" <?= $filter === 'verified_fake' ? 'selected' : '' ?>>Verified Fake</option>
                                    <option value="dismissed" <?= $filter === 'dismissed' ? 'selected' : '' ?>>Dismissed</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search me-1"></i>Search
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Reports Table -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>License Reports</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>License #</th>
                                        <th>Reporter</th>
                                        <th>Location</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($reports)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mb-0 mt-2">No reports found</p>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($reports as $report): ?>
                                        <tr>
                                            <td>
                                                <code><?= htmlspecialchars($report['license_number']) ?></code>
                                                <?php if ($report['verification_count'] > 0): ?>
                                                <br><small class="text-muted">
                                                    <i class="bi bi-search"></i> <?= $report['verification_count'] ?> searches
                                                </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($report['reporter_name'] ?: 'Anonymous') ?>
                                                <?php if ($report['reporter_email']): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars($report['reporter_email']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($report['location']) ?>
                                                <?php if ($report['city']): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars($report['city']) ?>, <?= htmlspecialchars($report['state']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= date('M d, Y', strtotime($report['reported_at'])) ?>
                                            </td>
                                            <td>
                                                <?php
                                                $statusBadges = [
                                                    'Pending' => 'warning',
                                                    'Under Review' => 'info',
                                                    'Verified Fake' => 'danger',
                                                    'Dismissed' => 'secondary'
                                                ];
                                                $badge = $statusBadges[$report['status']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?= $badge ?>"><?= htmlspecialchars($report['status']) ?></span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick="viewReport(<?= $report['id'] ?>)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php if ($totalPages > 1): ?>
                    <div class="card-footer bg-white">
                        <nav>
                            <ul class="pagination mb-0 justify-content-center">
                                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>&filter=<?= $filter ?>&search=<?= urlencode($search) ?>">Previous</a>
                                </li>
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&filter=<?= $filter ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                                </li>
                                <?php endfor; ?>
                                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>&filter=<?= $filter ?>&search=<?= urlencode($search) ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Detail Modal -->
    <div class="modal fade" id="reportModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Report Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="reportModalBody">
                    <!-- Loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewReport(id) {
            fetch('view-report.php?id=' + id)
                .then(r => r.text())
                .then(html => {
                    document.getElementById('reportModalBody').innerHTML = html;
                    new bootstrap.Modal(document.getElementById('reportModal')).show();
                });
        }
    </script>
</body>
</html>
