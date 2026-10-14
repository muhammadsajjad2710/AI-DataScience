<?php
include 'db_config.php';

$sql = "CREATE TABLE IF NOT EXISTS images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS labels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_id INT,
    label_data TEXT NOT NULL,
    FOREIGN KEY (image_id) REFERENCES images(id)
)";
$conn->query($sql);

echo "Database initialized.";
?>
