<?php

use PHPUnit\Framework\TestCase;

final class DbConnectionTest extends TestCase
{
    public function test_sqlite_memory_schema_and_insert_user(): void
    {
        // Build minimal schema in sqlite memory for unit test
        $pdo = get_pdo();
        $pdo->exec('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT, email TEXT UNIQUE, password_hash TEXT, name TEXT, role TEXT DEFAULT "doctor", created_at TEXT)');
        $stmt = $pdo->prepare('INSERT INTO users (email, password_hash, name) VALUES (:e,:p,:n)');
        $stmt->execute([':e' => 't@example.com', ':p' => password_hash('secret', PASSWORD_DEFAULT), ':n' => 'Test']);
        $this->assertGreaterThan(0, (int)$pdo->lastInsertId());
    }
}


