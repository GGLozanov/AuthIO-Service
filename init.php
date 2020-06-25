<?php

    $host = "localhost"; // to be changed if hosted on server
    $user_name = "root";
    $user_password = "";
    $db_name = "authdb";

    $con = mysqli_connect($host, $user_name, $user_password, $db_name, "3308"); // connects to DB with given web server params

    /* if($con)
    	echo "Connection successfull";
    else
    	echo "Connection unsuccessful"; */
?>
