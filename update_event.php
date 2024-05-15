<?php

// Set content type header
header("Content-Type: application/json");

require 'conn.php';

// Check if all required fields are provided
if (isset($_POST['event_id'], $_POST['event_name'], $_POST['event_start_date'], $_POST['event_end_date'])) {
    // Sanitize input to prevent SQL injection
    $eventId = $_POST['event_id'];
    $eventName = $_POST['event_name'];
    $eventStartDate = $_POST['event_start_date'];
    $eventEndDate = $_POST['event_end_date'];

    // Prepare and execute SQL query to update event
    $stmt = $conn->prepare("UPDATE calendar_event_master SET event_name = ?, event_start_date = ?, event_end_date = ? WHERE event_id = ?");
    $stmt->bind_param('sssi', $eventName, $eventStartDate, $eventEndDate, $eventId);
    
    if ($stmt->execute()) {
        // Event updated successfully
        $response = array(
            'status' => true,
            'message' => 'Event updated successfully'
        );
    } else {
        // Error occurred while updating event
        $response = array(
            'status' => false,
            'message' => 'Error updating event'
        );
    }
} else {
    // Required fields not provided
    $response = array(
        'status' => false,
        'message' => 'All fields are required'
    );
}

// Return JSON response
echo json_encode($response);

?>
