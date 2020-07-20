<?php
    // Receive request w/ Authorization header -> token
    // username inside token -> query db
    require "../init.php";
    include_once '../config/core.php';
    require __DIR__ . '../../vendor/autoload.php';
    use \Firebase\JWT\JWT;

    $jwt = $_SERVER['HTTP_Authorization']; // get token from header

    // TODO: Splice token if with "Bearer " prefix

    if($jwt) {
        $decoded = JWT::decode($jwt, $publicKey, array('RS256'));

        if($decoded) {
            $decoded_array = (array) $decoded;

            if($user = $db->getUser($decoded_array['username'])) {
                $status = "ok";
                http_response_code(200);

                echo json_encode(array("response"=>$status, "id"=>$user->id, "email"=>$user->email, "username"=>$user->username, "description"=>$user->description));
                $db->closeConnection();
                return;
            } else {
                $status = "User not found";
                http_response_code(404);
            }
        } else {
            $status = "Unauthorized. Token may have expired."; // handle client-side and send refresh token here
            http_response_code(401);
        }
    } else {
        $status = "Bad request. No token.";
        http_response_code(400);
    }
    
    echo json_encode(array("response"=>$status));

    $db->closeConnection();
?>