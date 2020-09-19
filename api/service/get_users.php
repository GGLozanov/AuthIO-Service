<?php
    // Retrieves all the users except the one with the id passed in with the GET request as a query param
    require "../init.php";
    include_once '../config/core.php';
    require "../utils/api_utils.php";
    require "../models/user.php";
    require "../../vendor/autoload.php";

    $headers = apache_request_headers();

    if(!array_key_exists('Authorization', $headers)) {
        APIUtils::displayAPIResult(array("response"=>"Bad request. No Authorization header or no id passed in query parameters."), 400);
        return;
    }

    $jwt = str_replace('Bearer: ', '', $headers['Authorization']);

    if($decoded = APIUtils::validateAuthorisedRequest($jwt)) {
        $id = $decoded['userId']; // id of auth'd user making the request; used to exempt from DB user query

        if($users = $db->getUsers($id)) {
            APIUtils::displayAPIResult(
                array_reduce($users, function($result, User $user) {
                    $result["user" . $user->id] = array(
                        "id"=>$user->id,
                        "username"=>$user->username, 
                        "description"=>$user->description,
                        "email"=>$user->email, 
                        "photo_url"=>$user->hasImage ? 
                            'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] .'/AuthIO-Service/uploads/' . $user->id . '.jpg' 
                                : null);
                    return $result;
            }, array())); // mapping twice; FIXME - refactor database to return JSON responses directly instead of model classes?
        } else {
            $status = "Internal server error.";
            $code = 500;
            APIUtils::displayAPIResult(array("response"=>$status, $code));
        }
    }
?>