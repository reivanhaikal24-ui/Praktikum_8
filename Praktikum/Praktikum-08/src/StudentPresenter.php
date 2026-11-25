<?php
declare(strict_types=1);

/**
 * PHP overloading: __call() menangani method yang TIDAK dideklarasikan.
 *
 * Pola showData() yang didukung (semua dialihkan ke __call):
 *   showData(nama)
 *   showData(nim, nama)
 *   showData(nim, nama, prodi)
 *   showData(nim, nama, prodi, semester)   <- latihan 2
 */
final class StudentPresenter {
    private const MIN_SEMESTER = 1;
    private const MAX_SEMESTER = 14;

    public function __call(string $method, array $args): string {
        if ($method !== 'showData') {
            throw new BadMethodCallException("Method tidak dikenal: $method");
        }

        $count = count($args);
        if ($count < 1 || $count > 4) {
            throw new InvalidArgumentException(
                'showData menerima 1 sampai 4 argumen: (nama), (nim, nama), (nim, nama, prodi), atau (nim, nama, prodi, semester).'
            );
        }

        // Tiga argumen pertama (nim/nama/prodi) wajib string tidak kosong.
        $texts = [];
        $limit = min($count, 3);
        for ($i = 0; $i < $limit; $i++) {
            if (!is_string($args[$i]) || trim($args[$i]) === '') {
                throw new InvalidArgumentException('Argumen ke-' . ($i + 1) . ' harus berupa string tidak kosong.');
            }
            $texts[] = trim($args[$i]);
        }

        if ($count === 1) {
            return 'Nama: ' . $texts[0];
        }
        if ($count === 2) {
            return 'NIM: ' . $texts[0] . ', Nama: ' . $texts[1];
        }
        if ($count === 3) {
            return 'NIM: ' . $texts[0] . ', Nama: ' . $texts[1] . ', Prodi: ' . $texts[2];
        }

        // $count === 4 : argumen keempat adalah semester.
        $semester = $this->validateSemester($args[3]);
        return 'NIM: ' . $texts[0] . ', Nama: ' . $texts[1] . ', Prodi: ' . $texts[2] . ', Semester: ' . $semester;
    }

    /** Semester boleh int atau string angka bulat (mis. 5 atau "5"), rentang 1-14. */
    private function validateSemester(mixed $value): int {
        if (is_int($value)) {
            $semester = $value;
        } elseif (is_string($value) && preg_match('/^[0-9]{1,2}$/', trim($value)) === 1) {
            $semester = (int) trim($value);
        } else {
            throw new InvalidArgumentException('Semester harus berupa bilangan bulat (int atau string angka).');
        }

        if ($semester < self::MIN_SEMESTER || $semester > self::MAX_SEMESTER) {
            throw new InvalidArgumentException(
                'Semester harus antara ' . self::MIN_SEMESTER . ' sampai ' . self::MAX_SEMESTER . '.'
            );
        }
        return $semester;
    }
}
