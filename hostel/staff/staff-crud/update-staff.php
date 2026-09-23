php


<?php require_once __DIR__ . '/../../config/connection.php'; ?>
<?php
// DECLARATION OF VARIABLE
$staffId      = trim($_POST['staffId'] ?? '');
$firstName    = trim($_POST['firstName'] ?? '');
$lastName     = trim($_POST['lastName'] ?? '');
$emailAddress = trim($_POST['emailAddress'] ?? '');
$roleId       = trim($_POST['roleId'] ?? '');
$statusId     = trim($_POST['statusId'] ?? '');
if ($staffId == '') {
    $response = [
        'success' => false,
        'message' => "STAFF ID IS REQUIRED, Kindly fill in the staff ID to continue"
    ];
    goto end;
}
if ($firstName == '') {
    $response = [
        'success' => false,
        'message' => "FIRST NAME IS REQUIRED, Kindly fill in the first name to continue"
    ];
    goto end;
}
if ($lastName == '') {
    $response = [
        'success' => false,
        'message' => "LAST NAME IS REQUIRED, Kindly fill in the last name to continue"
    ];
    goto end;
}
if ($emailAddress == '') {
    $response = [
        'success' => false,
        'message' => "EMAIL ADDRESS IS REQUIRED, Kindly fill in the email address to continue"
    ];
    goto end;
}
if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
    $response = [
        'response' => 102,
        'success'  => false,
        'message'  => "INVALID EMAIL ADDRESS! Enter a valid email address and try again"
    ];
    goto end;
}
if ($roleId == '') {
    $response = [
        'success' => false,
        'message' => "ROLE ID IS REQUIRED, Kindly fill in the role ID to continue"
    ];
    goto end;
}
if ($statusId == '') {
    $response = [
        'success' => false,
        'message' => "STATUS ID IS REQUIRED, Kindly fill in the status ID to continue"
    ];
    goto end;
}
// 1. CHECK IF STAFF EXISTS
$checkStaffQuery = mysqli_query($conn, "SELECT * FROM staff_tab WHERE staff_id = '$staffId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkStaffQuery) == 0) {
    $response = [
        'success' => false,
        'message' => 'STAFF NOT FOUND'
    ];
    goto end;
}

$emailCheck = mysqli_query($conn, "SELECT * FROM staff_tab WHERE email_address = '$emailAddress' AND staff_id != '$staffId' LIMIT 1") or die(mysqli_error($conn));
if (mysqli_num_rows($emailCheck) > 0) {
    $response = [
        'success' => false,
        'message' => "EMAIL ALREADY EXISTS!! This email $emailAddress is already used by someone. Kindly use another email address to continue"
    ];
    goto end;
}

mysqli_query($conn, "UPDATE staff_tab SET 
    first_name    = '$firstName', 
    last_name     = '$lastName', 
    email_address = '$emailAddress', 
    role_id       = '$roleId', 
    status_id     = '$statusId', 
    updated_at    = NOW() 
    WHERE staff_id = '$staffId'") or die(mysqli_error($conn));

$updateEachStaffQuery = mysqli_query($conn, "SELECT 
        staff_tab.*, 
        role_tab.role_name, 
        status_tab.status_name 
    FROM staff_tab, role_tab, status_tab 
    WHERE staff_tab.role_id = role_tab.role_id 
      AND staff_tab.status_id = status_tab.status_id 
      AND staff_tab.staff_id = '$staffId'") or die(mysqli_error($conn));
      
$staffData = mysqli_fetch_assoc($updateEachStaffQuery);
$response = [
    'success' => true,
    'message' => "STAFF UPDATE SUCCESSFUL",
    'data'    => [
        'staffId'      => $staffData['staff_id'],
        'firstName'    => $staffData['first_name'],
        'lastName'     => $staffData['last_name'],
        'emailAddress' => $staffData['email_address'],
        'roleId'       => $staffData['role_id'],
        'roleName'     => $staffData['role_name'],
        'statusId'     => $staffData['status_id'],
        'statusName'   => $staffData['status_name'],
        'createdAt'    => $staffData['created_at'],
        'updatedAt'    => $staffData['updated_at']
    ]
];
end:
echo json_encode($response);
?>