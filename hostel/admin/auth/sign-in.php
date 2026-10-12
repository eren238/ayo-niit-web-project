<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLES
$emailAddress = trim($_POST['emailAddress']);
$password = trim($_POST['password']);

if ($emailAddress == '') {
    $response = [
        'success' => false,
        'message' => "EMAIL ADDRESS IS REQUIRED, Kindly fill in your email address to continue"
    ];
    goto end;
}

if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
    $response = [
        'success' => false,
        'message' => "INVALID EMAIL ADDRESS! Enter a valid email address and try again"
    ];
    goto end;
}

if ($password == '') {
    $response = [
        'success' => false,
        'message' => "PASSWORD IS REQUIRED, Kindly fill in your password to continue"
    ];
    goto end;
}

$hashPassword = md5($password);

$loginQuery = mysqli_query($conn, "SELECT staff_tab.*, role_tab.role_name, status_tab.status_name FROM staff_tab, role_tab, status_tab WHERE staff_tab.role_id = role_tab.role_id AND staff_tab.status_id = status_tab.status_id AND staff_tab.email_address = '$emailAddress' AND staff_tab.password = '$hashPassword'") or die(mysqli_error($conn));

if (mysqli_num_rows($loginQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "INVALID LOGIN DETAILS! Check your email and password and try again"
    ];
    goto end;
}

$staffData = mysqli_fetch_assoc($loginQuery);

if ($staffData['status_id'] !== '1') {
    $response = [
        'success' => false,
        'message' => "ACCOUNT SUSPENDED OR INACTIVE! Contact system administrator for support"
    ];
    goto end;
}

$staffId = $staffData['staff_id'];

$loginStaffQuery = mysqli_query($conn, "SELECT staff_tab.*, role_tab.role_name, status_tab.status_name FROM staff_tab, role_tab, status_tab WHERE staff_tab.role_id = role_tab.role_id AND staff_tab.status_id = status_tab.status_id AND staff_tab.email_address = '$emailAddress'") or die(mysqli_error($conn));
$staffData = mysqli_fetch_assoc($loginStaffQuery);

$response = [
    'success' => true,
    'message' => "STAFF LOGIN SUCCESSFUL",
    'data' => [
        'staffId' => $staffData['staff_id'],
        'firstName' => $staffData['first_name'],
        'lastName' => $staffData['last_name'],
        'emailAddress' => $staffData['email_address'],
        'roleName' => $staffData['role_name'],
        'lastLogin' => $staffData['last_login'], 
        'statusName' => $staffData['status_name'],
    ]
];
end:
echo json_encode($response);
?>