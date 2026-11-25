# Praktikum PBO Pertemuan 8 — CRUD Data Mahasiswa (PHP)

*Topik:* Overloading dan Overriding — Konsep Fundamental, Perbandingan Implementasi, dan Praktikum Menggunakan PHP.

## Menjalankan

1. Gunakan PHP 8.0+ (disarankan PHP 8.2+).
2. **Laragon:** letakkan folder `Praktikum-08` di `C:\laragon\www\`, start Laragon, lalu buka http://localhost/Praktikum-08/ (atau http://praktikum-08.test jika auto virtual host aktif).
   **Tanpa Laragon:** dari folder proyek jalankan `php -S localhost:8000`, lalu buka http://localhost:8000.
3. Jalankan pengujian dari folder proyek (terminal VSCode): `php test/run.php`.
4. Jalankan eksperimen error (latihan 3 dan 4): `php eksperimen/jalankan.php`.

## Struktur
- src/Student.php            : abstract parent class, method final getId().
- src/RegularStudent.php     : overriding calculateFee().
- src/ScholarshipStudent.php : overriding calculateFee() dan summary() memakai parent::.
- src/InternationalStudent.php: overriding calculateFee() (biaya dasar + biaya internasional) dan summary().
- src/StudentRepository.php  : CRUD polymorphic (Student sebagai tipe parameter).
- src/StudentPresenter.php   : PHP-style overloading via __call() (showData 1 sampai 4 argumen).
- index.php: UI CRUD, pencarian, session storage, CSRF token, escaping output.
- test/run.php: pengujian konsep dan operasi CRUD.
- eksperimen/: salinan terpisah untuk latihan 3 dan 4 (sengaja menghasilkan Fatal error, tidak dipakai aplikasi).
- JAWABAN_LATIHAN.md: jawaban dan penjelasan latihan 1 sampai 5.

## Materi yang wajib dipahami

*Overriding:* subclass mendefinisikan ulang method yang diwariskan, dengan signature kompatibel. Dalam aplikasi, calculateFee() menghasilkan nominal berbeda untuk RegularStudent, ScholarshipStudent, dan InternationalStudent. summary() pada ScholarshipStudent dan InternationalStudent memakai parent::summary().

*Overloading PHP:* tidak sama dengan method overloading berbasis signature di Java/C++. StudentPresenter sengaja *tidak* memiliki method showData(). Pemanggilan tersebut diarahkan ke __call() untuk 1, 2, 3 string, atau 4 argumen (nim, nama, prodi, semester). Magic method digunakan sebagai demonstrasi, bukan anjuran mengganti seluruh API bisnis dengan __call().

*Variadic:* satu method dengan ...$args bisa menerima banyak argumen, tetapi bukan traditional method overloading.

## Latihan (status: selesai, lihat JAWABAN_LATIHAN.md)
1. Tambahkan subclass InternationalStudent dengan biaya kuliah berbeda tanpa mengubah StudentRepository.
2. Tambahkan pola showData(id, name, major, semester) pada __call() beserta validasi dan tes.
3. Coba ubah signature calculateFee() menjadi tidak kompatibel pada salinan file terpisah. Catat error PHP.
4. Coba override getId() (method final) di salinan terpisah. Catat error PHP.
5. Jelaskan mengapa StudentRepository menerima Student dan bukan hanya RegularStudent.

## Batasan
Ini aplikasi edukasi, bukan aplikasi produksi. Data hanya disimpan dalam PHP Session (bukan MySQL), belum ada login/otorisasi, audit trail, atau persistence lintas session. Form input diproses dan divalidasi server-side; pengamanan lebih lanjut diperlukan untuk produksi.
