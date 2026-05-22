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
		// jika pesan = 10 (bulk delete)
		elseif ($_GET['pesan'] == 10) {
            $berhasil = $_GET['berhasil'] ?? 0;
            $gagal = $_GET['gagal'] ?? 0;
            $msg = "$berhasil data permintaan berhasil dihapus.";
            if ($gagal > 0) {
                $msg .= " $gagal data gagal dihapus.";
                $type = "alert-warning";
                $title = "Peringatan!";
                $icon = "fas fa-exclamation-triangle";
                $text_color = "text-warning";
            } else {
                $type = "alert-success";
                $title = "Sukses!";
                $icon = "fas fa-check";
                $text_color = "text-success";
            }

			echo '  <div class="alert alert-notify '.$type.' alert-dismissible fade show" role="alert">
                        <span data-notify="icon" class="'.$icon.'"></span> 
                        <span data-notify="title" class="'.$text_color.'">'.$title.'</span> 
                        <span data-notify="message">'.$msg.'</span>
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
                <form id="formHapusBanyak" action="modules/permintaan/proses_hapus_banyak.php" method="POST">
                    <div class="mb-3">
                        <button type="button" id="btnHapusBanyak" class="btn btn-danger btn-round btn-sm">
                            <i class="fas fa-trash mr-2"></i> Hapus Terpilih
                        </button>
                    </div>
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="20" class="text-center" data-orderable="false"><input type="checkbox" id="checkAll"></th>
                                <th class="text-center">No.</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Peminta</th>
                                <th class="text-center">Total Item</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $modals = ''; // Variabel untuk menyimpan HTML modal
                            
                            $id_user = $_SESSION['id_user'] ?? 0;
                            // Jika Administrator atau Admin Gudang, tampilkan semua
                            if ($_SESSION['hak_akses'] == 'Administrator' || $_SESSION['hak_akses'] == 'Admin Gudang') {
                                $query_str = "SELECT a.kode_permintaan, a.tanggal, a.status, u.nama_user, COUNT(a.id_permintaan) as total_item
                                              FROM tbl_permintaan as a 
                                              INNER JOIN tbl_user as u ON a.id_user = u.id_user
                                              GROUP BY a.kode_permintaan, a.tanggal, a.status, u.nama_user
                                              ORDER BY a.tanggal DESC, a.kode_permintaan DESC";
                            } else {
                                $username = $_SESSION['username'];
                                $query_str = "SELECT a.kode_permintaan, a.tanggal, a.status, u.nama_user, COUNT(a.id_permintaan) as total_item
                                              FROM tbl_permintaan as a 
                                              INNER JOIN tbl_user as u ON a.id_user = u.id_user
                                              WHERE u.username = '$username'
                                              GROUP BY a.kode_permintaan, a.tanggal, a.status, u.nama_user
                                              ORDER BY a.tanggal DESC, a.kode_permintaan DESC";
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
                                    <td width="20" class="text-center"><input type="checkbox" name="id[]" class="checkItem" value="<?php echo $data['kode_permintaan']; ?>"></td>
                                    <td class="text-center"><?php echo $no++; ?></td>
                                    <td class="text-center"><?php echo date('d-m-Y', strtotime($data['tanggal'])); ?></td>
                                    <td><?php echo $data['nama_user']; ?></td>
                                    <td class="text-center"><?php echo $data['total_item']; ?> Barang</td>
                                    <td class="text-center"><span class="badge <?php echo $badge; ?>"><?php echo $data['status']; ?></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-info btn-sm text-white" data-toggle="modal" data-target="#modalDetail<?php echo $data['kode_permintaan']; ?>" title="Detail">
                                            <i class="fas fa-list"></i> Detail
                                        </button>
                                        
                                        <?php if ($data['status'] == 'Pending' && $_SESSION['hak_akses'] == 'Administrator') { ?>
                                            <a href="modules/permintaan/proses_acc.php?kode=<?php echo $data['kode_permintaan']; ?>" onclick="return confirm('Anda yakin ingin meng-ACC permintaan ini? (Stok akan otomatis terpotong)')" class="btn btn-success btn-sm text-white" data-toggle="tooltip" title="ACC">
                                                ACC
                                            </a>
                                        <?php } ?>
                                        
                                        <?php if ($data['status'] == 'ACC') { ?>
                                            <a href="modules/permintaan/cetak_bukti.php?kode=<?php echo $data['kode_permintaan']; ?>" target="_blank" class="btn btn-primary btn-sm text-white" data-toggle="tooltip" title="Cetak Bukti">
                                                <i class="fas fa-print"></i> Cetak
                                            </a>
                                        <?php } ?>
                                        
                                        <!-- Tombol Hapus -->
                                        <a href="modules/permintaan/proses_hapus.php?kode=<?php echo $data['kode_permintaan']; ?>" onclick="return confirm('Anda yakin ingin menghapus data permintaan ini? (Jika sudah ACC, maka stok dan transaksi barang keluar akan dikembalikan otomatis)')" class="btn btn-danger btn-sm text-white" data-toggle="tooltip" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                
                                <?php 
                                // Simpan HTML modal ke variabel
                                ob_start(); 
                                ?>
                                <!-- Modal Detail -->
                                <div class="modal fade" id="modalDetail<?php echo $data['kode_permintaan']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Detail Permintaan: <?php echo $data['kode_permintaan']; ?></h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>ID Barang</th>
                                                            <th>Nama Barang</th>
                                                            <th>Jumlah</th>
                                                            <th>Foto</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $kode = $data['kode_permintaan'];
                                                        $q_detail = mysqli_query($mysqli, "SELECT a.jumlah, b.id_barang, b.nama_barang, b.foto FROM tbl_permintaan a JOIN tbl_barang b ON a.barang = b.id_barang WHERE a.kode_permintaan = '$kode'");
                                                        while($detail = mysqli_fetch_assoc($q_detail)){
                                                            $foto = $detail['foto'] ? $detail['foto'] : 'no_image.png';
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $detail['id_barang']; ?></td>
                                                            <td><?php echo $detail['nama_barang']; ?></td>
                                                            <td><?php echo $detail['jumlah']; ?></td>
                                                            <td><img src="images/<?php echo $foto; ?>" style="height:50px;"></td>
                                                        </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php 
                                $modals .= ob_get_clean(); 
                                ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                </form>
            </div>
        </div>
    </div>
    
    <script type="text/javascript">
        $(document).ready(function() {
            $('#checkAll').click(function() {
                $('.checkItem').prop('checked', this.checked);
            });

            $('.checkItem').click(function() {
                if ($('.checkItem:checked').length == $('.checkItem').length) {
                    $('#checkAll').prop('checked', true);
                } else {
                    $('#checkAll').prop('checked', false);
                }
            });

            $('#btnHapusBanyak').click(function() {
                if ($('.checkItem:checked').length == 0) {
                    alert('Silakan pilih data yang ingin dihapus terlebih dahulu!');
                } else {
                    if (confirm('Anda yakin ingin menghapus ' + $('.checkItem:checked').length + ' data permintaan terpilih? (Jika sudah ACC, maka stok dan transaksi barang keluar akan dikembalikan otomatis)')) {
                        $('#formHapusBanyak').submit();
                    }
                }
            });
        });
    </script>
    
    <!-- Render semua modal di sini, di luar tabel -->
    <?php echo $modals; ?>
<?php } ?>
