# Jawaban Latihan — Praktikum PBO 8

## Latihan 1 — Subclass `InternationalStudent` tanpa mengubah `StudentRepository`

- File: `src/InternationalStudent.php` (didaftarkan di `src/bootstrap.php`).
- `calculateFee()` = `baseFee + internationalFee` (biaya dasar + biaya tambahan mahasiswa internasional), `summary()` memakai `parent::summary()` lalu menambah `[Internasional]`.
- `src/StudentRepository.php` **tidak diubah sama sekali**, karena parameternya bertipe `Student` (lihat latihan 5).
- UI (`index.php`) ditambah pilihan jenis "Internasional" dan kolom "Biaya Tambahan Internasional".
- Contoh: biaya dasar Rp5.000.000 + biaya internasional Rp2.500.000 = **Rp7.500.000**.

## Latihan 2 — `showData(id, name, major, semester)` lewat `__call()`

`StudentPresenter` tetap **tidak** punya method `showData()`; semua pemanggilan masuk ke `__call()`.

| Pemanggilan | Hasil |
|---|---|
| `showData('Alya')` | `Nama: Alya` |
| `showData('M001','Alya')` | `NIM: M001, Nama: Alya` |
| `showData('M001','Alya','Informatika')` | `NIM: M001, Nama: Alya, Prodi: Informatika` |
| `showData('M001','Alya','Informatika',5)` | `NIM: M001, Nama: Alya, Prodi: Informatika, Semester: 5` |

Validasi:
- Jumlah argumen harus 1–4, selain itu `InvalidArgumentException`.
- NIM, nama, prodi harus string dan tidak boleh kosong/spasi saja.
- Semester harus bilangan bulat (int atau string angka seperti `"5"`) dengan rentang 1–14. Ditolak: `0`, `15`, `-3`, `'lima'`, `5.5`, `null`.
- Nama method selain `showData` memunculkan `BadMethodCallException`.

Tes ada di `test/run.php` (bagian "Latihan 2").

## Latihan 3 — Signature `calculateFee()` tidak kompatibel (salinan terpisah)

Folder: `eksperimen/03a_parameter_wajib_tambahan/` dan `eksperimen/03b_return_type_berbeda/`.
Tiap folder punya salinan `Student.php` sendiri, jadi aplikasi utama tidak terpengaruh.

Menjalankan (otomatis menyimpan output asli ke `eksperimen/hasil_error.txt`):

```
php eksperimen/jalankan.php
```

Atau satu per satu: `php eksperimen/03a_parameter_wajib_tambahan/run.php`

Contoh error yang dihasilkan PHP 8.x (path dan nomor baris di komputermu akan berbeda; salin yang asli dari `hasil_error.txt`):

**3a** — child: `calculateFee(float $diskon): float`, parent: `calculateFee(): float`

```
Fatal error: Declaration of RegularStudentRusak::calculateFee(float $diskon): float must be compatible with Student::calculateFee(): float in ...\RegularStudentRusak.php on line 8
```

**3b** — child: `calculateFee(): string`

```
Fatal error: Declaration of RegularStudentRusak::calculateFee(): string must be compatible with Student::calculateFee(): float in ...\RegularStudentRusak.php on line 8
```

Penjelasan: overriding wajib mengikuti prinsip substitusi (LSP). Kode yang memanggil `$student->calculateFee()` lewat tipe `Student` mengharapkan tanpa argumen dan mengembalikan `float`. Jika child mewajibkan parameter tambahan atau mengubah return type menjadi tidak sesuai, jaminan itu rusak, sehingga PHP menolak saat class dideklarasikan. Ini *compile-time Fatal Error* yang tidak bisa ditangkap `try/catch`, makanya dijalankan di proses terpisah.

## Latihan 4 — Override method `final` `getId()` (salinan terpisah)

Folder: `eksperimen/04_override_final_getId/`.

Contoh error PHP 8.x:

```
Fatal error: Cannot override final method Student::getId() in ...\RegularStudentRusak.php on line 9
```

Penjelasan: `final` pada method melarang subclass mendefinisikannya ulang. `getId()` dibuat final karena NIM dipakai `StudentRepository` sebagai kunci data. Jika bisa di-override, subclass bisa mengembalikan ID palsu dan merusak integritas repository (data tertimpa/tidak ditemukan).

## Latihan 5 — Kenapa `StudentRepository` menerima `Student`, bukan hanya `RegularStudent`?

1. **Polimorfisme / Liskov Substitution.** `RegularStudent`, `ScholarshipStudent`, dan `InternationalStudent` semuanya `Student`, jadi semuanya bisa disimpan dan dipanggil `calculateFee()`-nya sesuai class aslinya (overriding).
2. **Open/Closed Principle.** Menambah jenis mahasiswa baru (misal `InternationalStudent` di latihan 1) cukup membuat subclass baru. `StudentRepository` tidak perlu diubah. Kalau tipe parameternya `RegularStudent`, setiap jenis baru memaksa kita mengubah repository, atau malah ditolak PHP dengan `TypeError` (`ScholarshipStudent` bukan `RegularStudent`; kelasnya `final`).
3. **Bergantung pada abstraksi, bukan implementasi konkret (Dependency Inversion).** Repository hanya butuh kontrak yang dijamin `Student` (mis. `getId()` yang final), bukan detail cara menghitung biaya.
4. **Aman secara tipe.** `Student` abstract, sehingga hanya objek turunan yang valid; objek lain (misal `stdClass`) ditolak dengan `TypeError`. Ini dibuktikan di `test/run.php` (bagian "Latihan 5").
