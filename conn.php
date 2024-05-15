<?php
$server = "localhost";
$username = "root";
$password = "";
$db_name = "calendar_event_management";

$conn = mysqli_connect($server, $username, $password, $db_name);

if($conn){
   null;
}
else {
    // die("error" . mysqli_connect_error());
    echo 'error occured';
}
?>