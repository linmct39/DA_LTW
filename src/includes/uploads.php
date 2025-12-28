<?php
function uploadImage($file) {
    $target_dir = "../../assets/images/"; 
    
    $file_name = basename($file["name"]);
    
    $new_name = time() . "_" . $file_name;
    $target_file = $target_dir . $new_name;
    $uploadOk = 1;
    
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $valid_extensions = array("jpg", "jpeg", "png", "gif");

    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return ["success" => false, "message" => "File không phải là ảnh."];
    }

    if(!in_array($imageFileType, $valid_extensions)) {
        return ["success" => false, "message" => "Chỉ chấp nhận file JPG, JPEG, PNG & GIF."];
    }

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ["success" => true, "filename" => $new_name];
    } else {
        return ["success" => false, "message" => "Có lỗi khi tải file lên."];
    }
}
?>