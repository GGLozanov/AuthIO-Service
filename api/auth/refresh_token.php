<?php
    require "../init.php";
    include_once '../config/core.php';
    require "../models/user.php";
    require "../../vendor/autoload.php";
    require "../utils/api_utils.php";

    $headers = apache_request_headers();

    if(!array_key_exists('Authorization', $headers)) {
        APIUtils::displayAPIResult(array("response"=>"Bad request. No Authorization header."), 400);
        return;
    }

    $refresh_jwt = str_replace('Bearer: ', '', $headers['Authorization']); // get refresh token from header (splice 'Bearer: ' prefix like access token)     

    // exp time for refresh token is one full day from time of issuing
    // if the request is authorised => reissue token
    if($decoded = APIUtils::validateAuthorisedRequest($refresh_jwt, "Expired refresh token. Reauthenticate.", "Unauthorised access. Invalid token. Reauthenticate.")) {
        $status = "ok";

        // reissue token (new access token); 
        $jwt = JWTUtils::encodeJWT(JWTUtils::getPayload($decoded['userId'], time() + (60 * 10))); // new jwt (access token)

        APIUtils::displayAPIResult(array("response"=>$status, "jwt"=>$jwt));
        return;
    }
?>