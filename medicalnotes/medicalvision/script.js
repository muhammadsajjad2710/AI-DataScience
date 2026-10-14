$(document).ready(function () {
    $("#uploadForm").on("submit", function (e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: "api/extract_text.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.status === "success") {
                    window.location.href = "view_text.php?file=" + response.filename;
                } else {
                    alert("Error: " + response.message);
                }
            },
            error: function () {
                alert("Something went wrong!");
            }
        });
    });
});
