<?php
    require "init.php";

    if($con) {
        // upload title and image strings to the server (received from client app)
        $title = $_POST['title']; // title = user's id (handle client-side to always send that or find way to sync without sending it in request)
        $image = $_POST['image']; // image is received as a base64 encoded string that is decoded and put later

        $upload_path = "uploads/$title.jpg";

        $sql = "UPDATE users SET image_path = '$upload_path' WHERE id = $title"; // received as string, used as int; shouldn't have a problem in sql?

        if(mysqli_query($con, $sql)) {
            // query is successful
            file_put_contents($upload_path, base64_decode($image)); // write decoded image to the filesystem
            $status = "Image Uploaded";
        } else {
            $status = "Image Upload Failed";
        }

        echo json_encode(array("response"=>$status)); // send the response back to the client for handling

        mysqli_close($con);
    }

?>
