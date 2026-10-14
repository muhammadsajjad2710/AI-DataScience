<?php
// Basic configuration for API keys and database connection
// Rename this file to config.php and fill in the credentials.

return [
    'app' => [
        'base_url' => '', // e.g., http://localhost/realtimevoicechatbot (optional)
        'upload_dir' => __DIR__ . DIRECTORY_SEPARATOR . 'uploads',
        'allowed_origins' => ['*'],
        'enable_tts' => false,
    ],
    'db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'database' => 'realtimevoicechatbot',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
    'google' => [
        // Either provide an API key or set GOOGLE_APPLICATION_CREDENTIALS to a service account JSON
        'api_key' => '',
        'speech_endpoint' => 'https://speech.googleapis.com/v1p1beta1/speech:recognize',
        // For medical models, use medical_conversation or medical_dictation depending on need
        'speech_model' => 'medical_conversation',
        'language_code' => 'en-US',
        'encoding' => 'LINEAR16', // or 'MP3', 'OGG_OPUS', based on upload
        'sample_rate_hertz' => 16000,
    ],
    'openai' => [
        'api_key' => 'YOUR_API_KEY_HERE',
        'model' => 'gpt-4o-mini',
        'endpoint' => 'https://api.openai.com/v1/chat/completions',
        'system_prompt' => <<<PROMPT
You are a medical transcription assistant.
Input: doctor's raw dictation text.
Output: structured clinical notes in this JSON format:
{
  "PatientName": "...",
  "Symptoms": "...",
  "Diagnosis": "...",
  "Prescription": "...",
  "FollowUp": "..."
}
If any field is missing, leave it blank. Do not invent details.
PROMPT,
    ],
];


