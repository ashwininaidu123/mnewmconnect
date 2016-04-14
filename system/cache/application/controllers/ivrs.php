<?php
class Ivrs extends Controller {
	var $data,$roleDetail;
	function Ivrs(){
		parent::controller();
		if(!$this->session->userdata('logged_in'))redirect('/site/login');
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('ivrsmodel');
		$this->load->model('configmodel');
		$this->load->model('empmodel');
		$this->load->model('systemmodel');
		$this->load->model('reportmodel');
		$this->load->model('leadsmodel');
		$this->load->model('supportmodel');
		$this->roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if(!$this->feature_access())redirect('Employee/access_denied');
	}
	public function __destruct() {
		$this->db->close();
	}	
	function index(){
		$this->manage();
	}
	function feature_access(){
		$show=0;
		$checklist=$this->systemmodel->checked_featuremanage();
		if(in_array(2,$checklist)){
			$show=1;
		}
		return $show;
	}
	function manage(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['4']['opt_view']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_ivrsmanage');
		$links = array();
		$links[] = '<li><a href="IVRSadd"><span title="Add IVRS" class="glyphicon glyphicon-plus-sign">&nbsp;Add IVRS</span></a></li>';
			$links[] = ($roleDetail['modules']['2']['opt_delete']) ?'<li><a href="ivrs/bulkDel" class="blkDel"><span title="Bulk Delete" class="glyphicon glyphicon-trash">&nbsp;Delete</span></a></li>':'';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search"class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$data['itemlist'] = $this->ivrsmodel->getIvrslist($bid,$ofset,$limit);
		$data['module']['title'] = $this->lang->line('label_ivrsmanage'). "[".$data['itemlist']['count']."]";
		$this->pagination->initialize(array(
						 'base_url'=>site_url('ManageIVRS')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
		$data['paging'] = $this->pagination->create_links();
		$fieldset = $this->configmodel->getFields('4');
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='filename'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) { 
					$formFields[] = array(
									'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>form_input(array(
											'name'      => $field['fieldname'],
											'class'      => 'form-control',
											'id'        => $field['fieldname']))
											);
				}				
	
			}elseif($field['type']=='c' && $field['show']){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked)$formFields[] = array(
							'label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
							'field'=>$this->configmodel->createFieldAdvance($field,isset($itemDetail[$field['fieldKey']]) ? $itemDetail[$field['fieldKey']] : '',''));
			}
		}
		$data ['links'] =$links;
		$data['form'] = array(
					'open'=>form_open('ManageIVRS',array('name'=>'search','class'=>'form','id'=>'search','method'=>'post')),
					'title'=>'IVRS Search',
					'form_field'=>$formFields,
					'parentids'=>$parentbids,
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close()
				);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
   function bulkDel(){
		$res=$this->ivrsmodel->blk_del($_POST['leadids']);
		echo "1";
	}
	function deleted(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['4']['opt_view']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_ivrsdeleted');
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$data['itemlist'] = $this->ivrsmodel->getIvrslistDeleted($bid,$ofset,$limit);
		$data['module']['title'] = $this->lang->line('label_ivrsdeleted').'['.$data['itemlist']['count'].']';
		$this->pagination->initialize(array(
						 'base_url'=>site_url('IVRSDelete')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
		$links = array();
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$data['paging'] = $this->pagination->create_links();
		$fieldset = $this->configmodel->getFields('4',$bid);
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='filename'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) { 
					$formFields[] = array(
									'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>form_input(array(
											'name'      => $field['fieldname'],
											'class'     => 'form-control',
											'id'        => $field['fieldname']))
											);
				}	
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$formFields[] = array(
						'label'=>'<label class=" col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
						'field'=>$this->configmodel->createFieldAdvance($field,'','search',"class='form-control'"));
				}
			}
		}
	    $data['links']= $links;
		$data['form'] = array(
					'open'=>form_open('IVRSDelete',array('name'=>'search','class'=>'form','id'=>'search','method'=>'post')),
					'title'=>'IVRS Search',
					'form_field'=>$formFields,
					'parentids'=>$parentbids,
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close()
				);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
     function addFrm(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['4']['opt_add']) redirect('Employee/access_denied');
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('title', 'title', 'required|min_length[4]|max_length[32]|alpha_numeric');
			$this->form_validation->set_rules('prinumber', 'Landing Number', 'required');
			$this->form_validation->set_rules('timeout', 'Time out', 'required|numeric');
			if(!$this->form_validation->run() == FALSE){
				if(count($_POST)>0){
					if(($_FILES['filename']['error']!=0
						|| $_FILES['filename']['size']=='0') && $_POST['ivrsid']==''){
						$this->session->set_flashdata(array('msgt' => 'error','msg' => $this->lang->line('ivrs_errorfile')));
						redirect($_SERVER['HTTP_REFERER']);
					}
					$get = $this->ivrsmodel->addivrs();
					redirect('ListOption/'.$get['ivrsid'].'/'.$get['optid']);
				}
			}	
		}		
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_ivrsconfig');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$data['module']['title'] = $this->lang->line('label_ivrsconfig');
		$fieldset = $this->configmodel->getFields('4',$bid);
		$formFields = array();
		$itemId = ($this->uri->segment(3)!=null)?$this->uri->segment(3):"";
		$itemDetail = $this->configmodel->getDetail('4',$itemId,'',$bid);
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
									'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp: </label>',
									'field'=>(
											($field['fieldname']=='filename')?
											form_input(array(
											'name'      => $field['fieldname'],
											'id'        => $field['fieldname'],
											'class'		=> 'required',
											'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:'',
											'type'	  => 'file'
											)).((isset($itemDetail[$field['fieldname']]))? 
											'<a target="_blank" href="'.site_url('sounds/'.$itemDetail[$field['fieldname']]).'"> <span title="Sound" class="fa fa-volume-up"></span></a>':'')
											:(($field['fieldname']=='ivrsnumber')?
											form_dropdown('prinumber',$this->systemmodel->getPriList(isset($itemDetail['prinumber'])?$itemDetail['prinumber']:set_value($field['fieldname']),'3'),((isset($itemDetail['prinumber']) && $itemDetail['prinumber']>10000) ? $itemDetail['prinumber'] : '0'),"id='prinumber' class = 'form-control required'")
											:form_input(array(
											'name'      => $field['fieldname'],
											'id'        => $field['fieldname'],
											'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:set_value($field['fieldname']),
											'class'     => ($field['fieldname'] == 'api') ? 'form-control' : 'form-control required' 
											))
											))
											);
			}elseif($field['type']=='c' && $field['show']){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked)$formFields[] = array(
							'label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
							'field'=>$this->configmodel->createFieldAdvance($field,isset($itemDetail[$field['fieldKey']]) ? $itemDetail[$field['fieldKey']] : '',''));
				
			}
		}
		$data['form'] = array(
		        'form_attr'=>array('action'=>'IVRSadd','name'=>'ivrsadd','id'=>'ivrsadd','enctype'=>"multipart/form-data"),
				'hidden'=>array('ivrsid'=>$itemId
							  ,'bid'=>$bid
							  ,'optid'=>isset($itemDetail['optid'])?$itemDetail['optid']:''
							  ,'modid'=>'4'
							),
				'fields'=>$formFields,
				'parentids'=>($itemId=='')?$parentbids:'',
				'busid'=>$bid,
				'pid'=>$this->session->userdata('pid'),
				'close'=>form_close()
				);
		return $data;
	}
	function checkfiletype($filetype){
		echo $filetype;exit;
	}
	function add(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['4']['opt_add']) redirect('Employee/access_denied');
		$this->sysconfmodel->viewLayout('form_view',$this->addFrm());
	}
	function addpopup(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['4']['opt_add']) redirect('Employee/access_denied');
		$this->load->view('form_view2',$this->addFrm());
	}
	function options(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['5']['opt_add']) redirect('Employee/access_denied');
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_ivrsoption');
		$data['module']['title'] = $this->lang->line('label_ivrsoption');
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$limit = '30';
		$args = array(
				 'bid' => $bid
				,'ivrsid'=>(($this->uri->segment(2)!=null)?$this->uri->segment(2):'-1')
				,'parentopt'=>(($this->uri->segment(3)!=null)?$this->uri->segment(3):'-1')
				,'ofset'=>$ofset
				,'limit'=>$limit
				);
		$data['itemlist'] = $this->ivrsmodel->getOptions($args);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('ListOption/'.$args['ivrsid'].'/'.$args['parentopt'])
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>5
				));
		$data['paging'] = $this->pagination->create_links();
		$links = array();
		$links[] = '<li><a class="btn-danger" data-toggle="modal" data-target="#modal-responsive" href="'."AddOption/".$args['ivrsid'].'/'.$args['parentopt'].'/0"><span title="Add Option" class="glyphicon glyphicon-plus-sign">&nbsp; Add Option</span></a></li>';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$fieldset = $this->configmodel->getFields('5',$bid);
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && !in_array($field['fieldname'],array('optsound','targettype'))){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) 
				$formFields[] = array(
						'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
								 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
						'field'=>form_input(array(
								'name'      => $field['fieldname'],
								'class'     => 'form-control',
								'id'        => $field['fieldname']))
								);
			}
		}
		$data['form'] = array(
					'open'=>form_open('ListOption/'.$args['ivrsid'].'/'.$args['parentopt'],array('name'=>'search','class'=>'form','id'=>'search','method'=>'post')),
					'title'=>'IVRS Search',
					'form_field'=>$formFields,
					'adv_search'=>array(),
					'close'=>form_close()
				);
		$data['links'] = $links;
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function addoptFrm(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['5']['opt_add']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		if(isset($_POST['update_system'])){
			if($_FILES['optsound']['error']!=0 && $_POST['optid']==''){
				$this->session->set_flashdata(array('msgt' => 'error','msg' => $this->lang->line('ivrs_errorfile')));
				redirect($_SERVER['HTTP_REFERER']);
			}
			if($_POST['optid']!=''){
				$result = $this->ivrsmodel->editOption();
				$msg = "Option Updated succefully";
			}else{
				$result = $this->ivrsmodel->addOption();
				$msg = "Option Inserted succefully";
			}
			if($result == 1){
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg',$msg );
			}elseif($result == 2){
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', "The option key already mapped, please select another key");
			}
			redirect($_SERVER['HTTP_REFERER']);
		}
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_ivrsoption');
		$data['module']['title'] = $this->lang->line('label_ivrsoption');
		$fieldset = $this->configmodel->getFields('5',$bid);
		$formFields = array();
		$itemId = ($this->uri->segment(4)!=null)?$this->uri->segment(4):"";
		$itemDetail = $this->configmodel->getDetail('5',$itemId,'',$bid);
		foreach($fieldset as $field){$checked = false;
			if($field['type']=='s' && $field['show'] && !in_array($field['fieldname'],array('targeteid'))){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
									'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>(($field['fieldname']=='optorder')?
											form_dropdown($field['fieldname'],
													array( '1'=> '1'
														  ,'2'=> '2'
														  ,'3'=> '3'
														  ,'4'=> '4'
														  ,'5'=> '5'
														  ,'6'=> '6'
														  ,'7'=> '7'
														  ,'8'=> '8'
														  ,'9'=> '9'
													),
													isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:'','id="'.$field['fieldname'].'" class="form-control"')
									:(($field['fieldname']=='targettype')
									?form_dropdown($field['fieldname'],
									array( ''=>'Select Type'
										  ,'list'=> 'List'
										  ,'employee'=> 'Employee'
										  ,'group'=> 'Group'
										  ,'pbx'=> 'PBX'
										  ,'sms'=> 'SMS'
										  ,'api'=> 'Api'
										  ,'input'=> 'Connect Reference'
										  ,'previous'=> 'Previous'
										  ,'main'=> 'Main'
										  ,'voicemgs'=> 'Voicemgs'
										  ,'hangup'=> 'Hangup'
									),isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:'','id="'.$field['fieldname'].'" class="form-control"')
									:(
									form_input(array(
											'name'      => $field['fieldname'],
											'id'        => $field['fieldname'],
											'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:'',
											'type'		=> ($field['fieldname']=='optsound')? 'file':'text',
											'class'		=> ($field['fieldname']=='optsound' && !isset($itemDetail[$field['fieldname']])) ? 'required audfil':(($field['fieldname']=='opttext') ? 'form-control' :'')
											)).(($field['fieldname']=='optsound' && isset($itemDetail[$field['fieldname']]))? 
											'<a href="'.site_url('sounds/'.$itemDetail[$field['fieldname']]).'"><span class="fa fa-volume-up" title="Sound"></span></a>':'')))
									)." <img src='system/application/img/icons/help.png' title='".$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname']."'>");

				}elseif($field['type']=='c' && $field['show']){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}		
					if($checked)$formFields[] = array(
							'label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
							'field'=>$this->configmodel->createFieldAdvance($field,isset($itemDetail[$field['fieldKey']]) ? $itemDetail[$field['fieldKey']] : '',''));
			}
		}
		$arr1 = array(" "=> "Select");
		$arr = form_dropdown('targetid',$arr1,'','id="targetid" class="form-control"');
		if(isset($itemDetail[$field['fieldname']]) && $itemDetail[$field['fieldname']]=='group'){
			$arr = form_dropdown('targeteid',$this->systemmodel->get_groups(),isset($itemDetail['targeteid'])?$itemDetail['targeteid']:'','id="targeteid" class="form-control"');
		}
		if(isset($itemDetail[$field['fieldname']]) && $itemDetail[$field['fieldname']]=='employee'){
			$arr =form_dropdown('targeteid',$this->systemmodel->get_emp_list(),isset($itemDetail['targeteid'])?$itemDetail['targeteid']:'','id="targeteid" class="form-control"');
		}
		if(isset($itemDetail[$field['fieldname']]) && $itemDetail[$field['fieldname']]=='pbx'){
			$arr = form_dropdown('targeteid',$this->systemmodel->get_pbx_list(),isset($itemDetail['targeteid'])?$itemDetail['targeteid']:'','id="targeteid" class="form-control"');
		}
		if(isset($itemDetail[$field['fieldname']]) && $itemDetail[$field['fieldname']]=='api'){
			$arr = form_input(array('name' => 'api_url','id'=>'api_url','value'=> isset($itemDetail['api_url'])?$itemDetail['api_url']:'','class'=>'required form-control'));
		}
		if(isset($itemDetail[$field['fieldname']]) && $itemDetail[$field['fieldname']]=='sms'){
			$arr = form_textarea(array('name' => 'sms_text','id'=>'sms_text','value'=> isset($itemDetail['sms_text'])?$itemDetail['sms_text']:'','class'=>'required form-control','maxlength'=>'140'));
		}
		if(isset($itemDetail[$field['fieldname']]) && $itemDetail[$field['fieldname']]=='list'){
			$arr = '';
		}
		$formFields[] = array(
					'label'=>'<label class="col-sm-4 text-right" for="targetid" id="testlabel"> Target: </label>',
					'field'=>'<div id="tlabel">'.$arr.'</div>');
		
		$data['form'] = array(
		            'form_attr'=>array('action'=>'AddOption/0/0/0','id'=>'addopt','name'=>'addopt','enctype'=>"multipart/form-data"),
					'hidden'=>array('optid'=> $itemId
								  ,'bid'=> $bid
								  ,'extURL'=>'ivrs/ivrsTarget'
								  ,'ivrsid'=>($this->uri->segment(2)!=null)?$this->uri->segment(2):""
								  ,'parentopt'=>($this->uri->segment(3)!=null)?$this->uri->segment(3):""
								  ,'modid'=>'5'
								),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		return $data;
	}
	function addopt(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['5']['opt_add']) redirect('Employee/access_denied');
		$this->load->view('form_view2',$this->addoptFrm());
	}
	function deleteivrs($ivrsid='',$optid=''){
		$args['ivrsid']=$ivrsid;
		$args['optid']=$optid;
		$this->ivrsmodel->deleteIvrs($args);
		redirect('ManageIVRS');
	}
	function deleteopt($ivrsid='',$optid=''){
		$args['ivrsid']=$ivrsid;
		$args['optid']=$optid;
		$this->ivrsmodel->deleteOpt($optid);
		redirect($_SERVER['HTTP_REFERER']);
	}
	function undelete($ivrsid=''){
		$args['ivrsid']=$ivrsid;
		$this->ivrsmodel->undeleteIvrs($args);
		redirect('IVRSDelete');
	}
	function report(){
		if(!isset($_POST['module']) || $_POST['module']!='ivrs'){
			$this->session->unset_userdata('Adsearch');
		}
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['16']['opt_view']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		if($this->input->post('download')){
			$filename = $this->ivrsmodel->ivrs_csvreport($bid);
			$dlink =  "<a href='".$this->config->item('reports_path').$filename.".zip"."' target='_blank' style='color:#fff;'><b>Download</b></a>  ";
		}else{
			$dlink = "";
		}
		$u3 = ($this->uri->segment(2)!='')?$this->uri->segment(2):'all';
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_ivrsreport');
		$links[] = '<li><a href="#" class="blkemail" rel="ivrs">&nbsp;<span title="Bulk Mail" class="glyphicon glyphicon-envelope">&nbsp;Email</span></a></li>';
		$links[] = '<li><a href="Report/blksms" class="blkSMs" data-toggle="modal" data-target="#modal-blksms"
		rel="ivrs">&nbsp;<span title="Bulk SMS" class="glyphicon glyphicon-comment">&nbsp;SMS</span></a></li>';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] =($roleDetail['modules']['16']['opt_download'])?'<li><a href="ivrs/Ivrs_csv" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Download" class="glyphicon glyphicon-download-alt">&nbsp;Download All </span></a></li>':'';
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$data['itemlist'] = $this->ivrsmodel->getIvrsReportlist($bid,$ofset,$limit,$u3);
		$data['module']['title'] = $this->lang->line('label_ivrsreport'). "[".$data['itemlist']['count']."]";
		$this->pagination->initialize(array(
						 'base_url'=>site_url('IVRSReport/'.$u3.'/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit		
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$fieldset = $this->configmodel->getFields('16',$bid);
		$formFields = array();$advsearch=array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='filename'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked && !in_array($field['fieldname'],array('options'))) { 
					$formFields[] = in_array($field['fieldname'],array('employee','name','email','callfrom','datetime','endtime','ivrstitle'))?array(
									'label'=>'<label  class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>form_input(array(
											'name'      => $field['fieldname'],
											'id'        => $field['fieldname'],
											'class'		=>($field['fieldname']=="endtime" ||$field['fieldname']=="datetime")?'datepicker_leads form-control':'form-control'))
											):array('label'=>'','field'=>'');
								$advsearch[$field['fieldname']]=(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']);				
							}				
		}elseif($field['type']=='c' && $field['show']){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked)
						$formFields[] = array(
							'label'=>'<label  class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
							'field'=>$this->configmodel->createFieldAdvance($field,isset($itemDetail[$field['fieldKey']]) ? $itemDetail[$field['fieldKey']] : '',''));
			}
		}
		unset($advsearch['options']);
		unset($advsearch['employee']);
		$save_cnt=save_search_count($bid,'16',$this->session->userdata('eid'));	
		$search_names=get_save_searchnames($bid,'16',$this->session->userdata('eid'));
		$data['form'] = array(
					'open'=>form_open('IVRSReport/'.$u3.'/0'
						,array('name'=>'search','class'=>'form','id'=>'search','method'=>'post')
						,array('module'=>'ivrs')),
					'title'=>'IVRS Report Search',
					'form_field'=>$formFields,
					'adv_search'=>$advsearch,
					'search_names'=>$search_names,
					'search_url'=>'IVRSReport/',
					'groups'=>$this->systemmodel->get_ivrs_list(),
					'employees'=>$this->groupmodel->employee_list(),
					'save_search'=>$save_cnt,
					'parentids'=>$parentbids,
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close()
				);
		$data['tab'] = true;	
		$data['links'] = $links;	
		$data['downlink'] = $dlink;	
	    if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->viewLayout('list_view',$data);		
	}
	function calldetail($hid){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['16']['opt_view']) redirect('Employee/access_denied');
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_ivrsreport');
		$data['module']['title'] = $this->lang->line('label_ivrsreport');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$fieldset = $this->configmodel->getFields('16',$bid);
		$formFields = array();
		$itemId = $hid;
		$itemDetail = $this->configmodel->getDetail('16',$itemId,'',$bid);
		foreach($fieldset as $field){$checked = false;
			if($field['type']=='s' && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
									'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:'');
			}elseif($field['type']=='c' && $field['show']){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked)$formFields[] = array(
							'label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
							'field'=>isset($itemDetail[$field['fieldKey']]) ? $itemDetail[$field['fieldKey']] : '','');
			}
		}
		$data['form'] = array('open'=>'<form class="form">','fields'=>$formFields,'close'=>'</form>');
		$this->load->view('active_view',$data);
	}
	function Ivrs_csv(){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data = array('systemfields'=>$this->configmodel->getFields('16',$bid),
					'roleDetail'=>$this->roleDetail,
					'ivrslist'=>$this->systemmodel->get_ivrs_list(),
					'eid'=>'',
					'bid' => $bid,
					 );
		$this->load->view('ivrs_csvreport',$data);
	}
	function print_csv($array){
		$filename = "Export_".date("Y-m-d_H-i",time());
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header("Content-disposition: filename=".$filename.".csv");
		print $array;exit;
	}
	function editReport($callid){
		if($this->input->post('update_system')){
			$res=$this->ivrsmodel->updateIvrsReport($callid);
			if($res == 1){
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', "You donot have enough usage of conversion of lead, please contact your account manager");
			}elseif($res == 2){
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', "You dont have enough SMS credits");
			}elseif($res == 3){
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', "You donot have enough usage of conversion of support, please contact your account manager");
			}else{
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg', "IVRS Call Updated Successfully");
			}
			redirect($this->session->userdata('refurl'));
		}
		if(isset($_SERVER['HTTP_REFERER']))$this->session->set_userdata(array('refurl'=>$_SERVER['HTTP_REFERER']));
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['16']['opt_view']) redirect('Employee/access_denied');
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_ivrsreport');
		$data['module']['title'] = $this->lang->line('label_ivrsreport');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$fieldset = $this->configmodel->getFields('16',$bid);
		$formFields = array();
		$itemId = $callid;
		$itemDetail = $this->configmodel->getDetail('16',$itemId,'',$bid);
		foreach($fieldset as $field){$checked = false;
			if($field['type']=='s' && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
									'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>in_array($field['fieldname'],array('name','email'))
												?form_input(array(
																	  'name'      => $field['fieldname'],
																	  'id'        => $field['fieldname'],
																	  'class'     => 'form-control',
																	  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:set_value($field['fieldname'])))
												:(isset($itemDetail[$field['fieldname']])
													?$itemDetail[$field['fieldname']]
													:''));
			}elseif($field['type']=='c' && $field['show']){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked)$formFields[] = array(
							'label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
							'field'=>$this->configmodel->createFieldAdvance($field,isset($itemDetail[$field['fieldKey']]) ? $itemDetail[$field['fieldKey']] : '',''));
			}
		}
		/* convert lead and support ticket start */
		$lead_access = $this->sysconfmodel->getfeatureAccess('13');
		if($lead_access == 1){
			$leadstchk = $this->reportmodel->leadstatuschk($itemDetail['callfrom'],$bid);
			if(empty($leadstchk) ){
				$leadtype = $this->sysconfmodel->leadtypeCheck();
				if($leadtype == 1 || $leadtype == 3){
					$cf=array('label'=>'<label class="col-sm-4 text-right">Convert As Lead :</label>	'
						,'field'=>form_checkbox(array("name"=>"convertlead","id"=>"convertlead","value"=>"2"))
						);
				}else{
					$cf=array('label'=>'<label class="col-sm-4 text-right">Convert As  :</label>	'
							,'field'=>form_radio(array("name"=>"convertlead","id"=>"convertlead","value"=>"1"))." &nbsp; Prospect &nbsp;  ".form_radio(array("name"=>"convertlead","id"=>"convertlead","value"=>"2"))." &nbsp; Lead &nbsp; "
							);
				}
			}elseif(isset($leadstchk['lead_status']) && $leadstchk['lead_status'] != 1){
				$cf=array('label'=>'<label class="col-sm-4 text-right">Update Lead :</label>	'
						,'field'=>form_checkbox(array("name"=>"updatelead","id"=>"updatelead","value"=>"1")));
			}else{
				$cf=array('label'=>'<label class="col-sm-4 text-right">Convert As Lead :</label>	'
						,'field'=>form_checkbox(array("name"=>"convertlead","id"=>"convertlead","value"=>"2"))
						);
			}
			array_push($formFields,$cf);
		}
		$cf=array('label'=>'<label class="col-sm-4 text-right" id="grLabel" style="display:none;">Lead Group :</label>	'
					,'field'=>form_dropdown('lgid',$this->leadsmodel->getGroups(),'',"id='grempId' class ='form-control' style='display:none;'")
					,"style"=>"none");
		array_push($formFields,$cf);
		$cf=array('label'=>'<label class="col-sm-4 text-right" id="assignLabel" style="display:none;">Lead Assignto :</label>	'
					,'field'=>form_dropdown('lassignto',$this->groupmodel->employee_list(),'',"id='assignemp'  class ='form-control' style='display:none;' ")	
				 ,"style"=>"none");
		array_push($formFields,$cf);
		$arr=array("0"=>"Select","1"=>"Email Alert","2"=>"SMS Alert","3"=>"Both");
		$cf = array('label'=>'<label class="col-sm-4 text-right" id="alertLabel" style="display:none;">Alert Type :</label>	'
					,"field"=>form_dropdown("lalerttype",$arr,'',"id='alerttype'  class ='form-control' style='display:none;'")
					,"style"=>"none");
		array_push($formFields,$cf);
		//~ $sup_access = $this->sysconfmodel->getfeatureAccess('14');
		//~ if($sup_access == 1){
			//~ $disabled = (isset($itemDetail['suptkt']) || $itemDetail['suptkt'] == NULL || $itemDetail['tktid'] == 0)  ? '1' : '0';
			//~ if($disabled == 1){
				//~ $cf=array('label'=>'<label  class="col-sm-4 text-right">Convert to Support Ticket :</label>	'
						//~ ,'field'=>form_checkbox(array("name"=>"convertsuptkt","id"=>"convertsuptkt","value"=>"1")));
				//~ array_push($formFields,$cf);
			//~ }else{
				//~ $cf=array('label'=>'<label class="col-sm-4 text-right">Update Support Ticket :</label>	'
						//~ ,'field'=>form_checkbox(array("name"=>"updatesuptkt","id"=>"updatesuptkt","value"=>"1")));
				//~ array_push($formFields,$cf);
			//~ }
		//~ }
		//~ $cf=array('label'=>'<label class="col-sm-4 text-right" id="supgrLabel" style="display:none;">Support Group :</label>	'
					//~ ,'field'=>form_dropdown('sgid',$this->supportmodel->getSupportGrps(),'','id="supgrId"  class ="form-control" style="display:none;"')
					//~ ,"style"=>"none");
					//~ array_push($formFields,$cf);
		//~ $cf=array('label'=>'<label class="col-sm-4 text-right" id="supassignLabel" style="display:none;">Ticket Assignto :</label>	'
					//~ ,'field'=>form_dropdown('sassignto',$this->supportmodel->getEmployees(),'',"id='supEmpid'  class ='form-control' style='display:none;'"),"style"=>"none");
					//~ array_push($formFields,$cf);
		//~ $cf=array('label'=>'<label  class="col-sm-4 text-right" id="suplevelLabel" style="display:none;">Ticket Level :</label>	'
					//~ ,'field'=>form_dropdown('tkt_level',$this->supportmodel->getSupTktLevel(),'',"id='tkt_level'   class ='form-control' style='display:none;'"),"style"=>"none");
					//~ array_push($formFields,$cf);
		//~ $escProcess = $this->systemmodel->getSupEscBusiness();
		//~ if($escProcess == 1){
			//~ $cf=array('label'=>'<label class="col-sm-4 text-right" id="suptimeLabel" style="display:none;">Ticket Escalation Time :</label>	'
						//~ ,'field'=>form_input(array(	  'name'      => 'tkt_esc_time',
													  //~ 'id'        => 'tkt_esc_time',
													  //~ 'value'     => '',
													  //~ 'class'     => 'form-control',
													  //~ 'style'	  => 'display:none;'
											//~ ))
						//~ ,"style"=>"none");
			//~ array_push($formFields,$cf);
		//~ }
		//~ $arr=array("0"=>"Select","1"=>"Email Alert","2"=>"SMS Alert","3"=>"Both");
		//~ $cf = array('label'=>'<label class="col-sm-4 text-right" id="supalertLabel" style="display:none;">Alert Type :</label>	'
					//~ ,"field"=>form_dropdown("salerttype",$arr,'',"id='supalerttype' class ='form-control' style='display:none;'")
					//~ ,"style"=>"none");
		//~ array_push($formFields,$cf);
		/*  End of Conversion */
		
		$data['form'] = array(
		            'form_attr'=>array('action'=>'IVRSEditReport/'.$callid,'name'=>'autoadd','id'=>'autoadd','enctype'=>"multipart/form-data"),
					'hidden'=>array('bid'=>$bid,'number'=>$itemDetail['callfrom']),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function ivRef(){
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
			$limit = '30';
			$i = $ofset+1;
			$header = array('#'
							,'Ivrs Name'
							,'Contact Name'
							,'Contact Number'
							,'Reference ID'
							,'File Number'
							,'Assign Date'
							,'Target Type'
							,'Target'
							,$this->lang->line('level_Action')
							);
			$data['itemlist']['header'] = $header;
			$emp_list=$this->ivrsmodel->getIvreflist($ofset,$limit);
			$data['module']['title'] ="Ivrs Reference List "."[".$emp_list['count']."]";
			$rec = array();
			if(count($emp_list['data'])>0)
			foreach ($emp_list['data'] as $item){
				$img=($item['status']<1)?'<img src="system/application/img/icons/undelete.png" title="UnDelete">':'<span title="Delete" class="glyphicon glyphicon-trash"></span>';
				$rec[] = array(
					$i
					,$item['title']
					,$item['contactName']
					,$item['number']
					,$item['refinput']
					,$item['file_number']
					,$item['assigndate']
					,ucfirst($item['targettype'])
					,$item['targetName']
					,'<a href="ivrs/AddIvrRef/'.$item['refid'].'"><span id="'.$item['refid'].'" title="Edit" class="fa fa-edit"></span></a>&nbsp;<a href="ivrs/delIvrRef/'.$item['refid'].'"  class="confirm deleteClass">'.$img.'</a>'
				);
				$i++;
			}
			$data['itemlist']['rec'] = $rec;
			$data['itemlist']['count'] = $emp_list['count'];
			$this->pagination->initialize(array(
							 'base_url'=>site_url('ivrs/ivRef/')
							,'total_rows'=>$data['itemlist']['count']
							,'per_page'=>$limit	
							,'uri_segment'=>3					
					));
			$data['paging'] = $this->pagination->create_links();
			$this->sysconfmodel->data['html']['title'] .= " | Manage Ivrs Reference";
			$links = array();
			$links[] = '<li><a href="ivrs/AddIvrRef"><span title="Add Number" class="glyphicon glyphicon-plus-sign">&nbsp;Add Number</span></a></li>';
			$links[] = '<li class="divider"><a>&nbsp;</a></li>';
	    	$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
			$formFields = array();
			$advsearch=array();
			$droparray=array_merge(array(''=>'Select'),$this->ivrsmodel->getIvlist());	
			$formFields[] = array(
					'label'=>'<label class="col-sm-4 text-right" for="f">Ivrs Name: </label>',
					'field'=>form_dropdown('ivrsid',$droparray,'','class="form-control"')
						);
			$advsearch['title']="Title";
			$formFields[] = array(
					'label'=>'<label class="col-sm-4 text-right" for="f">Contact Name: </label>',
					'field'=>form_input(array(
							'name'      => 'contactName',
							'id'        => 'contactName',
							'class'     => 'form-control',
							'value'     => $this->session->userdata('contactName')
							))
							);
			$advsearch['contactName']="Contact Name";				
			$formFields[] = array(
					'label'=>'<label class="col-sm-4 text-right" for="number">Contact Number: </label>',
					'field'=>form_input(array(
							'name'      => 'number',
							'id'        => 'number',
							'class'     => 'form-control',
							'value'     => $this->session->userdata('number')
							))
							);
			$advsearch['number']="Contact Number";
			$formFields[] = array(
					'label'=>'<label  class="col-sm-4 text-right" for="refinput">Reference ID: </label>',
					'field'=>form_input(array(
							'name'      => 'refinput',
							'id'        => 'refinput',
							'class'     => 'form-control',
							'value'     => $this->session->userdata('refinput')
							))
							);
			$advsearch['refinput']="Reference ID";
			$formFields[] = array(
					'label'=>'<label class="col-sm-4 text-right" for="file_number">File Number: </label>',
					'field'=>form_input(array(
							'name'      => 'file_number',
							'id'        => 'file_number',
							'class'     => 'form-control',
							'value'     => $this->session->userdata('file_number')
							))
							);
			$advsearch['file_number']="File Number";
			$formFields[] = array(
					'label'=>'<label class="col-sm-4 text-right" for="targettype">Target Type: </label>',
					'field'=>form_dropdown('targettype',
									array( ''=>'Select Type'
										  ,'ivrs'=> 'IVRS'
										  ,'employee'=> 'Employee'
										  ,'group'=> 'Group'
										  ,'pbx'=> 'PBX'
									)
									,$this->session->userdata('targettype'),
									'id="reftargettype" class="form-control"')
							);
			$advsearch['targettype']="Target Type";
			$formFields[] = array(
					'label'=>'<label class="col-sm-4 text-right" for="targetid">Target: </label>',
					'field'=>form_dropdown('targetid',
									array( ''=>'target')
									,$this->session->userdata('targetid'),
									'id="targetid" class="form-control"')
							);
			$advsearch['targetid']="Target";
			$save_cnt=3;
			$data['links'] = $links;
			$data['form'] = array(
			    'open'=>form_open_multipart('ivrs/ivRef/',array('name'=>'manageivRef','class'=>'form','id'=>'manageivRef','method'=>'post')),
				'form_field'=>$formFields,
				'save_search'=>$save_cnt,
				'adv_search'=>array(),
				'close'=>form_close(),
				'title'=>$this->lang->line('level_search')
				);
			if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
			$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function AddIvrRef($id=''){
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('refinput', 'Reference', 'required|min_length[1]|numeric');
			$this->form_validation->set_rules('cname', 'Contact Name', 'required|alpha_numeric');
			$this->form_validation->set_rules('cnumber', 'Contact Number', 'required|numeric');
			if(!$this->form_validation->run() == FALSE){
				if($id!=""){
					$res=$this->ivrsmodel->Iv_addRef($id);
					if($res){	
						$this->session->set_flashdata('msgt', 'success');
						$this->session->set_flashdata('msg', 'Ivrs Reference updated Successfully');
						redirect('IVRSref');
					}
				}else{
					$res=$this->ivrsmodel->Iv_addRef();
					if($res){	
						$this->session->set_flashdata('msgt', 'success');
						$this->session->set_flashdata('msg', "Ivrs Reference Added Successfully");
						redirect('IVRSref');
					}
				}
			}	
		}
		$IvDetail=$this->ivrsmodel->getIvRefDetail($id);
		$this->sysconfmodel->data['html']['title'] .= " | Add IVRS Reference ";
		$data['module']['title'] = "Add IVRS Reference";
		$formFields = array();$formFields1 = array();
		$cf=array('label'=>"<label  class='col-sm-4 text-right'b>IVRS Name : </label> ",
				  'field'=>form_dropdown('ivrs',$this->ivrsmodel->getIvlist(),isset($IvDetail['ivrsid'])?$IvDetail['ivrsid']:'',"id='ivrs' class='required auto form-control'"));
			array_push($formFields,$cf);
		$cf=array('label'=>"<label class='col-sm-4 text-right'>Reference ID : </label>",
				  'field'=>form_input(array(
									  'name'        => 'refinput',
										'id'        => 'refinput',
										'value'     => isset($IvDetail['refinput'])?$IvDetail['refinput']:'',
										'class'		=>'required digits form-control'
				  				)));
		array_push($formFields,$cf);
		$cf=array('label'=>"<label class='col-sm-4 text-right'>Contact Name : </label> ",
				  'field'=>form_input(array(
									  'name'        => 'cname',
										'id'          => 'cname',
										'value'       => isset($IvDetail['contactName'])?$IvDetail['contactName']:'',
										'class'		=>'required form-control'
				  				)));
		array_push($formFields,$cf);
		$cf=array('label'=>"<label class='col-sm-4 text-right'>Contact Number : </label> ",
				  'field'=>form_input(array(
									  'name'        => 'cnumber',
										'id'          => 'cnumber',
										'value'       => isset($IvDetail['number'])?$IvDetail['number']:'',
										'class'		=>'required digits form-control'
				  				)));
		array_push($formFields,$cf);
		$cf=array('label'=>"<label class='col-sm-4 text-right'>File Number : </label> ",
				  'field'=>form_input(array(
									  'name'        => 'fnumber',
										'id'          => 'fnumber',
										'value'       => isset($IvDetail['file_number'])?$IvDetail['file_number']:'',
										'class'		=>'number form-control'
				  				)));
		array_push($formFields,$cf);

		$cf=array('label'=>"<label class='col-sm-4 text-right'>Target Type : </label> ",
				  'field'=>form_dropdown('targettype',
									array( ''=>'Select Type'
										  ,'ivrs'=> 'IVRS'
										  ,'employee'=> 'Employee'
										  ,'group'=> 'Group'
										  ,'pbx'=> 'PBX'
										  ,'class'=>'form-control'
									)
									,isset($IvDetail['targettype'])?$IvDetail['targettype']:'',
									'id="reftargettype" class="form-control"')); 
		array_push($formFields,$cf);
		$dataArr = array(''=>'Target');
		$tid = isset($IvDetail['targetid'])?$IvDetail['targetid']:'';
		if(isset($IvDetail['targettype'])){
			if($IvDetail['targettype']=='group'){
				$dataArr = $this->systemmodel->get_groups();
			}elseif($IvDetail['targettype']=='employee'){
				$dataArr = $this->systemmodel->get_emp_list();
			}elseif($IvDetail['targettype']=='pbx'){
				$dataArr = $this->systemmodel->get_pbx_list();
			}elseif($IvDetail['targettype']=='ivrs'){
				$dataArr = $this->systemmodel->get_ivrs_list();
			}else{
				$dataArr = array(''=>'Target');
			}
		}else{
			$dataArr = array(''=>'Target');
		}
		$cf=array('label'=>"<label class='col-sm-4 text-right'>Target : </label> ",
				  'field'=>form_dropdown('targetid',$dataArr,$tid,'id="targetid" class="form-control"'));
		array_push($formFields,$cf);
		$data['form'] = array(
		        'form_attr'=>array('action'=>'AddIVRSref/'.$id,'name'=>'ivref','id'=>'ivref','enctype'=>"multipart/form-data"),
				'fields'=>$formFields,
				'fields1'=>$formFields1,
				'close'=>form_close()
			);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function delIvrRef($ivref_id){
		$res=$this->ivrsmodel->delIvrRef($ivref_id);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', "IVRS Reference Delete Status Updated Successfully");
		redirect('IVRSref');
	}
	function StatusIvrRef($ivref_id){
		$res=$this->ivrsmodel->StatusIvrRef($ivref_id);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', "IVRS Reference Status Updated Successfully");
		redirect('IVRSref');
	}
	function ivrsTarget(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$option = $_POST['optVal'];
		switch($option){
			case 'employee' :
					echo form_dropdown('targeteid',$this->systemmodel->get_emp_list(),isset($itemDetail['targeteid'])?$itemDetail['targeteid']:'','id="targeteid" class="form-control employees"');
			break;
			case 'group':
					echo form_dropdown('targeteid',$this->systemmodel->get_groups(),isset($itemDetail['targeteid'])?$itemDetail['targeteid']:'','id="targeteid" class="form-control"');
			break;
			case 'pbx':
					echo form_dropdown('targetid',$this->systemmodel->get_pbx_list(),isset($itemDetail['targetid'])?$itemDetail['targetid']:'','id="targetid" class="required form-control"');
			break;
			case 'api': echo form_input(array('name' => 'api_url','id'=>'api_url','value'=> isset($itemDetail['api_url'])?$itemDetail['api_url']:'','class'=>'form-control required'));
			break;
			case 'sms':
					echo form_textarea(array('name' => 'sms_text','id'=>'sms_text','value'=> isset($itemDetail['sms_text'])?$itemDetail['sms_text']:'','class'=>'form-control required','maxlength'=>'140'));
			break;
		}
	}
}
?>
