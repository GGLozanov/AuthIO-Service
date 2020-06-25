<?php
    require "init.php"; // set up dependency for this script to init php script

    $username = $_GET["username"];
    $password = $_GET["password"];
    $description = $_GET["description"];

    $sql = "SELECT username FROM users WHERE username = '$username'";

    $result = mysqli_query($con, $sql); // execute the query using the $con param from init.php (available here because it's required)

    if(mysqli_num_rows($result) > 0)
        $status = "exists";
    else {
        $sql = "INSERT INTO users(id, username, password, description) 
            VALUES(NULL, '$username', '$password', '$description')";

        if(mysqli_query($con, $sql))
            $status = "ok"; // 200
        else
            $status = "error"; // 500
    }

    echo json_encode(array("response"=>$status)); // output the result in the form of a json encoded response (response<->status)

    mysqli_close($con); // make sure to close the connection after that (don't allow too many auths in one instance of the web service)
?>
