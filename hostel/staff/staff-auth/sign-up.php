<?php require_once '../../config/connection.php'; ?>


<?php
// DECLERATION OF VARIABLE
   $firstName= trim($_POST['firstName']);
   $lastName= trim($_POST['lastName']);
   $emailAddress= trim($_POST['emailAddress']);  
   $phoneNumber= trim($_POST['phoneNumber']);
   $address= $_POST['address'];
   $statusId=1;

if ($firstName == ''){

    $response =[
        'success' => false,
        'message' => "FIRST NAME IS REQUIRED, Kindly fill in the first name to continue"
    ];
    goto end;
}

if ($lastName == ''){

    $response =[
        'success' => false,
        'message' => "LAST NAME IS REQUIRED, Kindly fill in the last name to continue"
    ];
    goto end;
}
if ($emailAddress == ''){

    $response =[
        'success' => false,
        'message' => "EMAIL ADDRESS NAME IS REQUIRED, Kindly fill in the email address to continue"
    ];
    goto end;
}
if ($phoneNumber == ''){

    $response =[
        'success' => false,
        'message' => "PHONE NUMBER IS REQUIRED, Kindly fill in the phone number to continue"
    ];
    goto end;
}
if ($address == ''){

    $response =[
        'success' => false,
        'message' => "ADDRESS IS REQUIRED, Kindly fill in the address to continue"
    ];
    goto end;
}



$userid ='USER' . date("Ymdhis");

$response = [
    'success' => true,
    'message' => "USER REGISTRATION SUCCESSFULLY",
    'data' => [
        'userid' => $userid,
        'firstName' => $firstName,
        'lastName' => $lastName,
        'emailAddress' => $emailAddress,
        'phoneNumber' => $phoneNumber,
        'address' =>$address,
    ]

 ];

end:
echo json_encode($response);
?>  
                                                                        