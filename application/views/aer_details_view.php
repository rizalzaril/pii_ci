<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Detail AER</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container py-4">

	<h2 class="mb-3">Detail Anggota AER</h2>

	<?php if (!empty($detail_aer)): ?>

		<div class="">

			<!-- Header Detail -->
			<div class="card mb-4 bg-light">
				<div class="card-header bg-light">
					<div class="d-flex justify-content-between align-items-center">
						<h3><?= $detail_aer['nama'] ?></h3>
						<p class="text-muted">Member Since: <?= date('d/m/Y', strtotime($detail_aer['created'])) ?></p>
					</div>
				</div>

				<div class="card-body">
					<!-- PHOTO -->
					<div class="d-flex justify-content-center mb-3">
						<img class="img-thumbnail" width="250"
							src="https://updmember.pii.or.id/assets/uploads/<?= $detail_aer['photo'] ?>"
							alt="">
					</div>

					<!-- PROFILE DETAIL -->
					<div class="card shadow-sm mb-3">
						<div class="card-body">
							<?php
							$profileFields = [
								'First Name' => 'firstname',
								'Last Name' => 'lastname',
								'Gender' => 'gender',
								'Mobile Phone' => 'mobilephone',
								'ID Card' => 'idcard',
								'VA' => 'va',
								'Date of Birth' => function ($d) {
									return $d['birthplace'] . ', ' . date('d-m-Y', strtotime($d['dob']));
								},
								'Website' => 'website',
								'Bersedia Menerima Bahan Publikasi' => function ($d) {
									return (isset($d['is_public']) && $d['is_public'] == '1') ? 'Bersedia data pribadi diserahkan ke PII' : '';
								},
								'Description' => 'description'
							];
							?>
							<?php foreach ($profileFields as $label => $key): ?>
								<div class="row mb-2">
									<div class="col-4">
										<label class="fw-bold"><?= $label ?></label>
									</div>
									<div class="col-8">
										<p class="">
											<?= is_callable($key) ? $key($detail_aer) : ($detail_aer[$key] ?? '-') ?>
										</p>
									</div>
								</div>
							<?php endforeach; ?>



							<?php ?>
						</div>
					</div>

					<!-- CONTACT -->
					<div class="card mb-3">
						<div class="card-body">
							<h5>Phone</h5>
							<hr>
							<?php foreach ($detail_aer['addresses'] as $contact): ?>
								<p><?= $contact['phone'] ?></p>
							<?php endforeach; ?>
							<h5>Email</h5>
							<hr>
							<?php foreach ($detail_aer['addresses'] as $contact): ?>
								<p><?= $contact['email'] ?></p>
							<?php endforeach; ?>
						</div>
					</div>

					<!-- ADDRESSES -->
					<div class="card mb-3">
						<div class="card-body">
							<h5 class="fw-bold">Address</h5>
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>Type</th>
										<th>Address</th>
									</tr>
								</thead>
								<tbody>
									<?php if (!empty($detail_aer['addresses'])): ?>
										<?php foreach ($detail_aer['addresses'] as $addr): ?>
											<tr>
												<td><?= $addr['address_type'] ?? 'Home' ?></td>
												<td>
													<?= $addr['address'] ?? '-' ?><br>
													<?= $addr['city'] ?? '' ?><br>
													<?= $addr['province'] ?? '' ?><br>
													<?= $addr['zipcode'] ?? '' ?>
												</td>
											</tr>
										<?php endforeach; ?>
									<?php else: ?>
										<tr>
											<td colspan="2"><em>Tidak ada data alamat</em></td>
										</tr>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
					</div>


					<!-- EXPERIENCE -->
					<div class="card mb-3">
						<div class="card-body">
							<h3 class="fw-bold text-center">Pengalaman Kerja/Profesional</h3>
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>Perusahaan</th>
										<th>Jabatan/Tugas</th>
										<th>Lokasi</th>
										<th>Periode</th>
										<th>Nama Aktifitas/Kegiatan/Proyek </th>
										<th>Uraian Singkat Tugas dan Tanggung Jawab Profesional </th>
										<th>Dokumen pendukung</th>
									</tr>
								</thead>
								<tbody>
									<?php if (!empty($detail_aer['experiences'])): ?>
										<?php foreach ($detail_aer['experiences'] as $exp): ?>
											<tr class="fw-bold text-muted">

												<td class="text-muted">
													<?= $exp['company'] ?? '-' ?><br>
												</td>
												<td class="text-muted"><?= $exp['title'] ?? '' ?></td>
												<td class="text-muted"><?= $exp['location'] . ', ' . $exp['provinsi'] . ', ' . $exp['negara'] ?? '' ?></td>

												<?php
												$startmonth = isset($exp['startmonth']) ? (int)$exp['startmonth'] : 0;
												$startyear  = $exp['startyear'] ?? '';
												$endmonth   = isset($exp['endmonth']) ? (int)$exp['endmonth'] : 0;
												$endyear    = $exp['endyear'] ?? '';

												$startmonthName = $startmonth > 0 ? date('M', mktime(0, 0, 0, $startmonth, 1)) : '';
												$endmonthName   = $endmonth > 0 ? date('M', mktime(0, 0, 0, $endmonth, 1)) : '';
												?>
												<td class="text-muted">
													<?= trim("$startmonthName $startyear - $endmonthName $endyear") ?>
												</td>



												<td class="text-muted"><?= $exp['actv'] ?? '' ?></td>
												<td class="text-muted">
													<?php
													$description = $exp['description'] ?? '';

													if (!empty($description)) {
														// Pisahkan teks berdasarkan nomor di awal (1. 2. 3. ...)
														$items = preg_split('/\d+\.\s*/', $description, -1, PREG_SPLIT_NO_EMPTY);

														if (!empty($items)) {
															echo '<ol>'; // Daftar bernomor otomatis
															foreach ($items as $item) {
																echo '<li>' . trim($item) . '</li>';
															}
															echo '</ol>';
														} else {
															// Jika tidak ada nomor, tampilkan teks utuh
															echo nl2br(htmlspecialchars($description));
														}
													} else {
														echo '-'; // Default jika kosong
													}
													?>
												</td>

												<td class="text-muted"> <?= $exp['title'] ?? '' ?></td>

											</tr>
										<?php endforeach; ?>
									<?php else: ?>
										<tr>
											<td colspan="2"><em>Tidak ada data alamat</em></td>
										</tr>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
					</div>




					<h5>Pengalaman Kerja</h5>
					<?php if (!empty($detail_aer['experiences'])): ?>
						<ul class="list-group mb-3">
							<?php foreach ($detail_aer['experiences'] as $exp): ?>
								<li class="list-group-item">
									<strong><?= $exp['company'] ?? '-' ?></strong> - <?= $exp['position'] ?? '-' ?><br>
									<small><?= $exp['start_date'] ?? '' ?> s/d <?= $exp['end_date'] ?? '' ?></small>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php else: ?>
						<p><em>Tidak ada data pengalaman</em></p>
					<?php endif; ?>

					<!-- EDUCATION -->
					<h5>Pendidikan</h5>
					<?php if (!empty($detail_aer['educations'])): ?>
						<ul class="list-group mb-3">
							<?php
							$ada = false;
							foreach ($detail_aer['educations'] as $edu):
								if ($edu['status'] == 1):
									$ada = true;
							?>
									<li class="list-group-item">
										<strong><?= $edu['school'] ?? '-' ?></strong> - <?= $edu['degree'] ?? '-' ?><br>
										<small><?= $edu['start_year'] ?? '' ?> s/d <?= $edu['end_year'] ?? '' ?></small>
									</li>
							<?php
								endif;
							endforeach;
							?>
							<?php if (!$ada): ?>
								<p><em>Tidak ada data pendidikan aktif</em></p>
							<?php endif; ?>
						</ul>
					<?php else: ?>
						<p><em>Tidak ada data pendidikan</em></p>
					<?php endif; ?>

					<!-- CERTIFICATIONS -->
					<h5>Sertifikat</h5>
					<?php if (!empty($detail_aer['certifications'])): ?>
						<ul class="list-group mb-3">
							<?php foreach ($detail_aer['certifications'] as $cert): ?>
								<li class="list-group-item">
									<strong><?= $cert['title'] ?? '-' ?></strong><br>
									<small><?= $cert['issuer'] ?? '' ?> (<?= $cert['year'] ?? '' ?>)</small>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php else: ?>
						<p><em>Tidak ada data sertifikat</em></p>
					<?php endif; ?>

				</div>
			</div>
		</div>

	<?php else: ?>
		<div class="alert alert-warning">Data tidak ditemukan.</div>
	<?php endif; ?>

</body>

</html>