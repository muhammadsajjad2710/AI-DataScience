<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

cors();
require_method('POST');

$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');
$password = (string)($input['password'] ?? '');
$name = trim($input['name'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['error' => 'Invalid email'], 422);
}
if (strlen($password) < 6) {
    json_response(['error' => 'Password too short'], 422);
}

$pdo = null;
try {
    $pdo = get_pdo();
} catch (Throwable $e) {
    json_response(['error' => 'Database connection failed: ' . $e->getMessage()], 500);
}
try {
    $stmt = $pdo->prepare('INSERT INTO users (email, password_hash, name) VALUES (:email, :hash, :name)');
    $stmt->execute([
        ':email' => $email,
        ':hash' => password_hash($password, PASSWORD_DEFAULT),
        ':name' => $name,
    ]);
    $_SESSION['user_id'] = (int)$pdo->lastInsertId();
    json_response(['ok' => true, 'user' => ['id' => $_SESSION['user_id'], 'email' => $email, 'name' => $name]]);
} catch (PDOException $e) {
    if ((int)$e->getCode() === 23000) {
        json_response(['error' => 'Email already registered'], 409);
    }
    json_response(['error' => 'Registration failed'], 500);
}


