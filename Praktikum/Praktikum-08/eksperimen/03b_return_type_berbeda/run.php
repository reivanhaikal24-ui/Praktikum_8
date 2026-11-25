<?php
declare(strict_types=1);
// Salinan terpisah: memakai Student.php milik folder ini, BUKAN src/.
require __DIR__ . '/Student.php';
require __DIR__ . '/RegularStudentRusak.php';

// Baris di bawah tidak pernah tercapai karena PHP berhenti dengan Fatal error saat class dideklarasikan.
echo "Tidak ada error (seharusnya tidak sampai di sini)\n";
