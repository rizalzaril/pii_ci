<div class="container mt-5">

	<?php if ($this->session->flashdata('success_update') || $this->session->flashdata('success_delete')): ?>
		<div class="alert alert-success alert-dismissible fade show" role="alert">
			<strong>Sukses!</strong> <?= $this->session->flashdata('success_save'); ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	<?php endif; ?>

	<a href="<?= base_url('/dashboard/add_data') ?>" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add data </a>


	<!-- MODAL IMPORT DARI EXCEL/CSV -->

	<!-- Button trigger modal -->
	<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal">
		<i class="fas fa-file-excel"></i> Import XLSX/CSV
	</button>

	<!-- Modal -->
	<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog ">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<?= form_open_multipart('import/import_proccess/') ?>

					<!-- Form file upload XLSX -->
					<div class="mb-3">
						<label for="" class="form-label fw-bold">Nama File XLSX/CSV*</label>
						<input type="file" name="excel_file" class="form-control shadow-sm" accept=".xls,.xlsx,.csv," required>
					</div>

					<div class="mb-3">
						<label for="kodkel" class="form-label fw-bold">Kode Kelompok*</label>
						<select name="kodkel" id="kodkel" class="form-control form-select shadow-sm">
							<?php foreach ($list_kelompok as $kodkel) : ?>
								<option value="<?= $kodkel->id ?>"><?= $kodkel->id ?>. <?= $kodkel->name ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="mb-3">
						<label for="passwordImport" class="form-label fw-bold">Password*</label>
						<input type="password" class="form-control shadow-sm" placeholder="Masukkan Password Default untuk Aplikan" required>
					</div>
					<button type="submit" class="btn btn-success">Import</button>
					<?= form_close() ?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary">Save changes</button>
				</div>
			</div>
		</div>
	</div>


	<h3>Daftar User</h3>
	<table id="table_users" class="table table-sm table-striped">
		<thead>
			<tr>
				<th>No</th>
				<th>Username</th>
				<th>Email</th>
				<th>Status</th>
				<th>Aksi</th>
			</tr>
		</thead>
	</table>

</div>
<?php $this->load->view('footer'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

<?php if ($this->session->flashdata('success_delete') || $this->session->flashdata('success_update')): ?>
	<script>
		//alert delete

		Swal.fire({
			icon: 'success',
			title: 'Berhasil!',
			text: '<?= $this->session->flashdata("success_delete") ?>',
			showConfirmButton: false,
			timer: 2000
		});


		Swal.fire({
			icon: 'success',
			title: 'Berhasil!',
			text: '<?= $this->session->flashdata("success_update") ?>',
			showConfirmButton: false,
			timer: 2000
		});
	</script>
<?php endif; ?>


<!-- Confirm delete -->
<script>
	$(document).ready(function() {
		$('.btn-delete').on('click', function(e) {
			e.preventDefault();
			const id = $(this).data('id');
			const kode = $(this).data('kode');

			Swal.fire({
				title: 'Yakin hapus data ini?',
				text: "Data dengan kode " + '' +
					kode + " yang dihapus tidak bisa dikembalikan!",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Ya, hapus!',
				cancelButtonText: 'Batal',
				reverseButtons: true
			}).then((result) => {
				if (result.isConfirmed) {
					window.location.href = '<?= base_url("/dashboard/delete_data/") ?>' + id;
				}
			});
		});
	});
</script>



<!-- Inisialisasi DataTable table users -->
<script>
	new DataTable('#table_users', {
		processing: true,
		serverSide: true,
		ajax: {
			url: "<?= base_url('users/get_users') ?>",
			type: "GET"
		}
	});
</script>