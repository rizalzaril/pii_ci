<?php


defined('BASEPATH') or exit('No direct script access allowed');

class Pii_Model extends CI_Model
{

  public function find_all()
  {
    $this->db->select('*');
    $this->db->from('persen_bagi');
    $this->db->order_by('id', 'DESC');
    return $this->db->get()->result();
  }

  public function save_data($data)
  {
    $this->db->insert('persen_bagi', $data);
    return $this->db->insert_id();
  }

  public function cek_kode()
  {
    $query = $this->db->query('SELECT MAX(kode) as kode_ from persen_bagi');
    $result = $query->row();
    return $result->kode_;
  }

  public function get_by_id($id)
  {
    return $this->db->get_where('persen_bagi', ['id' => $id])->result();
  }

  public function update($id, $data)
  {
    return $this->db->update('persen_bagi', $data, ['id' => $id]);
  }

  public function delete_by_id($id)
  {
    return $this->db->delete('persen_bagi', ['id' => $id]);
  }

  // ACPE \\
  public function get_acpe()
  {
    $this->db->select('*');
    $this->db->from('acpe');
    $this->db->order_by('id', 'DESC');
    return $this->db->get()->result();
  }

  public function insert_from_import($data)
  {
    return $this->db->insert('acpe', $data);
  }
}
