<?php
declare(strict_types=1);
/**
 * Menjalankan tiap eksperimen di proses PHP terpisah (karena Fatal error tidak bisa di-catch)
 * dan menyimpan outputnya ke eksperimen/hasil_error.txt.
 *
 * Cara pakai (dari folder proyek):  php eksperimen/jalankan.php
 */
$cases = [
    '03a_parameter_wajib_tambahan' => 'Latihan 3a - calculateFee() ditambah parameter wajib',
    '03b_return_type_berbeda'      => 'Latihan 3b - calculateFee() return type diganti string',
    '04_override_final_getId'      => 'Latihan 4  - override method final getId()',
];

$report = 'Dibuat : ' . date('Y-m-d H:i:s') . "\n"
        . 'PHP    : ' . PHP_VERSION . "\n"
        . 'OS     : ' . PHP_OS_FAMILY . "\n";

foreach ($cases as $dir => $title) {
    $script = __DIR__ . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . 'run.php';
    $cmd = [PHP_BINARY, '-d', 'display_errors=1', '-d', 'html_errors=0', '-d', 'log_errors=0', '-d', 'error_reporting=-1', $script];
    $proc = proc_open($cmd, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);

    $out = '(gagal menjalankan proses PHP)';
    if (is_resource($proc)) {
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $code = proc_close($proc);
        $out = trim($stdout . $stderr) . "\n(exit code: $code)";
    }

    $report .= "\n==== $title ====\n$out\n";
}

file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'hasil_error.txt', $report);
echo $report;
echo "\nTersimpan di eksperimen/hasil_error.txt\n";
