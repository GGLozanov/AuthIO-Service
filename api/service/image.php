<?php
    require "../init.php";
    require "../utils/api_utils.php";

    if(!array_key_exists('image', $_POST)) {
        APIUtils::displayAPIResult(array("response"=>$status), 400);
        return;
    }

    if(!$jwt = APIUtils::getJwtFromHeaders()) {
        return;
    }
    
    if($decoded = APIUtils::validateAuthorisedRequest($jwt)) {
        $title = $decoded['userId']; // title = user's id = id in token (unique profile image identifier)
        $image = $_POST['image']; // image is received as a base64 encoded string that is decoded and put later

        // upload title and image strings to the server (received from client app)

        $upload_path = "../../uploads/$title.jpg";

        file_put_contents($upload_path, base64_decode($image)); // write decoded image to the filesystem (1.jpg, 2.jpg, etc.)

        if($db->setUserHasImage($title)) {
            APIUtils::displayAPIResult(array("response"=>"Image Uploaded")); // send the response back to the client for handling
        }
    }
?>
