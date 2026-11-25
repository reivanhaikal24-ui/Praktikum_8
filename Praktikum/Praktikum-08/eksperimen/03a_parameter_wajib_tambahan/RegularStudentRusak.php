<?php
declare(strict_types=1);

// SENGAJA SALAH (latihan 3a): parent: calculateFee(): float
// child menambah parameter WAJIB -> signature tidak kompatibel.
final class RegularStudentRusak extends Student {
    public function getType(): string { return 'regular'; }
    public function calculateFee(float $diskon): float { return $this->baseFee - $diskon; }
}
