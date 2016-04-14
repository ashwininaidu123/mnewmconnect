<?php
class Profilemodel extends model
{
	function Profilemodel(){
		 parent::Model();
		
	}
	function get_profiledetails(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("select * from business where bid=".$bid);
		if($sql->num_rows()>0)
		{
			$res=$sql->result_array();
		}
		return $res;
	}
	function get_languages(){
		$res=array();
		$sql=$this->db->query("select * from language");
		if($sql->num_rows()>0)
		{
			$ret=$sql->result_array();
			$res['']=$this->lang->line('level_select');
			foreach($ret as $rec)
				$res[$rec['langid']] = $rec['language'];

		}	
		return $res;

	}
	function update_profile(){
		$arr=array_keys($_POST);
		for($i=0;$i<sizeof($arr);$i++)
		{
			if($arr[$i]!="update_system"){
				if(!in_array($arr[$i],array("custom","businessname","businessemail","contactemail"))){
						$this->db->set($arr[$i], $_POST[$arr[$i]]); 
					}
			}
		}
		$otp=isset($_POST['otp'])?'1':'0';
		$this->db->set('otp',$otp);
		$this->db->where('bid',$this->session->userdata('bid'));
		$this->db->update('business');
		//echo $this->db->last_query();exit;
		$this->auditlog->auditlog_info('Business Profile','Business Profile Updated');
		if(isset($_POST['custom'])){
			foreach($_POST['custom'] as $fid=>$val){
				$this->db->query("DELETE FROM ".$this->session->userdata('bid')."_customfieldsvalue 
								  where bid= '".$this->session->userdata('bid')."' and modid= '1' 
								  and fieldid = '".$fid."' 
								  and dataid= '".$this->session->userdata('bid')."'");
				$sql = "REPLACE INTO ".$this->session->userdata('bid')."_customfieldsvalue SET
					 bid			= '".$this->session->userdata('bid')."'
					,modid			= '1'
					,fieldid		= '".$fid."'
					,dataid			= '".$this->session->userdata('bid')."'
					,value			= '".(is_array($val)?implode(',',$val):$val)."'";
				$this->db->query($sql);
			}
			
		}
		$this->db->set('empname', $this->input->post('contactname')); 
		$this->db->set('empnumber', $this->input->post('contactphone')); 
		$this->db->where('eid','1');
		$this->db->update($this->session->userdata('bid')."_employee");
		$this->auditlog->auditlog_info('Business Info',"Admin Profile Details  updated");
		return 1;
	}
	function update_business($bid)
	{
		$this->db->set('businessaddress1', $this->input->post('businessaddress1')); 
		$this->db->set('locality', $this->input->post('locality')); 
		$this->db->set('country', $this->input->post('country')); 
		$this->db->set('state', $this->input->post('state')); 
		$this->db->set('city', $this->input->post('city')); 
		$this->db->set('zipcode', $this->input->post('postalcode'));
		//$this->db->set('businessname', $this->input->post('businessname')); 
		$this->db->set('contactname', $this->input->post('contactname')); 
		//$this->db->set('contactemail', $this->input->post('contactemail')); 	
		$this->db->set('contactphone', $this->input->post('contactphone')); 
		//$this->db->set('businessemail', $this->input->post('businessemail')); 
		$this->db->set('businessphone', $this->input->post('businessphone')); 
		$this->db->set('businessaddress', $this->input->post('businessaddress')); 
		$this->db->set('language', $this->input->post('language')); 
		$this->db->where('bid',$bid);
		$this->db->update('business');
		$this->auditlog->auditlog_info($this->lang->line('level_module_Profile'),"Updated Business Profile");

		$size=sizeof($_POST['c']);
		$arr=array_keys($_POST['c']);
		$inmp='';
		for($i=0;$i<sizeof($arr);$i++)
		{
				if(sizeof($_POST['c'][$arr[$i]])>1){
				$inmp=implode(",",$_POST['c'][$arr[$i]]);
				}else{
				$inmp=$_POST['c'][$arr[$i]];	
				}
			$this->db->set('defaultvalue', $inmp); 
			$this->db->where('fieldid',$arr[$i]);
			$this->db->update($this->session->userdata('bid')."_metafields");
			
			$check_field_id=$this->check_field_exits($arr[$i]);
			if($check_field_id!=1)
			{
					$this->db->set('fieldid', $arr[$i]); 
					$this->db->set('bid', $this->session->userdata('bid')); 
					$this->db->set('typeid', $bid); 
					$this->db->set('value', $inmp); 
					$this->db->insert($this->session->userdata('bid').'_metafieldsvalue'); 
				
			}
			else{
				$this->db->set('value', $inmp); 
				$this->db->where('fieldid',$arr[$i]);
				$this->db->update($this->session->userdata('bid')."_metafieldsvalue");
				
			}
		}
		return 1;
	}
	function check_field_exits($id)
	{
		$sql=$this->db->query("select * from ".$this->session->userdata('bid')."_metafieldsvalue where fieldid=$id");
		if($sql->num_rows()>0)
		{
				return 1;
		}
		else{
				return 0;
		}
		
	}
	function get_groups(){
		$res=array();	
		$sql=$this->db->query("select * from ".$this->session->userdata('bid')."_groups where status=1");
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $r)	{
				$res[$r['gid']]=$r['groupname'];
			}
		}
		return $res;
	}
	function get_employee_login(){
		$res=array();
		$sql=$this->db->query("select * from ".$this->session->userdata('bid')."_employee where login=0 and status=1");
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $r){
				$res[$r['eid']]=$r['empname'];
			}
		}
		return $res;
	}
	function changePassword(){
		$ret = false;
		$sql = "SELECT * FROM user WHERE bid='".$this->session->userdata('bid')."'
				AND uid='".$this->session->userdata('uid')."'
				AND password = '".md5($_POST['oldpass'])."'";
		$rst = $this->db->query($sql);
		
		//$rst->num_rows;exit;
		if($rst->num_rows>0){
			
			$sql=$this->db->query("UPDATE user set password='".md5($this->input->post('newpass'))."' where bid='".$this->session->userdata('bid')."' and uid='".$this->session->userdata('uid')."'");
			$emp_name=$this->db->query("select empname from ".$this->session->userdata('bid')."_employee where eid=".$this->session->userdata('eid'))->row()->empname;
			$this->auditlog->auditlog_info('Change Password',$emp_name." has changed password");
				
			
			
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg', $this->lang->line('label_chengepass'));
			$ret = true;
		}else{
			$this->session->set_flashdata('msgt', 'error');
			$this->session->set_flashdata('msg', $this->lang->line('label_chengepassfail'));		
		}
		return $ret;
	}
	function getParentBusiness(){
		$res=array();
		$sql=$this->db->query("SELECT * FROM business where pid=0");
		if($sql->num_rows()>0){
			$res['']="select";
			foreach($sql->result_array() as $ar){
				$res[$ar['bid']]=$ar['businessname'];
			}
		}
		return $res;
	}
	function getBusinesslist($ofset='0',$limit='20'){
		$q='where b.pid="'.$this->session->userdata('bid').'"';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['businessname']) && $s['businessname']!='')?" and b.businessname like '%".$s['businessname']."%'":"";
		$q.=(isset($s['bemail']) && $s['bemail']!='')?" and b.contactemail like '%".$s['bemail']."%'":"";
		$q.=(isset($s['phnumber']) && $s['phnumber']!='')?" and b.contactphone like '%".$s['phnumber']."%'":"";
		$q.=(isset($s['cname']) && $s['cname']!='')?" and b.contactname like '%".$s['cname']."%'":"";
		$q.=(isset($s['city']) && $s['city']!='')?" and b.city like '%".$s['city']."%'":"";
		$q.=(isset($s['state']) && $s['state']!='')?" and b.state like '%".$s['state']."%'":"";
		$q.=(isset($s['btype']) && $s['btype']!='')?" and b.act like '%".$s['btype']."%'":"";
		
		
		$res=array();
		$res['data']=$this->db->query("select SQL_CALC_FOUND_ROWS p.companyname,b.* from business b
										left join partner p on p.partner_id=b.domain_id $q LIMIT $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;
	}
	Function get_Partnerval($id=''){
		if($id!=""){
			$sql=$this->db->query("SELECT * FROM partner WHERE partner_id=".$id);
			return $sql->row();
			
		}else{
			return array();
		}
	}
	function get_salesEmp($emp){
		if($emp!=""){
			$s=$this->db->query("SELECT * FROM salesemp WHERE id=".$emp)->row();
			return $s;
		}else{
			return array();
		}
	}
	function check_apisecret($key){
		$res=array();
		$sql=$this->db->query("SELECT * FROM business where apisecret='".$key."'");
		if($sql->num_rows>0)
		{
			return false;
		}
		return true;
	}
}
/* end of profile model */
