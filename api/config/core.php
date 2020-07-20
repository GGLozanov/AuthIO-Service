<?php
    // show error reporting
    error_reporting(E_ALL);
    
    // set default time-zone
    date_default_timezone_set('Europe/Sofia');
    
    // variables used for jwt
    $publicKey = file_get_contents("../keys/public.pem"); // might these to separate back-end one day
    $privateKey = file_get_contents("../keys/private.pem");
    $iss = $_SERVER['SERVER_NAME'];
    $aud = $_SERVER['SERVER_NAME']; // to be probably changed
    $iat = 1356999524;
    $nbf = 1357000000;
?>