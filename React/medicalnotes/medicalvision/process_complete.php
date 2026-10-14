<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Prevent any HTML output
ini_set('display_errors', 0);
ini_set('log_errors', 1);

try {
    // Credits check (must have at least 1)
    if (!isset($_SESSION['member_id'])) {
        throw new Exception('You must be logged in.');
    }

    require_once 'database.php';
    if (!$conn) {
        throw new Exception('Database connection failed.');
    }

    $memberId = (int) $_SESSION['member_id'];
    $stmt = $conn->prepare('SELECT credits FROM members WHERE member_id = ?');
    $stmt->bind_param('i', $memberId);
    $stmt->execute();
    $stmt->bind_result($availableCredits);
    $stmt->fetch();
    $stmt->close();

    if ((int)$availableCredits < 1) {
        echo json_encode([
            'status' => 'error',
            'code' => 'insufficient_credits',
            'message' => 'You have 0 credits. Please buy credits first. <a href="buy_credits.php" class="alert-link">Buy Credits</a>'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Check if image exists in session
    if (!isset($_SESSION['uploaded_image']) || empty($_SESSION['uploaded_image'])) {
        throw new Exception("No image found in session. Please upload an image first.");
    }

    $imagePath = $_SESSION['uploaded_image'];
    if (!file_exists($imagePath)) {
        throw new Exception("Image file not found: $imagePath");
    }

    // Step 1: Run OCR and get extracted text
    $pythonPath = 'python';
    $scriptPath = __DIR__ . DIRECTORY_SEPARATOR . "handwritten_to_text.py";
    
    $cmd = escapeshellcmd($pythonPath) . ' ' . escapeshellarg($scriptPath) . ' ' . escapeshellarg($imagePath) . ' 2>&1';
    
    $output = shell_exec($cmd);
    
    // Parse JSON from Python output
    $jsonStart = strpos($output, '{');
    if ($jsonStart === false) {
        throw new Exception("No JSON output from Python script");
    }
    
    $jsonString = substr($output, $jsonStart);
    $ocrResponse = json_decode($jsonString, true);
    
    if ($ocrResponse === null) {
        throw new Exception("Failed to parse JSON from OCR: " . json_last_error_msg());
    }
    
    if (isset($ocrResponse['error'])) {
        throw new Exception("OCR Error: " . $ocrResponse['error']);
    }
    
    if (!isset($ocrResponse['handwritten_text']) || empty($ocrResponse['handwritten_text'])) {
        throw new Exception("No text extracted from image");
    }
    
    $extractedText = $ocrResponse['handwritten_text'];
    
    // Step 2: Store extracted text in JSON file
    $jsonDir = __DIR__ . "/../storage/uploads/images/json/";
    if (!is_dir($jsonDir)) {
        mkdir($jsonDir, 0755, true);
    }
    
    $jsonFilename = 'ocr_result_' . time() . '.json';
    $jsonPath = $jsonDir . $jsonFilename;
    
    $jsonData = [
        'timestamp' => date('Y-m-d H:i:s'),
        'image_path' => $imagePath,
        'extracted_text' => $extractedText,
        'ocr_status' => 'success'
    ];
    
    if (file_put_contents($jsonPath, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
        throw new Exception("Failed to save JSON file");
    }
    
    // Step 3: Process with OpenAI to create FHIR format
    include "config.php";
    $openaiApiKey = $openai_api_key;
    
    $openaiPrompt = "Convert the following medical document text into FHIR (Fast Healthcare Interoperability Resources) format. Create a structured JSON response that includes relevant FHIR resources like Patient, Observation, or DocumentReference. The text is:\n\n" . $extractedText;
    
    $openaiData = [
        'model' => 'gpt-4o-mini',
        'messages' => [
            [
                'role' => 'system',
                'content' => 'You are a medical data specialist. Convert medical documents to FHIR format. Return only valid JSON.'
            ],
            [
                'role' => 'user',
                'content' => $openaiPrompt
            ]
        ],
        'max_tokens' => 2000,
        'temperature' => 0.1
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($openaiData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $openaiApiKey
    ]);
    
    $openaiResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        throw new Exception("OpenAI API request failed with HTTP code: $httpCode");
    }
    
    $openaiResult = json_decode($openaiResponse, true);
    if ($openaiResult === null) {
        throw new Exception("Failed to parse OpenAI response");
    }
    
    $fhirContent = $openaiResult['choices'][0]['message']['content'] ?? '';
    
    // Try to extract JSON from OpenAI response
    $fhirJsonStart = strpos($fhirContent, '{');
    if ($fhirJsonStart !== false) {
        $fhirJsonString = substr($fhirContent, $fhirJsonStart);
        $fhirData = json_decode($fhirJsonString, true);
        if ($fhirData === null) {
            // If JSON parsing fails, store the raw content
            $fhirData = ['raw_content' => $fhirContent];
        }
    } else {
        $fhirData = ['raw_content' => $fhirContent];
    }
    
    // Step 4: Store in database (reuse existing $conn)
    // $memberId is already validated above
    
    $fhirJsonForDb = json_encode($fhirData);
    $stmt = $conn->prepare("INSERT INTO fhir_documents (member_id, image_path, extracted_text, fhir_json) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $memberId, $imagePath, $extractedText, $fhirJsonForDb);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to save to database: " . $stmt->error);
    }
    
    $documentId = $conn->insert_id;
    $stmt->close();
    
    // Step 5: Deduct 1 credit AFTER successful processing
    $deduct = $conn->prepare('UPDATE members SET credits = credits - 1 WHERE member_id = ?');
    $deduct->bind_param('i', $memberId);
    $deduct->execute();
    $deduct->close();

    // Fetch updated credits to return in response (optional UI update)
    $stmt2 = $conn->prepare('SELECT credits FROM members WHERE member_id = ?');
    $stmt2->bind_param('i', $memberId);
    $stmt2->execute();
    $stmt2->bind_result($remainingCredits);
    $stmt2->fetch();
    $stmt2->close();

    // Step 6: Return success response
    $response = [
        'status' => 'success',
        'message' => 'OCR and FHIR processing completed successfully',
        'data' => [
            'document_id' => $documentId,
            'extracted_text' => $extractedText,
            'fhir_data' => $fhirData,
            'json_file' => $jsonPath,
            'timestamp' => date('Y-m-d H:i:s'),
            'remaining_credits' => (int)($remainingCredits ?? ($availableCredits - 1))
        ]
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    $errorResponse = [
        'status' => 'error',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode($errorResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>
