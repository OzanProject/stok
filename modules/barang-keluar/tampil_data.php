<?php
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header('location: 404.html');
} else {
    if (isset($_GET['pesan'])) {
        if ($_GET['pesan'] == 1) {
            echo '  <div class="alert alert-notify alert-success alert-dismissible fade show" role="alert">
                        <span data-notify="icon" class="fas fa-check"></span> 
                        <span data-notify="title" class="text-success">Sukses!</span> 
                        <span data-notify="message">Data barang keluar berhasil disimpan.</span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        }
		// jika pesan = 10 (bulk delete)
		elseif ($_GET['pesan'] == 10) {
            $berhasil = $_GET['berhasil'] ?? 0;
            $gagal = $_GET['gagal'] ?? 0;
            $msg = "$berhasil data transaksi berhasil dihapus.";
            if ($gagal > 0) {
                $msg .= " $gagal data transaksi gagal dihapus.";
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
                    <h4 class="page-title text-white"><i class="fas fa-sign-out-alt mr-2"></i> Barang Keluar</h4>
                    <ul class="breadcrumbs">
                        <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
                        <li class="separator"><i class="flaticon-right-arrow"></i></li>
                        <li class="nav-item"><a href="?module=barang_keluar" class="text-white">Barang Keluar</a></li>
                        <li class="separator"><i class="flaticon-right-arrow"></i></li>
                        <li class="nav-item"><a>Data</a></li>
                    </ul>
                </div>
                <div class="ml-md-auto py-2 py-md-0">
                    <a href="?module=form_entri_barang_keluar" class="btn btn-secondary btn-round">
                        <span class="btn-label"><i class="fa fa-plus mr-2"></i></span> Entri Data
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Data Barang Keluar</div>
            </div>
            <div class="card-body">
                <form id="formHapusBanyak" action="modules/barang-keluar/proses_hapus_banyak.php" method="POST">
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
                                <th class="text-center">ID Transaksi</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Total Item</th>
                                <th class="text-center">Pengguna</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $modals = ''; // Variabel untuk menyimpan HTML modal
                            $query = mysqli_query($mysqli, "SELECT id_transaksi, tanggal, user, COUNT(id_keluar) as total_item
                                                            FROM tbl_barang_keluar 
                                                            GROUP BY id_transaksi, tanggal, user
                                                            ORDER BY id_transaksi DESC")
                                                            or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                            while ($data = mysqli_fetch_assoc($query)) { ?>
                                <tr>
                                    <td width="20" class="text-center"><input type="checkbox" name="id[]" class="checkItem" value="<?php echo $data['id_transaksi']; ?>"></td>
                                    <td width="50" class="text-center"><?php echo $no++; ?></td>
                                    <td width="90" class="text-center"><?php echo $data['id_transaksi']; ?></td>
                                    <td width="70" class="text-center"><?php echo date('d-m-Y', strtotime($data['tanggal'])); ?></td>
                                    <td width="100" class="text-center"><?php echo $data['total_item']; ?> Barang</td>
                                    <td width="220"><?php echo $data['user']; ?></td>
                                    <td width="100" class="text-center">
                                        <div>
                                            <button type="button" class="btn btn-info btn-sm text-white" data-toggle="modal" data-target="#modalDetail<?php echo $data['id_transaksi']; ?>" title="Detail">
                                                <i class="fas fa-list"></i> Detail
                                            </button>
                                            <a href="modules/barang-keluar/proses_hapus.php?id=<?php echo $data['id_transaksi']; ?>" onclick="return confirm('Anda yakin ingin menghapus data barang keluar <?php echo $data['id_transaksi']; ?>? (Stok akan dikembalikan)')" class="btn btn-danger btn-sm text-white" data-toggle="tooltip" data-placement="top" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                <?php 
                                ob_start(); 
                                ?>
                                <!-- Modal Detail -->
                                <div class="modal fade" id="modalDetail<?php echo $data['id_transaksi']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Detail Transaksi: <?php echo $data['id_transaksi']; ?></h5>
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
                                                            <th>Satuan</th>
                                                            <th>Jumlah</th>
                                                            <th>Foto</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $id_tk = $data['id_transaksi'];
                                                        $q_detail = mysqli_query($mysqli, "SELECT a.jumlah, b.id_barang, b.nama_barang, b.foto, c.nama_satuan FROM tbl_barang_keluar a JOIN tbl_barang b ON a.barang = b.id_barang JOIN tbl_satuan c ON b.satuan = c.id_satuan WHERE a.id_transaksi = '$id_tk'");
                                                        while($detail = mysqli_fetch_assoc($q_detail)){
                                                            $foto = $detail['foto'] ? $detail['foto'] : 'no_image.png';
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $detail['id_barang']; ?></td>
                                                            <td><?php echo $detail['nama_barang']; ?></td>
                                                            <td><?php echo $detail['nama_satuan']; ?></td>
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
                    if (confirm('Anda yakin ingin menghapus ' + $('.checkItem:checked').length + ' data transaksi terpilih? (Stok barang akan dikembalikan secara otomatis)')) {
                        $('#formHapusBanyak').submit();
                    }
                }
            });
        });
    </script>
    
    <!-- Render modal -->
    <?php echo $modals; ?>
<?php } ?>