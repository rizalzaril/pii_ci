
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


  public function get_users($start, $limit)
  {
    return $this->db
      ->select('id, username, email, activated') // <--- Tambahkan kolom yang dibutuhkan
      ->from('users')
      ->order_by('id', 'DESC')
      ->limit($limit, $start)
      ->get()
      ->result();
  }


  public function count_all()
  {
    return $this->db->count_all('users');
  }
}
