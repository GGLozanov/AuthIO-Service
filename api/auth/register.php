<?php
    require "../init.php"; // set up dependency for this script to init php script
    require "../config/core.php";
    require "../models/user.php";
    require "../../vendor/autoload.php";
    use \Firebase\JWT\JWT;

    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $description = $_POST["description"];

    if($db->userExistsOrPasswordTaken($email, $password)) {
        $status = "exists"; // user w/ same username or password exists
        http_response_code(204);
    } else {
        $sql = "INSERT INTO users(id, email, username, password, description) 
            VALUES(NULL, '$email', '$username', '$password', '$description')";

        if($id = $db->createUser(new User(null, $email, $password, $username, $description))) {
            $status = "ok";
            http_response_code(200);

            // Create token and send it here (with user's username in payload)
            $payload = array(
                $iss,
                $aud,
                $iat,
                $nbf,
                $exp = time() + 600,
                $username
            ); // jwt token payload; signed with private key

            $jwt = JWT::encode($payload, $privateKey, 'RS256'); // TODO: Extract into helper/util functions

            echo json_encode(array("response"=>$status, "userId"=>$id, "jwt"=>$jwt));
            $db->closeConnection(); // make sure to close the connection after that (don't allow too many auths in one instance of the web service)
            return;
        } else {
            $status = "error";
            http_response_code(404);
        }
    }

    echo json_encode(array("response"=>$status));
        // output the result in the form of a json encoded response (response<->status & new user id<->last insert id)
        // last inser Id might cause problems in the future and return incorrect ids if multiple queries are occurring at the same time

        // need id for image upload; might need to rework that and have image upload here.
        // id is nonexistent if there's a server error

   $db->closeConnection();
?>
