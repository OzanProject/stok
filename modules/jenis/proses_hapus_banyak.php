<?php
session_start();      

if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    header('location: ../../login.php?pesan=2');
} else {
    require_once "../../config/database.php";

    if (isset($_POST['id']) && is_array($_POST['id'])) {
        $berhasil = 0;
        $gagal = 0;

        foreach ($_POST['id'] as $id_jenis) {
            $id_jenis = mysqli_real_escape_string($mysqli, $id_jenis);
            
            $query = mysqli_query($mysqli, "SELECT jenis FROM tbl_barang WHERE jenis='$id_jenis'")
                                            or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
            $rows = mysqli_num_rows($query);

            if ($rows <> 0) {
                $gagal++;
            } else {
                $delete = mysqli_query($mysqli, "DELETE FROM tbl_jenis WHERE id_jenis='$id_jenis'")
                                                or die('Ada kesalahan pada query delete : ' . mysqli_error($mysqli));
                if ($delete) {
                    $berhasil++;
                }
            }
        }

        header("location: ../../main.php?module=jenis&pesan=10&berhasil=$berhasil&gagal=$gagal");
    } else {
        header('location: ../../main.php?module=jenis');
    }
}
