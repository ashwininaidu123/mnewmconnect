<?php
   class Mconnect extends controller
   {
   	var $data,$roleDetail;
   	function Mconnect(){
   		parent::controller();
   		if(!$this->session->userdata('logged_in'))redirect('/site/login');
   		$this->load->model('sysconfmodel');
   		$this->data = $this->sysconfmodel->init();
   		$this->load->model('systemmodel');
   		$this->load->model('mconnectmodel');
   	    $this->load->helper('mcube_helper');
   	    $this->load->model('ivrsmodel');
   	    $this->load->model('supportmodel');
   		$this->load->model('auditlog');
   		$this->load->model('configmodel');
   		$this->roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
   	}
   	public function __destruct() {
   		$this->db->close();
   	}
   	function feature_access(){
   		$show=0;
   		$checklist=$this->systemmodel->checked_featuremanage();
   		if(in_array(17,$checklist)){
   			$show=1;
   		}
   		return $show;
   	}
       function addproperty($id=''){
   		if(!$this->feature_access())redirect('Employee/access_denied');
   		$cbid=$this->session->userdata('cbid');
   		$bid=(isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
   		$parentbids=array();
   		if($this->session->userdata('eid')==1){
   			$parentbids=$this->systemmodel->getChildBusiness();
   		}
   		$roleDetail = $this->roleDetail;
   		if(!$roleDetail['modules']['52']['opt_view']) redirect('Employee/access_denied');
   		if($this->input->post('update_system')){
   			$this->form_validation->set_rules('propertyname', 'propertyname', 'required');
   			if (!$this->form_validation->run() == FALSE){
   			if($id == ""){
   			$res = $this->mconnectmodel->addproperty($bid);
   				$this->session->set_flashdata('msgt', 'success');
   					if($res == '0'){
   						$this->session->set_flashdata('msg', "Property added Successfully");
   						redirect('ListProperty/0');
   					}
   					if($res == 'error'){
   						$this->session->set_flashdata('msg', "File size too low");
   						redirect('mconnect/addproperty');
   					}
   		}else{
   			$res = $this->mconnectmodel->editproperty($bid,$id);
   				$this->session->set_flashdata('msgt', 'success');
   					if($res == '1'){
   						$this->session->set_flashdata('msg', "Property Updated Successfully");
   						redirect('ListProperty/0');
   					}
   		}
   		}
   		}
   		$this->sysconfmodel->data['html']['title'] .= " | Add Property";
   		$data['module']['title'] = "Add Property";
   		$fieldset = $this->configmodel->getFields('52',$bid);
   		$itemDetail = $this->configmodel->getDetail('52',$id,'',$bid);
   		//PRINT_R($itemDetail); exit;
   		foreach($fieldset as $field){
   			$checked = false;
   			if($field['type']=='s' && $field['show'] && $field['fieldname']!=''){
   				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
   				if($checked && !in_array($field['fieldname'] ,array())) 
   					$formFields[] = array(
   									'label'=>'<label  class="col-sm-4 control-label" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
   											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' <img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp: </label>',
   												'field'=> ($field['fieldname'] == 'propertyicon') ? 
   													(form_input(array(
   																	  'name'      => $field['fieldname']
   																	  ,'id'       => $field['fieldname']
   																      ,'type'     => 'file'
   																      ,'parsley-filemaxsize' =>"Upload|2"
   																      ,'style'     => 'float: left;'
   																      ,'accept'   => 'image/gif, image/jpeg , image/png'
   																      ,'value'    => isset($itemDetail['propertyicon'])?$itemDetail['propertyicon']:$this->input->post('propertyicon')
   																	 ))	)
   												:form_input(array(
   												'name'      => $field['fieldname'],
   												'id'        => $field['fieldname'],
   												'value'		=> (isset($itemDetail[$field['fieldname']])) ? $itemDetail[$field['fieldname']] : '',
   												'class'		=> ($field['fieldname'] == 'sitename') ? 'form-control required' : 'form-control'))
   												);
   			}elseif($field['type']=='c' && $field['show']){
   					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
   					if($checked)$formFields[] = array(
   							'label'=>'<label  class="col-sm-4 control-label" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
   							'field'=>$this->configmodel->createFieldAdvance($field,isset($itemDetail[$field['fieldKey']]) ? $itemDetail[$field['fieldKey']] : '',''));
   			}
   		}
   		$data['form'] = array(
   		            'form_attr'=>array('action'=>'mconnect/addproperty/'.$id,'name'=>'addproperty','id'=>'addproperty','enctype'=>"multipart/form-data"),
   					'hidden'=>array('bid'=>$bid,'propertyid'=>$id),
   					'fields'=>$formFields,'parentids'=>$parentbids,
   					'busid'=>$bid,
   					'pid'=>$this->session->userdata('pid'),
   					'close'=>form_close()
   				);
   		$this->sysconfmodel->viewLayout('form_view',$data);
   	}
   	
   	
   	function listproperty(){
   		if(!$this->feature_access())redirect('Employee/access_denied');
   		$cbid=$this->session->userdata('cbid');
   		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   		$roleDetail = $this->roleDetail;	
   		if($this->input->post('submit')){
   			if($this->session->userdata('search')!=""){
   				$s=$this->session->unset_userdata('search');
   			}
   		}
   		$parentbids=array();
   		if($this->session->userdata('eid')==1){
   			$parentbids=$this->systemmodel->getChildBusiness();
   		}
   		$ofset = ($this->uri->segment(2)!=null)?$this->uri->segment(2):0;
   		$limit = '30';
   		$data['itemlist'] = $this->mconnectmodel->getlistproperty($bid,$ofset,$limit);
   
   		$this->pagination->initialize(array(
   						 'base_url'=>site_url($this->uri->segment(1).'/')
   						,'total_rows'=>$data['itemlist']['count']
   						,'per_page'=>$limit		
   						,'uri_segment'=>2				
   				));
   		$data['module']['title'] = "Property [".$data['itemlist']['count']."]";	
   		$links = array();	
   		$links[] = '<li><a href="mconnect/addproperty"><span title="Add Property" class="glyphicon glyphicon-plus-sign">&nbsp;Add</span></a></li>';
   	    $links[] = ($roleDetail['modules']['52']['opt_delete']) ? '<li><a href="mconnect/bulkDelSite" class="blkDelsite"><span title="Bulk Delete" class="glyphicon glyphicon-trash">&nbsp;Delete</span></a></li>':'';
   	    $links[] = '<li class="divider"><a>&nbsp;</a></li>';
   		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
   		$fieldset = $this->configmodel->getFields('52',$bid);
   		$formFields = array();
   		foreach($fieldset as $field){
   			$checked = false;
   			if($field['type']=='s' && $field['show']){
   				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
   				if($checked) { $formFields[] = array(
   									'label'=>'<label class="col-sm-4 control-label" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
   											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
   									'field'=>form_input(array(
   												'name'      => $field['fieldname'],
   												'id'        => $field['fieldname'],
   												'class'		=>'form-control'
   												))
   									);
   								}		
   			}elseif($field['type']=='c' && $field['show']){
   				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
   				if($checked) { $formFields[] = array(
   								'label'=>'<label class="col-sm-4 control-label" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
   								'field'=>form_input(array(
   													'name'      => 'custom['.$field['fieldid'].']',
   													'class'      => 'form-control'
   													)));						
   							}						
   			}
   		}
   		$data['links'] = $links;
   		$data['form'] = array(
   					'open'=>form_open_multipart(site_url('mconnect/listproperty'),array('name'=>'listproperty','class'=>'form','id'=>'listproperty','method'=>'post')),
   					'form_field'=>$formFields,
   					'parentids'=>$parentbids,
   					'adv_search'=>array(),
   					'busid'=>$bid,
   					'pid'=>$this->session->userdata('pid'),
   					'close'=>form_close(),
   					'title'=>$this->lang->line('level_search')
   					);
   		$data['paging'] = $this->pagination->create_links();
   		$this->sysconfmodel->data['html']['title'] .= " | Property";
   		if(isset($_POST['search']) && $_POST['search'] == 'search'){
   			$this->load->view('search_view',$data);
   			return true;
   		}
   		$this->sysconfmodel->viewLayout('list_view',$data);
   	}
   	
   	 function deletedprop(){
   		if(!$this->feature_access(17))redirect('Employee/access_denied');
   		$roleDetail = $this->roleDetail;
   		$cbid=$this->session->userdata('cbid');
   		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   		$parentbids=array();
   		if($this->session->userdata('eid')==1){
   			$parentbids=$this->systemmodel->getChildBusiness();
   		}
   		$heading="Deleted Property";
   		if(!$roleDetail['modules']['52']['opt_delete']) redirect('Employee/access_denied');
   		if($this->input->post('submit')){	
   			if($this->session->userdata('search')!=""){
   				$s=$this->session->unset_userdata('search');
   			}
   		}
   		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
   		$limit = '20';
   		$data['itemlist'] = $this->mconnectmodel->deletedprop($bid,$ofset,$limit);
   		$this->pagination->initialize(array(
   						 'base_url'=>site_url('mconnect/deletedprop')
   						,'total_rows'=>$data['itemlist']['count']
   						,'per_page'=>$limit		
   						,'uri_segment'=>3				
   				));
   		$links = array();
   		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
   		$data['module']['title'] = "Deleted Property";
   		$fieldset = $this->configmodel->getFields('52',$bid);
   		$formFields = array();
   		$advsearch=array();
   		foreach($fieldset as $field){
   			$checked = false;
   			if($field['type']=='s' && $field['show']){
   				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
   				if($checked) { $formFields[] = array(
   									'label'=>'<label  class="col-sm-4 control-label" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
   											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
   									'field'=> form_input(array(
   													'name'      => $field['fieldname'],
   													'id'        => $field['fieldname'],
   													'class'		=>($field['fieldname']=="createdon" || $field['fieldname']=="lastmodified")?'datepicker_leads form-control':'form-control'
   													))
   										);
   										$advsearch[$field['fieldname']]=(($field['customlabel']!="")
   											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']);
   								}			
   			}elseif($field['type']=='c' && $field['show']){
   				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
   				if($checked) { $formFields[] = array(
   								'label'=>'<label  class="col-sm-4 control-label" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
   								'field'=>form_input(array(
   													'name'      => 'custom['.$field['fieldid'].']',
   													'class'     => 'form-control',
   													)));
   													$advsearch['custom['.$field['fieldid'].']']=$field['customlabel'];	
   												}
   			}
   		}
   		$data['links'] = $links;
   		$data['form'] = array(
   					'open'=>form_open_multipart(site_url('mconnect/deletedprop/'),array('name'=>'delprop','class'=>'form','id'=>'delprop','method'=>'post')),
   					'form_field'=>$formFields,
   					'adv_search'=>array(),
   					'parentids'=>$parentbids,
   					'busid'=>$bid,
   					'pid'=>$this->session->userdata('pid'),
   					'close'=>form_close(),
   					'title'=>$this->lang->line('level_search')
   					);
   		$data['paging'] = $this->pagination->create_links();
   		$this->sysconfmodel->data['html']['title'] .= " | Deleted Property ";
   		if(isset($_POST['search']) && $_POST['search'] == 'search'){
   			$this->load->view('search_view',$data);
   			return true;
   		}
   		$this->sysconfmodel->viewLayout('list_view',$data);
   	}
   	function delete_Prop($pid){
   		$roleDetail = $this->roleDetail;
   		if(!$roleDetail['modules']['52']['opt_delete']) redirect('Employee/access_denied');
   		$ret = $this->mconnectmodel->delete_Prop($pid);
   		if(!$ret == '0'){
   		redirect('ListProperty/0');
         	}
        }
        function undeletedprop($pid){
   		$cbid=$this->session->userdata('cbid');
   		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   		$res=$this->mconnectmodel->undeletedprop($pid,$bid);
   		$this->session->set_flashdata('msgt', 'success');
   		$this->session->set_flashdata('msg',"Deleted Record restored Successfully");
   		redirect('ListProperty/0');
   	}
   	
   	function addsite($pid='',$id=''){
   		$cbid=$this->session->userdata('cbid');
   		$bid=(isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
   		$roleDetail = $this->roleDetail;
   		if(!$roleDetail['modules']['48']['opt_view']) redirect('Employee/access_denied');
   			if($this->input->post('update_system')){
   			$this->form_validation->set_rules('sitename', 'Site Name', 'required');
   			$this->form_validation->set_rules('email', 'Email', 'required');
   			if(!$this->form_validation->run() == FALSE){
   				  $res = $this->mconnectmodel->addsite($pid,$id);
   					if($res == '0'){
   						$this->session->set_flashdata('msg', "Site added Successfully");
   				           redirect('ListSite/'.$pid);
   					}else{
   						$this->session->set_flashdata('msg', "Site Updated Successfully");
   				           redirect('ListSite/'.$pid);
   					}
   			}	
   		}
   		$target_dir = "./uploads";
   		$this->sysconfmodel->data['html']['title'] .= " | Add Site";
   		$data['module']['title'] = "Add Site";
   		$fieldset = $this->configmodel->getFields('48',$bid);
   		$itemDetail = $this->configmodel->getDetail('48',$id,'',$bid);
   		 foreach($fieldset as $field){
   			$rest = isset($itemDetail['sitemedia'])?substr($itemDetail['sitemedia'], 0, 4):'';
   			$checked = false;
   			if($field['type']=='s' && $field['show'] && $field['fieldname']!='' && $field['fieldname']!='sitemedia'){
   				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
   				if($checked && !in_array($field['fieldname'] ,array())) 
   					$formFields[] = array(
   									'label'=>'<label  class="col-sm-4 control-label" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
   											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' <img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp: </label>',
   												'field'=>($field['fieldname'] == 'tracknum') ? 
   												form_dropdown('tracknum',$this->systemmodel->getPriList(isset($itemDetail['tracknum'])?$itemDetail['tracknum']:'','1'),$this->input->post($field['fieldname']),"id='tracknum' class='form-control select2 required' data-style='input-sm btn-default'")
   											  :(($field['fieldname']=="site_employee")?
   											    form_dropdown('site_employee',$this->mconnectmodel->site_employee(),
   														(isset($itemDetail['site_employee'])?$itemDetail['site_employee']:set_value($field['fieldname'])),'id="site_employee" class="form-control select2" data-style="input-sm btn-default"')
   											  :((in_array($field['fieldname'],array('sitevideo')))? 
   																	(form_input(array(
   																	  'name'       => $field['fieldname']
   																	  ,'id'        => $field['fieldname']
   														              ,'class'	   => 'form-control'
   																      ,'style'     => 'float: left;'
   																      ,'value'     => (isset($itemDetail['sitemedia']) && $rest=="http")?$itemDetail['sitemedia']:$this->input->post('sitemedia')
   																	 )))
   											   :((in_array($field['fieldname'],array('siteicon')))? 
   																	(form_input(array(
   																	  'name'      => $field['fieldname']
   																	  ,'id'       => $field['fieldname']
   																      ,'type'     => 'file'
   																      ,'parsley-filemaxsize' =>"Upload|2"
   																      ,'style'     => 'float: left;'
   																      ,'accept'   => 'image/gif, image/jpeg , image/png'
   																      ,'value'    =>  isset($itemDetail['siteicon'])?$itemDetail['siteicon']:$this->input->post('siteicon')
   																	 ))	) .((isset($itemDetail['siteicon']) && $itemDetail['siteicon']!='')? "<img style=float:left; height=\"60\" width=\"60\" src=".$target_dir."/".$itemDetail['siteicon']." >":'')
   											    :((in_array($field['fieldname'],array('siteimg')))? 
   																	(form_input(array(
   																	  'name'      => $field['fieldname']
   																	  ,'id'       => $field['fieldname']
   																      ,'type'     => 'file'
   																      ,'parsley-filemaxsize' =>"Upload|2"
   																      ,'accept'   => 'image/gif, image/jpeg , image/png'
   																      ,'style'    => 'padding-bottom: 10px; float: left;'
   																      ,'value'    =>  (isset($itemDetail['sitemedia']) && $rest=="site")?$itemDetail['sitemedia']:$this->input->post('sitemedia')
   																	 ))	)
   																	 . ((isset($itemDetail['sitemedia']) && $itemDetail['sitemedia']!='' && $rest=="site")?
   																	  "<img style=float:left;padding-right:5px height=\"60\" width=\"60\" src=".$target_dir."/".$itemDetail['sitemedia']." >"
   																	  ."<img style=float:left;padding-right:5px height=\"60\" width=\"60\" src=".$target_dir."/".$itemDetail['site_image']." >"
   																	  ."<img style=float:left;padding-right:5px height=\"60\" width=\"60\" src=".$target_dir."/".$itemDetail['site_image1']." >"
   																	  ."<img style=float:left;padding-right:5px height=\"60\" width=\"60\" src=".$target_dir."/".$itemDetail['site_image2']." >"
   																	  :'')
   																	 .'<div class="input_image_wrap">
   														<span id="add_image_button" class="btn btn-success fileinput-button add_image_button" style="margin-left: 10px;"><i class="glyphicon glyphicon-plus"></i><span>Add image...</span></span>
   														
   												</div>'
   											  
   											:((in_array($field['fieldname'],array("siteinterest_opt")))?
   												form_textarea(array(
   											  'name'       => $field['fieldname']
   											  ,'id'        => $field['fieldname']
   											  ,'parsley-trigger' => "keyup"
   											  ,'parsley-rangelength'=> "[20,50]"
   											  ,'class'	   => 'form-control valid'
   											  ,'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:$this->input->post($field['fieldname']),
   											)).'<div class="input_fields_wrap">
   														<span  class="btn btn-success fileinput-button add_field_button" id="add_field_button"><i class="glyphicon glyphicon-plus"></i><span>Add field...</span></span>
   												</div>'
   											 :((in_array($field['fieldname'],array("sitedesc")))?
   												form_textarea(array(
   											  'name'       => $field['fieldname']
   											  ,'id'        => $field['fieldname']
   											  ,'parsley-trigger' => "keyup"
   											  ,'parsley-rangelength'=> "[20,160]"
   											  ,'class'	   => 'form-control valid'
   											  ,'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:$this->input->post($field['fieldname']),
   											))	
   											 :((in_array($field['fieldname'],array("email")))?
   												form_input(array(
   											  'name'       => $field['fieldname']
   											  ,'id'        => $field['fieldname']
   											  ,'class'	   => 'form-control required'
   											  ,'type'	   => 'email'
   											  ,'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:$this->input->post($field['fieldname']),
   											))	
   															 
   											  :form_input(array(
   												'name'      => $field['fieldname'],
   												'id'        => $field['fieldname'],
   												'value'		=> (isset($itemDetail[$field['fieldname']])) ? $itemDetail[$field['fieldname']] : '',
   												'class'		=> ($field['fieldname'] == 'sitename') ? 'form-control required' : 'form-control'))
   												))))))));
   			}elseif($field['type']=='c' && $field['show']){
   					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
   					if($checked)$formFields[] = array(
   							'label'=>'<label  class="col-sm-4 control-label" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
   							'field'=>$this->configmodel->createFieldAdvance($field,isset($itemDetail[$field['fieldKey']]) ? $itemDetail[$field['fieldKey']] : '',''));
   			}
   		}
   		$data['form'] = array(
   		            'form_attr'=>array('action'=>'mconnect/addsite/'.$pid.'/'.$id,'name'=>'addsite','id'=>'addsite','enctype'=>"multipart/form-data"),
   					'hidden'=>array('bid'=>$bid,'siteid'=>$id,'pid'=>$pid),
   					'fields'=>$formFields,
   					'busid'=>$bid,
   					'pid'=>$this->session->userdata('pid'),
   					'close'=>form_close()
   				);
   		$this->sysconfmodel->viewLayout('form_view',$data);
   	}
   	function listSite($pid){
   		$cbid=$this->session->userdata('cbid');
   		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   		$roleDetail = $this->roleDetail;	
   		if(!$roleDetail['modules']['48']['opt_view']) redirect('Employee/access_denied');
   		if($this->input->post('submit')){
   			if($this->session->userdata('search')!=""){
   				$s=$this->session->unset_userdata('search');
   			}
   		}
   		$parentbids=array();
   		if($this->session->userdata('eid')==1){
   			$parentbids=$this->systemmodel->getChildBusiness();
   		}
   		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
   		$limit = '30';
   		$data['itemlist'] = $this->mconnectmodel->getlistSite($pid,$bid,$ofset,$limit);
   		$this->pagination->initialize(array(
   						 'base_url'=>site_url($this->uri->segment(2).'/')
   						,'total_rows'=>$data['itemlist']['count']
   						,'per_page'=>$limit		
   						,'uri_segment'=>2				
   				));
   		$data['module']['title'] = "Sites [".$data['itemlist']['count']."]";	
   		$links = array();	
   		$links[] = '<li><a href="mconnect/addsite"><span title="Add Site" class="glyphicon glyphicon-plus-sign">&nbsp;Add</span></a></li>';
   	    $links[] = ($roleDetail['modules']['48']['opt_delete']) ? '<li><a href="mconnect/bulkDelSite" class="blkDelsite"><span title="Bulk Delete" class="glyphicon glyphicon-trash">&nbsp;Delete</span></a></li>':'';
   	    $links[] = '<li class="divider"><a>&nbsp;</a></li>';
   		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
   		$fieldset = $this->configmodel->getFields('48',$bid);
   		$formFields = array();
   		foreach($fieldset as $field){
   			$checked = false;
   			if($field['type']=='s' && $field['show'] && !in_array($field['fieldname'],array('siteicon','sitevideo','siteimg'))){
   				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
   				if($checked) { $formFields[] = array(
   									'label'=>'<label class="col-sm-4 control-label" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
   											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
   									'field'=>form_input(array(
   												'name'      => $field['fieldname'],
   												'id'        => $field['fieldname'],
   												'class'		=>($field['fieldname']=="createdon" || $field['fieldname']=="lastmodified")?'datepicker_leads form-control':'form-control'
   												))
   									);
   								}		
   			}elseif($field['type']=='c' && $field['show']){
   				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
   				if($checked) { $formFields[] = array(
   								'label'=>'<label class="col-sm-4 control-label" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
   								'field'=>form_input(array(
   													'name'      => 'custom['.$field['fieldid'].']',
   													'class'      => 'form-control'
   													)));
   								//$advsearch['custom['.$field['fieldid'].']']=$field['customlabel'];							
   							}						
   			}
   		}
   		$data['links'] = $links;
   		$data['nobulk']=true;	
   		$data['form'] = array(
   					'open'=>form_open_multipart(site_url('mconnect/listSite'),array('name'=>'listsite','class'=>'form','id'=>'listsite','method'=>'post')),
   					'form_field'=>$formFields,
   					'parentids'=>$parentbids,
   					'adv_search'=>array(),
   					'busid'=>$bid,
   					'pid'=>$this->session->userdata('pid'),
   					'close'=>form_close(),
   					'title'=>$this->lang->line('level_search')
   					);
   		$data['paging'] = $this->pagination->create_links();
   		$this->sysconfmodel->data['html']['title'] .= " | Sites";
   		if(isset($_POST['search']) && $_POST['search'] == 'search'){
   			$this->load->view('search_view',$data);
   			return true;
   		}
   		$this->sysconfmodel->viewLayout('list_view',$data);
   	}
   	function delSite($id){
   		if(!$this->feature_access(17))redirect('Employee/access_denied');
   		$cbid=$this->session->userdata('cbid');
   		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   		$roleDetail = $this->roleDetail;
   		if(!$roleDetail['modules']['48']['opt_delete']) redirect('Employee/access_denied');
   		$ret = $this->mconnectmodel->delSite($id,$bid);
   		if(!$ret == '0'){
   		redirect('mconnect/listSite/0');
         	}
        }
       function bulkDelSite(){
   		$res=$this->mconnectmodel->bulkDelSite($_POST['siteid']);
   		echo "1";
   	}
   	function deleteSite(){
   		if(!$this->feature_access())redirect('Employee/access_denied');
   		$roleDetail = $this->roleDetail;
   		$cbid=$this->session->userdata('cbid');
   		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   		$parentbids=array();
   		if($this->session->userdata('eid')==1){
   			$parentbids=$this->systemmodel->getChildBusiness();
   		}
   		$heading="Deleted Sites";
   		if(!$roleDetail['modules']['48']['opt_view']) redirect('Employee/access_denied');
   		if($this->input->post('submit')){	
   			if($this->session->userdata('search')!=""){
   				$s=$this->session->unset_userdata('search');
   			}
   		}
   		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
   		$limit = '20';
   		$data['itemlist'] = $this->mconnectmodel->delSlist($bid,$ofset,$limit,$url='');
   		$this->pagination->initialize(array(
   						 'base_url'=>site_url('mconnect/deleteSite')
   						,'total_rows'=>$data['itemlist']['count']
   						,'per_page'=>$limit		
   						,'uri_segment'=>3				
   				));
   		$links = array();
   		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
   		$data['module']['title'] = "Deleted Sites";
   		$fieldset = $this->configmodel->getFields('48',$bid);
   
   		$formFields = array();
   		$advsearch=array();
   		foreach($fieldset as $field){
   			$checked = false;
   			if($field['type']=='s' && $field['show'] && !in_array($field['fieldname'],array('siteicon','sitevideo','siteimg'))){
   				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
   				if($checked) { $formFields[] = array(
   									'label'=>'<label  class="col-sm-4 control-label" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
   											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
   									'field'=>
   										($field['fieldname']=='siteicon')
   											?form_dropdown('gid',$this->supportmodel->getSupportGrps(),'',"class='form-control'"):""
   										);
   										$advsearch[$field['fieldname']]=(($field['customlabel']!="")
   											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']);
   								}			
   			}elseif($field['type']=='c' && $field['show']){
   				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
   				if($checked) { $formFields[] = array(
   								'label'=>'<label  class="col-sm-4 control-label" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
   								'field'=>form_input(array(
   													'name'      => 'custom['.$field['fieldid'].']',
   													'class'     => 'form-control',
   													)));
   													$advsearch['custom['.$field['fieldid'].']']=$field['customlabel'];	
   												}
   			}
   		}
   		$save_cnt=save_search_count($bid,'40',$this->session->userdata('eid'));	
   		$data['links'] = $links;
   		$data['nobulk']=true;	
   		$data['form'] = array(
   					'open'=>form_open_multipart(site_url('mconnect/deleteSite'),array('name'=>'delsite','class'=>'form','id'=>'delsite','method'=>'post')),
   					'form_field'=>$formFields,
   					'adv_search'=>array(),
   					'save_search'=>$save_cnt,
   					'parentids'=>$parentbids,
   					'busid'=>$bid,
   					'pid'=>$this->session->userdata('pid'),
   					'close'=>form_close(),
   					'title'=>$this->lang->line('level_search')
   					);
   		$data['paging'] = $this->pagination->create_links();
   		$this->sysconfmodel->data['html']['title'] .= " | Deleted Sites ";
   		if(isset($_POST['search']) && $_POST['search'] == 'search'){
   			$this->load->view('search_view',$data);
   			return true;
   		}
   		$this->sysconfmodel->viewLayout('list_view',$data);
   	}
   	function undelSite($siteid){
   		$cbid=$this->session->userdata('cbid');
   		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   		$res=$this->mconnectmodel->undelSt($siteid,$bid);
   		$this->session->set_flashdata('msgt', 'success');
   		$this->session->set_flashdata('msg',"Deleted Record restored Successfully");
   		redirect('mconnect/listSite/0');
   	}
   	
   	
   	
   	
   	function addlocation($pid='',$id='',$locid = ''){
   		$cbid=$this->session->userdata('cbid');
   		$bid=(isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
   		$roleDetail = $this->roleDetail;
   		if(!$roleDetail['modules']['49']['opt_view']) redirect('Employee/access_denied');
   			if($this->input->post('update_system')){
   			$this->form_validation->set_rules('locname', 'Location Name', 'required');
   			if(!$this->form_validation->run() == FALSE){
   				  $res = $this->mconnectmodel->addnewlocation($pid,$id,$locid);
   					if($res == '0'){
   						$this->session->set_flashdata('msg', "Location added Successfully");
   				        redirect('ListLocation/'.$pid.'/'.$id);
   					}else{
   						$this->session->set_flashdata('msg', "Location Updated Successfully");
   						redirect('ListLocation/'.$pid.'/'.$id);
   					}
   			}	
   		}
   		$this->sysconfmodel->data['html']['title'] .= " | Add Location";
   		$data['module']['title'] = "Add Location";
   		$fieldset = $this->configmodel->getFields('49',$bid);
   		$itemDetail = $this->configmodel->getDetail('49',$locid,'',$bid);
   		foreach($fieldset as $field){
		   	$target_dir = "./uploads";
   			$checked = false;
   			if($field['type']=='s' && $field['show'] && $field['fieldname']!=''){
   				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
   				if($checked && !in_array($field['fieldname'] ,array())) 
   					$formFields[] = array(
   									'label'=>'<label  class="col-sm-4 control-label" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
   											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' <img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp: </label>',
   												'field'=>($field['fieldname'] == 'beaconid') ?((isset($itemDetail['beaconid'])) ?(form_input(array(
   																	  'name'      => $field['fieldname']
   																	  ,'id'       => $field['fieldname']
   																	  ,'class'	   => 'form-control valid'
   																	  ,'readonly' => 'readonly'
   																      ,'value'    => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:$this->input->post($field['fieldname'])
   																	 ))): form_dropdown('beaconid',$this->mconnectmodel->getBeaconlist(),isset($itemDetail['beaconid']) ? $itemDetail['beaconid']:'','id="beaconid" class="form-control required" '))
   											                        :((in_array($field['fieldname'],array('loc_image')))? 
   																	(form_input(array(
   																	  'name'      => $field['fieldname']
   																	  ,'id'       => $field['fieldname']
   																      ,'type'     => 'file'
   																      ,'parsley-filemaxsize' =>"Upload|1.5"
   																      ,'accept'   => 'image/gif, image/jpeg , image/png'
   																      ,'style'    => 'float: left;'
   																	 ,'value'    =>  (isset($itemDetail['loc_image']))?$itemDetail['loc_image']:$this->input->post('loc_image')
   																	 )))
   																	 . ((isset($itemDetail['loc_image']) && $itemDetail['loc_image']!='')?
   																	  "<img style=float:left;padding-right:5px height=\"60\" width=\"60\" src=".$target_dir."/".$itemDetail['loc_image']." >"
   																	  ."<img style=float:left;padding-right:5px height=\"60\" width=\"60\" src=".$target_dir."/".$itemDetail['loc_image1']." >"
   																	  ."<img style=float:left;padding-right:5px height=\"60\" width=\"60\" src=".$target_dir."/".$itemDetail['loc_image2']." >"
   																	  ."<img style=float:left;padding-right:5px height=\"60\" width=\"60\" src=".$target_dir."/".$itemDetail['loc_image3']." >"
   																	  :'')
   																	 .'<div class="input_img_wrap">
   														<span  class="btn btn-success fileinput-button add_img_button"><i class="glyphicon glyphicon-plus"></i><span>Add image...</span></span>
   												</div>'
   											  :((in_array($field['fieldname'],array("loc_desc")))?
   												form_textarea(array(
   											  'name'       => $field['fieldname']
   											  ,'id'        => $field['fieldname']
   											  ,'parsley-trigger' => "keyup"
   											  ,'parsley-rangelength'=> "[20,500]"
   											  ,'class'	   => 'form-control valid'
   											  ,'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:$this->input->post($field['fieldname']),
   											   ))
   									     	 :((in_array($field['fieldname'],array("locname")))?
   												form_input(array(
   											  'name'       => $field['fieldname']
   											  ,'id'        => $field['fieldname']
   											  ,'parsley-trigger' => "keyup"
   											  ,'parsley-rangelength'=> "[10,50]"
   											  ,'class'	   => 'form-control valid'
   											  ,'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:$this->input->post($field['fieldname']),
   											))
   										    :form_input(array(
   												'name'      => $field['fieldname'],
   												'id'        => $field['fieldname'],
   												'value'		=> (isset($itemDetail[$field['fieldname']])) ? $itemDetail[$field['fieldname']] : '',
   												'class'		=> ($field['fieldname'] == 'sitename') ? 'form-control required' : 'form-control'))
   												))));
   			}elseif($field['type']=='c' && $field['show']){
   					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
   					if($checked)$formFields[] = array(
   							'label'=>'<label  class="col-sm-4 control-label" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
   							'field'=>$this->configmodel->createFieldAdvance($field,isset($itemDetail[$field['fieldKey']]) ? $itemDetail[$field['fieldKey']] : '',''));
   			}
   		}
   		$data['form'] = array(
   		            'form_attr'=>array('action'=>'mconnect/addlocation/'.$pid.'/'.$id.'/'.$locid,'name'=>'addlocation','id'=>'addlocation','enctype'=>"multipart/form-data"),
   					'hidden'=>array('bid'=>$bid,'siteid'=>$id, 'locid'=>$locid,'pid'=>$pid),
   					'fields'=>$formFields,
   					'busid'=>$bid,
   					'pid'=>$this->session->userdata('pid'),
   					'close'=>form_close()
   				);
   		$this->sysconfmodel->viewLayout('form_view',$data);
   	}
      function listlocation($pid,$id){
   		$cbid=$this->session->userdata('cbid');
   		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   		$roleDetail = $this->roleDetail;	
   		if(!$roleDetail['modules']['49']['opt_view']) redirect('Employee/access_denied');
   		if($this->input->post('submit')){
   			if($this->session->userdata('search')!=""){
   				$s=$this->session->unset_userdata('search');
   			}
   		}
   		
   		$parentbids=array();
   		if($this->session->userdata('eid')==1){
   			$parentbids=$this->systemmodel->getChildBusiness();
   		}
   		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
   		$limit = '30';
   		$data['itemlist'] = $this->mconnectmodel->getlistlocation($bid,$pid,$ofset,$limit,$id);
   		$this->pagination->initialize(array(
   						 'base_url'=>site_url($this->uri->segment(3).'/')
   						,'total_rows'=>$data['itemlist']['count']
   						,'per_page'=>$limit		
   						,'uri_segment'=>3				
   				));
   		$data['module']['title'] = "Locations [".$data['itemlist']['count']."]";	
   		$links = array();	
   		$links[] = '<li><a href="mconnect/addlocation/'.$pid.'/'.$id.'"><span title="Add Location" class="glyphicon glyphicon-plus-sign">&nbsp;Add</span></a></li>';
   	    $links[] = ($roleDetail['modules']['49']['opt_delete']) ? '<li><a href="mconnect/bulkDelSite" class="blkDelsite"><span title="Bulk Delete" class="glyphicon glyphicon-trash">&nbsp;Delete</span></a></li>':'';
   	    $links[] = '<li class="divider"><a>&nbsp;</a></li>';
   		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
   		$fieldset = $this->configmodel->getFields('49',$bid);
   		$formFields = array();
   		foreach($fieldset as $field){
   			$checked = false;
   			if($field['type']=='s' && $field['show'] && !in_array($field['fieldname'],array('siteicon','sitevideo','siteimg'))){
   				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
   				if($checked) { $formFields[] = array(
   									'label'=>'<label class="col-sm-4 control-label" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
   											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
   									'field'=>form_input(array(
   												'name'      => $field['fieldname'],
   												'id'        => $field['fieldname'],
   												'class'		=>($field['fieldname']=="createdon" || $field['fieldname']=="lastmodified")?'datepicker_leads form-control':'form-control'
   												))
   									);
   								}		
   			}elseif($field['type']=='c' && $field['show']){
   				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
   				if($checked) { $formFields[] = array(
   								'label'=>'<label class="col-sm-4 control-label" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
   								'field'=>form_input(array(
   													'name'      => 'custom['.$field['fieldid'].']',
   													'class'      => 'form-control'
   													)));
   								//$advsearch['custom['.$field['fieldid'].']']=$field['customlabel'];							
   							}						
   			}
   		}
   		$data['links'] = $links;
   		$data['form'] = array(
   					'open'=>form_open_multipart(site_url('mconnect/listlocation/'.$pid.'/'.$id),array('name'=>'listlocation','class'=>'form','id'=>'listlocation','method'=>'post')),
   					'form_field'=>$formFields,
   					'parentids'=>$parentbids,
   					'adv_search'=>array(),
   					'busid'=>$bid,
   					'pid'=>$this->session->userdata('pid'),
   					'close'=>form_close(),
   					'title'=>$this->lang->line('level_search')
   					);
   		$data['paging'] = $this->pagination->create_links();
   		$this->sysconfmodel->data['html']['title'] .= " | Locations";
   		if(isset($_POST['search']) && $_POST['search'] == 'search'){
   			$this->load->view('search_view',$data);
   			return true;
   		}
   		$this->sysconfmodel->viewLayout('list_view',$data);
   	}
      
   	function deleteLocation($id=''){
   		if(!$this->feature_access())redirect('Employee/access_denied');
   		$cbid=$this->session->userdata('cbid');
   		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   		$roleDetail = $this->roleDetail;
   		$itemDetail = $this->configmodel->getDetail('49',$id,'',$bid);
   		$beaconid = $itemDetail['beaconid']; 
   		if(!$roleDetail['modules']['49']['opt_delete']) redirect('Employee/access_denied');
   		$ret = $this->mconnectmodel->deleteLocation($id,$bid,$beaconid);
   		if(!$ret == '0'){
   		redirect('mconnect/listlocation/0');
         	}
        }
   		 function sitevisits(){
   				if(!$this->feature_access())redirect('Employee/access_denied');
   			$cbid=$this->session->userdata('cbid');
   			$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   			$roleDetail = $this->roleDetail;	
   			if(!$roleDetail['modules']['49']['opt_view']) redirect('Employee/access_denied');
   			if($this->input->post('submit')){
   				if($this->session->userdata('search')!=""){
   					$s=$this->session->unset_userdata('search');
   				}
   			}
   			$data['nobulk']=true;	
   			$parentbids=array();
   			if($this->session->userdata('eid')==1){
   				$parentbids=$this->systemmodel->getChildBusiness();
   			}
   			$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
   			$limit = '30';
   			$data['itemlist'] = $this->mconnectmodel->sitevisits();
   			$this->pagination->initialize(array(
   							 'base_url'=>site_url($this->uri->segment(2).'/')
   							,'total_rows'=>$data['itemlist']['count']
   							,'per_page'=>$limit		
   							,'uri_segment'=>2				
   			));
   			$data['module']['title'] = "Site Visit";	
   			$links = array();	
   			$links[] = '<li><a href="mconnect/addlocation"><span title="Add Location" class="glyphicon glyphicon-plus-sign">&nbsp;Add</span></a></li>';
   			$links[] = ($roleDetail['modules']['49']['opt_delete']) ? '<li><a href="mconnect/bulkDelSite" class="blkDelsite"><span title="Bulk Delete" class="glyphicon glyphicon-trash">&nbsp;Delete</span></a></li>':'';
   			$links[] = '<li class="divider"><a>&nbsp;</a></li>';
   			$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
   			$this->sysconfmodel->viewLayout('list_view',$data);
   		}
           
   		function sitereferrals(){
   				if(!$this->feature_access())redirect('Employee/access_denied');
   			$cbid=$this->session->userdata('cbid');
   			$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   			$roleDetail = $this->roleDetail;	
   			if(!$roleDetail['modules']['49']['opt_view']) redirect('Employee/access_denied');
   			if($this->input->post('submit')){
   				if($this->session->userdata('search')!=""){
   					$s=$this->session->unset_userdata('search');
   				}
   			}
   			$data['nobulk']=true;	
   			$parentbids=array();
   			if($this->session->userdata('eid')==1){
   				$parentbids=$this->systemmodel->getChildBusiness();
   			}
   			$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
   			$limit = '30';
   			$data['itemlist'] = $this->mconnectmodel->sitereferrals();
   			$this->pagination->initialize(array(
   							 'base_url'=>site_url($this->uri->segment(2).'/')
   							,'total_rows'=>$data['itemlist']['count']
   							,'per_page'=>$limit		
   							,'uri_segment'=>2				
   			));
   			$data['module']['title'] = "Site Referral";	
   			$links = array();	
   			$links[] = '<li><a href="mconnect/addlocation"><span title="Add Location" class="glyphicon glyphicon-plus-sign">&nbsp;Add</span></a></li>';
   			$links[] = ($roleDetail['modules']['49']['opt_delete']) ? '<li><a href="mconnect/bulkDelSite" class="blkDelsite"><span title="Bulk Delete" class="glyphicon glyphicon-trash">&nbsp;Delete</span></a></li>':'';
   			$links[] = '<li class="divider"><a>&nbsp;</a></li>';
   			$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
   			$this->sysconfmodel->viewLayout('list_view',$data);
   		}
   		
        function siteoffers(){
   	   	if(!$this->feature_access())redirect('Employee/access_denied');
   		$cbid=$this->session->userdata('cbid');
   		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   		$roleDetail = $this->roleDetail;	
   		if(!$roleDetail['modules']['49']['opt_view']) redirect('Employee/access_denied');
   		if($this->input->post('submit')){
   			if($this->session->userdata('search')!=""){
   				$s=$this->session->unset_userdata('search');
   			}
   		}
   		
   		$parentbids=array();
   		if($this->session->userdata('eid')==1){
   			$parentbids=$this->systemmodel->getChildBusiness();
   		}
   		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
   		$limit = '30';
   		$data['itemlist'] = $this->mconnectmodel->sitevisits();
   		$this->pagination->initialize(array(
   						 'base_url'=>site_url($this->uri->segment(2).'/')
   						,'total_rows'=>$data['itemlist']['count']
   						,'per_page'=>$limit		
   						,'uri_segment'=>2				
   		));
   		$data['module']['title'] = "Site Offer";	
   		$links = array();	
   		$links[] = '<li><a href="mconnect/addlocation"><span title="Add Location" class="glyphicon glyphicon-plus-sign">&nbsp;Add</span></a></li>';
   	    $links[] = ($roleDetail['modules']['49']['opt_delete']) ? '<li><a href="mconnect/bulkDelSite" class="blkDelsite"><span title="Bulk Delete" class="glyphicon glyphicon-trash">&nbsp;Delete</span></a></li>':'';
   	    $links[] = '<li class="divider"><a>&nbsp;</a></li>';
   		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
   		$this->sysconfmodel->viewLayout('list_view',$data);
   	}
   	
   	 function addoffers($offerid){
   		$cbid=$this->session->userdata('cbid');
   		$bid=(isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
   		$parentbids=array();
   		if($this->session->userdata('eid')==1){
   			$parentbids=$this->systemmodel->getChildBusiness();
   		}
   		$roleDetail = $this->roleDetail;
   		if(!$roleDetail['modules']['50']['opt_view']) redirect('Employee/access_denied');
   		if($this->input->post('update_system')){
   			$this->form_validation->set_rules('offerper', 'Offer Percentage', 'required');
   			$this->form_validation->set_rules('siteid', 'Site Name', 'required');
   			if (!$this->form_validation->run() == FALSE){
   			$res = $this->mconnectmodel->addoffers($bid,$offerid);
   				$this->session->set_flashdata('msgt', 'success');
   					if($res == '0'){
   						$this->session->set_flashdata('msg', "Offer added Successfully");
   						redirect('ListOffers/0');
   					}else{
   						$this->session->set_flashdata('msg', "Offer Updated Successfully");
   						redirect('ListOffers/0');
   					}
   			}
   		}
   		$this->sysconfmodel->data['html']['title'] .= " | Add Offer";
   		$data['module']['title'] = "Add Offer";
   		$fieldset = $this->configmodel->getFields('50',$bid);
   		$itemDetail = $this->configmodel->getDetail('50',$offerid,'',$bid);
   		foreach($fieldset as $field){
   			$checked = false;
   			if($field['type']=='s' && $field['show'] && $field['fieldname']!='' && $field['fieldname']!=''){
   				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
   				if($checked && !in_array($field['fieldname'] ,array())) 
   					$formFields[] = array(
   									'label'=>'<label  class="col-sm-4 control-label" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
   											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' <img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp: </label>',
   												'field'=>(in_array($field['fieldname'],array("propertyname")) ?( form_dropdown('propertyname',$this->mconnectmodel->getpropertylist(),isset($itemDetail['propertyname']) ? $itemDetail['propertyname']:'','id="propertyname" class="form-control required" '))
   											   :((in_array($field['fieldname'],array('starttime','endtime')))?
   												form_input(array(
   											  'name'       => $field['fieldname']
   											  ,'id'        => $field['fieldname']
   											  ,'class'	   => 'datefutpicker form-control'
   											  ,'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:$this->input->post($field['fieldname']),
   											   ))
   											  :((in_array($field['fieldname'],array('siteid')))?
   												( form_dropdown('siteid',$this->mconnectmodel->getallsite(),isset($itemDetail['siteid']) ? $itemDetail['siteid']:'','id="siteid" class="form-control required" '))
   											  :form_input(array(
   												'name'      => $field['fieldname'],
   												'id'        => $field['fieldname'],
   												'value'		=> (isset($itemDetail[$field['fieldname']])) ? $itemDetail[$field['fieldname']] : '',
   												'class'		=> 'form-control ' ))
   												))));
   			}elseif($field['type']=='c' && $field['show']){
   					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
   					if($checked)$formFields[] = array(
   							'label'=>'<label  class="col-sm-4 control-label" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
   							'field'=>$this->configmodel->createFieldAdvance($field,isset($itemDetail[$field['fieldKey']]) ? $itemDetail[$field['fieldKey']] : '',''));
   			}
   		}
   		$data['form'] = array(
   		            'form_attr'=>array('action'=>'mconnect/addoffers/'.$offerid,'name'=>'addoffers','id'=>'addoffers','enctype'=>"multipart/form-data"),
   					'hidden'=>array('bid'=>$bid),
   					'fields'=>$formFields,'parentids'=>$parentbids,
   					'close'=>form_close()
   				);
   		$this->sysconfmodel->viewLayout('form_view',$data);
   	}
   	 function listoffers($id){
   		if(!$this->feature_access())redirect('Employee/access_denied');
   		$cbid=$this->session->userdata('cbid');
   		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   		$roleDetail = $this->roleDetail;	
   		if(!$roleDetail['modules']['50']['opt_view']) redirect('Employee/access_denied');
   		if($this->input->post('submit')){
   			if($this->session->userdata('search')!=""){
   				$s=$this->session->unset_userdata('search');
   			}
   		}
   		
   		$parentbids=array();
   		if($this->session->userdata('eid')==1){
   			$parentbids=$this->systemmodel->getChildBusiness();
   		}
   		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
   		$limit = '30';
   		$data['itemlist'] = $this->mconnectmodel->getlistoffers($bid,$ofset,$limit,$id);
   		$this->pagination->initialize(array(
   						 'base_url'=>site_url($this->uri->segment(2).'/')
   						,'total_rows'=>$data['itemlist']['count']
   						,'per_page'=>$limit		
   						,'uri_segment'=>2				
   				));
   		$data['module']['title'] = "Offers [".$data['itemlist']['count']."]";	
   		$links = array();	
   		$links[] = ' ';
   		$fieldset = $this->configmodel->getFields('50',$bid);
   		$formFields = array();
   		foreach($fieldset as $field){
   			$checked = false;
   			if($field['type']=='s' && $field['show'] && !in_array($field['fieldname'],array())){
   				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
   				if($checked) { $formFields[] = array(
   									'label'=>'<label class="col-sm-4 control-label" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
   											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
   									'field'=>form_input(array(
   												'name'      => $field['fieldname'],
   												'id'        => $field['fieldname'],
   												'class'		=>'form-control'
   									)));
   								}		
   			}elseif($field['type']=='c' && $field['show']){
   				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
   				if($checked) { $formFields[] = array(
   								'label'=>'<label class="col-sm-4 control-label" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
   								'field'=>form_input(array(
   													'name'      => 'custom['.$field['fieldid'].']',
   													'class'      => 'form-control'
   													)));				
   							}						
   			}
   		}
   		$data['links'] = $links;
   		$data['nobulk'] = true;
   		$data['form'] = array(
   					'open'=>form_open_multipart(site_url('mconnect/listoffers'),array('name'=>'listoffers','class'=>'form','id'=>'listoffers','method'=>'post')),
   					'form_field'=>$formFields,
   					'parentids'=>$parentbids,
   					'adv_search'=>array(),
   					'busid'=>$bid,
   					'pid'=>$this->session->userdata('pid'),
   					'close'=>form_close(),
   					'title'=>$this->lang->line('level_search')
   					);
   		$data['paging'] = $this->pagination->create_links();
   		$this->sysconfmodel->data['html']['title'] .= " | Offers";
   		if(isset($_POST['search']) && $_POST['search'] == 'search'){
   			$this->load->view('search_view',$data);
   			return true;
   		}
   		$this->sysconfmodel->viewLayout('list_view',$data);
   	}
       function deleteoffer($offerid=''){
   		if(!$this->feature_access())redirect('Employee/access_denied');
   		$cbid=$this->session->userdata('cbid');
   		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   		$roleDetail = $this->roleDetail;	
   		if(!$roleDetail['modules']['50']['opt_delete']) redirect('Employee/access_denied');
   		$ret = $this->mconnectmodel->deleteoffer($offerid,$bid);
   		if(!$ret == '1'){
   		redirect('mconnect/listoffers/0');
         	}
        }
   
   	
   	function addemptosites($pid='',$siteid='',$eid=''){
   		if(!$this->feature_access(1))redirect('Employee/access_denied');
   		$cbid=$this->session->userdata('cbid');
   		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   		if($this->input->post('update_system')){
   		if($_POST['empid']!=''){
   				$res=$this->mconnectmodel->editemp_site($pid,$siteid);
   				$this->session->set_flashdata('msgt', 'success');
   				$this->session->set_flashdata('msg', 'Updated Successfully');
   				redirect('ListExeSite/'.$pid.'/'.$siteid);
   			}else{
   				$res=$this->mconnectmodel->addemp_site($pid,$siteid);		
   				if($res==0){
   					$this->session->set_flashdata('msgt', 'error');
   					$this->session->set_flashdata('msg', $this->lang->line('error_alreadyexists'));
   					redirect('AddExeSite/'.$pid);
   				}else{
   					$this->session->set_flashdata('msgt', 'success');
   					$this->session->set_flashdata('msg', $this->lang->line('error_addempsucss'));
   					redirect('ListExeSite/'.$pid.'/'.$siteid);
   				}
   			}
   		}
   		$roleDetail = $this->roleDetail;
   		if(!$roleDetail['modules']['3']['opt_add']) redirect('Employee/access_denied');
   		$gpDetail = $this->configmodel->getDetail('3',$siteid,'',$bid);
   		if(count($_POST)>0){
   			redirect($_SERVER['HTTP_REFERER']);
   		}
   		$gpEmp = ($eid!='')?$this->mconnectmodel->getSiteEmpDetail($siteid,$eid):array();
   		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_addemptosites');
   		$data['module']['title'] = $this->lang->line('label_addemptosites');
   		$formFields = array();
   		$data['form'] =  array( 'form_attr'=>array('action'=>'AddExeSite/'.$pid.'/'.$siteid.'/'.$eid,'name'=>'addexe','id'=>'addexe','enctype'=>"multipart/form-data"),
   										'fields'=>$formFields,
   										'hidden' => array('bid'=>$bid,'pid'=>$pid,'siteid'=>$siteid,'empid'=>$eid),
   										'close'=>form_close(),
   										'emplist'=>$this->systemmodel->get_emp_list(),
   										'empexists'=>$this->mconnectmodel->site_enteremplist($siteid),
   										'gdetail'=>$gpDetail,
   										'gpEmp'=>$gpEmp,
   						);
   		$this->sysconfmodel->viewLayout('form_view_emp',$data);
   	}
   
   
        function site_emp_list($pid,$siteid){
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('label_siteemp');
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$gpDetail = $this->configmodel->getDetail('3',$siteid,'',$bid);
		$limit = '30';
		$header = array('#'
		                ,'Action'
	                	,$this->lang->line('level_empname')
						,$this->lang->line('label_lsite')
						,$this->lang->line('level_starttime')
						,$this->lang->line('level_endtime')
						,$this->lang->line('label_isfailover')
						,"Call Counter"
						);
		$data['itemlist']['header'] = $header;
	    $emp_list=$this->mconnectmodel->siteemplist($pid,$siteid,$ofset,$limit);
	    $roleDetail = $this->roleDetail;
		$opt_add=$roleDetail['modules']['3']['opt_add']; 
		$opt_delete=$roleDetail['modules']['3']['opt_delete']; 
		$rec = array();$list = array();
		if(count($emp_list['data'])>0){
			$i=1;
			foreach ($emp_list['data'] as $item){
				$rec = array($i);
				$opt = '';
			    $opt = '<div class="btn-group">
                        <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">Actions&nbsp;
                        <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu" role="menu" style="text-align:left;">' ;
				$s=($item['status']=="1")? '<li><a href="'.site_url('mconnect/dis_site_emp/'.$item['pid'].'/'.$item['siteid']."/".$item['eid']).'">
				<span class="fa fa-unlock" title="Disable">&nbsp;Disable</span></a></li>':'<li><a href="'.site_url('mconnect/dis_site_emp/'.$item['pid'].'/'.$item['siteid']."/".$item['eid']).'"><span class="fa fa-lock" title="Enable">&nbsp;Enable</span></a></li>';
				$opt.=($opt_add)?'<li><a href="'.site_url('AddExeSite/'.$item['pid'].'/'.$item['siteid']."/".$item['eid']).'"><span title="Edit" class="fa fa-edit">&nbsp;Edit</span></a><li>':'';
				$opt.=($opt_delete)?'<li><a href="'.site_url('mconnect/delete_site_emp/'.$item['pid'].'/'.$item['eid']."/".$item['siteid']).'">
								<span title="Delete Employee from Group" class="fa fa-fw fa-trash">&nbsp;Delete</span></a></li>':'';
				$opt.=($opt_add)?$s:'';
			    $rec['action'] = $opt;	 
				$rec[] = ($item['status']=="1" && $item['estatus']=='1') ? $item['empname'] : $item['empname'];
				$rec[] = ($item['status']=="1" && $item['estatus']=='1') ? $item['sitename'] : $item['sitename'];
				$rec[] = ($item['status']=="1" && $item['estatus']=='1') ? $item['starttime'] : $item['starttime'];
				$rec[] = ($item['status']=="1" && $item['estatus']=='1') ? $item['endtime'] : $item['endtime'];
				$rec[] = ($item['status']=="1" && $item['estatus']=='1') ? $item['failover'] : $item['failover'];
				$rec[] = ($item['status']=="1" && $item['estatus']=='1') ? $item['callcounter'] :$item['callcounter'];
				$i++;
				array_push($list,$rec);
			 }
		}
		$data['itemlist']['rec'] = $list;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('ListempGroup/'.$this->uri->segment(3))
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>4					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_groupemp'); 
		$links = array();
		$links[] ='<li><a href="AddExeSite/'.$pid.'/'.$siteid.'"><span title="Add Number" class="fa fa-fw fa-user-plus">&nbsp;Add Executive</span></a></li>';
		$links[] = '<li><a href="ResCounter/'.$siteid.'"> <span title="Reset Counter" class="fa fa-refresh">&nbsp;Reset Counter</span></a></li>';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$formFields = array();
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">Employee Name : </label>',
				  'field'=>form_input(array(
									   'name'         => 'empname',
									   'class'        => 'form-control',
										'id'          => 'empname',
										'value'       => $this->session->userdata('empname'))));
		array_push($formFields,$cf);
		$data['links'] = $links;	
		$data['nobulk']=true;				
		$data['form'] = array(
			'open'=>form_open_multipart('ListExeSite/'.$siteid,array('name'=>'managexe','class'=>'form','id'=>'managexe','method'=>'post')),
			'form_field'=>$formFields,
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
   	function refreshcounter($siteid){
   		$cbid=$this->session->userdata('cbid');
   		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   		$res = $this->mconnectmodel->refreshcounter($siteid,$bid);
   		if($res == 1){
   			$this->session->set_flashdata('msgt', 'success');
   			$this->session->set_flashdata('msg', ' The Call Counter has reset to 0');
   		}else{
   			$this->session->set_flashdata('msgt', 'error');
   			$this->session->set_flashdata('msg', ' Error while reset the counter');
   		}
   		redirect('ListExeSite/'.$siteid);
   	}
   	function delete_site_emp($pid,$eid,$siteid){
   		$res=$this->mconnectmodel->delete_site($pid,$eid,$siteid);
   			redirect('ListExeSite/'.$pid.'/'.$siteid);
   	}
   	function getSitelist($val =''){
		$option='';
		$option .='<option value=""> Select </option>';
		$result = $this->mconnectmodel->getSitelist($val);
		foreach($result as $res){
				$option.='<option value='.$res['siteid'].'>'.$res['sitename'].'</option>';
		}
		echo $option;
	}
   	function dis_site_emp($pid,$siteid,$eid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->mconnectmodel->dis_site_employee($pid,$siteid,$eid,$bid);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', ($res)?'Employee Enabled Succesfully':'Employee Disabled Succesfully');
		redirect('ListExeSite/'.$pid.'/'.$siteid);
	}
   }
   ?>
