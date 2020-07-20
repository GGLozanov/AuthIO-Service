<?php
    require "../init.php";

    if(!array_key_exists('title', $_POST) || !array_key_exists('image', $_POST)) {
        $status="Missing data.";
        http_response_code(400);
        echo json_encode(array("response"=>$status));
        return;
    }

    // upload title and image strings to the server (received from client app)
    $title = $_POST['title']; // title = user's id (handle client-side to always send that or find way to sync without sending it in request)
    $image = $_POST['image']; // image is received as a base64 encoded string that is decoded and put later

    $upload_path = "../../uploads/$title.jpg";

    file_put_contents($upload_path, base64_decode($image)); // write decoded image to the filesystem (1.jpg, 2.jpg, etc.)
    $status = "Image Uploaded";

    echo json_encode(array("response"=>$status)); // send the response back to the client for handling
?>
