<?php
Class Companymodel extends Model
{
	function Companymodel(){
		parent::Model();
		$this->load->model('ivrsmodel');
		$this->load->model('auditlog');
		$this->load->model('configmodel');
		$this->load->model('empmodel');
		$this->load->model('keywordmodel');
		$this->load->model('pollmodel');
	}
	function addCompany($cid){
		$this->db->set('companyname',$this->input->post('companyname'));
		$this->db->set('owner',$this->input->post('owner'));
		$this->db->set('bid',$this->session->userdata('bid'));
		$this->db->set('status','1');
		if($cid!=''){
			$this->db->where('cid',$cid);
			$this->db->update($this->session->userdata('bid')."_company");
			$this->auditlog->auditlog_info('Company Module',"Updated Company Information ".$this->input->post('companyname'));
		}else{
			$cid=$this->db->query("SELECT COALESCE(MAX(`cid`),0)+1 as id FROM ".$this->session->userdata('bid')."_company")->row()->id;
			$this->db->set('cid',$cid);
			$this->db->insert($this->session->userdata('bid')."_company");
			$this->auditlog->auditlog_info('Company Module',"Created New Company  ".$this->input->post('companyname'));
		}
		if(isset($_POST['custom'])){
			$arrs=array_keys($_POST['custom']);
			for($k=0;$k<sizeof($arrs);$k++){
				if(sizeof($_POST['custom'][$arrs[$k]])>1){
						$x=implode(",",$_POST['custom'][$arrs[$k]]);
					}
					else{
						$x=$_POST['custom'][$arrs[$k]];
					}
					$sql = "REPLACE INTO ".$this->session->userdata('bid')."_customfieldsvalue SET
						 bid			= '".$this->session->userdata('bid')."'
						,modid			= '26'
						,fieldid		= '".$arrs[$k]."'
						,dataid			= '".$cid."'
						,value			= '".$x."'";
					$this->db->query($sql);
					
					
				}
			}
		}
	function ListCompany($ofset='0',$limit='20'){
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS c.*,e.empname from ".$this->session->userdata('bid')."_company c left join ".$this->session->userdata('bid')."_employee e on c.owner=e.eid LIMIT $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;	
	}		
	function stat_operations($cid){
		$itemDetail = $this->configmodel->getDetail('26',$cid);
		$sql=$this->db->query("SELECT * FROM ".$this->session->userdata('bid')."_groups where cid='".$cid."'");
		if($itemDetail['status']!=0){
			$status=0;
		}else{
			$status=1;
		}
		$this->db->set('status',$status);
		$this->db->where('cid',$cid);
		$this->db->update($this->session->userdata('bid')."_company");
		return true;
	}
}
/* end of Company model */
