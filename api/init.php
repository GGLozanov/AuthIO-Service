<?php
    include_once 'service/database.php';

    $db = new Database();
    $db->getConnection(); // connects to DB with given web server params

?>
