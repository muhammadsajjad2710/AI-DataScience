<?php
// Set TEST_MODE and test the workflow
putenv('TEST_MODE=1');

require_once __DIR__ . '/backend/db.php';
require_once __DIR__ . '/backend/google_stt.php';
require_once __DIR__ . '/backend/openai.php';

echo "=== TESTING WITH MOCK DATA ===\n\n";

// Test the complete workflow with mock data
$testFile = tempnam(sys_get_temp_dir(), 'test');
file_put_contents($testFile, 'test audio content');

echo "1. Testing Google STT (should return mock transcript):\n";
$sttResult = google_medical_stt($testFile, 'audio/webm');
echo "   Result: " . ($sttResult['ok'] ? "✅ SUCCESS" : "❌ FAILED") . "\n";
echo "   Transcript: " . $sttResult['transcript'] . "\n\n";

echo "2. Testing OpenAI (should return mock structured notes):\n";
$aiResult = openai_generate_notes($sttResult['transcript']);
echo "   Result: " . ($aiResult['ok'] ? "✅ SUCCESS" : "❌ FAILED") . "\n";
$notes = json_decode($aiResult['content'], true);
echo "   Generated Notes:\n";
foreach ($notes as $key => $value) {
    echo "     $key: $value\n";
}

unlink($testFile);

echo "\n=== FRONTEND TESTING ===\n";
echo "Now test the frontend at: http://localhost:8080/frontend/dashboard.html\n";
echo "The system should work with mock data until you configure real API keys.\n";
