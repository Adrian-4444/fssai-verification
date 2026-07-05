<?php
/**
 * Verification Report API
 * FSSAI License Verification System
 *
 * Returns detailed verification report in JSON format
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only accept GET and POST requests
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}

define('APP_NAME', 'FSSAI_Verification');
require_once __DIR__ . '/../config/database.php';

/**
 * Sanitize and validate license number
 */
function sanitizeLicenseNumber($input) {
    $cleaned = preg_replace('/[^a-zA-Z0-9]/', '', $input);
    return strtoupper($cleaned);
}

/**
 * Validate license number format
 */
function isValidLicenseFormat($license) {
    return strlen($license) === 14 && ctype_alnum($license);
}

// Get license number from query or POST data
$licenseNumber = $_GET['license'] ?? $_POST['license'] ?? '';
$licenseNumber = sanitizeLicenseNumber($licenseNumber);

// Validate input
if (empty($licenseNumber)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'License number is required'
    ]);
    exit();
}

if (!isValidLicenseFormat($licenseNumber)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid license format. Must be exactly 14 alphanumeric characters.'
    ]);
    exit();
}

try {
    $db = getDB();
    
    // Fetch license details
    $stmt = $db->prepare("
        SELECT *
        FROM valid_licenses
        WHERE license_number = :license_number
    ");
    $stmt->execute([':license_number' => $licenseNumber]);
    $license = $stmt->fetch();
    
    // If license not found
    if (!$license) {
        http_response_code(404);
        echo json_encode([
            'success' => true,
            'found' => false,
            'license_number' => $licenseNumber,
            'message' => 'License not found in database'
        ]);
        exit();
    }
    
    // Fetch verification logs
    $stmt = $db->prepare("
        SELECT *
        FROM verification_logs
        WHERE license_number = :license_number
        ORDER BY verified_at DESC
    ");
    $stmt->execute([':license_number' => $licenseNumber]);
    $verificationLogs = $stmt->fetchAll();
    
    // Fetch reported info
    $stmt = $db->prepare("
        SELECT *
        FROM reported_fake_licenses
        WHERE license_number = :license_number
        ORDER BY reported_at DESC
        LIMIT 1
    ");
    $stmt->execute([':license_number' => $licenseNumber]);
    $reportedInfo = $stmt->fetch();
    
    // Calculate statistics
    $stats = [
        'total_verifications' => count($verificationLogs),
        'valid_count' => 0,
        'invalid_count' => 0,
        'expired_count' => 0
    ];
    
    foreach ($verificationLogs as $log) {
        match($log['result']) {
            'Valid' => $stats['valid_count']++,
            'Invalid' => $stats['invalid_count']++,
            'Expired' => $stats['expired_count']++,
            default => null
        };
    }
    
    // Check if license is expired
    $expiryDate = new DateTime($license['expiry_date']);
    $today = new DateTime();
    $isExpired = $today > $expiryDate;
    $daysUntilExpiry = $today->diff($expiryDate)->days;
    if ($isExpired) {
        $daysUntilExpiry = -$daysUntilExpiry;
    }
    
    // Build response
    $response = [
        'success' => true,
        'found' => true,
        'license' => $license,
        'expiry_status' => [
            'is_expired' => $isExpired,
            'days_until_expiry' => $daysUntilExpiry,
            'expiry_date' => $license['expiry_date']
        ],
        'verification_history' => $verificationLogs,
        'statistics' => $stats,
        'reported_info' => $reportedInfo,
        'generated_at' => date('c')
    ];
    
    http_response_code(200);
    echo json_encode($response);
    
} catch (PDOException $e) {
    error_log("Report API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
}
?>
