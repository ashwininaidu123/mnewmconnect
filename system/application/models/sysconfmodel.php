<?php
class Sysconfmodel extends Model {
	var $data;
    function Sysconfmodel(){
        parent::Model();
        $this->load->model('empmodel');
         $this->load->model('systemmodel');
    }
    function init(){
		if($this->session->userdata('logged_in')) {
			if(!$this->checkAuth()) redirect('/');
			$langname = $this->db->getwhere('language',"langid = '".$this->session->userdata('language')."'")->row()->language;
			$this->config->set_item('language', $langname);
			$this->session->set_userdata('roleid',$this->empmodel->getRoleid($this->session->userdata('eid')));
			$data['roleDetail'] = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
			$data['selfdis'] = $this->getEmpStat($this->session->userdata('eid'));
		}
		$this->load_languages();
		$data['html'] = array(
							'title'=>$this->lang->line('layout_title'),
							'meta'=>array(
									array('name' => 'description', 'content' => 'Call Track'),
									array('name' => 'keywords', 'content' => 'Voice Call, IVRS, Lead, call Forword'),
									array('name' => 'robots', 'content' => 'no-cache'),
									array('name' => 'Content-type', 'content' => 'text/html; charset=utf-8', 'type' => 'equiv')
									),
							'CLogo'=>''//$this->ChangedPartnerLogo()	
						);	
		$data['fcategories'] = $this->getFCats();
		$data['lead_access'] = $this->getfeatureAccess('13');
		$data['support_access'] = $this->getfeatureAccess('14');
		$data['interview_access'] = $this->getfeatureAccess('15');
		$data['outboundcalls_access'] = $this->getfeatureAccess('16');
		$data['mconnect_access'] = $this->getfeatureAccess('17');
		if($this->session->userdata('logged_in')){
			$data['leadstatus'] = $this->get_leadstatus();
			$data['leadView'] = $this->leadtypeCheck();
			$data['supstatus'] = $this->getSupTktStatus();
			$data['feature'] = $this->feature_access();
		}
		return $data;
	}
	
	function viewLayout($view = '',$data = ''){
		$this->load->view('mainheader',$this->data);
		$this->load->view('leftsidebar');
		$this->load->view($view,$data);
		$this->load->view('footer');
	}
	function viewLayout1($view = '',$data = ''){
		$this->load->view('counter_view1',$this->data);
		$this->load->view($view,$data);
	}
	function viewlayout2($view='',$data='')
	{
		$this->load->view($view,$data);
	}
	function load_languages(){
		$default_dir = $this->config->item('lang_path').$this->config->item('language')."/";
		if(!($dp = opendir($default_dir))) die("Cannot open $default_dir.");
		while($file = readdir($dp)){
			if(is_dir($file)){
				continue;
			}else if($file != '.' && $file != '..' && $file!="index.html"){
				$files=explode("_lang",$file);
					if(file_exists($default_dir)){
					$this->lang->load($files[0]);
				}
			}
		}
		closedir($dp);
	}
	function ChangedPartnerLogo(){
		$host=$_SERVER['HTTP_HOST'];
		$s=$this->db->query("SELECT * FROM partner where domain_name='$host' AND status=1");
		$res=$s->result_array();
		return $res[0]['logo'];
	}
	function checkDomain(){
		$host=$_SERVER['HTTP_HOST'];
		$s=$this->db->query("SELECT * FROM partner where domain_name='$host' AND status=1");
		if($s->num_rows()>0){
			return true;
		}else{
			return false;
		}
	}
	function checkAuth(){
		$sql = "SELECT u.uid FROM user u
				LEFT JOIN ".$this->session->userdata('bid')."_employee e on (e.bid=u.bid AND e.eid=u.eid)
				WHERE e.status='1' AND e.login='1' AND u.status='1' 
				AND u.bid='".$this->session->userdata('bid')."' AND u.eid='".$this->session->userdata('eid')."'";
		$rst = $this->db->query($sql);
		if($rst->num_rows()>0){
			return true;
		}else{
			$this->simplelogin->logout();
			redirect('/');
			return true;
		}
	}
	function get_baddons_number($lno){
		$sql=$this->db->query("SELECT * FROM  business_packageaddons WHERE number='".$lno."'");
		if($sql->num_rows()>0){
			return $sql->result_array();
		}else{
			return array();
		}
	}
	function get_baddons_number1($lno,$modid){
		$sql=$this->db->query("SELECT s.fieldname
							FROM systemfields s
							LEFT JOIN `features` f on s.addon=f.feature_id
							LEFT JOIN business_packageaddons b on (f.feature_id=b.feature_id AND b.number='".$lno."')
							WHERE s.modid='".$modid."' AND s.addon!=0 AND b.number is null");
		if($sql->num_rows()>0){
			return $sql->result_array();
		}else{
			return array();
		}
	}
	function getFields_addons($modid,$bid=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql = "SELECT * FROM ( 
				SELECT 's' as type,f.addon,f.fieldid,f.modid,
				f.fieldname,'' as fieldtype,'' as options,
				'' as defaultvalue,f.is_required,f.is_hidden,COALESCE(l.display_order,0) as display_order,
				l.customlabel,COALESCE(l.show,1) as `show`,COALESCE(l.listing,1) as `listing`
				FROM systemfields f
				LEFT JOIN (
					SELECT * FROM ".$bid."_custom_label
					WHERE fieldtype='s'
				) as l
				on (f.fieldid=l.fieldid AND l.modid=f.modid)
				WHERE f.modid='".$modid."'  AND f.is_hidden=0 AND f.addon!=0
				) as t ORDER BY display_order ASC";
		return $this->db->query($sql)->result_array();
	}
	function getFCats(){
		$res=array();
		$res = $this->db->query("SELECT id,category,subcategory,type,status FROM feedback WHERE status=1 ")->result_array();
		return $res;
	}
	function getfeatureAccess($fetId = ''){
		$fetId = ($fetId == '') ? '13' :$fetId;
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$resArr = array();
		$res = $this->db->query("SELECT feature_id FROM business_feature WHERE bid='".$bid."'")->result_array();
		foreach($res as $item){
			array_push($resArr,$item['feature_id']);
		}
		if(@in_array($fetId,$resArr))
			return '1';
		else
			return '0';
	}
	function getEmpStat($eid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("SELECT a.selfdisable FROM ".$bid."_employee a WHERE a.eid='".$eid."'");
		if($sql->num_rows()>0){
			$res = $sql->row();
			return $res->selfdisable;
		}else{
			return '';
		}
	}
	function get_leadstatus(){
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
	function leadtypeCheck(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$lechk = $this->db->query("SELECT design FROM business_lead_use WHERE bid='".$bid."' LIMIT 0,1");
		if($lechk->num_rows()>0){
			$res = $lechk->result_array();
			return $res[0]['design'];
		}
	}
	function getSupTktStatus(){
		$result = array();
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->db->query("SELECT sid,status FROM ".$bid."_support_status");
		if($res->num_rows()>0){
			foreach($res->result_array() as $e)
			$result[$e['sid']]=$e['status'];
		}
		return $result;
	}
	function feature_access(){
		$show=0;
		$data1=array();
		$checklist=$this->systemmodel->checked_featuremanage();
		if(in_array(1,$checklist))	$data1['call']='1';
		if(in_array(2,$checklist))	$data1['ivrs']='1';
		if(in_array(8,$checklist))	$data1['pbx']='1';
		if(in_array(13,$checklist))	$data1['lead']='1';
		if(in_array(14,$checklist))	$data1['support']='1';
		
		return $data1;
	}
}
/* end of model*/



