<?php require_once __DIR__ . '/../../config/connection.php'; ?>
<?php
header('Content-Type: application/json');

$roomId = trim($_POST['roomId'] ?? '');

if ($roomId === '') {
    echo json_encode(['success' => false, 'message' => "ROOM ID IS REQUIRED"]);
    exit;
}

$query = mysqli_query($conn, "
    SELECT bed_id, hostel_id, room_id, bed_number, status_id 
    FROM beds_tab 
    WHERE room_id = '$roomId' 
      AND (status_id = 'D' OR status_id IS NULL OR status_id = '' OR status_id = 'AVAILABLE')
    ORDER BY bed_number ASC
");

if (!$query) {
    echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    exit;
}

$beds = [];
while ($row = mysqli_fetch_assoc($query)) {
    $beds[] = [
        'bed_id'     => $row['bed_id'],
        'bedId'      => $row['bed_id'],
        'room_id'    => $row['room_id'],
        'roomId'     => $row['room_id'],
        'bed_number' => $row['bed_number'],
        'bedNumber'  => $row['bed_number'],
        'bed_type'   => 'Standard Single Bed'
    ];
}

echo json_encode(['success' => true, 'message' => "BEDS FETCHED", 'data' => $beds]);
?>
