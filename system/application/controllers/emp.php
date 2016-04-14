<?php
class Emp extends Controller {
	var $data;
	function Emp()
	{
		parent::controller();
		//if(!$this->session->userdata('logged_in'))redirect('/user');
		if(!$this->session->userdata('logged_in'))redirect('/site/login');
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('configmodel');
		$this->load->model('empmodel');
		$this->load->model('systemmodel');
	}
	
	function index(){
		$this->role();
	}
	
	function roleFrom(){
		$data['module']['title'] = $this->lang->line('emprole');
		$formFields = array();
		$roleid = ($this->uri->segment(2)!=null)?$this->uri->segment(2):0;
		$roleDetail = $this->empmodel->getRoledetail($roleid);
		$cf = array('label'=>'<label  for="rolename">'.$this->lang->line('rolename').' : </label>',
					'field'=>form_input(array(
								  'name'      => 'rolename',
								  'class'	  => 'required form-control',
								  'id'        => 'rolename',
								  'value'     => isset($roleDetail['role']['rolename'])?$roleDetail['role']['rolename']:""
						)
				)." <img src='system/application/img/icons/help.png' title='Name of the new role to be created. Select different entitlements from below check boxes.'>");
		array_push($formFields,$cf);
		$cf = array('label'=>'<label  for="recordlimit">'.$this->lang->line('recordlimit').' : </label>',
					'field'=>form_input(array(
								  'name'      => 'recordlimit',
								  'class'	  => 'form-control',
								  'id'        => 'recordlimit',
								  'value'     => isset($roleDetail['role']['recordlimit'])?$roleDetail['role']['recordlimit']:""
						)
				)." <img src='system/application/img/icons/help.png' title='Threshold of the maximum number of records this entitled user is allowed to see at one time.'>");
		array_push($formFields,$cf);
		$cf = array('label'=>'<label for="owngroup">'.$this->lang->line('level_coadmin').' : </label>',
					'field'=>form_checkbox(array(
											  'name'	=> 'coadmin',
											  'id'		=> 'coadmin',
											  'value'	=> 1,
											  'checked'	=> (isset($roleDetail['role']['admin']) && $roleDetail['role']['admin']==1)? true:false,
									)
							)." <img src='system/application/img/icons/help.png' title='Act As an Administrator'>");
		array_push($formFields,$cf);
		$cf = array('label'=>'<label for="owngroup">'.$this->lang->line('level_owngroup').' : </label>',
					'field'=>form_checkbox(array(
											  'name'	=> 'owngroup',
											  'id'		=> 'owngroup',
											  'value'	=> 1,
											  'checked'	=> (isset($roleDetail['role']['owngroup']) && $roleDetail['role']['owngroup']==1)? true:false,
									)
							)." <img src='system/application/img/icons/help.png' title='Access own groups only'>");
		array_push($formFields,$cf);
		$cf = array('label'=>'<label for="accessrecords">'.$this->lang->line('level_accessrecords').' : </label>',
					'field'=>form_checkbox(array(
											  'name'	=> 'accessrecords',
											  'id'		=> 'accessrecords',
											  'value'	=> 1,
											  'checked'	=> (isset($roleDetail['role']['accessrecords']) && $roleDetail['role']['accessrecords']==1)? true:false,
									)
							)." <img src='system/application/img/icons/help.png' title='Can not access Record files'>");
		array_push($formFields,$cf);
		$modFields = array();
		foreach($roleDetail['modules'] as $mod){
			$modFields[$mod['modid']]=$mod;
			$fieldset = $this->configmodel->getFields($mod['modid'],'','1');
			foreach($fieldset as $field){
				$checked = false;
				if($field['type']=='s' && $field['show']){
					foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					$cf = array('label'=>'&nbsp;<label for="s_'.$field['fieldid'].'">'.(($field['customlabel']!="")
											?$field['customlabel']
											:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).'</label>',
								'field'=>(form_checkbox(array(
											  'name'	=> 'module['.$mod['modid'].'][system]['.$field['fieldid'].']',
											  'id'		=> 's_'.$field['fieldid'],
											  'class'	=> ' chk_'.$mod['modid'],
											  'value'	=> 1,
											  'checked'	=> ($checked)? 'checked' : ''
									))));
					$modFields[$mod['modid']]['fields'][]=$cf;
				}elseif($field['type']=='c' && $field['show']){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					$cf = array('label'=>'&nbsp;<label for="c_'.$field['fieldid'].'">'.$field['customlabel'].'</label>',
								'field'=>form_checkbox(array(
											  'name'	=> 'module['.$mod['modid'].'][custom]['.$field['fieldid'].']',
											  'id'		=> 'c_'.$field['fieldid'],
											  'class'	=> 'chk_'.$mod['modid'],
											  'value'	=> 1,
											  'checked'	=> ($checked)? 'checked' : '',						 
									)));
					$modFields[$mod['modid']]['fields'][]=$cf;
				}
			}
		}
		$data['form'] = array(
		            'form_attr'=>array('action'=>'emp/roleadd','name'=>'form','id'=>'form','enctype'=>"multipart/form-data"),
					'hidden' =>array('roleid'=>(isset($roleDetail['role']['roleid']) ? $roleDetail['role']['roleid'] : ''),
								  'bid'=>(isset($roleDetail['role']['bid']) ? $roleDetail['role']['bid'] : $this->session->userdata('bid'))),
					'fields'=>$formFields,
					'modfields'=>$modFields,
					'close'=>form_close()
				);
		return $data;
		
	}
	function role(){
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('emprole');
		$this->sysconfmodel->viewLayout('emprole_view',$this->roleFrom());
	}
	function rolepopup(){
		$this->load->view('emprole_view',$this->roleFrom());
	}
	function roleadd(){
		$roleid = $this->empmodel->addrole();
		$flashdata = array('msgt' => 'success', 'msg' => 'Role Updated Successfully');
		$this->session->set_flashdata($flashdata);
		redirect('ManageRole');
	}
}
?>

