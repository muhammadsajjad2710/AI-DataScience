<?php
// Test script to show real workflow issues
require_once __DIR__ . '/backend/db.php';
require_once __DIR__ . '/backend/google_stt.php';
require_once __DIR__ . '/backend/openai.php';

echo "=== REAL WORKFLOW DIAGNOSTIC REPORT ===\n\n";

// Test 1: Check API Configuration
echo "1. API CONFIGURATION CHECK:\n";
$config = require __DIR__ . '/config.php';

echo "   Google API Key: " . (empty($config['google']['api_key']) ? "❌ MISSING" : "✅ CONFIGURED") . "\n";
echo "   OpenAI API Key: " . (empty($config['openai']['api_key']) ? "❌ MISSING" : "✅ CONFIGURED") . "\n";
echo "   TEST_MODE: " . (getenv('TEST_MODE') === '1' ? "✅ ENABLED (using mock data)" : "❌ DISABLED (using real APIs)") . "\n\n";

// Test 2: Test with real audio file
echo "2. REAL AUDIO PROCESSING TEST:\n";
$testFile = tempnam(sys_get_temp_dir(), 'test');
file_put_contents($testFile, 'test audio content');

$sttResult = google_medical_stt($testFile, 'audio/webm');
echo "   Google STT Result: " . ($sttResult['ok'] ? "✅ SUCCESS" : "❌ FAILED") . "\n";
if (!$sttResult['ok']) {
    echo "   Error: " . $sttResult['error'] . "\n";
} else {
    echo "   Transcript: " . substr($sttResult['transcript'], 0, 50) . "...\n";
}

$aiResult = openai_generate_notes($sttResult['transcript'] ?? 'test transcript');
echo "   OpenAI Result: " . ($aiResult['ok'] ? "✅ SUCCESS" : "❌ FAILED") . "\n";
if (!$aiResult['ok']) {
    echo "   Error: " . $aiResult['error'] . "\n";
} else {
    $notes = json_decode($aiResult['content'], true);
    echo "   Generated Notes: " . ($notes ? "✅ VALID JSON" : "❌ INVALID JSON") . "\n";
}

unlink($testFile);

// Test 3: Database test
echo "\n3. DATABASE TEST:\n";
try {
    $pdo = get_pdo();
    echo "   Database Connection: ✅ SUCCESS\n";
    
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM notes');
    $stmt->execute();
    $result = $stmt->fetch();
    echo "   Total Notes in DB: " . $result['count'] . "\n";
} catch (Exception $e) {
    echo "   Database Error: ❌ " . $e->getMessage() . "\n";
}

echo "\n=== DIAGNOSIS ===\n";
if (empty($config['google']['api_key']) || empty($config['openai']['api_key'])) {
    echo "❌ ISSUE: API keys are not configured in config.php\n";
    echo "   SOLUTION: Add your Google Cloud and OpenAI API keys to config.php\n";
    echo "   OR: Set TEST_MODE=1 to use mock data for testing\n\n";
} else {
    echo "✅ API keys are configured\n";
}

if (getenv('TEST_MODE') === '1') {
    echo "ℹ️  TEST_MODE is enabled - using mock data\n";
    echo "   To use real APIs: unset TEST_MODE and configure API keys\n\n";
} else {
    echo "ℹ️  TEST_MODE is disabled - using real APIs\n";
    echo "   To use mock data: set TEST_MODE=1\n\n";
}

echo "=== RECOMMENDATIONS ===\n";
echo "1. For testing: Set TEST_MODE=1 (currently using mock data)\n";
echo "2. For production: Configure real API keys in config.php\n";
echo "3. For development: Use mock data until API keys are ready\n";
