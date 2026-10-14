<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

cors();
require_method('POST');

$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');
$password = (string)($input['password'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['error' => 'Invalid email'], 422);
}

$pdo = null;
try {
    $pdo = get_pdo();
} catch (Throwable $e) {
    json_response(['error' => 'Database connection failed: ' . $e->getMessage()], 500);
}
$stmt = $pdo->prepare('SELECT id, password_hash, name FROM users WHERE email = :email');
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();
if (!$user || !password_verify($password, $user['password_hash'])) {
    json_response(['error' => 'Invalid credentials'], 401);
}

$_SESSION['user_id'] = (int)$user['id'];
json_response(['ok' => true, 'user' => ['id' => (int)$user['id'], 'email' => $email, 'name' => $user['name']]]);


