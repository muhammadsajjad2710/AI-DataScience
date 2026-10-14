<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/google_stt.php';
require_once __DIR__ . '/openai.php';

cors();
require_method('POST');
$userId = require_auth();

if (!isset($_FILES['audio'])) {
    json_response(['error' => 'No audio uploaded'], 400);
}

$file = $_FILES['audio'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    json_response(['error' => 'Upload error: ' . $file['error']], 400);
}

$uploadDir = ensure_upload_dir();
$ext = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'dat';
$safeName = 'u' . $userId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$dest = $uploadDir . DIRECTORY_SEPARATOR . $safeName;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    json_response(['error' => 'Failed to save file'], 500);
}

try {
    $mime = $file['type'] ?? null;
    $stt = google_medical_stt($dest, $mime);
    if (!$stt['ok']) {
        json_response(['error' => $stt['error'] ?? 'STT failed'], 502);
    }
    $transcript = $stt['transcript'] ?? '';

    $ai = openai_generate_notes($transcript);
    if (!$ai['ok']) {
        json_response(['error' => $ai['error'] ?? 'AI failed', 'transcription' => $transcript], 502);
    }
    $aiContent = $ai['content'] ?? '';

    $pdo = get_pdo();
    $stmt = $pdo->prepare('INSERT INTO notes (user_id, audio_filename, transcription, ai_output) VALUES (:uid, :fn, :tr, :ai)');
    $stmt->execute([
        ':uid' => $userId,
        ':fn' => $safeName,
        ':tr' => $transcript,
        ':ai' => $aiContent,
    ]);

    json_response(['ok' => true, 'note' => [
        'id' => (int)$pdo->lastInsertId(),
        'audio_filename' => $safeName,
        'transcription' => $transcript,
        'ai_output' => json_decode($aiContent, true) ?? $aiContent,
    ]]);
} catch (Throwable $e) {
    json_response(['error' => 'Processing failed: ' . $e->getMessage()], 500);
}


