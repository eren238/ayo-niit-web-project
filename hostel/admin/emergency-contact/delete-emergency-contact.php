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

$checkExist = mysqli_query($conn, "SELECT * FROM emergency_contacts_tab WHERE contact_id = '$contactId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkExist) == 0) {
    $response = [
        'success' => false,
        'message' => "EMERGENCY CONTACT RECORD NOT FOUND"
    ];
    goto end;
}

mysqli_query($conn, "DELETE FROM `emergency_contacts_tab` WHERE `contact_id` = '$contactId'") or die(mysqli_error($conn));

$response = [
    'success' => true,
    'message' => "EMERGENCY CONTACT DELETED SUCCESSFUL"
];

end:
echo json_encode($response);
?>