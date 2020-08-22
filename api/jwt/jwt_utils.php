<?php
    require "../../vendor/autoload.php";
    use \Firebase\JWT\JWT;
    use \Firebase\JWT\ExpiredException;

    // class used to abstract away back-end specifics from boilerplate w/ library
    // userId = given user's id written in their token
    // TODO: Add different payload variable to refresh token to signify refresh token-ness
    class JWTUtils {
        public static function getPayload(int $userId, int $time) {
            require "../config/core.php";

            return array(
                "iss" => $iss,
                "aud" => $aud,
                "iat" => $iat,
                "nbf" => $nbf,
                "exp" => $time,
                "userId" => $userId
            );
        }
    
        public static function encodeJWT(array $payload) {
            require "../config/core.php";

            return JWT::encode($payload, $privateKey, 'RS256');
        }

        public static function validateAndDecodeJWT(string $jwt) {
            require "../config/core.php";

            try {
                $decoded = JWT::decode($jwt, $publicKey, array('RS256'));
            } catch(ExpiredException $expired) {
                return false; // false = token is not valid anymore (expired)
            } catch(Exception $e) {
                return null; // null = token is invalid and shouldn't exist
            }

            return $decoded; // $decoded = token is valid and not expired
        }
    }
?>