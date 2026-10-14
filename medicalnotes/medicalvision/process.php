<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

include "database.php";

if (!isset($_SESSION['member_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in.']);
    exit;
}

if (!$conn) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["image_path"])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'No image_path provided.']);
    exit;
}

$userId = $_SESSION['member_id'];

// Check credits
$creditStmt = $conn->prepare("SELECT credits FROM members WHERE member_id = ?");
$creditStmt->bind_param("i", $userId);
$creditStmt->execute();
$creditStmt->bind_result($credits);
$creditStmt->fetch();
$creditStmt->close();

if ($credits < 1) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Not enough credits. Please buy credits to continue.',
        'remaining_credits' => $credits
    ]);
    exit;
}

$imagePath = $_POST['image_path'];
$imageName = basename($imagePath);

// Paths — adjust if needed
$pythonScript = __DIR__ . "/handwritten_to_text.py";
$venvPython = "/var/www/html/medicalvision/myenv/bin/python3"; // change if necessary

if (!file_exists($pythonScript)) {
    echo json_encode(['status' => 'error', 'message' => 'OCR script not found on server.']);
    exit;
}
if (!file_exists($venvPython)) {
    echo json_encode(['status' => 'error', 'message' => 'Python binary not found. Check venv path.']);
    exit;
}

$cmd = escapeshellcmd("$venvPython") . ' ' . escapeshellarg($pythonScript) . ' ' . escapeshellarg($imagePath) . ' 2>&1';
$output = shell_exec($cmd);

// try to parse JSON from output
$jsonStart = strpos($output, '{');
$response = null;
if ($jsonStart !== false) {
    $jsonString = substr($output, $jsonStart);
    $response = json_decode($jsonString, true);
}

if ($response === null) {
    // return error but include raw_output for debugging in the frontend (if needed)
    echo json_encode([
        'status' => 'error',
        'message' => 'OCR failed or returned invalid JSON.',
        'raw_output' => $output
    ]);
    exit;
}

if (isset($response['handwritten_text']) && !empty($response['handwritten_text'])) {
    $extractedText = $response['handwritten_text'];
    $jsonResponse = json_encode($response, JSON_UNESCAPED_UNICODE);
    $fhirJson = json_encode(["resourceType" => "DocumentReference", "content" => $response], JSON_UNESCAPED_UNICODE);

    $stmt = $conn->prepare("INSERT INTO fhir_documents (image_name, extracted_text, json_response, fhir_json) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $imageName, $extractedText, $jsonResponse, $fhirJson);

    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'DB insert error: ' . $conn->error]);
        exit;
    }
    $stmt->close();

    // Deduct 1 credit
    $updateCredits = $conn->prepare("UPDATE members SET credits = credits - 1 WHERE member_id = ?");
    $updateCredits->bind_param("i", $userId);
    $updateCredits->execute();
    $updateCredits->close();

    // Fetch remaining credits
    $creditStmt2 = $conn->prepare("SELECT credits FROM members WHERE member_id = ?");
    $creditStmt2->bind_param("i", $userId);
    $creditStmt2->execute();
    $creditStmt2->bind_result($remainingCredits);
    $creditStmt2->fetch();
    $creditStmt2->close();

    // Optionally generate AI response if OPENAI_API_KEY env var exists
    $aiResponseText = null;
    $openai_key = getenv('YOUR_API_KEY_HERE') ?: null;
    if ($openai_key) {
        // Simple Chat Completion via OpenAI API (uses gpt-3.5-turbo). Adjust model as needed.
        $prompt = "You are given this extracted handwritten text. Provide a short cleaned-up plain-text transcription and a short summary:\n\n" . $extractedText;

        $payload = json_encode([
            "model" => "gpt-3.5-turbo",
            "messages" => [
                ["role" => "system", "content" => "You are a helpful medical transcription assistant."],
                ["role" => "user", "content" => $prompt]
            ],
            "temperature" => 0.2,
            "max_tokens" => 500
        ]);

        $ch = curl_init("https://api.openai.com/v1/chat/completions");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $openai_key
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode === 200 && $result) {
            $resJson = json_decode($result, true);
            if (isset($resJson['choices'][0]['message']['content'])) {
                $aiResponseText = trim($resJson['choices'][0]['message']['content']);
            }
        } else {
            // If OpenAI fail, we won't block the main flow; include message
            $aiResponseText = null;
        }
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Text extracted and saved.',
        'extracted_text' => $extractedText,
        'ai_response' => $aiResponseText,
        'remaining_credits' => $remainingCredits
    ]);
    exit;
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No text extracted from image.',
        'raw_response' => $response
    ]);
    exit;
}
