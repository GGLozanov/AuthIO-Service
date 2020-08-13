<?php
    require "../init.php";
    require "../config/core.php";
    require "../../vendor/autoload.php";
    require "../jwt/jwt_utils.php";
    require "../utils/api_utils.php";

    if(!array_key_exists('email', $_GET) || 
    !array_key_exists('password', $_GET)) {
        APIUtils::displayAPIResult(array("response"=>"Missing data."), 400);
        return;
    }

    $email = $_GET["email"];
    $password = $_GET["password"]; // password is transmitted as plain-text over client; use TLS/HTTPS in future for securing client-server communication and avoiding MiTM

    if($username = $db->validateUser($email, $password)) {
        $status = "ok";

        // Create token and send it here (without id and other information; just unique username)

        $jwt = JWTUtils::encodeJWT(JWTUtils::getPayload($username, time() + (60 * 10))); // encodes specific jwt w/ exp time for access token
        $refresh_jwt = JWTUtils::encodeJWT(JWTUtils::getPayload($username, time() + (24 * 60 * 60))); // encode refresh token w/ long expiry

        APIUtils::displayAPIResult(array("response"=>$status, "jwt"=>$jwt, "refresh_jwt"=>$refresh_jwt));
    } else {
        $status = "failed"; // user doesn't exist (401 error code)
        $code = 401; // unauthorised

        APIUtils::displayAPIResult(array("response"=>$status), $code);
    }

    $db->closeConnection(); // make sure to close the connection after that (don't allow too many auths in one instance of the web service)
?>
