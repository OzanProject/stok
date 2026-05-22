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
        // jika pesan = 1
        if ($_GET['pesan'] == 1) {
            // tampilkan pesan sukses simpan data
            echo '  <div class="alert alert-notify alert-success alert-dismissible fade show" role="alert">
                        <span data-notify="icon" class="fas fa-check"></span> 
                        <span data-notify="title" class="text-success">Sukses!</span> 
                        <span data-notify="message">Data lokasi berhasil disimpan.</span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        }
        // jika pesan = 2
        elseif ($_GET['pesan'] == 2) {
            // tampilkan pesan sukses ubah data
            echo '  <div class="alert alert-notify alert-success alert-dismissible fade show" role="alert">
                        <span data-notify="icon" class="fas fa-check"></span> 
                        <span data-notify="title" class="text-success">Sukses!</span> 
                        <span data-notify="message">Data lokasi berhasil diubah.</span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        }
        // jika pesan = 3
        elseif ($_GET['pesan'] == 3) {
            // tampilkan pesan sukses hapus data
            echo '  <div class="alert alert-notify alert-success alert-dismissible fade show" role="alert">
                        <span data-notify="icon" class="fas fa-check"></span> 
                        <span data-notify="title" class="text-success">Sukses!</span> 
                        <span data-notify="message">Data lokasi berhasil dihapus.</span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        }
        // jika pesan = 4
        elseif ($_GET['pesan'] == 4) {
            // ambil data GET dari proses simpan/ubah
            $satuan = $_GET['satuan'];
            // tampilkan pesan gagal simpan data
            echo '  <div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
                        <span data-notify="icon" class="fas fa-times"></span> 
                        <span data-notify="title" class="text-danger">Gagal!</span> 
                        <span data-notify="message">Lokasi <strong>' . $satuan . '</strong> sudah ada. Silahkan ganti nama lokasi yang Anda masukan.</span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        }
        // jika pesan = 5
        elseif ($_GET['pesan'] == 5) {
            // tampilkan pesan gagal hapus data
            echo '  <div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
                        <span data-notify="icon" class="fas fa-times"></span> 
                        <span data-notify="title" class="text-danger">Gagal!</span> 
                        <span data-notify="message">Data lokasi tidak bisa dihapus karena sudah tercatat pada Data Barang.</span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        }
		// jika pesan = 10 (bulk delete)
		elseif ($_GET['pesan'] == 10) {
            $berhasil = $_GET['berhasil'] ?? 0;
            $gagal = $_GET['gagal'] ?? 0;
            $msg = "$berhasil data berhasil dihapus.";
            if ($gagal > 0) {
                $msg .= " $gagal data gagal dihapus karena sudah tercatat pada Data Barang.";
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
                    <!-- judul halaman -->
                    <h4 class="page-title text-white"><i class="fas fa-clone mr-2"></i> Lokasi</h4>
                    <!-- breadcrumbs -->
                    <ul class="breadcrumbs">
                        <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
                        <li class="separator"><i class="flaticon-right-arrow"></i></li>
                        <li class="nav-item"><a href="?module=satuan" class="text-white">Lokasi</a></li>
                        <li class="separator"><i class="flaticon-right-arrow"></i></li>
                        <li class="nav-item"><a>Data</a></li>
                    </ul>
                </div>
                <div class="ml-md-auto py-2 py-md-0">
                    <!-- button entri data -->
                    <a href="?module=form_entri_satuan" class="btn btn-secondary btn-round mr-2">
                        <span class="btn-label"><i class="fa fa-plus mr-2"></i></span> Entri Data
                    </a>
                    <!-- button export data -->
                    <a href="modules/satuan/export.php" class="btn btn-success btn-round">
                        <span class="btn-label"><i class="fa fa-file-excel mr-2"></i></span> Export
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="card">
            <div class="card-header">
                <!-- judul tabel -->
                <div class="card-title">Data Lokasi</div>
            </div>
            <div class="card-body">
                <form id="formHapusBanyak" action="modules/satuan/proses_hapus_banyak.php" method="POST">
                    <div class="mb-3">
                        <button type="button" id="btnHapusBanyak" class="btn btn-danger btn-round btn-sm">
                            <i class="fas fa-trash mr-2"></i> Hapus Terpilih
                        </button>
                    </div>
                <div class="table-responsive">
                    <!-- tabel untuk menampilkan data dari database -->
                    <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="20" class="text-center" data-orderable="false"><input type="checkbox" id="checkAll"></th>
                                <th class="text-center">No.</th>
                                <th class="text-center">Lokasi</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // variabel untuk nomor urut tabel
                            $no = 1;
                            // sql statement untuk menampilkan data dari tabel "tbl_satuan"
                            $query = mysqli_query($mysqli, "SELECT * FROM tbl_satuan ORDER BY id_satuan DESC")
                                                            or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                            // ambil data hasil query
                            while ($data = mysqli_fetch_assoc($query)) { ?>
                                <!-- tampilkan data -->
                                <tr>
                                    <td width="20" class="text-center"><input type="checkbox" name="id[]" class="checkItem" value="<?php echo $data['id_satuan']; ?>"></td>
                                    <td width="30" class="text-center"><?php echo $no++; ?></td>
                                    <td width="300"><?php echo $data['nama_satuan']; ?></td>
                                    <td width="70" class="text-center">
                                        <div>
                                            <!-- button ubah data -->
                                            <a href="?module=form_ubah_satuan&id=<?php echo $data['id_satuan']; ?>" class="btn btn-icon btn-round btn-secondary btn-sm mr-md-1" data-toggle="tooltip" data-placement="top" title="Ubah">
                                                <i class="fas fa-pencil-alt fa-sm"></i>
                                            </a>
                                            <!-- button hapus data -->
                                            <a href="modules/satuan/proses_hapus.php?id=<?php echo $data['id_satuan']; ?>" onclick="return confirm('Anda yakin ingin menghapus data lokasi <?php echo $data['nama_satuan']; ?>?')" class="btn btn-icon btn-round btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Hapus">
                                                <i class="fas fa-trash fa-sm"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
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
                    if (confirm('Anda yakin ingin menghapus ' + $('.checkItem:checked').length + ' data terpilih?')) {
                        $('#formHapusBanyak').submit();
                    }
                }
            });
        });
    </script>
<?php } ?>