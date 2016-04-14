<?php
Class R extends Model{
	
	function R(){
		parent::Model();
	}
	function getqrcodedetails($id){
		if($id!=""){
		$sql=$this->db->query("select * from qr where qrid=".$id);
		return $sql->row();
		}else{
		return "";	
		}
	}
	function getqrdealinfo($qrid,$bid){
		$res=array();
		if($qrid!=""){
			$sql=$this->db->query("select * from qrdeals where qrid=".$qrid." and bid=".$bid);
			return $sql->row();
		}else{
			return $res;	
			}	
	}
	function save_lead(){
		$did=$this->db->query("SELECT COALESCE(MAX(`leadid`),0)+1 as id FROM leadgeneration")->row()->id;
		$this->db->set('leadid',$did);
		$this->db->set('bid',$this->input->post('bid'));
		$this->db->set('scanid',$this->input->post('scanid'));
		$this->db->set('qrid',$this->input->post('qrid'));
		$this->db->set('gid',$this->input->post('gid'));
		$this->db->set('name',$this->input->post('name'));
		$this->db->set('mobile',$this->input->post('mobile'));
		$this->db->set('email',$this->input->post('email'));
		$this->db->set('query',$this->input->post('query'));
		$this->db->insert("leadgeneration");	
		if(isset($_POST['custom'])){
					$arrs=array_keys($_POST['custom']);
					for($k=0;$k<sizeof($arrs);$k++){
						if(sizeof($_POST['custom'][$arrs[$k]])>1){
								$x=implode(",",$_POST['custom'][$arrs[$k]]);
							}
							else{
								$x=$_POST['custom'][$arrs[$k]];
							}
							$this->db->set('bid',$this->input->post('bid'));
							$this->db->set('modid ',18);
							$this->db->set('fieldid',$arrs[$k]);
							$this->db->set('dataid',$did);
							$this->db->set('value ',$x);
							$this->db->insert($this->input->post('bid').'_customfieldsvalue');
						}
					} 
		return $did;	
	}
	function save_call($post){
		$rid = $this->db->query("SELECT COALESCE(MAX(`rid`),0)+1 as id FROM call_request")->row()->id;
		$sql = "INSERT INTO call_request SET
				rid			= '".$rid."'
				,bid		= '".$post['bid']."'
				,gid		= '".$post['gid']."'
				,number		= '".$post['number']."'
				,source		= 'qrtrack'
				,sourceid	= '".$post['scanid']."'
				,datetime	= '".(isset($post['callnow'])?date('Y-m-d H:i:s'):date('Y-m-d H:i:s',strtotime($post['datetime'])))."'";
		$this->db->query($sql);
		return;
	}
	function addReport($info){
		$scanid = $this->db->query("SELECT COALESCE(MAX(`scanid`),0)+1 as id FROM scanreport")->row()->id;
		$sql = "INSERT INTO scanreport SET
				scanid		= '".$scanid."'
				,qrid		= '".$info['qrid']."'
				,bid		= '".$info['bid']."'
				,gid		= '".$info['bid']."'
				,browser	= '".$info['browser']."'
				,location	= '".$_SERVER['REMOTE_ADDR']."'
				,platform	= '".$info['platform']."'";
		$this->db->query($sql);
		return $this->db->insert_id();
	}
	function send_deal(){
		//Send deal by SMS or Email to user
	}
}
/* end */
