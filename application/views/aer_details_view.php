<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile Resume</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f0f3ff;
    }

    .profile-card {
      border-radius: 10px;
      background: #fff;
      padding: 20px;
    }

    .skills span {
      background-color: #f0f2f5;
      padding: 5px 10px;
      border-radius: 20px;
      margin: 5px;
      display: inline-block;
      font-size: 14px;
    }

    .note-box textarea {
      resize: none;
    }

    .exp-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
    }
  </style>
</head>

<body>

  <div class="container-fluid py-4">
    <div class="row g-4">
      <!-- Left Side -->
      <div class="col-md-5">
        <div class="profile-card text-center">
          <img src="<?php echo ($detail_aer->photo != '') ? 'https://updmember.pii.or.id/assets/uploads/' . $detail_aer->photo : ''; ?>"
            class="rounded-circle  mb-3" width="150px" height="150px" alt="Profile">


          <!-- Profile Left -->
          <h5><?= $detail_aer->firstname ?> <?= $detail_aer->lastname ?></h5>
          <p class="text text-muted">Member Since: <span class="fs-6"><?= date('d/m/Y', strtotime($detail_aer->created))  ?></span></p>

          <div class="card shadow-sm">
            <div class="card-body bg-light">

              <!-- FIRST NAME -->
              <div class="row mb-3 text-start">
                <div class="col-md-4 label">
                  <small class="fw-bold">First name</small>
                </div>
                <div class="col-md-8 text-start">
                  <small>
                    <?= !empty($detail_aer->firstname)
                      ? htmlspecialchars($detail_aer->firstname)
                      : '<span class="text-muted">Belum diisi</span>' ?>
                  </small>
                </div>
              </div>

              <!-- LAST NAME -->
              <div class="row mb-3 text-start">
                <div class="col-md-4 label">
                  <small class="fw-bold">Last name</small>
                </div>
                <div class="col-md-8 text-start">
                  <small>
                    <?= !empty($detail_aer->lastname)
                      ? htmlspecialchars($detail_aer->lastname)
                      : '<span class="text-muted">Belum diisi</span>' ?>
                  </small>
                </div>
              </div>

              <!-- GENDER -->
              <div class="row mb-3 text-start">
                <div class="col-md-4 label">
                  <small class="fw-bold">Gender</small>
                </div>
                <div class="col-md-8 text-start">
                  <small>
                    <?= !empty($detail_aer->gender)
                      ? htmlspecialchars($detail_aer->gender)
                      : '<span class="text-muted">Belum diisi</span>' ?>
                  </small>
                </div>
              </div>

              <!-- Mobile Phone -->
              <div class="row mb-3 text-start">
                <div class="col-md-4 label">
                  <small class="fw-bold">Mobile Phone</small>
                </div>
                <div class="col-md-8 text-start">
                  <small>
                    <?= !empty($detail_aer->mobilephone)
                      ? htmlspecialchars($detail_aer->mobilephone)
                      : '<span class="text-muted">Belum diisi</span>' ?>
                  </small>
                </div>
              </div>

              <!-- ID CARD -->
              <div class="row mb-3 text-start">
                <div class="col-md-4 label">
                  <small class="fw-bold">ID Card</small>
                </div>
                <div class="col-md-8 text-start">
                  <small>
                    <?= !empty($detail_aer->idcard)
                      ? htmlspecialchars($detail_aer->idcard)
                      : '<span class="text-muted">Belum diisi</span>' ?>
                  </small>
                </div>
              </div>

              <!-- VA -->
              <div class="row mb-3 text-start">
                <div class="col-md-4 label">
                  <small class="fw-bold">VA</small>
                </div>
                <div class="col-md-8 text-start">
                  <small>
                    <?= !empty($detail_aer->va)
                      ? htmlspecialchars($detail_aer->va)
                      : '<span class="text-muted">Belum diisi</span>' ?>
                  </small>
                </div>
              </div>

              <!-- DOB -->
              <div class="row mb-3 text-start">
                <div class="col-md-4 label">
                  <small class="fw-bold">Date of Birth</small>
                </div>
                <div class="col-md-8 text-start">
                  <small>
                    <?= !empty($detail_aer->dob)
                      ? ucwords($detail_aer->birthplace) . ', ' . htmlspecialchars(date('d-m-Y', strtotime($detail_aer->dob)))
                      : '<span class="text-muted">-</span>' ?>
                  </small>
                </div>
              </div>

              <!-- WEBSITE -->
              <div class="row mb-3 text-start">
                <div class="col-md-4 label">
                  <small class="fw-bold">Website</small>
                </div>
                <div class="col-md-8 text-start">
                  <small>
                    <?= !empty($detail_aer->website)
                      ? htmlspecialchars($detail_aer->website)
                      : '<span class="text-muted">-</span>' ?>
                  </small>
                </div>
              </div>

              <!-- DESCRIPTION -->
              <div class="row mb-3 text-start">
                <div class="col-md-4 label">
                  <small class="fw-bold">Description</small>
                </div>
                <div class="col-md-8 text-start">
                  <small>
                    <?= !empty($detail_aer->profile_description)
                      ? htmlspecialchars($detail_aer->profile_description)
                      : '<span class="text-muted">-</span>' ?>
                  </small>
                </div>
              </div>

            </div>
          </div>




          <div class="skills text-start">
            <h6>Skills</h6>
            <span>User Interface Designing</span>
            <span>UX</span>
            <span>UI</span>
            <span>Adobe XD</span>
            <span>Mobile Apps</span>
            <span>User Research</span>
            <span>Wireframing</span>
            <span>Information Architecture</span>
          </div>
          <div class="note-box mt-3 text-start">
            <h6>Add Notes</h6>
            <textarea class="form-control mb-2" rows="3" placeholder="Add notes for future reference"></textarea>
            <button class="btn btn-primary w-100">Add Note</button>
          </div>
        </div>
      </div>

      <!---------------------------------------------- RIGHT SIDE --------------------------------------------------->

      <!-- Right Side -->
      <div class="col-md-7">
        <div class="profile-card">
          <!-- Basic Info -->

          <!-- Date Of Birth -->
          <div class="row mb-2">
            <div class="col-5">
              <strong>Date of Birth</strong><br>
            </div>
            <div class="col-7">
              <span>
                <?= !empty($detail_aer->dob)
                  ? ucwords($detail_aer->birthplace) . ', ' . htmlspecialchars(date('d-m-Y', strtotime($detail_aer->dob)))
                  : '<span class="text-muted">Belum diisi</span>' ?>
              </span>
            </div>
          </div>


          <!-- Mobile Phone -->
          <div class="row mb-2">
            <div class="col-5">
              <strong>Mobile Phone</strong><br>
            </div>
            <div class="col-7">
              <span>
                <?= !empty($detail_aer->mobilephone)
                  ?  htmlspecialchars($detail_aer->mobilephone)
                  : '<span class="text-muted">Belum diisi</span>' ?>
              </span>
            </div>
          </div>


          <!-- Email -->
          <div class="row mb-2">
            <div class="col-5">
              <strong>Email</strong><br>
            </div>
            <div class="col-7">
              <span>
                <?= !empty($detail_aer->email)
                  ?  htmlspecialchars($detail_aer->email)
                  : '<span class="text-muted">Belum diisi</span>' ?>
              </span>
            </div>
          </div>


          <!-- VA -->
          <div class="row mb-2">
            <div class="col-5">
              <strong>VA</strong><br>
            </div>
            <div class="col-7">
              <span>
                <?= !empty($detail_aer->va)
                  ?  htmlspecialchars($detail_aer->va)
                  : '<span class="text-muted">Belum diisi</span>' ?>
              </span>
            </div>
          </div>


          <!-- ID CARD -->
          <div class="row mb-2">
            <div class="col-5">
              <strong>ID Card</strong><br>
            </div>
            <div class="col-7">
              <span>
                <?= !empty($detail_aer->idcard)
                  ?  htmlspecialchars($detail_aer->idcard)
                  : '<span class="text-muted">Belum diisi</span>' ?>
              </span>
            </div>
          </div>


          <!-- WEBSITE -->
          <div class="row mb-2">
            <div class="col-5">
              <strong>Website</strong><br>
            </div>
            <div class="col-7">
              <span>
                <?= !empty($detail_aer->website)
                  ?  htmlspecialchars($detail_aer->website)
                  : '<span class="text-muted"></span>' ?>
              </span>
            </div>
          </div>



          <!-- Accordion -->
          <div class="accordion mt-5" id="accordionExample">

            <!-- EDUCATION -->
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#edu">
                  <h4>Pendidikan</h4>
                </button>
              </h2>
              <div id="edu" class="accordion-collapse collapse show">
                <div class="accordion-body">

                  <div class="card shadow-lg">
                    <div class="card-body bg-light">
                      <div class="row mb-3">
                        <div class="col-md-4 label fw-bold ">Institusi/Universitas</div>
                        <div class="col-md-8">
                          <?= !empty($detail_aer->school) ? htmlspecialchars($detail_aer->school) : '<span class="text-muted"></span>' ?>
                        </div>
                      </div>

                      <!-- Tahun -->
                      <div class="row mb-3">
                        <div class="col-md-4 label fw-bold ">Tahun</div>
                        <div class="col-md-8">
                          <?= !empty($detail_aer->startdate . $detail_aer->enddate) ? htmlspecialchars($detail_aer->startdate . ' - ' . $detail_aer->enddate) : '<span class="text-muted"></span>' ?>
                        </div>
                      </div>

                      <!-- Tingkat pendidikan -->
                      <div class="row mb-3">
                        <div class="col-md-4 label fw-bold ">Tingkat Pendidikan</div>
                        <div class="col-md-8">
                          <?= !empty($detail_aer->degree) ? htmlspecialchars($detail_aer->degree) : '<span class="text-muted"></span>' ?>
                        </div>
                      </div>

                      <!-- Fakultas -->
                      <div class="row mb-3">
                        <div class="col-md-4 label fw-bold ">Fakultas</div>
                        <div class="col-md-8">
                          <?= !empty($detail_aer->mayor) ? htmlspecialchars($detail_aer->mayor) : '<span class="text-muted"></span>' ?>
                        </div>
                      </div>

                      <!-- Kejuruan -->
                      <div class="row mb-3">
                        <div class="col-md-4 label fw-bold ">Jurusan/Kejuruan/
                          Nomor Sertifikat</div>
                        <div class="col-md-8">
                          <?= !empty($detail_aer->fieldofstudy) ? htmlspecialchars($detail_aer->fieldofstudy) : '<span class="text-muted"></span>' ?>
                        </div>
                      </div>

                      <!-- IPK/NILAI -->
                      <div class="row mb-3">
                        <div class="col-md-4 label fw-bold ">IPK/Nilai</div>
                        <div class="col-md-8">
                          <?= !empty($detail_aer->score) ? htmlspecialchars($detail_aer->score) : '<span class="text-muted"></span>' ?>
                        </div>
                      </div>

                      <!-- GELAR -->
                      <div class="row mb-3">
                        <div class="col-md-4 label fw-bold ">Gelar</div>
                        <div class="col-md-8">
                          <?= !empty($detail_aer->title) ? htmlspecialchars($detail_aer->title) : '<span class="text-muted"></span>' ?>
                        </div>
                      </div>

                      <!-- Aktivitas dan kegiatan sosial -->
                      <div class="row mb-3">
                        <div class="col-md-4 label fw-bold ">Aktivitas dan kegiatan sosial</div>
                        <div class="col-md-8">
                          <?= !empty($detail_aer->activities) ? htmlspecialchars($detail_aer->activities) : '<span class="text-muted"></span>' ?>
                        </div>
                      </div>

                      <!-- Deskripsi -->
                      <div class="row mb-3">
                        <div class="col-md-4 label fw-bold ">Deskripsi</div>
                        <div class="col-md-8">
                          <?= !empty($detail_aer->description) ? htmlspecialchars($detail_aer->description) : '<span class="text-muted"></span>' ?>
                        </div>
                      </div>

                      <!-- Dokumen pendukung pendidikan/ijazah -->
                      <div class="row mb-3">
                        <div class="col-md-4 label fw-bold">Dokumen Pendukung</div>
                        <div class="col-md-8">
                          <?= '<a target="_blank" href="https://updmember.pii.or.id/assets/uploads/' . $detail_aer->attachment . '">' . $detail_aer->attachment . '</a>'; ?>

                        </div>
                      </div>

                    </div>
                  </div>


                </div>
              </div>
            </div>


            <!-- CERTIFICATION -->
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#cert">
                  Certification
                </button>
              </h2>
              <div id="cert" class="accordion-collapse collapse">
                <div class="accordion-body">Content for Certification...</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


</body>

</html>