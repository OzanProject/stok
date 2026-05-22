<?php
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header('location: 404.html');
} else {
    // menampilkan pesan
    if (isset($_GET['pesan'])) {
        if ($_GET['pesan'] == 1) {
            echo '  <div class="alert alert-notify alert-success alert-dismissible fade show" role="alert">
                        <span data-notify="icon" class="fas fa-check"></span> 
                        <span data-notify="title" class="text-success">Sukses!</span> 
                        <span data-notify="message">Restore Database berhasil. Data telah diperbarui.</span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        } elseif ($_GET['pesan'] == 2) {
            echo '  <div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
                        <span data-notify="icon" class="fas fa-times"></span> 
                        <span data-notify="title" class="text-danger">Gagal!</span> 
                        <span data-notify="message">Terdapat kesalahan saat proses restore. Pastikan file berformat .sql</span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        } elseif ($_GET['pesan'] == 3) {
            echo '  <div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
                        <span data-notify="icon" class="fas fa-times"></span> 
                        <span data-notify="title" class="text-danger">Gagal!</span> 
                        <span data-notify="message">Tidak ada file yang diupload.</span>
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
                    <h4 class="page-title text-white"><i class="fas fa-database mr-2"></i> Backup & Restore Database</h4>
                    <ul class="breadcrumbs">
                        <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
                        <li class="separator"><i class="flaticon-right-arrow"></i></li>
                        <li class="nav-item"><a>Pengaturan</a></li>
                        <li class="separator"><i class="flaticon-right-arrow"></i></li>
                        <li class="nav-item"><a>Backup & Restore</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="row">
            <!-- Panel Backup -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title"><i class="fas fa-download mr-2"></i> Backup Database</div>
                    </div>
                    <div class="card-body">
                        <p>Fitur Backup digunakan untuk mencadangkan (download) seluruh data dan struktur tabel pada database ke dalam format <code>.sql</code>. Lakukan backup secara berkala untuk mencegah kehilangan data.</p>
                        
                        <div class="text-center mt-4">
                            <a href="modules/backup_restore/proses_backup.php" class="btn btn-primary btn-round">
                                <i class="fas fa-cloud-download-alt mr-2"></i> Download Backup Database (.sql)
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel Restore -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title"><i class="fas fa-upload mr-2"></i> Restore Database</div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <strong>Peringatan!</strong> Proses Restore akan menghapus data yang ada saat ini dan menggantinya dengan data dari file backup. Pastikan file backup yang Anda unggah sudah benar.
                        </div>
                        <form action="modules/backup_restore/proses_restore.php" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="file_sql">Upload File Backup (.sql) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control-file" id="file_sql" name="file_sql" accept=".sql" required>
                            </div>
                            <div class="text-center mt-3">
                                <button type="submit" class="btn btn-danger btn-round" onclick="return confirm('PERINGATAN: Semua data saat ini akan ditimpa! Anda yakin ingin melanjutkan proses Restore?');">
                                    <i class="fas fa-cloud-upload-alt mr-2"></i> Restore Sekarang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
