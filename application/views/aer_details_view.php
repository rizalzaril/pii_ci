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
					<div class="card mb-3 shadow">
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
					<div class="card mb-3 shadow">
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
					<div class="card mb-3 shadow">
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
											<td colspan="2"><em>Tidak ada pengalaman</em></td>
										</tr>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
					</div>


					<!-- PENDIDIKAN -->
					<div class="card mb-3 shadow">
						<div class="card-body">
							<h3 class="fw-bold text-center">Pendidikan</h3>
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>Tipe Pendidikan</th>
										<th>Institusi / Universitas</th>
										<th>Tahun</th>
										<th>Tingkat Pendidikan</th>
										<th>Fakultas</th>
										<th>Jurusan/Kejuruan/ Nomor Sertifikat</th>
										<th>IPK/Nilai</th>
										<th>Gelar</th>
										<th>Aktivitas dan kegiatan sosial</th>
										<th>Deskripsi</th>
										<th>Dokumen pendukung</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$hasEducation = false; // Untuk cek apakah ada data yang ditampilkan
									if (!empty($detail_aer['educations'])): ?>
										<?php foreach ($detail_aer['educations'] as $exp): ?>
											<?php if (isset($exp['status']) && $exp['status'] == 1): ?>
												<?php $hasEducation = true; ?>
												<tr class="fw-bold text-muted">
													<td class="text-muted"><?= $exp['company'] ?? '-' ?></td>
													<td class="text-muted"><?= $exp['school'] ?? '' ?></td>
													<td class="text-muted"><?= $exp['startdate'] . ' ' . $exp['enddate'] ?? '' ?></td>
													<td class="text-muted"><?= $exp['degree'] ?? '' ?></td>
													<td class="text-muted"><?= $exp['mayor'] ?? '' ?></td>
													<td class="text-muted"><?= $exp['fieldofstudy'] ?? '' ?></td>
													<td class="text-muted"><?= $exp['score'] ?? '' ?></td>
													<td class="text-muted"><?= $exp['title'] ?? '' ?></td>
													<td class="text-muted"><?= $exp['activities'] ?? '' ?></td>
													<td class="text-muted">
														<?php
														$description = $exp['description'] ?? '';
														if (!empty($description)) {
															$items = preg_split('/\d+\.\s*/', $description, -1, PREG_SPLIT_NO_EMPTY);
															if (!empty($items)) {
																echo '<ol>';
																foreach ($items as $item) {
																	echo '<li>' . trim($item) . '</li>';
																}
																echo '</ol>';
															} else {
																echo nl2br(htmlspecialchars($description));
															}
														} else {
															echo '-';
														}
														?>
													</td>
													<td class="text-muted"><?= $exp['document'] ?? '-' ?></td>
												</tr>
											<?php endif; ?>
										<?php endforeach; ?>
									<?php endif; ?>

									<?php if (!$hasEducation): ?>
										<tr>
											<td colspan="10"><em>Tidak ada pendidikan dengan status aktif</em></td>
										</tr>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
					</div>

					<!-- CERTIFICATIONS -->
					<div class="card mb-3 shadow">
						<div class="card-body">
							<h3 class="fw-bold text-center">Sertifikasi Profesional</h3>
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>Nama Sertifikasi </th>
										<th>Otoritas Sertifikasi </th>
										<th>Nomor lisensi </th>
										<th>URL sertifikasi </th>
										<th>Kualifikasi </th>
										<th>Tanggal</th>
										<th>Deskripsi</th>
										<th>Dokumen pendukung</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$hasCert = false; // Untuk cek apakah ada data yang ditampilkan
									if (!empty($detail_aer['certifications'])): ?>
										<?php foreach ($detail_aer['certifications'] as $cert): ?>
											<?php if (isset($cert['status']) && $cert['status'] == 1): ?>
												<?php $hasCert = true; ?>
												<tr class="fw-bold text-muted">
													<td class="text-muted"><?= $cert['cert_name'] ?? '-' ?></td>
													<td class="text-muted"><?= $cert['cert_auth'] ?? '' ?></td>
													<td class="text-muted"><?= $cert['lic_num'] ?? '' ?></td>
													<td class="text-muted"><?= $cert['cert_url'] ?? '' ?></td>
													<td class="text-muted"><?= $cert['cert_title'] ?? '' ?></td>
													<td class="text-muted">
														<?php
														// Tampilkan tanggal mulai
														$startYear  = isset($cert['startyear']) ? $cert['startyear'] : '';
														$startMonth = isset($cert['startmonth']) ? date('M', mktime(0, 0, 0, (int)$cert['startmonth'], 1)) : '';

														echo trim($startMonth . ' ' . $startYear);

														echo ' - ';

														// Tampilkan tanggal akhir atau "Present"
														if (isset($cert['is_present']) && $cert['is_present'] == '1') {
															echo 'Present';
														} else {
															$endYear  = isset($cert['endyear']) ? $cert['endyear'] : '';
															$endMonth = isset($cert['endmonth']) ? date('M', mktime(0, 0, 0, (int)$cert['endmonth'], 1)) : '';
															echo trim($endMonth . ' ' . $endYear);
														}
														?>
													</td>
													<td class="text-muted">
														<?php
														$description = $cert['description'] ?? '';
														if (!empty($description)) {
															$items = preg_split('/\d+\.\s*/', $description, -1, PREG_SPLIT_NO_EMPTY);
															if (!empty($items)) {
																echo '<ol>';
																foreach ($items as $item) {
																	echo '<li>' . trim($item) . '</li>';
																}
																echo '</ol>';
															} else {
																echo nl2br(htmlspecialchars($description));
															}
														} else {
															echo '-';
														}
														?>
													</td>



													<td class="text-muted"><?= $cert['document'] ?? '-' ?></td>
												</tr>
											<?php endif; ?>
										<?php endforeach; ?>
									<?php endif; ?>

									<?php if (!$hasEducation): ?>
										<tr>
											<td colspan="10"><em>Tidak ada pendidikan dengan status aktif</em></td>
										</tr>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
					</div>

				</div>
			</div>
		</div>

	<?php else: ?>
		<div class="alert alert-warning">Data tidak ditemukan.</div>
	<?php endif; ?>

</body>

</html>