<?php
class App1 extends Controller {
	var $data,$roleDetail;
	function App1(){
		parent::controller();
		//$this->load->model('sysconfmodel');
		//$this->data = $this->sysconfmodel->init();
		$this->load->model('appmodel'); 	
		$this->load->model('configmodel'); 	
		$this->load->model('commonmodel'); 	
		 $this->load->model('emailmodel');
		$this->modules = array(
						"calls" => array(
									"id"=>"6",
									"fields"=>array(
											'callername'=>array(
														'modid'=>'6',
														'fieldtype'=>'text',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'customlabel'=>'',
														'type'=>'s',
														),
											'callerbusiness'=>array(
														'modid'=>'6',
														'fieldtype'=>'text',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'customlabel'=>'',
														'type'=>'s',
														),
											'calleraddress'=>array(
														'modid'=>'6',
														'fieldtype'=>'textarea',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'customlabel'=>'',
														'type'=>'s',
														),
											'caller_email'=>array(
														'modid'=>'6',
														'fieldtype'=>'text',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'customlabel'=>'',
														'type'=>'s',
														),
											'callfrom'=>array(
														'modid'=>'6',
														'fieldtype'=>'label',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'customlabel'=>'',
														'type'=>'s',
														),
											'starttime'=>array(
														'modid'=>'6',
														'fieldtype'=>'label',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'customlabel'=>'',
														'type'=>'s',
														),
											'endtime'=>array(
														'modid'=>'6',
														'fieldtype'=>'label',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'customlabel'=>'',
														'type'=>'s',
														),
											'gid'=>array(
														'modid'=>'6',
														'fieldtype'=>'label',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'customlabel'=>'',
														'type'=>'s',
														),
											'pulse'=>array(
														'modid'=>'6',
														'fieldtype'=>'label',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'customlabel'=>'',
														'type'=>'s',
														),
											'remark'=>array(
													'modid'=>'6',
													'fieldtype'=>'textarea',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'customlabel'=>'',
													'type'=>'s',
													),
											'refid'=>array(
													'modid'=>'6',
													'fieldtype'=>'text',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'customlabel'=>'',
													'type'=>'s',
													),
											),
									),
						"ivrs" => array(
									"id"=>"16",
									"fields"=>array(
											'ivrstitle'=>array(
														'modid'=>'16',
														'fieldtype'=>'label',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'customlabel'=>'',
														'type'=>'s',
														),
											'callfrom'=>array(
														'modid'=>'16',
														'fieldtype'=>'label',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'customlabel'=>'',
														'type'=>'s',
														),
											'datetime'=>array(
														'modid'=>'16',
														'fieldtype'=>'label',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'customlabel'=>'',
														'type'=>'s',
														),
											'endtime'=>array(
													'modid'=>'16',
													'fieldtype'=>'label',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'customlabel'=>'',
													'type'=>'s',
													),
											'options'=>array(
													'modid'=>'16',
													'fieldtype'=>'label',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'customlabel'=>'',
													'type'=>'s',
													),
											'employee'=>array(
													'modid'=>'16',
													'fieldtype'=>'label',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'customlabel'=>'',
													'type'=>'s',
													),
											'name'=>array(
													'modid'=>'16',
													'fieldtype'=>'text',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'customlabel'=>'',
													'type'=>'s',
													),
											'email'=>array(
													'modid'=>'16',
													'fieldtype'=>'text',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'customlabel'=>'',
													'type'=>'s',
													),
											),
									),
						"pbx" => array(
									"id"=>"24",
									"fields"=>array(
											'pbxtitle'=>array(
														'modid'=>'24',
														'fieldtype'=>'label',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'customlabel'=>'',
														'type'=>'s',
														),
											'callfrom'=>array(
														'modid'=>'24',
														'fieldtype'=>'label',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'customlabel'=>'',
														'type'=>'s',
														),
											'starttime'=>array(
														'modid'=>'24',
														'fieldtype'=>'label',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'customlabel'=>'',
														'type'=>'s',
														),
											'endtime'=>array(
													'modid'=>'24',
													'fieldtype'=>'label',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'customlabel'=>'',
													'type'=>'s',
													),
											'pulse'=>array(
													'modid'=>'24',
													'fieldtype'=>'label',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'customlabel'=>'',
													'type'=>'s',
													),
											'name'=>array(
													'modid'=>'24',
													'fieldtype'=>'text',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'customlabel'=>'',
													'type'=>'s',
													),
											'email'=>array(
													'modid'=>'24',
													'fieldtype'=>'text',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'customlabel'=>'',
													'type'=>'s',
													),
											'extensions'=>array(
													'modid'=>'24',
													'fieldtype'=>'label',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'customlabel'=>'',
													'type'=>'s',
													),
											),
									),
						"leads" => array(
									"id"=>"26",
									"fields"=>array(
											'gid'=>array(
														'modid'=>'26',
														'fieldtype'=>'label',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'customlabel'=>'Group Name',
														'type'=>'s',
														),
											'assignto'=>array(
														'modid'=>'26',
														'fieldtype'=>'label',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'customlabel'=>'Assign to',
														'type'=>'s',
														),
											'enteredby'=>array(
														'modid'=>'26',
														'fieldtype'=>'label',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'customlabel'=>'Entered By',
														'type'=>'s',
														),
											'name'=>array(
													'modid'=>'26',
													'fieldtype'=>'text',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'customlabel'=>'',
													'type'=>'s',
													),
											'email'=>array(
													'modid'=>'26',
													'fieldtype'=>'text',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'customlabel'=>'',
													'type'=>'s',
													),
											'number'=>array(
													'modid'=>'26',
													'fieldtype'=>'text',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'customlabel'=>'',
													'type'=>'s',
													),
											'source'=>array(
													'modid'=>'26',
													'fieldtype'=>'text',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'customlabel'=>'',
													'type'=>'s',
													),
											),
									),
						"call_logs" => array(
									"id"=>"35",
									"fields"=>array(
											'call_type'=>array(
														'modid'=>'35',
														'fieldtype'=>'label',
														'options'=>'',
														'is_required'=>'1',
														'defaultvalue'=>'',
														'type'=>'s',
														'customlabel'=>'Call Type',
														),
											'number'=>array(
														'modid'=>'35',
														'fieldtype'=>'label',
														'options'=>'',
														'is_required'=>'1',
														'defaultvalue'=>'',
														'type'=>'s',
														'customlabel'=>'Number',
														),
											'duration'=>array(
														'modid'=>'35',
														'fieldtype'=>'label',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'type'=>'s',
														'customlabel'=>'Duration',
														),
											'name'=>array(
													'modid'=>'35',
													'fieldtype'=>'text',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'type'=>'s',
													'customlabel'=>'Name',
													),
											'email'=>array(
													'modid'=>'2',
													'fieldtype'=>'text',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'type'=>'s',
													'customlabel'=>'Email',
													),
											'recorded_file'=>array(
													'modid'=>'35',
													'fieldtype'=>'label',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'type'=>'s',
													'customlabel'=>'Recorded File',
													),
											'call_time'=>array(
													'modid'=>'35',
													'fieldtype'=>'label',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'type'=>'s',
													'customlabel'=>'Call Time',
													),
											
											),
									),
						"lead_followups" => array(
									"id"=>"29",
									"fields"=>array(
											'eid'=>array(
														'modid'=>'29',
														'fieldtype'=>'text',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'type'=>'s',
														),
											'comment'=>array(
														'modid'=>'29',
														'fieldtype'=>'text',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'type'=>'s',
														),
											'followupdate'=>array(
														'modid'=>'29',
														'fieldtype'=>'datepicker',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'type'=>'s',
														),
											
											),
									),
						
						);
		
		
	}
	
	// Authentication
	
	/*function checkAuth($format='json'){
		//$_POST['email'] = 'demo@vmc.in';
		$info = array();
		if(isset($_POST['email']) && $_POST['email']!=''){
			$info = $this->appmodel->authenticate($_POST['email']);
		}
		if(is_array($info) && count($info)>0){
			switch($format){
				case 'json':
				default:
					echo json_encode($info);
					break;
				case 'xml':
					$xml = new SimpleXMLElement('<auth/>');
					foreach($info as $k=>$v) {
						$xml->addChild($k , $v);
					}
					Header('Content-type: text/xml');
					print($xml->asXML());
					break;
			}
		}else{
			echo json_encode(array('erroe'=>'true'));
		}
	}*/
	/*
	 URL: https://mcube.vmc.in/app1/checkAuth
	 post data : email,password,url
	 return value: 
	 On Success: 
	 {"bid":"1","email":"demo@vmc.in","firstname":"VMC Demo","lastname":"VMC Demo","businessname":"VMC Demo","mobile":"8880009160","city":"Bangalore","state":"Karnataka","country":"India","eid":"1","track_incoming":"1","track_outgoing":"1","record_conversation":"1","login_type":"1","server":"https://mcube.vmc.in","code":"200","msg":"Business Login Successfull"}
	 On faileur:
	 {"msg":"","code":"201"}
	 
	 
	 */ 
	function checkAuth(){
		//print_r($_REQUEST);exit;
		/* $_POST =array(
				 'email'		=>'demo@vmc.in',
				 'password'		=>'tapan',
				 'url'		=>'http://mcube.vmc.in',
				 );*/

		$_POST['url']='http://mcube.vmc.in';
		$this->form_validation->set_rules('email', 'Email', 'required|min_length[4]|max_length[50]|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_rules('url', 'Server', 'required|callback_check_url');
		if(!$this->form_validation->run() == FALSE)
		{
		/*	$res = $this->appmodel->authenticate();
			
			if(count($res)>0)
			{
				$out['email']=$res->email;
				$out['firstname']=$res->firstname;
				$out['lastname']=$res->lastname;
				$out['businessname']=$res->businessname!=''?$res->businessname:'NA';
				$out['mobile']=$res->mobile;
				$out['city']=$res->city;
				$out['state']=$res->state;
				$out['country']=$res->country;
				$out['track_incoming']=(isset($res->track_incoming) && $res->track_incoming!='')?$res->track_incoming:'0';
				$out['track_outgoing']=(isset($res->track_outgoing) && $res->track_outgoing!='')?$res->track_outgoing:'0';
				$out['record_conversation']=(isset($res->record_conversation) && $res->record_conversation!='')?$res->record_conversation:'0';
				$out['login_type']='2';
				$out['code']='200';
				$out['server']=$this->input->post('url');
				$out['msg']= 'Login Successfull';
			}
			else
			{*/
				$bres = $this->checkAuth_business();
				if(count($bres)>0){
					$out['bid']=$bres->bid;
					$out['email']=$bres->empemail;
					$out['firstname']=$bres->empname;
					$out['lastname']=$bres->empname;
					$out['businessname']=$bres->businessname;
					$out['mobile']=$bres->empnumber;
					$out['city']=$bres->city;
					$out['state']=$bres->state;
					$out['country']=$bres->country;
					$out['eid']=$bres->eid;
					$out['track_incoming']=(isset($bres->track_incoming) && $bres->track_incoming!='')?$bres->track_incoming:'0';
					$out['track_outgoing']=(isset($bres->track_outgoing) && $bres->track_outgoing!='')?$bres->track_outgoing:'0';
					$out['record_conversation']=(isset($bres->record_conversation) && $bres->record_conversation!='')?$bres->record_conversation:'0';
					$out['login_type']='1';
					$out['server']=$this->input->post('url');
					$out['code']='200';
					$out['msg']= 'Business Login Successfull';
				}else{
					$out['code']= '401';
					$out['msg']= 'Invalid User Credentials';
				}
			//}
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
			$out['code']='201';
		}
		echo json_encode($out);
	}
	
	
	function checkAuth_business(){
		$username=$_POST['email'];
		$password=$_POST['password'];
		$earr = array();
		$sql=$this->db->query("SELECT * FROM m3.user WHERE username='$username' AND password='".md5($password)."' AND status='1'");
		 if($sql->num_rows()>0){
			 $arr = $sql->row();
			 unset($arr->password);
			 $bid = $arr->bid;
			 $eid = $arr->eid;
			 $eq = "SELECT e.*,b.*,ut.track_incoming,ut.track_outgoing,ut.record_conversation FROM m3.".$bid."_employee e 
					JOIN m3.business b ON b.bid=e.bid 
					LEFT JOIN m3.user_track_access ut ON ut.eid = e.eid
					WHERE e.bid='".$bid."' AND e.eid='".$eid."' AND e.status='1'";
			$esql=$this->db->query($eq);
			 if($esql->num_rows()>0){
				$earr = $esql->row(); 
			 }
			 return $earr;
		 }
		 else{
			 return array();
		 }
	}
	
	
	function checkAuth2($format='json'){
		$info = array();
		$exe = (isset($_REQUEST['reqid'])) ? explode('_',base64_decode($_REQUEST['reqid'])) : '';
		$authkey = isset($_REQUEST['authkey']) ? $_REQUEST['authkey'] : "";
		if(isset($exe['0']) && isset($exe['1']) && $authkey!=''){
			$data = array(
					'bid'=>$exe['0']
					,'eid'=>$exe['1']
					,'authkey'=>$authkey);
			$info = $this->appmodel->auth($data);
		}
		if(is_array($info) && count($info)>0){
			switch($format){
				case 'json':
				default:
					echo json_encode($info);
					break;
				case 'xml':
					$xml = new SimpleXMLElement('<auth/>');
					foreach($info as $k=>$v) {
						$xml->addChild($k , $v);
					}
					Header('Content-type: text/xml');
					print($xml->asXML());
					break;
			}
		}else{
			echo json_encode(array('erroe'=>'true'));
		}
	}
	
	// Get the letested call detail
	
	function getDetail($format='json'){
		//~ $_POST['bid'] = '1';
		//~ $_POST['eid'] = '1';
		$info = array();
		if(isset($_POST['bid']) 
			&& $_POST['bid']!=''
			&& isset($_POST['eid']) 
			&& $_POST['eid']!=''){
			$info = $this->appmodel->getLastCall($_POST);
			//print_r($info );
		}
		if(is_array($info) && count($info)>0){
			switch($format){
				case 'json':
				default:
					echo json_encode($info);
					break;
				case 'xml':
					$xml = new SimpleXMLElement('<info/>');
					foreach($info as $k=>$v) {
						$xml->addChild($k , $v);
					}
					Header('Content-type: text/xml');
					print($xml->asXML());
					break;
			}
		}else{
			echo json_encode(array('erroe'=>'true'));
		}
	}
	
	// Add Call
	
	function addCall($format='json'){
		
		$_POST =array(
				'bid'		=>'1',
				'gid'		=>'1',
				'source'	=>'calltrack',
				'callfrom'	=>'9739097139',
				'starttime'	=>date('Y-m-d H:i:s'),
				'endtime'	=>date('Y-m-d H:i:s'),
				'status'	=>'1',
				'filename'	=>'file.wav',
									
				);
		$out = array();
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('gid', 'Group Id', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			$out = $this->appmodel->addCallDetail($_POST);
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
		}
		echo json_encode($out);
	}
	// Update Call
	
	function updateCall($format='json'){
		
		//~ $_POST = array(
				//~ 'callid'	=> '97390971391348252483',
				//~ 'bid'		=> '1',
				//~ 'number'	=> '9739097139',
				//~ 'name'		=> 'Sundeep Misra',
				//~ 'email'		=> 'sundeep.misra@vmc.in',
				//~ 'address'	=> 'VMC Technologies'
		//~ );
		$info = array();
		if(isset($_POST['callid']) 
		   && $_POST['callid']!=''
		   && isset($_POST['bid']) 
		   && $_POST['bid']!=''){
			$info = $this->appmodel->updateCallDetail($_POST);
		}
		if(is_array($info) && count($info)>0){
			switch($format){
				case 'json':
				default:
					echo json_encode($info);
					break;
				case 'xml':
					$xml = new SimpleXMLElement('<info/>');
					foreach($info as $k=>$v) {
						$xml->addChild($k , $v);
					}
					Header('Content-type: text/xml');
					print($xml->asXML());
					break;
			}
		}else{
			echo json_encode(array('error'=>'true'));
		}
	}
	
	function getCallerids($format='json'){
		$info = array();
		$info = $this->appmodel->getCallerId();
		//print_r($info);exit;
		if(is_array($info) && count($info)>0){
			switch($format){
				case 'json':
				default:
					echo json_encode($info);
					break;
				case 'xml':
					$xml = new SimpleXMLElement('<info/>');
					foreach($info as $k=>$v) {
						$xml->addChild($k , $v);
					}
					Header('Content-type: text/xml');
					print($xml->asXML());
					break;
			}
		}else{
			echo json_encode(array('erroe'=>'true'));
		}
	}
	function Fetch_userDetail($eid,$bid,$case){
		 switch($case){
			  case 'empdetail':
						$empDetail=$this->appmodel->getEmpDetail($bid,$eid);
						return $empDetaill;
			  case 'roleDetail':
					    $empDetail=$this->appmodel->getEmpDetail($bid,$eid);
						$roleDetail=$this->appmodel->getRoledetail($empDetail->roleid,$bid);
						return $roleDetail;
		 }
	}
	
	function get_groups(){
		$_POST = $_REQUEST;
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			$out = array();
			$limit = isset($_REQUEST['limit'])?urldecode($_REQUEST['limit']):'';
			$empDetail=$this->appmodel->getEmpDetail(urldecode($_REQUEST['bid']),urldecode($_REQUEST['eid']));
			if(!empty($empDetail) && $empDetail->roleid!=0){
				if(!empty($empDetail) && $empDetail->roleid!=0){
					$bid=$_REQUEST['bid'];
					$res=array();
					$roleDetail=$this->appmodel->getRoledetail($empDetail->roleid,$empDetail->bid);
					$q='';
					$q.=($roleDetail['role']['owngroup']=='1' && $roleDetail['role']['admin']!='1') ? " AND a.eid = '".$_REQUEST['eid']."'":"";
					$sql=$this->db->query("select a.gid,a.groupname  from ".$bid."_groups a where a.status=1 $q");
					$res['']=$this->lang->line('level_select');
					foreach($sql->result_array() as $re)
					$res[$re['gid']]=$re['groupname'];
					$out['groups'] = $res;
					$out['code']= '200';
				}else{
					$out['msg'] = "Access Denied While Fetching Call Report";
					$out['code']= '201';
				}
			}else{
				$out['msg'] = "Invalid User or Access Denied While Fetching Call Report";
				$out['code']= '401';
			}
		}
			
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
			$out['code']= '201';
		}
		//echo "<pre>";print_r($out);exit;
		echo json_encode($out);
		
	}
	
	function calls(){
		//~ $_REQUEST = array(
			//~ 'bid'=>1
			//~ ,'eid'=>1
			//~ ,'limit'=>20
		//~ );
		$_POST = $_REQUEST;
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			$out = array();
			$cond = '';
			$res = array();
			$bid = $_REQUEST['bid'];
			$limit = isset($_REQUEST['limit'])?urldecode($_REQUEST['limit']):'';
			$gid = isset($_REQUEST['gid'])?urldecode($_REQUEST['gid']):'';
			if($gid!=''){
				$cond = " AND h.gid = '".$gid."'";
			}
			$empDetail=$this->appmodel->getEmpDetail(urldecode($_REQUEST['bid']),urldecode($_REQUEST['eid']));
			if(!empty($empDetail) && $empDetail->roleid!=0){
				$roleDetail=$this->appmodel->getRoledetail($empDetail->roleid,$empDetail->bid);
				if($roleDetail['modules']['6']['opt_view']!='0'){
					$call_details = $this->appmodel->getCallReport($empDetail,$empDetail->roleid,$limit,$cond);
					$out['call_details'] = $call_details;
					$q='';
					$q.=($roleDetail['role']['owngroup']=='1' && $roleDetail['role']['admin']!='1') ? " AND a.eid = '".$_REQUEST['eid']."'":"";
					$sql=$this->db->query("select a.gid,a.groupname  from ".$bid."_groups a where a.status=1 $q");
					$res['']='All';
					foreach($sql->result_array() as $re){
						$res[$re['gid']]=$re['groupname'];
					}
					$out['groups'] = $res;
					if(count($call_details)>0){
                                                $out['code']='200';
                                                $out['msg']= count($call_details).' records found.';
                                        }else{
                                                $out['code']='204';
                                                $out['msg']= 'No records found.';
                                        }
				}else{
					$out['msg'] = "Access Denied While Fetching Call Report";
					$out['code']= '201';
				}
			}else{
				$out['msg'] = "Invalid User or Access Denied While Fetching Call Report";
				$out['code']= '401';
			}
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
			$out['code']= '201';
		}
		//echo "<pre>";print_r($out);exit;
		 echo json_encode($out);
	}
	
	
	function edit_call($id=''){
		/*$_REQUEST = array(
			'bid'=>1
			,'eid'=>1
			,'id'=>'81230001991385046691'
		);*/
		$_POST = $_REQUEST;
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		$this->form_validation->set_rules('id', 'Call Id', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			$id = urldecode($_REQUEST['id']);
			$bid = urldecode($_REQUEST['bid']);
			$empDetail=$this->appmodel->getEmpDetail(urldecode($_REQUEST['bid']),urldecode($_REQUEST['eid']));
			$roleDetail = $this->appmodel->getRoledetail($empDetail->roleid,$empDetail->bid);
			if(!$roleDetail['modules']['6']['opt_add']) { 
				$out['msg'] = "Access Denied for Editing Call Report";
			}
			else{
				$fieldset = $this->form_fields(array('bid'=>$_POST['bid'],'eid'=>$_POST['eid'],'module'=>'calls'));//$this->configmodel->getFields('6');
				$itemDetail = $this->configmodel->getDetail('6',$id,'',$bid);
				if(count($itemDetail)>0){
					$itemDetail['convertaslead']=(isset($itemDetail['number']) &&  $itemDetail['number']==null) ? '0' : '1';
					$itemDetail['recorded_file']=(isset($itemDetail['filename'])&&$itemDetail['filename']!='')?base_url().'sounds/'.$itemDetail['filename']:"";
				}
				$out['fieldset'] = $fieldset;
				$out['itemDetail'] = $itemDetail;
			}
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
		}
		echo json_encode($out);
	}
	
	function update_call_track(){
		/*$_REQUEST =array(
				'bid'		=>'1',
				'eid'		=>'1',
				'callid'		=>'97390971391369202258',
				'refid'		=>'123456',
				'custom'		=>array('16'=>array('0'=>'1'),'18'=>'active','19'=>'cold','22'=>array('0'=>'2')),
				'callername'		=>'siva',
				'caller_email'		=>'sivame85@gmail.com',
				'remark'		=>'dfgfdgh',
				'calleraddress'		=>'gdfhfd',
				'callerbusiness'		=>'dfhdfh',
				'convertaslead' =>'1',
				);*/
				//print_r($_POST);exit;
		$_POST = $_REQUEST;
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		$this->form_validation->set_rules('callid', 'Call Id', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			//$id = urldecode($_REQUEST['id']);
			$bid = urldecode($_REQUEST['bid']);
			$empDetail=$this->appmodel->getEmpDetail(urldecode($_REQUEST['bid']),urldecode($_REQUEST['eid']));
			$roleDetail = $this->appmodel->getRoledetail($empDetail->roleid,$empDetail->bid);
			if(!$roleDetail['modules']['6']['opt_add']) { 
				$out['msg'] = "Access Denied for Editing Call Report";
			}
			else{
				$itemDetail = $this->configmodel->getDetail('6',$_REQUEST['callid'],'',$bid);
				if(count($itemDetail)>0){
					$res=$this->appmodel->update_caller_details($_REQUEST['callid'],$bid);
					$out['msg']= 'Call Track Details updated Successfully';
					$out['code']= '200';
				}
				else{
					$out['msg']= 'No Call Track Exists';
					$out['code']= '201';
				}
			}
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
		}
		echo json_encode($out);
	}
		
	function activerecords($id=''){
		$_POST = $_REQUEST;
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		$this->form_validation->set_rules('id', 'Call Id', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			$id = urldecode($_REQUEST['id']);
			$bid = urldecode($_REQUEST['bid']);
			$empDetail=$this->appmodel->getEmpDetail(urldecode($_REQUEST['bid']),urldecode($_REQUEST['eid']));
			$roleDetail = $this->appmodel->getRoledetail($empDetail->roleid,$empDetail->bid);
			if(!$roleDetail['modules']['6']['opt_add']) { 
				$out['msg'] = "Access Denied While retriving Call Report";
			}
			else
			{
				$out['fieldset'] = $this->configmodel->getFields('6',$bid);
				$out['itemDetail'] = $this->configmodel->getDetail('6',$id,'',$bid);
				//$out['follow_list'] = $this->reportmodel->getFollowuplist($id,$bid);
				
			}
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
		}
		//echo "<pre>";print_r($out);
		echo json_encode($out);
		
	}
	
	function empdetail($id){
		$_POST = $_REQUEST;
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('id', 'Employee Id', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			$id = urldecode($_REQUEST['id']);
			$bid = urldecode($_REQUEST['bid']);
			$out['itemlist'] = $this->appmodel->getReportlist2($id,$bid);
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
		}
		echo json_encode($out);
	}
	
	function Delete_call($id){
		$_POST = $_REQUEST;
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		$this->form_validation->set_rules('id', 'Call Id', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			$id = urldecode($_REQUEST['id']);
			$bid = urldecode($_REQUEST['bid']);
			$empDetail=$this->appmodel->getEmpDetail(urldecode($_REQUEST['bid']),urldecode($_REQUEST['eid']));
			$roleDetail = $this->appmodel->getRoledetail($empDetail->roleid,$empDetail->bid);
			if(!$roleDetail['modules']['6']['opt_delete']){ 
				$out['msg'] = "Access Denied While Deleting Call Report";
			}
			else{
				$this->appmodel->delete_call($id,$bid);
				$out['msg'] = "success";
			}
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
		}
		echo json_encode($out);

	}
	/****** Follow Up ******************************/
	function add_followup(){
		/*$_POST =array(
				'bid'		=>'1',
				'eid'		=>'1',
				'call_id'		=>'791',
				'comment'	=>'test followup',
				'alert'	=>'1',
				'module'	=>'Leads',
				'followupdate'	=>'2013-6-20 15:22:55',
				);*/
		$_POST = $_REQUEST;
		//$_REQUEST = $_POST;
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		$this->form_validation->set_rules('call_id', 'Call Id', 'required');
		$this->form_validation->set_rules('module', 'Module', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			$callid=urldecode($_REQUEST['call_id']);
			$bid=urldecode($_REQUEST['bid']);
			$eid=urldecode($_REQUEST['eid']);
			$set_array=array(
						"callid"=>urldecode($_REQUEST['call_id'])
						,"comment"=>urldecode($_REQUEST['comment'])
						,"followupdate"=>urldecode($_REQUEST['followupdate'])
						,"module"=>urldecode($_REQUEST['module'])
						,"alert"=>urldecode($_REQUEST['alert'])
						);
			$empDetail=$this->appmodel->getEmpDetail($bid,$eid);
			if(!empty($empDetail) && $empDetail->roleid!=0){
				$roleDetail=$this->appmodel->getRoledetail($empDetail->roleid,$empDetail->bid);
				if($roleDetail['modules']['6']['opt_add']!='0'){
					$res= $this->appmodel->createFollowup($bid,$eid,$set_array);
					$out['msg'] ="Success";
				}else{
					$out['msg'] = "Access Denied While creating Follow up";
				}
			}else{
				$out['msg'] = "Access Denied While creating Follow up";
			}
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
		}
		echo json_encode($out);
	}
	/**********End*********************************/
	/******List Followup************************/
	
	
	/*********End*****************************/
	
	
	function ivrs(){
		$_POST = $_REQUEST;
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			$limit = isset($_REQUEST['limit'])?urldecode($_REQUEST['limit']):'';
			$gid = isset($_REQUEST['gid'])?urldecode($_REQUEST['gid']):'';
			$cond='';
			if($gid!=''){
				$cond = " AND i.status=1 AND i.ivrsid = '".$gid."' ";
			}
			$empDetail=$this->appmodel->getEmpDetail(urldecode($_REQUEST['bid']),urldecode($_REQUEST['eid']));
			if(!empty($empDetail) && $empDetail->roleid!=0){
				$roleDetail=$this->appmodel->getRoledetail($empDetail->roleid,$empDetail->bid);
				if($roleDetail['modules']['16']['opt_view']!='0'){
					$ivrs_details = $this->appmodel->getIvrsReport($empDetail,$empDetail->roleid,$limit,$cond);
					$out['ivrs_details'] = $ivrs_details;
					$q='';
					//$q.=($roleDetail['role']['owngroup']=='1' && $roleDetail['role']['admin']!='1') ? " AND a.eid = '".$_REQUEST['eid']."'":"";
					$sql=$this->db->query("select ivrsid,title  from ivrs  where status=1 and bid='".$_REQUEST['bid']."'");
					$res['']='All';
					foreach($sql->result_array() as $re){
						$res[$re['ivrsid']]=$re['title'];
					}
					$out['groups'] = $res;
					if(count($ivrs_details)>0){
                                                $out['code']='200';
                                                $out['msg']= count($ivrs_details).' records found.';
                                        }else{
                                                $out['code']='204';
                                                $out['msg']= 'No records found.';
                                        }
				}else{
					$out['msg'] = "Access Denied While Fetching IVRS Report";
					$out['code']='201';
				}
			}else{
				$out['msg'] = "Invalid User or Access Denied While Fetching IVRS Report";
				$out['code']='401';
			}
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
			$out['code']='201';
		}
		echo json_encode($out);
	}
	
	function edit_ivrs($id=''){
		$_POST = $_REQUEST;
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		$this->form_validation->set_rules('id', 'IVRS Id', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			$id = urldecode($_REQUEST['id']);
			$bid = urldecode($_REQUEST['bid']);
			$empDetail=$this->appmodel->getEmpDetail(urldecode($_REQUEST['bid']),urldecode($_REQUEST['eid']));
			$roleDetail = $this->appmodel->getRoledetail($empDetail->roleid,$empDetail->bid);
			if(!$roleDetail['modules']['16']['opt_add']) { 
				$out['msg'] = "Access Denied for Editing IVRS Report";
			}
			else{
				$fieldset = $this->form_fields(array('bid'=>$_POST['bid'],'eid'=>$_POST['eid'],'module'=>'ivrs'));
				$itemDetail = $this->configmodel->getDetail('16',$id,'',$bid);
				$out['fieldset'] = $fieldset;
				$out['itemDetail'] = $itemDetail;
			}
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
		}
		echo json_encode($out);
	}
	
	function update_ivrs_report(){
		/*$_REQUEST =array(
				'bid'		=>'1',
				'eid'		=>'1',
				'callid'		=>'90350316601360301223',
				//'custom'		=>array('16'=>array('0'=>'1'),'18'=>'active','19'=>'cold','22'=>array('0'=>'2')),
				'name'		=>'siva',
				'email'		=>'sivame85@gmail.com',
				);*/
		$_POST = $_REQUEST;
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		$this->form_validation->set_rules('callid', 'Call Id', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			//$id = urldecode($_REQUEST['id']);
			$bid = urldecode($_REQUEST['bid']);
			$empDetail=$this->appmodel->getEmpDetail(urldecode($_REQUEST['bid']),urldecode($_REQUEST['eid']));
			$roleDetail = $this->appmodel->getRoledetail($empDetail->roleid,$empDetail->bid);
			if(!$roleDetail['modules']['16']['opt_add']) { 
				$out['msg'] = "Access Denied for Editing Call Report";
			}
			else{
				$itemDetail = $this->configmodel->getDetail('16',$_REQUEST['callid'],'',$bid);
				if(count($itemDetail)>0){
					$res=$this->appmodel->updateIvrsReport($_REQUEST['callid'],$bid);
					$out['msg']= 'IVRS Details updated Successfully';
					$out['code']= '200';
				}
				else{
					$out['msg']= 'No IVRS Exists';
					$out['code']= '201';
				}
			}
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
		}
		echo json_encode($out);
	}
	
	function pbx(){
		
		$_POST = $_REQUEST;
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			$out = array();
			$limit = isset($_REQUEST['limit'])?urldecode($_REQUEST['limit']):'';
			$gid = isset($_REQUEST['gid'])?urldecode($_REQUEST['gid']):'';
			$cond='';
			$bid = $_REQUEST['bid'];
			if($gid!=''){
				$cond = " AND p.status=1 AND p.pbxid = '".$gid."' ";
			}
			$empDetail=$this->appmodel->getEmpDetail(urldecode($_REQUEST['bid']),urldecode($_REQUEST['eid']));
			if(!empty($empDetail) && $empDetail->roleid!=0){
				$roleDetail=$this->appmodel->getRoledetail($empDetail->roleid,$empDetail->bid);
				if($roleDetail['modules']['24']['opt_view']!='0'){
					$pbx_details = $this->appmodel->getPbxReport($empDetail,$empDetail->roleid,$limit,$cond);
					$sql=$this->db->query("select a.pbxid,a.title  from ".$bid."_pbx a where a.status=1 ");
					$res['']='All';
					foreach($sql->result_array() as $re){
						$res[$re['pbxid']]=$re['title'];
					}
					$out['groups'] = $res;
					$out['pbx_details'] = $pbx_details;
					if(count($pbx_details)>0){
                                                $out['code']='200';
                                                $out['msg']= count($pbx_details).' records found.';
                                        }else{
                                                $out['code']='204';
                                                $out['msg']= 'No records found.';
                                        }
				}else{
					$out['msg'] = "Access Denied While Fetching IVRS Report";
					$out['code']='201';
				}
			}else{
				$out['msg'] = "Invalid User or Access Denied While Fetching IVRS Report";
				$out['code']='401';
			}
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
		}
		echo json_encode($out);
	}
	
	function edit_pbx($id=''){
		
		$_POST = $_REQUEST;
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			$id = urldecode($_REQUEST['id']);
			$bid = urldecode($_REQUEST['bid']);
			$empDetail=$this->appmodel->getEmpDetail(urldecode($_REQUEST['bid']),urldecode($_REQUEST['eid']));
			$roleDetail = $this->appmodel->getRoledetail($empDetail->roleid,$empDetail->bid);
			if(!$roleDetail['modules']['24']['opt_add']) { 
				$out['msg'] = "Access Denied for Editing IVRS Report";
			}
			else{
				$fieldset = $this->form_fields(array('bid'=>$_POST['bid'],'eid'=>$_POST['eid'],'module'=>'pbx'));
				$itemDetail = $this->configmodel->getDetail('24',$id,'',$bid);
				$out['fieldset'] = $fieldset;
				$out['itemDetail'] = $itemDetail;
			}
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
		}
		echo json_encode($out);
	}
	
	function update_pbx_report(){
		/*$_REQUEST =array(
				'bid'		=>'1',
				'eid'		=>'1',
				'callid'		=>'90350316601360299256',
				'custom'		=>array('17'=>array('0'=>'1')),
				'name'		=>'sivakumar',
				'email'		=>'sivame85@gmail.com',
				);*/
		$_POST = $_REQUEST;
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		$this->form_validation->set_rules('callid', 'Call Id', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			//$id = urldecode($_REQUEST['id']);
			$bid = urldecode($_REQUEST['bid']);
			$empDetail=$this->appmodel->getEmpDetail(urldecode($_REQUEST['bid']),urldecode($_REQUEST['eid']));
			$roleDetail = $this->appmodel->getRoledetail($empDetail->roleid,$empDetail->bid);
			if(!$roleDetail['modules']['24']['opt_add']) { 
				$out['msg'] = "Access Denied for Editing PBX Report";
			}
			else{
				$itemDetail = $this->configmodel->getDetail('24',$_REQUEST['callid'],'',$bid);
				if(count($itemDetail)>0){
					$res=$this->appmodel->updatePbxReport($_REQUEST['callid'],$bid);
					$out['msg']= 'PBX Details updated Successfully';
					$out['code']= '200';
				}
				else{
					$out['msg']= 'No PBX Exists';
					$out['code']= '201';
				}
			}
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
		}
		echo json_encode($out);
	}
	
	function leads(){
		
		$_POST = $_REQUEST;
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			$out = array();
			$limit = isset($_REQUEST['limit'])?urldecode($_REQUEST['limit']):'';
			$empDetail=$this->appmodel->getEmpDetail(urldecode($_REQUEST['bid']),urldecode($_REQUEST['eid']));
			if(!empty($empDetail) && $empDetail->roleid!=0){
				$roleDetail=$this->appmodel->getRoledetail($empDetail->roleid,$empDetail->bid);
				if($roleDetail['modules']['26']['opt_view']!='0'){
					$lead_details = $this->appmodel->get_leads($empDetail,$empDetail->roleid,$limit);
					$out['lead_details'] = $lead_details;
					if(count($lead_details)>0){
						$out['code']='200';
						$out['msg']= count($lead_details).' records found.';
					}else{
						$out['code']='204';
                                                $out['msg']= 'No records found.';
					}
				}else{
					$out['msg'] = "Access Denied While Fetching Leads";
					$out['code']='201';
				}
			}else{
				$out['msg'] = "Invalid User or Access Denied While Fetching Leads";
				$out['code']='401';
			}
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
		}
		echo json_encode($out);
	}
	
	function edit_lead($id=''){
		$_POST = $_REQUEST;
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		$this->form_validation->set_rules('id', 'Lead Id', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			$id = urldecode($_REQUEST['id']);
			$bid = urldecode($_REQUEST['bid']);
			$empDetail=$this->appmodel->getEmpDetail(urldecode($_REQUEST['bid']),urldecode($_REQUEST['eid']));
			$roleDetail = $this->appmodel->getRoledetail($empDetail->roleid,$empDetail->bid);
			if(!$roleDetail['modules']['26']['opt_add']) { 
				$out['msg'] = "Access Denied for Editing Leads";
			}
			else{
				$fieldset = $this->form_fields(array('bid'=>$_POST['bid'],'eid'=>$_POST['eid'],'module'=>'leads'));
				$itemDetail = $this->configmodel->getDetail('26',$id,'',$bid);
				$itemDetail['gid'] = $itemDetail['groupname'];
				$itemDetail['assignto'] = $itemDetail['assignempname'];
				$itemDetail['enteredby'] = $itemDetail['enteredempname'];
				$out['fieldset'] = $fieldset;
				$out['itemDetail'] = $itemDetail;
			}
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
		}
		echo json_encode($out);
	}
	
	function update_lead(){
		/*$_REQUEST =array(
				'bid'		=>'1',
				'eid'		=>'1',
				'leadid'		=>'792',
				//'custom'		=>array('17'=>array('0'=>'1')),
				'name'		=>'sivakumar',
				'email'		=>'sivame85@gmail.com',
				'number'		=>'7894561230',
				);*/
		$_POST = $_REQUEST;
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		$this->form_validation->set_rules('leadid', 'Lead Id', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			//$id = urldecode($_REQUEST['id']);
			$bid = urldecode($_REQUEST['bid']);
			$empDetail=$this->appmodel->getEmpDetail(urldecode($_REQUEST['bid']),urldecode($_REQUEST['eid']));
			$roleDetail = $this->appmodel->getRoledetail($empDetail->roleid,$empDetail->bid);
			if(!$roleDetail['modules']['26']['opt_add']) { 
				$out['msg'] = "Access Denied for Editing PBX Report";
			}
			else{
				$itemDetail = $this->configmodel->getDetail('26',$_REQUEST['leadid'],'',$bid);
				if(count($itemDetail)>0){
					$res=$this->appmodel->edit_lead($_REQUEST['leadid'],$bid);
					$out['msg']= 'Lead Details updated Successfully';
					$out['code']= '200';
				}
				else{
					$out['msg']= 'No Lead Exists';
					$out['code']= '201';
				}
			}
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
		}
		echo json_encode($out);
	}
	
	
	function followUps(){
		$_POST = $_REQUEST;
		//$_POST['bid']=$a;
		//$_POST['eid']=$b;
		//$_POST['limit']=$c;
		//$_REQUEST = $_POST;
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			$limit = isset($_REQUEST['limit'])?urldecode($_REQUEST['limit']):'';
			$empDetail=$this->appmodel->getEmpDetail(urldecode($_REQUEST['bid']),urldecode($_REQUEST['eid']));
			if(!empty($empDetail) && $empDetail->roleid!=0){
				$roleDetail=$this->appmodel->getRoledetail($empDetail->roleid,$empDetail->bid);
				$followups = $this->appmodel->get_followups($empDetail,$empDetail->roleid,$limit);
				if(count($followups)>0){
					$out['follow_ups'] = $followups; 
					$out['code']='200';
					$out['msg']= count($followups).' records found.';
				}else{
					$out['code']='204';
                                        $out['msg']= 'No records found.';
				}	
			}else{
				$out['msg'] = "Invalid User or Access Denied";
				$out['code']='401';
			}
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
			$out['code']='201';
		}
		echo json_encode($out);
	}
	
	function call_logs(){
		$_POST = $_REQUEST;
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			$out = array();
			$limit = isset($_REQUEST['limit'])?urldecode($_REQUEST['limit']):'';
			$empDetail=$this->appmodel->getEmpDetail(urldecode($_REQUEST['bid']),urldecode($_REQUEST['eid']));
			if(!empty($empDetail) && $empDetail->roleid!=0){
				$roleDetail=$this->appmodel->getRoledetail($empDetail->roleid,$empDetail->bid);
				/*if($roleDetail['modules']['35']['opt_view']!='0'){*/
					$call_logs = $this->appmodel->get_call_logs($empDetail,$empDetail->roleid,$limit);
					//$call_logs['callid'] = $call_logs['call_id'];
					$out['call_logs'] = $call_logs;
					if(count($call_logs)>0){
                                                $out['code']='200';
                                                $out['msg']= count($call_logs).' records found.';
                                        }else{
                                                $out['code']='204';
                                                $out['msg']= 'No records found.';
                                        }
				/*}else{
					$out['msg'] = "Access Denied While Fetching Call Logs";
					$out['code']='201';
				}*/
			}else{
				$out['msg'] = "Invalid User or Access Denied While Fetching Call Logs";
				$out['code']='401';
			}
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
		}
		echo json_encode($out);
	}
	
	
	function edit_call_log($id=''){
		$_POST = $_REQUEST;
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		$this->form_validation->set_rules('id', 'Lead Id', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			$id = urldecode($_REQUEST['id']);
			$bid = urldecode($_REQUEST['bid']);
			//$empDetail=$this->appmodel->getEmpDetail(urldecode($_REQUEST['bid']),urldecode($_REQUEST['eid']));
			//$roleDetail = $this->appmodel->getRoledetail($empDetail->roleid,$empDetail->bid);
			/*if(!$roleDetail['modules']['35']['opt_add']) { 
				$out['msg'] = "Access Denied for Editing Leads";
			}*/
			//else{
				$fields = $this->modules['call_logs']['fields'];
				foreach($fields as $skey=>$f){
					$formFields[] = array(
									'fieldname'=>$skey,
									'fieldtype'=>$f['fieldtype'],
									'type'=>$f['type'],
									'options'=>$f['options'],
									'defaultvalue'=>$f['defaultvalue'],
									'customlabel'=>$f['customlabel'],
									'is_required'=>$f['is_required'],
								);
				}
				$ff['form_fields'] = $formFields;
				$fieldset['form_fields'] = $formFields;//$this->form_fields(array('bid'=>$_POST['bid'],'eid'=>$_POST['eid'],'module'=>'call_logs'));
				$itemDetail = $this->configmodel->getDetail('35',$id,'',$bid);
				$itemDetail['recorded_file']=(isset($itemDetail['recorded_file'])&&$itemDetail['recorded_file']!='')?base_url().'sounds/'.$itemDetail['recorded_file']:"";
				$out['fieldset'] = $fieldset;
				$out['itemDetail'] = $itemDetail;
			//}
			
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
		}
		echo json_encode($out);
	}
	
	
	function update_call_log(){
		/*$_REQUEST =array(
				'bid'		=>'1',
				'eid'		=>'1',
				'callid'		=>'2147483647',
				//'custom'		=>array('17'=>array('0'=>'1')),
				'name'		=>'sivak',
				'email'		=>'sivame@gmail.com',
				'convertaslead'		=>'1',
				);*/
		$_POST = $_REQUEST;
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		$this->form_validation->set_rules('callid', 'Call Id', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			//$id = urldecode($_REQUEST['id']);
			$bid = urldecode($_REQUEST['bid']);
			$empDetail=$this->appmodel->getEmpDetail(urldecode($_REQUEST['bid']),urldecode($_REQUEST['eid']));
			$roleDetail = $this->appmodel->getRoledetail($empDetail->roleid,$empDetail->bid);
			/*if(!$roleDetail['modules']['26']['opt_add']) { 
				$out['msg'] = "Access Denied for Editing PBX Report";
			}*/
			//else{
				$itemDetail = $this->configmodel->getDetail('35',$_REQUEST['callid'],'',$bid);
				if(count($itemDetail)>0){
					$res=$this->appmodel->update_callLog($_REQUEST['callid'],$bid);
					$out['msg']= 'Call Log Details updated Successfully';
					$out['code']= '200';
				}
				else{
					$out['msg']= 'No Call Log Exists';
					$out['code']= '201';
				}
			//}
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
		}
		echo json_encode($out);
	}
	
	function form_fields($post=array())
	{
		if(count($post>0)){$_POST = $post;}
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		$this->form_validation->set_rules('module', 'Module name', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			$bid = urldecode($_POST['bid']);
			$module = urldecode($_POST['module']);
			$empDetail=$this->appmodel->getEmpDetail(urldecode($_POST['bid']),urldecode($_POST['eid']));
			$roleDetail = $this->appmodel->getRoledetail($empDetail->roleid,$empDetail->bid);
			if(!array_key_exists($module,$this->modules)) { 
				$out['msg'] = "Module Doesnot exists";
			}
			else{
				$fieldset = $this->configmodel->getFields($this->modules[$module]['id'],$bid);
				//echo "<pre>";print_r($fieldset);exit;
				$formFields = array();
				foreach($fieldset as $field){$checked = false;
					if($field['type']=='s' && $field['show']){
						foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
						if($checked){
							if(array_key_exists($field['fieldname'],$this->modules[$module]['fields'])){
								$f = $this->modules[$module]['fields'][$field['fieldname']];
								$formFields[] = array(
													'fieldname'=>$field['fieldname'],
													'fieldtype'=>$f['fieldtype'],
													'type'=>$f['type'],
													'options'=>$f['options'],
													'defaultvalue'=>$f['defaultvalue'],
													'customlabel'=>$field['customlabel']!=''?$field['customlabel']:$f['customlabel'],
													'is_required'=>$f['is_required'],
												);
							}
						}
											
					}elseif($field['type']=='c' && $field['show']){
						foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
						if($checked)$formFields[] = array(
												'fieldname'=>$field['fieldname'],
												'fieldid'=>$field['fieldid'],
												'fieldtype'=>$field['fieldtype'],
												'type'=>$field['type'],
												'options'=>$field['options'],
												'defaultvalue'=>$field['defaultvalue'],
												'customlabel'=>$field['customlabel'],
												'is_required'=>$field['is_required'],
											);
					}
				}
				$out['form_fields'] = $formFields;
			
			}
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
		}
		return $out;
	}
	
	
	function addCallLog($format='json'){
		/*$_REQUEST =array(
				'bid'		=>'1',
				'eid'		=>'1',
				'call_type'		=>'call log',
				'number'	=>'+918033282008',
				'name'	=>'sundeep',
				'email'	=>'sundeep.misra@vmc.in',
				'call_time'	=>date('Y-m-d H:i:s'),
				'duration'	=>'10',
				'status'	=>'1',
				'created_by'	=>'1',
				);
		$_POST = $_REQUEST ;*/
		$out = array();
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			$empDetail=$this->appmodel->getEmpDetail(urldecode($_REQUEST['bid']),urldecode($_REQUEST['eid']));
			if(!empty($empDetail) && $empDetail->roleid!=0){
				$_POST['created_by']=$_REQUEST['eid'];
				$this->appmodel->addCallLog($_POST);
				$out['msg'] = "Call Log have been added successfully";
				$out['code']='200';
			}else{
				$out['msg'] = "Invalid User or Access Denied";
				$out['code']='401';
			}
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
			$out['code']='201';
		}
		echo json_encode($out);
	}
	
	function edit_access(){
		/*$_REQUEST =array(
				'bid'		=>'1',
				'eid'		=>'1',
				'track_incoming'		=>'0',
				'track_outgoing'		=>'0',
				'record_conversation'		=>'1',
				);
		$_POST = $_REQUEST ;*/
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		$this->form_validation->set_rules('track_incoming', 'Track Incoming', 'required');
		$this->form_validation->set_rules('track_outgoing', 'Track Outgoing', 'required');
		$this->form_validation->set_rules('record_conversation', 'Record Conversation', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			$empDetail=$this->appmodel->getEmpDetail(urldecode($_REQUEST['bid']),urldecode($_REQUEST['eid']));
			if(!empty($empDetail) && $empDetail->roleid!=0){
				$update = $this->appmodel->update_access();
				if($update)
				{
					$adminDetail=$this->appmodel->getEmpDetail(urldecode($_REQUEST['bid']),'1');
					$track_incoming = $this->input->post('track_incoming')==0?"False":"True";
					$track_outgoing = $this->input->post('track_outgoing')==0?"False":"True";
					$record_conversation = $this->input->post('record_conversation')==0?"False":"True";
					$name=$empDetail->empname;
					$message='Tracking options on your MCube account "'.$empDetail->empemail.'" has been changed to:<br/><br/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					Track Incoming : '.$track_incoming.'<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					Track Outgoing : '.$track_outgoing .'<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					Record Conversation : '.$record_conversation.'<br/><br/>
					If you have not changed this then contact us immediately and click here to block your account for unauthorized access.<br/><br/>';
					$body=$this->emailmodel->newEmailBody($message,$name);
					$config['charset']    = 'utf-8';
					$config['newline']    = "\r\n";
					$config['mailtype'] = 'html'; // or html
					$config['validation'] = TRUE; // bool whether to validate email or not      
					$this->email->initialize($config);
					$this->email->from('<noreply@vmc.in>','Mcube');
					$this->email->to($this->input->post('email'));
					$this->email->cc($adminDetail->empemail); 
					$this->email->bcc('sivame85@gmail.com'); 
					$this->email->subject("Mcube User Track Details");
					$this->email->message($body);  
					$this->email->send();
					$out['msg']= 'User Access updated Successfully';
					$out['code']= '200';
				}
				else
				{
					$out['code']= '200';
					$out['msg']= 'No changes updated';
				}
			}else{
				$out['msg'] = "Invalid User or Access Denied";
				$out['code']='401';
			}
			
			
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
			$out['code']= '201';
		}
		echo json_encode($out);
		
			
	}
	
	function add_contacts(){
		/*$_REQUEST =array(
				'bid'		=>'1',
				'eid'		=>'1',
				'contacts' =>'{"phones":["9874561230","9874561231","9874561232"],"names":["a","b","c"],"emails":["a@a.com","NA","NA"]}',
				);
		$_POST = $_REQUEST ;*/
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		$this->form_validation->set_rules('contacts', 'Contacts', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			$empDetail=$this->appmodel->getEmpDetail(urldecode($_REQUEST['bid']),urldecode($_REQUEST['eid']));
			if(!empty($empDetail) && $empDetail->roleid!=0){
				$contacts = json_decode($_POST['contacts']);
				//print_r($contacts);exit;
				if(count($contacts)>0){
					$this->appmodel->del_contacts($_REQUEST['eid'],$_REQUEST['bid']);
					$phones = $contacts->phones;
					$names = $contacts->names;
					$emails = $contacts->emails;
					for($i=0;$i<sizeOf($phones);$i++){
						$contact = array('name'=>$names[$i],'email'=>$emails[$i],'number'=>$phones[$i]);
						$this->appmodel->add_contact($_REQUEST['eid'],$contact,$_REQUEST['bid']);
					}
					$out['msg']= 'Contacts Added Successfully.';
					$out['code']= '200';
				}
			}
			else
			{
				$out['msg']= 'Invalid User';
				$out['code']= '401';
			}
		}
		else
		{
			$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
			$validateerrors=str_replace("</p>","",$validateerrors);
			$validateerrors=str_replace("\n","",$validateerrors);
			$out['msg']=$validateerrors;
			$out['code']= '201';
		}
		echo json_encode($out);
	}
	function check_url(){
		$urls = array('http://qa1.vmc.in','https://mcube.vmc.in','http://mcube.vmc.in/calltrack','http://mcube.vmc.in');
		if(in_array($this->input->post('url'),$urls)){
			return TRUE;
		}else{
			$this->form_validation->set_message('check_url', 'Server Address Doesnot exists');
			return FALSE;
			
		}
	}
}


?>
