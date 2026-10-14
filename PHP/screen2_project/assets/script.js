$(document).ready(function () {
    // Handle Image Upload
    $("#uploadForm").on("submit", function (e) {
        e.preventDefault();
        let fileInput = $("#imageUpload")[0].files[0];

        if (fileInput) {
            let reader = new FileReader();
            reader.onload = function (e) {
                let imageData = e.target.result; // Base64 image data
                sessionStorage.setItem("uploadedImage", imageData); // Store in sessionStorage
                window.location.href = "label.php"; // Redirect to labeling screen
            };
            reader.readAsDataURL(fileInput);
        }
    });

    // Display Uploaded Image in Labeling Page
    if (window.location.pathname.includes("label.php")) {
        let storedImage = sessionStorage.getItem("uploadedImage");
        if (storedImage) {
            $("#imagePreview").attr("src", storedImage); // Display image
            analyzeImage(storedImage); // Send to ML model
        }
    }

    // Function to Send Image to Backend ML Model
    function analyzeImage(imageData) {
        $.ajax({
            url: "http://localhost:5000/analyze", // ML Backend URL
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({ image: imageData }),
            success: function (response) {
                $("#labelsContainer").empty(); // Clear previous labels
                response.labels.forEach(label => {
                    $("#labelsContainer").append(`<div class="label-box">${label}</div>`);
                });
            }
        });
    }
});
