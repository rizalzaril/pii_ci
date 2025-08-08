<div class="container mt-5">

  <?php if ($this->session->flashdata('success_update') || $this->session->flashdata('success_delete')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong>Sukses!</strong> <?= $this->session->flashdata('success_save'); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <a href="<?= base_url('/dashboard/add_data') ?>" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add data </a>

  <!-- <table id="table_users" class="table mt-3">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">Username</th>
        <th scope="col">Email</th>
        <th scope="col">User status</th>
        <th scope="col">Hasil akhir (%)</th>
        <th scope="col">Aksi</th>
      </tr>
    </thead>

    <tbody>
      <?php
      $no = 1;
      foreach ($users as $user) : ?>
        <tr>
          <th scope="row"><?= $no++ ?></th>
          <td><?= $user->username ?></td>
          <td><?= $user->email  ?></td>
          <td><?= $user->activated ?></td>
          <td>#</td>
          <td>
            <a class="btn btn-primary btn-sm" href="<?= base_url('/dashboard/edit_data/' . $user->id) ?>"><i class="fas fa-eye"></i></a>
            <a class="btn btn-primary btn-sm" href="<?= base_url('/dashboard/edit_data/' . $user->id) ?>"><i class="fas fa-edit"></i></a>
            <a class="btn btn-danger btn-sm btn-delete" data-id="<?= $user->id ?>" data-kode="<?= $user->id ?>"><i class="fas fa-trash"></i></a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table> -->

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