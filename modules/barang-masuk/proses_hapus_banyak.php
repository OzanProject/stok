<?php
session_start();      

if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    header('location: ../../login.php?pesan=2');
} else {
    require_once "../../config/database.php";

    if (isset($_POST['id']) && is_array($_POST['id'])) {
        $berhasil = 0;
        $gagal = 0;

        foreach ($_POST['id'] as $id_transaksi) {
            $id_transaksi = mysqli_real_escape_string($mysqli, $id_transaksi);
            
            // ambil semua data barang dan jumlah untuk update stok
            $query = mysqli_query($mysqli, "SELECT barang, jumlah FROM tbl_barang_masuk WHERE id_transaksi='$id_transaksi'")
                                            or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
            
            while($data = mysqli_fetch_assoc($query)) {
                $barang = $data['barang'];
                $jumlah = $data['jumlah'];

                // kurangi stok di tabel barang
                $update_stok = mysqli_query($mysqli, "UPDATE tbl_barang SET stok = stok - $jumlah WHERE id_barang = '$barang'")
                                              or die('Ada kesalahan pada query update stok : ' . mysqli_error($mysqli));
            }

            // hapus transaksi
            $delete = mysqli_query($mysqli, "DELETE FROM tbl_barang_masuk WHERE id_transaksi='$id_transaksi'")
                                            or die('Ada kesalahan pada query delete : ' . mysqli_error($mysqli));
            
            if ($delete) {
                $berhasil++;
            } else {
                $gagal++;
            }
        }

        header("location: ../../main.php?module=barang_masuk&pesan=10&berhasil=$berhasil&gagal=$gagal");
    } else {
        header('location: ../../main.php?module=barang_masuk');
    }
}
