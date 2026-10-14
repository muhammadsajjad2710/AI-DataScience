<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Image Upload</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h2>Upload a Medical Image</h2>
    <input type="file" id="imageUpload" accept="image/*">
    <button id="uploadBtn">Upload</button>
    <p id="status"></p>
    <img id="preview" src="" style="max-width: 300px; display: none;">
    
    <!-- Labeling Button -->
    <button id="labelImage" style="display: none;">Label Image</button>
    <p id="labelResults"></p>

    <script>
        $(document).ready(function(){
            $("#uploadBtn").click(function(){
                var file_data = $("#imageUpload")[0].files[0];
                if (!file_data) {
                    $("#status").text("Please select an image.");
                    return;
                }

                var formData = new FormData();
                formData.append("image", file_data);

                $.ajax({
                    url: "upload.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response){
                        if (response.startsWith("uploads/")) {
                            $("#status").text("Upload successful!");
                            $("#preview").attr("src", response).show();
                            $("#labelImage").show(); // Show labeling button after upload
                        } else {
                            $("#status").text(response);
                        }
                    }
                });
            });

            $("#labelImage").click(function(){
                $.ajax({
                    url: "label.php",
                    type: "POST",
                    success: function(response){
                        $("#labelResults").html(response);
                    }
                });
            });
        });
    </script>
</body>
</html>
