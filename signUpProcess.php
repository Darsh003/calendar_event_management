<?php

include "conn.php";
$showAlert = false;
$showError = false;
$exists = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_name = isset($_POST["name"]) ? $_POST["name"] : '';
    $user_email = isset($_POST["email"]) ? $_POST["email"] : '';
    $password = isset($_POST["pass"]) ? $_POST["pass"] : '';
    $conf_password = isset($_POST["re_pass"]) ? $_POST["re_pass"] : '';

    $check_password = checkPasswordStrength($password);

    if ($user_name != '' && $user_email != '' && $password != '' && $conf_password != '') {

        if ($check_password) {
            // Check if the user already exists
            $sql = "SELECT * FROM `user_info` WHERE user_name = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $user_name);
            $stmt->execute();
            $result = $stmt->get_result();
            // $num = $result->num_rows;
            $rows = $result->fetch_assoc();
            $user_email_fetch = isset($rows['user_email']) ? $rows['user_email'] : null;
            $user_name_fetch = isset($rows['user_name']) ? $rows['user_name'] : null;
            $stmt->close();

            if ($user_email_fetch != $user_email && $user_name_fetch != $user_name) {
                if ($password == $conf_password) {
                    // Hash the password
                    $hash = password_hash($password, PASSWORD_DEFAULT);

                    // Insert the user into the database
                    $insert_sql = "INSERT INTO `user_info` (`user_name`, `user_email`, `user_password`, `date`) VALUES (?, ?, ?, current_timestamp())";
                    $insert_stmt = $conn->stmt_init();
                    $insert_stmt = $conn->prepare($insert_sql);
                    $insert_stmt->bind_param("sss", $user_name, $user_email, $hash);
                    $insert_result = $insert_stmt->execute();
                    $insert_stmt->close();

                    if ($insert_result) {
                        $showAlert = 1;
                    }
                } else {
                    $showError = 1; // Passwords don't match
                }
            } else {
                $exists = 1; // User already exists
            }
        } else {
            $showError = 2; // Password doesn't meet requirements
        }
    }
}

function checkPasswordStrength($password) {
    // Define password strength requirements using regular expressions
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number = preg_match('@[0-9]@', $password);
    $specialChar = preg_match('@[^\w]@', $password);

    // Define minimum length for the password
    $minLength = 8;

    // Check if the password meets all the requirements
    if ($uppercase && $lowercase && $number && $specialChar && strlen($password) >= $minLength) {
        return true;
    } else {
        return false;
    }
}

?>
