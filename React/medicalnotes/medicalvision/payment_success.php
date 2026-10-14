<?php
session_start();
include('database.php');

$valid = isset($_GET['credits']) && isset($_SESSION['member_id']);
$credits = $valid ? intval($_GET['credits']) : 0;

if ($valid) {
    $userId = $_SESSION['member_id'];
    $stmt = $conn->prepare("UPDATE members SET credits = credits + ? WHERE member_id = ?");
    $stmt->bind_param("ii", $credits, $userId);
    $stmt->execute();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo $valid ? 'Payment Successful' : 'Invalid Request'; ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
  <style>
    :root{--ok:#22c55e;--ink:#0f172a;--muted:#475569;--bg:#f8fafc;--card:#ffffff;--border:#e2e8f0;--primary:#667eea}
    *{box-sizing:border-box}
    body{margin:0;font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,sans-serif;background:var(--bg);color:var(--ink)}
    .wrap{max-width:720px;margin:6rem auto;padding:0 1rem}
    .card{background:var(--card);border:1px solid var(--border);border-radius:16px;box-shadow:0 10px 30px rgba(0,0,0,.08);overflow:hidden}
    .head{display:flex;align-items:center;gap:.75rem;padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);
      background:linear-gradient(135deg,#f8fafc,#eef2ff)}
    .icon{width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;
      background:linear-gradient(135deg,#22c55e,#16a34a)}
    .icon i{font-style:normal;font-weight:800}
    h1{margin:0;font-size:1.25rem}
    .body{padding:1.5rem}
    p{margin:.25rem 0;color:var(--muted)}
    .callout{margin-top:.75rem;padding:1rem;border:1px solid var(--border);border-radius:12px;background:#f8fafc}
    .actions{display:flex;gap:.75rem;flex-wrap:wrap;margin-top:1rem}
    .btn{display:inline-block;text-decoration:none;font-weight:600;border-radius:10px;padding:.75rem 1rem}
    .btn-primary{background:var(--primary);color:#fff}
    .btn-outline{border:2px solid var(--primary);color:var(--primary);background:#fff}
    .btn:hover{filter:brightness(1.05)}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card" role="status" aria-live="polite">
      <div class="head">
        <div class="icon" aria-hidden="true">✅</div>
        <h1><?php echo $valid ? 'Payment Successful' : 'Invalid Request'; ?></h1>
      </div>
      <div class="body">
        <?php if ($valid): ?>
          <p>Your account has been credited with <strong><?php echo htmlspecialchars((string)$credits); ?> credits</strong>.</p>
          <div class="callout">
            <p>Return to your dashboard to continue processing documents.</p>
          </div>
          <div class="actions">
            <a class="btn btn-primary" href="index.php" aria-label="Return to dashboard">Return to Dashboard</a>
            <a class="btn btn-outline" href="buy_credits.php" aria-label="Buy more credits">Buy More Credits</a>
          </div>
        <?php else: ?>
          <p>We could not validate this request. Please return to the dashboard.</p>
          <div class="actions">
            <a class="btn btn-primary" href="index.php">Back to Dashboard</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>