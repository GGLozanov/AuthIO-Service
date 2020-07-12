<?php
    require "init.php"; // set up dependency for this script to init php script

    $email = $_GET["email"];
    $username = $_GET["username"];
    $password = $_GET["password"];
    $description = $_GET["description"];

    $sql = "SELECT username FROM users WHERE username = '$username'";

    $exists_result = mysqli_query($con, $sql); // execute the query using the $con param from init.php (available here because it's required)

    if(mysqli_num_rows($exists_result) > 0)
        $status = "exists";
    else {
        $sql = "INSERT INTO users(id, email, username, password, description) 
            VALUES(NULL, '$email', '$username', '$password', '$description')";

        $result = mysqli_query($con, $sql);

        if($result)
            $status = "ok"; // 200
        else
            $status = "error"; // 400-500
    }

    echo json_encode(array("response"=>$status, "id"=>mysqli_insert_id($con)));
        // output the result in the form of a json encoded response (response<->status & new user id<->last insert id)
        // last inser Id might cause problems in the future and return incorrect ids if multiple queries are occurring at the same time

        // need id for image upload; might need to rework that and have image upload here.
        // id is nonexistent if there's a server error

    mysqli_close($con); // make sure to close the connection after that (don't allow too many auths in one instance of the web service)
?>
