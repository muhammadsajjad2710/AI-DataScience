<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Screen1 - File Processing</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .step { display: none; }
        .step.active { display: block; }
        .progress { height: 20px; }
        .nav-btns { margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">File Processing - Step by Step</h2>

        <div class="progress mb-3">
            <div id="progressBar" class="progress-bar bg-success" role="progressbar" style="width: 25%;">Step 1</div>
        </div>

        <div class="step active" id="step1">
            <h4>Step 1: Upload File</h4>
            <form id="uploadForm" enctype="multipart/form-data">
                <input type="file" name="file" id="file" class="form-control mb-2">
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>
            <div id="uploadStatus"></div>
            <div class="nav-btns">
                <button class="btn btn-secondary" id="next1" disabled>Next</button>
            </div>
        </div>

        <div class="step" id="step2">
            <h4>Step 2: Normalize Data</h4>
            <button id="normalizeBtn" class="btn btn-warning">Start Normalization</button>
            <div id="normalizeStatus"></div>
            <div class="nav-btns">
                <button class="btn btn-secondary" id="back2">Back</button>
                <button class="btn btn-secondary" id="next2" disabled>Next</button>
            </div>
        </div>

        <div class="step" id="step3">
            <h4>Step 3: Integrate Data into MySQL</h4>
            <button id="integrateBtn" class="btn btn-success">Store in Database</button>
            <div id="integrateStatus"></div>
            <div class="nav-btns">
                <button class="btn btn-secondary" id="back3">Back</button>
                <button class="btn btn-secondary" id="next3" disabled>Next</button>
            </div>
        </div>

        <div class="step" id="step4">
            <h4>Step 4: Generate Report</h4>
            <button id="generateReportBtn" class="btn btn-info">Generate Report</button>
            <div id="reportStatus"></div>
            <div class="nav-btns">
                <button class="btn btn-secondary" id="back4">Back</button>
                <button class="btn btn-danger" id="restartBtn">Upload New File</button>
            </div>
        </div>
    </div>

    <script>
        let currentStep = 1;

        function showStep(step) {
            $(".step").removeClass("active");
            $("#step" + step).addClass("active");
            $("#progressBar").css("width", (step * 25) + "%").text("Step " + step);
        }

        function autoNextStep(nextBtnId) {
            setTimeout(() => { $(nextBtnId).click(); }, 2000);
        }

        $("#uploadForm").submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                url: "upload.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $("#uploadStatus").html(response);
                    $("#next1").prop("disabled", false);
                    autoNextStep("#next1");
                }
            });
        });

        $("#normalizeBtn").click(function() {
            $.get("normalize.php", function(response) {
                $("#normalizeStatus").html(response);
                $("#next2").prop("disabled", false);
                autoNextStep("#next2");
            });
        });

        $("#integrateBtn").click(function() {
            $.get("integrate.php", function(response) {
                $("#integrateStatus").html(response);
                $("#next3").prop("disabled", false);
                autoNextStep("#next3");
            });
        });

        $("#generateReportBtn").click(function() {
            $.get("document.php", function(response) {
                $("#reportStatus").html(response);
            });
        });

        $("#next1").click(() => { currentStep = 2; showStep(2); });
        $("#back2").click(() => { currentStep = 1; showStep(1); });
        $("#next2").click(() => { currentStep = 3; showStep(3); });
        $("#back3").click(() => { currentStep = 2; showStep(2); });
        $("#next3").click(() => { currentStep = 4; showStep(4); });
        $("#back4").click(() => { currentStep = 3; showStep(3); });

        $("#restartBtn").click(() => { 
            currentStep = 1;
            $(".step").removeClass("active");
            $("#step1").addClass("active");
            $("#progressBar").css("width", "25%").text("Step 1");
            $("#uploadStatus, #normalizeStatus, #integrateStatus, #reportStatus").html("");
            $("#next1, #next2, #next3").prop("disabled", true);
        });
    </script>
</body>
</html>
