<?php
class Scn extends Controller {
	var $data,$roleDetail;
	function Scn()
	{
		parent::controller();
		//if(!$this->session->userdata('logged_in'))redirect('/user');
		if(!$this->session->userdata('logged_in'))redirect('/site/login');
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('scnmodel');
		$this->load->model('configmodel');
		$this->load->model('empmodel');
		$this->load->model('systemmodel');
		$this->roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if(!$this->feature_access())redirect('Employee/access_denied');
	}
	
	function index(){
		$this->manage();
	}
	function feature_access()
	{
		$show=0;
		$checklist=$this->systemmodel->checked_featuremanage();
		if(in_array(9,$checklist)){
			$show=1;
			}
		return $show;
	}
	function manage(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['21']['opt_view']) redirect('Employee/access_denied');
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('label_scnlist');
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_scnlist');
		$this->sysconfmodel->data['file'] = "system/application/js/scn.js.php";
		$this->sysconfmodel->data['links'] = '<a href="scn/add"><span class="glyphicon glyphicon-plus-sign" title="Add Ivrs"></span></a>';
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '20';
		$data['itemlist'] = $this->scnmodel->getScnlist($bid,$ofset,$limit);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('scn/manage')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
		$data['paging'] = $this->pagination->create_links();
		$fieldset = $this->configmodel->getFields('21');
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='filename'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>form_input(array(
											'name'      => $field['fieldname'],
											'id'        => $field['fieldname']))
											);
			}
		}
		
		$data['form'] = array(
					'open'=>form_open('ivrs/manage',array('name'=>'search','class'=>'form','id'=>'search','method'=>'post')),
					'title'=>'IVRS Search',
					'form_field'=>$formFields,
					'close'=>form_close()
				);
		
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	
	function addFrm(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['21']['opt_add']) redirect('Employee/access_denied');
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('title', 'title', 'required|min_length[2]|max_length[32]|alpha_numeric');
			$this->form_validation->set_rules('prinumber', 'Landing Number', 'required');
			if(!$this->form_validation->run() == FALSE){
				if(count($_POST)>0){
					$get = $this->scnmodel->addscn();
					redirect('scn/members/'.$get['scnid']);
				}
			}	
		}		
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_scnadd');
		$this->sysconfmodel->data['file'] = "system/application/js/scn.js.php";
		$data['module']['title'] = $this->lang->line('label_scnadd');
		$fieldset = $this->configmodel->getFields('21');
		//echo "<pre>";print_r($fieldset);
		$formFields = array();
		$itemId = ($this->uri->segment(3)!=null)?$this->uri->segment(3):"";
		$itemDetail = ($itemId!='') ? $this->configmodel->getDetail('21',$itemId): array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(
											 ($field['customlabel']!="")
											 ?$field['customlabel']
											 :$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp: </label>',
									'field'=>(
											($field['fieldname']=='prinumber')
											?form_dropdown('prinumber',$this->systemmodel->getPriList(isset($itemDetail['prinumber'])?$itemDetail['prinumber']:set_value($field['fieldname'])),(isset($itemDetail['prinumber'])?$itemDetail['prinumber']:''),"id='prinumber' class='auto'")
											:form_input(array(
												'name'      => $field['fieldname'],
												'id'        => $field['fieldname'],
												'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:set_value($field['fieldname']),
												))
											)
									);
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked)$formFields[] = array(
								'label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createField($field,isset($itemDetail['custom['.$field['fieldid'].']'])?$itemDetail['custom['.$field['fieldid'].']']:""));
			}
		}
		$data['form'] = array(
					'open'=>form_open_multipart('scn/add'
								,array('name'=>'form','class'=>'form','id'=>'scnadd','method'=>'post')
								,array('scnid'=>$itemId
								  ,'bid'=>$this->session->userdata('bid')
								  ,'modid'=>'21'
								)),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		return $data;
	}

	function add(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['21']['opt_add']) redirect('Employee/access_denied');
		$this->sysconfmodel->viewLayout('form_view',$this->addFrm());
	}
	function addpopup(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['21']['opt_add']) redirect('Employee/access_denied');
		$this->load->view('form_view',$this->addFrm());
	}
	function members(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['22']['opt_add']) redirect('Employee/access_denied');
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_scnmembers');
		$this->sysconfmodel->data['file'] = "system/application/js/scn.js.php";
		$this->sysconfmodel->data['links'] = '<a  class="btn-danger" data-toggle="modal" data-target="#modal-responsive" href="'."scn/member/".$this->uri->segment(3).'"><span class="glyphicon glyphicon-plus-sign" title="Add Option"></span></a>';
		$data['module']['title'] = $this->lang->line('label_scnmembers');
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '20';
		$args = array(
				 'bid' => $this->session->userdata('bid')
				,'scnid'=>(($this->uri->segment(3)!=null)?$this->uri->segment(3):'0')
				,'ofset'=>$ofset
				,'limit'=>$limit
				);
		$data['itemlist'] = $this->scnmodel->getMembers($args);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('scn/members/'.$args['scnid'])
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit
						,'uri_segment'=>4
				));
		$data['paging'] = $this->pagination->create_links();
		$fieldset = $this->configmodel->getFields('22');
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='optsound' && $field['fieldname']!='targettype'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>form_input(array(
											'name'      => $field['fieldname'],
											'id'        => $field['fieldname']))
											);
			}
		}
		
		$data['form'] = array(
					'open'=>form_open('scn/members/'.$args['scnid'],array('name'=>'search','class'=>'form','id'=>'search','method'=>'post')),
					'title'=>'Member Search',
					'form_field'=>$formFields,
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function memberFrm(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['22']['opt_add']) redirect('Employee/access_denied');
		if(count($_POST)>0){
			$this->scnmodel->addMember();
			redirect($_SERVER['HTTP_REFERER']);
		}
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_scnmember');
		$data['module']['title'] = $this->lang->line('label_scnmember');
		$fieldset = $this->configmodel->getFields('22');
		$formFields = array();
		$itemId = ($this->uri->segment(4)!=null)?$this->uri->segment(4):"";
		$itemDetail = ($itemId!='') ? $this->configmodel->getDetail('22',$itemId) : array();
		foreach($fieldset as $field){$checked = false;
			if($field['type']=='s' && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>(form_input(array(
											'name'      => $field['fieldname'],
											'id'        => $field['fieldname'],
											'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:'',
											'class'		=> ($field['fieldname']=='optsound' && !isset($itemDetail[$field['fieldname']]))?'required':''
											)))." <img src='system/application/img/icons/help.png' title='".$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname']."'>");
				
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked)$formFields[] = array(
								'label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createField($field,isset($itemDetail['custom['.$field['fieldid'].']'])?$itemDetail['custom['.$field['fieldid'].']']:""));
			}
		}
		$data['form'] = array(
					'open'=>form_open('scn/member'
								,array('name'=>'addmember','class'=>'form','id'=>'addmember','method'=>'post')
								,array('mid'=>$itemId
								  ,'bid'=>$this->session->userdata('bid')
								  ,'scnid'=>($this->uri->segment(3)!=null)?$this->uri->segment(3):""
								  ,'modid'=>'22'
								)),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		return $data;
	}
	function member(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['22']['opt_add']) redirect('Employee/access_denied');
		$this->load->view('form_view',$this->memberFrm());
	}
	function deletemem($mid=''){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['22']['opt_delete']) redirect('Employee/access_denied');
		if($mid!='') $this->scnmodel->delMember($mid);
		redirect($_SERVER['HTTP_REFERER']);
	}
}
?>

