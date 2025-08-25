<?php
if (! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . '/libraries/FileDownloader.php');

const NUMBER_OF_KTA_DIGIT = 6;
const MEMBER_PHOTO_DIR = FCPATH . 'assets/uploads/';
const MEMBER_PHOTO_DUMMY_DIR = FCPATH . 'assets-temp/uploads/';
const MEMBER_IDCARD_DIR = FCPATH . 'assets/uploads/';
const MEMBER_IDCARD_DUMMY_DIR = FCPATH . 'assets-temp/uploads/';
const MEMBER_IJAZAH_DIR = FCPATH . 'assets/uploads/';
const MEMBER_IJASAH_DUMMY_DIR = FCPATH . 'assets-temp/uploads/';

const CSV_DATE_FORMAT = 'd/m/Y';
//const CSV_DATE_FORMAT = 'Y-m-d';


/**
 * SETUP:
 * export CIDIR="/var/www/dev"
 * sudo mkdir -p $CIDIR/assets-temp/uploads $CIDIR/assets/uploads/userprovisioner
 * sudo chown -R www-data:www-data $CIDIR/assets-temp/uploads $CIDIR/assets/uploads/userprovisioner
 * sudo chmod -R 775 $CIDIR/assets-temp/uploads $CIDIR/assets/uploads/userprovisioner
 */
class Userprovisioner extends CI_Controller
{
  // List of directory to store upload file
  protected $target_dirs;
  protected $reasons;
  protected $BACKUP_DIR;

  public function __construct()
  {
    parent::__construct();
    $this->load->helper(array('form', 'url', 'utility'));
    $this->load->helper('file');

    // Allowed directories for upload
    $this->target_dirs = array(
      4 => 'assets/uploads/userprovisioner/',
      1 => 'assets/',
      2 => 'assets/uploads/',
      3 => 'assets/uploads/faip_manual/'
    );

    // The reason why user use this file upload, since this feature is meant to be an emergency tool,
    // not for day to day job
    $this->reasons = array(
      4 => 'Upload data user yang akan diprovisi',
      3 => 'Revisi/pembaruan penilaian FAIP',
      1 => 'Fitur tidak tersedia di Admin UI',
      2 => 'Fitur tidak tersedia di Member UI'
    );

    // Directory to store backup file (the old file that replaced by file upload)
    $this->BACKUP_DIR = '/var/www/assets-temp/';

    if (!$this->session->userdata('is_admin_login') && $this->session->userdata('admin_username') !== 'sp') {
      redirect(base_url() . 'admin');
      exit;
    }
  }

  public function index()
  {
    $this->load->view(
      'admin/userprovisioning_view',
      array(
        'error' => '',
        'target_dirs' => $this->target_dirs,
        'reasons' => $this->reasons
      )
    );
  }

  public function upload()
  {
    $config['upload_path']          = FCPATH . 'temp/uploads/';
    $config['allowed_types']        = 'csv';
    $config['max_size']             = 10000;

    $this->load->library('upload', $config);

    if (!$this->upload->do_upload('userfile')) {
      $error = array(
        'error' => $this->upload->display_errors() . ' ' . $config['upload_path'],
        'target_dirs' => $this->target_dirs,
        'reasons' => $this->reasons,
        'comment' => $this->input->post('comment')
      );

      $this->load->view('admin/userprovisioning_view', $error);
      return;
    } else {
      $data = array(
        'upload_data' => $this->upload->data(),
        'target_dirs' => $this->target_dirs,
        'reasons' => $this->reasons,
        'comment' => $this->input->post('comment')
      );

      $target_dir = FCPATH . $this->target_dirs[$this->input->post('target_dir')];
      if (!$this->folder_exist($target_dir)) {
        $error = array(
          'error' => 'Upload failed: Target directory does not exist! ' . $target_dir,
          'target_dirs' => $this->target_dirs,
          'reasons' => $this->reasons,
          'comment' => $this->input->post('comment')
        );
        $this->load->view('admin/userprovisioning_view', $error);
        return;
      }
      $file = $target_dir . $this->upload->data('client_name');

      if (file_exists($file)) {
        if ($this->input->post('status') == 1) {
          // User choose to not overwrite if the same file name exist
          $error = array(
            'error' => 'Upload failed: File with the same name is exist!',
            'target_dirs' => $this->target_dirs,
            'reasons' => $this->reasons,
            'comment' => $this->input->post('comment')
          );

          $this->load->view('admin/userprovisioning_view', $error);
          return;
        }

        // Move existing file to backup dir
        elseif ($this->input->post('status') == 2) {
          $date = new \DateTime();
          rename($file, $this->BACKUP_DIR . 'backup-' . $date->format('YmdHis') . '~' . $this->upload->data('client_name'));
        }
      }

      // Move upload file to expected dir
      if (rename($config['upload_path'] . $this->upload->data('file_name'), $file)) {
        // Final location of the file to be shown user
        $data['file_location'] = $file;
      }

      // Log the activity
      $log_data = array(
        'filename' => $this->upload->data('client_name'),
        'uploadedby' => ($this->session->user_id || 0),
        'target_dir' => $this->target_dirs[$this->input->post('target_dir')],
        'status' => $this->input->post('status'),
        'reason' => $this->input->post('reason'),
        'comment' => $this->input->post('comment')
      );

      $this->db->insert('log_upload_files', $log_data);
    }

    $this->load->view('admin/userprovisioning_view', $data);
  }

  function folder_exist($folder)
  {
    // Get canonicalized absolute pathname
    $path = realpath($folder);

    // If it exist, check if it's a directory
    if ($path !== false and is_dir($path)) {
      // Return canonicalized absolute pathname
      return $path;
    }

    // Path/folder does not exist
    return false;
  }

  function extract_name($fullname)
  {

    if (empty($fullname) || trim($fullname) === '') {
      throw new Exception('Fullname cannot be empty');
    }

    // replace multiple spaces become single space
    $fullname = preg_replace('!\s+!', ' ', $fullname);
    $fullname_array = explode(' ', $fullname);

    $firstname = '';
    $lastname = '';

    if (count($fullname_array) == 1) {
      $firstname = $fullname_array[0];
      $lastname = '';
    } else if (count($fullname_array) == 2 && strlen($fullname_array[0]) == 1) {
      $firstname = $fullname_array[0] . ' ' . $fullname_array[1];
      $lastname = '';
    } else if (count($fullname_array) >= 2) {
      if (strlen($fullname_array[0]) == 1) {
        $firstname = array_shift($fullname_array) . ' ' . array_shift($fullname_array);
        $lastname = implode(' ', $fullname_array);
      } else {
        $firstname = array_shift($fullname_array);
        $lastname = implode(' ', $fullname_array);
      }
    }

    return array($firstname, $lastname);
  }

  protected function gender($gender)
  {
    $retval = '';
    switch (strtoupper($gender)) {
      case 'M':
      case 'MALE':
      case 'L':
      case 'LAKI-LAKI':
      case 'LAKI':
        $retval = 'Male';
        break;
      case 'F':
      case 'FEMALE':
      case 'P':
      case 'PEREMPUAN':
      case 'WANITA':
        $retval = 'Female';
        break;
    }
    return $retval;
  }

  /**
   * @return An array of {user} from `v_account` if user found in the database or FALSE if user not found
   */
  protected function is_user_exist($fullname, $email, $idnty_number, $mobilephone = null, $birthdate = null, $min_match = 2)
  {
    $name = preg_replace('!\s+!', ' ', $fullname);
    $name = strtoupper($name);

    // Format date for MySQL
    $dob = $birthdate->format('Y-m-d');

    $fullname     = mysql_escape_char($fullname);
    $email        = mysql_escape_char($email);
    $idnty_number = mysql_escape_char($idnty_number);
    $mobilephone  = mysql_escape_char($mobilephone);

    $user = $this->db
      ->where(
        "(UPPER(CONCAT(firstname,' ',lastname)) = '${fullname}') + (LOWER(email) = LOWER('${email}')) + " .
          "(IFNULL(idcard, 'randomxyz') = '${idnty_number}') + (IFNULL(mobilephone,'randomxyz') = '${mobilephone}') + " .
          "(dob = '${dob}') >= ${min_match}"
      )
      ->get('v_account')
      ->result_array();


    //$sql = $this->db->get_compiled_select();
    //echo $sql;

    if ($user) {
      return $user[0];
    } else {
      return FALSE;
    }
  }

  /**
   * @return An array of {user_id} if user found in the database or FALSE if user not found
   */
  protected function is_user_exist_old1($fullname, $email, $idnty_number, $mobilephone = null, $birthdate = null)
  {
    $name = preg_replace('!\s+!', ' ', $fullname);
    $name = strtoupper($name);

    // TODO: FIX using matching mechanism: match at lest 2 out of 4
    // WHERE (FirstName = ?) + (LastName = ?) + (... = ?) > 2
    // this https://stackoverflow.com/questions/7109375/mysql-matching-2-out-of-5-fields
    $this->db->select('u.id as user_id')
      ->from('users u', false)
      ->join('user_profiles up', 'up.id = u.id')
      ->where('1 = 2'); // make it false if no other additional where check
    if (! empty($fullname)) $this->db->or_where("UPPER(CONCAT(up.firstname,' ',up.lastname)) =", $name);
    if (! empty($email)) $this->db->or_where('u.email', $email);
    if (! empty($idnty_number)) $this->db->or_where('up.idcard', $idnty_number);
    if (! empty($mobilephone)) $this->db->or_where('up.mobilephone', $mobilephone);

    //$sql = $this->db->get_compiled_select();
    //echo $sql;


    if (($user_id = $this->db->get()->result()) == TRUE) {
      return $user_id[0]->user_id;
    } else {
      return FALSE;
    }
  }

  /**
   * Fot testing only, checking whether a user is exist
   */
  function check_user()
  {
    $ret = $this->is_user_exist(
      $this->input->get('fullname'),
      $this->input->get('email'),
      $this->input->get('idnumber'),
      $this->input->get('birthdate'),
      $this->input->get('mobilephone')
    );

    if ($ret) {
      return $this->output
        ->set_content_type('application/json')
        ->set_status_header(200)
        ->set_output(
          json_encode([
            'status' => TRUE,
            'message' => "Similar user found. user: " . print_r($ret, true)
          ])
        );
    } else {
      return $this->output
        ->set_content_type('application/json')
        ->set_status_header(404)
        ->set_output(
          json_encode([
            'status' => FALSE,
            'message' => "Similar user not found"
          ])
        );
    }
  }

  protected function indentity_type($type)
  {
    $retval = '';
    switch (strtoupper($type)) {
      case 'KTP':
      case 'KARTU TANDA PENDUDUK':
      case 'CITIZEN':
        $retval = 'Citizen';
        break;
      case 'PASPOR':
      case 'PASSPORT':
      case 'P':
        $retval = 'Passport';
        break;
      case 'SIM':
      case 'SURAT IZIN MENGEMUDI':
      case 'SURAT IJIN MENGEMUDI':
        $retval = 'Passport';
        break;
    }
    return $retval;
  }

  /**
   * default format 'mm/dd/yy' e.g. '12/31/24'
   */
  //  protected function check_birthdate($date_string, $format = 'd/m/Y', $maxage = 90) {
  protected function check_birthdate($date_string, $format = 'Y-m-d', $maxage = 90)
  {

    /*
        if ( ($date = DateTime::createFromFormat($format, $date_string)) === FALSE ) {
            throw new Exception('Birth date error. Format is not match: '.$format.', date_string: '.$date_string);
        }

        $now = new DateTime();
        $interval = $now->diff($date);
        if ( $interval->y > $maxage) {
            throw new Exception('Birth date error. Age is more than '.$maxage);
        }
*/
    //	$now = new DateTime();
    //	$date = DateTime::createFromFormat($format, $date_string) ;
    //       $birthday  =  $date->format(CSV_DATE_FORMAT);
    $birthday  =  '0000-00-00';
    return $birthday;
  }


  protected function format_mobilephone($no, $countrycode = '62', $withplus = false)
  {

    //--------------------------------
    /*
        $no = preg_replace('/(?!^\+)[^\d]/x', "", $no); //remove non numeric except + in the begining
        switch (true) {
            case (preg_match('#^8\d{4,11}$#', $no)):
                $no = $countrycode . $no;
                break;
            case (preg_match('#^08\d{5,13}$#', $no)):
                $no = $countrycode . substr($no, 1);
                break;
            case (preg_match('#^'.$countrycode.'\d{5,13}$#', $no)):
                $no = $no;
                break;
            case (preg_match('#^\+'.$countrycode.'\d{5,13}$#', $no)):
                $no = substr($no, 1);
                break;
            default:
                throw new Exception('Invalid mobile phone number format');
                break;
        }

*/
    //-----------------------------------------------------------------------------

    if ($withplus) $no = '+' . $no;
    return $no;
  }

  /**
   * @return user_id/person_id who own the KTA number
   */
  protected function is_kta_exist($kta)
  {
    if (empty($kta)) {
      return FALSE;
    }

    $result = $this->db->select('person_id')->from('members')->where('no_kta', $kta);
    if (($user_id = $this->db->get()->result()) == TRUE) {
      return $user_id[0]->person_id;
    } else {
      return FALSE;
    }
  }

  /**
   * Format KTA. Remove non numeric, ussually like: dot, space, dash
   * Get only 6 digit (NUMBER_OF_KTA_DIGIT) from the input
   */
  protected function format_kta($kta)
  {
    $no = preg_replace('/(?!^)[^\d]/x', "", $kta); //remove non numeric
    $no = substr($no, -NUMBER_OF_KTA_DIGIT);
    return $no;
  }

  /**
   * Fot testing only, checking a user is exist
   */
  function check_kta()
  {
    $kta = $this->format_kta($this->input->get('kta'));

    if ($this->is_kta_exist($kta)) {
      return $this->output
        ->set_content_type('application/json')
        ->set_status_header(200)
        ->set_output(
          json_encode([
            'status' => TRUE,
            'message' => "Same KTA is exist in the database. KTA: " . print_r($kta, true)
          ])
        );
    } else {
      return $this->output
        ->set_content_type('application/json')
        ->set_status_header(404)
        ->set_output(
          json_encode([
            'status' => FALSE,
            'message' => "Same KTA is not found. KTA has been search: " . print_r($kta, true)
          ])
        );
    }
  }

  /**
   * Update user's photo by copying from external URL
   * @param prefix Support to store photos in the temporary folder for testing, and update dummy `user_profile` table
   */
  function update_photo($user_id, $url, $user_modifier = 0, $prefix = '')
  {
    $retval = FALSE;
    if (empty($user_id) || empty($url)) {
      throw new InvalidArgumentException('Cannot update photo. user_id and url cannot be empty');
    }

    $url_path = parse_url($url)['path'];
    $ext      = pathinfo($url_path, PATHINFO_EXTENSION);
    $filename = time() . "_PHOTO_" . $user_id . "." . $ext;
    $fileloc  = MEMBER_PHOTO_DIR . $filename;
    if (! empty($prefix)) {
      $fileloc  = MEMBER_PHOTO_DUMMY_DIR . $filename;
    }

    // Feature flag - the old method of download copy() does not work when download a file from google drive
    $NEW_DOWNLOAD_METHOD = true;
    if ($NEW_DOWNLOAD_METHOD) {
      try {
        //Download file using Curl library
        $dl = new Filedownloader($url, "cookies.txt");
        $content = $dl->download();

        if (empty($content)) {
          throw new Exception('Cannot copy idcard/image from url: ' . $url . ' as file: ' . $filename . '. Reponse 0 content from the url');
        }

        // Fix extension file name if it empty, use extension file name from the source
        $saveName = 'nofilename.ext';
        if ($header = $dl->getHeader('Content-disposition')) {
          if (preg_match('/filename="?(.*)"?/', $header, $matches)) {
            $saveName = str_replace('"', '', $matches[1]);
          }
        }
        if (empty($ext)) {
          $ext      = pathinfo($saveName, PATHINFO_EXTENSION);
          $fileloc  = $fileloc . $ext;
        }
        file_put_contents($fileloc, $content);

        $new_ext = is_file_image($fileloc);
        if ($ext == 'ext' && $new_ext !== FALSE) {
          rename($fileloc, pathinfo($fileloc, PATHINFO_FILENAME) . $new_ext);
        }

        if (file_exists($fileloc)) {
          $this->db
            ->set('photo', basename($fileloc))
            ->set('modifiedby', $user_modifier)
            ->set('modifieddate', date('Y-m-d H:i:s')) // Should be autoupdated by MySQL)
            ->where('user_id', $user_id)
            ->update($prefix . 'user_profiles');

          $retval = basename($fileloc);
        }
      } catch (Throwable $t) {
        throw new Exception('Cannot copy photo/image from url: ' . $url . ' as file: ' . $filename . '. Message: ' . $t->getMessage());
      }

      // Old method - does works for UGM case (file not in Google Drive)
    } else {

      if (! @copy($url, $fileloc)) {
        $errors = error_get_last();
        throw new Exception('Cannot copy photo/image from url: ' . $url . ' as file: ' . $filename . '. Message: ' . $errors['message']);
      } else {
        $this->db
          ->set('photo', $filename)
          ->set('modifiedby', $user_modifier)
          ->set('modifieddate', date('Y-m-d H:i:s')) // Should be autoupdated by MySQL)
          ->where('user_id', $user_id)
          ->update($prefix . 'user_profiles');

        $retval = $filename;
      }
    }

    log_message('debug', '[SIMPONI] ' . __FUNCTION__ . ' - user_id: ' . $user_id . ', filename: ' . $retval);
    return $retval;
  }

  function update_idcard($user_id, $url, $user_modifier = 0, $prefix = '')
  {
    $retval = FALSE;
    if (empty($user_id) || empty($url)) {
      throw new InvalidArgumentException('Cannot update idcard. user_id and url cannot be empty');
    }

    $url_path = parse_url($url)['path'];
    $ext      = pathinfo($url_path, PATHINFO_EXTENSION);
    $filename = time() . "_KTP_" . $user_id . "." . $ext;
    $fileloc  = MEMBER_IDCARD_DIR . $filename;
    if (! empty($prefix)) {
      $fileloc  = MEMBER_IDCARD_DUMMY_DIR . $filename;
    }


    // Feature flag - the old method of download copy() does not work when download a file from google drive
    $NEW_DOWNLOAD_METHOD = true;
    if ($NEW_DOWNLOAD_METHOD) {
      try {
        //Download file using Curl library
        $dl = new Filedownloader($url, "cookies.txt");
        $content = $dl->download();

        if (empty($content)) {
          throw new Exception('Cannot copy idcard/image from url: ' . $url . ' as file: ' . $filename . '. Reponse 0 content from the url');
        }

        // Fix extension file name if it empty, use extension file name from the source
        $saveName = 'nofilename.ext';
        if ($header = $dl->getHeader('Content-disposition')) {
          if (preg_match('/filename="?(.*)"?/', $header, $matches)) {
            $saveName = str_replace('"', '', $matches[1]);
          }
        }
        if (empty($ext)) {
          $ext      = pathinfo($saveName, PATHINFO_EXTENSION);
          $fileloc  = $fileloc . $ext;
        }
        file_put_contents($fileloc, $content);

        $new_ext = @is_file_image($fileloc);
        if ($ext == 'ext' && $new_ext !== FALSE) {
          rename($fileloc, pathinfo($fileloc, PATHINFO_FILENAME) . $new_ext);
        }

        if (file_exists($fileloc)) {
          $this->db
            ->set('id_file', basename($fileloc))
            ->set('modifiedby', $user_modifier)
            ->set('modifieddate', date('Y-m-d H:i:s')) // Should be autoupdated by MySQL)
            ->where('user_id', $user_id)
            ->update($prefix . 'user_profiles');

          $retval = basename($fileloc);
        }
      } catch (Throwable $t) {
        throw new Exception('Cannot copy idcard/image from url: ' . $url . ' as file: ' . $filename . '. Message: ' . $t->getMessage());
      }

      // Old method - does works for UGM case (file not in Google Drive)
    } else {

      if (! @copy($url, $fileloc)) {
        $errors = error_get_last();
        throw new Exception('Cannot copy idcard/image from url: ' . $url . ' as file: ' . $filename . '. Message: ' . $errors['message']);
      } else {
        $this->db
          ->set('id_file', $filename)
          ->set('modifiedby', $user_modifier)
          ->set('modifieddate', date('Y-m-d H:i:s')) // Should be autoupdated by MySQL)
          ->where('user_id', $user_id)
          ->update($prefix . 'user_profiles');

        $retval = $filename;
      }
    }
    log_message('debug', '[SIMPONI] ' . __FUNCTION__ . ' - user_id: ' . $user_id . ', filename: ' . $retval);
    return $retval;
  }

  function update_ijazah($user_id, $user_edu_id, $url, $user_modifier = 0, $prefix = '')
  {
    $retval = FALSE;
    if (empty($user_id) || empty($url) || empty($user_edu_id)) {
      throw new InvalidArgumentException('Cannot update ijasah. user_id and url cannot be empty');
    }

    $url_path = parse_url($url)['path'];
    $ext      = pathinfo($url_path, PATHINFO_EXTENSION);
    $filename = time() . "_EDU_" . $user_id . "." . $ext;
    $fileloc  = MEMBER_IJAZAH_DIR . $filename;
    if (! empty($prefix)) {
      $fileloc  = MEMBER_IDCARD_DUMMY_DIR . $filename;
    }



    // Feature flag - the old method of download copy() does not work when download a file from google drive
    $NEW_DOWNLOAD_METHOD = true;
    if ($NEW_DOWNLOAD_METHOD) {
      try {
        //Download file using Curl library
        $dl = new Filedownloader($url, "cookies.txt");
        $content = $dl->download();

        if (empty($content)) {
          throw new Exception('Cannot copy idcard/image from url: ' . $url . ' as file: ' . $filename . '. Reponse 0 content from the url');
        }

        // Fix extension file name if it empty, use extension file name from the source
        $saveName = 'nofilename.ext';
        if ($header = $dl->getHeader('Content-disposition')) {
          if (preg_match('/filename="?(.*)"?/', $header, $matches)) {
            $saveName = str_replace('"', '', $matches[1]);
          }
        }
        if (empty($ext)) {
          $ext      = pathinfo($saveName, PATHINFO_EXTENSION);
          $fileloc  = $fileloc . $ext;
        }
        file_put_contents($fileloc, $content);

        $new_ext = @is_file_image($fileloc);
        if ($ext == 'ext' && $new_ext !== FALSE) {
          rename($fileloc, pathinfo($fileloc, PATHINFO_FILENAME) . $new_ext);
        }

        if (file_exists($fileloc)) {
          $this->db
            ->set('attachment', basename($fileloc))
            ->set('modifiedby', $user_modifier)
            ->set('modifieddate', date('Y-m-d H:i:s')) // Should be autoupdated by MySQL)
            ->where('user_id', $user_id)
            ->where('id', $user_edu_id)
            ->update($prefix . 'user_edu');

          $retval = basename($fileloc);
        }
      } catch (Throwable $t) {
        throw new Exception('Cannot copy ijasah/image from url: ' . $url . ' as file: ' . $filename . '. Message: ' . $t->getMessage());
      }

      // Old method - does works for UGM case (file not in Google Drive)
    } else {

      if (! @copy($url, $fileloc)) {
        $errors = error_get_last();
        throw new Exception('Cannot copy ijasah/image from url: ' . $url . ' as file: ' . $filename . '. Message: ' . $errors['message']);
      } else {
        $this->db
          ->set('attachment', $filename)
          ->set('modifiedby', $user_modifier)
          ->set('modifieddate', date('Y-m-d H:i:s')) // Should be autoupdated by MySQL)
          ->where('user_id', $user_id)
          ->where('id', $user_edu_id)
          ->update($prefix . 'user_edu');

        $retval = $filename;
      }
    }

    log_message('debug', '[SIMPONI] ' . __FUNCTION__ . ' - user_id: ' . $user_id . ', filename: ' . $retval);
    return $retval;
  }


  protected function generate_password()
  {
    $new_pwd = generate_random_password();
    //$encypt_pwd =
    return $new_pwd;
  }

  /**
   * Trim right and left, make first letter uppercase and remove unwanted chars e.g. numeric
   */
  protected function fortmat_name($name)
  {
    return ucwords(preg_replace('/\s*(?:[\d_]|[^\w\s])+/', '', strtolower(rtrim(ltrim($name)))));
  }

  /**
   * Main function to start processing CSV file
   * For test:
   * Folder: cd /var/www/dev/assets/uploads/userprovisioner/
   * https://simponi-dev.pii.or.id/index.php/admin/userprovisioner/process_csv/pengajuan_kta_feb2024_csvforexcel_enter_char_removed_test1row.csv
   * https://simponi-dev.pii.or.id/index.php/admin/userprovisioner/process_csv/pengajuan_kta_feb2024_batch13_ugm.csv
   * Pendaftaran_Grup_Kolektif_Keanggotaan_PII_Angkatan_1.csv
   */
  function process_csv($filename)
  {

    //error_reporting(0);

    if (empty($filename)) {
      $filename = $this->input->get('file');
    }

    $KOLEKTIF_IDS = array(
      //'744', //PSPPI Kehutanan UGM
      //'682' //UGM
      690,
      745,
      682,
      500,
      749
    );

    // Admin id for createdby & modifiedby value
    $CREATOR_MODIFICATOR = $this->session->userdata('admin_id');

    // Value for kolektif_name_id in profile table
    //    $KOLEKTIF_BATCH_INSERT_ID = '507'; // UGM - Peternakan
    $KOLEKTIF_BATCH_INSERT_ID = '512'; // UGM FT Angkatan 15
    //$KOLEKTIF_BATCH_INSERT_ID = '501'; // Kehutanan
    //  $KOLEKTIF_BATCH_INSERT_ID = '505'; // TEST PSPPI ITS Surabaya

    // Not used at the moment
    // Kolektif name should be inserted manual before running this
    //    $KOLEKTIF_BATCH_INSERT_NAME = 'DATA TEST UGM14 SEPT2024';
    $KOLEKTIF_BATCH_INSERT_NAME = 'UGM ANGKATAN 15A JUNI 2025';
    //    $KOLEKTIF_BATCH_INSERT_NAME = 'UGM PSPPI KEHUTANAN JAN 2025';

    $SCHOOL_NAME = 'UNIVERSITAS GAJAH MADA';
    //  $SCHOOL_NAME = 'UNIVERSITAS PENDIDIKAN INDONEIA';
    $SCHOOL_DEGREE = 'S1';

    // Number of fields to be check that user is already exist
    // Field are from fullname, no KTP,
    //   $MIN_FIEDLS_MATCH_EXISTINGUSER = 2;

    // Password is disabled - User should reset her/his password from SIMPONI web login page --- Password ---
    $__USE_DEFAULT_PASSWORD__ = TRUE;
    $DEFAULT_PASSWORD = '$2y$10$v6zVno3AVAdMJ3Bg1r.Mc.9zDyfkDnqAGRxXXBNZzmMKXGgAiw2YS'; //Simponi@1000

    // Country for company address (alamat perusahaan)
    $DEFAULT_COUNTRY_NAME = 'Indonesia';
    //	 $DEFAULT_COUNTRY_NAME = 'Timor Leste';

    // Set to FALSE since $this->db->insert_id() is always return 1 when using transaction.
    // Is it a bug in CI?
    $__USE_TRANSACTION__ = FALSE;

    $CSV_SEPARATOR        = ';';
    $CSV_ENCLOSURE        = '';
    $CSV_MAX_CHARS        = null; // Unlimited number of chars in a line
    $CSV_READ_MAX_LINES   = 220;
    $CSV_START_DATA_ROW   = 0;
    $CSV_MIN_COLUMN_COUNT = 18;

    $CSV_DATE_FORMAT = CSV_DATE_FORMAT; //UGM: 'yyyy-mm-dd' means YYYY-MM-DD
    $CSV_DIR = FCPATH . $this->target_dirs['4'];

    // FOR SIMULATION set it to TRUE
    // When it set to true, it will not insert data into the correct table
    // Instead it will create a dummy tables and insert into it
    $__USE_DUMMY_TABLES__ = TRUE; // diganti TRUE untuk ke table Dummy

    // Dummy table prefix
    $TABLE_PREFIX_FOR_DUMMY = 'dummy_';
    $prefix = '';
    if ($__USE_DUMMY_TABLES__) {
      $prefix = $TABLE_PREFIX_FOR_DUMMY;
    }

    // List of tables used
    $TABLE_USERS        = $prefix . 'users';
    $TABLE_USER_PROFILE = $prefix . 'user_profiles';
    $TABLE_USER_ADDRESS = $prefix . 'user_address';
    $TABLE_USER_EXP     = $prefix . 'user_exp';
    $TABLE_USER_EDU     = $prefix . 'user_edu';
    $TABLE_MEMBERS      = $prefix . 'members';
    $TABLE_USER_TRANSFER  = $prefix . 'user_transfer';
    $table_list = array($TABLE_USERS, $TABLE_USER_PROFILE, $TABLE_USER_ADDRESS, $TABLE_USER_EXP, $TABLE_USER_EDU, $TABLE_MEMBERS, $TABLE_USER_TRANSFER);

    $fhandle = fopen($CSV_DIR . $filename, "r");
    $rownum = 0;
    $rowrum_processed = 0;
    $result_list = array();
    $success_count = 0;
    $rownum_messages = array();

    // Need to be initiated here because needed in the catch Exception onn every line
    $user_id             = '';
    $ext_user_id         = '';
    $email               = '';
    $no_kta              = '';
    $status_db_education = 0;
    $status_db_transfer  = 0;
    $status_db_users     = 0;
    $status_db_profile   = 0;
    $status_db_kolektif  = 0;
    $status_db_address   = 0;
    $status_db_members   = 0;
    $status_db_job       = 0;
    $status_db_commit    = 0;
    $status_upload_photo = 0;
    $stasus_message      = '-';

    // Check if the csv file exist, if no the return an error (404)
    if (! file_exists($CSV_DIR . $filename)) {
      return $this->output
        ->set_content_type('application/json')
        ->set_status_header(404)
        ->set_output(
          json_encode([
            'status' => FALSE,
            'message' => 'File does not exist ' . $filename,
            'result' =>  $result_list
          ])
        );
    }
    log_message('debug', '[SIMPONI] ' . __FUNCTION__ . ' - Processing file: ' . $CSV_DIR . $filename);

    // Log every batch process into a single file.
    $log_file = $CSV_DIR . $prefix . 'log_' . date('Ymd-His') . '.log';
    // Dump variables for batch info into log file
    $defined_vars = print_r(get_defined_vars(), true);
    if (!write_file($log_file, $defined_vars . "\n", 'a+')) {
      throw new Exception('Unable to write the batch userprovisioning log to a file.');
    }

    // Log every batch process into a CSV formatted file.
    $csvlog_file = $CSV_DIR . $prefix . 'log_' . date('Ymd-His') . '.csv';
    $csvlog_header = 'rownum, ext_user_id, email, user_id, no_kta, status_db_users, status_db_profile,' .
      ' status_db_kolektif, status_db_address, status_db_job, status_db_edu, status_db_commit, ' .
      ' status_upload_photo, status_upload_idcard, status_upload_ijazah, message';
    if (!write_file($csvlog_file, $csvlog_header . "\n", 'a+')) {
      throw new Exception('Unable to write the batch userprovisioning log to a CSV file.');
    }

    // Create dummy tables for insert with the same structure from original tables
    if ($__USE_DUMMY_TABLES__) {
      log_message('debug', '[SIMPONI] ' . __FUNCTION__ . ' - Creating dummy/test tables.');

      foreach ($table_list as $tablename) {
        $tablename_orig = preg_replace('/^' . $TABLE_PREFIX_FOR_DUMMY . '/', '', $tablename);
        $this->db->query('DROP TABLE IF EXISTS `' . $tablename . '`');
        $this->db->query('CREATE TABLE `' . $tablename . '` LIKE `' . $tablename_orig . '`;');
      }

      // Clean up TEMPORARY photos directory
      log_message('debug', '[SIMPONI] ' . __FUNCTION__ . ' - Clean up dummy/test folder.');
      @array_map('unlink', array_filter((array) glob(MEMBER_PHOTO_DUMMY_DIR . "*")));
    }

    if ($fhandle !== FALSE) {

      //    while ( ($getData = fgetcsv($fhandle, $CSV_MAX_CHARS, $CSV_SEPARATOR, $CSV_ENCLOSURE)) !== FALSE )
      while (($getData = fgetcsv($fhandle, $CSV_MAX_CHARS, $CSV_SEPARATOR)) !== FALSE) {

        $rownum++;

        // Reset all variables for logging
        $user_id             = '';
        $ext_user_id         = '';
        $email               = '';
        $no_kta              = '';
        $status_db_education = 0;
        $status_db_transfer  = 0;
        $status_db_mmembers  = 0;
        $status_db_users     = 0;
        $status_db_profile   = 0;
        $status_db_kolektif  = 0;
        $status_db_address   = 0;
        $status_db_job       = 0;
        $status_db_commit    = 0;
        $status_upload_photo = 0;
        $stasus_message      = '-';

        // Ignore header if any
        if ($rownum <= $CSV_START_DATA_ROW) {
          continue;
        }

        //Ignore blank lines
        if ($getData === array(null) || trim(implode($getData)) === '') {
          continue;
        }

        // Prevent to much line in files. Make a short time to process and no timeout
        if ($rownum === $CSV_READ_MAX_LINES) {
          $rownum_messages[$rownum][] = 'ERROR - Stop processing the next line if any! Limit of lines has reached.';

          $result_list[] = array(
            "row_num" => $rownum,
            "message" => 'ERROR - Max number of lines that can be processed is exceeded: ' . $CSV_READ_MAX_LINES,
            "details" => $rownum_messages[$rownum]
          );
          break;
        }

        if (($col_num = count($getData)) < ($CSV_MIN_COLUMN_COUNT + 1)) {
          $rownum_messages[$rownum][] = 'ERROR - Stop processing the next line if any! Number of columns (array size) is ' . $col_num;

          $result_list[] = array(
            "row_num" => $rownum,
            "message" => 'ERROR - Failed to read all columns in a line, min_column: ' . $CSV_MIN_COLUMN_COUNT,
            "details" => $rownum_messages[$rownum]
          );
          break;
        }

        log_message('debug', '[SIMPONI] ' . __FUNCTION__ . ' - Processing row_num: ' . $rownum . ' ' . print_r($getData, true));

        try {
          $rowrum_processed++;

          $status_db_users      = 0;
          $status_db_profile    = 0;
          $status_db_kolektif   = 0;
          $status_db_address    = 0;
          $status_db_transfer   = 0;
          $status_db_mmembers   = 0;
          $status_db_job        = 0;
          $status_db_education  = 0;
          $status_db_commit     = 0;
          $status_upload_photo  = 0;
          $status_upload_idcard = 0;
          $status_upload_ijazah = 0;
          $stasus_message       = '-';

          $username = $getData[8]; //''; // Default value for non member is empty
          $password = ($__USE_DEFAULT_PASSWORD__) ? $DEFAULT_PASSWORD : $this->generate_password();
          $email = $getData[14];

          $ext_user_id = $getData[0];

          $kode_wil = $getData[6];
          $kowil = substr($kode_wil, 0, 2);
          $kode_bk  = $getData[7];
          $kode_kta = $getData[8];
          $years = "25";
          $from_date = $getData[43];
          $thru_date = $getData[44];
          $jenis_ang = "1";
          $status    =  1;

          $fullname       = $getData[5];
          $firstname      = $this->extract_name($fullname)[0];
          $lastname       = $this->extract_name($fullname)[1];
          $gender         = $this->gender($getData[15]);
          $idnty_type     = $this->indentity_type($getData[22]);
          $idnty_number   = $getData[23];
          $birthplace     = $getData[16];
          $birthdate      = $getData[17]; // $this->check_birthdate($getData[9], $CSV_DATE_FORMAT); Perubahan by Ipur
          $birthdate_date = DateTime::createFromFormat($CSV_DATE_FORMAT, $birthdate);
          $mobilephone    = $this->format_mobilephone($getData[24]);
          $va             = '89699' . $getData[6] . $getData[7] . $getData[8]; // Will be generated later manually by admin using set_active()
          $kolektif_batch = $KOLEKTIF_BATCH_INSERT_ID;
          $kolektif_ids   = implode(',', $KOLEKTIF_IDS);
          $createdby      = $CREATOR_MODIFICATOR;
          $modifiedby     = $CREATOR_MODIFICATOR;

          $addresstype = 1; // Home address
          $address     = $getData[25];
          $city        = $getData[26];
          $province    = ''; //$getData[2];
          $zipcode     = $getData[27];
          $homephone   = $getData[28];

          $lembaga_nama    = $getData[31];
          $lembaga_jabatan = $getData[32];
          $present_job     = 1;
          $lembaga_alamat  = $getData[34];
          $lembaga_kota    = $getData[35]; //NOT USED - Simponi doesn't support
          $lembaga_prov    = '';
          $lembaga_negara  = $DEFAULT_COUNTRY_NAME;
          $lembaga_kodepos = $getData[36]; //NOT USED - Simponi doesn't support
          $lembaga_phone   = $getData[37]; //NOT USED - Simponi doesn't support

          $photo_link = $getData[40];
          $idcard_link = $getData[41];
          $ijazah_link = $getData[42];

          $__INSERT_EXPERIENCE__ = true;
          $__INSERT_PHOTO__ = true;
          $__INSERT_IDCARD__ = true;
          $__INSERT_IJAZAH__ = true;

          // ---------------------------------------
          /*
                    if (empty($birthdate_date)) {
                        throw new Exception("Failed parsing user's birthdate. CSV_DATE_FORMAT: " . CSV_DATE_FORMAT .", birthdate: " .$birthdate, 1);

                    }


                    if ($idnty_type == 'Citizen' && validate_ktp($idnty_number, $birthdate_date->format('Y-m-d')) == FALSE ) {
                        throw new Exception("Nomor KTP is not valid. Number ($idnty_number) is not match with user's birth date ($birthdate)");
                    }

*/
          //---------------------------------------------

          //                    $existing_user = $this->is_user_exist($fullname, $email, $idnty_number, $mobilephone, $birthdate_date, $MIN_FIEDLS_MATCH_EXISTINGUSER);
          $existing_user_id = ($existing_user) ? @$existing_user['user_id'] : FALSE;

          $no_kta = $getData[8];
          $no_kta = $this->format_kta($no_kta);

          // KTA is exist in the database
          if (! empty($no_kta) && ($user_id_with_kta = $this->is_kta_exist($no_kta)) !== FALSE) {

            // id with similar data and the id who own the KTA is match
            if ($existing_user_id === $user_id_with_kta) {

              if (!empty($KOLEKTIF_IDS) && sizeof($KOLEKTIF_IDS) > 0) {
                // Update kolektif_ids
                foreach ($KOLEKTIF_IDS as $kolektif_id) {
                  $str_sql = "update user_profiles set kolektif_ids = case "
                    . "when (id = ${existing_user_id} AND (kolektif_ids is null OR kolektif_ids = '')) THEN '${kolektif_id}' "
                    . "when (id = ${existing_user_id} AND (find_in_set(${kolektif_id},kolektif_ids) > 0)) THEN  kolektif_ids "
                    . "when (id = ${existing_user_id} AND (find_in_set(${kolektif_id},kolektif_ids) = 0)) then CONCAT(kolektif_ids,',','${kolektif_id}') "
                    . "ELSE kolektif_ids "
                    . "END";

                  // No need code below. String substitution work with ${var} Cool!
                  //$str_sql = strtr($str_sql, array('${existing_user_id}' => $existing_user_id, '${kolektif_id}' => $kolektif_id));

                  $this->db->query($str_sql);
                }

                $status_db_profile = 1;
                $rownum_messages[$rownum][] = 'SUCCESS - Update user to have kolektif_ids: ' . implode(',', $KOLEKTIF_IDS) . ', id: ' . $existing_user_id;
              } else {
                $rownum_messages[$rownum][] = 'Skip setting kolektif_ids, since KOLEKTIF_IDS variable was not set';
              }
            } else {
              $rownum_messages[$rownum][] = 'WARNING - Person who has same KTA seems like different person. Please check manually.';
            }
            throw new Exception("KTA number is already exist. KTA: ${no_kta}, user_id: ${user_id_with_kta}");
          }
          /*
                    if ( $existing_user_id !== FALSE ) {
                        throw new Exception("Similar user already exist. existing_user_id: ${existing_user_id}, fullname: '${fullname}', email: ${email}, ktp: ${idnty_number}, mobile: ${mobilephone}");
                    }
*/
          // Start database insert
          if ($__USE_TRANSACTION__) {
            $this->db->trans_start();
          }

          $user_data = array(
            'username' => $no_kta, /// $username,
            'password' => $password,
            'email' => $email,
          );

          if ($this->db->insert($TABLE_USERS, $user_data)) {
            $user_id = $this->db->insert_id();
            $status_db_users = $user_id;
            $rownum_messages[$rownum][] = 'SUCCESS - Insert into table: ' . $TABLE_USERS . ', id: ' . $user_id;
          }

          $uprofile_data = array(
            'user_id' => $user_id,
            'va' => $va,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'gender' => $gender,
            'idtype' => $idnty_type,
            'idcard' => $idnty_number,
            'birthplace' => $birthplace,
            'dob' => $birthdate,
            'mobilephone' => $mobilephone,
            'kolektif_ids' => $kolektif_ids,
            'kolektif_name_id' => $kolektif_batch,
            'createdby' => $createdby,
            'modifiedby' => $modifiedby,
          );

          if ($this->db->insert($TABLE_USER_PROFILE, $uprofile_data)) {
            $uprofile_id = $this->db->insert_id();
            $status_db_profile   = $uprofile_id;
            $rownum_messages[$rownum][] = 'SUCCESS - Insert into table: ' . $TABLE_USER_PROFILE . ', id: ' . $uprofile_id;
          }


          $addr_data = array(
            'user_id' => $user_id,
            'addresstype' => $addresstype,
            'address' => $address,
            'city' => $city,
            'province' => $province,
            'zipcode' => $zipcode,
            'phone' => $homephone,
            'createdby' => $createdby,
            'modifiedby' => $modifiedby
          );

          if ($this->db->insert($TABLE_USER_ADDRESS, $addr_data)) {
            $addr_id = $this->db->insert_id();
            $status_db_address = $addr_id;
            $rownum_messages[$rownum][] = 'SUCCESS - Insert into table: ' . $TABLE_USER_ADDRESS . ', id: ' . $addr_id;
          }

          //----------------------------------------------------------------------------------- Penambahan insert ke table members by Ipur Tgl 12-06-2025 ----
          $members_data = array(
            'person_id'     => $user_id,
            'code_wilayah'  => $kode_wil,
            'code_mitra'    => 1,
            'code_bk_hkk'   => $kode_bk,
            'years'         => "25",       // Harus diganti sesuai tahun saat upload datanya.
            'no_kta'        => $no_kta,
            'from_date'     => $from_date,
            'thru_date'     => $thru_date,
            'jenis_anggota' => $jenis_ang,
            'status'        => 1,
            'created_at'    => date('Y-m-d H:i:s'),
            'created_by'    => $createdby,
            'updated_by'    => $modifiedby,
            'wil_id'        => $kowil,
          );

          if ($this->db->insert($TABLE_MEMBERS, $members_data)) {
            $members_id = $this->db->insert_id();
            $status_db_address = $members_id;
            $rownum_messages[$rownum][] = 'SUCCESS - Insert into table: ' . $TABLE_MEMBERS . ', id: ' . $members_id;
          }

          //-----------------------------------------------------------------------------------------------

          //----------------------------------------------------------------------------------- Penambahan insert ke table user_transfer by Ipur Tgl 12-06-2025 ----
          $user_transfer_data = array(
            'user_id'       => $user_id,
            'pay_type'      => 1,
            'order_id'      => 0,
            'rel_id'        => 0,
            'bukti'         => "INV046-PSPPI-UGM Angkatan 15 Bach 1",       // yang Bacth 2 INV051
            'atasnama'      => $fullname,
            'tgl'           => "2025-06-02",
            'status'        => 1,
            'description'   => 'Pembayaran kolektif UGM-15',
            'iuranpangkal'  => 100000,
            'iurantahunan'  => 225000,
            'sukarelatotal' => 325000,
            'vnv_status'    => 1,
            'remark'        => '[SIMPONI] Bulk insert 2025-06-12',
            'createddate'   => date('Y-m-d H:i:s'),
            'createdby'     => $createdby,
            'modifieddate'  => date('Y-m-d H:i:s'),
            'modifiedby'    => $createdby,

          );

          if ($this->db->insert($TABLE_USER_TRANSFER, $user_transfer_data)) {
            $user_transfer_id = $this->db->insert_id();
            $status_db_transfer = $user_transfer_id;
            $rownum_messages[$rownum][] = 'SUCCESS - Insert into table: ' . $TABLE_USER_TRANSFER . ', id: ' . $user_transfer_id;
          }

          //-----------------------------------------------------------------------------------------------

          $exprience_id = null;
          if ($__INSERT_EXPERIENCE__) {
            $experince_data = array(
              'user_id' => $user_id,
              'company' => $lembaga_nama,
              'title' => $lembaga_jabatan,
              'location' => $lembaga_alamat,
              'provinsi' => $lembaga_prov,
              'negara' => $lembaga_negara,
              'is_present' => $present_job,
              'createdby' => $createdby,
            );

            if ($this->db->insert($TABLE_USER_EXP, $experince_data)) {
              $exprience_id = $this->db->insert_id();
              $status_db_job = $exprience_id;
              $rownum_messages[$rownum][] = 'SUCCESS - Insert into table: ' . $TABLE_USER_EXP . ', id: ' . $exprience_id;
            }
          }

          $user_edu_id = null;
          if ($__INSERT_IJAZAH__) {
            $education_data = array(
              'user_id' => $existing_user_id,
              'type' => '1',
              'school' => $SCHOOL_NAME,
              'degree' => $SCHOOL_DEGREE,
              // 'fieldofstudy' => '',
              // 'mayor' => '',
              // 'statdate' => '',
              // 'enddate' => '',
              // 'title_prefix' => '',
              // 'title' => '',
              // 'score' => '',
              'description' => '[Data is automatically inserted]'
            );

            if ($this->db->insert($TABLE_USER_EDU, $education_data)) {
              $user_edu_id = $this->db->insert_id();
              $status_db_education = 1;
              $rownum_messages[$rownum][] = 'SUCCESS - Insert into table: ' . $TABLE_USER_EDU . ', id: ' . $user_edu_id;
            }
          }

          // Commit the transaction
          if ($__USE_TRANSACTION__) {
            $this->db->trans_complete();
          }

          if ($__USE_TRANSACTION__ && $this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $error = $this->db->error();
            $status_db_commit = 0;


            $csvlog_data = str_putcsv(array(
              $rownum,
              $ext_user_id,
              $email,
              $user_id,
              $no_kta,
              $status_db_users,
              $status_db_profile,
              $status_db_kolektif,
              $status_db_address,
              $status_db_job,
              $status_db_education,
              $status_db_members,
              $status_db_transfer,
              $status_db_commit,
              $status_upload_photo,
              $status_upload_idcard,
              $status_upload_ijazah,
              'DB_ROLLBACK'
            ));

            if (!write_file($csvlog_file, $csvlog_data . "\n", 'a+')) {
              throw new Exception('Unable to write the batch userprovisioning log to a CSV file.');
            }

            throw new Exception('[SIMPONI] ' . __FUNCTION__ . ' Failed to commit transaction while provision user: ' . $email . ', row_number: ' . $rownum . ' ' . $error);
          }

          // SUCCESSFULLY Updated all database trx
          else {
            $success_count++;

            if ($__INSERT_PHOTO__) {

              $tbl_prefix = ($__USE_DUMMY_TABLES__) ? $TABLE_PREFIX_FOR_DUMMY : '';
              if (($photofile = $this->update_photo($user_id, $photo_link, $modifiedby, $tbl_prefix)) !== FALSE) {
                $status_upload_photo = 1;
                $rownum_messages[$rownum][] = 'SUCCESS - Copy & update the user\'s photo. Filename: ' . $photofile;
              }
            }

            if ($__INSERT_IDCARD__) {

              $tbl_prefix = ($__USE_DUMMY_TABLES__) ? $TABLE_PREFIX_FOR_DUMMY : '';
              if (($idcardfile = $this->update_idcard($user_id, $idcard_link, $modifiedby, $tbl_prefix)) !== FALSE) {
                $status_upload_idcard = 1;
                $rownum_messages[$rownum][] = 'SUCCESS - Copy & update the user\'s photo. Filename: ' . $idcardfile;
              }
            }


            if ($__INSERT_IJAZAH__) {
              $tbl_prefix = ($__USE_DUMMY_TABLES__) ? $TABLE_PREFIX_FOR_DUMMY : '';
              if (($idcardfile = $this->update_ijazah($user_id, $user_edu_id, $ijazah_link, $modifiedby, $tbl_prefix)) !== FALSE) {
                $status_upload_ijazah = 1;
                $rownum_messages[$rownum][] = 'SUCCESS - Copy & update the user\'s education certificate (ijazah). Filename: ' . $idcardfile;
              }
            }

            $result_list[] = array(
              "row_num" => $rownum,
              "message" => 'SUCCESS - All insert user_id: ' . $user_id . ', email: ' . $email . ', address_id: ' . $addr_id . ', experience_id: ' . (($__INSERT_EXPERIENCE__) ? $exprience_id : ''),
              "details" => $rownum_messages[$rownum]
            );

            $csvlog_data = str_putcsv(array(
              $rownum,
              $ext_user_id,
              $email,
              $user_id,
              $no_kta,
              $status_db_users,
              $status_db_profile,
              $status_db_kolektif,
              $status_db_address,
              $status_db_members,
              $status_db_transfer,
              $status_db_job,
              $status_db_education,
              $status_db_commit,
              $status_upload_photo,
              $status_upload_idcard,
              $status_upload_ijazah,
              'SUCCESS'
            ));

            if (!write_file($csvlog_file, $csvlog_data . "\n", 'a+')) {
              throw new Exception('Unable to write the batch userprovisioning log to a CSV file.');
            }
          }
        } catch (Exception $t) {
          log_message('error', '[SIMPONI] ' . __FUNCTION__ . ' - ' .  $t->getMessage());
          $rownum_messages[$rownum][] = 'ERROR - ' .  $t->getMessage();
          $result_list[] = array(
            "row_no" => $rownum,
            "message" => 'ERROR - Last error message: ' . $t->getMessage(),
            "details" => $rownum_messages[$rownum]
          );
          $csvlog_data = str_putcsv(array(
            $rownum,
            $ext_user_id,
            $email,
            $user_id,
            $no_kta,
            $status_db_users,
            $status_db_profile,
            $status_db_kolektif,
            $status_db_address,
            $status_db_members,
            $status_db_transfer,
            $status_db_job,
            $status_db_education,
            $status_db_commit,
            $status_upload_photo,
            $status_upload_idcard,
            $status_upload_ijazah,
            'ERROR:' .  $t->getMessage()
          ));

          if (!write_file($csvlog_file, $csvlog_data . "\n", 'a+')) {
            throw new Exception('Unable to write the batch userprovisioning log to a CSV file.');
          }
        }
      } // end while loop
    }

    fclose($fhandle);


    if (!write_file($log_file, json_encode($result_list, JSON_PRETTY_PRINT) . "\n", 'a+')) {
      throw new Exception('Unable to write the batch userprovisioning log to a file.');
    }

    if ($success_count == 0) {
      return $this->output
        ->set_content_type('application/json')
        ->set_status_header(404)
        ->set_output(
          json_encode([
            'status' => FALSE,
            'message' => 'Failed processing ALL {' . $rowrum_processed . '} rows in file ' . $filename,
            'result' =>  $result_list
          ])
        );
    } else {
      return $this->output
        ->set_content_type('application/json')
        ->set_status_header(200)
        ->set_output(
          json_encode([
            'status' => TRUE,
            'message' => 'Successfuly processing ALL {' . $success_count . '} rows in file ' . $filename,
            'result' =>  $result_list
          ])
        );
    }
  }
}
