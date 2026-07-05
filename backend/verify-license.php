<?php
/**
 * License Verification API
 * FSSAI License Verification System
 *
 * Verifies a license number against the database
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
 * Sanitize and validate license number
 */
function sanitizeLicenseNumber($input) {
    // Remove all non-alphanumeric characters
    $cleaned = preg_replace('/[^a-zA-Z0-9]/', '', $input);
    // Convert to uppercase
    return strtoupper($cleaned);
}

/**
 * Validate license number format (14 characters)
 */
function isValidLicenseFormat($license) {
    return strlen($license) === 14 && ctype_alnum($license);
}

/**
 * Log verification attempt
 */
function logVerification($db, $licenseNumber, $inputMethod, $result) {
    try {
        $stmt = $db->prepare("
            INSERT INTO verification_logs (license_number, input_method, result, ip_address, user_agent)
            VALUES (:license_number, :input_method, :result, :ip_address, :user_agent)
        ");
        $stmt->execute([
            ':license_number' => $licenseNumber,
            ':input_method' => $inputMethod,
            ':result' => $result,
            ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ]);
    } catch (PDOException $e) {
        error_log("Failed to log verification: " . $e->getMessage());
    }
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);
$licenseNumber = $_POST['license_number'] ?? $input['license_number'] ?? '';
$inputMethod = $_POST['input_method'] ?? $input['input_method'] ?? 'Manual';

// Sanitize input
$licenseNumber = sanitizeLicenseNumber($licenseNumber);

// Validate license format
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
        'error' => 'Invalid license format. FSSAI license numbers must be exactly 14 alphanumeric characters.',
        'format_hint' => 'Example: 10012021000001'
    ]);
    exit();
}

try {
    $db = getDB();

    // Query for license
    $stmt = $db->prepare("
        SELECT
            license_number,
            business_name,
            owner_name,
            address,
            city,
            state,
            pincode,
            license_type,
            issue_date,
            expiry_date,
            status,
            food_category
        FROM valid_licenses
        WHERE license_number = :license_number
    ");

    $stmt->execute([
        ':license_number' => $licenseNumber
    ]);

    $license = $stmt->fetch();

    if ($license) {
        // License found - check status
        $result = 'Valid';

        // Check if expired
        $expiryDate = new DateTime($license['expiry_date']);
        $today = new DateTime();
        $isExpired = $today > $expiryDate;

        // Check if status is active
        $isActive = $license['status'] === 'Active';

        if ($isExpired || !$isActive) {
            $result = 'Expired';
            http_response_code(200); // Still return 200, but with expired status
            echo json_encode([
                'success' => true,
                'found' => true,
                'status' => 'warning',
                'result' => $result,
                'message' => $isExpired ? 'License has expired' : 'License is not active',
                'license' => $license,
                'expiry_status' => [
                    'expiry_date' => $license['expiry_date'],
                    'is_expired' => $isExpired,
                    'days_until_expiry' => $isExpired ? 0 : $today->diff($expiryDate)->days
                ]
            ]);
            logVerification($db, $licenseNumber, $inputMethod, $result);
            exit();
        } else {
            // Valid and active license
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'found' => true,
                'status' => 'success',
                'result' => $result,
                'message' => 'Valid FSSAI License',
                'license' => $license,
                'expiry_status' => [
                    'expiry_date' => $license['expiry_date'],
                    'is_expired' => false,
                    'days_until_expiry' => $today->diff($expiryDate)->days
                ]
            ]);
            logVerification($db, $licenseNumber, $inputMethod, $result);
            exit();
        }

    } else {
        // License not found
        $result = 'Invalid';
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'found' => false,
            'status' => 'error',
            'result' => $result,
            'message' => 'License number not found in FSSAI database',
            'license_number' => $licenseNumber,
            'suggestions' => [
                'Double-check the license number for typos',
                'Ensure all 14 characters are entered correctly',
                'Contact the business to verify their license number',
                'Report this as a suspicious license if you suspect fraud'
            ]
        ]);
        logVerification($db, $licenseNumber, $inputMethod, $result);
        exit();
    }

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred while verifying the license. Please try again later.'
    ]);
    exit();
}
?>
