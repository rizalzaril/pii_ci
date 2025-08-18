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

		<!-- Address -->
		<h5>Address</h5>
		<?php if (!empty($detail_aer['addresses'])): ?>
			<?php foreach ($detail_aer['addresses'] as $addr): ?>
				<div class="card mb-3">
					<div class="card-body">
						<p><strong>Type:</strong> <?= $addr['address_type'] ?? 'Home' ?></p>
						<p><strong>Street:</strong> <?= $addr['address'] ?? '-' ?></p>
						<p><strong>City:</strong> <?= $addr['city'] ?? '-' ?></p>
						<p><strong>Province:</strong> <?= $addr['province'] ?? '-' ?></p>
						<p><strong>Postal Code:</strong> <?= $addr['postal_code'] ?? '-' ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		<?php else: ?>
			<p><em>Tidak ada data alamat</em></p>
		<?php endif; ?>

		<div class="card mb-3">
			<div class="card-body">
				<h4><?= $detail_aer['nama'] ?? '-' ?> (KTA: <?= $detail_aer['kta'] ?? '-' ?>)</h4>
				<p><strong>Email:</strong> <?= $detail_aer['email'] ?? '-' ?></p>
				<p><strong>Alamat:</strong> <?= $detail_aer['address'] ?? '-' ?></p>
			</div>
		</div>

		<!-- Profile -->
		<h5>Profile</h5>
		<table class="table table-bordered">
			<tr>
				<th>Nama</th>
				<td><?= ($detail_aer['firstname'] ?? '') . ' ' . ($detail_aer['lastname'] ?? '') ?></td>
			</tr>
			<tr>
				<th>Gender</th>
				<td><?= $detail_aer['gender'] ?? '-' ?></td>
			</tr>
			<tr>
				<th>Mobile</th>
				<td><?= $detail_aer['mobilephone'] ?? '-' ?></td>
			</tr>
			<tr>
				<th>Website</th>
				<td><?= $detail_aer['website'] ?? '-' ?></td>
			</tr>
		</table>

		<!-- Experience -->
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

		<!-- Education -->
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


		<!-- Certifications -->
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

	<?php else: ?>
		<div class="alert alert-warning">Data tidak ditemukan.</div>
	<?php endif; ?>

</body>

</html>