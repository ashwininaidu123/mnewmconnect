<?php
class Customfield extends controller
{
	var $data;
	function Customfield(){
		parent::controller();
		//if(!$this->session->userdata('logged_in'))redirect('/user');
		if(!$this->session->userdata('logged_in'))redirect('/site/login');
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('systemmodel');
		$this->load->model('groupmodel');
		$this->load->model('configmodel');
		$this->load->model('empmodel');
		$this->load->model('configuremodel');
	}
	function index(){
		$this->Managecustomfield();
	}
	function ManageFeature(){
		if($this->input->post('submit')){
			$res=$this->systemmodel->update_featuremanage();
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg', $this->lang->line('error_featuresucc'));
			redirect('customfield/ManageFeature');
		}
		$data=array('feature_list'=>$this->systemmodel->feature_manage(),
					'subfeature_list'=>$this->systemmodel->sub_featuremanage(),
					'checked_list'=>$this->systemmodel->checked_featuremanage(),
		);
		$this->sysconfmodel->viewLayout('feature_manage',$data);
	}
	function AddGroup_Region($rid,$grid=''){
		if($this->input->post('update_system')){
			if($grid!=""){
				$res=$this->systemmodel->update_groupregion($rid,$grid);
				if($res){
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', $this->lang->line('updatesuccessmsg'));
					redirect('ManageGrpRegion/'.$rid);
				}
			}else{
				$res=$this->systemmodel->add_groupregion($rid);
				if($res){
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', $this->lang->line('successmsg'));
					redirect('ManageRegion');
				}
			}
		}
		$codes = $this->systemmodel->get_areacodewise($grid);
		$codelist = array();
		foreach($codes as $code)
			array_push($codelist,$code['code']);
		$data=array('codes'=>$this->systemmodel->get_stdcodes(),
					'groupregions'=>$this->systemmodel->get_groupRegions($rid,$grid),
					'codelist'=>$codelist,
					'rid'=>$rid);
		$this->sysconfmodel->viewLayout('AddGroupRegion',$data);
	}
	function Delete_Region($rid,$grid=''){
		$codes = $this->systemmodel->del_groupregion($rid,$grid);
		redirect('ManageRegion');
	}
	function AddRegion($id=''){
		if($this->input->post('update_system')){
			if($id!=""){
				$res=$this->systemmodel->update_region($id);
				if($res){
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', $this->lang->line('updatesuccessmsg'));
					redirect('ManageRegion');
				}
			}else{
				$res=$this->systemmodel->add_region();
				if($res){
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', $this->lang->line('successmsg'));
					redirect('ManageRegion');
				}
			}
		}
		$data=array('regions'=>$this->systemmodel->get_Regions($id));
		$this->sysconfmodel->viewLayout('AddRegion',$data);
	}
	function manageregion(){
		if($this->input->post('submit')){	
			if($this->input->post('regionname')!=""){
				if($this->session->userdata('regionname')!=""){
					$this->session->unset_userdata('regionname');
				}
				$this->session->set_userdata('regionname',$this->input->post('regionname'));
			}else{
				$this->session->unset_userdata('regionname');
			}
		}
		$data['module']['title'] =$this->lang->line('label_manageregion');
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '30';
		$header = array('#'
						,$this->lang->line('level_regionalgoname')
						,$this->lang->line('level_Action')
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->systemmodel->RegionsList($ofset,$limit);
		$rec = array();
		$i = ($this->uri->segment(4)!=null)?$ofset+$i:1;
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			$rec[] = array($i
				,$item['regionname']
				,'<a href="AddRegion/'.$item['regionid'].'"><span title="Edit" class="fa fa-edit"></span></a>'
				  .' <a href="'.base_url().'AddRegion/'.$item['regionid'].'"><img src="system/application/img/icons/bricks.png" title="Add Group Region" /></a>
				<a href="'.base_url().'ManageGrpRegion/'.$item['regionid'].'"><span class="fa fa-file-text"  title="View Group Region"></span></a>'
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('ManageRegion/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>4					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_manageregion');
		$links = array();
		$links[]='<li><a href="AddRegion/0"><span title="Add Region" class="glyphicon glyphicon-plus-sign">Add Region</span></a></li>';
			$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
	    $formFields = array();
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">'.$this->lang->line('level_regionalgoname').': </label>',
				'field'=>form_input(array(
						'name'      => 'regionname',
						'class'     => 'form-control',
						'id'        => 'regionname',
						'value'     => $this->session->userdata('regionname')
						))
						);
		$data['links'] = $links;
		$data['form'] = array(
			'open'=>form_open_multipart('ManageRegion/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'save_search'=>3,
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function View_GroupRegion($rid){
		if($this->input->post('submit')){	
			if($this->input->post('groupregionname')!=""){
				if($this->session->userdata('groupregionname')!=""){
					$this->session->unset_userdata('groupregionname');
				}
				$this->session->set_userdata('groupregionname',$this->input->post('groupregionname'));
			}else{
				$this->session->unset_userdata('groupregionname');
			}
			if($this->input->post('regionalgosname')!=""){
				if($this->session->userdata('regionalgosname')!=""){
					$this->session->unset_userdata('regionalgosname');
				}
				$this->session->set_userdata('regionalgosname',$this->input->post('regionalgosname'));
			}else{
				$this->session->unset_userdata('regionalgosname');
			}
		}
		$data['module']['title'] =$this->lang->line('level_groupregionname');
		$ofset = ($this->uri->segment(5)!=null)?$this->uri->segment(5):0;
		$limit = '30';
		$header = array('#'
						,$this->lang->line('level_regionalgoname')
						,$this->lang->line('level_groupregionname')
						,$this->lang->line('level_Action')
							);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->systemmodel->GroupRegionsList($rid,$ofset,$limit);
		$rec = array();
		$i = ($this->uri->segment(5)!=null)?$ofset+$i:1;
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			$rec[] = array($i
				,$item['custom_regionname']
				,$item['group_regionname']
				,'<a href="AddGrpRegion/'.$item['regionid'].'/'.$item['gregionid'].'"><span title="Edit" class="fa fa-edit"></span></a> '.
				'<a href="customfield/get_areacodewise/'.$item['regionid'].'/'.$item['gregionid'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="fa fa-file-text"  title="List"></span></a> '
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('ManageGrpRegion/'.$rid)
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>5					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_groupregion');
		$url='AddGrpRegion/'.$rid.'/0';
		$links = array();
		$links[]="<li><a href='".$url."'><span title='Add Number' class='glyphicon glyphicon-plus-sign'>&nbsp;Add Number</span></a></li>";
		$formFields = array();
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">'.$this->lang->line('level_groupregionname').': </label>',
				'field'=>form_input(array(
						'name'      => 'groupregionname',
						'class'     => 'form-control',
						'id'        => 'groupregionname',
						'value'     => $this->session->userdata('groupregionname')
						)));
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">'.$this->lang->line('label_manageregion').': </label>',
				'field'=>form_input(array(
						'name'      => 'regionalgosname',
						'class'     => 'form-control',
						'id'        => 'regionalgosname',
						'value'     => $this->session->userdata('regionalgosname')
						)));
		$data['links']= $links;
		$data['form'] = array(
			'open'=>form_open_multipart('ManageGrpRegion/'.$rid,array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function get_areacodewise($rid,$gid){
		$data=array('codelist'=>$this->systemmodel->get_areacodewise($gid));
		$this->load->view('codeslist',$data);
	}
	function Managecustomfield($mod=''){
		if($this->input->post('update_system')){
			$res=$this->systemmodel->update_system_labels($mod);
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg', $this->lang->line('error_managesucc'));
			redirect('Home');
		}
		$data=array('module_names'=>$this->systemmodel->get_all_modules());	
		$data['demo'] = $this->configuremodel->isConfig();
		$this->sysconfmodel->viewLayout('form_config',$data);
	}	
	function empmod($mod){			
		$modname=$this->systemmodel->get_modules($mod);
		$data['module']['title'] = $this->lang->line('label_'.$modname);
		$data['modlist'] = $this->systemmodel->get_all_modules();
		$fieldset = $this->configmodel->getFields($mod,'','1');
		$formFields = array();
		/* New Code For performance */
		$add_custom = $this->systemmodel->getAddcustom_access($mod);
		if($add_custom == 1){
			$link=base_url()."customfield/addcustomfields/".$mod;
		}else{
			$link='';
		}
		$checked='';
		array_push($formFields,array('label'=>'<label><b>System Label :</b> </label>',
									 'field'=>'<td class="small"><b>Custom Label</b></td>
												<td class="small"><b>Active</b></td>
												<td class="small"><b>Listing</b></td>
												<td class="small"><b>Display Order</b></td>'
					));
				
		foreach($fieldset as $field){
			$show = ($field['show']!=0)? 'checked="checked"':'';
			$listing = ($field['listing']!=0)? 'checked="checked"':'';
			$ls=base_url()."customfield/";
			if($field['type']=='s'){
				$cf = array('label'=>'<label for="'.$field['fieldname'].'">'.$this->lang->line('mod_'.$field['modid'])->$field['fieldname'].' : </label>',
							'field'=>'<td class="small">'.form_input(array(
										  'name'      => 'system[0]['.$field['fieldid'].']',
										  'class'      => 'required form-control',
										  'id'        => $field['fieldname'],
										  'value'     => (($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
								),
								)
						).'</td>'.(($field['is_required'])? 
							('<td class="small">
							<input type="checkbox"  disabled="disabled" id="'.$field['fieldname'].'" name="system[1]['.$field['fieldid'].']" '.$show.' value="1"/>
							<input type="checkbox"  hidden="hidden" id="'.$field['fieldname'].'" name="system[1]['.$field['fieldid'].']" '.$show.' value="1"/></td>')
							:('<td class="small"><input type="checkbox"  id="'.$field['fieldname'].'" name="system[1]['.$field['fieldid'].']" '.$show.' value="1"/></td>')).'
							<td class="small"><input type="checkbox" id="'.$field['fieldname'].'" name="system[3]['.$field['fieldid'].']" '.$listing.' value="1"/></td>'
							.'<td class="small">
							<input type="text" style="width:40px;height:25px;padding:2px;text-align:center;padding:0px;margin:0px;" id="'.$field['fieldname'].'" name="system[2]['.$field['fieldid'].']" value="'.$field['display_order'].'"/>'
							.(($field['fieldname'] == 'lead_status')? '<a href="'.$ls.'edit_lead_status/'.$field['fieldid'].'/'.$mod.'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Edit" class="fa fa-edit"></span></a>' : '').(($field['fieldname'] == 'tkt_level')? '<a href="'.$ls.'edit_ticket_level/'.$field['fieldid'].'/'.$mod.'" class="btn-empl" data-toggle="modal" data-target="#modal-empl"><img src="'.base_url().'system/application/img/icons/edit.png"/></a>' : '').(($field['fieldname'] == 'tkt_status')? '<a href="'.$ls.'edit_tkt_status/'.$field['fieldid'].'/'.$mod.'" class="btn-followup" data-toggle="modal" data-target="#modal-followup"><img src="'.base_url().'system/application/img/icons/edit.png"/></a>' : '').'</td>'
					);
				array_push($formFields,$cf);
			}elseif($field['type']=='c'){
				$cf = array('label'=>'<label for="'.$field['fieldname'].'">'.$field['fieldname'].' : </label>',
							'field'=>'<td>'.form_input(array(
										  'name'      => 'custom[0]['.$field['fieldid'].']',
										  'id'        => $field['fieldname'],
										  'class'      => 'required form-control',
										  'value'     => $field['customlabel'],
								)
								
						).'</td>'.('<td class="small">
						   <input type="checkbox"  id="'.$field['fieldname'].'" name="custom[1]['.$field['fieldid'].']" '.$show.' value="1"/></td>').'
							<td class="small">
							<input type="checkbox" id="'.$field['fieldname'].'" name="custom[3]['.$field['fieldid'].']" '.$listing.' value="1"/></td>'
							.'<td class="small">
							<input style="width:40px;height:25px;padding:2px;text-align:center;margin:0px;padding:0px;" type="text" id="'.$field['fieldname'].'" name="custom[2]['.$field['fieldid'].']" value="'.$field['display_order'].'"/>
							<a href="'.$ls.'delete_custom/'.$field['fieldid'].'/'.$mod.'" class="DelConfirm"><span title="Delete" class="glyphicon glyphicon-remove"></span></a>
							<a href="'.$ls.'edit_custom/'.$field['fieldid'].'/'.$mod.'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Edit" class="glyphicon glyphicon-pencil"></span></a></td>'
					);
				array_push($formFields,$cf);
			}
		}
		$data['URL'] = $link;
		$data['form'] = array('open'=>form_open_multipart('customfield/Managecustomfield/'.$mod,array('name'=>'customform','id'=>'customform','class'=>'form','method'=>'post')),'fields'=>$formFields,'close'=>form_close());
		$data['modids']=$mod;
		$this->load->view('form_view_settings',$data);
	}	
	function addcustomfields($str){
		$data=array('str'=>$str);
		$this->load->view('addcustom',$data);
	}	
	function Addcustom(){
		$res = $this->systemmodel->addcustomfield();
		echo $res;exit;
	}	
	function delete_custom($id,$modid){
		$res=$this->systemmodel->delete_customfields($id,$modid);
		echo $res; 
	}
	function edit_custom($id,$modid){
		$modid = ($modid == 46) ? '26' : $modid;
		if($this->input->post('Update_CustomField')){
			$res=$this->systemmodel->update_custom_label($id,$modid);
			redirect('ManageCustom');
		}
		$res=$this->systemmodel->get_custom_info($id,$modid);
		$data=array("fiedls"=>$res,'id'=>$id,'modid'=>$modid);
		$this->load->view("editcustom",$data);
	}
	function edit_lead_status($id,$modid){
		if($this->input->post('Update_leadStatus')){
			$res=$this->systemmodel->update_lead_status($id,$modid);
			redirect('ManageCustom');
		}
		$res=$this->systemmodel->get_leadstatus_info();
		$res_label=$this->systemmodel->get_leadstatus_info_label();
		$data=array("fields"=>$res,'id'=>$id,'modid'=>$modid,"label_name"=>$res_label);
		$this->load->view("lead_status",$data);
	}
	function edit_ticket_level($id,$modid){
		if($this->input->post('Update_supportLevel')){
			$res=$this->systemmodel->update_ticket_level($id,$modid);
			redirect('ManageCustom');
		}
		$res=$this->systemmodel->get_tkt_levels();
		$data=array("fields"=>$res,'id'=>$id,'modid'=>$modid);
		$escProcess = $this->systemmodel->getSupEscBusiness();
		if($escProcess == 1){
			$res_label=$this->systemmodel->get_tkt_levels_time();
			$data['escalation'] = $escProcess;
			$data['times'] = $res_label;
		}
		$this->load->view("sup_tktLevels",$data);
	}
	function edit_tkt_status($id,$modid){
		if($this->input->post('UpdateSuptktStatus')){
			$res=$this->systemmodel->update_suptkt_status($id,$modid);
			redirect('ManageCustom');
		}
		$res = $this->systemmodel->getSuptktstatus();
		$data=array("fields"=>$res,'id'=>$id,'modid'=>$modid);
		$this->load->view("sup_tkt_status",$data);
	}
	function Editcustom($id,$modid){
		$res=$this->systemmodel->updateCustom($id,$modid);
	}
	function EditTktLel($id,$modid){
		$res=$this->systemmodel->update_ticket_level($id,$modid);
	}
	function EditLeadstat($id,$modid){
		$res=$this->systemmodel->update_lead_status($id,$modid);
	}
	function EditTktStatus($id,$modid){
		$res=$this->systemmodel->update_suptkt_status($id,$modid);
	}
	function system_rolelist(){
		$res=$this->systemmodel->get_emp_roles();
		$data=array('rolelist'=>$res);
		$this->sysconfmodel->viewLayout('rolelist',$data);
	}
	function custommange($mod){
		if($this->input->post('update_system')){
			$res=$this->systemmodel->update_system_labels($mod);
			echo "1";
		}
	}
	function package_listing(){
		$sms_balance=$this->systemmodel->get_smsbalance();
		$call_balance=$this->systemmodel->get_call_balance();
		$this->paypal_lib->add_field('business', 'chatte_1217057572_biz@gmail.com');
		$this->paypal_lib->add_field('return', site_url('customfield/upgrade_package/'));
		$this->paypal_lib->add_field('cancel_return', site_url('customfield/package_listing/'));
		$this->paypal_lib->image('button_01.gif');
		$data=array(
					'sms_balance'=>$sms_balance,
					'call_balance'=>$call_balance,
					'packages'=> $this->commonmodel->get_packages(),
					'paypal_form'=> $this->paypal_lib->paypal_form()
				);
		$this->sysconfmodel->viewLayout('package_upgrade',$data);		
	}
	function upgrade_package(){
		if(isset($_REQUEST)){
			$this->systemmodel->upgrade_info($_REQUEST);
			redirect('customfield/package_listing');
		}
	}
	function auditlog(){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid != "") ? $cbid : $this->session->userdata('bid');
		if($this->input->post('submit')){	
			if($this->input->post('uname')!=""){
				if($this->session->userdata('uname')!=""){
					$this->session->unset_userdata('uname');
				}
				$this->session->set_userdata('uname',$this->input->post('uname'));
			}else{
				$this->session->unset_userdata('uname');
			}
			if($this->input->post('module')!=""){
				if($this->session->userdata('module')!=""){
					$this->session->unset_userdata('module');
				}
				$this->session->set_userdata('module',$this->input->post('module'));
			}else{
				$this->session->unset_userdata('module');
			}
			if($this->input->post('datefrom')!=""){
				if($this->session->userdata('datefrom')!=""){
					$this->session->unset_userdata('datefrom');
				}
				$this->session->set_userdata('datefrom',$this->input->post('datefrom'));
			}else{
				$this->session->unset_userdata('datefrom');
			}
			if($this->input->post('dateto')!=""){
				if($this->session->userdata('dateto')!=""){
					$this->session->unset_userdata('dateto');
				}
				$this->session->set_userdata('dateto',$this->input->post('dateto'));
			}else{
				$this->session->unset_userdata('dateto');
			}
		}
		if($this->input->post('download')){
			$filename = $this->auditlog->auditDownload($bid);
			$dlink =  "<a href='".site_url("reports/".$filename.".zip")."' target='_blank' style='color:#fff'><b>Download</b></a>  ";
		}else{
			$dlink = "";
		}
		$data['module']['title'] ="Audit Log";
		$ofset = ($this->uri->segment(2)!=null)?$this->uri->segment(2):0;
		$limit = '30';
		$header = array('#',
						'Username',
						'Module Name',
						'Action',
						'Date & time'
		);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->auditlog->get_autdit_log($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;
		foreach ($emp_list['data'] as $item){
			$rec[] = array($i
						  ,$item['username']
						  ,$item['module_name']
						  ,$item['action']
						  ,$item['date_time']
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('AuditTrail/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>2					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | Audit Log";
		$links = array();
		$links[] ='<li><a href="customfield/auditDown/" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Download" class="glyphicon glyphicon-download-alt">&nbsp;Download All</span></a>';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$formFields1 = array();
		$cf=array('label'=>'<label class="col-sm-4 control-label" for="groupname">'.$this->lang->line('level_groupname').' : </label>',
				  'field'=>form_input(array(
									  'name'        => 'groupname',
									  'class'       => 'form-control',
									  'id'          => 'groupname',
									  'value'       => $this->session->userdata('groupname'))));
		array_push($formFields1,$cf);
		$formFields = array();
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 control-label" for="f">username : </label>',
				'field'=>form_input(array(
						'name'      => 'uname',
					    'class'     => 'form-control',
						'id'        => 'uname',
						'value'     => $this->session->userdata('uname')
						)));
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 control-label" for="f">Module : </label>',
				'field'=>form_input(array(
						'name'      => 'module',  
						'class'        => 'form-control',
						'id'        => 'module',
						'value'     => $this->session->userdata('module')
						)));			
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 control-label" for="f">Date From : </label>',
				'field'=>form_input(array(
						'name'      => 'datefrom',
						'id'        => 'datefrom',
						'class'     => 'datepicker_leads form-control',
						'value'     => $this->session->userdata('datefrom')
						)));	
		$formFields[] = array(
				'label'=>'<label  class="col-sm-4 control-label" for="f">Date To : </label>',
				'field'=>form_input(array(
						'name'      => 'dateto',
						'id'        => 'dateto',
						'class'        => 'datepicker_leads form-control',
						'value'     =>($this->session->userdata('dto')!="")?$this->session->userdata('dto'):date('Y-m-d')
						)));
		$data['downlink'] =  $dlink;	
		$data['links'] =  $links;	
		$data['form'] = array(
							'open'=>form_open_multipart('AuditTrail/0',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->viewLayout('list_view',$data);	
	}
	function auditDown(){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$this->load->view('auditDownload');
	}
}
/* end of custom field controller */
