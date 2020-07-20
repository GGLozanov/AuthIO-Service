<?php
    // Receive request w/ Authorization header -> token
    // username inside token -> query db
    require "../init.php";
    include_once '../config/core.php';
    require "../models/user.php";
    require "../../vendor/autoload.php";
    use Firebase\JWT\ExpiredException;
    use \Firebase\JWT\JWT;

    $jwt = $_SERVER['HTTP_Authorization']; // get token from header

    // TODO: Splice token if with "Bearer " prefix

    if($jwt) {

        try {
            $decoded = JWT::decode($jwt, $publicKey, array('RS256'));
        } catch(ExpiredException $expired) {
            // reissue token (refresh token)
            $status = "Token Expired.";
            http_response_code(401);
            echo json_encode(array("response"=>$status)); // TODO: Optimise code flow
            return;
        } catch(Exception $e) {
            $status = "Unauthorised access. Invalid token.";
            http_response_code(401);
            echo json_encode(array("response"=>$status));
            return;
        }

        if($decoded) {
            $decoded_array = (array) $decoded;

            if($user = $db->getUser($decoded_array['username'])) {
                $status = "ok";
                http_response_code(200);

                echo json_encode(array("response"=>$status, "id"=>$user->id, "email"=>$user->email, "username"=>$user->username, "description"=>$user->description));
                $db->closeConnection();
                return;
            } else {
                $status = "User not found.";
                http_response_code(404);
            }
        } else {
            $status = "Could not decode token."; // handle client-side and send refresh token here
            http_response_code(500);
        }
    } else {
        $status = "Bad request. No token.";
        http_response_code(400);
    }
    
    echo json_encode(array("response"=>$status));

    $db->closeConnection();
?>