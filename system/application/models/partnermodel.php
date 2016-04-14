<?php
class partnermodel extends Model {
	var $data,$partner_id=null;
    function partnermodel(){
        parent::Model();
        $this->load->model('commonmodel');
        $this->load->model('auditlog');
        $this->load->model('emailmodel');
        $this->partner_id=$this->session->userdata('partner_id');
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
									'system/application/js/jquery.custom.js'
							),
							'CLogo'=>$this->ChangedPartnerLogo()	
						);		
		return $data;
	}
	
	function viewLayout($view = '',$data = ''){
			$this->load->view('partnerheader',$this->data);
			$this->load->view($view,$data);
			$this->load->view('sidebar');
			$this->load->view('footer');
	}
	function load_languages()
	{
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
	function GetBusinessusers($ofset='0',$limit='20'){
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['businessname']) && $s['businessname']!='')?" and businessname like '%".$s['businessname']."%'":"";
		
		$res=array();
				$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS * from business where domain_id=".$this->partner_id." $q LIMIT $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function AddBusinessUser($id=''){
		if($id!=""){
			$this->db->set('businessname', $this->input->post('login_businessname')); 
			$this->db->set('businessemail', $this->input->post('login_username')); 
			//$this->db->set('contactemail', $this->input->post('cemail')); 
			//$this->db->set('contactname', $this->input->post('cname')); 
			$this->db->set('contactphone', $this->input->post('cphone')); 
			$this->db->set('businessphone', $this->input->post('bphone')); 
			$this->db->set('businessaddress', htmlentities(mysql_escape_string($this->input->post('baddress')))); 
			$this->db->set('webaddress', $this->input->post('waddress')); 
			$this->db->set('businessaddress1', htmlentities(mysql_escape_string($this->input->post('baddress1'))));
			$this->db->set('city', $this->input->post('city')); 
			$this->db->set('state', $this->input->post('state')); 
			$this->db->set('zipcode', $this->input->post('zipcode')); 
			$this->db->set('country', $this->input->post('country')); 
			$this->db->set('locality', $this->input->post('locality')); 
			$this->db->set('language', $this->input->post('language')); 
			$this->db->where('bid',$id);
			$this->db->update('business');
			return true;
		}else{
			$res=$this->commonmodel->register($this->partner_id);
			return true;
			}
	}
	function get_busValues($bid){
		if($bid!=""){
			$sql=$this->db->query("SELECT * FROM business WHERE bid=".$bid);
			return $sql->result_array();
			
		}else{
			return array();
		}
		
	}
	function ChangeBusinessStatus($bid){
		$res=$this->get_busValues($bid);
		if($res[0]['status']!=0){
			$this->unsetStatus();
			$this->db->where('bid',$bid);
			$this->db->update($bid."_employee");
			
			$this->unsetStatus();
			$this->db->where('bid',$bid);
			$this->db->update($bid."_group_emp");
			
			$this->unsetStatus();
			$this->db->where('bid',$bid);
			$this->db->update("business");
			return true;	
		}else{
			$this->setStatus();
			$this->db->where('bid',$bid);
			$this->db->update($bid."_employee");
			
			$this->setStatus();
			$this->db->where('bid',$bid);
			$this->db->update($bid."_group_emp");
			
			$this->setStatus();
			$this->db->where('bid',$bid);
			$this->db->update("business");
			return true;	
		}
	}
	function setStatus(){
		$this->db->set('status',1);
	}
	function unsetStatus(){
		$this->db->set('status',0);
	}
	function CheckBusinessUser(){
		$email=$this->input->post('email');
		$bid=$this->input->post('bid');
		$q=($bid!="")?" and bid!=$bid":'';
		$clients=$this->db->query("select bid from business")->result_array();
		$ret = 0;
		if(!empty($clients)){
			foreach($clients as $client){
				$sql = $this->db->query("SELECT * FROM ".$client['bid']."_employee WHERE empemail ='".$email."' $q");
				if($sql->num_rows($sql)>0){
					$ret = 1;
					break;
				}
			}
		}
		return $ret;
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
	function updateproduct_config($bid){
		$keys=array_keys($_POST['product']);
		for($i=0;$i<sizeof($keys);$i++)
		{
			$this->db->set('rate',$_POST['product'][$keys[$i]]);
			$this->db->where('bid',$bid);
			$this->db->where('product_id',$keys[$i]);
			$this->db->update('product_rate');
			
		}	
		return true;
	}
	function businessproducts($bid){
		$res=array();
		$sql=$this->db->query("select p.bid,p.product_id,p.rate,pb.product_name as productname from product_rate p
								LEFT JOIN products pb on pb.product_id=p.product_id			
								where p.bid=$bid");
		if($sql->num_rows()>0){
			return $sql->result_array();
		}
		else{
			return $res;
		}
	}
	function feature_manage(){
		$sql=$this->db->query("SELECT * FROM `feature_list` where parent_id=0");
		return $sql->result_array();
	}
	function sub_featuremanage(){
		$sql=$this->db->query("SELECT * FROM `feature_list` where parent_id!=0");
		return $sql->result_array();
	}
	function checked_featuremanage($bid){
		$res=array();
		$sql=$this->db->query("SELECT feature_id FROM `business_feature` where bid=".$bid);
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $ress){
				$res[]=$ress['feature_id'];
			}
		}
		return $res;
	}
	function partner_features(){
		$res=array();
		$sql=$this->db->query("SELECT feature_id FROM `partner_feature` where partner_id=".$this->session->userdata('partner_id'));
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $ress){
				$res[]=$ress['feature_id'];
			}
		}
		return $res;
		
		
	}
	function update_featuremanage($bid){
		$sql=$this->db->query("delete from business_feature where bid=".$bid);
		foreach($_POST['featureconfig'] as $key=>$val){
			$sql="REPLACE INTO business_feature set bid='".$bid."',feature_id='".$key."'";
			$this->db->query($sql);		
		}
		return true;
	}
	function SendMails_Unconfirm($bid){
		$sql=$this->db->query("SELECT * from ".$bid."_employee where status=3");
		if($sql->num_rows()>0){
			$res=$sql->result_array();
			foreach($res as $rows){
						
					$message_body=$this->emailmodel->email_body($rows['empname'],$rows['eid'],$bid);
					
					
					$to  = $rows['empemail'];
					$subject = 'Registered Employee Details';
					$from='MCube <noreply@mcube.com>';
					$message = $this->emailmodel->email_header().$message_body.$this->emailmodel->email_footer();
					$headers = 'MIME-Version: 1.0' . "\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
					$headers .= 'From:'.$from. "\n";
					mail($to, $subject, $message, $headers);
			}
			return true;
		}else{
			return false;
		}
	}
	function Sendmail_unconfirm_Emp($bid){
		$eids=$this->input->post('eids');
		for($i=0;$i<sizeof($eids);$i++){
			$sql=$this->db->query("SELECT * from ".$bid."_employee where status=3 and eid=$eids[$i]");
			if($sql->num_rows()>0){
				$res=$sql->result_array();
				$message_body=$this->emailmodel->email_body($res[0]['empname'],$res[0]['eid'],$bid);
				$to  = $res[0]['empemail'];
				$subject = 'Registered Employee Details';
				$from='MCube <noreply@mcube.com>';
				$message = $this->emailmodel->email_header().$message_body.$this->emailmodel->email_footer();
				$headers = 'MIME-Version: 1.0' . "\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
				$headers .= 'From:'.$from. "\n";
				mail($to, $subject, $message, $headers);	
				}	
			}
			return true;
	}
	function get_unconfirmed_emp($bid){
		$res=array();
		$sql=$this->db->query("SELECT * from ".$bid."_employee where status=3");
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}
		return $res;	
	}
	function managePriList($ofset='0',$limit='20'){
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['prinumber']) && $s['prinumber']!='')?" and p.pri like '%".$s['prinumber']."%'":"";
		$q.=(isset($s['landing_number']) && $s['landing_number']!='')?" and p.landingnumber like '%".$s['landing_number']."%'":"";
		$q.=(isset($s['businessname']) && $s['businessname']!='')?" and b.businessname like '%".$s['businessname']."%'":"";
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS 
		p.number,p.pri,p.landingnumber,b.businessname as businessname from 
		prinumbers p left join business b on p.bid=b.bid where 
		p.partner_id=".$this->session->userdata('partner_id') ." $q LIMIT $ofset,$limit
									   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function get_businessusers()
	{
		$sql=$this->db->query("select * from business where status=1 and domain_id=".$this->partner_id);
		return $sql->result_array();
		
	}
	function getLandingNumber(){
		$res = array();
		$sql=$this->db->query("Select * from landingnumbers where status=0");
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}
		return $res;
	}
	function PriList_Auto(){
		$res=array();
		$sql=$this->db->query("SELECT SQL_CALC_FOUND_ROWS p.* FROM prilist p
							  where p.prinumber NOT IN(select pri from prinumbers where pri!=p.prinumber) and p.partner_id=".$this->partner_id);
		//	$res[]="select";
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $re)
			$res[$re['prinumber']]=$re['prinumber'];
		}
		return $res;
	}
	function MobilenumberConfig($number=''){
		if($number!=''){
				$this->db->set('number', $this->input->post('landingnumber')); 
				$this->db->set('pri', $this->input->post('pri')); 
				$this->db->set('region', $this->input->post('region')); 
				$this->db->where('number',$number);
				$this->db->update('landingnumbers');
				//$this->admin_activitylog(0,"Landing number :".$this->input->post('landingnumber')." Updated");
		}else{
			$s=$this->db->query("select * from landingnumbers where number='".$this->input->post('landingnumber')."'");
			if($s->num_rows()==0){
				$this->db->set('number', $this->input->post('landingnumber')); 
				$this->db->set('pri', $this->input->post('pri')); 
				$this->db->set('region', $this->input->post('region')); 
				$this->db->set('status', '0'); 
				$this->db->insert('landingnumbers');
				
				//$this->admin_activitylog(0,"Landing number :".$this->input->post('landingnumber')." Inserted");
				return true;
			}else{
				return false;
			}
		}
	}
	function getLandingNumber1(){
		$opt='';
		$sql=$this->db->query("Select * from landingnumbers where status=0");
		foreach($sql->result_array() as $num){
				$opt.="<option value='".$num['number']."' rel='".$num['pri']."' rel1='".$num['region']."'>".$num['number']."</option>";
		}
		return $opt;
	}
	function numberConfig($pri='',$bid=''){
		$rows=1;
		//return "select * from prinumbers where landingnumber=".$this->input->post('landingnumber');
		$q = "select * from prinumbers where landingnumber=".$this->input->post('landingnumber');
		$q .= ($pri!='')? " AND number!=$pri":"";
		$sql=$this->db->query($q);
		if($sql->num_rows()==0){
			
			if($pri!=''){
				   if($this->input->post('businessuser')!=$this->input->post('actualuser')){
					   	$s=$this->db->query("select * from prinumbers where associateid=0 and number=$pri");
						$rows=$s->num_rows();
					}
					if($rows==1){
						$this->db->set('bid', ($bid!="")?$bid:$this->input->post('businessuser')); 
						$this->db->set('pri', $this->input->post('pri')); 
						$this->db->set('landingnumber', $this->input->post('landingnumber')); 
						$this->db->set('partner_id',$this->partner_id);
						$this->db->where('number',$pri); 
						$this->db->update('prinumbers');
						//$this->admin_activitylog($this->input->post('businessuser'),$this->input->post('pri')." allotment changed"); 
						return true;
					}else{
						//$this->session->set_flashdata('msgt', 'error');
						//$this->session->set_flashdata('msg', "Landing Number assigned to group");
						return false;
					}
			
			}else{
				
				$this->db->set('bid', ($bid!="")?$bid:$this->input->post('businessuser')); 
				$this->db->set('pri', $this->input->post('pri')); 
				$this->db->set('landingnumber', $this->input->post('landingnumber')); 
				$this->db->set('partner_id',$this->partner_id);
				
				$number=$this->db->query("SELECT COALESCE(MAX(`number`),0)+1 as id FROM prinumbers")->row()->id;
				$this->db->set('number',$number);
				$this->db->insert('prinumbers');
				//$this->admin_activitylog($this->input->post('businessuser'),$this->input->post('pri')." allotment changed"); 
			}
			//echo $this->db->last_query();exit;
			$this->db->query("UPDATE landingnumbers SET status='1' WHERE number='".$this->input->post('landingnumber')."'");
			return true;
		 }
		 return false;
	}
	function getPridetails($pri)
	{
		$sql=$this->db->query("select * from prinumbers where number=$pri");
		return $sql->row();
	}
	function Prinumber_del($pri){
		$check=$this->db->query("select * from prinumbers where number=$pri and bid!=0 and status!=0");
		if($check->num_rows()==0){
			$ress=$this->getPridetails($pri);
			$this->db->query("update landingnumbers set status=0 where number=".$ress->landingnumber);
			$res=$this->db->query("DELETE from prinumbers where number=$pri");
			
			return "succ";
		}else{
			$res=$check->row();
			if($res->type==0){
				$sql=$this->db->query("update ".$res->bid."_groups set status=0,prinumber='' where gid=".$res->associateid);
				$this->db->query("update landingnumbers set status=0 where number=".$res->landingnumber);
				$res1=$this->db->query("DELETE from prinumbers where number=$pri");
				
				return "succ";
			}else{
					$sql=$this->db->query("update ivrs set status=0,prinumber='' where ivrsid=".$res->associateid." and bid=".$res->bid);
					$this->db->query("update landingnumbers set status=0 where number=".$res->landingnumber);
					$res1=$this->db->query("DELETE from prinumbers where number=$pri");
					return "succ";
			}
		}
	}
	function manageUnassignedNumbers($ofset='0',$limit='20'){
		//$q=' WHERE 1';
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['prinumber']) && $s['prinumber']!='')?" and pri like '%".$s['prinumber']."%'":"";
		$q.=(isset($s['landing_number']) && $s['landing_number']!='')?" and number like '%".$s['landing_number']."%'":"";
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM landingnumbers WHERE status=0 $q  LIMIT $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function getUnassingedInfo($num){
		$sql=$this->db->query("SELECT * FROM landingnumbers where status=0 and number='".$num."'");
		return $sql->result_array();
	}
	function Delete_UnassignedNumbers($number){
		$sql=$this->db->query("Delete from landingnumbers where status=0 and number='".$number."'");
		return true;
	}
	function password_reset($bid){
		$b=$this->business_by_id($bid);
		if(!empty($b)){
				$newPass='';
				$get_partner=$this->partner_info($b->domain_id);
				$newPass = "";for($i = 0; $i<=10 ; $i++){$newPass .= ($i%2==0)? chr(rand(97,122)) : rand(0,9);}
				$up=md5($newPass);
				$this->db->query("update user set password='$up' where bid=".$b->bid." and username='".$b->contactemail."'");
				$message_body="Hi  ".$b->contactname."<br/>
				
						Your Password is changed,please find the username and Password<br/><br/><br/>
					
						Username:".$b->contactemail."<br/>
						<br/>
						Password:".$newPass."<br/><br/>
						
						
						Regards<br/>
						
						MCube Team
				
				";
				$to  = $b->contactemail;
				$subject = 'New Password ';
				$from='MCube <noreply@mcube.com>';
				$message = $this->emailmodel->email_header().$message_body.$this->emailmodel->email_footer();
				$headers = 'MIME-Version: 1.0' . "\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
				$headers .= 'From:'.$from. "\n";
				mail($to, $subject, $message, $headers);
				return true;
			 }else{
				return false; 
				 
			 }
		}
	function business_by_id($id){
		$s=$this->db->query("select * from business where bid=".$id. " and status=1 AND domain_id!=0");
		return $s->row();
	}
	function partner_info($p){
		$p=$this->db->query("SELECT * from partner where partner_id=$p");
		return $p->row();
	}
	function partner_business(){
		$res=array();
		$sql=$this->db->query("select * from business where domain_id=".$this->partner_id);
		if($sql->num_rows()>0){
			return $sql->result_array();
		}else{
			return $res;
		}
	}
	function tax_list(){
		$tax=array();
		$sql=$this->db->query("SELECT * FROM tax");
		if($sql->num_rows()>0){
			return $sql->result_array();
		}else{
			return $res;
		}
	}
	function check_business_user($bid){
		$sql=$this->db->query("SELECT * FROM billconfig where bid=".$bid);
		if($sql->num_rows()>0){
			return 0;
		}else{
			return 1;
		}
	}
}
/* end of model*/
