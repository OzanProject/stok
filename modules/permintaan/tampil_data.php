<?php
// mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser dan hanya dapat dijalankan ketika di include oleh file lain
// jika file diakses secara langsung
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    // alihkan ke halaman error 404
    header('location: 404.html');
}
// jika file di include oleh file lain, tampilkan isi file
else {
    // menampilkan pesan sesuai dengan proses yang dijalankan
    // jika pesan tersedia
    if (isset($_GET['pesan'])) {
        if ($_GET['pesan'] == 1) {
            echo '  <div class="alert alert-notify alert-success alert-dismissible fade show" role="alert">
                        <span data-notify="icon" class="fas fa-check"></span> 
                        <span data-notify="title" class="text-success">Sukses!</span> 
                        <span data-notify="message">Pengajuan permintaan berhasil disimpan.</span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        } elseif ($_GET['pesan'] == 2) {
            echo '  <div class="alert alert-notify alert-success alert-dismissible fade show" role="alert">
                        <span data-notify="icon" class="fas fa-check"></span> 
                        <span data-notify="title" class="text-success">Sukses!</span> 
                        <span data-notify="message">Permintaan berhasil di ACC.</span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        } elseif ($_GET['pesan'] == 3) {
            echo '  <div class="alert alert-notify alert-success alert-dismissible fade show" role="alert">
                        <span data-notify="icon" class="fas fa-check"></span> 
                        <span data-notify="title" class="text-success">Sukses!</span> 
                        <span data-notify="message">Data permintaan berhasil dihapus.</span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        }
    }
?>
    <div class="panel-header bg-secondary-gradient">
        <div class="page-inner py-45">
            <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
                <div class="page-header text-white">
                    <h4 class="page-title text-white"><i class="fas fa-file-invoice mr-2"></i> Permintaan Barang</h4>
                    <ul class="breadcrumbs">
                        <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
                        <li class="separator"><i class="flaticon-right-arrow"></i></li>
                        <li class="nav-item"><a href="?module=permintaan" class="text-white">Permintaan Barang</a></li>
                        <li class="separator"><i class="flaticon-right-arrow"></i></li>
                        <li class="nav-item"><a>Data</a></li>
                    </ul>
                </div>
                <div class="ml-md-auto py-2 py-md-0">
                    <?php if ($_SESSION['hak_akses'] != 'Administrator' && $_SESSION['hak_akses'] != 'Admin Gudang') { ?>
                    <!-- button entri data hanya untuk user peminta -->
                    <a href="?module=form_entri_permintaan" class="btn btn-secondary btn-round">
                        <span class="btn-label"><i class="fa fa-plus mr-2"></i></span> Ajukan Permintaan
                    </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Data Permintaan Barang</div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Peminta</th>
                                <th class="text-center">Barang</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            
                            $id_user = $_SESSION['id_user'] ?? 0;
                            // Jika Administrator atau Admin Gudang, tampilkan semua
                            if ($_SESSION['hak_akses'] == 'Administrator' || $_SESSION['hak_akses'] == 'Admin Gudang') {
                                $query_str = "SELECT a.id_permintaan, a.tanggal, a.jumlah, a.status, b.nama_barang, u.nama_user, a.barang as id_barang
                                              FROM tbl_permintaan as a 
                                              INNER JOIN tbl_barang as b ON a.barang = b.id_barang 
                                              INNER JOIN tbl_user as u ON a.id_user = u.id_user
                                              ORDER BY a.id_permintaan DESC";
                            } else {
                                // Selain itu, tampilkan miliknya sendiri (But id_user session must be set, which it isn't in main.php directly, wait...)
                                // Actually let's query tbl_user based on username from session.
                                $username = $_SESSION['username'];
                                $query_str = "SELECT a.id_permintaan, a.tanggal, a.jumlah, a.status, b.nama_barang, u.nama_user, a.barang as id_barang
                                              FROM tbl_permintaan as a 
                                              INNER JOIN tbl_barang as b ON a.barang = b.id_barang 
                                              INNER JOIN tbl_user as u ON a.id_user = u.id_user
                                              WHERE u.username = '$username'
                                              ORDER BY a.id_permintaan DESC";
                            }

                            $query = mysqli_query($mysqli, $query_str) or die('Error query: ' . mysqli_error($mysqli));
                            while ($data = mysqli_fetch_assoc($query)) { 
                                if($data['status'] == 'Pending') {
                                    $badge = 'badge-warning';
                                } elseif($data['status'] == 'ACC') {
                                    $badge = 'badge-success';
                                } else {
                                    $badge = 'badge-danger';
                                }
                            ?>
                                <tr>
                                    <td class="text-center"><?php echo $no++; ?></td>
                                    <td class="text-center"><?php echo date('d-m-Y', strtotime($data['tanggal'])); ?></td>
                                    <td><?php echo $data['nama_user']; ?></td>
                                    <td><?php echo $data['nama_barang']; ?></td>
                                    <td class="text-center"><?php echo $data['jumlah']; ?></td>
                                    <td class="text-center"><span class="badge <?php echo $badge; ?>"><?php echo $data['status']; ?></span></td>
                                    <td class="text-center">
                                        <?php if ($data['status'] == 'Pending' && $_SESSION['hak_akses'] == 'Administrator') { ?>
                                            <a href="modules/permintaan/proses_acc.php?id=<?php echo $data['id_permintaan']; ?>" onclick="return confirm('Anda yakin ingin meng-ACC permintaan ini? (Stok akan otomatis terpotong)')" class="btn btn-success btn-sm text-white" data-toggle="tooltip" title="ACC">
                                                ACC
                                            </a>
                                        <?php } ?>
                                        
                                        <?php if ($data['status'] == 'ACC') { ?>
                                            <a href="modules/permintaan/cetak_bukti.php?id=<?php echo $data['id_permintaan']; ?>" target="_blank" class="btn btn-info btn-sm text-white" data-toggle="tooltip" title="Cetak Bukti">
                                                <i class="fas fa-print"></i> Cetak
                                            </a>
                                        <?php } ?>
                                        
                                        <!-- Tombol Hapus -->
                                        <a href="modules/permintaan/proses_hapus.php?id=<?php echo $data['id_permintaan']; ?>" onclick="return confirm('Anda yakin ingin menghapus data permintaan ini? (Jika sudah ACC, maka stok dan transaksi barang keluar akan dikembalikan otomatis)')" class="btn btn-danger btn-sm text-white" data-toggle="tooltip" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
