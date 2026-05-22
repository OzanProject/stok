<?php
session_start();      // mengaktifkan session

if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    header('location: ../../login.php?pesan=2');
} else {
    require_once "../../config/database.php";

    if (isset($_POST['simpan'])) {
        $id_transaksi  = mysqli_real_escape_string($mysqli, $_POST['id_transaksi']);
        $tanggal       = mysqli_real_escape_string($mysqli, trim($_POST['tanggal']));

        $tanggal_masuk = date('Y-m-d', strtotime($tanggal));

        $barang_array = $_POST['barang'];
        $jumlah_array = $_POST['jumlah'];

        if (!empty($barang_array)) {
            $berhasil = true;
            for ($i = 0; $i < count($barang_array); $i++) {
                $barang = mysqli_real_escape_string($mysqli, $barang_array[$i]);
                $jumlah = mysqli_real_escape_string($mysqli, $jumlah_array[$i]);

                // sql statement untuk insert data ke tabel "tbl_barang_masuk"
                $insert = mysqli_query($mysqli, "INSERT INTO tbl_barang_masuk(id_transaksi, tanggal, barang, jumlah) 
                                                VALUES('$id_transaksi', '$tanggal_masuk', '$barang', '$jumlah')")
                                                or die('Ada kesalahan pada query insert : ' . mysqli_error($mysqli));
                
                if ($insert) {
                    // update stok di tabel barang (ditambah)
                    $update_stok = mysqli_query($mysqli, "UPDATE tbl_barang SET stok = stok + $jumlah WHERE id_barang = '$barang'")
                                                or die('Ada kesalahan pada query update stok : ' . mysqli_error($mysqli));
                } else {
                    $berhasil = false;
                }
            }

            if ($berhasil) {
                // alihkan ke halaman barang masuk dan tampilkan pesan berhasil simpan data
                header('location: ../../main.php?module=barang_masuk&pesan=1');
            }
        } else {
            header('location: ../../main.php?module=form_entri_barang_masuk');
        }
    }
}
