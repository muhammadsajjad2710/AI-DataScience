<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

/**
 * @param string $transcript
 * @return array{ok:bool,content?:string,error?:string}
 */
function openai_generate_notes(string $transcript): array {
    // Check if TEST_MODE is enabled
    if (getenv('TEST_MODE') === '1') {
        $mock = [
            'PatientName' => 'John Doe',
            'Symptoms' => 'Fever, Cough',
            'Diagnosis' => 'Influenza',
            'Prescription' => 'Oseltamivir 75mg BID x5 days',
            'FollowUp' => 'Return if symptoms persist in 72 hours'
        ];
        return ['ok' => true, 'content' => json_encode($mock)];
    }
    $cfg = get_config()['openai'];
    $apiKey = $cfg['api_key'];
    if (!$apiKey) {
        return ['ok' => false, 'error' => 'OpenAI API key not configured'];
    }

    $payload = [
        'model' => $cfg['model'],
        'messages' => [
            ['role' => 'system', 'content' => $cfg['system_prompt']],
            ['role' => 'user', 'content' => $transcript],
        ],
        'temperature' => 0.1,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $cfg['endpoint'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 60,
    ]);

    $response = curl_exec($ch);
    if ($response === false) {
        return ['ok' => false, 'error' => 'OpenAI request failed: ' . curl_error($ch)];
    }
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode($response, true);
    if ($status >= 400) {
        return ['ok' => false, 'error' => 'OpenAI HTTP ' . $status . ': ' . ($data['error']['message'] ?? $response)];
    }

    $content = $data['choices'][0]['message']['content'] ?? '';
    return ['ok' => true, 'content' => $content];
}


