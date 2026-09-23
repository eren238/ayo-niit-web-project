<?php require_once __DIR__ . '/../../config/connection.php'; ?>



<?php
// DECLERATION OF VARIABLE
$userId = trim($_POST['userId']);
$firstName = trim($_POST['firstName']);
$lastName = trim($_POST['lastName']);
$emailAddress = trim($_POST['emailAddress']);
$phoneNumber = trim($_POST['phoneNumber']);
$statusId = 'A';



if ($userId == '') {

    $response = [
        'success' => false,
        'message' => "USER ID IS REQUIRED, Kindly fill in the user ID to continue"
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
        'message' => "EMAIL ADDRESS NAME IS REQUIRED, Kindly fill in the email address to continue"
    ];
    goto end;
}
if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) { /// start if 2
    $response = [
        'response' => 102,
        'success' => false,
        'message' => "INVALID EMAIL ADDRESS! Enter a valid email address and try again",
    ];
    goto end;
}

if ($phoneNumber == '') {
    $response = [
        'success' => false,
        'message' => "PHONE NUMBER IS REQUIRED, Kindly fill in the phone number to continue"
    ];
    goto end;
}

if ($userId == '') {
    $response = [
        'success' => false,
        'message' => 'USER ID REQUIRED'
    ];
   goto end;  

}
$checkUserQuery = mysqli_query($conn, "SELECT * FROM user_tab WHERE user_id = '$userId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkUserQuery) == 0) {
    $response = [
        'success' => false,
        'message' => 'USER NOT FOUND'
    ];
   goto end;  

}

$emailCheck = mysqli_query($conn, "SELECT * FROM user_tab WHERE email_address = '$emailAddress' AND user_id != '$userId' LIMIT 1") or die(mysqli_error($conn));
if (mysqli_num_rows($emailCheck) > 0) {
    $response = [
        'success' => false,
        'message' => "EMAIL ALREADY EXIST!! This email $emailAddress is already used by someone. Kindly use another email address to continue"
    ];
    goto end;
}

$updateEachUserQuery = mysqli_query($conn, "SELECT user_tab.*, status_tab.status_name FROM user_tab, status_tab WHERE user_tab.status_id = status_tab.status_id AND user_tab.user_id = '$userId'") or die(mysqli_error($conn));
$userData = mysqli_fetch_assoc($updateEachUserQuery);

$response = [
    'success' => true,
    'message' => "USER UPDATE SUCCESSFUL",
    'data' => [
        'userId' => $userData['user_id'],
        'firstName' => $userData['first_name'],
        'lastName' => $userData['last_name'],
        'emailAddress' => $userData['email_address'],
        'phoneNumber' => $userData['phone'],
        'statusId' => $userData['status_id'],
        'statusName' => $userData['status_name'],
        'password' => $userData['password'],
        'createdAt' => $userData['created_at'],
        'updatedAt' => $userData['updated_at']
    ]
];
end:
echo json_encode($response);
?> 