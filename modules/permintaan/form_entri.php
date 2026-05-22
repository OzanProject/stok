<?php
// mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser dan hanya dapat dijalankan ketika di include oleh file lain
// jika file diakses secara langsung
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    // alihkan ke halaman error 404
    header('location: 404.html');
}
// jika file di include oleh file lain, tampilkan isi file
else { ?>
    <!-- menampilkan pesan kesalahan -->
    <div id="pesan"></div>

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
            <form action="modules/permintaan/proses_simpan.php" method="post" id="formPermintaan" class="needs-validation" novalidate>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group">
                                <label>Tanggal Pengajuan <span class="text-danger">*</span></label>
                                <input type="text" name="tanggal" class="form-control date-picker" autocomplete="off" value="<?php echo date("d-m-Y"); ?>" required>
                                <div class="invalid-feedback">Tanggal pengajuan tidak boleh kosong.</div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="mt-3 mb-4">

                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group">
                                <label>Barang <span class="text-danger">*</span></label>
                                <select id="data_barang" class="form-control select2-single" autocomplete="off">
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
                            </div>

                            <div class="form-group">
                                <label>Stok Tersedia</label>
                                <div class="input-group">
                                    <input type="text" id="data_stok" class="form-control" readonly>
                                    <div id="data_satuan" class="input-group-append"></div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Jumlah Permintaan <span class="text-danger">*</span></label>
                                <input type="text" id="jumlah" class="form-control" autocomplete="off" onKeyPress="return goodchars(event,'0123456789',this)">
                            </div>
                            
                            <div class="form-group">
                                <button type="button" id="btnTambah" class="btn btn-primary btn-sm btn-round"><i class="fas fa-plus"></i> Tambah ke List</button>
                            </div>
                        </div>

                        <div class="col-md-5 ml-auto">
                            <div class="form-group">
                                <label>Foto Barang</label>
                                <div class="card mt-2 mb-2">
                                    <div class="card-body text-center">
                                        <img style="max-height:150px" id="foto_preview" src="images/no_image.png" class="img-fluid" alt="Foto Barang">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="mt-3 mb-4">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover" id="tabelKeranjang">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No.</th>
                                            <th class="text-center">ID Barang</th>
                                            <th class="text-center">Nama Barang</th>
                                            <th class="text-center">Jumlah</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- isi barang akan ditambahkan lewat javascript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-action">
                    <!-- button simpan data -->
                    <input type="submit" name="simpan" value="Simpan" class="btn btn-secondary btn-round pl-4 pr-4 mr-2" id="btnSimpan">
                    <!-- button kembali ke halaman tampil data -->
                    <a href="?module=permintaan" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            var nomor_urut = 1;

            // Menampilkan data barang dari select box ke textfield
            $('#data_barang').change(function() {
                var id_barang = $('#data_barang').val();

                $.ajax({
                    type: "GET",
                    url: "modules/barang-keluar/get_barang.php", // bisa pakai get_barang punya barang-keluar
                    data: {id_barang: id_barang},
                    dataType: "JSON",
                    success: function(result) {
                        $('#data_stok').val(result.stok);
                        $('#data_satuan').html('<span class="input-group-text">' + result.nama_satuan + '</span>');
                        
                        // Menampilkan foto
                        if(result.foto) {
                            $('#foto_preview').attr('src', 'images/' + result.foto);
                        } else {
                            $('#foto_preview').attr('src', 'images/no_image.png');
                        }
                        
                        // set data untuk tabel
                        $('#data_barang').data('nama_barang', result.nama_barang);
                        
                        $('#jumlah').focus();
                    }
                });
            });

            // Tambah barang ke keranjang
            $('#btnTambah').click(function() {
                var id_barang = $('#data_barang').val();
                var nama_barang = $('#data_barang').data('nama_barang');
                var jumlah = $('#jumlah').val();
                var stok = $('#data_stok').val();

                if (!id_barang) {
                    alert('Pilih barang terlebih dahulu!');
                    return;
                }
                if (!jumlah || jumlah == 0) {
                    alert('Isi jumlah barang!');
                    return;
                }
                if (parseInt(jumlah) > parseInt(stok)) {
                    alert('Stok tidak mencukupi!');
                    return;
                }

                // Cek apakah barang sudah ada di keranjang
                var cek_barang = $('input[name="barang[]"][value="' + id_barang + '"]').length;
                if (cek_barang > 0) {
                    alert('Barang ini sudah ada di keranjang!');
                    return;
                }

                var row = '<tr id="row_' + nomor_urut + '">' +
                            '<td class="text-center">' + nomor_urut + '</td>' +
                            '<td class="text-center">' + id_barang + '<input type="hidden" name="barang[]" value="' + id_barang + '"></td>' +
                            '<td>' + nama_barang + '</td>' +
                            '<td class="text-center">' + jumlah + '<input type="hidden" name="jumlah[]" value="' + jumlah + '"></td>' +
                            '<td class="text-center"><button type="button" class="btn btn-danger btn-sm btn-hapus" data-row="' + nomor_urut + '"><i class="fas fa-trash"></i></button></td>' +
                          '</tr>';
                
                $('#tabelKeranjang tbody').append(row);
                nomor_urut++;

                // Reset form input barang
                $('#data_barang').val('').trigger('change');
                $('#data_stok').val('');
                $('#data_satuan').html('');
                $('#jumlah').val('');
                $('#foto_preview').attr('src', 'images/no_image.png');
            });

            // Hapus barang dari keranjang
            $(document).on('click', '.btn-hapus', function() {
                var row_id = $(this).data('row');
                $('#row_' + row_id).remove();
            });

            // Validasi saat form dikirim
            $('#formPermintaan').submit(function(e) {
                if ($('input[name="barang[]"]').length === 0) {
                    e.preventDefault();
                    $('#pesan').html('<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-times"></span><span data-notify="title" class="text-danger">Gagal!</span> <span data-notify="message">Keranjang masih kosong. Tambahkan barang terlebih dahulu.</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                }
            });
        });
    </script>
<?php } ?>
