<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);
if (function_exists('mysqli_report')) {
    @mysqli_report(MYSQLI_REPORT_OFF);
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Content-Type, Accept, apiKey, userOsBrowser, userIpAddress, userDeviceId");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json; charset=UTF-8');

// Handle preflight OPTIONS requests
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$httpHost = $_SERVER['HTTP_HOST'] ?? '';
$isLocal = in_array($httpHost, ['localhost', '127.0.0.1']) || strpos($httpHost, 'localhost:') === 0 || strpos($httpHost, '127.0.0.1:') === 0;

if ($isLocal) {
    // Local development (XAMPP)
    $_HOST_NAME = "localhost";  
    $_DB_USERNAME = "root";
    $_DB_PASSWORD = "";
    $_DB_NAME     = "hostel_db";

    $conn = @mysqli_connect($_HOST_NAME, $_DB_USERNAME, $_DB_PASSWORD, $_DB_NAME);
    if (!$conn) {
        $conn = @mysqli_connect("127.0.0.1", "root", "", "hostel_db");
    }
} else {
    // Online production (hostelhub.niitwebdev.com.ng)
    $_HOST_NAME = "localhost";  
    $_DB_USERNAME = "niitwebd_hostelhub";
    $_DB_PASSWORD = "UPvXCZWtzGS48K6rn9Jb";
    $_DB_NAME     = "niitwebd_hosttel_hub_db";

    $conn = @mysqli_connect($_HOST_NAME, $_DB_USERNAME, $_DB_PASSWORD, $_DB_NAME);
    if (!$conn) {
        $conn = @mysqli_connect("127.0.0.1", $_DB_USERNAME, $_DB_PASSWORD, $_DB_NAME);
    }
    if (!$conn) {
        $conn = @mysqli_connect("102.68.98.184", $_DB_USERNAME, $_DB_PASSWORD, $_DB_NAME);
    }
}

if (!$conn) {
    echo json_encode([
        'success' => false,
        'message' => "Unable to connect to database: " . mysqli_connect_error()
    ]);
    exit();
}
?>