<?php
declare(strict_types=1);

// SENGAJA SALAH (latihan 4): Student::getId() bertanda final,
// jadi subclass tidak boleh mendefinisikannya ulang.
final class RegularStudentRusak extends Student {
    public function getType(): string { return 'regular'; }
    public function calculateFee(): float { return $this->baseFee; }
    public function getId(): string { return 'HACKED'; }
}
