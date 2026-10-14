<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/google_stt.php';
require_once __DIR__ . '/openai.php';

cors();
require_method('GET');

// Test Google STT
$testFile = tempnam(sys_get_temp_dir(), 'test');
file_put_contents($testFile, 'test audio content');
$sttResult = google_medical_stt($testFile, 'audio/webm');
unlink($testFile);

// Test OpenAI
$aiResult = openai_generate_notes('Patient has fever and cough for 3 days');

json_response([
    'test_mode' => getenv('TEST_MODE'),
    'stt_result' => $sttResult,
    'ai_result' => $aiResult,
]);
