<?php require_once __DIR__ . '/../../config/connection.php'; ?>
<?php
header('Content-Type: application/json');

$studentId = trim($_POST['studentId'] ?? '');
$hostelId  = trim($_POST['hostelId'] ?? '');
$roomId    = trim($_POST['roomId'] ?? '');
$bedId     = trim($_POST['bedId'] ?? '');

if ($studentId === '') {
    echo json_encode(['success' => false, 'message' => "STUDENT ID REQUIRED, Please sign in again"]);
    exit;
}

if ($hostelId === '') {
    echo json_encode(['success' => false, 'message' => "HOSTEL REQUIRED, Please select a hostel block"]);
    exit;
}

if ($roomId === '') {
    echo json_encode(['success' => false, 'message' => "ROOM REQUIRED, Please select a room"]);
    exit;
}

if ($bedId === '') {
    echo json_encode(['success' => false, 'message' => "BED SPACE REQUIRED, Please select a bed space"]);
    exit;
}

// 1. Verify student exists
$studentQuery = mysqli_query($conn, "SELECT * FROM student_tab WHERE student_id = '$studentId'");
if (!$studentQuery || mysqli_num_rows($studentQuery) === 0) {
    echo json_encode(['success' => false, 'message' => "STUDENT NOT FOUND. Please verify your login credentials."]);
    exit;
}

// 2. Check if bed is available
$bedQuery = mysqli_query($conn, "SELECT * FROM beds_tab WHERE bed_id = '$bedId'");
if (!$bedQuery || mysqli_num_rows($bedQuery) === 0) {
    echo json_encode(['success' => false, 'message' => "SELECTED BED NOT FOUND"]);
    exit;
}

$bedRow = mysqli_fetch_assoc($bedQuery);
if ($bedRow['status_id'] === 'O' && $bedRow['student_id'] !== $studentId) {
    echo json_encode(['success' => false, 'message' => "This bed space has just been taken by another student. Please select another bed."]);
    exit;
}

// 3. Clear previous allocation if student is switching beds
$prevAlloc = mysqli_query($conn, "SELECT bed_id FROM allocation_tab WHERE student_id = '$studentId' AND status_id = 'A'");
if ($prevAlloc && mysqli_num_rows($prevAlloc) > 0) {
    while ($old = mysqli_fetch_assoc($prevAlloc)) {
        $oldBedId = $old['bed_id'];
        mysqli_query($conn, "UPDATE beds_tab SET status_id = 'D', student_id = NULL WHERE bed_id = '$oldBedId'");
    }
    mysqli_query($conn, "DELETE FROM allocation_tab WHERE student_id = '$studentId'");
}

// 4. Create new allocation
$allocationId = 'ALLOC' . date("YmdHis");
$insertAlloc = mysqli_query($conn, "
    INSERT INTO allocation_tab (allocation_id, student_id, hostel_id, room_id, bed_id, status_id, created_at, updated_at) 
    VALUES ('$allocationId', '$studentId', '$hostelId', '$roomId', '$bedId', 'A', NOW(), NOW())
");

if (!$insertAlloc) {
    echo json_encode(['success' => false, 'message' => "Database error: " . mysqli_error($conn)]);
    exit;
}

// 5. Update bed status to Occupied ('O')
mysqli_query($conn, "UPDATE beds_tab SET status_id = 'O', student_id = '$studentId', updated_at = NOW() WHERE bed_id = '$bedId'");

echo json_encode([
    'success' => true,
    'message' => "ROOM & BED SPACE REGISTERED SUCCESSFULLY! Your accommodation is confirmed.",
    'data' => [
        'allocationId' => $allocationId,
        'studentId'    => $studentId,
        'hostelId'     => $hostelId,
        'roomId'       => $roomId,
        'bedId'        => $bedId
    ]
]);
?>
