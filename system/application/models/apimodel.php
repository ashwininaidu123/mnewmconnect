<?php
class Apimodel extends Model{
	 
	 function Apimodel(){
        parent::Model();
     }
	 function authenticate($username,$password){
		 $sql=$this->db->query("SELECT * FROM user WHERE username='$username' AND password='".md5($password)."' AND status='1'");
		 if($sql->num_rows()>0){
			 $arr = $sql->row();
			 unset($arr->password);
			 return $arr;
		 }
		 else{
			 return array();
		 }
	 }	
	 function get_role_info($bid){
		$sql=$this->db->query("SELECT * FROM  ".$bid."_employee WHERE bid=$bid and empemail='".urldecode($_REQUEST['username'])."'");
		 if($sql->num_rows()>0){
			 $res=$sql->row();
			 return $res->roleid; 
		 }else{
			 return 0;
		 }
	 }
	 function getEmpinfo($eid,$bid){
		 $sql=$this->db->query("SELECT * FROM ".$bid."_employee WHERE bid=$bid and eid=$eid");
		 return $sql->row();
	 }
	 function empnumber_exits($enumber,$empemail='',$bid){
		 $res=array();$q='';
		 if($empemail!=''){
			 $q= " and empemail='".$empemail."'";
		 }
		 $sql=$this->db->query("SELECT * FROM ".$bid."_employee where empnumber='".$enumber."' $q ");
		 if($sql->num_rows()>0){
			 return $sql->row();
		 }
		 return $res;
	 }
	 function emp_add($enumber,$empname,$empemail='',$bid){
			$eid=$this->db->query("SELECT COALESCE(MAX(`eid`),0)+1 as id FROM ".$bid."_employee")->row()->id;
			$this->db->set('eid',$eid);
			$this->db->set('bid',$bid);
			$this->db->set('roleid','2');
			$this->db->set('empname',$enumber);
			$this->db->set('empnumber',$enumber);
			$this->db->set('empemail',($empemail!='')?$empemail:'');
			$this->db->set('login','0');
			$this->db->insert($bid."_employee");
			return $eid;
		 
	 }
	 function getRoledetail($roleid='',$bid){
		$detail['role'] = (array)$this->db
					->query("SELECT * FROM ".$bid."_user_role
							WHERE roleid='".$roleid."'
							AND bid='".$bid."'")
					->row();
		$modules = $this->db
					->query("SELECT m.modid,m.modname,m.moddesc,COALESCE(o.opt_add,0) as opt_add,
							COALESCE(o.opt_view,0) as opt_view,COALESCE(opt_delete,0) as opt_delete FROM module m
							LEFT JOIN (SELECT * FROM ".$bid."_role_mod_opt
							WHERE roleid='".$roleid."'
							AND bid='".$bid."') as  o
							ON m.modid=o.modid")
					->result_array();
		foreach ($modules as $mod)$detail['modules'][$mod['modid']] = $mod;
		$detail['system'] = $this->db
					->query("SELECT a.*,f.fieldname FROM ".$bid."_role_access a
							LEFT JOIN systemfields f on a.fieldid=f.fieldid
							WHERE a.roleid='".$roleid."'
							AND a.bid='".$bid."'
							AND a.fieldtype='s'")
					->result_array();
		$detail['custom'] = $this->db
					->query("SELECT * FROM ".$bid."_role_access
							WHERE roleid='".$roleid."'
							AND bid='".$bid."'
							AND fieldtype='c'")
					->result_array();
		return $detail;
	}	
	function update_Employee($bid,$eid){
		$this->db->set('empname',urldecode($_REQUEST['empname']));
		$this->db->set('empnumber',urldecode($_REQUEST['empnumber']));
		$this->db->where('eid',$eid);
		$this->db->update($bid."_employee");
		return true;	
	}
	function Add_Employee($bid,$role){
		//echo $this->email_employee();exit;
		if(!$this->email_employee()){
			$eid=$this->db->query("SELECT COALESCE(MAX(`eid`),0)+1 as id FROM ".$bid."_employee")->row()->id;
			$this->db->set('eid',$eid);
			$this->db->set('bid',$bid);
			$this->db->set('roleid',$role);
			$this->db->set('empname',urldecode($_REQUEST['empname']));
			$this->db->set('empnumber',urldecode($_REQUEST['empnumber']));
			$this->db->set('empemail',urldecode($_REQUEST['empemail']));
			$this->db->set('login',urldecode($_REQUEST['login']));
			$this->db->insert($bid."_employee");
			if(urldecode($_REQUEST['login'])=="1"){
				$uid=$this->db->query("SELECT COALESCE(MAX(`uid`),0)+1 as id FROM `user`")->row()->id;
				$this->db->set('uid', $uid); 
				$this->db->set('bid',$bid);
				$this->db->set('eid', $eid); 
				$this->db->set('username',urldecode($_REQUEST['empemail']));
				$this->db->set('password',md5(urldecode($_REQUEST['loginpassword']))); 
				$this->db->insert('user'); 
			}
			return $eid;
		}else{
			return "0";
		}	
	}
	function getFreePri(){
		$sql = "SELECT * FROM dummynumber WHERE status='0' AND bid='0' LIMIT 0,1";
		$rst = $this->db->query($sql);
		$rec = $rst->result_array();
		return $rec[0]['number'];
	}

	function Emplist($eid,$bid){
			$sql=$this->db->query("select SQL_CALC_FOUND_ROWS a.eid,a.bid,a.`empname`,
						  a.`empid`,a.`empnumber`,a.`empemail`,
						  a.`login`,a.`status`,b.rolename  as role 
						  from ".$bid."_employee a LEFT JOIN
						  ".$bid."_user_role b on a.roleid=b.roleid
						  where a.status!=2");
			 return $sql->result_array();	
	}
	function landingnumber_exists($bid,$key,$number){
		$res=array();
		$sql=$this->db->query("SELECT * FROM prinumbers where  landingnumber='".$number."' and associateid=0 and bid='".$bid."' and landing_key='".$key."'");
		if($sql->num_rows()>0){
			$res=$sql->row();
			return $res;
		}
		return $res;
	}
	function verifyNumber($bid,$key,$number){
		$keys=(array)$this->landingnumber_exists1($bid,$key);
		$keyarr=array();
		if(!empty($keys)){
			$lDetail=(array)$this->landingnumber_exists($bid,$key,$number);
			if(!empty($lDetail)){
				if($lDetail['landingnumber']==$keys['landingnumber']){
					return array("100",$lDetail);
					
				}else{
					return array("103",$keyarr);
				}
				
			}else{
				return array("102",$keyarr);
			}
		}else{
			return array("101",$keyarr);
		}
	}
	function verifyNumber1($bid,$key,$number){
		$keys=(array)$this->landingnumber_exists1($bid,$key);
		$keyarr=array();
		if(!empty($keys)){
			$lDetail=(array)$this->Clandingnumber($bid,$number,$key);
			if(!empty($lDetail)){
				if($lDetail['landingnumber']==$keys['landingnumber']){
					return array("100",$lDetail);
					
				}else{
					return array("103",$keyarr);
				}
				
			}else{
				return array("102",$keyarr);
			}
		}else{
			return array("101",$keyarr);
		}
	}
	function landingnumber_exists1($bid,$key){
		$res=array();
		$sql=$this->db->query("SELECT * FROM prinumbers where  landing_key='".$key."'");
		if($sql->num_rows()>0){
			$res=$sql->row();
		}
		return $res;
	}
	function deletegroupEmployee($gid,$eid,$bid){
		$sql=$this->db->query("DELETE FROM ".$bid."_group_emp WHERE gid=$gid AND eid=$eid");
		return true;
	}
	function get_employee_id($bid,$email){
		 $employee_email=($email!="")?$email:$_REQUEST['empemail'];
		 $sql=$this->db->query("SELECT * FROM ".$bid."_employee WHERE empemail='".urldecode($employee_email)."'");
		 if($sql->num_rows()>0){
		 $res=$sql->row();
		 return $res->eid;}
		 else{
			 return "";
		 }
	}
	function Grouplist($bid){
		$sql=$this->db->query("SELECT * FROM ".$bid."_groups WHERE bid=$bid");
		return $sql->result_array();
	}
	function update_pri_free($number,$gid,$bid,$type=1){
		$sql=$this->db->query("UPDATE dummynumber SET associateid=$gid,bid=$bid,status=1,type=$type where number=$number");
		return true;
		
	}
	function Addgroup($bid,$eid,$number){
			//echo $this->get_rule_id();exit;
			//$pri=$this->get_PRI($bid,$eid);
			
			$gid=$this->db->query("SELECT COALESCE(MAX(`gid`),0)+1 as id FROM ".$bid."_groups")->row()->id;
			$this->db->set('gid',$gid);
			$this->db->set('eid',$eid);
			$this->db->set('bid',$bid);
			$this->db->set('groupname',urldecode($_REQUEST['groupname']));
			$this->db->set('prinumber',$number);
			$this->db->set('rules',$this->get_rule_id());
			$this->db->set('primary_rule',(($_REQUEST['region']!="")&& urldecode($_REQUEST['region'])!="All")?$this->get_primaryrule($bid):0);
			$this->db->set('record',(urldecode($_REQUEST['region'])!="0")?1:0);
			$this->db->insert($bid."_groups");	
			if($_REQUEST['landingnumber']!="0"){
			$this->update_pri($pri,$gid,$bid,$type=0);
			return $gid;
			}else{
				$this->update_pri_free($number,$gid,$bid,$type=1);
				return $gid;
			}
		
	}
	function update_pri($pri,$eid,$bid,$type=0){
		$sql=$this->db->query("UPDATE prinumbers SET status=1 ,associateid=$eid,type=$type where bid=$bid and number=$pri and landingnumber='".urldecode($_REQUEST['landingnumber'])."'");
		return true;
		
	}
	function get_primaryrule($bid){
		$sql=$this->db->query("SELECT * FROM ".$bid."_custom_region WHERE regionname='".urldecode($_REQUEST['region'])."'");
		$res=$sql->row();
		return $res->regionid;
		}
	
	function get_rule_id(){
		if($_REQUEST['rules']!=""){
		$sql=$this->db->query("SELECT * FROM group_rules WHERE rulename='".urldecode($_REQUEST['rules'])."'");
		$res=$sql->row();
		return $res->rulesid;}
		else{
		  return 0;	
		}
	}
	function get_PRI($bid,$eid=''){
		$sql=$this->db->query("SELECT * FROM prinumbers where bid=".$bid." and landingnumber='".urldecode($_REQUEST['landingnumber'])."' and associateid=0");
		$res=$sql->row();
		return $res->number;
	}
	function get_group_id($bid){
		$sql=$this->db->query("SELECT * FROM ".$bid."_groups where 	groupname='".urldecode($_REQUEST['groupname'])."'");
		if($sql->num_rows()>0){
			$res=$sql->row();
			return $res->gid;
		}else{
			return "";
		}
		
		
	}
	function check_groupEmployee($eid,$bid,$gid){
		$sql=$this->db->query("SELECT * FROM ".$bid."_group_emp where gid=$gid and eid=$eid");
		if($sql->num_rows()==0){
			return "1";
		}else{
			return "0";
		}
	}
	function AddtoGroup($eid,$bid,$gid){
			$this->db->set('gid',$gid);
			$this->db->set('eid',$eid);
			$this->db->set('bid',$bid);
			$this->db->insert($bid."_group_emp");	
			return "1";
	}
	function getGroupEmployees($gid,$bid){
		$sql=$this->db->query("SELECT * FROM ".$bid."_group_emp where gid=$gid");
		if($sql->num_rows()>0){
			return $sql->result_array();
		}else{
			return array();
		}
		
	}
	function addIvrs($bid,$number){
		
			$ivrsid=$this->db->query("SELECT COALESCE(MAX(`ivrsid`),0)+1 as id FROM ".$bid."_ivrs")->row()->id;
			$this->db->set('ivrsid',$ivrsid);
			$this->db->set('bid',$bid);
			$this->db->set('prinumber',$number);
			$this->db->set('title',urldecode($_REQUEST['ivrstitle']));
			$this->db->set('timeout','10');
			$this->db->set('status','1');
			$this->db->insert($bid.'_ivrs');
			
			$ivrparent=$this->db->query("SELECT COALESCE(MAX(`optid`),0)+1 as id FROM ".$bid."_ivrs_options")->row()->id;
			$this->db->set('optid',$ivrparent);
			$this->db->set('bid',$bid);
			$this->db->set('ivrsid',$ivrsid);
			$this->db->set('parentopt','0');
			$this->db->set('optorder','0');
			$this->db->set('optsound',urldecode($_REQUEST['audiofile']));
			$this->db->set('targettype','list');
			$this->db->insert($bid.'_ivrs_options');
		
			if(urldecode($_REQUEST['keyoption'])!=""){	
				$ivrparent=$this->db->query("SELECT COALESCE(MAX(`optid`),0)+1 as id FROM ivrs_options")->row()->id;
				$this->db->set('optid',$ivrparent);
				$this->db->set('bid',$bid);
				$this->db->set('ivrsid',$ivrsid);
				$this->db->set('parentopt',$ivrsid);
				$this->db->set('opttext',urldecode($_REQUEST['textspeech']));
				$this->db->set('optorder',urldecode($_REQUEST['keyoption']));
				$this->db->set('optsound',urldecode($_REQUEST['audiofile1']));
				if(urldecode($_REQUEST['target'])!="list"){
				$this->db->set('targettype',urldecode($_REQUEST['target']));
				$this->db->set('targeteid',urldecode($_REQUEST['targetoption']));
				}else{
					$this->db->set('targettype',urldecode($_REQUEST['target']));
				}
				$this->db->insert($bid."_ivrs_options");
			}
			if($_REQUEST['landingnumber']!="0"){
				$this->update_pri($number,$ivrsid,$bid,$type=0);
			
			}else{
				$this->update_pri_free($number,$ivrsid,$bid,$type=1);
			}
			
			return $ivrsid;
			
			
			
		
	}
	function getReportlist($bid,$type){
		
		$q=($type=='a')?"":"";
		$q.=($type=='m')?" AND a.endtime=a.starttime AND (a.callername='' AND a.callerbusiness='' AND a.calleraddress='' AND a.caller_email='' AND a.remark='')":"";
		$q.=($type=='q')?" AND (a.callername!='' OR a.callerbusiness!='' OR a.calleraddress!='' OR a.caller_email!='' OR a.remark!='')":"";
		$q.=($type=='u')?" AND (a.callername='' AND a.callerbusiness='' AND a.calleraddress='' AND a.caller_email='' AND a.remark='')":"";
		
		$roleinfo = $this->get_role_info($bid);
		$roleDetail=$this->getRoledetail($roleinfo,$bid);
		
		$sql="SELECT SQL_CALC_FOUND_ROWS x.*,a.cnt FROM
				(SELECT MAX(a.callid) as callid,count(a.callid) as cnt 
				FROM ".$bid."_callhistory a 
				where a.status!=2 $q
				GROUP BY a.callfrom,a.gid,a.eid) as a
				LEFT JOIN (
				SELECT a.callid,a.hid,a.starttime
				FROM ".$bid."_callhistory a
				left join ".$bid."_employee c on a.eid=c.eid
				left join ".$bid."_groups d on a.gid=d.gid
				) as x on a.callid=x.callid
				ORDER BY x.starttime DESC";							 
						// echo $sql;
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
		$fieldset = $this->getFields('6',$bid);
		//echo "<pre>";print_r($fieldset);exit;
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
		
		array_push($header,"Counter");
		if($opt_add || $opt_view || $opt_delete)
			array_push($header,$this->lang->line('level_Action'));
		$ret['header'] = $header;
		$list = array();
		$i = 1;
		foreach($rst as $rec){
			$data = array($i);
			$r = $this->getDetail('6',$rec['callid'],$bid);
			//print_r($r);
			foreach($keys as $k){
				if($k=="callfrom" && $rec['hid']!=0){
					$v=$r[$k];
				}
				elseif($k=='eid'){
					$v=$r[$k];
				}elseif($k=='gid'){
					$v=$r[$k];
				}else{
						$v = isset($r[$k])?$r[$k]:"";	
				}
				array_push($data,$v);
			}
			array_push($data,$rec['callid']);
			
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	
	function email_employee(){
		$usertable=$this->db->query("select * from user where username='".urldecode($_REQUEST['empemail'])."'");
		if($usertable->num_rows()==0){
			$sql=$this->db->query("select * from business where status=1");
			if($sql->num_rows()>0){
				foreach($sql->result_array() as $rows){
					$sqls=$this->db->query("select * from ".$rows['bid']."_employee where empemail='".urldecode($_REQUEST['empemail'])."'");
					if($sqls->num_rows()!=0){
						return 1;
					}
				}
			}
		}else{
			return 1;
		}
		return 0;
	}
	
	function getEmpDetail($bid,$eid){
		$sql=$this->db->query("SELECT * FROM  ".$bid."_employee WHERE bid='".$bid."' and eid='".$eid."'");
		 if($sql->num_rows()>0){
			 return $sql->row();
		 }else{
			 return array();
		 }
	 }
	function getEmpDetail_bynumber($bid,$enum,$actid,$agid){
		
		$sql=$this->db->query("SELECT * FROM  ".$bid."_employee WHERE bid='".$bid."' and empnumber='".$enum."'");
		 if($sql->num_rows()>0){
				$row=$sql->row();
				$s=$this->db->query("SELECT * FROM ".$bid."_activitymembers WHERE agid='".$agid."'  and actid='".$actid."' and eid='".$row->eid."'");
				if($s->num_rows()>0){
					return $s->row();
				}else{
					return array();
				}
		 }else{
			 return array();
		 }
	}
	
	function getExecDetail_bynumber($bid,$enum){
		
		$sql=$this->db->query("SELECT * FROM  ".$bid."_employee WHERE bid='".$bid."' and empnumber='".$enum."'");
		if($sql->num_rows()>0){
				return $sql->row();
		}else{
			 return array();
		 }
	}
	
	
	function getCallReport($user,$roleid,$time){
		$bid = $user->bid;
		$eid = $user->eid;
		$q = ($roleid!='1') ? " AND (h.eid='".$eid."' or g.eid='".$eid."') ":"";
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		$q .= ($type == '0') ? " AND h.pulse > '0'" : "";
		$roleDetail=$this->apimodel->getRoledetail($roleid,$bid);
		$sql="SELECT SQL_CALC_FOUND_ROWS h.callid FROM ".$bid."_callhistory h
				LEFT JOIN ".$bid."_groups g on g.gid=h.gid
				where h.status!=2 AND h.starttime>='".$time['start']."' and h.starttime<='".$time['end']."' ".$q."
				ORDER BY h.starttime DESC";				
			
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");

		$fieldset = $this->configmodel->getFields('6',$bid);
		$keys = array();
		$header = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,$field['fieldname']);
					$header[$field['fieldname']] = (($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_6')->$field['fieldname']);
				}
			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,'custom['.$field['fieldid'].']');
					$header['custom['.$field['fieldid'].']'] = $field['customlabel'];
				}
			}
		}
		$list = array();
		foreach($rst as $rec){
			$data = array('callid'=>$rec['callid']);
			$r = $this->configmodel->getDetail('6',$rec['callid'],'',$bid);
			foreach($keys as $k){
				$data[$header[$k]] = isset($r[$k])?$r[$k]:"";
			}
			$data['empemail']=$r['empemail'];
			array_push($list,$data);
		}
		return $list;
	}
	function getCallReportXML($user,$roleid,$time){
		$bid = $user->bid;
		$eid = $user->eid;
		$q = ($roleid!='1') ? " AND (h.eid='".$eid."' or g.eid='".$eid."') ":"";
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		$q .= ($type == '0') ? " AND h.pulse > '0'" : "";
		$roleDetail=$this->apimodel->getRoledetail($roleid,$bid);
		$sql="SELECT SQL_CALC_FOUND_ROWS h.callid FROM ".$bid."_callhistory h
				LEFT JOIN ".$bid."_groups g on g.gid=h.gid
				where h.status!=2 AND h.starttime>='".$time['start']."' and h.starttime<='".$time['end']."' ".$q."
				ORDER BY h.starttime DESC";		
			
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");

		$fieldset = $this->configmodel->getFields('6',$bid);
		$keys = array();
		$header = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,$field['fieldname']);
					$header[$field['fieldname']] = (($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_6')->$field['fieldname']);
				}
			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,$field['fieldname']);
					$header[$field['fieldname']] = $field['customlabel'];
				}
			}
		}
		$list = array();
		foreach($rst as $rec){
			$data = "\t<call>\n";
			$data .= "\t\t<callid>".$rec['callid']."</callid>\n";
			$r = $this->configmodel->getDetail('6',$rec['callid'],'',$bid);
			foreach($keys as $k){
				$tag = $k;
				if($k=='eid') 		$tag = 'calltoname';
				if($k=='callername')$tag = 'client_name';
				if($k=='starttime') $tag = 'startdatetime';
				if($k=='endtime') 	$tag = 'enddatetime';
				if($k=='callfrom') 	$tag = 'callfrom';
				if($k=='gid') 		$tag = 'group';
				$tag = str_replace(" ","_",$tag);
				$val = (isset($r[$k])) ? str_replace( array("<", ">", "\"", "'", "&"),
                                         array("&lt;", "&gt;", "&quot;", "&apos;", "&amp;"),$r[$k]) : "" ;

                $data .= "\t\t<".$tag.">".$val."</".$tag.">\n";
                                
			}
			$data .= "\t\t<callto>".$r['empnumber']."</callto>\n";
			$data .= "\t\t<empemail>".$r['empemail']."</empemail>\n";
			$data .= "\t</call>\n";
			array_push($list,$data);
		}
		return $list;
	}
	
	function getEmpbyPhone($bid,$phone){
		$sql=$this->db->query("SELECT * FROM  ".$bid."_employee WHERE bid='".$bid."' and empnumber='".$phone."'");
		 if($sql->num_rows()>0){
			 return $sql->row_array();
		 }else{
			 return array();
		 }
	}
	
	function addc2c($user,$emp,$request){
		$callid = time();
		$sql = "INSERT INTO ".$user->bid."_c2c SET
				callid		= '".$callid."',
				bid			= '".$user->bid."',
				eid			= '".(isset($emp['eid']) ? $emp['eid'] : '')."',
				callto		= '".$request['callto']."',
				callernumber= '".$request['callfrom']."',
				callername	= '".(isset($request['callername']) ? $request['callername'] : '')."',
				calleremail	= '".(isset($request['calleremail']) ? $request['calleremail'] : '')."',
				requesttime	= '".$request['time']."'";
		$this->db->query($sql);
		return array('callid'=>$callid);
	}
	function check_landingnumber($lno){
		$res=$this->db->query("SELECT * FROM prinumbers where landingnumber='".$lno."'");
		if($res->num_rows()>0){
			$r=$res->row();
			return $r->associateid;
		}else{
			return '';
		}
	}
	function get_reports_missedcalls($r,$bid,$eid){
		$a=array();
		$res=$this->db->query("select mid from ".$bid."_missedgroup where eid=".$eid)->row()->mid;
		$s=$this->db->query("SELECT * FROM ".$bid."_missedcallreport WHERE eid=".$eid." and mid=".$res);
		if($s->num_rows()>0){
			return $s->result_array();
		}else{
			return $a;
			
		}
	}
	function get_polls($bid){
		$tot=array();
		$r=$this->db->query("SELECT * FROM ".$bid."_poll WHERE status=1");
		if($r->num_rows()>0){
			foreach($r->result_array() as $rows){
				$res=$this->db->query("SELECT opt.poll_id, opt.optionval, COALESCE(count(p.polloption ),0) AS cnt
FROM ".$bid."_polloptions opt left join ".$bid."_pollreport p on p.polloption=opt.option_id where opt.poll_id=".$rows['poll_id']." group by p.polloption order by opt.optionkey");
				if($res->num_rows()>0){
					 $tot[]=$res->result_array();
						
				}
				
			}
		}
		return $tot;
	}
	
	function clickcallAdd($data){
		$callid = $this->db->query("SELECT COALESCE(MAX(`callid`),0)+1 as id FROM clickcall")->row()->id;
		$sql = "INSERT INTO clickcall SET
				callid		='".$callid."',
				bid		='".$data['bid']."',
				gid		='".$data['gid']."',
				number	='".$data['number']."',
				name	='".$data['name']."',
				email	='".$data['email']."',
				retry	='".$data['retry']."'";
				
		$this->db->query($sql);
		return array('msg'=>'success');
	}
	
	//~ function addLead($data){
		//~ $bid = $data['bid'];
		//~ $sql = "SELECT leadid FROM ".$bid."_leads WHERE number like '%".$data['number']."%' OR email like '%".$data['email']."%'";
		//~ $rst = $this->db->query($sql);
		//~ $duplicate = 0;
		//~ $parentId = 0;
		//~ if($rst->num_rows() >0 ){
			//~ $duplicate = 1;
			//~ $parentId = $rst->row()->leadid;
		//~ }
		//~ $leadid = $this->db->query("SELECT COALESCE(MAX(`leadid`),0)+1 as id FROM ".$bid."_leads")->row()->id;
		//~ $this->db->set('leadid',$leadid);
		//~ $this->db->set('bid',$data['bid']);
		//~ $this->db->set('enteredby',$data['enteredby']);
		//~ $this->db->set('convertedby',$data['convertedby']);
		//~ $this->db->set('duplicate',$duplicate);
		//~ $this->db->set('parentId',$parentId);
		//~ $this->db->set('dis_type',"API");
		//~ foreach($data as $k=>$v){
			//~ $keys = array_keys($data);
			//~ if($k == 'name' || $k == 'number' || $k == 'email' || $k == 'source' || $k=='lead_status'  )
				//~ $this->db->set($k,$v);
			//~ elseif($k == 'gid' || $k =='group' || $k == 'groupname'){
					//~ $group = $this->checkGroup($data['bid'],$v);
					//~ $gid = ($group == '') ? '0' : $group;
					//~ $this->db->set("gid",$gid);
				//~ if(!isset($data['assignto']) && $gid != '0' && (in_array('roundrobin',$keys) || in_array('sequential',$keys) || in_array('weighted',$keys) || in_array('autoassign',$keys))){
					//~ $rule = $this->db->query("SELECT group_rule as rule FROM ".$bid."_leads_groups WHERE gid='".$gid."'")->row()->rule;
					//~ if($rule == '2'){
						//~ $resultemp = $this->db->query("SELECT e.eid,COALESCE(((weight/(SELECT sum(weight) FROM ".$bid."_leads_grpemp WHERE gid=ge.gid))-(counter/(SELECT sum(counter) FROM ".$bid."_leads_grpemp WHERE gid=ge.gid))),0) as pc FROM ".$bid."_employee e LEFT JOIN ".$bid."_leads_grpemp ge on e.eid=ge.eid WHERE ge.gid='".$gid."' e.status = 1 AND ge.status = 1 ORDER BY pc DESC LIMIT 0,1")->result_array();
						//~ $eid = $resultemp[0]['eid'];
						//~ if(count($resultemp) > 0)
							//~ $this->db->set("assignto",$eid);
					//~ }elseif($rule == '1'){
						//~ $eid = $this->db->query("SELECT ge.eid FROM ".$bid."_leads_grpemp ge LEFT JOIN ".$bid."_employee e ON ge.eid = e.eid WHERE ge.gid='".$gid."' AND ge.status = 1 AND e.status = 1 AND ge.bid='".$bid."' ORDER BY counter LIMIT 0,1")->row()->eid;
						//~ if($eid != '')
							//~ $this->db->set("assignto",$eid);
					//~ }
					//~ $this->db->query("UPDATE ".$bid."_leads_grpemp SET `counter`=(`counter`+1) WHERE eid='".$eid."' AND gid='".$gid."'");
				//~ }
			//~ }elseif($k == 'assignto' || $k =='eid' || $k == 'empname' ){
				//~ if(is_numeric($v)){
					//~ $this->db->set("assignto",$v);
				//~ }else{
					//~ $assign = $this->checkEmp($data['bid'],$v);
					//~ $this->db->set("assignto",$assign);
				//~ }
			//~ }elseif($k == 'refId' || $k =='referenceId' || $k == 'referenceid' || $k == 'refid'){
				//~ $refExists = $this->checkRefer($data['bid'],$v);
				//~ if(count($refExists) > 0){
					//~ $this->db->set("refId",$v);
				//~ }else{
					//~ $this->db->set("refId",$v);
				//~ }
			//~ }
		//~ }
		//~ $this->db->set("status",1);
		//~ $this->db->set('createdon',date('Y-m-d h:i:s'));
		//~ $this->db->set('lastmodified',date('Y-m-d h:i:s'));
		//~ $this->db->insert($bid."_leads");
		//~ /* New code for resolving the performance issue(temp solution) */
		//~ //$this->db->query("REPLACE INTO ".$bid."_lead_child SET leadid='".$leadid."',bid = '".$bid."';");
		//~ /* End */
		//~ if(isset($data['remark'])){
			//~ $this->db->set('leadid',$leadid);
			//~ $this->db->set('bid',$data['bid']);
			//~ $this->db->set('eid',$data['enteredby']);
			//~ $this->db->set('remark',$data['remark']);
			//~ $this->db->insert($bid."_leads_remarks");
		//~ }
		//~ return array('msg'=>'success');
	//~ }
	function addProspect($data){
        $bid = $data['bid'];
        $dupChk = isset($data['duplicate']) ? $data['duplicate'] : 'TRUE' ;
        $sql = "SELECT leadid FROM ".$bid."_leads WHERE number like '%".$data['number']."%' OR email like '%".$data['email']."%'";
        $rst = $this->db->query($sql);
        $duplicate = 0;
        $parentId = 0;
		if($rst->num_rows() >0  && $dupChk == 'FALSE'){
            return array('msg'=>'Duplicate Error');
        }elseif($rst->num_rows() >0 && $dupChk == 'TRUE'){
	        $duplicate = 1;
            $parentId = $rst->row()->leadid;
        }
		foreach($data as $k=>$v){
			$keys = array_keys($data);
			if($k == 'group'){
				$group = $this->checkGroup($data['bid'],$v);
				$gid = ($group == '') ? '0' : $group;
				$this->db->set("gid",$gid);
			}
			if($k == 'assignto'){
				$emp = 0;
				$assign = $this->checkEmp($data['bid'],$v);
				if($assign != 0){
					$emp = $assign;
				}elseif($assign == 0 && $gid != 0){
					$rule = $this->db->query("SELECT group_rule as rule FROM ".$bid."_leads_groups WHERE gid='".$gid."'")->row()->rule;
					if($rule == '2'){
						$resultemp = $this->db->query("SELECT e.eid,COALESCE(((weight/(SELECT sum(weight) FROM ".$bid."_leads_grpemp WHERE gid=ge.gid))-(counter/(SELECT sum(counter) FROM ".$bid."_leads_grpemp WHERE gid=ge.gid))),0) as pc FROM ".$bid."_employee e LEFT JOIN ".$bid."_leads_grpemp ge on e.eid=ge.eid WHERE ge.gid='".$gid."' e.status = 1 AND ge.status = 1 ORDER BY pc DESC LIMIT 0,1")->result_array();
						$eid = $resultemp[0]['eid'];
						if(count($resultemp) > 0)
							$emp = $eid;
					}elseif($rule == '1'){
						$eid = $this->db->query("SELECT ge.eid FROM ".$bid."_leads_grpemp ge LEFT JOIN ".$bid."_employee e ON ge.eid = e.eid WHERE ge.gid='".$gid."' AND ge.status = 1 AND e.status = 1 AND ge.bid='".$bid."' ORDER BY counter LIMIT 0,1")->row()->eid;
						if($eid != '')
							$emp = $eid;
					}
					$this->db->query("UPDATE ".$bid."_leads_grpemp SET `counter`=(`counter`+1) WHERE eid='".$eid."' AND gid='".$gid."'");
				}else{
					$eid = $this->db->query("SELECT eid FROM ".$bid."_employee WHERE status = 1 LIMIT 0,1")->row()->eid;
					if($eid != '')
						$emp = $eid;
				}
				$this->db->set('assignto',$emp);
			}
			if($k == 'refId'){
				$refExists = $this->checkRefer($data['bid'],$v);
				if(count($refExists) > 0){
					$this->db->set("refId",$v);
				}else{
					$this->db->set("refId",$v);
				}
			}
			if($k == 'name' || $k == 'number' || $k == 'email' || $k == 'source' || $k=='lead_status'  )
				$this->db->set($k,$v);
		}
		$leadid = $this->db->query("SELECT COALESCE(MAX(`leadid`),0)+1 as id FROM ".$bid."_leads")->row()->id;
		$this->db->set('leadid',$leadid);
		$this->db->set('bid',$data['bid']);
		$this->db->set('enteredby',$data['enteredby']);
		$this->db->set('convertedby',$data['convertedby']);
		$this->db->set('duplicate',$duplicate);
		$this->db->set('parentId',$parentId);
		$this->db->set('dis_type',"API");
		$this->db->set("status",1);
		$this->db->set('createdon',date('Y-m-d h:i:s'));
		$this->db->set('lastmodified',date('Y-m-d h:i:s'));
		$this->db->set('convertedon',date('Y-m-d h:i:s'));
		$this->db->insert($bid."_leads");
		/* New code for resolving the performance issue(temp solution) */
		//$this->db->query("REPLACE INTO ".$bid."_lead_child SET leadid='".$leadid."',bid = '".$bid."';");
		/* End */
		if(isset($data['remark'])){
			$this->db->set('leadid',$leadid);
			$this->db->set('bid',$data['bid']);
			$this->db->set('eid',$data['enteredby']);
			$this->db->set('remark',$data['remark']);
			$this->db->insert($bid."_leads_remarks");
		}
		if($emp!='0'){
			$empdetails = $this->getEmpinfo($emp,$bid);
			$bal = $this->configmodel->smsBalance($bid);
			if($bal > 0){
				$message = "Your New assigned Lead Details are ";
				$message .= " no:".$data['number']." and Name:".$data['name'].".Powered By MCube";
				$api = "http://115.249.28.90/sms/sendSMS.php?from=vmc.in";
				$api.= "&to=".$empdetails->empnumber."&text=".urlencode($message);
				file($api);
				$fp = fopen('smslog.txt','a+');fwrite($fp,"\n[".date('Y-m-d H:i:s')."] ".$api);fclose($fp);
			}
			$to = $empdetails->empemail;
			$subject = "Assigned New Lead details";
			$msg = "Your New assigned Lead Details are ";
			$msg .= " Number:".$data['number']." <br/> Name:".$data['name'];
			$msg .= " <br/>Email:".$data['email']." <br/> Source:".$data['source'];
			$msg .= " <br/>Remark:".$data['remark'];
			$this->load->library('email');
			$this->email->from('noreply@mcube.com', 'MCube');
			$this->email->to($to);
			$this->email->subject($subject);
			$this->email->message($msg);
			$this->email->send();
		}
		return array('msg'=>'success');
    } 
	function addSupTicket($data){
		$bid = $data['bid'];
		$sql = "SELECT tktid FROM ".$bid."_support_tickets WHERE number like '%".$data['number']."%' OR email like '%".$data['email']."%'";
		$rst = $this->db->query($sql);
		$duplicate = 0;
		$parentId = 0;
		if($rst->num_rows() >0 ){
			$duplicate = 1;
			$parentId = $rst->row()->leadid;
		}
		foreach($data as $k=>$v){
			$keys = array_keys($data);
			if($k == 'group'){
				$group = $this->checkSupGroup($data['bid'],$v);
				$gid = ($group == '') ? '0' : $group;
				$this->db->set("gid",$gid);
			}
			if($k == 'assignto'){
				$emp = 0;
				$assign = $this->checkEmp($data['bid'],$v);
				if($assign != 0){
					$emp = $assign;
				}elseif($assign == 0 && $gid != 0){
					$rule = $this->db->query("SELECT group_rule as rule FROM ".$bid."_support_groups WHERE gid='".$gid."'")->row()->rule;
					if($rule == '2'){
						$resultemp = $this->db->query("SELECT e.eid,COALESCE(((weight/(SELECT sum(weight) FROM ".$bid."_support_grpemp WHERE gid=ge.gid))-(counter/(SELECT sum(counter) FROM ".$bid."_support_grpemp WHERE gid=ge.gid))),0) as pc FROM ".$bid."_employee e LEFT JOIN ".$bid."_support_grpemp ge on e.eid=ge.eid WHERE ge.gid='".$gid."' e.status = 1 AND ge.status = 1 ORDER BY pc DESC LIMIT 0,1")->result_array();
						$eid = $resultemp[0]['eid'];
						if(count($resultemp) > 0)
							$emp = $eid;
					}elseif($rule == '1'){
						$eid = $this->db->query("SELECT ge.eid FROM ".$bid."_support_grpemp ge LEFT JOIN ".$bid."_employee e ON ge.eid = e.eid WHERE ge.gid='".$gid."' AND ge.status = 1 AND e.status = 1 AND ge.bid='".$bid."' ORDER BY counter LIMIT 0,1")->row()->eid;
						if($eid != '')
							$emp = $eid;
					}
					$this->db->query("UPDATE ".$bid."_support_grpemp SET `counter`=(`counter`+1) WHERE eid='".$eid."' AND gid='".$gid."'");
				}else{
					$eid = $this->db->query("SELECT eid FROM ".$bid."_employee WHERE status = 1 LIMIT 0,1")->row()->eid;
					if($eid != '')
						$emp = $eid;
				}
				$this->db->set('assignto',$emp);
			}
			if($k == 'refId'){
				$refExists = $this->checkTktReferId($data['bid'],$v);
				if(count($refExists) > 0){
					$this->db->set("refId",$v);
				}else{
					$this->db->set("refId",$v);
				}
			}
			if($k == 'name' || $k == 'number' || $k == 'email' || $k == 'source' || $k == 'tkt_status'  )
				$this->db->set($k,$v);
		}
		$tktid = $this->db->query("SELECT COALESCE(MAX(`tktid`),0)+1 as id FROM ".$bid."_support_tickets")->row()->id;
		$this->db->set('tktid',$tktid);
		$this->db->set('bid',$data['bid']);
		$this->db->set('enteredby',$data['enteredby']);
		$this->db->set('convertedby',$data['convertedby']);
		$this->db->set('duplicate',$duplicate);
		$this->db->set('parentId',$parentId);
		$this->db->set('dis_type',"API");
		$this->db->set("status",1);
		$this->db->set('createdon',date('Y-m-d h:i:s'));
		$this->db->set('lastmodified',date('Y-m-d h:i:s'));
		$this->db->insert($bid."_support_tickets");
		if(isset($data['remark'])){
			$this->db->set('tktid',$tktid);
			$this->db->set('bid',$data['bid']);
			$this->db->set('eid',$data['enteredby']);
			$this->db->set('remark',$data['remark']);
			$this->db->insert($bid."_support_remarks");
		}
		return array('msg'=>'success');
	}
	function getEmpGroup($bid,$eid){
		$sql=$this->db->query("SELECT gid FROM  ".$bid."_group_emp WHERE bid='".$bid."' and eid='".$eid."'");
		 if($sql->num_rows()>0){
			 return $sql->row();
		 }else{
			 return array();
		 }
	 }
	 function checkGroup($bid,$group){
		$sql = $this->db->query("SELECT gid FROM  ".$bid."_leads_groups WHERE bid='".$bid."' and groupname like '%".$group."%'");
		if($sql->num_rows()>0){
			return $sql->row()->gid;
		}
	 }
	 function checkSupGroup($bid,$group){
		$sql = $this->db->query("SELECT gid FROM  ".$bid."_support_groups WHERE bid='".$bid."' and groupname like '%".$group."%'");
		if($sql->num_rows()>0){
			return $sql->row()->gid;
		}
	 }
	 function checkEmp($bid,$emp){
		$sql = $this->db->query("SELECT eid FROM  ".$bid."_employee WHERE bid='".$bid."' and (empname like '%".$emp."%' OR empemail like '%".$emp."%')");
		if($sql->num_rows()>0){
			return $sql->row()->eid;
		}
		return 0;
	 }
	 
	 function checkRefer($bid,$refId){
		$sql = $this->db->query("SELECT leadid FROM  ".$bid."_leads WHERE bid='".$bid."' AND refId = '".$refId."'");
		if($sql->num_rows()>0){
		 return $sql->row();
		}else{
		 return 0;
		}
	 }
	 function checkTktReferId($bid,$refId){
		$sql = $this->db->query("SELECT tktid FROM  ".$bid."_support_tickets WHERE bid='".$bid."' AND refId = '".$refId."'");
		if($sql->num_rows()>0){
		 return $sql->row();
		}else{
		 return 0;
		}
	 }
	 function landingDetails($num){
		 $sql=$this->db->query("SELECT * FROM prinumbers where landingnumber='".$num."' and status=1 and type='4'");
		 if($sql->num_rows()>0){
			return $sql->row();	
		 }else{
			 return array();
		 }
	 }
	 
	 function get_api_activity($bid,$keyword){
		
		$sql="SELECT SQL_CALC_FOUND_ROWS *  FROM ".$bid."_activity where keyword='".$keyword."' AND status=1" ;
        $res = $this->db->query($sql)->row();
		//$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		//$res['count'] = $rst1->row()->cnt;
		return $res;
	 }
	 function get_activity_fields($bid,$cid,$agid){
		
		$sql="SELECT *  FROM ".$bid."_customactivity where actid='".$cid."' AND agid='".$agid."'" ;
        $res = $this->db->query($sql)->result();
		//$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		//$res['count'] = $rst1->row()->cnt;
		return $res;
	 }
	 function add_activity_report($bid,$eid,$acid,$agid){
		$aid = $this->db->query("SELECT COALESCE(MAX(`aid`),0)+1 as id FROM ".$bid."_activityreport")->row()->id;
		$sql="INSERT INTO ".$bid."_activityreport(aid,eid,agid,actid) VALUES($aid,$eid,$agid,$acid)" ;
        $res = $this->db->query($sql);
        $id = $this->db->insert_id();
		//$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		//$res['count'] = $rst1->row()->cnt;
		return $id;
	 }
	 function add_activity_values($bid,$data){
		
		$sql="INSERT INTO ".$bid."_customactivityvalue(fieldid,agid,actid,dataid,value) VALUES(".$data['fieldid'].",".$data['agid'].",".$data['actid'].",'".$data['dataid']."','".$data['value']."')" ;
        $res = $this->db->query($sql);
		//$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		//$res['count'] = $rst1->row()->cnt;
		return $res;
	 }
	 
	 function add_outbound_values($bid,$data){
		$callid = $data['exenumber'].time();
		
		$sql = "INSERT INTO click2connect SET
			bid			    = '".$bid."'
			,callid		    = '".$callid."'
			,modid			= '".$data['modid']."'
			,dataid			= '".$data['dataid']."'
			,eid			= '".$data['eid']."'
			,exenumber		= '".$data['exenumber']."'
			,custnumber		= '".$data['custnumber']."'
			,callback_url	= '".$data['callback_url']."'";
			
		//echo $sql;exit;
		$res = $this->db->query($sql);
		return $res;
	 }
	 function group_set($lno,$bid,$eid){
		 	$gid=$this->db->query("SELECT COALESCE(MAX(`gid`),0)+1 as id FROM ".$bid."_groups")->row()->id;
			$this->db->set('gid',$gid);
			$this->db->set('eid',$eid);
			$this->db->set('bid',$bid);
			$this->db->set('groupname',urldecode($_REQUEST['landingnumber']));
			$this->db->set('prinumber',$lno);
			$this->db->set('rules','1');
			$this->db->set('primary_rule',0);
			$this->db->set('mailalert',0);
			$this->db->set('record',0);
			$this->db->set('recordnotice',0);
			$this->db->set('replymessage','The number of the advertiser you just called through 99acres is @number@');
			$this->db->set('replyattmsg','The number of the advertiser you just called through 99acres is @number@');
			$this->db->set('connectowner','0');
			$this->db->set('replytoexecutive','1');
			$this->db->set('replytocustomer','1');
			$this->db->set('bday','{"Mon":{"day":"1","st":"00:00","et":"23:59"},"Tue":{"day":"1","st":"00:00","et":"23:59"},"Wed":{"day":"1","st":"00:00","et":"23:59"},"Thu":{"day":"1","st":"00:00","et":"23:59"},"Fri":{"day":"1","st":"00:00","et":"23:59"},"Sat":{"day":"1","st":"00:00","et":"23:59"},"Sun":{"day":"1","st":"00:00","et":"23:59"}}');
			$this->db->insert($bid."_groups");	
			
			$this->db->query("
				INSERT INTO `".$bid."_group_emp` (`bid`, `gid`, `eid`, `empnumber`, `callid`, `callcounter`, `empweight`, `area_code`, `pincode`, `starttime`, `endtime`, `isfailover`, `status`) VALUES ('".$bid."', '".$gid."', '".$eid."', '', '', '0', '', '', '', '00:00:00', '23:59:00', '0', '1');
			");
			
			$this->update_pri($lno,$gid,$bid,$type=0);
			return $gid;
			
	 }
	 function Clandingnumber($bid,$lno,$key){
		 $res=array();
		 $sql=$this->db->query("SELECT * FROM prinumbers where landingnumber='".$lno."' and bid='".$bid."' and landing_key='".$key."'");
		 if($sql->num_rows()>0){
			 $res=$sql->row();
			 return (array)$res;
		 }else{
			 return $res;
		 }
	 }
	 function freeLnumber($lno,$bid){
		 if($this->db->query("SELECT * FROM prinumbers WHERE number='".$lno."' AND status='1'")->num_rows()>0){
			$sql=$this->db->query("UPDATE ".$bid."_groups set status=0 where prinumber='".$lno."'");
			$this->db->query("update prinumbers set status=0,associateid=0 where number='".$lno."'");
			return 1;
		}else{
			return 0;
		}	
	 }
	 function check_apikey($key){
		$res=array();
		if($key!=''){
			$sql=$this->db->query("SELECT * FROM business where apisecret='".$key."'");
			if($sql->num_rows>0)
			{
				$res=$sql->row();
				return $res;
			}
		}
		return $res;
	}
	function check_apisecret($bid,$email){
		$res=array();
		$sql=$this->db->query("SELECT empemail FROM ".$bid."_employee where empemail='".$email."'");
		if($sql->num_rows>0)
		{
			return false;
		}
		return true;
	}
	function EmpClickDetails($bid){
		$sql=$this->db->query("SELECT * from ".$bid."_employee where click2connect='1'");
		$res=$sql->row();
		if(!empty($res)){
			return array($res->eid,$res->empnumber);
		}else{
			$sql=$this->db->query("SELECT * from ".$bid."_employee where eid='1'");
			$res=$sql->row();	
			return array($res->eid,$res->empnumber);
		}
	}
	function ConnectCall($post){
		$this->load->model('configmodel');
		$calBal = $this->configmodel->callBalance($post['bid']);
		$enumber=$this->EmpClickDetails($post['bid']);
		if($calBal<='0'){
			$ret = '2';
		}else{
			$st = (array)json_decode(file_get_contents("http://180.179.200.180/filter.php?num=".substr($post['number'],-10,10)));
			$ret = ($st['dnd']=='1') ? '0' : '1';
			$callid = $enumber[1].time();
			$sql = "INSERT INTO click2connect SET
					callid		='".$callid."'
					,bid		='".$post['bid']."'
					,modid		='api'
					,dataid		=''
					,exenumber	='".$enumber[1]."'
					,custdnd	='".$st['dnd']."'
					,custnumber	='".substr($post['number'],-10,10)."'
					,eid		='".$enumber[0]."'";
			$this->db->query($sql);
		}
		return $ret;
	}
	
	function getIVRSReportXML($user,$roleid,$time){
		$bid = $user->bid;
		$eid = $user->eid;
		$roleDetail=$this->apimodel->getRoledetail($roleid,$bid);
		$sql="SELECT SQL_CALC_FOUND_ROWS h.hid,h.filename FROM ".$bid."_ivrshistory h
				where h.datetime>='".$time['start']."' and h.datetime<='".$time['end']."'
				ORDER BY h.datetime DESC";		
			
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");

		$fieldset = $this->configmodel->getFields('16',$bid);
		$keys = array();
		$header = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,$field['fieldname']);
					$header[$field['fieldname']] = (($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_16')->$field['fieldname']);
				}
			}
			//~ elseif($field['type']=='c' && $field['show'] && $field['listing']){
				//~ foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				//~ if($checked){
					//~ array_push($keys,$field['fieldname']);
					//~ $header[$field['fieldname']] = $field['customlabel'];
				//~ }
			//~ }
		}
		$list = array();
		foreach($rst as $rec){
			$data = "\t<call>\n";
			$data .= "\t\t<callid>".$rec['hid']."</callid>\n";
			$r = $this->configmodel->getDetail('16',$rec['hid'],'',$bid);
			foreach($keys as $k){
				$tag = $k;
				if($k=='eid') 		$tag = 'calltoname';
				if($k=='name')		$tag = 'client_name';
				if($k=='email')		$tag = 'client_email';
				if($k=='datetime') 	$tag = 'startdatetime';
				if($k=='endtime') 	$tag = 'enddatetime';
				if($k=='callfrom') 	$tag = 'callfrom';
				$tag = str_replace(" ","_",$tag);
				$val = (isset($r[$k])) ? str_replace("->",",",$r[$k]) : "";
				$val = (isset($r[$k])) ? str_replace("<br>","",$val) : "";
				$val = (isset($r[$k])) ? str_replace( array("<", ">", "\"", "'", "&"),
                                         array("&lt;", "&gt;", "&quot;", "&apos;", "&amp;"),$val) : "" ;

                $data .= "\t\t<".$tag.">".$val."</".$tag.">\n";
                                
			}
			$file = file_exists('sounds/'.$r['filename']) ? base_url()."sounds/".$r['filename'] : "";
			$data .= "\t\t<filename>".$file."</filename>\n";
			$data .= "\t</call>\n";
			array_push($list,$data);
		}
		return $list;
	}
	
	function smsLog($data){
		$sql = " INSERT INTO smsLog SET
				 `smsto`	='".$data['to']."'
				,`smsfrom`	='".$data['from']."'
				,`text`		='".$data['text']."'
				,`time`		='".$data['time']."'";
		$this->db->query($sql);
	}
	
	function getPRI($landingNumber){
		$sql = "SELECT number,bid,status,type,associateid,sms_limit,sms_count 
				FROM prinumbers WHERE landingnumber='".$landingNumber."'";
		return $this->db->query($sql)->row_array();
	}
	
	function smsAssign($data){
		//print_r($data);exit;
		$bid = $data['bid'];
		$callfrom = substr($data['from'],-10,10);
		$sql = "UPDATE prinumbers SET sms_count=sms_count+1 WHERE number='".$data['number']."'";
		$this->db->query($sql);
		$sql = "SELECT * FROM ".$bid."_groups WHERE gid='".$data['gid']."'";
		$gDetail = $this->db->query($sql)->row_array();
		//echo "<pre>";print_r($gDetail);echo "</pre>";exit;
		$eid = '0';
		$leadid= '0';
		if($gDetail['sameexe']=='1'){
			$sql = "SELECT * FROM (
					SELECT * FROM `".$bid."_callhistory` 
					WHERE bid = '".$bid."' AND callfrom = '".$callfrom."'
					UNION SELECT * FROM `".$bid."_callarchive` 
					WHERE bid = '".$bid."' AND callfrom = '".$callfrom."'
					) a ORDER BY starttime DESC limit 1";
			$eid = $this->db->query($sql)->row()->assignto;
			$leadid = $this->db->query($sql)->row()->leadid;
		}
		if($leadid=='0'){
			$sql = "SELECT leadid FROM ".$bid."_leads WHERE number='".$callfrom."'";
			$rst = $this->db->query($sql);
			if($rst->num_rows()>'0'){
				$leadid = $rst->row()->leadid;
			}
		}
		
		$sql = "SELECT e.eid,e.empname,e.empnumber,e.empemail
				FROM ".$bid."_group_emp ge
				LEFT JOIN ".$bid."_employee e ON ge.eid=e.eid
				WHERE ge.gid='".$data['gid']."'
				AND ge.status='1'
				AND e.status='1'
				AND e.selfdisable='0'
				AND ge.starttime<=CURRENT_TIME()
				AND ge.endtime>=CURRENT_TIME()
				AND ge.isfailover='0' ";
		$sql.= ($eid>0) ? " AND e.eid='".$eid."'" : "";
		$sql.= " ORDER BY ge.callcounter ASC LIMIT 0,1 ";
		$rst = $this->db->query($sql);
		if($rst->num_rows()=='0'){
			$sql = "SELECT e.eid,e.empname,e.empnumber,e.empemail
				FROM ".$bid."_group_emp ge
				LEFT JOIN ".$bid."_employee e ON ge.eid=e.eid
				WHERE ge.gid='".$bid."'
				AND ge.status='1'
				AND e.status='1'
				AND e.selfdisable='0'
				AND ge.starttime<=CURRENT_TIME()
				AND ge.endtime>=CURRENT_TIME()
				AND ge.isfailover='0' 
				ORDER BY ge.callcounter ASC LIMIT 0,1";
			$rst = $this->db->query($sql);
		}
		$exeDetail = $rst->row_array();
		$eid = isset($exeDetail['eid']) ? $exeDetail['eid'] : '0';
		$eName = isset($exeDetail['eid']) ? $exeDetail['empname'] : '';
		$eEmail = isset($exeDetail['eid']) ? $exeDetail['empemail'] : '';
		$eNumber = isset($exeDetail['eid']) ? $exeDetail['empnumber'] : '';
		$callid = $this->db->query("SELECT COALESCE(MAX(`calid`),0)+1 as id FROM ".$bid."_callhistory")->row()->id;
		$dt = date('Y-m-d H:i:s');
		$sql = " INSERT INTO ".$data['bid']."_callhistory SET
				 callid		='".$callfrom.time()."'
				,calid		='".$callid."'
				,refid		='".$callid."'
				,bid		='".$bid."'
				,gid		='".$data['gid']."'
				,eid		='".$eid."'
				,assignto	='".$eid."'
				,source		='SMS'
				,sms_content='".$data['text']."'
				,keyword	='".$gDetail['keyword']."'
				,callfrom	='".$callfrom."'
				,callto		='".$eNumber."'
				,leadid		='".$leadid."'
				,starttime	= '".$dt."'
				,endtime	= '".$dt."'
				,status		= '1'
				,dialstatus = 'SMS'
				";
		$this->db->query($sql);
		
		$sql = "UPDATE ".$bid."_group_emp SET callcounter=callcounter+1 WHERE gid='".$data['gid']."' AND eid='".$eid."'";
		$this->db->query($sql);
		
		if($eid!='0'){
			$api = "http://115.249.28.90/sms/sendSMS.php?from=vmc.in";
			$api.= "&to=".$eNumber."&text=".urlencode("You have a SMS from ".$callfrom." as ".$data['text']);
			file( $api);
			$fp = fopen('smslog.txt','a+');fwrite($fp,"\n[".date('Y-m-d H:i:s')."] ".$api);fclose($fp);
			
			$to = $eEmail;
			//$ownermail = $emp['ownermail'];
			$subject = "You have SMS from ".$callfrom;
			$message = "You have a SMS from ".$callfrom." for ".$gDetail['groupname'] .' as '.$data['text'];
			
			$this->load->library('email');
			$this->email->from('noreply@mcube.com', 'MCube');
			$this->email->to($to);
			//if($emp['mailalerttowoner']=='1')$this->email->cc($ownermail);
			$this->email->subject($subject);
			$this->email->message($message);
			$this->email->send();
		}
		
		return true;			
	}
}

/* end of model*/
