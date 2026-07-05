
<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($uri) {
    case '/':
    case '/index.php':
        require __DIR__ . '/../index.php';
        break;

    case '/verify':
    case '/verify.php':
        require __DIR__ . '/../verify.php';
        break;

    case '/report':
    case '/report.php':
        require __DIR__ . '/../report.php';
        break;

    case '/admin/login':
    case '/admin/login.php':
        require __DIR__ . '/../admin/login.php';
        break;

    case '/admin/dashboard':
    case '/admin/dashboard.php':
        require __DIR__ . '/../admin/dashboard.php';
        break;

    case '/admin/view-report':
    case '/admin/view-report.php':
        require __DIR__ . '/../admin/view-report.php';
        break;

    case '/backend/verify-license':
    case '/backend/verify-license.php':
        require __DIR__ . '/../backend/verify-license.php';
        break;

    case '/backend/submit-report':
    case '/backend/submit-report.php':
        require __DIR__ . '/../backend/submit-report.php';
        break;

    default:
        http_response_code(404);
        echo "404 Not Found";
}

?>