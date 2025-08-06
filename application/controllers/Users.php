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
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));

    $users = $this->Users_model->get_users($start, $length);
    $total = $this->Users_model->count_all();

    $data = [];
    $no = $start + 1;
    foreach ($users as $user) {
      $data[] = [
        $no++,
        $user->username,
        $user->email,
        $user->activated,
        '<a href="' . base_url('user/edit/' . $user->id) . '" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
       <a href="#" class="btn btn-sm btn-danger btn-delete" data-id="' . $user->id . '"><i class="fa fa-trash"></i></a>'
      ];
    }

    $output = [
      "draw" => $draw,
      "recordsTotal" => $total,
      "recordsFiltered" => $total,
      "data" => $data
    ];

    // Untuk debug JSON
    header('Content-Type: application/json');
    echo json_encode($output, JSON_PRETTY_PRINT);
    exit;
  }
}
