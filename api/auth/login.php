<?php
    require "../init.php";
    require "../config/core.php";
    require "../../vendor/autoload.php";
    require "../jwt/jwt_utils.php";
    use \Firebase\JWT\JWT;

    $email = $_GET["email"];
    $password = $_GET["password"];

    if($username = $db->validateUser($email, $password)) {
        $status = "ok";
        http_response_code(200);

        // Create token and send it here (without id and other information; just unique username)

        $jwt = JWTUtils::encodeJWT(JWTUtils::getPayload($username, time() + (60 * 10))); // encodes specific jwt w/ exp time for access token
        $refresh_jwt = JWTUtils::encodeJWT(JWTUtils::getPayload($username, time() + (24 * 60 * 60))); // encode refresh token w/ long expiry

        echo json_encode(array("response"=>$status, "jwt"=>$jwt, "refresh_jwt"=>$refresh_jwt));
    } else {
        $status = "failed"; // user doesn't exist (401 error code)
        http_response_code(400);

        echo json_encode(array("response"=>$status));
    }

    $db->closeConnection(); // make sure to close the connection after that (don't allow too many auths in one instance of the web service)
?>
