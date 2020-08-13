<?php
    // Receive request w/ Authorization header -> token
    // username inside token -> query db
    require "../init.php";
    include_once '../config/core.php';
    require "../utils/api_utils.php";
    require "../models/user.php";
    require "../../vendor/autoload.php";

    $headers = apache_request_headers();

    if(!array_key_exists('Authorization', $headers)) {
        APIUtils::displayAPIResult(array("response"=>"Bad request. No Authorization header."), 400);
        return;
    }

    $jwt = str_replace('Bearer: ', '', $headers['Authorization']); // get token from header (splice 'Bearer: ' prefix)

    if($decoded = APIUtils::validateAuthorisedRequest($jwt)) {
        if($user = $db->getUser($decoded['username'])) {
            $status = "ok";

            APIUtils::displayAPIResult(array("response"=>$status, "id"=>$user->id, "email"=>$user->email, "username"=>$user->username, "description"=>$user->description));
            $db->closeConnection();
            return;
        } else {
            $status = "User not found.";
            $code = 404;
            APIUtils::displayAPIResult(array("response"=>$status), $code);
        }
    }

    $db->closeConnection();
?>