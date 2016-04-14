<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Simplelogin Class
 *
 * Makes authentication simple
 *
 * Simplelogin is released to the public domain
 * (use it however you want to)
 * 
 * Simplelogin expects this database setup
 * (if you are not using this setup you may
 * need to do some tweaking)
 * 

	#This is for a MySQL table
	CREATE TABLE `users` (
	`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`username` VARCHAR( 64 ) NOT NULL ,
	`password` VARCHAR( 64 ) NOT NULL ,
	UNIQUE (
	`username`
	)
	);

 * 
 */
class Simplelogin
{
	var $CI;
	var $user_table = 'user';
	var $master_table='master_admin';
	var $partner='partner';
	
	function Simplelogin(){
		// get_instance does not work well in PHP 4
		// you end up with two instances
		// of the CI object and missing data
		// when you call get_instance in the constructor
		//$this->CI =& get_instance();
	}

	/**
	 * Create a user account
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	function create($user = '', $password = '', $auto_login = true) {
		//Put here for PHP 4 users
		$this->CI =& get_instance();		

		//Make sure account info was sent
		if($user == '' OR $password == '') {
			return false;
		}
		
		//Check against user table
		//$this->CI->db->where('username', $user); 
		//$query = $this->CI->db->getwhere($this->user_table);
		$query = $this->CI->db->query("SELECT u.* FROM ".$this->user_table." u
					LEFT JOIN business b on u.bid=b.bid
					WHERE u.status='1' AND b.status='1'");
		//echo $this->db->last_query();exit;
		if ($query->num_rows() > 0) {
			//username already exists
			return false;
			
		} else {
			//Encrypt password
			$password = md5($password);
			
			//Insert account into the database
			$data = array(
						'username' => $user,
						'password' => $password
					);
			$this->CI->db->set($data); 
			if(!$this->CI->db->insert($this->user_table)) {
				//There was a problem!
				return false;						
			}
			$user_id = $this->CI->db->insert_id();
			
			//Automatically login to created account
			if($auto_login) {		
				//Destroy old session
				$this->CI->session->sess_destroy();
				
				//Create a fresh, brand new session
				$this->CI->session->sess_create();
				
				//Set session data
				$this->CI->session->set_userdata(array('id' => $user_id,'username' => $user));
				
				//Set logged_in to true
				$this->CI->session->set_userdata(array('logged_in' => true));			
			
			}
			
			//Login was successful			
			return true;
		}

	}

	/**
	 * Delete user
	 *
	 * @access	public
	 * @param integer
	 * @return	bool
	 */
	function delete($user_id) {
		//Put here for PHP 4 users
		$this->CI =& get_instance();
		
		if(!is_numeric($user_id)) {
			//There was a problem
			return false;			
		}

		if($this->CI->db->delete($this->user_table, array('id' => $user_id))) {
			//Database call was successful, user is deleted
			return true;
		} else {
			//There was a problem
			return false;
		}
	}


	/**
	 * Login and sets session variables
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	function login($user = '', $password = '') {
		//Put here for PHP 4 users
		$this->CI =& get_instance();		

		//Make sure login info was sent
		if($user == '' OR $password == '') {
			return false;
		}
		//print_r($_POST);exit;
		//Check if already logged in
		if($this->CI->session->userdata('username') == $user) {
			//User is already logged in.
			return false;
		}

		//Check against user table
		//$this->CI->db->where('username', $user,"status='1'"); 
		//$query = $this->CI->db->getwhere($this->user_table);
		$query = $this->CI->db->query("SELECT u.*,b.pid,b.act,b.registrationdate,b.dnd_status FROM ".$this->user_table." u
										LEFT JOIN business b on u.bid=b.bid
										WHERE u.status='1' AND b.status='1' AND u.username='".$user."'");
		if ($query->num_rows() > 0) {
			$row = $query->row_array(); 
			//Check against password
			if(md5($password) != $row['password']) {
				return false;
			}
			
					//Destroy old session
					$this->CI->session->sess_destroy();
					//Create a fresh, brand new session
					$this->CI->session->sess_create();
					//Remove the password field
					unset($row['password']);
					//print_r($row);exit;
					//Set session data
					$this->CI->session->set_userdata($row);
					//Set logged_in to true
					$this->CI->session->set_userdata(array('logged_in' => true));
					
					$SNO=$this->CI->db->query("SELECT COALESCE(MAX(`sno`),0)+1 as id FROM ".$this->CI->session->userdata('bid')."_activitylog")->row()->id;
					$this->CI->db->set('sno',$SNO); 
					$this->CI->db->set('bid', $this->CI->session->userdata('bid')); 
					$this->CI->db->set('uid', $this->CI->session->userdata('uid')); 
					$this->CI->db->set('action',$this->CI->session->userdata('username')." Logged In from ". $_SERVER['REMOTE_ADDR']); 
					$this->CI->db->insert($this->CI->session->userdata('bid')."_activitylog");
					
					return true;			
		}else{
			return false;
		}

	}
	function exlogin($user = '', $password = '') {
		//echo $user."=".$password;
		//Put here for PHP 4 users
		$this->CI =& get_instance();		

		//Make sure login info was sent
		if($user == '' OR $password == '') {
			//echo $user."=".$password;
			return false;
		}
		
		//Check if already logged in
		if($this->CI->session->userdata('username') == $user){
			return false;
		}

		$host=$_SERVER['HTTP_HOST'];
		$query=$this->CI->db->query("SELECT * from vmc_executives where email='".$user."' AND status=1");
		if ($query->num_rows() > 0) {
			$row = $query->row_array(); 
			
			//Check against password
				if(md5($password) != $row['password']) {
					
					return false;
				}
				
				//Destroy old session
				$this->CI->session->sess_destroy();$this->CI =& get_instance();
				
				//Create a fresh, brand new session
				$this->CI->session->sess_create();
				
				//Remove the password field
				unset($row['password']);
				//print_r($row);exit;
				//Set session data
				$this->CI->session->set_userdata($row);
				
				//Set logged_in to true
				$this->CI->session->set_userdata(array('exe_in' => true));		
				//Login was successful			
				return true;
			
		} else {
			$this->CI->session->set_flashdata('msgt','error');
			$this->CI->session->set_flashdata('msg','Unauthorised Login');
			return false;
		}	

	}
	function masterlogin($user = '', $password = '') {
		//echo $user."=".$password;
		//Put here for PHP 4 users
		$this->CI =& get_instance();		

		//Make sure login info was sent
		if($user == '' OR $password == '') {
			//echo $user."=".$password;
			return false;
		}
		
		//Check if already logged in
		if($this->CI->session->userdata('username') == $user){
			return false;
		}

		$host=$_SERVER['HTTP_HOST'];
		//$query=$this->CI->db->query("SELECT * from master_admin where username='".$user."' AND domain_name='$host' AND status=1");
		$query=$this->CI->db->query("SELECT * from master_admin where username='".$user."' AND status=1");
		if ($query->num_rows() > 0) {
			$row = $query->row_array(); 
			
			//Check against password
				if(md5($password) != $row['password']) {
					
					return false;
				}
				
				//Destroy old session
				$this->CI->session->sess_destroy();$this->CI =& get_instance();
				
				//Create a fresh, brand new session
				$this->CI->session->sess_create();
				
				//Remove the password field
				unset($row['password']);
				//print_r($row);exit;
				//Set session data
				$this->CI->session->set_userdata($row);
				
				//Set logged_in to true
				$this->CI->session->set_userdata(array('adminlogged_in' => true));		
				
				$SNO=$this->CI->db->query("SELECT COALESCE(MAX(`sno`),0)+1 as id FROM admin_activitylog")->row()->id;
				$this->CI->db->set('sno',$SNO); 
				$this->CI->db->set('bid', 0); 
				$this->CI->db->set('uid', $this->CI->session->userdata('uid')); 
				$this->CI->db->set('action',$this->CI->session->userdata('username')." Logged In ". date('Y-m-d H:i:s')); 
				$this->CI->db->set('Ipaddress',$_SERVER['REMOTE_ADDR']); 
				$this->CI->db->insert("admin_activitylog");
					
				//Login was successful			
				return true;
			
		} else {
			$this->CI->session->set_flashdata('msgt','error');
			$this->CI->session->set_flashdata('msg','Unauthorised Login');
			return false;
		}	

	}
	function partnerLogin($user = '', $password = '') {
		//echo $user."=".$password;
		//Put here for PHP 4 users
		$this->CI =& get_instance();		

		//Make sure login info was sent
		if($user == '' OR $password == '') {
			//echo $user."=".$password;
			return false;
		}
		
		//Check if already logged in
		if($this->CI->session->userdata('username') == $user){
			return false;
		}
		
		//Check against user table
		$host=$_SERVER['HTTP_HOST'];
		$query=$this->CI->db->query("SELECT * from partner where email='".$user."' AND domain_name='$host' AND status=1");
		if ($query->num_rows() > 0) {
			$row = $query->row_array(); 
			//Check against password
			if(md5($password) != $row['password']) {
				return false;
			}
			//Destroy old session
			$this->CI->session->sess_destroy();
			
			//Create a fresh, brand new session
			$this->CI->session->sess_create();
			
			//Remove the password field
			unset($row['password']);
			//print_r($row);exit;
			//Set session data
			$this->CI->session->set_userdata($row);
			
			//Set logged_in to true
			$this->CI->session->set_userdata(array('partnerlogged_in' => true));			
			//Login was successful			
			return true;
		} else {
			$this->CI->session->set_flashdata('msgt','error');
			$this->CI->session->set_flashdata('msg','Unauthorised Login');
			//No database result found
			return false;
		}	

	}

	/**
	 * Logout user
	 *
	 * @access	public
	 * @return	void
	 */
	function logout() {
		$this->CI =& get_instance();		
		if($this->CI->session->userdata('adminlogged_in')){
			$SNO=$this->CI->db->query("SELECT COALESCE(MAX(`sno`),0)+1 as id FROM admin_activitylog")->row()->id;
			$this->CI->db->set('sno',$SNO); 
			$this->CI->db->set('bid', 0); 
			$this->CI->db->set('uid', $this->CI->session->userdata('uid')); 
			$this->CI->db->set('action',"$username Logged Out at ". date('Y-m-d H:i:s')); 
			$this->CI->db->set('Ipaddress',$_SERVER['REMOTE_ADDR']); 
			$this->CI->db->insert("admin_activitylog");
		}
		if($this->CI->session->userdata('logged_in')){
			$SNO=$this->CI->db->query("SELECT COALESCE(MAX(`sno`),0)+1 as id FROM ".$this->CI->session->userdata('bid')."_activitylog")->row()->id;
			$this->CI->db->set('sno',$SNO); 
			$this->CI->db->set('bid', $this->CI->session->userdata('bid')); 
			$this->CI->db->set('uid', $this->CI->session->userdata('uid')); 
			$this->CI->db->set('action',$this->CI->session->userdata('username')." Logged out at ". date('Y-m-d H:i:s')); 
			$this->CI->db->insert("".$this->CI->session->userdata('bid')."_activitylog");
		}
		
		//Put here for PHP 4 users
				
		//Destroy session
		$this->CI->session->sess_destroy();
	}
}
?>
