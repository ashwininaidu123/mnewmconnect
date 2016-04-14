<?php
class Executivemodel extends Model {
	var $data;
    function Executivemodel(){
        parent::Model();
        $this->load->model('auditlog');
        $this->load->model('emailmodel');
        $this->load->model('commonmodel');
         $this->load->plugin('to_pdf');
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
								'system/application/css/jquery.ui.autocomplete.css',
								'system/application/css/style.css',
								'system/application/css/style1.css',
								'system/application/css/ddsmoothmenu.css',
								'system/application/css/jquery.ui.datepicker.css',
								'system/application/css/jquery.ui.all.css',
								'system/application/css/jquery.multiselect2side.css',
								'system/application/css/paging.css',
								'system/application/css/jquery.alerts.css',
								
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
									'system/application/js/ui/jquery.ui.autocomplete.js',
									'system/application/js/jquery.bt.js',
									'system/application/js/jquery.validate.js',
									'system/application/js/jquery.tablesorter.js',
									'system/application/js/jquery.easy-confirm-dialog.js',
									'system/application/js/jquery.multiselect2side.js',
									'system/application/js/jquery.alerts.js',
									'system/application/js/jquery.custom.js',
									),
								'CLogo'=>''	
						);		
		return $data;
	}
	function viewLayout($view = '',$data = ''){
			$this->load->view('exheader',$this->data);
			$this->load->view($view,$data);
			$this->load->view('sidebar');
			$this->load->view('footer');
	}
	function load_languages(){
		$default_dir = $this->config->item('lang_path').$this->config->item('language')."/";
		if(!($dp = opendir($default_dir))) die("Cannot open $default_dir.");
		while($file = readdir($dp))
		{
			if(is_dir($file))
			{
			continue;
			}
			else if($file != '.' && $file != '..' && $file!="index.html")
			{
				$files=explode("_lang",$file);
					if(file_exists($default_dir)){//echo $files[0]."<br>";
					$this->lang->load($files[0]);
				}
			
			}
		}
		closedir($dp);
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
	function addnumbers(){
		if (($handle = fopen($_FILES['cfile']['tmp_name'],"r")) !== FALSE) {
			$i=0;
			$err=0;
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				
				if($i>0){
					
					$c = 0;
					$h=(isset($data['1']))?$data['1']:'';
					$sql=$this->db->query("SELECT number from vmc_numbers where number='".$h."'");
						if($sql->num_rows()==0){
								$number_type = array(""=>"Select","1"=>"Normal","2"=>"VIP","3"=>"Silver","4"=>"Gold");
								$type=(isset($data['2']))?array_search($data['2'],$number_type):'';
								$hday=$this->db->query("SELECT COALESCE(MAX(`id`),0)+1 as id FROM vmc_numbers")->row()->id;
								$this->db->set('id',$hday);
								$this->db->set('number',(isset($data['1']))?$data['1']:'');
								$this->db->set('type',$type);
								$this->db->set('operator',(isset($data['3']))?$data['3']:'');
								$this->db->set('region',(isset($data['4']))?$data['4']:'');
								$this->db->set('simtaken',(isset($data['5']))?$data['5']:'');
								$this->db->set('available_dt',date('Y-m-d'));
								$this->db->set('lastupdate_dt',date('Y-m-d H:i:s'));
								$this->db->set('status','0');
								$this->db->insert("vmc_numbers");
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
	function EditNumber($id){
		if($id!=""){
			$itemDetail = $this->getnumberDetail($id);
			
		}else{
			$id=$this->db->query("SELECT COALESCE(MAX(`id`),0)+1 as id FROM vmc_numbers")->row()->id;
			$this->db->set('id',$id);
			$this->db->set('number',$this->input->post('number'));
			$this->db->set('type',$this->input->post('type'));
			$this->db->set('operator',$this->input->post('operator'));
			$this->db->set('region',$this->input->post('region'));
			$this->db->set('available_dt',date('Y-m-d H:i:s'));
			$this->db->set('simtaken',$this->input->post('pool'));
			$this->db->set('lastupdate_dt',date('Y-m-d H:i:s'));
			$this->db->set('status',0);
			$this->db->insert("vmc_numbers");
			return true;
		}
		
	}
	function notAvailable($ofset='0',$limit='20'){
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['number']) && $s['number']!='')?" and a.number like '%".$s['number']."%'":"";
		$q.=(isset($s['type']) && $s['type']!='')?" and a.type = '".$s['type']."'":"";
		$q.=(isset($s['operator']) && $s['operator']!='')?" and a.operator like '%".$s['operator']."%'":"";
		$q.=(isset($s['region']) && $s['region']!='')?" and a.region ='".$s['region']."'":"";
		$q.=(isset($s['simtaken']) && $s['simtaken']!='')?" and a.simtaken ='".$s['simtaken']."'":"";
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS a.id,a.number,a.type,a.operator,a.region,a.status,a.available_dt
				,a.lastupdate_dt,a.simtaken FROM vmc_numbers a 
				WHERE a.status=2 $q ORDER BY a.available_dt DESC limit $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function Numbersblk($ofset='0',$limit='20'){
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['number']) && $s['number']!='')?" and a.number like '%".$s['number']."%'":"";
		$q.=(isset($s['type']) && $s['type']!='')?" and a.type = '".$s['type']."'":"";
		$q.=(isset($s['operator']) && $s['operator']!='')?" and a.operator like '%".$s['operator']."%'":"";
		$q.=(isset($s['region']) && $s['region']!='')?" and a.region ='".$s['region']."'":"";
		$q.=(isset($s['bdatefrom']) && $s['bdatefrom']!='')?" and a.blocked_dt >='".$s['bdatefrom']."'":"";
		$q.=(isset($s['bdateto']) && $s['bdateto']!='')?" and a.blocked_dt <='".$s['bdateto']."'":"";
		$q.=(isset($s['blkby']) && $s['blkby']!='')?" and a.blockedby ='".$s['blkby']."'":"";
		$q.=(isset($s['blkfor']) && $s['blkfor']!='')?" and a.blockedfor = '".$s['blkfor']."'":"";
		$q.=(isset($s['simtaken']) && $s['simtaken']!='')?" and a.simtaken ='".$s['simtaken']."'":"";
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS a.id,a.number,a.type,a.operator,a.region,a.status,a.available_dt,
				v.email as blockedby,c.email as blockedfor,a.blocked_dt,a.lastupdate_dt,a.simtaken,a.clientname FROM vmc_numbers a 
				left join vmc_executives v on a.blockedby =v.eid
				LEFT JOIN vmc_executives c on a.blockedfor=c.eid
				WHERE a.status=1 $q ORDER BY a.available_dt DESC limit $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function reqblk($ofset='0',$limit='20'){
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['number']) && $s['number']!='')?" and a.number like '%".$s['number']."%'":"";
		$q.=(isset($s['type']) && $s['type']!='')?" and a.type = '".$s['type']."'":"";
		$q.=(isset($s['operator']) && $s['operator']!='')?" and a.operator like '%".$s['operator']."%'":"";
		$q.=(isset($s['region']) && $s['region']!='')?" and a.region ='".$s['region']."'":"";
		$q.=(isset($s['bdatefrom']) && $s['bdatefrom']!='')?" and a.blocked_dt >='".$s['bdatefrom']."'":"";
		$q.=(isset($s['bdateto']) && $s['bdateto']!='')?" and a.blocked_dt <='".$s['bdateto']."'":"";
		$q.=(isset($s['blkby']) && $s['blkby']!='')?" and a.blockedby ='".$s['blkby']."'":"";
		$q.=(isset($s['blkfor']) && $s['blkfor']!='')?" and a.blockedfor = '".$s['blkfor']."'":"";
		$q.=(isset($s['simtaken']) && $s['simtaken']!='')?" and a.simtaken ='".$s['simtaken']."'":"";
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS a.id,a.number,a.type,a.operator,a.region,a.status,a.available_dt,
				v.email as blockedby,c.email as blockedfor,a.blocked_dt,a.lastupdate_dt,a.simtaken,a.clientname FROM vmc_numbers a 
				left join vmc_executives v on a.blockedby =v.eid
				LEFT JOIN vmc_executives c on a.blockedfor=c.eid
				WHERE a.status=4 $q ORDER BY a.available_dt DESC limit $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function availableNumbers($ofset='0',$limit='20'){
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['number']) && $s['number']!='')?" and a.number like '%".$s['number']."%'":"";
		$q.=(isset($s['type']) && $s['type']!='')?" and a.type = '".$s['type']."'":"";
		$q.=(isset($s['operator']) && $s['operator']!='')?" and a.operator like '%".$s['operator']."%'":"";
		$q.=(isset($s['region']) && $s['region']!='')?" and a.region ='".$s['region']."'":"";
		$q.=(isset($s['simtaken']) && $s['simtaken']!='')?" and a.simtaken ='".$s['simtaken']."'":"";
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS a.id,a.number,a.type,a.operator,a.region,a.status,a.available_dt
				,a.lastupdate_dt,a.simtaken FROM vmc_numbers a 
				WHERE a.status=0 $q ORDER BY a.available_dt DESC limit $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function addexecutive($id=''){
		if($id !=''){
			if($this->input->post('password') != '')
			$this->db->set('password',MD5($this->input->post('password')));
			$this->db->set('name',$this->input->post('name'));
			$this->db->set('number',$this->input->post('number'));
			$this->db->set('isadmin',$this->input->post('isadmin'));
			$this->db->set('loginaccess',$this->input->post('loginaccess'));
			$this->db->where('eid',$id);
			$this->db->update("vmc_executives");
			return true;
		}else{
			$sql = $this->db->query("SELECT eid FROM vmc_executives WHERE email='".$this->input->post('email')."'");
			if($sql->num_rows() == 0){
				$id=$this->db->query("SELECT COALESCE(MAX(`eid`),0)+1 as id FROM vmc_executives")->row()->id;
				$this->db->set('eid',$id);
				$this->db->set('name',$this->input->post('name'));
				$this->db->set('email',$this->input->post('email'));
				$this->db->set('password',MD5($this->input->post('password')));
				$this->db->set('isadmin',$this->input->post('isadmin'));
				$this->db->set('number',$this->input->post('number'));
				$this->db->set('loginaccess',$this->input->post('loginaccess'));
				$this->db->set('status',1);
				$this->db->insert("vmc_executives");
				return true;
			}else{
				return false;
			}
		}
	}
	function listexec($ofset='0',$limit='20'){
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['name']) && $s['name']!='')?" and a.name like '%".$s['name']."%'":"";
		$q.=(isset($s['email']) && $s['email']!='')?" and a.email like '%".$s['email']."%'":"";
		$q.=(isset($s['number']) && $s['number']!='')?" and a.number like '%".$s['number']."%'":"";
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS a.eid,a.name,a.email,a.number,a.status
				FROM vmc_executives a 
				WHERE a.email <> '".$this->session->userdata('email')."' $q limit $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;
	}
	function execDetails($eid){
		$res = $this->db->query("SELECT a.eid,a.name,a.email,a.number,a.status,a.loginaccess,a.emailpass
				FROM vmc_executives a 
				WHERE a.eid = '".$eid."'")->result_array();
		$result =  $res[0];
		return $result;
	}
	function statusChange($eid){
		$res = $this->db->query("SELECT a.status 
				FROM vmc_executives a 
				WHERE a.eid = '".$eid."'")->result_array();
		$stat =  $res[0]['status'];
		if($stat == 0)
			$this->db->set('status','1');
		else
			$this->db->set('status','0');
		$this->db->where('eid',$eid);
		$this->db->update("vmc_executives");
		return 1;
	}
	function getExecutives(){
		$eid=$this->session->userdata('eid');
		$sql=$this->db->query("SELECT eid,email,name from vmc_executives where status=1");
		$res=array(""=>"Select");
		foreach($sql->result_array() as $row){
			$res[$row['eid']]=$row['name']."[".$row['email']."]";
		}
		return $res;
	}
	function blkNumber(){
		$error=0;
		if($this->input->post('submit')){
			$this->load->model('emailmodel','emodel');
			$rs=explode(",",$_POST['ids']);
			$m='<table border="1" style="font-family: Helvetica Neue, Arial, Helvetica, Geneva, sans-serif;color: #444444;font-size: 13px;margin-left:35px;">';
			
			foreach($rs as $ids){
				//$nD=$this->getNumberDetails($ids);
				//if($_POST['actdate']>=$nD->available_dt){
					$m.='<tr><td>'.$ids.'</td></tr>';
					$error++;
					$this->db->set('status',4);
					$this->db->set('clientname',$this->input->post('client'));
					$this->db->set('blockedfor',$_POST['blkfor']);
					$this->db->set('blockedby',$this->session->userdata('eid'));
					$this->db->set('blocked_dt',date('Y-m-d H:i:s'));
					$this->db->set('lastupdate_dt',date('Y-m-d H:i:s'));
					$this->db->where('number',$ids);
					$this->db->update('vmc_numbers');
				//~ //}
			}
			$m.='</table>';
			}
			if($error!=0){
				$exD=$this->execDetails($this->session->userdata('eid'));
				$eDx=$this->execDetails($_POST['blkfor']);
				if($this->session->userdata('eid')!=$_POST['blkfor']){
					$message='Requested Numbers are blocked for the client '.$this->input->post('client').' blocked by '.$exD['name']. ' for '.$eDx['name'].'<br/>'.$m;					
				}else{
					$message=$eDx['name']. ' is requested to block the numbers for the client '.$this->input->post('client').'<br/>'.$m;
				}
				$body=$this->emailmodel->newEmailBody($message,' ');
				$config['charset']    = 'utf-8';
				$config['newline']    = "\r\n";
				$config['mailtype'] = 'html'; // or html
				$config['validation'] = TRUE; // bool whether to validate email or not      
				$this->email->initialize($config);
				$this->email->from('<noreply@mcube.com>','Mcube');
				$this->email->to('<raj.m@vmc.in>');
				$this->email->cc('<sundeep.misra@vmc.in>','ajay.jagtap@vmc.in');
				$this->email->bcc('tapan.chatterjee@vmc.in'); 
				$this->email->subject('Block Number for the clinet '.$this->input->post('client').' requested by '.$eDx['name']);
				$this->email->message($body);  
				$this->email->send();
				}
		return $error;
	}
	function unblkNumber(){
		$error=0;
		if($this->input->post('submit')){
			$rs=explode(",",$_POST['ids']);
			foreach($rs as $ids){
				
					$error++;
					$this->db->set('status',$_POST['status']);
					$this->db->set('clientname','');
					$this->db->set('blockedfor', '');
					$this->db->set('blockedby','');
					$this->db->set('blocked_dt','');
					$this->db->set('lastupdate_dt',date('Y-m-d H:i:s'));
					$this->db->where('number',$ids);
					$this->db->update('vmc_numbers');
				
			}
		}
		return $error;
	}
	function getNumberDetails($number){
		$sql=$this->db->query("SELECT * FROM vmc_numbers where number='".$number."'");
		return $sql->row();
	}
	function status_update($number){
		$this->db->set('status',$_POST['status']);
		$this->db->set('number',$_POST['number']);
		$this->db->set('simtaken',$_POST['simtaken']);
		$this->db->set('region',$_POST['region']);
		$this->db->set('type',$_POST['type']);
		$this->db->set('operator',$_POST['operator']);
		$this->db->set('simtaken',$this->input->post('pool'));
		$this->db->set('lastupdate_dt',date('Y-m-d H:i:s'));
		$this->db->where('number',$number);
		$this->db->update('vmc_numbers');
		return true;
	}
	function cBlk(){
		$ex=explode(",",$this->session->userdata('numbers'));
		foreach($ex as $n){
			$this->db->set('status',1);
			$this->db->set('lastupdate_dt',date('Y-m-d H:i:s'));
			$this->db->where('number',$n);
			$this->db->update('vmc_numbers');
			
		}
		return true;
	}
	function update_pass(){
		$this->db->set('emailpass',base64_encode($this->input->post('password')));
		$this->db->where('eid',$this->session->userdata('eid'));
		$this->db->update('vmc_executives');
		return true;
	}
}

/* end of model*/
