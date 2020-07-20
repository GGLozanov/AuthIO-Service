<?php
    require "../../vendor/autoload.php";
    use \Firebase\JWT\JWT;

    // class used to abstract away back-end specifics from boilerplate w/ library
    class JWTUtils {
        public static function getPayload(string $username, int $time) {
            require "../config/core.php";

            return array(
                "iss" => $iss,
                "aud" => $aud,
                "iat" => $iat,
                "nbf" => $nbf,
                "exp" => $time,
                "username" => $username
            );
        }
    
        public static function encodeJWT(array $payload) {
            require "../config/core.php";

            return JWT::encode($payload, $privateKey, 'RS256');
        }
    }
?>