<?php

declare(strict_types=1);

session_start();

/** @return array<string,mixed> */
function get_config(): array {
    static $config = null;
    if ($config === null) {
        $config = require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config.php';
    }
    return $config;
}

/** @return PDO */
function get_pdo(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;
    $cfg = get_config()['db'];
    $envDsn = getenv('DB_DSN') ?: '';
    if ($envDsn) {
        $dsn = $envDsn;
        $user = getenv('DB_USER') ?: '';
        $pass = getenv('DB_PASS') ?: '';
    } else {
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $cfg['host'], $cfg['port'], $cfg['database'], $cfg['charset']);
        $user = $cfg['username'];
        $pass = $cfg['password'];
    }
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
}

function json_response($data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json');
    header('Cache-Control: no-store');
    header('Pragma: no-cache');
    echo json_encode($data);
    exit;
}

function require_method(string $method): void {
    if ($_SERVER['REQUEST_METHOD'] !== $method) {
        json_response(['error' => 'Method Not Allowed'], 405);
    }
}

function ensure_upload_dir(): string {
    $dir = get_config()['app']['upload_dir'];
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    return $dir;
}

function require_auth(): int {
    if (isset($_SESSION['user_id'])) {
        return (int)$_SESSION['user_id'];
    }
    // Authentication disabled: use or create a default anonymous user
    return get_or_create_default_user_id();
}

function cors(): void {
    $origins = get_config()['app']['allowed_origins'];
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
    if (in_array('*', $origins, true) || in_array($origin, $origins, true)) {
        header('Access-Control-Allow-Origin: ' . ($origin ?: '*'));
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }
    if (strtoupper($_SERVER['REQUEST_METHOD']) === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

function get_or_create_default_user_id(): int {
    $pdo = get_pdo();
    $email = 'anonymous@local';
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :e');
    $stmt->execute([':e' => $email]);
    $row = $stmt->fetch();
    if ($row && isset($row['id'])) {
        return (int)$row['id'];
    }
    $insert = $pdo->prepare('INSERT INTO users (email, password_hash, name) VALUES (:e, :p, :n)');
    $insert->execute([
        ':e' => $email,
        ':p' => password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT),
        ':n' => 'Anonymous',
    ]);
    return (int)$pdo->lastInsertId();
}


