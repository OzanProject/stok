<?php
session_start();

// jika user belum login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    header('location: ../../login.php?pesan=2');
} else {
    require_once "../../config/database.php";

    if (isset($_GET['kode'])) {
        $kode_permintaan = mysqli_real_escape_string($mysqli, $_GET['kode']);
        
        // Ambil data permintaan
        $query = mysqli_query($mysqli, "SELECT * FROM tbl_permintaan WHERE kode_permintaan='$kode_permintaan'")
                                        or die('Ada kesalahan pada query : ' . mysqli_error($mysqli));
        
        if (mysqli_num_rows($query) > 0) {
            $id_transaksi_keluar_to_delete = "";
            while($data = mysqli_fetch_assoc($query)) {
                $status = $data['status'];
                $barang = $data['barang'];
                $jumlah = $data['jumlah'];
                $id_transaksi_keluar = $data['id_transaksi_keluar'];
                
                if(!empty($id_transaksi_keluar)) {
                    $id_transaksi_keluar_to_delete = $id_transaksi_keluar;
                }
                
                // Jika status ACC, kita kembalikan stok 
                if ($status == 'ACC') {
                    // Kembalikan stok
                    $update_stok = mysqli_query($mysqli, "UPDATE tbl_barang SET stok = stok + $jumlah WHERE id_barang='$barang'");
                }
            }
            
            // Hapus data barang keluar sekaligus jika ada
            if (!empty($id_transaksi_keluar_to_delete)) {
                $hapus_tk = mysqli_query($mysqli, "DELETE FROM tbl_barang_keluar WHERE id_transaksi='$id_transaksi_keluar_to_delete'");
            }
            
            // Terakhir, hapus data permintaannya sendiri
            $hapus = mysqli_query($mysqli, "DELETE FROM tbl_permintaan WHERE kode_permintaan='$kode_permintaan'")
                                            or die('Ada kesalahan pada query delete : ' . mysqli_error($mysqli));
            
            if ($hapus) {
                header('location: ../../main.php?module=permintaan&pesan=3');
            }
        } else {
            header('location: ../../main.php?module=permintaan');
        }
    }
}
