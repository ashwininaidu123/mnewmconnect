<?php
class poll extends Controller {
	var $data,$roleDetail;
	function poll(){	
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
		
		$this->roleDetail = $this->sysconfmodel->data['roleDetail'];
		
	}
	function addpoll($poll_id=''){
		$today_date=date('Y-m-d H:i:s');	
		if(!$this->roleDetail['modules']['24']['opt_add']) redirect('Employee/access_denied');
		$poll_details=$this->pollmodel->polldetails($poll_id);
		if($this->input->post('submit')){
				$this->form_validation->set_rules('ptitle', 'Poll Title', 'required');
				$this->form_validation->set_rules('stime', 'Start time', 'required|callback_checkstartime');
				$this->form_validation->set_rules('etime', 'End Time', 'required|callback_checkendtime');
				$this->form_validation->set_rules('ptype', 'Poll Type', 'required');
				if(!$this->form_validation->run() == FALSE){	
						if($poll_id!=""){
							$res=$this->pollmodel->update_poll($poll_id);
							$this->session->set_flashdata('msgt', 'success');
							$this->session->set_flashdata('msg', "Poll Updated Succesfully");
							redirect('poll/editpoll_opt/'.$poll_id);
							
						}else{
							$res=$this->pollmodel->pollconfig();
							$this->session->set_flashdata('msgt', 'success');
							$this->session->set_flashdata('msg', "Poll Added Succesfully");
							redirect('poll/poll_setup/'.$res);
						 }	
				}
		}
		
		$this->sysconfmodel->data['html']['title'] .= " | Poll setup";
		$data['module']['title'] ="New Poll Setup";
		$formFields=array();
		$formFields1=array();
		$roleDetail = $this->roleDetail;
		 $systemfields=array();
		 foreach($roleDetail['system'] as $ret){
			if($ret['modid']==24){
				$systemfields[$ret['fieldname']]=$ret['fieldname'];
			}
		 }
				$polltype=array(""=>"Select","1"=>"Single","2"=>"Multiple");
				$this->sysconfmodel->data['html']['title'] .= " | Number Selection";
				$data['module']['title'] ="Number Selection";
				$cf=array('label'=>'Poll Title',
						   'field'=>form_input(array(
									  'name'        => 'ptitle',
										'id'          => 'ptitle',
										'class'		=>'required',	
										'value'       => isset($poll_details->poll_title)?$poll_details->poll_title:'')));
						if(isset($systemfields['ptitle']))array_push($formFields,$cf);	
				$expire=isset($poll_details->end_date)?(($poll_details->end_date<=$today_date)?'1':'0'):'0';
				$cf=array('label'=>'Start Time',
						   'field'=>form_input(array(
									  'name'        => 'stime',
										'id'          => 'stime',
										'class'          => ($expire!=1)?'datepicker_leads required':'required',
										'readonly'=>'true',
										'value'       => isset($poll_details->startdate)?$poll_details->startdate:'')));
						if(isset($systemfields['stime']))array_push($formFields,$cf);	
				$cf=array('label'=>'End Time',
						   'field'=>form_input(array(
									  'name'        => 'etime',
										'id'          => 'etime',
										'class'          =>($expire!=1)?'datepicker_leads required':'required',
										'readonly'=>'true',
										'value'       => isset($poll_details->end_date)?$poll_details->end_date:'')));
						if(isset($systemfields['etime']))array_push($formFields,$cf);	
						$jss = 'id="ptype" class="required"';
				$cf=array('label'=>'Polltype',
						   'field'=>form_dropdown('ptype',$polltype,isset($poll_details->poll_type)?$poll_details->poll_type:'',$jss)	);		
					if(isset($systemfields['ptype']))array_push($formFields,$cf);	
				if(isset($poll_details->poll_type) && $poll_details->poll_type!=2){
					$jss1 = 'id="pri" class="required"';
					$cf=array('label'=>'Landing Number',
							  'field'=>form_dropdown('pri',$this->pollmodel->landing_number($poll_id,'4'),isset($poll_details->prinumber)?$poll_details->prinumber:'',$jss1));
					if(isset($systemfields['landingnumber']))array_push($formFields1,$cf);
					
				}
			$data['form'] = array(
						'open'=>form_open_multipart('poll/addpoll/'.$poll_id,array('name'=>'polls','class'=>'form','id'=>'polls','method'=>'post'),array('numbertype'=>isset($poll_details->poll_type)?$poll_details->poll_type:'','old_pri'=>isset($poll_details->prinumber)?$poll_details->prinumber:'')),
						'fields'=>$formFields,
						'fields1'=>$formFields1,
						'pris'=>$this->systemmodel->getPriList(),	
						'close'=>form_close(),
						'polr'=>isset($poll_details->poll_type)?$poll_details->poll_type:0,
						'expiry'=>$expire
						
					);
					
				$this->sysconfmodel->viewLayout('poll_page2',$data);
		
		
		
	}
	function index(){
		$this->addpoll();	

	}
	function checkstartime($start){
		$today=date('Y-m-d');
			//echo ($start==$today)?'dd':'dddd';exit;
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
	function poll_setup($poll_id){
		$poll_details=$this->pollmodel->polldetails($poll_id);
		if($this->input->post('submit')){
			$rs=$this->pollmodel->poll_options($poll_id,$poll_details->poll_type);
			if($rs!=0){
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', $rs." options not added");
				redirect('poll/editpoll_opt/'.$poll_id);
			}else{
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg', "Poll Added Succesfully");
				redirect('poll/listpoll');
			}
		}
		
		$formFields=array();
		
		$this->sysconfmodel->data['html']['title'] .= ($poll_details->poll_type!=2)?" | Single Number Selection":" | Multi Number Selection";
		$data['module']['title'] =($poll_details->poll_type!=2)?"  Single Number Selection":"  Multi Number Selection";
		$data['form'] = array(
				'open'=>form_open_multipart('poll/poll_setup/'.$poll_id,array('name'=>'polls_single','class'=>'form','id'=>'polls_single','method'=>'post'),array('numbertype'=>$this->input->post('polltype'),'st'=>'0')),
				'fields'=>$formFields,
				'pris'=>$this->systemmodel->getPriList(),	
				'close'=>form_close()
			);
			$view=($poll_details->poll_type!=2)?"poll_pagesingle":"poll_pagemulti";
		$this->sysconfmodel->viewLayout($view,$data);
	}
	function listpoll(){
		$data['module']['title'] ="List Poll";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array($this->lang->line('poll_id')
						,$this->lang->line('level_polltitle')
						,$this->lang->line('level_polltype')
						,$this->lang->line('level_starttime')
						,$this->lang->line('level_endtime')
						,$this->lang->line('level_Action')
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->pollmodel->listpoll($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			$status=($item['status']=="0")?'<span class="fa fa-lock" title="Enable"></a>':'<span class="fa fa-unlock" title="Disable">';
			$rec[] = array(
				$item['poll_id']
				,$item['poll_title']
				,($item['poll_type']!=2)?'Single Number':'Multi Number Poll'
				,$item['startdate']
				,$item['end_date']
				,'<a href="poll/addpoll/'.$item['poll_id'].'"><span title="Edit" class="fa fa-edit"></span></a>&nbsp;<a href="poll/Changestatus/'.$item['poll_id'].'">'.$status.'</a>&nbsp;<a href="poll/option_settings/'.$item['poll_id'].'" title="option Settings"><img src="'.site_url('system/application/img/icons/cog.png').'"/></a>&nbsp;<a href="poll/Result_Poll/'.$item['poll_id'].'" title="View Graph"><img src="'.site_url('system/application/img/icons/1340023691_chart_bar.png').'"/></a>&nbsp;<a href="poll/getReports/'.$item['poll_id'].'" title="View Repots"><img src="'.site_url('system/application/img/icons/1340096409_report_add.png').'"/></a>'
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
		
						
		$this->sysconfmodel->data['links'] = '<a href="poll/addpoll"><span title="Add Number" class="glyphicon glyphicon-plus-sign"></span></a>';
		//$fieldset = $this->configmodel->getFields('3');
		$formFields = array();
		$cf=array('label'=>'<label for="groupname">Poll Name : </label>',
				  'field'=>form_input(array(
									  'name'        => 'pollname',
										'id'          => 'pollname',
										'value'       => $this->session->userdata('pollname'))));
						array_push($formFields,$cf);
						$options=array(""=>"select","1"=>"Single Number Poll","2"=>"Multiple Number poll");
		$cf=array('label'=>'<label for="groupname">Poll type : </label>',
				  'field'=>form_dropdown('polltype',$options,'','id="polltype"'));
						array_push($formFields,$cf);
		$data['form'] = array(
			'open'=>form_open_multipart('poll/listpoll/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function editpoll_opt($poll_id){
		if($this->input->post('submit')){
			$res=$this->pollmodel->update_polloptions($poll_id);
			if($res!=0){
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', $res." options Not added");
				redirect('poll/editpoll_opt/'.$poll_id);
			}else{
				redirect('poll/listpoll');
			}
		}
		$res=$this->pollmodel->gt_options($poll_id);
		if(!empty($res)){
			$poll_details=$this->pollmodel->polldetails($poll_id);
			$formFields=array();
			$this->sysconfmodel->data['html']['title'] .= ($poll_details->poll_type!=2)?" | Single Number Selection":" | Multi Number Selection";
			$data['module']['title'] =($poll_details->poll_type!=2)?"  Single Number Selection":"  Multi Number Selection";
			$data['form'] = array(
						'open'=>form_open_multipart('poll/editpoll_opt/'.$poll_id,array('name'=>'polls_single','class'=>'form','id'=>'polls_single','method'=>'post'),array('poll_t'=>$poll_details->poll_type,'st'=>sizeof($res))),
						'fields'=>$formFields,
						'pris'=>$this->pollmodel->landing_number('','4'),	
						'options'=>$res,
						'close'=>form_close(),
						'pollty'=>$poll_details->poll_type,
						'id'=>$poll_id,
					);
					$view="edit_pollpage";
				$this->sysconfmodel->viewLayout($view,$data);
		}else{
			$this->poll_setup($poll_id);
		}
	}
	function remove_opt($poll_id,$option_id){
		$res=$this->pollmodel->del_polloptions($poll_id,$option_id);
		redirect('poll/editpoll_opt/'.$poll_id);
		
		
	}
	function Changestatus($poll_id){
		$s=$this->pollmodel->CHangeStatus($poll_id);
		if($s!=0){
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg', "Poll Enabled Succesfully");
			redirect('poll/listpoll');
		}else{
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg', "Poll Disabled Succesfully");
			redirect('poll/listpoll');
		}
		
	}
	function opt_delete($poll_id,$option_id){
		$res=$this->pollmodel->del_polloptions($poll_id,$option_id);
		redirect('poll/option_settings/'.$poll_id);
	}
	function option_settings($poll_id){
		$data['module']['title'] ="Poll Options";
		$header = array('Options'
						,'OPtion Key'
						,$this->lang->line('level_Action')
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->pollmodel->listpoll_options($poll_id);
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			$rec[] = array(
				$item['optionval']
				,$item['optionkey']
				,'<a href="'.base_url().'poll/opt_delete/'.$item['poll_id'].'/'.$item['option_id'].'"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>'
			);
		}

		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		
		//$data['itemlist'] = $this->groupmodel->getgrouplist($bid,$ofset,$limit);
		
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/Businesslist/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>''
						,'uri_segment'=>3					
				));
					$data['paging'] = '';
		$this->sysconfmodel->data['html']['title'] .= " | Poll Options";
		$data['links']=$data['links']='';
		$formFields1 = array();
		$this->sysconfmodel->data['links'] = '';
		//$fieldset = $this->configmodel->getFields('3');
		$formFields = array();
		$data['form'] = array(
			'open'=>form_open_multipart('Masteradmin/SenderIdList/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function resultpoll($poll_id){
		$get=$this->pollmodel->getpoll_count($poll_id);
		
		$data['values'] = array();
		$data['colours'] = array();
		$data['pie_labels'] = array();
		foreach($get as $rows){
			if($rows['cnt']!=0){
			 array_push($data['values'],$rows['cnt']);
			array_push($data['pie_labels'],$rows['optionval']);
			 array_push($data['colours'],$this->colormodel->getColor());
			}
		}
		include_once( 'system/application/views/open-flash-chart.php' );
		$g = new graph();
		$g->pie(40,'#505050','{font-size: 12px; color: #404040;');
		$g->pie_values($data['values'],$data['pie_labels']);
		
		//$g->pie_values($values,$pie_val);
		$g->pie_slice_colours($data['colours']);
		$g->set_tool_tip( '#x_label#<br>#val# Calls' );
		echo $g->render();
	
	}
	function Result_Poll($poll_id){
		
		$pold=$this->pollmodel->polldetails($poll_id);
		
		if(date('Y-m-d H:i:s')<=$pold->end_date){
			$result="Results will Display After completing the poll";
		}else{
			$get=$this->pollmodel->getpoll_count($poll_id);
			
			$r=array();
			foreach($get as $rows){
				$r[$rows['cnt']]=$rows['optionval'];
			}
			$new=max(array_keys($r));
			$result='The Poll Winner is '. $r[$new];
		}
		$data['form'] = array(
				'poll_id'=>$poll_id,
				'result'=>$result
			);
		$this->sysconfmodel->viewLayout('pollgraph',$data);
		//~ 
	}
	function getReports($poll_id){
		$data['module']['title'] ="Reports";
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '30';
		$pde=$this->pollmodel->polldetails($poll_id);
		($pde->poll_type!=2)?$header = array('Poll Number','OptionVal','Call From','Date and Time','option'):$header = array('Poll Number'
						,'OptionVal'
						,'Call From'
						,'Date and Time'
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->pollmodel->getReport_Poll($poll_id,$ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		 $pollnum='';$okey='';
		foreach ($emp_list['data'] as $item){
				$poll_details=$this->pollmodel->polldetails($item['poll_id']);
				if($poll_details->poll_type!=2){
					$pollnum=$this->pollmodel->getNumber($poll_details->prinumber);
					$okey=$item['optionkey'];
					$rec[] = array($pollnum,$item['optionval'],$item['callfrom'],$item['datetime'],$item['optionkey']);
				}else{
					$pollnum=$this->pollmodel->getNumber($item['optionkey']);
					$okey='';
					$rec[] = array($pollnum,$item['optionval'],$item['callfrom'],$item['datetime']);
				} 	
			
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('poll/getReports/'.$poll_id)
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>4					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | Poll Reports";
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
		$formFields[] = array(
				'label'=>'<label for="f">option: </label>',
				'field'=>form_input(array(
						'name'      => 'option',
						'id'        => 'option',
						'value'     => $this->session->userdata('option'),
						))
						);
		$data['form'] = array(
			'open'=>form_open_multipart('poll/getReports/'.$poll_id,array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
}
/* end of Employee controller */
