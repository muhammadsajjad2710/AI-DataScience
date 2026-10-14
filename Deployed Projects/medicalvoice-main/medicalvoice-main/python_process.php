<!-- Convert Section -->
<h2 class="mb-4 text-success">
    <i class="fas fa-microphone"></i> Convert Speech to Text
</h2>

<form action="" method="post" class="mb-4">
    <input type="hidden" name="action" value="convert">
    <button type="submit" name="convert" class="btn btn-success btn-lg">
        <i class="fas fa-play"></i> Convert
    </button>
</form>

<?php
include('database.php');
$userId = $_SESSION['member_id'];

// ✅ Handle Convert Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'convert') {

	    // Check credits
    $stmt = $conn->prepare("SELECT credits FROM members WHERE member_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($credits);
    $stmt->fetch();
    $stmt->close();

    if ($credits <= 0) {
        echo "<div class='alert alert-danger mt-3 p-3'>
                ❌ You have no credits left. 
                <a href='buy_credits.php' class='btn btn-primary btn-sm ms-2'>Buy Credits</a>
              </div>";
        exit;
    }

    // Deduct 1 credit
    $stmt = $conn->prepare("UPDATE members SET credits = credits - 1 WHERE member_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();


    if (isset($_SESSION['uploaded_file'])) {
        $audioFile = $_SESSION['uploaded_file'];
        $logFile = $_SESSION['log_file'];

        $venvPath = __DIR__ . "/myenv/bin/activate";
        $pythonBinary = __DIR__ . "/myenv/bin/python";
        $pythonScript = __DIR__ . "/process_audio.py";

        // ✅ Validate script and python binary
        if (!file_exists($pythonScript)) {
            echo "<div class='alert alert-danger mt-3'>❌ Python script not found.</div>";
            exit();
        }

        if (!file_exists($pythonBinary)) {
            echo "<div class='alert alert-danger mt-3'>❌ Virtual environment Python not found at $pythonBinary</div>";
            exit();
        }

        // ✅ Build Command
        $command = "bash -c 'source $venvPath && $pythonBinary \"$pythonScript\" \"$audioFile\" \"$logFile\"'";

        // ✅ Execute Command
        $output = shell_exec($command . " 2>&1");
        $responseData = json_decode($output, true);

        if ($responseData && isset($responseData['json_file'])) {
            $jsonFile = $responseData['json_file'];
            $_SESSION['json_file'] = $jsonFile;

            echo "<div class='alert alert-success mt-3 p-3'>
                    ✅ Speech converted successfully.
                  </div>";

        } else {
            echo "<div class='alert alert-danger mt-3 p-3'>❌ Failed to process audio file.</div>";
            if (file_exists($logFile)) {
                echo "<div class='card mt-3 p-3'>
                        <h4>Error Log:</h4>
                        <pre style='white-space: pre-wrap;'>" . htmlspecialchars(file_get_contents($logFile)) . "</pre>
                      </div>";
            }
        }

    } else {
        echo "<div class='alert alert-warning mt-3 p-3'>No uploaded file found in session.</div>";
    }
}
?>

<!-- Read Response Section -->
<h2 id="read" class="mt-5 mb-4 text-info">
    <i class="fas fa-book-open"></i> Read Response
</h2>

<form action="" method="post" class="mb-4">
    <input type="hidden" name="action" value="read">
    <button type="submit" name="read" class="btn btn-info btn-lg">
        <i class="fas fa-eye"></i> Read Response
    </button>
</form>

<?php
// ✅ Handle Read Response Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'read') {

    if (isset($_SESSION['json_file'])) {
        $jsonFile = $_SESSION['json_file'];

        if (file_exists($jsonFile)) {
            $jsonData = file_get_contents($jsonFile);
            $responseData = json_decode($jsonData, true);

            if (isset($responseData['transcribed_text']) && isset($responseData['chatgpt_response'])) {
                echo "<div class='card mt-3 p-4 shadow-sm'>
                        <h4 class='mb-3 text-primary'>Transcribed Text:</h4>
                        <p class='border rounded p-3 bg-light'>" . nl2br(htmlspecialchars($responseData['transcribed_text'])) . "</p>
                        <h4 class='mt-4 mb-3 text-success'>ChatGPT Response:</h4>
                        <p class='border rounded p-3 bg-light'>" . nl2br(htmlspecialchars($responseData['chatgpt_response'])) . "</p>
                      </div>";
            } else {
                echo "<div class='alert alert-warning mt-3 p-3'>Invalid response data in JSON file.</div>";
            }
        } else {
            echo "<div class='alert alert-danger mt-3 p-3'>JSON file not found.</div>";
        }
    } else {
        echo "<div class='alert alert-warning mt-3 p-3'>No JSON file path found in session.</div>";
    }
}
?>
