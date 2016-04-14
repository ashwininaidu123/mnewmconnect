<?php
class Mobapp extends Controller
{
    
    function Mobapp()
    {
        parent::controller();
        
        $this->load->model('mobappmodel', 'MM');
        $this->form_validation->_error_prefix = "<br>";
        $this->form_validation->_error_suffix = "";
    }
    
    function index()
    {
        echo "This is a privete URL.";
    }
    function mconnect_otp()
    {
        $data           = array();
        $data['number'] = $_POST['number'];
        $otp            = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $data['otp']    = $otp;
        $user           = $this->MM->mob_auth($data);
        if (is_numeric($user)) {
            $this->session->sess_create();
            $this->session->set_userdata('u', $this->input->post('number'));
            $sql = "SELECT * FROM mconnect_numotp WHERE otp='" . $user . "'";
            $emp = $this->db->query($sql)->row_array();
            $this->session->set_userdata('otp', $user);
            $api     = "http://115.249.28.90/sms/sendSMS.php?from=VMCIND";
            $message = "Your one time passwod for Mconnect is: " . $user;
            $sms     = $api . "&to=" . substr($emp['number'], -10, 10) . "&text=" . urlencode($message);
            $sms     = file($sms);
            
            $out = array(
                'otp' => $user,
                'code' => '400',
                'msg' => 'success'
            );
            echo json_encode($out);
        } else {
            $out = array(
                'code' => '202',
                'msg' => 'Already registered Please Login'
            );
            echo json_encode($out);
            exit;
        }
    }
    
    function checkAuth()
    {
        $user = $this->MM->authenticate($_POST);
        if (empty($user)) {
            $out = array(
                'code' => '202',
                'msg' => 'Invalid otp'
            );
            echo json_encode($out);
            exit;
        } else {
            $out = $user;
            $out = array(
                'code' => '400',
                'msg' => 'Otp verified'
            );
            echo json_encode($out);
            exit;
        }
        
    }
    function register()
    {
        $user = $this->MM->register($_POST);
        if($user == '1') {
             $out = array(
                'code' => '202',
                'msg' => 'Already registered Please Login'
            );
            echo json_encode($out);
            exit;
        } else{
			 $out = array(
                'code' => '400',
                'msg' => 'successfully Registered',
       

            );
            echo json_encode($out);
            exit;
        }
    }
    function login()
    {
        $this->form_validation->set_rules('email', 'email', 'required');
        $this->form_validation->set_rules('password', 'password', 'required');
        if (!$this->form_validation->run() == FALSE) {
            $user = $this->MM->loginDetails($_POST);
            if (empty($user)) {
                $out = array(
                    'code' => '202',
                    'msg' => 'Invalid Credentials'
                );
                echo json_encode($out);
                exit;
            }else{
                $out = array(
                    'code' => '400',
                    'msg' => 'Login Successful',
                    'authkey' => $user['authKey'],
                    'username' => $user['usrname'],
                    'image' => $user['image']
                );
                echo json_encode($out);
                exit;
            }
        } else {
            $out = array(
                'code' => '420',
                'msg' => validation_errors()
            );
        }
        echo json_encode($out);
        exit;
    }
    function forgot_pws()
    {
        $this->form_validation->set_rules('number', 'number', 'required');
        if (!$this->form_validation->run() == FALSE) {
            $otp         = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $data['otp'] = $otp;
            $user        = $this->MM->forgot_pws($data);
            if ($user == true) {
                $sql     = "SELECT * FROM mconnect_register WHERE usrnumber='" . $_POST['number'] . "'";
                $emp     = $this->db->query($sql)->row_array();
                $api     = "http://115.249.28.90/sms/sendSMS.php?from=VMCIND";
                $message = "Your OTP for Mconnect password change is: " . $emp['otp'];
                $sms     = $api . "&to=" . substr($emp['usrnumber'], -10, 10) . "&text=" . urlencode($message);
                $sms     = file($sms);
                $out     = array(
                    'code' => '400',
                    'msg' => 'Reset otp sent to your mobile number',
                    'otp' => $emp['otp']
                );
                echo json_encode($out);
                exit;
            } else {
                $out = array(
                    'code' => '202',
                    'msg' => 'Invalid Number'
                );
                echo json_encode($out);
                exit;
            }
        } else {
            $out = array(
                'code' => '420',
                'msg' => validation_errors()
            );
        }
        echo json_encode($out);
        exit;
    }

    function changepwd()
    {
        $this->form_validation->set_rules('password', 'newpassword', 'required');
        $this->form_validation->set_rules('otp', 'otp', 'required');
        if (!$this->form_validation->run() == FALSE) {
            $user = $this->MM->changepwd();
            if ($user == true) {
                $out = array(
                    'code' => '400',
                    'msg' => 'Password changed successfully'
                );
                echo json_encode($out);
                exit;
            }else {
                $out = array(
                    'code' => '202',
                    'msg' => 'Invalid otp'
                );
            }
            echo json_encode($out);
            exit;
        } else{
            $out = array(
                'code' => '420',
                'msg' => validation_errors()
            );
        }
        echo json_encode($out);
        exit;
    }
    function checkvisit()
    {
        $this->form_validation->set_rules('beaconid', 'beaconid', 'required');
        $this->form_validation->set_rules('authkey', 'authkey', 'required');
        if (!$this->form_validation->run() == FALSE) {
            $user = $this->MM->checkvisit();
             if($user == 'detailsent') {
                $out = array(
                     'code' => '200',
                     'msg' => 'details sent'
                );
                echo json_encode($out);
                exit;
            //~ }elseif($user == 'enterdetail') {
                //~ $out = array(
                    //~ 'code' => '201',
                    //~ 'msg' => 'visit entry'
                //~ );
                //~ echo json_encode($out);
                //~ exit;
            }elseif($user['id']== '1') {
                $out = array(
                    'code' => '202',
                    'msg' => 'Select your option',
                    'id' => $user['id'],
                    'siteid' => $user['siteid'],
                    'beaconid' => $user['beaconid'],
			        'data' => $user['data']
                   );	   
                echo json_encode($out);
                exit;
            }else {
				
                $out = array(
                    'code' => '400',
                    'msg' => 'Description of Site',
                    'name' => $user['name'].'-'. $user['lname'],
                    'desc' => $user['desc'],
                    'like' => $user['like'],
                    'logo' => $user['logo'],
                    'number' => $user['number'],
                    'siteid' => $user['siteid'],
                    'media' => $user['media'],
                    'beaconid' => $user['beaconid'],
                );
            }
            echo json_encode($out);
            exit;
        } else {
            $out = array(
                'code' => '420',
                'msg' => validation_errors()
            );
        }
        echo json_encode($out);
        exit;
    }

    function visitlist()
    {
        $this->form_validation->set_rules('authkey', 'authkey', 'required');
        if (!$this->form_validation->run() == FALSE) {
            $user = $this->MM->visitlist();
             if (empty($user)) {
                $out = array(
                    'code' => '202',
                    'msg' => 'No visits'
                );
                echo json_encode($out);
                exit;
            }else{
				
                $out = array(
                    'code' => '400',
                    'msg' => 'Visited sites',
                    'data' => $user
                );
                echo json_encode($out);
                exit;
            }
        } else {
            $out = array(
                'code' => '420',
                'msg' => validation_errors()
            );
        }
        echo json_encode($out);
        exit;
    }
    function sendlocation(){
      $this->form_validation->set_rules('siteid', 'siteid', 'required');
        if (!$this->form_validation->run() == FALSE) {
            $user = $this->MM->sendlocation();
             if (empty($user)) {
                $out = array(
                    'code' => '202',
                    'msg' => 'No locations'
                );
                echo json_encode($out);
                exit;
            }else{
				
                $out = array(
                    'code' => '400',
                    'msg' => 'Site Locations',
                    'data' => $user['site'],
                    'sitemedia' => $user['sitemedia'],
                    'sitedesc' => $user['sitedesc']
                );
                echo json_encode($out);
                exit;
            }
        } else {
            $out = array(
                'code' => '420',
                'msg' => validation_errors()
            );
        }
        echo json_encode($out);
        exit;
	}

    function locdetail_offline(){
      $this->form_validation->set_rules('beaconid', 'beaconid', 'required');
      $this->form_validation->set_rules('id', 'id', 'required');
        if (!$this->form_validation->run() == FALSE) {
            $user = $this->MM->locdetail_offline();
             if (empty($user)) {
                $out = array(
                    'code' => '202',
                    'msg' => 'No locations'
                );
                echo json_encode($out);
                exit;
            }else{
				
                $out = array(
                    'code' => '400',
                    'msg' => 'Site Locations',
                    'data' => $user
                );
                echo json_encode($out);
                exit;
            }
        } else {
            $out = array(
                'code' => '420',
                'msg' => validation_errors()
            );
        }
        echo json_encode($out);
        exit;
	}

    function getallimages(){
      $this->form_validation->set_rules('siteid', 'siteid', 'required');
        if (!$this->form_validation->run() == FALSE) {
            $user = $this->MM->getallimages();
             if (empty($user)) {
                $out = array(
                    'code' => '202',
                    'msg' => 'No images'
                );
                echo json_encode($out);
                exit;
            }else{
				
                $out = array(
                    'code' => '400',
                    'msg' => 'Site images',
                    'data' => $user
                );
                echo json_encode($out);
                exit;
            }
        } else {
            $out = array(
                'code' => '420',
                'msg' => validation_errors()
            );
        }
        echo json_encode($out);
        exit;
	}
   function deletevisitlist()
    {
        $this->form_validation->set_rules('authkey', 'authkey', 'required');
        $this->form_validation->set_rules('siteid', 'siteid', 'required');
        if (!$this->form_validation->run() == FALSE) {
            $user = $this->MM->deletevisitlist();
             if (empty($user)) {
                $out = array(
                    'code' => '202',
                    'msg' => 'No visits'
                );
                echo json_encode($out);
                exit;
            }else{
                $out = array(
                    'code' => '400',
                    'msg' => 'Site deleted successfully'
                );
                echo json_encode($out);
                exit;
            }
        } else {
            $out = array(
                'code' => '420',
                'msg' => validation_errors()
            );
        }
        echo json_encode($out);
        exit;
    }
    
    function like(){
        $this->form_validation->set_rules('authkey', 'authkey', 'required');
        $this->form_validation->set_rules('siteid', 'siteid', 'required');
        $this->form_validation->set_rules('bid', 'bid', 'required');
        $this->form_validation->set_rules('pid', 'pid', 'required');
        if (!$this->form_validation->run() == FALSE) {
            $user = $this->MM->likesite();
             if ($user == true){
                $out = array(
                    'code' => '400',
                    'msg' => 'Site liked'
                );
                echo json_encode($out);
                exit;
            }else{
                $out = array(
                    'code' => '200',
                    'msg' => 'Site unliked'
                );
                echo json_encode($out);
                exit;
            }
        } else {
            $out = array(
                'code' => '420',
                'msg' => validation_errors()
            );
        }
        echo json_encode($out);
        exit;
    }
    
    function profileupdate(){
        $this->form_validation->set_rules('authkey', 'authkey', 'required');
        if (!$this->form_validation->run() == FALSE) {
            $user = $this->MM->profileupdate();
             if ($user == true){
                $out = array(
                    'code' => '400',
                    'msg' => 'Profile updated'
                );
                echo json_encode($out);
                exit;
            }
        } else {
            $out = array(
                'code' => '420',
                'msg' => validation_errors()
            );
        }
        echo json_encode($out);
        exit;
    }
    function getprofiledetail(){
        $this->form_validation->set_rules('authkey', 'authkey', 'required');
        if (!$this->form_validation->run() == FALSE) {
            $user = $this->MM->getprofiledetail();
  
            if (empty($user)) {
                $out = array(
                    'code' => '202',
                    'msg' => 'No details'
                );
                echo json_encode($out);
                exit;
            }else{
                $out = array(
                    'code' => '400',
                    'msg' => 'Profile details',
                    'data' => $user
                );
                echo json_encode($out);
                exit;
            }
        } else {
            $out = array(
                'code' => '420',
                'msg' => validation_errors()
            );
        }
        echo json_encode($out);
        exit;
    }
     function getlikes(){
        $this->form_validation->set_rules('authkey', 'authkey', 'required');
        if (!$this->form_validation->run() == FALSE) {
            $user = $this->MM->getlikes();
          if($user == 0){
                $out = array(
                    'code' => '202',
                    'msg' => 'No Likes'
                );
                echo json_encode($out);
                exit;
           }else{
                $out = array(
                    'code' => '400',
                    'msg' => 'Likes',
                    'data' => $user
                );
                echo json_encode($out);
                exit;
            }
        } else {
            $out = array(
                'code' => '420',
                'msg' => validation_errors()
            );
        }
        echo json_encode($out);
        exit;
    }
   function userintrest(){
            $user = $this->MM->userintrest();
            if ($user != true){
                $out = array(
                    'code' => '202',
                    'msg' => 'Not updated'
                );
                echo json_encode($out);
                exit;
            }else{
                $out = array(
                    'code' => '400',
                    'msg' => 'Updated',
                );
                echo json_encode($out);
                exit;
            }

    }
     function getoffers(){
            $user = $this->MM->getoffers();
            if (empty($user)) {
                $out = array(
                    'code' => '202',
                    'msg' => 'No Offers'
                );
                echo json_encode($out);
                exit;
            }else{
                $out = array(
                    'code' => '400',
                    'msg' => 'Offers',
                    'data' => $user
                );
                echo json_encode($out);
                exit;
            }
    }
        //~ function getallproperty(){
		    //~ $this->form_validation->set_rules('authkey', 'authkey', 'required');
		      //~ if (!$this->form_validation->run() == FALSE) {
            //~ $user = $this->MM->getallproperty();
            //~ if (empty($user)) {
                //~ $out = array(
                    //~ 'code' => '202',
                    //~ 'msg' => 'No Property'
                //~ );
                //~ echo json_encode($out);
                //~ exit;
            //~ }else{
//~ 
                //~ $out = array(
                    //~ 'code' => '400',
                    //~ 'msg' => 'All Property',
                    //~ 'data' => $user
                //~ );
                //~ 
                //~ echo json_encode($out);
                //~ exit;
            //~ }
            //~ } else {
            //~ $out = array(
                //~ 'code' => '420',
                //~ 'msg' => validation_errors()
            //~ );
        //~ }
        //~ echo json_encode($out);
        //~ exit;
    //~ }
       function getallsites(){
		    $this->form_validation->set_rules('authkey', 'authkey', 'required');
		      if (!$this->form_validation->run() == FALSE) {
            $user = $this->MM->getallsites();
            if (empty($user)) {
                $out = array(
                    'code' => '202',
                    'msg' => 'No Sites'
                );
                echo json_encode($out);
                exit;
            }else{

                $out = array(
                    'code' => '400',
                    'msg' => 'All Sites',
                    'data' => $user
                );
                
                echo json_encode($out);
                exit;
            }
            } else {
            $out = array(
                'code' => '420',
                'msg' => validation_errors()
            );
        }
        echo json_encode($out);
        exit;
    }
   function refer()
    {
        $this->form_validation->set_rules('email', 'email', 'required');
        $this->form_validation->set_rules('number', 'number', 'required');
        $this->form_validation->set_rules('pid', 'pid', 'required');
        if (!$this->form_validation->run() == FALSE) {
            $user = $this->MM->refer($_POST);
            if (empty($user)) {
                $out = array(
                    'code' => '202',
                    'msg' => 'Already refered'
                );
                echo json_encode($out);
                exit;
            }else{
                $out = array(
                    'code' => '400',
                    'msg' => 'Successful',
                );
                echo json_encode($out);
                exit;
            }
        } else {
            $out = array(
                'code' => '420',
                'msg' => validation_errors()
            );
        }
        echo json_encode($out);
        exit;
    }
   function feedback(){
            $user = $this->MM->feedback();
            if ($user != true){
                $out = array(
                    'code' => '202',
                    'msg' => 'Not Submitted'
                );
                echo json_encode($out);
                exit;
            }else{
                $out = array(
                    'code' => '400',
                    'msg' => 'Successfully Submitted',
                );
                echo json_encode($out);
                exit;
            }

    }
    
	function getList(){
		//~ $_POST = array(
					//~ 'authKey'=>'1.1.5260dd755c18f'
					//~ ,'type'=>'followup'	//  track|ivrs|x|lead|followup
					//~ ,'ofset'=>'0'
					//~ ,'limit'=>'20'
					//~ ,'gid'=>'0'
				//~ );
		$this->form_validation->set_rules('authKey', 'authKey', 'required');
		$this->form_validation->set_rules('type', 'Type', 'required');
		if(!$this->form_validation->run() == FALSE){
			$user = $this->MM->userByKey($_POST['authKey']);
			if(empty($user)){
				$out = array(
							'code'=>'401',
							'msg'=>'Invalid User'
					);
			}else{
				$data = array(
						'bid'=>$user['bid'],
						'eid'=>$user['eid'],
						'type'=>$_POST['type'],
						'ofset'=>(isset($_POST['ofset']) ? $_POST['ofset'] : 0),
						'limit'=>(isset($_POST['limit']) ? $_POST['limit'] : 20),
						'gid'=>((isset($_POST['gid']) && $_POST['gid']>0) ? $_POST['gid'] : '')
					);
				$list = $this->MM->getAllList($data);
				if(isset($list['nodata'])){
					$out['code'] = "404";
					$out['msg'] = "No record found";
				}else{
					$out = $list;
					$out['code'] = "202";
					$out['msg'] = "Request Accepted";
				}
			}
		}else{
			$out = array(
						'code'=>'400',
						'msg'=>validation_errors()
					);
		}
		//~ echo "<pre>";
		//~ $x = json_encode($out);
		//~ print_r(json_decode($x));
		echo json_encode($out);exit;
	}
	
	function getFollowupHistory(){
		//~ $_POST = array(
					//~ 'authKey'=>'1.1.5260dd755c18f'
					//~ ,'type'=>'track'	//  track|ivrs|x|lead|followup
					//~ ,'callid'=>'90361034341383650992'
					//~ ,'ofset'=>'0'
					//~ ,'limit'=>'20'
				//~ );
		$this->form_validation->set_rules('authKey', 'authKey', 'required');
		$this->form_validation->set_rules('type', 'Type', 'required');
		$this->form_validation->set_rules('callid', 'callid', 'required');
		if(!$this->form_validation->run() == FALSE){
			$user = $this->MM->userByKey($_POST['authKey']);
			if(empty($user)){
				$out = array(
							'code'=>'401',
							'msg'=>'Invalid User'
					);
			}else{
				$data = array(
						'bid'=>$user['bid'],
						'eid'=>$user['eid'],
						'dataid'=>$_POST['callid'],
						'type'=>$_POST['type'],
						'ofset'=>(isset($_POST['ofset']) ? $_POST['ofset'] : 0),
						'limit'=>(isset($_POST['limit']) ? $_POST['limit'] : 20)
					);
				$list = $this->MM->getFollowupHistory($data);
				if(empty($list)){
					$out['code'] = "404";
					$out['msg'] = "No record found";
				}else{
					$out = $list;
					$out['code'] = "202";
					$out['msg'] = "Request Accepted";
				}
			}
		}else{
			$out = array(
						'code'=>'400',
						'msg'=>validation_errors()
					);
		}
		//~ echo "<pre>";
		//~ $x = json_encode($out);
		//~ print_r(json_decode($x));
		echo json_encode($out);exit;
	}
	
	function getDetail(){
		//~ $_POST = array(
					//~ 'authKey'=>'1.1.5260dd755c18f'
					//~ ,'type'=>'followup'	//  track|ivrs|x|lead|followup
					//~ ,'callid'=>'21'
					//~ ,'groupname'=>'calltrack'
				//~ );
		$this->form_validation->set_rules('authKey', 'auth Key', 'required');
		$this->form_validation->set_rules('type', 'Type', 'required');
		$this->form_validation->set_rules('callid', 'callid', 'required');
		$this->form_validation->set_rules('groupname', 'Group Name', 'required');
		if(!$this->form_validation->run() == FALSE){
			$user = $this->MM->userByKey($_POST['authKey']);
			if(empty($user)){
				$out = array(
							'code'=>'401',
							'msg'=>'Invalid User'
					);
			}else{
				$data = array(
						'bid'		=>$user['bid'],
						'eid'		=>$user['eid'],
						'type'		=>$_POST['type'],
						'callid'	=>$_POST['callid'],
						'groupname'	=>$_POST['groupname'],
					);
				$list = $this->MM->getDetails($data);
				if(isset($list['nodata'])){
					$out['code'] = "404";
					$out['msg'] = "No record found";
				}else{
					$out = $list;
					$out['code'] = "202";
					$out['msg'] = "Request Accepted";
				}
			}
		}else{
			$out = array(
						'code'=>'400',
						'msg'=>validation_errors()
					);
		}
		echo json_encode($out);exit;
	}
	
	function postDetail(){
		//~ $_POST = array(
				//~ 'authKey'=>'1.1.5260dd755c18f'
				//~ ,'type'=>'track'	//  track|ivrs|x|lead|followup
				//~ ,'callid'=>'096541004011353999093'
				//~ ,'groupname'=>'ext102'
				//~ ,'callername'=>'fd hdfbh'
			//~ );
		$this->form_validation->set_rules('authKey', 'auth Key', 'required');
		$this->form_validation->set_rules('type', 'Type', 'required');
		$this->form_validation->set_rules('callid', 'callid', 'required');
		$this->form_validation->set_rules('groupname', 'Group Name', 'required');
		if(!$this->form_validation->run() == FALSE){
			$user = $this->MM->userByKey($_POST['authKey']);
			if(empty($user)){
				$out = array(
							'code'=>'401',
							'msg'=>'Invalid User'
					);
			}else{
				$data = $_POST;
				$data['bid'] = $user['bid'];
				$data['eid'] = $user['eid'];
				
				if(!$this->MM->postDetails($data)){
					$out['code'] = "424";
					$out['msg'] = "Request Failed";
				}else{
					$out['code'] = "202";
					$out['msg'] = "Request Accepted";
				}
			}
		}else{
			$out = array(
						'code'=>'400',
						'msg'=>validation_errors()
					);
		}
		//~ echo "<pre>";
		//~ $x = json_encode($out);
		//~ print_r(json_decode($x));
		echo json_encode($out);exit;
	}
	
	function getGroups(){
		//~ $_POST = array(
					//~ 'authKey'=>'1.1.5260dd755c18f'
					//~ ,'type'=>'lead'	//  track|ivrs|x|lead|followup
				//~ );
				
		$this->form_validation->set_rules('authKey', 'authKey', 'required');
		$this->form_validation->set_rules('type', 'Type', 'required');
		if(!$this->form_validation->run() == FALSE){
			$user = $this->MM->userByKey($_POST['authKey']);
			if(empty($user)){
				$out = array(
							'code'=>'401',
							'msg'=>'Invalid User'
					);
			}else{
				$data = array(
						'bid'=>$user['bid'],
						'eid'=>$user['eid'],
						'type'=>$_POST['type'],
						'ofset'=>(isset($_POST['ofset']) ? $_POST['ofset'] : 0),
						'limit'=>(isset($_POST['limit']) ? $_POST['limit'] : 20),
						'gid'=>(isset($_POST['gid']) ? $_POST['gid'] : '')
					);
				$list = $this->MM->getAllGroups($data);
				if(isset($list['nodata'])){
					$out['code'] = "404";
					$out['msg'] = "No record found";
				}else{
					$out = $list;
					$out['code'] = "202";
					$out['msg'] = "Request Accepted";
				}
			}
		}else{
			$out = array(
						'code'=>'400',
						'msg'=>validation_errors()
					);
		}
		//$x = json_encode($out);
		//print_r(json_decode($x));
		echo json_encode($out);exit;
	}
	
	function followupFrm(){
		//~ $_POST = array(
					//~ 'authKey'=>'1.1.5260dd755c18f'
				//~ );
		$this->form_validation->set_rules('authKey', 'auth Key', 'required');
		if(!$this->form_validation->run() == FALSE){
			$user = $this->MM->userByKey($_POST['authKey']);
			if(empty($user)){
				$out = array(
							'code'=>'401',
							'msg'=>'Invalid User'
					);
			}else{
				$data = array(
						'bid'		=>$user['bid'],
						'eid'		=>$user['eid']
					);
				$list = $this->MM->getFollowupFielsd($data);
				if(isset($list['nodata'])){
					$out['code'] = "404";
					$out['msg'] = "No record found";
				}else{
					$out = $list;
					$out['code'] = "202";
					$out['msg'] = "Request Accepted";
				}
			}
		}else{
			$out = array(
						'code'=>'400',
						'msg'=>validation_errors()
					);
		}
		echo json_encode($out);exit;
	}
	
	function check_url(){
		$urls = array('qa1.vmc.in','mcube.vmc.in','mcube.vmc.in/calltrack');
		if(in_array($this->input->post('url'),$urls)){
			return TRUE;
		}else{
			$this->form_validation->set_message('check_url', 'Server Address Doesnot exists');
			return FALSE;
			
		}
	}
}


?>
