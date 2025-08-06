<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_profiles extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    $this->load->model('User_profiles_model');
    $this->load->library(['session', 'form_validation']);
  }

  public function index()
  {
    // $data['users'] = $this->Users_model->find_all_users();

    $id = $this->session->userdata('id');

    $data['profile_data'] = $this->User_profiles_model->get_profile_by_id($id);

    // echo '<pre>';
    // print_r($data);
    // echo '</pre>';
    // exit;
    $this->load->view('header');
    $this->load->view('/Dashboard/Vprofile', $data);
    $this->load->view('footer');
  }


  public function get_user_profiles()
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));

    $users = $this->Users_model->get_user_profiles($start, $length);
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



  public function update_info_pribadi($id)
  {
    $firstname = $this->input->post('firstname', true);
    $lastname = $this->input->post('lastname', true);
    $gender = $this->input->post('gender', true);
    $birthplace = $this->input->post('birthplace', true);
    $dob = $this->input->post('dob', true);
    $website = $this->input->post('website', true);

    $data = [
      'firstname'  => htmlspecialchars_decode($firstname),
      'lastname'   => htmlspecialchars($lastname),
      'gender'     => htmlspecialchars($gender),
      'birthplace' => htmlspecialchars($birthplace),
      'dob'        => htmlspecialchars($dob),
      'website'    => htmlspecialchars($website),
    ];

    // var_dump($data);
    // exit;

    $this->User_profiles_model->update_info_pribadi($id, $data);
    $this->session->set_flashdata('success_update', 'Data berhasil diupdate!');
    redirect('/User_profiles/');
  }
}
