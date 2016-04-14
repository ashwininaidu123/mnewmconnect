<?php
class App extends Controller {
	var $data,$roleDetail;
	function App(){
		parent::controller();
		//$this->load->model('sysconfmodel');
		//$this->data = $this->sysconfmodel->init();
		$this->load->model('appmodel'); 	
		$this->load->model('configmodel'); 	
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
														'type'=>'s',
														),
											'callerbusiness'=>array(
														'modid'=>'6',
														'fieldtype'=>'textarea',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'type'=>'s',
														),
											'caller_email'=>array(
														'modid'=>'6',
														'fieldtype'=>'text',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'type'=>'s',
														),
											'remark'=>array(
													'modid'=>'6',
													'fieldtype'=>'textarea',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'type'=>'s',
													),
											'refid'=>array(
													'modid'=>'6',
													'fieldtype'=>'text',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'type'=>'s',
													),
											),
									),
						"ivrs" => array(
									"id"=>"16",
									"fields"=>array(
											'ivrstitle'=>array(
														'modid'=>'16',
														'fieldtype'=>'text',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'type'=>'s',
														),
											'callfrom'=>array(
														'modid'=>'16',
														'fieldtype'=>'text',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'type'=>'s',
														),
											'datetime'=>array(
														'modid'=>'16',
														'fieldtype'=>'datepicker',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'type'=>'s',
														),
											'endtime'=>array(
													'modid'=>'16',
													'fieldtype'=>'datepicker',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'type'=>'s',
													),
											'options'=>array(
													'modid'=>'16',
													'fieldtype'=>'text',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													),
											'employee'=>array(
													'modid'=>'16',
													'fieldtype'=>'text',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'type'=>'s',
													),
											'name'=>array(
													'modid'=>'16',
													'fieldtype'=>'text',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'type'=>'s',
													),
											'email'=>array(
													'modid'=>'16',
													'fieldtype'=>'text',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'type'=>'s',
													),
											),
									),
						"pbx" => array(
									"id"=>"24",
									"fields"=>array(
											'pbxtitle'=>array(
														'modid'=>'24',
														'fieldtype'=>'text',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'type'=>'s',
														),
											'callfrom'=>array(
														'modid'=>'24',
														'fieldtype'=>'text',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'type'=>'s',
														),
											'starttime'=>array(
														'modid'=>'24',
														'fieldtype'=>'datepicker',
														'options'=>'',
														'is_required'=>'',
														'defaultvalue'=>'',
														'type'=>'s',
														),
											'endtime'=>array(
													'modid'=>'24',
													'fieldtype'=>'datepicker',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'type'=>'s',
													),
											'pulse'=>array(
													'modid'=>'24',
													'fieldtype'=>'text',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'type'=>'s',
													),
											'name'=>array(
													'modid'=>'24',
													'fieldtype'=>'text',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'type'=>'s',
													),
											'email'=>array(
													'modid'=>'24',
													'fieldtype'=>'text',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'type'=>'s',
													),
											'extensions'=>array(
													'modid'=>'24',
													'fieldtype'=>'text',
													'options'=>'',
													'is_required'=>'',
													'defaultvalue'=>'',
													'type'=>'s',
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
	
	function checkAuth($format='json'){
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
		echo "<pre>";print_r($_POST);exit;
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
	
	function calls(){
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
					$out['code']='200';
					$out['msg']= count($call_details).' records found.';
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
	
	function edit_call($id=''){
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
	function followup(){
		$_POST = $_REQUEST;
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		$this->form_validation->set_rules('callid', 'Call Id', 'required');
		if(!$this->form_validation->run() == FALSE){
			$callid=urldecode($_REQUEST['callid']);
			$bid=urldecode($_REQUEST['bid']);
			$eid=urldecode($_REQUEST['eid']);
			$set_array=array(
						"callid"=>urldecode($_REQUEST['callid'])
						,"comment"=>urldecode($_REQUEST['comment'])
						,"followupdate"=>urldecode($_REQUEST['followupdate'])
						);
			$empDetail=$this->appmodel->getEmpDetail($bid,$eid);
			if(!empty($empDetail) && $empDetail->roleid!=0){
				$roleDetail=$this->appmodel->getRoledetail($empDetail->roleid,$empDetail->bid);
				if($roleDetail['modules']['6']['opt_add']!='0'){
				$res= $this->appmodel->createFollowup($bid,$eid,$set_array);
					if(count($res)>0){
						$out['code']='200';
						$out['msg'] ="Success";
					}else{
						$out['code']='204';
						$out['msg'] ="No record found";
					}
				}else{
					$out['msg'] = "Access Denied While creating Follow up";
				}
			}else{
				$validateerrors=str_replace("<p>","",nl2br(validation_errors()));
				$validateerrors=str_replace("</p>","",$validateerrors);
				$validateerrors=str_replace("\n","",$validateerrors);
				$out['msg']=$validateerrors;
			}
		}else{
			$out['msg'] = "Access Denied While creating Follow up";
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
			$empDetail=$this->appmodel->getEmpDetail(urldecode($_REQUEST['bid']),urldecode($_REQUEST['eid']));
			if(!empty($empDetail) && $empDetail->roleid!=0){
				$roleDetail=$this->appmodel->getRoledetail($empDetail->roleid,$empDetail->bid);
				if($roleDetail['modules']['16']['opt_view']!='0'){
					$ivrs_details = $this->appmodel->getIvrsReport($empDetail,$empDetail->roleid,$limit);
					$out['ivrs_details'] = $ivrs_details;
					$out['code']='200';
					$out['msg']= count($ivrs_details).' records found.';
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
	
	function edit_ivrs($id){
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
				$fieldset = $this->configmodel->getFields('16',$bid);
				echo "<pre>";print_r($fieldset);exit;
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
	
	
	function pbx(){
		
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
				if($roleDetail['modules']['24']['opt_view']!='0'){
					$pbx_details = $this->appmodel->getPbxReport($empDetail,$empDetail->roleid,$limit);
					$out['pbx_details'] = $pbx_details;
					$out['code']='200';
					$out['msg']= count($pbx_details).' records found.';
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
				$fieldset = $this->configmodel->getFields('24',$bid);
				echo "<pre>";print_r($fieldset);exit;
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
	
	function followUps(){
		$_POST = $_REQUEST;
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		$this->form_validation->set_rules('eid', 'Employee Id', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			$limit = isset($_REQUEST['limit'])?urldecode($_REQUEST['limit']):'';
			$empDetail=$this->appmodel->getEmpDetail(urldecode($_REQUEST['bid']),urldecode($_REQUEST['eid']));
			if(!empty($empDetail) && $empDetail->roleid!=0){
				$roleDetail=$this->appmodel->getRoledetail($empDetail->roleid,$empDetail->bid);
				$followups = $this->appmodel->get_followups($empDetail,$empDetail->roleid,$limit);
				$out['followups'] = $followups;
				$out['code']='200';
				$out['msg']= count($followups).' records found.';
				
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
													'options'=>$f['options'],
													'defaultvalue'=>$f['defaultvalue'],
													'customlabel'=>$field['customlabel'],
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
				$out = $formFields;
			
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
	
	
	function addCallLog($format='json'){
		/*$_POST =array(
				'bid'		=>'1',
				'call_type'		=>'call log',
				'number'	=>'9739097139',
				'name'	=>'sundeep',
				'email'	=>'sundeep.misra@vmc.in',
				'call_time'	=>date('Y-m-d H:i:s'),
				'duration'	=>'10',
				'status'	=>'1',
				'created_by'	=>'1',
				);*/
		$out = array();
		$this->form_validation->set_rules('bid', 'Business Id', 'required');
		if(!$this->form_validation->run() == FALSE)
		{
			$out = $this->appmodel->addCallLog($_POST);
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
}


?>
