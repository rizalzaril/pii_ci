<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    $this->load->model('Pii_Model');
    $this->load->library(['session', 'form_validation']);
  }


  public function cek_db()
  {
    //cek koneksi ke db

    $query = $this->db->query('SELECT DATABASE() AS db');

    //cek hasil query
    if ($query->num_rows() > 0) {
      $row = $query->row();
      echo "Database anda berhasil terkoneksi";
      echo 'Database anda adalah ' . $row->db;
    } else {
      echo 'Database tidak terkoneksi, silahkan cek kembali!';
    }
  }


  public function list_data()
  {

    $data['list_data'] = $this->Pii_Model->find_all();

    $this->load->view('Dashboard/header');
    $this->load->view('Dashboard/list_persen_bagi', $data);
    $this->load->view('Dashboard/footer');
  }

  public function add_data()
  {
    $kode_terakhir = $this->Pii_Model->cek_kode(); // contoh hasil: B01-0001

    // Ambil angka urutan terakhir dari kode (misal: '0001')
    $no_urut = (int) substr($kode_terakhir, 2, 2); // mulai dari index 4, ambil 4 digit
    $new_urut = $no_urut + 1;

    // Format ulang dengan leading zero 4 digit (hasil: '0002')
    $format_urut = str_pad($new_urut, 0, '0', STR_PAD_LEFT);

    // Gabungkan dengan awalan kode (misal: 'B01-')
    $kode_baru = 'B0' . $format_urut;

    $data = array('kode' => $kode_baru);

    $this->load->view('Dashboard/header');
    $this->load->view('Dashboard/add_data', $data);
    $this->load->view('Dashboard/footer');
  }

  public function store_data()
  {

    $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
    $this->form_validation->set_rules('keterangan', 'Keterangan', 'required|trim');
    $this->form_validation->set_rules('persen', 'Persen', 'required|trim');

    if ($this->form_validation->run() == false) {
      return redirect('/dashboard/add_data');
    } else {
      $kode = $this->input->post('kode', true);
      $keterangan = $this->input->post('keterangan', true);
      $persen = $this->input->post('persen', true);


      $data = [
        'kode' => htmlspecialchars($kode),
        'keter' => ($keterangan),
        'persen' => htmlspecialchars($persen),
      ];

      // var_dump($data);
      // exit;

      $this->Pii_Model->save_data($data);

      $this->session->set_flashdata('success_save', 'Data berhasil disimpan.');
      redirect('/dashboard/add_data');
    }
  }


  public function edit_data($id)
  {

    $data['row'] = $this->Pii_Model->get_by_id($id);

    $this->load->view('Dashboard/header');
    $this->load->view('Dashboard/edit_data', $data);
    $this->load->view('Dashboard/footer');
  }


  public function update_data($id)
  {

    $this->form_validation->set_rules('keterangan', 'Keterangan', 'required|trim');
    $this->form_validation->set_rules('persen', 'Persen', 'required|numeric');

    if ($this->form_validation->run() === FALSE) {
      // Jika validasi gagal, kembali ke form edit
      $this->session->set_flashdata('error', 'Input tidak valid!');
      return redirect('/dashboard/edit_data/' . $id);
    }

    // Ambil input dari form
    $keterangan = $this->input->post('keterangan', true);
    $persen = $this->input->post('persen', true);

    $data = [
      'keter' => ($keterangan),
      'persen' => htmlspecialchars($persen),
    ];

    $this->Pii_Model->update($id, $data);


    $this->session->set_flashdata('success_update', 'Data berhasil diupdate!');
    redirect('/dashboard/list_data');
  }



  public function delete_data($id)
  {
    $test = $this->Pii_Model->delete_by_id($id);

    // debuging
    // var_dump($test);
    // exit;

    $this->session->set_flashdata('success_delete', 'Data berhasil dihapus');
    redirect('/dashboard/list_data');
  }
}
