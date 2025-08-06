<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    $this->load->model('Users_model');
    $this->load->library(['session', 'form_validation']);
  }

  public function index()
  {
    // $data['users'] = $this->Users_model->find_all_users();
    $this->load->view('header');
    $this->load->view('/Users/Vusers');
    $this->load->view('footer');
  }


  public function get_users()
  {
    $draw   = intval($this->input->get("draw"));
    $start  = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $search = $this->input->get("search")['value'];

    $users = $this->Users_model->get_users($start, $length, $search);
    $total = $this->Users_model->count_all();
    $filtered = $this->Users_model->count_filtered($search);

    $data = [];
    $no = $start + 1;
    foreach ($users as $user) {
      $data[] = [
        $no++,
        $user->username,
        $user->email,
        $user->activated == 1
          ? '<span class="badge bg-success">Aktif</span>'
          : '<span class="badge bg-secondary">Nonaktif</span>',
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
