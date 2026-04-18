<?php
/**
 * ============================================================
 *  Grand Madani 2 — Setup Script (TANPA SSH/Terminal)
 *  UPLOAD file ini ke: public_html/setup.php
 *  Akses via browser: https://grandmadani2.xyz/setup.php
 *  HAPUS FILE INI SEGERA SETELAH SELESAI!
 * ============================================================
 */

// ── Keamanan: hanya bisa diakses dengan secret key ──────────
$SECRET = 'SETUP_GM2_2025';  // Ganti ini sebelum upload!
if (!isset($_GET['key']) || $_GET['key'] !== $SECRET) {
    http_response_code(403);
    die('<h2 style="color:red">403 – Akses Ditolak. Tambahkan ?key=SETUP_GM2_2025 di URL</h2>');
}

// ── Path ke folder project (satu level di atas public_html) ─
// Sesuaikan nama folder "portal" jika berbeda
$projectPath = dirname(__DIR__) . '/portal';

if (!is_dir($projectPath)) {
    die("<h2 style='color:red'>❌ Folder project tidak ditemukan di: {$projectPath}</h2>
         <p>Pastikan folder project sudah diupload dan nama foldernya benar.</p>");
}

$phpBin   = PHP_BINARY ?: 'php';
$artisan  = $projectPath . '/artisan';
$results  = [];

function runArtisan(string $phpBin, string $artisan, string $command): array
{
    $fullCmd = escapeshellarg($phpBin) . ' ' . escapeshellarg($artisan) . ' ' . $command . ' 2>&1';
    $output  = shell_exec($fullCmd);
    return ['cmd' => "php artisan {$command}", 'output' => trim($output ?: '(no output)')];
}

// ── Jalankan semua command ───────────────────────────────────
if (isset($_POST['run'])) {
    $commands = [
        'key:generate --force',
        'migrate --force',
        'db:seed --force',
        'storage:link',
        'config:cache',
        'route:cache',
        'view:cache',
    ];
    foreach ($commands as $cmd) {
        $results[] = runArtisan($phpBin, $artisan, $cmd);
    }
}

// ── Jalankan 1 command custom ────────────────────────────────
if (isset($_POST['custom_cmd']) && !empty($_POST['custom_cmd'])) {
    $results[] = runArtisan($phpBin, $artisan, htmlspecialchars($_POST['custom_cmd']));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Setup – Grand Madani 2</title>
<style>
    body { font-family: monospace; background: #0f172a; color: #e2e8f0; padding: 24px; margin: 0; }
    h1   { color: #4ade80; font-size: 1.4rem; margin-bottom: 4px; }
    .warn { background: #7f1d1d; border: 1px solid #ef4444; color: #fca5a5; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; }
    .card { background: #1e293b; border-radius: 10px; padding: 20px; margin-bottom: 16px; }
    .cmd { color: #facc15; font-weight: bold; margin-bottom: 8px; }
    pre  { background: #0f172a; padding: 12px; border-radius: 6px; overflow-x: auto; font-size: 0.85rem; color: #86efac; white-space: pre-wrap; }
    .btn { background: #16a34a; color: white; border: none; padding: 12px 28px; border-radius: 8px; font-size: 1rem; cursor: pointer; font-weight: bold; }
    .btn:hover { background: #15803d; }
    .btn-sm { background: #1d4ed8; font-size: 0.85rem; padding: 8px 16px; border-radius: 6px; border: none; color: white; cursor: pointer; }
    input[type=text] { background: #0f172a; border: 1px solid #334155; color: #e2e8f0; padding: 8px 12px; border-radius: 6px; width: 300px; font-family: monospace; }
    .info { color: #94a3b8; font-size: 0.85rem; margin-top: 6px; }
    .ok   { color: #4ade80; }
    .step { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
</style>
</head>
<body>

<h1>🏡 Grand Madani 2 – Setup Script</h1>
<p class="info">PHP: <?= PHP_VERSION ?> | Project: <?= $projectPath ?></p>

<div class="warn">
    ⚠️ <strong>PENTING:</strong> Hapus file <code>setup.php</code> dari <code>public_html/</code> segera setelah setup selesai!
    Jangan biarkan file ini aktif di production.
</div>

<?php if (!empty($results)): ?>
<div class="card">
    <h2 class="ok">✅ Hasil Eksekusi:</h2>
    <?php foreach ($results as $r): ?>
    <div style="margin-bottom: 14px;">
        <div class="cmd">$ <?= htmlspecialchars($r['cmd']) ?></div>
        <pre><?= htmlspecialchars($r['output']) ?></pre>
    </div>
    <?php endforeach; ?>
    <hr style="border-color:#334155; margin: 16px 0;">
    <p class="ok">✅ Selesai! Sekarang <strong>hapus file setup.php</strong> dari public_html via File Manager.</p>
    <p class="info">Lalu cek website Anda: <a href="/" style="color:#4ade80">Buka Website →</a></p>
</div>
<?php endif; ?>

<?php if (empty($results)): ?>
<!-- Cek kondisi server -->
<div class="card">
    <h3 style="color:#facc15; margin-bottom: 12px;">🔍 Cek Kondisi Server</h3>
    <?php
    $checks = [
        ['label' => 'PHP versi 8.2+',   'ok' => version_compare(PHP_VERSION, '8.2', '>=')],
        ['label' => 'shell_exec aktif', 'ok' => function_exists('shell_exec')],
        ['label' => 'Folder project ada','ok' => is_dir($projectPath)],
        ['label' => 'File artisan ada',  'ok' => file_exists($artisan)],
        ['label' => 'File .env ada',     'ok' => file_exists($projectPath . '/.env')],
        ['label' => 'Folder vendor/ ada','ok' => is_dir($projectPath . '/vendor')],
        ['label' => 'storage/ writable', 'ok' => is_writable($projectPath . '/storage')],
    ];
    foreach ($checks as $c):
        $icon = $c['ok'] ? '✅' : '❌';
    ?>
    <div class="step">
        <span><?= $icon ?></span>
        <span style="color: <?= $c['ok'] ? '#4ade80' : '#f87171' ?>"><?= $c['label'] ?></span>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Run All Button -->
<div class="card">
    <h3 style="color:#facc15; margin-bottom: 12px;">🚀 Jalankan Setup Lengkap</h3>
    <p class="info" style="margin-bottom: 16px;">Akan menjalankan: key:generate → migrate → db:seed → storage:link → config:cache → route:cache → view:cache</p>
    <form method="POST">
        <input type="hidden" name="run" value="1">
        <button type="submit" class="btn">▶ Jalankan Semua Perintah Setup</button>
    </form>
</div>

<!-- Custom command -->
<div class="card">
    <h3 style="color:#94a3b8; margin-bottom: 12px;">🛠️ Jalankan Command Custom</h3>
    <form method="POST" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <span style="color:#94a3b8;">php artisan</span>
        <input type="text" name="custom_cmd" placeholder="migrate:fresh --seed" required>
        <button type="submit" class="btn-sm">▶ Run</button>
    </form>
    <p class="info" style="margin-top:8px;">Contoh: <code>migrate:fresh --seed</code> | <code>cache:clear</code> | <code>optimize:clear</code></p>
</div>

</body>
</html>
