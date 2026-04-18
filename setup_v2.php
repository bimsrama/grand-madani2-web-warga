<?php
/**
 * ============================================================
 *  Grand Madani RT 03 — Setup Script V3 (RT 03 Edition)
 *  Jalankan ini setelah upload project ke cPanel.
 *
 *  UPLOAD ke : public_html/setup_v2.php
 *  AKSES via : https://yourdomain.com/setup_v2.php?key=SETUP_RT03_2025
 *  HAPUS     : Segera hapus file ini setelah setup selesai!
 * ============================================================
 */

$SECRET = 'SETUP_RT03_2025';
if (!isset($_GET['key']) || $_GET['key'] !== $SECRET) {
    http_response_code(403);
    die('<h2 style="color:red">403 – Akses Ditolak.<br>Tambahkan <code>?key=SETUP_RT03_2025</code> di URL.</h2>');
}

// Path ke folder project (satu level DI ATAS public_html)
// Jika folder project bernama "portal", biarkan seperti ini.
// Jika beda nama, ubah 'portal' menjadi nama foldernya.
$projectPath = dirname(__DIR__) . '/portal';

if (!is_dir($projectPath)) {
    die("<h2 style='color:red'>❌ Folder project tidak ditemukan di: {$projectPath}</h2>
         <p>Pastikan folder project sudah diupload ke root hosting (sejajar public_html) dan nama foldernya benar.</p>
         <p><strong>Struktur yang benar:</strong><br>
         <code>/home/username/portal/</code> (folder project Laravel)<br>
         <code>/home/username/public_html/</code> (web root)</p>");
}

define('LARAVEL_START', microtime(true));
require $projectPath . '/vendor/autoload.php';
$app = require_once $projectPath . '/bootstrap/app.php';

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

function runCmd($command, $params = []) {
    try {
        echo "<li>⏳ Running: <strong>php artisan {$command}</strong> ... ";
        $exitCode = Artisan::call($command, $params);
        $output   = trim(Artisan::output());
        if ($exitCode === 0) {
            echo "<span style='color:#4ade80;font-weight:bold'>✅ BERHASIL</span>";
        } else {
            echo "<span style='color:#f87171;font-weight:bold'>❌ GAGAL (code: {$exitCode})</span>";
        }
        if ($output) {
            echo "<pre style='background:#0f172a;color:#86efac;padding:8px;border-radius:4px;font-size:11px;margin-top:4px;white-space:pre-wrap'>"
               . htmlspecialchars($output) . "</pre>";
        }
        echo "</li>";
        return $exitCode === 0;
    } catch (\Exception $e) {
        echo "<span style='color:#f87171'>❌ ERROR: " . htmlspecialchars($e->getMessage()) . "</span></li>";
        return false;
    }
}

// Server checks
$checks = [
    ['label' => 'PHP versi 8.2+',    'ok' => version_compare(PHP_VERSION, '8.2', '>=')],
    ['label' => 'Folder project ada', 'ok' => is_dir($projectPath)],
    ['label' => 'File artisan ada',   'ok' => file_exists($projectPath . '/artisan')],
    ['label' => 'File .env ada',      'ok' => file_exists($projectPath . '/.env')],
    ['label' => 'Folder vendor/ ada', 'ok' => is_dir($projectPath . '/vendor')],
    ['label' => 'storage/ writable',  'ok' => is_writable($projectPath . '/storage')],
    ['label' => 'bootstrap/cache/ writable', 'ok' => is_writable($projectPath . '/bootstrap/cache')],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Setup RT 03 – Grand Madani</title>
<style>
    *{box-sizing:border-box}
    body{font-family:'Segoe UI',monospace;background:#0f172a;color:#e2e8f0;padding:28px;margin:0;font-size:14px}
    h1{color:#4ade80;font-size:1.5rem;margin-bottom:4px}
    .warn{background:#7f1d1d;border:1px solid #ef4444;color:#fca5a5;padding:14px 18px;border-radius:10px;margin-bottom:22px}
    .card{background:#1e293b;border-radius:12px;padding:22px;margin-bottom:18px;border:1px solid #334155}
    h3{color:#facc15;margin:0 0 14px}
    .step{display:flex;align-items:center;gap:8px;margin-bottom:6px;font-size:13px}
    ul{padding-left:16px;margin:0}
    li{margin-bottom:10px;list-style:none}
    pre{background:#0f172a;padding:10px;border-radius:6px;overflow-x:auto;font-size:11px;color:#86efac;white-space:pre-wrap;margin:4px 0 0}
    .btn{background:#16a34a;color:#fff;border:none;padding:12px 30px;border-radius:8px;font-size:1rem;cursor:pointer;font-weight:bold;width:100%}
    .btn:hover{background:#15803d}
    .info{color:#94a3b8;font-size:12px;margin-top:6px}
    code{background:#0f172a;padding:2px 6px;border-radius:4px;color:#4ade80}
    a{color:#4ade80}
    .badge-ok{color:#4ade80} .badge-fail{color:#f87171}
</style>
</head>
<body>

<h1>🏡 Grand Madani RT 03 — Setup Script V3</h1>
<p class="info">PHP: <?= PHP_VERSION ?> | Project path: <code><?= $projectPath ?></code></p>

<div class="warn">
    ⚠️ <strong>PENTING:</strong> Hapus file <code>setup_v2.php</code> dari <code>public_html/</code>
    segera setelah setup berhasil. Jangan biarkan aktif di production!
</div>

<!-- SERVER CHECKS -->
<div class="card">
    <h3>🔍 Cek Kondisi Server</h3>
    <?php foreach ($checks as $c): ?>
    <div class="step">
        <span><?= $c['ok'] ? '✅' : '❌' ?></span>
        <span class="<?= $c['ok'] ? 'badge-ok' : 'badge-fail' ?>"><?= $c['label'] ?></span>
    </div>
    <?php endforeach; ?>
</div>

<!-- STEP 1: Clear Cache -->
<div class="card">
    <h3>Langkah 1 — Bersihkan Cache</h3>
    <p class="info" style="margin-bottom:12px">Wajib dijalankan jika baru mengedit file <code>.env</code></p>
    <ul>
        <?php
        runCmd('config:clear');
        runCmd('cache:clear');
        runCmd('view:clear');
        runCmd('route:clear');
        ?>
    </ul>
</div>

<!-- STEP 2: Database Migrations -->
<div class="card">
    <h3>Langkah 2 — Migrasi Database (RT 03)</h3>
    <p class="info" style="margin-bottom:12px">Menjalankan semua migrasi termasuk tabel baru RT 03</p>
    <ul>
        <?php
        runCmd('migrate', ['--force' => true]);
        ?>
    </ul>
    <p class="info" style="margin-top:10px">
        ⚠️ Jika tabel sudah ada sebelumnya dan ingin reset (HAPUS SEMUA DATA), gunakan command custom di bawah:
        <code>migrate:fresh --seed --force</code>
    </p>
</div>

<!-- STEP 3: Storage & Cache -->
<div class="card">
    <h3>Langkah 3 — Storage Link & Cache</h3>
    <ul>
        <?php
        runCmd('storage:link');
        runCmd('config:cache');
        runCmd('route:cache');
        ?>
    </ul>
</div>

<!-- STEP 4: Seed Admin -->
<div class="card">
    <h3>Langkah 4 — Buat Akun Admin RT 03</h3>
    <p class="info" style="margin-bottom:12px">
        Membuat akun admin dan data sample RT 03.
    </p>
    <ul>
        <?php
        runCmd('db:seed', ['--force' => true]);
        ?>
    </ul>
    <div style="margin-top:14px;background:#0f172a;border:1px solid #4ade80;border-radius:10px;padding:14px;">
        <p style="color:#4ade80;font-weight:bold;margin:0 0 6px">✅ Kredensial Admin RT 03:</p>
        <p style="margin:2px 0;font-size:13px">📧 <strong>Email &nbsp;&nbsp;:</strong> <code style="color:#facc15">admin@rt03.com</code></p>
        <p style="margin:2px 0;font-size:13px">🔑 <strong>Password:</strong> <code style="color:#facc15">GrandMadani2025!</code></p>
        <p style="margin:8px 0 0;font-size:11px;color:#94a3b8">⚠️ Segera ganti password setelah login pertama kali!</p>
    </div>
</div>

<hr style="border-color:#334155;margin:22px 0">

<!-- CUSTOM COMMAND -->
<div class="card">
    <h3>🛠️ Command Custom (Artisan)</h3>
    <form method="POST" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
        <input type="hidden" name="key_verify" value="1">
        <span style="color:#94a3b8">php artisan</span>
        <input type="text" name="custom_cmd" placeholder="migrate:fresh --seed --force"
               style="background:#0f172a;border:1px solid #334155;color:#e2e8f0;padding:8px 12px;border-radius:6px;width:280px;font-family:monospace">
        <button type="submit" style="background:#1d4ed8;color:#fff;border:none;padding:9px 20px;border-radius:6px;cursor:pointer;font-size:13px">
            ▶ Run
        </button>
    </form>
    <p class="info" style="margin-top:8px">
        Contoh berguna: <code>optimize:clear</code> | <code>migrate:status</code> | <code>queue:restart</code>
    </p>
    <?php
    if (isset($_POST['key_verify']) && !empty($_POST['custom_cmd'])) {
        echo "<ul style='margin-top:12px'>";
        runCmd(htmlspecialchars($_POST['custom_cmd']));
        echo "</ul>";
    }
    ?>
</div>

<!-- DONE -->
<div class="card" style="border-color:#4ade80">
    <p style="color:#4ade80;font-weight:bold;margin:0">
        ✅ Jika semua langkah BERHASIL →
        <a href="/" style="color:#4ade80;text-decoration:underline">Buka Website RT 03</a>
    </p>
    <p class="info" style="margin-top:8px">
        Setelah selesai, <strong style="color:#f87171">WAJIB hapus file ini</strong> via cPanel File Manager!
    </p>
</div>

</body>
</html>
