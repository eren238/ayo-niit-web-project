<?php require_once __DIR__ . '/../../config/connection.php'; ?>
<?php
header('Content-Type: application/json');

$hostelId = trim($_POST['hostelId'] ?? '');

if ($hostelId === '') {
    echo json_encode(['success' => false, 'message' => "HOSTEL ID IS REQUIRED"]);
    exit;
}

$query = mysqli_query($conn, "
    SELECT 
        r.room_id, 
        r.hostel_id, 
        r.room_number, 
        r.block, 
        r.floor, 
        r.room_capacity,
        COUNT(b.bed_id) AS total_beds,
        SUM(CASE WHEN b.status_id = 'O' THEN 1 ELSE 0 END) AS occupied_beds,
        SUM(CASE WHEN b.status_id = 'D' OR b.status_id IS NULL OR b.status_id = '' THEN 1 ELSE 0 END) AS available_beds
    FROM rooms_tab r
    LEFT JOIN beds_tab b ON r.room_id = b.room_id
    WHERE (r.hostel_id = '$hostelId' OR r.block = '$hostelId')
      AND (r.status_id = '1' OR r.status_id = 'AVAILABLE' OR r.status_id = 'Open')
    GROUP BY r.room_id, r.hostel_id, r.room_number, r.block, r.floor, r.room_capacity
    ORDER BY r.room_number ASC
");

if (!$query) {
    echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    exit;
}

$rooms = [];
while ($row = mysqli_fetch_assoc($query)) {
    $capacity = (int)($row['room_capacity'] ?: ($row['total_beds'] ?: 4));
    $occupied = (int)($row['occupied_beds'] ?: 0);
    $vacancies = max(0, $capacity - $occupied);

    $rooms[] = [
        'room_id'     => $row['room_id'],
        'roomId'      => $row['room_id'],
        'room_number' => $row['room_number'],
        'roomNumber'  => $row['room_number'],
        'block'       => $row['block'],
        'floor'       => $row['floor'],
        'capacity'    => $capacity,
        'occupied'    => $occupied,
        'vacancies'   => $vacancies
    ];
}

echo json_encode(['success' => true, 'message' => "ROOMS FETCHED", 'data' => $rooms]);
?>
