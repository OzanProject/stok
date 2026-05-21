<?php
session_start();      // mengaktifkan session

// pengecekan session login user 
// jika user belum login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    // alihkan ke halaman login dan tampilkan pesan peringatan login
    header('location: ../../login.php?pesan=2');
}
// jika user sudah login, maka jalankan perintah untuk insert
else {
    // panggil file "database.php" untuk koneksi ke database
    require_once "../../config/database.php";

    // mengecek data hasil submit dari form
    if (isset($_POST['simpan'])) {
        // ambil data hasil submit dari form
        $tanggal       = mysqli_real_escape_string($mysqli, trim($_POST['tanggal']));
        $barang        = mysqli_real_escape_string($mysqli, $_POST['barang']);
        $jumlah        = str_replace('.', '', mysqli_real_escape_string($mysqli, $_POST['jumlah']));
        $id_user       = $_SESSION['id_user']; // dari session login

        // ubah format tanggal menjadi Tahun-Bulan-Hari (Y-m-d) sebelum disimpan ke database
        $tanggal_permintaan = date('Y-m-d', strtotime($tanggal));

        // sql statement untuk insert data ke tabel "tbl_permintaan"
        $insert = mysqli_query($mysqli, "INSERT INTO tbl_permintaan(tanggal, id_user, barang, jumlah, status) 
                                        VALUES('$tanggal_permintaan', '$id_user', '$barang', '$jumlah', 'Pending')")
                                        or die('Ada kesalahan pada query insert : ' . mysqli_error($mysqli));
        // cek query
        // jika proses insert berhasil
        if ($insert) {
            // alihkan ke halaman permintaan dan tampilkan pesan berhasil simpan data
            header('location: ../../main.php?module=permintaan&pesan=1');
        }
    }
}
