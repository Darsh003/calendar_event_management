<?php
session_start();

if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true){
    header("location: login.php");
    exit;
}

require 'conn.php'; 

$user_name = $_SESSION['username'];

// Prepare and execute SQL query to select user ID
$stmt = $conn->prepare("SELECT id FROM user_info WHERE user_name = ?");
$stmt->bind_param("s", $user_name);
$stmt->execute();

// Get result
$result = $stmt->get_result();

// Check if result contains rows
if ($result->num_rows > 0) {
    // Fetch user ID
    $row = $result->fetch_assoc();
    $user_id = $row["id"];
    $_SESSION['user_id'] = $user_id;
    // echo "User ID: " . $user_id;
} else {
    echo "No user found with the provided user name.";
}


// Close statement and connection
$stmt->close();
$conn->close();

?>


<!doctype html>
<html lang="en">

<head>
	<title>Calendar Event Management System</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

	<!-- <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' /> -->

	<link rel="stylesheet" href="css/style.css">

</head>

<body>
	<!-- <section class="ftco-section"> -->
		<div class="container mt-4">
			<!-- <div class="row justify-content-center">
				<div class="col-md-6 text-center mb-5">
					<h2 class="heading-section">Calendar Event Management System</h2>
				</div>
			</div> -->
			<div class="row">
				<div class="col-md-12">
					<div class="elegant-calencar d-md-flex">
						<div class="wrap-header d-flex align-items-center img"
							style="background-image: url(images/bg.jpg);">
							<p id="reset">Today</p>
							<div id="header" class="p-0">
								<!-- <div class="pre-button d-flex align-items-center justify-content-center"><i class="fa fa-chevron-left"></i></div> -->
								<div class="head-info">
									<div class="head-month"></div>
									<div class="head-day"></div>
								</div>
								<!-- <div class="next-button d-flex align-items-center justify-content-center"><i class="fa fa-chevron-right"></i></div> -->
							</div>
						</div>
						<div class="calendar-wrap">
							<div class="w-100 button-wrap">
								<div class="pre-button d-flex align-items-center justify-content-center"><i
										class="fa fa-chevron-left"></i></div>
								<div class="next-button d-flex align-items-center justify-content-center"><i
										class="fa fa-chevron-right"></i></div>
							</div>
							<table id="calendar">
								<thead>
									<tr>
										<th>Sun</th>
										<th>Mon</th>
										<th>Tue</th>
										<th>Wed</th>
										<th>Thu</th>
										<th>Fri</th>
										<th>Sat</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
								</tbody>
							</table>
							<!-- Add Event Button -->
							<div class="container">
								<div class="row mt-3">
									<div class="btn_div">
										<button type="button" class="btn btn-primary" id="add_event_button">Add
											Event</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<!-- Add Selected Date Event Card -->
				<div class="container mt-4">
					<div class="card">
						<div class="card-header">
							Events for Selected Date
						</div>
						<div class="card-body" id="selected-date-events">
							<!-- Events for selected date will be dynamically added here -->
						</div>
					</div>
				</div>

			</div>
			<div class="row">
				<!-- Add Event Card -->
				<div class="container mt-4">
					<div class="card">
						<div class="card-header">
							All Events
						</div>
						<div class="card-body" id="event-list">
							<!-- Events will be dynamically added here -->
						</div>
					</div>
				</div>
			</div>

		</div>
	<!-- </section> -->

	<!-- Start popup dialog box -->
	<div class="modal fade" id="event_entry_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
		aria-hidden="true">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalLabel">Add New Event</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="img-container">
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<label for="event_name">Event name</label>
									<input type="text" name="event_name" id="event_name" class="form-control"
										placeholder="Enter your event name">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label for="event_start_date">Event start</label>
									<input type="date" name="event_start_date" id="event_start_date"
										class="form-control onlydatepicker" placeholder="Event start date">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="event_end_date">Event end</label>
									<input type="date" name="event_end_date" id="event_end_date" class="form-control"
										placeholder="Event end date">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" onclick="save_event()">Save Event</button>
				</div>
			</div>
		</div>
	</div>
	<!-- End popup dialog box -->


	  <!-- Edit Event Modal -->
	  <div class="modal fade" id="edit_event_modal" tabindex="-1" role="dialog" aria-labelledby="editEventModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="editEventModalLabel">Edit Event</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form id="edit_event_form">
					<div class="modal-body">
						<input type="hidden" id="edit_event_id">
						<div class="form-group">
							<label for="edit_event_name">Event Name</label>
							<input type="text" class="form-control" id="edit_event_name" required>
						</div>
						<div class="form-group">
							<label for="edit_event_start_date">Start Date</label>
							<input type="date" class="form-control" id="edit_event_start_date" required>
						</div>
						<div class="form-group">
							<label for="edit_event_end_date">End Date</label>
							<input type="date" class="form-control" id="edit_event_end_date" required>
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary">Save Changes</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<button type="button" class="btn btn-danger fixed-top mt-2 ml-auto mr-4" style="width:75px;"><a href="logout.php" class="text-decoration-none text-white">Logout</a></button>







	<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script> -->
	<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script> -->
	<script src="js/jquery.min.js"></script>
	<script src="js/popper.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/main.js"></script>

	<script>

		function save_event() {
			var event_name = $("#event_name").val();
			var event_start_date = $("#event_start_date").val();
			var event_end_date = $("#event_end_date").val();

			if (event_name == "" || event_start_date == "" || event_end_date == "") {
				alert("Please enter all required details.");
				return false;
			}

			$.ajax({
				url: "save_event.php",
				type: "POST",
				dataType: 'json',
				data: {
					event_name: event_name,
					event_start_date: event_start_date,
					event_end_date: event_end_date
				},
				success: function (response) {
					$('#event_entry_modal').modal('hide');
					if (response.status == true) {
						alert(response.msg);
						location.reload();
					} else {
						alert(response.msg);
					}
				},
				error: function (xhr, status) {
					console.log('Ajax error: ' + xhr.statusText);
					alert('Error saving event: ' + status);
				}
			});

			return false;
		}
	</script>


</body>

</html>