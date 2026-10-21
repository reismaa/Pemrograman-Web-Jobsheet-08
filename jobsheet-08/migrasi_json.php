<?php
require __DIR__ . '/includes/koneksi.php';

$path = __DIR__ . '/data/buku.json';

if (!file_exists($path)) {
    die("File $path tidak ditemukan.\n");
}

$data = json_decode(file_get_contents($path), true);

if (!is_array($data)) {
    die("Isi buku.json bukan JSON yang valid.\n");
}

$stmt = $pdo->prepare(
    "INSERT INTO buku (judul, pengarang, tahun, isbn, stok, kategori)
     VALUES (:judul, :pengarang, :tahun, :isbn, :stok, :kategori)"
);

$berhasil = 0;

try {
    $pdo->beginTransaction();

    foreach ($data as $b) {
        $stmt->execute([
            'judul'     => $b['judul'],
            'pengarang' => $b['pengarang'],
            'tahun'     => (int) $b['tahun'],
            'isbn'      => $b['isbn'] ?? null,
            'stok'      => (int) ($b['stok'] ?? 0),
            'kategori'  => $b['kategori'] ?? null,
        ]);
        $berhasil++;
    }

    $pdo->commit();
    echo "Migrasi selesai: $berhasil buku dimasukkan.\n";
} catch (PDOException $e) {
    $pdo->rollBack();
    die("Migrasi dibatalkan, tidak ada data yang masuk: " . $e->getMessage() . "\n");
}