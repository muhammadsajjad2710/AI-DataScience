<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

cors();
require_method('POST');

$cfg = get_config()['db'];

try {
    $pdo = new PDO(sprintf('mysql:host=%s;port=%d;charset=%s', $cfg['host'], $cfg['port'], $cfg['charset']), $cfg['username'], $cfg['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . str_replace('`','``',$cfg['database']) . '`');
} catch (Throwable $e) {
    json_response(['ok' => false, 'error' => 'Failed to create database: ' . $e->getMessage()], 500);
}

try {
    $pdo = get_pdo();
    $sql = file_get_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR . 'schema.sql');
    if ($sql === false) throw new RuntimeException('schema.sql not found');
    $pdo->exec($sql);
} catch (Throwable $e) {
    json_response(['ok' => false, 'error' => 'Failed to apply schema: ' . $e->getMessage()], 500);
}

json_response(['ok' => true, 'message' => 'Database initialized']);


