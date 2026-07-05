<?php
/**
 * Fake License Report Submission API
 * FSSAI License Verification System
 *
 * Handles submission of reported fake/suspicious licenses
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}

define('APP_NAME', 'FSSAI_Verification');
require_once __DIR__ . '/../config/database.php';

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate license number format
 */
function isValidLicenseFormat($license) {
    $cleaned = preg_replace('/[^a-zA-Z0-9]/', '', $license);
    return strlen($cleaned) === 14 && ctype_alnum($cleaned);
}

/**
 * Handle file upload
 */
function handleFileUpload($file) {
    $uploadDir = __DIR__ . '/../assets/uploads/';

    // Create upload directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Validate file
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'File upload error code: ' . $file['error']];
    }

    // Check file size
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'error' => 'File size exceeds 5MB limit'];
    }

    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'error' => 'Invalid file type. Only images are allowed.'];
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFilename = uniqid('evidence_') . '_' . time() . '.' . strtolower($extension);
    $uploadPath = $uploadDir . $newFilename;
    $webPath = 'assets/uploads/' . $newFilename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return ['success' => false, 'error' => 'Failed to save uploaded file'];
    }

    return ['success' => true, 'filename' => $newFilename, 'path' => $webPath];
}

// Parse input data
$input = json_decode(file_get_contents('php://input'), true);
$isJson = $input !== null;

// Get form data
if ($isJson) {
    $licenseNumber = $input['license_number'] ?? '';
    $reporterName = $input['reporter_name'] ?? '';
    $reporterEmail = $input['reporter_email'] ?? '';
    $reporterPhone = $input['reporter_phone'] ?? '';
    $location = $input['location'] ?? '';
    $city = $input['city'] ?? '';
    $state = $input['state'] ?? '';
    $address = $input['address'] ?? '';
    $description = $input['description'] ?? '';
} else {
    $licenseNumber = $_POST['license_number'] ?? '';
    $reporterName = $_POST['reporter_name'] ?? '';
    $reporterEmail = $_POST['reporter_email'] ?? '';
    $reporterPhone = $_POST['reporter_phone'] ?? '';
    $location = $_POST['location'] ?? '';
    $city = $_POST['city'] ?? '';
    $state = $_POST['state'] ?? '';
    $address = $_POST['address'] ?? '';
    $description = $_POST['description'] ?? '';
}

// Handle file upload
$evidenceImage = null;
$evidenceImagePath = null;

if ($isJson && isset($input['evidence_image_data'])) {
    // Handle base64 encoded image
    $imageData = $input['evidence_image_data'];
    if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
        $imageData = substr($imageData, strpos($imageData, ',') + 1);
        $type = strtolower($type[1]);

        $uploadDir = __DIR__ . '/../assets/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $newFilename = uniqid('evidence_') . '_' . time() . '.' . $type;
        $uploadPath = $uploadDir . $newFilename;
        $webPath = 'assets/uploads/' . $newFilename;

        $decoded = base64_decode($imageData);
        if ($decoded !== false && file_put_contents($uploadPath, $decoded)) {
            $evidenceImage = $newFilename;
            $evidenceImagePath = $webPath;
        }
    }
} elseif (!empty($_FILES['evidence_image']['name'])) {
    // Handle regular file upload
    $uploadResult = handleFileUpload($_FILES['evidence_image']);
    if ($uploadResult['success']) {
        $evidenceImage = $uploadResult['filename'];
        $evidenceImagePath = $uploadResult['path'];
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $uploadResult['error']
        ]);
        exit();
    }
}

// Validate required fields
$licenseNumber = sanitizeInput($licenseNumber);

if (empty($licenseNumber)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'License number is required'
    ]);
    exit();
}

// Clean license number (remove non-alphanumeric)
$licenseNumber = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $licenseNumber));

if (!isValidLicenseFormat($licenseNumber)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid license format. Must be exactly 14 alphanumeric characters.',
        'format_hint' => 'Example: 10012021000001'
    ]);
    exit();
}

if (empty($location)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Location of sighting is required'
    ]);
    exit();
}

try {
    $db = getDB();

    // Check if this license already exists in valid_licenses
    $stmt = $db->prepare("SELECT id FROM valid_licenses WHERE license_number = :license_number");
    $stmt->execute([':license_number' => $licenseNumber]);

    if ($stmt->fetch()) {
        // License exists in database - this might be a false report
        // Still allow the report but add a note
        $adminNotes = 'NOTE: This license number exists in the official database. Report may be fraudulent or about a different issue.';
    } else {
        $adminNotes = null;
    }

    // Insert the report
    $stmt = $db->prepare("
        INSERT INTO reported_fake_licenses (
            license_number,
            reporter_name,
            reporter_email,
            reporter_phone,
            location,
            city,
            state,
            address,
            description,
            evidence_image,
            evidence_image_path,
            admin_notes
        ) VALUES (
            :license_number,
            :reporter_name,
            :reporter_email,
            :reporter_phone,
            :location,
            :city,
            :state,
            :address,
            :description,
            :evidence_image,
            :evidence_image_path,
            :admin_notes
        )
    ");

    $stmt->execute([
        ':license_number' => $licenseNumber,
        ':reporter_name' => sanitizeInput($reporterName),
        ':reporter_email' => sanitizeInput($reporterEmail),
        ':reporter_phone' => sanitizeInput($reporterPhone),
        ':location' => sanitizeInput($location),
        ':city' => sanitizeInput($city),
        ':state' => sanitizeInput($state),
        ':address' => sanitizeInput($address),
        ':description' => sanitizeInput($description),
        ':evidence_image' => $evidenceImage,
        ':evidence_image_path' => $evidenceImagePath,
        ':admin_notes' => $adminNotes
    ]);

    $reportId = $db->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Report submitted successfully',
        'report_id' => $reportId,
        'reference_number' => 'FSSAI-RPT-' . str_pad($reportId, 6, '0', STR_PAD_LEFT),
        'next_steps' => [
            'Your report has been submitted for review',
            'Our team will investigate within 5-7 business days',
            'You will be contacted if additional information is needed',
            'Reference number: FSSAI-RPT-' . str_pad($reportId, 6, '0', STR_PAD_LEFT)
        ]
    ]);

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred while submitting your report. Please try again later.'
    ]);
}
?>
