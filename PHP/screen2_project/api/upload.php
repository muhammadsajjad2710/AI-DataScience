<?php
include '../database/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES["image"])) {
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO images (image_path) VALUES (?)");
        $stmt->bind_param("s", $target_file);
        $stmt->execute();
        echo json_encode(["status" => "success", "path" => $target_file]);
    } else {
        echo json_encode(["status" => "error"]);
    }
}
?>
