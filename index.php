<?php
$fileTeks = 'bukutamu.txt';
$error = "";
$sukses = "";


if (!file_exists($fileTeks)) {
    file_put_contents($fileTeks, "");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kirim'])) {
    $nama  = trim($_POST['nama']);
    $pesan = trim($_POST['pesan']);

    if (empty($nama) || empty($pesan)) {
        $error = "Nama dan Pesan tidak boleh kosong!";
    } else {

        $waktu = date('d-m-Y H:i');

        $namaClean  = str_replace(["\r", "\n", "|"], " ", $nama);
        $pesanClean = str_replace(["\r", "\n", "|"], " ", $pesan);

        $barisBaru = "{$waktu}|{$namaClean}|{$pesanClean}" . PHP_EOL;

        // Simpan ke file teks (FILE_APPEND agar menambah ke baris paling bawah)
        if (file_put_contents($fileTeks, $barisBaru, FILE_APPEND | LOCK_EX)) {
            $sukses = "Pesan berhasil disimpan ke dalam file teks!";
            header("Location: index.php?status=sukses");
            exit();
        } else {
            $error = "Gagal menulis ke file!";
        }
    }
}


$daftarTamu = [];
if (file_exists($fileTeks)) {
    $isiFile = file($fileTeks, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    // Balik urutan agar pesan terbaru ada di atas
    $daftarTamu = array_reverse($isiFile);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Proyek 5 - Buku Tamu File TXT</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f6f9; }
        .card { max-width: 550px; margin: auto; padding: 25px; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea, button { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { background-color: #007bff; color: white; border: none; font-weight: bold; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .alert-error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .alert-success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .tamu-item { background: #f8f9fa; border-left: 4px solid #007bff; padding: 10px 15px; margin-bottom: 10px; border-radius: 4px; }
        .tamu-header { font-size: 13px; color: #6c757d; margin-bottom: 5px; }
    </style>
</head>
<body>

<div class="card">
    <h2>Buku Tamu (File TXT)</h2>

    <?php if ($error): ?>
        <div class="alert-error"><?= $error; ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
        <div class="alert-success">Pesan berhasil disimpan ke file!</div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Nama Pengunjung:</label>
            <input type="text" name="nama" required placeholder="Masukkan nama Anda">
        </div>

        <div class="form-group">
            <label>Pesan / Kesan:</label>
            <textarea name="pesan" rows="3" required placeholder="Tuliskan pesan Anda"></textarea>
        </div>

        <button type="submit" name="kirim">Kirim Pesan</button>
    </form>

    <hr style="margin: 25px 0;">

    <h3>Daftar Pesan Pengunjung (<?= count($daftarTamu); ?>):</h3>

    <?php if (empty($daftarTamu)): ?>
        <p style="color: #888;">Belum ada pesan tersimpan.</p>
    <?php else: ?>
        <?php foreach ($daftarTamu as $baris): 
            $data = explode('|', $baris);
            if (count($data) == 3):
                list($waktu, $namaTamu, $pesanTamu) = $data;
        ?>
            <div class="tamu-item">
                <div class="tamu-header">
                    <strong><?= htmlspecialchars($namaTamu); ?></strong> • <?= htmlspecialchars($waktu); ?>
                </div>
                <div><?= htmlspecialchars($pesanTamu); ?></div>
            </div>
        <?php 
            endif;
        endforeach; 
        ?>
    <?php endif; ?>
</div>

</body>
</html>