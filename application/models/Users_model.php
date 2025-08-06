
<?php


defined('BASEPATH') or exit('No direct script access allowed');

class Users_model extends CI_Model
{


  public function find_all_users()
  {
    $this->db->select('*');
    $this->db->from('users');
    $this->db->order_by('id', 'DESC');
    $this->db->limit(100);
    return $this->db->get()->result();
  }

  public function get_user_detail($id)
  {
    return $this->db
      ->select('users.*, user_profiles.*')
      ->from('users')
      ->join('user_profiles', 'user_profiles.user_id = users.id', 'left')
      ->where('users.id', $id)
      ->limit(10)
      ->get()
      ->row();
  }



  public function get_users($start, $length, $search = null)
  {
    $this->db->select('id, username, email, activated');
    $this->db->from('users');

    // 🔍 Proses pencarian jika ada input
    if (!empty($search)) {
      $this->db->group_start();
      $this->db->like('username', $search);
      $this->db->or_like('email', $search);
      $this->db->group_end();
    }

    $this->db->order_by('id', 'DESC');
    $this->db->limit($length, $start);

    return $this->db->get()->result();
  }

  public function count_filtered($search = null)
  {
    $this->db->from('users');

    if (!empty($search)) {
      $this->db->group_start();
      $this->db->like('username', $search);
      $this->db->or_like('email', $search);
      $this->db->group_end();
    }

    return $this->db->count_all_results();
  }


  public function count_all()
  {
    return $this->db->count_all('users');
  }
}
