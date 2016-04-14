<?php
class Masteradmin extends controller
{
	var $data;
	function Masteradmin(){
		parent::controller();
		if(!$this->session->userdata('adminlogged_in') && $this->uri->segment('2')!="index")redirect('/Masteradmin/index');
		$this->load->model('masteradmin_model','mastermodel');
		$this->load->model('cronjobmodel','CM');
		$this->load->model('configmodel','Config');
		$this->data = $this->mastermodel->init();
		$this->load->model('commonmodel');
		$this->load->model('sysconfmodel','SM');
		//echo "<pre>";
		//print_r($this->session->all_userdata());
	}
	public function __destruct() {
		$this->db->close();
	}
	function index(){
		if($this->input->post('submit')){
				$this->load->helper('url');
				$this->load->library('validation');
				$rules['login_username']= "required|min_length[4]|max_length[100]|Email";
				$rules['login_password']= "required|min_length[4]|max_length[32]";		
				$rules['validator']			= "required|callback_check_captcha";		
				$this->validation->set_rules($rules);
				$fields['login_username'] = 'Username';
				$fields['login_password'] = 'Password';
				$this->validation->set_fields($fields);
						
				if ($this->validation->run() == false) {
					
					//If you are using OBSession you can uncomment these lines
					$flashdata = array('msgt' => 'error', 'msg' => 'Invalid Username/Password');
					$this->session->set_flashdata($flashdata);
					
					redirect('/Masteradmin/index');
				} 
				else {
					if($this->simplelogin->masterlogin($this->input->post('login_username'), $this->input->post('login_password'))=='1'){
						redirect('/Masteradmin/Landingpage');
					}
					else{		
						if($this->session->userdata('flash:new:msg')!=""){
							redirect('Masteradmin/index');
						}else{
							$flashdata = array('msgt' => 'error', 'msg' => 'Invalid Username/Password');
							$this->session->set_flashdata($flashdata);
							redirect('Masteradmin/index');	
						}
						
					}
					
				}
		}
		$this->simplelogin->logout();
		$this->data['html']['title'] .= " | ".$this->lang->line('Master_login');
		$this->load->view('siteheader1',$this->data);
		$this->load->view('masterlogin',$this->data);
	}
	function check_captcha($str){
		if($str!=$_SESSION['security_code']){
		$this->form_validation->set_message('check_captcha', 'The '.$str.' is not valid security code');
			return FALSE;
		}else{
			return true;
		}
	}
	function Landingpage(){
		$data=array(
					'lastcalls'=>$this->mastermodel->LastCalls()
					,'demousers'=>$this->mastermodel->DemoUsers('1')
					,'Newusers'=>$this->mastermodel->DemoUsers('0')
					,'log'=>$this->mastermodel->log()
					,'black'=>$this->mastermodel->BlackListnumbers()
					,'unassigned'=>$this->mastermodel->Unassigned_numbers()
					,'callcount'=>$this->mastermodel->getCreditlist()
					,'smscredit'=>$this->mastermodel->getsmsCreditlist()
				
		);
		$this->mastermodel->viewLayout('masterlanding',$data);
	}
	function forgetpass(){
		$data = array(
			'title' 		=> "Virtual Communications solution: Hosted PBX, Virtual PBX, call tracking, IVRS.",
			'keywords'		=> "Hosted IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Toll free phone numbers, Application Voice integration, Voice Portal,  Lead Tracking, Call Qualification, Missed Calls",
			'description'	=> "MCube is a Hosted Virtual Communication solution, This contains CallTrack, Virtual PBX, Hosted IVRS and Hosted Messaging solutions (Voice and Text)."
		);
		$this->load->view('siteheader',$data);
		$this->load->view('forgetpass');
		//~ $this->load->view('footer');
	}
	function AdminLog(){
		$data['module']['title']="Admin Log";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '20';
		$header = array('#','Username','Business Name','Action','Date & time','Ip Address');
		$data['itemlist']['header'] = $header;
		$emp_list=$this->mastermodel->AdminLogInfo($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;
		foreach ($emp_list['data'] as $item){
			$rec[] = array($i,$item['username'],($item['businessname']!="")?$item['businessname']:'Admin',$item['action'],$item['date_time'],$item['Ipaddress']);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/AdminLog/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->mastermodel->data['html']['title'] .= " | Admin Log";
		$data['links']='';
		$links = array();
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search"class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$data['links']= $links;	
		$formFields = array();
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">Business Name :</label>',
				  'field'=>form_input(array(
									  'name'        => 'businessname',
										'id'          => 'businessname',
										'class'       => 'form-control',
										'value'       => $this->session->userdata('businessname'))));
						array_push($formFields,$cf);
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">UserName : </label>',
				  'field'=>form_input(array(
									  'name'        => 'uname',
										'id'          => 'uname',
										'class'       => 'form-control',
										'value'       => $this->session->userdata('uname'))));
						array_push($formFields,$cf);
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">From : </label>',
				  'field'=>form_input(array(
									  'name'        => 'fdate',
										'id'          => 'fdate',
										'class'          => 'datepicker form-control',
										'value'       => $this->session->userdata('fdate'))));
						array_push($formFields,$cf);
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">To : </label>',
				  'field'=>form_input(array(
									  'name'        => 'tdate',
										'id'          => 'tdate',
										'class'          => 'datepicker form-control',
										'value'       => ($this->session->userdata('tdate')!="")?$this->session->userdata('tdate'):date('Y-m-d'))));
						array_push($formFields,$cf);
						
		$data['form'] = array(
							'open'=>form_open_multipart('Masteradmin/AdminLog/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'adv_search'=>array(),
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->mastermodel->viewLayout('list_view',$data);	
	}
function add_product($id=''){
		if($this->input->post('update_system')){
			if($id!=""){
				$res=$this->mastermodel->update_product($id);
				if($res)
				{
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', $this->lang->line('updatesuccessmsg'));
					redirect('Masteradmin/manage_product');
				}
			}else{
				$res=$this->mastermodel->add_product();
				if($res)
				{
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', $this->lang->line('successmsg'));
					redirect('Masteradmin/add_product');
				}
			}
		}
		
		$data=array('product'=>$this->mastermodel->get_product($id));
		$this->mastermodel->viewLayout('addproduct',$data);
	}
	function manage_product(){
		//print_r($this->mastermodel->get_allproducts());
			$data=array('products'=>$this->mastermodel->get_allproducts());
			$this->mastermodel->viewLayout('manageproduct',$data);
	}
	function logout(){
		//Logout
		$this->simplelogin->logout();
		redirect('/Masteradmin');
	}
	function change_productstatus($id){
		$this->mastermodel->change_productstatus($id);
		echo "1";
		
	}
	function Delete_product($id){
		$this->mastermodel->delete_product($id);
		echo "1";
	}
	function product_config(){
		$this->data['html']['business']=$this->mastermodel->get_businessusers();
		$this->load->view('business_process',$this->data);
	}
	function product_select(){
		$this->data['html']['packages']= $this->commonmodel->get_packages();
		
		$this->data['id']=$this->input->post('bid');
		$this->mastermodel->viewLayout('product_process',$this->data);
	}
	function senderIdstatus($id){
		$this->mastermodel->changesenderIdstatus($id);
		$flashdata = array('msgt' => 'success', 'msg' => 'Sender Id status updated Successfully');
		$this->session->set_flashdata($flashdata);
		redirect('Masteradmin/SenderIdList');
		
	}
	
	function numberConfig(){
		$access=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Number',0);
		if(!$access)redirect('Masteradmin/access_denied');
		if($this->input->post('submit')){
			//$siminfo = ($_POST['simholder'] != '') ? $_POST['simholder'] : (($_POST['tempnumber'] != '') ? $_POST['tempnumber'] : '');
			$res=$this->mastermodel->numberConfig();
			if($res!=0){				
				$flashdata = array('msgt' => 'success', 'msg' => 'landingNumber Added Successfully');
				$this->session->set_flashdata($flashdata);
				redirect('Masteradmin/numberConfig');
			}else{
				$flashdata = array('msgt' => 'error', 'msg' => 'landingNumber Already exists');
				$this->session->set_flashdata($flashdata);
				redirect('Masteradmin/numberConfig');
			}
		}
	
		$data=array('businessUsers'=>$this->mastermodel->get_businessusers(),
		            'modules'=>$this->mastermodel->get_allModules(),
		            'packages'=>$this->mastermodel->get_allpackages(),
					 'alldons'=>$this->mastermodel->get_allfeatures(),	
					'landingnumber'=>$this->mastermodel->getLandingNumber(),	
					'action1'=>'Masteradmin/AddLandingNumber',
					 'action'=>'Masteradmin/numberConfig',
					 'popaction'=>'Masteradmin/AssignMobileNumberPopup'	
					);
					
		$this->mastermodel->viewLayout('number_config',$data);
	}
	function AddNumber_Landing(){
		if($this->input->post('submit')){
			$res=$this->mastermodel->numberConfig();
			if($res!=0){				
				$flashdata = array('msgt' => 'success', 'msg' => 'landingNumber Added Successfully');
				$this->session->set_flashdata($flashdata);
			redirect('Masteradmin/numberConfig');
			}else{
				$flashdata = array('msgt' => 'error', 'msg' => 'landingNumber Already exists');
				$this->session->set_flashdata($flashdata);
				redirect('Masteradmin/numberConfig');
			}
		}
		$data=array('businessUsers'=>$this->mastermodel->get_businessusers(),
					'landingnumber'=>$this->mastermodel->getLandingNumber()
					);
		$this->load->view('number_config',$data);
	}
	function AssignMobileNumber($number=''){
		$access1=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Number',0);
		if(!$access1) redirect('Masteradmin/access_denied');
		if($this->input->post('submit')){
			if($number!=''){
				$res=$this->mastermodel->MobilenumberConfig($number);
				$flashdata = array('msgt' => 'success', 'msg' => 'landingNumber Updated Successfully');
						$this->session->set_flashdata($flashdata);
					redirect('Masteradmin/ManageunassignedNumbers');
			}else{	
					$res=$this->mastermodel->MobilenumberConfig();
					if($res){				
						$flashdata = array('msgt' => 'success', 'msg' => 'landingNumber Added Successfully');
						$this->session->set_flashdata($flashdata);
					redirect('Masteradmin/AssignMobileNumber');
					}else{
						$flashdata = array('msgt' => 'error', 'msg' => 'landingNumber Already exists');
						$this->session->set_flashdata($flashdata);
						redirect('Masteradmin/AssignMobileNumber');
					}
				 }	
		}
		$data=array('arr'=>array(),'id'=>'','provider'=>$this->mastermodel->Provider(),'dropdown'=>$this->mastermodel->PriList_Auto(),'add'=>'1','action'=>'Masteradmin/AssignMobileNumber/');
	
		$this->mastermodel->viewLayout('landingnumber',$data);
	}
	function AssignMobileNumberPopup($number=''){
	
		$data=array('arr'=>array(),'id'=>'','file' => "system/application/js/group.js.php",
					'dropdown'=>$this->mastermodel->PriList_Auto(),
					 	
		);
		$this->load->view('landingnumber1',$data);
	}
	function EditUnassignedNumber($number){
		if($this->input->post('submit')){
			$res=$this->mastermodel->update_unassign_number($number);
			$flashdata = array('msgt' => 'success', 'msg' => 'landingNumber Updated Successfully');
			$this->session->set_flashdata($flashdata);
			redirect('Masteradmin/ManageunassignedNumbers');
		}
		$data=array('arr'=>$this->mastermodel->getUnassingedInfo($number),
					 'id'=>$number,'add'=>'','action'=>'Masteradmin/EditUnassignedNumber/','dropdown'=>$this->mastermodel->PriList_Auto($number));
		$this->load->view('landingnumber1',$data);
	}
	function SenderIdList(){
		$data['module']['title'] ="Sender Id";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array($this->lang->line('level_id')
						,$this->lang->line('level_businessname')
						,$this->lang->line('level_code')
						,$this->lang->line('level_Date')
						,$this->lang->line('level_status')
						,$this->lang->line('level_Action')
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->mastermodel->getSenderIdList($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			$rec[] = array(
				$item['snid']
				,$item['businessname']
				,$item['senderid']
				,$item['datetime']
				,$item['status1']                                                                  
				,(($item['status']=="0")?'<a href="Masteradmin/senderIdstatus/'.$item['snid'].'"> <span id="'.$item['snid'].'" class="fa fa-lock" title="Enable"></span></a>':'<a href="Masteradmin/senderIdstatus/'.$item['snid'].'">  <span id="'.$item['snid'].'" class="fa fa-unlock" title="Disable"></span></a>')
			);
		}

		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		
		//$data['itemlist'] = $this->groupmodel->getgrouplist($bid,$ofset,$limit);
		
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/Businesslist/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		//$data['addlinks']="group/add_group";		
		$data['paging'] = $this->pagination->create_links();
		$this->mastermodel->data['html']['title'] .= " | Manage PriNumbers";
		$data['links'] = '';
		$formFields1 = array();
		$cf=array('label'=>'<label for="groupname">'.$this->lang->line('level_groupname').' : </label>',
				  'field'=>form_input(array(
									  'name'        => 'groupname',
										'id'          => 'groupname',
										'value'       => $this->session->userdata('groupname'))));
						array_push($formFields1,$cf);
						
		$this->mastermodel->data['links'] = '<a href="group/add_group"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>';
		//$fieldset = $this->configmodel->getFields('3');
		$formFields = array();
		$formFields[] = array(
				'label'=>'<label for="f">Business Name : </label>',
				'field'=>form_input(array(
						'name'      => 'businessname',
						'id'        => 'businessname',
						'value'     => $this->session->userdata('businessname')
						))
						);
		$formFields[] = array(
				'label'=>'<label for="f">sender Id: </label>',
				'field'=>form_input(array(
						'name'      => 'senderid',
						'id'        => 'senderid',
						'value'     => $this->session->userdata('senderid')
						))
						);
		
			
		$data['form'] = array(
			'open'=>form_open_multipart('Masteradmin/SenderIdList/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		$this->mastermodel->viewLayout('list_view',$data);
	}
	function AddLandingNumber(){
		$res=$this->mastermodel->MobilenumberConfig($this->input->post('landingnumber'));
		if($res!=""){
			echo $this->mastermodel->getLandingNumber1();
		}else{
			echo "Error";
		}
	}
	function addPrinumber(){
		redirect("Masteradmin/numberConfig");

		if($this->input->post('submit'))
		{
			$res=$this->mastermodel->addPrinumber();
			$flashdata = array('msgt' => 'success', 'msg' => 'PriNumber Added Successfully');
			$this->session->set_flashdata($flashdata);
			
			redirect('Masteradmin/addPrinumber');
		}
		$data=array();
		$this->mastermodel->viewLayout('addPrinumber',$data);

	}
	function managePriList($page=''){
	$access1=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Number',4);
	if(!$access1)redirect('Masteradmin/access_denied');
	$edit_opt=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Number',1);
	$delete_opt=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Number',2);
	$enable_s=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Number',3);
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('#'
						,$this->lang->line('level_Prinumbers')
						,$this->lang->line('level_landingnumber')
						,$this->lang->line('level_businessname')
						,'Type'
						,'Package'
						,'Modules'
						,'Addons'
						,'Ownership'
						,'Rental'
						,'Credit Limit'
						,'Free Limit'
						,'Used'
						,'Activation Date'
						
						,$this->lang->line('level_Action')
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->mastermodel->managePriList($ofset,$limit);
		$data['module']['title'] ="Manage Pri Numbers [".$emp_list['count']."]";
		$rec = array();
		if(count($emp_list['data'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;$status='';
		foreach ($emp_list['data'] as $item){
			($item['landingnumber']!=0)?$cls=$item['pri']." Assigned to group,If You want delete ":$cls="Are you sure to delete ".$item['number'];
			$status=($item['status']!=0)?'<span class="fa fa-lock" title="Enable"></span>':'<span class="fa fa-unlock" title="Disable"></span>';
			$title=($item['status']!=0)?" Are You sure to disable this number ".$item['landingnumber'] :" Are You sure to enable this number ".$item['landingnumber'];
			$edit=(!$edit_opt)?'':'<a href="'.(($item['type']!="Demo")?site_url('Masteradmin/editpri/'.$item['number']):'javascript:void(0)').'" class="'.(($item['type']!="Demo")?'':'').'"><span title="Edit" class="fa fa-edit"></span>
				  </a>';
			$edit.=(!$delete_opt)?'':'<a href="'.site_url('Masteradmin/delete_pri/'.$item['number']).'" class="confirm" id="'.$item['number'].'" title="'.$cls.'">
						<span title="Delete" class="glyphicon glyphicon-trash"></span>
				  </a>';  
			$edit.=	(!$edit_opt)?'':'<a href="'.(($item['type']!="Demo")?site_url('Masteradmin/reset_used/'.$item['bid'].'/'.	$item['number']):'javascript:void(0)').'"> <i class="fa fa-refresh" title="reset"></i>
				  </a>';
			$edit.= (!$enable_s)?'':'<a href="'.site_url('Masteradmin/cNumberstatus/'.$item['number'].'/'.$item['landingnumber']).'"  class="confirm" title="'.$title.'"/>'.$status.'</a>'; 
			$greeting_file = $this->mastermodel->chkGreetings($item['number'],$item['service'],$item['bid']);
			$file = ($greeting_file !='' && file_exists('sounds/'.$greeting_file))
					?'<a target="_blank" href="'.site_url('sounds/'.$greeting_file).'"><span title="Sound" class="fa fa-volume-up"></span></a>'
					:'<span class="glyphicon glyphicon-volume-off"></span> ';
			$rec[] = array(
				$i,
				$item['pri']
				,$item['landingnumber']
				,$item['businessname']
				,$item['type']
				,$item['packagename']
				,$this->mastermodel->Module_name($this->mastermodel->landingDetails($item['landingnumber']))
				,$this->get_landing_addons($item['bid'],$item['package_id'],$item['number'])
				,$item['ownership']
				,$item['rental']
				,$item['climit']
				,$item['flimit']
				,$item['used']
				,$item['svdate']
				,$edit.'<a href="'.(($item['type']!="Demo")?site_url('Masteradmin/Pack_history/'.$item['bid'].'/'.	$item['number']):'javascript:void(0)').'" class="'.(($item['type']!="Demo")?'btn-danger':'').'" data-toggle="modal" data-target="#modal-responsive">
						<i class="fa fa-list-alt" title="History"></i>
				  </a>'.$file
			);
			$i++;
		}
		
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/managePriList/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->mastermodel->data['html']['title'] .= " | Manage PriNumbers";
		
		$formFields1 = array();
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">'.$this->lang->line('level_groupname').' : </label>',
				  'field'=>form_input(array(
									  'name'        => 'groupname',
									  	'class'     => 'form-control',
										'id'          => 'groupname',
										'value'       => $this->session->userdata('groupname'))));
						array_push($formFields1,$cf);
						
		//~ $this->mastermodel->data['links'] = '<a href="group/add_group"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>';
		$formFields = array();
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">PriNumber : </label>',
				'field'=>form_input(array(
						'name'      => 'prinumber',
						'id'        => 'prinumber',
						'class'     => 'form-control',
						'value'     => $this->session->userdata('prinumber')
						))
						);
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Landing Number : </label>',
				'field'=>form_input(array(
						'name'      => 'landing_number',
						'class'     => 'form-control',
						'id'        => 'landing_number',
						'value'     => $this->session->userdata('landing_number')
						))
						);
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Business Name : </label>',
				'field'=>form_input(array(
						'name'      => 'businessname',
						'class'     => 'form-control',
						'id'        => 'businessname',
						'value'     => $this->session->userdata('businessname')
						))
						);
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Package Name : </label>',
				'field'=>form_input(array(
						'name'      => 'package',
						'class'     => 'form-control',
						'id'        => 'package',
						'value'     => $this->session->userdata('package')
						))
						);
		$droparray=array(""=>"select","0"=>"Regular","1"=>"Demo");				
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Type : </label>',
				'field'=>form_dropdown('btype',$droparray,'','class=form-control')
						);
		$formFields[] = array(
				'label'=>'<label  class="col-sm-4 text-right" for="f">Used by Percent : </label>',
				'field'=>form_input(array(
						'name'      => 'used',
						'id'        => 'used',
					     'class'     => 'form-control',
						'value'     => ''
						))
						);	
	    $links = array();
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search"class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="Masteradmin/numberConfig"><span title="Add Number" class="glyphicon glyphicon-plus-sign">&nbsp;Add Number</span></a></li>';
		$data['links']= $links;
		$data['form'] = array(
							'open'=>form_open_multipart('Masteradmin/managePriList/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'adv_search'=>array(),
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->mastermodel->viewLayout('list_view',$data);
	}
	function delete_unassigned($number){
		$r=$this->mastermodel->Delete_UnassignedNumbers($number);
		$flashdata = array('msgt' => 'success', 'msg' => 'Deleted Successfully');
			$this->session->set_flashdata($flashdata);
			redirect('Masteradmin/ManageunassignedNumbers');
		
	}
	function Pack_history($bid,$number){
		$data['module']['title'] ="History of number";
		$header = array('#'
						,$this->lang->line('level_Prinumbers')
						,$this->lang->line('level_landingnumber')
						,'Package'
						,'Modules'
						,'Addons'
						,'Rental'
						,'Credit Limit'
						,'Used'
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->mastermodel->PackageHistory($bid,$number);
		$rec = array();
		if(count($emp_list['data'])>0)
		$i=1;
		foreach ($emp_list['data'] as $item){
			$rec[] = array(
				$i,
				$item['pri']
				,$item['landingnumber']
				,$item['packagename']
				,$this->mastermodel->Module_name($this->mastermodel->landingDetails($item['landingnumber']))
				,$this->get_landing_addons_history($item['bid'],$item['package_id'],$item['number'])
				,$item['rental']
				,$item['creditlimit']
				,$item['used']
				
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		
		//$data['itemlist'] = $this->groupmodel->getgrouplist($bid,$ofset,$limit);
		$data['nosearch'] = true;
		
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/managePriList/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>''
						,'uri_segment'=>3					
				));
		//$data['addlinks']="group/add_group";		
		$data['paging'] = $this->pagination->create_links();
		//$data['paging'] =  pagination($limit, $page, $start, $total_pages, $targetpage);
		$this->mastermodel->data['html']['title'] .= " | Manage PriNumbers";
		$data['links']='';
		$formFields1 = array();
		
		$this->mastermodel->data['links'] = '';
		$formFields = array();
		$data['form'] = array(
							'open'=>form_open_multipart('Masteradmin/managePriList/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'adv_search'=>array(),
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->load->view('popupListView',$data);
		
	}
	function ManageunassignedNumbers($page=''){
		$access1=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Number',4);
		if(!$access1) redirect('Masteradmin/access_denied');
		
		$del_opt=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Number',2);
		$edit_opt=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Number',1);
		
		$data['module']['title'] ="Manage UnAssinged Numbers";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('#'
						,$this->lang->line('level_Prinumbers')
						,$this->lang->line('level_landingnumber')
						,"Region"
						,$this->lang->line('level_Action')
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->mastermodel->manageUnassignedNumbers($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;
		foreach ($emp_list['data'] as $item){
			$edit=(!$del_opt)?'':'<a href="'.site_url('Masteradmin/delete_unassigned/'.$item['number']).'" class="confirm" id="'.$item['number'].'" title="">
						<span title="Delete" class="glyphicon glyphicon-trash"></span>
				  </a>';
			$edit.=	(!$edit_opt)?'':'<a href="'.site_url('Masteradmin/EditUnassignedNumber/'.$item['number']).'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive">
						<span title="Edit" class="fa fa-edit"></span>
				  </a>';
			$rec[] = array(
				$i,
				$item['pri']
				,$item['number']
				,$item['region']
				,$edit
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$data['module']['title'] .= '['.$emp_list['count'].']';
		//$data['itemlist'] = $this->groupmodel->getgrouplist($bid,$ofset,$limit);
		
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/ManageunassignedNumbers/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		//$data['addlinks']="group/add_group";		
		$data['paging'] = $this->pagination->create_links();
		//$data['paging'] =  pagination($limit, $page, $start, $total_pages, $targetpage);
		$this->mastermodel->data['html']['title'] .= " | Manage UnAssinged Numbers";
		//$data['links']='<a href="Masteradmin/AssignMobileNumber/"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>';
	    $links = array();
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search"class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="Masteradmin/AssignMobileNumber/"><span title="Add Number" class="glyphicon glyphicon-plus-sign">&nbsp;Add Number</span></a></li>';
		$data['links']= $links;	
		$formFields1 = array();
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">'.$this->lang->line('level_groupname').' : </label>',
				  'field'=>form_input(array(
									  'name'        => 'groupname',
										'id'          => 'groupname',
										'class'     => 'form-control',
										'value'       => $this->session->userdata('groupname'))));
						array_push($formFields1,$cf);
						
		$this->mastermodel->data['links'] = '<a href="group/add_group"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>';
		//$fieldset = $this->configmodel->getFields('3');
		$formFields = array();
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">PriNumber : </label>',
				'field'=>form_input(array(
						'name'      => 'prinumber',
						'id'        => 'prinumber',
						'class'     => 'form-control',
						'value'     => $this->session->userdata('prinumber')
						))
						);
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Landing Number : </label>',
				'field'=>form_input(array(
						'name'      => 'landing_number',
						'id'        => 'landing_number',
						'class'     => 'form-control',
						'value'     => $this->session->userdata('landing_number')
						))
						);
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Region : </label>',
				'field'=>form_input(array(
						'name'      => 'region',
						'id'        => 'region',
						'class'     => 'form-control',
						'value'     => $this->session->userdata('region')
						))
						);
		
			
		$data['form'] = array(
							'open'=>form_open_multipart('Masteradmin/ManageunassignedNumbers/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'close'=>form_close(),
							'adv_search'=>array(),
							'title'=>$this->lang->line('level_search')
							);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->mastermodel->viewLayout('list_view',$data);
		
	}
	function editpri($pri){
		$access=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Number',1);
		if(!$access)redirect('Masteradmin/access_denied');
		if($this->input->post('submit')){
			$res=$this->mastermodel->EditNumberCon($pri);
			if($res){
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg', "Landing number updated successfully");
				redirect('Masteradmin/managePriList');
			}else{
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', "Landing number is already in use");
				redirect('Masteradmin/managePriList');
			 }
		}
		$c=$this->mastermodel->getPridetails($pri);
		//echo "<pre>"; print_r($c);exit;
		$data=array('businessUsers'=>$this->mastermodel->get_businessusers(),
					'selectedlist'=>$this->mastermodel->getPridetails($pri),
					'modules'=>$this->mastermodel->get_allModules(),
					'packages'=>$this->mastermodel->get_allpackages(),
					'landing_details'=>$this->mastermodel->landingDetails($c->landingnumber),
					 'baddons'=>$this->mastermodel->get_baddons($c->bid,$c->package_id,$c->number),	
					 'alldons'=>$this->mastermodel->get_allfeatures(),	
					'action'=>'Masteradmin/editpri/',
					'package'=>$c->package_id,
					'show'=>'1',
					'prlist'=>$this->mastermodel->PriList_Auto(),
					'obid'=>$c->bid,
					'mid'=>$c->type
					);
		$this->mastermodel->viewLayout('number_config1',$data);
		
		//$this->load->view('number_config1',$data);
	}
	function creditConfig(){
		$access1=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Creditassign',0);
		if(!$access1) redirect('Masteradmin/access_denied');
		if($this->input->post('submit'))
		{
			$res=$this->mastermodel->creditConfig();
			$flashdata = array('msgt' => 'success', 'msg' => 'Credit added Successfully');
			$this->session->set_flashdata($flashdata);
			redirect('Masteradmin/creditConfig');
		}
		$data=array('businessUsers'=>$this->mastermodel->get_businessusers(),
					'credit'=>'Call Credit',
					'action'=>'Masteradmin/creditConfig'
					
					);
		$this->mastermodel->viewLayout('credit_config',$data);
	}
	function smscreditConfig(){
		$access1=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Creditassign',0);
		if(!$access1) redirect('Masteradmin/access_denied');
		if($this->input->post('submit'))
		{
			$res=$this->mastermodel->SMScreditConfig();
			$flashdata = array('msgt' => 'success', 'msg' => 'SMSCredit added Successfully');
			$this->session->set_flashdata($flashdata);
			redirect('Masteradmin/smscreditConfig');
		}
		$data=array('businessUsers'=>$this->mastermodel->get_businessusers(),
						'credit'=>'SMS Credit',
					'action'=>'Masteradmin/smscreditConfig'
					
					);
		$this->mastermodel->viewLayout('credit_config',$data);
	}
	function leadscredit($bid=''){
		$access1=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Creditassign',0);
		if(!$access1) redirect('Masteradmin/access_denied');
		if($this->input->post('submit')){
			$res=$this->mastermodel->leadscredit();
			$flashdata = array('msgt' => 'success', 'msg' => 'Lead Type added Successfully');
			$this->session->set_flashdata($flashdata);
			redirect('Masteradmin/leadsversions');
		}
		$data=array('businessUsers'=>$this->mastermodel->get_businessusers(),
					'leadtype'=>$this->mastermodel->get_leadtypes('type'),
					'leaddesign'=>$this->mastermodel->get_leaddesigns(),
					'busdetails'=>$this->mastermodel->getbusleaddetails($bid),
					'action'=>'Masteradmin/leadscredit'
					);
		$this->mastermodel->viewLayout('leads_credit',$data);
	}
	function creditlist(){
		$access1=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Creditassign',4);
		if(!$access1) redirect('Masteradmin/access_denied');
		$data['module']['title'] ="Credit List";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('#'
						,$this->lang->line('level_businessname')
						,'Credit Limit'
						,$this->lang->line('level_notes')
						,$this->lang->line('level_date_time')
						,"Credited By "
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->mastermodel->getCreditlist($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			$rec[] = array(
				$item['id']
				,$item['businessname']
				,$item['credit']
				,wordwrap($item['note'], 150, "<br />\n")
				,$item['assigndate']
				,$item['name']
			);
		}

		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/creditlist/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$links = array();
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search"class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="Masteradmin/creditConfig"><span title="Add Number" class="glyphicon glyphicon-plus-sign">&nbsp;Add Number</span></a></li>';
		$data['links']= $links;	
		
		$formFields1 = array();
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">'.$this->lang->line('level_groupname').' : </label>',
				  'field'=>form_input(array(
									  'name'        => 'groupname',
									  'class'     => 'form-control',
										'id'          => 'groupname',
										'value'       => $this->session->userdata('groupname'))));
						array_push($formFields1,$cf);
						
		$formFields = array();
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Business Name : </label>',
				'field'=>form_input(array(
						'name'      => 'businessname',
						'id'        => 'businessname',
						'class'     => 'form-control',
						'value'     => $this->session->userdata('businessname')
						))
						);
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Credit Range From : </label>',
				'field'=>form_input(array(
						'name'      => 'from',
						'id'        => 'from',
						'class'     => 'form-control',
						'value'     => $this->session->userdata('from')
						))
						);
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">To : </label>',
				'field'=>form_input(array(
						'name'      => 'to',
						'class'     => 'form-control',
						'id'        => 'to',
						'value'     => $this->session->userdata('to')
						))
						);
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Date From : </label>',
				'field'=>form_input(array(
						'name'      => 'dfrom',
						'id'        => 'dfrom',
						'value'     => $this->session->userdata('dfrom'),
						'class'		=>'datepicker form-control',
						))
						);
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Date To : </label>',
				'field'=>form_input(array(
						'name'      => 'dto',
						'id'        => 'dto',
						'value'     => ($this->session->userdata('dto')!="")?$this->session->userdata('dto'):date('Y-m-d'),
						'class'		=>'datepicker form-control',
						))
						);
			
		$data['form'] = array(
			'open'=>form_open_multipart('Masteradmin/creditlist/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->mastermodel->viewLayout('list_view',$data);
		
		
	}
	function smsCredilist(){
		$access1=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Creditassign',4);
		if(!$access1) redirect('Masteradmin/access_denied');
		$data['module']['title'] ="SMSCredit List";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('#'
						,$this->lang->line('level_businessname')
						,'Credit Limit'
						,$this->lang->line('level_notes')
						,$this->lang->line('level_date_time')
						,"Credited By "
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->mastermodel->getsmsCreditlist($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			$rec[] = array(
				$item['crid']
				,$item['businessname']
				,$item['credit']
				,wordwrap($item['remark'], 50, "<br />\n")
				,$item['datetime']
				,$item['name']
				
			);
		}

		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/smsCredilist/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->mastermodel->data['html']['title'] .= " | Manage PriNumbers";
		$data['links']='<a href="Masteradmin/creditConfig"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>';
        $links = array();
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search"class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="Masteradmin/creditConfig"><span title="Add Number" class="glyphicon glyphicon-plus-sign">&nbsp;Add Number</span></a></li>';
		$data['links']= $links;	
		
		$formFields1 = array();
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">'.$this->lang->line('level_groupname').' : </label>',
				  'field'=>form_input(array(
									  'name'        => 'groupname',
										'id'          => 'groupname',
										'class'     => 'form-control',
										'value'       => $this->session->userdata('groupname'))));
						array_push($formFields1,$cf);
		$formFields = array();
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Business Name : </label>',
				'field'=>form_input(array(
						'name'      => 'businessname',
						'id'        => 'businessname',
						'class'     => 'form-control',
						'value'     => $this->session->userdata('businessname')
						))
						);
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Credit Range From : </label>',
				'field'=>form_input(array(
						'name'      => 'from',
						'id'        => 'from',
						'class'     => 'form-control',
						'value'     => $this->session->userdata('from')
						))
						);
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">To : </label>',
				'field'=>form_input(array(
						'name'      => 'to',
						'class'     => 'form-control',
						'id'        => 'to',
						'value'     => $this->session->userdata('to')
						))
						);
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Date From : </label>',
				'field'=>form_input(array(
						'name'      => 'dfrom',
						'id'        => 'dfrom',
						'value'     => $this->session->userdata('dfrom'),
						'class'		=>'datepicker form-control',
						))
						);
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Date To : </label>',
				'field'=>form_input(array(
						'name'      => 'dto',
						'id'        => 'dto',
						'value'     => ($this->session->userdata('dto')!="")?$this->session->userdata('dto'):date('Y-m-d'),
						'class'		=>'datepicker form-control',
						))
						);
			
		$data['form'] = array(
			'open'=>form_open_multipart('Masteradmin/smsCredilist/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->mastermodel->viewLayout('list_view',$data);
	}
	function leadsversions(){
		$access1=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Creditassign',4);
		if(!$access1) redirect('Masteradmin/access_denied');
		$data['module']['title'] ="Lead Versions";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('#'
						,$this->lang->line('level_businessname')
						,'Lead Type'
						,'Lead View'
						,'Actions'
						);
		$data['itemlist']['header'] = $header;
		$lead_blist = $this->mastermodel->getleadversions($ofset,$limit);
		$rec = array();
		foreach ($lead_blist['data'] as $item){
			$rec[] = array(
				$item['bid']
				,$item['businessname']
				,$item['type']
				,$item['design']
				,"<a href='Masteradmin/leadscredit/".$item['bid']."'><span title='Edit' class='fa fa-edit'></span></a>"
			);
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $lead_blist['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/leadsversions/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$data['links']='<a href="Masteradmin/leadscredit"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>';
		$links = array();
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search"class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="Masteradmin/leadscredit"><span title="Add Number" class="glyphicon glyphicon-plus-sign">&nbsp;Add Number</span></a></li>';
		$data['links']= $links;	
		
		$formFields1 = array();
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">'.$this->lang->line('level_groupname').' : </label>',
				  'field'=>form_input(array(
									  'name'        => 'groupname',
										'id'          => 'groupname',
										'class'     => 'form-control',
										'value'       => $this->session->userdata('groupname'))));
						array_push($formFields1,$cf);
						
		$formFields = array();
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Business Name : </label>',
				'field'=>form_input(array(
						'name'      => 'businessname',
						'class'     => 'form-control',
						'id'        => 'businessname',
						'value'     => $this->session->userdata('businessname')
						))
						);
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Credit Range From : </label>',
				'field'=>form_input(array(
						'name'      => 'from',
						'id'        => 'from',
						'class'     => 'form-control',
						'value'     => $this->session->userdata('from')
						))
						);
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">To : </label>',
				'field'=>form_input(array(
						'name'      => 'to',
						'class'     => 'form-control',
						'id'        => 'to',
						'value'     => $this->session->userdata('to')
						))
						);
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Date From : </label>',
				'field'=>form_input(array(
						'name'      => 'dfrom',
						'id'        => 'dfrom',
						'value'     => $this->session->userdata('dfrom'),
						'class'		=>'datepicker form-control',
						))
						);
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Date To : </label>',
				'field'=>form_input(array(
						'name'      => 'dto',
						'id'        => 'dto',
						'value'     => ($this->session->userdata('dto')!="")?$this->session->userdata('dto'):date('Y-m-d'),
						'class'		=>'datepicker form-control',
						))
						);
			
		$data['form'] = array(
			'open'=>form_open_multipart('Masteradmin/leadscredit/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->mastermodel->viewLayout('list_view',$data);
		
		
	}
	function dndfilter($bid){
		$res=$this->mastermodel->dnd_filter($bid);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', "DND Status Updated successfully");
		redirect($_SERVER['HTTP_REFERER']);
		
		
	}
	function fsetup($bid){
		$res=$this->mastermodel->fsetup($bid);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', "Followup SMS Status Updated successfully");
		redirect($_SERVER['HTTP_REFERER']);
	}
	

	
	function Businesslist(){
		$access=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Business',4);
		if(!$access) redirect('Masteradmin/access_denied');
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('#'
						,$this->lang->line('level_businessname')
						,$this->lang->line('level_contactname')
						,$this->lang->line('level_contactemail')
						,$this->lang->line('level_contactphone')
						,'type'
						,'introducer'
						,'Introducer By'
						,'Registration Date'
						,$this->lang->line('level_Action')
						
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->mastermodel->getBusinesslist($ofset,$limit);
		$data['module']['title'] ="Business List "."[".$emp_list['count']."]";
		//print_r($emp_list);
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			$related=($item['relatedto']!=2)?'Partner':'Sales Executive';
			$Emp=($item['relatedto']!=2)?$this->mastermodel->get_Partnerval($item['employee'])->firstname
			:$this->mastermodel->get_salesEmp($item['employee'])->empname;
			//~ $migrate_regular=($item['act']!="0")?'<a href="Masteradmin/migrate_user/'.$item['bid'].'"><img src="system/application/img/icons/migrate.jpg" /></a>':'';
			$ncall=($item['dnd_status']!=1)?'ncall.png':'dnd.png';
			$fsms=($item['dnd_status']!=1)?'sms_dis.jpg':'sms_sent.jpg';
			$supesc=($item['supEsc'] == 1) ? 'security-high.png' : 'security-low.png';
			$rec[] = array(
				$item['bid']
				,$item['businessname']
				,$item['contactname']
				,$item['contactemail']
				,$item['contactphone']
				,($item['act']!=0)?'Demo':'Regular'
				,$related,$Emp
				,$item['registrationdate']
				,'<a href="Masteradmin/editbusiness/'.$item['bid'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Edit" class="fa fa-edit"></span></a><a href="Masteradmin/businessview/'.$item['bid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><img  id="'.$item['bid'].'" src="system/application/img/icons/page_white_text_width.png" title="View" width="16" height="16" /></a>'.(($item['status']=="0")?'<a href="Masteradmin/changeBusinessstatus/'.$item['bid'].'"><span class="fa fa-lock changeBusStatus" id="'.$item['bid'].'"  title="Enable"></a>':'<a href="Masteradmin/changeBusinessstatus/'.$item['bid'].'"><span class="fa fa-unlock changeBusStatus" id="'.$item['bid'].'"  title="Disable"></a>')/*'<a class="callPopup" href="Masteradmin/ProductConfigure/'.$item['bid'].'"><img id="'.$item['bid'].'" src="system/application/img/icons/cog.png" title="Configure Rate" width="16" height="16" /></a>*/.'<a href="Masteradmin/ManageFeature/'.$item['bid'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><img  id="'.$item['bid'].'" src="system/application/img/icons/manage.png" title="Feature Manage" width="16" height="16" /></a>
				'./*'<a href="Masteradmin/SendMail_UnConfirm/'.$item['bid'].'" 
				><img  id="'.$item['bid'].'" 
				src="system/application/img/icons/sendmail.png" 
				title="Send Mail to Unconfirm Employees" width="16" height="16" 
				/></a>'.*/' <a 
				href="Masteradmin/SendMail_UnConfirm_selected/'.$item['bid'].'" 
				class="btn-danger" data-toggle="modal" data-target="#modal-responsive"
				><img  id="'.$item['bid'].'" 
				src="system/application/img/icons/sendmail1.png" 
				title="Send Mail to Unconfirm Employees" width="16" height="16" 
				/></a>'.' <a 
				href="Masteradmin/passwordreset/'.$item['bid'].'" 
				><img  id="'.$item['bid'].'" 
				src="system/application/img/icons/reset.png" 
				title="Reset" width="16" height="16" 
				/></a>'/*' <a 
				href="Masteradmin/generateBill_byuser/'.$item['bid'].'" 
				><img  id="'.$item['bid'].'" 
				src="system/application/img/icons/payment.png" 
				title="Regenerate" width="16" height="16" 
				/></a>'*/.' <a href="Masteradmin/unconfirm_list_business/'.$item['bid'].'"><span title="List Employees" class="glyphicon glyphicon-user"></span></a>'.'<a href="Masteradmin/LandingNumberDetails/'.$item['bid'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><img src="system/application/img/icons/1340096409_report_add.png" title="List Employees" width="16" height="16" /></a>'.'<a href="Masteradmin/dailyReport/'.$item['bid'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><img src="system/application/img/icons/report_email.png" title="Email Report" width="16" height="16" /></a>'.
				'<a href="Masteradmin/dndfilter/'.$item['bid'].'"><img src="system/application/img/icons/'.$ncall.'" title="DND Filter" width="16" height="16" /></a><a href="Masteradmin/fsetup/'.$item['bid'].'"><img src="system/application/img/icons/'.$fsms.'" title="Folloup Setup" width="16" height="16" /></a>'.
				'<a href="Masteradmin/supEscConfig/'.$item['bid'].'"><img src="system/application/img/icons/'.$supesc.'" title="Support Escalation Settings" width="16" height="16" /></a>'
				.'<a href="Masteradmin/listlanding/'.$item['bid'].'"><img src="system/application/img/icons/mobile-phone--pencil.png" title="List Landing NUmbers" width="16" height="16" /></a>'
			);
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/Businesslist/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->mastermodel->data['html']['title'] .= " | Manage PriNumbers";
		$data['links']='';
		$links = array();
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search"class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$data['links']= $links;	
		
		$formFields1 = array();
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">'.$this->lang->line('level_groupname').' : </label>',
				  'field'=>form_input(array(
									  'name'        => 'groupname',
										'id'          => 'groupname',
										'value'       => $this->session->userdata('groupname'))));
						array_push($formFields1,$cf);
						$data['form'] = array(
							'open'=>form_open_multipart('group/manage_group/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields1,
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->mastermodel->data['links'] = '<a href="group/add_group"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>';
		//$fieldset = $this->configmodel->getFields('3');
		$formFields = array();
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Business Name: </label>',
				'field'=>form_input(array(
						'name'      => 'businessname',
						'id'        => 'businessname',
						'class'     => 'form-control',
						'value'     => $this->session->userdata('businessname')
						))
						);
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Business Email: </label>',
				'field'=>form_input(array(
						'name'      => 'bemail',
						'id'        => 'bemail',
						'class'     => 'form-control',
						'value'     => $this->session->userdata('bemail')
						))
						);
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Phonenumber: </label>',
				'field'=>form_input(array(
						'name'      => 'phnumber',
						'id'        => 'phnumber',
						'class'     => 'form-control',
						'value'     => $this->session->userdata('phnumber')
						))
						);
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Contact Name: </label>',
				'field'=>form_input(array(
						'name'      => 'cname',
						'id'        => 'cname',
						'class'     => 'form-control',
						'value'     => $this->session->userdata('cname')
						))
						);
			
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">City: </label>',
				'field'=>form_input(array(
						'name'      => 'city',
						'id'        => 'city',
						'class'     => 'form-control',
						'value'     => $this->session->userdata('city')
						))
						);
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">State: </label>',
				'field'=>form_input(array(
						'name'      => 'state',
						'id'        => 'state',
						'class'     => 'form-control',
						'value'     => $this->session->userdata('state')
						))
						);
		$droparray=array(""=>"select","0"=>"Regular","1"=>"Demo");			
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Type : </label>',
				'field'=>form_dropdown('btype',$droparray,'','class=form-control')
						);
			
		$data['form'] = array(
			'open'=>form_open_multipart('Masteradmin/Businesslist/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->mastermodel->viewLayout('list_view',$data);
		
		
	}
	function LandingNumberDetails($bid){
	$data['module']['title'] ="Landing Number's List";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('Pri'
						,'Landing Number'
						,'Package Name'
						,'Credit Limit'
						,'Used'
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->mastermodel->getBusinessNumbers($bid);
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			$rec[] = array(
				$item['pri']
				,$item['landingnumber']
				,$item['packagename']
				,$item['climit']
				,$item['used']
				
				
			);
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$data['nosearch']=false;
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/Businesslist/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		//$data['addlinks']="group/add_group";		
		$data['paging'] = $this->pagination->create_links();
		$this->mastermodel->data['html']['title'] .= " | Manage PriNumbers";
		$data['links']=$data['links']='';
		$formFields1 = array();
		$cf=array('label'=>'<label for="groupname">'.$this->lang->line('level_groupname').' : </label>',
				  'field'=>form_input(array(
									  'name'        => 'groupname',
										'id'          => 'groupname',
										'value'       => $this->session->userdata('groupname'))));
						array_push($formFields1,$cf);
						
		$this->mastermodel->data['links'] = '';
		$formFields = array();
		$formFields[] = array();
			
		$data['form'] = array(
			'open'=>form_open_multipart('Masteradmin/keywordactive/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		$this->load->view('popupListView',$data);	
	}
	function businessview($bid){
		
		$this->load->model('configmodel');
		$data['module']['title'] = $this->lang->line('level_business_details');
		$fieldset = $this->configmodel->getFields('1',$bid);
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('1',$bid,'',$bid);
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
						$cf = array('label'=>'<label class="col-sm-4 text-right">'.(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										).' : </label>',
									'field'=>isset($itemDetail[$field['fieldname']]
									)?(($field['fieldname']=='language') ? $itemDetail['lang'] :$itemDetail[$field['fieldname']]):''
								);
						array_push($formFields,$cf);
				
			}
		}
		$data['form'] = array(
		            'form_attr'=>array('action'=>'Mastermodel/businessview','name'=>'businessprofile','id'=>'businessprofile','enctype'=>"multipart/form-data"),
					//~ 'open'=>form_open_multipart('Mastermodel/businessview',array('name'=>'businessprofile','id'=>'businessprofile','class'=>'form','method'=>'post')),
					'fields'=>$formFields,
					'submit'=>'1',
					'close'=>form_close()
				);
		$this->load->view('popupFormView',$data);
		
	}
	function dailyReport($bid){
		if($this->input->post('submit')){
				$fieldset = $this->Config->getFields('6',$bid);
				$callids = $this->CM->getCalls_email($bid,$_POST['rdate']);
				$allCalls = array();
				foreach($callids as $call){
					array_push($allCalls,$this->Config->getDetail('6',$call['callid'],'',$bid));
				}
				foreach($_POST['eids'] as $erow){
					$emp=$this->mastermodel->empD($erow,$bid);
					$roleDetail = $this->CM->empRole($emp['roleid'],$bid);
					$keys = array();
						$header = array('Sl');
						foreach($fieldset as $field){
							$checked = false;
							if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
								foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
								if($checked && in_array($field['fieldname'],array('callfrom','callername','starttime','endtime','dialstatus','gid','eid','source','filename'))){
									array_push($keys,$field['fieldname']);
									array_push($header,(($field['customlabel']!="")
														?$field['customlabel']
														:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
								}
							}
						}
						$output = "<table border='1'>\n";
						$output .= "\t<tr>\n\t\t<th colspan='".count($header)."'>CallTrack Report</th>\n\t</tr>\n";
						
						$output .= "\t<tr>\n";foreach($header as $h) $output .= "\t\t<th>".$h."</th>\n";$output .= "</tr>\n";
						
						$i = 1;
						
						foreach($allCalls as $calls){
							if($roleDetail['role']['admin']=='1' || $emp['eid']==$calls['empid'] || $emp['eid']==$calls['geid']){
								$output .= "\t<tr>\n\t\t<td>".$i."</td>\n";
								foreach($keys as $k) $output .= "\t\t<td>".(isset($calls[$k])?$calls[$k]:"")."</td>\n";
								$output .= "</tr>\n";
								$i++;
							}
						}
						$output .= "</table>\n";
						if($i>'1'){
							$to=$emp['empemail'];
							$subject="CallTrack Report ";
							//~ $headers  = 'MIME-Version: 1.0' . "\n";
							//~ $headers .= 'Content-type: text/html; charset=UTF-8' . "\n";
							//~ $headers .= 'To: '.$emp['empname'].'<'.$to.'>' . "\n";
							//~ $headers .= 'From: MCube <noreply@mcube.com>' . "\n";
							//~ //$headers .= 'BCC:tapan.chatterjee@vmc.in' . "\n";
							//~ $mail = mail($to, $subject,$output, $headers);
							//echo $emp['empname']." ".$emp['empemail']."\n".$output;
							$this->load->library('email');
							$this->email->from('noreply@mcube.com', 'MCube');
							$this->email->to($to);
							$this->email->subject($subject);
							$this->email->message($output);
							$this->email->send();	
							
							
							
						}
					}
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', "Mail Send Successfully");
					redirect($_SERVER['HTTP_REFERER']);
			}
		echo "<script type='text/javascript'>
			$(function() {
				$( '.datepicker' ).datepicker({
				dateFormat: 'yy-mm-dd',
				changeMonth: true,
				changeYear: true,
				minDate: -15,
				maxDate: +0
			});
			$('#dreport').validate({
					errorPlacement: function(error, element) {
						error.appendTo( element.parent().next() );
					}		
				});
		});
		</script>";
		echo '	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title" id="myModalLabel">Email Report</h4>
			</div>
			<div class="modal-body">
		<form action="Masteradmin/dailyReport/'.$bid.'" class="form" id="dreport" name="dreport" method="POST">

				<TABLE>
					<tr>
						<div class="form-group "><label class="col-sm-4 text-right">Date :</label>
						 <div class="col-sm-6 input-icon">
							<input type="text" name="rdate" id="rdate" class="required datepicker form-control" />
					  </div>
					</div>
					</tr>
					<tr>
						<div class="form-group "><label class="col-sm-4 text-right">Employee :</label>
						<div class="col-sm-6 input-icon">
							'.form_dropdown('eids[]',$this->mastermodel->emps($bid),'',' multiple id="eids" class="required form-control"').'
						  </div>
					</div>
					</tr>
					
				</TABLE>
				<table><tr><td><center>
				<input id="button1" type="submit"  class="btn btn-primary" name="submit" value='.$this->lang->line('submit').' /> 
				<input id="button2" type="reset"  class="btn btn-default" value='.$this->lang->line('reset').' />
				</center></td></tr></table>
				</form>

</div>';
	}
	function editbusiness($bid){
		if($this->input->post('update_system')){
			$res=$this->mastermodel->update_business($bid);
			redirect('Masteradmin/Businesslist');
			
		}
		$this->load->model('configmodel');
		$data['module']['title'] = $this->lang->line('level_business_details');
		$fieldset = $this->configmodel->getFields('1',$bid);
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('1',$bid,'',$bid);
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
						$cf = array('label'=>'<label class="col-sm-4 text-right">'.(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										).' : </label>',
									'field'=>($field['fieldname']=="businessaddress"||$field['fieldname']=="businessaddress1")?
									form_textarea(array(
												  'name'      => $field['fieldname'],
												  'id'        => $field['fieldname'],
												  'class'     => 'form-control',
												  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:''
										)):
										(($field['fieldname']=="language")?
										form_dropdown('language',$this->mastermodel->get_all_languages(),isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:'','id="language" class="form-control"')						
										
											:form_input(array(
												  'name'      => $field['fieldname'],
												  'id'        => $field['fieldname'],
												  'class'     => 'form-control',
												  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:''
										))
								)
							);
						array_push($formFields,$cf);
				
			}
		}
		$select=($itemDetail['pid']!=0)?'selected':'';
		$cf=array('label'=>'<label class="col-sm-4 text-right">Parent</label>',
				  'field'=>'<select name="parents" id="parents" class="form-control">
					<option value="">select</option>
					<option value="parent" '.$select.'>Parent</option>
				</select>');
				array_push($formFields,$cf);
				$js1 = 'id="pids" class="form-control"';
		$cf=array('label'=>'<label  class="col-sm-4 text-right">Select Parent Business</label>',
				  'field'=>form_dropdown("pids",$this->mastermodel->getParentBusiness($bid),isset($itemDetail['pid'])?$itemDetail['pid']:'',$js1));
				array_push($formFields,$cf);
				
				
		$data['form'] = array(
		            'form_attr'=>array('action'=>'Masteradmin/editbusiness/'.$bid,'name'=>'businessprofile','id'=>'businessprofile','enctype'=>"multipart/form-data"),
					//~ 'open'=>form_open_multipart('Masteradmin/editbusiness/'.$bid,array('name'=>'businessprofile','id'=>'businessprofile','class'=>'form','method'=>'post')),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$this->load->view('popupFormView',$data);
		
	}
	function AddBEmp($bid,$id=''){
		
		
		$this->mastermodel->data['html']['title'] .= " | Add Employee ";
		$data['module']['title'] = "Add Employee";
		$roles=$this->mastermodel->user_role($bid);
		$formFields = array();$formFields1 = array();
		$cf=array('label'=>"<label>Employee Name : </label> ",
				  'field'=>form_input(array(
									  'name'        => 'empname',
										'id'          => 'empname',
										'value'       => '',
										'class'		=>'required'
				  				) 
				  		)
							);
			array_push($formFields,$cf);
			
		$cf=array('label'=>"<label>Mobile Number: </label> ",
				  'field'=>form_input(array(
									  'name'        => 'empnumber',
										'id'          => 'empnumber',
										'value'       => '',
										'class'		=>'required number'
				  				) 
				  		)
							);
							$s='';
						array_push($formFields,$cf);
		$cf=array('label'=>"<label>Email : </label> ",
				  'field'=>form_input(array(
									  'name'        => 'empemail',
										'id'          => 'empemail',
										'value'       => '',
										'class'		=>'required email'
				  				) 
				  		)
							);
							$s='';
						array_push($formFields,$cf);
						$js=" id='roleid' ";
		$cf=array('label'=>"<label>Role : </label> ",
				  'field'=>form_dropdown('roleid',$roles,'',$js)
							);
							
						array_push($formFields,$cf);
		
		$cf=array('label'=>"<label>Login Access: </label> ",
				  'field'=>form_checkbox(array(
									  'name'        => 'login',
										'id'          => 'login',
										'value'       => '1',
										'class'		=>''
				  				) 
				  		)
							);
						
						array_push($formFields,$cf);
						
		
		$data['form'] = array(
		        'form_attr'=>array('action'=>'Masteradmin/addBEmp/'.$bid,'name'=>'addpackage','id'=>'addpackage','enctype'=>"multipart/form-data"),
				//~ 'open'=>form_open_multipart('Masteradmin/addBEmp/'.$bid,array('name'=>'addpackage','class'=>'form','id'=>'addpackage','method'=>'post')),
				'fields'=>$formFields,
				'fields1'=>$formFields1,
				'close'=>form_close()
			);
		$this->mastermodel->viewLayout('form_view',$data);
		
		
		
		
		
	}
	function changeBusinessstatus($id){
		$access=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Business',3);
		if(!$access)redirect('Masteradmin/access_denied');
		$this->mastermodel->changeBusinessstatus($id);
		$flashdata = array('msgt' => 'success', 'msg' => 'Business status updated Successfully');
		$this->session->set_flashdata($flashdata);
		redirect('Masteradmin/Businesslist');
		
	}
	function keywordactive(){
		$data['module']['title'] ="Keyword List";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array($this->lang->line('level_keywordid')
						,$this->lang->line('level_keyword')
						,$this->lang->line('level_businessname')
						,$this->lang->line('level_fowardto_type')
						,$this->lang->line('level_code')
						,$this->lang->line('level_keyworduse')
						,$this->lang->line('level_Action')
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->mastermodel->getkeywordlist($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			$rec[] = array(
				$item['keyword_id']
				,$item['keyword']
				,$item['businessname']
				,$item['fowardto_type']
				,$item['code']
				,$item['keyworduse']
				,(($item['status']=="0")?'<a href="Masteradmin/changekeystatus/'.$item['keyword_id'].'"><span class="fa fa-lock changeBusStatus" id="'.$item['keyword_id'].'"  title="Enable"></a>':'<a href="Masteradmin/changekeystatus/'.$item['keyword_id'].'"><span class="fa fa-unlock changeBusStatus" id="'.$item['keyword_id'].'"  title="Disable"></a>')
			);
		}

		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		
		//$data['itemlist'] = $this->groupmodel->getgrouplist($bid,$ofset,$limit);
		
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/Businesslist/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		//$data['addlinks']="group/add_group";		
		$data['paging'] = $this->pagination->create_links();
		$this->mastermodel->data['html']['title'] .= " | Manage PriNumbers";
		$data['links']=$data['links']='';
		$formFields1 = array();
		$cf=array('label'=>'<label for="groupname">'.$this->lang->line('level_groupname').' : </label>',
				  'field'=>form_input(array(
									  'name'        => 'groupname',
										'id'          => 'groupname',
										'value'       => $this->session->userdata('groupname'))));
						array_push($formFields1,$cf);
						
		$this->mastermodel->data['links'] = '<a href="group/add_group"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>';
		//$fieldset = $this->configmodel->getFields('3');
		$formFields = array();
		$formFields[] = array(
				'label'=>'<label for="f">Business Name : </label>',
				'field'=>form_input(array(
						'name'      => 'businessname',
						'id'        => 'businessname',
						'value'     => $this->session->userdata('businessname')
						))
						);
		$formFields[] = array(
				'label'=>'<label for="f">Keyword: </label>',
				'field'=>form_input(array(
						'name'      => 'keyword',
						'id'        => 'keyword',
						'value'     => $this->session->userdata('keyword')
						))
						);
		$formFields[] = array(
				'label'=>'<label for="f">Code: </label>',
				'field'=>form_input(array(
						'name'      => 'code',
						'id'        => 'code',
						'value'     => $this->session->userdata('code')
						))
						);
		$formFields[] = array(
				'label'=>'<label for="f">Keyword use: </label>',
				'field'=>form_input(array(
						'name'      => 'keyword_use',
						'id'        => 'keyword_use',
						'value'     => $this->session->userdata('keyword_use')
						))
						);
			
		$data['form'] = array(
			'open'=>form_open_multipart('Masteradmin/keywordactive/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'close'=>form_close(),
			'adv_search'=>array(),
			'title'=>$this->lang->line('level_search')
			);
		$this->mastermodel->viewLayout('list_view',$data);
	}
	function changekeystatus($id){
		$status=$this->mastermodel->get_keystatus($id);
		if($status!=0){
		$res=$this->mastermodel->changekeystatus($id);
		$flashdata = array('msgt' => 'success', 'msg' => 'Keyword status updated Successfully');
		$this->session->set_flashdata($flashdata);
		redirect('Masteradmin/keywordactive');
		}else{
			redirect('Masteradmin/keyword_expire/'.$id);
			
		}
	}
	function keyword_expire($id){
		if($this->input->post('submit')){
			$res=$this->mastermodel->changekeystatus($id);
			$flashdata = array('msgt' => 'success', 'msg' => 'Keyword status updated Successfully');
			$this->session->set_flashdata($flashdata);
			redirect('Masteradmin/keywordactive');
		}
		
		$data=array('id'=>$id);
		
		$this->mastermodel->viewLayout('keywordexpire',$data);
		
	}
	function ProductConfigure($bid){
		if($this->input->post('submit')){
			$this->mastermodel->updateproduct_config($bid);
			$flashdata = array('msgt' => 'success', 'msg' => 'Product Configured Successfully');
			$this->session->set_flashdata($flashdata);
			redirect('Masteradmin/Businesslist');
		}
		$data=array('res'=>$this->mastermodel->businessproducts($bid),
					 'bid'=>$bid,
					 'faction'=>'Masteradmin/ProductConfigure/'.$bid
					 );
		$this->load->view('businessproducts',$data);
		
	}
	function delete_pri($pri){
		$access=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Number',2);
		if(!$access)redirect('Masteradmin/access_denied');
		$err=$this->mastermodel->Prinumber_del($pri);	
		
		/*if($err=="err"){
				$flashdata = array('msgt' => 'error', 'msg' => 'Error-Deleting PriNumber assigned to Business');
				$this->session->set_flashdata($flashdata);
				redirect('Masteradmin/managePriList');
		}else{*/
				$flashdata = array('msgt' => 'success', 'msg' => 'Prinumber Successfully Deleted');
				$this->session->set_flashdata($flashdata);
				redirect('Masteradmin/managePriList');
			
		//}
	}
	function ManageFeature($bid){
		if($this->input->post('submit')){
			$res=$this->mastermodel->update_featuremanage($bid);
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg', $this->lang->line('error_featuresucc'));
			redirect('Masteradmin/Businesslist');
		}
		$data=array('feature_list'=>$this->mastermodel->feature_manage(),
					'subfeature_list'=>$this->mastermodel->sub_featuremanage(),
					'checked_list'=>$this->mastermodel->checked_featuremanage($bid),
					'partnerfeatures'=>array(),
					'bid'=>$bid,
					'faction'=>'Masteradmin/ManageFeature/'.$bid
		);
		$this->load->view('feature_manage',$data);
	}
	function checklanding_number(){
		echo $this->mastermodel->landing_duplicant();
		
	}
	function SendMail_UnConfirm($bid){
		
		$send=$this->mastermodel->SendMails_Unconfirm($bid);
		if($send){
			$flashdata = array('msgt' => 'success', 'msg' => 'Mail send  
			Successfully to Unconfirmed Employees');
			$this->session->set_flashdata($flashdata);
			redirect('Masteradmin/Businesslist');
		}else{
			$flashdata = array('msgt' => 'error', 'msg' => 'No 
			unconfirmed Employee Found');
			$this->session->set_flashdata($flashdata);
			redirect('Masteradmin/Businesslist');
		}
	}
	function confirmEmp($eid,$bid){
		$r=$this->mastermodel->ConfirmEmp($eid,$bid);
		if($r==1){
			$flashdata = array('msgt' => 'success', 'msg' => 'Employee Confirmed Successfully');	
		}else{
			$flashdata = array('msgt' => 'error', 'msg' => 'Employee Number found in DNC Registry please follow Process to Active,Message Sent to Employee Number');
		}
		$this->session->set_flashdata($flashdata);
		redirect('Masteradmin/unconfirm_list_business/'.$bid);
	}
	function unconfirm_list_business($bid){
		
		$data['module']['title'] ="Unconfrim Employees";
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '30';
		$header = array('#'
						,'Employee Name'
						,$this->lang->line('level_contactemail')
						,$this->lang->line('level_contactphone')
						,$this->lang->line('level_Action')
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->mastermodel->get_allunconfirmEmployees($bid,$ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			$rec[] = array(
				$item['eid']
				,$item['empname']
				,$item['empemail']
				,$item['empnumber']
				,'<a href=Masteradmin/delete_unconfirmEmployee/'.$item['eid'].'/'.$bid.'><span title="Delete" class="glyphicon glyphicon-trash"></span></a>'.'<a href="Masteradmin/confirmEmp/'.$item['eid'].'/'.$bid.'"><img src="system/application/img/icons/disable.gif"/></a>'
				
			);
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/unconfirm_list_business/'.$bid)
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>4					
				));
		$data['paging'] = $this->pagination->create_links();
		$data['links']='';
	      $links = array();
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search"class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$data['links']= $links;	
		
		$formFields= array();
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">Employee Name : </label>',
				  'field'=>form_input(array(
									    'name'        => 'empname',
										'id'          => 'empname',
										'class'       => 'form-control',
										'value'       => $this->session->userdata('empname'))));
						array_push($formFields,$cf);
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">Employee Email: </label>',
				  'field'=>form_input(array(
									    'name'        => 'empemail',
										'id'          => 'empemail',
										'class'       => 'form-control',
										'value'       => $this->session->userdata('empemail'))));
						array_push($formFields,$cf);
						
		$this->mastermodel->data['links'] = '<a href=""><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>';
		//$fieldset = $this->configmodel->getFields('3');
		
			
		$data['form'] = array(
			'open'=>form_open_multipart('Masteradmin/unconfirm_list_business/'.$bid,array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->mastermodel->viewLayout('list_view',$data);	
	}
function editgroup($bid,$id){
 $access=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Business',($bid!='')?1:0);
		if(!$access)redirect('Masteradmin/access_denied');
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('groupname', 'Group Name', 'required|min_length[4]|max_length[50]|alpha_numeric|callback_uniquegroup');
			$this->form_validation->set_rules('Rules', 'Rule', 'required');
			$this->form_validation->set_rules('LandingNumber', 'Landing Number', 'required');
		    if(!$this->form_validation->run() == FALSE){
				if($id!=""){
					$res=$this->mastermodel->update_group($bid,$id);
					if($res){	
						$this->session->set_flashdata('msgt', 'success');
						$this->session->set_flashdata('msg', $this->lang->line('error_groupupdatedsuccmsg'));
						redirect('Masteradmin/listlanding/'.$bid);
					}
				}
			}	
		}
		$fieldset = $this->Config->getFields('3',$bid);
		$this->mastermodel->data['html']['title'] .= " | Group Configuration";
		$data['module']['title'] ="Group Configuration";
		$get_Fields=$this->mastermodel->get_GroupValues($bid,$id);
		$formFields = array();
		$formFields1 = array();	
	    $this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$cf=array('label'=>'Group Name',
				  'field'=>form_input(array(
									    'name'        => 'groupname',
										'id'          => 'groupname',
										'value'       =>  (isset($get_Fields[0]['groupname']))?$get_Fields[0]['groupname']:'',
										'class'		=>'required')
									));
		array_push($formFields,$cf);
		$cf=array('label'=>'Landing Number ',
				  'field'=>form_input(array( 'readonly'=>'true',
									    'name'        => 'LandingNumber',
										'id'          => 'LandingNumber',
										'value'       =>  (isset($get_Fields[0]['LandingNumber']))?$get_Fields[0]['LandingNumber']:'system')
										));
							array_push($formFields,$cf);
							
		$js = 'id="Rules" name="Rules" class="auto" ';
		$cf=array('label'=>'Rules',
				  'field'=>form_dropdown("Rules",$this->mastermodel->RuleList($bid),
			               (isset($get_Fields[0]['rules']))?$get_Fields[0]['rules']:set_value($get_Fields[0]['rules']),$js));
							array_push($formFields,$cf);

		$js = 'id="Groupowner" name="Groupowner" class="auto" ';
		$cf=array('label'=>'Group owner',
				  'field'=>form_dropdown("Groupowner",$this->mastermodel->employeelist($bid),
			               (isset($get_Fields[0]['eid']))?$get_Fields[0]['eid']:set_value($get_Fields[0]['eid']),$js));
							array_push($formFields,$cf);					
		
											
		$cf=array('label'=>'keyword',
				  'field'=>form_input(array(
									    'name'        => 'keyword',
										'id'          => 'keyword',
										'value'       => (isset($get_Fields[0]['keyword']))?$get_Fields[0]['keyword']:'')
									));
							array_push($formFields,$cf);     
				   
		$js = 'id="Regionname" name="Regionname" class="auto" ';
		$cf=array('label'=>'Region ',
				  'field'=>form_dropdown("Regionname",$this->mastermodel->PrimaryRuleList($bid,
			               (isset($get_Fields[0]['Regionname']))?$get_Fields[0]['Regionname']:''),
			               (isset($get_Fields[0]['primary_rule']))?$get_Fields[0]['primary_rule']:set_value($get_Fields[0]['primary_rule']),$js));
							array_push($formFields,$cf);
			
				
		$cf=array('label'=>'Record Conversation  ',
				  'field'=>form_checkbox(array(
									    'name'        => 'record',
										'id'          => 'record',
										'checked'       =>($get_Fields[0]['record'])?true:'')
									));
							array_push($formFields,$cf);        
		$cf=array('label'=>'Business Days and Hours  ',
				  'field'=>  (isset($get_Fields[0]['bday']))?
										                      '<table class="short"><tr>'
																.'<td>'.form_checkbox(array('name'=>'bday[Mon][day]','value'=>'1','id'=>'mon_day','checked'=>((isset($bday->Mon->day) || $id!='')?true:''))).'<label for="mon_day">Monday</label>'.'</td>'
																.'<td>'.form_input(array('name'=>'bday[Mon][st]','id'=>'mon_st','class'=>'timepicker','placeholder'=>'Start Time','value'=>(isset($bday->Mon->st)?$bday->Mon->st:'00:00'))).'</td>'
																.'<td>'.form_input(array('name'=>'bday[Mon][et]','id'=>'mon_et','class'=>'timepicker','placeholder'=>'End Time','value'=>(isset($bday->Mon->et)?$bday->Mon->et:'23:59'))).'</td>'
																.'</tr><tr>'
																.'<td>'.form_checkbox(array('name'=>'bday[Tue][day]','value'=>'1','id'=>'tue_day','checked'=>((isset($bday->Tue->day) || $id!='')?true:''))).'<label for="tue_day">Tuesday</label>'.'</td>'
																.'<td>'.form_input(array('name'=>'bday[Tue][st]','id'=>'tue_st','class'=>'timepicker','placeholder'=>'Start Time','value'=>(isset($bday->Tue->st)?$bday->Tue->st:'00:00'))).'</td>'
																.'<td>'.form_input(array('name'=>'bday[Tue][et]','id'=>'tue_et','class'=>'timepicker','placeholder'=>'End Time','value'=>(isset($bday->Tue->et)?$bday->Tue->et:'23:59'))).'</td>'
																.'</tr><tr>'
																.'<td>'.form_checkbox(array('name'=>'bday[Wed][day]','value'=>'1','id'=>'wed_day','checked'=>((isset($bday->Wed->day) || $id!='')?true:''))).'<label for="wed_day">Wednesday</label>'.'</td>'
																.'<td>'.form_input(array('name'=>'bday[Wed][st]','id'=>'wed_st','class'=>'timepicker','placeholder'=>'Start Time','value'=>(isset($bday->Wed->st)?$bday->Wed->st:'00:00'))).'</td>'
																.'<td>'.form_input(array('name'=>'bday[Wed][et]','id'=>'wed_et','class'=>'timepicker','placeholder'=>'End Time','value'=>(isset($bday->Wed->et)?$bday->Wed->et:'23:59'))).'</td>'
																.'</tr><tr>'
																.'<td>'.form_checkbox(array('name'=>'bday[Thu][day]','value'=>'1','id'=>'thu_day','checked'=>((isset($bday->Thu->day) || $id!='')?true:''))).'<label for="thu_day">Thursday</label>'.'</td>'
																.'<td>'.form_input(array('name'=>'bday[Thu][st]','id'=>'thu_st','class'=>'timepicker','placeholder'=>'Start Time','value'=>(isset($bday->Thu->st)?$bday->Thu->st:'00:00'))).'</td>'
																.'<td>'.form_input(array('name'=>'bday[Thu][et]','id'=>'thu_et','class'=>'timepicker','placeholder'=>'End Time','value'=>(isset($bday->Thu->et)?$bday->Thu->et:'23:59'))).'</td>'
																.'</tr><tr>'
																.'<td>'.form_checkbox(array('name'=>'bday[Fri][day]','value'=>'1','id'=>'fri_day','checked'=>((isset($bday->Fri->day)  || $id!='')?true:''))).'<label for="fri_day">Friday</label>'.'</td>'
																.'<td>'.form_input(array('name'=>'bday[Fri][st]','id'=>'fri_st','class'=>'timepicker','placeholder'=>'Start Time','value'=>(isset($bday->Fri->st)?$bday->Fri->st:'00:00'))).'</td>'
																.'<td>'.form_input(array('name'=>'bday[Fri][et]','id'=>'fri_et','class'=>'timepicker','placeholder'=>'End Time','value'=>(isset($bday->Fri->et)?$bday->Fri->et:'23:59'))).'</td>'
																.'</tr><tr>'
																.'<td>'.form_checkbox(array('name'=>'bday[Sat][day]','value'=>'1','id'=>'sat_day','checked'=>((isset($bday->Sat->day)  || $id!='')?true:''))).'<label for="sat_day">Saturday</label>'.'</td>'
																.'<td>'.form_input(array('name'=>'bday[Sat][st]','id'=>'sat_st','class'=>'timepicker','placeholder'=>'Start Time','value'=>(isset($bday->Sat->st)?$bday->Sat->st:'00:00'))).'</td>'
																.'<td>'.form_input(array('name'=>'bday[Sat][et]','id'=>'sat_et','class'=>'timepicker','placeholder'=>'End Time','value'=>(isset($bday->Sat->et)?$bday->Sat->et:'23:59'))).'</td>'
																.'</tr><tr>'
																.'<td>'.form_checkbox(array('name'=>'bday[Sun][day]','value'=>'1','id'=>'sun_day','checked'=>((isset($bday->Sun->day) || $id!='')?true:''))).'<label for="sun_day">Sunday</label>'.'</td>'
																.'<td>'.form_input(array('name'=>'bday[Sun][st]','id'=>'sun_st','class'=>'timepicker','placeholder'=>'Start Time','value'=>(isset($bday->Sun->st)?$bday->Sun->st:'00:00'))).'</td>'
																.'<td>'.form_input(array('name'=>'bday[Sun][et]','id'=>'sun_et','class'=>'timepicker','placeholder'=>'End Time','value'=>(isset($bday->Sun->et)?$bday->Sun->et:'23:59'))).'</td>'
																.'</tr></table>':'');
								
							array_push($formFields,$cf);

		$cf=array('label'=>'Text Message for non Business Day  ',
				  'field'=>form_input(array(
									  'name'        => 'hdaytext',
										'id'          => 'hdaytext',
										'value'       => (isset($get_Fields[0]['hdaytext']))?$get_Fields[0]['hdaytext']:'')
									));
							array_push($formFields,$cf);
	    $cf=array('label'=>'Audio Message for non Business Day  ',
				  'field'=>form_upload(array(
									  'name'        => 'hdayaudio',
										'id'          => 'hdayaudio',
										'value'       => (isset($get_Fields[0]['hdayaudio']))?$get_Fields[0]['hdayaudio']:'')
									) .((isset($get_Fields[0]['hdayaudio']) && $get_Fields[0]['hdayaudio']!='' && file_exists('sounds/'.$get_Fields[0]['hdayaudio']))? 
											'<a target="_blank" href="'.site_url('sounds/'.$get_Fields[0]['hdayaudio']).'">
											
											 <span id="closeimg" title="Sound" class="fa fa-volume-up"></span></a>':''));
							array_push($formFields,$cf);
		$cf=array('label'=>'Greetings  ',
				  'field'=>form_upload(array(
									  'name'        => 'greetings',
										'id'          => 'greetings',
										'value'       => ($get_Fields[0]['greetings'])?$get_Fields[0]['greetings']:'')
									).((isset($get_Fields[0]['greetings']) && $get_Fields[0]['greetings']!='' && file_exists('sounds/'.$get_Fields[0]['greetings']))? 
											'<a target="_blank" href="'.site_url('sounds/'.$get_Fields[0]['greetings']).'">
											 <span id="closeimg" title="Sound" class="fa fa-volume-up"></span>
											</a>':''));
							array_push($formFields,$cf);					
		
		$cf=array('label'=>'Reply Message for missed call',
				  'field'=>form_textarea(array(
									    'name'        => 'replymessage',
										'id'          => 'replymessage',
										'value'       => (isset($get_Fields[0]['replymessage']))?$get_Fields[0]['replymessage']:'')
									));
							array_push($formFields,$cf);
							
		$cf=array('label'=>'SMS To Customer  ',
				  'field'=>form_checkbox(array(
									  'name'        => 'replytocustomer',
										'id'          => 'replytocustomer',
										'checked'      =>($get_Fields[0]['replytocustomer'])?true:'')
									));
							array_push($formFields,$cf);
        					
		$cf=array('label'=>'SMS To Executive  ',
				  'field'=>form_checkbox(array(
									  'name'      => 'replytoexecutive',
										'id'      => 'replytoexecutive',
										'checked'   => ($get_Fields[0]['replytoexecutive'])?true:'')
									));
							array_push($formFields,$cf);
		
		
		$cf=array('label'=>'Repeated Call to same Executive  ',
				  'field'=>form_checkbox(array(
									    'name'        => 'sameexe',
										'id'          => 'sameexe',
										'checked'     =>($get_Fields[0]['sameexe'])?true:'')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'Record Notification  ',
				  'field'=>form_checkbox(array(
									    'name'        => 'recordnotice',
										'id'          => 'recordnotice',
										'checked'    => ($get_Fields[0]['recordnotice'])?true:'')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'Missed Calls Only  ',
				  'field'=>form_checkbox(array(
									    'name'        => 'misscall',
										'id'          => 'misscall',
										'checked'    => ($get_Fields[0]['misscall'])?true:'')
									));
							array_push($formFields,$cf);
		
	
		$cf=array('label'=>'Connect Group Owner  ',
				  'field'=>form_checkbox(array(
									    'name'        => 'connectowner',
										'id'          => 'connectowner',
										'checked'     => ($get_Fields[0]['connectowner'])?true:'')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'Time Out to connect Call  ',
				  'field'=>form_input(array(
									    'name'        => 'timeout',
										'id'          => 'timeout',
										'value'       =>(isset($get_Fields[0]['timeout']))?$get_Fields[0]['timeout']:'')
									));
							array_push($formFields,$cf);
					
	    $cf=array('label'=>'Action Perform on Edit  ',
				  'field'=>form_input(array(
									    'name'        => 'oneditaction',
										'id'          => 'oneditaction',
										'value'       => (isset($get_Fields[0]['oneditaction']))?$get_Fields[0]['oneditaction']:'')
									));
							array_push($formFields,$cf);		
					
		$cf=array('label'=>'On Hangup  ',
				  'field'=>form_input(array(
									  'name'        => 'onhangup',
										'id'          => 'onhangup',
										'value'       =>(isset($get_Fields[0]['onhangup']))?$get_Fields[0]['onhangup']:'')
									));
							array_push($formFields,$cf);		
					
		$cf=array('label'=>'Action Perform on Convert Lead  ',
				  'field'=>form_input(array(
									  'name'        => 'leadaction',
										'id'          => 'leadaction',
										'value'       =>(isset($get_Fields[0]['leadaction']))?$get_Fields[0]['leadaction']:'')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'Reply Message For attended Call  ',
				  'field'=>form_textarea(array(
									  'name'        => 'replyattmsg',
										'id'          => 'replyattmsg',
										'value'       =>(isset($get_Fields[0]['replyattmsg']))?$get_Fields[0]['replyattmsg']:'')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'Action Perform on Call  ',
				  'field'=>form_input(array(
									  'name'        => 'oncallaction',
										'id'          => 'oncallaction',
										'value'       => (isset($get_Fields[0]['oncallaction']))?$get_Fields[0]['oncallaction']:'')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'Connect Executlve by Pincode  ',
				  'field'=>form_checkbox(array(
									  'name'        => 'pincode',
										'id'          => 'pincode',
										'checked'      => ($get_Fields[0]['pincode'])?true:'')
									));
							array_push($formFields,$cf);
							
							
	    $cf=array('label'=>'Action Perform on Convert Support  ',
				  'field'=>form_input(array(
									  'name'        => 'supportaction',
										'id'          => 'supportaction',
										'value'       => (isset($get_Fields[0]['supportaction']))?$get_Fields[0]['supportaction']:'')
									));
							array_push($formFields,$cf);
							
		$cf=array('label'=>'Distribute Call for All Groups ',
				  'field'=>form_checkbox(array(
									  'name'        => 'allgroup',
										'id'          => 'allgroup',
										'checked'      => ($get_Fields[0]['allgroup'])?true:'')
									));
							array_push($formFields,$cf);
							
												
	       
			        $data['form'] = array(   
			          'form_attr'=>array('action'=>'Masteradmin/editgroup/'.$bid.'/'.$id,'name'=>'editgroup','id'=>'editgroup','enctype'=>"multipart/form-data"),                            
					//~ 'open'=>form_open_multipart('Masteradmin/editgroup/'.$bid.'/'.$id,
					//~ array('name'=>'editgroup','class'=>'form','id'=>'editgroup','method'=>'post'),
					        'hidden' => array('bid'=>$id,'oldprinumber'=>isset($itemDetail['prinumber'])?$itemDetail['prinumber']:""),
							'fields'=>$formFields,
							'fields1'=>$formFields1,
							'close'=>form_close()
				);
		$this->mastermodel->viewLayout('form_view',$data);
	}

function group_emp_list($bid,$gid){

		$data['module']['title'] = $this->lang->line('label_groupemp');
		$gpDetail = $this->mastermodel->getDetail($gid,$bid);
	
	    $limit = '20';
		$header = array($this->lang->line('level_empname')
						,$this->lang->line('level_group'));
		foreach ($gpDetail as $item1){			
          if($item1['primary_rule']>0)$header[]=$this->lang->line('label_region');				
	      if($item1['rule']=='2')$header[]=$this->lang->line('level_empweight');
          if($item1['rule']=='3')$header[]=$this->lang->line('level_empPriority');
		array_push($header,$this->lang->line('level_starttime')
						,$this->lang->line('level_endtime')
						,$this->lang->line('label_isfailover')
						,"Call Counter");
						
		
		$data['itemlist']['header'] = $header;
		$emp_list=$this->mastermodel->groupemplist($bid,$gid);

		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
				 
			 		$r = array(
				(($item['status']=="1" && $item['estatus']=='1') ? $item['empname'] : '<font color=red>'.$item['empname'].'</font>')
				,(($item['status']=="1" && $item['estatus']=='1') ? $item['groupname'] : '<font color=red>'.$item['groupname'].'</font>'));
	    if($item1['primary_rule']>0)$r[]=(($item['status']=="1" && $item['estatus']=='1') ? $item['region'] : '<font color=red>'.$item['region'].'</font>');	
		if($item1['rule']=='2')	$r[]=($item['status']=="1" && $item['estatus']=='1') ? $item['empweight'] : '<font color=red>'.$item['empweight'].'</font>';
		if($item1['rule']=='3')$r[]=($item['status']=="1" && $item['estatus']=='1') ? $item['empPriority'] : '<font color=red>'.$item['empPriority'].'</font>';
				$r = array_merge($r,array(
							(($item['status']=="1" && $item['estatus']=='1') ? $item['starttime'] : '<font color=red>'.$item['starttime'].'</font>'),
							(($item['status']=="1" && $item['estatus']=='1') ? $item['endtime'] : '<font color=red>'.$item['endtime'].'</font>'),
							(($item['status']=="1" && $item['estatus']=='1') ? $item['failover'] : '<font color=red>'.$item['failover'].'</font>'),
							(($item['status']=="1" && $item['estatus']=='1') ? $item['callcounter'] : '<font color=red>'.$item['callcounter'].'</font>')
							));
					$rec[] = $r;
			 }
        }
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		
		
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/group_emp_list/'.$bid.'/'.$gid)
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>4					
				));
		
		$data['paging'] = $this->pagination->create_links();
		$this->mastermodel->data['html']['title'] .= " | ".$this->lang->line('label_groupemp');                    
		$data['links']=$data['links']='<a style="display: none"; href="Masteradmin/addemptogroup/'.$gid.'/'.$bid.'"><span class="glyphicon glyphicon-plus-sign" title="Add Employee"></span></a>';   
		$data['links'] .= '<a style="display: none";  href="Masteradmin/refreshcounter/'.$gid.'/'.$bid.'"> <span title="Reset Counter" class="fa fa-refresh"></span></a>';
	    $links = array();
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search"class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$data['links']= $links;	
		$formFields = array();
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">Employee Name : </label>',
				  'field'=>form_input(array(
									    'name'        => 'empname',
										'id'          => 'empname',
										'class'       => 'form-control',
										'value'       => $this->session->userdata('empname'))));
						array_push($formFields,$cf);
	
		$data['form'] = array(
			'open'=>form_open_multipart('Masteradmin/group_emp_list/'.$bid.'/'.$gid,array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->mastermodel->viewLayout('list_view',$data);
	}
	function refreshcounter($gid,$bid){

		$res = $this->mastermodel->refreshcounter($gid,$bid);
	
		if($res == 1){
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg', ' The Call Counter has reset to 0');
		}else{
			$this->session->set_flashdata('msgt', 'error');
			$this->session->set_flashdata('msg', ' Error while reset the counter');
		}
		redirect('Masteradmin/group_emp_list/'.$bid.'/'.$gid);
	}

function addemptogroup($gid,$bid){
		
		if($this->input->post('update_system')){

				$array=$this->mastermodel->get_group($bid,$gid);
				$get_empgroupC=$this->mastermodel->groupemplist($bid,$gid);
				$res=$this->mastermodel->addemp_group($bid,$gid);	
			
				if($res==0){
					    $this->session->set_flashdata('msgt', 'error');
						$this->session->set_flashdata('msg', $this->lang->line('error_alreadyexists'));
					   redirect('Masteradmin/addemptogroup/'.$gid.'/'.$bid);
				}
				else{
				      $this->session->set_flashdata('msgt', 'success');
					   $this->session->set_flashdata('msg', $this->lang->line('error_addempsucss'));
					   redirect('Masteradmin/listlanding/'.$bid);
				}
				
		
		}
		$gpDetail = $this->mastermodel->getDetail($gid,$bid);
		$this->mastermodel->data['html']['title'] .= " | ".$this->lang->line('label_addemptogroup');
		$data['module']['title'] = $this->lang->line('label_addemptogroup');
	
		$formFields = array();
		$data['form'] = array(
					'open'=>form_open_multipart(current_url()
								,array('name'=>'addemp','class'=>'form','id'=>'addemp','method'=>'post')
								,array('bid'=>$bid
								  ,'gid'=>$gid
								 
								  )),
					'fields'=>$formFields,
					'close'=>form_close(),
					'emplist'=>$this->mastermodel->get_emp_list($bid),
					'empexists'=>$this->mastermodel->group_enteremplist($bid,$gid),
					'gdetail'=>$gpDetail,
					'bid'=>$bid,
				);

		$this->mastermodel->viewLayout('form_view_masteremp',$data);
	}


function listlanding($bid){
        $this->mastermodel->data['html']['title'] .= " | ".$this->lang->line('label_groupmanage');
		$data['module']['title'] ="Landing Numbers";
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '30';
		$emp_list=$this->mastermodel->getAllLandingnum($bid,$ofset,$limit);
		
		$header = array('Si No'
				        ,'Landing Number'
		                ,'Group Name'
		                ,'Group Owner'
		                ,'Keyword'
		                ,'Rules'
		                ,'Region'
						,'Business Days and Hours'
						,'Connect Group Owner'
						,'Time Out to connect Call'
						,'Action'
						);
		$data['itemlist']['header'] = $header;
	
        
		$rec = array();
		if(count($emp_list['data'])>0)
	    $sn_count = 1;
	    
		foreach ($emp_list['data'] as $item){
                     
		    $connectowner=(isset($item['connectowner']) && $item['connectowner']=='1')?"Yes":"No";
	        $act = "";                               
			    $act .= '<a class="btn-danger" data-toggle="modal" data-target="#modal-responsive" href="Masteradmin/activerecords/'.$item['gid'].'/'.$bid.'"><span class="fa fa-file-text"  title="View Group"></span></a>';
				$act .= '<a href="Masteradmin/group_emp_list/'.$bid.'/'.$item['gid'].'"><span title="List Employees" class="glyphicon glyphicon-user"></span></a>';
			$bday = json_decode($item['bday']);
            $v = '';
			foreach($bday as $b => $d){ $v .= (isset($d->day) && $d->day=='1')?$b.'='.$d->st.'-'.$d->et.'<br>':'';}
			$rec[] = array(
		         $sn_count++
		        ,$item['LandingNumber']
				,$item['GroupName']
				,$item['Groupowner']
			    ,$item['keyword']
				,$item['Rules']
				,$item['Regionname']
				,$v
				,$connectowner
				,$item['timeout']	
		        ,$act
			);
			}
	
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/unconfirm_list_business/'.$bid)
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>4					
				));
		$data['paging'] = $this->pagination->create_links();
		$data['links']='';
	    $links = array();
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search"class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$data['links']= $links;	
		$formFields= array();
		$data['form'] = array(
			'open'=>form_open_multipart('Masteradmin/listlanding/'.$bid,array('name'=>'listlanding','class'=>'form','id'=>'listlanding','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			);
	    if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->mastermodel->viewLayout('list_view',$data);
	}
	
	
	
	function SendMail_UnConfirm_selected($bid){
		if($this->input->post('submit')){
			$res=$this->mastermodel->Sendmail_unconfirm_Emp($bid);
			$flashdata = array('msgt' => 'success', 'msg' => 'Mail send  
			Successfully to Unconfirmed Employees');
			$this->session->set_flashdata($flashdata);
			redirect('Masteradmin/Businesslist');
		}
		$r=$this->mastermodel->get_unconfirmed_emp($bid);
		$data=array('bid'=>$bid,
					'res'=>$r,
					'faction'=>'Masteradmin/SendMail_UnConfirm_selected/'.$bid);
		$this->load->view('UnconfirmEmployee',$data);			
	}
	function AddUassignedPris(){
		if($this->input->post('update_system')){
			//print_r($_POST);exit;
			$res=$this->mastermodel->Addpris();
			if($res!=""){
				if(isset($_POST['demo'])){
				redirect('Masteradmin/AssignMobileNumber');
			}
			}else{
				echo "Error";
			}
		}
		
		$this->load->view('AddPri');
	}
	function blocknumbers_check($str){
		//echo $str;exit;
		if($this->mastermodel->blacknumberexists($str)=="exists"){
		$this->form_validation->set_message('blocknumbers_check', 'The '.$str.' is already in the list');
			return FALSE;
		}else{
			return TRUE;
		}
	}
	function BlockNumber(){
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('blacklist', 'blocknumbers', 'required|min_length[10]|max_length[12]');
			$this->form_validation->set_rules('blacklist', 'blocknumbers','callback_blocknumbers_check');
			if(!$this->form_validation->run() == FALSE){	
						$res=$this->mastermodel->admin_blacklistnumber();
						if($res!=""){
							$this->session->set_flashdata('msgt', 'success');
							$this->session->set_flashdata('msg', $this->lang->line('error_blacksuccmsg'));
							redirect('Masteradmin/BlockNumber');
							}
							
					}	
		}
		
		$this->mastermodel->data['html']['title'] .= " | ".$this->lang->line('blocknumbers');
	   
		$data['module']['title'] = $this->lang->line('blocknumbers');
		$formFields = array();$formFields1 = array();
		$cf=array('label'=>'<label class="col-sm-4 text-right">'.$this->lang->line('level_blocknumbers').'</label>',
				  'field'=>form_input(array(
									  'name'        => 'blacklist',
										'id'          => 'blacklist',
										'value'       => '',
										'class'		=>'required form-control'
				  				)
				  		)
							);
						array_push($formFields,$cf);
			$data['form'] = array(
		       	   'form_attr'=>array('action'=>'Masteradmin/BlockNumber/','id'=>'Adminblocknumbers','name'=>'Adminblocknumbers','enctype'=>"multipart/form-data"),
					//~ 'open'=>form_open_multipart('Masteradmin/BlockNumber/',array('name'=>'Adminblocknumbers','class'=>'form','id'=>'Adminblocknumbers','method'=>'post')),
					'fields'=>$formFields,
					'fields1'=>$formFields1,
					'close'=>form_close()
				);
		
		$this->mastermodel->viewLayout('form_view',$data);
	}
	function Delete_blocknumber($id){
			$res=$this->mastermodel->Delete_blocknumber($id);
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg', $this->lang->line('error_delblacksuccmsg'));
			redirect('Masteradmin/blocklistnumbers');
		
		
	}
	function blocklistnumbers(){
		$data['module']['title'] =$this->lang->line('blocklist');
		$header = array('#',
						'Number',
						'Date &time',
						'Action'
						);
		$data['itemlist']['header'] = $header;
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '20';
		$credit_info=$this->mastermodel->blocknumber_list($ofset,$limit);
		$rec = array();
		if(count($credit_info['data'])>0)
		$i=1;
		foreach ($credit_info['data'] as $item){
			$rec[] = array(
					$item['id'],
					$item['number'],
					$item['datetime'],
					'<a href="'.base_url().'Masteradmin/Delete_blocknumber/'.$item['id'].'"  title="Are you sure to Delete the group"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>',
			);
			$i++;
		}
	    $links = array();
	   
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search"class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="Masteradmin/BlockNumber"><span class="glyphicon glyphicon-plus-sign" title="Add Blacklist Number">Add Blacklist</span></a></li>';
		$data['links']= $links;	
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $credit_info['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/blocklistnumbers/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->mastermodel->data['html']['title'] .= " |".$this->lang->line('blocklist');
		
		$formFields1 = array();
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">'.$this->lang->line('blocknumber').' : </label>',
				  'field'=>form_input(array(
									  'name'          => 'blnumber',
										'id'          => 'blnumber',
										'class'       => 'form-control',
										'value'       => $this->session->userdata('blnumber'))));
						array_push($formFields1,$cf);
		$formFields = array();
		$formFields[] = array();
		$data['form'] = array(
							'open'=>form_open_multipart('Masteradmin/blocklistnumbers/',array('name'=>'blocklist','class'=>'form','id'=>'blocklist','method'=>'post')),
							'form_field'=>$formFields1 ,
							'adv_search'=>array(),
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
	     if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->mastermodel->viewLayout('list_view',$data);
	}
	Function Listpartner(){
		$data['module']['title'] =$this->lang->line('listpartner');
		$header = array('#',
						'Name',
						'Email',
						'Domain Name',
						'Create Date',
						'Action'
						);
		$data['itemlist']['header'] = $header;
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '20';
		$credit_info=$this->mastermodel->partnersList($ofset,$limit);
		$rec = array();
		if(count($credit_info['data'])>0)
		$i=1;
		foreach ($credit_info['data'] as $item){
			($item['status']=="0")
				?$s=' <a href="Masteradmin/ChangePartnerS/'.$item['partner_id'].'"><span class="fa fa-lock changeBusStatus" id="'.$item['partner_id'].'"  title="Enable"></a>'
				:$s=' <a href="Masteradmin/ChangePartnerS/'.$item['partner_id'].'"><span class="fa fa-unlock changeBusStatus" id="'.$item['partner_id'].'"  title="Disable"></a>';
			
			
			$rec[] = array(
					$item['partner_id'],
					$item['companyname'],
					$item['email'],
					$item['domain_name'],
					$item['create_date'],
					'<a href="'.base_url().'Masteradmin/Addpartner/'.$item['partner_id'].'"  title="Edit"><span title="Edit" class="fa fa-edit"></span></a><a href="'.base_url().'Masteradmin/Deletepartner/'.$item['partner_id'].'"  title="Delete"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>'.$s.
					'<a href="'.base_url().'Masteradmin/Prisettings/'.$item['partner_id'].'"  title="settings" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><img src="system/application/img/icons/cog.png" title="Edit" /></a>'
					
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $credit_info['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/Listpartner/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->mastermodel->data['html']['title'] .= " |".$this->lang->line('listpartner');
		$data['links']='<a href="Masteradmin/Addpartner"><span class="glyphicon glyphicon-plus-sign" title="Add Partner"></span></a>';
		$formFields1 = array();
		$cf=array('label'=>'<label for="groupname">'.$this->lang->line('partneremail').' : </label>',
				  'field'=>form_input(array(
									  'name'        => 'partneremail',
										'id'          => 'partneremail',
										'value'       => $this->session->userdata('blnumber'))));
						array_push($formFields1,$cf);
		$cf=array('label'=>'<label for="groupname">'.$this->lang->line('Domain').' : </label>',
				  'field'=>form_input(array(
									  'name'        => 'domainname',
										'id'          => 'domainname',
										'value'       => $this->session->userdata('domainname'))));
						array_push($formFields1,$cf);
		$formFields = array();
		$formFields[] = array();
		$data['form'] = array(
							'open'=>form_open_multipart('Masteradmin/Listpartner/',array('name'=>'blocklist','class'=>'form','id'=>'blocklist','method'=>'post')),
							'form_field'=>$formFields1 ,
							'adv_search'=>array(),
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->mastermodel->viewLayout('list_view',$data);
	}
	function Prisettings($partner_id){
		if($this->input->post('submit')){
			$res=$this->mastermodel->pripartner($partner_id);
			redirect('Masteradmin/Listpartner');
		}
		
		$data=array('partner_id'=>$partner_id,
					"prilist"=>$this->mastermodel->availblepri(),
					 "selectedlist"=>$this->mastermodel->availblepri($partner_id));
					 
		$this->load->view('partner_config',$data);
		
		
	}
	Function DeletedList(){
		
		
		$data['module']['title'] =$this->lang->line('listpartner');
		$header = array('#',
						'Name',
						'Email',
						'Domain Name',
						'Create Date',
						'Action'
						);
		$data['itemlist']['header'] = $header;
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '20';
		$credit_info=$this->mastermodel->DeletepartnersList($ofset,$limit);
		$rec = array();
		if(count($credit_info['data'])>0)
		$i=1;
		foreach ($credit_info['data'] as $item){
			$rec[] = array(
					$item['partner_id'],
					$item['firstname'],
					$item['email'],
					$item['domain_name'],
					$item['create_date'],
					'<a href="'.base_url().'Masteradmin/UnDeletepartner/'.$item['partner_id'].'"  title="Delete"><img src="system/application/img/icons/undelete.png" title="Delete" /></a>',
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $credit_info['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/DeletedList/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->mastermodel->data['html']['title'] .= " |".$this->lang->line('listpartner');
		$data['links']='<a href="Masteradmin/Addpartner"><span class="glyphicon glyphicon-plus-sign" title="Add Partner"></span></a>';
		$formFields1 = array();
		$cf=array('label'=>'<label for="groupname">'.$this->lang->line('partneremail').' : </label>',
				  'field'=>form_input(array(
									  'name'        => 'partneremail',
										'id'          => 'partneremail',
										'value'       => $this->session->userdata('blnumber'))));
						array_push($formFields1,$cf);
		$cf=array('label'=>'<label for="groupname">'.$this->lang->line('Domain').' : </label>',
				  'field'=>form_input(array(
									  'name'        => 'domainname',
										'id'          => 'domainname',
										'value'       => $this->session->userdata('domainname'))));
						array_push($formFields1,$cf);
		$formFields = array();
		$formFields[] = array();
		$data['form'] = array(
							'open'=>form_open_multipart('Masteradmin/Listpartner/',array('name'=>'blocklist','class'=>'form','id'=>'blocklist','method'=>'post')),
							'form_field'=>$formFields1 ,
							'adv_search'=>array(),
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->mastermodel->viewLayout('list_view',$data);
	}
	function del_logo($id){
		$res=$this->mastermodel->del_logo($id);
		redirect('Masteradmin/Addpartner/'.$id);
		
	}
	function del_photocopy($id){
		$res=$this->mastermodel->del_photocopy($id);
		redirect('Masteradmin/Addpartner/'.$id);
		
	}
	function Addpartner($id=''){
		$access=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Partner',($id!="")?1:0);
		if(!$access)redirect('Masteradmin/access_denied');
		$files=1;
		if(isset($_POST)){
			$image=array("image/png","image/jpg","image/jpeg","image/gif");
			if($id==""){
			if(isset($_FILES['logo']['name']) && $_FILES['logo']['name']!="" && !in_array($_FILES['logo']['type'],$image)){
				$files=0;
				$this->form_validation->set_message('logo','Invalid Format uploaded');
				}
			if(isset($_FILES['photocopy']['name']) && $_FILES['photocopy']['name']!="" && !in_array($_FILES['photocopy']['type'],$image)){
				$files=0;
				$this->form_validation->set_message('photocopy','Invalid Format uploaded');
				}
			}
			$this->form_validation->set_rules('firstname', 'firstname', 'required|max_length[20]');
			$this->form_validation->set_rules('lastname', 'lastname', 'required|max_length[20]');
			$this->form_validation->set_rules('companyname', 'companyname', 'required|min_length[4]|max_length[20]');
			$this->form_validation->set_rules('mobilenumber', 'mobilenumber','required|numeric');
			$this->form_validation->set_rules('companyphone', 'companyphone','numeric');
			$this->form_validation->set_rules('industry', 'industry', 'required|min_length[4]|max_length[20]');
			$this->form_validation->set_rules('partneremail', 'partneremail','required|valid_email');
			
			$this->form_validation->set_rules('address', 'address','required');
			$this->form_validation->set_rules('city', 'city', 'required|min_length[2]|max_length[20]');
			$this->form_validation->set_rules('state', 'state', 'required|min_length[2]|max_length[20]');
			$this->form_validation->set_rules('pincode', 'pincode', 'required|min_length[4]|numeric');
			$this->form_validation->set_rules('pancard', 'pancard', 'pancard|min_length[4]');
			$this->form_validation->set_rules('bank', 'bank','required|max_length[50]');
			$this->form_validation->set_rules('bankaddress', 'bankaddress','required');			
			$this->form_validation->set_rules('accno', 'accno','required');
			if($id==""){
				$this->form_validation->set_rules('confirmemail', 'confirmemail','required|valid_email|matches[partneremail]');
			$this->form_validation->set_rules('domainname', 'domainname','required|min_length[4]|max_length[20]');
			$this->form_validation->set_rules('cdomainname', 'cdomainname','required|min_length[4]|matches[cdomainname]');
			$this->form_validation->set_rules('password', 'password','required|min_length[4]');
			$this->form_validation->set_rules('cpassword', 'cpassword','required|min_length[4]|matches[cpassword]');	
		}
			
			if(!$this->form_validation->run() == FALSE && $files!=0){	
				
					if($id!=""){
						$update=$this->mastermodel->Addpartner($id);
						$this->session->set_flashdata('msgt', 'success');
						$this->session->set_flashdata('msg', $this->lang->line('updatesuccessmsg'));
						redirect('Masteradmin/Listpartner');
						
					}else{	
						$rs=$this->mastermodel->AddPartner();
						$this->session->set_flashdata('msgt', 'success');
						$this->session->set_flashdata('msg', $this->lang->line('successmsg'));
						redirect('Masteradmin/Listpartner');
					}
				}	
		}
		$this->mastermodel->data['html']['title'] .= " | ".$this->lang->line('Addpartner');
		$data['module']['title'] = $this->lang->line('Addpartner');
		$get_Fields=$this->mastermodel->get_PartnerValues($id);
		$formFields = array();$formFields1 = array();
		$cf=array('label'=>'<label>FirstName :</label>',
				  'field'=>form_input(array(
									  'name'        => 'firstname',
										'id'          => 'firstname',
										'value'       => (isset($get_Fields[0]['firstname']))?$get_Fields[0]['firstname']:'',
										'class'		=>'required')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'<label>LastName :</label>',
				  'field'=>form_input(array(
									  'name'        => 'lastname',
										'id'          => 'lastname',
										'value'       => (isset($get_Fields[0]['lastname']))?$get_Fields[0]['lastname']:'',
										'class'		=>'required')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'<label>CompanyName :</label>',
				  'field'=>form_input(array(
									  'name'        => 'companyname',
										'id'          => 'companyname',
										'value'       => (isset($get_Fields[0]['companyname']))?$get_Fields[0]['companyname']:'',
										'class'		=>'required')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'<label>Mobile Number :</label>',
				  'field'=>form_input(array(
									  'name'        => 'mobilenumber',
										'id'          => 'mobilenumber',
										'value'       => (isset($get_Fields[0]['mobilenumber']))?$get_Fields[0]['mobilenumber']:'',
										'class'		=>'required')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'<label>Company Phone :</label>',
				  'field'=>form_input(array(
									  'name'        => 'companyphone',
										'id'          => 'companyphone',
										'value'       => (isset($get_Fields[0]['companyphone']))?$get_Fields[0]['companyphone']:'',
										'class'		=>'')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'<label>Industry :</label>',
				  'field'=>form_input(array(
									  'name'        => 'industry',
										'id'          => 'industry',
										'value'       => (isset($get_Fields[0]['industry']))?$get_Fields[0]['industry']:'',
										'class'		=>'requried')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'<label>Email :</label>',
				  'field'=>form_input(array(
									  'name'        => 'partneremail',
										'id'          => 'partneremail',
										'value'       => (isset($get_Fields[0]['email']))?$get_Fields[0]['email']:'',
										'class'		=>'required email')
									));
							array_push($formFields,$cf);
							if($id==""){
		$cf=array('label'=>'<label>Confirm Email :</label>',
				  'field'=>form_input(array(
									  'name'        => 'confirmemail',
										'id'          => 'confirmemail',
										'value'       => (isset($get_Fields[0]['email']))?$get_Fields[0]['email']:'',
										'class'		=>'required email')
									));
							array_push($formFields,$cf);
						}
		$cf=array('label'=>'<label>'.$this->lang->line('partneraddress').' :</label>',
				  'field'=>form_textarea(array(
									  'name'        => 'address',
										'id'          => 'address',
										'value'       => (isset($get_Fields[0]['address']))?$get_Fields[0]['address']:'',)
										));
						array_push($formFields,$cf);
		$cf=array('label'=>'<label>City :</label>',
				  'field'=>form_input(array(
									  'name'        => 'city',
										'id'          => 'city',
										'value'       => (isset($get_Fields[0]['city']))?$get_Fields[0]['city']:'',
										'class'		=>'required')
									));
							array_push($formFields,$cf);						
		$cf=array('label'=>'<label>State :</label>',
				  'field'=>form_input(array(
									  'name'        => 'state',
										'id'          => 'state',
										'value'       => (isset($get_Fields[0]['state']))?$get_Fields[0]['state']:'',
										'class'		=>'required')
									));
							array_push($formFields,$cf);						
		$cf=array('label'=>'<label>Pincode :</label>',
				  'field'=>form_input(array(
									  'name'        => 'pincode',
										'id'          => 'pincode',
										'value'       => (isset($get_Fields[0]['pincode']))?$get_Fields[0]['pincode']:'',
										'class'		=>'required')
									));
							array_push($formFields,$cf);						
		$cf=array('label'=>'<label>Company Pan Card :</label>',
				  'field'=>form_input(array(
									  'name'        => 'pancard',
										'id'          => 'pancard',
										'value'       => (isset($get_Fields[0]['pancard']))?$get_Fields[0]['pancard']:'',
										'class'		=>'required')
									));
							array_push($formFields,$cf);						
		$cf=array('label'=>'<label>Photo copy of Pan Card :</label>',
				  'field'=>(isset($get_Fields[0]['photocopy']) && $get_Fields[0]['photocopy']!="")?img('../../qrcode/company_logos/'.$get_Fields[0]['photocopy'])."<a href='Masteradmin/del_photocopy/".$id."'>x</a>":
				  				form_input(array(
									  'name'        => 'photocopy',
										'id'          => 'photocopy',
										'type' 		=>'file',
										'value'=>(isset($get_Fields[0]['photocopy']))?$get_Fields[0]['photocopy']:'',
										'class'		=>'')
									));
							array_push($formFields,$cf);						
		$cf=array('label'=>'<label>Company Bank Name :</label>',
				  'field'=>form_input(array(
									  'name'        => 'bank',
										'id'          => 'bank',
										'value'       => (isset($get_Fields[0]['bank']))?$get_Fields[0]['bank']:'',
										'class'		=>'required')
									));
							array_push($formFields,$cf);						
		$cf=array('label'=>'<label>Company Bank Address:</label>',
				  'field'=>form_textarea(array(
									  'name'        => 'bankaddress',
										'id'          => 'bankaddress',
										'value'       => (isset($get_Fields[0]['bankaddress']))?$get_Fields[0]['bankaddress']:'',
										'class'		=>'required')
									));
							array_push($formFields,$cf);		
		$cf=array('label'=>'<label>Company Account Number :</label>',
				  'field'=>form_input(array(
									  'name'        => 'accno',
										'id'          => 'accno',
										'value'       => (isset($get_Fields[0]['accno']))?$get_Fields[0]['accno']:'',
										'class'		=>'required')
									));
									array_push($formFields,$cf);
		
		$cf=array('label'=>'<label>Company Address Proof submitted :</label>',
				  'field'=>form_checkbox(array(
									  'name'        => 'addressproof',
										'id'          => 'addressproof',
										'value'	=>'1'
										,'checked'=>(isset($get_Fields[0]['addressproof']) && $get_Fields[0]['addressproof']=='1')? TRUE: false
										)
									));
							array_push($formFields,$cf);	
		$cf=array('label'=>'<label>Company Logo :</label>',
				  'field'=>(isset($get_Fields[0]['logo']) && $get_Fields[0]['logo']!="")?img('../../qrcode/company_logos/'.$get_Fields[0]['logo'])."<a href='Masteradmin/del_logo/".$id."'>x</a>":				  
								form_input(array(
									  'name'        => 'logo',
										'id'          => 'logo',
										'type' 		=>'file',
										'value'=>(isset($get_Fields[0]['logo']))?$get_Fields[0]['logo']:'',
										'class'		=>'')
									));
									array_push($formFields,$cf);							
		$cf=array('label'=>'<label>'.$this->lang->line('Domain').' :</label>',
				  'field'=>($id!="")?
							form_input(array(
									  'name'        => 'domainname',
										'id'          => 'domainname',
										'value'       => (isset($get_Fields[0]['domain_name']))?$get_Fields[0]['domain_name']:'',
										'class'		=>'required',
										'readonly' =>'readonly'
										)):form_input(array(
									  'name'        => 'domainname',
										'id'          => 'domainname',
										'value'       => '',
										'class'		=>'required',
										))
									);
									array_push($formFields,$cf);
									if($id==""){
		$cf=array('label'=>'<label>Confirm Domain Name :</label>',
				  'field'=>form_input(array(
									  'name'        => 'cdomainname',
										'id'          => 'cdomainname',
										'value'       => '',
										'class'		=>'required',
										)));
									array_push($formFields,$cf);
								}
		$cf=array('label'=>($id!="")?'':'<label>'.$this->lang->line('level_password').' :</label>',
				  'field'=>($id!="")?
							form_hidden(array(
									  'name'        => 'password',
										'id'          => 'password',
										'value'       => (isset($get_Fields[0]['password']))?$get_Fields[0]['password']:'',
										'class'		=>'required',
										'readonly' =>'readonly'
										)):form_password(array(
									  'name'        => 'password',
										'id'          => 'password',
										'value'       => '',
										'class'		=>'required',
										))
										
										
									);
										
						array_push($formFields,$cf);
		$cf=array('label'=>($id!="")?'':'<label>Confirm Password:</label>',
				  'field'=>($id!="")?
							'':form_password(array(
									  'name'        => 'cpassword',
										'id'          => 'cpassword',
										'value'       => '',
										'class'		=>'required',
										))
										
										
									);
										
						array_push($formFields,$cf);
						
						
			$data['form'] = array(
					'open'=>form_open_multipart('Masteradmin/Addpartner/'.$id,array('name'=>'Addpartner','class'=>'form','id'=>'Addpartner','method'=>'post'),array('patner_id'=>$id)),
					'fields'=>$formFields,
					'fields1'=>$formFields1,
					
					'close'=>form_close(),
					'feature_list'=>$this->mastermodel->feature_manage(),
					'subfeature_list'=>$this->mastermodel->sub_featuremanage(),
					'partnerfeatures'=>$this->mastermodel->partner_features($id),
				);
		$this->mastermodel->viewLayout('form_view3',$data);
	}
	function checkfilextension($str){
			echo $str;exit;	
			$this->form_validation->set_message('check_captcha', 'The '.$str.' is not valid security code');
			return FALSE;
		
	}
	function checkDomain_exists(){
		$res=$this->mastermodel->Domain_Check();
		echo $res;
	}
	function Deletepartner($id){
		$access=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Partner',2);
		if(!$access)redirect('Masteradmin/access_denied');
		$res=$this->mastermodel->delete_partner($id);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', $this->lang->line('delete_succ'));
		redirect('Masteradmin/Listpartner');
	}
	function UnDeletepartner($id){
		$access=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Partner',2);
		if(!$access)redirect('Masteradmin/access_denied');
		$res=$this->mastermodel->undelete_partner($id);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', $this->lang->line('undelete_succ'));
		redirect('Masteradmin/Listpartner');
	}
	Function ChangePartnerS($Partner_id){
		$access=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Partner',3);
		if(!$access)redirect('Masteradmin/access_denied');
		$changeStatus=$this->mastermodel->ChangePartnerStatus($Partner_id);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', $this->lang->line('status_update'));
		redirect('Masteradmin/Listpartner');
	}
	function checkLanding(){
		print_r($_POST);exit;
	}
	function addtopartner($page=''){
		$data['module']['title'] ="Assign Pri To Partners";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '15';
		$header = array('#'
						,$this->lang->line('level_Prinumbers')
						,$this->lang->line('level_partnername')
						,$this->lang->line('level_Action')
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->mastermodel->assignedParnterPris($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;
		foreach ($emp_list['data'] as $item){
			$rec[] = array(
				$i,
				$item['prinumber'],
				$item['firstname'].$item['lastname']
				,'
					<a href="'.site_url('Masteradmin/partnerpri/'.$item['prinumber']).'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive">
					<span title="Add" class="glyphicon glyphicon-plus-sign"></span>
				  </a>'
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
			
		//$data['itemlist'] = $this->groupmodel->getgrouplist($bid,$ofset,$limit);
		
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/addtopartner/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		//$data['addlinks']="group/add_group";		
		$data['paging'] = $this->pagination->create_links();
		//$data['paging'] =  pagination($limit, $page, $start, $total_pages, $targetpage);
		$this->mastermodel->data['html']['title'] .= " | Manage UnAssinged Numbers";
		$data['links']='<a href="Masteradmin/AssignMobileNumber/"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>';
		$formFields1 = array();
		$cf=array('label'=>'<label for="groupname">'.$this->lang->line('level_groupname').' : </label>',
				  'field'=>form_input(array(
									  'name'        => 'groupname',
										'id'          => 'groupname',
										'value'       => $this->session->userdata('groupname'))));
						array_push($formFields1,$cf);
						
		$this->mastermodel->data['links'] = '<a href="group/add_group"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>';
		//$fieldset = $this->configmodel->getFields('3');
		$formFields = array();
		$formFields[] = array(
				'label'=>'<label for="f">PriNumber : </label>',
				'field'=>form_input(array(
						'name'      => 'prinumber',
						'id'        => 'prinumber',
						'value'     => $this->session->userdata('prinumber')
						))
						);
		$data['form'] = array(
							'open'=>form_open_multipart('Masteradmin/addtopartner/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'adv_search'=>array(),
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->mastermodel->viewLayout('list_view',$data);
		
	}
	function partnerpri($bid=''){
		if($this->input->post('update_system')){
					$res=$this->mastermodel->pritopartner($this->uri->segment('3'));
					redirect('Masteradmin/addtopartner');
			}	
		$formFields = array();
		$cf=array('label'=>'<label>Partner :</label>',
				  'field'=>form_dropdown('partner_id',$this->mastermodel->getpartners(),'','id="partner_id" class="auto"'));
							array_push($formFields,$cf);
		$data=array('res'=>$this->mastermodel->getpartners());
		$data['module']['title'] ="Assign Pri To Partners";
		$data['form'] = array(
		            'form_attr'=>array('action'=>'Masteradmin/partnerpri/'.$bid,'name'=>'Addpartnerpri','id'=>'Addpartnerpri','enctype'=>"multipart/form-data"),
					//~ 'open'=>form_open_multipart('Masteradmin/partnerpri/'.$bid,array('name'=>'Addpartnerpri','class'=>'form','id'=>'Addpartnerpri','method'=>'post')),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$this->load->view('form_view',$data);
	}
	function partnerDenied($id,$pass){
		$res=$this->mastermodel->partnerDenied($id,$pass);
		if($res!=0){
			redirect('site/pdenied');
		}else{
			redirect('site/error');
		}
	}
	function partneraccept($id,$pass){
		$res=$this->mastermodel->partneraccept($id,$pass);
		if($res!=0){
			redirect('site/pthanks');
		}else{
			redirect('site/error');
		}	
	}
	function addbusinessuser($id=''){
		$access=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Business',($id!='')?1:0);
		if(!$access)redirect('Masteradmin/access_denied');
		if(isset($_POST)){
			$this->form_validation->set_rules('login_businessname', 'Business Name', 'required|min_length[4]|max_length[32]|alpha_numeric');
			$this->form_validation->set_rules('cname', 'Contact Name', 'required|min_length[4]|max_length[32]|alpha_numeric');
			$this->form_validation->set_rules('cemail', 'Contact Email', 'required|min_length[4]|max_length[50]|valid_email');
			$this->form_validation->set_rules('login_username', 'Confirm Email', 'required|min_length[4]|max_length[50]|valid_email|matches[cemail]');
			$this->form_validation->set_rules('cphone', 'Contact Phone', 'required|min_length[10]|max_length[50]|numeric');
			$this->form_validation->set_rules('bphone', 'Business Phone', 'required|min_length[10]|max_length[50]|numeric');
			$this->form_validation->set_rules('baddress', 'Business Address', 'required|min_length[4]|max_length[150]');
			$this->form_validation->set_rules('baddress1', 'Business Address1', 'min_length[4]|max_length[150]');
			$this->form_validation->set_rules('language', 'Language', 'required');
			$this->form_validation->set_rules('city', 'city', 'required|min_length[4]|max_length[50]|alpha');
			$this->form_validation->set_rules('state', 'state', 'required|min_length[2]|max_length[50]|alpha');
			$this->form_validation->set_rules('country', 'country', 'required|min_length[4]|max_length[50]|alpha');
			$this->form_validation->set_rules('locality', 'locality', 'required|min_length[3]|max_length[50]|alpha');
			$this->form_validation->set_rules('zipcode', 'zipcode', 'required|min_length[6]|max_length[10]|numeric');
			$this->form_validation->set_rules('login_username', 'Username', 'required|min_length[6]|max_length[50]|valid_email|matches[cemail]|callback_username_check');
			if($id==""){
			$this->form_validation->set_rules('login_password', 'Password', 'required|min_length[5]|max_length[10]');
			$this->form_validation->set_rules('cpassword', 'Confirm Password', 'required|min_length[5]|max_length[10]|matches[login_password]');
			}
			$this->form_validation->set_rules('waddress', 'Website Address', 'valid_url');
			if(!$this->form_validation->run() == FALSE){	
				
					if($id!=""){
						$update=$this->mastermodel->AddBusinessUser($id);
						$this->session->set_flashdata('msgt', 'success');
						$this->session->set_flashdata('msg', $this->lang->line('updatesuccessmsg'));
						redirect('Masteradmin/Businesslist');
						
					}else{	
						$rs=$this->mastermodel->AddBusinessUser();
						$this->session->set_flashdata('msgt', 'success');
						$this->session->set_flashdata('msg', $this->lang->line('successmsg'));
						redirect('Masteradmin/Businesslist');
					}
				}	
		}
		$this->mastermodel->data['html']['title'] .= " | Add Business User";
		$data['module']['title'] ="Add Business User";
			$get_Fields=$this->mastermodel->get_busValues($id);
		$formFields = array();$formFields1 = array();
		
		//~ $cf=array('label'=>'Partner Name',
				  //~ 'field'=>form_dropdown("partner",$this->mastermodel->Listpartners(),(isset($get_Fields[0]['domain_id']))?$get_Fields[0]['domain_id']:'',$jss));
							//~ array_push($formFields,$cf);
		$cf=array('label'=>'<label class="col-sm-4 text-right">Business Name : </label>',
				  'field'=>form_input(array(
									  'name'        => 'login_businessname',
										'id'          => 'login_businessname',
										'value'       =>  (isset($get_Fields[0]['businessname']))?$get_Fields[0]['businessname']:'',
										'class'		=>'required form-control')
									));
							array_push($formFields,$cf);
		$cf=array('label'=> '<label class="col-sm-4 text-right">Contact Name :</label>',
				  'field'=>form_input(array(
									  'name'        => 'cname',
										'id'          => 'cname',
										'value'       => (isset($get_Fields[0]['contactname']))?$get_Fields[0]['contactname']:'',
										'class'		=>'required form-control')
									));
							array_push($formFields,$cf);
					if($id!=""){
		$cf=array('label'=>'<label class="col-sm-4 text-right">Contact Email :</label>',
				  'field'=>form_input(array(
									  'name'        => 'cemail',
										'id'          => 'cemail',
										'value'       =>(isset($get_Fields[0]['contactemail']))?$get_Fields[0]['contactemail']:'',
										'class'		=>'required form-control',
										'readonly'=>'true')
									));
		}else{
		$cf=array('label'=>'<label class="col-sm-4 text-right">Contact Email :</label>',
				  'field'=>form_input(array(
									  'name'        => 'cemail',
										'id'          => 'cemail',
										'value'       =>(isset($get_Fields[0]['contactemail']))?$get_Fields[0]['contactemail']:'',
										'class'		=>'required form-control')
									));
								}
							array_push($formFields,$cf);
		$cf=array('label'=>'<label class="col-sm-4 text-right">Confirm Email :</label>',
				  'field'=>form_input(array(
									  'name'        => 'login_username',
										'id'          => 'login_username',
										'value'       =>(isset($get_Fields[0]['contactemail']))?$get_Fields[0]['contactemail']:'',
										'class'		=>'required form-control')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'<label class="col-sm-4 text-right">Web Address :</label>',
				  'field'=>form_input(array(
									  'name'        => 'waddress',
										'id'          => 'waddress',
										'value'       => (isset($get_Fields[0]['webaddress']))?$get_Fields[0]['webaddress']:'',
										'class'		=>'form-control')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'<label class="col-sm-4 text-right">Contact Phone :</label>',
				  'field'=>form_input(array(
									  'name'        => 'cphone',
										'id'          => 'cphone',
										'value'       =>(isset($get_Fields[0]['contactphone']))?$get_Fields[0]['contactphone']:'',
										'class'		=>'required form-control')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'<label class="col-sm-4 text-right">Business Phone :</label>',
				  'field'=>form_input(array(
									  'name'        => 'bphone',
										'id'          => 'bphone',
										'value'       => (isset($get_Fields[0]['businessphone']))?$get_Fields[0]['businessphone']:'',
										'class'		=>'required form-control')
									));
							array_push($formFields,$cf);
		
		$cf=array('label'=>'<label class="col-sm-4 text-right">Business Address :</label>',
				  'field'=>form_textarea(array(
									  'name'        => 'baddress',
										'id'          => 'baddress',
										'value'       => (isset($get_Fields[0]['businessaddress']))?$get_Fields[0]['businessaddress']:'',
										'class'		=>'required form-control')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'<label class="col-sm-4 text-right">Business Address1 :</label>',
				  'field'=>form_textarea(array(
									  'name'        => 'baddress1',
										'id'          => 'baddress1',
										'value'       => (isset($get_Fields[0]['businessaddress1']))?$get_Fields[0]['businessaddress1']:'',
										'class'		=>'form-control')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'<label class="col-sm-4 text-right">City :</label>',
				  'field'=>form_input(array(
									  'name'        => 'city',
										'id'          => 'city',
										'value'       => (isset($get_Fields[0]['city']))?$get_Fields[0]['city']:'',
										'class'		=>'required form-control')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'<label class="col-sm-4 text-right">State :</label>',
				  'field'=>form_input(array(
									  'name'        => 'state',
										'id'          => 'state',
										'value'       => (isset($get_Fields[0]['state']))?$get_Fields[0]['state']:'',
										'class'		=>'required form-control')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'<label class="col-sm-4 text-right">Country :</label>',
				  'field'=>form_input(array(
									  'name'        => 'country',
										'id'          => 'country',
										'value'       => (isset($get_Fields[0]['country']))?$get_Fields[0]['country']:'',
										'class'		=>'required form-control')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'<label class="col-sm-4 text-right">Locality :</label>',
				  'field'=>form_input(array(
									  'name'        => 'locality',
										'id'          => 'locality',
										'value'       =>(isset($get_Fields[0]['locality']))?$get_Fields[0]['locality']:'',
										'class'		=>'required form-control')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'<label class="col-sm-4 text-right">Zipcode :</label>',
				  'field'=>form_input(array(
									  'name'        => 'zipcode',
										'id'          => 'zipcode',
										'value'       => (isset($get_Fields[0]['zipcode']))?$get_Fields[0]['zipcode']:'',
										'class'		=>'required form-control')
									));
							array_push($formFields,$cf);
							$js = 'id="language" class="required form-control"';
		$cf=array('label'=>'<label class="col-sm-4 text-right">Language :</label>',
				  'field'=>form_dropdown("language",$this->profilemodel->get_languages(),(isset($get_Fields[0]['language']))?$get_Fields[0]['language']:'',$js));
							array_push($formFields,$cf);
		$cf=array('label'=>'<label class="col-sm-4 text-right">Password :</label>',
				  'field'=>form_password(array(
									  'name'        => 'login_password',
										'id'          => 'login_password',
										'value'       => '',
										'class'		=>'required form-control')
									));
							($id=="")?array_push($formFields,$cf):'';
		$cf=array('label'=>'<label class="col-sm-4 text-right">Confirm Password :</label>',
				  'field'=>form_password(array(
									  'name'        => 'cpassword',
										'id'          => 'cpassword',
										'value'       => '',
										'class'		=>'required form-control')
									));
							($id=="")?array_push($formFields,$cf):'';
			$array=array(""=>"select","1"=>"partner","2"=>"executive");			
			$array1=array(""=>"select");			
			$jss = 'id="relatedto" class="required form-control"';		
			$jss1 = 'id="emplp" class="required form-control"';		
							
		$cf=array('label'=>'<label class="col-sm-4 text-right">Introducer :</label>',
				  'field'=>form_dropdown("relatedto",$array,'',$jss));
							($id=="")?array_push($formFields,$cf):'';					
		$cf=array('label'=>'<label class="col-sm-4 text-right">Introduce By :</label>',
				  'field'=>form_dropdown("emplp",$array1,'',$jss1));
							($id=="")?array_push($formFields,$cf):'';					
			$cf=array('label'=>'<label class="col-sm-4 text-right">Description :</label>',
				  'field'=>form_textarea(array(
									  'name'        => 'desc',
										'id'          => 'desc',
										'value'       => '',
										'class'		=>'form-control')
									));
							($id=="")?array_push($formFields,$cf):'';					
							
							
							
			$data['form'] = array(
			        'form_attr'=>array('action'=>'Masteradmin/addbusinessuser/'.$id,'name'=>'addbusinessuser','enctype'=>"multipart/form-data",'id'=>'addbusinessuser'),
					//~ 'open'=>form_open_multipart('Masteradmin/addbusinessuser/'.$id,array('name'=>'addbusinessuser','class'=>'form','id'=>'addbusinessuser','method'=>'post'),array('bid'=>$id)),
					'hidden'=>array('bid'=>$id),
					'fields'=>$formFields,
					'fields1'=>$formFields1,
					'close'=>form_close()
				);
		$this->mastermodel->viewLayout('form_view',$data);
	}
	function checkBusinessuser(){
		$res=$this->mastermodel->CheckBusinessUser();
		echo $res;
	}
	function passwordreset($bid){
		$res=$this->mastermodel->password_reset($bid);
		if($res){
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg',"Password reseted Successfully");
			redirect('Masteradmin/Businesslist');
			
		}else{
			$this->session->set_flashdata('msgt', 'error');
			$this->session->set_flashdata('msg', "Error While Resting the Passsword");
			redirect('Masteradmin/Businesslist');
			
		}
	}
	function Billconfig($bid=''){
		if(isset($_POST)){
			
			$this->form_validation->set_rules('businessuser', 'Business Name', 'required|is_natural');
			//$this->form_validation->set_rules('paycycle', 'PayCycle', 'required|is_natural');
			$this->form_validation->set_rules('billgenerate', 'Billgenerate Date', 'required');
			//$this->form_validation->set_rules('duedate', 'due Date', 'required|is_natural');
			$this->form_validation->set_rules('disamount', 'Discount Amount', 'required|numeric');
			//$this->form_validation->set_rules('rental', 'Rental', 'required|numeric');
			if(!$this->form_validation->run() == FALSE){	
				
					$res=$this->mastermodel->addbill_config($bid);
					$this->session->set_flashdata('msgt', 'success');
					if($bid!=""){
						$this->session->set_flashdata('msg', "Configuration Updated sucessfully");
					}else{
						$this->session->set_flashdata('msg', $this->lang->line('successmsg'));
					}
					redirect('Masteradmin/listconfig');
			
			}
			
		}
		$formFields=array();
		$formFields1=array();
		$data['module']['title'] ="Bill Configuration";
		$res=$this->mastermodel->get_businessusers();
		$tax=$this->mastermodel->tax_list();
		$bconfig=$this->mastermodel->BillConfig_user($bid);
		$options=array(""=>"select");
		foreach($res as $r){
			$options[$r['bid']]=$r['businessname'];
		}
		$options1=array();
		foreach($tax as $t){
			$options1[$t['taxid']]=$t['percentage'];
		}
		$days=array();
		for($i=1;$i<=30;$i++){
			$days[$i]= $i .(($i>1)?' Days':' Day');
		}
		$paycycle=array("1"=>"1 Month","3"=>"Quaterly","6"=>"Half yearly","12"=>"Yearly");
		$js = 'id="businessuser" class="required"';
		$js1 = 'id="paycycle" class="required"';
		$js2 = 'id="duedate" class="required"';
		$js3 = 'id="tax" class="required"';
		$cf=array('label'=>'Business Name',
				  'field'=>form_dropdown("businessuser",$options,(isset($bconfig->bid))?$bconfig->bid:'',$js));
							array_push($formFields,$cf);
		//~ $cf=array('label'=>'Paycycle',
				  //~ 'field'=>form_dropdown("paycycle",$paycycle,'',$js1));
							//~ array_push($formFields,$cf);
		$cf=array('label'=>'Billgenerate Date',
				  'field'=>form_input(array(
							  'name'        => 'billgenerate',
								'id'          => 'billgenerate',
								'value'       => '',
								'readonly'=>'true',	
								'class'		=>'required datepicker')
									));
							array_push($formFields,$cf);								
		//~ $cf=array('label'=>'Due Date',
				  //~ 'field'=>form_dropdown("duedate",$days,'',$js2));
							//~ array_push($formFields,$cf);
		//~ 
		$checked='';$checked1='';$disval='';
		if(isset($bconfig->discount_type)){
			$checked=($bconfig->discount_type!=1)?'checked':'';
			$checked1=($bconfig->discount_type!=0)?'checked':'';
			$disval=($bconfig->discount_type!=1)?$bconfig->discount_amount:$bconfig->discount_percentage;
		}else{
			$checked='checked';
		}
		$cf=array('label'=>'Discount Type',
				  'field'=>'<input type="radio" name="distype" id="distype" value="2" '.$checked.' />Fixed
							<input type="radio" name="distype" id="distype" value="1" '.$checked1.'/>Percentage'
								);
				array_push($formFields,$cf);
		$cf=array('label'=>'Discount Amount',
				  'field'=>form_input(array(
							  'name'        => 'disamount',
								'id'          => 'disamount',
								'value'       => $disval,
								'class'		=>'required')
									));
							array_push($formFields,$cf);					
		//~ $cf=array('label'=>'Rental',
				  //~ 'field'=>form_input(array(
							  //~ 'name'        => 'rental',
								//~ 'id'          => 'rental',
								//~ 'value'       => '',
								//~ 'class'		=>'required')
									//~ ));
							//~ array_push($formFields,$cf);					
		$cf=array('label'=>'Tax',
				  'field'=>form_dropdown("tax",$options1,'',$js3));
							array_push($formFields,$cf);					
							
							
															
		$data['form'] = array(
		           'form_attr'=>array('action'=>'Masteradmin/Billconfig/'.$bid,'name'=>'billconfig','id'=>'billconfig','enctype'=>"multipart/form-data"),
					//~ 'open'=>form_open_multipart('Masteradmin/Billconfig/'.$bid,array('name'=>'billconfig','class'=>'form','id'=>'billconfig','method'=>'post'),array('bid'=>'')),
					'fields'=>$formFields,
					'fields1'=>$formFields1,
					'close'=>form_close()
				);
		$this->mastermodel->viewLayout('form_view',$data);
	}
	function bills(){
		
		$data['module']['title'] ="Generated Bills";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('Bill Id'
						,'Business Name'
						,'Amount'
						,'Generated Date'
						,'Due Date'
						,'Action'
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->mastermodel->generated_bills($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;
		foreach ($emp_list['data'] as $item){
			$active=($item['latest']!=0)?'<a href="Masteradmin/billpayment/'.$item['bill_id'].'"><img src="'.site_url('system/application/img/icons/payment.png').'"  /></a>':'';
			
			$rec[] = array(
				$item['bill_id'],
				$item['businessname']
				,$item['netamount']
				,$item['bill_generate_date']
				,$item['due_date']
				,'<a href="Masteradmin/pdffile/'.$item['bill_id'].'"><img src="'.site_url('system/application/img/icons/pdf.png').'" title="pdf" width="16" height="16" /></a>'.''.$active.'<a href="Masteradmin/listpayments/'.$item['bill_id'].'"><img src="'.site_url('system/application/img/icons/paid_Money.png').'"/></a>'
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('partner/managePriList/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		//$data['addlinks']="group/add_group";		
		$data['paging'] = $this->pagination->create_links();
		$this->mastermodel->data['html']['title'] .= " | Manage PriNumbers";
		$data['links']='<a href=""><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>';
		$formFields1 = array();
		$cf=array('label'=>'<label for="groupname">'.$this->lang->line('level_groupname').' : </label>',
				  'field'=>form_input(array(
									  'name'        => 'groupname',
										'id'          => 'groupname',
										'value'       => $this->session->userdata('groupname'))));
						array_push($formFields1,$cf);
						
		$this->mastermodel->data['links'] = '<a href="group/add_group"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>';
		//$fieldset = $this->configmodel->getFields('3');
		$formFields = array();
		$formFields[] = array(
				'label'=>'<label for="f">BusinessName : </label>',
				'field'=>form_input(array(
						'name'      => 'bname',
						'id'        => 'bname',
						'value'     => $this->session->userdata('bname')
						))
						);
		$formFields[] = array(
				'label'=>'<label for="f">Date From : </label>',
				'field'=>form_input(array(
						'name'      => 'datefrom',
						'id'        => 'datefrom',
						'value'     => $this->session->userdata('datefrom'),
						'class'		=>'datepicker_leads'
						))
						);
		$formFields[] = array(
				'label'=>'<label for="f">Date To: </label>',
				'field'=>form_input(array(
						'name'      => 'dateto',
						'id'        => 'dateto',
						'value'     => $this->session->userdata('dateto'),
						'class'		=>'datepicker_leads'
						))
						);
			
		$data['form'] = array(
							'open'=>form_open_multipart('Masteradmin/bills/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'adv_search'=>array(),
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->mastermodel->viewLayout('list_view',$data);
	}
	function businessbillconfig($bid){
		$res=$this->mastermodel->check_business_user($bid);
		if($res!=1){
			$this->form_validation->set_message('businessbillconfig', 'Already Bill is Configured to this Business User');
			return FALSE;
		}else{
			return true;
			}
	}
	function pdffile($bill){
		$bill_report=$this->mastermodel->bill_pdf($bill);
	}
	function billpayment($bill_id){
		if($this->input->post('update_system')){
			$res=$this->mastermodel->billpayment($bill_id);
			redirect('Masteradmin/bills');
		}
		$formFields=array();
		$formFields1=array();
		$data['module']['title'] ="Bill Payment";
		$options=array("cheque"=>"cheque","cash"=>"cash");
		$options1=array("1"=>"cleared","2"=>"uncleared");
		$js = 'id="paymode" class="required"';
		$js1 = 'id="status" class="required"';
		
		$billdetails=$this->mastermodel->getBilldetails($bill_id);
		$cf=array('label'=>'Business Name ',
				  'field'=>": ".$billdetails[0]['businessname']);
							array_push($formFields,$cf);
		$cf=array('label'=>'Bill Amount ',
				  'field'=>": ".$billdetails[0]['netamount']);
							array_push($formFields,$cf);
		$cf=array('label'=>'Bill Generated Date ',
				  'field'=>": ".$billdetails[0]['bill_generate_date']);
							array_push($formFields,$cf);
		$cf=array('label'=>'Billing Period  ',
				  'field'=>": ".$billdetails[0]['billing_form']."  To ".$billdetails[0]['billing_to']);
							array_push($formFields,$cf);
		$cf=array('label'=>'Bill Due Date ',
				  'field'=>": ".$billdetails[0]['due_date']);
							array_push($formFields,$cf);
		$cf=array('label'=>'Due Amount',
				  'field'=>": ".$billdetails[0]['due_amount']);
							array_push($formFields,$cf);
	   
		$cf=array('label'=>'Payment',
				  'field'=>": ".form_input(array(
						'name'      => 'payment',
						'id'        => 'payment',
						'value'     => '',
						'class'=>'required'
						))."  <input type='button' id='addp' name='addp' value='addpayment'/>"
						
						);
						array_push($formFields,$cf);
		$cf=array('label'=>'Mode',
				  'field'=>": ".form_dropdown("paymode",$options,'',$js)
						
						);
		array_push($formFields,$cf);
		$cf=array('label'=>'status',
				  'field'=>": ".form_dropdown("status",$options1,'',$js1)
						
						);
		array_push($formFields,$cf);
		$cf=array('label'=>'cheque No',
				  'field'=>": ".form_input(array(
						'name'      => 'cheno',
						'id'        => 'cheno',
						'value'     => ''
						))
						
						);
		array_push($formFields,$cf);
		$cf=array('label'=>'Bank Name',
				  'field'=>": ".form_input(array(
						'name'      => 'bname',
						'id'        => 'bname',
						'value'     => ''
						))
						
						);
		array_push($formFields,$cf);
		$cf=array('label'=>'Branch Name',
				  'field'=>": ".form_input(array(
						'name'      => 'brname',
						'id'        => 'brname',
						'value'     => ''
						))
						
						);
		array_push($formFields,$cf);
		
	   
		$data['form'] = array(
		            'form_attr'=>array('action'=>'Masteradmin/billpayment/'.$bill_id,'name'=>'billpayment','id'=>'billpayment','enctype'=>"multipart/form-data"),
					//~ 'open'=>form_open_multipart('Masteradmin/billpayment/'.$bill_id,array('name'=>'billpayment','class'=>'form','id'=>'billpayment','method'=>'post'),array('bid'=>$billdetails[0]['bid'],'payid'=>0,'netamt'=>$billdetails[0]['netamount'],'billid'=>$bill_id)),
					'fields'=>$formFields,
					'fields1'=>$formFields1,
					'close'=>form_close()
				);
		$this->mastermodel->viewLayout('form_view',$data);
	}
	function listpayments($bill_id){
		$data['module']['title'] ="Payment Details";
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '30';
		$header = array('Bill Id'
						,'Paid Amount'
						,'Payment Mode'
						,'Cheque No'
						,'Bankname'
						,'Action'
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->mastermodel->paymentsList($bill_id,$ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;
		foreach ($emp_list['data'] as $item){
			$rec[] = array(
				$item['payment_id'],
				$item['payment_amount']
				,$item['payment_mode']
				,$item['chequeno_dd']
				,$item['bankname']
				,'<a href="Masteradmin/editpayment/'.$item['bill_id'].'/'.$item['payment_id'].'"><span title="Edit" class="fa fa-edit"></span></a>'
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('payment/listpayments/'.$bill_id)
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>4					
				));
		//$data['addlinks']="group/add_group";		
		$data['paging'] = $this->pagination->create_links();
		$this->mastermodel->data['html']['title'] .= " | Manage PriNumbers";
		$data['links']='<a href="partner/addPrinumber/"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>';
		$formFields1 = array();
		$cf=array('label'=>'<label for="groupname">'.$this->lang->line('level_groupname').' : </label>',
				  'field'=>form_input(array(
									  'name'        => 'groupname',
										'id'          => 'groupname',
										'value'       => $this->session->userdata('groupname'))));
						array_push($formFields1,$cf);
						
		$this->mastermodel->data['links'] = '<a href="group/add_group"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>';
		//$fieldset = $this->configmodel->getFields('3');
		$formFields = array();
		$formFields[] = array(
				'label'=>'<label for="f">PriNumber : </label>',
				'field'=>form_input(array(
						'name'      => 'prinumber',
						'id'        => 'prinumber',
						'value'     => $this->session->userdata('prinumber')
						))
						);
		$formFields[] = array(
				'label'=>'<label for="f">Landing Number : </label>',
				'field'=>form_input(array(
						'name'      => 'landing_number',
						'id'        => 'landing_number',
						'value'     => $this->session->userdata('landing_number')
						))
						);
		$formFields[] = array(
				'label'=>'<label for="f">Business Name : </label>',
				'field'=>form_input(array(
						'name'      => 'businessname',
						'id'        => 'businessname',
						'value'     => $this->session->userdata('businessname')
						))
						);
			
		$data['form'] = array(
							'open'=>form_open_multipart('Masteradmin/listpayments/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'adv_search'=>array(),
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->mastermodel->viewLayout('list_view',$data);
	}
	function editpayment($bill_id,$payment_id){
		if($this->input->post('update_system')){
			$res=$this->mastermodel->updatepayment($payment_id);
			redirect("Masteradmin/listpayments/".$bill_id);
		}
		$formFields=array();
		$formFields1=array();
		$data['module']['title'] ="Edit Paid Bill";
		$options=array("cheque"=>"cheque","cash"=>"cash");
		$options1=array("cleared"=>"cleared","uncleared"=>"uncleared");
		$js = 'id="paymode" class="required"';
		$js1 = 'id="status" class="required"';
		$billdetails=$this->mastermodel->editbill($payment_id,$bill_id);
		$cf=array('label'=>'Payment',
				  'field'=>": ".form_input(array(
						'name'      => 'payment',
						'id'        => 'payment',
						'value'     => $billdetails[0]['payment_amount'],
						'class'=>'required'
						)));
						array_push($formFields,$cf);
		$cf=array('label'=>'Mode',
				  'field'=>": ".form_dropdown("paymode",$options,$billdetails[0]['payment_mode'],$js)
						
						);
		array_push($formFields,$cf);
		$cf=array('label'=>'status',
				  'field'=>": ".form_dropdown("status",$options1,$billdetails[0]['status'],$js1)
						
						);
		array_push($formFields,$cf);
		$cf=array('label'=>'cheque No',
				  'field'=>": ".form_input(array(
						'name'      => 'cheno',
						'id'        => 'cheno',
						'value'     => $billdetails[0]['chequeno_dd']
						))
						
						);
		array_push($formFields,$cf);
		$cf=array('label'=>'Bank Name',
				  'field'=>": ".form_input(array(
						'name'      => 'bname',
						'id'        => 'bname',
						'value'     => $billdetails[0]['bankname']
						))
						
						);
		array_push($formFields,$cf);
		$cf=array('label'=>'Branch Name',
				  'field'=>": ".form_input(array(
						'name'      => 'brname',
						'id'        => 'brname',
						'value'     => $billdetails[0]['branchname']
						))
						
						);
		array_push($formFields,$cf);
		
	   
		$data['form'] = array(
		            'form_attr'=>array('action'=>'Masteradmin/editpayment/'.$bill_id.'/'.$payment_id,'name'=>'billpayment','id'=>'billpayment','enctype'=>"multipart/form-data"),
		 			//~ 'open'=>form_open_multipart('Masteradmin/editpayment/'.$bill_id.'/'.$payment_id,array('name'=>'billpayment','class'=>'form','id'=>'billpayment','method'=>'post'),
		 			'hidden' =>array('billid'=>$bill_id),
					'fields'=>$formFields,
					'fields1'=>$formFields1,
					'close'=>form_close()
				);
		$this->mastermodel->viewLayout('form_view',$data);
	}
	function deletepayment($bill_id,$pid){
		$res=$this->mastermodel->deletepayament($bill_id,$pid);
		redirect("Masteradmin/listpayments/".$bill_id);
	}
	function generateBill_byuser($bid){
		$res=$this->mastermodel->getuserbill($bid);
	}
	function listconfig(){
		$data['module']['title'] ="Business Bill Configuration";
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '30';
		$header = array('Business Name'
						//,'Bill Cycle'
						//,'Bill Generate Date'
						//,'Bill Due Date'
						,'Discount'
						//,'Rental'
						,'Action'
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->mastermodel->listbusiness_config($ofset,$limit);
		$rec = array();
		$bc=array("1"=>"Monthly","3"=>"Quaterly","6"=>"Half Yearly","12"=>"Yearly");
		if(count($emp_list['data'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;
		
		foreach ($emp_list['data'] as $item){
			$dis=($item['discount_type']==1)?$item['discount_percentage']."%":$item['discount_amount'];
			$rec[] = array(
				$item['businessname']
				//,$bc[$item['billing_cycle']]
				//,$item['bill_generate_date']."th of every ".$bc[$item['billing_cycle']]
				//,$item['bill_due_date']
				,$dis
				//,$item['rental']
				,'<a href="'.site_url('Masteradmin/Billconfig/'.$item['bid']).'">
						<span title="Edit" class="fa fa-edit"></span>
				  </a>'
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('payment/listpayments/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>4					
				));
		//$data['addlinks']="group/add_group";		
		$data['paging'] = $this->pagination->create_links();
		$this->mastermodel->data['html']['title'] .= " | Business Configuration List";
		$data['links']='<a href="Masteradmin/Billconfig/"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>';
		//$fieldset = $this->configmodel->getFields('3');
		$formFields = array();
		
		$formFields[] = array(
				'label'=>'<label for="f">Business Name : </label>',
				'field'=>form_input(array(
						'name'      => 'bname',
						'id'        => 'bname',
						'value'     => $this->session->userdata('bname')
						))
						);
			
		$data['form'] = array(
							'open'=>form_open_multipart('Masteradmin/listconfig/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'adv_search'=>array(),
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->mastermodel->viewLayout('list_view',$data);
	}
	function delete_unconfirmEmployee($eid,$bid){
		$res=$this->mastermodel->delete_unconfirmBusinessEmployee($eid,$bid);
		$flashdata = array('msgt' => 'success', 'msg' => 'Unconfirm Employee deleted successfully');
		$this->session->set_flashdata($flashdata);
		redirect('Masteradmin/unconfirm_list_business/'.$bid);			
		
	}
	function addModule($id=''){
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('mname', 'Module Name', 'required');
			if(!$this->form_validation->run() == FALSE){	
						$res=$this->mastermodel->addModule($id);
						if($id!=""){
							$this->session->set_flashdata('msgt', 'success');
							$this->session->set_flashdata('msg', "Module Name updated Successfully");
						}else{
							$this->session->set_flashdata('msgt', 'success');
							$this->session->set_flashdata('msg', "Module Name Inserted Successfully");
						}
						redirect('Masteradmin/listModule');
							
							
					}	
		}
		$res=$this->mastermodel->get_module($id);
		$this->mastermodel->data['html']['title'] .= " | Add Module";
		$data['module']['title'] = "Add Module";
		$formFields = array();$formFields1 = array();
		$cf=array('label'=>"<label>Module Name : </label> ",
				  'field'=>form_input(array(
									  'name'        => 'mname',
										'id'          => 'mname',
										'value'       => (isset($res->module_name))?$res->module_name:'',
										'class'		=>'required'
				  				)
				  		)
							);
						array_push($formFields,$cf);
		$cf=array('label'=>"<label>Module Description : </label> ",
				  'field'=>form_textarea(array(
									  'name'        => 'mdesc',
										'id'          => 'mdesc',
										'value'       => (isset($res->module_description))?$res->module_description:'',
										'class'		=>''
				  				)
				  		)
							);
						array_push($formFields,$cf);
		$data['form'] = array(
		        'form_attr'=>array('action'=>'Masteradmin/addModule/'.$id,'name'=>'addmodule','id'=>'addmodule','enctype'=>"multipart/form-data"),
				//~ 'open'=>form_open_multipart('Masteradmin/addModule/'.$id,array('name'=>'addmodule','class'=>'form','id'=>'addmodule','method'=>'post')),
				'fields'=>$formFields,
				'fields1'=>$formFields1,
				'close'=>form_close()
			);
		$this->mastermodel->viewLayout('form_view',$data);
	}
	function listModule(){
		$data['module']['title'] ="Module List";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('#'
						,'Module Name'
						,'Action'
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->mastermodel->getModuleList($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			
			$rec[] = array(
				$item['module_id']
				,$item['module_name']
				,'<a href="'.site_url('Masteradmin/addModule/'.$item['module_id']).'">
						<span title="Edit" class="fa fa-edit"></span>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           
				  </a>'.(($item['status']=="0")?'<a href="Masteradmin/modstatus/'.$item['module_id'].'"><span class="fa fa-lock changeBusStatus" id="'.$item['module_id'].'"  title="Enable"></a>':'<a href="Masteradmin/modstatus/'.$item['module_id'].'"><span class="fa fa-unlock changeBusStatus" id="'.$item['module_id'].'"  title="Disable"></a>').(($item['status']=="3")?'<a href="Masteradmin/mod_del/'.$item['module_id'].'"><img class="changeBusStatus" id="'.$item['module_id'].'" src="system/application/img/icons/undelete.png" title="Undelete" width="16" height="16" /></a>':'<a href="Masteradmin/mod_del/'.$item['module_id'].'"> <span title="Delete" class="changeBusStatus" id="'.$item['module_id'].'" class="glyphicon glyphicon-trash"></span></a>').'<a href="Masteradmin/addAddon/'.$item['module_id'].'">
				  <span id="'.$item['module_id'].'" class="glyphicon glyphicon-plus changeBusStatus" title="Add Addons" ></span></a>'.'<a href="Masteradmin/listaddon/'.$item['module_id'].'">
				  <span  id="'.$item['module_id'].'" title="List Option" class="fa fa-list-ul changeBusStatus"></span></a>'
			);
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/listModule/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->mastermodel->data['html']['title'] .= " | Module List";
		$this->mastermodel->data['links'] = '<a href="Masteradmin/addModule"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>';
		$formFields = array();
		$formFields[] = array(
				'label'=>'<label for="f">Module Name : </label>',
				'field'=>form_input(array(
						'name'      => 'mname',
						'id'        => 'mname',
						'value'     => $this->session->userdata('mname')
						))
						);
		$data['form'] = array(
			'open'=>form_open_multipart('Masteradmin/listModule/',array('name'=>'listModule','class'=>'form','id'=>'listModule','method'=>'post')),
			'form_field'=>$formFields,
			'close'=>form_close(),
			'adv_search'=>array(),
			'title'=>$this->lang->line('level_search')
			);
		$this->mastermodel->viewLayout('list_view',$data);
	}
	function modstatus($pid){
		$this->mastermodel->modstatus($pid);
		$flashdata = array('msgt' => 'success', 'msg' => 'Module status updated Successfully');
		$this->session->set_flashdata($flashdata);
		redirect('Masteradmin/listModule');
	}
	function mod_del($pid){
		$this->mastermodel->mod_del($pid);
		$flashdata = array('msgt' => 'success', 'msg' => 'Module updated Successfully');
		$this->session->set_flashdata($flashdata);
		redirect('Masteradmin/listModule');
	}
	function access_denied(){
		$this->mastermodel->viewLayout('form_err');
		
	}
	function addAddon($id){
		$access=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Addon',1);
		if(!$access)redirect('Masteradmin/access_denied');
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('featurename', 'Feature Name', 'required');
			$this->form_validation->set_rules('rate', 'Rate', 'required|numeric');
			if(!$this->form_validation->run() == FALSE){	
						$res=$this->mastermodel->addAddon($id);
						if($id!=""){
							$this->session->set_flashdata('msgt', 'success');
							$this->session->set_flashdata('msg', "Addon updated Successfully");
						}else{
							$this->session->set_flashdata('msgt', 'success');
							$this->session->set_flashdata('msg', "Addon Inserted Successfully");
						}
						redirect('Masteradmin/listaddons');
				}	
		}
		$res=$this->mastermodel->get_feature($id);
		$this->mastermodel->data['html']['title'] .= " | Add Addon";
		$data['module']['title'] = "Add Addon";
		$formFields = array();$formFields1 = array();
		$cf=array('label'=>"<label class='col-sm-4 text-right'>Feature Name : </label> ",
				  'field'=>form_input(array(
									  'name'        => 'featurename',
										'id'          => 'featurename',
										'value'       => (isset($res->feature_name))?$res->feature_name:'',
										'class'		=>'required form-control'
				  				)
				  		)
							);
						array_push($formFields,$cf);
		$cf=array('label'=>"<label class='col-sm-4 text-right'>Rate : </label> ",
				  'field'=>form_input(array(
									  'name'        => 'rate',
										'id'          => 'rate',
										'value'       => (isset($res->rate))?$res->rate:'',
										'class'		=>'required number form-control'
				  				)
				  		)
							);
						array_push($formFields,$cf);
		$cf=array('label'=>"<label>Validity(in days) : </label> ",
				  'field'=>form_input(array(
									  'name'        => 'validity',
										'id'          => 'validity',
										'value'       => (isset($res->validity))?$res->validity:'',
										'class'		=>'required number form-control'
				  				)
				  		)
							);
						//array_push($formFields,$cf);
		$data['form'] = array(
		        'form_attr'=>array('action'=>'Masteradmin/addAddon/'.$id,'name'=>'feature_idd','id'=>'feature_idd','enctype'=>"multipart/form-data"),
				//~ 'open'=>form_open_multipart('Masteradmin/addAddon/'.$id,array('name'=>'feature_idd','class'=>'form','id'=>'feature_idd','method'=>'post')),
				'fields'=>$formFields,
				'fields1'=>$formFields1,
				'close'=>form_close()
			);
		$this->mastermodel->viewLayout('form_view',$data);
	}
	function listaddons(){
		
		$data['module']['title'] ="Addons";
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '30';
		$header = array('#'
						,'Feature Name'
						,'Rate'
						//,'Validity(in days)'
						,'Action'
						
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->mastermodel->getAddons($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			$rec[] = array(
				$item['feature_id']
				,$item['feature_name']
				,$item['rate']
				//,$item['validity']
				,'<a href="'.site_url('Masteradmin/addAddon/'.$item['feature_id']).'">
						<span title="Edit" class="fa fa-edit"></span>
				  </a>'//'<a href="'.site_url('Masteradmin/del_addon/'.$item['feature_id']).'">						<img src="'.site_url('system/application/img/icons/delete.png').'" title="Delete" width="16" height="16" />				  </a>'
				);
		}
        $links = array();
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search"class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$data['links']= $links;	
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/listaddon/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>4					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->mastermodel->data['html']['title'] .= " | Addons";
		$formFields1 = array();
		$this->mastermodel->data['links'] = '';
		$formFields = array();
		$formFields[] = array(
				'label'=>'<label  class="col-sm-4 text-right" for="f">Feature Name : </label>',
				'field'=>form_input(array(
						'name'      => 'fname',
						'id'        => 'fname',
						'class'     =>  'form-control',
						'value'     => $this->session->userdata('fname')
						))
						);
		
		$data['form'] = array(
			'open'=>form_open_multipart('Masteradmin/listaddon/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->mastermodel->viewLayout('list_view',$data);
	}
	function del_addon($aid){
		$r=$this->mastermodel->del_addon($aid);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', "Addon removed Successfully");
		redirect('Masteradmin/listaddon/'.$mid);
	}
	function addpackage($id=''){
		$access=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Package',($id!="")?1:0);
		if(!$access)redirect('Masteradmin/access_denied');
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('pname', 'Package Name', 'required');
			//$this->form_validation->set_rules('sdate', 'Start Date', 'required|callback_checkstartime');
			//$this->form_validation->set_rules('edate', 'End Date', 'required|callback_checkendtime');
			//$this->form_validation->set_rules('validity', 'Validity', 'required|numeric');
			$this->form_validation->set_rules('rental', 'Rental', 'required|numeric');
			$this->form_validation->set_rules('flimit', 'Free Limit', 'required|numeric');
			$this->form_validation->set_rules('climit', 'Credit Limit', 'required|numeric');
			$this->form_validation->set_rules('eids', 'Enployee Count', 'required|numeric');
			$this->form_validation->set_rules('rpi', 'Rate Per Min', 'required|numeric|callback_checkRate');
			if(!$this->form_validation->run() == FALSE){	
					$res=$this->mastermodel->addPackage($id);
					if($id!=""){
						$this->session->set_flashdata('msgt', 'success');
						$this->session->set_flashdata('msg', "Package updated Successfully");
					}else{
						$this->session->set_flashdata('msgt', 'success');
						$this->session->set_flashdata('msg', "Package Inserted Successfully");
					}
					redirect('Masteradmin/listpackage');
				}	
		}
		$rs=$this->mastermodel->get_package($id);
		$this->mastermodel->data['html']['title'] .= " | Add Package";
		$data['module']['title'] = "Add Package";
		$formFields = array();$formFields1 = array();
		$cf=array('label'=>"<label class='col-sm-4 text-right'>Package Name : </label> ",
				  'field'=>form_input(array(
									  'name'        => 'pname',
										'id'          => 'pname',
										'value'       => (isset($rs->packagename))?$rs->packagename:'',
										'class'		=>'required form-control'
				  				) 
				  		)
							);
			array_push($formFields,$cf);
			$out='<ul style="list-style:none;float:left;" id="fids">';
			$c=''; $rss='';
			foreach($this->mastermodel->get_allModules() as $res){
				$rss=($this->mastermodel->get_featureaddons($id,$res['module_id'],0)!=0)?'checked':'';
				$out.='<li style="float:left;width:300;"><label class="col-sm-6 text-right"><input type="checkbox" name="moduleids[]" class="moduleids" value="'.$res['module_id'].'" '.$rss.'/>'.$res['module_name'].'</label>';
			 	$rss1='';	
				
				$out.='</li>';
			}			
			
			$out.='</ul>';
						
		$cf=array('label'=>"<label class='col-sm-4 text-right'>Modules  : </label> ",
				  'field'=>$out
							);array_push($formFields,$cf);
				$out='<ul style="list-style:none;float:left;" id="fids">';
			$c=''; $rss='';
			foreach($this->mastermodel->get_allfeatures() as $rows){
				$rss1=($this->mastermodel->get_featureaddons($id,'0',$rows['feature_id'])!=0)?'checked':'';
				$out.='<li ><label class="col-sm-4 text-right"><input type="checkbox" name="featureids[]"  id="featureids[]" value="'.$rows['feature_id'].'" '.$rss1.'/>&nbsp;'.$rows['feature_name'].'</label>';	
					$out.='</li>';
				}	
				$out.='</ul>';
								
							
		$cf=array('label'=>"<label class='col-sm-4 text-right'>Addons  : </label> ",
				  'field'=>$out
							);
		array_push($formFields,$cf);
		//~ $cf=array('label'=>"<label>Start Date : </label> ",
				  //~ 'field'=>form_input(array(
									  //~ 'name'        => 'sdate',
										//~ 'id'          => 'sdate',
										//~ 'value'       => (isset($rs->startdate))?$rs->startdate:'',
										//~ 'class'		=>'required datepicker'
				  				//~ ) 
				  		//~ )
							//~ );
							//~ $s='';
						//~ array_push($formFields,$cf);
		//~ $cf=array('label'=>"<label>End Date : </label> ",
				  //~ 'field'=>form_input(array(
									  //~ 'name'        => 'edate',
										//~ 'id'          => 'edate',
										//~ 'value'       => (isset($rs->endate))?$rs->endate:'',
										//~ 'class'		=>'required datepicker'
				  				//~ ) 
				  		//~ )
							//~ );
						//~ array_push($formFields,$cf);
		//~ $cf=array('label'=>"<label>Validity(in days) : </label> ",
				  //~ 'field'=>form_input(array(
									  //~ 'name'        => 'validity',
										//~ 'id'          => 'validity',
										//~ 'value'       => (isset($rs->validity))?$rs->validity:'',
										//~ 'class'		=>'required number'
				  				//~ ) 
				  		//~ )
							//~ );
							//~ $s='';
						//~ array_push($formFields,$cf);
		$cf=array('label'=>"<label class='col-sm-4 text-right'>Free Limit : </label> ",
				  'field'=>form_input(array(
									  'name'        => 'flimit',
										'id'          => 'flimit',
										'value'       => (isset($rs->freelimit))?$rs->freelimit:'',
										'class'		=>'required number form-control'
				  				) 
				  		)
							);
							$s='';
						array_push($formFields,$cf);
		$cf=array('label'=>"<label class='col-sm-4 text-right'>Credit Limit : </label> ",
				  'field'=>form_input(array(
									  'name'        => 'climit',
										'id'          => 'climit',
										'value'       => (isset($rs->creditlimit))?$rs->creditlimit:'',
										'class'		=>'required number form-control'
				  				) 
				  		)
							);
							$s='';
						array_push($formFields,$cf);
		$cf=array('label'=>"<label class='col-sm-4 text-right'>Rental : </label> ",
				  'field'=>form_input(array(
									  'name'        => 'rental',
										'id'          => 'rental',
										'value'       => (isset($rs->rental))?$rs->rental:'',
										'class'		=>'required number form-control'
				  				) 
				  		)
							);
							$s='';
						array_push($formFields,$cf);
		$cf=array('label'=>"<label class='col-sm-4 text-right'>No Of Employees : </label> ",
				  'field'=>form_input(array(
									  'name'        => 'eids',
										'id'          => 'eids',
										'value'       => (isset($rs->eid))?$rs->eid:'',
										'class'		=>'required number form-control'
				  				) 
				  		)
							);
							$s='';
						array_push($formFields,$cf);
		$cf=array('label'=>"<label class='col-sm-4 text-right'>Rate Per Min : </label> ",
				  'field'=>form_input(array(
									  'name'        => 'rpi',
										'id'          => 'rpi',
										'value'       => (isset($rs->rpi))?$rs->rpi:'',
										'class'		=>'required number form-control'
				  				) 
				  		)
							);
						
						array_push($formFields,$cf);
						
		
		$data['form'] = array(
		        'form_attr'=>array('action'=>'Masteradmin/addpackage/'.$id,'name'=>'addpackage','id'=>'addpackage','enctype'=>"multipart/form-data"),
				//~ 'open'=>form_open_multipart('Masteradmin/addpackage/'.$id,array('name'=>'addpackage','class'=>'form','id'=>'addpackage','method'=>'post')),
				'fields'=>$formFields,
				'fields1'=>$formFields1,
				'close'=>form_close()
			);
		$this->mastermodel->viewLayout('form_view',$data);
	}
	function checkRate($rate){
		if($rate < 0){
			$this->form_validation->set_message('checkRate', 'Negative Values Not Allowed');
			return false;
		}else{
			return true;
			
		}
	}
	function checkstartime($start){
		$today=date('Y-m-d');
		if($today>=$start){
			$this->form_validation->set_message('checkstartime', "Start time must be greater than todays date");
			return FALSE;
		}else{
			return true;
			}
	}
	function checkendtime($end){
		$today=$this->input->post('stime');
		if($end<$today){
			$this->form_validation->set_message('checkendtime', "End time must be greater than Start date");
			return FALSE;
		}else{
			return true;
			}
	}
	function listpackage(){
		$data['module']['title'] ="Package List";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('#'
						,'Package Name'
						,'Rental'
						,'Free Limit'
						,'Credit Limit'
						,'Rate Per Call'
						//~ ,'End Date'
						//~ ,'Validity'
						,'Action'
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->mastermodel->getPackageList($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			
			$rec[] = array(
				$item['package_id']
				,$item['packagename']
				,$item['rental']
				,$item['freelimit']
				,$item['creditlimit']
				,$item['rpi']
				//~ ,$item['startdate']
				//~ ,$item['endate']
				//~ ,$item['validity']
				,'<a href="'.site_url('Masteradmin/addpackage/'.$item['package_id']).'">
						<span title="Edit" class="fa fa-edit"></span>
				  </a>'.(($item['status']=="0")?'<a href="Masteradmin/packagestatus/'.$item['package_id'].'"><span class="fa fa-lock changeBusStatus" id="'.$item['package_id'].'"  title="Enable"></a>'
				  :'<a href="Masteradmin/packagestatus/'.$item['package_id'].'"><span class="fa fa-unlock changeBusStatus" id="'.$item['package_id'].'"  title="Disable"></a>').(($item['status']=="3")?'<a href="Masteradmin/package_del/'.$item['package_id'].'"><img class="changeBusStatus" id="'.$item['package_id'].'" src="system/application/img/icons/undelete.png" title="Undelete" width="16" height="16" /></a>':
				  '<a href="Masteradmin/package_del/'.$item['package_id'].'"><span title="Delete" class="changeBusStatus" id="'.$item['package_id'].'" class="glyphicon glyphicon-trash"></span></a>')
			); 
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/listpackage/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->mastermodel->data['html']['title'] .= " | Module List";
		$this->mastermodel->data['links'] = '<a href="Masteradmin/addpackage"><span title="Add Number" class="glyphicon glyphicon-plus-sign"></span></a>';
		$formFields = array();
		$formFields[] = array(
				'label'=>'<label for="f">Package Name : </label>',
				'field'=>form_input(array(
						'name'      => 'pname',
						'id'        => 'pname',
						'value'     => $this->session->userdata('pname')
						))
						);
		$data['nobulk']=true;	
		$data['form'] = array(
			'open'=>form_open_multipart('Masteradmin/listpackage/',array('name'=>'listModule','class'=>'form','id'=>'listModule','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		$this->mastermodel->viewLayout('list_view',$data);
	}
	function packagestatus($pid){
		$access=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Package',3);
		if(!$access)redirect('Masteradmin/access_denied');
		$this->mastermodel->packagestatus($pid);
		$flashdata = array('msgt' => 'success', 'msg' => 'Package status updated Successfully');
		$this->session->set_flashdata($flashdata);
		redirect('Masteradmin/listpackage');
	}
	function package_del($pid){
		$access=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Package',2);
		if(!$access)redirect('Masteradmin/access_denied');
		$this->mastermodel->package_del($pid);
		$flashdata = array('msgt' => 'success', 'msg' => 'Package Delete Status Successfully');
		$this->session->set_flashdata($flashdata);
		redirect('Masteradmin/listpackage');
	}
	function convertpackage($bid,$pid,$number){
		$access=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Number',1);
		if(!$access)redirect('Masteradmin/access_denied');
		if($this->input->post('submit')){
			$res=$this->mastermodel->convert_package($bid,$pid,$number);
			if($res){
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg', "package Updated Successfully");
				redirect('Masteradmin/managePriList');
				
			}else{
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', "package not changed,please change the package and convert");
				redirect('Masteradmin/managePriList');
				
			}
			
		}
		
		$c=$this->mastermodel->getPridetails($number);
		$data=array('businessUsers'=>$this->mastermodel->get_businessusers(),
					'selectedlist'=>$this->mastermodel->getPridetails($number),
					'modules'=>$this->mastermodel->get_allModules(),
					'packages'=>$this->mastermodel->get_allpackages(),
					'landing_details'=>$this->mastermodel->landingDetails($c->landingnumber),
					 'baddons'=>$this->mastermodel->get_baddons($c->bid,$c->package_id,$c->number),	
					'action'=>'Masteradmin/convertpackage/'.$bid.'/'.$pid,
					'show'=>'0'
					);
		$this->mastermodel->viewLayout('convertpackage',$data);
		
	}
	function getPackage_modules($packid,$modid=''){
		if($modid!=""){
			$rs=$this->mastermodel->get_package($packid);
			$rss='~'.$rs->rental.'~'.$rs->freelimit.'~'.$rs->creditlimit.'~'.$rs->rpi;
			$option='';
			$option.='<ul style="list-style:none;float:left;" id="fids">';
			foreach($this->mastermodel->get_allfeatures() as $res){
				//if($modid==$res['relatedto']){
				$check=($this->mastermodel->get_featureaddons($packid,'0',$res['feature_id'])!=0)?'checked':'';
				$disabled=($this->mastermodel->get_featureaddons($packid,'0',$res['feature_id'])!=0)?'disabled':'';
					$option.='<li style="width:300;padding-right:40px;"><input type="checkbox" name="featureids[]" id="featureids[]" value="'.$res['feature_id'].'" '.$check.' '.$disabled.'  />'.$res['feature_name'].'</label></li>';
				//}
			}
			$option.='</ul>'.$rss;
			
		}else{
			$option='';
			$option.='<option value="">--Select--</option>';
			foreach($this->mastermodel->get_allModules() as $res){
				$check=$this->mastermodel->get_featureaddons($packid,$res['module_id'],0);
				if($check!=0){
					$option.='<option value="'.$res['module_id'].'">'.$res['module_name'].'</option>';
				}
			}
		}
		echo $option;
	}
	function get_landing_addons($bid,$pid,$number){
		$arr=$this->mastermodel->get_baddons($bid,$pid,$number);
		$ot=array();
		foreach($arr as $a){
			 $ot[]=$this->mastermodel->feature_name($a['feature_id']);
		}
		return "<pre>".implode("\n",$ot)."</pre>";
	}
	function get_landing_addons_history($bid,$pid,$number){
		$arr=$this->mastermodel->get_baddons_history($bid,$pid,$number);
		$ot=array();
		foreach($arr as $a){
			 $ot[]=$this->mastermodel->feature_name($a['feature_id']);
		}
		return "<pre>".implode("\n",$ot)."</pre>";
	}
	function salesemp(){
		$data['module']['title'] ="Sale Employee List";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('#'
						,'Employee Name'
						,'Joined Date'
						,'Action'
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->mastermodel->getSalesEmp($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			
			$rec[] = array(
				$item['id']
				,$item['empname']
				,$item['joined_date']
				,'<a href="'.site_url('Masteradmin/addSalesEmp/'.$item['id']).'">
						<span title="Edit" class="fa fa-edit"></span>
				  </a>'.(($item['status']=="0")?'<a href="Masteradmin/salesEmp_status/'.$item['id'].'"><span class="fa fa-lock changeBusStatus" id="'.$item['id'].'"  title="Enable"></a>':'<a href="Masteradmin/salesEmp_status/'.$item['id'].'"><span class="fa fa-unlock changeBusStatus" id="'.$item['id'].'"  title="Disable"></a>').(($item['status']=="3")?'<a href="Masteradmin/salesEmp_del/'.$item['id'].'"><img class="changeBusStatus" id="'.$item['id'].'" src="system/application/img/icons/undelete.png" title="Undelete" width="16" height="16" /></a>':'<a href="Masteradmin/salesEmp_del/'.$item['id'].'">
				 <span title="Delete" class="changeBusStatus" id="'.$item['id'].'" class="glyphicon glyphicon-trash"></span></a>')
			
			);
		}
		 $links = array();
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search"class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="Masteradmin/addSalesEmp"><span title="Add Number" class="glyphicon glyphicon-plus-sign">&nbsp;Add Number</span></a></li>';
		$data['links']= $links;	
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/salesemp/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->mastermodel->data['html']['title'] .= " | Sales Employee List";
		$this->mastermodel->data['links'] = '<a href="Masteradmin/addSalesEmp"><span title="Add Number" class="glyphicon glyphicon-plus-sign"></span></a>';
		$formFields = array();
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Package Name : </label>',
				'field'=>form_input(array(
						'name'      => 'pname',
						'id'        => 'pname',
						'class'     => 'form-control',
						'value'     => $this->session->userdata('pname')
						))
						);
		$data['form'] = array(
			'open'=>form_open_multipart('Masteradmin/salesemp/',array('name'=>'listModule','class'=>'form','id'=>'listModule','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->mastermodel->viewLayout('list_view',$data);
	}
	function addSalesEmp($id=''){
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('emp', 'Employee Name', 'required');
			$this->form_validation->set_rules('jdate', 'Joined Date', 'required');
			$this->form_validation->set_rules('cnumber', 'Contact Number', 'required|numeric');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
			if(!$this->form_validation->run() == FALSE){	
						$res=$this->mastermodel->addsalesEmp($id,$fid);
						if($fid!=""){
							$this->session->set_flashdata('msgt', 'success');
							$this->session->set_flashdata('msg', "Addon updated Successfully");
						}else{
							$this->session->set_flashdata('msgt', 'success');
							$this->session->set_flashdata('msg', "Addon Inserted Successfully");
						}
						redirect('Masteradmin/salesemp');
				}	
		}
		$res=$this->mastermodel->get_salesEmp($id);
		$this->mastermodel->data['html']['title'] .= " | Add Employee";
		$data['module']['title'] = "Add Employee";
		$formFields = array();$formFields1 = array();
		$cf=array('label'=>"<label  class='col-sm-4 text-right'>Employee Name : </label> ",
				  'field'=>form_input(array(
									  'name'        => 'emp',
										'id'          => 'emp',
										'value'       => (isset($res->empname))?$res->empname:'',
										'class'		=>'required form-control'
				  				)
				  		)
							);
						array_push($formFields,$cf);
		$cf=array('label'=>"<label  class='col-sm-4 text-right'>Contact Number : </label> ",
				  'field'=>form_input(array(
									  'name'        => 'cnumber',
										'id'          => 'cnumber',
										'value'       => (isset($res->contact))?$res->contact:'',
										'class'		=>'required number form-control'
				  				)
				  		)
							);
						array_push($formFields,$cf);
		$cf=array('label'=>"<label  class='col-sm-4 text-right'>Email : </label> ",
				  'field'=>form_input(array(
									  'name'        => 'email',
										'id'          => 'email',
										'value'       => (isset($res->email))?$res->email:'',
										'class'		=>'required email form-control'
				  				)
				  		)
							);
						array_push($formFields,$cf);
		$cf=array('label'=>"<label  class='col-sm-4 text-right'>Joined date : </label> ",
				  'field'=>form_input(array(
									  'name'        => 'jdate',
										'id'          => 'jdate',
										'value'       => (isset($res->joined_date))?$res->joined_date:'',
										'class'		=>'required datepicker_leads form-control'
				  				)
				  		)
							);
						array_push($formFields,$cf);
		
		$data['form'] = array(
		       'form_attr'=>array('action'=>'Masteradmin/addSalesEmp/'.$id,'name'=>'feature_idd','id'=>'feature_idd','enctype'=>"multipart/form-data"),
				//~ 'open'=>form_open_multipart('Masteradmin/addSalesEmp/'.$id,array('name'=>'feature_idd','class'=>'form','id'=>'feature_idd','method'=>'post')),
				'fields'=>$formFields,
				'fields1'=>$formFields1,
				'close'=>form_close()
			);
		$this->mastermodel->viewLayout('form_view',$data);
	}
	function salesEmp_status($pid){
		$this->mastermodel->salesemp($pid);
		$flashdata = array('msgt' => 'success', 'msg' => 'Package status updated Successfully');
		$this->session->set_flashdata($flashdata);
		redirect('Masteradmin/salesemp');
	}
	function salesEmp_del($pid){
		$this->mastermodel->salesemp_del($pid);
		$flashdata = array('msgt' => 'success', 'msg' => 'Package Delete Status Successfully');
		$this->session->set_flashdata($flashdata);
		redirect('Masteradmin/salesemp');
	}
	function get_relemps($id){
		
		$option='';
			$option.='<option value="">--Select--</option>';
		if($id!=1){
			$resS=$this->commonmodel->get_executives();
			foreach($resS as $res){
				$option.='<option value="'.$res['id'].'">'.$res['empname'].'</option>';
			}
		}else{
			$resS=$this->commonmodel->get_partner();
			foreach($resS as $res){
				$option.='<option value="'.$res['partner_id'].'">'.$res['firstname'].'</option>';
			}
		}
		echo $option;
	}
	function reset_used($bid,$number){
		$access=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Number',1);
		if(!$access)redirect('Masteradmin/access_denied');
		$res=$this->mastermodel->reset_usedLimit($number);
		if($res){
			$flashdata = array('msgt' => 'success', 'msg' => 'Used Limit reset successfully');
			$this->session->set_flashdata($flashdata);
			redirect('Masteradmin/managePriList');
		}else{
			$flashdata = array('msgt' => 'error', 'msg' => 'Fail to reset');
			$this->session->set_flashdata($flashdata);
			redirect('Masteradmin/managePriList');
		}
	}
	function adminroles(){
		$data['module']['title'] ="Admin Role List";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('#'
						,'Role Name'
						,'Action'
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->mastermodel->getadminRoles($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			
			$rec[] = array(
				$item['role_id']
				,$item['rolename']
				,'<a href="'.site_url('Masteradmin/addAdminRole/'.$item['role_id']).'">
						<span title="Edit" class="fa fa-edit"></span>
				  </a>'
			);
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/adminroles/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->mastermodel->data['html']['title'] .= " | Admin Roles List";
		$this->mastermodel->data['links'] = '<a href="Masteradmin/addAdminRole"><span title="Add Number" class="glyphicon glyphicon-plus-sign"></span></a>';
		$formFields = array();
		 $links = array();

		$links[] = '<li><a href="Masteradmin/addAdminRole"><span title="Add Number" class="glyphicon glyphicon-plus-sign">&nbsp;Add Number</span></a></li>';
		$data['links']= $links;	
		
		$data['form'] = array(
			'open'=>form_open_multipart('Masteradmin/adminroles/',array('name'=>'listModule','class'=>'form','id'=>'listModule','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
	
		$this->mastermodel->viewLayout('list_view',$data);
	}
	function addAdminRole($role_id=''){
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('rolename', 'Role Name', 'required');
			if(!$this->form_validation->run() == FALSE){
				$res=$this->mastermodel->adminRole($role_id);
				redirect('Masteradmin/adminroles');
			}
		}
		$res=$this->mastermodel->get_admin_role($role_id);
		$this->mastermodel->data['html']['title'] .= " | Add Admin Role";
		$data['module']['title'] = "Add Admin Role";
		$formFields = array();$formFields1 = array();
		$cf=array('label'=>"<label class='col-sm-4 text-right'>Role Name : </label> ",
				  'field'=>form_input(array(
									  'name'        => 'rolename',
										'id'          => 'rolename',
										'value'       =>(isset($res->rolename))?$res->rolename:'',
										'class'		=>'required form-control'
				  				))	);
						array_push($formFields,$cf);
			$business_mod=$this->mastermodel->get_module_role($role_id,'Business');		
		$cf=array('label'=>"<label class='col-sm-4 text-right'>Module Name : </label> ",
				  'field'=>'Businees Module'.'<br/>'.form_checkbox(array(
									  'name'        => 'business_add_access',
										'id'          => 'business_add_access',
										'value'       => '1',
										'checked'		=>(isset($business_mod->add_access) && $business_mod->add_access!=0)?TRUE:'')).'Add'.'&nbsp;&nbsp;'.form_checkbox(array(
									  'name'        => 'business_edit_access',
										'id'          => 'business_edit_access',
										'value'       => '1',
										'checked'		=>(isset($business_mod->edit_access) && $business_mod->edit_access!=0)?TRUE:'')).'Edit'.'&nbsp;&nbsp;'.form_checkbox(array(
									  'name'        => 'business_view_access',
										'id'          => 'business_view_access',
										'value'       => '1',
										'checked'		=>(isset($business_mod->view_access) && $business_mod->view_access!=0)?TRUE:'')).'View'.'&nbsp;&nbsp;'					
										.form_checkbox(array(
									  'name'        => 'business_delete_access',
										'id'          => 'business_delete_access',
										'value'       => '1',
										'checked'		=>(isset($business_mod->delete_access) && $business_mod->delete_access!=0)?TRUE:'')).'Delete'.'&nbsp;&nbsp;'.form_checkbox(array(
									  'name'        => 'business_enable_access',
										'id'          => 'business_enable_access',
										'value'       => '1',
										'checked'		=>(isset($business_mod->enable_access) && $business_mod->enable_access!=0)?TRUE:'')).'Enable/Disable'
							);
						array_push($formFields,$cf);
						$partner_mod=$this->mastermodel->get_module_role($role_id,'Partner');	
		$cf=array('label'=>"<label class='col-sm-4 text-right'> </label> ",
				  'field'=>'partner Module'.'<br/>'.form_checkbox(array(
									  'name'        => 'partner_add_access',
										'id'          => 'partner_add_access',
										'value'       => '1',
										'checked'		=>(isset($partner_mod->add_access) && $partner_mod->add_access!=0)?TRUE:'')).'Add'.'&nbsp;&nbsp;'.form_checkbox(array(
									  'name'        => 'partner_edit_access',
										'id'          => 'partner_edit_access',
										'value'       => '1',
										'checked'		=>(isset($partner_mod->edit_access) && $partner_mod->edit_access!=0)?TRUE:'')).'Edit'.'&nbsp;&nbsp;'.form_checkbox(array(
									  'name'        => 'partner_view_access',
										'id'          => 'partner_view_access',
										'value'       => '1',
										'checked'		=>(isset($partner_mod->view_access) && $partner_mod->view_access!=0)?TRUE:'')).'View'.'&nbsp;&nbsp;'
										
										.form_checkbox(array(
									  'name'        => 'partner_delete_access',
										'id'          => 'partner_delete_access',
										'value'       => '1',
										'checked'		=>(isset($partner_mod->delete_access) && $partner_mod->delete_access!=0)?TRUE:'')).'Delete'.'&nbsp;&nbsp;'.form_checkbox(array(
									  'name'        => 'partner_enable_access',
										'id'          => 'partner_enable_access',
										'value'       => '1',
										'checked'		=>(isset($partner_mod->enable_access) && $partner_mod->enable_access!=0)?TRUE:'')).'Enable/Disable'
							);
						array_push($formFields,$cf);
		$package_mod=$this->mastermodel->get_module_role($role_id,'Package');	
		$cf=array('label'=>"<label class='col-sm-4 text-right'> </label> ",
				  'field'=>'package Module'.'<br/>'.form_checkbox(array(
									  'name'        => 'package_add_access',
										'id'          => 'package_add_access',
										'value'       => '1',
										'checked'		=>(isset($package_mod->add_access) && $package_mod->add_access!=0)?TRUE:'')).'Add'.'&nbsp;&nbsp;'.form_checkbox(array(
									  'name'        => 'package_edit_access',
										'id'          => 'package_edit_access',
										'value'       => '1',
										'checked'		=>(isset($package_mod->edit_access) && $package_mod->edit_access!=0)?TRUE:'')).'Edit'.'&nbsp;&nbsp;'
										.form_checkbox(array(
									  'name'        => 'package_view_access',
										'id'          => 'package_view_access',
										'value'       => '1',
										'checked'		=>(isset($package_mod->view_access) && $package_mod->view_access!=0)?TRUE:'')).'View'.'&nbsp;&nbsp;'
										
										.form_checkbox(array(
									  'name'        => 'package_delete_access',
										'id'          => 'package_delete_access',
										'value'       => '1',
										'checked'		=>(isset($package_mod->delete_access) && $package_mod->delete_access!=0)?TRUE:'')).'Delete'.'&nbsp;&nbsp;'.form_checkbox(array(
									  'name'        => 'package_enable_access',
										'id'          => 'package_enable_access',
										'value'       => '1',
										'checked'		=>(isset($package_mod->enable_access) && $package_mod->enable_access!=0)?TRUE:'')).'Enable/Disable'
							);
						array_push($formFields,$cf);
		$number_mod=$this->mastermodel->get_module_role($role_id,'Number');	
		$cf=array('label'=>"<label class='col-sm-4 text-right'> </label> ",
				  'field'=>'Number Configuration'.'<br/>'.form_checkbox(array(
									  'name'        => 'number_add_access',
										'id'          => 'number_add_access',
										'value'       => '1',
										'checked'		=>(isset($number_mod->add_access) && $number_mod->add_access!=0)?TRUE:'')).'Add'.'&nbsp;&nbsp;'.form_checkbox(array(
									  'name'        => 'number_edit_access',
										'id'          => 'number_edit_access',
										'value'       => '1',
										'checked'		=>(isset($number_mod->edit_access) && $number_mod->edit_access!=0)?TRUE:'')).'Edit'.'&nbsp;&nbsp;'
										.form_checkbox(array(
									  'name'        => 'number_view_access',
										'id'          => 'number_view_access',
										'value'       => '1',
										'checked'		=>(isset($number_mod->view_access) && $number_mod->view_access!=0)?TRUE:'')).'View'.'&nbsp;&nbsp;'
										.form_checkbox(array(
									  'name'        => 'number_delete_access',
										'id'          => 'number_delete_access',
										'value'       => '1',
										'checked'		=>(isset($number_mod->delete_access) && $number_mod->delete_access!=0)?TRUE:'')).'Delete'.'&nbsp;&nbsp;'.form_checkbox(array(
									  'name'        => 'number_enable_access',
										'id'          => 'number_enable_access',
										'value'       => '1',
										'checked'		=>(isset($number_mod->enable_access) && $number_mod->enable_access!=0)?TRUE:'')).'Enable/Disable'
							);
						array_push($formFields,$cf);
		$credit_mod=$this->mastermodel->get_module_role($role_id,'Creditassign');	
		$cf=array('label'=>"<label class='col-sm-4 text-right'> </label> ",
				  'field'=>'Credit Assign'.'<br/>'.form_checkbox(array(
									  'name'        => 'credit_add_access',
										'id'          => 'credit_add_access',
										'value'       => '1',
										'checked'		=>(isset($credit_mod->add_access) && $credit_mod->add_access!=0)?TRUE:'')).'Add'.'&nbsp;&nbsp;'.form_checkbox(array(
									  'name'        => 'credit_edit_access',
										'id'          => 'credit_edit_access',
										'value'       => '1',
										'checked'		=>(isset($credit_mod->edit_access) && $credit_mod->edit_access!=0)?TRUE:'')).'Edit'.'&nbsp;&nbsp;'
										.form_checkbox(array(
									  'name'        => 'credit_view_access',
										'id'          => 'credit_view_access',
										'value'       => '1',
										'checked'		=>(isset($credit_mod->view_access) && $credit_mod->view_access!=0)?TRUE:'')).'View'.'&nbsp;&nbsp;'
										
										
										
										.form_checkbox(array(
									  'name'        => 'credit_delete_access',
										'id'          => 'credit_delete_access',
										'value'       => '1',
										'checked'		=>(isset($credit_mod->delete_access) && $credit_mod->delete_access!=0)?TRUE:'')).'Delete'.'&nbsp;&nbsp;'.form_checkbox(array(
									  'name'        => 'credit_enable_access',
										'id'          => 'credit_enable_access',
										'value'       => '1',
										'checked'		=>(isset($credit_mod->enable_access) && $credit_mod->enable_access!=0)?TRUE:'')).'Enable/Disable'
							);
						array_push($formFields,$cf);
						$addon_mod=$this->mastermodel->get_module_role($role_id,'Addon');	
		$cf=array('label'=>"<label class='col-sm-4 text-right'> </label> ",
				  'field'=>'addon Module'.'<br/>'.'&nbsp;&nbsp;'.form_checkbox(array(
									  'name'        => 'addon_edit_access',
										'id'          => 'addon_edit_access',
										'value'       => '1',
										'checked'		=>(isset($addon_mod->edit_access) && $addon_mod->edit_access!=0)?TRUE:'')).'Edit'
							);
						array_push($formFields,$cf);
	   
		$data['form'] = array(
		       'form_attr'=>array('action'=>'Masteradmin/addAdminRole/'.$role_id,'name'=>'adminrole','id'=>'adminrole','enctype'=>"multipart/form-data"),
				//~ 'open'=>form_open_multipart('Masteradmin/addAdminRole/'.$role_id,array('name'=>'adminrole','class'=>'form','id'=>'adminrole','method'=>'post')),
				'fields'=>$formFields,
				'fields1'=>$formFields1,
				'close'=>form_close()
			);
	
		$this->mastermodel->viewLayout('form_view',$data);
	}
	function addAdminuser($user_id=''){
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('name', 'Name', 'required');
			$this->form_validation->set_rules('uname', 'username', 'required|callback_checkAdminuser');
			if(!$this->form_validation->run() == FALSE){
				$res=$this->mastermodel->addAdminuser($user_id);
				redirect('Masteradmin/ListAdminusers');
				
			}
			
		}
		$userDetails=$this->mastermodel->getAdminDetails($user_id);
		$this->mastermodel->data['html']['title'] .= " | Add Admin user";
		$data['module']['title'] = "Add Admin user";
		$formFields = array();$formFields1 = array();
		$cf=array('label'=>"<label class='col-sm-4 text-right'>Name : </label> ",
				  'field'=>form_input(array(
									  'name'        => 'name',
										'id'          => 'name',
										'value'       =>(isset($userDetails->name))?$userDetails->name:'',
										'class'		=>'required form-control'
				  				))	);
						array_push($formFields,$cf);
		$cf=array('label'=>"<label class='col-sm-4 text-right'>Username : </label> ",
				  'field'=>form_input(array(
									  'name'        => 'uname',
										'id'          => 'uname',
										'value'       =>(isset($userDetails->username))?$userDetails->username:'',
										'class'		=>'required email form-control'
				  				))	);
						array_push($formFields,$cf);
		
		$rows=$this->mastermodel->get_roles();
		
		$cf=array('label'=>"<label class='col-sm-4 text-right'>Role: </label> ",
				  'field'=>form_dropdown("role_id",$rows,(isset($userDetails->role_id))?$userDetails->role_id:''," 'id'='role_id' class=form-control"));
						array_push($formFields,$cf);
		$data['form'] = array(
		        'form_attr'=>array('action'=>'Masteradmin/addAdminuser/'.$user_id,'name'=>'adminrole','id'=>'adminrole','enctype'=>"multipart/form-data"),
				//~ 'open'=>form_open_multipart('Masteradmin/addAdminuser/'.$user_id,array('name'=>'adminrole','class'=>'form','id'=>'adminrole','method'=>'post')),
				'fields'=>$formFields,
				'fields1'=>$formFields1,
				'close'=>form_close()
			);
		$this->mastermodel->viewLayout('form_view',$data);
		
	}
	function checkAdminuser($str,$uid){
		
		$check=$this->mastermodel->CheckAdminUser($str);
		if($check!=1){
			return true;
		}else{
			$this->form_validation->set_message('checkAdminuser', 'The '.$str.' is already added');
			return FALSE;
		}
		
	}
	function ListAdminusers(){
		$data['module']['title'] ="Admin user's List";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('#'
						,'Name'
						,'Email'
						,'Role'
						,'Action'
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->mastermodel->getadminusers($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)$dis='';
		foreach ($emp_list['data'] as $item){
			$dis=($item['uid']==1)?'disabled':'';
			$status=($item['status']!=0)?'<span class="fa fa-lock" title="Enable" '.$dis.'>':'<span class="fa fa-unlock" title="Disable" '.$dis.'>';
			$rec[] = array(
				$item['uid']
				,$item['name']
				,$item['username']
				,$item['rolename']
				,'<a href="'.(($item['uid']!=1)?site_url('Masteradmin/addAdminuser/'.$item['uid']):'javascript:void(0)').'">
						<span title="Edit" class="fa fa-edit"></span>
				  </a>'.'<a href="'.(($item['uid']!=1)?site_url('Masteradmin/ChangeAdminStatus/'.$item['uid']):'javascript:void(0)').'">'.$status.'</a>'
				
			);
		}
	    $links = array();
		$links[] = '<li><a href="Masteradmin/addAdminuser"><span title="Add Number" class="glyphicon glyphicon-plus-sign">&nbsp;Add Number</span></a></li>';
		$data['links']= $links;	
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/adminroles/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->mastermodel->data['html']['title'] .= " | Admin Roles List";
		$data['nosearch']=true ;
		$this->mastermodel->data['links'] = '<a href="Masteradmin/addAdminuser"><span title="Add Number" class="glyphicon glyphicon-plus-sign"></span></a>';
		$formFields = array();
		$data['form'] = array(
			'open'=>form_open_multipart('Masteradmin/ListAdminusers/',array('name'=>'listModule','class'=>'form','id'=>'listModule','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search'),
			
			);
		$this->mastermodel->viewLayout('list_view',$data);
	}
	function ChangeAdminStatus($uid){
		$rs=$this->mastermodel->chgAdminstatus($uid);
		$flashdata = array('msgt' => 'success', 'msg' => 'Status updated Successfully');
		$this->session->set_flashdata($flashdata);
		redirect('Masteradmin/ListAdminusers');
		
	}
	function lbsnumbers($page=''){
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('#'
						,'Number'
						,$this->lang->line('level_Action')
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->mastermodel->listlbsNumber($ofset,$limit);
		$data['module']['title'] ="Manage LBS Number [".$emp_list['count']."]";
		$rec = array();
		if(count($emp_list['data'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;
		foreach ($emp_list['data'] as $item){
			$rec[] = array(
				$i,
				$item['number']
				,'<a href="Masteradmin/deleteLbs/'.$item['number'].'"><span title="Delete" class="glyphicon glyphicon-trash"></span>'
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		
		//$data['itemlist'] = $this->groupmodel->getgrouplist($bid,$ofset,$limit);
		
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/lbsnumbers/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		//$data['addlinks']="group/add_group";		
		$data['paging'] = $this->pagination->create_links();
		//$data['paging'] =  pagination($limit, $page, $start, $total_pages, $targetpage);
	
	    $links = array();
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search"class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="Masteradmin/Addlbsnumber/"><span title="Add Number" class="glyphicon glyphicon-plus-sign">&nbsp;Add Number</span></a></li>';
		$data['links']= $links;	
		
		$formFields1 = array();
		$this->mastermodel->data['links'] = '<a href="Masteradmin/Addlbsnumber"><span title="Add Number" class="glyphicon glyphicon-plus-sign"></span></a>';
		//$fieldset = $this->configmodel->getFields('3');
		$formFields = array();
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Number : </label>',
				'field'=>form_input(array(
						'name'      => 'number',
						'id'        => 'number',
						'class'     => 'form-control',
						'value'     => $this->session->userdata('number')
						))
						);
		
			
		$data['form'] = array(
							'open'=>form_open_multipart('Masteradmin/lbsnumbers/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'adv_search'=>array(),
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->mastermodel->viewLayout('list_view',$data);
		
	}
	function Addlbsnumber($number=''){
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('number', 'Number', 'required');
			if(!$this->form_validation->run() == FALSE){
				$res=$this->mastermodel->AddLbsnumber($number);
				redirect('Masteradmin/lbsnumbers');
			}
		}
		$res=$this->mastermodel->getLbsnumber($number);
		$this->mastermodel->data['html']['title'] .= " | Add LBS Number";
		$data['module']['title'] = "Add LBS Number";
		$formFields = array();$formFields1 = array();
		$cf=array('label'=>"<label class='col-sm-4 text-right' >Number: </label> ",
				  'field'=>form_input(array(
									  'name'        => 'number',
										'id'          => 'number',
										'value'       =>(isset($res->number))?$res->number:'',
										'class'		=>'required number form-control'
				  				))	);
						array_push($formFields,$cf);
				$data['form'] = array(
				'form_attr'=>array('action'=>'Masteradmin/Addlbsnumber/'.$number,'name'=>'adminrole','id'=>'adminrole','enctype'=>"multipart/form-data"),
				//~ 'open'=>form_open_multipart('Masteradmin/Addlbsnumber/'.$number,array('name'=>'adminrole','class'=>'form','id'=>'adminrole','method'=>'post')),
				'fields'=>$formFields,
				'fields1'=>$formFields1,
				'close'=>form_close()
			);
		$this->mastermodel->viewLayout('form_view',$data);
	}
	function deleteLbs($number){
		$d=$this->mastermodel->DdeleteLbs($number);
		redirect('Masteradmin/lbsnumbers');
	}
	function priUsage($month,$year=''){
		$res=$this->mastermodel->pRiUsage($month,$year);	
		echo "pri usage -Mail send successfully";
	}
	
	/**************  Feedback Categories UI **********************/
	
	function addFCategories($catId = ''){
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('category', 'Catgeory', 'required');
			//$this->form_validation->set_rules('type', 'Type', 'required');
			if(!$this->form_validation->run() == FALSE){
				$res=$this->mastermodel->addFCats($catId);
				redirect('Masteradmin/ListFCategories');
				
			}
		}
		$catDetails=$this->mastermodel->getFCatDetails($catId);
		$this->mastermodel->data['html']['title'] .= " | Add Feedback Categories";
		$data['module']['title'] = "Add Feedback Categories";
		$formFields = array();$formFields1 = array();
		$cf=array('label'=>"<label class='col-sm-4 text-right'>Category : </label> ",
				  'field'=>form_input(array(
									  'name'        => 'category',
										'id'          => 'category',
										'value'       =>(isset($catDetails->category))?$catDetails->category:'',
										'class'		=>'required form-control'
				  				))	);
						array_push($formFields,$cf);
		$cf=array('label'=>"<label class='col-sm-4 text-right'>Subcategory Label : </label> ",
				  'field'=>form_input(array(
									    'name'        => 'label',
										'id'          => 'label',
										'value'       =>(isset($catDetails->label))?$catDetails->label:'',
										'class'		=>'required form-control'
				  				))	);
			array_push($formFields,$cf);
		$cf=array('label'=>"<label class='col-sm-4 text-right'>Sub Categories : </label> ",
				  'field'=>form_textarea(array(
									    'name'        	=> 'subcat',
										'id'          	=> 'subcat',
										'value'       	=> (isset($catDetails->subcategory)) ? str_replace('<br />',' ',$catDetails->subcategory) : '',
										'class'			=> 'required form-control'
				  				))	);
						array_push($formFields,$cf);
		$rows = array(""=>"Select","dropdown"=>"Dropdown","radio"=>"Radio","checkbox"=>"Check Box");
		$cf=array('label'=>"<label class='col-sm-4 text-right'>Sub Catgeory type: </label> ",
				  'field'=>form_dropdown("type",$rows,(isset($catDetails->type))?$catDetails->type:''," 'id'='type' class=form-control"));
			array_push($formFields,$cf);
		
		$data['form'] = array(
		        'form_attr'=>array('action'=>'Masteradmin/addFCategories/'.$catId,'name'=>'addFcategory','id'=>'addFcategory','enctype'=>"multipart/form-data"),
				//~ 'open'=>form_open_multipart('Masteradmin/addFCategories/'.$catId,array('name'=>'addFcategory','class'=>'form','id'=>'addFcategory','method'=>'post')),
				'fields'=>$formFields,
				'fields1'=>$formFields1,
				'close'=>form_close()
			);
		$this->mastermodel->viewLayout('form_view',$data);
	}
	function ListFCategories(){
		$data['module']['title'] ="Feedback Catgeories ";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('#'
						,'Category'
						,'SubCategory Label'
						,'SubCategory'
						,'Type'
						,'Action'
						);
		$data['itemlist']['header'] = $header;
		$cat_list = $this->mastermodel->getFCategories($ofset,$limit);
		$rec = array();
		if(count($cat_list['data'])>0)$dis='';
		foreach ($cat_list['data'] as $item){
			$status=($item['status']!=0)?'<span class="fa fa-lock" title="Enable" '.$dis.'>':'<span class="fa fa-unlock" title="Disable" '.$dis.'>';
			$rec[] = array(
				$item['id']
				,$item['category']
				,$item['label']
				,$item['subcategory']
				,$item['type']
				,'<a href="Masteradmin/addFCategories/'.$item['id'].'">
						<span title="Edit" class="fa fa-edit"></span>
				  </a>'.'<a href="Masteradmin/ChangeFCatStatus/'.$item['id'].'">'.$status.'</a>'
			);
		}
	    $links = array();

		$links[] = '<li><a href="Masteradmin/addFCategories"><span title="Add Number" class="glyphicon glyphicon-plus-sign">&nbsp;Add Number</span></a></li>';
		$data['links']= $links;	
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $cat_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/ListFCategories/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->mastermodel->data['html']['title'] .= " | Feedback Catgeories";
		$data['nosearch']=true ;
		$this->mastermodel->data['links'] = '<a href="Masteradmin/addFCategories"><span title="Add Number" class="glyphicon glyphicon-plus-sign"></span></a>';
		
		$formFields = array();
		$data['form'] = array(
			'open'=>form_open_multipart('Masteradmin/ListFCategories/',array('name'=>'listModule','class'=>'form','id'=>'listModule','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search'),
			);
		$this->mastermodel->viewLayout('list_view',$data);
	}
	function ChangeFCatStatus($id){
		$rs = $this->mastermodel->chgFCatstatus($id);
		$flashdata = array('msgt' => 'success', 'msg' => 'Status updated Successfully');
		$this->session->set_flashdata($flashdata);
		redirect('Masteradmin/ListFCategories');
		
	}
	function listFeedbackData(){
		$data['module']['title'] ="Feedback Data ";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('#'
						,'Name'
						,'Email'
						,'Phone'
						,'Category'
						,'SubCategory'
						,'Message'
						//,'Actions'
						);
		$data['itemlist']['header'] = $header;
		$cat_list = $this->mastermodel->getFeedbackData($ofset,$limit);
		$rec = array();
		if(count($cat_list['data'])>0)$dis='';
		foreach ($cat_list['data'] as $item){
			$status=($item['status']!=0)?'<span class="fa fa-lock" title="Enable" '.$dis.'>':'<span class="fa fa-unlock" title="Disable" '.$dis.'>';
			$rec[] = array(
				$item['id']
				,$item['name']
				,$item['email']
				,$item['phone']
				,$item['category']
				,$item['subcategory']
				,$item['message']
				//,'<a href="Masteradmin/ChangeFCatStatus/'.$item['id'].'">&nbsp;&nbsp;'.$status.'</a>'
			);
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $cat_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/ListFCategories/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->mastermodel->data['html']['title'] .= " | Feedback Catgeories";
		$data['nosearch']=true ;
		$this->mastermodel->data['links'] = '';
		$formFields = array();
		$data['nobulk']=true;
		$data['form'] = array(
			'open'=>form_open_multipart('Masteradmin/ListFCategories/',array('name'=>'listModule','class'=>'form','id'=>'listModule','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search'),
			);
		$this->mastermodel->viewLayout('list_view',$data);
	}
	function prihistory(){
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$emp_list=$this->mastermodel->getReportHistory($ofset,$limit);
		$rec = array();
		$header=array('#');
		$i=$ofset+1;$j=0;
		if($emp_list['count']>0)
		foreach ($emp_list['data'] as $rows){
			$list=array($i);
			if($j==0){
				foreach($rows as $field => $val) if($field!='bid') array_push($header,$field);
			}
			foreach($rows as $field => $val) if($field!='bid') array_push($list,$val);
			$i++;$j++;
			array_push($rec,$list);
		}
	
		$data['itemlist']['header'] = $header;
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$links = array();
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search"class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$data['links']= $links;	
		
		//$data['itemlist'] = $this->groupmodel->getgrouplist($bid,$ofset,$limit);
		
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/prihistory/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		//$data['addlinks']="group/add_group";		
		$data['paging'] = $this->pagination->create_links();
		$data['module']['title'] = "  Number Usage Report [".$data['itemlist']['count']."]";
		$data['html']['title'] = " | Number Usage Report [".$data['itemlist']['count']."]";
		$data['links']=$data['links']='';
		$formFields1 = array();
		$cf=array('label'=>'<label for="groupname">'.$this->lang->line('level_groupname').' : </label>',
				  'field'=>form_input(array(
									  'name'        => 'groupname',
										'id'          => 'groupname',
										'value'       => $this->session->userdata('groupname'))));
						array_push($formFields1,$cf);
						
		$formFields = array();
		$busers=$this->mastermodel->get_businessusers();
		$boptions=array(""=>"select");
		foreach($busers as $brow){
			$boptions[$brow['bid']]=$brow['businessname'];
		}
	    $links = array();
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search"class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$data['links']= $links;	
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">Business Name : </label>',
				'field'=>form_dropdown('business',$boptions,'',' id="business" class="form-control"'));
		$formFields[] = array(
			'label'=>'<label class="col-sm-4 text-right" for="f">Date: </label>',
				'field'=>form_input(array(
						'name'      => 'bdate',
						'id'        => 'bdate',
						'value'     => '',
						'class'		=>'monthyear form-control'
						))
						);
		
			
		$data['form'] = array(
			'open'=>form_open_multipart('Masteradmin/prihistory/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->mastermodel->viewLayout('list_view',$data);
		
	}
	function cNumberstatus($number,$landingNumber){
		$res=$this->mastermodel->number_dis($number,$landingNumber);
		$this->session->set_flashdata('msgt', 'success');
		($res!=0)?$this->session->set_flashdata('msg', $landingNumber." is enabled "):$this->session->set_flashdata('msg', $landingNumber." is disabled ");
		
		redirect('Masteradmin/managePriList');
	}
	function DNDEmpStatus(){
			$data['module']['title'] ="DND Employee Status ";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('Employee'
						,'Business'
						,'Mobile'
						,'Email'
						,'Status'
						,'Sent Date'
						,'Verified Date'
						//,'Actions'
						);
		$data['itemlist']['header'] = $header;
		$cat_list = $this->mastermodel->getDNDstatus($ofset,$limit);
		$rec = array();
		if(count($cat_list['data'])>0)$dis='';
		foreach ($cat_list['data'] as $item){
	
			$status=($item['status']!=0)?'<span class="fa fa-lock" title="Enable" '.$dis.'>':'<span class="fa fa-unlock" title="Disable" '.$dis.'>';
			$rec[] = array(
				$item['ename']
				,$item['businessname']
				,$item['number']
				,$item['email']
				,($item['status']==1)?'verified':'Not Verfied'
				,$item['request_date']
				,$item['verified_date']
				//,'<a href="Masteradmin/ChangeFCatStatus/'.$item['id'].'">&nbsp;&nbsp;'.$status.'</a>'
			);
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $cat_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/DNDEmpStatus/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->mastermodel->data['html']['title'] .= " | DND Employee Status ";
		$data['nosearch']=true;
		$this->mastermodel->data['links'] = '';
		$formFields = array();
		$data['nobulk']=true;	
		$data['form'] = array(
			'open'=>form_open_multipart('Masteradmin/DNDEmpStatus/',array('name'=>'listModule','class'=>'form','id'=>'listModule','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search'),
			);
		$this->mastermodel->viewLayout('list_view',$data);
	}
	function MassPri_Del($bid){
		$this->mastermodel->MasDel($bid);
	}	
	function number_data(){
		$this->mastermodel->data['html']['title'] .= " | Number Data ";
		$data['module']['title'] = "Number Data";
		$formFields = array();$formFields1 = array();
		$cf=array('label'=>"<label class='col-sm-4 text-right'>Date From : </label> ",
				  'field'=>form_input(array(
									  'name'        => 'dfrom',
										'id'          => 'dfrom',
										'value'       => '',
										'class'		=>'datepicker_leads form-control'
				  				) 
				  		)
							);
			array_push($formFields,$cf);
			
		$cf=array('label'=>"<label class='col-sm-4 text-right'>Date To: </label> ",
				  'field'=>form_input(array(
									  'name'        => 'dto',
										'id'          => 'dto',
										'value'       => '',
										'class'		=>'datepicker_leads form-control'
				  				) 
				  		)
							);
							$s='';
						array_push($formFields,$cf);
		$cf=array('label'=>"<label class='col-sm-4 text-right'>Landing Number : </label> ",
				  'field'=>form_input(array(
									  'name'        => 'lno',
										'id'          => 'lno',
										'value'       => '',
										'class'		=>'required number form-control'
				  				) 
				  		)
							);
							$s='';
						array_push($formFields,$cf);
		
		
			$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
			$limit = '30';
		$header = array('Date'
						,'Pulse'
						,'count'
						//,'Actions'
						);
		$data['itemlist']['header'] = $header;
		$data['itemlist']['rec'] = array();
		$data['itemlist']['count']='';
		$cat_list = $this->mastermodel->get_Numberdata($ofset,$limit);
		$tcount=(isset($cat_list['tcount']) && $cat_list['tcount']!="")?$cat_list['tcount']:0;
		$data['module']['title'] = "Number Data"." [Total Calls : ".$tcount."]";
		if(isset($cat_list['data']) && count($cat_list['data'])>0){
			$data['itemlist']['count'] = $cat_list['count'];
			foreach ($cat_list['data'] as $item){
				$rec[] = array(
					$item['sdate']
					,$item['pulse']
					,$item['cnt']
				);
			}
			$data['itemlist']['rec'] = $rec;
		}
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/number_data/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
	
		$data['nosearch']=true ;
		$this->mastermodel->data['links'] = '';
		 
		$data['form'] = array(
				'open'=>form_open_multipart('Masteradmin/number_data/',array('name'=>'addpackages','class'=>'form','id'=>'addpackages','method'=>'post')),
				'fields'=>$formFields,
				'fields1'=>$formFields1,
				'adv_search'=>array(),
				'close'=>form_close()
			);
		$data['tab'] = false;		
		$this->mastermodel->viewLayout('form_view_Master',$data);
	}
	function getLeadViews($typeid){
		$rs = $this->mastermodel->get_leaddesigns($typeid);
		$option='';
		foreach($rs as $rst){
			$option .= "<option value='".$rst['id']."'>".$rst['design']."</option>";
		}
		echo $option;
	}		
		
	function supEscConfig($bid){
		$res=$this->mastermodel->supEscConfig($bid);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', "Suport Escalation Configured successfully");
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	function importNumbers(){
		if($this->input->post('submit')){
			$err = FALSE;
			$msg = '';
			$ext = pathinfo($_FILES['numbers']['name'],PATHINFO_EXTENSION);
			if(!(($_FILES['numbers']['type'] == 'text/csv') || ($ext == 'csv'))){
				$msg .= "Please upload the .csv format file. ";
				$err = "TRUE";
			}
			$err_number = 'reports/importNumbers.csv';
			if($err == FALSE){
				$ext = pathinfo($_FILES['numbers']['name'],PATHINFO_EXTENSION);
				$newName = "IM".date('YmdHis').".".$ext;
				@move_uploaded_file($_FILES['numbers']['tmp_name'],$this->config->item('sound_path').$newName);
				$moved_file = $this->config->item('sound_path').$newName;
				@chmod($moved_file,0777);
				$row = 1;
				$emp = array();
				if (($handle = fopen($moved_file, "r")) !== FALSE) {
					while (($csvdata = fgetcsv($handle,1000, ',') )!== FALSE) {
						$num = count($csvdata);
						if($row == 1)
							$headerData = $csvdata;
						else{
							for($k=0;$k<count($csvdata);$k++){
								if($headerData[$k] == 'DID')
									$emp['pri'] = $csvdata[$k];
								if($headerData[$k] == 'Landing Number')
									$emp['landingnumber'] = $csvdata[$k];
								if($headerData[$k] == 'Region')
									$emp['region'] = $csvdata[$k];
							}
							$addNumber = $this->mastermodel->import($emp);
							if($addNumber == 0){
								$fp = fopen($err_number,'w');
								fwrite($fp,$emp['landingnumber'].":".$emp['pri']);
							}
						}
						$row++;
					}
					fclose($handle);
				}
			}else{
				redirect('Masteradmin/Landingpage');
			}
		}
		$data['module']['title'] = "Import Numbers";
		$data=array('businessUsers'=>$this->mastermodel->get_businessusers(),
					'packages'=>$this->mastermodel->get_allpackages(),
					'action1'=>'Masteradmin/AddLandingNumber',
					 'action'=>'Masteradmin/importNumbers',
					);				
		$this->mastermodel->viewLayout('number-config-import',$data);
		
	}	
	function activerecords($gid,$bid){
	  	$fieldset = $this->Config->getFields('3',$bid);
	    $k = $this->mastermodel->getDetail($gid,$bid);
	    $this->mastermodel->data['html']['title'] .= " | Group Configuration";
		$data['module']['title'] ="Group Configuration";
		$formFields = array();
		foreach($fieldset as $field){
			if($field['type']=='s'){
				 foreach($k as $itemDetail){
						$v = '';
						if($field['fieldname']=='eid'){
							$v =$itemDetail[$field['fieldname']];
						}elseif($field['fieldname']=='primary_rule'){
							$v = isset($r['regionname'])?$r['regionname']:"";
						}elseif($field['fieldname']=='rules'){
							$v = ($itemDetail[$field['fieldname']]!='')?'<a title="'.$itemDetail[$field['fieldname']].'">'.$itemDetail[$field['fieldname']].'</a>':$itemDetail[$field['fieldname']];
						}elseif($field['fieldname']=='bday' && $itemDetail[$field['fieldname']]!=''){
							$bday = json_decode($itemDetail[$field['fieldname']]);
							$v = '';
							foreach($bday as $b => $d){ $v .= (isset($d->day) && $d->day=='1')?$b.'='.$d->st.'-'.$d->et.'<br>':'';}
						}elseif($field['fieldname']=='connectowner'){
							$v = isset($itemDetail[$field['fieldname']])?$itemDetail['connectwner']:"";
						}else{
							$v = isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:"";
						}
						$cf = array('label'=>'<label class="col-sm-4 text-right"  for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										).' : </label>',
									'field'=>$v
							);
						array_push($formFields,$cf);
			}
		}
			elseif($field['type']=='c'){
            		 foreach($k as $itemDetail){
					$cf = array('label'=>'<label class="col-sm-4 text-right" >'.$field['customlabel'].' : </label>',
								'field'=> isset($itemDetail['custom['.$field['fieldid'].']'])?
											$itemDetail['custom['.$field['fieldid'].']']:''
						);
					array_push($formFields,$cf);
				
			}
		}
		}
		$cf = array('label'=>'<label class="col-sm-4 text-right"  for="groupkey">Group key For Click To Call : </label>',
						'field'=>isset($itemDetail['groupkey'])?$itemDetail['groupkey']:'');
			array_push($formFields,$cf);
		$data['form'] = array(
					'open'=>form_open("Masteradmin/listlanding/".$bid,array('name'=>'form','class'=>'form','id'=>'addgroup','method'=>'post')),
					'fields'=>$formFields,
					'close'=>form_close()
				);
				$this->load->view('active_view',$data);
		
	}	
}
	
	/**************  Feedback Categories UI END **********************/


/* end of master admin controller */
