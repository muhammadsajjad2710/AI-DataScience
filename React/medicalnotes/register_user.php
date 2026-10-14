<?php
include("database.php");

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize input
    $login = trim($_POST['login']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $company = trim($_POST['company']);
    $level = 1; // default user level
    $jointid = date("Y-m-d H:i:s");
    $stripeid = null;

    // Check if username or email already exists
    $checkSql = "SELECT member_id FROM members WHERE login = ? OR email = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("ss", $login, $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        showError("Username or email already exists. Please choose different credentials or sign in instead.");
        exit;
    }
    $checkStmt->close();

    // Insert user into database
    $sql = "INSERT INTO members (login, password, name, email, phone, company, level, jointid, stripeid)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("ssssssiss", $login, $password, $name, $email, $phone, $company, $level, $jointid, $stripeid);

    if ($stmt->execute()) {
        header("Location: login.php?success=registered");
        exit;
    } else {
        showError("Registration failed: " . $stmt->error . ". Please try again.");
    }

    $stmt->close();
    $conn->close();
} else {
    showError("Invalid request. Please submit the registration form properly.");
}

function showError($message) {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Registration Error - Medical AI</title>
        <link href='https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap' rel='stylesheet'>
        <style>
            :root{--danger:#ef4444;--text:#1f2937;--bg:#f9fafb;--card:#ffffff;--border:#e5e7eb}
            *{box-sizing:border-box}
            body{font-family:Inter,system-ui,sans-serif;background:var(--bg);margin:0;color:var(--text);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1rem}
            .error-card{background:var(--card);border:1px solid var(--border);border-radius:16px;box-shadow:0 10px 25px rgba(0,0,0,0.08);max-width:480px;width:100%;padding:2rem;text-align:center}
            .error-icon{width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#ef4444,#dc2626);margin:0 auto 1.5rem;display:flex;align-items:center;justify-content:center;color:#fff;font-size:2rem}
            h1{margin:0 0 1rem;font-size:1.5rem;font-weight:600}
            p{margin:0 0 1.5rem;color:#6b7280;line-height:1.6}
            .actions{display:flex;gap:0.75rem;justify-content:center;flex-wrap:wrap}
            .btn{display:inline-block;background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;padding:0.75rem 1.5rem;border-radius:10px;text-decoration:none;font-weight:600;transition:all 0.2s ease}
            .btn:hover{filter:brightness(1.05);transform:translateY(-1px)}
            .btn-outline{background:#fff;color:#667eea;border:2px solid #667eea}
        </style>
    </head>
    <body>
        <div class='error-card' role='alert' aria-live='assertive'>
            <div class='error-icon' aria-hidden='true'>⚠</div>
            <h1>Registration Failed</h1>
            <p>" . htmlspecialchars($message) . "</p>
            <div class='actions'>
                <a href='register.php' class='btn' aria-label='Back to registration'>← Try Again</a>
                <a href='login.php' class='btn-outline' aria-label='Go to login'>Sign In</a>
            </div>
        </div>
    </body>
    </html>";
}
?>

