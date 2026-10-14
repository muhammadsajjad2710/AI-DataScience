<?php

use PHPUnit\Framework\TestCase;

final class OpenAiClientTest extends TestCase
{
    public function test_openai_mock_returns_json(): void
    {
        $out = openai_generate_notes('test transcript');
        $this->assertTrue($out['ok']);
        $this->assertIsString($out['content']);
        $data = json_decode($out['content'], true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('Diagnosis', $data);
    }
}


