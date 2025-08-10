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

			$existingEmails = $this->db->select('email')->get('users')->result_array();
			$existingEmails = array_column($existingEmails, 'email'); // jadi array 1 dimensi

			$username = '';
			$password_hash = password_hash($password, PASSWORD_DEFAULT);

			// Loop mulai dari baris kedua (skip header)
			foreach ($sheetData as $rowIndex => $row) {
				if ($rowIndex === 1) continue; // skip header

				// Skip baris kosong
				if (empty(array_filter($row))) continue;

				// Ambil value gender dari kolom E
				$gender_excel = strtolower(trim($row['E'])); // lowercase biar aman

				// Mapping ke value database
				if ($gender_excel === 'laki-laki') {
					$gender_db = 'Male';
				} elseif ($gender_excel === 'perempuan') {
					$gender_db = 'Female';
				} else {
					$gender_db = null; // atau default lain kalau datanya kosong/tidak valid
				}
				$email = trim($row['D']);
				$kodkel = $this->input->post('kodkel', true);
				$firstname = trim($row['B']);
				$lastname = trim($row['C']);
				$idcard = trim($row['G']);
				$birthplace = trim($row['I']);

				$dob_cell = trim($row['J']);
				$dob_db = null;

				if (!empty($dob_cell)) {
					if (is_numeric($dob_cell)) {
						// Format tanggal dari serial Excel
						$dob_db = ExcelDate::excelToDateTimeObject($dob_cell)->format('Y-m-d');
					} else {
						// Format manual dari string
						$dob_db = date('Y-m-d', strtotime($dob_cell));
					}
				}

				$mobilephone = trim($row['K']);

				// Cek apakah email sudah ada di database
				$email_exist = $this->db->get_where('users', ['email' => $email])->row();

				// Validasi jika email sudah ada maka akana ada pesan error
				// Cek apakah email sudah ada di DB
				if (in_array($email, $existingEmails)) {
					$this->session->set_flashdata('error', "❌ Email '$email' sudah terdaftar!");
					redirect('/import');
					return; // stop proses import
				}

				$username = '';
				///////////////////////// GET DATA USERS \\\\\\\\\\\\\\\\\\\\\\
				$data_users = [
					'username' => $username,
					'email' => $email,
					'password' => $row = $password_hash,
				];

				///////////////////////// GET DATA USER PROFILE \\\\\\\\\\\\\\\\\\\\\\


				$data_profiles = [
					'user_id'               => '1',
					'firstname'             => $firstname,
					'lastname'              => $lastname,
					'gender'                => $gender_db,
					'idtype'                => 'Citizen',
					'idcard'                => $idcard,
					'birthplace'            => $birthplace,
					'dob'                   => $dob_db,
					'mobilephone'           => $mobilephone,
					'kolektif_name_id'      => htmlspecialchars($kodkel),
				];

				///////////////////////// GET DATA USER ADDRESS \\\\\\\\\\\\\\\\\\\\\\

				// $data_user_address = [
				//   'test' => trim($row['C'])
				// ];
				// var_dump($data_users, $data_profiles);
				// exit;
				// Validasi kolom wajib
				// if (!empty($data['no_acpe']) && !empty($data['doi']) && !empty($data['nama'])) {
				// }
				$this->Pii_Model->insert_from_import($data_users);
				$this->Pii_Model->insert_data_profiles($data_profiles);
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
