<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
// DECLERATION OF VARIABLE

$contactId = trim($_POST['contactId']);

if ($contactId == '') {
    $response = [
        'success' => false,
        'message' => "CONTACT ID IS REQUIRED, Kindly fill in the contact id to continue"
    ];
    goto end;
}

$query = mysqli_query($conn, "SELECT emergency_contacts_tab.*, student_tab.first_name, student_tab.last_name, status_tab.status_name FROM emergency_contacts_tab LEFT JOIN student_tab ON emergency_contacts_tab.student_id = student_tab.student_id LEFT JOIN status_tab ON emergency_contacts_tab.status_id = status_tab.status_id WHERE emergency_contacts_tab.contact_id = '$contactId'") or die(mysqli_error($conn));

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => "EMERGENCY CONTACT RECORD NOT FOUND"
    ];
    goto end;
}

$contactData = mysqli_fetch_assoc($query);

$response = [
    'success' => true,
    'message' => "EMERGENCY CONTACT FETCHED SUCCESSFUL",
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