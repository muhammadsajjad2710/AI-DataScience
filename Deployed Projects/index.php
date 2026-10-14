<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medical AI Document Assistant</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background-color: #f9f9f9;
            color: #333;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
        }
        .container {
            max-width: 700px;
            margin: auto;
        }
        .link-box {
            background-color: #fff;
            border: 2px solid #007BFF;
            padding: 20px;
            margin: 25px 0;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            transition: transform 0.2s ease;
        }
        .link-box:hover {
            transform: scale(1.02);
        }
        
        h2 {
            margin-top: 0;
        }
        a {
            text-decoration: none;
            font-size: 18px;
            color: #007BFF;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Medical AI Document Assistant</h1>
        <p style="text-align:center;">Choose a module to get started:</p>

        <div class="link-box">
            <h2>Medical Voice Transcription</h2>
            <p><a href="medicalvoice/index.php">Go to Voice-to-Text Converter</a></p>
        </div>

        <div class="link-box">
            <h2>Medical Vision Recognition</h2>
            <p><a href="medicalvision/index.php">Go to Handwriting or Image Text Extractor</a></p>
        </div>
    </div>

</body>
</html>

