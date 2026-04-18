<?php
/**
 * check.php — Diagnosa masalah server tanpa perlu Laravel aktif
 * Upload ke: public_html/check.php
 * Akses: https://grandmadani2.xyz/check.php
 * HAPUS setelah selesai!
 */
$portal = dirname(__DIR__) . '/portal';
$env    = $portal . '/.env';

// Baca APP_KEY dari .env
$appKey = '';
if (file_exists($env)) {
    preg_match('/^APP_KEY=(.*)$/m', file_get_contents($env), $m);
    $appKey = trim($m[1] ?? '');
}

$checks = [
    ['PHP ≥ 8.2',             version_compare(PHP_VERSION, '8.2.0', '>=')],
    ['shell_exec aktif',      function_exists('shell_exec') && !in_array('shell_exec', array_map('trim', explode(',', ini_get('disable_functions'))))],
    ['Folder portal/ ada',    is_dir($portal)],
    ['File artisan ada',      file_exists($portal . '/artisan')],
    ['Folder vendor/ ada',    is_dir($portal . '/vendor')],
    ['File .env ada',         file_exists($env)],
    ['APP_KEY diisi',         !empty($appKey) && $appKey !== 'base64:'],
    ['storage/ writable',     is_writable($portal . '/storage')],
    ['bootstrap/cache/ writable', is_writable($portal . '/bootstrap/cache')],
    ['ext pdo_mysql',         extension_loaded('pdo_mysql')],
    ['ext mbstring',          extension_loaded('mbstring')],
    ['ext openssl',           extension_loaded('openssl')],
    ['ext gd / imagick',      extension_loaded('gd') || extension_loaded('imagick')],
    ['ext fileinfo',          extension_loaded('fileinfo')],
    ['ext zip',               extension_loaded('zip')],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Diagnosa – Grand Madani 2</title>
<style>
    body { font-family: monospace; background:#0f172a; color:#e2e8f0; padding:32px; }
    h2   { color:#4ade80; }
    .row { display:flex; gap:12px; align-items:center; padding:6px 0; border-bottom:1px solid #1e293b; }
    .ok  { color:#4ade80; }
    .err { color:#f87171; }
    .info{ color:#94a3b8; font-size:0.85rem; }
    pre  { background:#1e293b; padding:16px; border-radius:8px; overflow-x:auto; font-size:0.8rem; }
    .btn { display:inline-block; margin-top:16px; padding:10px 22px; background:#16a34a; color:white; border-radius:8px; text-decoration:none; font-weight:bold; }
    .warn{ background:#7f1d1d; border:1px solid #ef4444; padding:12px; border-radius:8px; margin-top:20px; color:#fca5a5; }
</style>
</head>
<body>
<h2>🔍 Server Diagnostic – Grand Madani 2</h2>
<p class="info">PHP: <?= PHP_VERSION ?> | Server: <?= $_SERVER['SERVER_NAME'] ?? '-' ?></p>

<?php foreach ($checks as [$label, $ok]): ?>
<div class="row">
    <span class="<?= $ok ? 'ok' : 'err' ?>"><?= $ok ? '✅' : '❌' ?></span>
    <span style="color:<?= $ok ? '#e2e8f0' : '#f87171' ?>"><?= $label ?></span>
</div>
<?php endforeach; ?>

<br>
<p class="info">APP_KEY: <code><?= $appKey ? substr($appKey, 0, 20) . '...' : '⚠️ KOSONG' ?></code></p>
<p class="info">Portal path: <code><?= $portal ?></code></p>

<?php
// Baca isi .env (sensor password)
if (file_exists($env)) {
    $envContent = file_get_contents($env);
    $envContent = preg_replace('/(DB_PASSWORD|APP_KEY|FONNTE_TOKEN)=(.+)/m', '$1=*****', $envContent);
    echo '<br><p class="info">Isi .env (password disembunyikan):</p><pre>' . htmlspecialchars($envContent) . '</pre>';
}
?>

<?php if (empty($appKey)): ?>
<a href="/keygen.php" class="btn">🔑 Generate APP_KEY dulu →</a>
<?php else: ?>
<a href="/setup.php?key=SETUP_GM2_2025" class="btn">▶ Jalankan Setup →</a>
<?php endif; ?>

<div class="warn">⚠️ Hapus file <code>check.php</code> dan <code>keygen.php</code> setelah selesai!</div>
</body>
</html>
