<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Userprovisioner extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url', 'file'));
		$this->load->database();

		// cek login admin
		if (!$this->session->userdata('is_admin_login') && $this->session->userdata('admin_username') !== 'sp') {
			redirect(base_url() . 'admin');
			exit;
		}
	}

	public function index()
	{
		$this->load->view('admin/userprovisioning_view', ['error' => '']);
	}

	public function upload_csv()
	{
		$config['upload_path']   = FCPATH . 'assets-temp/uploads/';
		$config['allowed_types'] = 'csv';
		$config['max_size']      = 10000;
		$config['overwrite']     = TRUE;

		if (!is_dir($config['upload_path'])) {
			mkdir($config['upload_path'], 0777, TRUE);
		}

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('userfile')) {
			$error = $this->upload->display_errors();
			$this->session->set_flashdata('error', $error);
			redirect('admin/userprovisioner');
			return;
		}

		$fileData = $this->upload->data();
		$filePath = $fileData['full_path'];

		$insertCount = 0;
		$row = 0;
		$CSV_SEPARATOR = ";"; // sesuaikan CSV kamu
		$CSV_ENCLOSURE = '"';
		$CSV_MAX_CHARS = 10000;

		if (($fhandle = fopen($filePath, "r")) !== FALSE) {
			while (($getData = fgetcsv($fhandle, $CSV_MAX_CHARS, $CSV_SEPARATOR, $CSV_ENCLOSURE)) !== FALSE) {
				$row++;

				// skip header
				if ($row == 1) continue;

				// ambil email & password (misal kolom H=7, B=1)
				$email    = isset($getData[0]) ? trim($getData[0]) : '';
				$password = isset($getData[1]) ? trim($getData[1]) : '';

				if (empty($email) || empty($password)) continue;

				$data = [
					'email'    => $email,
					'password' => password_hash($password, PASSWORD_BCRYPT)
				];

				if ($this->db->insert('users', $data)) {
					$insertCount++;
				} else {
					log_message('error', 'Gagal insert row ' . $row . ': ' . $this->db->last_query());
				}
			}
			fclose($fhandle);
		}

		$this->session->set_flashdata('success', "CSV berhasil diproses. Total data yang masuk: " . $insertCount);
		redirect('admin/userprovisioner');
	}
}
