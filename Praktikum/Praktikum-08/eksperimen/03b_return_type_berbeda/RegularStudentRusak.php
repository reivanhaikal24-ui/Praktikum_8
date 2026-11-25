<?php
declare(strict_types=1);

// SENGAJA SALAH (latihan 3b): parent: calculateFee(): float
// child mengubah return type menjadi string -> signature tidak kompatibel.
final class RegularStudentRusak extends Student {
    public function getType(): string { return 'regular'; }
    public function calculateFee(): string { return 'gratis'; }
}
