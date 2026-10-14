<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

cors();
require_method('GET');
$userId = require_auth();

$pdo = get_pdo();
$stmt = $pdo->prepare('SELECT id, transcription, ai_output, audio_filename, created_at FROM notes WHERE user_id = :uid ORDER BY created_at DESC LIMIT 200');
$stmt->execute([':uid' => $userId]);
$rows = $stmt->fetchAll();
json_response(['ok' => true, 'notes' => $rows]);


