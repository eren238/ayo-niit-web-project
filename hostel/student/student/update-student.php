php


<?php require_once __DIR__ . '/../../config/connection.php'; ?>
<?php
// DECLARATION OF VARIABLE
$studentId    = trim($_POST['studentId']);
$firstName    = trim($_POST['firstName']);
$lastName     = trim($_POST['lastName']);
$gender       = trim($_POST['gender']);
$department   = trim($_POST['department']);
$level        = trim($_POST['level']);
$phoneNumber  = trim($_POST['phoneNumber']);
$emailAddress = trim($_POST['emailAddress']);
$statusId     = trim($_POST['statusId']);
if ($studentId == '') {
    $response = [
        'success' => false,
        'message' => "STUDENT ID IS REQUIRED, Kindly fill in the student ID to continue"
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
        'success'  => false,
        'message'  => "INVALID EMAIL ADDRESS! Enter a valid email address and try again"
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
// 1. CHECK IF STUDENT EXISTS
$checkStudentQuery = mysqli_query($conn, "SELECT * FROM student_tab WHERE student_id = '$studentId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkStudentQuery) == 0) {
    $response = [
        'success' => false,
        'message' => 'STUDENT NOT FOUND'
    ];
    goto end;
}
// 2. CHECK IF EMAIL IS ALREADY USED BY ANOTHER STUDENT
$emailCheck = mysqli_query($conn, "SELECT * FROM student_tab WHERE email_address = '$emailAddress' AND student_id != '$studentId' LIMIT 1") or die(mysqli_error($conn));
if (mysqli_num_rows($emailCheck) > 0) {
    $response = [
        'success' => false,
        'message' => "EMAIL ALREADY EXISTS!! This email $emailAddress is already used by someone. Kindly use another email address to continue"
    ];
    goto end;
}
// 3. UPDATE STUDENT RECORD
mysqli_query($conn, "UPDATE student_tab SET 
    first_name    = '$firstName', 
    last_name     = '$lastName', 
    gender        = '$gender',
    department    = '$department',
    level         = '$level',
    phone_number  = '$phoneNumber',
    email_address = '$emailAddress', 
    status_id     = '$statusId', 
    updated_at    = NOW() 
    WHERE student_id = '$studentId'") or die(mysqli_error($conn));
// 4. FETCH UPDATED STUDENT DATA
$updateEachStudentQuery = mysqli_query($conn, "SELECT 
        student_tab.*, 
        status_tab.status_name 
    FROM student_tab, status_tab 
    WHERE student_tab.status_id = status_tab.status_id 
      AND student_tab.student_id = '$studentId'") or die(mysqli_error($conn));
      
$studentData = mysqli_fetch_assoc($updateEachStudentQuery);
$response = [
    'success' => true,
    'message' => "STUDENT UPDATE SUCCESSFUL",
    'data'    => [
        'studentId'    => $studentData['student_id'],
        'firstName'    => $studentData['first_name'],
        'lastName'     => $studentData['last_name'],
        'gender'       => $studentData['gender'],
        'department'   => $studentData['department'],
        'level'        => $studentData['level'],
        'phoneNumber'  => $studentData['phone_number'],
        'emailAddress' => $studentData['email_address'],
        'statusId'     => $studentData['status_id'],
        'statusName'   => $studentData['status_name'],
        'createdAt'    => $studentData['created_at'],
        'updatedAt'    => $studentData['updated_at']
    ]
];
end:
echo json_encode($response);
?>