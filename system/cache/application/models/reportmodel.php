<?php
Class Reportmodel extends Model
{
	function Reportmodel(){
		 parent::Model();
		 $this->load->helper('mcube_helper');
	}
	function getReportlist($bid,$ofset='0',$limit='20',$type='a',$tab)
	{
		$DB2 = (in_array($bid,array('257','538'))) ? $this->load->database('download', TRUE) : $this->load->database('download1', TRUE);
		$stype=((isset($_POST['type'])) && ($_POST['type']=="count"))?"count":"search";
		$content='';
		$q=($type=='a')?"":"";
		$q.=($type=='m')?" AND a.pulse=0":"";
		$q.=($type=='q')?" AND (a.callername!='' OR a.callerbusiness!='' OR a.calleraddress!='' OR a.caller_email!='' OR a.remark!='')":"";
		$q.=($type=='u')?" AND (a.callername='' AND a.callerbusiness='' AND a.calleraddress='' AND a.caller_email='' AND a.remark='')":"";
		$q.=($type=='at')?" AND a.pulse!=0":"";
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		if(isset($_POST['Adv_submit'])){
			$this->session->set_userdata('Adsearch',$_POST);
		}else{
			$this->session->unset_userdata('Adsearch');
		}
		if(isset($_POST['sav_search'])){
			Save_search_data($bid,$this->session->userdata('eid'),json_encode($_POST),'6');
			$this->session->set_userdata('Adsearch',$_POST);
		}
		if($tab>0 && $this->uri->segment(3)!="group" && $this->uri->segment(3)!="emp"){
			$qs=get_save_searchrow($bid,'6',$this->session->userdata('eid'),$tab);
			$content=(array)json_decode($qs['content']);
			$this->session->set_userdata('Adsearch',$content);
		}
		if($this->session->userdata('Adsearch')){
			$Ads = $this->session->userdata('Adsearch');
		}
		if(isset($Ads) && sizeof($Ads)>0){
			switch($Ads['timespan']){
				case 'all':
				default :
						 $q.='';	
				break;
				case 'today':
						 $q.=" and date(a.starttime)>= '".date('Y-m-d')."'";
				break;
				case 'last7':
						$date=date('Y-m-d',strtotime('-6 days'));
						$q.=" and date(a.starttime)>= '".$date."'";	
				break;			
				case 'month':
						$date=date('Y-m-01');
						$q.=" and date(a.starttime)>= '".$date."'";	
				break;				
			}
			if(isset($Ads['multiselect_gid']) && sizeof($Ads['multiselect_gid'])>0){
				$gids=implode(",",$Ads['multiselect_gid']);
			    $q.=" and a.gid in (".$gids.")";
				
			}
			if(isset($Ads['multiselect_eids']) && sizeof($Ads['multiselect_eids'])>0){
				$eids=implode(",",$Ads['multiselect_eids']);
				$q.=" and a.eid in (".$eids.")";
			}
			$custiom_ids=array();
			$cust=0;
			$field_array=array("callfrom"=>"a.callfrom",
								"calid"=>"a.calid",
								"refid"=>"a.refid",
								"dialstatus"=>"a.dialstatus",
								"source"=>"a.source",
								"callername"=>"a.callername",
								"caller_email"=>"a.caller_email",
								"starttime"=>"date(a.starttime)",
								"endtime"=>"date(a.endtime)",
								"pulse"=>"a.pulse",
								"gid"=>"d.groupname",
								"remark"=>"a.remark",
								"assignto"=>"k.empname",
								"eid"=>"e.empname",
								"keyword"=>"a.keyword"
								);
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
				//~ if(preg_match('/custom/',$Ads['field_d'][$i])){
					//~ $cust++;
					//~ $str=str_replace("custom[", "", $Ads['field_d'][$i]);
					//~ $Ads['field_d'][$i]=str_replace("]", "", $str);
					//~ $custiom_ids=$this->configmodel->customSearch_ADV((isset($Ads['field_d'][$i]))?$Ads['field_d'][$i]:'','6',$bid,$app);
					//~ 
				//~ }else{
					$q .= (isset($field_array[$Ads['field_d'][$i]])) ? " ".$Ads['cond'][$i]."  ".$field_array[$Ads['field_d'][$i]]."  ".$app : '';	
				//~ }	
			}
			//~ if($cust>0){
				//~ $qs=@implode(",",$custiom_ids);
				//~ $q.=(strlen($qs)>1)?" and a.callid in(".$qs.")":"";
				//~ $q.=(!$qs)?" AND 0 ":"";
			//~ }
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
		$q.=(isset($s['callfrom']) && $s['callfrom']!='')?" AND a.callfrom LIKE '%".$s['callfrom']."%'":"";
		$q.=(isset($s['gid']) && $s['gid']!='0')?" AND a.gid = '".$s['gid']."'":"";
		$q.=(isset($s['empid']) && $s['empid']!='0')?" AND a.eid = '".$s['empid']."'":"";
		$q.=(isset($s['starttime']) && $s['starttime']!='') ? " AND date(a.starttime)>= '".$s['starttime']."'":"";
		$q.=(isset($s['endtime']) && $s['endtime']!='')?" AND date(a.endtime)<= '".$s['endtime']."'":"";
		$q.=(isset($s['callername']) && $s['callername']!='')?" AND a.callername like '%".$s['callername']."%'":"";
		$q.=(isset($s['caller_email']) && $s['caller_email']!='')?" AND a.caller_email like '%".$s['caller_email']."%'":"";
		$q.=(isset($s['pulse']) && $s['pulse']!='')?" AND if(a.pulse>0,ceil(a.pulse/60),a.pulse) ".$s['ptype']." '".$s['pulse']."'":"";	
		$q.=(isset($s['dialstatus']) && $s['dialstatus']!='')?" and a.dialstatus like '%".$s['dialstatus']."%'":"";
		$q.=(isset($s['calid']) && $s['calid']!='')?" and a.calid = '".$s['calid']."'":"";
		$q.=(isset($s['refid']) && $s['refid']!='')?" and a.refid = '".$s['refid']."'":"";
		$q.=(isset($s['source']) && $s['source']!='')?" and a.source like '%".$s['source']."%'":"";
		$q.=(isset($s['keyword']) && $s['keyword']!='')?" and a.keyword like '%".$s['keyword']."%'":"";
		$q.=(isset($s['last_modified']) && $s['last_modified']!='')?" and a.last_modified like '%".$s['last_modified']."%'":"";
		$q.=(isset($s['assignto']) && $s['assignto']!='')?" and k.empname like '%".$s['assignto']."%'":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$ge = " ";
		//Employee Hirarchey
		$reportto = $this->db->query("SELECT GROUP_CONCAT(lv SEPARATOR ',') as eid FROM (SELECT @pv:=(SELECT GROUP_CONCAT(eid SEPARATOR ',') FROM ".$bid."_employee WHERE reportto IN (@pv)) AS lv FROM ".$bid."_employee JOIN (SELECT @pv:=".$this->session->userdata('eid').")tmp WHERE reportto IN (@pv)) z ")->row()->eid;
		$k = ($reportto != '') ? $reportto.",".$this->session->userdata('eid') : $this->session->userdata('eid');
		if($roleDetail['role']['admin']!=1){
			if($roleDetail['role']['roleid']==4){
				$q .= " AND ge.eid is not null ";
				$ge = " LEFT JOIN ".$bid."_group_emp ge ON (d.gid=ge.gid AND ge.eid= '".$this->session->userdata('eid')."') ";
			}elseif($roleDetail['role']['owngroup']=='1'){
				$q .= " AND (a.eid IN (".$k.") 
						OR d.eid='".$this->session->userdata('eid')."' 
						OR (LOCATE('".$this->session->userdata('eid')."',d.access_reports)>0) 
						OR a.assignto='".$this->session->userdata('eid')."')";
			}
		}
		$type = $DB2->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND a.pulse > '0'";
		}
		$uc = $this->session->userdata('UniqueCall');
		$uniqueBy = "";
		$uniqueBy.= (isset($uc['call']) && $uc['call']=='1') ? (($uniqueBy == '') ? " a.callfrom " : " ,a.callfrom " ): "";
		$uniqueBy.= (isset($uc['gid']) && $uc['gid']=='1') ? (($uniqueBy == '') ? " a.callfrom,a.gid " : " ,a.gid " ) : "";
		$uniqueBy.= (isset($uc['eid']) && $uc['eid']=='1') ? (($uniqueBy == '') ? " a.callfrom,a.eid " : " ,a.eid " ): "";
		if($uniqueBy == ''){
			$sql = "SELECT SQL_CALC_FOUND_ROWS a.callid,a.hid,
					a.assignto,a.callback,a.source,
					if(a.pulse=0,'1','0') as missed,l.lead_status FROM 
					".$bid."_callhistory a
					LEFT JOIN ".$bid."_leads l ON a.leadid = l.leadid 
					LEFT JOIN ".$bid."_groups d on a.gid=d.gid 
					WHERE a.status!=2 ".$q." 
					ORDER BY a.starttime DESC limit $ofset,$limit";
		}else{
			$sql = "SELECT SQL_CALC_FOUND_ROWS h2.callid,h2.hid,
					h2.assignto,h2.callback,h2.source,
					if(h2.pulse=0,'1','0') as missed,a.cnt,l.lead_status FROM 
					(SELECT a.assignto,a.callfrom,a.gid,a.eid,
					count(a.callid) as cnt,max(a.starttime) as tt
					FROM `".$bid."_callhistory` a
					LEFT JOIN ".$bid."_groups d on a.gid=d.gid
					LEFT JOIN ".$bid."_employee e ON e.eid = a.eid 
					LEFT JOIN ".$bid."_employee k ON k.eid = a.assignto  
					".$ge." 
					WHERE a.status!=2 ".$q."
					GROUP BY ".$uniqueBy.") as a
					LEFT JOIN ".$bid."_callhistory h2
					on (a.callfrom=h2.callfrom
					AND h2.starttime=a.tt)
					LEFT JOIN ".$bid."_leads l ON h2.leadid = l.leadid 
					ORDER BY h2.starttime DESC limit $ofset,$limit";
		}
		//echo $sql;exit;
		$rst = $DB2->query($sql)->result_array();
		$rst1 = $DB2->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='6'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('6',$bid);
		if($tab=="basic"){
			$fieldset1 = array();
			foreach($fieldset as $fields){
				if(in_array($fields['fieldname'],array('callfrom','eid','gid','starttime','pulse','dialstatus','remark'))){
					$fieldset1[] = $fields;
				}
			}
			$fieldset = $fieldset1;
		}elseif($tab=="contact"){
			$fieldset1 = array();
			foreach($fieldset as $fields){
				if(in_array($fields['fieldname'],array('callfrom','eid','gid','starttime','pulse','callername','caller_email','callername','remark'))){
					$fieldset1[] = $fields;
				}
			}
			$fieldset = $fieldset1;
		}
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
					if($field['fieldname'] == 'pulse'){
						array_push($keys,'duration');
						array_push($header,'Duration');
					}
				}
			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,$field['fieldKey']);
					array_push($header,$field['customlabel']);
				}
			}
		}
		array_push($header,"Counter");
		array_push($header,"CallBack");
		if($opt_add || $opt_view || $opt_delete)
			array_push($header,$this->lang->line('level_Action'));
		$ret['header'] = $header;
		$list = array();
		$i = $ofset+1;
		$empDetail='';
		foreach($rst as $rec){
			$data = array($i);
			$r = $this->configmodel->getDetail('6',$rec['callid'],'',$bid);
			$v='<input type="checkbox" class="blk_check" name="blk[]" value="'.$rec['callid'].'"/>';	
			array_push($data,$v);
			foreach($keys as $k){
				if($k=="callfrom" && $rec['hid']!=0 && $rec['source']=='ivrs'){
					$v='<a href="ivrs/calldetail/'.$rec['hid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r[$k].'</a>';
				}elseif($k=='eid'){
					$v = '<a href="Employee/activerecords/'.$r['empid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r[$k].'</a>';
				}elseif($k=='gid'){
					$v = '<a href="group/activerecords/'.$r['grid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r[$k].'</a>';
				}elseif($k=='callername'){
					$v=	'<a href="Report/ContactDetails/'.$r['callfrom'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r[$k].'</a>';
				}elseif($k=='assignto'){
					$v='<a href="Employee/activerecords/'.$r['asto'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r[$k].'</a>';
				}else{
						$v = isset($r[$k])? nl2br(wordwrap($r[$k],60,"\n")) :"";
				}
				$leadColor = $rec['lead_status'];
				$v = ($r['lead']!=null && $leadColor != 1)? "<font color='green'>".$v."</font>"
						:($leadColor == 1 ? "<font color='#007F55'>".$v."</font>"
						:(($r['suptkt']!=null && $r['tktid'] != 0) ? "<font color='orange'>".$v."</font>"
						:(($rec['missed']=='1')?"<font color='red'>".$v."</font>":$v)));
				array_push($data,$v);
			}
			$v = (isset($rec['cnt'])) ?	"<a href=\"Javascript:void(null)\" onClick=\"window.open('/Report/calldetail/".$rec['callid']."', 'Counter', 'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=980,height=500,resizable=1')\">".$rec['cnt'].'</a>' : '';
			array_push($data,$v);
			$v=	"<a href=\"Javascript:void(null)\" onClick=\"window.open('/Report/callback/".$rec['callid']."', 'callback', 'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=980,height=500,resizable=1')\">".$r['callback'].'</a>';
			array_push($data,$v);
			if($opt_add || $opt_view || $opt_delete){
				$act = ($opt_add) ?'<a href="EditTrackReport/'.$r['callid'].'"><span title="Edit" class="fa fa-edit"></span></a>':'';
				$act .= ($opt_delete) ? '<a href="'.base_url().'Report/Delete_call/'.$r['callid'].'" class="deleteClass"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
				$act .= '<a href="Report/activerecords/'.$r['callid'].'" class="btn-list" data-toggle="modal" data-target="#modal-list"><span class="fa fa-file-text"  title="List Employee Group"></span></a>';
			    $act .= ($roleDetail['role']['accessrecords']=='0') ? (($r['filename']!='' && file_exists('sounds/'.$r['filename'])) ? '<a target="_blank" href="'.site_url('sounds/'.$r['filename']).'"><span title="Sound" class="fa fa-volume-up"></span></a>' :'<span class="glyphicon glyphicon-volume-off"></span> ') : "";
				$act .= '<a href="Report/empdetail/'.$rec['callid'].'" class="btn-empl" data-toggle="modal" data-target="#modal-empl"><span title="List Employees" class="glyphicon glyphicon-user"></span></a>';
				$act .= '<a href="Report/followup/'.$rec['callid'].'/0/calltrack" class="btn-followup" data-toggle="modal" data-target="#modal-followup"><img src="system/application/img/icons/comments.png" title="Followups" style="vertical-align:top;" width="16" height="16" /></a>';		
				$act .= ($r['caller_email']!='')?"<a href=\"javascript:void(null)\" onClick=\"window.open('/Email/compose/".$rec['callid']."/calls', 'Sent Email', 'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=970,height=480,resizable=1')\">&nbsp;<span title='Send Mail' class='fa fa-envelope-o'></span></a>":'&nbsp;<span title="Send Mail" class="fa fa-envelope"></span>';
				$act .= anchor("Report/clicktoconnect/".$rec['callid']."/1", '<span title="click To Connect" class="fa fa-phone"></span>',array('class'=>'clickToConnect'));
				$act .= anchor("Report/sendSms/".$rec['callid']."/calltrack", '&nbsp;<span title="Click to send SMS" class="glyphicon glyphicon-comment"></span>','class="clickToSMS" data-toggle="modal" data-target="#modal-empl"');	
				$act .= "<a href=\"Javascript:void(null)\" onClick=\"window.open('Report/sendFields/".$rec['callid']."/".$bid."/6/calltrack', 'Send Fields', 'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=550,height=500,left=200,top=20,resizable=1')\">&nbsp;<span title='Click to Send Fields' class='fa fa-list-alt'></span></a>";
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		$DB2->close();
		return $ret;
	}
	function getDeletedlist($bid,$ofset='0',$limit='20',$type='a'){//echo $limit;
		$q=($type=='a')?"":"";
		$q.=($type=='m')?" AND a.pulse=0":"";
		$q.=($type=='q')?" AND (a.callername!='' OR a.callerbusiness!='' OR a.calleraddress!='' OR a.caller_email!='' OR a.remark!='')":"";
		$q.=($type=='u')?" AND (a.callername='' AND a.callerbusiness='' AND a.calleraddress='' AND a.caller_email='' AND a.remark='')":"";
		$q.=($type=='at')?" AND a.pulse!=0":"";
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		if(isset($_POST['sav_search'])){
			Save_search_data($bid,$this->session->userdata('eid'),json_encode($_POST),'3');
			$this->session->set_userdata('Adsearch',$_POST);
		}
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$q.=(isset($s['calid']) && $s['calid']!='')?" and a.calid = '".$s['calid']."'":"";
		$q.=(isset($s['gid']) && $s['gid']!='')?" and a.gid = '".$s['gid']."'":"";
		$q.=(isset($s['callername']) && $s['callername']!='')?" and a.callername like '%".$s['callername']."%'":"";
		$q.=(isset($s['caller_email']) && $s['caller_email']!='')?" and a.caller_email like '%".$s['caller_email']."%'":"";
		$q.=(isset($s['callfrom']) && $s['callfrom']!='')?" and a.callfrom like '%".$s['callfrom']."%'":"";
		$q.=(isset($s['empid']) && $s['empid']!='')?" and a.eid = '".$s['empid']."'":"";
		$q.=(isset($s['starttime']) && $s['starttime']!='')?" and date(a.starttime)>= '".$s['starttime']."'":"";
		$q.=(isset($s['endtime']) && $s['endtime']!='')?" and date(a.endtime)<= '".$s['endtime']."'":"";
		$q.=(isset($s['pulse']) && $s['pulse']!='')?" and if(a.pulse>0,ceil(a.pulse/60),a.pulse) ".$s['ptype']." '".$s['pulse']."'":"";	
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (a.eid='".$this->session->userdata('eid')."' or d.eid='".$this->session->userdata('eid')."' )";
		}
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND a.pulse > '0'";
		}
		$sql = "SELECT SQL_CALC_FOUND_ROWS a.*,b.cnt
				FROM (SELECT a.callid,a.hid,a.`gid`,a.`eid`,a.`callfrom`,a.`starttime`,if(a.pulse>0,ceil(a.pulse/60),a.pulse) as pulse,
				if(a.pulse=0,'1','0') as missed 
				FROM `".$bid."_callhistory` a
				LEFT JOIN ".$bid."_callhistory h2
				on (a.callfrom=h2.callfrom
				AND a.gid=h2.gid AND a.eid=h2.eid
				AND a.starttime< h2.starttime)
				LEFT JOIN ".$bid."_groups d on a.gid=d.gid
				WHERE h2.callid is null
				AND a.status=2 $q
				) a
				LEFT JOIN
				(SELECT count(callid) as cnt,
				callfrom,gid,eid FROM `".$bid."_callhistory`
				GROUP BY callfrom,gid,eid) b
				ON (a.callfrom=b.callfrom AND a.gid=b.gid AND a.eid=b.eid)
				ORDER BY a.starttime DESC limit $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='6'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('6',$bid);
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
					array_push($keys,$field['fieldKey']);
					array_push($header,$field['customlabel']);
				}
			}
		}
		array_push($header,"Counter");
		if($opt_add || $opt_view || $opt_delete)
			array_push($header,$this->lang->line('level_Action'));
		$ret['header'] = $header;
		$list = array();
		$i = $ofset+1;
		foreach($rst as $rec){
			$data = array($i);
			$r = $this->configmodel->getDetail('6',$rec['callid'],'',$bid);
			foreach($keys as $k){
				if($k=="callfrom" && $rec['hid']!=0){
					$v='<a href="ivrs/calldetail/'.$rec['hid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r[$k].'</a>';
				}
				elseif($k=='eid'){
					$v = '<a href="Employee/activerecords/'.$r['empid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r[$k].'</a>';
				}elseif($k=='gid'){
					$v = '<a href="group/activerecords/'.$r['grid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r[$k].'</a>';
				}else{
						$v = isset($r[$k])?nl2br(wordwrap($r[$k],60,"\n")):"";	
				}
				$v = ($rec['missed']=='1')? "<font color='red'>".$v."</font>":$v;
				array_push($data,$v);
			}
			array_push($data,anchor("Report/calldetail/".$rec['callid'], $rec['cnt'],array(' class="btn-danger" data-toggle="modal" data-target="#modal-responsive"')));
			if($opt_add || $opt_view || $opt_delete){
				$act = '<a href="'.base_url().'Report/UnDel/'.$r['callid'].'"><img src="system/application/img/icons/undelete.png" style="vertical-align:top;" title="Delete" /></a>';
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function UnDel($callid,$bid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$this->db->set('status','1');
		$this->db->where('callid',$callid);
		$this->db->update($bid."_callhistory");
		return true;
	}
	function getArchivelist($args){
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$args['bid'];
		$ofset 		= isset($args['ofset']) ? $args['ofset'] : '0';
		$limit 		= isset($args['limit']) ? $args['limit'] : '20';
		$year 		= isset($args['year']) ? $args['year'] : '0';
		$month 		= isset($args['month']) ? $args['month'] : '0';
		$q = "";
		if($year!=0 && $month!=0){
			$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
			$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
						:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
						
			$q = " AND DATE_FORMAT(h.`starttime`,'%m')='".$month."'";
			$q .= " AND DATE_FORMAT(h.`starttime`,'%Y')='".$year."'";
			if($roleDetail['role']['admin']!=1){
				$q .= " AND (h.eid='".$this->session->userdata('eid')."' OR g.eid='".$this->session->userdata('eid')."' OR h.assignto='".$this->session->userdata('eid')."')";
			}
			$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
			if($type == '0'){
				$q .= " AND h.pulse > '0'";
			}
			$sql = "SELECT SQL_CALC_FOUND_ROWS h.callid,if(h.pulse=0,1,0) missed
					FROM `".$bid."_callarchive` h
					left join ".$bid."_groups g on h.gid=g.gid
					WHERE h.status!=2 $q
					ORDER BY h.starttime DESC limit $ofset,$limit";
			$rst = $this->db->query($sql)->result_array();
			$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
			$ret['count'] = $rst1->row()->cnt;
			foreach($roleDetail['modules'] as $mod){
				if($mod['modid']=='6'){
					$opt_add 	= $mod['opt_add'];
					$opt_view 	= $mod['opt_view'];
					$opt_delete = $mod['opt_delete'];
				}
			}
			$fieldset = $this->configmodel->getFields('6',$bid);
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
				$r = $this->configmodel->getDetail('callarchive',$rec['callid'],'',$bid);
				foreach($keys as $k){
					if($k=='eid'){
						$v = '<a href="Employee/activerecords/'.$r['empid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r[$k].'</a>';
					}elseif($k=='gid'){
						$v = '<a href="group/activerecords/'.$r['grid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r[$k].'</a>';
					}else{
							$v = isset($r[$k])?nl2br(wordwrap($r[$k],60,"\n")):"";	
					}
					$v = ($rec['missed']=='1')? "<font color='red'>".$v."</font>":$v;
					array_push($data,$v);
				}
				if($opt_add || $opt_view || $opt_delete){
					
					$act = ($roleDetail['role']['accessrecords']=='0') ? (($r['filename']!='' && file_exists('sounds/'.$r['filename']))
						?'<a target="_blank" href="'.site_url('sounds/'.$r['filename']).'"><span title="Sound" class="fa fa-volume-up"></span></a>'
						:'<span class="glyphicon glyphicon-volume-off"></span> '):"";
					$act .= anchor("Report/empdetail/".$rec['callid'], '<span title="List Employees" class="glyphicon glyphicon-user"></span>','class="btn-empl" data-toggle="modal" data-target="#modal-empl"');
					array_push($data,$act);
				}
				$i++;
				array_push($list,$data);
			}
		}else{
			if($roleDetail['role']['admin']!=1){
				$q .= " AND (h.eid='".$this->session->userdata('eid')."' or g.eid='".$this->session->userdata('eid')."')";
			}else{
				$q .= "";
			}
			$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
			if($type == '0'){
				$q .= " AND h.pulse > '0'";
			}
			$sql = "SELECT SQL_CALC_FOUND_ROWS count(*) `count`,
					DATE_FORMAT(h.`starttime`,'%m') nmonth,
					DATE_FORMAT(h.`starttime`,'%Y') year,
					DATE_FORMAT(h.`starttime`,'%M') month
					FROM `".$bid."_callarchive` h
					LEFT JOIN ".$bid."_groups g on h.gid=g.gid
					WHERE h.status!=2 $q
					GROUP BY DATE_FORMAT(`starttime`,'%m'),DATE_FORMAT(`starttime`,'%Y')
					ORDER BY DATE_FORMAT(`starttime`,'%Y') DESC,DATE_FORMAT(`starttime`,'%m') DESC";
			$rst = $this->db->query($sql)->result_array();
			$ret['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
			$keys = array('year','month','count','action');
			$header = array('#','Year','Month','Count','Action');
			$ret['header'] = $header;
			$list = array();
			$i = $ofset+1;
			foreach($rst as $rec){
				$data = array($i);
				foreach($keys as $k){
					$v = isset($rec[$k])?$rec[$k]:"";	
					$v = ($k=='action')
						? "<a href='CallArchive/".$rec['year']."/".$rec['nmonth']."'>List</a>&nbsp;&nbsp;&nbsp;" .
						  "<a href='Report/archive_download/".$rec['year']."/".$rec['nmonth']."'>Download</a>"
						:$v;
					array_push($data,$v);
				}
				$i++;
				array_push($list,$data);
			}
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function getReportlist1($id,$bid){
		$q = '';
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (a.eid='".$this->session->userdata('eid')."' OR d.eid='".$this->session->userdata('eid')."' OR a.assignto='".$this->session->userdata('eid')."')";
		}
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND a.pulse > '0'";
		}
		$uc = $this->session->userdata('UniqueCall');
		$q .= " AND a.callfrom=(SELECT callfrom FROM ".$bid."_callhistory WHERE callid='".$id."') ";
		$q .= (isset($uc['gid']) && $uc['gid']=='1') ? " AND a.gid=(SELECT gid FROM ".$bid."_callhistory WHERE callid='".$id."') " : "";
		$q .= (isset($uc['eid']) && $uc['eid']=='1') ? " AND a.eid=(SELECT eid FROM ".$bid."_callhistory WHERE callid='".$id."') " : "";
		$sql = "SELECT SQL_CALC_FOUND_ROWS a.* FROM (
				SELECT 'cal' as type,a.callid,a.eid,a.status,a.callfrom,
				a.starttime,a.gid,if(a.pulse=0,'1','0') as missed,a.assignto
				FROM ".$bid."_callhistory a				
				UNION
				SELECT 'arc' as type,a.callid,a.eid,a.status,a.callfrom,
				a.starttime,a.gid,if(a.pulse=0,'1','0') as missed,a.assignto
				FROM ".$bid."_callarchive a
				) a
				LEFT JOIN ".$bid."_employee c on a.eid=c.eid
				LEFT JOIN ".$bid."_groups d on a.gid=d.gid
				WHERE a.status!=2 $q	
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
					array_push($keys,$field['fieldKey']);
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
			$r = ($rec['type']=='cal')
					? $this->configmodel->getDetail('6',$rec['callid'],'',$bid)
					: $this->configmodel->getDetail('callarchive',$rec['callid'],'',$bid);
			foreach($keys as $k){
				$v = isset($r[$k])?nl2br(wordwrap($r[$k],60,"\n")):"";
				$v = ($rec['missed']=='1')? "<font color='red'>".$v."</font>":$v;
				array_push($data,$v);
			}
			$act = ($opt_add) ?'<a href="Report/edit/'.$r['callid'].'"><span title="Edit" class="fa fa-edit"></span></a>':'';
			$act .= ($opt_delete) ? '<a href="'.base_url().'Report/Delete_call/'.$r['callid'].'" class="deleteClass"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
			$act .= '<a href="Report/activerecords/'.$r['callid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="fa fa-file-text"  title="List Employee Group"></span></a>';
			$act .= ($roleDetail['role']['accessrecords']=='0') ? (($r['filename']!='' && file_exists('sounds/'.$r['filename']))
					?'<a target="_blank" href="'.site_url('sounds/'.$r['filename']).'"><span title="Sound" class="fa fa-volume-up"></span></a>'
					:'<span class="glyphicon glyphicon-volume-off"></span> '):"";
			$act .= anchor("Report/empdetail/".$rec['callid'], '<span title="List Employees" class="glyphicon glyphicon-user"></span>','class="btn-empl" data-toggle="modal" data-target="#modal-empl"');
			array_push($data,$act);
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function getCallbackList($id,$bid){
		$q = '';
		$sql="select SQL_CALC_FOUND_ROWS *
				from ".$bid."_outboundcalls
				WHERE modid='1'
				AND dataid='".$id."'
				ORDER BY starttime DESC";							 
		$rst = $this->db->query($sql)->result_array();
		$header = array('#'
						,'Executive Number'
						,'Customer Number'
						,'Start Time'
						,'End Time'
						,'Credit Used'
						,'Call Status'
						);
		$ret['header'] = $header;
		$list = array();
		$i = 1;
		$status=array(
			"0"=>"Failed",
			"1"=>"Originate",
			"2"=>"Executive Busy",
			"3"=>"Customer Busy",
			"4"=>"Call Complete",
			"5"=>"Insufficient Balance"
		);
		foreach($rst as $rec){
			$data = array($i
						  ,$rec['executive']
						  ,$rec['customer']
						  ,$rec['starttime']
						  ,$rec['endtime']
						  ,$rec['pulse']
						  ,$status[$rec['status']]
						  );
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	
	function getReportlist2($id,$bid){
		$sql="select SQL_CALC_FOUND_ROWS c.calltime,c.lat,c.long,
				e.eid,e.empname,e.empnumber,e.empemail from 
				".$bid."_calltrackemp c
				LEFT JOIN ".$bid."_employee e on c.eid=e.eid
				where c.callid='".$id."'
				ORDER BY c.calltime ASC";	
		$rst = $this->db->query($sql)->result_array();

		$keys = array('empname',
					  'empnumber',
					  'empemail',
					  'calltime');
		$header = array('#',
						'Emp Name',
						'Emp Number',
						'Emp Email',
						'Call Time');
		
		$ret['header'] = $header;
		$list = array();
		$i = 1;
		foreach($rst as $rec){
			$data = array($i);
			foreach($keys as $k){
				$v = isset($rec[$k])?(($k=='empname' && ($rec['lat']>0 && $rec['long']>0))?'<a  class="btn-danger" data-toggle="modal" data-target="#modal-responsive" href="/Report/showmap/'.$rec['lat'].','.$rec['long'].'">'.$rec[$k].'</a>':$rec[$k]):"";
				array_push($data,$v);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	
	function getFollowuplist($id,$bid,$dsh=''){
		$q = '';
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$where = ($dsh != '' && $dsh == 1) ? " followupdate >= CURRENT_DATE() AND " : " ";
		$sql="SELECT * FROM ".$bid."_followup WHERE ".$where." callid='".$id."' $q  ORDER BY cdate ASC";					 
		$rst = $this->db->query($sql)->result_array();
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='29'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('29',$bid);
		$keys = array();
		$header = array('#');
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){
					if($f['fieldid']==$field['fieldid'])$checked = true;
				}
				if($checked){
					if($field['fieldname'] != 'eid'){
					array_push($keys,$field['fieldname']);
					array_push($header,(($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
					}
				}
				}elseif($field['type']=='c' && $field['show'] && $field['listing']){
					foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){
						array_push($keys,$field['fieldKey']);
						array_push($header,$field['customlabel']);
					}
			}
		}
		array_push($keys,"reach_time");
		array_push($header,"Notification Time Limit");
		$ret['header'] = $header;
		$list = array();
		$i = 1;
		foreach($rst as $rec){
			$data = array($i);
			$r = $this->configmodel->getDetail('29',$rec['id'],'',$bid);
	
			foreach($keys as $k){
				$v = isset($r[$k])?nl2br(wordwrap($r[$k],80,"\n")):"";	
				array_push($data,$v);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	
	function addFollowup($post){
		$bid = $_POST['bid'];
		$arr=array_keys($_POST);
		for($i=0;$i<sizeof($arr);$i++){
			if(!in_array($arr[$i],array("update_system","notify_time"))){
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
		$id=$this->db->query("SELECT COALESCE(MAX(`id`),0)+1 as id FROM ".$bid."_followup")->row()->id;
		$this->db->set('id',$id);
		$this->db->set('reach_time',$this->input->post('notify_time'));
		$this->db->set('eid',$this->session->userdata('eid'));
		$this->db->insert($bid."_followup");
	    $this->auditlog->auditlog_info('Followup',$id." ".$this->session->userdata('username'). " Followup added  ");
		return TRUE;
	}
	
	function exportdata($bid){
		$q = '';
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (a.eid='".$this->session->userdata('eid')."' or d.eid='".$this->session->userdata('eid')."')";
		}
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND a.pulse > '0'";
		}
		$res=array();
		$sql=$this->db->query("select a.callfrom,a.callid,a.`starttime`,a.`endtime`,
								a.`status`,b.businessname,c.empname,d.groupname 
								from ".$bid."_callhistory a,
								business b,".$bid."_employee c,
								".$bid."_groups d 
								where a.bid=b.bid and a.eid=c.eid and a.gid=d.gid and (a.status=1 or a.status=0) ".$q);
		if($sql->num_rows()>0)
		{
			$res=$sql->result_array();
		}
		return $res;
	}
	function delete_call($id,$bid)
	{
		$this->db->set('status', '2');
		$this->db->where('callid',$id);
		$this->db->update($bid."_callhistory");
		$this->auditlog->auditlog_info('Report',$id. " Deleted By ".$this->session->userdata('username'));
		return 1;	
	}
	function edit_call_list($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();
		$sql=$this->db->query("select a.bid,a.gid,a.eid,a.pulse, 
								a.callfrom,a.callid,a.`starttime`,
								a.`endtime`,a.`status`,b.businessname,
								c.empname,d.groupname 
								from ".$bid."_callhistory a,
								business b,".$bid."_employee c,
								".$bid."_groups d 
								where a.bid=b.bid and a.eid=c.eid and a.gid=d.gid and a.callid=$id");
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}
		return $res;
	}
	function update_caller_details($id,$bid){
		$itemDetail = $this->configmodel->getDetail('6',$id,'',$bid);
		if(isset($_POST['assignto']) && $itemDetail['assignto']!=$_POST['assignto']){
			$this->db->set('assignto',$_POST['assignto']);
		}
		$return = '';
		$rate=0;
		$val='';
		$arr=array_keys($_POST);
		for($i=0;$i<sizeof($arr);$i++){
			if(! @in_array($arr[$i],array("update_system","custom","convertlead","assignto","lgid","lassignto","lalerttype","updatelead","convertsuptkt","sgid","sassignto","salerttype","updatesuptkt","number","tkt_level","tkt_esc_time"))){
				/* Changed for custom fields */
				$val = (is_array($_POST[$arr[$i]])) ? @implode(',',$_POST[$arr[$i]]) : $_POST[$arr[$i]];
				$this->db->set($arr[$i],$val);
				/* Changed for custom fields end */
			}
		}
		$this->db->set('last_modified',date("Y-m-d H:i:s"));
		$this->db->set('rate',$rate);
		$this->db->where('callid',$id);
		$this->db->update($bid.'_callhistory'); 
		$sql=$this->db->query("SELECT * FROM ".$bid."_callhistory where callfrom='".$itemDetail['callfrom']."'");
		if($sql->num_rows()>0){
			if(isset($_POST['assignto']) && $itemDetail['assignto']!=$_POST['assignto']){
				$this->db->query("UPDATE ".$bid."_callhistory SET assignto='".$_POST['assignto']."' WHERE callfrom='".$itemDetail['callfrom']."'");
			}
		}
		$this->auditlog->auditlog_info('Report',$id. "Call Details updated by ".$this->session->userdata('username'));
		if($this->input->post('callername')!='' || $this->input->post('caller_email')!=''){
			$data = array(
					'bid'		=>$bid,
					'name'		=>$this->input->post('callername'),
					'number'	=>$itemDetail['callfrom'],
					'email'		=>$this->input->post('caller_email')
			);
			$this->configmodel->UpdateContact($data);
		}
		if($this->input->post('convertlead') || $this->input->post('updatelead')){
			$source = array("type"=>"calltrack","id"=>$id,"bid"=>$bid,"keyword"=>$itemDetail['keyword']);
			$res = $this->configmodel->callconvert($source);
		}
		$itemDetailNew = $this->configmodel->getDetail('6',$id,'',$bid);
		$rs=$this->configmodel->getDetail('3',$itemDetail['grid'],'',$bid);
		if(isset($rs['leadaction']) && $rs['leadaction']!=""){
			$url = $rs['leadaction'];
			$data = 'data='.urlencode(json_encode($itemDetailNew));
			$objURL = curl_init($url);
			curl_setopt($objURL, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($objURL,CURLOPT_POST,1);
			curl_setopt($objURL, CURLOPT_POSTFIELDS,$data);
			$retval = trim(curl_exec($objURL));
			curl_close($objURL);
			$ret = serialize($retval);
			$fp =fopen("apilog.txt","a");fwrite($fp,"\n".'['.date('Y-m-d H:i:s').'] bid:'.$bid.' '. $ret);fclose($fp);
		}
		if($rs['supportaction']!=""){
			$url = $rs['supportaction'];
			$data = 'data='.urlencode(json_encode($itemDetailNew));
			$objURL = curl_init($url);
			curl_setopt($objURL, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($objURL,CURLOPT_POST,1);
			curl_setopt($objURL, CURLOPT_POSTFIELDS,$data);
			$retval = trim(curl_exec($objURL));
			curl_close($objURL);
			$ret = serialize($retval);
			$fp =fopen("apilog.txt","a");fwrite($fp,"\n".'['.date('Y-m-d H:i:s').'] bid:'.$bid.' '. $ret);fclose($fp);
		}		
		/* convert support ticket end **/
		if($rs['oneditaction']!=""){
			$url = $rs['oneditaction'];
			$data = 'data='.urlencode(json_encode($itemDetailNew));
			$objURL = curl_init($url);
			curl_setopt($objURL, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($objURL,CURLOPT_POST,1);
			curl_setopt($objURL, CURLOPT_POSTFIELDS,$data);
			$retval = trim(curl_exec($objURL));
			curl_close($objURL);
			$ret = serialize($retval);
			$fp =fopen("apilog.txt","a");fwrite($fp,"\n".'['.date('Y-m-d H:i:s').'] bid:'.$bid.' '. $ret);fclose($fp);
		}
		
		$sql = "SELECT * FROM ".$bid."_customfields WHERE modid=6 AND fieldtype in ('checkbox','dropdown','radio')";
		$rst = $this->db->query($sql)->result_array();
		foreach($rst as $rec){
			if(@in_array($rec['field_key'],$arr)){
				$itemDetailNew = $this->configmodel->getDetail('6',$id,'',$bid);
				$fieldset = $this->configmodel->getFields('6',$bid);
				$data = array();
				foreach($fieldset as $field){
					if($field['type']=='s' && !$field['is_hidden']){
						$data[$field['customlabel']]=$itemDetailNew[$field['fieldname']];
					}elseif($field['type']=='c' && $field['show']){
						$data[$field['customlabel']]=$itemDetailNew[$field['fieldKey']];
					}
				}
				$set = @explode("\n",$rec['options']);
				foreach($set as $s){
					$val = @explode("|",$s);
					if(count($val)>1 && $val['0']==$cf){
						$url = $val[1];
						$data = 'data='.urlencode(json_encode($itemDetailNew));
						$objURL = curl_init($url);
						curl_setopt($objURL, CURLOPT_RETURNTRANSFER, 1); 
						curl_setopt($objURL,CURLOPT_POST,1);
						curl_setopt($objURL, CURLOPT_POSTFIELDS,$data);
						$retval = trim(curl_exec($objURL));
						curl_close($objURL);
						$ret = serialize($retval);
						$fp = fopen("apilog.txt","a");fwrite($fp,"\n".'['.date('Y-m-d H:i:s').'] bid:'.$bid.' '. $ret);fclose($fp);
					}
				}
			}
		}
		return $return;
	}
	/*function get_updatecaller(){
		if($this->session->userdata('type')!=1){
			if($this->session->userdata('type')!=2){
				$q=" and a.eid=".$this->session->userdata('eid');
			}else{
				$q=" and a.eid=".$this->session->userdata('eid')." and a.gid=".$this->session->userdata('gid');
			}
		}else{
				$q='';
				
		}
		$res=array();
		$sql=$this->db->query("select a.callfrom,a.callid,
								a.`starttime`,a.`endtime`,
								a.`status`,b.businessname,
								c.empname,d.groupname 
								from ".$this->session->userdata('bid')."_callerupdate a,
								business b,".$this->session->userdata('bid')."_employee c,
								".$this->session->userdata('bid')."_groups d 
								where a.bid=b.bid and a.eid=c.eid and a.gid=d.gid and (a.status=1 or a.status=0)".$q);
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}
		return $res;
	}	
	function delete_callupdate($id){
		$this->db->set('status', '2');
		$this->db->where('callid',$id);
		$this->db->update($this->session->userdata('bid')."_callerupdate");
		//return $this->db->last_query();
		return 1;
	}*/
	function get_lastcals(){
		$res=array();
		$sql=$this->db->query("select a.callfrom,a.callid,a.`starttime`,a.`endtime`,
								a.`status`,b.businessname,c.empname,d.groupname 
								from ".$this->session->userdata('bid')."_callhistory a,
								business b,".$this->session->userdata('bid')."_employee c,
								".$this->session->userdata('bid')."_groups d 
								where a.bid=b.bid and a.eid=c.eid and a.gid=d.gid and a.status=1 
								group by a.gid order by a.callid desc limit 0,5");
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}
		return $res;
	}

	function groupnames(){
		$ret=array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q = " AND (eid='".$this->session->userdata('eid')."' or g.eid='".$this->session->userdata('eid')."')";
		}else{
			$q = "";
		}
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$this->session->userdata('bid')."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND pulse > '0'";
		}
		
		$sql=$this->db->query("SELECT g.gid,g.groupname,
			(SELECT count(callid) FROM ".$this->session->userdata('bid')."_callhistory 
			WHERE gid=g.gid AND date(endtime)=(DATE_SUB(CURRENT_DATE(),INTERVAL 0 DAY)) ".$q." ) as day0,
			(SELECT count(callid) FROM ".$this->session->userdata('bid')."_callhistory 
			WHERE gid=g.gid AND date(endtime)=(DATE_SUB(CURRENT_DATE(),INTERVAL 1 DAY)) ".$q." ) as day1,
			(SELECT count(callid) FROM ".$this->session->userdata('bid')."_callhistory 
			WHERE gid=g.gid AND date(endtime)=(DATE_SUB(CURRENT_DATE(),INTERVAL 2 DAY)) ".$q." ) as day2,
			(SELECT count(callid) FROM ".$this->session->userdata('bid')."_callhistory 
			WHERE gid=g.gid AND date(endtime)=(DATE_SUB(CURRENT_DATE(),INTERVAL 3 DAY)) ".$q." ) as day3,
			(SELECT count(callid) FROM ".$this->session->userdata('bid')."_callhistory 
			WHERE gid=g.gid AND date(endtime)=(DATE_SUB(CURRENT_DATE(),INTERVAL 4 DAY)) ".$q." ) as day4,
			(SELECT count(callid) FROM ".$this->session->userdata('bid')."_callhistory 
			WHERE gid=g.gid AND date(endtime)=(DATE_SUB(CURRENT_DATE(),INTERVAL 5 DAY)) ".$q." ) as day5,
			(SELECT count(callid) FROM ".$this->session->userdata('bid')."_callhistory 
			WHERE gid=g.gid AND date(endtime)=(DATE_SUB(CURRENT_DATE(),INTERVAL 6 DAY)) ".$q." ) as day6
			FROM ".$this->session->userdata('bid')."_groups g
			WHERE g.bid=".$this->session->userdata('bid'));
		if($sql->num_rows()>0){
			$res=$sql->result_array();
			foreach($res as $rec){
				if($rec['day0']!='0' || $rec['day1']!='0' ||
				   $rec['day2']!='0' || $rec['day3']!='0' ||
				   $rec['day4']!='0' || $rec['day5']!='0' ||
				   $rec['day6']!='0'){
					$ret[] = $rec;
				}
			}
		}	
		return $ret;
	}

	function getmessages(){
		$res=array();
		$sql=$this->db->query("SELECT k.keyword_id,k.keyword,
					(SELECT count(incid) FROM ".$this->session->userdata('bid')."_keywordinbox WHERE keyword_id=k.keyword_id 
					AND date(date_time)=(DATE_SUB(CURRENT_DATE(),INTERVAL 0 DAY))) as day0,
					(SELECT count(incid) FROM ".$this->session->userdata('bid')."_keywordinbox WHERE keyword_id=k.keyword_id 
					AND date(date_time)=(DATE_SUB(CURRENT_DATE(),INTERVAL 1 DAY))) as day1,
					(SELECT count(incid) FROM ".$this->session->userdata('bid')."_keywordinbox WHERE keyword_id=k.keyword_id 
					AND date(date_time)=(DATE_SUB(CURRENT_DATE(),INTERVAL 2 DAY))) as day2,
					(SELECT count(incid) FROM ".$this->session->userdata('bid')."_keywordinbox WHERE keyword_id=k.keyword_id 
					AND date(date_time)=(DATE_SUB(CURRENT_DATE(),INTERVAL 3 DAY))) as day3,
					(SELECT count(incid) FROM ".$this->session->userdata('bid')."_keywordinbox WHERE keyword_id=k.keyword_id 
					AND date(date_time)=(DATE_SUB(CURRENT_DATE(),INTERVAL 4 DAY))) as day4,
					(SELECT count(incid) FROM ".$this->session->userdata('bid')."_keywordinbox WHERE keyword_id=k.keyword_id 
					AND date(date_time)=(DATE_SUB(CURRENT_DATE(),INTERVAL 5 DAY))) as day5,
					(SELECT count(incid) FROM ".$this->session->userdata('bid')."_keywordinbox WHERE keyword_id=k.keyword_id 
					AND date(date_time)=(DATE_SUB(CURRENT_DATE(),INTERVAL 6 DAY))) as day6
					FROM keword k
					WHERE k.bid=".$this->session->userdata('bid'));
			if($sql->num_rows()>0)
			{
				$res=$sql->result_array();
			}	
			return $res;
		}	
	function total_calls(){
		$q='';
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (c.eid='".$this->session->userdata('eid')."' or g.eid='".$this->session->userdata('eid')."')";
		}	
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$this->session->userdata('bid')."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND c.pulse > '0'";
		}
		$date=date('Y-m-d',strtotime('-6 days'));
		$sl=$this->db->query("SELECT * FROM `".$this->session->userdata('bid')."_callhistory` c
							  LEFT JOIN ".$this->session->userdata('bid')."_groups g on c.gid=g.gid				
							  WHERE c.status!=2 and date(c.starttime)>='$date' $q");
		//echo $this->db->last_query();		  
		return $sl->num_rows();
	}
	function groupwise_todaycall(){
		$q=''; $p = '';
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (h.eid='".$this->session->userdata('eid')."' or g.eid='".$this->session->userdata('eid')."')";
		}
		$res=array();
		$date=date('Y-m-d',strtotime('-6 days'));
		$sql=$this->db->query("SELECT g.gid,g.groupname,COALESCE(h.cnt,0) as count FROM `".$this->session->userdata('bid')."_groups` g
							LEFT JOIN 
							(SELECT count(h.callid) as cnt,h.gid,h.eid 
							FROM ".$this->session->userdata('bid')."_callhistory h
							LEFT JOIN ".$this->session->userdata('bid')."_groups g on g.gid=h.gid
							WHERE h.status!=2 and date(h.starttime)>='".date('Y-m-d',strtotime('-6 days'))."' ".$q." 
							GROUP BY h.gid) h on g.gid=h.gid WHERE 1 $q AND h.cnt>0");
		//echo $this->db->last_query();
		if($sql->num_rows()>0)
		{
			$res=$sql->result_array();
		}	
		return $res;
	}	
	function callbytime(){
		$q='';
		$c1='';
		$p1='';
		$iv1='';
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (c.eid='".$this->session->userdata('eid')."' or g.eid='".$this->session->userdata('eid')."')";
		}
		if($this->session->userdata('filter')){
			$s = $this->session->userdata('filter');
		}
		//print_r($s);
		$res=array();
		$date=(isset($s['stime']) && $s['stime']!='')?$s['stime']:date('Y-m-d',strtotime('-6 days'));
		$c1=(isset($s['etime']) && $s['etime']!='')?" AND date(c.starttime)<='".$s['etime']."'":'';
		$sql=$this->db->query("SELECT c.gid,hour(c.starttime) as hor,count(*) as cnt,g.groupname
							FROM ".$this->session->userdata('bid')."_callhistory c
							left join ".$this->session->userdata('bid')."_groups g on c.gid=g.gid
							WHERE date(c.starttime)>='".$date."' $c1 $q
							AND c.source='calltrack' 
							GROUP BY c.gid,hour(c.starttime)");
		if($sql->num_rows()>0){
			$rec=$sql->result_array();
			$gid = '';
			foreach($rec as $r){
				if($gid != $r['gid']){
					$gid = $r['gid'];
					$res[$gid] = array('name'=>$r['groupname']);
				}
				$res[$gid][$r['hor']] = $r['cnt'];
				//$res[$r['gid']] = array($r['hor']=>$r['cnt'],'name'=>$r['groupname']);
				//echo "<pre>";print_r($res);echo "</pre>";
			}
		}	
		return $res;
		
	}
	function callbyweek(){
		$q='';
		$c1='';
		
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (c.eid='".$this->session->userdata('eid')."' or g.eid='".$this->session->userdata('eid')."')";
		}
		if($this->session->userdata('filter')){
			$s = $this->session->userdata('filter');
		}
		$res=array();
		$date=(isset($s['stime']) && $s['stime']!='')?$s['stime']:date('Y-m-d',strtotime('-6 days'));
		$c1=(isset($s['etime']) && $s['etime']!='')?" AND date(c.starttime)<='".$s['etime']."'":'';
		$sql=$this->db->query("SELECT c.gid,DAYNAME(c.starttime) as hor,count(*) as cnt,g.groupname
							FROM ".$this->session->userdata('bid')."_callhistory c
							left join ".$this->session->userdata('bid')."_groups g on c.gid=g.gid
							WHERE date(c.starttime)>='".$date."' $c1 $q
							AND c.source='calltrack'
							GROUP BY c.gid,DAYNAME(c.starttime)");
		if($sql->num_rows()>0){
			$rec=$sql->result_array();
			$gid = '';
			foreach($rec as $r){
				if($gid != $r['gid']){
					$gid = $r['gid'];
					$res[$gid] = array('name'=>$r['groupname']);
				}
				$res[$gid][$r['hor']] = $r['cnt'];
				//$res[$r['gid']] = array($r['hor']=>$r['cnt'],'name'=>$r['groupname']);
				//echo "<pre>";print_r($res);echo "</pre>";
			}
		}	
		return $res;
		
	}
	function callbyregion(){
		$q='';
		$c1='';
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (c.eid='".$this->session->userdata('eid')."' or g.eid='".$this->session->userdata('eid')."')";
		}
		if($this->session->userdata('filter')){
			$s = $this->session->userdata('filter');
		}
		$res=array();
		$date=(isset($s['stime']) && $s['stime']!='')?$s['stime']:date('Y-m-d',strtotime('-6 days'));
		$c1=(isset($s['etime']) && $s['etime']!='')?" AND date(c.starttime)<='".$s['etime']."'":'';
		$query = "
								SELECT s.area,count(*) as cnt
								FROM ".$this->session->userdata('bid')."_callhistory c
								left join ".$this->session->userdata('bid')."_groups g on c.gid=g.gid
								LEFT JOIN series_list1 s on 
								(substr(c.callfrom,1,4)=s.scode
								OR substr(c.callfrom,1,3)=s.scode
								OR substr(c.callfrom,1,2)=s.scode)
								WHERE date(c.starttime)>='".$date."' $c1 $q
								GROUP BY s.area";
		$sql=$this->db->query($query);
		if($sql->num_rows()>0){
			$rec=$sql->result_array();
			$gid = '';$count=0;

			$res = array();
			foreach($rec as $skey=>$r){
				if(count($res)>0)
				{
					if(array_key_exists($r['area'],$res))
					{
						$res[$r['area']] = $res[$r['area']]+$r['cnt'];
					}
					else
					{
						$res[$r['area']] = isset($res[$r['area']])?$res[$r['area']]+$r['cnt']:$r['cnt'];
					}
				}
				else
				{
					$res[$r['area']] = $r['cnt'];
				}
			}
		}	
		return $res;
	}
	function keyword_his(){
		$res=array();
		$date=date('Y-m-d');
		
		$sql=$this->db->query("SELECT k.keyword,k.from as number,se.code 
								FROM `".$this->session->userdata('bid')."_keywordinbox` k
								LEFT JOIN (SELECT code,codeid FROM shortcode) se on k.code_id=se.codeid 
								order by k.incid desc limit 0,20");
		if($sql->num_rows()>0)
		{
			$res=$sql->result_array();
		}	
		return $res;
	}	
	function recent_calls(){
		$res=array();
		$date=date('Y-m-d',strtotime('-6 days'));
		$q = "";
		//$q = ($this->session->userdata('eid')!='1')? " AND g.eid='".$this->session->userdata('eid')."'":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (h.eid='".$this->session->userdata('eid')."' or g.eid='".$this->session->userdata('eid')."')";
		}
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$this->session->userdata('bid')."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND h.pulse > '0'";
		}
		$sql=$this->db->query("SELECT g.groupname,COALESCE(h.cnt,0) as count 
							FROM `".$this->session->userdata('bid')."_groups` g
							LEFT JOIN (SELECT count(callid) as cnt,h.gid,h.eid 
							FROM ".$this->session->userdata('bid')."_callhistory h
							LEFT JOIN ".$this->session->userdata('bid')."_groups g on g.gid=h.gid
							where date(h.starttime)>='".$date."' $q GROUP BY h.gid) h on g.gid=h.gid WHERE 1 ");
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}	
		return $res;
	}	
	function empgroupwise_todaycalls(){
		$res=array();
		$q='';
		$date=date('Y-m-d',strtotime('-6 days'));
		//$q = ($this->session->userdata('eid')!='1')? " AND eid='".$this->session->userdata('eid')."'":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (h.eid='".$this->session->userdata('eid')."' or g.eid='".$this->session->userdata('eid')."')";
		}
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$this->session->userdata('bid')."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND h.pulse > '0'";
		}
		
		$sql=$this->db->query("SELECT e.eid,e.status,e.empname,COALESCE(h.cnt,0) as count 
								FROM ".$this->session->userdata('bid')."_employee e
								LEFT JOIN (SELECT count(h.callid) as cnt,h.eid 
								FROM ".$this->session->userdata('bid')."_callhistory h
								LEFT JOIN ".$this->session->userdata('bid')."_groups g on h.gid=g.gid
								WHERE h.status!=2 and date(h.starttime)>='".$date."' $q GROUP BY h.eid) h on e.eid=h.eid 
								WHERE h.cnt>0
								ORDER BY e.empname");
		//echo $this->db->last_query();
		if($sql->num_rows()>0){
			$res=$sql->result_array();
			$uncount=0;
			foreach($res as $ikey=>$r){
				$esql=$this->db->query("SELECT e.eid,e.status,e.empname,COALESCE(h1.cnt,0) as count   
								FROM ".$this->session->userdata('bid')."_employee e
								LEFT JOIN (SELECT count(h.callid) as cnt,h.eid 
								FROM ".$this->session->userdata('bid')."_callhistory h
								LEFT JOIN ".$this->session->userdata('bid')."_groups g on h.gid=g.gid
								WHERE h.status!=2 and date(h.starttime)>='".$date."' $q GROUP BY h.callfrom,h.gid,h.eid) h1 on e.eid=h1.eid 
								WHERE e.eid = ".$r['eid']);
				if($esql->num_rows()>0){
					$eres=$esql->row();
					$uncount=$eres->count;
					$res[$ikey]['ucount'] = $eres->count;
					//$res[$ikey]['ucount'] = $esql->num_rows();

				}
				else{$res[$ikey]['ucount'] = 0;}			
			}
		}
		return $res;
	}
	function call_types($type,$c='0'){
		$date=date('Y-m-d',strtotime('-6 days'));
		$q=($type=='a')?"":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (a.eid='".$this->session->userdata('eid')."' or g.eid='".$this->session->userdata('eid')."')";
		}
		$q.=($type=='m')?" AND a.pulse=0":"";
		$q.=($type=='q')?" AND (a.callername!='' OR a.callerbusiness!='' OR a.calleraddress!='' OR a.caller_email!='' OR a.remark!='')":"";
		$q.=($type=='u')?" AND (a.callername='' AND a.callerbusiness='' AND a.calleraddress='' AND a.caller_email='' AND a.remark='')":"";
		$q.=($type=='at')?" AND a.pulse!=0":"";
		$q.= " AND date(a.starttime)>='".$date."' ";
		
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$this->session->userdata('bid')."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND a.pulse > '0'";
		}
		$group = ($c=='1') ? " GROUP BY a.callfrom,a.gid,a.eid ": "";
		
		$sql=$this->db->query("select count(a.callid) as cnt
								from ".$this->session->userdata('bid')."_callhistory a
								LEFT JOIN ".$this->session->userdata('bid')."_groups g on a.gid=g.gid
								WHERE a.status!=2 ".$q.$group);
		//echo $this->db->last_query()."<br>";
		return ($sql->num_rows()>0) ? (($c=='1')?$sql->num_rows():$sql->row()->cnt):0;
	}
	function last_calls(){
		$res=array();
		$res1=array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$query = "SELECT c.callfrom as callfrom,g.groupname as groupname,e.empname as empname,c.starttime as starttime 
				FROM `".$this->session->userdata('bid')."_callhistory` c
				LEFT JOIN ".$this->session->userdata('bid')."_groups g on c.gid=g.gid
				LEFT JOIN ".$this->session->userdata('bid')."_employee e on c.eid=e.eid
				WHERE c.status!=2 AND date(c.starttime)>=(DATE_SUB(CURRENT_DATE(),INTERVAL 6 DAY))";
		if($roleDetail['role']['admin']!=1){
			$query .= " AND (c.eid='".$this->session->userdata('eid')."' or g.eid='".$this->session->userdata('eid')."')";
		}		
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$this->session->userdata('bid')."'")->row()->lead_generate;
		if($type == '0'){
			$query .= " AND c.pulse > '0'";
		}
		$query .= " UNION ALL ";
		$query .= "SELECT pr.callfrom as callfrom,p.title as groupname,'' as empname,pr.starttime as starttime 
				FROM `".$this->session->userdata('bid')."_pbxreport` pr
				LEFT JOIN ".$this->session->userdata('bid')."_pbx p on p.pbxid=pr.pbxid
				WHERE date(pr.starttime)>=(DATE_SUB(CURRENT_DATE(),INTERVAL 6 DAY))";
		$query.=" UNION ALL ";			
		$query.="SELECT i.callfrom as callfrom,iv.title as groupname,'' as empname,i.datetime as starttime 
				FROM `".$this->session->userdata('bid')."_ivrshistory` i
				LEFT JOIN ".$this->session->userdata('bid')."_ivrs iv on i.ivrsid=iv.ivrsid and iv.bid=".$this->session->userdata('bid')."
				WHERE date(i.datetime)>=(DATE_SUB(CURRENT_DATE(),INTERVAL 6 DAY))";
					
		$query .= "	ORDER BY starttime DESC";
		$sql=$this->db->query($query);
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}	
		return $res;
	}
	function call_last_calls(){
		$res=array();
		$res1=array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$query = "SELECT c.callfrom as callfrom,g.groupname as groupname,e.empname as empname,c.starttime as starttime 
				FROM `".$this->session->userdata('bid')."_callhistory` c
				LEFT JOIN ".$this->session->userdata('bid')."_groups g on c.gid=g.gid
				LEFT JOIN ".$this->session->userdata('bid')."_employee e on c.eid=e.eid
				WHERE c.status!=2 AND date(c.starttime)>=(DATE_SUB(CURRENT_DATE(),INTERVAL 6 DAY))";
		if($roleDetail['role']['admin']!=1){
			$query .= " AND (c.eid='".$this->session->userdata('eid')."' or g.eid='".$this->session->userdata('eid')."')";
		}		
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$this->session->userdata('bid')."'")->row()->lead_generate;
		if($type == '0'){
			$query .= " AND c.pulse > '0'";
		}
		$query .= "	ORDER BY starttime DESC";
		$sql=$this->db->query($query);
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}	
		return $res;
	}
	function returning_custmer(){
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$res=array();
		$q = " WHERE c.status!=2  AND date(c.starttime)>=(DATE_SUB(CURRENT_DATE(),INTERVAL 6 DAY))";
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (c.eid='".$this->session->userdata('eid')."' or g.eid='".$this->session->userdata('eid')."')";
		}
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$this->session->userdata('bid')."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND c.pulse > '0'";
		}
		$sql=$this->db->query("SELECT c.callfrom,c.callid,g.groupname,e.empname
							   FROM ".$this->session->userdata('bid')."_callhistory c
							   LEFT JOIN ".$this->session->userdata('bid')."_groups g on c.gid=g.gid
							   LEFT JOIN ".$this->session->userdata('bid')."_employee e on c.eid=e.eid
							   ".$q."
							   GROUP BY c.callfrom,c.gid,c.eid
							   HAVING count(c.callid)>1 
							   ORDER BY c.starttime DESC");
		//echo $this->db->last_query();
		if($sql->num_rows()>0)
		{
			$res=$sql->result_array();
		}	
		return $res;	
	}
	function for_pieinfo($type)
	{
		$date=date('Y-m-d',strtotime('-6 days'));
		$q=($type=='a')?"":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (a.eid='".$this->session->userdata('eid')."' or d.eid='".$this->session->userdata('eid')."')";
		}
		$lead = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$this->session->userdata('bid')."'")->row()->lead_generate;
		if($lead == '0'){
			$q .= " AND a.pulse > '0'";
		}
		$q.=($type=='m')?" AND a.pulse=0":'';
		$q.=($type=='q')?" AND (a.callername!='' OR a.callerbusiness!='' OR a.calleraddress!='' OR a.caller_email!='' OR a.remark!='')":'';
		$q.=($type=='u')?" AND (a.callername='' AND a.callerbusiness='' AND a.calleraddress='' AND a.caller_email='' AND a.remark='')":'';
		$q.=($type=='at')?" AND a.pulse!=0":"";
		
		$sql="select count(a.callid) as cnt,d.groupname 	
					from ".$this->session->userdata('bid')."_callhistory a
					left join ".$this->session->userdata('bid')."_employee c on a.eid=c.eid
					left join ".$this->session->userdata('bid')."_groups d on a.gid=d.gid
					where a.status!=2 and date(a.starttime)>='$date' $q GROUP BY a.gid ORDER BY a.gid";
		
		$rst = $this->db->query($sql)->result_array();
		return $rst;				
						
	}
	function getSmsreport($ofset='0',$limit='20'){
		
		$q='where 1';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		if($this->session->userdata('senderid')!=""){
			$senderid=$this->session->userdata('senderid');
		}
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (s.eid='".$this->session->userdata('eid')."' or e.eid='".$this->session->userdata('eid')."')";
		}
		$q.=(isset($s['number']) && $s['number']!='')?" and s.number like '%".$s['number']."%'":"";
		$q.=(isset($s['datefroms']) && $s['datefroms']!='')?" and date(s.datetime)>='".$s['datefrom']."'":"";
		$q.=(isset($s['dateto']) && $s['dateto']!='')?" and date(s.datetime)<='".$s['dateto']."'":"";
		$res=array();
		$res['data']=$this->db->query("select SQL_CALC_FOUND_ROWS s.*,e.empname from ".$this->session->userdata('bid')."_smsreport s
										left join ".$this->session->userdata('bid')."_employee e on s.eid=e.eid
									  $q ORDER BY datetime desc
										LIMIT $ofset,$limit
								   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;
	}
	function get_smsreport(){
		$q='where 1';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		if($this->session->userdata('senderid')!=""){
			$senderid=$this->session->userdata('senderid');
		}
		$q.=(isset($s['senderid']) && $s['senderid']!='')?" and c.senderid like '%".$s['vnumber']."%'":"";
		$q.=(isset($s['datefroms']) && $s['datefroms']!='')?" and date(c.datetime)>='".$s['datefroms']."'":"";
		$q.=(isset($s['datetos']) && $s['datetos']!='')?" and date(c.datetime)<='".$s['datetos']."'":"";
		$q.=(isset($s['sstatus']) && $s['sstatus']!='')?" and s.status='".$s['sstatus']."'":"";
		
		$res=array();
		$res=$this->db->query("select SQL_CALC_FOUND_ROWS s.smsid,s.number,s.status,c.senderid,c.content,c.datetime,c.scheduleat,c.total from ".$this->session->userdata('bid')."_smsreport s LEFT JOIN sms_content c on s.contentid=c.contentid $q ")->result_array();
		return $res;
	}
	function getVoicereport($ofset='0',$limit='20'){
		$res=array();
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		if($this->session->userdata('sendername')!=""){
			$sendername=$this->session->userdata('sendername');
		}
		$q.=(isset($s['vnumber']) && $s['vnumber']!='')?" and v.number like '%".$s['vnumber']."%'":"";
		$q.=(isset($s['sdatef']) && $s['sdatef']!='')?" and date(v.scheduletime)>='".$s['sdatef']."'":"";
		$q.=(isset($s['sdatet']) && $s['sdatet']!='')?" and date(v.scheduletime)<='".$s['sdatet']."'":"";
		$q.=(isset($s['soundid']) && $s['soundid']!='')?" and s.soundid='".$s['sdatet']."'":"";
		$res['data']=$this->db->query("select v.number,v.scheduletime,v.starttime,v.endtime,v.dtmf,s.title,b.soundid from ".$this->session->userdata('bid')."_voicereport v 
										LEFT JOIN broadcast b on v.brid=b.drid
										LEFT JOIN sounds s on s.soundid=b.soundid where v.dtmf!='' $q
										LIMIT $ofset,$limit
								   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;
	}
	function getBVoicereport($ofset='0',$limit='20'){
		$res=array();
		$q='where 1';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		if($this->session->userdata('sendername')!=""){
			$sendername=$this->session->userdata('sendername');
		}
		$q.=(isset($s['vnumber']) && $s['vnumber']!='')?" and c.number like '%".$s['vnumber']."%'":"";
		$q.=(isset($s['sdatef']) && $s['sdatef']!='')?" and date(c.scheduletime)>='".$s['sdatef']."'":"";
		$q.=(isset($s['sdatet']) && $s['sdatet']!='')?" and date(c.scheduletime)<='".$s['sdatet']."'":"";
		
		$res['data']=$this->db->query("SELECT b.*,c.*,s.title FROM broadcast b
									LEFT JOIN (SELECT count(vcid) as rep,brid,scheduletime,number FROM `".$this->session->userdata('bid')."_voicereport` WHERE dtmf!='' GROUP BY brid) as c
									on c.brid=b.drid
									LEFT JOIN sounds s on s.soundid=b.soundid $q
									LIMIT $ofset,$limit
								   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;
	}
	function get_voicereport(){
		$res=array();
		$q="where 1";
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		if($this->session->userdata('sendername')!=""){
			$sendername=$this->session->userdata('sendername');
		}
		$q.=(isset($s['vnumber']) && $s['vnumber']!='')?" and v.number like '%".$s['vnumber']."%'":"";
		$q.=(isset($s['sdatef']) && $s['sdatef']!='')?" and date(v.scheduletime)>='".$s['sdatef']."'":"";
		$q.=(isset($s['sdatet']) && $s['sdatet']!='')?" and date(v.scheduletime)<='".$s['sdatet']."'":"";
		$q.=(isset($s['soundid']) && $s['soundid']!='')?" and s.soundid='".$s['sdatet']."'":"";
		$res=$this->db->query("select v.number,v.scheduletime,v.starttime,v.endtime,v.dtmf,s.title,b.soundid from ".$this->session->userdata('bid')."_voicereport v 
										LEFT JOIN broadcast b on v.brid=b.drid
										LEFT JOIN sounds s on s.soundid=b.soundid $q ")->result_array();
		return $res;
	}
	function incomingcalls_csv($type,$bid){
		$DB2 = (in_array($bid,array('257','538'))) ? $this->load->database('download', TRUE) : $this->load->database('download1', TRUE);
		$res=array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$q= " WHERE 1 ";
		$q.=($type=='all')?"":"";
		$q.=($type=='MissedTrackReport')?" AND a.pulse=0":"";
		$q.=($type=='QualTrackReport')?" AND (a.callername!='' OR a.callerbusiness!='' OR a.calleraddress!='' OR a.caller_email!='' OR a.remark!='')":"";
		$q.=($type=='UnQualTrackReport')?" AND (a.callername='' AND a.callerbusiness='' AND a.calleraddress='' AND a.caller_email='' AND a.remark='')":"";
		$q.=($type=='AttTrackReport')?" AND a.pulse!=0":"";
		$type = $DB2->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND a.pulse > '0'";
		}
		if($_POST['endtimes']!=""){
			$q.=" AND date(a.starttime)<='".$_POST['endtimes']."'" ;
		}
		if($_POST['starttimes']!=""){
			$q.=" and date(a.starttime)>='".$_POST['starttimes']."'";
		}
		if(!empty($_POST['groupname'])){
			if($_POST['groupname'][0]!=""){
			$q.=" and a.gid in (".implode(",",$_POST['groupname']).")";
			}
		}
		if(!empty($_POST['empname'])){
			if($_POST['empname'][0]!=""){
			$q.=" and a.eid in(".implode(",",$_POST['empname']).")";
			}
		}
		$ge = '';
		if($roleDetail['role']['admin']!=1){
			if($roleDetail['role']['roleid']==4){
				$q .= " AND ge.eid is not null ";
				$ge = " LEFT JOIN ".$bid."_group_emp ge ON (d.gid=ge.gid AND ge.eid= '".$this->session->userdata('eid')."') ";
			}elseif($roleDetail['role']['owngroup']=='1'){
				$q .= " AND (a.eid='".$this->session->userdata('eid')."' 
						OR d.eid='".$this->session->userdata('eid')."' 
						OR a.assignto='".$this->session->userdata('eid')."')";
			}
		}
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = $roleDetail['role']['recordlimit'];
		$csv_output = "";
		$ke=array();
		foreach($_POST['lisiting'] as $key=>$val){
			$hkey[]=$key;
			$header[]=$val;
			if($key=='pulse'){
				$hkey[]='duration';
				$header[]='Duration';
			}
		}
		$csv_output .=implode(",",$header)."\n";
		$sql="SELECT SQL_CALC_FOUND_ROWS * FROM
				(SELECT a.callid,a.starttime,'ct' as type
				FROM ".$bid."_callhistory a
				LEFT JOIN ".$bid."_employee c on a.eid=c.eid
				LEFT JOIN ".$bid."_groups d on a.gid=d.gid
				$ge  $q
				UNION
				SELECT a.callid,a.starttime,'ca' as type
				FROM ".$bid."_callarchive a
				LEFT JOIN ".$bid."_employee c on a.eid=c.eid
				LEFT JOIN ".$bid."_groups d on a.gid=d.gid $ge  $q) a
				ORDER BY a.starttime DESC
				LIMIT 0,$limit";		 
		$rst = $DB2->query($sql)->result_array();
		$total_record_count = $DB2->query($sql)->num_rows();
		$name = $bid.'_'.
				$this->session->userdata('eid').'_'.
				time();
		mkdir('reports/'.$name);
		chmod('reports/'.$name,0777);
		$files = array();
		$data_file = 'reports/'.$name.'/calls.csv';
		$fp = fopen($data_file,'w');
		fwrite($fp,$csv_output);
		foreach($rst as $rec){
			$data = array();
			$type = ($rec['type']=='ct') ? '6' : 'callarchive';
			$r = $this->configmodel->getDetail($type,$rec['callid'],'',$bid);
			$i=0;
			foreach($hkey as $k){
				$v=(isset($r[$k])) ? '"'.str_replace("\n"," ",$r[$k]).'"' : '';
				array_push($data,$v);
				if(isset($r[$k]) && $k=="filename" && $r[$k]!=''){
					$path="sounds/".$r[$k];
					if (file_exists($path))	{
						copy($path,'reports/'.$name.'/'.$r[$k]);
					}
				}
			}
			$csv_output =implode(",",$data)."\n";
			fwrite($fp,$csv_output);
		}
		fclose($fp);
/* mail functionality for download */
		$sql1 = $DB2->query("SELECT down_notify FROM account_settings WHERE bid='".$bid."'");
		$notify = '';
		if($sql1->num_rows() >0){
			$res = $sql1->row();
			$notify = $res->down_notify;
		}
		if($notify == 1){
			$sql = $DB2->query("SELECT empemail FROM ".$bid."_employee WHERE roleid=1 and status=1")->result_array();
			$sqlempemail = array();
			foreach($sql as $sql_emp) {
				$sqlempemail[] = $sql_emp['empemail'];
			}
			$sqlempemail_val = implode(",",$sqlempemail);
			$empname_arr = array();	
			$groupname_arr = array();
			if((isset($_POST['groupname'])) ){
				$mail_groupid = implode(",",$_POST['groupname']);
				if($mail_groupid!='') {
					$sql_groupname = $DB2->query("SELECT groupname FROM ".$bid."_groups WHERE gid IN (".$mail_groupid.")")->result_array();
					foreach($sql_groupname as $sqlgroupval) {
						$groupname_arr[] = $sqlgroupval['groupname'];
					}
				}
			}
			if(isset($_POST['empname'])){
				$mail_empid = implode(",",$_POST['empname']);
				$sql_empname = $DB2->query("SELECT empname FROM ".$bid."_employee WHERE eid IN (".$mail_empid.")")->result_array();
				foreach($sql_empname as $sqlempval) {
					$empname_arr[] = $sqlempval['empname'];
				}
			}
			if(isset($sqlempemail_val)){
				$message1  = "Export Records Details:<br/>";
				$message1 .= "<br/>Start Date:".$_POST['starttimes'];
				$message1 .= "<br/>End Date:".$_POST['endtimes'];
				$message1 .= "<br/>Group Name:".implode(",",$groupname_arr);
				$message1 .= "<br/>Employee Name:".implode(",",$empname_arr);
				$message1 .= "<br/>Executive Name:".$this->empgetname($this->session->userdata('eid'));
				$message1 .= "<br/>Total Records:".$total_record_count."<br><br>";
				$body = $this->emailmodel->newEmailBody($message1,'');
				$to  = $sqlempemail_val; 
				$subject = ' MCube Group Export Records Details ';
				$this->load->library('email');
				$this->email->from('noreply@mcube.com', 'MCube');
				$this->email->to($to);
				$this->email->subject($subject);
				$this->email->message($body);
				$this->email->send();
			}
		} 
		/*mail functionality for download */
		chdir('reports');
		exec('zip -r '.$name.'.zip '.$name);
		exec('rm -rf '.$name);
		return $name;
	}
	function Lastcalls(){
		$res=array();
		$sql=$this->db->query("SELECT pri.landingnumber,pri.used,pri.climit,
								if(pri.status=0,'Not in use',
								if(pri.type='0','Calltrack',
								if(pri.type='1','IVRS',
								if(pri.type='2','PBX','Not Assign')))) as type,
								pri.associateid,
								if(pri.status=0,'None',
								if(pri.type='0',g.groupname,
								if(pri.type='1',i.title,
								if(pri.type='2',pbx.title,'None')))) as title,
								if(pri.status=0,'None',
								if(pri.type='0',ch.lastcall,
								if(pri.type='1',ih.lastcall,
								if(pri.type='2',ph.lastcall,'None')))) as lastcall
								FROM prinumbers pri
								LEFT JOIN ".$this->session->userdata('bid')."_groups g on (g.prinumber=pri.number AND g.gid=pri.associateid AND g.status=1)
								LEFT JOIN ".$this->session->userdata('bid')."_ivrs i on (i.prinumber=pri.number AND i.ivrsid=pri.associateid AND i.status=1)
								LEFT JOIN ".$this->session->userdata('bid')."_pbx pbx on (pbx.prinumber=pri.number AND pbx.pbxid=pri.associateid AND pbx.status=1)
								LEFT JOIN (SELECT g.gid,max(h.starttime) as lastcall FROM ".$this->session->userdata('bid')."_groups g LEFT JOIN ".$this->session->userdata('bid')."_callhistory h on g.gid=h.gid GROUP BY g.gid) ch on g.gid=ch.gid
								LEFT JOIN (SELECT i.ivrsid,max(h.datetime) as lastcall FROM ".$this->session->userdata('bid')."_ivrs i LEFT JOIN ".$this->session->userdata('bid')."_ivrshistory h on i.ivrsid=h.ivrsid GROUP BY i.ivrsid) ih on i.ivrsid=ih.ivrsid
								LEFT JOIN (SELECT p.pbxid,max(h.starttime) as lastcall FROM ".$this->session->userdata('bid')."_pbx p LEFT JOIN ".$this->session->userdata('bid')."_pbxreport h on p.pbxid=h.pbxid GROUP BY p.pbxid) ph on pbx.pbxid=ph.pbxid
								WHERE pri.bid=".$this->session->userdata('bid'));
		if($sql->num_rows()>0)
		{
			$res=$sql->result_array();
		}	
		return $res;
	}
	function calltrack_lastcalls(){
		$res=array();
		$sql=$this->db->query("SELECT 
								pri.landingnumber,
								pri.used,
								pri.climit,
								if(pri.type='0','Calltrack','Not Assign') as type,
								pri.associateid,
								if(pri.type='0',g.groupname,'None') as title,
								if(pri.type='0',ch.lastcall,'None') as lastcall 
								FROM prinumbers pri 
								LEFT JOIN ".$this->session->userdata('bid')."_groups g on (g.prinumber=pri.number AND g.gid=pri.associateid AND g.status=1) 
								LEFT JOIN (SELECT g.gid,max(h.starttime) as lastcall FROM ".$this->session->userdata('bid')."_groups g LEFT JOIN ".$this->session->userdata('bid')."_callhistory h on g.gid=h.gid GROUP BY g.gid) ch on g.gid=ch.gid 
								WHERE pri.type='0' and pri.bid=".$this->session->userdata('bid'));
		if($sql->num_rows()>0)
		{
			$res=$sql->result_array();
		}	
		return $res;
	}
	function getAutodiallerReport($bid,$ofset,$limit){
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$con = "";
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		$s = ($this->session->userdata('search'))?$this->session->userdata('search'):'';
		$con .= (isset($s['title']) && $s['title']!='')?" AND i.title like '%".$s['title']."%'":"";
		$con .= (isset($s['prinumber']) && $s['prinumber']!='')?" AND p.landingnumber like '%".$s['ivrsnumber']."%'":"";
		$con .= (isset($s['operator']) && $s['operator']!='')?" AND i.operator ='".$s['operator']."'":"";
		$sql = "SELECT SQL_CALC_FOUND_ROWS l.title,h.*,a.number,a.others,e.empname 
				FROM ".$bid."_autodialhistory h
				LEFT JOIN ".$this->session->userdata('bid')."_autodialler a on h.did=a.did
				LEFT JOIN ".$this->session->userdata('bid')."_autodailerlist l on l.id=a.refer_id
				LEFT JOIN ".$this->session->userdata('bid')."_employee e on h.eid=e.eid
				ORDER BY h.calltime DESC
				limit $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		$fieldset = $this->configmodel->getFields('20');
		$keys = array('title','empname','number','calltime','endtime');
		$header = array('#'
						,'Title'
						,'Employee Name'
						,'Call To'
						,'Call at'
						,'Call End');
		$ret['header'] = $header;
		$list = array();
		$i = $ofset+1;
		foreach($rst as $rec){
			$data['sl'] = $i;
			foreach($keys as $k){
				$v = isset($rec[$k])?$rec[$k]:"";
				$data[$k]=$v;
			}
			$act = "";
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	
	/* creates a compressed zip file */
	function create_zip($files = array(),$destination = '',$overwrite = false) {
		//if the zip file already exists and overwrite is false, return false
		if(file_exists($destination) && !$overwrite) { return false; }
		//vars
		$valid_files = array();
		//if files were passed in...
		if(is_array($files)) {
			//cycle through each file
			foreach($files as $file) {
				//make sure the file exists
				if(file_exists($file)) {
					$valid_files[] = $file;
				}
			}
		}
		//if we have good files...
		if(count($valid_files)) {
			//create the archive
			$zip = new ZipArchive();
			if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
				return false;
			}
			//add the files
			foreach($valid_files as $file) {
				$zip->addFile($file,$file);
			}
			//debug
			//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
			
			//close the zip -- done!
			$zip->close();
			
			//check to make sure the file exists
			return file_exists($destination);
		}
		else
		{
			return false;
		}
	}
	function get_calldetails($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("SELECT * FROM ".$bid."_callhistory where callid='".$id."'");
		return $sql->row();
	}
	function get_condetails($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("SELECT * FROM ".$bid."_contact where contid='".$id."'");
		return $sql->result_array();
	}
	//~ function Addcontacts(){
		//~ $cbid=$this->session->userdata('cbid');
	    //~ $bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
	    //~ $arr=array_keys($_POST);
	    //~ $contid=$this->db->query("SELECT COALESCE(MAX(`contid`),0)+1 as id FROM ".$bid."_contact")->row()->id;
		//~ $this->db->set('contid',$contid);
				//~ for($i=0;$i<sizeof($arr);$i++){
//~ 
				//~ if($arr[$i]!="update_system" ){
						//~ /* Changed for custom fields */
						//~ //if($bid == 1 || $bid == 47  || $bid == 257){
							//~ if(is_array($_POST[$arr[$i]]))
								//~ echo $val = @implode(',',$_POST[$arr[$i]]);
							//~ elseif($_POST[$arr[$i]]!="")
								//~ echo $val=$_POST[$arr[$i]];
							//~ else
								//~ $val='';
							//~ $this->db->set($arr[$i],$val);
						//~ //}
						//~ /* Changed for custom fields end */
					//~ }
					//~ 
				//~ }
		//~ 
			//~ $this->db->insert($bid."_contact");
			//~ if(isset($_POST['custom'])){
			//~ $arrs=array_keys($_POST['custom']);
			//~ for($k=0;$k<sizeof($arrs);$k++){
				//~ 
				//~ if(is_array($_POST['custom'][$arrs[$k]])){
						//~ $x=implode(",",$_POST['custom'][$arrs[$k]]);
					//~ }
					//~ else{
						//~ $x=$_POST['custom'][$arrs[$k]];
					//~ }
					//~ if($x!=''){
						//~ $this->db->query("DELETE FROM ".$bid."_customfieldsvalue WHERE bid= '".$bid."' AND modid= '23' AND fieldid = '".$arrs[$k]."'");
						//~ $sql = "REPLACE INTO ".$bid."_customfieldsvalue SET
							 //~ bid			= '".$bid."'
							//~ ,modid			= '23'
							//~ ,fieldid		= '".$arrs[$k]."'
							//~ ,dataid			= '".$this->input->post('number')."'
							//~ ,value			= '".$x."'";
						//~ $this->db->query($sql);
					//~ }
				//~ }
			//~ }
			//~ $this->auditlog->auditlog_info('Contact',$this->input->post('name'). " New Contact added to Contact List ");
			//~ return true;
		//~ 
		//~ if($err>1){
			//~ return false;
		//~ }else{
			//~ return true;
		//~ }
	//~ }
	function Addcontacts(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		
		if (($handle = fopen($_FILES['filename']['tmp_name'],"r")) !== FALSE) {
			$i=0;
			$err=0;
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				
				if($i>0){
					$c = 0;
					$sql=$this->db->query("SELECT number from ".$bid."_contact where number='".$data['0']."'");
						if($sql->num_rows()==0){
								$contid=$this->db->query("SELECT COALESCE(MAX(`contid`),0)+1 as id FROM ".$bid."_contact")->row()->id;
								$this->db->set('contid',$contid);
								$this->db->set('bid',$bid);
								$this->db->set('name',isset($data['1']) ? $data['1'] : '');
								$this->db->set('remarks',isset($data['3']) ? $data['3'] : '');
								$this->db->set('email',isset($data['2']) ? $data['2'] : '');
								$this->db->set('number',isset($data['0']) ? $data['0'] : '');
								$this->db->insert($bid."_contact");
								$this->auditlog->auditlog_info('Contact',$data['1']. " New Contact added to Contact List ");
						}else{
								$err++;	
						}	
					}else{
						$i++;
						$fields = $data;
					}
			}
		}
		if($err>1){
			return false;
		}else{
			return true;
		}
	}
	function listcontacts($ofset='0',$limit='20'){
		$res=array();
		$q = 'WHERE 1';
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
		if(isset($s)){
			$arr = array_keys($s);
			for ($n =0;$n<count($arr);$n++){
				if(strstr($arr[$n],'c_')){
					if(is_array($s[$arr[$n]])){
						$s[$arr[$n]] = @implode(',',$s[$arr[$n]]);
					}
					$q.=(isset($s[$arr[$n]]) && $s[$arr[$n]]!='' && $s[$arr[$n]]!=' ') ? " AND ".$arr[$n]."= '".$s[$arr[$n]]."'":"";
				}
			}
		}
		$q.=(isset($s['name']) && $s['name']!='')?" and name like '%".$s['name']."%'":"";
		$q.=(isset($s['number']) && $s['number']!='')?" and number like '%".$s['number']."%'":"";
		$q.=(isset($s['email']) && $s['email']!='')?" and email like '%".$s['email']."%'":"";
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM ".$bid."_contact $q LIMIT $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;
	}
	function deleteContact($contid){
		$sql = $this->db->query("DELETE FROM ".$this->session->userdata('bid')."_contact WHERE contid=".$contid);
		$this->auditlog->auditlog_info('Contact',$contid. " Contact Deleted ");
		return true;
	}
	function editcontact($contid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$arr=array_keys($_POST);
		if($contid!=""){
			$res=$this->get_condetails($contid);
			for($i=0;$i<sizeof($arr);$i++){
				if($arr[$i]!="update_system" ){
					/* Changed for custom fields */
					if(is_array($_POST[$arr[$i]]))
						echo $val = @implode(',',$_POST[$arr[$i]]);
					elseif($_POST[$arr[$i]]!="")
						echo $val=$_POST[$arr[$i]];
					else
						$val='';
					$this->db->set($arr[$i],$val);
					/* Changed for custom fields end */
				}
			}
			$this->db->where('number',$contid);
			$this->db->update($bid."_contact");
			if(isset($_POST['custom'])){
			$arrs=array_keys($_POST['custom']);
			for($k=0;$k<sizeof($arrs);$k++){
				if(is_array($_POST['custom'][$arrs[$k]])){
						$x=implode(",",$_POST['custom'][$arrs[$k]]);
					}else{
						$x=$_POST['custom'][$arrs[$k]];
					}
					if($x!=''){
						$this->db->query("DELETE FROM ".$bid."_customfieldsvalue WHERE bid= '".$bid."' AND modid= '23' AND fieldid = '".$arrs[$k]."' and dataid= '".$contid."'");
						$sql = "REPLACE INTO ".$bid."_customfieldsvalue SET
							 bid			= '".$bid."'
							,modid			= '23'
							,fieldid		= '".$arrs[$k]."'
							,dataid			= '".$contid."'
							,value			= '".$x."'";
						$this->db->query($sql);
					}
				}
			}
			$this->auditlog->auditlog_info('Contact',$this->input->post('name'). " Contact Updated ");
			return true;
		}else{
			$contid=$this->db->query("SELECT COALESCE(MAX(`contid`),0)+1 as id FROM ".$bid."_contact")->row()->id;
			$this->db->set('contid',$contid);
			for($i=0;$i<sizeof($arr);$i++){
				if($arr[$i]!="update_system" ){
					/* Changed for custom fields */
					if(is_array($_POST[$arr[$i]]))
						$val = @implode(',',$_POST[$arr[$i]]);
					elseif($_POST[$arr[$i]]!="")
						$val=$_POST[$arr[$i]];
					else
						$val='';
					$this->db->set($arr[$i],$val);
					/* Changed for custom fields end */
				}
			}
			$this->db->insert($bid."_contact");
			if(isset($_POST['custom'])){
			$arrs=array_keys($_POST['custom']);
			for($k=0;$k<sizeof($arrs);$k++){
				if(is_array($_POST['custom'][$arrs[$k]])){
						$x=implode(",",$_POST['custom'][$arrs[$k]]);
					}else{
						$x=$_POST['custom'][$arrs[$k]];
					}
					if($x!=''){
						$this->db->query("DELETE FROM ".$bid."_customfieldsvalue WHERE bid= '".$bid."' AND modid= '23' AND fieldid = '".$arrs[$k]."'");
						$sql = "REPLACE INTO ".$bid."_customfieldsvalue SET
							 bid			= '".$bid."'
							,modid			= '23'
							,fieldid		= '".$arrs[$k]."'
							,dataid			= '".$this->input->post('number')."'
							,value			= '".$x."'";
						$this->db->query($sql);
					}
				}
			}
			$this->auditlog->auditlog_info('Contact',$this->input->post('name'). " New Contact added to Contact List ");
			return true;
		}
	}
	function uniqueNum($num){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("SELECT * FROM ".$bid."_contact WHERE number='".$num."'");
		if($sql->num_rows()>0){
			return "exists";
		}else{
			return "no";
		}
	}
	
	function archiveDownload($args){
		$bid 		= $args['bid'];
		$year 		= isset($args['year']) ? $args['year'] : '0';
		$month 		= isset($args['month']) ? $args['month'] : '0';
		$csv_output='';
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$q = " AND DATE_FORMAT(h.`starttime`,'%m')='".$month."'";
		$q .= " AND DATE_FORMAT(h.`starttime`,'%Y')='".$year."'";
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (h.eid='".$this->session->userdata('eid')."' or g.eid='".$this->session->userdata('eid')."')";
		}
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$this->session->userdata('bid')."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND h.pulse > '0'";
		}
		$sql = "SELECT SQL_CALC_FOUND_ROWS h.callid,if(h.pulse=0,1,0) missed
				FROM `".$this->session->userdata('bid')."_callarchive` h
				left join ".$this->session->userdata('bid')."_groups g on h.gid=g.gid
				WHERE h.status!=2 $q
				ORDER BY h.starttime DESC";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		$fieldset = $this->configmodel->getFields('6');
		$keys = array();
		$header = array();
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
					array_push($keys,'custom['.$field['fieldid'].']');
					array_push($header,$field['customlabel']);
				}
			}
		}
		$ret['header'] = $header;
		$csv_output .=implode(",",$header)."\n";
		$name = $bid.'_'.
				$this->session->userdata('eid').'_'.
				time();
		mkdir('reports/'.$name);
		chmod('reports/'.$name,0777);
		$list = array();
		$data_file = 'reports/'.$name.'/archive.csv';
		$fp = fopen($data_file,'a+');
		fwrite($fp,$csv_output);
		foreach($rst as $rec){
			$data = array();
			$r = $this->configmodel->getDetail('callarchive',$rec['callid']);
			foreach($keys as $k){
				$v = isset($r[$k])?'"'.$r[$k].'"':'""';	
				array_push($data,$v);
			}
			$csv_output =implode(",",$data)."\n";
			fwrite($fp,$csv_output);
		}
		fclose($fp);
		chdir('reports')."<br>";
		exec('zip -r '.$name.'.zip '.$name);
		exec('rm -rf '.$name);
		return $name.'.zip';
	}
	/* Function for Call Followups */
	function followUps(){
		$res=array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$bid = $this->session->userdata('bid');
		$eid = $this->session->userdata('eid');
		$where = ($roleDetail['role']['admin']!=1)?"(h.eid='".$eid."' or g.eid='".$eid."') AND" : "";
		$where2 = ($roleDetail['role']['admin']!=1)?"(i.eid='".$eid."' or f.eid='".$eid."') AND" : "";
		$where1 = ($roleDetail['role']['admin']!=1)?"(l.enteredby ='".$eid."' OR l.assignto='".$eid."' or g.eid='".$eid."') AND" : "";
		$where4 = ($roleDetail['role']['admin']!=1)?"(t.assignto='".$eid."' or g.eid='".$eid."') AND" : "";
		$where3 = ($roleDetail['role']['admin']!=1)?" e.eid='".$eid."' AND" : "";
		$q="SELECT h.callername AS callername,h.callfrom as callfrom,f.followupdate as followupdate,f.callid as callid,e.eid as eid,e.empname as empname,g.gid as gid,g.groupname as groupname,f.type as source FROM ".$bid."_followup f LEFT JOIN ".$bid."_callhistory h on f.callid = h.callid LEFT JOIN ".$bid."_groups g on h.gid = g.gid LEFT JOIN ".$bid."_employee e on e.eid=h.eid
		WHERE $where f.type='calltrack' and f.followupdate >= CURRENT_DATE() AND f.followupdate <= (CURRENT_DATE()+INTERVAL 6 DAY) 
		UNION
		SELECT i.name as callername,i.callfrom as callfrom,f.followupdate as followupdate,f.callid as callid,e.eid as eid,e.empname as empname,iv.ivrsid as gid,iv.title as groupname,f.type as source from ".$bid."_followup f
		LEFT JOIN ".$bid."_ivrshistory i on f.callid =i.hid
		LEFT JOIN ".$bid."_ivrs iv on i.ivrsid = iv.ivrsid
		LEFT JOIN ".$bid."_employee e on e.eid=f.eid
		WHERE $where2 f.type='ivrs' and f.followupdate >= CURRENT_DATE() AND f.followupdate <= (CURRENT_DATE()+INTERVAL 6 DAY)
		UNION
		select p.name as callername,p.callfrom as callfrom,f.followupdate as followupdate,f.callid as callid,e.eid as eid,e.empname as empname,px.pbxid as gid,px.title as groupname,f.type as source from ".$bid."_followup f
		LEFT JOIN ".$bid."_pbxreport p on f.callid =p.callid
		LEFT JOIN ".$bid."_pbx px on p.pbxid=px.pbxid
		LEFT JOIN ".$bid."_employee e on e.eid=f.eid WHERE $where3 f.type='pbx' and f.followupdate >= CURRENT_DATE() AND f.followupdate <= (CURRENT_DATE()+INTERVAL 6 DAY)
		UNION 
		SELECT l.name as callername,l.number as callfrom,lf.followupdate as followupdate,l.leadid as callid,e.eid as eid,e.empname as empname,g.gid as gid,g.groupname as groupname,lf.type as source
		FROM ".$bid."_followup lf 
		LEFT JOIN ".$bid."_leads l on lf.callid = l.leadid 
		LEFT JOIN ".$bid."_leads_groups g on l.gid = g.gid 
		LEFT JOIN ".$bid."_employee e on e.eid=l.assignto
		WHERE $where1  lf.followupdate >= CURRENT_DATE() 
		AND lf.followupdate <= (CURRENT_DATE()+INTERVAL 6 DAY)
		AND  lf.type='leads'
		UNION
		SELECT t.name as callername,t.number as callfrom,f.followupdate as followupdate,t.tktid as callid,e.eid as eid,e.empname as empname,g.gid as gid,g.groupname as groupname,f.type as source FROM ".$bid."_followup f
		LEFT JOIN ".$bid."_support_tickets t on f.callid = t.tktid 
		LEFT JOIN ".$bid."_support_groups g on t.gid = g.gid 
		LEFT JOIN ".$bid."_employee e on e.eid=t.assignto
		WHERE $where4 f.type='support' AND f.followupdate >= CURRENT_DATE() 
		AND f.followupdate <= (CURRENT_DATE()+INTERVAL 6 DAY)
		
		";
		$sql=$this->db->query($q);
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}	
		return $res;
	}
	function call_followUps(){
		$res=array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$bid = $this->session->userdata('bid');
		$eid = $this->session->userdata('eid');
		$where = ($roleDetail['role']['admin']!=1)?"(h.eid='".$eid."' or g.eid='".$eid."') AND" : "";
		$where2 = ($roleDetail['role']['admin']!=1)?"(i.eid='".$eid."' or f.eid='".$eid."') AND" : "";
		$where1 = ($roleDetail['role']['admin']!=1)?"(l.assignto='".$eid."' or g.eid='".$eid."') AND" : "";
		$where4 = ($roleDetail['role']['admin']!=1)?"(t.assignto='".$eid."' or g.eid='".$eid."') AND" : "";
		$where3 = ($roleDetail['role']['admin']!=1)?" e.eid='".$eid."' AND" : "";
		$q="SELECT 
			h.callername AS callername,
			h.callfrom as callfrom,
			f.*,
			e.eid as eid,
			e.empname as empname,
			g.gid as gid,
			g.groupname as groupname,
			f.type as source 
			FROM 
			".$bid."_followup f
			LEFT JOIN ".$bid."_callhistory h on f.callid = h.callid 
			LEFT JOIN ".$bid."_groups g on h.gid = g.gid 
			LEFT JOIN ".$bid."_employee e on e.eid=h.eid
			WHERE  f.type='calltrack' and f.followupdate >= CURRENT_DATE() AND f.followupdate <= (CURRENT_DATE()+INTERVAL 6 DAY)";
		$sql=$this->db->query($q);
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}	
		
		return $res;
	}
	/***************************  Click To Call Module *******************************/
	function getClickToCalls($bid,$ofset='0',$limit='20'){
		$q= '';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		if($this->uri->segment(3)==""){
             $this->session->unset_userdata('search');
             $s = array();
        }
		$q .=(isset($s['gid']) && $s['gid']!='')?" and a.gid = '".$s['gid']."'":"";
		$q .=(isset($s['empid']) && $s['empid']!='')?" and a.eid = '".$s['empid']."'":"";
		$q .=(isset($s['custnumber']) && $s['custnumber']!='')?" and a.custnumber like '%".$s['custnumber']."%'":"";
		$q .=(isset($s['custname']) && $s['custname']!='')?" and a.custname like '%".$s['custname']."%'":"";
		$q .=(isset($s['custemail']) && $s['custemail']!='')?" and a.custemail like '%".$s['custemail']."%'":"";
		$q .=(isset($s['starttime']) && $s['starttime']!='')?" and date(a.starttime)>= '".$s['starttime']."'":"";
		$q .=(isset($s['endtime']) && $s['endtime']!='')?" and date(a.endtime)<= '".$s['endtime']."'":"";
		$q .=(isset($s['pulse']) && $s['pulse']!='')?" and if(a.pulse>0,ceil(a.pulse/60),a.pulse) ".$s['ptype']." '".$s['pulse']."'":"";	
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (a.eid='".$this->session->userdata('eid')."' or d.eid='".$this->session->userdata('eid')."')";
		}
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND a.pulse > '0'";
		}
		$sql = "SELECT SQL_CALC_FOUND_ROWS *  FROM ".$bid."_c2c a 
				LEFT JOIN ".$bid."_groups d on a.gid=d.gid 
				LEFT JOIN ".$bid."_employee e on a.eid=e.eid
				WHERE 1 $q limit $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='25'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('25',$bid);
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
					array_push($keys,'custom['.$field['fieldid'].']');
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
			$r = $this->configmodel->getDetail('25',$rec['callid'],'',$bid);
			foreach($keys as $k){
				if($k=="callfrom" && $rec['hid']!=0){
					$v='<a href="ivrs/calldetail/'.$rec['hid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r[$k].'</a>';
				}elseif($k=='eid'){
					$v = '<a href="Employee/activerecords/'.$r['eid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r['empname'].'</a>';
				}elseif($k=='gid'){
					$v = '<a href="group/activerecords/'.$r['gid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r['groupname'].'</a>';
				}else{
					$v = isset($r[$k])?nl2br(wordwrap($r[$k],60,"\n")):"";	
				}
				array_push($data,$v);
			}
			if($opt_add || $opt_view || $opt_delete){
				$act = ($opt_add) ?'<a href="Report/c2cedit/'.$rec['callid'].'"><span title="Edit" class="fa fa-edit"></span></a>':'';
				$act .= ($opt_delete) ? '<a href="'.base_url().'Report/c2c_delete/'.$rec['callid'].'" class="deleteClass"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
				$act .= '<a href="Report/c2cActive/'.$rec['callid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="fa fa-file-text"  title="List Employee Group"></span></a>';
				$act .= ($roleDetail['role']['accessrecords']=='0') ? (($rec['filename']!='' && file_exists('sounds/'.$rec['filename']))
					?'<a target="_blank" href="'.site_url('sounds/'.$rec['filename']).'"><span title="Sound" class="fa fa-volume-up"></span></a>'
					:'<span class="glyphicon glyphicon-volume-off"></span> '):"";
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function update_clicktocall($id,$bid){
		$itemDetail = $this->configmodel->getDetail('25',$id,'',$bid);
		$rate=0;
		$val='';
		$arr=array_keys($_POST);
		for($i=0;$i<sizeof($arr);$i++){
			if($arr[$i]!="update_system" && $arr[$i]!="custom" && $arr[$i]!="convertaslead" && $arr[$i]!="saveascontact"){
				if($_POST[$arr[$i]]!=""){$val=$_POST[$arr[$i]];}else{$val='';}
				$this->db->set($arr[$i],$val);
			}
		}
		$this->db->where('callid',$id);
		$this->db->update($bid.'_c2c'); 
		$res = $this->reportmodel->getContact_Info($itemDetail['custnumber'],$bid);
		if($res == 1){ 
			$cbid = $this->session->userdata('cbid');
			$bid = (isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
			$contid = $this->db->query("SELECT COALESCE(MAX(`contid`),0)+1 as id FROM ".$bid."_contact")->row()->id;
			$this->db->set('contid',$contid);
			$this->db->set('bid',$bid);
			$this->db->set('name',$this->input->post('custname'));
			$this->db->set('email',$this->input->post('custemail'));
			$this->db->set('number',$itemDetail['custnumber']);
			$this->db->set('remarks',"Click to call converted ");
			$this->db->insert($bid."_contact");
		}
		$this->auditlog->auditlog_info('Report',$id. "Click to Call Details updated by ".$this->session->userdata('username'));
		if(isset($_POST['custom'])){
			foreach($_POST['custom'] as $fid=>$val){
				if($val!=''){
					$this->db->query("DELETE FROM ".$bid."_customfieldsvalue where bid= '".$bid."' and modid= '25' and fieldid		= '".$fid."' and dataid= '".$id."'");
					$sql = "REPLACE INTO ".$bid."_customfieldsvalue SET
							 bid			= '".$bid."'
							,modid			= '25'
							,fieldid		= '".$fid."'
							,dataid			= '".$id."'
							,value			= '".(is_array($val)?implode(',',$val):$val)."'";
					$this->db->query($sql);
				}
				
			}
		}
		if($this->input->post('convertaslead')){
			if(isset($itemDetail['eid'])){ 
				$cbid = $this->session->userdata('cbid');
				$bid = (isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
				$leadid = $this->db->query("SELECT COALESCE(MAX(`leadid`),0)+1 as id FROM ".$bid."_leads")->row()->id;
				$this->db->set('leadid',$leadid);
				$this->db->set('bid',$bid);
				$this->db->set('gid',$itemDetail['gid']);
				$this->db->set('assignto',$itemDetail['eid']);
				$this->db->set('enteredby',$this->session->userdata('eid'));
				$this->db->set('leadowner',$this->session->userdata('eid'));
				$this->db->set('name',$this->input->post('custname'));
				$this->db->set('email',$this->input->post('custemail'));
				$this->db->set('number',$itemDetail['custnumber']);
				$this->db->set('source','clikc to call');
				$this->db->set('createdon',date("Y-m-d H:i:s"));
				$this->db->set('status',1);
				$this->db->insert($bid."_leads");
			}
			
		}
		if(isset($_POST['custom'])){
			$fids = array_keys($_POST['custom']);
			$sql = "SELECT * FROM ".$bid."_customfields WHERE fieldid in (".implode(',',$fids).") 
					AND fieldtype in ('checkbox','dropdown','radio')";
			$fields = $this->db->query($sql)->result_array();
			if(count($fields)>0){
				$itemDetailNew = $this->configmodel->getDetail('6',$id,'',$bid);
				$fieldset = $this->configmodel->getFields('6',$bid);
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
						//echo $val[1];exit;
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
		return 1;
	}
	function delete_clicktocall($id,$bid){
		$this->db->set('status', '2');
		$this->db->where('callid',$id);
		$this->db->update($bid."_c2c");
		$this->auditlog->auditlog_info('Report',$id. " Deleted By ".$this->session->userdata('username'));
		return 1;	
	}
	function deletedC2CList($bid,$ofset='0',$limit='20',$type='a'){//echo $limit;
		$q= '';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		if($this->uri->segment(3)==""){
             $this->session->unset_userdata('search');
             $s = array();
        }
		$q.=(isset($s['gid']) && $s['gid']!='')?" and a.gid = '".$s['gid']."'":"";
		$q.=(isset($s['empid']) && $s['empid']!='')?" and a.eid = '".$s['empid']."'":"";
		$q.=(isset($s['custnumber']) && $s['custnumber']!='')?" and a.custnumber like '%".$s['custnumber']."%'":"";
		$q.=(isset($s['custname']) && $s['custname']!='')?" and a.custname like '%".$s['custname']."%'":"";
		$q.=(isset($s['custemail']) && $s['custemail']!='')?" and a.custemail like '%".$s['custemail']."%'":"";
		$q.=(isset($s['starttime']) && $s['starttime']!='')?" and date(a.starttime)>= '".$s['starttime']."'":"";
		$q.=(isset($s['endtime']) && $s['endtime']!='')?" and date(a.endtime)<= '".$s['endtime']."'":"";
		$q.=(isset($s['pulse']) && $s['pulse']!='')?" and if(a.pulse>0,ceil(a.pulse/60),a.pulse) ".$s['ptype']." '".$s['pulse']."'":"";	
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (a.eid='".$this->session->userdata('eid')."' or d.eid='".$this->session->userdata('eid')."')";
		}
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND a.pulse > '0'";
		}
		$sql = "SELECT * FROM ".$bid."_c2c a 
				LEFT JOIN ".$bid."_groups d on a.gid=d.gid 
				WHERE 1 $q";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='25'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('25',$bid);
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
					array_push($keys,'custom['.$field['fieldid'].']');
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
			$r = $this->configmodel->getDetail('25',$rec['callid'],'',$bid);
			foreach($keys as $k){
				if($k=="callfrom" && $rec['hid']!=0){
					$v='<a href="ivrs/calldetail/'.$rec['hid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r[$k].'</a>';
				}
				elseif($k=='eid'){
					$v = '<a href="Employee/activerecords/'.$r['eid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r['empname'].'</a>';
				}elseif($k=='gid'){
					$v = '<a href="group/activerecords/'.$r['gid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r['groupname'].'</a>';
				}else{
						$v = isset($r[$k])?nl2br(wordwrap($r[$k],60,"\n")):"";	
				}
				array_push($data,$v);
			}
			if($opt_add || $opt_view || $opt_delete){
				$act = '<a href="'.base_url().'Report/UnDelC2C/'.$r['callid'].'"><img src="system/application/img/icons/undelete.png" style="vertical-align:top;" title="Delete" /></a>';
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function UnDelc2c($callid,$bid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$this->db->set('status','1');
		$this->db->where('callid',$callid);
		$this->db->update($bid."_c2c");
		return true;
	}
	function click2calls_csv($eid='',$bid){
		$res=array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$q= " WHERE 1 ";
		if($_POST['endtimes']!=""){
			$q.=" AND date(a.starttime)<='".$_POST['endtimes']."'" ;
		}
		if($_POST['starttimes']!=""){
			$q.=" and date(a.starttime)>='".$_POST['starttimes']."'";
		}
		if(!empty($_POST['groupname'])){
			if($_POST['groupname'][0]!=""){
			$q.=" and a.gid in (".implode(",",$_POST['groupname']).")";
			}
		}
		if(!empty($_POST['empname'])){
			if($_POST['empname'][0]!=""){
			$q.=" and a.eid in(".implode(",",$_POST['empname']).")";
			}
		}
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (a.eid='".$this->session->userdata('eid')."' or d.eid='".$this->session->userdata('eid')."')";
		}
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = $roleDetail['role']['recordlimit'];
		$csv_output = "";
		foreach($_POST['lisiting'] as $key=>$fiels){
			if($key=='custom'){
				foreach($_POST['lisiting']['custom'] as $key=>$fiels){
					$hkey[]=$fiels;
					$header[]=$fiels;
				}
			}else{
				$hkey[]=$key;
				$header[]=$fiels;
			}
		}
		$csv_output .=implode(",",$header)."\n";
		$sql="SELECT SQL_CALC_FOUND_ROWS a.callid
				FROM ".$bid."_c2c a
				left join ".$bid."_employee c on a.eid=c.eid
				left join ".$bid."_groups d on a.gid=d.gid $q
				ORDER BY a.starttime DESC limit 0,$limit";		 
		$rst = $this->db->query($sql)->result_array();
		$name = $bid.'_'.
				$this->session->userdata('eid').'_'.
				time();
		mkdir('reports/'.$name);
		chmod('reports/'.$name,0777);
		$files = array();
		foreach($rst as $rec){
			$data = array();
			$r = $this->configmodel->getDetail('25',$rec['callid'],'',$bid);
			foreach($hkey as $k){
				$v = isset($r[$k])? '"'.$r[$k].'"':"";
				array_push($data,$v);
				if(isset($r[$k]) && $k=="filename" && $r[$k]!=''){
					$path="sounds/".$r[$k];
					if (file_exists($path))	{
						copy($path,'reports/'.$name.'/'.$r[$k]);
					}
				}
			}
			$csv_output .=implode(",",$data)."\n";
		}
		
		$data_file = 'reports/'.$name.'/clicktocall.csv';
		$fp = fopen($data_file,'w');fwrite($fp,$csv_output);fclose($fp);
		$files[] = $data_file;
		//$result = $this->create_zip($files,'reports/'.$name.'.zip');
		chdir('reports')."<br>";
		exec('zip -r '.$name.'.zip '.$name);
		exec('rm -rf '.$name);
			
		return $name;
		
	}
	
		/*********************  Click to calls end *******************************/
		
	

	function Contacts_Csv($bid){
		//echo "<pre>";
		//print_r($_POST);exit;
		$res=array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$q= " WHERE 1 ";
		$limit = $roleDetail['role']['recordlimit'];

		////////////////////////////////////
		$csv_output = "";
		$ke=array();
		
		foreach($_POST['lisiting'] as $key=>$val){
			if($key=='custom'){
				foreach($val as $key=>$val){
					$header[]=$val;
					$hkey[]='custom['.$key.']';
				}
			}else{
				$hkey[]=$key;
				$header[]=$val;
			}
		}
		
		//print_r($hkey);exit;
		$csv_output .=implode(",",$header)."\n";
		
		$sql="SELECT SQL_CALC_FOUND_ROWS a.number
				FROM ".$bid."_contact a
				$q
				ORDER BY a.contid DESC limit 0,$limit";		 
		$rst = $this->db->query($sql)->result_array();
		$name = $bid.'_'.time();
		mkdir('reports/'.$name);
		chmod('reports/'.$name,0777);
		$files = array();
		foreach($rst as $rec){
			$data = array();
			$r = $this->configmodel->getDetail('23',$rec['number'],'',$bid);
			$i=0;
			foreach($hkey as $k){
				$v=(isset($r[$k])) ? '"'.$r[$k].'"' : '';
				array_push($data,$v);
				if(isset($r[$k]) && $k=="filename" && $r[$k]!=''){
					$path="sounds/".$r[$k];
					if (file_exists($path))	{
						copy($path,'reports/'.$name.'/'.$r[$k]);
					}
				}
			}
			$csv_output .=implode(",",$data)."\n";
		}
		$data_file = 'reports/'.$name.'/contacts.csv';
		$fp = fopen($data_file,'w');fwrite($fp,$csv_output);fclose($fp);
		chdir('reports');
		exec('zip -r '.$name.'.zip '.$name);
		exec('rm -rf '.$name);
		return $name;
	}
	function getLeadContact_Info($num,$bid){
		$sql=$this->db->query("SELECT leadid FROM ".$bid."_leads  WHERE number='".$num."'");
		if($sql->num_rows()>0){
			return '0';
		}else{
			return '1';
		}
	}
	function getContact_Info($num,$bid){
		$sql=$this->db->query("SELECT contid FROM ".$bid."_contact  WHERE number='".$num."'");
		if($sql->num_rows()>0){
			return '0';
		}else{
			return '1';
		}
	}
	
	function getDetail($bid,$dataid,$module){
		$ret = '0';
		$smsBal = $this->configmodel->callBalance($bid);
		if($smsBal<='0'){
			$ret = 'Insufficient Call Balance';
		}else{
			$empDetail = $this->configmodel->getDetail('2',$this->session->userdata('eid'),'',$bid);
			switch($module){
				case '1':// Track Report
					$callDetail = $this->configmodel->getDetail('6',$dataid,'',$bid);
					$ret = '1';
				break;
				case '2':// IVRS Report
					$callDetail = $this->configmodel->getDetail('16',$dataid,'',$bid);
					$ret = '1';
				break;
				case '3':// PBX Report
					$callDetail = $this->configmodel->getDetail('24',$dataid,'',$bid);
					$ret = '1';
				break;
				case '4':// Lead
					$callDetail = $this->configmodel->getDetail('26',$dataid,'',$bid);
					$callDetail['callfrom'] = $callDetail['number'];
					$ret = '1';
				break;
				//~ case '5':// Contact
					//~ $callDetail = $this->configmodel->getDetail('18',$dataid,'',$bid);
					//~ $callDetail['callfrom'] = $callDetail['number'];
					//~ $ret = '1';
				//~ break;
				case '5':// Contact
					$callDetail['callfrom'] = $dataid;
					$ret = '1';
				break;
				//~ case '6':// Campaign Report
					//~ $callDetail = $this->configmodel->getDetail('34',$dataid,'',$bid);
					//~ $callDetail['callfrom'] = $callDetail['caller_number'];
					//~ $ret = '1';
				//~ break;
				case '7':// Support
					$callDetail = $this->configmodel->getDetail('40',$dataid,'',$bid);
					$callDetail['callfrom'] = $callDetail['number'];
					$ret = '1';
				break;
				case '8':// outboundcall
					$callDetail = $this->configmodel->getDetail('45',$dataid,'',$bid);
					$callDetail['callfrom'] = $callDetail['contact_no'];
					$ret = '1';
				break;
				default:
					$ret = '0';
				break;
			}
		}
		//~ if($ret == '1'){
			//~ $st = (array)json_decode(file_get_contents("http://180.179.200.180/filter.php?num=".$empDetail['empnumber']));
			//~ $ret = ($st['dnd']=='1') ? 'Employee Number is DND' : '1';
		//~ }
		if($ret == '1'){
			$st = (array)json_decode(file_get_contents("http://180.179.200.180/filter.php?num=".substr($callDetail['callfrom'],-10,10)));
			$ret = ($st['dnd']=='1') ? 'Customer Number is DND' : '1';
		}
		if($ret == '1'){
			$callid = $empDetail['empnumber'].time();
			$sql = "INSERT INTO click2connect SET
					callid		='".$callid."'
					,bid		='".$bid."'
					,modid		='".$module."'
					,dataid		='".$dataid."'
					,exenumber	='".$empDetail['empnumber']."'
					,custnumber	='".substr($callDetail['callfrom'],-10,10)."'
					,eid		='".$empDetail['eid']."'";
			$this->db->query($sql);
		}
		return $ret;
	}
	
	 function outbound_calls_list($bid,$ofset='0',$limit='20'){
		$q=' WHERE 1';$s='';$q1 = '';$q2 = '';$q3 = '';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		$s = ($this->session->userdata('search'))?$this->session->userdata('search'):'';
		$q.= (isset($s['executive']) && $s['executive']!='' && $s['executive']!=' ')?" AND executive like '%".$s['executive']."%'":"";
		$q.= (isset($s['customer']) && $s['customer']!='' && $s['customer']!=' ')?" AND customer like '%".$s['customer']."%'":"";
		$q.= (isset($s['source']) && $s['source']!='' && $s['source']!=' ')?" AND modid='".$s['source']."'":"";
		$q.= (isset($s['stat']) && $s['stat']!='' && $s['stat']!=' ')?" AND status = '".$s['stat']."'":"";
		$q.=(isset($s['startTime']) && $s['startTime']!='')?" and date(starttime)>= '".$s['startTime']."'":"";
		$q.=(isset($s['endTime']) && $s['endTime']!='')?" and date(endtime)<= '".$s['endTime']."'":"";
		$q.= (isset($s['modid']) && $s['modid']!='')?" AND modid='".$s['modid']."'":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		if($roleDetail['role']['admin']!=1){
			$q1 .= " AND eid IN(
			SELECT ge.eid FROM ".$bid."_group_emp ge JOIN ".$bid."_groups g ON g.gid = ge.gid WHERE g.eid ='".$this->session->userdata('eid')."' AND g.status=1 )";
			$q2 .= " AND eid IN(SELECT ge.eid FROM ".$bid."_leads_grpemp ge JOIN ".$bid."_leads_groups g ON g.gid = ge.gid WHERE g.eid ='".$this->session->userdata('eid')."' AND g.status = 1) ";
			$q3 .= " AND eid IN(
			SELECT eid FROM ".$bid."_groups WHERE eid ='".$this->session->userdata('eid')."'
			UNION 
			SELECT eid FROM ".$bid."_leads_groups  WHERE eid ='".$this->session->userdata('eid')."')";		
		}
		$res=array();
		$sql = "SELECT SQL_CALC_FOUND_ROWS a.* FROM(SELECT o.* FROM ".$bid."_outboundcalls o $q $q1
				 UNION
				 SELECT o.* FROM ".$bid."_outboundcalls o $q $q2
				 UNION
				 SELECT p.* FROM ".$bid."_outboundcalls p $q $q3
				 ) a ORDER BY starttime DESC LIMIT $ofset,$limit";
		$res['data']=$this->db->query($sql)->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;
	}
	function sms_message($set_arr){
		$bid=$this->session->userdata('bid');
		$id=$this->db->query("SELECT COALESCE(MAX(`smsid`),0)+1 as id FROM ".$bid."_smsreport")->row()->id;
		$set_arr['smsid']=$id;
		$set_arr['eid']=$this->session->userdata('eid');
		$this->db->insert($bid."_smsreport",$set_arr);
		$this->sms_use('1');
	}
	function sms_use($use){
		$bid=$this->session->userdata('bid');
		$c=$this->db->query("SELECT * FROM credit_use WHERE bid='".$bid."'");
		if($c->num_rows()==0){
			$this->db->set('bid',$bid);
			$this->db->set('cr_used',$use);
			$this->db->insert('credit_use');
		}else{
			$this->db->query("UPDATE credit_use set cr_used=cr_used+$use where bid='".$bid."'");
		}
	}
	function sms_bal(){
		$bid=$this->session->userdata('bid');
		$sql=$this->db->query("SELECT balance FROM sms_bal  where bid='".$bid."'");
		if($sql->num_rows()>0){
			$res = $sql->row();
			return $res->balance;
		}else{
			return 0;
		}
	}
	function call_bal(){
		$bid=$this->session->userdata('bid');
		$sql=$this->db->query("SELECT balance FROM call_bal where bid='".$bid."'");
		if($sql->num_rows()>0){
			$res = $sql->row();
			return $res->balance;
		}else{
			return 0;
		}
	}
	function getfollowups($ofset,$limit,$bid){
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q = '';$con = '';
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$empreport = $this->getreportemp($this->session->userdata('eid'),$bid);
		if($roleDetail['role']['admin']!=1){
			$q .= ($empreport != '') ? " AND (f.eid=".$this->session->userdata('eid')." OR f.eid IN(".$empreport.") )" : " AND f.eid=".$this->session->userdata('eid');
		}
		if(!(isset($s['followupdate1']) && isset($s['followupdate2']))){
			$q .= " AND f.followupdate >= CURRENT_DATE() ";
		}
		if(isset($s)){
            $arr = array_keys($s);
            for ($n =0;$n<count($arr);$n++){
                if(strstr($arr[$n],'c_')){
					if(is_array($s[$arr[$n]])){
						$s[$arr[$n]] = @implode(',',$s[$arr[$n]]);
					}
                    $con .=(isset($s[$arr[$n]]) && $s[$arr[$n]]!='' && $s[$arr[$n]]!=' ') ? " AND f.".$arr[$n]."= '".$s[$arr[$n]]."'":"";
                }
            }
        }
		$q .= (isset($s['eid']) && $s['eid']!='' && $s['eid'] != 0) ? " AND f.eid = '".$s['eid']."'":"";
		$q .= (isset($s['comment']) && $s['comment']!='')?" AND f.comment LIKE '%".$s['comment']."%'":"";
		$q .= (isset($s['followupdate1']) && $s['followupdate1']!='')?" AND f.followupdate >= '".$s['followupdate1']."'":"";
		$q .= (isset($s['followupdate2']) && $s['followupdate2']!='')?" AND f.followupdate <= '".$s['followupdate2']."'":"";
		$q .= (isset($s['src1']) && $s['src1']!='')?" AND f.source = '".$s['src1']."'":"";
		$sql="SELECT SQL_CALC_FOUND_ROWS f.callid as detId,f.eid,e.empname,f.comment,f.cdate as date,
				   f.followupdate,f.id,f.type as source
				   FROM ".$bid."_followup f 
				   LEFT JOIN ".$bid."_employee e ON e.eid=f.eid 
				   WHERE 1 $q ".$con." LIMIT $ofset,$limit";
		$rs = $this->db->query($sql);
		$ret['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		$rst= $rs->result_array();
		$fieldset = $this->configmodel->getFields('29',$bid);
		$keys = array('empname',
					  'source',
					  'alert',
					);
	    $header = array('#',
						'Employee Name',
						'Call From',
						'Caller Name',
						'Source',
						'Alert Type',
					);

		foreach($fieldset as $field){
			
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){
					if($f['fieldid']==$field['fieldid'])$checked = true;
				}
				if($checked){
					if($field['fieldname'] != 'eid'){
					array_push($keys,$field['fieldname']);
					array_push($header,(($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
					}
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
		$alertArr = array("0"=>"No Alert","1"=>"Email","2"=>"SMS","3"=>"Email & SMS");
		$list = array();
		$i = ($ofset!=0)?$ofset+1:1;
		foreach($rst as $rec){
			$data = array($i);		
			$r = $this->configmodel->getDetail('29',$rec['id'],'',$bid);
			foreach($keys as $k){	
				$links='';
				$vals = '';
				if($k == 'alert')
					$v = $alertArr[$r[$k]];
				elseif($k == 'source'){
					switch($rec['source']){
					case 'calltrack':
						$sql = $this->db->query("SELECT h.callername AS callername,h.callfrom as callfrom FROM ".$bid."_callhistory h WHERE h.callid='".$r['detId']."'")->result_array();
						if(count($sql) > 0){
							$link="Report/activerecords/".$r['detId']."/1";
							$links = ($sql[0]['callfrom'] != '') ? "<a  class='btn-danger' data-toggle='modal' data-target='#modal-responsive' href='".$link."'>".$sql[0]['callfrom']."</a>" : ' ';
							$vals = ($sql[0]['callername'] != '') ?  $sql[0]['callername'] : ' ';
					    }
						break;
					case 'ivrs':
						$sql = $this->db->query("SELECT i.name as callername,i.callfrom as callfrom FROM ".$bid."_ivrshistory i WHERE i.hid='".$r['detId']."'")->result_array();
						if(count($sql) > 0){
							$link="ivrs/calldetail/".$r['detId']."/1";
							$links = ($sql[0]['callfrom'] != '') ? "<a class='btn-danger' data-toggle='modal' data-target='#modal-responsive' href='".$link."'>".$sql[0]['callfrom']."</a>" : ' ';
							$vals = ($sql[0]['callername'] != '') ?  $sql[0]['callername'] : ' ';
						}
						break;
					case 'pbx':
						 $sql = $this->db->query("SELECT p.name AS callername,p.callfrom as callfrom,p.pbxid FROM ".$bid."_pbxreport p WHERE p.callid='".$r['detId']."'")->result_array();
						 if(count($sql) > 0){
							 $link="pbx/detail/".$sql[0]['pbxid'];
							 $links = ($sql[0]['callfrom'] != '') ? "<a class='btn-danger' data-toggle='modal' data-target='#modal-responsive' href='".$link."'>".$sql[0]['callfrom']."</a>" : ' ';
							 $vals = ($sql[0]['callername'] != '') ?  $sql[0]['callername'] : ' ';
						}
						 break;
					case 'leads':
						 $sql = $this->db->query("SELECT l.name AS callername,l.number as callfrom,l.lead_status as type FROM ".$bid."_leads l WHERE leadid='".$r['detId']."'")->result_array();
						 if(count($sql) > 0){
							 $link="leads/active_lead/".$r['detId']."/".$sql[0]['type'];
							 $links = ($sql[0]['callfrom'] != '') ? "<a class='btn-danger' data-toggle='modal' data-target='#modal-responsive' href='".$link."'>".$sql[0]['callfrom']."</a>" : ' ';
							 $vals = ($sql[0]['callername'] != '') ?  $sql[0]['callername'] : ' ';
						 }
						 break;
					case 'support':
						 $sql = $this->db->query("SELECT s.name AS callername,s.number as callfrom FROM ".$bid."_support_tickets s WHERE tktid='".$rec['detId']."'")->result_array();
						 if(count($sql) > 0){
							 $link="support/activeSupportTkt/".$r['detId']."/1";
							 $links = ($sql[0]['callfrom'] != '') ? "<a class='btn-danger' data-toggle='modal' data-target='#modal-responsive' href='".$link."'>".$sql[0]['callfrom']."</a>" : ' ';
							 $vals = ($sql[0]['callername'] != '') ?  $sql[0]['callername'] : ' ';
						 }
						 break;
						 
					}
					array_push($data,$links);
					array_push($data,$vals);
					$v = $r[$k];
				}
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
function followupdownload($bid){
        $res=array();
        $roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
        $q = $q1 = '';
        if($_POST['endtimes']!=""){
            $q .= " AND date(f.followupdate)<='".$_POST['endtimes']."'" ;
            $q1 .= " AND date(lf.followupdate)<='".$_POST['endtimes']."'" ;
        }
        if($_POST['starttimes']!=""){
            $q .= " AND date(f.followupdate)>='".$_POST['starttimes']."'";
            $q1 .= " AND date(lf.followupdate)>='".$_POST['starttimes']."'";

        }
        $limit = $roleDetail['role']['recordlimit'];
        $csv_output = "";
        $header=array();
        array_push($header,"CallFrom ");
        array_push($header,"Caller Name ");
        foreach($_POST['lisiting'] as $key=>$val){
            if($key=='custom'){
                foreach($val as $key=>$val){
                    $header[]=$val;
                    $hkey[]='custom['.$key.']';
                }
            }else{
                $hkey[]=$key;
                $header[]=$val;
            }
        }
        array_push($header,"Source");
        $csv_output .=implode(",",$header)."\n";
        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM (SELECT f.*, h.callername AS callername,h.callfrom as callfrom,e.empname as empname,g.gid as gid,g.groupname as groupname 
        FROM ".$bid."_followup f
        LEFT JOIN ".$bid."_callhistory h on f.callid = h.callid 
        LEFT JOIN ".$bid."_groups g on h.gid = g.gid LEFT JOIN ".$bid."_employee e on e.eid=h.eid
        WHERE f.type='calltrack' $q
        UNION
        SELECT  f.*,i.name as callername,i.callfrom as callfrom,e.empname as empname,iv.ivrsid as gid,iv.title as groupname 
        FROM ".$bid."_followup f
        LEFT JOIN ".$bid."_ivrshistory i on f.callid =i.hid
        LEFT JOIN ".$bid."_ivrs iv on i.ivrsid = iv.ivrsid
        LEFT JOIN ".$bid."_employee e on e.eid=f.eid
        WHERE f.type='ivrs' $q
        UNION
        SELECT f.*,p.name as callername,p.callfrom as callfrom,e.empname as empname,px.pbxid as gid,px.title as groupname 
        FROM ".$bid."_followup f
        LEFT JOIN ".$bid."_pbxreport p on f.callid =p.callid
        LEFT JOIN ".$bid."_pbx px on p.pbxid=px.pbxid
        LEFT JOIN ".$bid."_employee e on e.eid=f.eid 
        WHERE f.type='pbx' $q
        UNION
        SELECT f.*,s.name as callername,s.number as callfrom,e.empname as empname,sg.gid as gid,sg.groupname as groupname 
        FROM ".$bid."_followup f
        LEFT JOIN ".$bid."_support_tickets s on f.callid =s.tktid
        LEFT JOIN ".$bid."_support_groups sg on s.gid=sg.gid
        LEFT JOIN ".$bid."_employee e on e.eid=f.eid 
        WHERE f.type='support' $q
        UNION
        SELECT f.*,l.name as callername,l.number as callfrom,e.empname as empname,g.gid as gid,g.groupname as groupname 
        FROM ".$bid."_followup f
        LEFT JOIN ".$bid."_leads l on f.callid = l.leadid
        LEFT JOIN ".$bid."_groups g on l.gid = g.gid
        LEFT JOIN ".$bid."_employee e on e.eid=l.assignto
        WHERE f.type='leads' $q ) a ORDER BY a.followupdate DESC
        LIMIT 0,$limit";
        $rst = $this->db->query($sql)->result_array();
        $name = $bid.'_'.
                $this->session->userdata('eid').'_'.
                time();
        mkdir('reports/'.$name, 0777);
        chmod('reports/'.$name,0777);
        $files = array();
        $alertArr = array("1"=>"Email","2"=>"SMS","3"=>"Email&SMS");
        foreach($rst as $rec){
            $data = array();
            $r = $rec;
            $i=0;
            $v=    $rec['callfrom'];
            array_push($data,$v);
            $v=    $rec['callername'];
            array_push($data,$v);
            foreach($hkey as $k){
                if($k == 'alert')
                    $v = $alertArr[$r[$k]];
                elseif($k == 'eid')
                    $v = $r['empname'];
                else if (strstr($k,'custom')){
                    $val = str_replace('custom[','',$k);
                    $val = str_replace(']','',$val);
                    $v = $this->db->query("SELECT value FROM ".$bid."_customfieldsvalue where bid= '".$bid."' AND modid= '29' AND fieldid= '".$val."'")->row()->value;
                }else
                    $v=(isset($r[$k])) ? '"'.str_replace("\n"," ",$r[$k]).'"' : '';
                array_push($data,$v);
            }
            $v=    $rec['type'];
            array_push($data,$v);
            $csv_output .=implode(",",$data)."\n";
        }

        $data_file = 'reports/'.$name.'/followups.csv';
        $fp = fopen($data_file,'w');fwrite($fp,$csv_output);fclose($fp);
        chdir('reports')."<br>";
        exec('zip -r '.$name.'.zip '.$name);
        exec('rm -rf '.$name);
        return $name;
    }



	function offlineusers(){
		$bid=$this->session->userdata('bid');
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$q = '';
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (e.eid='".$this->session->userdata('eid')."' or d.eid='".$this->session->userdata('eid')."')";
		}
		$sql=$this->db->query("SELECT DISTINCT(e.eid),e.empname,eb.start_time FROM ".$bid."_employee e
		                       LEFT JOIN ".$bid."_emp_break eb ON e.eid = eb.eid
		                       LEFT JOIN ".$bid."_groups d ON e.eid = d.eid
		                       WHERE eb.end_time='0000-00-00' AND e.selfdisable=1 $q GROUP BY d.gid,e.eid");
		if($sql->num_rows()>0){
			$res=$sql->result_array();
			return $res;
		}else{
			return array();
		}
	}
	function sendF($call_id,$bid,$mod,$type){
		$getDetail=$this->configmodel->getDetail($mod,$call_id,'',$bid);
        $fieldset = $this->configmodel->getFields($mod,$bid);
		if($type == 'calltrack'){
			if($getDetail['assignto']!=$_POST['asto']){
				$this->db->set('assignto',$_POST['asto']);
				$this->db->where('callid',$call_id);
				$this->db->update($bid."_callhistory");
			}
		}
	    $label = array();
	    $value  = array();
		$case=(isset($_POST['sendSMs']))?'1':'2';
		$message='';
		if($type == "ivrs"){
			$arr= array("Ivrs Title" => "Tit",
			"Call From" => "Frm",
			"Start Time" => "Stim",
			"End Time" => "Etim",
			"Options" => "Opt",
			"Employee" => "Emp",
			"Name" => "Name",
			"Email" => "Email");
	    }elseif($type == "pbx"){
			$arr=array("PBX title" => "Tit",
				"Call From" => "Frm",
				"Start Time" => "Stime",
				"End Time" => "Etime",
				"Options" => "Opt",
				"Name" => "Name",
				"Pulse" => "Pulse",
				"Email" => "Email",
				'Mcube Extensions'=>'Ext');
		 }elseif($type == "calltrack"){
			$arr=array(
				"Call From" => "Frm",
				"Group" => "Grp",
				"Employees" => "Emp",
				"Start Time" => "Stime",
				"End Time" => "Etime",
				"Options" => "Opt",
				"Caller Email" => "Email",
				"Caller Name" => "Name",
				"Call ID" => "ID",
				"Reference ID" => "RefID",
				"Assign To" => "Asgnto",
				"Last Modified" => "LstMod");
		 }elseif($type == "support"){
			 $arr=array(
				"Group Name " => "Grp",
				"Ticket Id" => "id",
				"Entered By" => "Entby",
				"Customer Name" => "Name",
				"Source" => "src",
				"Customer Email" => "CsEmail",
				"Customer Number" => "CsNbr",
				"Caller Address" => "Adr",
				"Caller Business" => "Buss",
				"Ticket Raised On" => "Raid",
				"Ticket Updated On" => "Upd",
				"Ticket Escalation Time" => "EscTm",
				"Ticket Level" => "Lvel",
				"Ticket Status" => "Sts",
				"Assign To" => "AsgnTo",
				"Auto Followup" => "Folwup");
		 }
		foreach($fieldset as $field){
			foreach($arr as $key => $valu) {
				if($field['customlabel'] == $key){
					$arr[$key] = $valu;
				}
			}
		}
		foreach($_POST['formfields'] as $form){
			$fs=explode("~",$form);
			$label = $fs[0];
			$value = $fs[1];
			if($arr[$label] != ''){
				$message.=$arr[$label].":".$value."<br/>";
			}else{
				$message.=$label.":".$value."<br/>";
			}
		}
		$empDetail=$this->configmodel->getDetail('2',$_POST['asto'],'',$bid);
		switch($case){
			case '1':
				if($_POST['smsBal']>0){
					$reply=sms_send($empDetail['empnumber'],$message);
					
					$set_array=array("contentid"=>(isset($reply[0]))?$reply[0]:'',
							 "number"=>$empDetail['empnumber'],
							"content"=>$message,
							"datetime"=>date('Y-m-d h:i:s'),
							"source"=> $type,
							"dnd_status"=>'0',
							"status"=>1);	
					$sms_push=$this->sms_message($set_array);
					return "SMS Sent Successfully";exit;
				}else{
					return "Fail to sent";exit;
				}
				break;
			case '2':	
					if($_POST['Emailconfig']>0){
						$itemDetail = $this->configmodel->getDetail('27',$bid,'',$bid);
						$config['protocol']    = 'smtp';
						$config['smtp_host']    = $itemDetail['smtp'];
						$config['smtp_port']    = $itemDetail['port'];
						$config['smtp_user']    = $itemDetail['email'];
						$config['smtp_pass']    = base64_decode($itemDetail['password']);
						$config['charset']    = 'utf-8';
						$config['newline']    = "\r\n";
						$config['mailtype'] = 'html'; // or html
						$config['validation'] = TRUE; // bool whether to validate email or not   
						$config['validation'] = TRUE; // bool whether to validate email or not      
						$this->email->initialize($config);
						$this->email->from($itemDetail['email'],$itemDetail['fname']);
						$this->email->to($empDetail['empemail']); 
						$this->email->subject("Field data from ". $empDetail['empemail']);
						$this->email->message($message);  
						$status=$this->email->send();
						$res=$this->configmodel->sentmails($bid,$itemDetail['email'],$empDetail['empemail'],$status);
						return "Email Sent Successfully";exit;
					}else{
						return "Fail to sent";exit;
					}
		}		
	}
	function getBreakTimings($bid,$ofset,$limit){
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q = '';
		//$custiom_ids=$this->configmodel->customSearch((isset($s['custom']))?$s['custom']:'','36',$bid);
		$q.=(isset($s['eid']) && $s['eid']!='')?" AND eb.eid = '".$s['eid']."'":"";
		$q.=(isset($s['start_time']) && $s['start_time']!='')?" AND eb.start_time >='".$s['start_time']."'":"";
		$q.=(isset($s['end_time']) && $s['end_time']!='')?" AND eb.end_time <= '".$s['end_time']."'":"";
		$q.=(isset($s['duration']) && $s['duration']!='')?" AND eb.duration <= '".$s['duration']."'":"";
		//$q.=(strlen($custiom_ids)>1)?" AND eb.id in(".$custiom_ids.")":"";
		//$q.=(!$custiom_ids)?" AND 0 ":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (eb.eid='".$this->session->userdata('eid')."' or d.eid='".$this->session->userdata('eid')."')";
		}
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND a.pulse > '0'";
		}
		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT(eb.id)
				FROM ".$bid."_emp_break eb 
				LEFT JOIN ".$bid."_employee e ON e.eid = eb.eid
				LEFT JOIN ".$bid."_groups d ON d.eid = eb.eid
				WHERE 1 $q ORDER BY eb.id DESC limit $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
//		echo $this->db->last_query();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;

		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='36'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('36',$bid);
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
					array_push($keys,'custom['.$field['fieldid'].']');
					array_push($header,$field['customlabel']);
				}
			}
		}
		$ret['header'] = $header;
		$list = array();
		$i = $ofset+1;
		foreach($rst as $rec){
			$data = array($i);
			$r = $this->configmodel->getDetail('36',$rec['id'],'',$bid);
			foreach($keys as $k){
				if($k=='eid'){
					$v = '<a href="Employee/activerecords/'.$r['empid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r['empname'].'</a>';
				}elseif($k == 'end_time' && $r[$k] == '0000-00-00 00:00:00'){
					$v = 'Employee still in break';
				}else{
						$v = isset($r[$k])?$r[$k]:"";	
				}
				array_push($data,$v);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function breakHisDownload($bid){
		$res=array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$q= " ";
		if($_POST['endtimes']!=""){
			$q.=" AND date(a.start_time)<='".$_POST['endtimes']."'" ;
		}
		if($_POST['starttimes']!=""){
			$q.=" and date(a.start_time)>='".$_POST['starttimes']."'";
		}
		if(!empty($_POST['empname'])){
			if($_POST['empname'][0]!=""){
			$q.=" and a.eid in(".implode(",",$_POST['empname']).")";
			}
		}
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (a.eid='".$this->session->userdata('eid')."' or d.eid='".$this->session->userdata('eid')."')";
		}
		$limit = $roleDetail['role']['recordlimit'];
		$csv_output = "";
		$ke=array();
		foreach($_POST['lisiting'] as $key=>$val){
			if($key=='custom'){
				foreach($val as $key=>$val){
					$header[]=$val;
					$hkey[]='custom['.$key.']';
				}
			}else{
				$hkey[]=$key;
				$header[]=$val;
			}
		}
		$csv_output .=implode(",",$header)."\n";
		$sql="SELECT SQL_CALC_FOUND_ROWS DISTINCT(a.id)
				FROM ".$bid."_emp_break a 
				LEFT JOIN ".$bid."_employee e ON e.eid = a.eid
				LEFT JOIN ".$bid."_groups d ON d.eid = a.eid
				WHERE 1 $q ORDER BY a.id DESC limit 0,$limit";	 
		$rst = $this->db->query($sql)->result_array();
		$name = $bid.'_'.
				$this->session->userdata('eid').'_'.
				time();
		
		mkdir('reports/'.$name);
		chmod('reports/'.$name,0777);
		$files = array();
		foreach($rst as $rec){
			$data = array();
			$r = $this->configmodel->getDetail('36',$rec['id'],'',$bid);
			$i=0;
			foreach($hkey as $k){
				if($k =='eid')
					$v = $r['empname'];
				else
					$v=(isset($r[$k])) ? '"'.$r[$k].'"' : '';
				array_push($data,$v);
				if(isset($r[$k]) && $k=="filename" && $r[$k]!=''){
					$path="sounds/".$r[$k];
					if (file_exists($path))	{
						copy($path,'reports/'.$name.'/'.$r[$k]);
					}
				}
			}
			$csv_output .=implode(",",$data)."\n";
		}
		$data_file = 'reports/'.$name.'/breakHistory.csv';
		$fp = fopen($data_file,'w');fwrite($fp,$csv_output);fclose($fp);
		chdir('reports')."<br>";
		exec('zip -r '.$name.'.zip '.$name);
		exec('rm -rf '.$name);
		return $name;
	}
	function blk_assignTo(){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$call_ids = @explode(',',$this->input->post('ids'));
		foreach($call_ids as $callids){
			$itemDetail = $this->configmodel->getDetail('6',$callids,'',$bid);
			$this->db->set('assignto',$this->input->post('empname'));
			$this->db->where('callid',$callids);
			$this->db->update($bid."_callhistory");
			$re=$this->Dup_calls($itemDetail['callfrom'],$bid);
			if($re->cnt>0){
				$this->db->set('assignto',$this->input->post('empname'));
				$this->db->where('callfrom',$itemDetail['callfrom']);
				$this->db->update($bid."_callhistory");
			}
		}
		return 1;
	}
	function Dup_calls($callfrom,$bid){
		$sql=$this->db->query("SELECT count(*) as cnt from ".$bid."_callhistory where callfrom='".$callfrom."'");
		return $sql->row();
	}
	function blk_down($bid){
		$csv_output = "";
		$hkey=array();
		foreach($_POST['lisiting'] as $key=>$fiels){
			$hkey[]=$key;
			$header[]=$fiels;
		}
		$csv_output = @implode(",",$header)."\n";
		$call_ids=explode(",",$_POST['call_ids']);
		$name = $bid.'_'.
				$this->session->userdata('eid').'_'.
				time();
		mkdir('reports/'.$name);
		chmod('reports/'.$name,0777);
		$data_file = 'reports/'.$name.'/calls.csv';
		$fp = fopen($data_file,'w');
		fwrite($fp,$csv_output);
		foreach($call_ids as $callids){
			$data = array();
			$r = $this->configmodel->getDetail('6',$callids,'',$bid);
			$i=0;
			foreach($hkey as $k){
				$v=(isset($r[$k])) ? '"'.$r[$k].'"' : '';
				array_push($data,$v);
				if(isset($r[$k]) && $k=="filename" && $r[$k]!=''){
					$path="sounds/".$r[$k];
					if (file_exists($path))	{
						copy($path,'reports/'.$name.'/'.$r[$k]);
					}
				}
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
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$empname=$this->db->query("SELECT empname FROM ".$bid."_employee WHERE eid='".$eid."'")->row()->empname;
		return ($empname != '') ? $empname : '';
	}
	
	
	
	 /******************function for ivrs Dashboard *************************/
	
	 /*
	 * 
	 * name: ivrs_last_calls
	 * @param
	 * @return last 7 days calls report
	 * 
	 */

	function ivrs_last_calls(){
		$res=array();
		$q='';
		$date=date('Y-m-d',strtotime('-6 days'));
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND c.eid=".$this->session->userdata('eid')." ";
		}
		$query="SELECT i.callfrom AS callfrom, iv.title AS groupname, e.empname AS empname
				FROM `".$this->session->userdata('bid')."_ivrshistory` i LEFT JOIN `".$this->session->userdata('bid')."_callhistory` c ON i.hid = c.callid
				LEFT JOIN `".$this->session->userdata('bid')."_ivrs` iv ON i.ivrsid = iv.ivrsid AND iv.bid =1
				LEFT JOIN `".$this->session->userdata('bid')."_employee` e ON c.eid = e.eid
				WHERE date( i.datetime ) >='$date' $q";	
					
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$this->session->userdata('bid')."'")->row()->lead_generate;
		if($type == '0'){
			$query .= " AND c.pulse > '0'";
		}
		$sql=$this->db->query($query);
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}	
		return $res;
	}
	
	/*
	 * 
	 * name: ivrs_total_calls
	 * @param
	 * @return total calls report of group for last 7 days
	 * 
	 */
	function ivrs_total_calls(){
		$q='';
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND c.eid=".$this->session->userdata('eid')." ";
		}	
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$this->session->userdata('bid')."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND c.pulse > '0'";
		}
		$date=date('Y-m-d',strtotime('-6 days'));
		$sl=$this->db->query("SELECT count( iv.ivrsid ) AS count FROM ".$this->session->userdata('bid')."_ivrshistory iv 
							  left join ".$this->session->userdata('bid')."_callhistory c on iv.hid=c.callid 
							  WHERE iv.status !=2 AND date( iv.datetime ) >='$date' $q ") ;
		if($sl->num_rows()>0)
		{
			$sll=$sl->result_array();
		}	
		return $sll;
	}
	
	 /*
	 * 
	 * name: ivrs_groupwise_todaycall
	 * @param
	 * @return last 7 days calls report of each group
	 * 
	 */
	function ivrs_groupwise_todaycall(){
		$q='';
		$res=array();
		$sql=$this->db->query("SELECT count( i.ivrsid ) as count ,iv.title as title FROM ".$this->session->userdata('bid')."_ivrshistory i
							 INNER JOIN ".$this->session->userdata('bid')."_ivrs iv ON i.ivrsid = iv.ivrsid
							 WHERE i.status !=2
							 AND 
							 date( i.datetime ) >= date_sub( current_date( ) , INTERVAL 6 DAY )
							 GROUP BY i.ivrsid");
		
		if($sql->num_rows()>0)
		{
			$res=$sql->result_array();
		}

		return $res;
	}
	/*
	 * 
	 * name: ivrs_Lastcalls
	 * @param
	 * @return landing no status of ivrs
	 * 
	 */
	function ivrs_Lastcalls(){

		$res=array();
		$sql=$this->db->query("
							SELECT 
							pri.landingnumber,
							pri.used,
							pri.climit,
							pri.associateid,
							if(pri.type='1','IVRS','Not Assign') as type,
							if(pri.type='1',i.title,'None') as title,
							if(pri.type='1',ih.lastcall,'None') as lastcall
							FROM prinumbers pri
							LEFT JOIN ".$this->session->userdata('bid')."_ivrs i on (i.prinumber=pri.number AND i.ivrsid=pri.associateid AND i.status=1)
							LEFT JOIN (SELECT i.ivrsid,max(h.datetime) as lastcall FROM ".$this->session->userdata('bid')."_ivrs i LEFT JOIN ".$this->session->userdata('bid')."_ivrshistory h on i.ivrsid=h.ivrsid GROUP BY i.ivrsid) ih on i.ivrsid=ih.ivrsid
							WHERE pri.type='1' and pri.bid=".$this->session->userdata('bid'));
		if($sql->num_rows()>0)
		{
			$res=$sql->result_array();
		}	
		return $res;
	}
	/*
	 * 
	 * name: ivrs_followUps
	 * @param
	 * @return follow up reports of ivrs
	 * 
	 */
	function ivrs_followUps(){
		$res=array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$bid = $this->session->userdata('bid');
		$eid = $this->session->userdata('eid');
		$where2 = ($roleDetail['role']['admin']!=1)?"(i.eid='".$eid."' or f.eid='".$eid."') AND" : "";
		$q="SELECT i.name as callername,i.callfrom as callfrom,f.followupdate as followupdate,f.callid as callid,e.eid as eid,e.empname as empname,iv.ivrsid as gid,iv.title as groupname,f.type as source from ".$bid."_followup f
		LEFT JOIN ".$bid."_ivrshistory i on f.callid =i.hid
		LEFT JOIN ".$bid."_ivrs iv on i.ivrsid = iv.ivrsid
		LEFT JOIN ".$bid."_employee e on e.eid=f.eid
		WHERE $where2 f.type='ivrs' and f.followupdate >= CURRENT_DATE() AND f.followupdate <= (CURRENT_DATE()+INTERVAL 6 DAY)";
		
		$sql=$this->db->query($q);
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}

		return $res;
	}
	
	/*
	 * 
	 * name: ivrs_callbytime
	 * @param
	 * @return ivrs call time to graph
	 * 
	 */
	function ivrs_callbytime(){
		$q='';
		$c1='';
		$p1='';
		$iv1='';
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND h.eid='".$this->session->userdata('eid')."' ";
		}
		if($this->session->userdata('filter')){
			$s = $this->session->userdata('filter');
		}
		//print_r($s);
		$res=array();
		$date=(isset($s['stime']) && $s['stime']!='')?$s['stime']:date('Y-m-d',strtotime('-6 days'));
		$iv1=(isset($s['etime']) && $s['etime']!='')?" AND date(i.datetime)<='".$s['etime']."'":'';
		$sql=$this->db->query("
							select i.ivrsid as gid,hour(i.datetime) as hor ,count(*) as cnt,iv.title as groupname
							FROM ".$this->session->userdata('bid')."_ivrshistory i
							LEFT JOIN ".$this->session->userdata('bid')."_ivrs iv on i.ivrsid=iv.ivrsid
							LEFT JOIN ".$this->session->userdata('bid')."_callhistory h ON i.hid = h.hid
							WHERE date(i.datetime)>='".$date."' $q $iv1
							GROUP BY i.ivrsid,hour(i.datetime)
						");
		if($sql->num_rows()>0){
			$rec=$sql->result_array();
			$gid = '';
			foreach($rec as $r){
				if($gid != $r['gid']){
					$gid = $r['gid'];
					$res[$gid] = array('name'=>$r['groupname']);
				}
				$res[$gid][$r['hor']] = $r['cnt'];
				//$res[$r['gid']] = array($r['hor']=>$r['cnt'],'name'=>$r['groupname']);
				//echo "<pre>";print_r($res);echo "</pre>";
			}
		}	
		return $res;
		
	}
	
	/*
	 * 
	 * name: ivrs_callbyweek
	 * @param
	 * @return ivrs call by week to graph
	 * 
	 */
	function ivrs_callbyweek(){
		$q='';
		$c1='';
		
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND h.eid='".$this->session->userdata('eid')."' ";
		}
		if($this->session->userdata('filter')){
			$s = $this->session->userdata('filter');
		}
		$res=array();
		$date=(isset($s['stime']) && $s['stime']!='')?$s['stime']:date('Y-m-d',strtotime('-6 days'));
		$iv1=(isset($s['etime']) && $s['etime']!='')?" AND date(i.datetime)<='".$s['etime']."'":'';
		$sql=$this->db->query("select i.ivrsid as gid,DAYNAME(i.datetime) as hor ,count(*) as cnt,iv.title as groupname
							FROM ".$this->session->userdata('bid')."_ivrshistory i
							LEFT JOIN ".$this->session->userdata('bid')."_ivrs iv on i.ivrsid=iv.ivrsid
							LEFT JOIN ".$this->session->userdata('bid')."_callhistory h ON i.hid = h.hid
							WHERE date(i.datetime)>='".$date."' $q $iv1
							GROUP BY i.ivrsid,DAYNAME(i.datetime)
							");
		if($sql->num_rows()>0){
			$rec=$sql->result_array();
			$gid = '';
			foreach($rec as $r){
				if($gid != $r['gid']){
					$gid = $r['gid'];
					$res[$gid] = array('name'=>$r['groupname']);
				}
				$res[$gid][$r['hor']] = $r['cnt'];
				//$res[$r['gid']] = array($r['hor']=>$r['cnt'],'name'=>$r['groupname']);
				//echo "<pre>";print_r($res);echo "</pre>";
			}
		}	
		return $res;
		
	}
	
	/*
	 * 
	 * name: ivrs_recent_calls
	 * @param
	 * @return ivrs recent_calls in pie chart
	 * 
	 */
	function ivrs_recent_calls(){
		$res=array();
		$date=date('Y-m-d',strtotime('-6 days'));
		$q = "";
		//$q = ($this->session->userdata('eid')!='1')? " AND g.eid='".$this->session->userdata('eid')."'":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND h.eid='".$this->session->userdata('eid')."' ";
		}
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$this->session->userdata('bid')."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND h.pulse > '0'";
		}
		
		$sql=$this->db->query("SELECT i.title AS groupname, coalesce( iv.cnt, 0 ) AS count
							   FROM ".$this->session->userdata('bid')."_ivrs i 
							   LEFT JOIN (SELECT count( iv.ivrsid ) AS cnt, iv.ivrsid 
							   FROM ".$this->session->userdata('bid')."_ivrshistory iv 
							   LEFT JOIN ".$this->session->userdata('bid')."_ivrs i ON iv.ivrsid = i.ivrsid
							   LEFT JOIN ".$this->session->userdata('bid')."_callhistory h ON iv.hid = h.hid
							   WHERE date( iv.datetime ) >= '".$date."' $q GROUP BY iv.ivrsid)iv ON i.ivrsid = iv.ivrsid WHERE 1  ");
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}	
		return $res;
	}
	
	/*
	 * 
	 * name: ivrs_groupnames
	 * @param
	 * @return ivrs For Last 7 Days
	 * 
	 */
	function ivrs_groupnames(){
		$ret=array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q = " AND (eid='".$this->session->userdata('eid')."' or g.eid='".$this->session->userdata('eid')."')";
		}else{
			$q = "";
		}
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$this->session->userdata('bid')."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND pulse > '0'";
		}
		
		$sql=$this->db->query("SELECT i.title as groupname,i.ivrsid as gid,
				(SELECT count(hid) FROM ".$this->session->userdata('bid')."_ivrshistory 
					WHERE ivrsid=i.ivrsid AND date(endtime)=(DATE_SUB(CURRENT_DATE(),INTERVAL 0 DAY))) as day0,
				(SELECT count(hid) FROM ".$this->session->userdata('bid')."_ivrshistory 
					WHERE ivrsid=i.ivrsid AND date(endtime)=(DATE_SUB(CURRENT_DATE(),INTERVAL 1 DAY))) as day1,
				(SELECT count(hid) FROM ".$this->session->userdata('bid')."_ivrshistory 
					WHERE ivrsid=i.ivrsid AND date(endtime)=(DATE_SUB(CURRENT_DATE(),INTERVAL 2 DAY))) as day2,
				(SELECT count(hid) FROM ".$this->session->userdata('bid')."_ivrshistory 
					WHERE ivrsid=i.ivrsid AND date(endtime)=(DATE_SUB(CURRENT_DATE(),INTERVAL 3 DAY))) as day3,
				(SELECT count(hid) FROM ".$this->session->userdata('bid')."_ivrshistory 
					WHERE ivrsid=i.ivrsid AND date(endtime)=(DATE_SUB(CURRENT_DATE(),INTERVAL 4 DAY))) as day4,
				(SELECT count(hid) FROM ".$this->session->userdata('bid')."_ivrshistory 
					WHERE ivrsid=i.ivrsid AND date(endtime)=(DATE_SUB(CURRENT_DATE(),INTERVAL 5 DAY))) as day5,
				(SELECT count(hid) FROM ".$this->session->userdata('bid')."_ivrshistory 
					WHERE ivrsid=i.ivrsid AND date(endtime)=(DATE_SUB(CURRENT_DATE(),INTERVAL 6 DAY))) as day6
		from ivrs i where i.bid=".$this->session->userdata('bid'));
		if($sql->num_rows()>0){
			$res=$sql->result_array();
			foreach($res as $rec){
				if($rec['day0']!='0' || $rec['day1']!='0' ||
				   $rec['day2']!='0' || $rec['day3']!='0' ||
				   $rec['day4']!='0' || $rec['day5']!='0' ||
				   $rec['day6']!='0'){
					$ret[] = $rec;
				}
			}
		}	
		return $ret;
	}
	 /******************end of ivrs Dashboard *************************/
	 
	 
	 
	  /******************function for pbx Dashboard starts *************************/
	  
	  
	 /*
	 * 
	 * name: pbx_last_calls
	 * @param
	 * @return last 7 days calls report
	 * 
	 */

	function pbx_last_calls()
	{
		$res=array();
		$res1=array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$query ="SELECT pr.callfrom as callfrom,p.title as groupname,'' as empname,pr.starttime as starttime 
				FROM `".$this->session->userdata('bid')."_pbxreport` pr
				LEFT JOIN ".$this->session->userdata('bid')."_pbx p on p.pbxid=pr.pbxid
				WHERE p.status !=2 AND date(pr.starttime)>=(DATE_SUB(CURRENT_DATE(),INTERVAL 6 DAY))";
				
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$this->session->userdata('bid')."'")->row()->lead_generate;
		if($type == '0'){
			$query .= " AND c.pulse > '0'";
		}
		$sql=$this->db->query($query);
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}	
		return $res;
	}
	
	/*
	 * 
	 * name: pbx_total_calls
	 * @param
	 * @return total calls report of group for last 7 days
	 * 
	 */
	function pbx_total_calls(){
		$q='';
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			//$q .= " AND (c.eid='".$this->session->userdata('eid')."' or g.eid='".$this->session->userdata('eid')."')";
		}	
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$this->session->userdata('bid')."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND c.pulse > '0'";
		}
		$date=date('Y-m-d',strtotime('-6 days'));
		$sl=$this->db->query("SELECT * FROM `".$this->session->userdata('bid')."_pbxreport` pr LEFT JOIN ".$this->session->userdata('bid')."_pbx p ON pr.pbxid = p.pbxid WHERE p.status !=2 AND date( pr.starttime ) >='$date' $q");					  
		return $sl->num_rows();
	}
	
	/*
	 * 
	 * name: PBX_groupwise_todaycall
	 * @param
	 * @return last 7 days calls report of each group
	 * 
	 */
	function pbx_groupwise_todaycall(){
		$q=''; $p = '';
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			//$q .= " AND (h.eid='".$this->session->userdata('eid')."' or g.eid='".$this->session->userdata('eid')."')";
		}
		$res=array();
		$date=date('Y-m-d',strtotime('-6 days'));
		$sql=$this->db->query("
							SELECT p.pbxid, p.title as groupname, COALESCE( pr.cnt, 0 ) AS count
							FROM `".$this->session->userdata('bid')."_pbx` p
							LEFT JOIN (
								SELECT count( pr.callid ) AS cnt, pr.pbxid
								FROM ".$this->session->userdata('bid')."_pbxreport pr
								LEFT JOIN ".$this->session->userdata('bid')."_pbx p ON pr.pbxid = p.pbxid
								WHERE date( pr.starttime ) >= '".date('Y-m-d',strtotime('-6 days'))."' ".$q."
								GROUP BY pr.pbxid)
							pr ON p.pbxid = pr.pbxid
							WHERE pr.cnt >0
							AND p.status !=2
							");
		if($sql->num_rows()>0)
		{
			$res=$sql->result_array();
		}	
		return $res;
	}
	
	/*
	 * 
	 * name: pbx_Lastcalls
	 * @param
	 * @return landing no status of ivrs
	 * 
	 */
	function pbx_Lastcalls(){

		$res=array();
		$sql=$this->db->query("
								SELECT pri.landingnumber, pri.used, pri.climit, pri.associateid, 
								if( pri.type = '2', 'PBX', 'Not Assign' ) AS type , 
								if( pri.type = '2', pbx.title, 'None' ) AS title, 
								if( pri.type = '2', ph.lastcall, 'None' ) AS lastcall
								FROM prinumbers pri
								LEFT JOIN ".$this->session->userdata('bid')."_pbx pbx ON ( pbx.prinumber = pri.number
								AND pbx.pbxid = pri.associateid
								AND pbx.status =1 )
								LEFT JOIN (SELECT p.pbxid, max( h.starttime ) AS lastcall
								FROM ".$this->session->userdata('bid')."_pbx p
								LEFT JOIN ".$this->session->userdata('bid')."_pbxreport h ON p.pbxid = h.pbxid
								GROUP BY p.pbxid
								)ph ON pbx.pbxid = ph.pbxid
								WHERE pri.type=2 AND pri.bid =".$this->session->userdata('bid'));
		if($sql->num_rows()>0)
		{
			$res=$sql->result_array();
		}	
		return $res;
	}
	
	/*
	 * 
	 * name: pbx_followUps
	 * @param
	 * @return follow up reports of ivrs
	 * 
	 */
	function pbx_followUps(){
		$res=array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$bid = $this->session->userdata('bid');
		$eid = $this->session->userdata('eid');
		$where3 = ($roleDetail['role']['admin']!=1)?" e.eid='".$eid."' AND" : "";
		$q="SELECT p.name as callername,p.callfrom as callfrom,f.followupdate as followupdate,f.callid as callid,e.eid as eid,e.empname as empname,px.pbxid as gid,px.title as groupname,f.type as source from ".$bid."_followup f
		LEFT JOIN ".$bid."_pbxreport p on f.callid =p.callid
		LEFT JOIN ".$bid."_pbx px on p.pbxid=px.pbxid
		LEFT JOIN ".$bid."_employee e on e.eid=f.eid WHERE $where3 f.type='pbx' and f.followupdate >= CURRENT_DATE() AND f.followupdate <= (CURRENT_DATE()+INTERVAL 6 DAY)
		";
		$sql=$this->db->query($q);
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}	
		return $res;
	}
	
	/*
	 * 
	 * name: pbx_callbyweek
	 * @param
	 * @return pbx call by week to graph
	 * 
	 */
	function pbx_callbyweek(){
		$q='';
		$c1='';
		
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (c.eid='".$this->session->userdata('eid')."' or g.eid='".$this->session->userdata('eid')."')";
		}
		if($this->session->userdata('filter')){
			$s = $this->session->userdata('filter');
		}
		$res=array();
		$date=(isset($s['stime']) && $s['stime']!='')?$s['stime']:date('Y-m-d',strtotime('-6 days'));
		$p1=(isset($s['etime']) && $s['etime']!='')?" AND date(p.starttime)<='".$s['etime']."'":'';
		$sql=$this->db->query("SELECT p.pbxid as gid ,DAYNAME(p.starttime) as hor,count(*) as cnt ,pp.title as groupname
							FROM ".$this->session->userdata('bid')."_pbxreport p
							LEFT JOIN ".$this->session->userdata('bid')."_pbx pp on p.pbxid=pp.pbxid
							WHERE pp.status!=2 AND date(p.starttime)>='".$date."' $p1
							GROUP BY p.pbxid,DAYNAME(p.starttime)
							");
		if($sql->num_rows()>0){
			$rec=$sql->result_array();
			$gid = '';
			foreach($rec as $r){
				if($gid != $r['gid']){
					$gid = $r['gid'];
					$res[$gid] = array('name'=>$r['groupname']);
				}
				$res[$gid][$r['hor']] = $r['cnt'];
				
			}
		}	
		return $res;
		
	}
	
	/*
	 * 
	 * name: pbx_callbytime
	 * @param
	 * @return pbx call time to graph
	 * 
	 */
	function pbx_callbytime(){
		$q='';
		$c1='';
		$p1='';
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (c.eid='".$this->session->userdata('eid')."' or g.eid='".$this->session->userdata('eid')."')";
		}
		if($this->session->userdata('filter')){
			$s = $this->session->userdata('filter');
		}
		//print_r($s);
		$res=array();
		$date=(isset($s['stime']) && $s['stime']!='')?$s['stime']:date('Y-m-d',strtotime('-6 days'));
		$p1=(isset($s['etime']) && $s['etime']!='')?" AND date(p.starttime)<='".$s['etime']."'":'';
		$sql=$this->db->query("
							SELECT p.pbxid as gid ,hour(p.starttime) as hor,count(*) as cnt ,pp.title as groupname
							FROM ".$this->session->userdata('bid')."_pbxreport p
							LEFT JOIN ".$this->session->userdata('bid')."_pbx pp on p.pbxid=pp.pbxid
							WHERE pp.status!=2 and date(p.starttime)>='".$date."' $p1
							GROUP BY p.pbxid,hour(p.starttime)
							");
		if($sql->num_rows()>0){
			$rec=$sql->result_array();
			$gid = '';
			foreach($rec as $r){
				if($gid != $r['gid']){
					$gid = $r['gid'];
					$res[$gid] = array('name'=>$r['groupname']);
				}
				$res[$gid][$r['hor']] = $r['cnt'];
				
			}
		}	
		return $res;
		
	}
	
	/*
	 * 
	 * name: pbx_groupnames
	 * @param
	 * @return pbx For Last 7 Days
	 * 
	 */
	function pbx_groupnames(){
		$ret=array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q = " AND (eid='".$this->session->userdata('eid')."' or g.eid='".$this->session->userdata('eid')."')";
		}else{
			$q = "";
		}
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$this->session->userdata('bid')."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND pulse > '0'";
		}
		
		$sql=$this->db->query("
				SELECT c.pbxid as gid,c.title as groupname,
				(SELECT count(callid) FROM ".$this->session->userdata('bid')."_pbxreport 
					WHERE pbxid=c.pbxid AND date(endtime)=(DATE_SUB(CURRENT_DATE(),INTERVAL 0 DAY))) as day0,
				(SELECT count(callid) FROM ".$this->session->userdata('bid')."_pbxreport 
					WHERE pbxid=c.pbxid AND date(endtime)=(DATE_SUB(CURRENT_DATE(),INTERVAL 1 DAY))) as day1,
				(SELECT count(callid) FROM ".$this->session->userdata('bid')."_pbxreport 
					WHERE pbxid=c.pbxid AND date(endtime)=(DATE_SUB(CURRENT_DATE(),INTERVAL 2 DAY))) as day2,
				(SELECT count(callid) FROM ".$this->session->userdata('bid')."_pbxreport 
					WHERE pbxid=c.pbxid AND date(endtime)=(DATE_SUB(CURRENT_DATE(),INTERVAL 3 DAY))) as day3,
				(SELECT count(callid) FROM ".$this->session->userdata('bid')."_pbxreport 
					WHERE pbxid=c.pbxid AND date(endtime)=(DATE_SUB(CURRENT_DATE(),INTERVAL 4 DAY))) as day4,	
				(SELECT count(callid) FROM ".$this->session->userdata('bid')."_pbxreport 
					WHERE pbxid=c.pbxid AND date(endtime)=(DATE_SUB(CURRENT_DATE(),INTERVAL 5 DAY))) as day5,
				(SELECT count(callid) FROM ".$this->session->userdata('bid')."_pbxreport 
					WHERE pbxid=c.pbxid AND date(endtime)=(DATE_SUB(CURRENT_DATE(),INTERVAL 6 DAY))) as day6
						from ".$this->session->userdata('bid')."_pbx c
			where c.bid=".$this->session->userdata('bid'));
		if($sql->num_rows()>0){
			$res=$sql->result_array();
			foreach($res as $rec){
				if($rec['day0']!='0' || $rec['day1']!='0' ||
				   $rec['day2']!='0' || $rec['day3']!='0' ||
				   $rec['day4']!='0' || $rec['day5']!='0' ||
				   $rec['day6']!='0'){
					$ret[] = $rec;
				}
			}
		}	
		return $ret;
	}
	
	/*
	 * 
	 * name: pbx_recent_calls
	 * @param
	 * @return pbx recent_calls in pie chart
	 * 
	 */
	function pbx_recent_calls(){
		$res=array();
		$date=date('Y-m-d',strtotime('-6 days'));
		$q = "";
		//$q = ($this->session->userdata('eid')!='1')? " AND g.eid='".$this->session->userdata('eid')."'":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			//$q .= " AND (h.eid='".$this->session->userdata('eid')."' or g.eid='".$this->session->userdata('eid')."')";
		}
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$this->session->userdata('bid')."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND h.pulse > '0'";
		}
		
		$sql=$this->db->query("
							  SELECT p.title AS groupname, COALESCE( pr.cnt, 0 ) AS count
								FROM `".$this->session->userdata('bid')."_pbx` p
								LEFT JOIN (
									SELECT count( callid ) AS cnt, pr.pbxid
									FROM ".$this->session->userdata('bid')."_pbxreport pr
									LEFT JOIN ".$this->session->userdata('bid')."_pbx p ON pr.pbxid = p.pbxid
									WHERE date( pr.starttime ) >= '".$date."' $q
								GROUP BY pr.pbxid
								)pr ON p.pbxid = pr.pbxid
							 ");
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}	
		return $res;
	}	
	  /******************end of pbx Dashboard *************************/
	
	
	
	/******************start of lead Dashboard *************************/
	/*
	 * 
	 * name: leads_employee wise _calls
	 * @param
	 * @return total calls report of group for last 7 days
	 * 
	 */
	function lead_empgroupwise_todaycalls(){
		$res=array();
		$q='';
		$date=date('Y-m-d',strtotime('-6 days'));
		//$q = ($this->session->userdata('eid')!='1')? " AND eid='".$this->session->userdata('eid')."'":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (e.eid='".$this->session->userdata('eid')."' or lg.eid='".$this->session->userdata('eid')."')";
		}
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$this->session->userdata('bid')."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND h.pulse > '0'";
		}
		
		$sql=$this->db->query("SELECT count( l.assignto ) AS count, e.empname AS employee, e.empnumber AS number, e.eid AS eid
								FROM `".$this->session->userdata('bid')."_leads` l
								LEFT JOIN `".$this->session->userdata('bid')."_employee` e ON l.assignto = e.eid
								LEFT JOIN `".$this->session->userdata('bid')."_leads_groups` lg ON l.gid = lg.gid
								WHERE l.status =1
								AND date( l.createdon ) >= '".$date."' $q 
								GROUP BY l.assignto
								ORDER BY e.empname");
		//echo $this->db->last_query();
		if($sql->num_rows()>0){
			$res=$sql->result_array();
			$uncount=0;
			foreach($res as $ikey=>$r){
				$esql=$this->db->query("SELECT count( l.assignto ) AS count, e.empname AS employee, e.empnumber AS number, e.eid AS eid
								FROM `".$this->session->userdata('bid')."_leads` l
								LEFT JOIN `".$this->session->userdata('bid')."_employee` e ON l.assignto = e.eid
								LEFT JOIN `".$this->session->userdata('bid')."_leads_groups` lg ON l.gid = lg.gid
								WHERE l.status =1
								AND date( l.createdon ) >= '".$date."' $q  and  e.eid = '".$r['eid']."'
								GROUP BY l.assignto 
								");
				if($esql->num_rows()>0){
					$eres=$esql->row();
					$uncount=$eres->count;
					$res[$ikey]['ucount'] = $eres->count;
					//$res[$ikey]['ucount'] = $esql->num_rows();

				}
				else{$res[$ikey]['ucount'] = 0;}			
			}
		}
		return $res;
	}
	
	/*
	 * 
	 * name: leads_followUps
	 * @param
	 * @return follow up reports of lead
	 * 
	 */
	function leads_followUps(){
		$res=array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$bid = $this->session->userdata('bid');
		$eid = $this->session->userdata('eid');
		$where3 = ($roleDetail['role']['admin']!=1)?" e.eid='".$eid."' AND" : "";
		$q="SELECT l.name as callername,l.number as callfrom,lf.followupdate as followupdate,lf.callid as callid,
			e.eid as eid,e.empname as empname,g.gid as gid,g.groupname as groupname,lf.type as source
			FROM ".$bid."_followup lf 
			LEFT JOIN ".$bid."_leads l on lf.callid = l.leadid 
			LEFT JOIN ".$bid."_leads_groups g on l.gid = g.gid 
			LEFT JOIN ".$bid."_employee e on e.eid=l.assignto
			WHERE ".$where3." lf.followupdate >= CURRENT_DATE() 
			AND lf.followupdate <= (CURRENT_DATE()+INTERVAL 6 DAY)
			AND lf.type = 'leads'";
		
		$sql=$this->db->query($q);
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}
		
		return $res;
	}
	
	
	
	/*
	 * 
	 * name: leads_assigned_detail
	 * @param
	 * @return latest assined lead for 7 days
	 * 
	 */
	function leads_assigned_detail(){
		$res=array();
		$date=date('Y-m-d',strtotime('-6 days'));
		$q = "";
		//$q = ($this->session->userdata('eid')!='1')? " AND e.eid=".$this->session->userdata('eid')."":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND e.eid=".$this->session->userdata('eid')." ";
		}
		
		$sql=$this->db->query("SELECT l.name, l.email, l.number,l.createdon,e.empname, e.empemail, e.empnumber
		                       FROM `".$this->session->userdata('bid')."_leads` l
		                       LEFT JOIN ".$this->session->userdata('bid')."_employee e ON l.assignto = e.eid
		                        WHERE l.status!=2 and date( l.createdon ) >= '".$date."' $q  order by l.leadid desc");
		if($sql->num_rows()>0){
			$res=$sql->result_array();
			
		}	
		return $res;
	}
	/*
	 * 
	 * name: lead_groupwiseleads
	 * @param
	 * @return  lead_groupwiseleads in pie chart
	 * 
	 */
	function lead_groupwiseleads(){
		$res=array();
		$date=date('Y-m-d',strtotime('-6 days'));
		$q = "";
		//$q = ($this->session->userdata('eid')!='1')? " AND g.eid='".$this->session->userdata('eid')."'":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND lg.eid='".$this->session->userdata('eid')."' ";
		}
		$sql=$this->db->query("
								SELECT count( l.gid ) AS count, l.gid, lg.groupname
								FROM `".$this->session->userdata('bid')."_leads` l
								LEFT JOIN `".$this->session->userdata('bid')."_leads_groups` lg ON l.gid = lg.gid
								WHERE date(l.createdon ) >= '".$date."' 
								AND lg.status =1
								AND l.status !=0 $q
								GROUP BY l.gid
							 ");
		
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}	
		return $res;
	}	
	/*
	 * 
	 * name: lead_types
	 * @param
	 * @return  status details of the assigned leads for last 7 days
	 * 
	 */	
	function lead_types($type){
		$date=date('Y-m-d',strtotime('-6 days'));
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		
		$q=($type=='o')?"l.status=1":"";
		$q.=($type=='p')?"l.status=2":"";
		$q.=($type=='cw')?"l.status=3":"";
		$q.=($type=='cl')?"l.status=4":"";
		if($roleDetail['role']['admin']!=1){
			$q .= " AND e.eid=".$this->session->userdata('eid')."";
		}
		$q.= " AND date(l.createdon)>='".$date."' ";
		$sql=$this->db->query("SELECT count( l.status ) AS cnt, l.status FROM `".$this->session->userdata('bid')."_leads` l
								LEFT JOIN `".$this->session->userdata('bid')."_leads_groups` lg ON l.gid = lg.gid
								LEFT JOIN `leads_status` ls ON l.status = ls.id 
								LEFT JOIN `".$this->session->userdata('bid')."_employee` e ON l.assignto = e.eid
								where ".$q);
		return ($sql->num_rows()>0) ?$sql->row()->cnt:0;
	}
	/******************End of lead Dashboard *************************/
	
	
	/******************start of supports Dashboard *************************/
	
	
	/*
	 * 
	 * name: supports_assigned_detail
	 * @param
	 * @return latest assined ticket for 7 days
	 * 
	 */
	function supports_assigned_detail()
	{
		$res=array();
		$date=date('Y-m-d',strtotime('-6 days'));
		$q = "";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND  e.eid=".$this->session->userdata('eid')."";
		}
		
		$sql=$this->db->query("SELECT s.name, s.email, s.number,s.createdon,e.empname, e.empemail, e.empnumber
		FROM `".$this->session->userdata('bid')."_support_tickets` s
		LEFT JOIN ".$this->session->userdata('bid')."_employee e ON s.assignto = e.eid
		WHERE s.status!='2' and date( s.createdon ) >= '".$date."' $q order by s.tktid desc ");
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}	
		return $res;
	}
	
	/*
	 * 
	 * name: support_followUps
	 * @param
	 * @return follow up reports of support
	 * 
	 */
	function support_followUps(){
		$res=array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$bid = $this->session->userdata('bid');
		$eid = $this->session->userdata('eid');
		$where4 = ($roleDetail['role']['admin']!=1)?" e.eid='".$eid."' AND" : "";
		$q="SELECT t.name as callername,t.number as callfrom,f.followupdate as followupdate,t.tktid as callid,e.eid as eid,e.empname as empname,g.gid as gid,g.groupname as groupname,f.type as source FROM ".$bid."_followup f
		LEFT JOIN ".$bid."_support_tickets t on f.callid = t.tktid 
		LEFT JOIN ".$bid."_support_groups g on t.gid = g.gid 
		LEFT JOIN ".$bid."_employee e on e.eid=t.assignto
		WHERE $where4 f.type='support' AND f.followupdate >= CURRENT_DATE() 
		AND f.followupdate <= (CURRENT_DATE()+INTERVAL 6 DAY)";
		$sql=$this->db->query($q);
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}	
		return $res;
	}
	
	/*
	 * 
	 * name: support_employee wise _calls
	 * @param
	 * @return total calls report of group for last 7 days
	 * 
	 */
	function support_empgroupwise_todaycalls(){
		$res=array();
		$q='';
		$date=date('Y-m-d',strtotime('-6 days'));
		//$q = ($this->session->userdata('eid')!='1')? " AND eid='".$this->session->userdata('eid')."'":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND e.eid=".$this->session->userdata('eid')."";
		}
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$this->session->userdata('bid')."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND h.pulse > '0'";
		}
		
		$sql=$this->db->query("SELECT count( st.assignto ) AS count, e.empname AS employee, e.empnumber AS number, e.eid AS eid
								FROM `".$this->session->userdata('bid')."_support_tickets` st
								LEFT JOIN `".$this->session->userdata('bid')."_employee` e ON st.assignto = e.eid
								LEFT JOIN `".$this->session->userdata('bid')."_support_groups` sg ON st.gid = sg.gid
								WHERE st.status =1
								AND date( st.createdon ) >= '".$date."' $q 
								GROUP BY st.assignto
								ORDER BY e.empname");

		if($sql->num_rows()>0){
			$res=$sql->result_array();
			$uncount=0;
			foreach($res as $ikey=>$r){
				$esql=$this->db->query("SELECT count( st.assignto ) AS count, e.empname AS employee, e.empnumber AS number, e.eid AS eid
										FROM `".$this->session->userdata('bid')."_support_tickets` st
										LEFT JOIN `".$this->session->userdata('bid')."_employee` e ON st.assignto = e.eid
										LEFT JOIN `".$this->session->userdata('bid')."_support_groups` sg ON st.gid = sg.gid
										WHERE st.status =1
										AND date( st.createdon ) >= '".$date."' $q 
										and  e.eid = '".$r['eid']."'
										GROUP BY st.assignto");
				if($esql->num_rows()>0){
					$eres=$esql->row();
					$uncount=$eres->count;
					$res[$ikey]['ucount'] = $eres->count;
					//$res[$ikey]['ucount'] = $esql->num_rows();

				}
				else{$res[$ikey]['ucount'] = 0;}			
			}
		}
		return $res;
	}
	/*
	 * 
	 * name: support_groupwiseTickets
	 * @param
	 * @return  support_groupwiseTickets in pie chart
	 * 
	 */
	function support_groupwisesupports(){
		$res=array();
		$date=date('Y-m-d',strtotime('-6 days'));
		$q = "";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND sg.eid=".$this->session->userdata('eid')."";
		}
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$this->session->userdata('bid')."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND h.pulse > '0'";
		}
		
		$sql=$this->db->query("
								SELECT count( st.gid ) AS count, st.gid, sg.groupname
								FROM `".$this->session->userdata('bid')."_support_tickets` st
								LEFT JOIN `".$this->session->userdata('bid')."_support_groups` sg ON st.gid = sg.gid
								WHERE date( st.createdon ) >= date_sub( current_date( ) , INTERVAL 6
								DAY )
								AND sg.status =1
								AND st.status !=0 $q
								GROUP BY st.gid
							 ");
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}	
		return $res;
	}
	/*
	 * 
	 * name: support_types
	 * @param
	 * @return  status details of the support ticket for last 7 days
	 * 
	 */	
	function support_types($type){
		$date=date('Y-m-d',strtotime('-6 days'));
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		
		$q=($type=='o')?"st.status=1":"";
		$q.=($type=='p')?"st.status=2":"";
		$q.=($type=='r')?"st.status=3":"";
		$q.=($type=='c')?"st.status=4":"";
		if($roleDetail['role']['admin']!=1){
			$q .= " AND e.eid=".$this->session->userdata('eid')." ";
		}
		$q.= " AND date(st.createdon)>='".$date."' ";
		$sql=$this->db->query("SELECT count( st.status ) AS cnt, ss.status FROM `".$this->session->userdata('bid')."_support_tickets` st
								LEFT JOIN `".$this->session->userdata('bid')."_support_groups` sg ON st.gid = sg.gid
								LEFT JOIN `".$this->session->userdata('bid')."_support_status` ss ON st.status = ss.sid 
								LEFT JOIN `".$this->session->userdata('bid')."_employee` e ON st.assignto = e.eid where ".$q);
		return ($sql->num_rows()>0) ?$sql->row()->cnt:0;
	}
	
	/******************End of support Dashboard *************************/
	
	/* leads Check */
	
	function leadscheck($callfrom,$gid,$callid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$DB2 = (in_array($bid,array('257','538'))) ? $this->load->database('download', TRUE) : $this->load->database('download1', TRUE);
		$sql=$DB2->query("SELECT leadid FROM `".$bid."_callhistory` WHERE callfrom='".$callfrom."' AND gid='".$gid."' AND leadid>0
								UNION 
							   SELECT leadid FROM `".$bid."_callarchive` WHERE callfrom='".$callfrom."' AND gid='".$gid."' AND leadid>0");
		$res=$sql->result_array();
		if($sql->num_rows()>0){
			$leadid = $res[0]['leadid'];
			$sql=$this->db->query("UPDATE ".$bid."_callhistory SET leadid='".$leadid."'  WHERE callid='".$callid."'");
		}else{
			$leadid = 0;
		}
		return $leadid;
	}
	function getreportemp($eid,$bid){
		$DB2 = (in_array($bid,array('257','538'))) ? $this->load->database('download', TRUE) : $this->load->database('download1', TRUE);
		$sql = $DB2->query("SELECT eid FROM ".$bid."_employee WHERE reportto='".$eid."'");
		$res = array();
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $r)	{
				$res[]=$r['eid'];
			}
			$reporteid = @implode(',',$res);
		}else{
			$reporteid = '';
		}
		return $reporteid;
	}
	function leadstatuschk($callfrom,$bid){
		$DB2 = (in_array($bid,array('257','538'))) ? $this->load->database('download', TRUE) : $this->load->database('download1', TRUE);
		$sql = $DB2->query("SELECT leadid,lead_status FROM ".$bid."_leads WHERE number='".$callfrom."'");
		$leadDetail = array();
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $r)	{
				if($r['lead_status'] != '1'){
					$leadDetail = $r;
				}else{
					$leadDetail['leadid'] = $r['leadid'];
				}
			}
		}else{
			$leadDetail = array();
		}
		return $leadDetail;
	}
	function leadidstatus($leadid,$bid){
		$DB2 = (in_array($bid,array('257','538'))) ? $this->load->database('download', TRUE) : $this->load->database('download1', TRUE);
		$sql = $DB2->query("SELECT lead_status FROM ".$bid."_leads WHERE leadid='".$leadid."'");
		$leadDetail = array();
		if($sql->num_rows()>0){
			$leadDetail = $sql->row_array();
			if($leadDetail['lead_status'] != 1)
				return "true";
		}
	}
	
	function blk_del($arr){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$arr = str_replace('on,','',$arr);
		$sql = $this->db->query("UPDATE ".$bid."_callhistory SET status=2 WHERE callid IN(".$arr.")");
		if($this->db->affected_rows() >0){
			$this->auditlog->auditlog_info('Report'," Calls Deleted By ".$this->session->userdata('username'));
			return 1;
		}
		else
			return 0;	
	}
	
	function getCallSummary($search,$ofset,$limit){
		$bid = $this->session->userdata('bid');
		$search = array(
			 'sdate' => (isset($search['sdate']) && $search['sdate']!='') ? $search['sdate'] : date('Y-m-d 00:00')
			,'edate' => (isset($search['edate']) && $search['edate']!='') ? $search['edate'] : date('Y-m-d H:i')
			,'gid' => (isset($search['gid']) && $search['gid']!='0') ? $search['gid'] : '0'
		);
		
		$con = ($search['gid']!='0') ? " WHERE g.gid='".$search['gid']."'" : "";
		$ret = array();
		$data = array();
		$sql = "SELECT SQL_CALC_FOUND_ROWS g.gid,g.groupname,g.keyword,count(a.callid) as total FROM 
				".$bid."_groups g 
				LEFT JOIN (
				SELECT callid,gid FROM ".$bid."_callhistory 
				WHERE starttime >='".$search['sdate']."' 
				AND starttime<='".$search['edate']."'
				UNION
				SELECT callid,gid FROM ".$bid."_callarchive 
				WHERE starttime >='".$search['sdate']."' 
				AND starttime<='".$search['edate']."'
				) a ON a.gid=g.gid
				$con
				GROUP BY g.gid
				ORDER BY g.gid limit $ofset,$limit";
		
		$rst = $this->db->query($sql)->result_array();
		$ret['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		foreach($rst as $rec){
			$data[$rec['gid']] = $rec;
		}
		
		$sql = "SELECT g.gid,count(a.callid) as total FROM 
				".$bid."_groups g 
				LEFT JOIN (
				SELECT callid,gid FROM ".$bid."_callhistory 
				WHERE starttime >='".$search['sdate']."' 
				AND starttime<='".$search['edate']."'
				GROUP BY callfrom,gid
				UNION
				SELECT callid,gid FROM ".$bid."_callarchive 
				WHERE starttime >='".$search['sdate']."' 
				AND starttime <='".$search['edate']."'
				GROUP BY callfrom,gid
				) a ON a.gid=g.gid
				$con
				GROUP BY g.gid
				ORDER BY g.gid limit $ofset,$limit";
		
		$rst = $this->db->query($sql)->result_array();
		foreach($rst as $rec){
			$data[$rec['gid']]['uniquecall'] = $rec['total'];
		}
		
		$sql = "SELECT g.gid,count(a.callid) as total FROM 
				".$bid."_groups g 
				LEFT JOIN (
				SELECT callid,gid FROM ".$bid."_callhistory 
				WHERE starttime >='".$search['sdate']."' 
				AND starttime <='".$search['edate']."'
				AND pulse!='0'
				UNION
				SELECT callid,gid FROM ".$bid."_callarchive 
				WHERE starttime >='".$search['sdate']."' 
				AND starttime <='".$search['edate']."'
				AND pulse!='0'
				) a ON a.gid=g.gid
				$con
				GROUP BY g.gid
				ORDER BY g.gid limit $ofset,$limit";
		
		$rst = $this->db->query($sql)->result_array();
		foreach($rst as $rec){
			$data[$rec['gid']]['answeredcall'] = $rec['total'];
		}
		
		$sql = "SELECT g.gid,count(a.callid) as total FROM 
				".$bid."_groups g 
				LEFT JOIN (
				SELECT callid,gid FROM ".$bid."_callhistory 
				WHERE starttime >='".$search['sdate']."' 
				AND starttime <='".$search['edate']."'
				AND pulse='0'
				UNION
				SELECT callid,gid FROM ".$bid."_callarchive 
				WHERE starttime >='".$search['sdate']."' 
				AND starttime <='".$search['edate']."'
				AND pulse='0'
				) a ON a.gid=g.gid
				$con
				GROUP BY g.gid
				ORDER BY g.gid limit $ofset,$limit";
		
		$rst = $this->db->query($sql)->result_array();
		foreach($rst as $rec){
			$data[$rec['gid']]['missedcall'] = $rec['total'];
		}
		
		$ret['data'] = $data;
		//echo "<pre>";print_r($ret);echo "</pre>";
		return $ret;
	}
	function list_outbound($bid,$ofset,$limit){
		$q= '1';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
        //$custiom_ids=$this->configmodel->customSearch((isset($s['custom']))?$s['custom']:'','47',$bid);
		$q.=(isset($s['eid']) && $s['eid']!='0')?" AND a.eid = '".$s['eid']."'":"";
		$q.=(isset($s['empnumber']) && $s['empnumber']!='')?" AND a.empnumber LIKE '%".$s['empnumber']."%'":"";
		$q.=(isset($s['callto']) && $s['callto']!='')?" AND a.callto LIKE '%".$s['callto']."%'":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND a.eid='".$this->session->userdata('eid')."'";
		}
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$sql = "SELECT SQL_CALC_FOUND_ROWS a.callid FROM ".$bid."_outbound a
				WHERE $q ORDER BY calltime DESC 
		        LIMIT $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='47'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('47',$bid);
		$keys = array();
		$header = array('#');
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && !in_array($field['fieldname'],array('recordfile'))){
				foreach($roleDetail['system'] as $f){
					if($f['fieldid']==$field['fieldid'])$checked = true;
				}
				if($checked){
					array_push($keys,$field['fieldname']);
					array_push($header,(($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
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
			$r = $this->configmodel->getDetail('47',$rec['callid'],'',$bid);
			foreach($keys as $k){
				$v = isset($r[$k])?$r[$k]:"";	
				array_push($data,$v);
			}
			if($opt_add || $opt_view || $opt_delete){
				$act = ($r['recordfile']!='' && file_exists('sounds/'.$r['recordfile']))
				?'<a target="_blank" href="'.site_url('sounds/'.$r['recordfile']).'"><span title="Sound" class="fa fa-volume-up"></span></a>'
				:'<span class="glyphicon glyphicon-volume-off"></span> ';
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function obDownload($bid){
		$res=array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$q= " WHERE 1 ";
		if($_POST['endtimes']!=""){
			$q.=" AND date(a.calltime)<='".$_POST['endtimes']."'" ;
		}
		if($_POST['starttimes']!=""){
			$q.=" and date(a.calltime)>='".$_POST['starttimes']."'";
		}
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (a.eid='".$this->session->userdata('eid')."' or d.eid='".$this->session->userdata('eid')."')";
		}
		$limit = $roleDetail['role']['recordlimit'];
		$csv_output = "";
		$ke=array();
		foreach($_POST['lisiting'] as $key=>$val){
			if($key=='custom'){
				foreach($val as $key=>$val){
					$header[]=$val;
					$hkey[]='custom['.$key.']';
				}
			}else{
				$hkey[]=$key;
				$header[]=$val;
				if($key=='pulse'){
					$hkey[]='duration';
					$header[]='Duration';
				}
			}
		}
		$csv_output .= implode(",",$header)."\n";
		$sql="SELECT SQL_CALC_FOUND_ROWS callid  FROM ".$bid."_outbound a $q 
				ORDER BY a.calltime DESC LIMIT 0,$limit";
		$rst = $this->db->query($sql)->result_array();
		$total_record_count = $this->db->query($sql)->num_rows();
		$name = $bid.'_'.
				$this->session->userdata('eid').'_'.
				time();
		mkdir('reports/'.$name);
		chmod('reports/'.$name,0777);
		$files = array();
		$data_file = 'reports/'.$name.'/outboundCalls.csv';
		$fp = fopen($data_file,'w');
		fwrite($fp,$csv_output);
		foreach($rst as $rec){
			$data = array();
			$r = $this->configmodel->getDetail(47,$rec['callid'],'',$bid);
			$i=0;
			foreach($hkey as $k){
				$v=(isset($r[$k])) ? '"'.str_replace("\n"," ",$r[$k]).'"' : '';
				array_push($data,$v);
				if(isset($r[$k]) && $k=="recordfile" && $r[$k]!=''){
					$path="sounds/".$r[$k];
					if (file_exists($path))	{
						copy($path,'reports/'.$name.'/'.$r[$k]);
					}
				}
			}
			$csv_output =implode(",",$data)."\n";
			fwrite($fp,$csv_output);
		}
		fclose($fp);
		chdir('reports');
		exec('zip -r '.$name.'.zip '.$name);
		exec('rm -rf '.$name);
		return $name;
	}
	function callsumDownload($bid){
		$res=array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$s = array(
			 'sdate' => (isset($_POST['starttimes']) && $_POST['starttimes']!='') ? $_POST['starttimes'] : date('Y-m-d 00:00')
			,'edate' => (isset($_POST['endtimes']) && $_POST['endtimes']!='') ? $_POST['endtimes'] : date('Y-m-d H:i')
		);
		$gids = '';
		if(!empty($_POST['groupname'])){
			if($_POST['groupname'][0]!=""){
				$gids = @implode(",",$_POST['groupname']);
			}
		}else{
			$gids = @implode(",",array_keys($this->systemmodel->CSGroups()));
		}
		$limit = $roleDetail['role']['recordlimit'];
		$csv_output = "";
		$ke=array();
		foreach($_POST['lisiting'] as $key=>$val){
			$hkey[]=$key;
			$header[]=$val;
		}
		$csv_output .=implode(",",$header)."\n";
		$ret = array();
		$data = array();
		$con = ($gids !='') ? " WHERE g.gid IN (".$gids.")" : "";
		$sql = "SELECT SQL_CALC_FOUND_ROWS g.gid,g.groupname,g.keyword,count(a.callid) as total FROM 
				".$bid."_groups g 
				LEFT JOIN (
				SELECT callid,gid FROM ".$bid."_callhistory 
				WHERE starttime >='".$s['sdate']."' 
				AND starttime<='".$s['edate']."'
				UNION
				SELECT callid,gid FROM ".$bid."_callarchive 
				WHERE starttime >='".$s['sdate']."' 
				AND starttime<='".$s['edate']."'
				) a ON a.gid=g.gid
				$con
				GROUP BY g.gid
				ORDER BY g.gid limit 0,$limit";
		$rst = $this->db->query($sql)->result_array();
		$ret['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		foreach($rst as $rec){
			$data[$rec['gid']] = $rec;
		}
		$sql = "SELECT g.gid,count(a.callid) as total FROM 
				".$bid."_groups g 
				LEFT JOIN (
				SELECT callid,gid FROM ".$bid."_callhistory 
				WHERE starttime >='".$s['sdate']."' 
				AND starttime<='".$s['edate']."'
				GROUP BY callfrom,gid
				UNION
				SELECT callid,gid FROM ".$bid."_callarchive 
				WHERE starttime >='".$s['sdate']."' 
				AND starttime <='".$s['edate']."'
				GROUP BY callfrom,gid
				) a ON a.gid=g.gid
				$con
				GROUP BY g.gid
				ORDER BY g.gid limit 0,$limit";
		
		$rst = $this->db->query($sql)->result_array();
		foreach($rst as $rec){
			$data[$rec['gid']]['uniquecall'] = $rec['total'];
		}
		$sql = "SELECT g.gid,count(a.callid) as total FROM 
				".$bid."_groups g 
				LEFT JOIN (
				SELECT callid,gid FROM ".$bid."_callhistory 
				WHERE starttime >='".$s['sdate']."' 
				AND starttime <='".$s['edate']."'
				AND pulse!='0'
				UNION
				SELECT callid,gid FROM ".$bid."_callarchive 
				WHERE starttime >='".$s['sdate']."' 
				AND starttime <='".$s['edate']."'
				AND pulse!='0'
				) a ON a.gid=g.gid
				$con
				GROUP BY g.gid
				ORDER BY g.gid limit 0,$limit";
		
		$rst = $this->db->query($sql)->result_array();
		foreach($rst as $rec){
			$data[$rec['gid']]['answeredcall'] = $rec['total'];
		}
		$sql = "SELECT g.gid,count(a.callid) as total FROM 
				".$bid."_groups g 
				LEFT JOIN (
				SELECT callid,gid FROM ".$bid."_callhistory 
				WHERE starttime >='".$s['sdate']."' 
				AND starttime <='".$s['edate']."'
				AND pulse='0'
				UNION
				SELECT callid,gid FROM ".$bid."_callarchive 
				WHERE starttime >='".$s['sdate']."' 
				AND starttime <='".$s['edate']."'
				AND pulse='0'
				) a ON a.gid=g.gid
				$con
				GROUP BY g.gid
				ORDER BY g.gid limit 0,$limit";
		
		$rst = $this->db->query($sql)->result_array();
		foreach($rst as $rec){
			$data[$rec['gid']]['missedcall'] = $rec['total'];
		}
		$name = $bid.'_'.
				$this->session->userdata('eid').'_'.
				time();
		mkdir('reports/'.$name);
		chmod('reports/'.$name,0777);
		$files = array();
		$data_file = 'reports/'.$name.'/calls.csv';
		$fp = fopen($data_file,'w');
		fwrite($fp,$csv_output);
		$rst = $data;
		foreach($rst as $rec){
			$dat = array();
			$i=0;
			foreach($hkey as $k){
				$v=(isset($rec[$k])) ? '"'.str_replace("\n"," ",$rec[$k]).'"' : '';
				array_push($dat,$v);
			}
			$csv_output =implode(",",$dat)."\n";
			fwrite($fp,$csv_output);
		}
		fclose($fp);
		chdir('reports');
		exec('zip -r '.$name.'.zip '.$name);
		exec('rm -rf '.$name);
		return $name;
	}
	
	function outbound_calls_csv($bid){
		$res=array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$q= " WHERE 1 ";
		if($_POST['endtimes']!=""){
			$q.=" AND date(a.endtime)<='".$_POST['endtimes']."'" ;
		}
		if($_POST['starttimes']!=""){
			$q.=" AND date(a.starttime)>='".$_POST['starttimes']."'";
		}
		if($_POST['exenumber'] !=""){
			$q.=" AND a.executive LIKE '%".$_POST['exenumber']."%'";
		}
		if($_POST['cusnumber'] !=""){
			$q.=" AND a.customer LIKE '%".$_POST['cusnumber']."%'";
		}
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (a.eid='".$this->session->userdata('eid')."')";
		}
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = $roleDetail['role']['recordlimit'];
		$csv_output = "";
		foreach($_POST['lisiting'] as $key=>$fiels){
			if($key=='custom'){
				foreach($_POST['lisiting']['custom'] as $key=>$fiels){
					$hkey[]=$fiels;
					$header[]=$fiels;
				}
			}else{
				$hkey[]=$key;
				$header[]=$fiels;
			}
		}
		$csv_output .=implode(",",$header)."\n";
         $sql="SELECT a.*,modid as source,pulse as credit
				FROM ".$bid."_outboundcalls a
				LEFT JOIN ".$bid."_employee c on a.eid=c.eid 
				$q
				ORDER BY a.starttime DESC LIMIT 0,$limit";	 
			
		$rst = $this->db->query($sql)->result_array();
				//echo "<pre>";print_r($rst);exit;
		$name = $bid.'_'.
				$this->session->userdata('eid').'_'.
				time();
		mkdir('reports/'.$name);
		chmod('reports/'.$name,0777);
		$data_file = 'reports/'.$name.'/clicktoconnect.csv';
		$fp = fopen($data_file,'w');
		$status=array(
			//""=>"Select",
			"0"=>"Failed",
			"1"=>"Originate",
			"2"=>"Executive Busy",
			"3"=>"Customer Busy",
			"4"=>"Call Complete",
			"5"=>"Insufficient Balance"
		);
		$source_arr = array(
			 ''	 => ''
			,'0' => 'API'
			,'1' => 'Track Report'
			,'2' => 'IVRS Report'
			,'3' => 'PBX Report'
			,'4' => 'Lead'
			,'6' => 'Support'
			,'5' => 'Contact'
		);
		foreach($rst as $rec){
			$s1 = $status[$rec['status']];
		    $s = $source_arr[$rec['source']];
			$data = array();
			foreach($hkey as $k){
				if($k == 'source'){
					$v = $s;
				}elseif($k == 'status'){
					$v = $s1;
				}else{
					$v = isset($rec[$k])? '"'.$rec[$k].'"':"";
		        }
				array_push($data,$v);
				if(isset($rec[$k]) && $k=="filename" && $rec[$k]!=''){
					$path="sounds/".$rec[$k];
					if (file_exists($path))	{
						 copy($path,'reports/'.$name.'/'.$rec[$k]);
					}
				}
			}
			$csv_output .=implode(",",$data)."\n";
		}
		fwrite($fp,$csv_output);
		fclose($fp);
		$files[] = $data_file;
		//$result = $this->create_zip($files,'reports/'.$name.'.zip');
		chdir('reports')."<br>";
		exec('zip -r '.$name.'.zip '.$name);
		exec('rm -rf '.$name);
		return $name;
	}
	function getClientid($bid){
		$sql = $this->db->query("SELECT apisecret AS clientid FROM business WHERE bid='".$bid."'")->row()->clientid;
		return $sql;
	}
}



/* end of report model */
