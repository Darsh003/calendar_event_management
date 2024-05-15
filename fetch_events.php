<?php

session_start();
// Set content type header
header("Content-Type: application/json");

require 'conn.php'; 
$user_id = $_SESSION['user_id'];
// Fetch events from the database
$result = mysqli_query($conn, "SELECT * FROM calendar_event_master WHERE user_id = $user_id");

$events = array();
while ($row = mysqli_fetch_assoc($result)) {
    $events[] = $row;
}

// Output JSON response
echo json_encode($events);

// Close connection
mysqli_close($conn);
?>
