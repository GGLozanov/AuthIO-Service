<?php
    require "init.php";

    $username = $_GET["username"];
    $password = $_GET["password"];

    $sql = "SELECT description FROM users WHERE username = '$username' AND password = '$password'";

    $result = mysqli_query($con, $sql);

    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result); // fetch the resulting rows in the form of a map (associative array)
        $description = $row['description'];
        $status = "ok"; // 200
        echo json_encode(array("response"=>$status, "username"=>$username, "description"=>$description));
    } else {
        $status = "failed"; // user doesn't exist (401 error code)
        echo json_encode(array("response"=>$status));
    }

    mysqli_close($con); // make sure to close the connection after that (don't allow too many auths in one instance of the web service)

?>
