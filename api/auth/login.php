<?php
    require __DIR__ . '../../vendor/autoload.php';
    require "../init.php";
    require "../config/core.php";
    use \Firebase\JWT\JWT;

    $email = $_GET["email"];
    $password = $_GET["password"];

    if($username = $db->validateUser($email, $password)) {
        
        $status = "ok"; // 200

        // Create token and send it here (without id and other information; just unique username)
        $payload = array(
            $iss,
            $aud,
            $iat,
            $nbf,
            $exp => time() + 10,
            $username
        );

        $jwt = JWT::encode($payload, $privateKey, array('RS256')); // TODO: Extract into helper/util functions

        echo json_encode(array("response"=>$status, "jwt"=>$jwt));
    } else {
        $status = "failed"; // user doesn't exist (401 error code)
        echo json_encode(array("response"=>$status));
    }

    $db->closeConnection(); // make sure to close the connection after that (don't allow too many auths in one instance of the web service)

?>
