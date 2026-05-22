<?php
session_start();      // mengaktifkan session

// pengecekan session login user 
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    header('location: ../../login.php?pesan=2');
}
// jika user sudah login dan hak akses Administrator
else if ($_SESSION['hak_akses'] == 'Administrator' || $_SESSION['hak_akses'] == 'Admin Gudang') {
    require_once "../../config/database.php";

    if (isset($_GET['kode'])) {
        $kode_permintaan = mysqli_real_escape_string($mysqli, $_GET['kode']);
        
        // ambil detail permintaan
        $query = mysqli_query($mysqli, "SELECT * FROM tbl_permintaan WHERE kode_permintaan='$kode_permintaan' AND status='Pending'")
                                        or die('Ada kesalahan pada query : ' . mysqli_error($mysqli));
        
        if (mysqli_num_rows($query) > 0) {
            // Pengecekan stok untuk semua barang terlebih dahulu
            $semua_cukup = true;
            $items = [];
            $id_user_peminta = "";
            while($data = mysqli_fetch_assoc($query)) {
                $barang = $data['barang'];
                $jumlah = $data['jumlah'];
                $id_user_peminta = $data['id_user'];
                
                // Cek stok barang
                $cek_stok = mysqli_query($mysqli, "SELECT stok FROM tbl_barang WHERE id_barang='$barang'");
                $stok_data = mysqli_fetch_assoc($cek_stok);
                
                if ($stok_data['stok'] < $jumlah) {
                    $semua_cukup = false;
                    break;
                }
                
                $items[] = [
                    'id_permintaan' => $data['id_permintaan'],
                    'barang' => $barang,
                    'jumlah' => $jumlah
                ];
            }
            
            if ($semua_cukup && count($items) > 0) {
                $tanggal = date('Y-m-d');
                
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
                
                foreach($items as $item) {
                    $id_permintaan = $item['id_permintaan'];
                    $barang = $item['barang'];
                    $jumlah = $item['jumlah'];
                    
                    // Update status permintaan menjadi ACC dan simpan id_transaksi_keluar
                    $update_permintaan = mysqli_query($mysqli, "UPDATE tbl_permintaan SET status='ACC', id_transaksi_keluar='$id_transaksi' WHERE id_permintaan='$id_permintaan'");
                    
                    // Update stok barang di tabel barang
                    $update_stok = mysqli_query($mysqli, "UPDATE tbl_barang SET stok = stok - $jumlah WHERE id_barang='$barang'");
                    
                    // Insert ke tabel barang keluar (karena tabel ini sekarang mendukung multiple row per id_transaksi)
                    $insert_tk = mysqli_query($mysqli, "INSERT INTO tbl_barang_keluar(id_transaksi, tanggal, barang, jumlah, user) 
                                                        VALUES('$id_transaksi', '$tanggal', '$barang', '$jumlah', '$nama_peminta')");
                }
                
                // Alihkan dengan pesan berhasil
                header('location: ../../main.php?module=permintaan&pesan=2');
                
            } else {
                // Jika stok tidak cukup, redirect kembali
                header('location: ../../main.php?module=permintaan');
            }
        } else {
            header('location: ../../main.php?module=permintaan');
        }
    }
}
