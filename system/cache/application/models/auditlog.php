<?php
class Auditlog extends model
{
	function Auditlog(){
		 parent::Model();
	}
	function auditlog_info($module,$action){
		$sno=$this->db->query("SELECT COALESCE(MAX(`sno`),0)+1 as id FROM ".$this->session->userdata('bid')."_activitylog")->row()->id;
		$this->db->set('sno', $sno); 
		$this->db->set('bid', $this->session->userdata('bid')); 
		$this->db->set('uid', $this->session->userdata('uid')); 
		$this->db->set('module_name',$module); 
		$this->db->set('action',$action);
		$this->db->insert($this->session->userdata('bid').'_activitylog');  
	}
	function get_autdit_log($ofset='0',$limit='20'){
		$q='';
		if($this->session->userdata('uname')!=""){
			$uname=$this->session->userdata('uname');
			$q.=" WHERE u.username LIKE '%".$uname."%'";
		}
		if($this->session->userdata('module')!=""){
			$module=$this->session->userdata('module');
			if($q!=""){
				$q.=" AND a.module_name LIKE '%".$module."%'";
			}else{
				$q.=" WHERE a.module_name LIKE '%".$module."%'";
			}
		}
		if($this->session->userdata('datefrom')!=""){
			$datefrom=$this->session->userdata('datefrom');
				if($q!=""){
					$q.=" AND date(a.date_time)>='$datefrom'";
				}else{
					$q.=" WHERE date(a.date_time)>='$datefrom'";
				}
			}
		if($this->session->userdata('dateto')!=""){
			$dateto=$this->session->userdata('dateto');
			if($q!=""){
				$q.=" AND date(a.date_time)<='$dateto'";
			}else{
				$q.=" WHERE date(a.date_time)<='$dateto'";
			}
		}
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS a.sno,a.module_name,a.action,a.date_time,u.username FROM ".$this->session->userdata('bid')."_activitylog a LEFT JOIN user u on a.uid=u.uid $q ORDER BY a.sno desc LIMIT $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;
	}
	function get_audit_count(){
		$sql=$this->db->query("SELECT count(*) as counts FROM ".$this->session->userdata('bid')."_activitylog");
		$res=$sql->row();
		return $res->counts;
	}
	function auditDownload($bid){
		$res=array();
		$q = '';
		if($_POST['endtimes']!=""){
			$q.=" AND date(a.date_time)<='".$_POST['endtimes']."'" ;
		}
		if($_POST['starttimes']!=""){
			$q.=" AND date(a.date_time)>='".$_POST['starttimes']."'";
		}
		$csv_output = "";
		$ke=array();
		foreach($_POST['lisiting'] as $key=>$val){
			$hkey[]=$key;
			$header[]=$val;
		}
		$csv_output .=implode(",",$header)."\n";
		$sql="SELECT SQL_CALC_FOUND_ROWS a.sno,a.module_name as module,a.action,a.date_time as datetime,u.username 
			  FROM ".$bid."_activitylog a 
			  LEFT JOIN user u on a.uid=u.uid 
			  WHERE 1 $q 
			  ORDER BY a.sno DESC"; 
		$rst = $this->db->query($sql)->result_array();
		$total_record_count = $this->db->query($sql)->num_rows();
		$name = $bid.'_'.
				$this->session->userdata('eid').'_'.
				time();
		mkdir('reports/'.$name);
		chmod('reports/'.$name,0777);
		$files = array();
		$data_file = 'reports/'.$name.'/auditlog.csv';
		$fp = fopen($data_file,'w');
		fwrite($fp,$csv_output);
		foreach($rst as $rec){
			$data = array();
			$i=0;
			foreach($hkey as $k){
				$v=(isset($rec[$k])) ? '"'.str_replace("\n"," ",$rec[$k]).'"' : '';
				array_push($data,$v);
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
}
/* end of audit log */
