<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php

$emailAddress = trim($_POST['emailAddress'] ?? '');
$password     = trim($_POST['password'] ?? '');

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

$loginQuery = mysqli_query($conn, "SELECT student_tab.*, status_tab.status_name FROM student_tab, status_tab WHERE student_tab.status_id = status_tab.status_id AND student_tab.email_address = '$emailAddress' AND student_tab.password = '$hashPassword'") or die(mysqli_error($conn));

if (mysqli_num_rows($loginQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "INVALID LOGIN DETAILS! Check your email and password and try again"
    ];
    goto end;
}

$studentData = mysqli_fetch_assoc($loginQuery);
$studentId = $studentData['student_id'];

mysqli_query($conn, "UPDATE `student_tab` SET `last_login` = NOW() WHERE `student_id` = '$studentId'") or die(mysqli_error($conn));

$response = [
    'success' => true,
    'message' => "STUDENT LOGIN SUCCESSFUL",
    'data'    => [
        'studentId'    => $studentData['student_id'],
        'firstName'    => $studentData['first_name'],
        'lastName'     => $studentData['last_name'],
        'emailAddress' => $studentData['email_address'],
        'gender'       => $studentData['gender'],
        'lastLogin'    => $studentData['last_login'],
        'statusName'   => $studentData['status_name']
    ]
];

end:
echo json_encode($response);
?>