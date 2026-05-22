<?php
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header('location: 404.html');
} else { ?>
    <div id="pesan"></div>

    <div class="panel-header bg-secondary-gradient">
        <div class="page-inner py-4">
            <div class="page-header text-white">
                <h4 class="page-title text-white"><i class="fas fa-sign-in-alt mr-2"></i> Barang Masuk</h4>
                <ul class="breadcrumbs">
                    <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
                    <li class="separator"><i class="flaticon-right-arrow"></i></li>
                    <li class="nav-item"><a href="?module=barang_masuk" class="text-white">Barang Masuk</a></li>
                    <li class="separator"><i class="flaticon-right-arrow"></i></li>
                    <li class="nav-item"><a>Entri</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Entri Data Barang Masuk</div>
            </div>
            <form action="modules/barang-masuk/proses_simpan.php" id="formBarangMasuk" method="post" class="needs-validation" novalidate>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group">
                                <?php
                                $query = mysqli_query($mysqli, "SELECT RIGHT(id_transaksi,7) as nomor FROM tbl_barang_masuk ORDER BY id_transaksi DESC LIMIT 1")
                                                                or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                                $rows = mysqli_num_rows($query);
                                if ($rows <> 0) {
                                    $data = mysqli_fetch_assoc($query);
                                    $nomor_urut = $data['nomor'] + 1;
                                } else {
                                    $nomor_urut = 1;
                                }
                                $id_transaksi = "TM-" . str_pad($nomor_urut, 7, "0", STR_PAD_LEFT);
                                ?>
                                <label>ID Transaksi <span class="text-danger">*</span></label>
                                <input type="text" name="id_transaksi" class="form-control" value="<?php echo $id_transaksi; ?>" readonly>
                            </div>
                        </div>

                        <div class="col-md-5 ml-auto">
                            <div class="form-group">
                                <label>Tanggal <span class="text-danger">*</span></label>
                                <input type="text" name="tanggal" class="form-control date-picker" autocomplete="off" value="<?php echo date("d-m-Y"); ?>" required>
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
                                    $query_barang = mysqli_query($mysqli, "SELECT id_barang, nama_barang FROM tbl_barang ORDER BY id_barang ASC")
                                                                           or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                                    while ($data_barang = mysqli_fetch_assoc($query_barang)) {
                                        echo "<option value='$data_barang[id_barang]'>$data_barang[id_barang] - $data_barang[nama_barang]</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Stok Saat Ini</label>
                                <div class="input-group">
                                    <input type="text" id="data_stok" class="form-control" readonly>
                                    <div id="data_satuan" class="input-group-append"></div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Jumlah Masuk <span class="text-danger">*</span></label>
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
                                            <th class="text-center">Jumlah Masuk</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Javascript list -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-action">
                    <input type="submit" name="simpan" value="Simpan" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">
                    <a href="?module=barang_masuk" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            var nomor_urut = 1;
            $('#data_barang').change(function() {
                var id_barang = $('#data_barang').val();
                $.ajax({
                    type: "GET",
                    url: "modules/barang-masuk/get_barang.php",
                    data: {id_barang: id_barang},
                    dataType: "JSON",
                    success: function(result) {
                        $('#data_stok').val(result.stok);
                        $('#data_satuan').html('<span class="input-group-text">' + result.nama_satuan + '</span>');
                        if(result.foto) {
                            $('#foto_preview').attr('src', 'images/' + result.foto);
                        } else {
                            $('#foto_preview').attr('src', 'images/no_image.png');
                        }
                        $('#data_barang').data('nama_barang', result.nama_barang);
                        $('#jumlah').focus();
                    }
                });
            });

            $('#btnTambah').click(function() {
                var id_barang = $('#data_barang').val();
                var nama_barang = $('#data_barang').data('nama_barang');
                var jumlah = $('#jumlah').val();

                if (!id_barang) {
                    alert('Pilih barang terlebih dahulu!');
                    return;
                }
                if (!jumlah || jumlah == 0) {
                    alert('Isi jumlah masuk!');
                    return;
                }
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
                $('#data_barang').val('').trigger('change');
                $('#data_stok').val('');
                $('#data_satuan').html('');
                $('#jumlah').val('');
                $('#foto_preview').attr('src', 'images/no_image.png');
            });

            $(document).on('click', '.btn-hapus', function() {
                var row_id = $(this).data('row');
                $('#row_' + row_id).remove();
            });

            $('#formBarangMasuk').submit(function(e) {
                if ($('input[name="barang[]"]').length === 0) {
                    e.preventDefault();
                    $('#pesan').html('<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-times"></span><span data-notify="title" class="text-danger">Gagal!</span> <span data-notify="message">Keranjang masih kosong.</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                }
            });
        });
    </script>
<?php } ?>