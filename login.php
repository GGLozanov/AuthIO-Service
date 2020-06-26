<?php
    require "init.php";

    $email = $_GET["email"];
    $password = $_GET["password"];

    $sql = "SELECT description, username FROM users WHERE email = '$email' AND password = '$password'";

    $result = mysqli_query($con, $sql);

    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result); // fetch the resulting rows in the form of a map (associative array)

	$description = $row['description'];
	$username = $row['username'];
	
	$status = "ok"; // 200
        echo json_encode(array("response"=>$status, "email"=>$email, "username"=>$username, "description"=>$description));
    } else {
        $status = "failed"; // user doesn't exist (401 error code)
        echo json_encode(array("response"=>$status));
    }

    mysqli_close($con); // make sure to close the connection after that (don't allow too many auths in one instance of the web service)

?>
