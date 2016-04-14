<?php
class CallLogs extends Controller {
	var $data,$roleDetail;
	function CallLogs(){	
		parent::controller();
		if(!$this->session->userdata('logged_in'))redirect('/site/login');
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('systemmodel');
		$this->load->model('groupmodel');
		$this->load->model('configmodel');
		$this->load->model('empmodel');
		$this->load->model('callLogsmodel');
		$this->roleDetail = $this->sysconfmodel->data['roleDetail'];
	}
	function index(){
		$this->callLogsList('act');
	}
	
	function details($cid){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['35']['opt_view']) redirect('Employee/access_denied');
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_CallLogs');
		$data['module']['title'] = $this->lang->line('label_CallLogs');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$fieldset = $this->configmodel->getFields('35',$bid);
		$formFields = array();
		$itemId = $cid;
		$itemDetail = $this->configmodel->getDetail('35',$itemId,'',$bid);
		foreach($fieldset as $field){$checked = false;
			if($field['type']=='s' && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:'');
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked)$formFields[] = array(
								'label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>isset($itemDetail['custom['.$field['fieldid'].']'])?$itemDetail['custom['.$field['fieldid'].']']:"");
			}
		}
		$data['form'] = array('open'=>'<form class="form">','fields'=>$formFields,'close'=>'</form>');
		$this->load->view('active_view',$data);
	}
	
	function callLogsList($type=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;	
		if($this->input->post('download')){
			$filename = $this->reportmodel->incomingcalls_csv($bid);
			$dlink =  "<a href='".$this->config->item('reports_path').$filename.".zip"."' target='_blank' style='color:#fff'>Start Download</a>  ";
		}else{
			$dlink = "";
		}
		if(!$roleDetail['modules']['35']['opt_view']) redirect('Employee/access_denied');
		if($this->input->post('submit')){	
			if($this->session->userdata('search')!=""){
				$s=$this->session->unset_userdata('search');
			}
		}
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '20';
		$data['itemlist'] = $this->callLogsmodel->getcallLogsList($bid,$ofset,$limit,$type);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('callLogs/callLogsList/'.$type.'/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit		
						,'uri_segment'=>4				
				));
		$data['module']['title'] = $this->lang->line('label_CallLogs'). ' ['.$data['itemlist']['count'].']';
		$data['links']= '';
		//$csv_link=($this->uri->segment(3)!='')?$this->uri->segment(3):'all';
		$data['links']=($roleDetail['modules']['6']['opt_download']!=0)?$dlink.'<a href="campaign/campaign_csv/"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Download" class="glyphicon glyphicon-download-alt"></span></a>':'';
		$data['links'] .= '<a href="#" class="blkemail" rel="campaign">&nbsp;<span title="Bulk Mail" class="fa fa-envelope-o"></span>&nbsp;</a>';
		$data['links'] .= '<a href="Report/blksms" class="blkSMs" data-toggle="modal" data-target="#modal-blksms" rel="campaign">&nbsp;<span title="Bulk SMS" class="fa fa-comment"></span>&nbsp;</a>';
		$fieldset = $this->configmodel->getFields('35',$bid);
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && !in_array($field['fieldname'],array('status'))){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>($field['fieldname']=='created_by')
												?form_dropdown('created_by',$this->groupmodel->employee_list(),'',"class='auto'")
												:form_input(array(
													'name'      => $field['fieldname'],
													'id'        => $field['fieldname']
												))
									);
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked)$formFields[] = array(
								'label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>form_input(array(
													'name'      => 'custom['.$field['fieldid'].']'
													)));
			}
		}
		$data['form'] = array(
					'open'=>form_open_multipart(site_url('callLogs/callLogsList/'.$type.'/'),array('name'=>'callLogs','class'=>'form','id'=>'callLogs','method'=>'post')),
					'form_field'=>$formFields,
					'parentids'=>$parentbids,
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close(),
					'title'=>$this->lang->line('level_search')
					);
							
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_CallLogs');
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function callLog_edit($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['35']['opt_add']) redirect('Employee/access_denied');
		if($this->input->post('update_system')){
			$res=$this->callLogsmodel->update_callLog($id,$bid);
			redirect('callLogs');
		}
		//$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";form_dropdown('empid',$this->groupmodel->employee_list(),'',"class='auto'")
		$data['module']['title'] = $this->lang->line('label_CallLogs');
		$fieldset = $this->configmodel->getFields('35');
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('35',$id,'',$bid);
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && !in_array($field['fieldname'],array('status'))){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked && $field['fieldname']!="recorded_file"){
						$cf = array('label'=>'<label>'.(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										).' : </label>',
									'field'=>($field['fieldname']=="created_by")?form_dropdown('created_by',$this->groupmodel->employee_list(),isset($itemDetail['empid'])? $itemDetail['empid']:'',"class='auto'")
												:(($field['fieldname']=="duration" || $field['fieldname']=="call_time" || $field['fieldname']=="call_type" || $field['fieldname']=="call_id" || $field['fieldname']=="number") ? $itemDetail[$field['fieldname']]
												:form_input(array( 
												  'name'      => $field['fieldname'],
												  'id'        => $field['fieldname'],
												  'value'     => isset($itemDetail[$field['fieldname']])? $itemDetail[$field['fieldname']]:''
												))
												)
							);
						array_push($formFields,$cf);
						
				}
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$cf = array('label'=>'<label>'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createField($field,
											isset($itemDetail['custom['.$field['fieldid'].']'])?
											$itemDetail['custom['.$field['fieldid'].']']:'')
						);
					array_push($formFields,$cf);
				}
			}
		}
		$cf = ($roleDetail['role']['accessrecords']=='0') ? array('label'=>'<label>Recorded File : </label>',
								'field'=>($roleDetail['role']['accessrecords']=='0') ? (($itemDetail['recorded_file']!='' && file_exists('sounds/'.$itemDetail['recorded_file']))
					?'<a target="_blank" href="'.site_url('sounds/'.$itemDetail['recorded_file']).'"><span title="Sound" class="fa fa-volume-up"></span></a>'
					:'<span class="glyphicon glyphicon-volume-off"></span> '):""
						):'';
					array_push($formFields,$cf);	
		$data['form'] = array(
		            'form_attr'=>array('action'=>'callLogs/callLog_edit/'.$id,'name'=>'editcallLog'),
					//~ 'open'=>form_open_multipart('callLogs/callLog_edit/'.$id,array('name'=>'editcallLog','id'=>'editcallLog','class'=>'form','method'=>'post')),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function delete_callLog($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['35']['opt_delete']) redirect('Employee/access_denied');
		$this->callLogsmodel->del_callLog($id,$bid,"2");
		return 1;
	}
	function undelete_callLog($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['35']['opt_delete']) redirect('Employee/access_denied');
		$this->callLogsmodel->del_callLog($id,$bid,"1");
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg',"Record restored Successfully");
		redirect('callLogs/callLogsList/del');
	}
	
}
/* end of Campaign Module controller */
