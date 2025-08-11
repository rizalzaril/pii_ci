
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
			->select('dummy_users.*, dummy_user_profiles.*')
			->from('dummy_users')
			->join('dummy_user_profiles', 'dummy_user_profiles.user_id = dummy_users.id', 'left')
			->where('dummy_users.id', $id)
			->limit(10)
			->get()
			->row();
	}



	// public function get_users($start, $length, $search = null)
	// {
	//   $this->db->select('id, username, email, activated');
	//   $this->db->from('users');

	//   // 🔍 Proses pencarian jika ada input
	//   if (!empty($search)) {
	//     $this->db->group_start();
	//     $this->db->like('username', $search);
	//     $this->db->or_like('email', $search);
	//     $this->db->group_end();
	//   }

	//   $this->db->order_by('id', 'DESC');
	//   $this->db->limit($length, $start);

	//   return $this->db->get()->result();
	// }

	public function get_users($start, $length, $search = null, $order_col = null, $order_dir = null, $is_duplicate = null)
	{
		$this->db->select('*');
		$this->db->from('dummy_users');

		// 🔹 Filter duplicate berdasarkan tabel users
		if ($is_duplicate !== null && $is_duplicate !== '') {
			if ($is_duplicate == 1) {
				// Duplicate Only → email sudah ada di users
				$this->db->where("email IN (SELECT email FROM users)", NULL, FALSE);
			} elseif ($is_duplicate == 0) {
				// Non Duplicate Only → email belum ada di users
				$this->db->where("email NOT IN (SELECT email FROM users)", NULL, FALSE);
			}
		}

		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('username', $search);
			$this->db->or_like('email', $search);
			$this->db->group_end();
		}

		if ($order_col && $order_dir) {
			$this->db->order_by($order_col, $order_dir);
		} else {
			$this->db->order_by('id', 'DESC');
		}

		$this->db->limit($length, $start);
		return $this->db->get()->result();
	}

	public function count_filtered($search = null, $is_duplicate = null)
	{
		$this->db->from('dummy_users');

		if ($is_duplicate !== null && $is_duplicate !== '') {
			if ($is_duplicate == 1) {
				$this->db->where("email IN (SELECT email FROM users)", NULL, FALSE);
			} elseif ($is_duplicate == 0) {
				$this->db->where("email NOT IN (SELECT email FROM users)", NULL, FALSE);
			}
		}

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
		return $this->db->count_all('dummy_users');
	}
}
