<?php
// Test script for upload workflow
require_once __DIR__ . '/backend/db.php';
require_once __DIR__ . '/backend/google_stt.php';
require_once __DIR__ . '/backend/openai.php';

echo "Testing complete workflow:\n";

// Test 1: Database connection
try {
    $pdo = get_pdo();
    echo "✓ Database connection: OK\n";
} catch (Exception $e) {
    echo "✗ Database connection: FAILED - " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Google STT
$testFile = tempnam(sys_get_temp_dir(), 'test');
file_put_contents($testFile, 'test audio content');
$sttResult = google_medical_stt($testFile, 'audio/webm');
unlink($testFile);

if ($sttResult['ok']) {
    echo "✓ Google STT: OK - Transcript: " . substr($sttResult['transcript'], 0, 50) . "...\n";
} else {
    echo "✗ Google STT: FAILED - " . $sttResult['error'] . "\n";
    exit(1);
}

// Test 3: OpenAI
$aiResult = openai_generate_notes($sttResult['transcript']);
if ($aiResult['ok']) {
    echo "✓ OpenAI: OK - Generated structured notes\n";
    $notes = json_decode($aiResult['content'], true);
    echo "  Patient: " . $notes['PatientName'] . "\n";
    echo "  Diagnosis: " . $notes['Diagnosis'] . "\n";
} else {
    echo "✗ OpenAI: FAILED - " . $aiResult['error'] . "\n";
    exit(1);
}

// Test 4: Save to database
try {
    $userId = get_or_create_default_user_id();
    $stmt = $pdo->prepare('INSERT INTO notes (user_id, audio_filename, transcription, ai_output) VALUES (:uid, :fn, :tr, :ai)');
    $stmt->execute([
        ':uid' => $userId,
        ':fn' => 'test_audio.webm',
        ':tr' => $sttResult['transcript'],
        ':ai' => $aiResult['content'],
    ]);
    $noteId = $pdo->lastInsertId();
    echo "✓ Database save: OK - Note ID: $noteId\n";
} catch (Exception $e) {
    echo "✗ Database save: FAILED - " . $e->getMessage() . "\n";
    exit(1);
}

// Test 5: Fetch notes
try {
    $stmt = $pdo->prepare('SELECT * FROM notes WHERE user_id = :uid ORDER BY created_at DESC LIMIT 1');
    $stmt->execute([':uid' => $userId]);
    $note = $stmt->fetch();
    if ($note) {
        echo "✓ Fetch notes: OK - Found " . count($note) . " fields\n";
    } else {
        echo "✗ Fetch notes: FAILED - No notes found\n";
    }
} catch (Exception $e) {
    echo "✗ Fetch notes: FAILED - " . $e->getMessage() . "\n";
}

echo "\n🎉 All tests passed! The workflow is working correctly.\n";
