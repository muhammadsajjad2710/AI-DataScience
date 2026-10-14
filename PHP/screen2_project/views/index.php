<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Image</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
    <h2>Upload an Image for ML Labeling</h2>
    
    <input type="file" id="imageUpload">
    <button id="uploadBtn">Upload & Process</button>
    
    <script>
        document.getElementById("uploadBtn").addEventListener("click", function () {
            var file = document.getElementById("imageUpload").files[0];
            if (!file) {
                alert("Please select an image first.");
                return;
            }
            
            var reader = new FileReader();
            reader.onload = function (e) {
                sessionStorage.setItem("uploadedImage", e.target.result); // Store image in sessionStorage
                window.location.href = "screen2.php"; // Move to Screen 2
            };
            reader.readAsDataURL(file);
        });
    </script>
</body>
</html>
