<?php
// mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser dan hanya dapat dijalankan ketika di include oleh file lain
// jika file diakses secara langsung
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    // alihkan ke halaman error 404
    header('location: 404.html');
}
// jika file di include oleh file lain, tampilkan isi file
else { ?>
    <div class="panel-header bg-secondary-gradient">
        <div class="page-inner py-4">
            <div class="page-header text-white">
                <!-- judul halaman -->
                <h4 class="page-title text-white"><i class="fas fa-file-invoice mr-2"></i> Permintaan Barang</h4>
                <!-- breadcrumbs -->
                <ul class="breadcrumbs">
                    <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
                    <li class="separator"><i class="flaticon-right-arrow"></i></li>
                    <li class="nav-item"><a href="?module=permintaan" class="text-white">Permintaan Barang</a></li>
                    <li class="separator"><i class="flaticon-right-arrow"></i></li>
                    <li class="nav-item"><a>Entri Data</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="card">
            <div class="card-header">
                <!-- judul form -->
                <div class="card-title">Form Pengajuan Permintaan Barang</div>
                <div class="card-category">Form ini digunakan untuk menambah data permintaan barang baru.</div>
            </div>
            <!-- form entri data -->
            <form action="modules/permintaan/proses_simpan.php" method="post" class="needs-validation" novalidate>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group">
                                <label>Tanggal Pengajuan <span class="text-danger">*</span></label>
                                <input type="text" name="tanggal" class="form-control date-picker" autocomplete="off" value="<?php echo date("d-m-Y"); ?>" required>
                                <div class="invalid-feedback">Tanggal pengajuan tidak boleh kosong.</div>
                            </div>

                            <div class="form-group">
                                <label>Barang <span class="text-danger">*</span></label>
                                <select name="barang" id="barang" class="form-control select2-single" autocomplete="off" required>
                                    <option selected disabled value="">-- Pilih Barang --</option>
                                    <?php
                                    // sql statement untuk menampilkan data dari tabel "tbl_barang"
                                    $query_barang = mysqli_query($mysqli, "SELECT id_barang, nama_barang FROM tbl_barang ORDER BY id_barang ASC")
                                                                    or die('Ada kesalahan pada query tampil barang : ' . mysqli_error($mysqli));
                                    // ambil data hasil query
                                    while ($data_barang = mysqli_fetch_assoc($query_barang)) {
                                        echo "<option value='$data_barang[id_barang]'>$data_barang[id_barang] - $data_barang[nama_barang]</option>";
                                    }
                                    ?>
                                </select>
                                <div class="invalid-feedback">Barang tidak boleh kosong.</div>
                            </div>

                            <div class="form-group">
                                <label>Jumlah Permintaan <span class="text-danger">*</span></label>
                                <input type="text" id="jumlah" name="jumlah" class="form-control" autocomplete="off" onKeyPress="return goodchars(event,'0123456789',this)" required>
                                <div class="invalid-feedback">Jumlah permintaan tidak boleh kosong.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-action">
                    <!-- button simpan data -->
                    <input type="submit" name="simpan" value="Simpan" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">
                    <!-- button kembali ke halaman tampil data -->
                    <a href="?module=permintaan" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
                </div>
            </form>
        </div>
    </div>
<?php } ?>
