<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

/**
 * @param string $audioFilePath
 * @param string|null $mimeType
 * @return array{ok:bool,transcript?:string,error?:string}
 */
function google_medical_stt(string $audioFilePath, ?string $mimeType = null): array {
    // Check if TEST_MODE is enabled
    if (getenv('TEST_MODE') === '1') {
        return ['ok' => true, 'transcript' => 'Patient John Doe has fever and cough for 3 days. Diagnosis likely influenza. Start oseltamivir.'];
    }
    $cfg = get_config()['google'];
    $apiKey = $cfg['api_key'];
    if (!$apiKey) {
        return ['ok' => false, 'error' => 'Google API key not configured'];
    }

    if (!file_exists($audioFilePath)) {
        return ['ok' => false, 'error' => 'Audio file not found'];
    }

    $audioData = file_get_contents($audioFilePath);
    if ($audioData === false) {
        return ['ok' => false, 'error' => 'Failed to read audio'];
    }

    $encoding = $cfg['encoding'];
    if ($mimeType) {
        if (stripos($mimeType, 'mp3') !== false) $encoding = 'MP3';
        elseif (stripos($mimeType, 'ogg') !== false || stripos($mimeType, 'opus') !== false) $encoding = 'OGG_OPUS';
        elseif (stripos($mimeType, 'wav') !== false) $encoding = 'LINEAR16';
        elseif (stripos($mimeType, 'm4a') !== false) $encoding = 'MULAW';
    }

    $payload = [
        'config' => [
            'encoding' => $encoding,
            'languageCode' => $cfg['language_code'],
            'enableAutomaticPunctuation' => true,
            'useEnhanced' => true,
            'model' => $cfg['speech_model'],
            'sampleRateHertz' => $cfg['sample_rate_hertz'],
        ],
        'audio' => [
            'content' => base64_encode($audioData),
        ],
    ];

    $endpoint = $cfg['speech_endpoint'] . '?key=' . urlencode($apiKey);

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 120,
    ]);

    $response = curl_exec($ch);
    if ($response === false) {
        return ['ok' => false, 'error' => 'Google STT request failed: ' . curl_error($ch)];
    }
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode($response, true);
    if ($status >= 400) {
        return ['ok' => false, 'error' => 'Google STT HTTP ' . $status . ': ' . ($data['error']['message'] ?? $response)];
    }

    $transcript = '';
    if (isset($data['results']) && is_array($data['results'])) {
        foreach ($data['results'] as $result) {
            $alt = $result['alternatives'][0]['transcript'] ?? '';
            if ($alt) $transcript .= ($transcript ? ' ' : '') . $alt;
        }
    }

    return ['ok' => true, 'transcript' => trim($transcript)];
}


