<?php
class Report extends controller
{
	var $data,$roleDetail;
	function Report()
	{
		parent::controller();
		if(!$this->session->userdata('logged_in'))redirect('/site/login');
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('systemmodel');
		$this->load->helper('mcube_helper');
		$this->load->model('groupmodel');
		$this->load->model('leadsmodel');
		$this->load->model('supportmodel');
		$this->load->model('reportmodel');
		$this->load->model('colormodel');
		$this->load->model('auditlog');
		$this->load->model('msgmodel');
		$this->load->library('zip');
		$this->roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
	}
	function __destruct() {
		$this->db->close();
	}
	function index(){
		redirect('TrackReport/all');
	}
	function feature_access(){
		$show=0;
		$checklist=$this->systemmodel->checked_featuremanage();
		if(in_array(1,$checklist)){
			$show=1;
		}
		return $show;
	}
	function undeleteCalls(){
		$roleDetail = $this->roleDetail;
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$heading="Deleted Calls";
		if(!$this->feature_access())redirect('Employee/access_denied');
		if(!$roleDetail['modules']['6']['opt_view']) redirect('Employee/access_denied');
		if($this->input->post('submit')){	
			if($this->session->userdata('search')!=""){
				$s=$this->session->unset_userdata('search');
			}
		}
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$data['itemlist'] = $this->reportmodel->getDeletedlist($bid,$ofset,$limit,$url='');
		$this->pagination->initialize(array(
						 'base_url'=>site_url('DeleteReport/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit		
						,'uri_segment'=>3				
				));
		$data['module']['title'] =$heading . ' ['.$data['itemlist']['count'].']';
		$csv_link=($this->uri->segment(3)!='')?$this->uri->segment(3):'all';
		$links = array();
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$data['links'] = $links;
		$fieldset = $this->configmodel->getFields('6',$bid);
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && !in_array($field['fieldname'],array('calleraddress','callerbusiness','region','rate','exefeedback','custfeedback','dialstatus'))){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) { 
					$formFields[] = array(
									'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>
										($field['fieldname']=='gid')
											?form_dropdown('gid',$this->systemmodel->get_groups(),'',"class='form-control'")
											:(($field['fieldname']=='eid')
												?form_dropdown('empid',$this->groupmodel->employee_list(),'',"class='form-control'")
												:(($field['fieldname']=='pulse')
													?
													form_dropdown('ptype',array(
																				'>'=>' > ',
																				'='=>' = ',
																				'<'=>' < '
																			),'',"style='width:50px;' class='form-control'").' '.
													form_input(array(
													'name'      => $field['fieldname'],
													'class'      => 'form-control',
													'id'        => $field['fieldname']
													),'',"style='width:200px;'")
													:form_input(array(
													'name'      => $field['fieldname'],
													'id'        => $field['fieldname'],
													'class'		=>($field['fieldname']=="starttime" || $field['fieldname']=="endtime")?'datepicker_leads form-control':'form-control'
													))
												)
											)
						);
				}
			}
		}
		$data['form'] = array(
					'open'=>form_open_multipart(site_url('DeleteReport/'),array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
					'form_field'=>$formFields,
					'adv_search'=>array(),
					'save_search'=>'',
					'close'=>form_close(),
					'parentids'=>$parentbids,
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'title'=>$this->lang->line('level_search')
					);	
		$data['paging'] = $this->pagination->create_links();
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('level_report');
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function UnDel($callid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->reportmodel->UnDel($callid,$bid);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg',"Record undeleted Successfully");
		redirect('DeleteReport');
	}
	function bulk_Assign(){
			echo '<div class="modal-dialog modal-lg">
		         <div class="modal-content">
		         	<div class="modal-body">
					<button aria-hidden="true" data-dismiss="modal" class="close" type="button"><i class="fa fa-times"></i></button>
                      <h4>Bulk Call Assign</h4>
		  <form action="Report/assignTO_Emp/" class="form" id="leadsassign" name="leadsassign" method="POST">
                <div class="form-group col-sm-12">
					<label class="col-sm-4 text-right">Employee :</label>
						<!--<input type="text" class="form-control" name="empname" id="empname" />-->
						<input type="hidden" class="form-control" name="ids" id="ids" />
						 <div class="col-sm-6 input-icon right">             
							'.form_dropdown('empname',$this->groupmodel->employee_list(),'','id="empname" class="form-control"').'
					</div>
				</div>
				<div class="form-group text-center">
					<input id="button1" type="submit" class="btn btn-primary blk_submit" name="submit" value='.$this->lang->line('submit').' > 
                    <input id="button2" type="reset" class="btn btn-default" value='.$this->lang->line('reset').' />
                </div>
				</form>
			</div></div></div>
			</div>';
	}
	function Bulk_down($url,$type){
		$t='';	
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;			
		$fieldset = $this->configmodel->getFields('6',$bid);
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$t .= '<input type="checkbox" checked name="lisiting['.$field['fieldname'].']" value="'.(($field['customlabel']!="")?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).'" />'.(($field['customlabel']!="")?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).'<br/>';
				}
			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked)
				$t .= '<input type="checkbox" checked name="lisiting['.$field['fieldKey'].']" value="'.$field['fieldname'].'" />'.$field['customlabel'].'<br/>';
			}
		}
		$t.='<input type="checkbox" name="lisiting[filename]" value="filename" checked />Filename';
		echo '<div class="modal-dialog modal-lg">
		<div class="modal-content">
		<div class="modal-body">
			<div class="row">					
				<button aria-hidden="true" data-dismiss="modal" class="close" type="button"><i class="fa fa-times"></i></button>
				  <h4>Bulk Call Download</h4>
			<form action="'.$url.'/'.$type.'" class="form" id="blk_ddd" name="blk_ddd" method="POST">
					<TABLE>
						<tr>
							<th><label class="col-sm-4 text-right">Fields :</label></th>
							<td><!--<input type="text" name="empname" id="empname" />-->
							 <div class="col-sm-6 input-icon right">
							<input type="hidden" name="call_ids" id="call_ids" />
								'.$t.'
							</div>
							</td>
							<td></td>
						</tr>
					</TABLE>
					<div class="form-group text-center">
						<input id="button1" type="submit" class="btn btn-primary blk_submit" name="blk_down" value='.$this->lang->line('submit').' > 
						<input id="button2" type="reset" class="btn btn-default" value='.$this->lang->line('reset').' />
					 </div>
					</form>
		</div></div></div>
			</div>';
	}
	function assignTO_Emp(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		$result = $this->reportmodel->blk_assignTo();
		if($result == 1){
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg',"Bulk Assign to successfully done ");
			redirect('TrackReport/all');
		}
	}
	
	function call(){
		if(!isset($_POST['module']) || $_POST['module']!='track'){
			$this->session->unset_userdata('Adsearch');
		}
		if(isset($_POST['unique']) && $_POST['unique']=='Set'){
			$uc['call'] = isset($_POST['num']) ? '1' : '0';
			$uc['gid'] = isset($_POST['grp']) ? '1' : '0';
			$uc['eid'] = isset($_POST['emp']) ? '1' : '0';
			$uc['dialstatus'] = isset($_POST['dialstatus']) ? '1' : '0';
			$this->session->set_userdata('UniqueCall',$uc);
		}
		$uc = $this->session->userdata('UniqueCall');
		$nc = (isset($uc['call']) && $uc['call']=='1') ? ' checked="checked" ' : '';
		$gc = (isset($uc['gid']) && $uc['gid']=='1') ? ' checked="checked" ' : '';
		$ec = (isset($uc['eid']) && $uc['eid']=='1') ? ' checked="checked" ' : '';
		$dc = (isset($uc['dialstatus']) && $uc['dialstatus']=='1') ? ' checked="checked" ' : '';
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;	
		if(!$this->feature_access())redirect('Employee/access_denied');
		$url="all";
		if($this->uri->segment(2)=='group'){
			$_POST['submit']= 'submit';
			$_POST['gid']=$this->uri->segment(3);
		}
		elseif($this->uri->segment(2)=='emp'){
			$_POST['submit']= 'submit';
			$_POST['empid']=$this->uri->segment(3);
		}
		elseif($this->uri->segment(1)=='MissedTrackReport'){
			$url="m";
		}
		elseif($this->uri->segment(1)=='QualTrackReport'){
			$url="q";
		}
		elseif($this->uri->segment(1)=='UnQualTrackReport'){
			$url="u";
		}
		elseif($this->uri->segment(1)=='AttTrackReport'){
			$url="at";
		}
		$listingarray=array(
				"m"=>$this->lang->line('level_Report_missed'),
				"at"=>$this->lang->line('level_AttendCalls'),
				"u"=>$this->lang->line('level_Report_unqualified'),
				"q"=>$this->lang->line('level_Report_qualified'),
				"emp"=>$this->lang->line('level_Employeewise'),
				'group'=>$this->lang->line('level_Recent_calls')
			);
		if(!empty($listingarray[$this->uri->segment(3)])){ $heading=$listingarray[$url]; }
		else{ $heading=$this->lang->line('level_Report');}
		$page = $this->uri->segment(1);
		// All/Basic/Contact
		$u3 = ($this->uri->segment(2)!='')?$this->uri->segment(2):'all';
		$dlink = "";
		if($this->input->post('download')){
			$filename = $this->reportmodel->incomingcalls_csv($this->uri->segment(1),$bid);
			$dlink =  "<a href='".$this->config->item('reports_path').$filename.".zip' target='_blank' style='color:#fff'><b>Download</b></a>  ";
		}elseif($this->input->post('blk_down')){
			$filename = $this->reportmodel->blk_down($bid);
			$dlink =  "<a href='".$this->config->item('reports_path').$filename.".zip"."' target='_blank' style='color:#fff'><b>Download</b></a>  ";
		}
		if(!$roleDetail['modules']['6']['opt_view']) redirect('Employee/access_denied');
		if($this->input->post('submit')){	
			if($this->session->userdata('search')!=""){
				$s=$this->session->unset_userdata('search');
			}
		}
		$data['downlink'] = $dlink;
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$data['itemlist'] = $this->reportmodel->getReportlist($bid,$ofset,$limit,$url,$u3);
		$this->pagination->initialize(array(
						 'base_url'=>site_url($this->uri->segment(1).'/'.$u3).'/'
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit		
		));
		$data['module']['title'] =$heading . ' ['.$data['itemlist']['count'].']';
		$csv_link = ($this->uri->segment(2)!='')?$this->uri->segment(2):'all';
		$csv_link1 = ($this->uri->segment(1)!='')?$this->uri->segment(1):'TrackReport';
		$unique = array();
		$unique[] = "<span ><input type='checkbox' name='num' id='num' ".$nc." ><label  style='font-size:13px;color:#FFF;' for='num'>Number&nbsp;</label></span>";
		$unique[] = "<span><input type='checkbox' name='grp' id='grp' ".$gc."><label  style='font-size:13px;color:#FFF;' for='grp'>Group&nbsp;</label></span>";
		$unique[] = "<span><input type='checkbox' name='emp' id='emp' ".$ec."><label style='font-size:13px;color:#FFF;' for='emp'>Employee&nbsp;</label></span>";
		$data['unique']  = $unique;
		$links = array();
		$links[] = ($roleDetail['modules']['6']['opt_delete']) ?'<li><a  class="blkDeletecall" href="Report/bulk_Del" ><span title="Bulk Delete" class="glyphicon glyphicon-trash">&nbsp;Delete</span></a></li>':'';
		$links[] = '<li><a href="#" class="blkemail" rel="calls"><span title="Bulk Mail" class="glyphicon glyphicon-envelope">&nbsp;Email</span></a></li>';
		$links[] = ($roleDetail['modules']['6']['opt_add']) ?'<li><a href="Report/bulk_Assign" class="lead_owner" data-toggle="modal" data-target="#modal-leadowner"><span title="Bulk Assign" class="glyphicon glyphicon-share">&nbsp;Assign</span></a></li>':'';
		$links[] = '<li><a href="Report/blksms" class="blkSMs" data-toggle="modal" data-target="#modal-blksms" rel="calltrack"><span title="Bulk SMS" class="glyphicon glyphicon-comment">&nbsp;SMS</span></a></li>';
		$links[] = '<li  class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search">&nbsp;Search</span></a></li>';
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-advsearch" data-toggle="modal" data-target="#modal-advsearch" ><span title="Search" class="glyphicon glyphicon-zoom-in">&nbsp;Advance Search</span></a></li>';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = ($roleDetail['modules']['6']['opt_download']!=0) ? '<li><a href="Report/Bulk_down/'.$csv_link1.'/'.$csv_link.'" class="blk_calls" data-toggle="modal" data-target="#modal-pop"><span title="Download" class="glyphicon glyphicon-arrow-down">&nbsp;Download Select</span></a></li>':'';
		$links[] = ($roleDetail['modules']['6']['opt_download']!=0) ? '<li><a href="Report/incomingcalls_csv/'.$csv_link1.'/'.$csv_link.'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Download" class="glyphicon glyphicon-download-alt">&nbsp;Download All</span></a></li>':'';
		$fieldset = $this->configmodel->getFields('6',$bid);
		$formFields = array();
		$advsearch = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && !in_array($field['fieldname'],array(
																						'calleraddress',
																						'callerbusiness',
																						'region',
																						'rate',
																						'exefeedback',
																						'custfeedback'))){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) { $formFields[] = array(
									'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>
										($field['fieldname']=='gid')
											?form_dropdown('gid',$this->systemmodel->get_groups(),'',"class='form-control'")
											:(($field['fieldname']=='eid')
												?form_dropdown('empid',$this->groupmodel->employee_list(),'',"class='form-control'")
												:(($field['fieldname']=='pulse')
													?
													form_dropdown('ptype',array(
																				'>'=>' > ',
																				'='=>' = ',
																				'<'=>' < '
																			),'',"style='width:60px;color:#000;' class='form-control'").' '.
													form_input(array(
													'name'      => $field['fieldname'],
													'class'     => 'form-control',
													'id'        => $field['fieldname']
													),'',"style='width:79.5%;'")
													:form_input(array(
													'name'      => $field['fieldname'],
													'id'        => $field['fieldname'],
													'class'		=>($field['fieldname']=="starttime" || $field['fieldname']=="endtime")?'datepicker_leads form-control':'form-control'
													))
												)
											)
										);
								$advsearch[$field['fieldname']]=(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']);		
							}			
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$formFields[] = array(
						'label'=>'<label class=" col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
						'field'=>$this->configmodel->createFieldAdvance($field,'','search',"class='form-control'"));
					$advsearch[$field['fieldKey']]=$field['customlabel'];
				}					
			}
		}
		unset($advsearch['gid']);
		unset($advsearch['eid']);
		$save_cnt=save_search_count($bid,'6',$this->session->userdata('eid'));	
		$search_names=get_save_searchnames($bid,'6',$this->session->userdata('eid'));
		$data['form'] = array(
						'open'=>form_open_multipart(site_url($page.'/'.$u3.'/')
						,array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')
						,array('module'=>'track')),
					'form_field'=>$formFields,
					'adv_search'=>$advsearch,
					'search_names'=>$search_names,
					'search_url'=>$page.'/',
					'groups'=>$this->systemmodel->get_groups(),
					'employees'=>$this->groupmodel->employee_list(),
					'save_search'=>$save_cnt,
					'parentids'=>$parentbids,
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close(),
					'title'=>$this->lang->line('level_search')
					);
		$data['tab'] = true;	
		$data['tab1'] = true;	
		$data['paging'] = $this->pagination->create_links();
		$data['links'] = $links;
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('level_report');
		if(isset($_POST['search'])){
			if($_POST['search'] == 'search'){
				$this->load->view('search_view',$data);
				return true;
			}
			if($_POST['search'] == 'advsearch'){
				$this->load->view('advsearch_view',$data);
				return true;
			}
		}
		$this->sysconfmodel->viewLayout('list_view',$data);
	}

	
	function callarchive($y='0',$m='0'){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['6']['opt_view']) redirect('Employee/access_denied');
		if($this->input->post('submit')){	
			if($this->session->userdata('search')!=""){
				$s=$this->session->unset_userdata('search');
			}
		}
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$data['module']['title'] ='Call Archive';
		$ofset = ($this->uri->segment(5)!=null)?$this->uri->segment(5):0;
		$limit = '30';
		$data['itemlist'] = $this->reportmodel->getArchivelist(
												array('bid'		=> $bid,
													  'ofset'	=> $ofset,
													  'limit'	=> $limit,
													  'year'	=> $y,
													  'month'	=> $m)
												);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('CallArchive/'.$y.'/'.$m)
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit		
						,'uri_segment'=>5				
				));
		$data['links']='';
		$data['nosearch']='1';
	    $data['nobulk']=true;	
		$formFields = array();
		$data['form'] = array(
					'open'=>form_open(site_url('CallArchive/'.$y.'/'.$m),array('name'=>'callarchive','class'=>'form','id'=>'callarchive','method'=>'post')),
					'form_field'=>$formFields,
					'adv_search'=>array(),
					'close'=>form_close(),
					'parentids'=>$parentbids,
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'title'=>$this->lang->line('level_search')
					);
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('level_report');
		$this->sysconfmodel->viewLayout('list_view',$data);
	}

	function edit($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['6']['opt_add']) redirect('Employee/access_denied');
		if($this->input->post('update_system')){
			$res=$this->reportmodel->update_caller_details($id,$bid);
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
				$this->session->set_flashdata('msg', "Call Updated Successfully");
			}
			redirect($this->session->userdata('refurl'));
		}
		if(isset($_SERVER['HTTP_REFERER']))$this->session->set_userdata(array('refurl'=>$_SERVER['HTTP_REFERER']));
		$data['module']['title'] = $this->lang->line('level_Report');
		$fieldset = $this->configmodel->getFields('6');
		$formFields = array();
		$itemDetail = array();
		$itemDetail = $this->configmodel->getDetail('6',$id,'',$bid);
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){
						$cf = array('label'=>'<label class="col-sm-4 text-right">'.(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										).' : </label>',
									'field'=>($field['fieldname']!="callername" 
												&& $field['fieldname']!="callerbusiness" 
												&& $field['fieldname']!="assignto" 
												&& $field['fieldname']!="calleraddress" 
												&& $field['fieldname']!="caller_email" 
												&& $field['fieldname']!="remark"&& $field['fieldname']!="refid")?(isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:''):(($field['fieldname']=="calleraddress" || $field['fieldname']=="remark")?form_textarea(array(
												  'name'      => $field['fieldname'],
												  'id'        => $field['fieldname'],
												   'class'        => 'form-control',
												   'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:''
										)):(($field['fieldname']=="assignto")?form_dropdown('assignto',$this->groupmodel->employee_list(),$itemDetail['asto'],"id='assignto'  class='form-control'"):form_input(array(
												  'name'      => $field['fieldname'],
												  'id'        => $field['fieldname'],
												  'class'        => 'form-control',
												  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:''
										))))
							);
						array_push($formFields,$cf);
				}
			}elseif($field['type']=='c' && $field['show']){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked)
						$formFields[] = array(
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
		$cf = array('label'=>'<label class="col-sm-4 text-right" id="grLabel" style="display:none;">Lead Group :</label>	'
					,'field'=>form_dropdown('lgid',$this->leadsmodel->getGroups(),'',"id='grempId' class ='form-control' style='display:none;'")
					,"style"=>"none");
		array_push($formFields,$cf);
		$cf=array('label'=>'<label class="col-sm-4 text-right" id="assignLabel" style="display:none;">Lead Assignto :</label>	'
					,'field'=>form_dropdown('lassignto',$this->groupmodel->employee_list(),'',"id='assignemp'  class ='form-control' style='display:none;' ")	
				 ,"style"=>"none");
					array_push($formFields,$cf);
		$arr=array("0"=>"select","1"=>"Email Alert","2"=>"SMS Alert","3"=>"Both");
		$cf = array('label'=>'<label class="col-sm-4 text-right" id="alertLabel" style="display:none;">Alert Type :</label>	'
					,"field"=>form_dropdown("lalerttype",$arr,'',"id='alerttype'  class ='form-control' style='display:none;'")
					,"style"=>"none");
					array_push($formFields,$cf);
		$sup_access = $this->sysconfmodel->getfeatureAccess('14');
		if($sup_access == 1){
			$disabled = ($itemDetail['suptkt']==null || $itemDetail['tktid'] == 0)  ? '1' : '0';
			if($disabled == 1){
				$cf=array('label'=>'<label  class="col-sm-4 text-right">Convert to Support Ticket :</label>	'
						,'field'=>form_checkbox(array("name"=>"convertsuptkt","id"=>"convertsuptkt","value"=>"1")));
				array_push($formFields,$cf);
			}else{
				$cf=array('label'=>'<label class="col-sm-4 text-right">Update Support Ticket :</label>	'
						,'field'=>form_checkbox(array("name"=>"updatesuptkt","id"=>"updatesuptkt","value"=>"1")));
				array_push($formFields,$cf);
			}
		}
		$cf=array('label'=>'<label class="col-sm-4 text-right" id="supgrLabel" style="display:none;">Support Group :</label>	'
					,'field'=>form_dropdown('sgid',$this->supportmodel->getSupportGrps(),'','id="supgrId"  class ="form-control" style="display:none;"')
					,"style"=>"none");
					array_push($formFields,$cf);
		$cf=array('label'=>'<label class="col-sm-4 text-right" id="supassignLabel" style="display:none;">Ticket Assignto :</label>	'
					,'field'=>form_dropdown('sassignto',$this->supportmodel->getEmployees(),'',"id='supEmpid'  class ='form-control' style='display:none;'"),"style"=>"none");
					array_push($formFields,$cf);
		$cf=array('label'=>'<label  class="col-sm-4 text-right" id="suplevelLabel" style="display:none;">Ticket Level :</label>	'
					,'field'=>form_dropdown('tkt_level',$this->supportmodel->getSupTktLevel(),'',"id='tkt_level'   class ='form-control' style='display:none;'"),"style"=>"none");
					array_push($formFields,$cf);
		$escProcess = $this->systemmodel->getSupEscBusiness();
		if($escProcess == 1){
			$cf=array('label'=>'<label class="col-sm-4 text-right" id="suptimeLabel" style="display:none;">Ticket Escalation Time :</label>	'
						,'field'=>form_input(array(	  'name'      => 'tkt_esc_time',
													  'id'        => 'tkt_esc_time',
													  'value'     => '',
													  'class'     => 'form-control',
													  'style'	  => 'display:none;'
											))
						,"style"=>"none");
			array_push($formFields,$cf);
		}
		$arr=array("0"=>"Select","1"=>"Email Alert","2"=>"SMS Alert","3"=>"Both");
		$cf = array('label'=>'<label class="col-sm-4 text-right" id="supalertLabel" style="display:none;">Alert Type :</label>	'
					,"field"=>form_dropdown("salerttype",$arr,'',"id='supalerttype' class ='form-control' style='display:none;'")
					,"style"=>"none");
		array_push($formFields,$cf);
		/*  End of Conversion */
		$cf = ($roleDetail['role']['accessrecords']=='0') ? array('label'=>'<label class="col-sm-4 text-right">Recorded File : </label>',
								'field'=>($roleDetail['role']['accessrecords']=='0') ? (($itemDetail['filename']!='' && file_exists('sounds/'.$itemDetail['filename']))
					?'<a target="_blank" href="'.site_url('sounds/'.$itemDetail['filename']).'"><span title="Sound" class="fa fa-volume-up"></span></a>'
					:'<span class="glyphicon glyphicon-volume-off"></span>'):""
						):'';
		array_push($formFields,$cf);
		$cf = array('label'=>'<label class="col-sm-4 text-right">Click To Connect : </label>',
					'field'=>anchor("Report/clicktoconnect/".$itemDetail['callid']."/1", '<span title="click To Connect" class="fa fa-phone"></span>',array('class'=>'clickToConnect'))
					);
		array_push($formFields,$cf);
		$data['form'] = array(
		            'form_attr'=>array('action'=>'Report/edit/'.$id,'name'=>'editreport','id'=>'editreport','enctype'=>"multipart/form-data"),
					'hidden' =>array('gid'=>$itemDetail['grid'],'number'=>$itemDetail['callfrom']),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	
	function activerecords($id=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['6']['opt_add']) redirect('Employee/access_denied');
		$data['module']['title'] = $this->lang->line('level_Report');
		$fieldset = $this->configmodel->getFields('6',$bid);
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('6',$id,'',$bid);
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){
						$cf = array('label'=>'<label class="col-sm-4 text-right">'.(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										).' : </label>',
									'field'=>$itemDetail[$field['fieldname']]
							);
						array_push($formFields,$cf);
				}
			}elseif($field['type']=='c' && $field['show']){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked)$formFields[] = array(
						'label'=>'<label  class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
						'field'=>isset($itemDetail[$field['fieldKey']]) ? $itemDetail[$field['fieldKey']] : '','');
			}
		}
		$data['form'] = array(
		'open'=>form_open_multipart('Report/edit/'.$id
									,array('name'=>'editreport','id'=>'editreport','class'=>'form','method'=>'post')
									),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$fdata['module']['title'] = "Followups";
		$fdata['links'] = '';
		$fdata['nosearch']=true;
		$fdata['paging'] = '';
		$fdata['itemlist'] = $this->reportmodel->getFollowuplist($id,$bid);
		if(!empty($fdata['itemlist']['rec'])){
			$data['followups'] = $fdata;
		}
		$this->load->view('active_view',$data);
		//$this->load->view('list_view',$fdata);
	}
	function Delete_call($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['6']['opt_delete']) redirect('Employee/access_denied');
		$this->reportmodel->delete_call($id,$bid);
		return 1;
	}
	function Updatecaller(){
		$callist=$this->reportmodel->get_updatecaller();
		$data=array(
		'call_list'=>$callist
		);
		$this->sysconfmodel->viewLayout('UpdateReport',$data);
	}
	function Delete_callupte($id){		
		$re=$this->reportmodel->delete_callupdate($id);
		echo  $re;
	}
	function exportdata(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$csv_output='';
		$res=$this->reportmodel->exportdata($bid);
		$arrays=array($this->lang->line('g_groupname'),$this->lang->line('e_employeename'),
					  $this->lang->line('level_callfrom'),$this->lang->line('level_starttime'),
					  $this->lang->line('level_endtime'));
		$array1=array('groupname','empname','callfrom','starttime','endtime');					  
			for($i=0;$i<sizeof($arrays);$i++){
				$csv_output .='"'.trim($arrays[$i]).'",';
			}				
			$csv_output .= "\n";
		for($j=0;$j<sizeof($res);$j++){
			for($k=0;$k<sizeof($array1);$k++)
			{
				$csv_output .='"'.$res[$j][$array1[$k]].'",';
			}
			$csv_output .= "\n";
		}	
		$csv_output .= "\n";	
		$filename = "Export_".date("Y-m-d_H-i",time());
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header( "Content-disposition: filename=".$filename.".csv");
		print $csv_output;
		exit;
	}
	function calldetail($id=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data = $this->data;
		$data['module']['title'] = $this->lang->line('level_Report');
		$data['links'] = '';
		$data['nosearch']=true;
		$data['paging'] = '';
		$data['title'] = 'Counter report';
		$formFields=array();
		$data['itemlist'] = $this->reportmodel->getReportlist1($id,$bid);
		$this->load->view('counter_view',$data);
	}
	function callback($id=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data = $this->data;
		$data['module']['title'] = $this->lang->line('level_Report');
		$data['links'] = '';
		$data['nosearch']=true;
		$data['paging'] = '';
		$data['title'] = 'Callback report';
		$formFields=array();
		$data['itemlist'] = $this->reportmodel->getCallbackList($id,$bid);
		$this->load->view('counter_view',$data);
	}
	function empdetail($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data['module']['title'] = "Employees";
		$data['links'] = '';
		$data['nosearch']=true;
		$data['paging'] = '';
		$data['itemlist'] = $this->reportmodel->getReportlist2($id,$bid);
		$data['form'] = array('adv_search'=>array());
		$this->load->view('popupListView',$data);
	}
	function showmap($lat='',$long=''){
		echo '<!DOCTYPE html>  
		<html>  
		<head>  
		<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />  
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false">  
		</script>  
		<script type="text/javascript">  
		  function initialize() {  
			var latlng = new google.maps.LatLng('.$lat.','.$long.');  
			var myOptions = {  
			  zoom: 17,  
			  center: latlng,  
			  mapTypeId: google.maps.MapTypeId.ROADMAP  
			};  
			  
			var map = new google.maps.Map(document.getElementById("map_canvas"),  
				myOptions);  
		  
			// Creating a marker and positioning it on the map    
			var marker = new google.maps.Marker({    
			  position: new google.maps.LatLng('.$lat.','.$long.'),    
			  map: map    
			});  
		  }  
		</script>  
		</head>  
		<body onload="initialize()">  
		  <div id="map_canvas" style="width:100%; height:100%"></div>  
		</body>  
		</html> ';
	}
	function followup($id='',$dsh = '',$type=''){
		if(isset($_POST['callid'])){
			if (new DateTime() < new DateTime($_POST['followupdate'])) {
				$this->reportmodel->addFollowup($_POST);
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg', "Followups added Successfully");
				redirect($_SERVER['HTTP_REFERER']);
			}else{
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', "Error While adding followup. Please select Future Date");
				redirect($_SERVER['HTTP_REFERER']);
			}
		}
		$ftype = $type;
		$cbid=$this->session->userdata('cbid');
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));	
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$fdata['module']['title'] = "Followups";
		$data['module']['title'] = "Followups";
		$fdata['links'] = '';
		$fdata['nosearch']=true;
		$fdata['paging'] = '';
		$fdata['form']=array('adv_search'=>array());
		$fdata['itemlist'] = $this->reportmodel->getFollowuplist($id,$bid,$dsh);
		if($dsh == 1){
			$this->load->view('popupListView',$fdata);
		}elseif($dsh != 1){
			$fieldset = $this->configmodel->getFields('29',$bid);
			$formFields = array();
			foreach($fieldset as $field){
				$checked = false;
				if($field['type']=='s' && $field['show'] && $field['fieldname']!='eid'){
					foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked) 
						$formFields[] = array(
										'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'" class="col-sm-4 text-right">'.(($field['customlabel']!="")
												 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' <img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp: </label>',
										'field'=>($field['fieldname']=="comment")?form_textarea(array(
													'name'      => 'comment',
													'id'        => 'comment',
													'class'     => 'form-control'))
													:form_input(array(
														'name'      => $field['fieldname'],
														'id'        => $field['fieldname'],
														'value'		=> date('Y-m-d H:i:s'),
                  								    	'class'		=>($field['fieldname']=="followupdate") ? 'datetimepicker form-control':'form-control'

														))
												);
												
				}elseif($field['type']=='c' && $field['show']){
						foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
						if($checked)$formFields[] = array(
								'label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createFieldAdvance($field,isset($itemDetail[$field['fieldKey']]) ? $itemDetail[$field['fieldKey']] : '',''));
				}
			}	
			$array=array("0"=>"Select","1"=>"Email Alert","2"=>"SMS Alert","3"=>"Both");
			$formFields[] =array("label"=>"<label for='alert' class='col-sm-4 text-right'>Alert :</label>"
								,"field"=>form_dropdown("alert",$array,'',"class='form-control'")
								);
			$formFields[] =array("label"=>"<label for='alert' class='col-sm-4 text-right' >Notification Time :<img title='Time Limit of reaching SMS alert' src='system/application/img/icons/help.png' /></label>"
								,"field"=>form_input(array(
													'name'      => 'notify_time',
													'id'        => 'notify_time',
													'value'		=> '5',
													'class'		=> 'form-control'
													))." Mins (Previous)"
								);									
			$data['form'] = array(
					   'form_attr'=>array('action'=>'Report/followup/','name'=>'followup'),
						'hidden' => array('bid'=>$bid,'callid'=>$id,'type'=>$ftype),
						'fields'=>$formFields,
						'parentids'=>'',
						'busid'=>$bid,
						'pid'=>$this->session->userdata('pid'),
						'close'=>form_close()
					);
			if(!empty($fdata['itemlist']['rec'])){
				$data['followups'] = $fdata;
			}
			$this->load->view('popupFormView',$data);
	    }
	}
	function missed(){
		$date=date('Y-m-d',strtotime('-7 days'));
		if(!$this->feature_access())redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['6']['opt_view']) redirect('Employee/access_denied');
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('level_Report_missed');
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$data['itemlist'] = $this->reportmodel->getReportlist($bid,$ofset,$limit,'m');
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Report/missed')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
				$links = array();
				$links[]='<li><a href="Report/incomingcalls_csv/m" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Download" class="glyphicon glyphicon-download-alt">Download All</span></a></li>';
				$fieldset = $this->configmodel->getFields('6');
				$formFields = array();
				foreach($fieldset as $field){
					$checked = false;
					if($field['type']=='s' && $field['show']){
						foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
						if($checked) $formFields[] = array(
											'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
													 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
											'field'=>($field['fieldname']!='eid'&& $field['fieldname']!='gid')
													?form_input(array(
															'name'      => $field['fieldname'],
															'id'        => $field['fieldname'],
															'class'		=>($field['fieldname']=="starttime" ||$field['fieldname']=="endtime")?'datepicker_leads':''))
													:(($field['fieldname']=='gid')?
															form_dropdown('gid',$this->systemmodel->get_groups(),'',"class='auto'"):
																							
														form_dropdown('empid',$this->groupmodel->employee_list(),'',"class='auto'"))
													);
					}
				}
				$data['links'] = $links;
				$data['form'] = array(
							'open'=>form_open_multipart('Report/missed/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('level_report');
		$this->sysconfmodel->viewLayout('list_view',$data);
	}

	function AttendCalls(){
		$date=date('Y-m-d',strtotime('-7 days'));
		if(!$this->feature_access())redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['6']['opt_view']) redirect('Employee/access_denied');
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('level_AttendCalls');
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$data['itemlist'] = $this->reportmodel->getReportlist($bid,$ofset,$limit,'at');
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Report/missed')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
				$links = array();
				$links[]='<li><a href="Report/incomingcalls_csv/at" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Download" class="glyphicon glyphicon-download-alt">Download All</span></a></li>';
				$fieldset = $this->configmodel->getFields('6');
				$formFields = array();
				foreach($fieldset as $field){
					$checked = false;
					if($field['type']=='s' && $field['show']){
						foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
						if($checked) $formFields[] = array(
											'label'=>'<label  class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
													 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
											'field'=>($field['fieldname']!='eid'&& $field['fieldname']!='gid')
													?form_input(array(
															'name'      => $field['fieldname'],
															'id'        => $field['fieldname'],
															'class'		=>($field['fieldname']=="starttime" ||$field['fieldname']=="endtime")?'datepicker_leads':''))
													:(($field['fieldname']=='gid')?
															form_dropdown('gid',$this->systemmodel->get_groups(),'',"class='auto'"):
																							
														form_dropdown('empid',$this->groupmodel->employee_list(),'',"class='auto'"))
													);
					}
				}
				$data['links'] = $links;
				$data['form'] = array(
							'open'=>form_open_multipart('Report/missed/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('level_report');
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function EmployeeCallReport($eid){
		$_POST= array('submit'=>'submit','empid'=>$eid);
		$date=date('Y-m-d',strtotime('-7 days'));
		if(!$this->feature_access())redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['6']['opt_view']) redirect('Employee/access_denied');
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('level_Employeewise');
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$data['itemlist'] = $this->reportmodel->getReportlist($bid,$ofset,$limit,'a');
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Report/missed')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
		));
		$links = array();
		$links[]='<li><a href="Report/incomingcalls_csv/a/'.$eid.'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Download" class="glyphicon glyphicon-download-alt">Download All</span></a></li>';
		$fieldset = $this->configmodel->getFields('6');
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
									'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>($field['fieldname']!='eid'&& $field['fieldname']!='gid')
											?form_input(array(
													'name'      => $field['fieldname'],
													'id'        => $field['fieldname'],
													'class'		=>($field['fieldname']=="starttime" ||$field['fieldname']=="endtime")?'datepicker_leads':''))
											:(($field['fieldname']=='gid')?
													form_dropdown('gid',$this->systemmodel->get_groups(),'',"class='auto'"):
																					
												form_dropdown('empid',$this->groupmodel->employee_list(),'',"class='auto'"))
											);
			}
		}	
		$data['links'] = $links;
		$data['form'] = array(
					'open'=>form_open_multipart('Report/missed/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
					'form_field'=>$formFields,
					'close'=>form_close(),
					'title'=>$this->lang->line('level_search')
					);
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('level_report');
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function unqualified(){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['6']['opt_view']) redirect('Employee/access_denied');
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('level_Report_unqualified');
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$data['itemlist'] = $this->reportmodel->getReportlist($bid,$ofset,$limit,'u');
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Report/unqualified')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
		));
		$links = array();
		$links[]='<li><a href="Report/incomingcalls_csv/u" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Download" class="glyphicon glyphicon-download-alt">Download All</span></a></li>';
		$fieldset = $this->configmodel->getFields('6');
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
									'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>($field['fieldname']!='eid'&& $field['fieldname']!='gid')
											?form_input(array(
													'name'      => $field['fieldname'],
													'id'        => $field['fieldname'],
													'class'		=>($field['fieldname']=="starttime" ||$field['fieldname']=="endtime")?'datepicker_leads':''))
											:(($field['fieldname']=='gid')?
													form_dropdown('gid',$this->systemmodel->get_groups(),'',"class='auto'"):
																					
												form_dropdown('empid',$this->groupmodel->employee_list(),'',"class='auto'"))
											);
			}
		}
		$data['links'] = $links;
		$data['form'] = array(
					'open'=>form_open_multipart('Report/unqualified/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
					'form_field'=>$formFields,
					'close'=>form_close(),
					'title'=>$this->lang->line('level_search')
					);
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('level_report');
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function qualified(){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['6']['opt_view']) redirect('Employee/access_denied');
		if($this->input->post('submit')){	
			
		}
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('level_Report_qualified');
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$data['itemlist'] = $this->reportmodel->getReportlist($bid,$ofset,$limit,'q');
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Report/qualified')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
				$links =array();
				$links[]='<li><a href="Report/incomingcalls_csv/q" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Download" class="glyphicon glyphicon-download-alt">Download All</span> </a></li>';
				$fieldset = $this->configmodel->getFields('6');
				$formFields = array();
				foreach($fieldset as $field){
					$checked = false;
					if($field['type']=='s' && $field['show']){
						foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
						if($checked) $formFields[] = array(
											'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
													 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
											'field'=>($field['fieldname']!='eid'&& $field['fieldname']!='gid')
													?form_input(array(
															'name'      => $field['fieldname'],
															'id'        => $field['fieldname'],
															'class'		=>($field['fieldname']=="starttime" ||$field['fieldname']=="endtime")?'datepicker_leads':''))
													:(($field['fieldname']=='gid')?
															form_dropdown('gid',$this->systemmodel->get_groups(),'',"class='auto'"):
																							
														form_dropdown('empid',$this->groupmodel->employee_list(),'',"class='auto'"))
													);
					}
				}
				$data['links'] = $links;
				$data['form'] = array(
							'open'=>form_open_multipart('Report/qualified/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('level_report');
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function smsreport(){
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('#'
						,$this->lang->line('level_number')
						,$this->lang->line('level_content')
						,'Source'
						,'DND Status'
						,$this->lang->line('level_datetime')
						,'Send by'
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->reportmodel->getSmsreport($ofset,$limit);
		$data['module']['title'] = $this->lang->line('level_smsreport').'['.$emp_list['count'].']';
		$rec = array();
		if(count($emp_list['data'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;
		foreach ($emp_list['data'] as $item){
			$rec[] = array($i
						   ,$item['number']	
						   ,$item['content']	
						   ,$item['source']	
						   ,($item['dnd_status']!=1)?'False':'True'	
						   ,$item['datetime']	
						   ,$item['empname']
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Report/smsreport/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('level_smsreport');
		$links = array();
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search"class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$formFields = array();
		$advsearch = array();
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">'.$this->lang->line('level_number').' : </label>',
				'field'=>form_input(array(
						'name'      => 'number',
						'class'     => 'form-control',
						'id'        => 'number',
						'value'     => $this->session->userdata('number')
						))
						);
		$advsearch['number'] ="Number";				
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">'.$this->lang->line('level_datefrom').' : </label>',
				'field'=>form_input(array(
						'name'      => 'datefrom',
						'id'        => 'datefrom',
						'class'		=>'datepicker_leads form-control',
						'value'     => $this->session->userdata('datefrom')
						))
						);
			$advsearch['datefrom'] ="Date from";			
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">'.$this->lang->line('level_dateto').' : </label>',
				'field'=>form_input(array(
						'name'      => 'dateto',
						'id'        => 'dateto',
						'class'		=>'datepicker_leads form-control',
						'value'     =>($this->session->userdata('dateto')!="")?$this->session->userdata('dateto'):date('Y-m-d'),
						))
						);
			$advsearch['dateto'] ="Date to";	
		$data['links']= $links;				
		$data['form'] = array(
			'open'=>form_open_multipart('Report/smsreport/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
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
	function vbroadcast(){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['8']['opt_view']) redirect('Employee/access_denied');
		$data['module']['title'] = $this->lang->line('level_voicereport');
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('#'
						,$this->lang->line('level_title')
						,$this->lang->line('level_sheduletime')
						,$this->lang->line('level_count')
						,$this->lang->line('level_response')
						
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->reportmodel->getBVoicereport($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;
		foreach ($emp_list['data'] as $item){
			$rec[] = array($i
							,$item['title']	
						   ,$item['scheduleat']	
						   ,$item['count']	
						   ,"<a href='Report/Voicebroadreport' class='btn-danger' data-toggle='modal' data-target='#modal-responsive'>".$item['rep']."</a>"	
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Report/Voicebroadreport/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>4					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('level_voicereport');
		$links = array();
		$links[]='<li><a href="Report/voice_csv" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Download" class="glyphicon glyphicon-download-alt">Download All</span></a></li>';
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">'.$this->lang->line('level_number').' : </label>',
				'field'=>form_input(array(
						'name'      => 'vnumber',
						'id'        => 'vnumber',
						'value'     => $this->session->userdata('vnumber')
						))
						);
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">'.$this->lang->line('level_sheduletime').'From : </label>',
				'field'=>form_input(array(
						'name'      => 'sdatef',
						'id'        => 'sdatef',
						'class'		=>'datepicker_leads',
						'value'     => $this->session->userdata('sdatef')
						))
						);
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">'.$this->lang->line('level_sheduletime').'To : </label>',
				'field'=>form_input(array(
						'name'      => 'sdatet',
						'id'        => 'sdatet',
						'class'		=>'datepicker_leads',
						'value'     =>($this->session->userdata('sdatet')!="")?$this->session->userdata('sdatet'):date('Y-m-d'),
						))
						);
		$data['links'] = $links;
		$data['form'] = array(
			'open'=>form_open_multipart('Report/vbroadcast/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		$this->sysconfmodel->viewLayout('list_view',$data);
		
		
	}
	function Voicebroadreport(){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['8']['opt_view']) redirect('Employee/access_denied');
		$data['module']['title'] = $this->lang->line('level_voicereport');
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('#'
						,$this->lang->line('level_number')
						,$this->lang->line('level_sheduletime')
						,$this->lang->line('level_starttime')
						,$this->lang->line('level_endtime')
						,$this->lang->line('level_dtmf')
						,$this->lang->line('level_title')
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->reportmodel->getVoicereport($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;
		foreach ($emp_list['data'] as $item){
			$rec[] = array($i
						   ,$item['number']	
						   ,$item['scheduletime']	
						   ,$item['starttime']	
						   ,$item['endtime']	
						   ,$item['dtmf']	
						   ,$item['title']	
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Report/Voicebroadreport/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>4					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('level_voicereport');
		$links = array();
		$links[]='<li><a href="Report/voice_csv" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Download" class="glyphicon glyphicon-download-alt">Download All</span></a></li>';
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">'.$this->lang->line('level_number').' : </label>',
				'field'=>form_input(array(
						'name'      => 'vnumber',
						'id'        => 'vnumber',
						'value'     => $this->session->userdata('vnumber')
						))
						);
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">'.$this->lang->line('level_sheduletime').'From : </label>',
				'field'=>form_input(array(
						'name'      => 'sdatef',
						'id'        => 'sdatef',
						'class'		=>'datepicker_leads',
						'value'     => $this->session->userdata('sdatef')
						))
						);
		$formFields[] = array(
				'label'=>'<label class="col-sm-4 text-right" for="f">'.$this->lang->line('level_sheduletime').'To : </label>',
				'field'=>form_input(array(
						'name'      => 'sdatet',
						'id'        => 'sdatet',
						'class'		=>'datepicker_leads',
						'value'     =>($this->session->userdata('sdatet')!="")?$this->session->userdata('sdatet'):date('Y-m-d'),
						))
						);
		$data['links'] = $links;
		$data['form'] = array(
			'open'=>form_open_multipart('Report/Voicebroadreport/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		$this->load->view('list_view',$data);
	}
		
		
		
		
	function sms_csv(){
		if($this->input->post('submit')){
				$res=$this->reportmodel->get_smsreport();
				//print_r($res);exit;
				$csv_output='';
				$arraykeys=array_keys($_POST['lisiting']);
				for($i=0;$i<sizeof($arraykeys);$i++){
					$arrays[]=$arraykeys[$i];
				}
				for($i=0;$i<sizeof($arrays);$i++){
					$csv_output .='"'.trim($arrays[$i]).'",';
				}				
				$csv_output .= "\n";
				$rec=array();
				for($j=0;$j<sizeof($res);$j++){
					for($k=0;$k<sizeof($arrays);$k++){
						$csv_output .='"'.$res[$j][$arrays[$k]].'",';
					}
					$csv_output .= "\n";
				}$csv_output .= "\n";
				$this->print_csv($csv_output);
		}
		$data=array('senderlist'=>$this->msgmodel->getSenderidList());
		$this->load->view('smsreport',$data);
	}
	function voice_csv(){
		if($this->input->post('submit')){				
				$res=$this->reportmodel->get_voicereport();
				//print_r($res);exit;
				$csv_output='';
				$arraykeys=array_keys($_POST['lisiting']);
				for($i=0;$i<sizeof($arraykeys);$i++){
					$arrays[]=$arraykeys[$i];
				}
				for($i=0;$i<sizeof($arrays);$i++){
					$csv_output .='"'.trim($arrays[$i]).'",';
				}				
				$csv_output .= "\n";
				$rec=array();
				for($j=0;$j<sizeof($res);$j++){
					for($k=0;$k<sizeof($arrays);$k++){
						$csv_output .='"'.$res[$j][$arrays[$k]].'",';
					}
					$csv_output .= "\n";
				}$csv_output .= "\n";
				$this->print_csv($csv_output);
		}
		$data=array();
		$this->load->view('voicereport',$data);
	}
	
	function incomingcalls_csv($type='',$eid=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data=array('systemfields'=>$this->configmodel->getFields('6',$bid),
					'roleDetail'=>$this->roleDetail,
					 'type'=>$type,
					 'bid'=>$bid,
					 'eid'=>($eid!="")?$eid:'');
		$this->load->view('incomingcalls_csv',$data);
	}
		
	function autodial(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['3']['opt_view']) redirect('Employee/access_denied');
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('label_autodialreport');
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_autodialreport');
		$this->sysconfmodel->data['links'] = '';
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$data['itemlist'] = $this->reportmodel->getAutodiallerReport($bid,$ofset,$limit);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('pbx/manage')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
		$data['paging'] = $this->pagination->create_links();
		$formFields = array();
		$formFields[] = array('label'=>'<label class="col-sm-4 text-right" for="eid">Employee : </label>',
							  'field'=>form_input(array(
											'name'      => 'eid',
											'id'        => 'eid'))
											);
		
		$data['form'] = array(
					'open'=>form_open('Report/autodial',array('name'=>'search','class'=>'form','id'=>'search','method'=>'post')),
					'title'=>'Autodialer Search',
					'form_field'=>$formFields,
					'close'=>form_close()
				);
		
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function addContacts(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		if(!$this->feature_access(11))redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['23']['opt_add']) redirect('Employee/access_denied');
		if($this->input->post('update_system')){
			if($_FILES['filename']['size']>0){
				$this->form_validation->set_rules('filename', 'Filename', 'callback_file_extensions');
				if (!$this->form_validation->run() == FALSE){
					$res=$this->reportmodel->Addcontacts();
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', "Contacts added Successfully");
					redirect('Listcontacts/0');
				}
			}else{
				$this->form_validation->set_rules('name', 'Name', 'required');
				$this->form_validation->set_rules('number', 'Number', 'required|numeric|callback_check_uniqNumber');
				$this->form_validation->set_rules('email', 'email', 'email');
				if (!$this->form_validation->run() == FALSE){
					$res=$this->reportmodel->editcontact();
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', "Contact added Successfully");
					redirect('AddContacts');
				}
			}
		}
		$roleDetail = $this->roleDetail;
		$this->sysconfmodel->data['html']['title'] .= " | Add Contacts";
		$data['module']['title'] = "Add Contacts";
		$formFields[] = array('label'=>'<label class="col-sm-4 text-right" for="filename">'.$this->lang->line('label_autodiallerfile').'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="Upload csv or txt file with the list of numbers to be called and tracked. The format of the file should be Number, Name, Email, Remarks">&nbsp;&nbsp;: </label>',
							  'field'=>form_input(array(
											'name'      => 'filename',
											'id'        => 'filename',
											'class'	  => ' ',
											'type'	  => 'file')
											)
								);
		$formFields[] = array('label'=>'',
							  'field'=>'[or]'
								);						
		$fieldset = $this->configmodel->getFields('23',$bid);
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='filename'){
		foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
		if($checked && !in_array($field['fieldname'] ,array('greetings','bday','hdaytext','hdayaudio','operator','prinumber','record','remark','noext'))) 
			$formFields[] = array(
							'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
									 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
							'field'=>($field['fieldname']=='remarks')?form_textarea(array(
									'name'      => $field['fieldname'],
									'id'        => $field['fieldname'],
									'class'	  => 'form-control '))
								:form_input(array(
									'name'      => $field['fieldname'],
									'id'        => $field['fieldname'],
									'class'	    => ($field['fieldname'] =='number' || $field['fieldname'] == 'name')? 'form-control' : 'form-control'))
									);
				}elseif($field['type']=='c' && $field['show']){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked)$formFields[] = array(
							'label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
							'field'=>$this->configmodel->createFieldAdvance($field,'','search'));
			}
		}				
		$data['form'] = array(
	    	'form_attr'=>array('action'=>'AddContacts','name'=>'autoadd','id'=>'autoadd','enctype'=>"multipart/form-data"),
					'fields'=>$formFields,'parentids'=>$parentbids,
			        'busid'=>$bid,
			        'pid'=>$this->session->userdata('pid'),
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function file_extensions($str){
		$allow_types=array("text/csv");
		if($_FILES['attachments']['size']>0){
			if(!in_array($_FILES['attachments']['type'],$allow_types)){
				$this->form_validation->set_message('file_extensions', 'File Extension Not Allowed');
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			return true;
		}
	}
	function listcontacts(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['23']['opt_view']) redirect('Employee/access_denied');
		$ofset = ($this->uri->segment(2)!=null)?$this->uri->segment(2):0;
		$limit = '30';
		$fieldset = $this->configmodel->getFields('23',$bid);
		$keys = array();
		$header = array('#',"<a href='javascript://'><span id='c_all' class='glyphicon glyphicon-gok'></span></a>");
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,$field['fieldname']);
					array_push($header,(($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
				}
				
			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,$field['fieldKey']);
					array_push($header,$field['customlabel']);
				}
			}
		}	
		$opt_add 	= $roleDetail['modules']['23']['opt_add'];
		$opt_view 	= $roleDetail['modules']['23']['opt_view'];
		$opt_delete = $roleDetail['modules']['23']['opt_delete'];
		if($opt_add || $opt_view || $opt_delete){
			array_push($header,$this->lang->line('level_Action'));
			array_push($keys,"Action");			
		}
		$data['itemlist']['header'] = $header;
		$emp_list=$this->reportmodel->listcontacts($ofset,$limit);
		if($this->input->post('download')){
			$filename = $this->reportmodel->Contacts_Csv($bid);
			$dlink =  "<a href='".$this->config->item('reports_path').$filename.".zip' target='_blank' style='color:#fff;'>Download</a>  ";
		}else{
			$dlink = "";
		}
		$links = array();
		$links[] ='<li><a href="Report/blksms" class="blkSMs" data-toggle="modal" data-target="#modal-blksms" 
		rel="contact"><span title="Bulk SMS" class="glyphicon glyphicon-comment">&nbsp;SMS</span></a></li>';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$data['module']['title'] ="List Contacts". "[".$emp_list['count']."]";
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = ($roleDetail['modules']['23']['opt_download']!=0)?
	   '<li><a href="Report/ContactsCsv/" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Download" class="glyphicon glyphicon-download-alt">&nbsp;Download All</span></a></li>':'';
		$rec = array();
		if(count($emp_list['data'])>0)
		$i = $ofset+1;
		foreach ($emp_list['data'] as $item){
			$arrs = array($i);
			$r = $this->configmodel->getDetail('23',$item['number'],'',$bid);
			$v='<input type="checkbox" class="blk_check" name="blk[]" value="'.$item['number'].'"/>';
			array_push($arrs,$v);
			foreach($keys as $k){
				$v='';
				if($k=="Action"){
					$v.=($opt_add)? '<a href="Report/Editcontact/'.$item['number'].'"><span title="Edit" class="fa fa-edit"></span></a>':'';
					$v.=($opt_delete)?'<a href="Report/deletContact/'.$item['contid'].'"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
					$v.= anchor("Report/clicktoconnect/".$item['number']."/5", '<span title="click To Connect" class="fa fa-phone"></span>',array('class'=>'clickToConnect'));
					$v .= anchor("Report/sendSms/".$item['number']."/contact", ' <span title="Click to send SMS" class="glyphicon glyphicon-comment"></span>','class="clickToSMS" data-toggle="modal" data-target="#modal-empl"');	
				}else{
						$v = isset($r[$k])?$r[$k]:"";
					}
				array_push($arrs,$v);
			}
			$i++;
			array_push($rec,$arrs);
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Listcontacts/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>2					
				));
		$data['paging'] = $this->pagination->create_links();
		$formFields = array();
		$advsearch = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='filename'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked && !in_array($field['fieldname'] ,array('greetings','bday','hdaytext','hdayaudio','operator','prinumber','record','remark','noext'))){ 
					$formFields[] = array(
									'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>form_input(array(
											'name'      => $field['fieldname'],
											'id'        => $field['fieldname'],
											'class'		=>($field['fieldname']=="endtime" ||$field['fieldname']=="datetime")?'datepicker_leads':'form-control'))
											);
							$advsearch[$field['fieldname']]=(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']);				
						}					
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) { $formFields[] = array(
								'label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>form_input(array(
													'name'      => 'custom['.$field['fieldid'].']',
													'class'      => 'form-control',
													)));
								$advsearch['custom['.$field['fieldid'].']']=$field['customlabel'];					
							 }						
			       }
		}
		 $save_cnt=save_search_count($bid,'23',$this->session->userdata('eid'));
		$data['links'] = $links;	
		$data['downlink'] = $dlink;		
		$data['form'] = array(
			'open'=>form_open_multipart('Listcontacts/0',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'save_search'=>$save_cnt,
			'parentids'=>$parentbids,
			'busid'=>$bid,
			'pid'=>$this->session->userdata('pid'),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function deletContact($contid){
		$res=$this->reportmodel->deleteContact($contid);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', "Contacts Deleted Successfully");
		redirect('Listcontacts/0');
	}
	function Editcontact($contid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if($this->input->post('update_system')){
			$res=$this->reportmodel->editcontact($contid);	
			if($res!=""){
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg', "Contacts updated Successfully");
				redirect('Listcontacts/0');
			}else{
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', "Error While Updating contact");
				redirect('Listcontacts/0');
			}
		}
		$this->sysconfmodel->data['html']['title'] .= " | Edit Contact";
		$data['module']['title'] = "Edit Contact";
		$fieldset = $this->configmodel->getFields('23',$bid);						
		$itemDetail = $this->configmodel->getDetail('23',$contid,'',$bid);
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$formFields = array();
					$cf=array('label'=>'<label class="col-sm-4 text-right">Name :</label>',
							  'field'=>form_input(array(
												  'name'        => 'name',
													'id'          => 'name',
													'value'       => (isset($itemDetail['name']))?$itemDetail['name']:'',
													'class'		=>'required form-control')
												));
										array_push($formFields,$cf);
					$cf=array('label'=>'<label class="col-sm-4 text-right">Email :</label>',
							  'field'=>form_input(array(
												  'name'        => 'email',
													'id'          => 'email',
													'value'       => (isset($itemDetail['email']))?$itemDetail['email']:'',
													'class'		=>'required form-control')
												));
										array_push($formFields,$cf);
					$cf=array('label'=>'<label class="col-sm-4 text-right">Number :</label>',
							  'field'=>(isset($itemDetail['number']))?$itemDetail['number']:'');
										array_push($formFields,$cf);
					$cf=array('label'=>'<label class="col-sm-4 text-right">Remarks :</label>',
							  'field'=>form_textarea(array(
												  'name'        => 'remarks',
													'id'          => 'remarks',
													'value'       => (isset($itemDetail['remarks']))?$itemDetail['remarks']:'',
													'class'		=>'form-control')
												));
							array_push($formFields,$cf);
				}
			}elseif($field['type']=='c' && $field['show']){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked)$formFields[] = array(
							'label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
							'field'=>$this->configmodel->createFieldAdvance($field,isset($itemDetail[$field['fieldKey']]) ? $itemDetail[$field['fieldKey']] : '',''));
			}
			}						
			$data['form'] = array(
			        'form_attr'=>array('action'=>'Report/Editcontact/'.$contid,'name'=>'editcontact','id'=>'editcontact','enctype'=>"multipart/form-data"),
					'fields'=>$formFields,
					'close'=>form_close(),
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function check_uniqNumber($num){
		if($this->reportmodel->uniqueNum($num)=="exists"){
			$this->form_validation->set_message('check_uniqNumber', 'The '.$num.' is already in the list');
			return FALSE;
		}else{
			return TRUE;
		}
	}
	
	function archive_download($y='0',$m='0'){
		$args['year']=($y!='0')?$y:date('Y');
		$args['month']=($m!='0')?$m:date('m');
		$args['bid']=$this->session->userdata('bid');
		$list = $this->reportmodel->archiveDownload($args);
		redirect(base_url()."downloads.php?file=".$list);
	}
	function ContactDetails($cno){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['23']['opt_add']) redirect('Employee/access_denied');
		$data['module']['title'] = "Contact Detail";
		$fieldset = $this->configmodel->getFields('23',$bid);
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('23',$cno,'',$bid);
		foreach($fieldset as $field){
			$checked=false;
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
			$data['form'] = array(
						'open'=>form_open_multipart('Report/edit/'.$cno,array('name'=>'editreport','id'=>'editreport','class'=>'form','method'=>'post')),
						'fields'=>$formFields,
						'close'=>form_close()
					);
			$this->load->view('active_view',$data);
		}

	/******************************** Click to Call Report ************************************/
	function click2call(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;	
		if(!$this->feature_access())redirect('Employee/access_denied');
		//$u3 = ($this->uri->segment(3)!='')?$this->uri->segment(3):'all';
		if($this->input->post('download')){
			$filename = $this->reportmodel->click2calls_csv($u3,$bid);
			$dlink =  "<a href='".site_url("reports/".$filename.".zip")."' target='_blank' style='color:#fff;'><b>Download</b></a>  ";
		}else{
			$dlink = "";
		}
		if(!$roleDetail['modules']['25']['opt_view']) redirect('Employee/access_denied');
		if($this->input->post('submit')){	
			if($this->session->userdata('search')!=""){
				$s=$this->session->unset_userdata('search');
			}
		}
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$data['module']['title'] = "Connect To Group";
		$ofset = ($this->uri->segment(2)!=null)?$this->uri->segment(2):0;
		$limit = '30';
		$data['itemlist'] = $this->reportmodel->getClickToCalls($bid,$ofset,$limit);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('C2GroupReport/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit		
						,'uri_segment'=>2));
	   	$links = array();
		$links[]=($roleDetail['modules']['25']['opt_download']!=0) ? '<li><a href="Report/click2calls_csv/" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Download" class="glyphicon glyphicon-download-alt">&nbsp;Download All</span></a></li>':'';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$fieldset = $this->configmodel->getFields('25',$bid);
		$formFields = array();$advsearch=array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && !in_array($field['fieldname'],array('filename'))){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) { 
					$formFields[] = array(
					'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
							 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
					'field'=>
						($field['fieldname']=='gid')
							?form_dropdown('gid',$this->systemmodel->get_groups(),'',"class='form-control'")
							:(($field['fieldname']=='eid')
								?form_dropdown('empid',$this->groupmodel->employee_list(),'',"class='form-control'")
								:(($field['fieldname']=='pulse')
									?form_dropdown('ptype',array(
																'>'=>' > ',
																'='=>' = ',
																'<'=>' < '
															),'',"style='width:60px;' class='form-control'").' '.
									form_input(array(
									'class'     => 'form-control',
									'name'      => $field['fieldname'],
									'id'        => $field['fieldname']
									),'',"style='width:79.5%;'")
									:form_input(array(
									'name'      => $field['fieldname'],
									'id'        => $field['fieldname'],
									'class'		=>($field['fieldname']=="starttime" || $field['fieldname']=="endtime")?'datepicker_leads form-control':'form-control'
									))
								)
							)
						);
				}			
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) { 
					$formFields[] = array(
								'label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>form_input(array(
													'name'      => 'custom['.$field['fieldid'].']'
							)));
				}
			}
		}
		$data['form'] = array(
					'open'=>form_open_multipart(site_url('C2GroupReport/0'),array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
					'form_field'=>$formFields,
					'groups'=>$this->systemmodel->get_groups(),
					'employees'=>$this->groupmodel->employee_list(),
					'parentids'=>$parentbids,
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close(),
					'title'=>$this->lang->line('level_search')
					);
		$data['paging'] = $this->pagination->create_links();
		$data['tab'] = false;
		$data['links'] = $links;
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('level_report');
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function outbound_calls(){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');	
		$ofset = ($this->uri->segment(2)!=null) ? $this->uri->segment(2) : 0;
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		if($this->input->post('submit')){
			if($this->session->userdata('search')!=""){
				$s=$this->session->unset_userdata('search');
			}
		}
		if($this->input->post('download')){
			$filename = $this->reportmodel->outbound_calls_csv($bid,$ofset);
			$dlink =  "<a href='".site_url("reports/".$filename.".zip")."' target='_blank' style='color:#fff;'><b>Download</b></a>  ";
		}else{
			$dlink = "";
		}
		$header = array('#',
						'Executive Number',
						'Customer Number',
						'Source',
						'Start time',
						'End time',
						'Credit used',
						'Status',
						'Recorded File'
						);
		$data['itemlist']['header'] = $header;
		$ofset = ($this->uri->segment(2)!=null)?$this->uri->segment(2):0;
		$limit = '30';
		$calls_info=$this->reportmodel->outbound_calls_list($bid,$ofset,$limit);
		$rec = array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$source = array(
			'0'	 => array('mod'=>'API')
			,'1' => array('mod'=>'Track Report')
			,'2' => array('mod'=>'IVRS Report')
			,'3' => array('mod'=>'PBX Report')
			,'4' => array('mod'=>'Lead')
			,'5' => array('mod'=>'Contact')
		);
		$source_arr = array(
			' '	 => 'Select'
			,'0' => 'API'
			,'1' => 'Track Report'
			,'2' => 'IVRS Report'
			,'3' => 'PBX Report'
			,'4' => 'Lead'
			,'6' => 'Support'
			,'5' => 'Contact'
		);
		$status=array(
			" "=>"Select",
			"0"=>"Failed",
			"1"=>"Originate",
			"2"=>"Executive Busy",
			"3"=>"Customer Busy",
			"4"=>"Call Complete",
			"5"=>"Insufficient Balance"
		);
		if(count($calls_info['data'])>0)
		$i=$ofset+1;
		foreach ($calls_info['data'] as $item){
			$emp = ($item['eid']!='0') ? $item['executive'] : $item['executive'];
			$recording=($roleDetail['role']['accessrecords']=='0') ? (($item['filename']!='' && file_exists('sounds/'.$item['filename']))
					?'<a target="_blank" href="'.site_url('sounds/'.$item['filename']).'"><span title="Sound" class="fa fa-volume-up"></span></a>'
					:'<span class="glyphicon glyphicon-volume-off"></span>'):"";
			$rec[] = array(
					$i,
					$emp,
					$item['customer'],
				    $source[$item['modid']]['mod'],
					$item['starttime'],
					$item['endtime'],
					$item['pulse'],
					$status[$item['status']],
					$recording
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $calls_info['count'];
		$data['module']['title'] ='Outbound Calls'. "[".$data['itemlist']['count']."]";
		$this->pagination->initialize(array(
						 'base_url'=>site_url('C2CReport/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>2					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | OutBound Calls";
		$links = array();
		$links[] = '<li><a href="Report/callMe/" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="glyphicon glyphicon-th" title="Click To Connect">&nbsp;Dialer</span></a></li>';
		$links[] = '<li><a href="Report/outbound_calls_csv/" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Download" class="glyphicon glyphicon-download-alt">&nbsp;Download All</span></a></li>';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$formFields1 = array();
		$formFields1[]=array('label'=>'<label class="col-sm-4 text-right" for="executive">Executive Number: </label>',
				  'field'=>form_input(array(
									  'name'          => 'executive',
									  'class'         => 'form-control',
										'id'          => 'executive',
										'value'       => $this->session->userdata('executive'))));								
		$formFields1[]=array('label'=>'<label class="col-sm-4 text-right" for="customer">Customer Number: </label>',
				 'field'=>form_input(array(
									  'name'       	  => 'customer',
										'id'          => 'customer', 
								     'class'          => 'form-control',
									 'value'     	  => $this->session->userdata('customer'))));										 
	   $formFields1[]=array('label'=>'<label class="col-sm-4 text-right" for="start">Start Date: </label>',
				  'field'=>form_input(array(
									  'name'        => 'startTime',
										'id'        => 'startTime',
										'class'		=> 'datepicker_leads form-control',	
										'value'     => date('Y-m-d')
									)));								
		$formFields1[]=array('label'=>'<label  class="col-sm-4 text-right" for="enddate">End Date: </label>',
				  'field'=>form_input(array(
									  'name'        => 'endTime',
										'id'        => 'endTime',
										'class'		=> 'datepicker_leads form-control',	
										'value'     => date('Y-m-d')
									)));	
		$formFields1[]=array('label'=>'<label class="col-sm-4 text-right" for="status">Status: </label>',
				  'field'=>form_dropdown('stat',$status,'',"class='form-control'"));	  
		$formFields1[]=array('label'=>'<label class="col-sm-4 text-right" for="source">Source: </label>',
				  'field'=>form_dropdown('source',$source_arr,'',"class='form-control'"));
		$formFields = array();
		$data['links'] = $links;
		$data['downlink'] = $dlink;
		$data['form'] = array(
			'open'=>form_open_multipart('C2CReport/0',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields1,
			'parentids'=>$parentbids,
			'busid'=>$bid,
			'pid'=>$this->session->userdata('pid'),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}	
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function c2cedit($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['25']['opt_add']) redirect('Employee/access_denied');
		if($this->input->post('update_system')){
			$res=$this->reportmodel->update_clicktocall($id,$bid);
			redirect('C2GroupReport/0');
		}
		$data['module']['title'] = $this->lang->line('level_clicktocall');
		$fieldset = $this->configmodel->getFields('25');
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('25',$id,'',$bid);
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){
						$cf = array('label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>($field['fieldname']!="custname" 
												&& $field['fieldname']!="custemail"
												&& $field['fieldname']!="hostname")?
												(isset($itemDetail[$field['fieldname']])? (($field['fieldname']=='gid') ? $itemDetail['groupname'] : (($field['fieldname']=='eid') ? $itemDetail['empname'] :$itemDetail[$field['fieldname']])):'')
												:(form_input(array(
												  'name'      => $field['fieldname'],
												  'id'        => $field['fieldname'],
												  'class'     => 'form-control',
												  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:''
										)))
										
								
							);
						array_push($formFields,$cf);
				}
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$cf = array('label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createField($field,
											isset($itemDetail['custom['.$field['fieldid'].']'])?
											$itemDetail['custom['.$field['fieldid'].']']:'')
						);
					array_push($formFields,$cf);
				}
			}
		}
		$disabled=$this->leads_Contact($itemDetail['custnumber']);
		if($disabled!=0){
		$cf=array('label'=>'<label class="col-sm-4 text-right">Convert As a Lead : </label>'
					,'field'=>form_checkbox(array("name"=>"convertaslead","id"=>"convertaslead","value"=>"1")));
					array_push($formFields,$cf);
		}else{
			$cf=array('label'=>'<label class="col-sm-4 text-right">Convert As a Lead : </label>'
					,'field'=>form_checkbox(array("name"=>"convertaslead","id"=>"convertaslead","value"=>"1","checked"=>"true","disabled"=>"disabled")));
					array_push($formFields,$cf);
			
		}

		$data['form'] = array(
		            'form_attr'=>array('action'=>'Report/c2cedit/'.$id,'name'=>'editclicktocall','id'=>'editclicktocall','enctype'=>"multipart/form-data"),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function c2c_delete($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['25']['opt_delete']) redirect('Employee/access_denied');
		$this->reportmodel->delete_clicktocall($id,$bid);
		return 1;
	}
	function undeleteC2Calls(){
		$roleDetail = $this->roleDetail;
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$heading="Deleted Click to Calls";
		if(!$this->feature_access())redirect('Employee/access_denied');
		
		if(!$roleDetail['modules']['25']['opt_view']) redirect('Employee/access_denied');
		if($this->input->post('submit')){	
			if($this->session->userdata('search')!=""){
				$s=$this->session->unset_userdata('search');
			}
		}
		$u3 = ($this->uri->segment(3)!='')?$this->uri->segment(3):'0';
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$data['itemlist'] = $this->reportmodel->deletedC2CList($bid,$ofset,$limit,$url='');
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Report/undeleteC2Calls')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit		
						,'uri_segment'=>3				
				));
		$links = array();
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$data['links'] = $links;
		$data['module']['title'] = $this->lang->line('level_clicktocall');
		$fieldset = $this->configmodel->getFields('25',$bid);
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && !in_array($field['fieldname'],array('filename'))){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
									'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>
										($field['fieldname']=='gid')
											?form_dropdown('gid',$this->systemmodel->get_groups(),'',"class='form-control'")
											:(($field['fieldname']=='eid')
												?form_dropdown('empid',$this->groupmodel->employee_list(),'',"class='form-control'")
												:(($field['fieldname']=='pulse')
													?
													form_dropdown('ptype',array(
																				'>'=>' > ',
																				'='=>' = ',
																				'<'=>' < '
																			),'',"style='width:60px;' class='form-control'").' '.
													form_input(array(
													'name'      => $field['fieldname'],
													'id'        => $field['fieldname']
													),'',"style='width:81%;'  class='form-control'")
													:form_input(array(
													'name'      => $field['fieldname'],
													'id'        => $field['fieldname'],
													'class'		=>($field['fieldname']=="starttime" || $field['fieldname']=="endtime")?'datepicker_leads form-control':'form-control'
													))
												)
											)
										);
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked)$formFields[] = array(
								'label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>form_input(array(
													'name'      => 'custom['.$field['fieldid'].']'
													)));
			}
		}
		$data['form'] = array(
					'open'=>form_open_multipart(site_url('Report/undeleteC2Calls/'.$u3),array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
					'form_field'=>$formFields,
					'adv_search'=>array(),
					'parentids'=>$parentbids,
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close(),
					'title'=>$this->lang->line('level_search')
					);
		$data['paging'] = $this->pagination->create_links();
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('level_report');
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function UnDelC2C($callid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->reportmodel->UnDelc2c($callid,$bid);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg',"Record undeleted Successfully");
		redirect('Report/undeleteC2Calls');
	}
	function c2cActive($id=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['25']['opt_add']) redirect('Employee/access_denied');
		$data['module']['title'] = $this->lang->line('level_Report');
		$fieldset = $this->configmodel->getFields('25',$bid);
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('25',$id,'',$bid);
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){
						$cf = array('label'=>'<label class="col-sm-4 text-right">'.(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										).' : </label>',
									'field'=>($field['fieldname']=='gid')
											?$itemDetail['groupname']
											:(($field['fieldname']=='eid')
												?$itemDetail['empname']:$itemDetail[$field['fieldname']])									
							);
						array_push($formFields,$cf);
				}
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$cf = array('label'=>'<label class="col-sm-4 text-right">'.$field['customlabel'].' : </label>',
								'field'=>(isset($itemDetail['custom['.$field['fieldid'].']'])?$itemDetail['custom['.$field['fieldid'].']']:"")
						);
					array_push($formFields,$cf);
				}
			}
		}
		$data['form'] = array(
					'open'=>form_open_multipart('Report/c2cedit/'.$id,array('name'=>'c2ceditreport','id'=>'c2ceditreport','class'=>'form','method'=>'post')),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$this->load->view('active_view',$data);
	}
	/********************** END of CLick to Call Functions ****************************/
	function ContactsCsv(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data=array('systemfields'=>$this->configmodel->getFields('23',$bid),
					'roleDetail'=>$this->roleDetail,
					 );
		$this->load->view('contactsCSV',$data);
	}
	function leads_Contact($num){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->reportmodel->getLeadContact_Info($num,$bid);
		return $res;
	}
	function callanalytics(){
		if($this->input->post('submit')){
				$this->session->set_userdata('filter',$_POST);
				$data=$_POST;
		}else{
			$data=array(
				'stime'=>date('Y-m-d',strtotime('-6 days')),
				'etime'=>date('Y-m-d')
			);
		}
		$this->sysconfmodel->viewLayout('callanalytics',$data);
	}
	function callbytime(){
		$res=$this->reportmodel->callbytime();
		$max = 0;
		include_once( 'system/application/views/open-flash-chart.php' );
		$g = new graph();
		$hr=array();
		foreach($res as $r){
			$g->line_hollow( 2, 4, $this->colormodel->getColor(), $r['name'], 10 );
			$data = array();
			for($i=0;$i<=24;$i++){
				$data[] = (isset($r[$i]))? $r[$i] : '0';
			}
		$g->set_data($data);
		$max = ($max < max($data)) ? max($data) : $max;
		$g->set_tool_tip($r['name'].'<br>#val# Calls' );
		}
		for($i=0;$i<=24;$i++){
			$hr[]= $i;
		}
		$g->set_x_labels($hr);
		$g->set_x_label_style( 10, '0x000000', 0, 3 );
		$g->set_y_max((ceil($max/10)*10));
		$g->y_label_steps(10);
		$g->set_y_legend( 'Calls Count', 12, '#000000' );
		$g->set_x_legend( 'Call Timing', 12, '#000000' );
		$g->set_tool_tip( '#key#:#val#' );
		$g->title( 'Calls by Time', '{font-size:18px; color: #d01f3c}' );
		echo $g->render();
	}
	function ivrs_callbytime(){
		$res=$this->reportmodel->ivrs_callbytime();
		$max = 0;
		include_once( 'system/application/views/open-flash-chart.php' );
		$g = new graph();
		$hr=array();
		//echo "<pre>";print_r($res);echo "</pre>";
		foreach($res as $r){
			$g->line_hollow( 2, 4, $this->colormodel->getColor(), $r['name'], 10 );
			//$g->bar( 50, $this->colormodel->getColor(), $r['name'], 10 );
			//$g->bar( 2, 4, $this->colormodel->getColor(), $r['name'], 10 );
			$data = array();
			for($i=0;$i<=24;$i++){
				$data[] = (isset($r[$i]))? $r[$i] : '0';
			}
		$g->set_data($data);
		$max = ($max < max($data)) ? max($data) : $max;
		$g->set_tool_tip($r['name'].'<br>#val# Calls' );
		}
		for($i=0;$i<=24;$i++){
			$hr[]= $i;
		}
		
		$g->set_x_labels($hr);
		$g->set_x_label_style( 10, '0x000000', 0, 3 );
		$g->set_y_max((ceil($max/10)*10));
		$g->y_label_steps(10);
		$g->set_y_legend( 'Calls Count', 12, '#000000' );
		$g->set_x_legend( 'Call Timing', 12, '#000000' );
		$g->set_tool_tip( '#key#:#val#' );
		$g->title( 'Calls by Time', '{font-size:18px; color: #d01f3c}' );
		echo $g->render();
	}
	function pbx_callbytime(){
		$res=$this->reportmodel->pbx_callbytime();
		$max = 0;
		include_once( 'system/application/views/open-flash-chart.php' );
		$g = new graph();
		$hr=array();
		//echo "<pre>";print_r($res);echo "</pre>";
		foreach($res as $r){
			$g->line_hollow( 2, 4, $this->colormodel->getColor(), $r['name'], 10 );
			//$g->bar( 50, $this->colormodel->getColor(), $r['name'], 10 );
			//$g->bar( 2, 4, $this->colormodel->getColor(), $r['name'], 10 );
			$data = array();
			for($i=0;$i<=24;$i++){
				$data[] = (isset($r[$i]))? $r[$i] : '0';
			}
		$g->set_data($data);
		$max = ($max < max($data)) ? max($data) : $max;
		$g->set_tool_tip($r['name'].'<br>#val# Calls' );
		}
		for($i=0;$i<=24;$i++){
			$hr[]= $i;
		}
		
		$g->set_x_labels($hr);
		$g->set_x_label_style( 10, '0x000000', 0, 3 );
		$g->set_y_max((ceil($max/10)*10));
		$g->y_label_steps(10);
		$g->set_y_legend( 'Calls Count', 12, '#000000' );
		$g->set_x_legend( 'Call Timing', 12, '#000000' );
		$g->set_tool_tip( '#key#:#val#' );
		$g->title( 'Calls by Time', '{font-size:18px; color: #d01f3c}' );
		echo $g->render();
	}
	function callbyweek(){
		$week=array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
		$res=$this->reportmodel->callbyweek();
		$max = 0;
		include_once( 'system/application/views/open-flash-chart.php' );
		$g = new graph();
		$hr=array();
		//echo "<pre>";print_r($res);echo "</pre>";
		foreach($res as $r){
			$g->line_hollow( 2, 4, $this->colormodel->getColor(), $r['name'], 10 );
			//$g->bar( 50, $this->colormodel->getColor(), $r['name'], 10 );
			//$g->bar( 2, 4, $this->colormodel->getColor(), $r['name'], 10 );
			$data = array();
			for($i=0;$i<sizeof($week);$i++){
				$data[] = (isset($r[$week[$i]]))? $r[$week[$i]] : '0';
			}
		$g->set_data($data);
		$max = ($max < max($data)) ? max($data) : $max;
		$g->set_tool_tip($r['name'].'<br>#val# Calls' );
		}
		for($i=0;$i<sizeof($week);$i++){
			$hr[]= $week[$i];
		}
		
		$g->set_x_labels($hr);
		//$g->set_x_label_style( 10, '0x000000', 0, 3 );
		$g->set_y_max((ceil($max/10)*10));
		$g->y_label_steps(10);
		$g->set_y_legend( 'Calls Count', 12, '#000000' );
		$g->set_x_legend( 'Days', 12, '#000000' );
		$g->set_tool_tip( '#key#:#val#' );
		$g->title( 'Calls by Day', '{font-size:18px; color: #d01f3c}' );
		echo $g->render();
	}
	function ivrs_callbyweek(){
		$week=array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
		$res=$this->reportmodel->ivrs_callbyweek();
		$max = 0;
		include_once( 'system/application/views/open-flash-chart.php' );
		$g = new graph();
		$hr=array();
		//echo "<pre>";print_r($res);echo "</pre>";
		foreach($res as $r){
			$g->line_hollow( 2, 4, $this->colormodel->getColor(), $r['name'], 10 );
			//$g->bar( 50, $this->colormodel->getColor(), $r['name'], 10 );
			//$g->bar( 2, 4, $this->colormodel->getColor(), $r['name'], 10 );
			$data = array();
			for($i=0;$i<sizeof($week);$i++){
				$data[] = (isset($r[$week[$i]]))? $r[$week[$i]] : '0';
			}
		$g->set_data($data);
		$max = ($max < max($data)) ? max($data) : $max;
		$g->set_tool_tip($r['name'].'<br>#val# Calls' );
		}
		for($i=0;$i<sizeof($week);$i++){
			$hr[]= $week[$i];
		}
		
		$g->set_x_labels($hr);
		//$g->set_x_label_style( 10, '0x000000', 0, 3 );
		$g->set_y_max((ceil($max/10)*10));
		$g->y_label_steps(10);
		$g->set_y_legend( 'Calls Count', 12, '#000000' );
		$g->set_x_legend( 'Days', 12, '#000000' );
		$g->set_tool_tip( '#key#:#val#' );
		$g->title( 'Calls by Day', '{font-size:18px; color: #d01f3c}' );
		echo $g->render();
	}
	function pbx_callbyweek(){
		$week=array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
		$res=$this->reportmodel->pbx_callbyweek();
		$max = 0;
		include_once( 'system/application/views/open-flash-chart.php' );
		$g = new graph();
		$hr=array();
		//echo "<pre>";print_r($res);echo "</pre>";
		foreach($res as $r){
			$g->line_hollow( 2, 4, $this->colormodel->getColor(), $r['name'], 10 );
			//$g->bar( 50, $this->colormodel->getColor(), $r['name'], 10 );
			//$g->bar( 2, 4, $this->colormodel->getColor(), $r['name'], 10 );
			$data = array();
			for($i=0;$i<sizeof($week);$i++){
				$data[] = (isset($r[$week[$i]]))? $r[$week[$i]] : '0';
			}
		$g->set_data($data);
		$max = ($max < max($data)) ? max($data) : $max;
		$g->set_tool_tip($r['name'].'<br>#val# Calls' );
		}
		for($i=0;$i<sizeof($week);$i++){
			$hr[]= $week[$i];
		}
		
		$g->set_x_labels($hr);
		//$g->set_x_label_style( 10, '0x000000', 0, 3 );
		$g->set_y_max((ceil($max/10)*10));
		$g->y_label_steps(10);
		$g->set_y_legend( 'Calls Count', 12, '#000000' );
		$g->set_x_legend( 'Days', 12, '#000000' );
		$g->set_tool_tip( '#key#:#val#' );
		$g->title( 'Calls by Day', '{font-size:18px; color: #d01f3c}' );
		echo $g->render();
	}
	function callbyregion(){
		// generate some random data
		srand((double)microtime()*1000000);
		$res=$this->reportmodel->callbyregion();
		$data = array();
		$region = array();
		for( $i=0; $i<5; $i++ )
		{
		  $data[] = rand(5,15);
		}
		$labels = array_keys($res);
		foreach($labels as $label)
		{
			///$region_arr = explode('.',$label);
			//$service = substr($label, -2);;
			//$full_region = explode(' ',$label);
			//$region[] = $full_region[0];
			$colours[] = $this->colormodel->getColor();
		}
		include_once( 'system/application/views/open-flash-chart.php' );
		$g = new graph();

		//
		// PIE chart, 60% alpha
		//
		$g->pie(60,'#505050','{font-size: 12px; color: #404040;');
		//
		// pass in two arrays, one of data, the other data labels
		//
		$g->pie_values(array_values($res), array_keys($res));
		//$g->pie_values( array(3,488,1), array('Karnataka Telecom Circle. RG','Karnataka Telecom Circle. TD','Delhi Metro Telecom Circle (includes NCR, Faridabad, Ghaziabad, Gurgaon & Noida) VF') );
		//
		// Colours for each slice, in this case some of the colours
		// will be re-used (3 colurs for 5 slices means the last two
		// slices will have colours colour[0] and colour[1]):
		//
		$g->pie_slice_colours($colours);

		//$g->set_tool_tip( '#val#' );
		$g->set_tool_tip( '#x_label#<br>#val# Calls' );

		$g->title( 'Calls by Region', '{font-size:18px; color: #d01f3c}' );
		echo $g->render();
	}
	
	function clicktoconnect($callid,$module){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$ret = '0';
		if($module!=''){
			$ret = $this->reportmodel->getDetail($bid,$callid,$module);
		}
		echo $ret;
	}
	function followupreport(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if($this->input->post('submit')){
			if($this->session->userdata('search')!=""){
				$s=$this->session->unset_userdata('search');
			}
		}
		if($this->input->post('download')){
			$filename = $this->reportmodel->followupdownload($bid);
			$dlink = "<a href='".site_url("reports/".$filename.".zip")."' target='_blank' style='color:#fff'>Download</a>  ";
		}else{
			$dlink = "";
		}
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$data['module']['title'] ='Followups';
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$data['itemlist'] = $this->reportmodel->getfollowups($ofset,$limit,$bid);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('FollowupReport/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit		
						,'uri_segment'=>3				
				));
		$links = array();	
		$links[] ='<li><a href="Report/followupDownload/" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Download" class="glyphicon glyphicon-download-alt">&nbsp;Download All</span></a></li>';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$formFields = array();
		$advsearch = array();
		$formFields[] = array('label'=>'<label class="col-sm-4 text-right" for="employee">Employee:</label>',
							'field'=>form_dropdown('eid',$this->groupmodel->employee_list(),'',"id='eid' class='form-control'"));
		$formFields[] = array(	'label'=>'<label  class="col-sm-4 text-right" for="comment">Comment : </label>',
								'field'=>form_textarea(array(
												'name'      => 'comment',
												'id'        => 'comment',
												'class'		=> 'required form-control'	
												)));

		$advsearch['comment']="Comment";									
		$formFields[]=array('label'=>'<label class="col-sm-4 text-right" for="followupdate">Start Date: </label>',
				  'field'=>form_input(array(
									  'name'        => 'followupdate1',
										'id'        => 'followupdate1',
										'class'		=> 'datepicker_leads form-control',	
										'value'     => $this->session->userdata('followupdate1'))));
		$formFields[]=array('label'=>'<label class="col-sm-4 text-right" for="followupdate">End Date: </label>',
				  'field'=>form_input(array(
									  'name'        => 'followupdate2',
										'id'        => 'followupdate2',
										'class'		=> 'datepicker_leads form-control',	
										'value'     => $this->session->userdata('followupdate2'))));
		$advsearch['followupdate']="Followupdate";								
		$formFields[]=array('label'=>'<label class="col-sm-4 text-right" for="source">Source: </label>',
				  'field'=>form_input(array(
									    'name'        => 'src1',
									    'class'       => 'form-control',
										'id'          => 'src1',
										'value'       => $this->session->userdata('src1'))));
										
							
		$data['form'] = array(
					'open'=>form_open(site_url('FollowupReport/'),array('name'=>'followups','class'=>'form','id'=>'followups','method'=>'post')),
					'form_field'=>$formFields,
					'adv_search'=>array(),
					'save_search'=>3,
					'parentids'=>$parentbids,
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close(),
					'title'=>$this->lang->line('level_search')
					);
	    $data['links'] = $links;	
	    $data['downlink'] = $dlink;		
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('level_report');
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	
	function showData($str){
		echo $str;
	}
	function blkSms($source=''){
		$bid=$this->session->userdata('bid');
		if($this->input->post('update_system')){
			$number=explode(",",$_POST['to']);
			$to=array();
			$source_target=array("calltrack"=>'6'
								 ,"pbx"=>'24'
								 ,"ivrs"=>'16'
								 ,"employee"=>'2'
								 ,"contact"=>'23'
								 ,"leads"=>'26' 
								 ,"support"=>'40'
								 ,"campaign"=>'34');
			foreach($number as $n_to){
				$callDetail=$this->configmodel->getDetail($source_target[$_POST['source']],$n_to,'',$bid);
				$to[]= ($_POST['source'] == 'leads' || $_POST['source'] == 'support' || $_POST['source'] == 'contact') 
							? $callDetail['number']
							: (($_POST['source'] == 'employee')
								? $callDetail['empnumber'] 
								: (($_POST['source'] == 'campaign') 
									? $callDetail['caller_number'] 
									: $callDetail['callfrom']
								  )
							  );
			}
			$to=array_unique($to);
			$this->load->helper('mcube_helper');
			$dnds=0;
			foreach($to as $to){
				if($this->session->userdata('dnd_status')!=0){
					$reply=sms_send($to,$_POST['sms_content']);
					$fp = fopen("smslog.txt","a");fwrite($fp,"\n".'['.date('Y-m-d H:i:s').'] : '.'SMS:'.$reply);fclose($fp);
					$dnd_status=0;
				}else{
					$dnd = (array)filter_dnd($to);	
					if($dnd['dnd']==0){
						$reply=sms_send($to,$_POST['sms_content']);
						$fp = fopen("smslog.txt","a");fwrite($fp,"\n".'['.date('Y-m-d H:i:s').'] : '.'SMS:'.$reply);fclose($fp);

						$dnd_status=0;
					}else{
						$dnd_status=1;
					}
				}
				$set_array=array("contentid"=>(isset($reply[0]))?$reply[0]:'',
								 "number"=>$to,
								"content"=>$_POST['sms_content'],
								"datetime"=>date('Y-m-d h:i:s'),
								"source"=>$_POST['source'],
								"dnd_status"=>$dnd_status,
								"status"=>1);	
				$sms_push=$this->reportmodel->sms_message($set_array);
			 }
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg',"SMS Delivered Successfully");
			$r = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->redirect_source($_POST['source']);
			redirect($r);
		  }
		$data['module']['title'] = "Bulk SMS";
		$data['links'] = '';
		$data['nosearch']=true;
		$data['paging'] = '';
		$formFields[] = array(	'label'=>'<label class="col-sm-4 text-right" for="comment">SMS Template : </label>',
								'field'=>form_dropdown('temp_id',$this->empmodel->smsTempnames(),'',"id='temp_id' class='form-control'"));
		
		$formFields[] = array(	'label'=>'<label class="col-sm-4 text-right" for="comment">Message : </label>',
								'field'=>form_textarea(array(
											'name'      => 'sms_content',
											'id'        => 'sms_content',
											'class'		=>'word_count required form-control'	
											)));						
		$formFields[] = array(	'label'=>'<input type="hidden" name="to" id="to" />',
								'field'=>'');
		$data['form'] = array(
					'form_attr'=>array('action'=>'Report/blkSms','id'=>'sendsms','name'=>'sendsms','enctype'=>"multipart/form-data"),
					'fields'=>$formFields,
					'hidden'=>array('source'=>$source),
					'parentids'=>'',
					'busid'=>'',
					'pid'=>'',
					'close'=>form_close()
				);
		$this->load->view('form_view2',$data);
	}
	function sendSms($calid,$source){
		$bid=$this->session->userdata('bid');
		$to='';
		switch($source){
			case 'calltrack':
					$callDetail=$this->configmodel->getDetail('6',$calid,'',$bid);
					$to=$callDetail['callfrom'];
					break;
			case 'ivrs':
					$callDetail=$this->configmodel->getDetail('16',$calid,'',$bid);
					$to=$callDetail['callfrom'];
					break;
			
			case 'pbx':
					$callDetail=$this->configmodel->getDetail('24',$calid,'',$bid);
					$to=$callDetail['callfrom'];
					break;
			case 'leads':
					$callDetail=$this->configmodel->getDetail('26',$calid,'',$bid);
					$to=$callDetail['number'];
					break;
			case 'support':
					$callDetail=$this->configmodel->getDetail('40',$calid,'',$bid);
					$to=$callDetail['number'];
					break;
			case 'contact':
					$callDetail=$this->configmodel->getDetail('23',$calid,'',$bid);
					$to=$callDetail['number'];
					break;
			case 'employee':
					$callDetail=$this->configmodel->getDetail('2',$calid,'',$bid);
					$to=$callDetail['empnumber'];
					break;
			case 'campaign':
					$callDetail=$this->configmodel->getDetail('34',$calid,'',$bid);
					$to=$callDetail['caller_number'];
					break;
		}
		$data['module']['title'] = "Send SMS";
		$data['links'] = '';
		$data['nosearch']=true;
		$data['paging'] = '';
		$formFields[] = array(	'label'=>'<label for="comment" class="col-sm-4 text-right">To : </label>',
								'field'=>$to);
		$templates = $this->empmodel->smsTempnames();
		if(! empty($templates)){
			$formFields[] = array(	'label'=>'<label class="col-sm-4 text-right" for="comment">SMS Template : </label>',
								'field'=>form_dropdown('temp_id',$templates,'',"class='form-control' id='temp_id'"));
		}
		$formFields[] = array(	'label'=>'<label class="col-sm-4 text-right" for="comment">Message : </label>',
								'field'=>form_textarea(array(
											'name'      => 'sms_content',
											'id'        => 'sms_content',
											'class'		=> 'word_count required form-control'	
											)));						
		$data['form'] = array(
					'form_attr'=>array('action'=>'Report/Send_SMS','id'=>'sendsms','name'=>'sendsms','enctype'=>"multipart/form-data"),
					'fields'=>$formFields,
					'hidden'=>array('to'=>$to,'source'=>$source),
					'parentids'=>'',
					'busid'=>'',
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close()
				);
		$this->load->view('form_view2',$data);
	}
	function Send_SMS(){
		$fp = fopen("smslog.txt","a");fwrite($fp,"\n".'['.date('Y-m-d H:i:s').'] : '.'SMS:');fclose($fp);
		$this->load->helper('mcube_helper');
		if($this->session->userdata('dnd_status')!=0){
			$reply=sms_send($_POST['to'],$_POST['sms_content']);
			$fp = fopen("smslog.txt","a");fwrite($fp,"\n".'['.date('Y-m-d H:i:s').'] : '.'SMS:'.$reply);fclose($fp);
			$dnd_status=0;
		}else{
			$dnd = (array)filter_dnd($_POST['to']);	
			if($dnd['dnd']==0){
				$reply=sms_send($_POST['to'],$_POST['sms_content']);
				$fp = fopen("smslog.txt","a");fwrite($fp,"\n".'['.date('Y-m-d H:i:s').'] : '.'SMS:'.$reply);fclose($fp);

				$dnd_status=0;
			}else{
				$dnd_status=1;
			}
		}
		$set_array=array("contentid"=>(isset($reply[0]))?$reply[0]:'',
							 "number"=>$_POST['to'],
							"content"=>$_POST['sms_content'],
							"datetime"=>date('Y-m-d h:i:s'),
							"source"=>$_POST['source'],
							"dnd_status"=>$dnd_status,
							"status"=>1);	
			$sms_push=$this->reportmodel->sms_message($set_array);		
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg',"SMS Delivered Successfully");
			redirect($this->redirect_source($_POST['source']));	
	}
	function redirect_source($source){
		switch($source){
				case 'calltrack':
						$redirect="TrackReport/all";
						break;
				case 'ivrs':
						$redirect="IVRSReport/all/0";
						break;
				case 'pbx':
						$redirect="PBXReport/all/0";
						break;
				case 'leads':
						$redirect="ListLead/2";
						break;
				case 'support':
						$redirect="ListSupTkt/0";
						break;
				case 'contact':
						$redirect="Listcontacts/0";
						break;
				case 'employee':
						$redirect="ManageEmployee/0";
						break;
				case 'campaign':
						$redirect="campaign/campaignReport";
						break;
		}
		return $redirect;
	}
	
	function followupDownload(){
		$eid = $this->session->userdata('eid');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data=array('systemfields'=>$this->configmodel->getFields('29',$bid),
					'roleDetail'=>$this->roleDetail,
				    'bid' => $bid,
					'eid'=>($eid!="")?$eid:'');
	  $this->load->view('followupdownload',$data);
	}
	function click2calls_csv($eid=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data=array('systemfields'=>$this->configmodel->getFields('25'),
					'roleDetail'=>$this->roleDetail,
					'eid'=>($eid!="")?$eid:'');
		$this->load->view('clicktocalls_csv',$data);
	}
	function outbound_calls_csv($eid=''){
	    $eid = $this->session->userdata('eid');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data= $this->reportmodel->outbound_calls_list($bid);
	     	//echo "<pre>"; print_r($data);	exit;
		$this->load->view('outbound_calls_csv',$data);
	}
	function sendFields($call_id,$bid,$mod,$type){
		if($this->input->post('sendSMs') || $this->input->post('sendEmail')){
				$case=(isset($_POST['sendSMs']))?'1':'2';
				$re=$this->reportmodel->sendF($call_id,$bid,$mod,$type);
				$this->session->set_flashdata('msgt', ($re!="Fail to sent")?'success':'error');
				$this->session->set_flashdata('msg',$re);
				if($type == 'calltrack'){
		         redirect('TrackReport/all');
		        }elseif( $type == 'pbx'){
			      redirect('PBXReport/all/0');
		        }elseif( $type == 'ivrs'){
			      redirect('IVRSReport/all/0');
		        }elseif( $type == 'support'){
			      redirect('support/listSupportTkt');
		        }			
		}
		$formFields = array();
		$smsbalance=$this->configmodel->smsBalance($bid);
		$EmailDetail = $this->configmodel->getDetail('27',$bid,'',$bid);
		$itemDetail = $this->configmodel->getDetail($mod,$call_id,'',$bid);
		if($type == ' calltrack'){
			$itemDetail['asto'] = $itemDetail['asto'];
		}elseif( $type == 'ivrs'){
			$itemDetail['asto'] = $itemDetail['eid'];
		}elseif( $type == 'support'){
			$itemDetail['asto'] = $itemDetail['asto'];
		}
		$empDetail=$this->configmodel->getDetail('2',$itemDetail['asto'],'',$bid);
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));			
		$fieldset = $this->configmodel->getFields($mod,$bid);
		$empnumber=(isset($empDetail['empnumber']) && $empDetail['empnumber']!="")?$empDetail['empnumber']:'';
		$totalFelds=array();
		$formFields[] = array('label'=>'<label class="col-sm-4 text-right" for="Email">Assign to :</label>',
									'field'=>form_dropdown('asto',$this->groupmodel->employee_list(),$itemDetail['asto'],"id='asto'"));
		$formFields[] = array('label'=>'<label class="col-sm-4 text-right" for="To">SMS To	 :</label>',
									'field'=>'<span id="enumber">'.$empnumber.'</span>');
		
		$formFields[] = array('label'=>'<label class="col-sm-4 text-right" for="Email">Fields :</label>',
									'field'=>'');
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					
					$formFields[] = array('label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'"></label>',
									'field'=>'<input type="checkbox" name="formfields[]" value="'.(($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).'~'.$itemDetail[$field['fieldname']].'"/><label class="col-sm-4 text-right">'.(($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).'</label>');
				
				}
			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				$fieldKey = $this->systemmodel->getFieldKey($field['fieldid'],$bid);
				$field['fieldKey'] = $fieldKey;
				if($checked){
					$formFields[] = array(
					        'label'=>'<label class="col-sm-4 text-right" for= "'.$fieldKey.'"></label>',
							'field'=>'<input type="checkbox" name="formfields[]" value="'.$field['customlabel'].'~'.((isset($itemDetail[$fieldKey]) && $itemDetail[$fieldKey]!="")?$itemDetail[$fieldKey]:'').'"/>'.$field['customlabel']);
				}
			}
		}

		$data['module']['title'] = "Send Field Data";
		$data['links'] = '';
		$data['nosearch']=true;
		$data['paging'] = '';
		$data['form'] = array(
						'open'=>form_open_multipart('Report/sendFields/'.$call_id.'/'.$bid.'/'.$mod.'/'.$type
									,array('name'=>'sendField','class'=>'form','id'=>'sendField','method'=>'post')
									,array('smsBal'=>($smsbalance>0)?'1':'0','Emailconfig'=>(empty($itemDetail))?'0':'1')
									),
						'fields'=>$formFields,
						'parentids'=>'',
						'busid'=>'',
						'pid'=>$this->session->userdata('pid'),
						'close'=>form_close()
					);
		$this->load->view('form_view_field',$data);
	}
	function Edetail($eid){
		$empDetail=$this->configmodel->getDetail('2',$eid,'',$this->session->userdata('bid'));
		echo $empDetail['empnumber'];
	}
	function empBreakHis(){
		$roleDetail = $this->roleDetail;
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$heading="Employee Break Timings";
		if(!$roleDetail['modules']['36']['opt_view']) redirect('Employee/access_denied');
		if($this->input->post('submit')){	
			if($this->session->userdata('search')!=""){
				$s=$this->session->unset_userdata('search');
			}
		}
		if($this->input->post('download')){
			$filename = $this->reportmodel->breakHisDownload($bid);
			$dlink =  "<a href='".site_url("reports/".$filename.".zip")."' target='_blank' style='color:#fff'>Download</a>  ";
		}else{
			$dlink = "";
		}
		$ofset = ($this->uri->segment(2)!=null)?$this->uri->segment(2):0;
		$limit = '30';
		$data['itemlist'] = $this->reportmodel->getBreakTimings($bid,$ofset,$limit);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('EmpBreakHis/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit		
						,'uri_segment'=>2				
				));
		$data['module']['title'] =$heading . ' ['.$data['itemlist']['count'].']';
		$links = array();
		$links[] = '<li><a href="Report/breakHisDownload/" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Download" class="glyphicon glyphicon-download-alt">Download All</span> </a></li>';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$fieldset = $this->configmodel->getFields('36',$bid);
		$formFields = array();
		$advsearch = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) {
					$formFields[] = array(
									'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>($field['fieldname'] =='eid') ? form_dropdown('eid',$this->groupmodel->employee_list(),'',"class='form-control'")
												:form_input(array(
														'name'      => $field['fieldname'],
														'id'        => $field['fieldname'],
														'class'		=>($field['fieldname'] == "start_time" || $field['fieldname'] == "end_time") ? 'datepicker_leads form-control':'form-control'
														))
										);
					$advsearch[$field['fieldname']]=(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']);
				}
			 }elseif($field['type']=='c' && $field['show']){
				 foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) { $formFields[] = array(
								'label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
							    'field'=>form_input(array(
										'name'      => 'custom['.$field['fieldid'].']',
										'class'     => 'form-control',
													)));
								$advsearch['custom['.$field['fieldid'].']']=$field['customlabel'];							
							 }						
			 }
		}
		$save_cnt=save_search_count($bid,'36',$this->session->userdata('eid'));
		$data['form'] = array(
					'open'=>form_open_multipart(site_url('EmpBreakHis/0/'),array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
					'form_field'=>$formFields,
					'close'=>form_close(),
					'parentids'=>$parentbids,
					'adv_search'=>array(),
					'save_search'=>$save_cnt,
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'title'=>$this->lang->line('level_search')
					);
		$data['links'] = $links;
	    $data['downlink'] = $dlink;	
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('level_report');
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function breakHisDownload(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$eid = $this->session->userdata('eid');
		$data=array('systemfields'=>$this->configmodel->getFields('36',$bid),
					'roleDetail'=>$this->roleDetail,
					 'eid'=>($eid!="")?$eid:'');
		$this->load->view('breakHisdownload',$data);
	}
	function del_search($sid,$url){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		remove_search($sid,$bid);
		$url = str_replace("~","/",$url);
		redirect($url);
	}
	function bulk_Del(){
	    print_r($POST);
		$rst = $this->reportmodel->blk_del($_POST['callid']);
		echo "1";	
	}
	
	function callsummary(){
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = 'Call Summary';
		$ofset = ($this->uri->segment(2)!=null)?$this->uri->segment(2):0;
		$limit = '30';
		$search = array(
			 'sdate' => (isset($_POST['sdate']) && $_POST['sdate']!='') ? $_POST['sdate'] : date('Y-m-d 00:00')
			,'edate' => (isset($_POST['edate']) && $_POST['edate']!='') ? $_POST['edate'] : date('Y-m-d H:i')
			,'gid' => (isset($_POST['gid']) && $_POST['gid']!='0') ? $_POST['gid'] : '0'
		);
		if((isset($_POST['sdate']) || isset($_POST['sdate']) || isset($_POST['gid'])) || !$this->session->userdata('search')){
			$this->session->set_userdata('search',$search);
		}else{
			$search = $this->session->userdata('search');
		}
		if($this->input->post('download')){
			$filename = $this->reportmodel->callsumDownload($bid);
			$dlink =  "<a href='".site_url("reports/".$filename.".zip")."' target='_blank' style='color:#fff'><b>Download</b></a>  ";
		}else{
			$dlink = "";
		}
		$list=$this->reportmodel->getCallSummary($search,$ofset,$limit);
		$header = array(
					 'SlNo.'
					,'Group Name'
					,'Keyword'
					,'Total Call'
					,'Unique Call'
					,'Answered Call'
					,'Missed Call'
				);
		$data['itemlist']['header'] = $header;
		$list1 = array();
		$i = $ofset;
		foreach($list['data'] as $r){
			$rec = array();
			$rec['slno'] = ++$i;
			$rec['groupname'] = $r['groupname'];
			$rec['keyword'] = $r['keyword'];
			$rec['total'] = $r['total'];
			$rec['uniquecall'] = $r['uniquecall'];
			$rec['answeredcall'] = $r['answeredcall'];
			$rec['missedcall'] = $r['missedcall'];
			$list1[] = $rec;
		}
		$data['itemlist']['rec'] = $list1;
		$data['itemlist']['count'] = $list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('CallSummary/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>2		
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | Call Summary";
		$links = array();
		$links[] ='<li><a href="Report/callsumDownload/" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Download" class="glyphicon glyphicon-download-alt">&nbsp;Download All</span></a></li>';
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$formFields = array();
		$formFields[] =array('label'=>'<label class="col-sm-4 text-right" for="startdate">Start Date : </label>',
				  'field'=>form_input(array(
									    'name'      => 'sdate',
										'id'        => 'sdate',
										'class'     => 'datetimepicker form-control',
										'value'     => isset($search['sdate']) ? $search['sdate'] : date('Y-m-d 00:00')
										)));
		$formFields[] =array('label'=>'<label class="col-sm-4 text-right" for="enddate">End Date : </label>',
				  'field'=>form_input(array(
									  'name'        => 'edate',
										'id'        => 'edate',
										'class'     => 'datetimepicker form-control',
										'value'     => isset($search['edate']) ? $search['edate'] : date('Y-m-d H:i')
										)));
		$formFields[] =array('label'=>'<label class="col-sm-4 text-right" for="groupname">Select Group : </label>',
				  'field'=>form_dropdown('gid',$this->systemmodel->CSGroups(),$search['gid'],"class='form-control'")
				 );
		$data['downlink'] = $dlink;
		$data['links'] = $links;
		$data['form'] = array(
			'open'=>form_open_multipart('CallSummary/0/',array('name'=>'callsummary','class'=>'form','id'=>'callsummary','method'=>'post')),
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
	function callsumDownload(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data=array('roleDetail'=>$this->roleDetail,
					 'attributes' => array('class' => 'form', 'id' =>'csdown','name'=>'csdown'),
					 'URL' => "CallSummary/0/",
					 'bid' => $bid
					 );
		$this->load->view('csdownload',$data);
	}
	function outbound(){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;	
		if(!$roleDetail['modules']['47']['opt_view']) redirect('Employee/access_denied');
		if($this->input->post('submit')){	
			if($this->session->userdata('search')!=""){
				$s=$this->session->unset_userdata('search');
			}
		}
		if($this->input->post('download')){
			$filename = $this->reportmodel->obDownload($bid);
			$dlink =  "<a href='".site_url("reports/".$filename.".zip")."' target='_blank' style='color:#fff'>Download</a>  ";
		}else{
			$dlink = "";
		}
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$data['itemlist'] = $this->reportmodel->list_outbound($bid,$ofset,$limit);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('OutboundReport/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit		
						,'uri_segment'=>3				
				));
		$data['module']['title'] = "Outbound Calls [".$data['itemlist']['count']."]";	
		$link = array();	
		$links[] ='<li><a href="Report/obDownload/" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Download" class="glyphicon glyphicon-download-alt">&nbsp;Download All</span></a></li>';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$fieldset = $this->configmodel->getFields('47',$bid);
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && !in_array($field['fieldname'],array('recordfile','calltime','duration'))){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) { $formFields[] = array(
						'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
								 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
						'field'=>
							($field['fieldname']=='eid')
								?form_dropdown('eid',$this->groupmodel->employee_list(),'',"class='form-control'")
								:form_input(array(
									'name'      => $field['fieldname'],
									'id'        => $field['fieldname'],
									'class'		=>($field['fieldname']=="createdon" || $field['fieldname']=="lastmodified"|| $field['fieldname']=="convertedon")?'datepicker_leads form-control':'form-control'
									))
						);
					}
				}		
		}
		$data['links']= $links;
		$data['downlink']= $dlink;
		$data['form'] = array(
					'open'=>form_open_multipart(site_url('OutboundReport/'),array('name'=>'outboundcall','class'=>'form','id'=>'outboundcall','method'=>'post')),
					'form_field'=>$formFields,
					'parentids'=>$parentbids,
					'adv_search'=>array(),
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close(),
					'title'=>$this->lang->line('level_search')
					);
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | Outbound Calls ";
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function obDownload(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$fields = $this->configmodel->getFields(47,$bid);
		$data=array('systemfields'=>$this->configmodel->getFields(47,$bid),
					'roleDetail'=>$this->roleDetail,
					 'attributes' => array('class' => 'form', 'id' =>'obdown','name'=>'obdown'),
					 'URL' => "OutboundReport/",
					 'bid' => $bid
					 );
		$this->load->view('obDownload',$data);
	}
	function callMe(){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$roleDetail = $this->roleDetail;	
		if(!$roleDetail['modules']['25']['opt_view']) redirect('Employee/access_denied');
		$data = array();
		$formFields = array();
		$data=$this->data;
		$clientid = $this->reportmodel->getClientid($bid);
		$data['form'] = array(
						'open'=>form_open_multipart('api/callMe/'.$clientid
									,array('name'=>'connectcall','class'=>'form','id'=>'connectcall','method'=>'post')
									,array('bid'=>$bid)
									),
						'fields'=>$formFields,
						'close'=>form_close(),
					);
		$this->load->view('c2cdailer',$data);
	}
	
}
/* end of report controller */



