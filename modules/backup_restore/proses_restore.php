<?php
session_start();

if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    header('location: ../../login.php?pesan=2');
} elseif ($_SESSION['hak_akses'] == 'Administrator') {
    require_once "../../config/database.php";

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_sql'])) {
        $file = $_FILES['file_sql'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        
        if ($ext != 'sql') {
            header('location: ../../main.php?module=backup_restore&pesan=2');
            exit;
        }

        $query = file_get_contents($file['tmp_name']);
        
        if ($query) {
            // Mematikan sementara foreign key check agar DROP TABLE tidak error
            mysqli_query($mysqli, "SET FOREIGN_KEY_CHECKS=0");
            
            // Menggunakan multi_query untuk mengeksekusi banyak perintah SQL sekaligus
            if (mysqli_multi_query($mysqli, $query)) {
                do {
                    // Menyimpan hasil agar koneksi siap untuk query berikutnya
                    if ($result = mysqli_store_result($mysqli)) {
                        mysqli_free_result($result);
                    }
                } while (mysqli_more_results($mysqli) && mysqli_next_result($mysqli));
                
                // Menyalakan kembali foreign key check
                mysqli_query($mysqli, "SET FOREIGN_KEY_CHECKS=1");
                header('location: ../../main.php?module=backup_restore&pesan=1');
            } else {
                mysqli_query($mysqli, "SET FOREIGN_KEY_CHECKS=1");
                header('location: ../../main.php?module=backup_restore&pesan=2');
            }
        } else {
            header('location: ../../main.php?module=backup_restore&pesan=2');
        }
    } else {
        header('location: ../../main.php?module=backup_restore&pesan=3');
    }
} else {
    header('location: ../../404.html');
}
?>
