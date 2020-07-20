<?php
    require "../init.php";
    include_once '../config/core.php';
    require "../models/user.php";
    require "../../vendor/autoload.php";
    require "../jwt/jwt_utils.php";
    use Firebase\JWT\ExpiredException;
    use \Firebase\JWT\JWT;

    if(!array_key_exists('refresh_jwt', $_POST)) {
        $status = "No token.";
        http_response_code(400);
        echo json_encode(array("status"=>$status));
    }

    $refresh_jwt = $_POST['refresh_jwt'];        

    // exp time for refresh token is one full day from time of issuing
    try {
        $decoded = JWT::decode($refresh_jwt, $publicKey, array('RS256'));
    } catch(ExpiredException $expired) {
        $status = "Expired refresh token. Reauthenticate.";
        http_response_code(401);
        echo json_encode(array("response"=>$status)); // TODO: Optimise code flow and reduce repition
    } catch(Exception $e) {
        $status = "Unauthorised access. Invalid token. Reauthenticate.";
        http_response_code(401);
        echo json_encode(array("response"=>$status, "error"=>(string) $e));
    }
    
    if($decoded && ($decoded_array = (array) $decoded) && 
        array_key_exists('username', $decoded_array) && $username = $decoded_array['username']) {
        $status = "ok";
        http_response_code(200);

        // reissue token (new access token); 
        $jwt = JWTUtils::encodeJWT(JWTUtils::getPayload($username, time() + (60 * 10))); // new jwt (access token)

        echo json_encode(array("response"=>$status, "jwt"=>$jwt));
    } else {
        $status = "Missing token info.";
        http_response_code(404);
        echo json_encode(array("response"=>$status));
    }

?>