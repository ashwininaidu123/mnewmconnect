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
	    $to=$calid;
		$data['module']['title'] = "Send SMS";
		$data['links'] = '';
		$data['nosearch']=true;
		$data['paging'] = '';
		$formFields[] = array(	'label'=>'<label for="comment" class="col-sm-4 text-right" name="to">To : </label>',
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
					'form_attr'=>array('action'=>'Report/Send_SMS/','id'=>'sendsms','name'=>'sendsms','enctype'=>"multipart/form-data"),
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
		$set_array=array(   "contentid"=>(isset($reply[0]))?$reply[0]:'',
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
				case 'referrals':
						$redirect="Referrals/0";
						break;
				case 'sitevisit':
						$redirect="SiteVisits/0";
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
   function mcubecalls($callid,$module){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$ret = '0';
		if($module!='' && $callid != 0){
			$ret = $this->reportmodel->getDetail($bid,$callid,$module);
		}
		echo $ret;
	}

	function converttolead($calid,$source){
		$bid=$this->session->userdata('bid');
		$to='';
	    $to=$calid;
		$data['module']['title'] = "Send SMS";
		$data['links'] = '';
		$data['nosearch']=true;
		$data['paging'] = '';
		$formFields[] = array(	'label'=>'<label for="comment" class="col-sm-4 text-right" name="to">To : </label>',
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
					'form_attr'=>array('action'=>'Report/Send_SMS/','id'=>'sendsms','name'=>'sendsms','enctype'=>"multipart/form-data"),
					'fields'=>$formFields,
					'hidden'=>array('to'=>$to,'source'=>$source),
					'parentids'=>'',
					'busid'=>'',
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close()
				);
		$this->load->view('form_view2',$data);
	}
}
/* end of report controller */



