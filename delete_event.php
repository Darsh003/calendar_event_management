<?php
// Set content type header
header("Content-Type: application/json");

require 'conn.php'; 

// Check if event ID is provided
if (isset($_POST['event_id'])) {
    $event_id = mysqli_real_escape_string($conn, $_POST['event_id']);

    // Delete the event from the database
    $delete_query = "DELETE FROM calendar_event_master WHERE event_id = '$event_id'";
    if (mysqli_query($conn, $delete_query)) {
        $data = array(
            'status' => true,
            'msg' => 'Event deleted successfully!'
        );
    } else {
        $data = array(
            'status' => false,
            'msg' => 'Error deleting event.'				
        );
    }
} else {
    $data = array(
        'status' => false,
        'msg' => 'Event ID not provided.'				
    );
}

// Close connection
mysqli_close($conn);

// Output JSON response
echo json_encode($data);	
?>
