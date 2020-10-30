<?php
    require "../init.php";
    include_once '../config/core.php';
    require "../models/user.php";
    require "../../vendor/autoload.php";
    require "../utils/api_utils.php";

    if(!$refresh_jwt = APIUtils::getJwtFromHeaders()) {
        return;
    }

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