<?php
session_start();
// Set content type header
header("Content-Type: application/json");

require 'conn.php'; 

// Get selected date from GET request
$selectedDate = $_GET['selected_date'];
$user_id = $_SESSION['user_id'];

// Fetch events for the selected date from the database
$stmt = $conn->prepare("SELECT * FROM calendar_event_master WHERE event_start_date <= ? AND event_end_date >= ? AND user_id = ?");
$stmt->bind_param("sss", $selectedDate, $selectedDate, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$events = array();
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

// Output JSON response
echo json_encode($events);

// Close connection
$stmt->close();
$conn->close();
?>
