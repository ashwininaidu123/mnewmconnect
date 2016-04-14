<?php
class missedalerts extends Controller {
	var $data,$roleDetail;
	function missedalerts(){	
		parent::controller();
		//if(!$this->session->userdata('logged_in'))redirect('/user');
		if(!$this->session->userdata('logged_in'))redirect('/site/login');
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('systemmodel');
		$this->load->model('groupmodel');
		$this->load->model('configmodel');
		$this->load->model('colormodel');
		$this->load->model('empmodel');
		$this->load->model('pollmodel');
		$this->load->model('missedcallmodel');
		$this->roleDetail = $this->sysconfmodel->data['roleDetail'];
	}
	function index(){
		$this->addmissedgroup();	
	}
	//~ 
	function addmissedgroup($id=''){
		//echo $this->pollmodel->landing_number($id);exit;
		if(!$this->roleDetail['modules']['25']['opt_add']) redirect('Employee/access_denied');
		$poll_details=$this->missedcallmodel->missedcallDetails($id);
		if($this->input->post('update_system')){
			
				$this->form_validation->set_rules('keyword', 'keyword', 'required');
				$this->form_validation->set_rules('csms', 'Sms to client', 'required');
				$this->form_validation->set_rules('esms', 'Sms to Business User', 'required');
				if(!$this->form_validation->run() == FALSE){	
						if($id!=""){
							$res=$this->missedcallmodel->addMissedcallgroup($id);
							$this->session->set_flashdata('msgt', 'success');
							$this->session->set_flashdata('msg', "Missed Call Group Updated Successfully");
							redirect('missedalerts/listcalls');
						}else{
							$res=$this->missedcallmodel->addMissedcallgroup($id);
							$this->session->set_flashdata('msgt', 'success');
							$this->session->set_flashdata('msg', "Missed Call Group Created Successfully");
							redirect('missedalerts/listcalls');
						 }	
				}
		}
		$roleDetail = $this->roleDetail;
		 $systemfields=array();
		 foreach($roleDetail['system'] as $ret){
			if($ret['modid']==25){
				$systemfields[$ret['fieldname']]=$ret['fieldname'];
			}
		 }
		 //print_r($systemfields);exit;
		$this->sysconfmodel->data['html']['title'] .= " | Missed Call setup";
		$data['module']['title'] ="New  Missed Call setup";
		$formFields=array();
		$formFields1=array();
		$jss1 = 'id="pri" class="required"';
				$this->sysconfmodel->data['html']['title'] .= " | Missed Call setup";
				$data['module']['title'] ="Missed Call setup";
				$cf=array('label'=>'Landing Number',
							  'field'=>form_dropdown('pri',($id!="")?$this->pollmodel->landing_number($id,'5'):$this->systemmodel->getPriList(),isset($poll_details->prinumber)?$poll_details->prinumber:'',$jss1));
					if(isset($systemfields['landingnumber']))array_push($formFields,$cf);
				$cf=array('label'=>'Keyword',
						   'field'=>form_input(array(
									  'name'        => 'keyword',
										'id'          => 'keyword',
										'class'		=>'required',	
										'value'       => isset($poll_details->keyword)?$poll_details->keyword:'')));
						if(isset($systemfields['keyword']))array_push($formFields,$cf);	
				
				$cf=array('label'=>'Employees',
						   'field'=>form_dropdown('eid',$this->systemmodel->get_emp_list(),isset($poll_details->eid)?$poll_details->eid:'','id="eid"'));
						if(isset($systemfields['employee']))array_push($formFields,$cf);	
				$cf=array('label'=>'SMS to customer',
						   'field'=>form_textarea(array(
									  'name'        => 'csms',
										'id'          => 'csms',
										'class'          => 'required word_count',
										'value'       => isset($poll_details->smstoclient)?$poll_details->smstoclient:'')));
						if(isset($systemfields['smstocustomer']))array_push($formFields,$cf);	
					
				$cf=array('label'=>'SMS to Employee<br/>',
						   'field'=>form_textarea(array(
									  'name'        => 'esms',
										'id'          => 'esms',
										'class'          => 'required word_count',
										'value'       => isset($poll_details->smstobusiness)?$poll_details->smstobusiness:'')));
						if(isset($systemfields['smstobusiness']))array_push($formFields,$cf);	
			$data['form'] = array(
			            'form_attr'=>array('action'=>'missedalerts/addmissedgroup/'.$id,'name'=>'missedgroup'),
						//~ 'open'=>form_open_multipart('missedalerts/addmissedgroup/'.$id,array('name'=>'missedgroup','class'=>'form','id'=>'missedgroup','method'=>'post'),array('old_pri'=>isset($poll_details->prinumber)?$poll_details->prinumber:'')),
						'fields'=>$formFields,
						'fields1'=>$formFields1,
						'close'=>form_close()
						);
				$this->sysconfmodel->viewLayout('form_view',$data);
		}
		function listcalls(){
		$data['module']['title'] ="List Missed Call Groups";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('#'
						,'Landing Number'
						,'keyword'
						,'Employee'
						,$this->lang->line('level_Action')
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->missedcallmodel->listcalls($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			$pollnum=$this->pollmodel->getNumber($item['prinumber']);
			$status=($item['status']=="0")?'<span class="fa fa-lock" title="Enable"></a>':'<span class="fa fa-unlock" title="Disable">';
			$mp=$this->empmodel->get_employee($item['eid']);
			$rec[] = array(
				$item['mid']
				,$pollnum
				,$item['keyword']
				,$mp[0]['empname']
				,'<a href="missedalerts/addmissedgroup/'.$item['mid'].'"><span title="Edit" class="fa fa-edit"></span></a>&nbsp;<a href="missedalerts/Changestatus/'.$item['mid'].'">'.$status.'</a>&nbsp;<a href="missedalerts/getmissedcallReports/'.$item['mid'].'" title="Show reports"><img src="'.site_url('system/application/img/icons/1340096409_report_add.png').'"/></a>'
			);
		}

		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		
		//$data['itemlist'] = $this->groupmodel->getgrouplist($bid,$ofset,$limit);
		
		$this->pagination->initialize(array(
						 'base_url'=>site_url('poll/listpoll/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		//$data['addlinks']="group/add_group";		
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | Poll";
		$data['links']=$data['links']='';
		$formFields1 = array();
		$cf=array('label'=>'<label for="groupname">'.$this->lang->line('level_groupname').' : </label>',
				  'field'=>form_input(array(
									  'name'        => 'groupname',
										'id'          => 'groupname',
										'value'       => $this->session->userdata('groupname'))));
						array_push($formFields1,$cf);
						
		$this->sysconfmodel->data['links'] = '<a href="poll/addpoll"><span title="Add Number" class="glyphicon glyphicon-plus-sign"></span></a>';
		//$fieldset = $this->configmodel->getFields('3');
		$formFields = array();
		$data['form'] = array(
			'open'=>form_open_multipart('poll/listpoll/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function Changestatus($poll_id){
		$s=$this->missedcallmodel->CHangeStatus($poll_id);
		if($s!=0){
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg', "Poll Enabled Succesfully");
			redirect('missedalerts/listcalls');
		
		}else{
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg', "Poll Disabled Succesfully");
			redirect('missedalerts/listcalls');
		}
		
	}
	function getmissedcallReports($id){
			$data['module']['title'] ="Reports";
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '30';
		$header = array('#','Callfrom','Date & Time',);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->missedcallmodel->getcallReports($id,$ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		 $pollnum='';$okey='';
		foreach ($emp_list['data'] as $item){
				$rec[] = array($item['msid'],$item['callfrom'],$item['datetime']);
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('missedalerts/getmissedcallReports/'.$id)
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>4					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | Missed call Reports";
		$data['links']=$data['links']='';
		$this->sysconfmodel->data['links'] = '<a href="poll/"><span title="Add Number" class="glyphicon glyphicon-plus-sign"></span></a>';
		$formFields = array();
		$formFields[] = array(
				'label'=>'<label for="f">Start Time : </label>',
				'field'=>form_input(array(
						'name'      => 'starttime',
						'id'        => 'starttime',
						'value'     => $this->session->userdata('starttime'),
						'class'=>'datepicker_leads'
						))
						);
		$formFields[] = array(
				'label'=>'<label for="f">End Time : </label>',
				'field'=>form_input(array(
						'name'      => 'endtime',
						'id'        => 'endtime',
						'value'     => $this->session->userdata('endtime'),
						'class'=>'datepicker_leads'
						))
						);
		
		$data['form'] = array(
			'open'=>form_open_multipart('missedalerts/getmissedcallReports/'.$id,array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		$this->sysconfmodel->viewLayout('list_view',$data);
		
		
	}
	
}
/* end of Employee controller */
