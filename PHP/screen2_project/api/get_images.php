<?php
include '../database/db_config.php';

$result = $conn->query("SELECT * FROM images ORDER BY uploaded_at DESC");
$images = [];
while ($row = $result->fetch_assoc()) {
    $images[] = $row;
}
echo json_encode($images);
?>
