<html>

<head>
	<title>Upload Form</title>
	<?php $this->load->view('admin/common/meta_tags'); ?>
	<?php $this->load->view('admin/common/before_head_close'); ?>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>

<body class="skin-blue">
	<?php $this->load->view('admin/common/after_body_open'); ?>
	<?php $this->load->view('admin/common/header'); ?>

	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php $this->load->view('admin/common/left_side'); ?>

		<aside class="right-side">

			<div class="container">
				<div class="row">
					<!-- Tambahkan offset agar form ke tengah -->


					<div class="col-md-8 col-md-offset-2">
						<h3 class="text-center">Upload file</h3>

						<?php
						if (!empty($upload_data)) {
							echo '<div class="alert alert-success" role="alert">
								<p><strong>Sukses!</strong> Your file was successfully uploaded!</p>
								<hr>
								<p class="mb-0">File stored in: <a href="' . site_url('feature_not_implemented_yet') . '">' . @($file_location) . '</a></p>
							</div>';
						} elseif ($error !== '') {
							echo '<div class="alert alert-warning" role="alert">' . @($error) . '</div>';
						}
						?>

						<?php echo form_open_multipart('admin/userprovisioner/upload', ['class' => 'form-horizontal']); ?>

						<div class="form-group">
							<label for="userfile" class="col-sm-3 control-label">File to be uploaded:</label>
							<div class="col-sm-9">
								<input type="file" name="userfile" id="userfile" class="form-control" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-3 control-label">Operation:</label>
							<div class="col-sm-9">
								<div class="radio">
									<label>
										<input type="radio" id="status1" name="status" value="1" checked="checked">
										New file (Throw an error if the same file name exist)
									</label>
								</div>
								<div class="radio">
									<label>
										<input type="radio" id="status2" name="status" value="2">
										Backup & Replace existing
									</label>
								</div>
								<div class="radio">
									<label>
										<input type="radio" id="status3" name="status" value="3">
										Remove/Overwrite existing
									</label>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="targetdir" class="col-sm-3 control-label">Choose target directory:</label>
							<div class="col-sm-9">
								<select name="target_dir" id="targetdir" class="form-control">
									<?php foreach ($target_dirs as $key => $value) {
										echo '<option value="' . $key . '">APP_DIR/' . $value . '</option>';
									} ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label for="reasons" class="col-sm-3 control-label">Choose reason:</label>
							<div class="col-sm-9">
								<select name="reason" id="reasons" class="form-control">
									<?php foreach ($reasons as $key => $value) {
										echo '<option value="' . $key . '">' . $value . '</option>';
									} ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label for="comment" class="col-sm-3 control-label">Comment:</label>
							<div class="col-sm-9">
								<textarea id="comment" name="comment" rows="4" class="form-control"
									placeholder="Berikan keterangan detail kenapa file ini perlu diupload manual oleh Anda. Apakah seharusnya user yang bersangkutan bisa mengupload file ini?"><?php echo @($comment) ?></textarea>
							</div>
						</div>

						<div class="form-group">
							<div class="col-sm-offset-3 col-sm-9">
								<input type="submit" value="Upload" class="btn btn-primary" />
							</div>
						</div>

						</form>
					</div>
				</div>
			</div>


		</aside>
	</div>
	<?php $this->load->view('admin/common/footer'); ?>
</body>

</html>