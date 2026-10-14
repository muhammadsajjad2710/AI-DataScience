<?php

use PHPUnit\Framework\TestCase;

final class GoogleSttClientTest extends TestCase
{
    public function test_stt_mock_returns_transcript(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'aud');
        file_put_contents($tmp, 'audio');
        $out = google_medical_stt($tmp, 'audio/webm');
        $this->assertTrue($out['ok']);
        $this->assertIsString($out['transcript']);
        unlink($tmp);
    }
}


