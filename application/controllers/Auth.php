<?php


defined('BASEPATH') or exit('No direct script access allowed');


class Auth extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    $this->load->model('AuthModel');
    $this->load->library(['session', 'form_validation', 'encryption']);
  }


  public function index()
  {

    echo 'Hello, ini halaman auth!<br>';

    //cek koneksi database

    //menjalankan query untuk get db
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


  public function register()
  {
    $firstname = $this->input->post('firstname', true);
    $lastname  = $this->input->post('lastname', true);
    $email     = $this->input->post('email', true);
    $password  = $this->input->post('password', true);
    $passconf  = $this->input->post('passconf', true);

    // Validation rules
    $this->form_validation->set_rules('firstname', 'Nama Depan', 'required|trim');
    $this->form_validation->set_rules('lastname', 'Nama Belakang', 'required|trim');
    $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]|trim');
    $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|trim');
    $this->form_validation->set_rules('passconf', 'Konfirmasi Password', 'required|matches[password]|trim');

    // Jika validasi gagal
    if ($this->form_validation->run() == false) {
      return $this->load->view('Auth/Vregister');
    } else {
      $data = [
        'email'       => htmlspecialchars($email),
        'password'    => password_hash($password, PASSWORD_DEFAULT),
        'created'  => date('Y-m-d H:i:s')
      ];

      $insert_id = $this->AuthModel->register($data);

      if ($insert_id > 0) {
        $kode_user = 'USR' . str_pad($insert_id, 4, '0', STR_PAD_LEFT) . '-' . date('Ymd');
        $this->db->where('id', $insert_id);
        $this->db->update('users', ['kode_user' => $kode_user]);
      }

      $this->session->set_flashdata('success', 'Akun berhasil didaftarkan. Silakan login.');
      redirect('Auth/Vlogin');
    }
  }


  public function login()
  {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
      $vals = array(
        'img_path'      => './captcha/',
        'img_url'       => base_url('captcha/'),
        'font_path' => FCPATH . 'system/fonts/texb.ttf',
        'img_width'     => 500,
        'img_height'    => 50,
        'expiration'    => 7200,
        'word_length'   => 8,
        'font_size'     => 200,
        'img_id'        => 'Imageid',
        'pool'          => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',

        // White background and border, black text and red grid
        'colors'        => array(
          'background' => array(255, 255, 255),
          'border' => array(255, 255, 255),
          'text' => array(0, 0, 0),
          'grid' => array(255, 40, 40)
        )
      );

      $captcha = create_captcha($vals);

      if ($captcha === false) {
        log_message('error', 'CAPTCHA gagal dibuat: ' . print_r($vals, true));
        $data['captcha'] = ['image' => '<span style="color:red;">Gagal memuat captcha</span>'];
      } else {
        $this->session->set_userdata('captcha', $captcha['word']);
        $data['captcha'] = $captcha;
      }


      return $this->load->view('Auth/Vlogin', $data);
    }


    // Proses POST Login
    $email = $this->input->post('email', true);
    $password = $this->input->post('password');
    $inputCaptcha = $this->input->post('captcha');

    $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
    $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|trim');
    $this->form_validation->set_rules('captcha', 'Kode Captcha', 'required|trim');

    if ($this->form_validation->run() == false) {
      return redirect('auth/login');
    }

    // Validasi CAPTCHA
    if (strtoupper($inputCaptcha) != strtoupper($this->session->userdata('captcha'))) {
      $this->session->set_flashdata('error', 'Kode Captcha salah!');
      return redirect('Auth/login');
    }

    // Validasi user dan password
    $user = $this->AuthModel->getUsersEmail($email);
    if ($user && password_verify($password, $user->password)) {
      $ip_public = @file_get_contents('https://api.ipify.org');
      $data = [
        'id' => $user->id,
        'email' => $user->email,
        'created' => $user->created_at,
        'last_login' => $user->last_login,
      ];
      $this->session->set_userdata($data);
      $this->AuthModel->update_last_login($user->id, $ip_public);
      redirect('/User_profiles');
    } else {
      $this->session->set_flashdata('error', 'Email atau Password salah!');
      redirect('Auth/login');
    }
  }


  public function test_write()
  {
    if (is_writable('./captcha')) {
      echo "Folder captcha/ bisa ditulis ✔️";
    } else {
      echo "Folder captcha/ TIDAK bisa ditulis ❌";
    }
  }

  public function test_captcha()
  {

    $vals = array(
      'img_path'   => FCPATH . 'captcha/',
      'img_url'    => base_url('captcha/'),
      'img_width'  => 150,
      'img_height' => 50,
      'expiration' => 7200,
      'word_length' => 6,
      'font_size' => 16,
      'pool' => '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ',

      // ✅ Gunakan salah satu font_path SAJA dan pastikan path ini BENAR!
      'font_path' => FCPATH . 'system/fonts/texb.ttf',

      'colors' => array(
        'background' => array(255, 255, 255),
        'border' => array(255, 255, 255),
        'text' => array(0, 0, 0),
        'grid' => array(255, 240, 240)
      )
    );

    echo '<pre>';
    echo 'Checking font path: ' . $vals['font_path'] . "\n";
    echo file_exists($vals['font_path']) ? '✔ Font file ditemukan' : '❌ Font file TIDAK ditemukan';
    echo "\nChecking folder captcha: " . $vals['img_path'] . "\n";
    echo is_writable($vals['img_path']) ? '✔ Folder captcha bisa ditulis' : '❌ Folder captcha TIDAK bisa ditulis';
    echo '</pre>';

    $captcha = create_captcha($vals);

    echo '<pre>';
    var_dump($captcha);
    echo '</pre>';

    if ($captcha !== false) {
      echo $captcha['image'];
    } else {
      echo '<p style="color:red;">❌ CAPTCHA gagal dibuat. Pastikan folder <code>/captcha</code> bisa ditulis & file font <code>texb.ttf</code> ada dan bisa diakses.</p>';
    }
  }





  public function logout()
  {
    $this->session->unset_userdata(['id', 'email', 'name', 'role', 'created_at', 'last_login']);
    $this->session->sess_destroy();
    redirect('auth/login');
  }



  public function ubah_password_view()
  {
    $user_id = $this->session->userdata('id');
    $password = $this->AuthModel->getUsersPassword($user_id);
    foreach ($password as $pass) {
      echo $pass;
    }
    $this->load->view('/auth/ubah_password', $password);
  }

  public function ubah_password()
  {
    $this->form_validation->set_rules('oldPassword', 'Old Password', 'required|trim');
    $this->form_validation->set_rules('newPassword', 'New Password', 'required|min_length[8]|trim');

    $old_password = $this->input->post('oldPassword');

    if ($this->form_validation->run() == false) {
      redirect('/auth/ubah_password_view');
    } else {

      //cek session login by id
      $user_id = $this->session->userdata('id');

      //tampung user id ke model
      $password = $this->AuthModel->getUsersPassword($user_id);

      if ($password && password_verify($old_password, $password->password)) {
        $new_password = password_hash($this->input->post('newPassword'), PASSWORD_DEFAULT);
        $this->AuthModel->ubahPassword($new_password, $user_id);
        $this->session->set_flashdata('success', 'Password berhasil diubah.');

        //log activity
        $this->AuthModel->log_activity($user_id, 'Mengubah password');


        redirect('/auth/ubah_password_view');
      } else {
        $this->session->set_flashdata('error', 'Password lama anda salah!');
        redirect('/auth/ubah_password_view');
      }
    }
  }


  public function forgot_password()
  {
    $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');

    if ($this->form_validation->run() == false) {
      return $this->load->view('auth/forgot_password');
    } else {

      $email = $this->input->post('email', true);
      $user = $this->AuthModel->getUsersEmail($email);

      if ($user) {
        $token = bin2hex(random_bytes(32));
        $token_data = [

          'email' => $email,
          'token' => $token,
          'created_at' => time(),
        ];

        $this->AuthModel->insertToken($token_data);

        $reset_link = base_url("auth/reset_password?token=$token");
        log_message("debug", "Reset password link: $reset_link");

        // $this->session->set_flashdata('success', 'Link reset password telah dikirimkan (lihat log)');
        // echo ($reset_link);

        $this->session->set_flashdata('reset_link', $reset_link);
        redirect('auth/forgot_password');
      } else {
        $this->session->set_flashdata('error', 'Email tidak ditemukan.');
        redirect('auth/forgot_password');
      }
    }
  }


  public function reset_password()
  {
    $token = $this->input->get('token');
    $user_token = $this->AuthModel->getUserToken($token);

    if (!$user_token) {
      show_error('Token tidak valid atau kadaluarsa');
    }

    $this->form_validation->set_rules('password', 'Password Baru', 'required|min_length[6]|trim');

    if ($this->form_validation->run() == false) {
      $this->load->view('auth/reset_password', ['token' => $token]);
    } else {
      $new_password = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
      $this->AuthModel->updatePassword($user_token->email, $new_password);
      $this->AuthModel->deleteToken($user_token->email);
      $this->session->set_flashdata('success', 'Password berhasil diubah.');
      redirect('auth/login');
    }
  }






  public function save_profile()
  {
    $this->form_validation->set_rules('address', 'Alamat', 'required|trim');
    $this->form_validation->set_rules('phone', 'No. Telepon', 'required|trim|numeric');

    if ($this->form_validation->run() == false) {
      $this->session->set_flashdata('errors', validation_errors());
      redirect('/dashboard');
    } else {
      $user_id = $this->session->userdata('id');
      $data_profile = [
        'user_id' => $user_id, // dibutuhkan untuk insert baru
        'address' => htmlspecialchars($this->input->post('address', true)),
        'phone' => htmlspecialchars($this->input->post('phone', true)),
        'photo' => htmlspecialchars('.png'),
      ];

      // Cek apakah data profile user sudah ada
      $existing = $this->AuthModel->getProfileByUserId($user_id);

      if ($existing) {
        // Jika sudah ada, update
        $this->AuthModel->updateProfile($user_id, $data_profile);
      } else {
        // Jika belum ada, insert
        $this->AuthModel->insertProfile($data_profile);
      }

      $this->session->set_flashdata('success', 'Profile berhasil diperbarui');
      redirect('/dashboard');
    }
  }
}
