<?php
session_start();
// Set content type header
header("Content-Type: application/json");

require 'conn.php'; // Assuming you have a file named conn.php for database connection

// Check if event ID is provided in the request
if (isset($_GET['event_id'])) {
    $user_id = $_SESSION['user_id'];
    // Sanitize the input to prevent SQL injection
    $eventId = $_GET['event_id'];

    // Prepare SQL query to fetch event details
    $stmt = $conn->prepare("SELECT * FROM calendar_event_master WHERE event_id = ? AND user_id = ?");

    // Bind parameter
    $stmt->bind_param('is', $eventId, $user_id);

    // Execute prepared statement
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    // Fetch event details
    $event = $result->fetch_assoc();

    if ($event) {
        // Event details found, return success response with event data
        $response = array(
            'status' => true,
            'event' => $event
        );
    } else {
        // Event not found with the provided ID
        $response = array(
            'status' => false,
            'message' => 'Event not found'
        );
    }
} else {
    // Event ID not provided in the request
    $response = array(
        'status' => false,
        'message' => 'Event ID is required'
    );
}

// Return JSON response
echo json_encode($response);

?>
