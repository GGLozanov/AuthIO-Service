<?php
    // Receive request w/ Authorization header -> token
    // id inside token -> query db
    require "../init.php";
    include_once '../config/core.php';
    require "../utils/api_utils.php";
    require "../models/user.php";
    require "../../vendor/autoload.php";

    if(!$jwt = APIUtils::getJwtFromHeaders()) {
        return;
    }

    if($decoded = APIUtils::validateAuthorisedRequest($jwt)) {
        if($user = $db->getUser($decoded['userId'])) {
            $status = "ok";

            APIUtils::displayAPIResult(array(
                "response"=>$status, 
                "id"=>$user->id, 
                "email"=>$user->email, 
                "username"=>$user->username, 
                "description"=>$user->description,
                "photo_url"=> $user->hasImage ? 
                    'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] .'/AuthIO-Service/uploads/' . $user->id . '.jpg'
                        : null
                )
            );
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