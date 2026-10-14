<?php

session_start();
if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_POST['filename'])) {
    die("Invalid access!");
}

$filename = $_POST['filename'];
$jsonPath = "uploads/{$filename}_text.json";

if (!file_exists($jsonPath)) {
    die("Extracted JSON file not found.");
}

$jsonData = json_decode(file_get_contents($jsonPath), true);
$text = $jsonData["text"] ?? "";

if (empty($text)) {
    die("No text found in JSON!");
}

// Include database connection
require_once("database.php"); // this should create $conn
// Include OpenAI API key
require_once("config.php"); 

$text = substr($text, 0, 2000);
$url = "https://api.openai.com/v1/chat/completions";

// First check if it's a law document
$checkData = [
    "model" => "gpt-4",
    "messages" => [
        ["role" => "system", "content" => "You are a document classifier. Check if the following text is from a legal or law-related document. Reply only with 'yes' or 'no'."],
        ["role" => "user", "content" => $text]
    ]
];

$headers = [
    "Content-Type: application/json",
    "Authorization: Bearer $openai_api_key"
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($checkData));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
curl_close($ch);

$responseData = json_decode($response, true);
$isLawDocument = strtolower(trim($responseData["choices"][0]["message"]["content"] ?? "")) === "yes";

$points = [];
$chatgptFullJsonResponse = null; // Store full JSON response

if ($isLawDocument) {
    $data = [
        "model" => "gpt-4",
        "messages" => [
            ["role" => "system", "content" => "You are a helpful legal assistant. Extract only the most important points from this legal document. Return the result as a JSON array of objects, where each object must have two fields: 'label' and 'text'. Label must be one of these: [Warning], [Rule], [Alert], [Definition], [Procedure], or [Point]. No bullet points or numbering, pure JSON output."],
            ["role" => "user", "content" => $text]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);

    // Store full ChatGPT JSON response
    $chatgptFullJsonResponse = json_decode($response, true);

    if (isset($chatgptFullJsonResponse["choices"][0]["message"]["content"])) {
        $gptContent = $chatgptFullJsonResponse["choices"][0]["message"]["content"];
        
        // Try to decode the text content which must be a JSON array
        $extractedPoints = json_decode($gptContent, true);

        if (is_array($extractedPoints)) {
            foreach ($extractedPoints as $point) {
                if (!empty($point['label']) && !empty($point['text'])) {
                    $points[] = [
                        'label' => trim($point['label'], '[]'), // Remove [ ] if present
                        'text' => $point['text']
                    ];
                }
            }
        }
    }
}

// --- After processing, insert into the database ---

$pdfName = $filename; // name of the uploaded PDF
$extractedText = $text; // extracted text (limited to 2000 characters above)
$chatgptResponseJson = json_encode($chatgptFullJsonResponse); // encode full GPT response as JSON string

// Prepare Insert Query
$insertQuery = "INSERT INTO pdf_chatgpt_responses (pdf_name, extracted_text, chatgpt_response, created_at) VALUES (?, ?, ?, NOW())";
$insertStmt = $conn->prepare($insertQuery);
$insertStmt->bind_param("sss", $pdfName, $extractedText, $chatgptResponseJson);

if ($insertStmt->execute()) {
    echo "✅ Data inserted successfully!";
} else {
    // Duplicate entry error (MySQL error code 1062)
    if ($conn->errno == 1062) {
        echo "⚠️ Duplicate detected. Data was not inserted.";
    } else {
        echo "❌ Error inserting data: " . $conn->error;
    }
}

$insertStmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ChatGPT Legal Document Analysis</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: #f4f6f8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.08);
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        .highlight-label {
            display: inline-block;
            padding: 8px 18px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            margin-bottom: 15px;
            letter-spacing: 1px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        /* Label colors */
        .label-Definition { background: linear-gradient(45deg, #28a745, #218838); color: #fff; }
        .label-Rule { background: linear-gradient(45deg, #007bff, #0056b3); color: #fff; }
        .label-Warning { background: linear-gradient(45deg, #dc3545, #a71d2a); color: #fff; }
        .label-Procedure { background: linear-gradient(45deg, #6f42c1, #4b2a8e); color: #fff; }
        .label-Alert { background: linear-gradient(45deg, #fd7e14, #e8590c); color: #fff; }
        .label-Point { background: linear-gradient(45deg, #6c757d, #343a40); color: #fff; }

        /* General block styles */
        .point-block {
            background: #f8f9fa;
            border-left: 6px solid #ced4da;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            font-size: 17px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            line-height: 1.6;
            color: #333;
        }

        .point-block:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 24px rgba(0,0,0,0.1);
        }

        /* Special styles for Warning block */
        .warning-block {
            background: #fff0f0;
            border-left: 10px solid #dc3545;
            font-size: 24px; /* <--- Larger font for Warning */
            font-weight: bold;
            color: #b21f2d;
            line-height: 1.8;
        }

        /* Upload another button */
        a {
            display: inline-block;
            text-decoration: none;
            background: #007bff;
            color: #fff;
            padding: 10px 25px;
            border-radius: 30px;
            margin-top: 30px;
            text-align: center;
            transition: background 0.3s ease;
        }

        a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<?php include('header.php'); ?>

<div class="container">
    <h2>ChatGPT Legal Summary:</h2>

    <?php if (!$isLawDocument): ?>
        <h2 style='color: red; text-align:center;'>The uploaded document is not related to law. Please upload a document related to law.</h2>
    <?php else: ?>
        <?php if (!empty($points)): ?>
            <?php foreach ($points as $point): ?>
                <?php
                    $blockClass = ($point['label'] === 'Warning') ? 'point-block warning-block' : 'point-block';
                ?>
                <div class="<?php echo $blockClass; ?>">
                    <span class="highlight-label label-<?php echo htmlentities($point['label']); ?>">
                        [<?php echo htmlentities($point['label']); ?>]
                    </span>
                    <?php echo htmlentities($point['text']); ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>

    <div style="text-align:center;">
        <a href="index.php">Upload Another Document</a>
    </div>
</div>

<?php include('footer.php'); ?>
</body>
</html>
