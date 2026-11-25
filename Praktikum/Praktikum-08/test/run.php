<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';

$pass = 0;
function check(bool $ok, string $label): void {
    global $pass;
    if (!$ok) {
        throw new RuntimeException('FAIL: ' . $label);
    }
    $pass++;
    echo "PASS: $label\n";
}
function fails(callable $fn, string $exception, string $label): void {
    try {
        $fn();
    } catch (Throwable $e) {
        check($e instanceof $exception, $label);
        return;
    }
    throw new RuntimeException('FAIL (tidak ada exception): ' . $label);
}

$r = new StudentRepository();
$a = new RegularStudent('M01', 'Alya', 'Informatika', 5000000);
$b = new ScholarshipStudent('M02', 'Bima', 'SI', 5000000, 40);

// ---- Inheritance & overriding ----
check($a instanceof Student, 'Inheritance regular');
check($b instanceof Student, 'Inheritance scholarship');
check($a->calculateFee() === 5000000.0, 'Overriding biaya reguler');
check($b->calculateFee() === 3000000.0, 'Overriding biaya beasiswa');
check(str_contains($b->summary(), '[Beasiswa]'), 'Overriding + parent::');

// ---- CRUD ----
$r->create($a);
$r->create($b);
check(count($r->all()) === 2, 'Create');
check($r->find('M02') === $b, 'Read');
fails(fn() => $r->create($a), InvalidArgumentException::class, 'NIM duplikat ditolak');
$r->update('M01', new RegularStudent('M03', 'Cici', 'Informatika', 4000000));
check($r->find('M01') === null && $r->find('M03') !== null, 'Update NIM');
$r->delete('M03');
check(count($r->all()) === 1, 'Delete');

// ---- Latihan 1: InternationalStudent tanpa mengubah StudentRepository ----
$c = new InternationalStudent('M10', 'Chen', 'Informatika', 5000000, 2500000);
check($c instanceof Student, 'Latihan 1: InternationalStudent adalah Student');
check($c->getType() === 'international', 'Latihan 1: tipe international');
check($c->calculateFee() === 7500000.0, 'Latihan 1: biaya = dasar + biaya internasional');
check($c->getInternationalFee() === 2500000.0, 'Latihan 1: getInternationalFee');
check(str_contains($c->summary(), '[Internasional]'), 'Latihan 1: summary memakai parent::');
$r->create($c);
check($r->find('M10') === $c, 'Latihan 1: repository menyimpan InternationalStudent apa adanya');
check($r->find('M10')->calculateFee() === 7500000.0, 'Latihan 1: polimorfisme calculateFee lewat repository');
fails(fn() => new InternationalStudent('M11', 'Dewi', 'TI', 1000, -1), InvalidArgumentException::class, 'Latihan 1: biaya internasional negatif ditolak');
$r->delete('M10');
check(count($r->all()) === 1, 'Latihan 1: hapus InternationalStudent');

// ---- Latihan 5: tipe parameter repository adalah Student (abstraksi) ----
$paramType = (new ReflectionMethod(StudentRepository::class, 'create'))->getParameters()[0]->getType();
check($paramType instanceof ReflectionNamedType && $paramType->getName() === 'Student', 'Latihan 5: create() bertipe Student, bukan RegularStudent');
fails(fn() => $r->create(new stdClass()), TypeError::class, 'Latihan 5: objek non-Student ditolak (TypeError)');

// ---- Overloading via __call (StudentPresenter) ----
$p = new StudentPresenter();
check($p->showData('Alya') === 'Nama: Alya', '__call satu argumen');
check(str_contains($p->showData('M01', 'Alya'), 'NIM:'), '__call dua argumen');
check(str_contains($p->showData('M01', 'Alya', 'TI'), 'Prodi:'), '__call tiga argumen');
fails(fn() => $p->showData(), InvalidArgumentException::class, '__call argumen invalid');
fails(fn() => $p->unknown(), BadMethodCallException::class, '__call method invalid');

// ---- Latihan 2: showData(id, name, major, semester) ----
check($p->showData('M01', 'Alya', 'Informatika', 5) === 'NIM: M01, Nama: Alya, Prodi: Informatika, Semester: 5', 'Latihan 2: empat argumen (semester int)');
check($p->showData('M01', 'Alya', 'Informatika', '5') === 'NIM: M01, Nama: Alya, Prodi: Informatika, Semester: 5', 'Latihan 2: semester string angka diterima');
check($p->showData('M01', 'Alya', 'Informatika', 1) === 'NIM: M01, Nama: Alya, Prodi: Informatika, Semester: 1', 'Latihan 2: batas bawah semester 1');
check($p->showData('M01', 'Alya', 'Informatika', 14) === 'NIM: M01, Nama: Alya, Prodi: Informatika, Semester: 14', 'Latihan 2: batas atas semester 14');
fails(fn() => $p->showData('M01', 'Alya', 'Informatika', 0), InvalidArgumentException::class, 'Latihan 2: semester 0 ditolak');
fails(fn() => $p->showData('M01', 'Alya', 'Informatika', 15), InvalidArgumentException::class, 'Latihan 2: semester 15 ditolak');
fails(fn() => $p->showData('M01', 'Alya', 'Informatika', -3), InvalidArgumentException::class, 'Latihan 2: semester negatif ditolak');
fails(fn() => $p->showData('M01', 'Alya', 'Informatika', 'lima'), InvalidArgumentException::class, 'Latihan 2: semester non-angka ditolak');
fails(fn() => $p->showData('M01', 'Alya', 'Informatika', 5.5), InvalidArgumentException::class, 'Latihan 2: semester desimal ditolak');
fails(fn() => $p->showData('M01', 'Alya', 'Informatika', null), InvalidArgumentException::class, 'Latihan 2: semester null ditolak');
fails(fn() => $p->showData('', 'Alya', 'Informatika', 5), InvalidArgumentException::class, 'Latihan 2: NIM kosong ditolak');
fails(fn() => $p->showData('M01', '   ', 'Informatika', 5), InvalidArgumentException::class, 'Latihan 2: nama spasi saja ditolak');
fails(fn() => $p->showData('M01', 'Alya', 123, 5), InvalidArgumentException::class, 'Latihan 2: prodi bukan string ditolak');
fails(fn() => $p->showData('M01', 'Alya', 'Informatika', 5, 'extra'), InvalidArgumentException::class, 'Latihan 2: lima argumen ditolak');

// ---- Beasiswa & final ----
fails(fn() => new ScholarshipStudent('M04', 'Dina', 'TI', 1000, 101), InvalidArgumentException::class, 'Diskon invalid');
check((new ReflectionMethod(Student::class, 'getId'))->isFinal(), 'Final method tidak boleh override');

echo "TOTAL: $pass tests passed\n";
