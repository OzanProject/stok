<?php
session_start();      // mengaktifkan session

if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    header('location: ../../login.php?pesan=2');
} else {
    require_once "../../config/database.php";
    
    if (isset($_GET['id'])) {
        $id_permintaan = mysqli_real_escape_string($mysqli, $_GET['id']);
        
        $query = mysqli_query($mysqli, "SELECT a.id_permintaan, a.tanggal, a.jumlah, a.status, b.nama_barang, b.id_barang, c.nama_satuan, u.nama_user 
                                        FROM tbl_permintaan as a 
                                        INNER JOIN tbl_barang as b ON a.barang = b.id_barang 
                                        INNER JOIN tbl_satuan as c ON b.satuan = c.id_satuan
                                        INNER JOIN tbl_user as u ON a.id_user = u.id_user
                                        WHERE a.id_permintaan='$id_permintaan' AND a.status='ACC'")
                                        or die('Ada kesalahan pada query : ' . mysqli_error($mysqli));
        
        if (mysqli_num_rows($query) > 0) {
            $data = mysqli_fetch_assoc($query);
        } else {
            echo "Data tidak ditemukan atau belum di-ACC.";
            exit;
        }
    } else {
        exit;
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cetak Bukti Permintaan Barang</title>
    <style type="text/css">
        body { font-family: Arial, sans-serif; font-size: 14px; }
        .container { width: 800px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; }
        .header { text-align: center; border-bottom: 2px solid #000; margin-bottom: 20px; padding-bottom: 10px; }
        .header h2 { margin: 0; padding: 0; }
        .header h3 { margin: 5px 0 0 0; padding: 0; font-weight: normal; }
        .content { margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { display: flex; justify-content: space-between; margin-top: 50px; text-align: center; }
        .signature { width: 250px; }
        .signature p { margin-bottom: 70px; }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <div class="header">
            <h2>BUKTI PENGELUARAN BARANG</h2>
            <h3>Gudang Material</h3>
        </div>
        
        <div class="content">
            <p>Telah diberikan kepada:</p>
            <p><strong>Nama Peminta :</strong> <?php echo $data['nama_user']; ?></p>
            <p><strong>Tanggal Keluar :</strong> <?php echo date('d-m-Y', strtotime($data['tanggal'])); ?></p>
            
            <table>
                <thead>
                    <tr>
                        <th width="5%">No.</th>
                        <th width="20%">Kode Barang</th>
                        <th width="45%">Nama Barang</th>
                        <th width="15%">Jumlah</th>
                        <th width="15%">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center;">1</td>
                        <td><?php echo $data['id_barang']; ?></td>
                        <td><?php echo $data['nama_barang']; ?></td>
                        <td style="text-align: center;"><?php echo $data['jumlah']; ?></td>
                        <td><?php echo $data['nama_satuan']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="footer">
            <div class="signature">
                <p>Pemohon,</p>
                <br>
                <strong>( <?php echo $data['nama_user']; ?> )</strong>
            </div>
            <div class="signature">
                <p>Pengurus Barang,</p>
                <br>
                <strong>( ......................................... )</strong>
            </div>
        </div>
    </div>
</body>
</html>
<?php } ?>
