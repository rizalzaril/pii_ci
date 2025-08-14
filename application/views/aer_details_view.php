<div class="container mt-3">
  <div class="card bg-light">

    <div class="card-header">
      <div class="d-flex justify-content-between mt-3">
        <h5><?= $detail_aer->firstname ?> <?= $detail_aer->lastname ?></h5>
        <p class="text text-dark">Member Since: <span class="fs-6"><?= date('d/m/Y', strtotime($detail_aer->created))  ?></span></p>
      </div>
    </div>

    <div class="card-body">


    </div>


  </div>
</div>
</div>


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

  <div class="container py-4">
    <div class="row g-4">
      <!-- Left Side -->
      <div class="col-md-4">
        <div class="profile-card text-center">
          <img src="<?php echo ($detail_aer->photo != '') ? 'https://updmember.pii.or.id/assets/uploads/' . $detail_aer->photo : ''; ?>"
            class="rounded-circle  mb-3" width="150px" height="150px" alt="Profile">


          <!-- Profile Left -->
          <h5><?= $detail_aer->firstname ?> <?= $detail_aer->lastname ?></h5>
          <p class="text text-muted">Member Since: <span class="fs-6"><?= date('d/m/Y', strtotime($detail_aer->created))  ?></span></p>
          <p class="small">
            Full stack product designer with hands-on experience in solving problems for clients ranging from Real Estate, Hospitality, Hotels, On Demand Healthcare, IT, Services & Social Network among others...
          </p>
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

      <!-- Right Side -->
      <div class="col-md-8">
        <div class="profile-card">
          <!-- Basic Info -->
          <div class="row g-3 mb-3">
            <div class="col-sm-4"><strong>AGE</strong><br>28 years</div>
            <div class="col-sm-4"><strong>YEARS OF EXPERIENCE</strong><br>6 years</div>
            <div class="col-sm-4"><strong>PHONE</strong><br>+91 98123 55679</div>
            <div class="col-sm-4"><strong>CTC</strong><br>12.5 Lc</div>
            <div class="col-sm-4"><strong>LOCATION</strong><br>Ahmedabad, Gujarat</div>
            <div class="col-sm-4"><strong>EMAIL</strong><br>ananyasharma@gmail.com</div>
          </div>
          <div class="mb-4">
            <button class="btn btn-primary me-2">Download Resume</button>
            <button class="btn btn-outline-primary">Send Email</button>
          </div>

          <!-- Experience -->
          <h6>Experience</h6>
          <div class="mb-3 d-flex align-items-center">
            <div class="exp-icon bg-primary me-3">ST</div>
            <div>
              <strong>Infosys</strong><br>
              Product & UI/UX Designer<br>
              <small>Apr 2016 - Present | Pune, India</small>
            </div>
          </div>
          <div class="mb-3 d-flex align-items-center">
            <div class="exp-icon bg-pink me-3">PS</div>
            <div>
              <strong>Pixel Studio</strong><br>
              UI/UX Designer<br>
              <small>Oct 2016 - July 2018 | Bengaluru, India</small>
            </div>
          </div>
          <div class="mb-3 d-flex align-items-center">
            <div class="exp-icon bg-warning me-3">RS</div>
            <div>
              <strong>Rameotion Studio</strong><br>
              Web Designer<br>
              <small>Apr 2015 - July 2016 | Bengaluru, India</small>
            </div>
          </div>

          <!-- Accordion -->
          <div class="accordion" id="accordionExample">

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