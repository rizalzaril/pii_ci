<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('Users_model');
		$this->load->model('Pii_Model');
		$this->load->library(['session', 'form_validation']);
	}

	public function index()
	{
		// $data['users'] = $this->Users_model->find_all_users();

		$data_kelompok['list_kelompok'] = $this->Pii_Model->get_data_kelompok();

		//Jika data kelompok 0 maka tampilkan pesan
		if (!$data_kelompok) {
			echo 'Data Kelompok kosong';
		}

		//cek last_id dari table users
		// $last_id = $this->Pii_Model->cek_next_id_users();
		// echo '<pre>';
		// var_dump('NEXT ID USERS:' . $last_id);
		// echo '</pre>';
		// exit;

		// echo '<pre>';
		// var_dump($data_kelompok);
		// echo '</pre>';
		// exit;

		$this->load->view('header');
		$this->load->view('/Users/Vusers', $data_kelompok);
		$this->load->view('footer');
	}

	public function detail_aer($kta)
	{
		// Ambil data dari model
		$detail_aer = $this->Users_model->get_detail_aer($kta);

		// Kalau objeknya sama sekali tidak ada
		if (!$detail_aer) {
			show_404(); // atau tampilkan pesan lain
			return;
		}

		// Ganti semua property NULL jadi tanda '-'
		foreach ($detail_aer as $key => $value) {
			if (is_null($value) || $value === '') {
				$detail_aer->$key = '-';
			}
		}

		// Debug var_dump
		echo '<pre>';
		var_dump($detail_aer);
		echo '</pre>';
		exit;

		// Kirim ke view
		$data['detail_aer'] = $detail_aer;
		$this->load->view('header');
		$this->load->view('aer_details_view', $data);
		$this->load->view('footer');
	}





	//get users with ajax
	public function get_users()
	{
		$draw         = intval($this->input->get("draw"));
		$start        = intval($this->input->get("start"));
		$length       = intval($this->input->get("length"));
		$search       = $this->input->get("search")['value'];
		$order_col    = $this->input->get("order_by");
		$order_dir    = $this->input->get("order_dir");
		$is_duplicate = $this->input->get("is_duplicate");

		$users    = $this->Users_model->get_users($start, $length, $search, $order_col, $order_dir, $is_duplicate);
		$total    = $this->Users_model->count_all();
		$filtered = $this->Users_model->count_filtered($search, $is_duplicate);

		$data = [];
		$no = $start + 1;

		foreach ($users as $user) {
			$existsInUsers = $this->db
				->get_where('users', ['email' => $user->email])
				->num_rows() > 0;

			$emailDisplay = $existsInUsers
				? '<span class="text text-danger fw-bold">' . $user->email . '</span>'
				: $user->email;

			$duplicateBadge = $existsInUsers
				? '<span class="badge bg-danger">Cannot Import <i class="fa fa-times"></i></span>'
				: '<span class="badge bg-success">Ready to Import <i class="fa fa-check"></i></span>';

			$actionButtons = '
            <a href="' . base_url('users/get_user_detail/' . $user->id) . '" class="btn btn-sm btn-dark">
                <i class="fa fa-eye"></i>
            </a>
            <a href="' . base_url('users/edit/' . $user->id) . '" class="btn btn-sm btn-warning">
                <i class="fa fa-edit"></i>
            </a>
        ';

			$data[] = [
				'<input type="checkbox" class="row_checkbox" value="' . $user->id . '">',
				$no++,
				$user->id,
				$user->username,
				$emailDisplay,
				$duplicateBadge,
				$user->created,
				$actionButtons
			];
		}

		echo json_encode([
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => $filtered,
			"data" => $data
		]);
	}


	public function delete_selected_dummy_users()
	{
		$ids = $this->input->post('ids');

		if (!empty($ids)) {
			// Hapus dari tabel dummy_users
			$this->db->where_in('id', $ids)->delete('users');

			//Hapus dari tabel dummy_user_profiles
			$this->db->where_in('user_id', $ids)->delete('user_profiles');

			//Hapus dari tabel dummy_user_address
			$this->db->where_in('user_id', $ids)->delete('user_address');
		}

		echo json_encode(['Status' => 'Delete selected success!']);
	}


	public function delete_all_dummy_users()
	{
		// Kosongkan tabel dummy_users
		// $data = $this->db->empty_table('users');
		// var_dump($data);
		// exit;

		// Kosongkan tabel dummy_user_profiles
		// $this->db->empty_table('user_profiles');

		// Kosongkan tabel dummy_user_address
		// $this->db->empty_table('user_address');

		echo json_encode(['Status' => 'Delete success!']);
	}





	public function get_user_detail($id)
	{
		$data['user_detail'] = $this->Users_model->get_user_detail($id);
		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';

		$this->load->view('Users/Vuser_profiles', $data);
	}
}
