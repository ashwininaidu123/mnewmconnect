<?php
class Company extends controller
{
	var $data;
	function Company(){
		parent::controller();
		//if(!$this->session->userdata('logged_in'))redirect('/user');
		if(!$this->session->userdata('logged_in'))redirect('/site/login');
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('systemmodel');
		$this->load->model('empmodel');
		$this->load->model('configuremodel');
		$this->load->model('companymodel');
		$this->roleDetail = $this->sysconfmodel->data['roleDetail'];
	}
	function index(){
		$this->listcompany();
	}
	function listcompany(){
		$data['module']['title']="List Company";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '20';
		$header = array('#','Company','Owner','Action');
		$data['itemlist']['header'] = $header;
		$emp_list=$this->companymodel->ListCompany($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;
		foreach ($emp_list['data'] as $item){
			 $status=($item['status']!=0)?'<span class="fa fa-lock" title="Enable"></span>':'<span class="fa fa-unlock" title="Disable"></span>';
			$rec[] = array($i,$item['companyname'],$item['empname'],'<a href="company/addCompany/'.$item['cid'].'"><span title="Edit" class="fa fa-edit"></span></a>'.'&nbsp;<a href="company/Actionoperations/'.$item['cid'].'")>'.$status.'</a>');
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('company/listcompany/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | List Company";
		$data['links']='';
		$formFields = array();
		
						
		$this->mastermodel->data['links'] = '';
		
		$data['form'] = array(
							'open'=>form_open_multipart('company/listcompany/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->sysconfmodel->viewLayout('list_view',$data);	
	}
	function addCompany($id=''){
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('companyname', 'Company Name', 'required|min_length[4]|max_length[50]');
			$this->form_validation->set_rules('owner', 'Owner', 'required');
			if(!$this->form_validation->run() == FALSE)
			{	
				$res=$this->companymodel->addCompany($id);
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg', 'Company Added Successfully');
				redirect('company/listcompany');
				
			}
		}
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['26']['opt_add']) redirect('Employee/access_denied');
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_Company');
		$data['module']['title'] = $this->lang->line('label_Company');
		$fieldset = $this->configmodel->getFields('26');
		$formFields = array();$formFields1 = array();
		$itemDetail = $this->configmodel->getDetail('26',$id);
		(sizeof($itemDetail)>0)?$x='edit':$x='add';
		
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){
						$cf = array('label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp: </label>',
									'field'=>($field['fieldname']=="owner")?
												form_dropdown('owner',$this->groupmodel->employee_list(),($id!="")?$itemDetail[$field['fieldname']]:$this->input->post('owner'),'id="owner" class="auto required"')
									
									
											:form_input(array(
											  'name'      => $field['fieldname'],
											  'id'        => $field['fieldname'],
											  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:set_value($field['fieldname'])
											  ,'class'=>'required'
												)));
						array_push($formFields,$cf);
				}
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$cf = array('label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createField($field,
											isset($itemDetail['custom['.$field['fieldid'].']'])?
											$itemDetail['custom['.$field['fieldid'].']']:'')
						);
					array_push($formFields,$cf);
				}
			}
		}
		
		$data['form'] = array(
					'open'=>form_open_multipart('company/addCompany/'.$id,array('name'=>'addcompany','class'=>'form','id'=>'addcompany','method'=>'post')),
					'fields'=>$formFields,
					'fields1'=>$formFields1,
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('form_view1',$data);
	}
	function Actionoperations($cid){
		$res=$this->companymodel->stat_operations($cid);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', 'Status updated successfully');
		redirect('company/listcompany');
	}
}
/* end of custom field controller */
