<?php $this->load->view('header'); ?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">

      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0">Profil Pengguna</h4>

        </div>

        <div class="card-body">


          <!-- Informasi pribadi -->
          <div class="card">
            <div class="card-header">
              <h5>Informasi pribadi</h5>
            </div>
            <div class="card-body">

              <?= form_open('user_profiles/update_info_pribadi/' . ($profile_data->user_id ?? '')) ?>

              <div class="row mb-3">
                <label for="firstname" class="col-sm-4 col-form-label">Nama depan</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control"
                    id="firstname"
                    name="firstname"
                    value="<?= $profile_data->firstname ?? '' ?>"
                    placeholder="<?= empty($profile_data->firstname) ? 'Belum diisi' : '' ?>">
                </div>
              </div>

              <div class="row mb-3">
                <label for="lastname" class="col-sm-4 col-form-label">Nama belakang</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control"
                    id="lastname"
                    name="lastname"
                    value="<?= $profile_data->lastname ?? '' ?>"
                    placeholder="<?= empty($profile_data->lastname) ? 'Belum diisi' : '' ?>">
                </div>
              </div>

              <div class="row mb-3">
                <label for="gender" class="col-sm-4 col-form-label">Gender</label>
                <div class="col-sm-8">
                  <select name="gender" id="gender" class="form-control">
                    <option value="">-- Pilih Gender --</option>
                    <option value="Laki-laki" <?= (isset($profile_data->gender) && $profile_data->gender == 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="Perempuan" <?= (isset($profile_data->gender) && $profile_data->gender == 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                  </select>
                </div>
              </div>

              <div class="row mb-3">
                <label for="birthplace" class="col-sm-4 col-form-label">Tempat lahir</label>
                <div class="col-sm-8">
                  <input type="text"
                    class="form-control <?= empty($profile_data->birthplace) ? 'text-muted' : '' ?>"
                    id="birthplace"
                    name="birthplace"
                    value="<?= $profile_data->birthplace ?? '' ?>"
                    placeholder="<?= empty($profile_data->birthplace) ? 'Belum diisi' : '' ?>">
                </div>
              </div>

              <div class="row mb-3">
                <label for="dob" class="col-sm-4 col-form-label">Tanggal lahir</label>
                <div class="col-sm-8">
                  <input type="date"
                    class="form-control <?= empty($profile_data->dob) ? 'text-muted' : '' ?>"
                    id="dob"
                    name="dob"
                    value="<?= $profile_data->dob ?? '' ?>">
                </div>
              </div>

              <div class="row mb-3">
                <label for="website" class="col-sm-4 col-form-label">Website</label>
                <div class="col-sm-8">
                  <input type="text"
                    class="form-control <?= empty($profile_data->website) ? 'text-muted' : '' ?>"
                    id="website"
                    name="website"
                    value="<?= $profile_data->website ?? '' ?>"
                    placeholder="<?= empty($profile_data->website) ? 'Belum diisi' : '' ?>">
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-4 col-form-label">Warga Asing</label>
                <div class="col-sm-8 pt-1">
                  <?php if (isset($profile_data->warga_asing)) : ?>
                    <?php if ($profile_data->warga_asing == 0): ?>
                      <div class="badge bg-info text-dark">Tidak</div>
                    <?php else: ?>
                      <div class="badge bg-success">Ya</div>
                    <?php endif; ?>
                  <?php else: ?>
                    <span class="badge bg-dark">Tidak diketahui</span>
                  <?php endif; ?>
                </div>
              </div>

              <button type="submit" class="btn btn-primary float-end">Simpan</button>

              <?= form_close() ?>



            </div>
          </div>









          <!-- Informasi kontak -->
          <div class="card shadow-sm mt-4">
            <div class="card-header">
              <h5>Informasi kontak</h5>
            </div>
            <div class="card-body">
              <div class="row mb-3">
                <label class="col-sm-4 col-form-label">Email</label>
                <div class="col-sm-8">
                  <?= $this->session->userdata('email') ?>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-4 col-form-label">No hp</label>
                <div class="col-sm-8">
                  <?= !empty($profile_data->mobilephone) ? htmlspecialchars($profile_data->mobilephone) : '<span class="text-danger">Belum diisi</span>' ?>
                </div>
              </div>

            </div>
          </div>

          <!-- Informasi pelengkap -->

          <div class="card shadow-sm mt-4">
            <div class="card-header">
              <h5>Informasi surat pelengkap</h5>
            </div>
            <div class="card-body">
              <div class="row mb-3">
                <label class="col-sm-4 col-form-label">Sertifikat legal</label>
                <div class="col-sm-8">
                  <?= !empty($profile_data->sertifikat_legal) ? htmlspecialchars($profile_data->sertifikat_legal) : '<span class="text-danger">Belum diisi</span>' ?>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-4 col-form-label">Tanda bukti</label>
                <div class="col-sm-8">
                  <?= !empty($profile_data->tanda_bukti) ? htmlspecialchars($profile_data->tanda_bukti) : '<span class="text-danger">Belum diisi</span>' ?>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-4 col-form-label">Surat dukungan</label>
                <div class="col-sm-8">
                  <?= !empty($profile_data->surat_dukungan) ? htmlspecialchars($profile_data->surat_dukungan) : '<span class="text-danger">Belum diisi</span>' ?>
                </div>
              </div>


              <div class="row mb-3">
                <label class="col-sm-4 col-form-label">Surat pernyataan</label>
                <div class="col-sm-8">
                  <?= !empty($profile_data->surat_pernyataan) ? htmlspecialchars($profile_data->surat_pernyataan) : '<span class="text-danger">Belum diisi</span>' ?>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-4 col-form-label">Surat ijin domisili</label>
                <div class="col-sm-8">
                  <?= !empty($profile_data->surat_ijin_domisili) ? htmlspecialchars($profile_data->surat_ijin_domisili) : '<span class="text-danger">Belum diisi</span>' ?>
                </div>
              </div>

            </div>
          </div>


          <div class="d-flex justify-content-end gap-2 mt-4">
            <?php if (empty($profile_data->firstname) || empty($profile_data->lastname)) : ?>
              <a href="<?= base_url('users/edit/' . $this->session->userdata('id')) ?>" class="btn btn-warning">
                <i class="fas fa-user-edit"></i> Lengkapi Profil
              </a>
            <?php else : ?>
              <a href="<?= base_url('users/edit/' . $profile_data->id) ?>" class="btn btn-success">
                <i class="fas fa-edit"></i> Update Profil
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<?php $this->load->view('footer'); ?>