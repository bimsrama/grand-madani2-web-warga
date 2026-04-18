<?php
/**
 * keygen.php — Generate APP_KEY untuk Laravel tanpa butuh Laravel aktif
 * Upload ke: public_html/keygen.php
 * Akses: https://grandmadani2.xyz/keygen.php
 * HAPUS setelah selesai!
 */

// Path ke .env (satu level di atas public_html, di folder portal)
$envPath = dirname(__DIR__) . '/portal/.env';

// Generate APP_KEY
$key = 'base64:' . base64_encode(random_bytes(32));

$updated = false;
$message = '';

if (!file_exists($envPath)) {
    $message = "❌ File .env tidak ditemukan di: {$envPath}";
} else {
    $content = file_get_contents($envPath);

    if (strpos($content, 'APP_KEY=') !== false) {
        // Ganti nilai APP_KEY yang ada
        $content = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $key, $content);
    } else {
        // Tambahkan APP_KEY di awal file
        $content = 'APP_KEY=' . $key . "\n" . $content;
    }

    file_put_contents($envPath, $content);
    $updated = true;
    $message = "✅ APP_KEY berhasil ditulis ke .env";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Keygen – Grand Madani 2</title>
<style>
    body { font-family: monospace; background: #0f172a; color: #e2e8f0; padding: 32px; }
    .ok  { color: #4ade80; font-size: 1.1rem; }
    .err { color: #f87171; font-size: 1.1rem; }
    pre  { background: #1e293b; padding: 16px; border-radius: 8px; margin-top: 12px; overflow-x: auto; }
    .btn { display:inline-block; margin-top:20px; padding:10px 24px; background:#16a34a; color:white; border-radius:8px; text-decoration:none; font-weight:bold; }
    .warn { background:#7f1d1d; border:1px solid #ef4444; padding:12px 16px; border-radius:8px; margin-top:20px; color:#fca5a5; }
</style>
</head>
<body>
<h2>🔑 Laravel App Key Generator</h2>

<p class="<?= $updated ? 'ok' : 'err' ?>"><?= $message ?></p>

<?php if ($updated): ?>
<pre>APP_KEY=<?= htmlspecialchars($key) ?></pre>
<a href="/setup.php?key=SETUP_GM2_2025" class="btn">▶ Lanjut ke Setup.php →</a>
<div class="warn">⚠️ Hapus file <code>keygen.php</code> ini segera dari public_html setelah selesai!</div>
<?php else: ?>
<p>Pastikan folder <code>portal/</code> ada di <code>/home/grand349/</code> dan file .env sudah ada di dalamnya.</p>
<?php endif; ?>

</body>
</html>
