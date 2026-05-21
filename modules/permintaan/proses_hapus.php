<?php
session_start();

// jika user belum login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    header('location: ../../login.php?pesan=2');
} else {
    require_once "../../config/database.php";

    if (isset($_GET['id'])) {
        $id_permintaan = mysqli_real_escape_string($mysqli, $_GET['id']);
        
        // Ambil data permintaan
        $query = mysqli_query($mysqli, "SELECT * FROM tbl_permintaan WHERE id_permintaan='$id_permintaan'")
                                        or die('Ada kesalahan pada query : ' . mysqli_error($mysqli));
        
        if (mysqli_num_rows($query) > 0) {
            $data = mysqli_fetch_assoc($query);
            $status = $data['status'];
            $barang = $data['barang'];
            $jumlah = $data['jumlah'];
            $id_transaksi_keluar = $data['id_transaksi_keluar'];
            
            // Jika status ACC, kita kembalikan stok dan hapus barang keluarnya
            if ($status == 'ACC') {
                // Kembalikan stok
                $update_stok = mysqli_query($mysqli, "UPDATE tbl_barang SET stok = stok + $jumlah WHERE id_barang='$barang'");
                
                // Hapus data barang keluar
                if (!empty($id_transaksi_keluar)) {
                    $hapus_tk = mysqli_query($mysqli, "DELETE FROM tbl_barang_keluar WHERE id_transaksi='$id_transaksi_keluar'");
                }
            }
            
            // Terakhir, hapus data permintaannya sendiri
            $hapus = mysqli_query($mysqli, "DELETE FROM tbl_permintaan WHERE id_permintaan='$id_permintaan'")
                                            or die('Ada kesalahan pada query delete : ' . mysqli_error($mysqli));
            
            if ($hapus) {
                header('location: ../../main.php?module=permintaan&pesan=3');
            }
        } else {
            header('location: ../../main.php?module=permintaan');
        }
    }
}
