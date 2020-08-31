<?php
    require "../init.php";
    require "../utils/api_utils.php";
    require "../models/user.php";

    $headers = apache_request_headers();

    if(!array_key_exists('Authorization', $headers)) {
        APIUtils::displayAPIResult(array("response"=>"Bad request. No Authorization header."), 400);
        return;
    }

    $hasEmail = array_key_exists('email', $_POST);
    $hasPassword = array_key_exists('password', $_POST);
    $hasUsername = array_key_exists('username', $_POST);
    $hasDescription = array_key_exists('description', $_POST);

    if(!$hasEmail && !$hasPassword && !$hasUsername && !$hasDescription) {
        APIUtils::displayAPIResult(array("response"=>"Bad request. No credentials for update."), 400);
        return;
    }

    $jwt = str_replace('Bearer: ', '', $headers['Authorization']);

    if($decoded = APIUtils::validateAuthorisedRequest($jwt)) {
        if($db->updateUser(
            new User(
                $decoded['userId'], 
                $hasEmail ? $_POST['email'] : null, 
                $hasPassword ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null, 
                $hasUsername ? $_POST['username'] : null, 
                $hasDescription ? $_POST['description'] : null, 
                null
            ))) {
            
            $status = "ok";
            $code = 200;
        } else {
            $status = "User not updated";
            $code = 500;
        }

        APIUtils::displayAPIResult(array("response"=>$status), $code);
    }
?>