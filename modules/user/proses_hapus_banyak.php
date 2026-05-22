<?php
session_start();      

if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    header('location: ../../login.php?pesan=2');
} else {
    require_once "../../config/database.php";

    if (isset($_POST['id']) && is_array($_POST['id'])) {
        $berhasil = 0;
        $gagal = 0;

        foreach ($_POST['id'] as $id_user) {
            $id_user = mysqli_real_escape_string($mysqli, $id_user);
            
            $delete = mysqli_query($mysqli, "DELETE FROM tbl_user WHERE id_user='$id_user'");
            
            if ($delete) {
                $berhasil++;
            } else {
                $gagal++;
            }
        }

        header("location: ../../main.php?module=user&pesan=10&berhasil=$berhasil&gagal=$gagal");
    } else {
        header('location: ../../main.php?module=user');
    }
}
