<?php
session_start();      

if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    header('location: ../../login.php?pesan=2');
} else {
    require_once "../../config/database.php";

    if (isset($_POST['id']) && is_array($_POST['id'])) {
        $berhasil = 0;
        $gagal = 0;

        foreach ($_POST['id'] as $id_barang) {
            $id_barang = mysqli_real_escape_string($mysqli, $id_barang);
            
            // Periksa di tabel barang_masuk, barang_keluar, dan permintaan
            $query_masuk = mysqli_query($mysqli, "SELECT barang FROM tbl_barang_masuk WHERE barang='$id_barang'");
            $rows_masuk = mysqli_num_rows($query_masuk);

            $query_keluar = mysqli_query($mysqli, "SELECT barang FROM tbl_barang_keluar WHERE barang='$id_barang'");
            $rows_keluar = mysqli_num_rows($query_keluar);

            $query_permintaan = mysqli_query($mysqli, "SELECT barang FROM tbl_permintaan WHERE barang='$id_barang'");
            $rows_permintaan = mysqli_num_rows($query_permintaan);

            if ($rows_masuk > 0 || $rows_keluar > 0 || $rows_permintaan > 0) {
                $gagal++;
            } else {
                // Hapus foto jika ada
                $query_foto = mysqli_query($mysqli, "SELECT foto FROM tbl_barang WHERE id_barang='$id_barang'");
                $data_foto = mysqli_fetch_assoc($query_foto);
                if ($data_foto && !empty($data_foto['foto'])) {
                    @unlink("../../images/" . $data_foto['foto']);
                }

                $delete = mysqli_query($mysqli, "DELETE FROM tbl_barang WHERE id_barang='$id_barang'");
                if ($delete) {
                    $berhasil++;
                }
            }
        }

        header("location: ../../main.php?module=barang&pesan=10&berhasil=$berhasil&gagal=$gagal");
    } else {
        header('location: ../../main.php?module=barang');
    }
}
