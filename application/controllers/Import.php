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

			$kodkel           = $this->input->post('kodkel', true);
			$passwordDefault  = $this->input->post('password', true); // Ambil password dari form

			// Validasi password default
			// if (empty($passwordDefault)) {
			// 	$this->session->set_flashdata('error', 'Password default wajib diisi!');
			// 	redirect('/users');
			// 	return;
			// }

			// Ambil semua email existing dari DB
			$existingEmails = $this->db->select('email')->get('users')->result_array();
			$existingEmails = array_column($existingEmails, 'email');

			$errors = [];

			// Loop data excel (mulai dari baris ke-2)
			foreach ($sheetData as $rowIndex => $row) {
				if ($rowIndex === 1) continue; // Skip header
				if (empty(array_filter($row))) continue; // Skip baris kosong

				$email = trim($row['D']);

				// Cek duplikat email (di DB atau di file)
				if (in_array($email, $existingEmails)) {
					$errors[] = "Baris $rowIndex: Email '$email' sudah terdaftar.";
					continue;
				}

				// Mapping gender
				$gender_excel = strtolower(trim($row['E']));
				if ($gender_excel === 'laki-laki') {
					$gender_db = 'Male';
				} elseif ($gender_excel === 'perempuan') {
					$gender_db = 'Female';
				} else {
					$gender_db = null;
				}

				// Format DOB
				$dob_cell = trim($row['J']);
				$dob_db = null;
				if (!empty($dob_cell)) {
					if (is_numeric($dob_cell)) {
						$dob_db = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dob_cell)->format('Y-m-d');
					} else {
						$dob_db = date('Y-m-d', strtotime($dob_cell));
					}
				}

				// ===================== INSERT USERS =====================
				$data_users = [
					'username' => '',
					'email'    => $email,
					'password' => password_hash($passwordDefault, PASSWORD_DEFAULT), // Gunakan password default
				];
				$this->Pii_Model->insert_from_import($data_users);

				// Ambil ID user yang baru dibuat
				$user_id = $this->db->insert_id();
				$mobilephone = trim($row['K']);

				// ===================== INSERT USER PROFILE =====================
				$data_profiles = [
					'user_id'          => $user_id,
					'firstname'        => trim($row['B']),
					'lastname'         => trim($row['C']),
					'gender'           => $gender_db,
					'idtype'           => 'Citizen',
					'idcard'           => trim($row['G']),
					'birthplace'       => trim($row['I']),
					'dob'              => $dob_db,
					'mobilephone'      => $mobilephone,
					'kolektif_name_id' => htmlspecialchars($kodkel),
				];
				$this->Pii_Model->insert_data_profiles($data_profiles);

				// ===================== INSERT USER ADDRESS =====================
				$data_address = [
					'user_id'          		=> $user_id,
					'addresstype'        	=> 'Rumah',
					'address'         		=> trim($row['L']),
					'city'           			=> trim($row['M']),
					'province'           	=> trim($row['N']),
					'phone'           		=> $mobilephone,
					'zipcode'       			=> trim($row['O']),
					'email'              	=> $email,
					'createddate'      		=> date('Y-m-d'), // perbaikan format date
				];
				$this->Pii_Model->insert_user_address($data_address);

				// Tambahkan email ke list supaya duplikat di file ikut dicegah
				$existingEmails[] = $email;
			}

			// Hapus file upload
			if (file_exists($uploadedFile['full_path'])) {
				unlink($uploadedFile['full_path']);
			}

			// Feedback hasil import
			if (!empty($errors)) {
				$this->session->set_flashdata('error', implode('<br>', $errors));
			} else {
				$this->session->set_flashdata('success_import', '✅ Semua data berhasil diimpor.');
			}
		} catch (\Exception $e) {
			$this->session->set_flashdata('error', 'Gagal memproses file: ' . $e->getMessage());
		}

		redirect('/users');
	}
}
