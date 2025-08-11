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
			echo 'Data kosong';
		}

		// echo '<pre>';
		// var_dump($data_kelompok);
		// echo '</pre>';
		// exit;

		$this->load->view('header');
		$this->load->view('/Users/Vusers', $data_kelompok);
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
		$is_duplicate = $this->input->get("is_duplicate"); // 🔹 ambil filter

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

			$data[] = [
				$no++,
				$user->username,
				$emailDisplay,
				$duplicateBadge,
				'<a href="' . base_url('users/get_user_detail/' . $user->id) . '" class="btn btn-sm btn-dark"><i class="fa fa-eye"></i></a>
             <a href="' . base_url('users/edit/' . $user->id) . '" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
             <a href="#" class="btn btn-sm btn-danger btn-delete" data-id="' . $user->id . '"><i class="fa fa-trash"></i></a>'
			];
		}

		echo json_encode([
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => $filtered,
			"data" => $data
		]);
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
