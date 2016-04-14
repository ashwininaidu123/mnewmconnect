<?php
define('SALT','DINESH');
class Api extends Controller {
	var $data,$roleDetail;
	function Api(){
		parent::controller();
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('configmodel');
		$this->load->model('apimodel'); 	
	}
	function user_authenticate($username,$password){
		$res=$this->apimodel->authenticate($username,$password);
		return $res;
	}
	function index(){
		echo "Invalid Request";
	}
	function encrypt($text){ 
		return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, SALT, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)))); 
	} 

	function decrypt($text){ 
		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, SALT, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))); 
	}
	/* addEmp: 
	 * API: http://mcube.vmc.in/api/addEmp?username=xxxxxxx&password=xxxxx&empname=xxxx&empnumber=xxxxxx&empemail=xxxxxx&login=0
	 * 
	 * */
	 
	function addEmp(){
		$check_authenticate=$this->user_authenticate(
												urldecode($_REQUEST['username'])
											  ,urldecode($_REQUEST['password']));
		if(!empty($check_authenticate)){
			$role_detail=$this->apimodel->get_role_info($check_authenticate->bid);
			if($role_detail!=0){
				$roledetail=$this->apimodel->getRoledetail($role_detail,$check_authenticate->bid);
				if($roledetail['modules']['2']['opt_add']!=0){
					if($this->apimodel->Add_Employee($check_authenticate->bid,$role_detail)){
						$out=array("msg"=>"Employee Added successfully");
					}else{
						$out=array("msg"=>"Employee already Exists");
					}
				}else{
					$out=array("msg"=>"Access Denied While adding Employee");
				}
			}else{
				$out=array("msg"=>"Access Denied While adding Employee");
			}
		}else{
			$out=array("msg"=>"Invalid User credentials");
		}
		echo json_encode($out);
	}
	/* Edit Emp: 
	 * API: http://mcube.vmc.in/api/Edit_Emp?username=xxxxx&password=xxxxxx&empname=xxxxxx&empnumber=xxxxxx&empemail=xxxxxxxxx
	 * 
	 * */
	function Edit_Emp(){
		$check_authenticate=$this->user_authenticate(
												urldecode($_REQUEST['username'])
											  ,urldecode($_REQUEST['password']));
		if(!empty($check_authenticate)){
			$role_detail=$this->apimodel->get_role_info($check_authenticate->bid);
			if($role_detail!=0){
				$roledetail=$this->apimodel->getRoledetail($role_detail,$check_authenticate->bid);
				if($roledetail['modules']['2']['opt_add']!=0){
					$r=$this->apimodel->get_employee_id($check_authenticate->bid,$_REQUEST['empemail']);
					if($r!=""){
						$up=$this->apimodel->update_Employee($check_authenticate->bid,$r);
						$out=array("msg"=>"Employee updated Successfully");
						
					}else{
						$out=array("msg"=>"Email Id not Found in your Business");
					}				
				}else{
					$out=array("msg"=>"Access Denied While Edit Employee");
				}
			}
			else{
				$out=array("msg"=>"Access Denied While Edit Employee");
			}
		}
		else{
			$out=array("msg"=>"Invalid User credentials");
		}									  
		echo json_encode($out);
	}
	
	function editEmp(){
		$check_authenticate=$this->user_authenticate(
												urldecode($_REQUEST['username'])
											  ,urldecode($_REQUEST['password']));
		if(!empty($check_authenticate)){
			$role_detail=$this->apimodel->get_role_info($check_authenticate->bid);
			if($role_detail!=0){
				$roledetail=$this->apimodel->getRoledetail($role_detail,$check_authenticate->bid);
				if($roledetail['modules']['2']['opt_add']!=0){
					if($this->apimodel->update_Employee($check_authenticate->bid,$check_authenticate->eid)){
						$out=array("msg"=>"Employee Updated successfully");
					}
				}
				else{
					$out=array("msg"=>"Access Denied While Edit Employee");
				}
			}
			else{
				$out=array("msg"=>"Access Denied While Edit Employee");
			}
		}
		else{
			$out=array("msg"=>"Invalid User credentials");
		}									  
		echo json_encode($out);
	}
	
	function delEmp(){
		//Delete Employee
	}
	
	function getEmp(){
		$check_authenticate=$this->user_authenticate(
												urldecode($_REQUEST['username'])
											  ,urldecode($_REQUEST['password']));
		if(!empty($check_authenticate)){
			$role_detail=$this->apimodel->get_role_info($check_authenticate->bid);
			if($role_detail!=0){
				$roledetail=$this->apimodel->getRoledetail($role_detail,$check_authenticate->bid);
				if($roledetail['modules']['2']['opt_view']!=0){
					$r=$this->apimodel->get_employee_id($check_authenticate->bid,$_REQUEST['empemail']);
					if($r!=""){
						$x=$this->apimodel->getEmpinfo($r,$check_authenticate->bid);
						$out= array("msg"=>$x); 
					}else{
						$out=array("msg"=>"Email Id not Found in your Business");
					}	
				}else{
					$out=array("msg"=>"Access Denied While view Employee");
				}
			}else{
				$out=array("msg"=>"Access Denied While view Employee");
			}
		}else{
			$out=array("msg"=>"Invalid User credentials");
		}					
		echo json_encode($out);
	}
	function empList(){
		$check_authenticate=$this->user_authenticate(
												urldecode($_REQUEST['username'])
											  ,urldecode($_REQUEST['password']));
		if(!empty($check_authenticate)){
			$role_detail=$this->apimodel->get_role_info($check_authenticate->bid);
			if($role_detail!=0){
				$roledetail=$this->apimodel->getRoledetail($role_detail,$check_authenticate->bid);
				if($roledetail['modules']['2']['opt_view']!=0){
					$x=$this->apimodel->Emplist($check_authenticate->eid,$check_authenticate->bid);
					echo json_encode((array)$x); 
				}
				else{
					$out=array("msg"=>"Access Denied While Feteching Employee List");
				}
			}
			else{
				$out=array("msg"=>"Access Denied While Feteching Employee List");
			}
		}
		else{
			$out=array("msg"=>"Invalid User credentials");
		}			
		 echo json_encode($out);	
	}
	
	function createGroup(){
		$check_authenticate=$this->user_authenticate(
												urldecode($_REQUEST['username'])
											  ,urldecode($_REQUEST['password']));
		if(!empty($check_authenticate)){
			$role_detail=$this->apimodel->get_role_info($check_authenticate->bid);
			if($role_detail!=0){
				$roledetail=$this->apimodel->getRoledetail($role_detail,$check_authenticate->bid);
				if($roledetail['modules']['2']['opt_add']!=0){
					$eid=$this->apimodel->get_employee_id($check_authenticate->bid);
					if($this->input->post('landingnumber')!="0"){
						$res=$this->apimodel->landingnumber_exists($check_authenticate->bid);
						if($res!=0){
								
								$number=$this->apimodel->get_PRI($check_authenticate->bid,$eid);
								$ss=$this->apimodel->Addgroup($check_authenticate->bid,$eid,$number);
								$out=array("msg"=>"Group Added Succesfully");
						}else{
							$out=array("msg"=>"Landing Number already assigned to another group");
						}
					}else{
						$free_pri=$this->apimodel->getFreePri();
						$ss=$this->apimodel->Addgroup($check_authenticate->bid,$eid,$free_pri);
						$out=array("msg"=>"Group Added Succesfully");
					}
				}
				else{
					$out=array("msg"=>"Access Denied While Adding Group");
				}
			}
			else{
				$out=array("msg"=>"Access Denied While Adding Group");
			}
		}
		else{
			$out=array("msg"=>"Invalid User credentials");
		}	
		echo json_encode($out);
	}
	
	function delGroupEmployee(){
		$out=array();
		$check_authenticate=$this->user_authenticate(
												urldecode($_REQUEST['username'])
											  ,urldecode($_REQUEST['password']));
		if(!empty($check_authenticate)){
			$role_detail=$this->apimodel->get_role_info($check_authenticate->bid);
			if($role_detail!=0){
				$roledetail=$this->apimodel->getRoledetail($role_detail,$check_authenticate->bid);
				if($roledetail['modules']['2']['opt_delete']!=0){
						$gid=$this->apimodel->get_group_id($check_authenticate->bid);
						if($gid!=""){
								$eid=$this->apimodel->get_employee_id($check_authenticate->bid);
								if($eid!=""){
									$res=$this->apimodel->deletegroupEmployee($gid,$eid,$check_authenticate->bid);
									$out['msg']="Employee Deleted From the group";
								}else{
									$out['msg']="Requested Employee Not found in the Group";
								}
								
						}else{
							$out['msg']="You Requested Group Name Not Found";
						}
					}
				else{
					$out['msg']="Access Denied While Deleting Employee Group";
				}
			}
			else{
				$out['msg']="Access Denied While Deleting Employee Group";
			}
		}
		else{
			$out['msg']="Invalid User credentials";
		}
		echo json_encode($out);
	}
	
	function grpList(){
		
		$check_authenticate=$this->user_authenticate(
												urldecode($_REQUEST['username'])
											  ,urldecode($_REQUEST['password']));
		if(!empty($check_authenticate)){
			$role_detail=$this->apimodel->get_role_info($check_authenticate->bid);
			if($role_detail!=0){
				$roledetail=$this->apimodel->getRoledetail($role_detail,$check_authenticate->bid);
				if($roledetail['modules']['2']['opt_view']!=0){
					$x=$this->apimodel->Grouplist($check_authenticate->bid);
					$out = (array)$x; 
				}else{
					$out = array('error' => "Access Denied While Adding Group");
				}
			}else{
				$out = array('error' => "Access Denied While Adding Group");
			}
		}else{
			$out = array('error' => "Invalid User credentials");
		}	
		echo json_encode($out);
	}
	
	function addGrpEmp(){
		$out=array();
		$check_authenticate=$this->user_authenticate(
												urldecode($_REQUEST['username'])
											  ,urldecode($_REQUEST['password']));
		if(!empty($check_authenticate)){
			$role_detail=$this->apimodel->get_role_info($check_authenticate->bid);
			if($role_detail!=0){
				$roledetail=$this->apimodel->getRoledetail($role_detail,$check_authenticate->bid);
				if($roledetail['modules']['2']['opt_add']!=0){
						$gid=$this->apimodel->get_group_id($check_authenticate->bid);
						if($gid!=""){
								$eid=$this->apimodel->get_employee_id($check_authenticate->bid,$_REQUEST['empemail']);
								if($eid!=""){
									$check_employee=$this->apimodel->check_groupEmployee($eid,$check_authenticate->bid,$gid);
									if($check_employee!=0){
										$res=$this->apimodel->AddtoGroup($eid,$check_authenticate->bid,$gid);
										$out['msg']="Employee Added to Group";
									}else{
										$out['msg']="Employee Already Added to Requested Group";
									}
								}else{
									$out['msg']="Employee Email id is not found in the Employee List";
								}
								
								
						}else{
							$out['msg']= "You Requested Group Name Not Found";
						}
					}
				else{
					$out['msg']= "Access Denied While Adding Employee Group";
				}
			}
			else{
				$out['msg']= "Access Denied While Adding Employee Group";
			}
		}
		else{
			$out['msg']= "Invalid User credentials";
		}	
		echo json_encode($out);
	}
	
	function getGrpEmp(){
		$out=array();
		$check_authenticate=$this->user_authenticate(
												urldecode($_REQUEST['username'])
											  ,urldecode($_REQUEST['password']));
		if(!empty($check_authenticate)){
			$role_detail=$this->apimodel->get_role_info($check_authenticate->bid);
			if($role_detail!=0){
				$roledetail=$this->apimodel->getRoledetail($role_detail,$check_authenticate->bid);
				if($roledetail['modules']['2']['opt_view']!=0){
					$gid=$this->apimodel->get_group_id($check_authenticate->bid);
					if($gid!=""){
						$res=$this->apimodel->getGroupEmployees($gid,$check_authenticate->bid);
						echo json_encode((array)$res); 
					}else{
						$out['msg']= "No group Found ";
					}	
					
				}
				else{
					$out['msg']= "Access Denied While Fetching Employee Group";
				}
			}
			else{
				$out['msg']= "Access Denied While Fetching Employee Group";
			}
		}
		else{
			$out['msg']= "Invalid User credentials";
		}
	}
	
	function addIvrs(){
		$out=array();
		$check_authenticate=$this->user_authenticate(
												urldecode($_REQUEST['username'])
											  ,urldecode($_REQUEST['password']));
		if(!empty($check_authenticate)){
			$role_detail=$this->apimodel->get_role_info($check_authenticate->bid);
			if($role_detail!=0){
				$roledetail=$this->apimodel->getRoledetail($role_detail,$check_authenticate->bid);
				if($roledetail['modules']['4']['opt_add']!=0){
					if($this->input->post('landingnumber')!="0"){
						$res=$this->apimodel->landingnumber_exists($check_authenticate->bid);
						if($res!=0){
								$number=$this->apimodel->get_PRI($check_authenticate->bid);
						}else{
							$out['msg']= "Landing Number Not Found";
						}
					}else{
						$number=$this->apimodel->getFreePri();
						}
						$ivrs=$this->apimodel->addIvrs($check_authenticate->bid,$number);
						$out['msg']= "IVRS Added Successfully";
						
					}
				else{
					$out['msg']= "Access Denied While Adding Ivrs";
				}
			}
			else{
				$out['msg']= "Access Denied While Adding Ivrs";
			}
		}
		else{
			$out['msg']= "Invalid User credentials";
		}
	}
	
	function callReport_missedcalls($type){
		$out=array();
		$check_authenticate=$this->user_authenticate(
												urldecode($_REQUEST['username'])
											  ,urldecode($_REQUEST['password']));
		if(!empty($check_authenticate)){
			$role_detail=$this->apimodel->get_role_info($check_authenticate->bid);
			if($role_detail!=0){
				$roledetail=$this->apimodel->getRoledetail($role_detail,$check_authenticate->bid);
				if($roledetail['modules']['6']['opt_view']!=0){
					$result=$this->apimodel->getReportlist($check_authenticate->bid,$type);
					$arr=array();
					$arr1=array();
					 foreach($result['header'] as $hd){
						$arr[]=$hd;
					}
						
					$i=0;
					foreach($result['rec'] as $item){
						$j=0;
						foreach($item as $it){ 
							$j++;
							$arr1[]=$it;}
							
					 $i++;}
					echo json_encode($arr1);
										}
				else{
					$out['msg']= "Access Denied While Fetching Call Report";
				}
			}
			else{
				$out['msg']= "Access Denied While Fetching Call Report";
			}
		}
		else{
			$out['msg']= "Invalid User credentials";
		}
	}
	
	function keywordReport(){
		//Get incoming keyword lead report
	}
	
	function getNumbers(){
		
	}
	
	function calls(){
		$out = array();
		$time['start'] = isset($_REQUEST['stime']) ? date('Y-m-d H:i:s',strtotime($_REQUEST['stime'])) : date('Y-m-d 00:00:00');
		$time['end'] = isset($_REQUEST['etime']) ? date('Y-m-d H:i:s',strtotime($_REQUEST['etime'])) : date('Y-m-d h:i:s');
		$userDetail=$this->user_authenticate(urldecode($_REQUEST['username']),urldecode($_REQUEST['password']));
		if(!empty($userDetail)){
			$empDetail=$this->apimodel->getEmpDetail($userDetail->bid,$userDetail->eid);
			if($empDetail->roleid!=0){
				$roleDetail=$this->apimodel->getRoledetail($empDetail->roleid,$userDetail->bid);
				if($roleDetail['modules']['6']['opt_view']!='0'){
					$out = $this->apimodel->getCallReport($userDetail,$empDetail->roleid,$time);
				}else{
					$out['msg'] = "Access Denied While Fetching Call Report";
				}
			}else{
				$out['msg'] = "Access Denied While Fetching Call Report";
			}
		}else{
			$out['msg'] = "Invalid User credentials";
		}
		echo json_encode($out);
	}
	
	function callsxml(){
		$out = array();
		$time['start'] = isset($_REQUEST['stime']) ? date('Y-m-d H:i:s',strtotime($_REQUEST['stime'])) : date('Y-m-d 00:00:00');
		$time['end'] = isset($_REQUEST['etime']) ? date('Y-m-d H:i:s',strtotime($_REQUEST['etime'])) : date('Y-m-d H:i:s');
		$userDetail=$this->user_authenticate(urldecode($_REQUEST['username']),urldecode($_REQUEST['password']));
		
		if(!empty($userDetail)){
			$empDetail=$this->apimodel->getEmpDetail($userDetail->bid,$userDetail->eid);
			if($empDetail->roleid!=0){
				$roleDetail=$this->apimodel->getRoledetail($empDetail->roleid,$userDetail->bid);
				//echo "<pre>";print_r($roleDetail);echo "</pre>";
				if($roleDetail['modules']['6']['opt_view']!='0'){
					$out = $this->apimodel->getCallReportXML($userDetail,$empDetail->roleid,$time);
				}else{
					$out[] = "<msg>Access Denied While Fetching Call Report</msg>";
				}
			}else{
				$out[] = "<msg>Access Denied While Fetching Call Report</msg>";
			}
		}else{
			$out[] = "<msg>Invalid User credentials</msg>";
		}
		//echo $xml;
		header ("Content-Type:text/xml");
		$xml = "<calls>\n";
		$xml .= implode("",$out);
		echo $xml .= "</calls>\n";
	}
	
	//~ function c2c(){
		//~ $_REQUEST['time'] 		= isset($_REQUEST['time']) ? date('Y-m-d H:i:s',strtotime($_REQUEST['time'])) : date('Y-m-d 00:00:00');
		//~ $_REQUEST['callto'] 	= isset($_REQUEST['callto']) ? $_REQUEST['callto'] : '';
		//~ $_REQUEST['callfrom'] 	= isset($_REQUEST['callfrom']) ? $_REQUEST['callfrom'] : '';
		//~ if(!preg_match('/^[1-9][0-9]{9}$/',$_REQUEST['callto']) || !preg_match('/^[1-9][0-9]{9}$/',$_REQUEST['callfrom'])){
			//~ $out['msg'] = "Invalid Numbers";
		//~ }else{
			//~ $userDetail=$this->apimodel->authenticate(urldecode($_REQUEST['username']),urldecode($_REQUEST['password']));
			//~ if(!empty($userDetail)){
				//~ $empDetail=$this->apimodel->getEmpDetail($userDetail->bid,$userDetail->eid);
				//~ if($empDetail->roleid!=0){
					//~ $roleDetail=$this->apimodel->getRoledetail($empDetail->roleid,$userDetail->bid);
					//~ if($roleDetail['modules']['23']['opt_add']!='0'){
						//~ $emp = $this->apimodel->getEmpbyPhone($userDetail->bid,$_REQUEST['callto']);
						//~ $out = $this->apimodel->addc2c($userDetail,$emp,$_REQUEST);
					//~ }else{
						//~ $out['msg'] = "Access Denied";
					//~ }
				//~ }else{
					//~ $out['msg'] = "Access Denied";
				//~ }
			//~ }else{
				//~ $out['msg'] = "Invalid User credentials";
			//~ }
		//~ }
		//~ echo json_encode($out);
	//~ }
	
	function getMissedCallDetails(){
		$out=array();
		$userDetail=$this->apimodel->authenticate(urldecode($_REQUEST['username']),urldecode($_REQUEST['password']));
		if(!empty($userDetail)){
			$empDetail=$this->apimodel->getEmpDetail($userDetail->bid,$userDetail->eid);
			if($empDetail->roleid!=0){
				$roleDetail=$this->apimodel->getRoledetail($empDetail->roleid,$userDetail->bid);
				if($roleDetail['modules']['25']['opt_add']!='0'){
					$res=$this->apimodel->check_landingnumber($_REQUEST['landing_number']);
					if($res!=""){
						$out=$this->apimodel->get_reports_missedcalls($res,$userDetail->bid,$userDetail->eid);
					}else{
						$out['msg'] = "Invalid Landing Number";
					}
				}else{
					$out['msg'] = "Access Denied";
				}
			}else{
				$out['msg'] = "Access Denied";
			}
		}else{
			$out['msg'] = "Invalid User credentials";
		}
		echo json_encode($out);
	}
	   
	function poll_results(){
		$out=array();
		$userDetail=$this->apimodel->authenticate(urldecode($_REQUEST['username']),urldecode($_REQUEST['password']));
		if(!empty($userDetail)){
			$empDetail=$this->apimodel->getEmpDetail($userDetail->bid,$userDetail->eid);
			if($empDetail->roleid!=0){
				$roleDetail=$this->apimodel->getRoledetail($empDetail->roleid,$userDetail->bid);
				if($roleDetail['modules']['24']['opt_add']!='0'){
					$out=$this->apimodel->get_polls($userDetail->bid);
				}else{
					$out['msg'] = "Access Denied";
				}
			}else{
				$out['msg'] = "Access Denied";
			}
		}else{
			$out['msg'] = "Invalid User credentials";
		}
		echo json_encode($out);
	}
	
	function clickcall(){
		//~ echo "<pre>";
		//~ print_r($_SERVER);
		//~ exit;
		$out=array();
		$groupInfo = (isset($_REQUEST['group'])) ? base64_decode($_REQUEST['group']) : '';
		$d = explode('_',$groupInfo);
		if(isset($d['0']) && isset($d['1']) && preg_match('/^[7-9][0-9]{9}$/',substr($_REQUEST['number'],-10,10))){
			$data = array(
					'bid'=>$d['0']
					,'gid'=>$d['1']
					,'number'=>substr($_REQUEST['number'],-10,10)
					,'name'=>(isset($_REQUEST['name']) ? $_REQUEST['name'] : '')
					,'email'=>(isset($_REQUEST['email']) ? $_REQUEST['email'] : '')
					,'email'=>(isset($_REQUEST['email']) ? $_REQUEST['email'] : '')
					,'retry'=>(isset($_REQUEST['retry']) ? $_REQUEST['retry'] : '0')
			);
			$res=$this->apimodel->clickcallAdd($data);
			$out['msg']=$res['msg'];
		}else{
			$out['msg'] = "Invalid Data";
		}
		echo json_encode($out);
	}
	/*
	 http://mcube.vmc.in/api/clickcall?group=MV85&number=8884976982 
	 */
	 
	 /* Sales Track Leads */
	 function leads_api(){
		$fp = fopen("leadslog.txt","a");
		fwrite($fp,"[".date('Y-m-d H:i:s')."]".serialize($data)."\n");
		fclose($fp);
		$data = $_REQUEST;
		$userDetail=$this->apimodel->authenticate(urldecode($data['username']),urldecode($data['password']));
		if(!empty($userDetail)){
			if((isset($data['number']) && $data['number'] !='') || (isset($data['email']) && $data['email']!='')){
				$out=array();
					$empDetail=$this->apimodel->getEmpDetail($userDetail->bid,$userDetail->eid);
					if($empDetail->roleid!=0){
						$roleDetail=$this->apimodel->getRoledetail($empDetail->roleid,$userDetail->bid);
						if($roleDetail['modules']['26']['opt_add']!='0'){
							 $data['bid'] = $userDetail->bid;
							 $data['enteredby'] = $userDetail->eid;
							 $data['convertedby'] = $userDetail->eid;
							 $data['lead_status'] = 2;
							 $data['source'] = isset($data['source']) ? $data['source'] : "API";
							 $out = $this->apimodel->addProspect($data);
						}else{
							$out['msg'] = "Access Denied";
						}
					}else{
						$out['msg'] = "Access Denied";
					}
			}else{
				$out['msg'] = "Invalid Data";
			}
		}else{
				$out['msg'] = "Invalid User credentials";
		}
		echo json_encode($out);
	}
	function prospects_api(){
		$data = $_REQUEST;
		$fp = fopen("leadslog.txt","a");
		fwrite($fp,"[".date('Y-m-d H:i:s')."]".serialize($data)."\n");
		$userDetail=$this->apimodel->authenticate(urldecode($data['username']),urldecode($data['password']));
		if(!empty($userDetail)){
			if((isset($data['number']) && $data['number'] !='') || (isset($data['email']) && $data['email']!='')){
				$out=array();
					$empDetail=$this->apimodel->getEmpDetail($userDetail->bid,$userDetail->eid);
					if($empDetail->roleid!=0){
						$roleDetail=$this->apimodel->getRoledetail($empDetail->roleid,$userDetail->bid);
						if($roleDetail['modules']['46']['opt_add']!='0'){
							 $data['bid'] = $userDetail->bid;
							 $data['enteredby'] = $userDetail->eid;
							 $data['convertedby'] = $userDetail->eid;
							 $data['lead_status'] = 1;
							 $data['source'] = isset($data['source']) ? $data['source'] : "API";
							 $out = $this->apimodel->addProspect($data);
						}else{
							$out['msg'] = "Access Denied";
						}
					}else{
						$out['msg'] = "Access Denied";
					}
			}else{
				$out['msg'] = "Invalid Data";
			}
		}else{
				$out['msg'] = "Invalid User credentials";
		}
		fwrite($fp,"[".date('Y-m-d H:i:s')."]".serialize($out)."\n");
		fclose($fp);
		echo json_encode($out);
	}
	/*
	 http://mcube.vmc.in/api/leads_api/
	 */
	 
	 function act($num,$enum,$msg){
		 $out = array();
		 if((isset($num) && $num !='' && preg_match('/^[7-9][0-9]{9}$/',substr($num,-10,10)))){
			$landing_number=$this->apimodel->landingDetails($num);
			if(!empty($landing_number)){
				$bid = $landing_number->bid;
				$umsg = explode(',',$msg);
				$activity_details = $this->apimodel->get_api_activity($bid,$umsg[0]);
				if(count($activity_details)>0){
					$empDetail=$this->apimodel->getEmpDetail_bynumber($bid,$enum,$activity_details->acid,$landing_number->associateid);
					if(count($empDetail)>0){
						$fields = $this->apimodel->get_activity_fields($bid,$activity_details->acid,$activity_details->agid);
						if(count($fields)>0){
							if(sizeOf($fields) == (sizeOf($umsg)-1)){
								$aid = $this->apimodel->add_activity_report($bid,$empDetail->eid,$activity_details->acid,$activity_details->agid);
								foreach($fields as $ikey=>$field){
									
									$act_data = array(
											'dataid'=>$aid,
											'fieldid'=>$field->fieldid,
											'agid'=>$activity_details->agid,
											'actid'=>$activity_details->acid,
											'value'=>$umsg[$ikey+1],
											);
									$this->apimodel->add_activity_values($bid,$act_data);
									$out['msg'] = "Success..!"; 
								}
							}
						else{
							$out['msg']="Insufficient parameters..";
						}
					}else{
							$out['msg']="No Fields exists";
						}
					}else{
						$out['msg']="No Employee Found";	
					}
				}
				else{
					$out['msg']="No Keyword exists";
				}
				//print_r($activity_details);exit;
			}else{
				$out['msg']="No Landing Number exists";
			}
		 }else{
			$out['msg'] = "Invalid Data"; 
		 }
		 echo json_encode($out);
	 }

	function outboundcall(){
		$out = array();
		$err = array();
		$err = $this->validate_outbound($_REQUEST);
		if(count($err)==0 )
		{
		//$userDetail=$this->user_authenticate(urldecode($_REQUEST['username']),urldecode($_REQUEST['password']));
			$userDetail=$this->apimodel->check_apikey(urldecode($_REQUEST['apikey']));
			if(!empty($userDetail)){
				//$empDetail=$this->apimodel->getEmpDetail($userDetail->bid,$userDetail->eid);
				$bid = $userDetail->bid;
				$executive_detail = $this->apimodel->getExecDetail_bynumber($bid,$_REQUEST['exenumber']);
				$eid = 0;
				$dnd = 0;
				if(count($executive_detail)>0){
					$eid = $executive_detail->eid;
				}
				if($eid==0)
				{
					$chk_dnd = (array)$this->check_dnd($_REQUEST['exenumber']);
					if(count($chk_dnd)>0){
						if($chk_dnd['dnd']!='0')$dnd = 1;
					}
				}
				if(!$dnd)
				{
					$refid = isset($_REQUEST['refid']) ? urldecode($_REQUEST['refid']) : "";
					$url = isset($_REQUEST['url']) ? urldecode($_REQUEST['url']) : "";
					$outbound_data = array(
								'exenumber'=>urldecode($_REQUEST['exenumber']),
								'custnumber'=>urldecode($_REQUEST['custnumber']),
								'dataid'=>urldecode($_REQUEST['refid']),
								'eid'=>$eid,
								'bid'=>$bid,
								'modid'=>0,
								'refid'=>$refid,
								'callback_url'=>$url
								);
					$this->apimodel->add_outbound_values($bid,$outbound_data);
					$out['msg'] = "success"; 
				}else{
					$out['msg'] = "Executive Number is under NDNC";
				}
			}else{
				$out['msg'] = "Invalid Api key";
			}
		}
		else
		{
			$out['msg'] = $err;
		}
		echo json_encode($out);
	}
	
	function validate_outbound($req=array()){
		$allowedFields = array(
			'exenumber'=>'Executive Number',
			'custnumber'=>'Customer Number',
			'url'=>'URL',
		);
		$fields_types = array(
			'exenumber'=>'mobile',
			'custnumber'=>'mobile',
			'url'=>'url',
		);
		 
		// Loop through the $_POST array, which comes from the form...
		$error = array();
		foreach($req AS $key => $value)
		{
			// first need to make sure this is an allowed field
			if(array_key_exists($key, $allowedFields))
			{
				if (empty($value)&& $key!='url'){ 
					$error[] = 'Please Enter Value for '.$allowedFields[$key];
				}else{
					if($fields_types[$key]=='mobile'){
						$mobile = $value;
						if(!preg_match('/^[0-9]{10}$/', $mobile)){
							$error[] = $allowedFields[$key].' is invalid  ';
						}
						else if($key=='custnumber'){
							$dnd = (array)$this->check_dnd($mobile);
							if(count($dnd)>0){
								$dnd['dnd']!='0'?$error[] = $allowedFields[$key].' is under NDNC ':'';
							}else{
								$error[] = 'Error While Calling DND Registry';
							}
							
						}
					}
					else if($fields_types[$key]=='url' && $value!=''){
						$url = $value;
						if(!preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url)){
							$error[] = $allowedFields[$key].' is invalid  ';
						}
						
					}
					
				}  
			}
		}
		return $error;
	}
	
	
	function check_dnd($mobile){
		$dnd=array();
		$curl_handle=curl_init();
		$url = "http://180.179.200.180/filter.php?num=".$mobile;
		curl_setopt($curl_handle,CURLOPT_URL,$url);
		curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
		curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
		$buffer = curl_exec($curl_handle);
		curl_close($curl_handle);
		if (!empty($buffer))
		{
			 $dnd = json_decode($buffer);
		}
		return $dnd;
	}
	
	//~ function groupSetup()
	 //~ {
		 //~ $userDetail=$this->apimodel->authenticate(urldecode($_REQUEST['username']),urldecode($_REQUEST['password']));
		  //~ if(!empty($userDetail)){
				 //~ $email=(isset($_REQUEST['empemail']) && $_REQUEST['empemail']!="")?urldecode($_REQUEST['empemail']):'';
				//~ $Check_number_exists=(array)$this->apimodel->empnumber_exits(urldecode($_REQUEST['empnumber']),$email,$userDetail->bid);
				//~ if(!empty($Check_number_exists)){
					//~ $c_lno=(array)$this->apimodel->landingnumber_exists($userDetail->bid);
					//~ if(!empty($c_lno)){
						//~ $c_g=$this->apimodel->group_set($c_lno['number'],$userDetail->bid,$Check_number_exists['eid']);
							//~ $out['msg'] = "Group Created Successfully";
						//~ 
					//~ }else{
							//~ $out['msg'] = "Landing Number not available";
					//~ }
				//~ }else{
					//~ $eid=$this->apimodel->emp_add(urldecode($_REQUEST['empnumber']),urldecode($_REQUEST['empname']),urldecode($_REQUEST['empemail']),$userDetail->bid);
					//~ $c_lno=(array)$this->apimodel->landingnumber_exists($userDetail->bid);
					//~ if(!empty($c_lno)){
						//~ $c_g=$this->apimodel->group_set($c_lno['number'],$userDetail->bid,$Check_number_exists['eid']);
						//~ $out['msg'] = "Group Created Successfully";
					//~ }else{
							//~ $out['msg'] = "Landing Number not available";
					//~ }
					//~ 
				//~ }
			//~ }else{
					//~ $out['msg'] = "Invalid User credentials";
			//~ }
		 //~ 
		   //~ echo json_encode($out);
		 //~ 
	 //~ }
	 function groupSetup(){
		 $statement=array("101"=>" INVALID_LANDING_KEY","102"=>"LANDING_NUMBER_IN_USE","103"=>"LANDING_KEY_MISMATCH ");
		  $userDetail=$this->apimodel->authenticate(urldecode($_REQUEST['username']),urldecode($_REQUEST['password']));
		  if(!empty($userDetail)){
				if(isset($_REQUEST['landingkey']) && $_REQUEST['landingkey']!=''){
					list($code,$res)=$this->apimodel->verifyNumber($userDetail->bid,urldecode($_REQUEST['landingkey']),urldecode($_REQUEST['landingnumber']));
					if($code=="100"){
						$Check_number_exists=(array)$this->apimodel->empnumber_exits(urldecode($_REQUEST['listingnumber']),'',$userDetail->bid);
						if(!empty($Check_number_exists)){
							$c_g=$this->apimodel->group_set($res['number'],$userDetail->bid,$Check_number_exists['eid']);
							$out['msg'] = "SUCCESS";
							
						}else{
							list($name,$email)=$this->gen_emp($userDetail->bid);
							$eid=$this->apimodel->emp_add(urldecode($_REQUEST['listingnumber']),$name,$email,$userDetail->bid);
							$c_g=$this->apimodel->group_set($res['number'],$userDetail->bid,$eid);
							$out['msg'] = "SUCCESS";
						}
					}else{
						$out['msg']=$statement[$code];
					}
				}else{
					$out['msg'] = "MISSING_LANDING_KEY";
				}
			}else{
					$out['msg'] = "INVALID_USER_ID";
			}
		   echo json_encode($out);
	}
	function FreeNumber(){
		 $statement=array("101"=>" INVALID_LANDING_KEY","102"=>"LANDING_NUMBER_IN_USE","103"=>"LANDING_KEY_MISMATCH ");
		 $userDetail=$this->apimodel->authenticate(urldecode($_REQUEST['username']),urldecode($_REQUEST['password']));
		  if(!empty($userDetail)){
				if(isset($_REQUEST['landingkey']) && $_REQUEST['landingkey']!=''){
					list($code,$res)=$this->apimodel->verifyNumber1($userDetail->bid,urldecode($_REQUEST['landingkey']),urldecode($_REQUEST['landingnumber']));
					if($code=="100"){
						 $change_status=$this->apimodel->freeLnumber($res['number'],$userDetail->bid);
						  if($change_status!=0){
							$out['msg'] = "LANDING_NUMBER_FREE";
						 }else{
							 $out['msg'] = "LANDING_NUMBER_FREE";
						 }
					}else{
						$out['msg']=$statement[$code];
					}
					
				}else{
					$out['msg'] = "MISSING_LANDING_KEY";
				}
		 }else{
					$out['msg'] = "INVALID_USER_ID";
			}
		echo json_encode($out);
		
	}
	function Numberscript(){
		$number=array(
				'39466595'	=>	'7569037736',
				'39466267'	=>	'9026769688'
			);

		foreach($number as $n=>$v){
			//echo $n."==>".$v;
			$sql = "SELECT count(*) as cnt FROM prinumbers WHERE pri='".$n."' OR landingnumber='".$v."'";
			$rst = $this->db->query($sql)->row()->cnt;
			if($rst=='0'){
				$number=$this->db->query("SELECT COALESCE(MAX(`number`),0)+1 as id FROM prinumbers")->row()->id;
				$this->db->set('bid', '1'); 
				$this->db->set('pri', $n); 
				$this->db->set('landingnumber',$v);
				$this->db->set('landing_key', $this->gen_landingkey()); 
				$this->db->set('package_id', '1'); 
				$this->db->set('number',$number);
				$this->db->set('climit','1000');
				$this->db->set('flimit','0');
				$this->db->set('assigndate',date('Y-m-d H:i:s'));
				$this->db->set('rental','0');
				$this->db->set('rpi','1.0');
				$this->db->set('ntype','0');
				$this->db->set('svdate',date('Y-m-d H:i:s'));
				$this->db->set('ownership','vmc');
				$this->db->set('payment_term','yearly');
				$this->db->set('sms_limit','0');
				$this->db->set('parallel_limit','0');
				$this->db->insert('prinumbers');
				
				$this->db->set('bid', '1'); 
				$this->db->set('package_id','1'); 
				$this->db->set('activated_date',date('Y-m-d h:i:s'));
				$this->db->set('status','1');
				$this->db->set('rental','0');
				$this->db->set('climit','1000');
				$this->db->set('activatedby','1');
				$this->db->insert('package_activate');
				
				$this->db->query("UPDATE landingnumbers SET status='1',module_id='1' WHERE number='".$v."'");
				
				$s=$this->db->query("SELECT * FROM package_feature where package_id=1 and feature_id!=0 ");
				if($s->num_rows()>0){
					foreach($s->result_array() as $rs){
							$this->db->query("REPLACE INTO  business_packageaddons set bid='1',package_id='1',feature_id='".$rs['feature_id']."',startdate='".date('Y-m-d h:i:s')."',number='".$number."'");
					}
				}
				
			}
		
		}
		echo "Number Configured";
	}


	function gen_emp($bid){
		$name="99acres_".$this->api_emailrand();
		$email = $name."@vm.com";
		$name="99acres_".$this->api_emailrand();
		while(!$this->apimodel->check_apisecret($bid,$email)){
			$name="99acres_".$this->api_emailrand();
			$email = $name."@vm.com";
		}
		return array($name,$email);
		
	}
	function api_emailrand(){
		$apisecret=str_pad(mt_rand(0, 9999), 3, '0', STR_PAD_LEFT);
		return $apisecret;
	}
	function numberfree($lno,$bid){
		$sql=$this->db->query("SELECT number from prinumbers where landingnumber='".$lno."'")->row();
		$res=$this->apimodel->freeLnumber($sql->number,$bid);
		echo json_encode($res);
	}
	function gen_landingkey(){
		$landingkey = $this->key_rand();
		while(!$this->check_landingkey($landingkey)){
			$landingkey = $this->key_rand();
		}
		return $landingkey ;
		
	}
	function key_rand(){
		//$apisecret = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
		$apisecret = md5(uniqid(rand(), true));
		return $apisecret;
	}
	function check_landingkey($key){
		$res=array();
		$sql=$this->db->query("SELECT number FROM prinumbers where landing_key='".$key."'");
		if($sql->num_rows>0)
		{
			return false;
		}
		return true;
	}
	function update_landingNumber($lno,$bid){
		$key=$this->gen_landingkey();
		$this->db->query("update prinumbers set landing_key='".$key."' where landingnumber='".$lno."' and bid='".$bid."'");
		echo "1";
	}

	function callapi(){
		header("Access-Control-Allow-Origin: *");
		echo "hi-dfhdf";
	}

	function UserAuthenticate(){
		header("Access-Control-Allow-Origin: *");
		$userDetail=$this->apimodel->check_apikey(urldecode($_REQUEST['authKey']));
		$exp=explode(",",$userDetail->allow_ips);
		if(in_array($_REQUEST['hname'],$exp)){
			echo (!empty($userDetail))?"e":"n";
		}else{
			echo "n";
		}
		
	}

	function callMe(){
		$userDetail=$this->apimodel->check_apikey(urldecode($_REQUEST['client_id']));
		$data=array();$formFields=array();
		$data=$this->data;
		$data['form'] = array(
					'open'=>form_open_multipart(current_url()
								,array('name'=>'connectcall','class'=>'form','id'=>'connectcall','method'=>'post')
								,array('bid'=>$userDetail->bid)
								),
					'fields'=>$formFields,
					'business_name'=>$userDetail->businessname,
					'close'=>form_close(),
					
					
				);
		$this->load->view('connect_call',$data);
	}
	function click2connect(){
		$post=$_POST;
		$res=$this->apimodel->ConnectCall($post);
		echo $res;
	}
	//~ https://mcube.vmc.in/api/ivrsxml?username=xxxxxxx&password=xxxxx&stime=2014-01-01 00:00:00&etime=2014-01-17 00:00:00
	function ivrsxml(){
		$out = array();
		$time['start'] = isset($_REQUEST['stime']) ? date('Y-m-d H:i:s',strtotime($_REQUEST['stime'])) : date('Y-m-d 00:00:00');
		$time['end'] = isset($_REQUEST['etime']) ? date('Y-m-d H:i:s',strtotime($_REQUEST['etime'])) : date('Y-m-d H:i:s');
		$userDetail=$this->user_authenticate(urldecode($_REQUEST['username']),urldecode($_REQUEST['password']));
		
		if(!empty($userDetail)){
			$empDetail=$this->apimodel->getEmpDetail($userDetail->bid,$userDetail->eid);
			if($empDetail->roleid!=0){
				$roleDetail=$this->apimodel->getRoledetail($empDetail->roleid,$userDetail->bid);
				//echo "<pre>";print_r($roleDetail);echo "</pre>";
				if($roleDetail['modules']['16']['opt_view']!='0'){
					$out = $this->apimodel->getIVRSReportXML($userDetail,$empDetail->roleid,$time);
				}else{
					$out[] = "<msg>Access Denied While Fetching Ivrs Report</msg>";
				}
			}else{
				$out[] = "<msg>Access Denied While Fetching Ivrs Report</msg>";
			}
		}else{
			$out[] = "<msg>Invalid User credentials</msg>";
		}
		//echo $xml;
		header ("Content-Type:text/xml");
		$xml = "<calls>\n";
		$xml .= implode("",$out);
		echo $xml .= "</calls>\n";
	}
	function support_api(){
		$data = $_REQUEST;
		$userDetail=$this->apimodel->authenticate(urldecode($data['username']),urldecode($data['password']));
		if(!empty($userDetail)){
			if((isset($data['number']) && $data['number'] !='') || (isset($data['email']) && $data['email']!='')){
				$out=array();
					$empDetail=$this->apimodel->getEmpDetail($userDetail->bid,$userDetail->eid);
					if($empDetail->roleid!=0){
						$roleDetail=$this->apimodel->getRoledetail($empDetail->roleid,$userDetail->bid);
						if($roleDetail['modules']['40']['opt_add']!='0'){
							 $data['bid'] = $userDetail->bid;
							 $data['enteredby'] = $userDetail->eid;
							 $data['convertedby'] = $userDetail->eid;
							 $data['tkt_status'] = 1;
							 $data['source'] = isset($data['source']) ? $data['source'] : "API";
							 $out = $this->apimodel->addSupTicket($data);
						}else{
							$out['msg'] = "Access Denied";
						}
					}else{
						$out['msg'] = "Access Denied";
					}
			}else{
				$out['msg'] = "Invalid Data";
			}
		}else{
				$out['msg'] = "Invalid User credentials";
		}
		echo json_encode($out);
	}
	
	/***************************************************************************************************/
	function getSMS(){
		//print_r($_REQUEST);
		$this->apimodel->smsLog($_REQUEST);
		if(!preg_match('/^[7-9][0-9]{9}$/',substr($_REQUEST['from'],-10,10))) exit;
		//echo $_REQUEST['time'].'='.date('Y-m-d H:i:s',strtotime($_REQUEST['time']))."<br>";
		$priDetail = $this->apimodel->getPRI(substr($_REQUEST['to'],-10,10));
		//print_r($priDetail);
		if($priDetail['status']=='1' && $priDetail['type']=='0' && $priDetail['associateid']>'0'){
			$data = $_REQUEST;
			$data['bid'] = $priDetail['bid'];
			$data['gid'] = $priDetail['associateid'];
			$data['number'] = $priDetail['number'];
			$this->apimodel->smsAssign($data);
		}
	}
	
	/***************************************************************************************************/
	
}

?>
