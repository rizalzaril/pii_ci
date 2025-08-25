<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">

	<?php $this->load->view('admin/common/meta_tags'); ?>
	<?php $this->load->view('admin/common/before_head_close'); ?>

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />


	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap.css') ?>" />
	<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.js') ?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/bootstrap.js') ?>"></script>

	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="<?php echo base_url('assets/js/jquery-3.1.1.min.js') ?>" type="text/javascript"></script>


	<style type="text/css">
		.table thead th {
			background-color: #ffae00 !important;
			/* Orange */
			color: #000 !important;
			/* Teks hitam */
			font-weight: bold;
			/* Tebal */
			text-align: center;
			vertical-align: middle;
		}

		.awesome_style {
			font-size: 16px;
		}
	</style>

	<link rel="stylesheet" type="text/css" href="/assets/css/style.css" />
	<script src="/assets/js/jquery.js" type="text/javascript"></script>

	<link rel="stylesheet" type="text/css" href="/assets/css/datatable.css" />
	<script src="/assets/js/jquery.dataTables.js" type="text/javascript"></script>

	<?php $this->load->view('admin/common/after_body_open'); ?>
	<?php $this->load->view('admin/common/header'); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php $this->load->view('admin/common/left_side'); ?>

		<?php if ($selesai == 'y') { ?>
			<div class="container">
				<div class="alert alert-success fade in">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					Data telah disimpan....
				</div>
			</div>
		<?php } ?>

		<!-- Right side column -->
		<aside class="right-side">
			<section class="content-header">
				<h1> ASEAN Eng Management </h1>
				<ol class="breadcrumb">
					<li><a href="<?php echo base_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
					<li class="active">Manage Members</li>
				</ol>
			</section>

			<script type="text/javascript">
				$(document).ready(function() {
					$('#datatables').dataTable({
						"sScrollY": "450px",
						"sScrollX": "100%",
						"bScrollCollapse": true,
						"bPaginate": true,
						"bJQueryUI": true
					});
				});

				function load_upload_skip_view(id) {
					var idd = id;
					var id_proses = "Simpan";

					$.ajax({
						url: '<?php echo base_url('admin/aer/get_aer_by_id'); ?>',
						dataType: "html",
						type: "POST",
						data: {
							id: idd
						},
						success: function(jsonObject) {
							var x = JSON.parse(jsonObject);
							$('#id_id').val(x.id);
							$('#id_kta').val(x.kta);
							$('#id_noaer').val(x.no_aer);
							$('#id_nama').val(x.nama);
							$('#id_grade').val(x.grade);
							$('#id_doi').val(x.doi);
							$('#id_url').val(x.url_aer);
						}
					});

					$('#id_proses').val(id_proses);
					$('.modal-title').html('Ubah Data ASEAN Eng');
					$('#quick_upload_skip').modal('show');
				}

				function load_tambah_data() {
					$('#id_id').val('');
					$('#id_kta').val('');
					$('#id_noaer').val('');
					$('#id_nama').val('');
					$('#id_grade').val('');
					$('#id_pros').val("Simpan");
					$('.modal-title').html('Tambah Data ASEAN Eng');
					$('#myModal').modal('show');
				}
			</script>

</head>

<body class="skin-blue">

	<div class="wrapper row-offcanvas row-offcanvas-left">
		<main class="main">
			<div class="content">

				<div class="">
					<div class="text-right" style="margin-bottom:10px;">
						<a onclick="load_tambah_data()" href="#" data-target="#myModal" data-toggle="modal" class="btn btn-primary btn-xs">
							<i class="fa fa-plus"></i> Tambah Data
						</a>
					</div>

					<table id="datatables" class="table table-bordered table-striped table-hover table-condensed">
						<thead>
							<tr>
								<th>#</th>
								<th>NO. AER</th>
								<th>NAMA</th>
								<th>GRADE</th>
								<th>NO KTA</th>
								<th>DOI</th>
								<th>URL FILE</th>
								<th>ACTION</th>
							</tr>
						</thead>
						<tbody>
							<?php
							if (isset($list_aer) && !empty($list_aer)) {
								$no = 1;
								foreach ($list_aer as $isinya) {

									echo '<tr>';
									echo '<td class="text-center">' . $no++ . '</td>';
									echo '<td>' . $isinya->no_aer . '</td>';
									echo '<td><a href="' . base_url('admin/aer/detail_aer/' . $isinya->kta) . '"><h5>' . $isinya->nama . '</h5></a></td>';
									echo '<td>' . $isinya->grade . '</td>';
									echo '<td>' . $isinya->kta . '</td>';
									echo '<td>' . $isinya->doi . '</td>';

									// Kolom URL FILE
									echo '<td>';
									if (!empty($isinya->url_aer)) {
										echo '<a href="' . $isinya->url_aer . '" target="_blank" class="btn btn-info btn-xs">
									<i class="fa fa-file"></i> Lihat File
								  </a>';
									} else {
										echo '<span class="text-danger">File tidak tersedia</span>';
									}
									echo '</td>';



									// Kolom ACTION
									echo '<td class="text-center">
								<a onclick="load_upload_skip_view(' . $isinya->id . ')" 
								   href="#" 
								   data-target="#quick_upload_skip" 
								   data-toggle="modal" 
								   class="btn btn-primary btn-xs">
								   <i class="fa fa-edit"></i>Edit
								</a>
							  </td>';
									echo '</tr>';
								}
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</main>
	</div>

	<!-- Modal Update Data -->
	<div class="modal fade" id="quick_upload_skip" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title">Ubah Data ASEAN Eng.</h3>
				</div>
				<div class="modal-body">
					<form id="form_aer" class="form-horizontal" action="<?= base_url('/admin/aer/update_aer') ?>" method="post">

						<div class="form-group">
							<label for="id_noaer" class="col-sm-3 control-label">Nomor</label>
							<div class="col-sm-9">
								<input type="text" name="id_noaer" id="id_noaer" class="form-control" placeholder="Masukkan Nomor">
							</div>
						</div>

						<div class="form-group">
							<label for="id_kta" class="col-sm-3 control-label">Nomor KTA</label>
							<div class="col-sm-9">
								<input type="text" name="id_kta" id="id_kta" class="form-control" placeholder="Masukkan Nomor KTA">
							</div>
						</div>

						<div class="form-group">
							<label for="id_grade" class="col-sm-3 control-label">Grade</label>
							<div class="col-sm-9">
								<input type="text" name="id_grade" id="id_grade" class="form-control" placeholder="Masukkan Grade">
							</div>
						</div>

						<div class="form-group">
							<label for="id_nama" class="col-sm-3 control-label">Nama</label>
							<div class="col-sm-9">
								<input type="text" name="id_nama" id="id_nama" class="form-control" placeholder="Masukkan Nama">
							</div>
						</div>

						<div class="form-group">
							<label for="id_doi" class="col-sm-3 control-label">DOI</label>
							<div class="col-sm-9">
								<input type="text" name="id_doi" id="id_doi" class="form-control" placeholder="Masukkan DOI">
							</div>
						</div>

						<div class="form-group">
							<label for="id_url" class="col-sm-3 control-label">URL</label>
							<div class="col-sm-9">
								<input type="text" name="id_url" id="id_url" class="form-control" placeholder="Masukkan URL">
							</div>
						</div>

						<input type="hidden" name="id_id" id="id_id" />

						<div class="modal-footer">
							<button type="submit" class="btn btn-success" name="id_proses" id="id_proses">
								<i class="glyphicon glyphicon-ok"></i> Simpan
							</button>
							<button type="button" class="btn btn-default" data-dismiss="modal">
								<i class="glyphicon glyphicon-remove"></i> Batal
							</button>
						</div>

					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Tambah Data -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title">Tambah Data ASEAN Eng.</h3>
				</div>
				<div class="modal-body">
					<form id="form_aer" class="form-horizontal" action="<?= base_url('/admin/aer/tambah_aer') ?>" method="post">

						<div class="form-group">
							<label for="id_noaer" class="col-sm-3 control-label">Nomor</label>
							<div class="col-sm-9">
								<input type="text" name="id_noaer" id="id_noaer" class="form-control" placeholder="Masukkan Nomor">
							</div>
						</div>

						<div class="form-group">
							<label for="id_kta" class="col-sm-3 control-label">Nomor KTA</label>
							<div class="col-sm-9">
								<input type="text" name="id_kta" id="id_kta" class="form-control" placeholder="Masukkan Nomor KTA">
							</div>
						</div>

						<div class="form-group">
							<label for="id_grade" class="col-sm-3 control-label">Grade</label>
							<div class="col-sm-9">
								<input type="text" name="id_grade" id="id_grade" class="form-control" placeholder="Masukkan Grade">
							</div>
						</div>

						<div class="form-group">
							<label for="id_nama" class="col-sm-3 control-label">Nama</label>
							<div class="col-sm-9">
								<input type="text" name="id_nama" id="id_nama" class="form-control" placeholder="Masukkan Nama">
							</div>
						</div>

						<input type="hidden" name="id_id" id="id_id">

						<div class="modal-footer">
							<button type="submit" class="btn btn-success" name="id_pros" id="id_pros">
								<i class="glyphicon glyphicon-ok"></i> Simpan
							</button>
							<button type="button" class="btn btn-default" data-dismiss="modal">
								<i class="glyphicon glyphicon-remove"></i> Batal
							</button>
						</div>

					</form>

				</div>
			</div>
		</div>
	</div>

</body>

</html>