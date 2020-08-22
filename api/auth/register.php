<?php
    require "../init.php"; // set up dependency for this script to init php script
    require "../config/core.php";
    require "../models/user.php";
    require "../../vendor/autoload.php";
    require "../jwt/jwt_utils.php";
    require "../utils/api_utils.php";

    if(!array_key_exists('email', $_POST) || 
        !array_key_exists('username', $_POST) || 
        !array_key_exists('password', $_POST) || 
            !array_key_exists('description', $_POST)) {
        APIUtils::displayAPIResult(array("response"=>"Missing data."), 400);
        return;
    }

    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $description = $_POST["description"];

    if($db->userExistsOrPasswordTaken($username, $password)) {
        $status = "exists"; // user w/ same username or password exists
        $code = 204; // resource already exists
    } else {
        if($id = $db->createUser(new User(null, $email, $password, $username, $description, 0))) {
            $status = "ok";

            $jwt = JWTUtils::encodeJWT(JWTUtils::getPayload($id, time() + (60 * 10))); // encodes specific jwt w/ expiry time for access token
            $refresh_jwt = JWTUtils::encodeJWT(JWTUtils::getPayload($id, time() + (24 * 60 * 60))); // encode refresh token w/ long expiry

            APIUtils::displayAPIResult(array("response"=>$status, "jwt"=>$jwt, "refresh_jwt"=>$refresh_jwt));
            $db->closeConnection(); // make sure to close the connection after that (don't allow too many auths in one instance of the web service)
            return;
        } else {
            $status = "failed";
            $code = 406; // 406 - bad input
        }
    }

    APIUtils::displayAPIResult(array("response"=>$status), $code);
        // output the result in the form of a json encoded response (response<->status & new user id<->last insert id)
        // last inser Id might cause problems in the future and return incorrect ids if multiple queries are occurring at the same time

        // need id for image upload; might need to rework that and have image upload here.
        // id is nonexistent if there's a server error

   $db->closeConnection();
?>
