<?php
Class Configuremodel extends Model
{
	function Configuremodel()
	{
		 parent::Model();
		$this->load->model('ivrsmodel');
		$this->load->model('empmodel');
	}
	function demoPris(){
		$res=$this->db->query("SELECT landingnumber,bid,type,number from prinumbers where status=0 and bid='".$this->session->userdata('bid')."'" );	
		if($res->num_rows()>0){
			return $res->result_array();
		}else{
			return array();
		}
	}
	function Configuration(){
		$owner='';
		$emp = array();
		$i=0;
		foreach($_POST['emp'] as $key => $rs){
			$i++;
			if($i>1){
				if($rs['empemail']!="" && $this->empmodel->email_employee($rs['empemail'])){
					$eid=$this->db->query("SELECT COALESCE(MAX(`eid`),0)+1 as id FROM ".$this->session->userdata('bid')."_employee")->row()->id;
					$this->db->set('eid',$eid);
					$this->db->set('empname',$rs['empname']);
					$this->db->set('bid',$this->session->userdata('bid'));
					$this->db->set('empemail',$rs['empemail']);
					$this->db->set('empnumber',$rs['number']);
					$this->db->set('status','1');
					$this->db->set('roleid','1');
					$this->db->set('login','0');
					$this->db->insert($this->session->userdata('bid')."_employee");
					if($key==$_POST['owner'])$owner=$eid;
					$emp[] = array('eid'=>$eid,'ext'=>$rs['ext']);
				}	
			}else{
				if($key==$_POST['owner'])$owner=$this->input->post('eid');
				$emp[] = array('eid'=>$this->input->post('eid'),'ext'=>$rs['ext']);
			}
		}
			if(sizeof($emp)>1){
				$newName='';
				if($_FILES['greetings']['error']==0){
					$ext=pathinfo($_FILES['greetings']['name'],PATHINFO_EXTENSION);
					$newName = "G".date('YmdHis').".".$ext;
					move_uploaded_file($_FILES['greetings']['tmp_name'],$this->config->item('sound_path').$newName);
					$this->db->set('greetings',$newName);
				}
				$gid=$this->db->query("SELECT COALESCE(MAX(`gid`),0)+1 as id FROM ".$this->session->userdata('bid')."_groups")->row()->id;
				$this->db->set('gid',$gid);
				$this->db->set('bid',$this->session->userdata('bid'));
				$this->db->set('eid',$owner);
				$this->db->set('keyword','');
				$this->db->set('groupname',$this->input->post('groupname'));
				$this->db->set('primary_rule','0');
				$this->db->set('url','');
				$this->db->set('prinumber',$this->input->post('gn'));
				$this->db->set('record',1);
				$this->db->set('recordnotice',1);
				$this->db->set('rules','1');
				$this->db->set('bday','{"Mon":{"day":"1","st":"00:00","et":"23:59"},"Tue":{"day":"1","st":"00:00","et":"23:59"},"Wed":{"day":"1","st":"00:00","et":"23:59"},"Thu":{"day":"1","st":"00:00","et":"23:59"},"Fri":{"day":"1","st":"00:00","et":"23:59"},"Sat":{"day":"1","st":"00:00","et":"23:59"},"Sun":{"day":"1","st":"00:00","et":"23:59"}}');
				$this->db->set('hdaytext ','');
				$this->db->set('hdayaudio',''); 	
				$this->db->set('replymessage ',1);	
				$this->db->set('replytocustomer ',1);
				$this->db->set('replytoexecutive',1);
				$this->db->set('sameexe ',1);
				$this->db->set('misscall','');
				$this->db->set('status',1);
				$this->db->insert($this->session->userdata('bid')."_groups");
				$this->db->query("UPDATE prinumbers SET status='1',associateid='".$gid."' WHERE number='".$this->input->post('gn')."'");

				$pbxid=$this->db->query("SELECT COALESCE(MAX(`pbxid`),0)+1 as id FROM ".$this->session->userdata('bid')."_pbx")->row()->id;
				$this->db->set('pbxid',$pbxid);
				$this->db->set('operator',$owner);
				$this->db->set('bid',$this->session->userdata('bid'));
				$this->db->set('greetings',$newName);
				$this->db->set('bday','{"Mon":{"day":"1","st":"00:00","et":"23:59"},"Tue":{"day":"1","st":"00:00","et":"23:59"},"Wed":{"day":"1","st":"00:00","et":"23:59"},"Thu":{"day":"1","st":"00:00","et":"23:59"},"Fri":{"day":"1","st":"00:00","et":"23:59"},"Sat":{"day":"1","st":"00:00","et":"23:59"},"Sun":{"day":"1","st":"00:00","et":"23:59"}}');
				$this->db->set('hdaytext ','');
				$this->db->set('hdayaudio',''); 
				$this->db->set('operator',$owner);
				$this->db->set('prinumber',$this->input->post('pn'));
				$this->db->set('title',$this->input->post('pbxname'));
				$this->db->set('record',1);
				$this->db->set('status',1);
				$this->db->set('noext',0);
				$this->db->insert($this->session->userdata('bid')."_pbx");
				$this->db->query("UPDATE prinumbers SET status='1',associateid='".$pbxid."' WHERE number='".$this->input->post('pn')."'");
				foreach($emp as $e){
					$sql = "INSERT INTO ".$this->session->userdata('bid')."_group_emp SET
							bid		='".$this->session->userdata('bid')."',
							gid		='".$gid."',
							eid		='".$e['eid']."',
							status	='1'";
					
					$this->db->query($sql);
					
					$extid=$this->db->query("SELECT COALESCE(MAX(`extid`),0)+1 as id FROM ".$this->session->userdata('bid')."_pbxext")->row()->id;
					if($e['ext']!=""){
						$sql2 = "INSERT INTO ".$this->session->userdata('bid')."_pbxext SET
							extid	='".$extid."',
							bid		='".$this->session->userdata('bid')."',
							pbxid	='".$pbxid."',
							ext		='".$e['ext']."',
							targettype='employee',
							targetid='".$e['eid']."'";
						$this->db->query($sql2);		
					}
					
				}
				return true;
		}else{
			return false;
		}
	}
	
	function isConfig(){
		$cnt = $this->db->query("SELECT count(*) as cnt FROM prinumbers WHERE bid='".$this->session->userdata('bid')."' AND status='0'")->row()->cnt;
		
		return ($cnt>0)?true:false;
	}
	
}	
