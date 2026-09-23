<?php require_once __DIR__ . '/../../config/connection.php'; ?>
<?php
// DECLARATION OF VARIABLES
$firstName    = trim($_POST['firstName']);
$lastName     = trim($_POST['lastName']);
$gender       = trim($_POST['gender']);
$department   = trim($_POST['department']);
$level        = trim($_POST['level']);
$phoneNumber  = trim($_POST['phoneNumber']);
$emailAddress = trim($_POST['emailAddress']);
$password     = $_POST['password'];
$statusId     = trim($_POST['statusId']);
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
if ($gender == '') {
    $response = [
        'success' => false,
        'message' => "GENDER IS REQUIRED, Kindly fill in the gender to continue"
    ];
    goto end;
}
if ($department == '') {
    $response = [
        'success' => false,
        'message' => "DEPARTMENT IS REQUIRED, Kindly fill in the department to continue"
    ];
    goto end;
}
if ($level == '') {
    $response = [
        'success' => false,
        'message' => "LEVEL IS REQUIRED, Kindly fill in the level to continue"
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
if ($statusId == '') {
    $response = [
        'success' => false,
        'message' => "STATUS ID IS REQUIRED, Kindly fill in the status id to continue"
    ];
    goto end;
}
$emailcheck = mysqli_query($conn, "SELECT * FROM student_tab WHERE email_address = '$emailAddress'") or die(mysqli_error($conn));
if (mysqli_num_rows($emailcheck) > 0) {
    $response = [
        "success" => false,
        "message" => "EMAIL ADDRESS ALREADY EXIST kindly proceed to sign in or forget password"
    ];
    goto end;
}
$studentId    = 'STUDENT' . date("Ymdhis");
$hashPassword = md5($password);
mysqli_query($conn, "INSERT INTO `student_tab`
    (`student_id`, `first_name`, `last_name`, `gender`, `department`, `level`, `phone_number`, `email_address`, `password`, `status_id`, `created_at`, `updated_at`) VALUES
    ('$studentId', '$firstName', '$lastName', '$gender', '$department', '$level', '$phoneNumber', '$emailAddress', '$hashPassword', '$statusId', NOW(), NOW())") or die(mysqli_error($conn));
$createStudentQuery = mysqli_query($conn, "SELECT student_tab.*, status_tab.status_name FROM student_tab, status_tab WHERE student_tab.status_id = status_tab.status_id AND student_tab.email_address = '$emailAddress'") or die(mysqli_error($conn));
$studentData = mysqli_fetch_assoc($createStudentQuery);
$response = [
    'success' => true,
    'message' => "STUDENT CREATED SUCCESSFUL",
    'data' => [
        'studentId'    => $studentData['student_id'],
        'firstName'    => $studentData['first_name'],
        'lastName'     => $studentData['last_name'],
        'gender'       => $studentData['gender'],
        'department'   => $studentData['department'],
        'level'        => $studentData['level'],
        'phoneNumber'  => $studentData['phone_number'],
        'emailAddress' => $studentData['email_address'],
        'statusName'   => $studentData['status_name'],
    ]
];
end:
echo json_encode($response);
?>


