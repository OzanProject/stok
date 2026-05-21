<?php
session_start();      // mengaktifkan session

// pengecekan session login user 
// jika user belum login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    // alihkan ke halaman login dan tampilkan pesan peringatan login
    header('location: ../../login.php?pesan=2');
}
// jika user sudah login dan hak akses Administrator
else if ($_SESSION['hak_akses'] == 'Administrator' || $_SESSION['hak_akses'] == 'Admin Gudang') {
    // panggil file "database.php" untuk koneksi ke database
    require_once "../../config/database.php";

    // mengecek data GET "id"
    if (isset($_GET['id'])) {
        $id_permintaan = mysqli_real_escape_string($mysqli, $_GET['id']);
        
        // ambil detail permintaan
        $query = mysqli_query($mysqli, "SELECT * FROM tbl_permintaan WHERE id_permintaan='$id_permintaan' AND status='Pending'")
                                        or die('Ada kesalahan pada query : ' . mysqli_error($mysqli));
        
        if (mysqli_num_rows($query) > 0) {
            $data = mysqli_fetch_assoc($query);
            $barang = $data['barang'];
            $jumlah = $data['jumlah'];
            $id_user_peminta = $data['id_user'];
            $tanggal = date('Y-m-d');
            
            // Cek stok barang
            $cek_stok = mysqli_query($mysqli, "SELECT stok FROM tbl_barang WHERE id_barang='$barang'");
            $stok_data = mysqli_fetch_assoc($cek_stok);
            
            if ($stok_data['stok'] >= $jumlah) {
                // Update status permintaan menjadi ACC
                $update_permintaan = mysqli_query($mysqli, "UPDATE tbl_permintaan SET status='ACC' WHERE id_permintaan='$id_permintaan'");
                
                if ($update_permintaan) {
                    // Update stok barang di tabel barang
                    $update_stok = mysqli_query($mysqli, "UPDATE tbl_barang SET stok = stok - $jumlah WHERE id_barang='$barang'");
                    
                    // Generate ID Transaksi untuk barang keluar
                    $query_tk = mysqli_query($mysqli, "SELECT RIGHT(id_transaksi,7) as nomor FROM tbl_barang_keluar ORDER BY id_transaksi DESC LIMIT 1");
                    if (mysqli_num_rows($query_tk) > 0) {
                        $data_tk = mysqli_fetch_assoc($query_tk);
                        $nomor_urut = $data_tk['nomor'] + 1;
                    } else {
                        $nomor_urut = 1;
                    }
                    $id_transaksi = "TK-" . str_pad($nomor_urut, 7, "0", STR_PAD_LEFT);
                    
                    // Ambil nama user peminta
                    $query_user = mysqli_query($mysqli, "SELECT nama_user FROM tbl_user WHERE id_user='$id_user_peminta'");
                    $data_user = mysqli_fetch_assoc($query_user);
                    $nama_peminta = $data_user['nama_user'];
                    
                    // Insert ke tabel barang keluar
                    $insert_tk = mysqli_query($mysqli, "INSERT INTO tbl_barang_keluar(id_transaksi, tanggal, barang, jumlah, user) 
                                                        VALUES('$id_transaksi', '$tanggal', '$barang', '$jumlah', '$nama_peminta')");
                    
                    // Simpan id_transaksi_keluar ke tabel permintaan agar bisa dilacak jika dihapus
                    $update_link = mysqli_query($mysqli, "UPDATE tbl_permintaan SET id_transaksi_keluar='$id_transaksi' WHERE id_permintaan='$id_permintaan'");
                    
                    // Alihkan dengan pesan berhasil
                    header('location: ../../main.php?module=permintaan&pesan=2');
                }
            } else {
                // Jika stok tidak cukup, seharusnya tampilkan pesan error, tapi untuk ringkas kita redirect dengan pesan berbeda jika ada
                // Tapi saat ini kita belum mendefinisikan pesan 3, jadi kita redirect kembali saja
                header('location: ../../main.php?module=permintaan');
            }
        } else {
            header('location: ../../main.php?module=permintaan');
        }
    }
}
