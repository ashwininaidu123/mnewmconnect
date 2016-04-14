<?php
Class Supportmodel extends Model
{
	function Supportmodel(){
		 parent::Model();
		 $this->load->model('systemmodel');
	}
	function listSupportTkt($supstatus,$ofset='0',$limit='20',$tab){
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$modid = '40' ;
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$q= '';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		if(isset($s['module']) && $s['module'] != 'support'){
			$this->session->unset_userdata('search');
			$s = array();
		}
		if(isset($_POST['Adv_submit'])){
			$this->session->set_userdata('Adsearch',$_POST);
		}else{
			$this->session->unset_userdata('Adsearch');
		}
		if(isset($_POST['sav_search'])){
			Save_search_data($bid,$this->session->userdata('eid'),json_encode($_POST),$modid);
			$this->session->set_userdata('Adsearch',$_POST);
		}
		if($tab>0){
			$qs=get_save_searchrow($bid,$modid,$this->session->userdata('eid'),$tab);
			$content=(array)json_decode($qs['content']);
			$this->session->set_userdata('Adsearch',$content);
		}
		if($this->session->userdata('Adsearch')){
			$Ads = $this->session->userdata('Adsearch');
		}
		if(isset($Ads) && sizeof($Ads)>0){
			$this->session->unset_userdata('search');
			$s = array();
			switch($Ads['timespan']){
				case 'all':
				default :
						 $q.='';	
				break;
				case 'today':
						 $q.=" AND DATE(a.createdon)>= '".date('Y-m-d')."'";
				break;
				case 'last7':
						$date=date('Y-m-d',strtotime('-6 days'));
						$q.=" AND DATE(a.createdon)>= '".$date."'";	
				break;			
				case 'month':
						$date=date('Y-m-01');
						$q.=" AND DATE(a.createdon)>= '".$date."'";	
				break;				
			}
			if(isset($Ads['multiselect_gid']) && sizeof($Ads['multiselect_gid'])>0){
				$gids=implode(",",$Ads['multiselect_gid']);
				      $q.=" AND a.gid in (".$gids.")";
			}
			if(isset($Ads['multiselect_eids']) && sizeof($Ads['multiselect_eids'])>0){
				$eids=implode(",",$Ads['multiselect_eids']);
				      $q.=" AND (a.assignto IN (".$eids.") OR a.enteredby IN (".$eids.") )";
			}
	 		$custiom_ids=array();
			$cust=0;
			$field_array=array("gid"=>"d.groupname",
			                   "assignto"=>"e.empname",
							   "createdon"=>"date(a.createdon)",
			                   "lastmodified"=>"date(a.lastmodified)",
			                   "name"=>"a.name",
			                   "number"=>"a.number",
			                   "email"=>"a.email",
			                   "caller_add"=>"a.caller_add",
			                   "caller_bus"=>"a.caller_bus",
			                   "refId"=>"a.refId",
			                   "keyword"=>"a.keyword",
			                   "tkt_status"=>"a.tkt_status",
			                   "tkt_criticality"=>"a.tkt_criticality",
			                   "enteredby"=>"e1.empname",
			                   "tkt_level"=>"a.tkt_level",
			                   "tkt_esc_time"=>"a.tkt_esc_time",
			                   "ticket_id"=>"a.ticket_id",
			                   "source"=>"a.source");
			$app='';
			for($n=0;$n<sizeof($Ads['field_d']);$n++){
				if(strstr($Ads['field_d'][$n],'c_')){
					$field_array[$Ads['field_d'][$n]] = $Ads['field_d'][$n];
				}
			}
			for($i=0;$i<sizeof($Ads['field_d']);$i++){
				switch($Ads['equ'][$i]){
					case 1:
						$app=" like '%".$Ads['fval'][$i]."%' ";
					break;
					case 2:
						$app=" not like '%".$Ads['fval'][$i]."%' ";
					break;
					case 4:
						$app=" != '".$Ads['fval'][$i]."' ";
					 break;
					 case 5:
						$app=" > '".$Ads['fval'][$i]."' ";
					 break;
					 case 6:
						$app=" < '".$Ads['fval'][$i]."' ";
					 break;
					 case 7:
						$app=" >= '".$Ads['fval'][$i]."' ";
					break;
					case 8:
						 $app=" <= '".$Ads['fval'][$i]."' ";	
					break;	 
					case 3:
					default:
						$app=" = '".$Ads['fval'][$i]."' ";
					break;
				}
				$q .= (isset($field_array[$Ads['field_d'][$i]])) ? " ".$Ads['cond'][$i]."  ".$field_array[$Ads['field_d'][$i]]."  ".$app : '';
			}
		}
		if(isset($s)){
            $arr = array_keys($s);
            for ($n =0;$n<count($arr);$n++){
                if(strstr($arr[$n],'c_')){
					if(is_array($s[$arr[$n]])){
						$s[$arr[$n]] = @implode(',',$s[$arr[$n]]);
					}
                    $q.=(isset($s[$arr[$n]]) && ($s[$arr[$n]]!='')  && ($s[$arr[$n]]!=' ') && ($s[$arr[$n]]!='0')) ? " AND a.".$arr[$n]." LIKE '%".$s[$arr[$n]]."%'":"";
                }
            }
          }
		//$custiom_ids =   $this->configmodel->customSearch((isset($s['custom']))?$s['custom']:'','40',$bid);
		$q.=(isset($s['ticket_id']) && $s['ticket_id']!='')?" and a.ticket_id = '".$s['ticket_id']."'":"";
		$q.=(isset($s['gid']) && $s['gid']!='')?" and a.gid = '".$s['gid']."'":"";
		$q.=(isset($s['assignto']) && $s['assignto']!='')?" and a.assignto = '".$s['assignto']."'":"";
		$q.=(isset($s['enteredby']) && $s['enteredby']!='')?" and a.enteredby = '".$s['enteredby']."'":"";
		$q.=(isset($s['convertedby']) && $s['convertedby']!='')?" and a.convertedby = '".$s['convertedby']."'":"";
		$q.=(isset($s['name']) && $s['name']!='')?" and a.name like '%".$s['name']."%'":"";
		$q.=(isset($s['number']) && $s['number']!='')?" and a.number ='".$s['number']."'":"";
		$q.=(isset($s['email']) && $s['email']!='')?" and a.email like '%".$s['email']."%'":"";
		$q.=(isset($s['source']) && $s['source']!='')?" and a.source like '%".$s['source']."%'":"";
		$q.=(isset($s['caller_add']) && $s['caller_add']!='')?" and a.caller_add like '%".$s['caller_add']."%'":"";
		$q.=(isset($s['caller_bus']) && $s['caller_bus']!='')?" and a.caller_bus like '%".$s['caller_bus']."%'":"";
		$q.=(isset($s['keyword']) && $s['keyword']!='')?" and a.keyword like '%".$s['keyword']."%'":"";
		$q.=(isset($s['refId']) && $s['refId']!='')?" and a.refId like '%".$s['refId']."%'":"";
		$q.=(isset($s['tkt_status']) && $s['tkt_status']!='0')?" AND a.tkt_status ='".$s['tkt_status']."'":"";
		$q.=(isset($s['tkt_criticality']) && $s['tkt_criticality']!='0')?" AND a.tkt_criticality ='".$s['tkt_criticality']."'":"";
		$q.=(isset($s['tkt_level']) && $s['tkt_level']!='0'&& $s['tkt_level']!='')?" AND a.tkt_level ='".$s['tkt_level']."'":"";
		$q.=(isset($s['tkt_esc_time']) && $s['tkt_esc_time']!='0'&& $s['tkt_esc_time']!='')?" AND a.tkt_esc_time ='".$s['tkt_esc_time']."'":"";
		if(isset($s['createdon_from']) && $s['createdon_from']!='' && isset($s['createdon_to']) && $s['createdon_to']!=''){
			$q .= " and (date(a.createdon) BETWEEN '".$s['createdon_from']."' AND '".$s['createdon_to']."')";
		}else{
			$q.=(isset($s['createdon_from']) && $s['createdon_from']!='')?" and a.createdon >= '".$s['createdon_from']."'":"";
			$q.=(isset($s['createdon_to']) && $s['createdon_to']!='')?" and a.createdon <= '".$s['createdon_to']."'":"";
		}
		if(isset($s['lastmodified_from']) && $s['lastmodified_from']!='' && isset($s['lastmodified_to']) && $s['lastmodified_to']!=''){
			$q .= " and (date(a.lastmodified) BETWEEN '".$s['lastmodified_from']."' AND '".$s['lastmodified_to']."')";
		}else{
			$q.=(isset($s['lastmodified_from']) && $s['lastmodified_from']!='')?" and a.lastmodified >= '".$s['lastmodified_from']."'":"";
			$q.=(isset($s['lastmodified_to']) && $s['lastmodified_to']!='')?" and a.lastmodified <= '".$s['lastmodified_to']."'":"";
		}
		//$q.=(strlen($custiom_ids)>1)?" AND a.tktid in(".$custiom_ids.")":"";
		//$q.=(!$custiom_ids)?"  ":"";
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$ge = " ";		
		if($roleDetail['role']['admin']!=1){
			if($roleDetail['role']['roleid']==4){
				$q .= " AND ge.eid is not null ";
				$ge = " LEFT JOIN ".$bid."_support_grpemp ge ON (d.gid=ge.gid AND ge.eid= '".$this->session->userdata('eid')."') ";
			}else{
				$q .= " AND (a.enteredby='".$this->session->userdata('eid')."' OR d.eid='".$this->session->userdata('eid')."' OR a.assignto='".$this->session->userdata('eid')."')";
			}
		}
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND a.pulse > '0'";
		}
		if($supstatus != 0 )
			$q .= " AND a.tkt_status = ".$supstatus ;
		$sql = "SELECT SQL_CALC_FOUND_ROWS a.`tktid` 
				FROM ".$bid."_support_tickets a 
				LEFT JOIN ".$bid."_support_groups d on a.gid=d.gid 
				LEFT JOIN ".$bid."_employee e on a.assignto=e.eid 
				LEFT JOIN ".$bid."_employee e1 on a.enteredby=e1.eid
				 $ge 
				WHERE a.status!='2' $q ORDER BY a.`tktid` DESC LIMIT $ofset,$limit";//a.`lastmodified` DESC,
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']==$modid){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$notinarray = array();
		$escProcess = $this->systemmodel->getSupEscBusiness();
		if($escProcess != 1)
			array_push($notinarray,'tkt_esc_time');
		$fieldset = $this->configmodel->getFields($modid,$bid);
		$keys = array();
		$header = array('#',"<a href='javascript://'><span id='c_all' class='glyphicon glyphicon-gok'></span></a>");
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){
					if($f['fieldid']==$field['fieldid'])$checked = true;
				}
				if($checked && !in_array($field['fieldname'] ,$notinarray)) {
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
		//array_push($keys,'dialstatus');
		//array_push($header,'Dial Status');
		array_push($header,'Remark');
		array_push($header,'Comments');
		array_push($header,'Distribution Type');
		array_push($header,'Alert');
		array_push($header,'Counter');
		if($opt_add || $opt_view || $opt_delete)
			array_push($header,$this->lang->line('level_Action'));
		$ret['header'] = $header;
		$list = array();
		$i = $ofset+1;
		foreach($rst as $rec){
			$data = array($i);
			$v = '<input type="checkbox" class="blk_check" name="blk[]" value="'.$rec['tktid'].'"/>';	
			array_push($data,$v);
			$r = $this->configmodel->getDetail($modid,$rec['tktid'],'',$bid);
			$callCnt = $this->callcount($rec['tktid']);
			$dis_type = array("0"=>"Uncategorized","1"=>"Manual","2"=>"Auto");
			$alert_type = array("0"=>" No ","1"=>" Email Alert ","2"=>" SMS Alert ","3"=>" Both ");
			foreach($keys as $k){
				if($k=='assignto'){
					$v = '<a href="Employee/activerecords/'.$r['assignto'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r['assignempname'].'</a>';
				}elseif($k=='enteredby'){	
					$v = '<a href="Employee/activerecords/'.$r['enteredby'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r['enteredempname'].'</a>';
				}elseif($k=='gid'){
					$v = '<a href="support/actSupportGrp/'.$r['gid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r['groupname'].'</a>';
				}elseif($k=='tkt_level'){
					$v = $r['level'];
				}elseif($k == 'tkt_status'){
					$v = $r['status'];
				}elseif($k == 'tkt_criticality'){
					$v = $r['type'];
				}else{
						$v = isset($r[$k])?nl2br(wordwrap($r[$k],80,"\n")):"";	
				}
				array_push($data,$v);
			}
			$v = $this->getRemarkById($rec['tktid'],$bid,1);
			array_push($data,nl2br(wordwrap($v,80,"\n")));
			$v = $this->getCommentById($rec['tktid'],$bid,1);
			array_push($data,nl2br(wordwrap($v,80,"\n")));
			$v = (isset($r['dis_type']) && $r['dis_type'] != '') ? $dis_type[$r['dis_type']]: '';
			array_push($data,$v);
			$v = (isset($r['alert_type']) && $r['alert_type'] != '') ? $alert_type[$r['alert_type']]: '';
			array_push($data,$v);
			$v = (isset($callCnt) && $r['source'] == 'Calltrack') ? "<a href=\"Javascript:void(null)\" onClick=\"window.open('/support/callHistory/".$rec['tktid']."', 'Counter', 'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=980,height=700,resizable=1')\">".$callCnt.'</a>': '';
			array_push($data,$v);
			if($opt_add || $opt_view || $opt_delete){
				$act = ($opt_add) ?'<a href="EditSupTkt/'.$rec['tktid'].'"><span title="Edit" class="fa fa-edit"></span></a>':'';
				$act .= ($opt_delete) ? '<a href="'.base_url().'support/delSupportTkt/'.$rec['tktid'].'" class="deleteClass"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
				$act .= '<a href="support/activeSupportTkt/'.$rec['tktid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="fa fa-file-text"  title="Support Ticket Details"></span></a>';
				$act .= ($roleDetail['role']['accessrecords']=='0') ? 
					(($r['filename']!='' && file_exists('sounds/'.$r['filename']))
					?'<a target="_blank" href="'.site_url('sounds/'.$r['filename']).'"><span title="Sound" class="fa fa-volume-up"></span></a>'
					:'<span class="glyphicon glyphicon-volume-off"></span>')
					:"";
				$act .= '<a href="Report/followup/'.$rec['tktid'].'/0/support" class="btn-followup" data-toggle="modal" data-target="#modal-followup"><img src="system/application/img/icons/comments.png" style="vertical-align:top;" title="Followups" width="16" height="16" /></a>';	
				$act .= ($r['email']!='')?"<a href=\"Javascript:void(null)\" onClick=\"window.open('/Email/compose/".$rec['tktid']."/support', 'Counter', 'toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=950,height=480,resizable=1')\">&nbsp;<span title='Send Mail' class='fa fa-envelope'></span></a>":'&nbsp;<span title="Send Mail" class="fa fa-envelope"></span>';
				$act .= anchor("Report/clicktoconnect/".$rec['tktid']."/5", '<span title="click To Connect" class="fa fa-phone"></span>',array('class'=>'clickToConnect'));
				$act .= anchor("Report/sendSms/".$rec['tktid']."/support", '&nbsp;<span title="Click to send SMS" class="glyphicon glyphicon-comment"></span>','class="clickToSMS" data-toggle="modal" data-target="#modal-empl"');	
				$act .= "<a href=\"Javascript:void(null)\" onClick=\"window.open('Report/sendFields/".$rec['tktid']."/".$bid."/40/support', 'Send Fields', 'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=550,height=500,left=200,top=20,resizable=1')\">&nbsp;<span title='Click to Send Fields' class='fa fa-list-alt'></span></a>";
				$act .= anchor("support/comments/".$rec['tktid'], '<span title="Support Ticket Comments" class="fa fa-comment"></span>','class="btn-danger" data-toggle="modal" data-target="#modal-responsive"');
				$act .= anchor("support/remarks/".$rec['tktid']."/", ' <img src="system/application/img/icons/remarks.png" title="Remarks" style="vertical-align:top;" width="16" height="16">',' class="btn-danger" data-toggle="modal" data-target="#modal-responsive"');
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function editSupportTkt($id,$bid){
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$modid = '40' ;
		$itemDetail = $this->configmodel->getDetail($modid,$id,'',$bid);
		$return = 1;
		$rate=0;
		$val='';
		$arr=array_keys($_POST);
		$assempid = 0;
		$gid = $this->input->post('gid');
		if(is_numeric($this->input->post('assignto'))){
			 $assempid = $this->input->post('assignto');
			 $dis_type = 1;
		 }elseif($roleDetail[$modid]['fieldname'] == 'assignto'){
			$rule = $this->db->query("SELECT group_rule as rule FROM ".$bid."_support_groups WHERE gid='".$gid."'")->row()->rule;
			if($rule == '2'){
				$resultemp = $this->db->query("SELECT e.eid,COALESCE(((weight/(SELECT sum(weight) FROM ".$bid."_support_grpemp WHERE gid=ge.gid))-(counter/(SELECT sum(counter) FROM ".$bid."_support_grpemp WHERE gid=ge.gid))),0) as pc FROM ".$bid."_employee e LEFT JOIN ".$bid."_support_grpemp ge on e.eid=ge.eid WHERE ge.gid='".$gid."' AND ge.status ='1' AND e.status = '1' ORDER BY pc DESC LIMIT 0,1")->result_array();
				if(count($resultemp) > 0)
					$assempid = $resultemp[0]['eid'];
			}elseif($rule == '1'){
				$eid = $this->db->query("SELECT ge.eid FROM ".$bid."_support_grpemp ge LEFT JOIN ".$bid."_employee e ON ge.eid = e.eid WHERE ge.gid='".$this->input->post('gid')."' AND ge.bid='".$bid."' AND ge.status = 1 AND e.status = 1 ORDER BY ge.counter ASC LIMIT 0,1")->result_array();
				if(count($eid) > 0){
					$assempid = $eid[0]['eid'];
				}	
			}
			$dis_type = 2;
		}for($i=0;$i<sizeof($arr);$i++){
			 if(!in_array($arr[$i],array("update_system","autoAssign","comments","refId","httpRefer","createdon","assignto","gid"))){
				 if($_POST[$arr[$i]]!=""){$val=$_POST[$arr[$i]];}else{$val='';}
                    $this->db->set($arr[$i],$val);
				}
	}
		isset($_POST['auto_followup']) ? '' : $this->db->set('auto_followup',0);
		(isset($assempid) && $assempid != 0 ) ? $this->db->set('assignto',$assempid) : '';
		$this->db->set('lastmodified',date("Y-m-d H:i:s"));
		$this->db->where('tktid',$id);
		$this->db->update($bid.'_support_tickets');
		$ticketnumber = $itemDetail['ticket_id'];
		if($itemDetail['tkt_status'] != $_POST['tkt_status']){
			$smschk = $this->db->query("SELECT sms,smscontent FROM ".$bid."_support_status WHERE sid='".$_POST['tkt_status']."'")->row_array();
			if($smschk['sms'] == 1 && $smschk['smscontent'] != ''){
				$smsbal = $this->configmodel->smsBalance($bid);
				if($smsbal > 0){
					$api = "http://115.249.28.90/sms/sendSMS.php?from=vmc.in";
					$remsg = str_replace('@ticketid@',$ticketnumber,$smschk['smscontent']);
					$sms = $api."&to=".substr($this->input->post('number'),-10,10)."&text=".urlencode();
					$sms = file($sms);
					$this->configmodel->smsDeduct($bid,'1');
				}else{
					$return = '3';
				}
			}
		}
		$this->auditlog->auditlog_info('Support ',$id." - ".$ticketnumber. " Support Ticket Details updated by ".$this->session->userdata('username'));
		if(trim($this->input->post('comments'))!= ''){
			$this->db->set('bid',$bid);
			$this->db->set('tktid',$id);
			$this->db->set('cdate',date("Y-m-d H:i:s"));
			$this->db->set('eid',$this->session->userdata('eid'));
			$this->db->set('comment',$this->input->post('comments'));
			$this->db->insert($bid.'_support_comments'); 
		}
		if(trim($this->input->post('remark')) != ''){
			$this->db->set('bid',$bid);
			$this->db->set('tktid',$id);
			$this->db->set('cdate',date("Y-m-d H:i:s"));
			$this->db->set('eid',$this->session->userdata('eid'));
			$this->db->set('remark',$this->input->post('remark'));
			$this->db->insert($bid.'_support_remarks');
		}
		$sql = $this->db->query("SELECT empemail,empnumber,empname FROM ".$bid."_employee WHERE eid='".$assempid."'")->result_array();
		if(isset($_POST['alert_type']) && ($this->input->post('alert_type') =='3' || $this->input->post('alert_type') =='1')){
			$message = "You have assigned to New Support Ticket (".$ticketnumber."). Ticket Details are <br/>";
			$message .= "<br/>Name:".$this->input->post('name');
			$message .= "<br/>Email:".$this->input->post('email');
			$message .= "<br/>Number:".$this->input->post('number');
			$message .= "<br/>Address:".$this->input->post('caller_add');
			$message .= "<br/>Business:".$this->input->post('caller_bus');
			$message .= "<br/>Remarks:".$this->input->post('remark');
			$message .= "<br/>Comments:".$this->input->post('comments');
			$message .= "<br/>Assigned By:".$this->empgetname($this->session->userdata('eid'));
			$body = $this->emailmodel->newEmailBody($message,$sql[0]['empname']);
			$to  = $sql[0]['empemail']; 
			$subject = ' Assigned Support Ticket details ';
			$this->load->library('email');
			$this->email->from('noreply@mcube.com', 'MCube');
			$this->email->to($to);
			$this->email->subject($subject);
			$this->email->message($body);
			$this->email->send();
		}
		//SMS
		if(isset($_POST['alert_type']) && ($this->input->post('alert_type') =='3' || $this->input->post('alert_type') =='2')){
			$smsbal = $this->configmodel->smsBalance($bid);
			if($smsbal > 0){
				$message = "Your New assigned Ticket (".$ticketnumber.") Details are ";
				$message .= " no:".$this->input->post('number')." and Name:".$this->input->post('name').".Powered By Mcube";
				$api = "http://115.249.28.90/sms/sendSMS.php?from=vmc.in";
				$sms = $api."&to=".substr($sql[0]['empnumber'],-10,10)."&text=".urlencode($message);
				$sms = file($sms);
				$this->configmodel->smsDeduct($bid,'1');
			}else{
				$return = '3';
			}
		}
		//~ if(isset($_POST['custom'])){
			//~ $modid = '40';
			//~ foreach($_POST['custom'] as $fid=>$val){
				//~ $this->db->query("DELETE FROM ".$bid."_customfieldsvalue where bid= '".$bid."' and modid= '".$modid."' and fieldid= '".$fid."' and dataid= '".$id."'");
				//~ $sql = "REPLACE INTO ".$bid."_customfieldsvalue SET
						 //~ bid			= '".$bid."'
						//~ ,modid			= '".$modid."'
						//~ ,fieldid		= '".$fid."'
						//~ ,dataid			= '".$id."'
						//~ ,value			= '".(is_array($val)?implode(',',$val):$val)."'";
				//~ $this->db->query($sql);
			//~ }
		//~ }
		if(isset($_POST['custom'])){
			$modid = '40';
			$fids = array_keys($_POST['custom']);
			$sql = "SELECT * FROM ".$bid."_customfields WHERE fieldid in (".implode(',',$fids).") 
					AND fieldtype in ('checkbox','dropdown','radio')";
			$fields = $this->db->query($sql)->result_array();
			if(count($fields)>0){
				$itemDetailNew = $this->configmodel->getDetail($modid,$id,'',$bid);
				$fieldset = $this->configmodel->getFields($modid,$bid);
				$data = array();
				foreach($fieldset as $field){
					if($field['type']=='s' && !$field['is_hidden']){
						$data[$field['customlabel']]=$itemDetailNew[$field['fieldname']];
					}elseif($field['type']=='c' && $field['show']){
						$data[$field['customlabel']]=$itemDetailNew['custom['.$field['fieldid'].']'];
					}
				}
			}
			foreach($fields as $fid){
				$cf = (is_array($_POST['custom'][$fid['fieldid']])
						?implode(',',$_POST['custom'][$fid['fieldid']])
						:$_POST['custom'][$fid['fieldid']]);
				if($cf!=$itemDetail['custom['.$fid['fieldid'].']']){
					$set = explode("\n",$fid['options']);
					foreach($set as $s){
						$val = explode("|",$s);
						if(count($val)>1 && $val['0']==$cf){
							$url = $val[1];
							$data = 'data='.urlencode(json_encode($itemDetailNew));
							$objURL = curl_init($url);
							curl_setopt($objURL, CURLOPT_RETURNTRANSFER, 1); 
							curl_setopt($objURL,CURLOPT_POST,1);
							curl_setopt($objURL, CURLOPT_POSTFIELDS,$data);
							$retval = trim(curl_exec($objURL));
							curl_close($objURL);
						}
					}
				}
			}
		}
		return $return;
	}
	function delSupportTkt($id,$bid){
		$this->db->set('status', '2');
		$this->db->where('tktid',$id);
		$this->db->update($bid."_support_tickets");
		$supemp_use = $this->configmodel->supusageCheck($bid,'support');
		if($supemp_use['type'] == 1)
			$this->db->query("UPDATE  business_support_use SET `supportlimit`=(`supportlimit`+1) WHERE bid='".$bid."'");
		$this->auditlog->auditlog_info('Support',$id. " Deleted By ".$this->session->userdata('username'));
		return 1;	
	}
	function delSupTktList($bid,$ofset='0',$limit='20',$type='a'){//echo $limit;
		$q= '';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
        //$custiom_ids=$this->configmodel->customSearch((isset($s['custom']))?$s['custom']:'','40',$bid);
		$q.=(isset($s['gid']) && $s['gid']!='')?" AND a.gid = '".$s['gid']."'":"";
		$q.=(isset($s['assignto']) && $s['assignto']!='')?" AND a.assignto = '".$s['assignto']."'":"";
		$q.=(isset($s['enteredby']) && $s['enteredby']!='')?" AND a.enteredby = '".$s['enteredby']."'":"";
		$q.=(isset($s['name']) && $s['name']!='')?" AND a.name LIKE '%".$s['name']."%'":"";
		$q.=(isset($s['email']) && $s['email']!='')?" AND a.email LIKE '%".$s['email']."%'":"";
		$q.=(isset($s['createdon']) && $s['createdon']!='')?" AND date(a.createdon)>= '".$s['createdon']."'":"";
		$q.=(isset($s['lastmodified']) && $s['lastmodified']!='')?" AND date(a.lastmodified)<= '".$s['lastmodified']."'":"";
		$q.=(isset($s['source']) && $s['source']!='')?" AND a.source LIKE '%".$s['source']."%'":"";
		//$q.=(strlen($custiom_ids)>1)?" AND a.tktid IN(".$custiom_ids.")":"";
		//$q.=(!$custiom_ids)?" AND 0 ":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (a.assignto='".$this->session->userdata('eid')."' OR a.enteredby='".$this->session->userdata('eid')."'  or d.eid='".$this->session->userdata('eid')."')";
		}
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND a.pulse > '0'";
		}
		$sql = "SELECT a.* FROM ".$bid."_support_tickets a 
				LEFT JOIN ".$bid."_support_groups d ON a.gid=d.gid 
				WHERE a.status='2' $q";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='40'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('40',$bid);
		$keys = array();
		$header = array('#');
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
		if($opt_add || $opt_view || $opt_delete)
			array_push($header,$this->lang->line('level_Action'));
		$ret['header'] = $header;
		$list = array();
		$i = $ofset+1;
		foreach($rst as $rec){
			$data = array($i);
			$r = $this->configmodel->getDetail('40',$rec['tktid'],'',$bid);
			foreach($keys as $k){
				if($k=='assignto'){
					$v = '<a href="Employee/activerecords/'.$r['assignto'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r['assignempname'].'</a>';
				}elseif($k=='enteredby'){
					$v = '<a href="Employee/activerecords/'.$r['enteredby'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r['enteredempname'].'</a>';
				}elseif($k=='gid'){
					$v = '<a href="support/actSupportGrp/'.$r['gid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r['groupname'].'</a>';
				}else{
					$v = isset($r[$k])?nl2br(wordwrap($r[$k],80,"\n")):"";	
				}
				array_push($data,$v);
			}
			if($opt_add || $opt_view || $opt_delete){
				$act = '<a href="'.base_url().'support/undelSupportTkt/'.$r['tktid'].'"><img src="system/application/img/icons/undelete.png" style="vertical-align:top;" title="Restore" /></a>';
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function undelSupportTkt($tktid,$bid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$this->db->set('status','1');
		$this->db->where('tktid',$tktid);
		$this->db->update($bid."_support_tickets");
		$supemp_use = $this->configmodel->supusageCheck($bid,'support');
		if($supemp_use['type'] == 1)
			$this->db->query("UPDATE  business_support_use SET `supportlimit`=(`supportlimit`-1) WHERE bid='".$bid."'");
		return true;
	}
	function addSupportTkt($type=''){
		$type = ($type == '') ? 1 : $type;
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$arr=array_keys($_POST);
		$tkt_use = $this->configmodel->supusageCheck($bid,'support');
		if($tkt_use['type'] == 1 && $tkt_use['used'] == 0 && $id == ''){
			return '2';
		}else{
			$assempid = 0;
				if($this->input->post('assignto') != '' && $this->input->post('assignto') !=0){
					$assempid = $this->input->post('assignto');
				}else{
					if($this->input->post('gid') != ''){
						$rule = $this->db->query("SELECT group_rule as rule FROM ".$bid."_support_groups WHERE gid='".$this->input->post('gid')."'")->row()->rule;
						if($rule == '2'){
							$resultemp = $this->db->query("SELECT e.eid,COALESCE(((weight/(SELECT sum(weight) FROM ".$bid."_support_grpemp WHERE gid=ge.gid))-(counter/(SELECT sum(counter) FROM ".$bid."_support_grpemp WHERE gid=ge.gid))),0) as pc FROM ".$bid."_employee e LEFT JOIN ".$bid."_support_grpemp ge on e.eid=ge.eid WHERE ge.gid='".$this->input->post('gid')."' AND ge.status = '1' AND e.status = '1' ORDER BY pc DESC LIMIT 0,1")->result_array();
							if(count($resultemp) > 0){
								$assempid = $resultemp[0]['eid'];
							} 
						}elseif($rule == '1'){
							$eid = $this->db->query("SELECT ge.eid FROM ".$bid."_support_grpemp ge LEFT JOIN ".$bid."_employee e ON ge.eid = e.eid WHERE ge.gid='".$this->input->post('gid')."' AND ge.bid='".$bid."' AND ge.status = '1' AND e.status = '1' ORDER BY ge.counter ASC LIMIT 0,1")->row()->eid;
							if($eid != ''){
								$assempid = $eid;
							}
						}
					}
				}
				$tktid = $this->db->query("SELECT COALESCE(MAX(`tktid`),0)+1 as id FROM ".$bid."_support_tickets")->row()->id;
				$ticket_id = $this->db->query("SELECT COALESCE(MAX(`ticket_id`),0)+1 as tktid FROM ".$bid."_support_tickets")->row()->tktid;
				$this->db->set('tktid',$tktid);
				for($i=0;$i<sizeof($arr);$i++){
					if($arr[$i]!="update_system" && $arr[$i]!="custom" && $arr[$i]!="autoAssign"){
						if($_POST[$arr[$i]]!=""){$val=$_POST[$arr[$i]];}else{$val='';}
						$this->db->set($arr[$i],$val);
					}
				}
				for($i=0;$i<sizeof($arr);$i++){
				   if(!in_array($arr[$i],array("update_system","autoAssign","auto_followup","tkt_esc_time","tkt_level","refId",
				   "caller_bus","caller_bus","createdon","source","number","email","name","assignto","gid"))){
						/* Changed for custom fields */
						if(is_array($_POST[$arr[$i]]))
							$val = @implode(',',$_POST[$arr[$i]]);
						elseif($_POST[$arr[$i]]!="")
							$val=$_POST[$arr[$i]];
						else
							$val='';
						$this->db->set($arr[$i],$val);
					}
				}
				$this->db->set('assignto',$assempid);
				$this->db->set('enteredby',$this->session->userdata('eid'));
				$this->db->set('createdon',$this->input->post('createdon'));
				$this->db->set('lastmodified',date('Y-m-d H:i:s'));
				$this->db->set('status',1);
				$this->db->set('tkt_status',$type);
				$this->db->set('ticket_id',$ticket_id);
				$this->db->insert($bid."_support_tickets");
				if($tkt_use['type'] == 1)
					$this->db->query("UPDATE business_support_use SET `supportlimit`=(`supportlimit`-1) WHERE bid='".$bid."'");
				$this->db->query("UPDATE ".$bid."_support_grpemp SET `counter`=(`counter`+1) WHERE eid='".$assempid."' AND gid='".$this->input->post('gid')."'");
				$this->auditlog->auditlog_info('Support',$this->input->post('name'). " New Support Ticket added to List ");
				return '1';
		}
	}
	function supportTktCSV($bid){
		$res=array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$q= " WHERE a.status!=2 ";
		if(!empty($_POST['group_name'])){
			if($_POST['group_name'][0]!=""){
				$q.=" AND a.gid in (".implode(",",$_POST['group_name']).")";
			}
		}
		if($_POST['endtimes']!=""){
			$q.=" AND date(a.createdon)<='".$_POST['endtimes']."'" ;
		}
		if($_POST['starttimes']!=""){
			$q.=" AND date(a.createdon)>='".$_POST['starttimes']."'";
		}
		if(!empty($_POST['emp_name'])){
			if($_POST['emp_name'][0]!=""){
			$q.=" AND a.assignto IN(".implode(",",$_POST['emp_name']).")";
			}
		}
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (a.enteredby='".$this->session->userdata('eid')."'  OR a.assignto='".$this->session->userdata('eid')."' or d.eid='".$this->session->userdata('eid')."')";
		}
		$limit = $roleDetail['role']['recordlimit'];
		$csv_output = "";
		foreach($_POST['lisiting'] as $key=>$fiels){
			if($key=='custom'){
				foreach($fiels as $key=>$fiels){
					$header[]=$fiels;
					$hkey[]='custom['.$key.']';
				}
			}else{
				$hkey[]=$key;
				$header[]=$fiels;
			}
		}
		$csv_output .= implode(",",$header)."\n";
		$sql="SELECT SQL_CALC_FOUND_ROWS a.tktid
				FROM ".$bid."_support_tickets a
				LEFT JOIN ".$bid."_employee c ON a.assignto=c.eid 
				LEFT JOIN ".$bid."_support_groups d ON a.gid=d.gid $q
				ORDER BY a.createdon DESC
				LIMIT 0,$limit";		 
		$rst = $this->db->query($sql)->result_array();
		$name = $bid.'_'.$this->session->userdata('eid').'_'.time();
		mkdir('reports/'.$name);
		chmod('reports/'.$name,0777);
		$files = array();
		foreach($rst as $rec){
			$data = array();
			$r = $this->configmodel->getDetail('40',$rec['tktid'],'',$bid);
			$i=0;
			foreach($hkey as $k){
				if(@in_array($k,array('gid','assignto','enteredby'))){
					$v=($k=='gid')?$r['groupname']:((($k=='enteredby')) ? $r['enteredempname'] : $r['assignempname']);
				}elseif(@in_array($k,array('tkt_status','tkt_criticality'))){
					$v=($k=='tkt_status')?$r['status']:$r['type'];
				}elseif($k=='tkt_level'){
					$v = $r['level'];
				}elseif($k == "comments"){
					$v = $this->tktComments($rec['tktid'],$bid);
				}elseif($k == "remarks"){
					$v = $this->tktRemarks($rec['tktid'],$bid);
				}else{
					$v=(isset($r[$k])) ? '"'.$r[$k].'"' : '';
				}
				array_push($data,$v);
			}
			$csv_output .= @implode(",",$data)."\n";
		}
		$data_file = 'reports/'.$name.'/support_tickets.csv';
		$fp = fopen($data_file,'w');fwrite($fp,$csv_output);fclose($fp);
		chdir('reports')."<br>";
		exec('zip -r '.$name.'.zip '.$name);
		exec('rm -rf '.$name);
		return $name;
	}
	function getSupTktSysfields($bid,$modid){
		$sql = "DESC ".$bid."_support_tickets";
		$rst = $this->db->query($sql)->result_array();
		foreach($rst as $r){
			if(! @in_array($r['Field'],array('bid','tktid','gid','enteredby','createdon','lastmodified','status','tkt_level','dis_type','alert_type','convertedby','tkt_esc_time','ticket_id','remark','refId','tkt_status','tkt_criticality')))
				$result[$r['Field']] = $this->lang->line('mod_'.$modid)->$r['Field'];
		}
		return $result;
	}
	function Systemfields(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql = "DESC ".$bid."_support_tickets";
		$rst = $this->db->query($sql)->result_array();
		foreach($rst as $r){
			$result[$r['Field']] = $r['Field'];
		}
		return $result;
	}
	function getSupTktCustfields(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql = "SELECT fieldname FROM ".$bid."_customfields WHERE modid='40' AND bid='".$bid."'";
		$rst = $this->db->query($sql)->result_array();
		if(!empty($rst)){
			foreach($rst as $r){
				$result[$r['fieldname']] = $r['fieldname'];
			}
		}else{
			$result = array();
		}
		return $result;
	}
	function addImportSupTkt($data){
		$bid=$data['bid'];
		$tkt_use = $this->configmodel->supusageCheck($bid,'support');
		if($tkt_use['type'] == 1 && $tkt_use['used'] == 0){
			return '2';
		}else{
			$vnum = $vemail = 0;
			if(!(@array_key_exists("number",$data)) && !(@array_key_exists("email",$data))){
				return 0;
			}
			if(preg_match("/^[0-9]+$/",$data['number']) && intval($data['number']) != 0){
				$vnum = 1;
			}else{
				$data['number']='';
			}
			if(preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $data['email'])){
				$vemail = 1;
			}else{
				$data['email']='';
			}
			if($vnum =='0' && $vemail=='0'){
				return 0;
			}else{
				$tktid=$this->db->query("SELECT COALESCE(MAX(`tktid`),0)+1 as id FROM ".$bid."_support_tickets")->row()->id;
				$this->db->set('tktid',$tktid);
				$ticktid=$this->db->query("SELECT COALESCE(MAX(`ticket_id`),0)+1 as tktid FROM ".$bid."_support_tickets")->row()->tktid;
				$this->db->set('ticket_id',$ticktid);
				$fields = $this->Systemfields();
				foreach($data as $k=>$v){
					if(@in_array($k,$fields)){
						if($k != '' || $v != '' ){
							$this->db->set($k,mysql_real_escape_string($v));
						}
					}
				}
				$this->db->set('status','1');
				$this->db->insert($bid."_support_tickets");
				if($tkt_use['type'] == 1)
					$this->db->query("UPDATE business_support_use SET `supportlimit`=(`supportlimit`-1) WHERE bid='".$bid."'");
				if($data['assignto'] != '' && $data['gid'] != '' )
					$this->db->query("UPDATE ".$bid."_support_grpemp SET `counter`=(`counter`+1) WHERE eid='".$data['assignto']."' AND gid='".$data['gid']."'");
				$cfields = $this->getSupTktCustfields();
				//~ foreach($data as $k=>$v){
					//~ if(@in_array($k,$cfields)){
						//~ $fieldid = $this->db->query("SELECT fieldid as id FROM ".$bid."_customfields WHERE bid='".$bid."' AND fieldname='".$k."' AND modid='40'")->row()->id;
						//~ $this->db->query("DELETE FROM ".$bid."_customfieldsvalue WHERE bid= '".$bid."' AND modid= '40' AND fieldid = '".$fieldid."' AND dataid='".$tktid."'");
						//~ $sql = "INSERT INTO ".$bid."_customfieldsvalue SET
								 //~ bid                  = '".$bid."'
								//~ ,modid                = '40'
								//~ ,fieldid              = '".$fieldid."'
								//~ ,dataid               = '".$tktid."'
								//~ ,value                = '".$v."'";
						//~ $this->db->query($sql);
					//~ }
				//~ }
				if(isset($data['remarks'])){
					if($data['remarks'] != ''){
						$sql = "INSERT INTO ".$bid."_support_remarks SET
								tktid		='".$tktid."',
								bid			='".$bid."',
								eid			='".$this->session->userdata('eid')."',
								remark		='".mysql_real_escape_string($data['remarks'])."'";
						$this->db->query($sql);
					}
				}
				if(isset($data['comments'])){
					if($data['comments'] != ''){
						$sql = "INSERT INTO ".$bid."_support_comments SET
								tktid		='".$tktid."',
								bid			='".$bid."',
								eid			='".$this->session->userdata('eid')."',
								comment		='".mysql_real_escape_string($data['comments'])."'";
						$this->db->query($sql);
					}
				}
			}
			return '1';
		}
	}
	function empAssign($gid){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$result = array();
		$rst = $this->db->query("SELECT eid FROM ".$bid."_support_grpemp WHERE gid=".$gid)->result_array();
		foreach($rst as $r){
			$result[] = $r['eid'];
		}
		return $result;
	}
	function getFollowuplist($id,$type='support'){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$q = '';
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		//$custiom_ids=$this->configmodel->customSearch((isset($s['custom']))?$s['custom']:'','39',$bid);
		//$q.=(strlen($custiom_ids)>1)?" AND a.tktid in(".$custiom_ids.")":"";
		//$q.=(!$custiom_ids)?" AND 0 ":"";
		$where = ($dsh != '' && $dsh == 1) ? " followupdate >= CURRENT_DATE() AND " : " ";
		$sql="SELECT * FROM ".$bid."_followup WHERE ".$where." callid='".$id."' $q  ORDER BY cdate ASC";					 
		$rst = $this->db->query($sql)->result_array();
		$keys = array('cdate',
					  'comment',
					  'followupdate',
					  'reach_time');
		$header = array('#',
						'Comment Date',
						'Comment',
						'Next Followup Date',
						'Notification Time Limit');
		
		$ret['header'] = $header;
		$list = array();
		$i = 1;
		foreach($rst as $rec){
			$data = array($i);
			foreach($keys as $k){
				$v = isset($rec[$k])?$rec[$k]:"";
				array_push($data,$v);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function empAssign_exists($empemail){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$empassignId = $this->db->query("SELECT e.eid FROM ".$bid."_employee e WHERE e.empemail='".$empemail."' AND e.status='1'")->result_array();
		return (@array_key_exists('eid',$empassignId[0])) ? $empassignId[0]['eid'] : NULL;
	}
	
	function empAssigngr_exists($eid){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$empgroupId = $this->db->query("SELECT ge.gid FROM ".$bid."_support_grpemp ge LEFT JOIN ".$bid."_groups g ON ge.gid = g.gid WHERE ge.eid ='".$eid."' AND g.status='1' LIMIT 0,1")->result_array();
		$grID = (@array_key_exists('gid',$empgroupId[0])) ? $empgroupId[0]['gid'] : NULL;
		if($grID == NULL){
			$empgroupId = $this->db->query("SELECT g.gid FROM ".$bid."_support_groups g WHERE g.eid ='".$eid."' AND g.status='1'")->result_array();
			$grID = (@array_key_exists('gid',$empgroupId[0])) ? $empgroupId[0]['gid'] : NULL;
		}
		return $grID;
	}
	
	function bulkDelSupTkt($arr){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$sql="UPDATE ".$bid."_support_tickets SET status=2 WHERE tktid IN(".$arr.")";
		$this->db->query($sql);
		$s=$this->db->query("SELECT name FROM ".$bid."_support_tickets WHERE tktid IN(".$arr.")");
		if($s->num_rows()>0){
			$cnt = $s->num_rows();
			$supemp_use = $this->configmodel->supusageCheck($bid,'support');
			if($supemp_use['type'] == 1){
				$this->db->query("UPDATE  business_support_use SET `supportlimit`=(`supportlimit`+".$cnt.") WHERE bid='".$bid."'");
			}
			foreach($s->result_array() as $row){
				$this->auditlog->auditlog_info('Support',$row['name']. " Deleted By ".$this->session->userdata('username'));
			}	
		}
		return 1;
	}
	function bulkDelSupGrp($arr){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$sql="UPDATE ".$bid."_support_groups SET status=2 WHERE gid IN(".$arr.")";
		$this->db->query($sql);
		$s=$this->db->query("SELECT groupname FROM ".$bid."_support_groups WHERE gid IN(".$arr.")");
		if($s->num_rows()>0){
			foreach($s->result_array() as $row){
				$this->auditlog->auditlog_info('Support',$row['groupname']. " Deleted By ".$this->session->userdata('username'));
			}	
		}
		return 1;
	}
	function bulkAssignEmpTkt(){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$tktids = @explode(',',$this->input->post('ids'));
		if($this->input->post('gid') != '' || $this->input->post('assignto') != ''){
			for($id=0;$id<count($tktids);$id++){
				if($this->input->post('assignto') != '' && $this->input->post('assignto') !=0){
					$assempid = $this->input->post('assignto');
				}else{
					if($this->input->post('gid') != ''){
						$rule = $this->db->query("SELECT group_rule as rule FROM ".$bid."_support_groups WHERE gid='".$this->input->post('gid')."'")->row()->rule;
						if($rule == '2'){
							$resultemp = $this->db->query("SELECT e.eid,COALESCE(((weight/(SELECT sum(weight) FROM ".$bid."_support_grpemp WHERE gid=ge.gid))-(counter/(SELECT sum(counter) FROM ".$bid."_support_grpemp WHERE gid=ge.gid))),0) as pc FROM ".$bid."_employee e LEFT JOIN ".$bid."_support_grpemp ge on e.eid=ge.eid WHERE ge.gid='".$this->input->post('gid')."' AND ge.status = '1' AND e.status = '1' ORDER BY pc DESC LIMIT 0,1")->result_array();
							if(count($resultemp) > 0){
								$assempid = $resultemp[0]['eid'];
							} 
						}elseif($rule == '1'){
							$eid = $this->db->query("SELECT ge.eid FROM ".$bid."_support_grpemp ge LEFT JOIN ".$bid."_employee e ON ge.eid = e.eid WHERE ge.gid='".$this->input->post('gid')."' AND ge.bid='".$bid."' AND ge.status = '1'  AND e.status = '1' ORDER BY counter ASC LIMIT 0,1")->row()->eid;
							if($eid != ''){
								$assempid = $eid;
							}
						}
					}
				}
				if(isset($_POST['gid']))
					$this->db->set('gid',$this->input->post('gid'));
				$this->db->set('assignto',$assempid);
				$this->db->where('tktid',$tktids[$id]);
				$this->db->update($bid."_support_tickets");
				$this->db->query("UPDATE ".$bid."_support_grpemp SET `counter`=(`counter`+1) WHERE eid='".$assempid."' AND gid='".$this->input->post('gid')."'");
			}
			return 1;
		}else{
			return "Please Select Employee or Group";
		}
	}
	function getSupportGrps(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();	
		$sql=$this->db->query("SELECT * FROM ".$bid."_support_groups WHERE status=1");
		$res['']=$this->lang->line('level_select');
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $r)	{
				$res[$r['gid']]=$r['groupname'];
			}
		}
		return $res;
	}
	function supportCsvTest($bid){
		$res=array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$q= " WHERE a.status!=2 ";
		if(!empty($_POST['groupname'])){
			if($_POST['groupname'][0]!=""){
			$q.=" and a.gid in (".implode(",",$_POST['groupname']).")";
			}
		}
		if($_POST['endtimes']!=""){
			$q.=" AND date(a.createdon)<='".$_POST['endtimes']."'" ;
		}
		if($_POST['starttimes']!=""){
			$q.=" and date(a.createdon)>='".$_POST['starttimes']."'";
		}
		if(!empty($_POST['empname'])){
			if($_POST['empname'][0]!=""){
			$q.=" and a.assignto in(".implode(",",$_POST['empname']).")";
			}
		}
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (a.enteredby='".$this->session->userdata('eid')."'  OR a.assignto='".$this->session->userdata('eid')."' or d.eid='".$this->session->userdata('eid')."')";
		}
		$limit = $roleDetail['role']['recordlimit'];
		$csv_output = "";
		echo "<table border=1><tr>";
		foreach($_POST['lisiting'] as $key=>$fiels){
			if($key=='custom'){
				foreach($fiels as $key=>$fiels){
					$header[]=$fiels;
					$hkey[]='custom['.$key.']';
					echo "<th>".$fiels."</th>";
				}
			}else{
				$hkey[]=$key;
				$header[]=$fiels;
				echo "<th>".$fiels."</th>";
			}
			
		}
		echo "</tr>";
		$sql="SELECT SQL_CALC_FOUND_ROWS a.tktid
				FROM ".$bid."_support_tickets a
				LEFT JOIN ".$bid."_employee c ON a.assignto=c.eid 
				LEFT JOIN ".$bid."_groups d ON a.gid=d.gid $q
				ORDER BY a.createdon DESC
				LIMIT 0,$limit";		 
		$rst = $this->db->query($sql)->result_array();
		$files = array();
		foreach($rst as $rec){
			echo "<tr>";
			$data = array();
			$r = $this->configmodel->getDetail('40',$rec['tktid'],'',$bid);
			$i=0;
			foreach($hkey as $k){
				if(in_array($k,array('gid','assignto','enteredby'))){
					$v=($k=='gid')?$r['groupname']:((($k=='enteredby')) ? $r['enteredempname'] : $r['assignempname']);
				}else{
					$v=(isset($r[$k])) ? nl2br(wordwrap($r[$k],80,"\n")): '';
				}
				echo "<td nowrap>".$v."</td>";
			}
			echo "</tr>";
		}
		echo "</table>";
	}
	function addSupportGrp(){
		$arr = array_keys($_POST);
		$bid = $_POST['bid'];
		$id = $_POST['id'];
		$supgr_use = $this->configmodel->supusageCheck($bid,'group');
		if($supgr_use['type'] == 1 && $supgr_use['used'] == 0 && $id ==''){
			return '2';
		}else{
			$sql=$this->db->query("SELECT gid FROM ".$bid."_support_groups WHERE groupname='".$_POST['groupname']."' AND gid !='".$_POST['id']."'");
			if($sql->num_rows()==0){
				for($i=0;$i<sizeof($arr);$i++){
				   if(!in_array($arr[$i],array("update_system","bid","id","grule"))){
						if(is_array($_POST[$arr[$i]]))
							$val = @implode(',',$_POST[$arr[$i]]);
						elseif($_POST[$arr[$i]]!="")
							$val=$_POST[$arr[$i]];
						else
							$val='';
						$this->db->set($arr[$i],$val);
					}
				}
				$this->db->set('groupname',$_POST['groupname']);
				$this->db->set('group_rule',$_POST['grule']);
				$this->db->set('group_desc',$_POST['group_desc']);
				$this->db->set('eid',$_POST['eid']);
				if($id  ==''){
					$id = $this->db->query("SELECT COALESCE(MAX(`gid`),0)+1 as id FROM ".$bid."_support_groups")->row()->id;
					$this->db->set('bid',$bid);
					$this->db->set('status','1');
					$this->db->set('gid',$id);
					$this->db->insert($bid."_support_groups");
					if($supgr_use['type'] == 1)
						$this->db->query("UPDATE business_support_use SET `grplimit`=(`grplimit`-1) WHERE bid='".$bid."'");
				}else{
					for($i=0;$i<sizeof($arr);$i++){
					   if(!in_array($arr[$i],array("update_system","grule","id"))){
							/* Changed for custom fields */
							if(is_array($_POST[$arr[$i]]))
								$val = @implode(',',$_POST[$arr[$i]]);
							elseif($_POST[$arr[$i]]!="")
								$val=$_POST[$arr[$i]];
							else
								$val='';
							$this->db->set($arr[$i],$val);
						}
					}
					$this->db->where('gid',$id);
					$this->db->update($bid."_support_groups");
				}
				return $id;
			}else{
				return '0';
			}
		}
	}
	function listSupportGrp($bid,$ofset,$limit,$type=''){
		$q= '';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q .= ($type == 'del') ? " a.status =2 " : " a.status =1 ";
		$q.=(isset($s['eid']) && $s['eid']!='')?" AND a.eid = '".$s['eid']."'":"";
		$q.=(isset($s['groupname']) && $s['groupname']!='')?" AND a.groupname LIKE '%".$s['groupname']."%'":"";
		$q.=(isset($s['group_desc']) && $s['group_desc']!='')?" AND a.group_desc LIKE '%".$s['group_desc']."%'":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$sql = "SELECT SQL_CALC_FOUND_ROWS a.* FROM ".$bid."_support_groups a
				WHERE $q ORDER BY a.gid DESC 
		        LIMIT $ofset,$limit";  
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='38'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$group_rule = array(""=>"Select","1"=>"Sequential","2"=>"Weighted");
		$fieldset = $this->configmodel->getFields('38',$bid);
		$keys = array();
		$header = array('#',"<a href='javascript://'><span id='c_all' class='glyphicon glyphicon-gok'></span></a>");
			if($opt_add || $opt_view || $opt_delete)
			array_push($header,$this->lang->line('level_Action'));
			array_push($header,'Weekly Report');
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){
					if($f['fieldid']==$field['fieldid'])$checked = true;
				}
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
	
		$ret['header'] = $header;
		$list = array();
		$i = $ofset+1;
		foreach($rst as $rec){
			$data = array($i);
			$v = '<input type="checkbox" class="blk_check" name="blk[]" value="'.$rec['gid'].'"/>';	
			array_push($data,$v);
			if($opt_add || $opt_view || $opt_delete){
				$act = '';
				if($type == 'del'){
					$act .= '<a href="'.base_url().'support/delSupportGrp/'.$rec['gid'].'/1"><img src="system/application/img/icons/undelete.png" style="vertical-align:top;" title="Restore" /></a>';
				}else{
					$act .= ($opt_add) ?'<a href="support/addSupportGrp/'.$rec['gid'].'"><span title="Edit" class="fa fa-edit"></span></a>':'';
					$act .= ($opt_delete) ? '<a href="'.base_url().'support/delSupportGrp/'.$rec['gid'].'" class="deleteClass"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
					$act .= '<a href="AddempSupGroup/'.$rec['gid'].'" ><span class="fa fa-plus" title="Add Employee"></span></a>&nbsp;';
					$act .= '<a href="ListempSupGroup/'.$rec['gid'].'" ><span title="List Employees" class="glyphicon glyphicon-user"></span></a>';
				}
				array_push($data,$act);
			}
			$ret['graph'] = $this->weekly_support($rec['gid']);
			$data['graph'] = $ret['graph'];
			$r = $this->configmodel->getDetail('38',$rec['gid'],'',$bid);
			foreach($keys as $k){
				if($k == 'eid')
					$v = $r['empname'];
				elseif($k == 'group_rule')
					$v = $group_rule[$r[$k]];
				else
					$v = isset($r[$k])?nl2br(wordwrap($r[$k],80,"\n")):"";	
				array_push($data,$v);
			}
		
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function weekly_support($gid){
		$res=array();
		$ret=array();
		$sql=$this->db->query("SELECT COALESCE(COUNT(tktid), 0) AS cnt
							FROM ".$this->session->userdata('bid')."_support_tickets h 
							WHERE (h.`createdon` >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)) AND h.gid = ".$gid." GROUP BY DAY(h.createdon)");
							

		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}
		foreach($res as $item){
		    foreach($item as $im){
	         $ret[] = $im;
        }
	  } 
		return $ret;
	}	
	function delSupportGrp($id,$bid,$type=''){
		$type = ($type =='') ? '2' : $type;
		$this->db->set('status', $type);
		$this->db->where('gid',$id);
		$this->db->update($bid."_support_groups");
		$supgr_use = $this->configmodel->supusageCheck($bid,'group');
		if($supgr_use['type'] == 1)
			$this->db->query("UPDATE  business_support_use SET `grplimit`=(`grplimit`+1) WHERE bid='".$bid."'");
		$this->auditlog->auditlog_info('Support',$id. " Updated By ".$this->session->userdata('username'));
		return 1;
	}
	function addGrpEmp($sgid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$err=0;
		foreach($_POST['emp_ids'] as $eids){
			$check=$this->db->query("SELECT seid FROM ".$bid."_support_grpemp WHERE eid=".$eids." AND gid='".$sgid."'");
			if($check->num_rows()==0){
				$sgremp_use = $this->configmodel->supusageCheck($bid,'employee');
				if($sgremp_use['type'] == 1 && $sgremp_use['used'] == 0 && $id == ''){
					return '2';
				}else{
					$err++;
					$this->db->set('bid', $bid);                       
					$this->db->set('gid', $sgid);                       
					$this->db->set('eid', $eids);                     
					if(isset($_POST['empweight'.$eids])){
						$this->db->set('weight', $this->input->post('empweight'.$eids)); 
					}
					$this->db->set('status',1); 
					$this->db->insert($bid."_support_grpemp");
					$emp_name=$this->get_empname($eids);
					$gname=$this->db->query("SELECT groupname FROM 	".$bid."_support_groups WHERE gid='".$sgid."'")->row()->groupname;
					$this->auditlog->auditlog_info('Group Employee',$emp_name->empname." added to the group ".$gname);
					if($sgremp_use['type'] == 1)
						$query=$this->db->query("UPDATE business_support_use SET `emplimit`=(`emplimit` -1) WHERE bid='".$bid."'");
				}
			}
		}
		if($err!=0){
			return 1;
		}else{
			return 0;
		}
	}
	function supportGrpEmpExist($lgid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();
		$res1=$this->db->query("SELECT SQL_CALC_FOUND_ROWS g.groupname,e.empname,a.eid,a.gid ,a.status
							   FROM ".$bid."_support_grpemp a
							   LEFT JOIN ".$bid."_support_groups g ON a.gid=g.gid
							   LEFT JOIN ".$bid."_employee e ON a.eid=e.eid
							   WHERE a.gid=$lgid");
		if($res1->num_rows()>0){					   
			foreach($res1->result_array() as $row){
				$res[]=$row['eid'];
			}
		}
		return $res;
	}
	function listGrpEmp($sgid,$ofset='0',$limit='20'){
		$q='';
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q= (isset($s['empname']) && $s['empname']!='')?" AND e.empname like '%".$s['empname']."%'":"";
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS g.groupname,e.empname,a.weight,a.counter,a.eid,a.gid,a.status	
							   FROM ".$bid."_support_grpemp a
							   LEFT JOIN ".$bid."_support_groups g on a.gid=g.gid
							   LEFT JOIN ".$bid."_employee e on a.eid=e.eid
							   WHERE a.gid=$sgid $q 
							   ORDER BY e.`empname`
							   LIMIT $ofset,$limit
							   ")->result_array();
       $res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;
	}
	function delGrpEmp($id,$lgid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("DELETE FROM ".$bid."_support_grpemp WHERE eid=$id AND gid=$lgid");
		$supgr_use = $this->configmodel->supusageCheck($bid,'employee');
		if($supgr_use['type'] == 1)
			$this->db->query("UPDATE  business_support_use SET `emplimit`=(`emplimit`+1) WHERE bid='".$bid."'");
		$emp_name=$this->get_empname($id);
		$gname=$this->db->query("SELECT groupname FROM	".$bid."_support_groups WHERE gid='".$lgid."'")->row()->groupname;
		$this->auditlog->auditlog_info('Group Employee',$emp_name->empname." is Removed From the Group ".$gname);
		return true;
	}
	function get_empname($eid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("SELECT empname FROM ".$bid."_employee WHERE eid=$eid");
		$res=$sql->row();
		return $res;
	}
	function disGrpEmp($eid,$lgid,$bid){
		$check=$this->db->query("SELECT * FROM ".$bid."_support_grpemp WHERE eid=".$eid." AND gid=$lgid")->row_array();	
		$status=($check['status']==0)?'1':'0';
		$this->db->set('status',$status);
		$this->db->where('eid',$eid);	
		$this->db->where('gid',$lgid);
		$this->db->update($bid.'_support_grpemp');
		$itemDetail= $this->configmodel->getDetail('38',$lgid,'',$bid);
		$empDetail= $this->configmodel->getDetail('2',$eid,'',$bid);
		$text=($status)?" Enabled":" Disabled";
		$this->auditlog->auditlog_info('Support', $empDetail['empname'].$text." from the group ".$itemDetail['groupname']);
		return $status;
	}
	function getEmployees($grid=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res = ($grid=='') ? array(''=>'Select Employee') : array();
		$query=$this->db->query("SELECT eid,empname FROM ".$bid."_employee WHERE status=1 ORDER BY empname");
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$q='';
		$q.=($roleDetail['role']['owngroup']=='1' && $roleDetail['role']['admin']!='1') ? " AND (g.eid = '".$this->session->userdata('eid')."' OR e.eid='".$this->session->userdata('eid')."')":"";
		$q .= ($grid=='') ? '' : " AND g.gid='".$grid."'";
		$query = ($grid=='') ? "SELECT * FROM ".$bid."_employee WHERE status='1' ORDER BY empname" : "SELECT e.* FROM ".$bid."_employee e
								 LEFT JOIN ".$bid."_support_grpemp ge ON e.eid=ge.eid
								 LEFT JOIN ".$bid."_support_groups g ON g.gid=ge.gid
								 WHERE e.status=1 AND ge.status = '1' ".$q." ORDER BY e.empname";
		$query=$this->db->query($query);
		if($query->num_rows()>0){
			foreach($query->result_array() as $rt)
			$res[$rt['eid']]=$rt['empname'];
		}
		return $res;
	}
	function getSupLevelTimes($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res = $this->db->query("SELECT time	
							   FROM ".$bid."_support_levels a
							   WHERE a.id='".$id."'")->row()->time;
		return $res;
	}
	function get_grEmployees($lgid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res = $this->db->query("SELECT e.empname,a.eid	
							   FROM ".$bid."_support_grpemp a
							   LEFT JOIN ".$bid."_employee e on a.eid=e.eid
							   WHERE a.gid=$lgid AND e.status = '1' AND a.status = '1' 
							   ORDER BY e.`empname`")->result_array();
		return $res;
	}
	function allEmployees($gid,$eid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res = array();
		$query = "SELECT e.eid,e.empname FROM ".$bid."_employee e
				 LEFT JOIN ".$bid."_support_grpemp ge on e.eid=ge.eid
				 LEFT JOIN ".$bid."_support_groups g on g.gid=ge.gid
				 WHERE e.status = '1' AND ge.status = '1' AND g.gid='".$gid."' ORDER BY e.empname";
		$query=$this->db->query($query);
		if($query->num_rows()>0){
			foreach($query->result_array() as $rt){
				$res[$rt['eid']]=$rt['empname'];
			}
		}
		if( $eid != '' && ! array_key_exists($eid,$res)){
			$emp = $this->empgetname($eid);
			if(isset($emp) && $emp != '')
				$res[$eid] = $emp;
		}
		return $res;
	}
	function addComments($post){
		$bid = $_POST['bid'];
		$sql = "INSERT INTO ".$bid."_support_comments SET
				tktid		='".$_POST['tktid']."',
				bid			='".$bid."',
				eid			='".$this->session->userdata('eid')."',
				comment		='".$_POST['comments']."'";
		$this->db->query($sql);
		$this->auditlog->auditlog_info('Support',$this->input->post('name'). " Added new comments to Support Ticket ".$_POST['tktid']);
		return 1;
	}
	function addRemarks($post){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$sql = "INSERT INTO ".$bid."_support_remarks SET
				tktid		='".$_POST['tktid']."',
				bid			='".$bid."',
				eid			='".$this->session->userdata('eid')."',
				remark		='".addslashes($_POST['remark'])."'";
		$this->db->query($sql);
		$this->auditlog->auditlog_info('Support',$this->input->post('name'). " Added new Remarks to Support Ticket ".$_POST['tktid']);
		return 1;
	}
	function getComments($tktid,$bid){
		$sql="SELECT SQL_CALC_FOUND_ROWS lc.*,e.empname FROM ".$bid."_support_comments lc
			  LEFT JOIN ".$bid."_employee e ON lc.eid = e.eid 
			  WHERE lc.tktid='".$tktid."' ORDER BY lc.cdate DESC";				 
		$rst = $this->db->query($sql)->result_array();
		$header = array('#'
						,'Comments'
						,'Commented By'
						,'Date'
						);
		$ret['header'] = $header;
		$list = array();
		$i = 1;
		foreach($rst as $rec){
			$data = array($i
						  ,stripslashes(nl2br(wordwrap($rec['comment'],80,"\n")))
						  ,$rec['empname']
						  ,$rec['cdate']
						  );
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function getRemarks($tktid,$bid){
		$sql="SELECT SQL_CALC_FOUND_ROWS lc.*,e.empname FROM ".$bid."_support_remarks lc
			  LEFT JOIN ".$bid."_employee e ON lc.eid = e.eid 
			  WHERE lc.tktid='".$tktid."' ORDER BY lc.cdate DESC ";				 
		$rst = $this->db->query($sql)->result_array();
		$header = array('#'
						,'Remark'
						,'Remark By'
						,'Date'
						);
		$ret['header'] = $header;
		$list = array();
		$i = 1;
		foreach($rst as $rec){
			$data = array($i
						  ,stripslashes(nl2br(wordwrap($rec['remark'],80,"\n")))
						  ,$rec['empname']
						  ,$rec['cdate']
						  );
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		
		return $ret;
	}
	function getCommentById($tktid,$bid){
		$sql="SELECT lc.comment FROM ".$bid."_support_comments lc
			  WHERE lc.tktid='".$tktid."' ORDER BY lc.cdate DESC LIMIT 0,1";				 
		$rst = $this->db->query($sql)->result_array();
		return isset($rst[0]['comment']) ? $rst[0]['comment'] : '' ;
	}
	function getRemarkById($tktid,$bid){
		$sql="SELECT lc.remark FROM ".$bid."_support_remarks lc
			  WHERE lc.tktid='".$tktid."' ORDER BY lc.cdate DESC LIMIT 0,1";				 
		$rst = $this->db->query($sql)->result_array();
		return isset($rst[0]['remark']) ? $rst[0]['remark'] : '' ;
	}
	function supEsctime($tktlevel){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$sql="SELECT time FROM ".$bid."_support_levels 
			  WHERE level='".$tktlevel."' LIMIT 0,1";				 
		$rst = $this->db->query($sql)->result_array();
		return isset($rst[0]['time']) ? $rst[0]['time'] : '' ;
	}
	function getTktComments($tktid,$bid){
		$sql="SELECT SQL_CALC_FOUND_ROWS lc.*,e.empname FROM ".$bid."_support_comments lc
			  LEFT JOIN ".$bid."_employee e ON lc.eid = e.eid 
			  WHERE lc.tktid='".$tktid."' ORDER BY lc.cdate DESC";				 
		$rst = $this->db->query($sql)->result_array();
		$data = '';
		foreach($rst as $rec){
			$data .= $rec['empname']." : ".stripslashes(str_replace("\n"," ",$rec['comment']))."  ";
		}
		return $data;
	}
	function getTktRemarks($tktid,$bid){
		$sql="SELECT SQL_CALC_FOUND_ROWS lc.*,e.empname FROM ".$bid."_leads_remarks lc
			  LEFT JOIN ".$bid."_employee e ON lc.eid = e.eid 
			  WHERE lc.tktid='".$tktid."' ORDER BY lc.cdate DESC";				 
		$rst = $this->db->query($sql)->result_array();
		$data = '';
		foreach($rst as $rec){
			$data .= $rec['empname']." : ".stripslashes(str_replace("\n"," ",$rec['remark']))."  ";
		}
		return $data;
	}
	function getSupStatus($bid){
		$res=array();	
		$sql=$this->db->query("SELECT sid,status FROM ".$bid."_support_status ");
		$res[0]=$this->lang->line('level_select');
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $r)	{
				$res[$r['sid']]=$r['status'];
			}
		}
		return $res;
	}
	function getSupTktCritic(){
		$res=array();	
		$sql=$this->db->query("SELECT * FROM support_criticality ");
		$res[0]=$this->lang->line('level_select');
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $r)	{
				$res[$r['id']]=$r['type'];
			}
		}
		return $res;
	}
	function getSupTktLevel(){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$res=array();	
		$sql=$this->db->query("SELECT * FROM ".$bid."_support_levels ");
		$res[0]=$this->lang->line('level_select');
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $r)	{
				$res[$r['id']]=$r['level'];
			}
		}
		return $res;
	}
	function callHistoryList($tktid,$bid){
		$q = '';
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (a.eid='".$this->session->userdata('eid')."' OR d.eid='".$this->session->userdata('eid')."' OR a.assignto='".$this->session->userdata('eid')."')";
		}
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND a.pulse > '0'";
		}
		 $sql ="SELECT SQL_CALC_FOUND_ROWS a.callid,a.pulse=0,
				if(a.pulse=0,'1','0') as missed
				FROM ".$bid."_callhistory a
				LEFT JOIN ".$bid."_employee c ON a.eid=c.eid
				LEFT JOIN ".$bid."_groups d ON a.gid=d.gid
				WHERE a.status!='2' $q 
				AND a.tktid=$tktid
				ORDER BY a.starttime DESC";							 
		$rst = $this->db->query($sql)->result_array();
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='6'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('6');
		$keys = array();
		$header = array('#');
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,$field['fieldname']);
					array_push($header,(($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
					if($field['fieldname'] == 'pulse'){
						array_push($keys,'duration');
						array_push($header,'Duration');
					}
				}
			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,'custom['.$field['fieldid'].']');
					array_push($header,$field['customlabel']);
				}
			}
		}
		array_push($header,$this->lang->line('level_Action'));
		$ret['header'] = $header;
		$list = array();
		$i = 1;
		foreach($rst as $rec){
			$data = array($i);
			$r = $this->configmodel->getDetail('6',$rec['callid'],'',$bid);
			foreach($keys as $k){
				$v = isset($r[$k])?$r[$k]:"";
				$v = ($rec['missed']=='1')? "<font color='red'>".$v."</font>":$v;
				array_push($data,$v);
			}
			$act = ($opt_add) ?'<a href="Report/edit/'.$r['callid'].'"><span title="Edit" class="fa fa-edit"></span></a>':'';
			$act .= ($opt_delete) ? '<a href="'.base_url().'Report/Delete_call/'.$r['callid'].'" class="deleteClass"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
			$act .= '<a href="Report/activerecords/'.$r['callid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="fa fa-file-text"  title="List Employee Group"></span></a>';
			$act .= ($roleDetail['role']['accessrecords']=='0') ? (($r['filename']!='' && file_exists('sounds/'.$r['filename']))
					?'<a target="_blank" href="'.site_url('sounds/'.$r['filename']).'"><span title="Sound" class="fa fa-volume-up"></span></a>'
					:'<span class="glyphicon glyphicon-volume-off"></span>'):"";
			$act .= anchor("Report/empdetail/".$rec['callid'], ' <span title="List Employees" class="glyphicon glyphicon-user"></span>',array(' class="btn-danger" data-toggle="modal" data-target="#modal-responsive"'));
			array_push($data,$act);
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function blk_down($bid){
		$itemDetail=array();
		$fieldset = $this->configmodel->getFields('40',$bid);
		$csv_output = "";
		$hkey=array();
		$csv_output ='';
	foreach($_POST['formfields'] as $key=>$fiels){
				$hkey[]=$key;
				$header[]=$fiels;
		}
		if(@in_array('comments',$_POST['formfields'])){
			$header[] = 'Comments';
			$hkey[] = 'comments';
		}
		if(@in_array('remark',$_POST['formfields'])){
			$header[] = 'Remarks';
			$hkey[] = 'remarks';
		}
		$csv_output =implode(",",$header)."\n";
		$call_ids=explode(",",$_POST['call_ids']);
		$name = $bid.'_'.
				$this->session->userdata('eid').'_'.
				time();
		mkdir('reports/'.$name);
		chmod('reports/'.$name,0777);
		$data_file = 'reports/'.$name.'/support.csv';
		$fp = fopen($data_file,'w');
		fwrite($fp,$csv_output);
		foreach($call_ids as $callids){
			$data = array();
			$r = $this->configmodel->getDetail('40',$callids,'',$bid);
			$i=0;
			foreach($hkey as $k){
				if(@in_array($k,array('gid','assignto','enteredby'))){
					$v=($k=='gid')?$r['groupname']:((($k=='enteredby')) ? $r['enteredempname'] : $r['assignempname']);
				}elseif(@in_array($k,array('tkt_status','tkt_criticality'))){
					$v=($k=='tkt_status')?$r['status']:$r['type'];
				}elseif($k=='tkt_level'){
					$v = $r['level'];
				}elseif($k == "comments"){
					$v = $this->tktComments($callids,$bid);
				}elseif($k == "remarks"){
					$v = $this->tktRemarks($callids,$bid);
				}else{
					$v=(isset($r[$k])) ? '"'.$r[$k].'"' : '';
				}
				array_push($data,$v);
			}
			$csv_output =implode(",",$data)."\n";
			fwrite($fp,$csv_output);
		}
		fclose($fp);
		chdir('reports')."<br>";
		exec('zip -r '.$name.'.zip '.$name);
		exec('rm -rf '.$name);
		return $name;
	}
	function empgetname($eid){
		if($eid != '' && $eid != 0){
			$cbid = $this->session->userdata('cbid');
			$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
			$empname=$this->db->query("SELECT empname FROM ".$bid."_employee WHERE eid='".$eid."'");
			if($empname->num_rows() >0){
				$emp = $empname->row()->empname;
				return $emp;
			}
		}
	}
	function refreshcounter($lgid,$bid){
		$sql=$this->db->query("UPDATE ".$bid."_support_grpemp SET counter = 0 WHERE gid=$lgid");
		if($this->db->affected_rows() >0){
			$this->auditlog->auditlog_info('Support',$lgid." Counter Reset By ".$this->session->userdata('username'));
			return 1;
		}
		else
			return 0;
	}
	function callcount($tktid){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$sql=$this->db->query("SELECT count(callid) as cnt FROM `".$bid."_callhistory` WHERE tktid='".$tktid."'")->result_array();
		$callcnt = $sql[0]['cnt'];
		return $callcnt;
	}
	function ticketnumber($tktid){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$number=$this->db->query("SELECT number FROM ".$bid."_support_tickets WHERE tktid='".$tktid."'")->row()->number;
		return ($number != '') ? $number : '';
	}
	function tktComments($tktid,$bid){
		$sql="SELECT SQL_CALC_FOUND_ROWS sc.*,e.empname FROM ".$bid."_support_comments sc
			  LEFT JOIN ".$bid."_employee e ON sc.eid = e.eid 
			  WHERE sc.tktid='".$tktid."' ORDER BY sc.cdate DESC";				 
		$rst = $this->db->query($sql)->result_array();
		$data = '';
		foreach($rst as $rec){
			$data .= $rec['empname']." : ".stripslashes(str_replace("\n"," ",$rec['comment']))."  ";
		}
		return $data;
	}
	function tktRemarks($tktid,$bid){
		$sql="SELECT SQL_CALC_FOUND_ROWS sc.*,e.empname FROM ".$bid."_support_remarks sc
			  LEFT JOIN ".$bid."_employee e ON sc.eid = e.eid 
			  WHERE sc.tktid='".$tktid."' ORDER BY sc.cdate DESC";				 
		$rst = $this->db->query($sql)->result_array();
		$data = '';
		foreach($rst as $rec){
			$data .= $rec['empname']." : ".stripslashes(str_replace("\n"," ",$rec['remark']))."  ";
		}
		return $data;
	}
	function supstatsmschk($sid,$bid){
		$sql="SELECT sms FROM ".$bid."_support_status 
			  WHERE sid='".$sid."' ";				 
		$rst = $this->db->query($sql)->row()->sms;
		return $rst;
	}
	function followupSetup($bid){
		$sql = "REPLACE INTO support_configure SET
				bid				= '".$bid."',
				followup		= '".$_POST['followup']."',
				time_interval	= '".$_POST['interval']."'";
		$this->db->query($sql);
		$this->auditlog->auditlog_info('Support',$this->input->post('name'). " Configured the Support Auto Followup ");
		return 1;
	}
	function getconfiguration($bid){
		$sql="SELECT followup,time_interval FROM support_configure  
			  WHERE bid='".$bid."' ";				 
		$rst = $this->db->query($sql)->row();
		return $rst;
	}
	function getTktStatus($bid){
		$res=array(""=>"Select");
		$sql=$this->db->query("SELECT sid,syslabel FROM ".$bid."_support_status");
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $r)	{
				$res[$r['sid']]=$r['syslabel'];
			}
		}
		return $res;
	}
}
