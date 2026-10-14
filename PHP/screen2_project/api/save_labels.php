<?php
include '../database/db_config.php';

$data = json_decode(file_get_contents("php://input"), true);
$image_id = $data['image_id'];
$labels = json_encode($data['labels']);

$stmt = $conn->prepare("INSERT INTO labels (image_id, label_data) VALUES (?, ?)");
$stmt->bind_param("is", $image_id, $labels);
$stmt->execute();
echo json_encode(["status" => "success"]);
?>
