<?php require_once __DIR__ . '/../../config/connection.php'; ?>
<?php
header('Content-Type: application/json');

$query = mysqli_query($conn, "SELECT hostel_id, hostel_name, code, gender, hostel_capacity, status_id FROM hostels_tab WHERE status_id = '1' OR status_id = 'ACTIVE' ORDER BY hostel_name ASC");

if (!$query) {
    echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    exit;
}

if (mysqli_num_rows($query) === 0) {
    echo json_encode(['success' => false, 'message' => "NO HOSTELS FOUND", 'data' => []]);
    exit;
}

$hostels = mysqli_fetch_all($query, MYSQLI_ASSOC);
echo json_encode(['success' => true, 'message' => "HOSTELS FETCHED", 'data' => $hostels]);
?>
