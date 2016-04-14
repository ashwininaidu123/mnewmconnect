<?php
Class Systemmodel extends Model
{
	function Systemmodel(){
		 parent::Model();
		 $this->load->model('auditlog');
		 $this->load->model('groupmodel');
	}
	
	function update_system_labels($mod){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$mod = ($mod=='46') ? '26' : $mod;
		$arr=array_keys($_POST['system']['0']);
		for($i=0;$i<sizeof($arr);$i++){
			$show = (isset($_POST['system']['1'][$arr[$i]]))? 1 :0 ;
			$listing = (isset($_POST['system']['3'][$arr[$i]]))? 1 :0 ;
			$sql="REPLACE INTO ".$bid."_custom_label set 
			bid = '".$bid."',
			modid='".$mod."',
			fieldid='".$arr[$i]."',
			fieldtype='s',
			customlabel='".$_POST['system']['0'][$arr[$i]]."',
			listing='".$listing."',
			display_order='".$_POST['system']['2'][$arr[$i]]."',
		
			`show`=".$show;
			$this->db->query($sql);
			$this->auditlog->auditlog_info('System Label',$_POST['system']['0'][$arr[$i]]. " System Label updated");
		}
		$arr=array_keys($_POST['custom']['0']);
		for($i=0;$i<sizeof($arr);$i++){
			$show = (isset($_POST['custom']['1'][$arr[$i]]))? 1 :0 ;
			$listing = (isset($_POST['custom']['3'][$arr[$i]]))? 1 :0 ;
			$sql="REPLACE INTO ".$bid."_custom_label set 
			bid = '".$bid."',
			modid='".$mod."',
			fieldid='".$arr[$i]."',
			fieldtype='c',
			customlabel='".$_POST['custom']['0'][$arr[$i]]."',
				listing='".$listing."',
			display_order='".$_POST['custom']['2'][$arr[$i]]."',
			`show`=".$show;
			$this->db->query($sql);
			$this->auditlog->auditlog_info('System Label',$_POST['custom']['0'][$arr[$i]]. " Custom Label updated");
		}
		$this->auditlog->auditlog_info($this->lang->line('level_module_Configuration'),"Updated System Lables");
	}
	function RegionsList($ofset='0',$limit='20'){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$q='';
		if($this->session->userdata('regionname')!=""){
			$regionname=$this->session->userdata('regionname');
				$q.=" where regionname like '%".$regionname."%'";
			}
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS regionid,regionname from ".$bid."_custom_region $q LIMIT $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;
		
	}
	function feature_manage()
	{
		$sql=$this->db->query("SELECT * FROM `feature_list` where parent_id=0");
		return $sql->result_array();
	}
	function sub_featuremanage()
	{
		$sql=$this->db->query("SELECT * FROM `feature_list` where parent_id!=0");
		return $sql->result_array();
	}
	function checked_featuremanage($bid=''){
		//$cbid=$this->session->userdata('cbid');
		$bid = ($bid!='') ? $bid : $this->session->userdata('bid');
		$res=array();
		$sql=$this->db->query("SELECT feature_id FROM `business_feature` where bid=".$bid);
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $ress){
				$res[]=$ress['feature_id'];
			}
		}
		return $res;
	}
	function update_featuremanage()
	{
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("delete from business_feature where bid=".$bid);
		for($i=1;$i<=5;$i++)
		{
			if(isset($_POST['featureconfig'][$i])){
				$sql="REPLACE INTO business_feature set bid=".$bid.",
						feature_id=".$i;
				$this->db->query($sql);		
			}
		}
		return true;
	}
	function addcustomfield(){
		$bid=$this->session->userdata('bid');
		$modid = ($this->input->post('grouptype')=='46') ? '26' : $this->input->post('grouptype');
		$fieldid=$this->db->query("SELECT COALESCE(MAX(`fieldid`),0)+1 as id FROM ".$bid."_customfields")->row()->id;
		$this->db->set('fieldid', $fieldid); 
		$this->db->set('bid', $bid); 
		$this->db->set('modid', $modid); 
		$this->db->set('fieldname',$this->input->post('label_name')); 
		$this->db->set('fieldtype', $this->input->post('label_list')); 
		$this->db->set('defaultvalue', $this->input->post('labeldefaultval'));  
		$this->db->set('options', trim($this->input->post('labeloptions'))); 
		$this->db->set('is_required', $this->input->post('isrequired'));
		/* New code added for Custom management */
		$field_key = "c_".$fieldid;
		$this->db->set('field_key', $field_key);
		$this->db->insert($bid.'_customfields'); 
		/* New code added for Custom management */
		if($this->lang->line('table_'.$modid) != ''){
			$sql1 = "ALTER TABLE ".$bid."_".$this->lang->line('table_'.$modid)." ADD ".$field_key." VARCHAR(50)";
			$result = $this->db->query($sql1);
			if($modid == 6 && $result == 1){
				$sql2 = "ALTER TABLE ".$bid."_callarchive ADD ".$field_key." VARCHAR(50)";
				$result1 = $this->db->query($sql2);
			}
		}
		/* end */
	    $module_name=$this->get_all_modules($this->input->post('grouptype'));
		$this->auditlog->auditlog_info('Custom Label',$this->input->post('label_name'). " Custom Field added for Module ".$module_name[0]['modname']);
		$id=$this->db->insert_id();
		$sql="REPLACE INTO ".$bid."_custom_label set 
			bid = '".$bid."',
			modid='".$modid."',
			fieldid='".$fieldid."',
			fieldtype='c',
			customlabel='".$this->input->post('label_name')."',	
			`show`=1";
		$this->db->query($sql);
		$module_name=$this->get_modules($this->input->post('grouptype'));
		$lname=$this->input->post('label_name');
		$field_access=$this->field_access($bid,$fieldid,$modid);
		return $this->input->post('grouptype');
	}
	function field_access($bid,$fid,$modid){
		$role_id=$this->db->query("SELECT roleid FROM ".$bid."_user_role ")->result_array();
		foreach($role_id as $role){
			$this->db->set('bid',$bid);
			$this->db->set('fieldid',$fid);
			$this->db->set('roleid',$role['roleid']);
			$this->db->set('modid',$modid);
			$this->db->set('fieldtype','c');
			$this->db->insert($bid."_role_access");
		}
		return true;
	}
	function delete_customfields($id,$modid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		//if(($bid == 47 || $bid == 1  || $bid == 257)){
			$field_key = $this->getFieldKey($id,$bid);
			if($this->lang->line('table_'.$modid) != ''){
				$sql1 = "ALTER TABLE ".$bid."_".$this->lang->line('table_'.$modid)." DROP `".$field_key."`";
				$result = $this->db->query($sql1);
				if($modid == 6 && $result == 1){
					$sql2 = "ALTER TABLE ".$bid."_callarchive DROP ".$field_key;
					$result1 = $this->db->query($sql2);
				}
				if($result){
					$sql=$this->db->query("DELETE FROM ".$bid."_custom_label WHERE fieldid=$id AND modid=$modid AND fieldtype='c'");
					$sql1=$this->db->query("DELETE FROM ".$bid."_customfields WHERE fieldid=$id AND modid=$modid");
					$sql1=$this->db->query("DELETE FROM ".$bid."_role_access WHERE fieldid=$id AND fieldtype='c' AND modid=$modid");
					return $modid;
				}
			}
		return $modid;
	}
	function get_custom_info($id,$modid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->db->query("SELECT f.fieldname,f.fieldtype,f.options,c.customlabel FROM ".$this->session->userdata('bid')."_customfields f LEFT join ".$this->session->userdata('bid')."_custom_label c on f.fieldid=c.fieldid and f.modid=c.modid
		where f.fieldid='".$id."' and f.modid='".$modid."'");
		return $res->result_array();
		
	}
	function get_leadstatus_info(){
		$result = array();
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->db->query("SELECT id,type FROM ".$bid."_leads_status");
		if($res->num_rows()>0){
			foreach($res->result_array() as $e)
			$result[$e['id']]=$e['type'];
		}
		return $result;
	}
	function get_leadstatus_info_label(){
		$result = array();
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->db->query("SELECT id,syslabel FROM ".$bid."_leads_status");
		if($res->num_rows()>0){
			foreach($res->result_array() as $e)
			$result[$e['id']]=$e['syslabel'];
		}
		return $result;
	}
	function get_tkt_levels(){
		$result = array();
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->db->query("SELECT id,level,syslabel FROM ".$bid."_support_levels");
		if($res->num_rows()>0){
			//~ foreach($res->result_array() as $e)
			//~ $result[$e['id']]=$e['level'];
			$result = $res->result_array();
		}
		return $result;
	}
	function get_tkt_levels_time(){
		$result = array();
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->db->query("SELECT id,time FROM ".$bid."_support_levels");
		if($res->num_rows()>0){
			foreach($res->result_array() as $e)
			$result[$e['id']]=$e['time'];
		}
		return $result;
	}
	function getSuptktstatus(){
		$result = array();
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->db->query("SELECT * FROM ".$bid."_support_status");
		if($res->num_rows()>0){
			$result = $res->result_array();
		}
		return $result;
	}
	function getSuptktstatus_label(){
		$result = array();
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->db->query("SELECT sid,syslabel FROM ".$bid."_support_status");
		if($res->num_rows()>0){
			foreach($res->result_array() as $e)
			$result[$e['sid']]=$e['syslabel'];
		}
		return $result;
	}
	function update_custom_label($id,$modid){

		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$types=$this->get_custom_info($id,$modid);
		$fbname=$this->input->post('label_name');
		if($fbname!=$types[0]['customlabel']){
			$message="Updated Custom Field ".$types[0]['customlabel']." to ".$fbname;
		}else{
			$message="Updated Custom Field ".$fbname;
		}
		$sql=$this->db->query("update ".$bid."_custom_label SET customlabel='".$fbname."' WHERE fieldid=$id");
		$modulename=$this->get_modules($modid);
		$this->auditlog->auditlog_info($modulename,$message);
		return 1;
	}
	function update_lead_status($id,$modid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		for($i=1;$i<=$_POST['statcnt'];$i++){
			$val = $_POST['stat_'.$i];
			if($val != ''){
				$this->db->set('type',$val);
				$this->db->where('id',$i);
				$this->db->update($bid."_leads_status");
			}
		}
		return 1;
	}
	function update_suptkt_status($id,$modid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		for($i=1;$i<=$_POST['statcnt'];$i++){
			$val = $_POST['stat_'.$i];
			$sms = (isset($_POST['sms_'.$i])) ? '1' : '0' ;
			if($val != ''){
				if(isset($_POST['syslabel_'.$i])){
					$sql = "REPLACE INTO ".$bid."_support_status (`sid`,`status`,`syslabel`,`sms`,`smscontent`) VALUES (".$i.",'".$val."','".$_POST['syslabel_'.$i]."','".$sms."','".$_POST['smscontent_'.$i]."')";
					$res = $this->db->query($sql);
				}else{
					$this->db->set('status',$val);
					$this->db->set('sms',$sms);
					$this->db->set('smscontent',$_POST['smscontent_'.$i]);
					$this->db->where('sid',$i);
					$this->db->update($bid."_support_status");
				}
			}

		}
		return 1;
	}
	function update_ticket_level($id,$modid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$cont = $_POST['statcnt'];
		for($i=1;$i<=$cont;$i++){
			$level = (isset($_POST['level_'.$i])) ? $_POST['level_'.$i] : '';
			$time = (isset($_POST['time_'.$i])) ? $_POST['time_'.$i] : '';
			if($level != ''){
				if(isset($_POST['label_'.$i])){
					$sql = "REPLACE INTO ".$bid."_support_levels (`id`,`level`,`syslabel`,`time`) VALUES (".$i.",'".$level."','".$_POST['label_'.$i]."','".$time."')";
					$res = $this->db->query($sql);
				}else{
					$this->db->set('level',$level);
					$this->db->set('time',$time);
					$this->db->where('id',$i);
					$this->db->update($bid."_support_levels");
				}
			}
		}
		return 1;
	}
	function get_modules($modid){
		$sql=$this->db->query("select * from module where modid=$modid AND status=1");
		 $res=$sql->result_array();
		 return $res[0]['modname'];
	}
	function get_all_modules($modid=''){
		$q=($modid!='')?" and modid='".$modid."'":'';	
		$sql=$this->db->query("select * from module where status=1 $q");
		 $res=$sql->result_array();
		 return $res;
	}
	function get_all_languages(){
		$res=array();
		$sql=$this->db->query("select * from language");
		if($sql->num_rows()>0)
		{
			foreach($sql->result_array() as $e)
			$res[$e['langid']]=$e['language'];
			
		}
		return $res;
	}
	function get_group_list(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();
		$sql=$this->db->query("select a.eid,a.empname  from ".$bid."_employee a,user b where a.eid=b.eid and a.bid=b.bid and a.status=1");
		//$res['']=$this->lang->line('level_select');
		foreach($sql->result_array() as $re)
		$res[$re['eid']]=$re['empname'];
		return $res;
		
	}

	function get_groups(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$q='';$q1 = '';
		if($roleDetail['role']['owngroup']=='1' && $roleDetail['role']['admin']!='1'){
			$q1 .= " LEFT JOIN ".$bid."_group_emp ge ON g.gid = ge.gid ";
			$q .= " AND (ge.eid = '".$this->session->userdata('eid')."' OR g.eid = '".$this->session->userdata('eid')."') ";
		}
		$sql = $this->db->query("SELECT g.gid,g.groupname FROM `".$bid."_groups` g $q1 WHERE g.status = 1 $q");
		$res['0']=$this->lang->line('level_select');
		foreach($sql->result_array() as $re)
		$res[$re['gid']]=$re['groupname'];
		return $res;
		
	}
	

	function get_emp_list(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();
		$sql=$this->db->query("select a.eid,a.empname,a.empnumber 
							   from ".$bid."_employee a
							   WHERE a.status!='2'
							   ORDER BY a.`empname`");
		//$res['']=$this->lang->line('level_select');
		foreach($sql->result_array() as $re)
		$res[$re['eid']]=$re['empname'].' ['.$re['empnumber'].']';
		return $res;
	}
	
	function get_ivrs_list(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();
		$sql=$this->db->query("select * from ".$bid."_ivrs WHERE bid = '".$bid."' AND status='1'");
		foreach($sql->result_array() as $re)
		$res[$re['ivrsid']]=$re['title'];
		return $res;
	}
	
	function get_pbx_list(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();
		$sql=$this->db->query("select * from ".$bid."_pbx WHERE bid = '".$bid."' AND status='1'");
		foreach($sql->result_array() as $re)
		$res[$re['pbxid']]=$re['title'];
		return $res;
	}
	function get_emp_roles(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$r=array();
		$res=$this->db->query("select * from ".$bid."_user_role");
		if($res->num_rows()>0)
		{
			$r=$res->result_array();
			return $r;
		}else{
			return $r;
		}
	}
	function get_smsbalance(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$r=array();
		$sql=$this->db->query("select * from sms_bal where bid=".$bid);
		if($sql->num_rows()>0)
		{
			$r=$sql->row();
			return $r;
		}else{
			return $r;
		}
	}
	function get_call_balance()
	{
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$r=array();
		$sql=$this->db->query("select * from call_bal where bid=".$bid);
		if($sql->num_rows()>0)
		{
			$r=$sql->row();
			return $r;
		}else{
			return $r;
		}
	}
	function upgrade_info($request)
	{
		$get_packages_info=$this->db->query("select * from packages where package_id=".$request['custom']);
		$res=$get_packages_info->row();
		$crid = $this->db->query("SELECT COALESCE(MAX(`crid`),0)+1 as id FROM `call_credit`")->row()->id;
		$this->db->set('crid', $crid); 
		$this->db->set('bid', $this->session->userdata('bid')); 
		$this->db->set('credit',$res->call_credit); 
		$this->db->insert('call_credit');
		
		$crid = $this->db->query("SELECT COALESCE(MAX(`crid`),0)+1 as id FROM `sms_credit`")->row()->id;
		$this->db->set('crid', $crid); 
		$this->db->set('bid', $this->session->userdata('bid')); 
		$this->db->set('credit',$res->sms_credit); 
		$this->db->insert('sms_credit');
	}


	function get_Regions($id=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();
		if($id!=""){
			$sql=$this->db->query("select * from ".$bid."_custom_region where regionid=$id");
			$res=$sql->result_array();
			return $res;
		}else{
			return $res;
		}
		
	}
	
	function get_groupRegions($rid,$grid=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();
		if($grid!=""){
			$sql=$this->db->query("select * from ".$bid."_group_region where gregionid=$grid and regionid=$rid");
			$res=$sql->result_array();
			return $res;
		}else{
			return $res;
		}
	}
	function add_region(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$this->db->set('regionid',$this->db->query("SELECT COALESCE(MAX(`regionid`),0)+1 as id FROM ".$bid."_custom_region")->row()->id);
		$this->db->set('bid',$bid);
		$this->db->set('regionname',$this->input->post('regionname'));
		$this->db->insert($this->session->userdata('bid')."_custom_region");
		return true;
	}
	function update_groupregion($rid,$grid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$this->db->set('regionname',$this->input->post('regionname'));
		$this->db->where('gregionid',$grid);
		$this->db->update($bid."_group_region");
		$this->db->query("delete from ".$bid."_gregion_list where gregionid=$grid");
		for($i=0;$i<sizeof($_POST['multiselect_codes']);$i++){
			$sql=$this->db->query("REPLACE INTO ".$bid."_gregion_list SET 
				bid='".$bid."',
				gregionid='".$grid."',
				code='".$_POST['multiselect_codes'][$i]."'");
		}
		return true;
	}
	function add_groupregion($rid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$regionid = $this->db->query("SELECT COALESCE(MAX(`gregionid`),0)+1 as id FROM ".$bid."_group_region")->row()->id;
		$this->db->set('gregionid',$regionid);
		$this->db->set('bid',$bid);
		$this->db->set('regionid',$rid);
		$this->db->set('regionname',$this->input->post('regionname'));
		$this->db->insert($bid."_group_region");
		$last_id=$regionid;
		for($i=0;$i<sizeof($_POST['firstSelect']);$i++){
			$sql=$this->db->query("REPLACE INTO ".$bid."_gregion_list SET 
				bid='".$this->session->userdata('bid')."',
				gregionid='".$last_id."',
				code='".$_POST['firstSelect'][$i]."'");
		}
		return true;
	}
	function update_region($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$this->db->set('regionname',$this->input->post('regionname'));
		$this->db->where('regionid',$id);
		$this->db->update($bid."_custom_region");
		return true;
	}
	function del_groupregion($id){
		echo $id;
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query('DELETE FROM '.$bid.'_custom_region WHERE regionid="'.$id.'"');
		return true;
	}
	function get_stdcodes(){
		$opts=array();
		$sql=$this->db->query("select * from series_list")->result_array();
		foreach($sql as $res){
			$opts[$res['scode']]=$res['scode']." ".$res['area'];
		}
		return $opts;
	}
	function GroupRegionsList($rid,$ofset='0',$limit='20'){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$q='';
		if($this->session->userdata('groupregionname')!=""){
			$groupregionname=$this->session->userdata('groupregionname');
			$q.=" and g.regionname like'%".$groupregionname."%'";
		}
		if($this->session->userdata('regionalgosname')!=""){
			$regionalgosname=$this->session->userdata('regionalgosname');
			$q.=" and c.regionname like'%".$regionalgosname."%'";
		}
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS g.gregionid,g.regionname as group_regionname,g.regionid,c.regionname as custom_regionname
						from ".$bid."_group_region g
						left join ".$bid."_custom_region c on c.regionid =g.regionid
						where g.regionid=$rid $q	
						LIMIT $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;
	}
	function get_areacodewise($gid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();
		$sql=$this->db->query("SELECT g.code,s.area FROM ".$bid."_gregion_list g 
							   LEFT JOIN series_list s on s.scode=g.code
							   where g.gregionid='".$gid."'");
		if($sql->num_rows()>0){
		 $res=$sql->result_array();
		}
		return $res;
	}
	function getListCompanies(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array(""=>"select");
		$sql=$this->db->query("SELECT * FROM ".$bid."_company where status=1");
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $re){
					$res[$re['cid']]=$re['companyname'];
			}
		}
		return $res;
	}
	function getChildBusiness(){
		$res=array($this->session->userdata('bid')=>"Self");
	    $sql=$this->db->query("SELECT * FROM business where pid='".$this->session->userdata('bid')."'");
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $re){
					 $res[$re['bid']]=$re['businessname'];
	
			}
		}
		return $res;
	}
	function countOFChild(){
		$sql=$this->db->query("SELECT * FROM business where pid='".$this->session->userdata('bid')."'");
		if($sql->num_rows()>0){
			return 1;
		}else{
			return 0;
		}
	}
	function email_Settings(){
		$bid=$this->session->userdata('bid');
		$this->db->set('fname',$this->input->post('fname'));
		$this->db->set('faddress',$this->input->post('faddress'));
		$this->db->set('eprovider',$this->input->post('eprovider'));
		$this->db->set('smtp',$this->input->post('smtp'));
		$this->db->set('port',$this->input->post('port'));
		$this->db->set('email',$this->input->post('email'));
		$this->db->set('pop',$this->input->post('pop'));
		$this->db->set('password',base64_encode($this->input->post('password')));
		$itemDetail = $this->configmodel->getDetail('27',$bid,'',$bid);
		if(isset($itemDetail['bid'])){
			$this->db->where('bid',$itemDetail['bid']);
			$this->db->update('email_settings');
		}else{
			$this->db->set('bid',$bid);
			$this->db->insert('email_settings');
		}
		return true;
	}
	function getEmailContacts(){
		$bid=$this->session->userdata('bid');
		$sParam = $_REQUEST['q'];
		$query=$this->db->query("SELECT * FROM ".$bid."_contact where email LIKE '%{$sParam}%'");
		 foreach ($query->result_array() as $aValues) {
            echo $aValues['email'] . "\n";
        }
	}
	function updateCustom($id,$modid){
		$this->db->set('customlabel',$this->input->post('label_name'));
		$this->db->where('fieldid',$id);
		$this->db->where('modid',$modid);
		$this->db->update($this->session->userdata('bid')."_custom_label");
		if(isset($_POST['ftype'])){
			$action=$_POST['action'];
			$ots='';
			foreach($_POST['option'] as $opt=>$option){
				$cont=isset($action[$opt])?$action[$opt]:'';
				$ots.=($cont!='')?$option."|".$cont."\n":$option."\n";
			}
			$this->db->set('options',rtrim($ots));
			$this->db->where('fieldid',$id);
			$this->db->where('modid',$modid);
			$this->db->update($this->session->userdata('bid')."_customfields");
			return true;
		}
	}
	
	function getWelcomeFile(){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$res=$this->db->query("SELECT groupname,greetings FROM `".$bid."_groups` WHERE bid = '".$bid."'")->result_array();											
		return $res;
	}
	function getVoicemessageFile(){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$res=$this->db->query("SELECT groupname,voicemessage FROM `".$bid."_groups` WHERE bid = '".$bid."'")->result_array();											
		return $res;
	}
	/* New Code For performance */
	function getAddcustom_access($mod){
		$res = $this->db->query("SELECT add_custom FROM module WHERE modid = '".$mod."'")->row()->add_custom;
		return $res;
	}
	function getFieldKey($fieldid,$bid){
		 $fieldkey = $this->db->query("SELECT field_key FROM ".$bid."_customfields WHERE fieldid='".$fieldid."'")->row()->field_key;
		return $fieldkey;
	}
	//
	function getSupEscBusiness(){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$res=$this->db->query("SELECT supEsc FROM business WHERE bid = '".$bid."'")->row()->supEsc;
		return $res;
	}
	function getAudioFile(){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$res=$this->db->query("SELECT groupname,hdayaudio FROM `".$bid."_groups` WHERE bid = '".$bid."'")->result_array();
		return $res;
	}
	// newly added for call summary download
	function CSGroups(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$q='';$q1 = '';
		if($roleDetail['role']['owngroup']=='1' && $roleDetail['role']['admin']!='1'){
			$q1 .= " LEFT JOIN ".$bid."_group_emp ge ON g.gid = ge.gid ";
			$q .= " AND (ge.eid = '".$this->session->userdata('eid')."' OR g.eid = '".$this->session->userdata('eid')."') ";
		}
		$sql = $this->db->query("SELECT g.gid,g.groupname FROM `".$bid."_groups` g $q1 WHERE 1 $q");
		$res['0']=$this->lang->line('level_select');
		foreach($sql->result_array() as $re)
		$res[$re['gid']]=$re['groupname'];
		return $res;
		
	}
	function getPriList($num='',$mod=''){
		$res=array(''=>"Select",'0'=>'System');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$rst=$this->db->query("SELECT p.status,p.number,p.landingnumber from prinumbers p
								LEFT JOIN landingnumbers l on l.pri=p.pri
								WHERE p.bid='".$bid."' 
								AND p.ntype=0 AND l.module_id='".$mod."'
								")->result_array();
		if(count($rst)>0){
			foreach($rst as $re){
				if($re['status']==0 || $re['number']==$num){
					$res[$re['number']]=$re['landingnumber'];
				}
			}
		}
		return $res;
	}
	
}






/* end */




