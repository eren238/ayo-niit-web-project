<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
// DECLERATION OF VARIABLE

$contactId = trim($_POST['contactId']);
$nokName = trim($_POST['nokName']);
$relationship = trim($_POST['relationship']);
$nokPhone = trim($_POST['nokPhone']);
$nokAddress = trim($_POST['nokAddress'] ?? '');
$statusId = trim($_POST['statusId'] ?? '1');
if ($statusId == '') $statusId = '1';

if ($contactId == '') {
    $response = [
        'success' => false,
        'message' => "CONTACT ID IS REQUIRED, Kindly fill in the contact id to continue"
    ];
    goto end;
}

if ($nokName == '') {
    $response = [
        'success' => false,
        'message' => "NEXT OF KIN NAME IS REQUIRED, Kindly fill in the full name to continue"
    ];
    goto end;
}

if ($relationship == '') {
    $response = [
        'success' => false,
        'message' => "RELATIONSHIP IS REQUIRED, Kindly fill in the relationship to continue"
    ];
    goto end;
}

if ($nokPhone == '') {
    $response = [
        'success' => false,
        'message' => "EMERGENCY PHONE NUMBER IS REQUIRED, Kindly fill in the phone number to continue"
    ];
    goto end;
}

if ($nokAddress == '') {
    $response = [
        'success' => false,
        'message' => "RESIDENTIAL ADDRESS IS REQUIRED, Kindly fill in the address to continue"
    ];
    goto end;
}

if ($statusId == '') {
    $statusId = '1';
}

$checkExist = mysqli_query($conn, "SELECT * FROM emergency_contacts_tab WHERE contact_id = '$contactId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkExist) == 0) {
    $response = [
        'success' => false,
        'message' => "EMERGENCY CONTACT RECORD NOT FOUND"
    ];
    goto end;
}

mysqli_query($conn, "UPDATE `emergency_contacts_tab` SET `nok_name` = '$nokName', `relationship` = '$relationship', `phone_number` = '$nokPhone', `residential_address` = '$nokAddress', `status_id` = '$statusId', `updated_at` = NOW() WHERE `contact_id` = '$contactId'") or die(mysqli_error($conn));

$createQuery = mysqli_query($conn, "SELECT emergency_contacts_tab.*, student_tab.first_name, student_tab.last_name, status_tab.status_name FROM emergency_contacts_tab LEFT JOIN student_tab ON emergency_contacts_tab.student_id = student_tab.student_id LEFT JOIN status_tab ON emergency_contacts_tab.status_id = status_tab.status_id WHERE emergency_contacts_tab.contact_id = '$contactId'") or die(mysqli_error($conn));
$contactData = mysqli_fetch_assoc($createQuery);

$response = [
    'success' => true,
    'message' => "EMERGENCY CONTACT UPDATED SUCCESSFUL",
    'data' => [
        'contactId' => $contactData['contact_id'],
        'studentId' => $contactData['student_id'],
        'firstName' => $contactData['first_name'],
        'lastName' => $contactData['last_name'],
        'nokName' => $contactData['nok_name'],
        'relationship' => $contactData['relationship'],
        'nokPhone' => $contactData['phone_number'],
        'nokAddress' => $contactData['residential_address'],
        'statusName' => $contactData['status_name'],
    ]
];

end:
echo json_encode($response);
?>