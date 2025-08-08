<?php


defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;

class Import extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    $this->load->model('Pii_Model');
    $this->load->library(['session', 'form_validation']);
  }


  public function index()
  {
    $this->load->view('header');
    $this->load->view('import_view');
    $this->load->view('footer');
  }

  public function import_proccess()
  {
    // Konfigurasi upload
    $config = [
      'upload_path'   => './uploads/excel_import/',
      'allowed_types' => 'xlsx|xls|csv',
      'max_size'      => 2048,
      'file_name'     => 'excel_import_' . time()
    ];

    $this->load->library('upload', $config);

    // Upload file
    if (!$this->upload->do_upload('excel_file')) {
      $this->session->set_flashdata('error', $this->upload->display_errors());
      redirect('/dashboard/acpe');
      return;
    }

    $uploadedFile = $this->upload->data();

    try {
      // Load spreadsheet
      $spreadsheet = IOFactory::load($uploadedFile['full_path']);
      $sheetData   = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);


      //POST

      $password = $this->input->post('password');


      $username = '';
      $password_hash = password_hash($password, PASSWORD_DEFAULT);

      // Loop mulai dari baris kedua (skip header)
      foreach ($sheetData as $rowIndex => $row) {
        if ($rowIndex === 1) continue; // skip header

        // Skip baris kosong
        if (empty(array_filter($row))) continue;

        // $data = [
        //   'no_acpe'       => trim($row['A']),
        //   'doi'           => trim($row['B']),
        //   'nama'          => trim($row['C']),
        //   'kta'           => trim($row['D']),
        //   'new_po_no'     => trim($row['E']),
        //   'bk_acpe'       => trim($row['F']),
        //   'asosiasi_prof' => trim($row['G']),
        // ];

        ///////////////////////// GET DATA USERS \\\\\\\\\\\\\\\\\\\\\\
        // Cek apakah email sudah ada di database
        $email = trim($row['D']);

        $email_exist = $this->db->get_where('users', ['email' => $email])->row();

        // Validasi jika email sudah ada maka akana ada pesan error
        if ($email_exist) {
          // Kalau sudah ada, beri pesan error dan hentikan proses
          $this->session->set_flashdata('error', "❌ Email '$email' sudah terdaftar!");
          redirect('/import');
          return;
        }

        $username = '';
        $data_users = [
          'username' => $username,
          'email' => $email,
          'password' => $row = $password_hash,
        ];

        ///////////////////////// GET DATA USER PROFILE \\\\\\\\\\\\\\\\\\\\\\

        // $data_user_profiles = [
        //   'test'                  => trim($row['B']),
        //   'user_id'               => trim($row['B']),
        //   'firstname'             => trim($row['B']),
        //   'lastname'              => trim($row['B']),
        //   'gender'                => trim($row['B']),
        //   'idtype'                => trim($row['B']),
        //   'idcard'                => trim($row['B']),
        //   'birthplace'            => trim($row['B']),
        //   'dob'                   => trim($row['B']),
        //   'mobilephone'           => trim($row['B']),
        //   'kolektif_name_id'      => trim($row['B']),
        // ];

        ///////////////////////// GET DATA USER ADDRESS \\\\\\\\\\\\\\\\\\\\\\

        // $data_user_address = [
        //   'test' => trim($row['C'])
        // ];
        // var_dump($data_users);
        // exit;
        // Validasi kolom wajib
        // if (!empty($data['no_acpe']) && !empty($data['doi']) && !empty($data['nama'])) {
        // }
        $this->Pii_Model->insert_from_import($data_users);
      }

      $this->session->set_flashdata('success_import', '✅ Data berhasil diimpor.');
    } catch (\Exception $e) {
      $this->session->set_flashdata('error', 'Gagal memproses file: ' . $e->getMessage());
    }

    // Hapus file upload untuk keamanan
    if (file_exists($uploadedFile['full_path'])) {
      unlink($uploadedFile['full_path']);
    }

    redirect('/import');
  }
}
