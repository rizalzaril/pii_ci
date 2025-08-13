<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		//$this->load->library('security');
		$this->load->helper('security');
		$this->load->library('tank_auth');
		$this->lang->load('tank_auth');
	}

	function index()
	{
		if ($message = $this->session->flashdata('message')) {
			$this->load->view('auth/general_message', array('message' => $message));
		} else {
			redirect('/auth/login/');
		}
	}

	/**
	 * Login user on the site
	 *
	 * @return void
	 */
	function login()
	{
		if ($this->tank_auth->is_logged_in()) {									// logged in
			redirect('member');
		} elseif ($this->tank_auth->is_logged_in(FALSE)) {						// logged in, not activated
			redirect('/auth/send_again/');
		} else {
			$data['login_by_username'] = ($this->config->item('login_by_username', 'tank_auth') and	$this->config->item('use_username', 'tank_auth'));
			$data['login_by_email'] = $this->config->item('login_by_email', 'tank_auth');

			$this->form_validation->set_rules('login', 'Login', 'trim|required|xss_clean');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
			//$this->form_validation->set_rules('remember', 'Remember me', 'integer');

			// Get login for counting attempts to login
			if ($this->config->item('login_count_attempts', 'tank_auth') and ($login = $this->input->post('login'))) {
				$login = $this->security->xss_clean($login);
			} else {
				$login = '';
			}

			$data['use_recaptcha'] = $this->config->item('use_recaptcha', 'tank_auth');
			if ($this->tank_auth->is_max_login_attempts_exceeded($login)) {
				if ($data['use_recaptcha'])
					$this->form_validation->set_rules('recaptcha_response_field', 'Confirmation Code', 'trim|xss_clean|required|callback__check_recaptcha');
				else
					$this->form_validation->set_rules('captcha', 'Confirmation Code', 'trim|xss_clean|required|callback__check_captcha');
			}
			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if ($this->tank_auth->login(
					$this->form_validation->set_value('login'),
					$this->form_validation->set_value('password'),
					$this->form_validation->set_value('remember'),
					$data['login_by_username'],
					$data['login_by_email']
				)) {								// success

					$this->load->model('main_mod');
					$tmp = $this->main_mod->msrquery('select * from parameter ')->result();
					if (isset($tmp[0])) {
						if ($tmp[0]->value == '1') $this->session->set_userdata('is_pkb', '1');
					}

					redirect('member');
				} else {
					$errors = $this->tank_auth->get_error_message();
					if (isset($errors['banned'])) {								// banned user
						$this->_show_message($this->lang->line('auth_message_banned') . ' ' . $errors['banned']);
					} elseif (isset($errors['not_activated'])) {				// not activated user
						redirect('/auth/send_again/');
					} else {													// fail
						foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
					}
				}
			}
			$data['show_captcha'] = FALSE;
			if ($this->tank_auth->is_max_login_attempts_exceeded($login)) {
				$data['show_captcha'] = TRUE;
				if ($data['use_recaptcha']) {
					$data['recaptcha_html'] = $this->_create_recaptcha();
				} else {
					$data['captcha_html'] = $this->_create_captcha();
				}
			}
			$this->load->view('auth/login_form', $data);
		}
	}
	function check_kta_()
	{
		return;
		if ($this->tank_auth->is_logged_in()) {									// logged in
			redirect('member');
		} elseif ($this->tank_auth->is_logged_in(FALSE)) {						// logged in, not activated
			redirect('/auth/send_again/');
		} else {
			$data['errors'] = array();
			if ($this->input->get('e') == "1") {
				$data['errors']['kta'] = $this->lang->line('auth_incorrect_kta_or_dob');
			}

			$this->form_validation->set_rules('kta', 'No Unik KTA', 'trim|required|xss_clean');
			$this->form_validation->set_rules('dob', 'Tanggal Lahir', 'trim|required|xss_clean');

			/*$data['use_recaptcha'] = $this->config->item('use_recaptcha', 'tank_auth');
			if ($this->tank_auth->is_max_login_attempts_exceeded($login)) {
				if ($data['use_recaptcha'])
					$this->form_validation->set_rules('recaptcha_response_field', 'Confirmation Code', 'trim|xss_clean|required|callback__check_recaptcha');
				else
					$this->form_validation->set_rules('captcha', 'Confirmation Code', 'trim|xss_clean|required|callback__check_captcha');
			}*/


			if ($this->form_validation->run()) {								// validation ok
				if (!is_null($data = $this->tank_auth->check_kta(
					$this->form_validation->set_value('kta'),
					$this->form_validation->set_value('dob')
				))) {

					/*$data['site_name'] = $this->config->item('website_name', 'tank_auth');
					
					// Send email with password activation link
					$this->_send_email('forgot_password', $data['email'], $data);
					//print_r($data);
					$this->_show_message($this->lang->line('auth_message_new_password_sent'));*/

					redirect('auth/valid_kta?kta=' . $this->form_validation->set_value('kta') . '&dob=' . $this->form_validation->set_value('dob'));
				} else {
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
					//print_r($data);
				}
			}
			/*$data['show_captcha'] = FALSE;
			if ($this->tank_auth->is_max_login_attempts_exceeded($login)) {
				$data['show_captcha'] = TRUE;
				if ($data['use_recaptcha']) {
					$data['recaptcha_html'] = $this->_create_recaptcha();
				} else {
					$data['captcha_html'] = $this->_create_captcha();
				}
			}*/
			$this->load->view('auth/check_kta_form', $data);
		}
	}

	function valid_kta()
	{
		if ($this->tank_auth->is_logged_in()) {									// logged in
			redirect('member');
		} elseif ($this->tank_auth->is_logged_in(FALSE)) {						// logged in, not activated
			redirect('/auth/send_again/');
		} else {
			$kta = $this->input->get('kta');
			$dob = $this->input->get('dob');

			$data = $this->tank_auth->check_kta($kta, $dob);
			if (isset($data['user_id'])) {
				$this->form_validation->set_rules('kta', 'No Unik KTA', 'trim|required|xss_clean');
				$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean');
				$this->form_validation->set_rules('dob', 'Tanggal Lahir', 'trim|required|xss_clean');

				$data['errors'] = array();

				if (!$this->form_validation->run()) {								// validation ok
					$this->load->view('auth/valid_kta_form', $data);
				} else {
					$data['site_name'] = $this->config->item('website_name', 'tank_auth');
					$data = $this->tank_auth->check_kta(
						$this->form_validation->set_value('kta'),
						$this->form_validation->set_value('dob')
					);

					$temp = $this->tank_auth->set_email_default($this->form_validation->set_value('kta'), $this->form_validation->set_value('email'));

					$data['site_name'] = $this->config->item('website_name', 'tank_auth');
					//print_r($data);
					$this->_send_email('forgot_password', $this->form_validation->set_value('email'), $data);
					//redirect('auth');
					$this->_show_message($this->lang->line('auth_message_new_password_sent'));
				}
			} else {
				/*$this->error = array('kta' => 'auth_incorrect_kta_or_dob');
				$errors = $this->tank_auth->get_error_message();
				foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				$this->load->view('auth/check_kta_form', $data);*/
				redirect('auth/check_kta?e=1');
			}
		}
	}

	function alt_email()
	{
		if ($this->tank_auth->is_logged_in()) {									// logged in
			redirect('member');
		} elseif ($this->tank_auth->is_logged_in(FALSE)) {						// logged in, not activated
			redirect('/auth/send_again/');
		} else {
			$kta = $this->input->get('kta');
			$dob = $this->input->get('dob');
			$data = $this->tank_auth->check_kta($kta, $dob);
			$data['username'] = $kta;
			$data['dob'] = $dob;

			$this->load->model('members_model');
			$data["m_cab"] = $this->members_model->get_all_cabang();
			$data["m_bk"] = $this->members_model->get_all_bk();

			$this->form_validation->set_rules('kta', 'No Unik KTA', 'trim|required|xss_clean');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean');
			$this->form_validation->set_rules('kodeWilCab', 'Kode Wilayah', 'trim|required|xss_clean');
			$this->form_validation->set_rules('kodeBkHkk', 'BK HKK', 'trim|required|xss_clean');
			$this->form_validation->set_rules('dob', 'Tanggal Lahir', 'trim|required|xss_clean');

			$data['errors'] = array();

			if (!$this->form_validation->run()) {								// validation ok
				$this->load->view('auth/alt_email_form', $data);
			} else {
				$data['site_name'] = $this->config->item('website_name', 'tank_auth');
				$data = $this->tank_auth->check_kta_bykodewilbk(
					$this->form_validation->set_value('kta'),
					$this->form_validation->set_value('dob'),
					$this->form_validation->set_value('kodeWilCab'),
					$this->form_validation->set_value('kodeBkHkk'),
					$this->form_validation->set_value('email')
				);

				$temp = $this->tank_auth->set_email_default($this->form_validation->set_value('kta'), $this->form_validation->set_value('email'));

				$data['site_name'] = $this->config->item('website_name', 'tank_auth');
				//print_r($data);
				if (isset($data['user_id'])) {
					$this->_send_email('forgot_password', $this->form_validation->set_value('email'), $data);
					$this->_show_message($this->lang->line('auth_message_new_password_sent'));
				} else {
					//echo '<script>alert("not valid value");</script>';
					//edirect('auth');
					$this->_show_message('not valid value');
				}
			}
		}
	}

	function check_name_()
	{
		return;
		if ($this->tank_auth->is_logged_in()) {									// logged in
			redirect('member');
		} elseif ($this->tank_auth->is_logged_in(FALSE)) {						// logged in, not activated
			redirect('/auth/send_again/');
		} else {
			$this->form_validation->set_rules('nama', 'Nama', 'trim|required|xss_clean');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean');

			/*$data['use_recaptcha'] = $this->config->item('use_recaptcha', 'tank_auth');
			if ($this->tank_auth->is_max_login_attempts_exceeded($login)) {
				if ($data['use_recaptcha'])
					$this->form_validation->set_rules('recaptcha_response_field', 'Confirmation Code', 'trim|xss_clean|required|callback__check_recaptcha');
				else
					$this->form_validation->set_rules('captcha', 'Confirmation Code', 'trim|xss_clean|required|callback__check_captcha');
			}*/
			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if (!is_null($data = $this->tank_auth->check_name(
					$this->form_validation->set_value('nama'),
					$this->form_validation->set_value('email')
				))) {

					$temp = $this->tank_auth->set_email_default($data['username'], $this->form_validation->set_value('email'));

					$data['site_name'] = $this->config->item('website_name', 'tank_auth');
					//print_r($data);
					if (isset($data['user_id'])) {
						$this->_send_email('forgot_password', $this->form_validation->set_value('email'), $data);
					}
					//redirect('auth');
					$this->_show_message($this->lang->line('auth_message_new_password_sent'));
				} else {
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
					//print_r($data);
				}
			}
			/*$data['show_captcha'] = FALSE;
			if ($this->tank_auth->is_max_login_attempts_exceeded($login)) {
				$data['show_captcha'] = TRUE;
				if ($data['use_recaptcha']) {
					$data['recaptcha_html'] = $this->_create_recaptcha();
				} else {
					$data['captcha_html'] = $this->_create_captcha();
				}
			}*/
			$this->load->view('auth/check_name_form', $data);
		}
	}


	/**
	 * Logout user
	 *
	 * @return void
	 */
	function logout()
	{
		$this->tank_auth->logout();

		//$this->_show_message($this->lang->line('auth_message_logged_out'));
		redirect('');
	}

	/**
	 * Register user on the site
	 *
	 * @return void
	 */
	function register()
	{

		//$this->load->view('auth/info_form');

		if ($this->tank_auth->is_logged_in()) {									// logged in
			redirect('member');
		} elseif ($this->tank_auth->is_logged_in(FALSE)) {						// logged in, not activated
			redirect('/auth/send_again/');
		} elseif (!$this->config->item('allow_registration', 'tank_auth')) {	// registration is off
			$this->_show_message($this->lang->line('auth_message_registration_disabled'));
		} else {
			//Profile
			$this->form_validation->set_rules('firstname', 'First Name', 'trim|required|xss_clean');
			$this->form_validation->set_rules('lastname', 'Last Name', 'trim|xss_clean');
			$this->form_validation->set_rules('birthplace', 'Birth Place', 'trim|required|xss_clean');
			$this->form_validation->set_rules('dob', 'Date Of Birth', 'trim|required|xss_clean');
			$this->form_validation->set_rules('gender', 'Gender', 'trim|required|xss_clean');

			$use_username = $this->config->item('use_username', 'tank_auth');
			if ($use_username) {
				$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean|min_length[' . $this->config->item('username_min_length', 'tank_auth') . ']|max_length[' . $this->config->item('username_max_length', 'tank_auth') . ']|alpha_dash');
			}

			$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email|callback_email_check');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|min_length[' . $this->config->item('password_min_length', 'tank_auth') . ']|max_length[' . $this->config->item('password_max_length', 'tank_auth') . ']'); //|alpha_dash
			$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|xss_clean|matches[password]');

			$captcha_registration	= $this->config->item('captcha_registration', 'tank_auth');
			$use_recaptcha			= $this->config->item('use_recaptcha', 'tank_auth');
			if ($captcha_registration) {
				if ($use_recaptcha) {
					$this->form_validation->set_rules('recaptcha_response_field', 'Confirmation Code', 'trim|xss_clean|required|callback__check_recaptcha');
				} else {
					$this->form_validation->set_rules('captcha', 'Confirmation Code', 'trim|xss_clean|required|callback__check_captcha');
				}
			}
			$data['errors'] = array();

			$email_activation = $this->config->item('email_activation', 'tank_auth');

			$this->load->model('main_mod');

			if ($this->form_validation->run()) {								// validation ok
				if (!is_null($data = $this->tank_auth->create_user(
					$use_username ? $this->form_validation->set_value('username') : '',
					$this->form_validation->set_value('email'),
					$this->form_validation->set_value('password'),
					$email_activation
				))) {									// success

					$data['site_name'] = $this->config->item('website_name', 'tank_auth');

					if ($email_activation) {									// send "activate" email
						$data['activation_period'] = $this->config->item('email_activation_expire', 'tank_auth') / 3600;

						$this->_send_email('activate', $data['email'], $data);

						unset($data['password']); // Clear password (just for any case)

						$this->_show_message($this->lang->line('auth_message_registration_completed_1'));
					} else {
						if ($this->config->item('email_account_details', 'tank_auth')) {	// send "welcome" email

							$this->_send_email('welcome', $data['email'], $data);
						}
						unset($data['password']); // Clear password (just for any case)

						//PROFILE
						$where = array(
							"user_id" => $data['user_id']
						);
						$row = array(
							'firstname' => $this->form_validation->set_value('firstname'),
							'lastname' => $this->form_validation->set_value('lastname'),
							'birthplace' => $this->form_validation->set_value('birthplace'),
							'dob' => date('Y-m-d', strtotime($this->form_validation->set_value('dob'))),
							'gender' => $this->form_validation->set_value('gender'),
						);
						$update = $this->main_mod->update('user_profiles', $where, $row);

						//print_r($row);
						$this->_show_message($this->lang->line('auth_message_registration_completed_2') . ' ' . anchor('/auth/login/', 'Login'));
					}
				} else {

					$data['dob'] = $this->form_validation->set_value('dob');

					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			if ($captcha_registration) {
				if ($use_recaptcha) {
					$data['recaptcha_html'] = $this->_create_recaptcha();
				} else {
					$data['captcha_html'] = $this->_create_captcha();
				}
			}


			$data['use_username'] = $use_username;
			$data['captcha_registration'] = $captcha_registration;
			$data['use_recaptcha'] = $use_recaptcha;

			//print_r($this->form_validation);
			$this->load->view('auth/register_form', $data);
		}
	}

	public function email_check($post_email)
	{

		$this->db->where('email', $post_email);

		$query = $this->db->get('users');

		$count_row = $query->num_rows();

		if ($count_row > 0) {
			$this->form_validation->set_message('email_check', 'The email address you have entered is already registered,');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function register_bc()
	{
		if ($this->tank_auth->is_logged_in()) {									// logged in
			redirect('member');
		} elseif ($this->tank_auth->is_logged_in(FALSE)) {						// logged in, not activated
			redirect('/auth/send_again/');
		} elseif (!$this->config->item('allow_registration', 'tank_auth')) {	// registration is off
			$this->_show_message($this->lang->line('auth_message_registration_disabled'));
		} else {
			//Profile
			$this->form_validation->set_rules('fn', 'Firstname', 'trim|required|xss_clean');
			$this->form_validation->set_rules('ln', 'Lastname', 'trim|required|xss_clean');
			$this->form_validation->set_rules('gender', 'Gender', 'trim|required|xss_clean');
			$this->form_validation->set_rules('ktp', 'ID', 'trim|required|xss_clean');
			$this->form_validation->set_rules('dob', 'Date of Birth', 'trim|required|xss_clean');
			$this->form_validation->set_rules('website', 'Website', 'trim|xss_clean');
			$this->form_validation->set_rules('is_public', 'is_public', 'trim|xss_clean');
			$this->form_validation->set_rules('is_datasend', 'is_datasend', 'trim|xss_clean');
			$this->form_validation->set_rules('desc', 'Description', 'trim|xss_clean');
			$this->form_validation->set_rules('mailingaddr', 'mailing addr', 'trim|xss_clean');

			$this->form_validation->set_rules('fieldofexpert', 'fieldofexpert', 'trim|xss_clean');
			$this->form_validation->set_rules('subfield', 'subfield', 'trim|xss_clean');
			$this->form_validation->set_rules('accauth', 'accauth', 'trim|xss_clean');
			$this->form_validation->set_rules('filename', 'filename', 'trim|xss_clean');
			$this->form_validation->set_rules('desc2', 'desc2', 'trim|xss_clean');


			$this->form_validation->set_rules('email[]', 'Email', 'trim|required|xss_clean|valid_email');


			$use_username = $this->config->item('use_username', 'tank_auth');
			if ($use_username) {
				$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean|min_length[' . $this->config->item('username_min_length', 'tank_auth') . ']|max_length[' . $this->config->item('username_max_length', 'tank_auth') . ']|alpha_dash');
			}
			/*			
			$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|min_length['.$this->config->item('password_min_length', 'tank_auth').']|max_length['.$this->config->item('password_max_length', 'tank_auth').']|alpha_dash');
			$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|xss_clean|matches[password]');
*/
			$captcha_registration	= $this->config->item('captcha_registration', 'tank_auth');
			$use_recaptcha			= $this->config->item('use_recaptcha', 'tank_auth');
			if ($captcha_registration) {
				if ($use_recaptcha) {
					$this->form_validation->set_rules('recaptcha_response_field', 'Confirmation Code', 'trim|xss_clean|required|callback__check_recaptcha');
				} else {
					$this->form_validation->set_rules('captcha', 'Confirmation Code', 'trim|xss_clean|required|callback__check_captcha');
				}
			}
			$data['errors'] = array();

			$email_activation = $this->config->item('email_activation', 'tank_auth');

			$this->load->model('main_mod');

			$data['typephone'] = $this->input->post('typephone[]') <> null ? $this->input->post('typephone[]') : "";
			$data['phone'] = $this->input->post('phone[]') <> null ? $this->input->post('phone[]') : "";

			$data['typeemail'] = $this->input->post('typeemail[]') <> null ? $this->input->post('typeemail[]') : "";
			$data['email'] = $this->input->post('email[]') <> null ? $this->input->post('email[]') : "";

			$data['typeaddress'] = $this->input->post('typeaddress[]') <> null ? $this->input->post('typeaddress[]') : "";
			$data['address'] = $this->input->post('address[]') <> null ? $this->input->post('address[]') : "";
			$data['addressphone'] = $this->input->post('addressphone[]') <> null ? $this->input->post('addressphone[]') : "";
			$data['addresszip'] = $this->input->post('addresszip[]') <> null ? $this->input->post('addresszip[]') : "";



			if ($this->form_validation->run()) {								// validation ok
				/*if (!is_null($data = $this->tank_auth->create_user(
						$use_username ? $this->form_validation->set_value('username') : '',
						$this->form_validation->set_value('email'),
						$this->form_validation->set_value('password'),
						$email_activation))) {									// success*/

				if (!is_null($data = $this->tank_auth->create_user(
					$use_username ? $this->form_validation->set_value('username') : '',
					$data['email'][0],
					'',
					$email_activation
				))) {									// success

					$data['site_name'] = $this->config->item('website_name', 'tank_auth');

					if ($email_activation) {									// send "activate" email
						$data['activation_period'] = $this->config->item('email_activation_expire', 'tank_auth') / 3600;

						$this->_send_email('activate', $data['email'], $data);

						unset($data['password']); // Clear password (just for any case)

						$this->_show_message($this->lang->line('auth_message_registration_completed_1'));
					} else {
						if ($this->config->item('email_account_details', 'tank_auth')) {	// send "welcome" email

							$this->_send_email('welcome', $data['email'], $data);
						}
						unset($data['password']); // Clear password (just for any case)

						$data['typephone'] = $this->input->post('typephone[]') <> null ? $this->input->post('typephone[]') : "";
						$data['phone'] = $this->input->post('phone[]') <> null ? $this->input->post('phone[]') : "";

						$data['typeemail'] = $this->input->post('typeemail[]') <> null ? $this->input->post('typeemail[]') : "";
						$data['email'] = $this->input->post('email[]') <> null ? $this->input->post('email[]') : "";

						$data['typeaddress'] = $this->input->post('typeaddress[]') <> null ? $this->input->post('typeaddress[]') : "";
						$data['address'] = $this->input->post('address[]') <> null ? $this->input->post('address[]') : "";
						$data['addressphone'] = $this->input->post('addressphone[]') <> null ? $this->input->post('addressphone[]') : "";
						$data['addresszip'] = $this->input->post('addresszip[]') <> null ? $this->input->post('addresszip[]') : "";


						//UPLOAD
						$document = $this->upload();
						$nameDoc = "";
						if (isset($document['status'])) {
							$nameDoc = $document['message'];
						}

						//PROFILE
						$where = array(
							"user_id" => $data['user_id']
						);
						$row = array(
							'firstname' => $this->form_validation->set_value('fn'),
							'lastname' => $this->form_validation->set_value('ln'),
							'gender' => $this->form_validation->set_value('gender'),
							'idcard' => $this->form_validation->set_value('ktp'),
							'dob' => $this->form_validation->set_value('dob'),
							'website' => $this->form_validation->set_value('website'),
							'is_public' => $this->form_validation->set_value('is_public'),
							'is_datasend' => $this->form_validation->set_value('is_datasend'),
							'description' => $this->form_validation->set_value('desc'),

							'fieldofexpert' => $this->form_validation->set_value('fieldofexpert'),
							'accauth' => $this->form_validation->set_value('accauth'),
							'subfield' => $this->form_validation->set_value('subfield'),
							'document' => $nameDoc,
							'description2' => $this->form_validation->set_value('desc2')
						);
						$update = $this->main_mod->update('user_profiles', $where, $row);
						//PHONE
						$i = 0;
						foreach ($data['typephone'] as $val) {
							$row = array(
								'user_id' => $data['user_id'],
								'phonetype' => $data['typephone'][$i],
								'phonenumber' => $data['phone'][$i]
							);
							$insert = $this->main_mod->insert('user_phone', $row);
							$i++;
						}
						//EMAIL
						$i = 0;
						foreach ($data['typeemail'] as $val) {
							$row = array(
								'user_id' => $data['user_id'],
								'emailtype' => $data['typeemail'][$i],
								'email' => $data['email'][$i]
							);
							$insert = $this->main_mod->insert('user_email', $row);
							$i++;
						}
						//ADDRESS
						$i = 0;
						$mailing = $this->form_validation->set_value('mailingaddr');
						$mailing = $mailing - 1;
						foreach ($data['typeaddress'] as $val) {
							$temp = 0;
							if ($mailing == $i)
								$temp = 1;
							$row = array(
								'user_id' => $data['user_id'],
								'addresstype' => $data['typeaddress'][$i],
								'address' => $data['address'][$i],
								'notelp' => $data['addressphone'][$i],
								'zipcode' => $data['addresszip'][$i],
								'is_mailing' => $temp
							);
							$insert = $this->main_mod->insert('user_address', $row);
							$i++;
						}

						//EXPERIENCE
						$title = $this->input->post('title[]') <> null ? $this->input->post('title[]') : "";
						$company = $this->input->post('company[]') <> null ? $this->input->post('company[]') : "";
						$loc = $this->input->post('loc[]') <> null ? $this->input->post('loc[]') : "";
						$year = $this->input->post('year[]') <> null ? $this->input->post('year[]') : "";
						$year2 = $this->input->post('year2[]') <> null ? $this->input->post('year2[]') : "";
						$typetimeperiod = $this->input->post('typetimeperiod[]') <> null ? $this->input->post('typetimeperiod[]') : "";
						$typetimeperiod2 = $this->input->post('typetimeperiod2[]') <> null ? $this->input->post('typetimeperiod2[]') : "";
						$work = $this->input->post('work[]') <> null ? $this->input->post('work[]') : "";
						$desc = $this->input->post('desc[]') <> null ? $this->input->post('desc[]') : "";
						$i = 1;
						if (isset($title[1])) {
							foreach ($title as $val) {
								$check = $this->main_mod->msrwhere('m_company', array('desc' => $company[$i]), 'id', 'desc')->result();
								if (!isset($check[0])) {
									$row = array(
										'desc' => $company[$i],
									);
									$insert = $this->main_mod->insert('m_company', $row);
								}

								$row = array(
									'user_id' => $data['user_id'],
									'company' => $company[$i],
									'title' => $title[$i],
									'location' => $loc[$i],
									'startyear' => $year[$i],
									'startmonth' => $typetimeperiod[$i],
									'endyear' => $year2[$i],
									'endmonth' => $typetimeperiod2[$i],
									'is_present' => ($work[$i] == "true" ? "1" : "0"),
									'description' => $desc[$i]
								);
								$insert = $this->main_mod->insert('user_exp', $row);
								$i++;
							}
						}
						//EDUCATION
						$school = $this->input->post('school[]') <> null ? $this->input->post('school[]') : "";
						$startdate = $this->input->post('dateattend[]') <> null ? $this->input->post('dateattend[]') : "";
						$enddate = $this->input->post('dateattend2[]') <> null ? $this->input->post('dateattend2[]') : "";
						$degree = $this->input->post('degree[]') <> null ? $this->input->post('degree[]') : "";
						$fieldofstudy = $this->input->post('fos[]') <> null ? $this->input->post('fos[]') : "";
						$grade = $this->input->post('grade[]') <> null ? $this->input->post('grade[]') : "";
						$score = $this->input->post('score[]') <> null ? $this->input->post('score[]') : "";
						$activities = $this->input->post('actv[]') <> null ? $this->input->post('actv[]') : "";
						$description = $this->input->post('descedu[]') <> null ? $this->input->post('descedu[]') : "";
						$i = 1;
						if (isset($school[1])) {
							foreach ($school as $val) {
								$row = array(
									'user_id' => $data['user_id'],
									'school' => $school[$i],
									'startdate' => $startdate[$i],
									'enddate' => $enddate[$i],
									'degree' => $degree[$i],
									'fieldofstudy' => $fieldofstudy[$i],
									'grade' => $grade[$i],
									'score' => $score[$i],
									'activities' => $activities[$i],
									'description' => $description[$i]
								);
								$insert = $this->main_mod->insert('user_edu', $row);
								$i++;
							}
						}
						//CERTIFICATIONS
						$cert_name = $this->input->post('certname[]') <> null ? $this->input->post('certname[]') : "";
						$cert_auth = $this->input->post('certauth[]') <> null ? $this->input->post('certauth[]') : "";
						$lic_num = $this->input->post('lic[]') <> null ? $this->input->post('lic[]') : "";
						$cert_url = $this->input->post('url[]') <> null ? $this->input->post('url[]') : "";
						$startmonth = $this->input->post('certdate[]') <> null ? $this->input->post('certdate[]') : "";
						$startyear = $this->input->post('certyear[]') <> null ? $this->input->post('certyear[]') : "";
						$endmonth = $this->input->post('certdate2[]') <> null ? $this->input->post('certdate2[]') : "";
						$endyear = $this->input->post('certyear2[]') <> null ? $this->input->post('certyear2[]') : "";
						$is_present = $this->input->post('certwork[]') <> null ? $this->input->post('certwork[]') : "";
						$description = $this->input->post('certdesc[]') <> null ? $this->input->post('certdesc[]') : "";
						$i = 1;
						if (isset($cert_name[1])) {
							foreach ($cert_name as $val) {
								$row = array(
									'user_id' => $data['user_id'],
									'cert_name' => $cert_name[$i],
									'cert_auth' => $cert_auth[$i],
									'lic_num' => $lic_num[$i],
									'cert_url' => $cert_url[$i],
									'startmonth' => $startmonth[$i],
									'startyear' => $startyear[$i],
									'endmonth' => $endmonth[$i],
									'endyear' => $endyear[$i],
									'is_present' => ($is_present[$i] == "true" ? "1" : "0"),
									'description' => $description[$i]
								);
								$insert = $this->main_mod->insert('user_cert', $row);
								$i++;
							}
						}
						//ORGANIZATIONS
						$organization = $this->input->post('org[]') <> null ? $this->input->post('org[]') : "";
						$position = $this->input->post('posit[]') <> null ? $this->input->post('posit[]') : "";
						$occupation = $this->input->post('occ[]') <> null ? $this->input->post('occ[]') : "";
						$startmonth = $this->input->post('orgdate[]') <> null ? $this->input->post('orgdate[]') : "";
						$startyear = $this->input->post('orgyear[]') <> null ? $this->input->post('orgyear[]') : "";
						$endmonth = $this->input->post('orgdate2[]') <> null ? $this->input->post('orgdate2[]') : "";
						$endyear = $this->input->post('orgyear2[]') <> null ? $this->input->post('orgyear2[]') : "";
						$is_present = $this->input->post('orgwork[]') <> null ? $this->input->post('orgwork[]') : "";
						$description = $this->input->post('orgdesc[]') <> null ? $this->input->post('orgdesc[]') : "";
						$i = 1;
						if (isset($organization[1])) {
							foreach ($organization as $val) {
								$row = array(
									'user_id' => $data['user_id'],
									'organization' => $organization[$i],
									'position' => $position[$i],
									'occupation' => $occupation[$i],
									'startmonth' => $startmonth[$i],
									'startyear' => $startyear[$i],
									'endmonth' => $endmonth[$i],
									'endyear' => $endyear[$i],
									'is_present' => ($is_present[$i] == "true" ? "1" : "0"),
									'description' => $description[$i]
								);
								$insert = $this->main_mod->insert('user_org', $row);
								$i++;
							}
						}
						//AWARD
						$name = $this->input->post('awardname[]') <> null ? $this->input->post('awardname[]') : "";
						$issue = $this->input->post('issue[]') <> null ? $this->input->post('issue[]') : "";
						$description = $this->input->post('awarddesc[]') <> null ? $this->input->post('awarddesc[]') : "";
						$i = 1;
						if (isset($name[1])) {
							foreach ($name as $val) {
								$row = array(
									'user_id' => $data['user_id'],
									'name' => $name[$i],
									'issue' => $issue[$i],
									'description' => $description[$i]
								);
								$insert = $this->main_mod->insert('user_award', $row);
								$i++;
							}
						}
						//COURSES
						$coursename = $this->input->post('coursename[]') <> null ? $this->input->post('coursename[]') : "";
						$hour = $this->input->post('hour[]') <> null ? $this->input->post('hour[]') : "";
						$courseorg = $this->input->post('courseorg[]') <> null ? $this->input->post('courseorg[]') : "";
						$startmonth = $this->input->post('courseperiod[]') <> null ? $this->input->post('courseperiod[]') : "";
						$startyear = $this->input->post('courseyear[]') <> null ? $this->input->post('courseyear[]') : "";
						$endmonth = $this->input->post('courseperiod2[]') <> null ? $this->input->post('courseperiod2[]') : "";
						$endyear = $this->input->post('courseyear2[]') <> null ? $this->input->post('courseyear2[]') : "";
						$i = 1;
						if (isset($coursename[1])) {
							foreach ($coursename as $val) {
								$row = array(
									'user_id' => $data['user_id'],
									'coursename' => $coursename[$i],
									'hour' => $hour[$i],
									'courseorg' => $courseorg[$i],
									'startmonth' => $startmonth[$i],
									'startyear' => $startyear[$i],
									'endmonth' => $endmonth[$i],
									'endyear' => $endyear[$i]
								);
								$insert = $this->main_mod->insert('user_course', $row);
								$i++;
							}
						}
						//PROFESIONAL
						$organization = $this->input->post('proforg[]') <> null ? $this->input->post('proforg[]') : "";
						$type = $this->input->post('proftype[]') <> null ? $this->input->post('proftype[]') : "";
						$position = $this->input->post('profposit[]') <> null ? $this->input->post('profposit[]') : "";
						$startmonth = $this->input->post('profperiod[]') <> null ? $this->input->post('profperiod[]') : "";
						$startyear = $this->input->post('profyear[]') <> null ? $this->input->post('profyear[]') : "";
						$endmonth = $this->input->post('profperiod2[]') <> null ? $this->input->post('profperiod2[]') : "";
						$endyear = $this->input->post('profyear2[]') <> null ? $this->input->post('profyear2[]') : "";
						$subject = $this->input->post('profsubject[]') <> null ? $this->input->post('profsubject[]') : "";
						$description = $this->input->post('profdesc[]') <> null ? $this->input->post('profdesc[]') : "";
						$i = 1;
						if (isset($organization[1])) {
							foreach ($organization as $val) {
								$row = array(
									'user_id' => $data['user_id'],
									'organization' => $organization[$i],
									'type' => $type[$i],
									'position' => $position[$i],
									'subject' => $subject[$i],
									'description' => $description[$i],
									'startmonth' => $startmonth[$i],
									'startyear' => $startyear[$i],
									'endmonth' => $endmonth[$i],
									'endyear' => $endyear[$i]
								);
								$insert = $this->main_mod->insert('user_prof', $row);
								$i++;
							}
						}
						//PUBLICATION
						$topic = $this->input->post('publicationtopic[]') <> null ? $this->input->post('publicationtopic[]') : "";
						$type = $this->input->post('publicationtype[]') <> null ? $this->input->post('publicationtype[]') : "";
						$media = $this->input->post('publicationmedia[]') <> null ? $this->input->post('publicationmedia[]') : "";
						$startmonth = $this->input->post('publicationperiod[]') <> null ? $this->input->post('publicationperiod[]') : "";
						$startyear = $this->input->post('publicationyear[]') <> null ? $this->input->post('publicationyear[]') : "";
						$endmonth = $this->input->post('publicationperiod2[]') <> null ? $this->input->post('publicationperiod2[]') : "";
						$endyear = $this->input->post('publicationyear2[]') <> null ? $this->input->post('publicationyear2[]') : "";
						$journal = $this->input->post('publicationjournal[]') <> null ? $this->input->post('publicationjournal[]') : "";
						$event = $this->input->post('publicationevent[]') <> null ? $this->input->post('publicationevent[]') : "";
						$description = $this->input->post('publicationdesc[]') <> null ? $this->input->post('publicationdesc[]') : "";
						$i = 1;
						if (isset($topic[1])) {
							foreach ($topic as $val) {
								$row = array(
									'user_id' => $data['user_id'],
									'topic' => $topic[$i],
									'media' => $media[$i],
									'type' => $type[$i],
									'journal' => $journal[$i],
									'event' => $event[$i],
									'description' => $description[$i],
									'startmonth' => $startmonth[$i],
									'startyear' => $startyear[$i],
									'endmonth' => $endmonth[$i],
									'endyear' => $endyear[$i]
								);
								$insert = $this->main_mod->insert('user_publication', $row);
								$i++;
							}
						}
						//SKILL
						$name = $this->input->post('skillname[]') <> null ? $this->input->post('skillname[]') : "";
						$proficiency = $this->input->post('proficiency[]') <> null ? $this->input->post('proficiency[]') : "";
						$description = $this->input->post('skilldesc[]') <> null ? $this->input->post('skilldesc[]') : "";
						$i = 1;
						if (isset($name[1])) {
							foreach ($name as $val) {
								$row = array(
									'user_id' => $data['user_id'],
									'name' => $name[$i],
									'proficiency' => $proficiency[$i],
									'description' => $description[$i]
								);
								$insert = $this->main_mod->insert('user_skill', $row);
								$i++;
							}
						}
						//print_r($row);
						$this->_show_message($this->lang->line('auth_message_registration_completed_2') . ' ' . anchor('/auth/login/', 'Login'));
					}
				} else {
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			if ($captcha_registration) {
				if ($use_recaptcha) {
					$data['recaptcha_html'] = $this->_create_recaptcha();
				} else {
					$data['captcha_html'] = $this->_create_captcha();
				}
			}


			$data['use_username'] = $use_username;
			$data['captcha_registration'] = $captcha_registration;
			$data['use_recaptcha'] = $use_recaptcha;

			$data['m_phone'] = $this->main_mod->msr('m_phone', 'id', 'asc')->result();
			$data['m_address'] = $this->main_mod->msr('m_address', 'id', 'asc')->result();
			$data['m_company'] = $this->main_mod->msr('m_company', 'id', 'asc')->result();
			$data['m_proftype'] = $this->main_mod->msr('m_proftype', 'id', 'asc')->result();
			$data['m_publicjurnal'] = $this->main_mod->msr('m_publicjurnal', 'id', 'asc')->result();
			$data['m_publictype'] = $this->main_mod->msr('m_publictype', 'id', 'asc')->result();

			$data['m_fieldofexpert'] = $this->main_mod->msr('m_fieldofexpert', 'id', 'asc')->result();
			$data['m_accauth'] = $this->main_mod->msr('m_accauth', 'id', 'asc')->result();
			$data['m_subfield'] = $this->main_mod->msr('m_subfield', 'id', 'asc')->result();




			//print_r($this->form_validation);
			$this->load->view('auth/register_form', $data);
		}
	}

	/**
	 * Send activation email again, to the same or new email address
	 *
	 * @return void
	 */
	function send_again()
	{
		if (!$this->tank_auth->is_logged_in(FALSE)) {							// not logged in or activated
			redirect('/auth/login/');
		} else {
			$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');

			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if (!is_null($data = $this->tank_auth->change_email(
					$this->form_validation->set_value('email')
				))) {			// success

					$data['site_name']	= $this->config->item('website_name', 'tank_auth');
					$data['activation_period'] = $this->config->item('email_activation_expire', 'tank_auth') / 3600;

					$this->_send_email('activate', $data['email'], $data);

					$this->_show_message(sprintf($this->lang->line('auth_message_activation_email_sent'), $data['email']));
				} else {
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			$this->load->view('auth/send_again_form', $data);
		}
	}

	/**
	 * Activate user account.
	 * User is verified by user_id and authentication code in the URL.
	 * Can be called by clicking on link in mail.
	 *
	 * @return void
	 */
	function activate()
	{
		$user_id		= $this->uri->segment(3);
		$new_email_key	= $this->uri->segment(4);

		// Activate user
		if ($this->tank_auth->activate_user($user_id, $new_email_key)) {		// success
			$this->tank_auth->logout();
			$this->_show_message($this->lang->line('auth_message_activation_completed') . ' ' . anchor('/auth/login/', 'Login'));
		} else {																// fail
			$this->_show_message($this->lang->line('auth_message_activation_failed'));
		}
	}

	/**
	 * Generate reset code (to change password) and send it to user
	 *
	 * @return void
	 */
	function forgot_password()
	{
		if ($this->tank_auth->is_logged_in()) {									// logged in
			redirect('member');
		} elseif ($this->tank_auth->is_logged_in(FALSE)) {						// logged in, not activated
			redirect('/auth/send_again/');
		} else {
			$this->form_validation->set_rules('login', 'Email or login', 'trim|required|xss_clean');

			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if (!is_null($data = $this->tank_auth->forgot_password(
					$this->form_validation->set_value('login')
				))) {

					$data['site_name'] = $this->config->item('website_name', 'tank_auth');

					// Send email with password activation link
					$this->_send_email('forgot_password', $data['email'], $data);
					//phpinfo();
					$this->_show_message($this->lang->line('auth_message_new_password_sent'));
				} else {
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			$this->load->view('auth/forgot_password_form', $data);
		}
	}

	/**
	 * Replace user password (forgotten) with a new one (set by user).
	 * User is verified by user_id and authentication code in the URL.
	 * Can be called by clicking on link in mail.
	 *
	 * @return void
	 */
	function reset_password()
	{
		$user_id		= $this->uri->segment(3);
		$new_pass_key	= $this->uri->segment(4);

		$this->form_validation->set_rules('new_password', 'New Password', 'trim|required|xss_clean|min_length[' . $this->config->item('password_min_length', 'tank_auth') . ']|max_length[' . $this->config->item('password_max_length', 'tank_auth') . ']|alpha_dash');
		$this->form_validation->set_rules('confirm_new_password', 'Confirm new Password', 'trim|required|xss_clean|matches[new_password]');

		$data['errors'] = array();

		if ($this->form_validation->run()) {								// validation ok
			if (!is_null($data = $this->tank_auth->reset_password(
				$user_id,
				$new_pass_key,
				$this->form_validation->set_value('new_password')
			))) {	// success

				$data['site_name'] = $this->config->item('website_name', 'tank_auth');

				// Send email with new password
				$this->_send_email('reset_password', $data['email'], $data);

				$this->_show_message($this->lang->line('auth_message_new_password_activated') . ' ' . anchor('/auth/login/', 'Login'));
			} else {														// fail
				$this->_show_message($this->lang->line('auth_message_new_password_failed'));
			}
		} else {
			// Try to activate user by password key (if not activated yet)
			if ($this->config->item('email_activation', 'tank_auth')) {
				$this->tank_auth->activate_user($user_id, $new_pass_key, FALSE);
			}

			if (!$this->tank_auth->can_reset_password($user_id, $new_pass_key)) {
				$this->_show_message($this->lang->line('auth_message_new_password_failed'));
			}
		}
		$this->load->view('auth/reset_password_form', $data);
	}

	/**
	 * Change user password
	 *
	 * @return void
	 */
	function change_password()
	{
		if (!$this->tank_auth->is_logged_in()) {								// not logged in or not activated
			redirect('/auth/login/');
		} else {
			$this->form_validation->set_rules('old_password', 'Old Password', 'trim|required|xss_clean');
			$this->form_validation->set_rules('new_password', 'New Password', 'trim|required|xss_clean|min_length[' . $this->config->item('password_min_length', 'tank_auth') . ']|max_length[' . $this->config->item('password_max_length', 'tank_auth') . ']|alpha_dash');
			$this->form_validation->set_rules('confirm_new_password', 'Confirm new Password', 'trim|required|xss_clean|matches[new_password]');

			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if ($this->tank_auth->change_password(
					$this->form_validation->set_value('old_password'),
					$this->form_validation->set_value('new_password')
				)) {	// success
					$this->_show_message($this->lang->line('auth_message_password_changed'));
				} else {														// fail
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			$this->load->view('auth/change_password_form', $data);
		}
	}

	/**
	 * Change user email
	 *
	 * @return void
	 */
	function change_email()
	{
		if (!$this->tank_auth->is_logged_in()) {								// not logged in or not activated
			redirect('/auth/login/');
		} else {
			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');

			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if (!is_null($data = $this->tank_auth->set_new_email(
					$this->form_validation->set_value('email'),
					$this->form_validation->set_value('password')
				))) {			// success

					$data['site_name'] = $this->config->item('website_name', 'tank_auth');

					// Send email with new email address and its activation link
					$this->_send_email('change_email', $data['new_email'], $data);

					$this->_show_message(sprintf($this->lang->line('auth_message_new_email_sent'), $data['new_email']));
				} else {
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			$this->load->view('auth/change_email_form', $data);
		}
	}

	/**
	 * Replace user email with a new one.
	 * User is verified by user_id and authentication code in the URL.
	 * Can be called by clicking on link in mail.
	 *
	 * @return void
	 */
	function reset_email()
	{
		$user_id		= $this->uri->segment(3);
		$new_email_key	= $this->uri->segment(4);

		// Reset email
		if ($this->tank_auth->activate_new_email($user_id, $new_email_key)) {	// success
			$this->tank_auth->logout();
			$this->_show_message($this->lang->line('auth_message_new_email_activated') . ' ' . anchor('/auth/login/', 'Login'));
		} else {																// fail
			$this->_show_message($this->lang->line('auth_message_new_email_failed'));
		}
	}

	/**
	 * Delete user from the site (only when user is logged in)
	 *
	 * @return void
	 */
	function unregister()
	{
		if (!$this->tank_auth->is_logged_in()) {								// not logged in or not activated
			redirect('/auth/login/');
		} else {
			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');

			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if ($this->tank_auth->delete_user(
					$this->form_validation->set_value('password')
				)) {		// success
					$this->_show_message($this->lang->line('auth_message_unregistered'));
				} else {														// fail
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			$this->load->view('auth/unregister_form', $data);
		}
	}

	/**
	 * Show info message
	 *
	 * @param	string
	 * @return	void
	 */
	function _show_message($message)
	{
		$this->session->set_flashdata('message', $message);
		redirect('/auth/');
	}

	/**
	 * Send email message of given type (activate, forgot_password, etc.)
	 *
	 * @param	string
	 * @param	string
	 * @param	array
	 * @return	void
	 */
	function _send_email($type, $email, &$data)
	{






		$this->load->library('MyPHPMailer');
		$mail = new PHPMailer();


		//$mail->IsSMTP(true); // we are going to use SMTP
		$mail->SMTPAuth   = true; // enabled SMTP authentication
		/* // 16Apr2024 - Commented by Eryan due to Google Workspace is not working anymore - migrating to Microsoft365 	
		$mail->SMTPSecure = "ssl";  // prefix for secure protocol to connect to the server
                $mail->Host       = "smtp.gmail.com";      // setting GMail as our SMTP server
                $mail->Port       = 465;                   // SMTP port to connect to GMail
                //$mail->Username   = "updmember@gmail.com";  // user email address
                //$mail->Password   = "serimpi37!1";            // password in GMail  serimpi37!1
		
		$mail->Username   = "simponi@pii.or.id";  // user email address
                $mail->Password   = "S!mponi@PII";            // password in GMail  serimpi37!1
		*/

		$mail->IsSMTP(true);
		$mail->Host = 'smtp.office365.com';
		$mail->Port       = 587;
		$mail->SMTPSecure = 'tls';
		$mail->SMTPAuth   = true;
		$mail->Username = 'simponi@pii.or.id';
		$mail->Password = 'ndkgyyllfkzhbmzz';
		//$mail->SMTPDebug  = 2;
		//$mail->Debugoutput = function($str, $level) {echo "debug level $level; message: $str";};


		$mail->SetFrom('simponi@pii.or.id', 'simponi');  //Who is sending the email
		$mail->AddReplyTo('simponi@pii.or.id', 'simponi');

		/*
		$mail->SMTPSecure = "tls";  // prefix for secure protocol to connect to the server
                $mail->Host       = "mail.pii.or.id";      // setting GMail as our SMTP server
                $mail->Port       = 587;                   // SMTP port to connect to GMail
                $mail->Username   = "updmember@pii.or.id";  // user email address
                $mail->Password   = "123456789";            // password in GMail
                $mail->SetFrom('updmember@pii.or.id', 'updmember');  //Who is sending the email       
		 */

		/* 16 April 2024 - Commented by Eryan - Migrating to MS O365 
		$mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
		);
		*/



		$mail->Subject    = sprintf($this->lang->line('auth_subject_' . $type), $this->config->item('website_name', 'tank_auth'));
		$mail->Body      	= $this->load->view('email/' . $type . '-html', $data, TRUE);
		$mail->AltBody    = $this->load->view('email/' . $type . '-txt', $data, TRUE);
		$destino = $email;
		$mail->AddAddress($destino);

		//$mail->addBcc("blank.anonim4@gmail.com");

		try {
			if (!$mail->Send()) {
				echo "Error: " . $mail->ErrorInfo;
			}
		} catch (Exception $e) {
			echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}

















		/*
		   $config = [
               'mailtype'  => 'html',
               'charset'   => 'utf-8',
               'protocol'  => 'smtp',
               'smtp_host' => 'mail.pii.or.id',
               'smtp_user' => 'updmember@pii.or.id',    // Ganti dengan email gmail kamu
               'smtp_pass' => '123456789',      // Password gmail kamu
               'smtp_port' =>  587,
			   //'starttls'  =>  true,
			   'smtp_crypto' => 'tls',
           ];
		/*
		$config = [
               'mailtype'  => 'html',
               'charset'   => 'utf-8',
               'protocol'  => 'smtp',
               'smtp_host' => 'smtp.gmail.com',
               'smtp_user' => 'blank.anonim5@gmail.com',    // Ganti dengan email gmail kamu
               'smtp_pass' => '',      // Password gmail kamu
               'smtp_port' => 587,
			   'starttls'  => true,
           ];  */
		/* 
		$config['smtp_timeout'] = 5;
		$config['wordwrap'] = TRUE;
		$config['wrapchars'] = 76;
		$config['validate'] = FALSE;
		$config['priority'] = 3;
		$config['crlf'] = "\r\n";
		$config['newline'] = "\r\n";
		$config['bcc_batch_mode'] = FALSE;
		$config['bcc_batch_size'] = 200;   
		   




		   

        // Load library email dan konfigurasinya
        //$this->load->library('email', $config);
		
		//$this->load->library('email');
		
		$this->email->initialize($config);
		
		$this->email->from($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
		$this->email->reply_to($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
		$this->email->to($email);
		$this->email->subject(sprintf($this->lang->line('auth_subject_'.$type), $this->config->item('website_name', 'tank_auth')));
		$this->email->message($this->load->view('email/'.$type.'-html', $data, TRUE));
		$this->email->set_alt_message($this->load->view('email/'.$type.'-txt', $data, TRUE));
		$this->email->set_crlf( "\r\n" );
		try {
			$this->email->send();
			//echo $this->email->print_debugger();
		} catch (Exception $e) {
			echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
		//$headers = "From: angger.ardiatma@adaro.com\r\n";
		//$headers .= "Reply-To: angger.ardiatma@adaro.com\r\n";
		//$headers .= "CC: susan@example.com\r\n";
		//$headers .= "MIME-Version: 1.0\r\n";
		//$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		
		//mail($email, sprintf($this->lang->line('auth_subject_'.$type), $this->config->item('website_name', 'tank_auth')), $this->load->view('email/'.$type.'-html', $data, TRUE), $headers);
		*/
	}

	/**
	 * Create CAPTCHA image to verify user as a human
	 *
	 * @return	string
	 */
	function _create_captcha()
	{
		$this->load->helper('captcha');

		$cap = create_captcha(array(
			'img_path'		=> './' . $this->config->item('captcha_path', 'tank_auth'),
			'img_url'		=> base_url() . $this->config->item('captcha_path', 'tank_auth'),
			'font_path'		=> './' . $this->config->item('captcha_fonts_path', 'tank_auth'),
			'font_size'		=> $this->config->item('captcha_font_size', 'tank_auth'),
			'img_width'		=> $this->config->item('captcha_width', 'tank_auth'),
			'img_height'	=> $this->config->item('captcha_height', 'tank_auth'),
			'show_grid'		=> $this->config->item('captcha_grid', 'tank_auth'),
			'expiration'	=> $this->config->item('captcha_expire', 'tank_auth'),
			'word_length' => 3,
		));

		// Save captcha params in session
		$this->session->set_flashdata(array(
			'captcha_word' => $cap['word'],
			'captcha_time' => $cap['time'],
		));

		return $cap['image'];
	}

	/**
	 * Callback function. Check if CAPTCHA test is passed.
	 *
	 * @param	string
	 * @return	bool
	 */
	function _check_captcha($code)
	{
		$time = $this->session->flashdata('captcha_time');
		$word = $this->session->flashdata('captcha_word');

		list($usec, $sec) = explode(" ", microtime());
		$now = ((float)$usec + (float)$sec);

		/*if ($now - $time > $this->config->item('captcha_expire', 'tank_auth')) {
			$this->form_validation->set_message('_check_captcha', $this->lang->line('auth_captcha_expired'));
			return FALSE;

		} else*/
		if (strtolower($code) != strtolower($word)) {
			$this->form_validation->set_message('_check_captcha', $this->lang->line('auth_incorrect_captcha'));
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Create reCAPTCHA JS and non-JS HTML to verify user as a human
	 *
	 * @return	string
	 */
	function _create_recaptcha()
	{
		$this->load->helper('recaptcha');

		// Add custom theme so we can get only image
		$options = "<script>var RecaptchaOptions = {theme: 'custom', custom_theme_widget: 'recaptcha_widget'};</script>\n";

		// Get reCAPTCHA JS and non-JS HTML
		$html = recaptcha_get_html($this->config->item('recaptcha_public_key', 'tank_auth'), NULL, $this->config->item('use_ssl', 'tank_auth'));

		return $options . $html;
	}

	/**
	 * Callback function. Check if reCAPTCHA test is passed.
	 *
	 * @return	bool
	 */
	function _check_recaptcha()
	{
		$this->load->helper('recaptcha');

		$resp = recaptcha_check_answer(
			$this->config->item('recaptcha_private_key', 'tank_auth'),
			$_SERVER['REMOTE_ADDR'],
			$_POST['recaptcha_challenge_field'],
			$_POST['recaptcha_response_field']
		);

		if (!$resp->is_valid) {
			$this->form_validation->set_message('_check_recaptcha', $this->lang->line('auth_incorrect_captcha'));
			return FALSE;
		}
		return TRUE;
	}

	function upload()
	{
		//set preferences
		/*$config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'txt|pdf|zip';
        $config['max_size']    = '100';

        //load upload class library
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('filename'))
        {
            // case - failure
            $upload_error = array('error' => $this->upload->display_errors());
            $this->load->view('upload_file_view', $upload_error);
        }
        else
        {
            // case - success
            $upload_data = $this->upload->data();
            $data['success_msg'] = '<div class="alert alert-success text-center">Your file <strong>' . $upload_data['file_name'] . '</strong> was successfully uploaded!</div>';
            $this->load->view('upload_file_view', $data);
        }
		*/


		$valid_formats_img = array("zip", "pdf", "rar");
		$name = $_FILES['filename']['name'];
		$temp = $name;
		$size = $_FILES['filename']['size'];

		$lastDot = strrpos($name, ".");
		$name = str_replace(".", "", substr($name, 0, $lastDot)) . substr($name, $lastDot);

		$data = array();

		if (strlen($name)) {
			list($txt, $ext) = explode(".", $name);
			if ($size < (50024 * 50024)) {
				//load upload class library
				$actual_image_name = time() . substr(str_replace(" ", "_", $txt), 5) . "." . $ext;
				$config['upload_path'] = './assets/uploads/';
				$config['allowed_types'] = '*';
				$config['max_size']	= '10024';
				$config['file_name'] = $actual_image_name;
				$config['remove_spaces'] = FALSE;

				$this->load->library('upload', $config);

				if ($this->upload->do_upload('filename')) {
					$data = array("status" => "success", "message" => $actual_image_name, "file" => $temp);
				} else {
					$te = "Please try again. " . $this->upload->display_errors();
					$data = array("status" => "error", "message" => $te);
				}
			} else {
				$data = array("status" => "error", "message" => "Sorry, maximum file size should be 10MB.");
			}
		} else {
			$data = array("status" => "error", "message" => "Error uploading document. Please try again.");
		}
		return $data;
	}
}

/* End of file auth.php */
/* Location: ./application/controllers/auth.php */
