<?php
    class APIUtils { // might rename this class; improper name
        // helper function designed to set response codes and display a response in a JSON format for interpretation by clients
        public static function displayAPIResult(array $response, $responseCode = 200, string $error = null) {
            if($responseCode != 200)
                http_response_code($responseCode);

            if($error !== null)
                array_push($response, $error);

            echo json_encode($response, JSON_UNESCAPED_SLASHES);
        }

        // returns an assoc array of decoded jwt if valid; else displays API result (error) and returns false (invalid request)
        public static function validateAuthorisedRequest(string $jwt, string $expiredTokenError = null, string $invalidTokenError = null) {
            require "../jwt/jwt_utils.php";

            if($expiredTokenError == null)
                $expiredTokenError = "Expired token. Get refresh token.";

            if($invalidTokenError == null)
                $invalidTokenError = "Unauthorised access. Invalid token.";

            $decoded = JWTUtils::validateAndDecodeJWT($jwt);

            if($decoded) {
                if(($decodedAssoc = (array) $decoded) && 
                    array_key_exists('userId', $decodedAssoc) && $decodedAssoc['userId']) // check if the token is one generated from here also has a username field
                        return $decodedAssoc;
                else {
                    $status = "Missing token info.";
                    $code = 406;
                }
            } else {
                if($decoded == false) 
                    $status = $expiredTokenError;
                else
                    $status = $invalidTokenError;

                $code = 401;
            }

            APIUtils::displayAPIResult(array("response"=>$status), $code);
            return false;
        }

        public static function getJwtFromHeaders() {
            $headers = apache_request_headers();

            if(!array_key_exists('Authorization', $headers)) {
                APIUtils::displayAPIResult(array("response"=>"Bad request. No Authorization header."), 400);
                return null;
            }

            return str_replace('Bearer: ', '', $headers['Authorization']);
        }
    }
?>