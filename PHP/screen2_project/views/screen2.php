<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Labeling Process</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h2>Step 1: Auto Labeling Using ML Model</h2>
    <canvas id="canvas"></canvas>
    <button id="runML">Run ML Model</button>
    <button id="nextStep">Next: Quality Assurance</button>

    <script>
        $(document).ready(function () {
            var imgSrc = sessionStorage.getItem("uploadedImage"); // Get image from sessionStorage
            if (!imgSrc) {
                alert("No image uploaded!");
                window.location.href = "index.php";
                return;
            }

            // Display image on canvas
            var canvas = document.getElementById("canvas");
            var ctx = canvas.getContext("2d");
            var img = new Image();
            img.src = imgSrc;
            img.onload = function () {
                canvas.width = img.width / 2;
                canvas.height = img.height / 2;
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            };

            $("#runML").click(function () {
                $.ajax({
                    url: "../api/ml_process.php",
                    type: "POST",
                    data: { imageData: imgSrc },
                    success: function (response) {
                        console.log(response);
                        alert("ML Processing Complete! Labels Updated.");
                    }
                });
            });

            $("#nextStep").click(function () {
                alert("Proceeding to Quality Assurance...");
            });
        });
    </script>
</body>
</html>
