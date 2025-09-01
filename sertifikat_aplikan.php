<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Data Sertifikat</title>
  <?php $this->load->view('admin/common/meta_tags'); ?>
  <?php $this->load->view('admin/common/before_head_close'); ?>

  <!-- Bootstrap 3 CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="shortcut icon" href="<?php echo base_url(); ?>/assets/images/favicon_16.png">

  <style>
    .panel-body {
      overflow-x: auto;
    }

    .table th,
    .table td {
      vertical-align: middle !important;
    }
  </style>
</head>

<body class="skin-blue">
  <?php $this->load->view('member/common/after_body_open'); ?>
  <?php $this->load->view('member/common/header'); ?>

  <div class="wrapper row-offcanvas row-offcanvas-left">
    <?php $this->load->view('member/common/left_side'); ?>

    <!-- Right side column -->
    <div class="container">
      <aside class="right-side">
        <section class="content-header">
          <h1 class="text-primary">
            <i class="glyphicon glyphicon-file"></i> DATA SERTIFIKAT
          </h1>
        </section>

        <div class="panel panel-primary">
          <div class="panel-heading">
            <h3 class="panel-title"><i class="glyphicon glyphicon-user"></i> Informasi Anggota</h3>
          </div>
          <div class="panel-body">
            <div class="row">
              <div class="col-sm-6">
                <p><strong>NO KTA:</strong> <?php echo $nokta; ?></p>
              </div>
              <div class="col-sm-6">
                <p><strong>NAMA:</strong> <?php echo $nama; ?></p>
              </div>
            </div>
          </div>
        </div>

        <!-- Sertifikat -->
        <div class="panel panel-default">
          <div class="panel-heading bg-info">
            <h3 class="panel-title"><i class="glyphicon glyphicon-certificate"></i> Detail Sertifikat</h3>
          </div>
          <div class="panel-body">
            <table class="table table-bordered table-striped">
              <thead>
                <tr class="bg-primary text-white">
                  <th class="text-center">SKIP</th>
                  <th class="text-center">STRI</th>
                  <th class="text-center">ACPE</th>
                  <th class="text-center">APEC Eng</th>
                  <th class="text-center">ASEAN Eng</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Tanggal SK: <span class="label label-info"><?php echo $sk_skip; ?></span></td>
                  <td>Tanggal SK: <span class="label label-info"><?php echo $stri_sk; ?></span></td>
                  <td>No. ACPE: <span class="label label-success"><?php echo $kode_acpe; ?></span></td>
                  <td>No. APEC: <span class="label label-success"><?php echo $kode_apec; ?></span></td>
                  <td>No. AER: <span class="label label-success"><?php echo $no_aer; ?></span></td>
                </tr>
                <tr>
                  <td>From Date: <span class="label label-default"><?php echo $skip_from; ?></span></td>
                  <td>From Date: <span class="label label-default"><?php echo $stri_from; ?></span></td>
                  <td>Date of Issue: <span class="label label-default"><?php echo $doi; ?></span></td>
                  <td>No. Registrasi: <span class="label label-default"><?php echo $noreg; ?></span></td>
                  <td>Grade: <span class="label label-default"><?php echo $grade; ?></span></td>
                </tr>
                <tr>
                  <td>Thru Date: <span class="label label-warning"><?php echo $stri_thru; ?></span></td>
                  <td>Thru Date: <span class="label label-warning"><?php echo $skip_thru; ?></span></td>
                  <td>New PE No.: <span class="label label-warning"><?php echo $new_pe; ?></span></td>
                  <td>No. SIP: <span class="label label-warning"><?php echo $nosip; ?></span></td>
                  <td>
                    Certificate:
                    <a href="<?php echo $url_aer; ?>" target="_blank" class="btn btn-primary btn-sm">
                      View
                    </a>
                  </td>
                </tr>
                <tr>
                  <td>SKIP ID: <span class="badge"><?php echo $skip_id; ?></span></td>
                  <td>STRI ID: <span class="badge"><?php echo $stri_id; ?></span></td>
                  <td></td>
                  <td></td>
                </tr>
                <tr>
                  <td>SKIP Type: <span class="label label-primary"><?php echo $cert_type . ' - ' . $cert_ket; ?></span></td>
                  <td>STRI Type: <span class="label label-primary"><?php echo $stri_tipe; ?></span></td>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </aside>
    </div>

  </div>

  <?php $this->load->view('member/common/footer'); ?>

  <!-- Bootstrap 3 JS -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>

</html>