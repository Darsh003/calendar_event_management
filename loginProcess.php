<?php

include "conn.php";
$login = false;
$loginerror = false;

if($_SERVER["REQUEST_METHOD"] == "POST"){


    @$user_name_login = $_POST["your_name"];
    @$password_login = $_POST["your_pass"];


    if($user_name_login !='' && $password_login !=''){

        $sql = "SELECT * from `user_info` where user_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s",$user_name_login);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $num = count($rows);
        $stmt->close();
        if($num == 1){
                foreach ($rows as $row){
                if(password_verify($password_login, $row['user_password'])){
                    $login = true;
                    session_start();
                    session_regenerate_id();
                    $_SESSION['loggedin'] = true;
                    $_SESSION['username'] = $user_name_login;
                    header("location: index.php");
                }
                else{
                    $loginerror = 1;
                }
            }
        }
        else if($num == 0){
            $loginerror = 2;
        }
    }

}
?>