<?php
// Simple visit counter app - demonstrates DB connectivity and data persistence
$host = getenv('DB_HOST') ?: 'db';
$user = getenv('DB_USER') ?: 'appuser';
$pass = getenv('DB_PASSWORD') ?: 'apppassword';
$db   = getenv('DB_NAME') ?: 'homeworkdb';

$appVersion = getenv('APP_VERSION') ?: 'v2';

$connected = false;
$count = null;
$errorMsg = null;
$hostname = gethostname();

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $pdo->exec("CREATE TABLE IF NOT EXISTS visits (
        id INT AUTO_INCREMENT PRIMARY KEY,
        visited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("INSERT INTO visits () VALUES ()");
    $count = $pdo->query("SELECT COUNT(*) FROM visits")->fetchColumn();
    $connected = true;

} catch (PDOException $e) {
    $errorMsg = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Docker + Swarm Homework App</title>
<style>
  :root {
    --bg: #0f172a;
    --card: #ffffff;
    --accent: #2563eb;
    --accent-light: #dbeafe;
    --text: #1e293b;
    --muted: #64748b;
    --ok: #16a34a;
    --err: #dc2626;
  }
  * { box-sizing: border-box; }
  body {
    margin: 0;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
    padding: 24px;
  }
  .card {
    background: var(--card);
    border-radius: 16px;
    padding: 40px 48px;
    max-width: 480px;
    width: 100%;
    box-shadow: 0 20px 50px rgba(0,0,0,0.35);
    text-align: center;
  }
  .badge {
    display: inline-block;
    background: var(--accent-light);
    color: var(--accent);
    font-weight: 600;
    font-size: 13px;
    letter-spacing: 0.03em;
    padding: 4px 12px;
    border-radius: 999px;
    margin-bottom: 16px;
    text-transform: uppercase;
  }
  h1 {
    font-size: 26px;
    margin: 0 0 4px;
    color: var(--text);
  }
  .subtitle {
    color: var(--muted);
    font-size: 14px;
    margin-bottom: 28px;
  }
  .status {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 24px;
  }
  .dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
  }
  .status.ok { color: var(--ok); }
  .status.ok .dot { background: var(--ok); }
  .status.err { color: var(--err); }
  .status.err .dot { background: var(--err); }
  .counter {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
  }
  .counter .number {
    font-size: 40px;
    font-weight: 700;
    color: var(--accent);
    line-height: 1;
  }
  .counter .label {
    font-size: 13px;
    color: var(--muted);
    margin-top: 6px;
  }
  .meta {
    font-size: 12px;
    color: var(--muted);
    font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
  }
  .error-box {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: var(--err);
    border-radius: 8px;
    padding: 12px;
    font-size: 13px;
    text-align: left;
    word-break: break-word;
  }
</style>
</head>
<body>
  <div class="card">
    <span class="badge">Version <?= htmlspecialchars($appVersion) ?></span>
    <h1>Docker + Swarm Homework App</h1>
    <div class="subtitle">PHP &middot; MySQL &middot; Docker &middot; Swarm</div>

    <?php if ($connected): ?>
      <div class="status ok"><span class="dot"></span> Connected to MySQL</div>
      <div class="counter">
        <div class="number"><?= htmlspecialchars($count) ?></div>
        <div class="label">total visits recorded</div>
      </div>
      <div class="meta">served by container <?= htmlspecialchars($hostname) ?></div>
    <?php else: ?>
      <div class="status err"><span class="dot"></span> Database connection failed</div>
      <div class="error-box"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>
  </div>
</body>
</html>
