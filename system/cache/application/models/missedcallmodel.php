<?php
class missedcallmodel extends Model {
	var $data;
    function missedcallmodel(){
        parent::Model();
        $this->load->model('auditlog');
        $this->load->model('emailmodel');
        $this->load->model('commonmodel');
        $this->load->model('ivrsmodel');
        $this->load->model('pollmodel');
         
         
    }
    function init(){
		if(!$this->checkDomain()){
			redirect('/sitenotavailable');
		}
		if($this->session->userdata('logged_in')) {
			$langname = $this->db->getwhere('language',"langid = '".$this->session->userdata('language')."'")->row()->language;
			$this->config->set_item('language', $langname);//echo $langname;
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
							'links'=>array(
								'system/application/css/theme5.css',
								'system/application/css/style.css',
								'system/application/css/style1.css',
								'system/application/css/ddsmoothmenu.css',
								'system/application/css/jquery.ui.datepicker.css',
								'system/application/css/paging.css',
								'system/application/css/jquery.ui.all.css'
							),
							'scripts'=>array(
									'system/application/js/jquery-1.5.2.js',
									'system/application/js/ddsmoothmenu.js',
									'system/application/js/ui/jquery-ui-1.8.9.custom.js',
									'system/application/js/ui/jquery.ui.slider.js',
									'system/application/js/ui/jquery.effects.core.js',
									'system/application/js/ui/jquery.effects.blind.js',
									'system/application/js/ui/jquery.blockUI.js',
									'system/application/js/ui/jquery.ui.datepicker.js',
									'system/application/js/ui/jquery.ui.widget.js',
									'system/application/js/ui/jquery.ui.core.js',
									'system/application/js/ui/jquery-ui-timepicker-addon.js',
									'system/application/js/jquery.bt.js',
									'system/application/js/jquery.validate.js',
									'system/application/js/jquery.tablesorter.js',
									'system/application/js/jquery.easy-confirm-dialog.js',
									'system/application/js/jquery.custom.js',
								),
								'CLogo'=>''	
						);		
		return $data;
	}
	function checkDomain(){
		$host=$_SERVER['HTTP_HOST'];
		$s=$this->db->query("SELECT * FROM master_admin where domain_name='$host'");
		if($s->num_rows()>0){
			return true;
		}else{
			return false;
		}
	}
	function missedcallDetails($id){
		$res=array();
		if($id!=""){
			$s=$this->db->query("select * from ".$this->session->userdata('bid')."_missedgroup where mid=".$id);
			if($s->num_rows()>0){
				return $s->row();
			}else{
				return $res;
			}
		}else{
				return $res;
		}
		
	}
	function addMissedcallgroup($id){
		if($id!=""){
			if($this->input->post('old_pri')!=$this->input->post('pri')){
				$this->pollmodel->updatePri($this->input->post('old_pri'),0,$this->session->userdata('bid'),0,0);
				$this->pollmodel->updatePri($this->input->post('pri'),1,$this->session->userdata('bid'),5,$id);
				$this->db->set('prinumber',$this->input->post('pri'));
				$this->db->set('keyword',$this->input->post('keyword'));
				$this->db->set('eid',$this->input->post('eid'));
				$this->db->set('smstoclient',$this->input->post('csms'));
				$this->db->set('smstobusiness',$this->input->post('esms'));
				$this->db->where('mid',$id);
				$this->db->update($this->session->userdata('bid')."_missedgroup");
				}
			else{
				$this->db->set('keyword',$this->input->post('keyword'));
				$this->db->set('eid',$this->input->post('eid'));
				$this->db->set('smstoclient',$this->input->post('csms'));
				$this->db->set('smstobusiness',$this->input->post('esms'));
				$this->db->where('mid',$id);
				$this->db->update($this->session->userdata('bid')."_missedgroup");
			}
		}else{
			$poll_id=$this->db->query("SELECT COALESCE(MAX(`mid`),0)+1 as id FROM ".$this->session->userdata('bid')."_missedgroup")->row()->id;
			$this->db->set('mid',$poll_id);
			$this->db->set('prinumber',$this->input->post('pri'));
			$this->db->set('keyword',$this->input->post('keyword'));
			$this->db->set('eid',$this->input->post('eid'));
			$this->db->set('smstoclient',$this->input->post('csms'));
			$this->db->set('smstobusiness',$this->input->post('esms'));
			$this->db->set('status','1');
			$this->db->insert($this->session->userdata('bid')."_missedgroup");
			$this->pollmodel->updatePri($this->input->post('pri'),1,$this->session->userdata('bid'),5,$poll_id);
			return $poll_id;
			
		}
	}
	function listcalls($ofset='0',$limit='20'){
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM ".$this->session->userdata('bid')."_missedgroup LIMIT $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;
	}
	function CHangeStatus($id){
		$poll=$this->missedcallDetails($id);
		$status=($poll->status==0)?'1':'0';
		$this->db->set('status',$status);
		$this->db->where('mid',$id);
		$this->db->update($this->session->userdata('bid')."_missedgroup");
		return $status;
	}
	function getcallReports($id,$ofset='0',$limit='20'){
			$q='where mid='.$id;
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['starttime']) && $s['starttime']!='')?" and datetime>='".$s['starttime']."'":"";
		$q.=(isset($s['endtime']) && $s['endtime']!='')?" and datetime<='".$s['endtime']."'":"";
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM ".$this->session->userdata('bid')."_missedcallreport $q LIMIT $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;
		
	}	
}	
	
	
	/* end of poll model */
