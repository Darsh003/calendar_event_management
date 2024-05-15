<?php
session_start();
// Set content type header
header("Content-Type: application/json");
require 'conn.php'; 
$user_id = $_SESSION['user_id'];


// Get POST data and sanitize
$event_name = mysqli_real_escape_string($conn, $_POST['event_name']);
$event_start_date = date("Y-m-d", strtotime($_POST['event_start_date'])); 
$event_end_date = date("Y-m-d", strtotime($_POST['event_end_date'])); 

// Prepare SQL statement
$insert_query = "INSERT INTO calendar_event_master (event_name, user_id, event_start_date, event_end_date) VALUES (?,?, ?, ?)";
$stmt = mysqli_prepare($conn, $insert_query);

// Bind parameters
mysqli_stmt_bind_param($stmt, 'ssss', $event_name, $user_id, $event_start_date, $event_end_date);

// Execute statement
if(mysqli_stmt_execute($stmt)) {
    $data = array(
        'status' => true,
        'msg' => 'Event added successfully!'
    );
} else {
    $data = array(
        'status' => false,
        'msg' => 'Sorry, Event not added.'				
    );
}

// Close statement and connection
mysqli_stmt_close($stmt);
mysqli_close($conn);

// Output JSON response
echo json_encode($data);	
?>
