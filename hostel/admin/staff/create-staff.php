<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
// DECLERATION OF VARIABLE

$firstName = trim($_POST['firstName']);
$lastName = trim($_POST['lastName']);
$emailAddress = trim($_POST['emailAddress']);
$password = $_POST['password'];
$roleId = $_POST['roleId'];
$statusId = $_POST['statusId'];

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
        'message' => "EMAIL ADDRESS NAME IS REQUIRED, Kindly fill in the email address to continue"
    ];
    goto end;
}

if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
    $response = [
        'response' => 102,
        'success' => false,
        'message' => "INVALID EMAIL ADDRESS! Enter a valid email address and try again",
    ];
    goto end;
}

if ($password == '') {
    $response = [
        'success' => false,
        'message' => "PASSWORD IS REQUIRED, Kindly fill in the password to continue"
    ];
    goto end;
}

if ($roleId == '') {
    $response = [
        'success' => false,
        'message' => "ROLE ID IS REQUIRED, Kindly fill in the role id to continue"
    ];
    goto end;
}

if ($statusId == '') {
    $response = [
        'success' => false,
        'message' => "STATUS ID IS REQUIRED, Kindly fill in the status id to continue"
    ];
    goto end;
}

$emailcheck = mysqli_query($conn, "SELECT * FROM staff_tab WHERE email_address = '$emailAddress'") or die(mysqli_error($conn));
if (mysqli_num_rows($emailcheck) > 0) {
    $response = [
        "success" => false,
        "message" => "EMAIL ADDRESS ALREADY EXIST kindly proceed to sign in or forget password"
    ];
    goto end;
}

$staffId = 'STAFF' . date("Ymdhis");
$hashPassword = md5($password);

mysqli_query($conn, "INSERT INTO `staff_tab`
    ( `staff_id`, `first_name`, `last_name`, `email_address`,  `status_id`,  `password`, `role_id`, `created_at`, `updated_at`) VALUES
    ('$staffId', '$firstName', '$lastName', '$emailAddress',   '$statusId',  '$hashPassword',  '$roleId', NOW(), NOW())") or die(mysqli_error($conn));

$createStaffQuery = mysqli_query($conn, "SELECT staff_tab.*, role_tab.role_name, status_tab.status_name FROM staff_tab, role_tab, status_tab WHERE staff_tab.role_id = role_tab.role_id AND staff_tab.status_id = status_tab.status_id AND staff_tab.email_address = '$emailAddress'") or die(mysqli_error($conn));
$staffData = mysqli_fetch_assoc($createStaffQuery);

$response = [
    'success' => true,
    'message' => "STAFF CREATED SUCCESSFUL",
    'data' => [
        'staffId' => $staffData['staff_id'],
        'firstName' => $staffData['first_name'],
        'lastName' => $staffData['last_name'],
        'emailAddress' => $staffData['email_address'],
        'password' => $staffData['password'],
        'roleName' => $staffData['role_name'],
        'lastLogin' => $staffData['last_login'], 
        'statusName' => $staffData['status_name'],
    ]
];

end:
echo json_encode($response);
?>