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
		return $this->db->insert('users', $data);
	}

	public function insert_data_profiles($data_profiles)
	{
		return $this->db->insert('user_profiles', $data_profiles);
	}

	public function insert_user_address($data_address)
	{
		return $this->db->insert('user_address', $data_address);
	}


	/// ITS \\\

	public function get_its($start, $length, $search = null)
	{
		$this->db->select('id, username, email, activated');
		$this->db->from('users');
		$this->db->order_by('id', 'DESC');

		// 🔍 Proses pencarian jika ada input
		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('username', $search);
			$this->db->or_like('email', $search);
			$this->db->group_end();
		}

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


	//Get DATA KOLEKTIF

	public function get_data_kelompok()
	{
		return $this->db->get('m_kolektif')->result();
	}
}
