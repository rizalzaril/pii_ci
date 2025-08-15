
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


	public function get_detail_aer($kta)
	{
		$kta = trim($kta); // hapus spasi jika ada

		$sql = "
        SELECT 
            aer.*, 
            members.*, 
            user_profiles.*,  
            user_profiles.description AS profile_description, 
            users.*, 
            user_address.*, 
            user_exp.*, 
            user_edu.description AS edu_description, 
            user_edu.*, 
            user_exp.description AS exp_description
        FROM aer
        LEFT JOIN members 
            ON aer.kta COLLATE utf8mb4_unicode_ci = members.no_kta COLLATE utf8mb4_unicode_ci
        LEFT JOIN user_profiles 
            ON members.person_id AND members.no_kta = user_profiles.user_id
				LEFT JOIN users
            ON user_profiles.user_id = users.id
        LEFT JOIN user_address
            ON users.id = user_address.user_id
        LEFT JOIN user_exp
            ON users.id = user_exp.user_id
        LEFT JOIN user_edu
            ON users.id = user_edu.user_id
			
        WHERE aer.kta COLLATE utf8mb4_unicode_ci = ?
    ";

		$query = $this->db->query($sql, [$kta]);

		if ($query->num_rows() > 0) {
			$row = $query->result();

			// Ganti semua value NULL atau string kosong jadi '-'
			foreach ($row as $key => $value) {
				if (is_null($value) || $value === '') {
					$row->$key = '-';
				}
			}

			return $row;
		}

		return null; // jika tidak ditemukan
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

	public function get_users($start, $length, $search = null, $order_col = null, $order_dir = null, $is_duplicate = null, $start_date = null, $end_date = null)
	{
		$this->db->select('*');
		$this->db->from('users');

		// 🔹 Filter duplicate
		if ($is_duplicate !== null && $is_duplicate !== '') {
			if ($is_duplicate == 1) {
				$this->db->where("email IN (SELECT email FROM users)", NULL, FALSE);
			} elseif ($is_duplicate == 0) {
				$this->db->where("email NOT IN (SELECT email FROM users)", NULL, FALSE);
			}
		}

		// 🔹 Filter date
		if (!empty($start_date) && !empty($end_date)) {
			$this->db->where('DATE(created) >=', $start_date);
			$this->db->where('DATE(created) <=', $end_date);
		} elseif (!empty($start_date)) {
			$this->db->where('DATE(created) >=', $start_date);
		} elseif (!empty($end_date)) {
			$this->db->where('DATE(created) <=', $end_date);
		}

		// 🔹 Search
		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('username', $search);
			$this->db->or_like('email', $search);
			$this->db->group_end();
		}

		// 🔹 Sorting
		if ($order_col && $order_dir) {
			$this->db->order_by($order_col, $order_dir);
		} else {
			$this->db->order_by('id', 'DESC');
		}

		// 🔹 Limit
		$this->db->limit($length, $start);

		return $this->db->get()->result();
	}

	public function count_filtered($search = null, $is_duplicate = null, $start_date = null, $end_date = null)
	{
		$this->db->from('users');

		// 🔹 Filter duplicate
		if ($is_duplicate !== null && $is_duplicate !== '') {
			if ($is_duplicate == 1) {
				$this->db->where("email IN (SELECT email FROM users)", NULL, FALSE);
			} elseif ($is_duplicate == 0) {
				$this->db->where("email NOT IN (SELECT email FROM users)", NULL, FALSE);
			}
		}

		// 🔹 Filter date
		if (!empty($start_date) && !empty($end_date)) {
			$this->db->where('DATE(created) >=', $start_date);
			$this->db->where('DATE(created) <=', $end_date);
		} elseif (!empty($start_date)) {
			$this->db->where('DATE(created) >=', $start_date);
		} elseif (!empty($end_date)) {
			$this->db->where('DATE(created) <=', $end_date);
		}

		// 🔹 Search
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
