<?php
/**
 * SiswaMart Deploy Helper (Untuk Hosting DirectAdmin / cPanel tanpa Terminal)
 */

$secretKey = 'siswamart2026';

if (($_GET['key'] ?? '') !== $secretKey) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    die('Akses ditolak: Kunci pengaman tidak valid. Gunakan ?key=siswamart2026');
}

define('LARAVEL_START', microtime(true));

$candidatePaths = [
    dirname(__DIR__) . '/siswamart',
    dirname(__DIR__) . '/siswamartid',
    dirname(dirname(__DIR__)) . '/siswamart',
    dirname(dirname(__DIR__)) . '/siswamartid',
    dirname(__DIR__) . '/core',
    dirname(__DIR__) . '/laravel',
    dirname(__DIR__),
];

$applicationPath = null;
foreach ($candidatePaths as $candidate) {
    if (is_file($candidate . '/vendor/autoload.php') && file_exists($candidate . '/bootstrap/app.php')) {
        $applicationPath = $candidate;
        break;
    }
}

if (! $applicationPath) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    die("Folder core Laravel tidak ditemukan.\nJalur yang diperiksa:\n- " . implode("\n- ", $candidatePaths) . "\n\nPastikan folder core bernama 'siswamart' atau 'siswamartid' diletakkan sejajar dengan public_html.");
}

require $applicationPath.'/vendor/autoload.php';
$app = require_once $applicationPath.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

header('Content-Type: text/html; charset=utf-8');
echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>SiswaMart Deploy Helper</title><style>body{font-family:monospace;background:#18181b;color:#4ade80;padding:28px;line-height:1.6}h2{color:#38bdf8}h3{color:#f59e0b;margin-top:20px}pre{background:#09090b;padding:16px;border-radius:8px;border:1px solid #27272a;overflow-x:auto;color:#f4f4f5}</style></head><body>';
echo '<h2>🚀 SiswaMart Hosting Setup & Migration Helper</h2>';
echo '<p style="color:#a1a1aa">Script ini menjalankan migration dan konfigurasi Laravel otomatis pada hosting tanpa terminal.</p>';

// 1. Migrate Database
echo '<h3>1. Menjalankan Database Migration (migrate --force)...</h3>';
echo '<pre>';
try {
    $kernel->call('migrate', ['--force' => true]);
    echo htmlspecialchars($kernel->output() ?: 'Tidak ada migration baru (skema sudah up-to-date).');
} catch (\Throwable $e) {
    echo 'Peringatan / Error: ' . htmlspecialchars($e->getMessage());
}
echo '</pre>';

// 2. Storage Link
echo '<h3>2. Menghubungkan Storage (storage:link)...</h3>';
echo '<pre>';
try {
    $target = $applicationPath . '/storage/app/public';
    $link = __DIR__ . '/storage';
    if (! file_exists($link)) {
        if (@symlink($target, $link)) {
            echo "Storage symlink berhasil dibuat: $link -> $target\n";
        } else {
            $kernel->call('storage:link');
            echo htmlspecialchars($kernel->output());
        }
    } else {
        echo "Storage symlink sudah terpasang.\n";
    }
} catch (\Throwable $e) {
    echo 'Info: ' . htmlspecialchars($e->getMessage());
}
echo '</pre>';

// 3. Clear Caches
echo '<h3>3. Membersihkan dan Memperbarui Cache...</h3>';
echo '<pre>';
try {
    $kernel->call('config:clear');
    echo htmlspecialchars($kernel->output());
    $kernel->call('route:clear');
    echo htmlspecialchars($kernel->output());
    $kernel->call('view:clear');
    echo htmlspecialchars($kernel->output());
    echo "Seluruh cache berhasil dibersihkan.\n";
} catch (\Throwable $e) {
    echo 'Error: ' . htmlspecialchars($e->getMessage());
}
echo '</pre>';

echo '<hr style="border:0;border-top:1px solid #3f3f46;margin:28px 0">';
echo '<div style="background:#451a03;border:1px solid #f59e0b;color:#fef3c7;padding:16px;border-radius:8px">';
echo '⚠️ <strong>Penting Demi Keamanan:</strong> Setelah proses di atas berhasil, silakan <strong>hapus file <code>deploy-helper.php</code></strong> dari File Manager hosting Anda!';
echo '</div>';
echo '</body></html>';
