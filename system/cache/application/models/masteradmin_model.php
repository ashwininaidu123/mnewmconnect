<?php
class Masteradmin_model extends Model {
	var $data;
    function Masteradmin_model(){
        parent::Model();
        $this->load->model('auditlog');
        $this->load->model('emailmodel');
        $this->load->model('commonmodel');
         $this->load->plugin('to_pdf');
    }
    function init(){
		//~ if(!$this->checkDomain()){
			//~ redirect('/sitenotavailable');
		//~ }
			
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
									'system/application/js/jquery.bt.js',
									'system/application/js/jquery.validate.js',
									'system/application/js/jquery.tablesorter.js',
									'system/application/js/jquery.easy-confirm-dialog.js',
									'system/application/js/jquery.custom.js',
									'system/application/js/master.js'
								),
								'CLogo'=>''	
						);		
		return $data;
	}
	
	
	function viewLayout($view = '',$data = ''){
			$this->load->view('masterheader',$this->data);
			$this->load->view('sidebar');
			$this->load->view($view,$data);
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
	function add_product()
	{
		$this->db->set('product_name', $this->input->post('productname')); 
		$this->db->set('rate', $this->input->post('Product_rate')); 
		$this->db->set('rate_type', $this->input->post('Product_ratetype')); 
		$this->db->insert('products');
		return $this->db->insert_id();
	}
	function update_product($id)
	{
		$this->db->set('product_name', $this->input->post('productname')); 
		$this->db->set('rate', $this->input->post('Product_rate')); 
		$this->db->set('rate_type', $this->input->post('Product_ratetype')); 
		$this->db->set('discount', $this->input->post('discount_rate')); 
		$this->db->set('discount_date', $this->input->post('discount_date')); 
		$this->db->where('product_id',$id);
		$this->db->update('products');
		return true;
	}
	function get_all_languages()
	{
		$res=array();
		$sql=$this->db->query("select * from language");
		if($sql->num_rows()>0)
		{
			foreach($sql->result_array() as $e)
			$res[$e['langid']]=$e['language'];
			
		}
		return $res;
	}
	function getParentBusiness($bid){
		$res=array();
		$sql=$this->db->query("SELECT * FROM business where pid=0 and bid!='".$bid."'");
		if($sql->num_rows()>0){
			$res['']="select";
			foreach($sql->result_array() as $ar){
				$res[$ar['bid']]=$ar['businessname'];
			}
		}
		return $res;
	}
	function get_allproducts()
	{
		$sql=$this->db->query("select * from products");
		if($sql->num_rows()>0){
			return $sql->result_array();	
			}
		else{
			return array();
		}
	}	
	function get_product($id)
	{
		if($id!=""){
			$sql=$this->db->query("select * from products where product_id=$id");
			return $sql->row();	
			}
		else{
			return array();
		}
	}
	function change_productstatus($id)
	{
		$status=$this->get_product($id);
		if($status->status=="1"){$s=0;}else{$s=1;}
		$sql=$this->db->query("update products set status=$s where product_id=$id");
		return true;
	}
	function delete_product($id){
		$sql=$this->db->query("update products set status=2 where product_id=$id");
		return true;
	}
	function get_businessusers(){
		$sql=$this->db->query("select * from business where status=1 ORDER BY businessname ASC");
		return $sql->result_array();
		
	}
	function get_leadtypes(){
		$sql=$this->db->query("SELECT id,type FROM lead_usage_types");
		return $sql->result_array();
	}
	function get_leaddesigns($param=''){
		$q = ($param != '') ? " WHERE typeid='".$param."' " : '';
		$sql=$this->db->query("SELECT id,design FROM lead_usage_design $q");
		return $sql->result_array();
	}
	function Prinumbers(){
		$res=array();
		$sql=$this->db->query("Select * from prinumbers where status=0 and bid=0");
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}
		return $res;
	}
	function getLandingNumber(){
		$res = array();
		$sql=$this->db->query("Select * from landingnumbers where status=0");
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}
		return $res;
	}
	function getLandingNumber1(){
		$opt='';
		$sql=$this->db->query("Select * from landingnumbers where status=0");
		foreach($sql->result_array() as $num){
				$opt.="<option value='".$num['number']."' rel='".$num['pri']."' rel1='".$num['region']."'>".$num['number']."</option>";
		}
		return $opt;
	}
	function EditNumberCon($pri){
		$s=$this->db->query("select * from prinumbers where number='".$pri."'");
		//~ $cnt=$s->num_rows();
		//if($cnt=='1'){
			$res=$s->row();
		
			if($this->input->post('businessuser')==""){
				$this->upgBid($res,$pri);
			}
			if($this->input->post('businessuser')!=$this->input->post('actualuser')){
				$this->upgBid($res,$pri);
			}
			
			if($this->input->post('businessuser')!=$this->input->post('actualuser')){
				$this->upgBid($res,$pri);
			}
			if($this->input->post('mod')!=$this->input->post('mid')){
				$array=array("1"=>"0","2"=>"3","3"=>"1","4"=>"4");
				$this->upgBid($res,$pri);
				$this->db->set('type',$array[$this->input->post('mod')]); 
			}
			$this->db->set('bid', ($this->input->post('businessuser')!="")?$this->input->post('businessuser'):'0'); 
			$this->db->set('landingnumber', $this->input->post('landingnumber')); 
			if($this->input->post('businessuser')==""){
					$this->db->set('associateid ','0'); 
					$this->db->set('svdate',''); 
					$this->db->set('used','0');
					$this->db->set('status','0');
			}
			if($this->input->post('businessuser')!=$this->input->post('actualuser')){
				$this->db->set('associateid ',''); 
				$this->db->set('svdate',$this->input->post('svdate')); 
				$this->db->set('used','0');
				$this->db->set('status','0');
			}
			$this->db->set('rental',$this->input->post('prental'));
			$this->db->set('flimit',$this->input->post('pfmins'));
			$this->db->set('climit',$this->input->post('pclimit'));
			$this->db->set('rpi',$this->input->post('prate'));
			$this->db->set('payment_term',$this->input->post('pterm'));
			$this->db->set('sms_limit',$this->input->post('slimit'));
			$this->db->set('parallel_limit',$this->input->post('plimit'));
			$this->db->set('support',$this->input->post('support'));
			$this->db->set('autoreset',$this->input->post('autoreset'));
			$this->db->set('package_id',$this->input->post('package'));
			if($_POST['Opri']!=$_POST['pri']){
				$this->db->set('pri',$_POST['pri']);	
				$this->db->where('pri',$_POST['Opri']);	
			}
			else{
				$this->db->where('number',$pri);
			}
			$this->db->update('prinumbers');
			$this->db->set('number',$this->input->post('landingnumber'));
			$this->db->set('module_id',$this->input->post('module')); 
			if($_POST['Opri']!=$_POST['pri']){
				$this->db->set('pri',$_POST['pri']);	
				$this->db->where('pri',$_POST['Opri']);	
			}
			else{
				$this->db->where('pri',$_POST['pri']);
			}
			
			$this->db->update('landingnumbers');
			$this->db->query("DELETE FROM business_packageaddons WHERE number='".$res->number."'");
			$s=$this->db->query("SELECT * FROM package_feature where package_id=".$this->input->post('package')." and feature_id!=0");
				if($s->num_rows()>0){
					
					$padons=array();
					foreach($s->result_array() as $rs){
						$padons[]=$rs['feature_id'];
					}
				}
				$parent_id='';
				for($i=0;$i<sizeof($_POST['addons']);$i++){
						if(in_array($_POST['addons'][$i],$padons)){
						$parent_id=$this->input->post('package');
					}else{
						$parent_id=0;
					}
						$bid=($this->input->post('businessuser')!="")?$this->input->post('businessuser'):'0';
						 $this->db->query("REPLACE INTO  business_packageaddons set bid='".$bid."',package_id='".$parent_id."',feature_id='".$_POST['addons'][$i]."',startdate='".date('Y-m-d h:i:s')."',number='".$pri."'");
						}
					
					return true;
			//}
	}
	function numberConfig($pri=''){
		$s=$this->db->query("SELECT * from prinumbers where associateid=0 and number='".$pri."'");
		$cnt=$s->num_rows();
		if($cnt=='1'){
			$sql = "SELECT count(*) as cnt FROM prinumbers WHERE (pri='".$this->input->post('pri')."' OR landingnumber='".$this->input->post('landingnumber')."') AND number!='".$pri."'";
			$rst = $this->db->query($sql)->row()->cnt;
			if($rst=='0'){
				$this->db->set('bid', $this->input->post('businessuser')); 
				$this->db->set('pri', $this->input->post('pri')); 
				$this->db->set('landingnumber', $this->input->post('landingnumber')); 
				$this->db->where('number',$pri); 
				$this->db->update('prinumbers');
				return true;
			}else{
				return false;
			}
		}else{
			$sql = "SELECT count(*) as cnt FROM prinumbers WHERE pri='".$this->input->post('pri')."' OR landingnumber='".$this->input->post('landingnumber')."'";
			$rst = $this->db->query($sql)->row()->cnt;
			if($rst=='0'){
				$siminfo = (($_POST['simholder'] != '')) ? $_POST['simholder'] : (($_POST['tempnumber'] != '') ? $_POST['tempnumber'] : '');	
				$PDetails=$this->get_package($this->input->post('package'));
				$number=$this->db->query("SELECT COALESCE(MAX(`number`),0)+1 as id FROM prinumbers")->row()->id;
				if($this->input->post('businessuser')!=""){
					$this->db->set('bid', $this->input->post('businessuser')); 
				}
				$type = array(
					'1' => '0',
					'2' => '2',
					'3' => '1',
					'4' => '3'
				);
				$this->db->set('type', $type[$this->input->post('module')]); 
				$this->db->set('pri', $this->input->post('pri')); 
				$this->db->set('landingnumber', $this->input->post('landingnumber')); 
				$this->db->set('landing_key', $this->gen_landingkey()); 
				$this->db->set('climit', floatval($PDetails->creditlimit)); 
				$this->db->set('package_id', $this->input->post('package')); 
				$this->db->set('number',$number);
				$this->db->set('climit',$this->input->post('pclimit'));
				$this->db->set('flimit',$this->input->post('pfmins'));
				$this->db->set('assigndate',date('Y-m-d H:i:s'));
				$this->db->set('rental',$this->input->post('prental'));
				$this->db->set('rpi',$this->input->post('prate'));
				$this->db->set('ntype',$this->input->post('ntype'));
				$this->db->set('svdate',$this->input->post('svdate'));
				$this->db->set('ownership',$this->input->post('owner'));
				$this->db->set('payment_term',$this->input->post('pterm'));
				$this->db->set('sms_limit',$this->input->post('slimit'));
				$this->db->set('parallel_limit',$this->input->post('plimit'));
				$this->db->set('support',$this->input->post('support'));
			    $this->db->set('autoreset', $this->input->post('autoreset'));
				$this->db->set('siminfo',$siminfo);
				$this->db->insert('prinumbers');
				// Business Auto reset
				if($this->input->post('autoreset') && $this->input->post('businessuser')!=""){ 
					$this->db->set('autoreset','1');
					$this->db->where('bid', $this->input->post('businessuser')); 
					$this->db->update('business');
			    }
				if($this->input->post('businessuser')!=""){
					$this->db->set('bid', $this->input->post('businessuser')); 
				}
				$this->db->set('package_id', $this->input->post('package')); 
				$this->db->set('activated_date',date('Y-m-d h:i:s'));
				$this->db->set('status','1');
				$this->db->set('rental',$PDetails->rental);
				$this->db->set('climit',$PDetails->creditlimit);
				$this->db->set('number',$number);
				$this->db->set('activatedby',$this->session->userdata('uid'));
				$this->db->insert('package_activate');
				
				if($this->input->post('businessuser')!=""){
					$this->admin_activitylog($this->input->post('businessuser'),$this->input->post('pri')." allotment changed");
				}
				$this->db->query("UPDATE landingnumbers SET status='1',module_id='".$this->input->post('module')."' WHERE number='".$this->input->post('landingnumber')."'");
				$s=$this->db->query("SELECT * FROM package_feature where package_id=".$this->input->post('package')." and feature_id!=0 ");
				if($s->num_rows()>0){
					foreach($s->result_array() as $rs){
							$bid=($this->input->post('businessuser')!="")?$this->input->post('businessuser'):'0';
							$this->db->query("REPLACE INTO  business_packageaddons set bid='".$bid."',package_id='".$this->input->post('package')."',feature_id='".$rs['feature_id']."',startdate='".date('Y-m-d h:i:s')."',number='".$number."'");
					}
				}
				if(sizeof($_POST['featureids'])>0){
					for($i=0;$i<sizeof($_POST['featureids']);$i++){
						$bid=($this->input->post('businessuser')!="")?$this->input->post('businessuser'):'0';
						$this->db->query("REPLACE INTO  business_packageaddons set bid='".$bid."',package_id='0',feature_id='".$_POST['featureids'][$i]."',startdate='".date('Y-m-d h:i:s')."',number='".$number."'");
					}
				}
				if($this->input->post('businessuser')!=""){
					$bdetail=$this->get_busValues($this->input->post('businessuser'));
					$message = $this->input->post('landingnumber') ." is assigned to the client ".$bdetail[0]['businessname']."\n having the package <b>'".$PDetails->packagename."'</b> , Rental to this number is <b>".$this->input->post('prental')."</b> and service activation date will be on ".$this->input->post('svdate');
					$body=$this->emailmodel->newEmailBody($message,' All');
					$config['charset']    = 'utf-8';
					$config['newline']    = "\r\n";
					$config['mailtype'] = 'html'; // or html
					$config['validation'] = TRUE; // bool whether to validate email or not      
					$this->email->initialize($config);
					$this->email->from('<noreply@mcube.com>','Mcube');
					$this->email->to("support@vmc.in");
					//$this->email->cc('sundeep.misra@vmc.in','ajay.jagtap@vmc.in','raj.m@vmc.in');
					//$this->email->bcc('tapan.chatterjee@vmc.in'); 
					$this->email->subject($this->input->post('landingnumber').' assigned to  client '.$bdetail[0]['businessname']);
					$this->email->message($body);  
					$this->email->send();
					$to = "support@vmc.in";
					$subject = $this->input->post('landingnumber').' assigned to  client '.$bdetail[0]['businessname'];
					//MCubeMail($to,$subject,$body);
				}
				
				
				
				return true;
			}else{
				//error
				return false;
			}
		}
	}
	function addPrinumber(){
		$t=($this->input->post('Prinumber')+$this->input->post('from'));
		
		for($i=$t;$i<($this->input->post('to')+$t);$i++){
			$this->db->set('number',$i); 
			$this->db->insert('prinumbers'); 
			
		}
		$this->admin_activitylog(0,"New Pri Numbers Inserted from ".$this->input->post('from')." to ".$this->input->post('to'));
		return true;
	}
	function managePriList($ofset='0',$limit='20'){
		$q=' WHERE 1';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{

		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['prinumber']) && $s['prinumber']!='')?" and p.pri like '%".$s['prinumber']."%'":"";
		$q.=(isset($s['landing_number']) && $s['landing_number']!='')?" and p.landingnumber like '%".$s['landing_number']."%'":"";
		$q.=(isset($s['businessname']) && $s['businessname']!='')?" and b.businessname like '%".$s['businessname']."%'":"";
		$q.=(isset($s['package']) && $s['package']!='')?" and pac.packagename like '%".$s['package']."%'":"";
		$q.=(isset($s['btype']) && $s['btype']!='')?" and b.act like '%".$s['btype']."%'":"";
		$q.=(isset($s['used']) && $s['used']!='')?" AND p.used > (p.climit*".($s['used']/100).")":"";
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS pac.packagename,p.svdate,p.climit,p.rental,p.used,b.bid,p.package_id,p.number,p.pri,p.landingnumber,b.businessname as businessname,if(b.act=0,'Regular','Demo') as type,p.ownership,p.flimit,p.status,p.type as service from prinumbers p
										LEFT JOIN business b on p.bid=b.bid 
										LEFT JOIN package pac ON pac.package_id = p.package_id $q 
										ORDER BY assigndate desc LIMIT $ofset,$limit 
									   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function listlbsNumber($ofset='0',$limit='20'){
		$q=' WHERE 1';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['number']) && $s['number']!='')?" and number like '%".$s['number']."%'":"";
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS * from lbs_numbers $q LIMIT $ofset,$limit
									   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function PackageHistory($bid,$number){
		$res['data']=$this->db->query("SELECT pact.bid,pact.number,pack.package_id,pack.packagename,pack.freelimit,pact.rental,pact.climit as creditlimit		,pack.rpi,pact.used,prin.landingnumber ,prin.pri,if(pact.used>=pack.freelimit,(pact.used - pack.freelimit),0) as extuse  	FROM package pack
			LEFT JOIN package_activate pact on pack.package_id=pact.package_id
			LEFT JOIN prinumbers prin on pact.number=prin.number
			where pact.bid='".$bid."' and pact.status='0' and pact.number='".$number."'
									   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function getUnassingedInfo($num){
		$sql=$this->db->query("SELECT * FROM landingnumbers where status=0 and number='".$num."'");
		return $sql->result_array();
	}
	function Delete_UnassignedNumbers($number){
		$this->admin_activitylog('0',$number." Number was deleted");
		$sql=$this->db->query("Delete from landingnumbers where status=0 and number='".$number."'");
		return true;
	}
	function manageUnassignedNumbers($ofset='0',$limit='20'){
		//$q=' WHERE 1';
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}/*else{
			$this->session->unset_userdata('search');
		}*/
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['region']) && $s['region']!='')?" and region like '%".$s['region']."%'":"";
		$q.=(isset($s['prinumber']) && $s['prinumber']!='')?" and pri like '%".$s['prinumber']."%'":"";
		$q.=(isset($s['landing_number']) && $s['landing_number']!='')?" and number like '%".$s['landing_number']."%'":"";
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM landingnumbers WHERE status=0 $q  LIMIT $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function assignedParnterPris($ofset='0',$limit='20'){
		
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
		$q.=(isset($s['prinumber']) && $s['prinumber']!='')?" and p.prinumber like '%".$s['prinumber']."%'":"";
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS p.*,pp.firstname,pp.lastname FROM prilist p
									left join partner pp on pp.partner_id=p.partner_id
									where p.prinumber NOT IN(select pri from prinumbers where pri=p.prinumber)
										$q  LIMIT $ofset,$limit")->result_array();
			$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function availblepri($partner_id=''){
		$q='';
		$res=array();
		if($partner_id!=""){
			$q=" and p.partner_id=".$partner_id;
		}else{
			$q=" and p.partner_id=0";
		}
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS p.* FROM prilist p
									   where p.prinumber NOT IN(select pri from prinumbers where pri=p.prinumber)
										$q ")->result_array();
			$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
		
	}
	function pripartner($partner_id){
		$res=$this->get_PartnerValues($partner_id);
		$arr=$this->input->post('sepris');
		//$this->db->query("update prilist set `partner_id`=0 where `partner_id`=".$partner_id);
		for($i=0;$i<sizeof($arr);$i++){
			$this->db->set('partner_id',$partner_id);
			$this->db->where('prinumber',$arr[$i]);
			$this->db->update('prilist'); 	
			$this->admin_activitylog('0',$arr[$i]." assigned to partner :".$res[0]['firstname']);
		}
		return true;
	}
	function getPridetails($pri)
	{
		$sql=$this->db->query("select * from prinumbers where number=$pri");
		return $sql->row();
	}
	function creditConfig(){
			$gid=$this->db->query("SELECT COALESCE(MAX(`id`),0)+1 as id FROM call_credit")->row()->id;
			$this->db->set('id',$gid); 
			$this->db->set('bid', $this->input->post('businessuser')); 
			$this->db->set('note', $this->input->post('notes')); 
			$this->db->set('credit',$this->input->post('credit')); 
			$this->db->set('creditby',$this->session->userdata('uid')); 
			//$this->db->insert('credit_assign'); 
			$this->db->insert('call_credit'); 
			$res=$this->get_busValues($this->input->post('businessuser'));
			$this->admin_activitylog('0',"Credit assigned to ".$rec[0]['businessname']);
			return $this->db->insert_id();
	}
	function leadscredit(){
			$this->db->set('type', $this->input->post('leadtype')); 
			$this->db->set('design',$this->input->post('leaddesign')); 
			$this->db->WHERE('bid', $this->input->post('businessuser'));
			$this->db->update('business_lead_use'); 
			$res=$this->get_busValues($this->input->post('businessuser'));
			$this->admin_activitylog('0',"Leads Version changed to ".$rec[0]['businessname']);
			return '1';
	}
	function SMScreditConfig()
	{
			$gid=$this->db->query("SELECT COALESCE(MAX(`crid`),0)+1 as id FROM credit_assign")->row()->id;
			$this->db->set('crid',$gid); 
			$this->db->set('bid', $this->input->post('businessuser')); 
			$this->db->set('remark', $this->input->post('notes')); 
			$this->db->set('credit',$this->input->post('credit')); 
			$this->db->set('creditby',$this->session->userdata('uid')); 
			$this->db->insert('credit_assign'); 
			$res=$this->get_busValues($this->input->post('businessuser'));
			$this->admin_activitylog('0',"SMSCredit assigned to ".$rec[0]['businessname']);
			return $this->db->insert_id();
	}
	function getCreditlist($ofset='0',$limit='20'){
		$q='where 1';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['businessname']) && $s['businessname']!='')?" and b.businessname like '%".$s['businessname']."%'":"";
		$q.=(isset($s['from']) && $s['from']!='')?" and s.credit>='".$s['from']."'":"";
		$q.=(isset($s['to']) && $s['to']!='')?" and s.credit<='".$s['to']."'":"";
		$q.=(isset($s['dfrom']) && $s['dfrom']!='')?" and date(s.assigndate)<='".$s['dfrom']."'":"";
		$q.=(isset($s['dto']) && $s['dto']!='')?" and date(s.assigndate)<='".$s['dto']."'":"";
		
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS s.id,s.credit,s.assigndate,s.note ,b.businessname as businessname,m.name 	from  call_credit s 
		left join business b on s.bid=b.bid 
		LEFT JOIN master_admin m on s.creditby=m.uid
		$q LIMIT $ofset,$limit
	   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function getsmsCreditlist($ofset='0',$limit='20'){
		$q='where 1';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['businessname']) && $s['businessname']!='')?" and b.businessname like '%".$s['businessname']."%'":"";
		$q.=(isset($s['from']) && $s['from']!='')?" and s.credit>='".$s['from']."'":"";
		$q.=(isset($s['to']) && $s['to']!='')?" and s.credit<='".$s['to']."'":"";
		$q.=(isset($s['dfrom']) && $s['dfrom']!='')?" and date(s.assigndate)<='".$s['dfrom']."'":"";
		$q.=(isset($s['dto']) && $s['dto']!='')?" and date(s.assigndate)<='".$s['dto']."'":"";
		
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS s.crid,s.credit,s.datetime,s.remark ,b.businessname as businessname ,m.name from   credit_assign s left join business b on s.bid=b.bid  LEFT JOIN master_admin m on s.creditby=m.uid $q LIMIT $ofset,$limit
									   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function getleadversions($ofset='0',$limit='20'){
		$q=' WHERE 1 ';
		//~ if(isset($_POST['submit'])){
			//~ $this->session->set_userdata('search',$_POST);
		//~ }else{
			//~ $this->session->unset_userdata('search');
		//~ }
		//~ if($this->session->userdata('search')){
			//~ $s = $this->session->userdata('search');
		//~ }
		//~ $q.=(isset($s['businessname']) && $s['businessname']!='')?" and b.businessname like '%".$s['businessname']."%'":"";
		//~ $q.=(isset($s['from']) && $s['from']!='')?" and s.credit>='".$s['from']."'":"";
		//~ $q.=(isset($s['to']) && $s['to']!='')?" and s.credit<='".$s['to']."'":"";
		//~ $q.=(isset($s['dfrom']) && $s['dfrom']!='')?" and date(s.assigndate)<='".$s['dfrom']."'":"";
		//~ $q.=(isset($s['dto']) && $s['dto']!='')?" and date(s.assigndate)<='".$s['dto']."'":"";
		
		$res=array();
		$sql = "SELECT SQL_CALC_FOUND_ROWS bl.bid,lt.type,ld.design,b.businessname FROM  business_lead_use bl 
				LEFT JOIN lead_usage_types lt ON bl.type = lt.id 
				LEFT JOIN lead_usage_design ld ON bl.design = ld.id 
				LEFT JOIN business b ON bl.bid = b.bid 
				$q ORDER BY bl.bid LIMIT $ofset,$limit ";
		$res['data']=$this->db->query($sql)->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;
	}
	function getbusleaddetails($bid){
		$res=array();
		$sql = "SELECT * FROM  business_lead_use WHERE bid='".$bid."' ";
		$res = $this->db->query($sql)->result_array();
		return $res[0];
	}
	function getkeywordlist($ofset='0',$limit='20'){
		$q='where 1';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['businessname']) && $s['businessname']!='')?" and b.businessname like '%".$s['businessname']."%'":"";
		$res=array();
		
		$res['data']=$this->db->query("select SQL_CALC_FOUND_ROWS k.keyword_id,k.status,k.keyword,k.default_msg,ku.keyworduse as keyworduse,k.fowardto_type,s.code,b.businessname from keword k
										LEFT JOIN keyword_use ku on ku.keyword_useid=k.keyword_use
										LEFT JOIN shortcode s on s.codeid=k.code_id
										LEFT JOIN business b on b.bid=k.bid $q
										LIMIT $ofset,$limit
									   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;
	}
	function changesenderIdstatus($id){
		$sql=$this->db->query("SELECT * FROM senderid WHERE snid=".$id)->row()->status;
		$status=($sql!=0)?0:1;
		$this->db->set('status',$status);
		$this->db->where('snid',$id);
		$this->db->update('senderid');
	}
	function getSenderIdList($ofset='0',$limit='20'){
		$q='where 1';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['businessname']) && $s['businessname']!='')?" and b.businessname like '%".$s['businessname']."%'":"";
		$q.=(isset($s['senderid']) && $s['senderid']!='')?" and s.senderid like '%".$s['senderid']."%'":"";
		$res=array();
		$res['data']=$this->db->query("select s.snid,s.senderid,s.datetime,s.status,if(s.status=1,'Enable','Disable') as status1,b.businessname  from senderid s
										LEFT JOIN business b on b.bid=s.bid		
										$q
										LIMIT $ofset,$limit
									   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function getBusinesslist($ofset='0',$limit='20'){
		$q='where 1';
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
										left join partner p on p.partner_id=b.domain_id $q 
										ORDER BY b.bid DESC
										LIMIT $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;
	}
	function changeBusinessstatus($id){
		$sql = $this->db->query("SELECT status,businessname FROM business
				WHERE bid = '".$id."'");
		$rec = $sql->row();
		if($rec->status=='1'){
			$sql1 = $this->db->query("UPDATE ".$id."_employee SET status='0' WHERE bid = '".$id."'");
			
			$sql=$this->db->query("update user set status=0 where bid=$id");
			$sql1 = $this->db->query("UPDATE business SET status='0' WHERE bid = '".$id."'");
			$this->admin_activitylog('0',$rec->businessname." Business was disabled");
			//$this->auditlog->auditlog_info('Business user',"Business User ".$rec->businessname  ." status disabled");
			
			//$sql2 = $this->db->query("UPDATE group_emp SET status='1' WHERE eid = '".$eid."'");
			
		}else{
			$sql1 = $this->db->query("UPDATE ".$id."_employee SET status='1' WHERE bid = '".$id."'");
			$sql1 = $this->db->query("UPDATE business SET status='1' WHERE bid = '".$id."'");
			$sql=$this->db->query("update user set status=1 where bid=$id");
			$this->admin_activitylog('0',$rec->businessname." Business was enabled");
			//$this->auditlog->auditlog_info('Business user',"Business User ".$rec->businessname  ." status enabled");
			
		}
		return true;
	}
	function get_keystatus($id){
		$sql=$this->db->query("select status from keword where keyword_id=$id")->row()->status;
		return $sql;
	}
	function changekeystatus($id){
		$sql=$this->get_keystatus($id);
		
		($sql==0)?$x=1:$x=0;
		if($sql==0){
			$date=$this->input->post('exdate');
			$sql=$this->db->query("update keword set status=$x,exp_date='$date' WHERE keyword_id=$id");	
		}else{
			$sql=$this->db->query("update keword set status=$x WHERE keyword_id=$id");
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
	function getBusinessNumbers($bid){
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS pri.pri,pri.landingnumber,pri.used,pri.number,pri.package_id,
									pac.packagename,pac.rental,pac.freelimit,pac.rpi,pri.climit,
									if(pri.used>=pac.freelimit,(pri.used - pac.freelimit),0) as extuse
									FROM prinumbers pri
									LEFT JOIN package pac on pri.package_id=pac.package_id
									WHERE pri.bid='".$bid."'")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;		
		return $res;								
		
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
	function Number_email($number,$clientname){
		$message_body="
						".$number." is Removed From the Business - ".$clientname."<br/><br/><br/>
						Regards<br/>
						MCube Team";
				$to=(base_url()=='https://mcube.vmc.in')?'account.blr@vmc.in,sundeep.misra@vmc.in':'accounts@vmc.in,sundeep.misra@vmc.in';
				$subject = $number.' Removed From Business '.$clientname;
				$from='MCube <noreply@mcube.com>';
				$content=$this->emailmodel->newEmailBody($message_body,'');
				$this->load->library('email');
				$this->email->from('noreply@mcube.com', 'MCube');
				$this->email->to($to);
				$this->email->bcc('tapan.chatterjee@vmc.in');
				$this->email->subject($subject);
				$this->email->message($content);
				$this->email->send();	
		
		
	}
	function Prinumber_del($pri){
		$check=$this->db->query("select * from prinumbers where number=$pri");
		$res=$check->row();
		$bdetails=$this->get_busValues($res->bid);
		if($res->type==0){
			$sql=$this->db->query("update ".$res->bid."_groups set status=0,prinumber='' where gid=".$res->associateid);
			$this->admin_activitylog($res->bid,$this->getPRilanding($pri) ." Pri has deleted");
		}elseif($res->type==1){
			$sql=$this->db->query("update ivrs set status=0,prinumber='' where ivrsid='".$res->associateid."' and bid='".$res->bid."'");
			$this->admin_activitylog($res->bid,$this->getPRilanding($pri) ." Ivrs has deleted");
		}else{
			$sql=$this->db->query("update ".$res->bid."_pbx set status=0,prinumber='' where pbxid='".$res->associateid."'");
			$this->admin_activitylog($res->bid,$this->getPRilanding($pri) ." Pri has deleted");
		}
		
		$sql = "INSERT INTO report_log
				SELECT 
				p.bid,
				p.businessname as `Business Name`,
				p.landingnumber `Landing Number`,
				p.pri as `PRI`,
				p.packagename  as `Package`,
				p.climit as `Limit`,
				(p.used + COALESCE(n.reset,0)) as `Usage`,
				if(((p.used + COALESCE(n.reset,0))-p.flimit)>0,
				((p.used + COALESCE(n.reset,0))-p.flimit),0) as `Extra Usage`,
				p.assigndate as `Activation Date`,
				p.svdate as `Service Start Date`,
				DATE_FORMAT(SUBDATE( CURDATE(), INTERVAL 0 MONTH), '%Y-%m') as `Billing Month`
				FROM
				(SELECT 
				p.number,			b.businessname,			p.landingnumber,
				p.pri,				pp.packagename,			p.climit,	p.flimit,
				p.used,				p.assigndate,			p.svdate,	p.bid
				FROM `prinumbers` p
				LEFT JOIN business b on p.bid=b.bid
				LEFT JOIN package pp on p.package_id=pp.package_id
				WHERE b.bid is not null
				AND p.number='".$pri."'
				ORDER BY b.bid,p.assigndate) p
				LEFT JOIN 
				(SELECT number,COALESCE(sum(used),0) as `reset` FROM `number_reset`
				WHERE 1 AND DATE_FORMAT(`rdate`,'%Y-%m')=DATE_FORMAT(SUBDATE( CURDATE(), INTERVAL 0 MONTH), '%Y-%m')
				GROUP BY number) n on p.number=n.number";
				
		$this->db->query($sql);
		
		$this->db->query("update landingnumbers set status=0,module_id=0 where number=".$res->landingnumber);
		$this->db->query("DELETE FROM  business_packageaddons where number='".$pri."'");
		$this->db->query("DELETE FROM  package_activate  where number='".$pri."'");
		$this->db->query("DELETE FROM  package_history  where number='".$pri."'");
		$res1=$this->db->query("DELETE from prinumbers where number='".$pri."'");
		$this->Number_email($res->landingnumber,$bdetails[0]['businessname']);
		return true;
	}
	function getPRilanding($pri){
		//echo "SELECT landingnumber FROM prinumbers where number=$pri";exit;
		$s=$this->db->query("SELECT landingnumber FROM prinumbers where number=$pri")->row()->landingnumber;
		return $s;
	}
	function Creditinfo($ofset='0',$limit='20'){
		$q='where 1';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['businessname']) && $s['businessname']!='')?" and b.businessname like '%".$s['businessname']."%'":"";
		
		$res['data']=$this->db->query("select SQL_CALC_FOUND_ROWS b.bid,c.balance,b.businessname,b.contactemail from credit_bal c
							  left join business b on c.bid=b.bid $q order by c.balance asc LIMIT $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;					  
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
	function update_featuremanage($bid){
		$sql=$this->db->query("delete from business_feature where bid=".$bid);
		foreach($_POST['featureconfig'] as $key=>$val){
			$sql="REPLACE INTO business_feature set bid='".$bid."',feature_id='".$key."'";
			$this->db->query($sql);		
		}
		return true;
	}
	function MobilenumberConfig($number=''){
		$sql = "SELECT COUNT(*) as cnt FROM landingnumbers WHERE number='".$number."'";
		if($this->db->query($sql)->row()->cnt>0){
				$this->db->set('number', $this->input->post('landingnumber')); 
				$this->db->set('pri', $this->input->post('pri')); 
				$this->db->set('region', $this->input->post('region')); 
				$this->db->where('number',$number);
				$this->db->update('landingnumbers');
				$this->admin_activitylog(0,"Landing number :".$this->input->post('landingnumber')." Updated");
		}else{
			$s=$this->db->query("select * from landingnumbers where number='".$number."'");
			if($s->num_rows()==0){
				$this->db->set('number',$this->input->post('landingnumber')); 
				$this->db->set('pri', $this->input->post('pri')); 
				$this->db->set('region', $this->input->post('region')); 
				$this->db->set('status', '0'); 
				$this->db->insert('landingnumbers');
				$this->admin_activitylog(0,"Landing number :".$this->input->post('landingnumber')." Inserted");
				return true;
			}else{
				return false;
			}
		}
	}
	function landing_duplicant(){
		$sl=$this->db->query("select * from prinumbers where landingnumber=".$this->input->post('landingnumber')." and number!=".$this->input->post('prihidden'));
		if($sl->num_rows()>0){
			return 1;	
		}else{
			$this->db->set('bid', $this->input->post('businessuser')); 
			$this->db->set('landingnumber', $this->input->post('landingnumber')); 
			$this->db->where('number', $this->input->post('prihidden')); 
			$this->db->update('prinumbers');
			return 0;
		}
		
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
					//~ $headers = 'MIME-Version: 1.0' . "\n";
					//~ $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
					//~ $headers .= 'From:'.$from. "\n";
					//~ mail($to, $subject, $message, $headers);
					$this->load->library('email');
					$this->email->from('noreply@mcube.com', 'MCube');
					$this->email->to($to);
					$this->email->subject($subject);
					$this->email->message($message);
					$this->email->send();	
					
			}
			$binfo=$this->get_busValues($bid);
			//print_r($binfo);exit;
			$this->admin_activitylog(0,"Mail send to ".$binfo[0]['businessname']." business Unconfirm Employees ");
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
				//~ $headers = 'MIME-Version: 1.0' . "\n";
				//~ $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
				//~ $headers .= 'From:'.$from. "\n";
				//~ mail($to, $subject, $message, $headers);
				$this->load->library('email');
				$this->email->from('noreply@mcube.com', 'MCube');
				$this->email->to($to);
				$this->email->subject($subject);
				$this->email->message($message);
				$this->email->send();	
				
				
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
	function PriList_Auto($number=''){
		$res=array();
		$r=array();
		$sql=$this->db->query("SELECT p.* FROM prilist p
							   LEFT JOIN landingnumbers r on p.prinumber=r.pri
							   where r.pri is null");
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $re)
			$res[$re['prinumber']]=$re['prinumber'];
		}
		if($number!=''){
			$sql=$this->db->query("SELECT * FROM landingnumbers WHERE number='".$number."'");
			$rows=$sql->row();
			$r=array($rows->pri=>$rows->pri);
		}
		return array_merge($res,$r);
	}
	//provider
	function Provider(){
		$res=array();
		$r=array();
		$sql=$this->db->query("SELECT * FROM Provider");
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $re)
			$res[$re['Provider']]=$re['Pro_name'];
		}
		
		return array_merge($res,$r);
	}
	function PriList_select(){
		$opt='';
		$sql=$this->db->query("select * from prilist");
		foreach($sql->result_array() as $num){
				$opt.="<option value='".$num['prinumber']."'>".$num['prinumber']."</option>";
		}
		return $opt;
		
		
	}
	function Addpris(){
		$sq=$this->db->query("SELECT * from  prilist where prinumber='".$this->input->post('prinum')."'");
		if($sq->num_rows()==0){
			 
			 if($this->input->post('from')!="" && $this->input->post('to')!=""){
				 $j=0;
				 
				for($i=$this->input->post('from');$i<=$this->input->post('to');$i++){
					 	$gid=$this->db->query("SELECT COALESCE(MAX(`id`),0)+1 as id FROM prilist")->row()->id;
					 	$this->db->set('id',$gid); 
					 	$this->db->set('provider',$this->input->post('provider')); 
					 	$this->db->set('prinumber',($j==0)?$this->input->post('prinum'):$this->input->post('prinum')+$j); 
						$this->db->insert('prilist');
						if(isset($_POST['demo'])){
							$ext=(base_url()!='http://mcube.vmc.in')?'080':'0120';
							$this->db->set('number',($j==0)?$ext.$this->input->post('prinum'):$ext.($this->input->post('prinum')+$j));
							$this->db->set('pri',($j==0)?$this->input->post('prinum'):$this->input->post('prinum')+$j); 
							$this->db->set('region','ka'); 
							$this->db->set('status','1'); 
							$this->db->set('module_id',($_POST['calltrack']==0)?1:2); 
							$this->db->insert('landingnumbers');
							
							$PDetails=$this->get_package($this->input->post('package'));
							
							$number=$this->db->query("SELECT COALESCE(MAX(`number`),0)+1 as id FROM prinumbers")->row()->id;
							
							$this->db->set('pri', ($j==0)?$this->input->post('prinum'):$this->input->post('prinum')+$j); 
							$this->db->set('landingnumber', ($j==0)?$ext.$this->input->post('prinum'):$ext.($this->input->post('prinum')+$j)); 
							$this->db->set('climit', floatval($PDetails->creditlimit)); 
							$this->db->set('package_id', $this->input->post('package')); 
							$this->db->set('number',$number);
							$this->db->set('climit',$PDetails->creditlimit);
							$this->db->set('rental',$PDetails->rental);
							$this->db->insert('prinumbers');
							
							$this->db->set('package_id', $this->input->post('package')); 
							$this->db->set('activated_date',date('Y-m-d h:i:s'));
							$this->db->set('status','1');
							$this->db->set('rental',$PDetails->rental);
							$this->db->set('climit',$PDetails->creditlimit);
							$this->db->set('number',$number);
							$this->db->insert('package_activate');
							
							$s=$this->db->query("SELECT * FROM package_feature where package_id=".$this->input->post('package')." and feature_id!=0 ");
							if($s->num_rows()>0){
								foreach($s->result_array() as $rs){
										$bid='0';
										$this->db->query("REPLACE INTO  business_packageaddons set bid='".$bid."',package_id='".$this->input->post('package')."',feature_id='".$rs['feature_id']."',startdate='".date('Y-m-d h:i:s')."',number='".$number."'");
								}
							}
						}
						$j++;
				 }
				 $sum=$this->input->post('prinum')+$j;
				 $this->admin_activitylog(0,"New Pri Numbers Inserted from".$this->input->post('prinum')." to". $sum);
				 return true;
			 }else{
						$gid=$this->db->query("SELECT COALESCE(MAX(`id`),0)+1 as id FROM prilist")->row()->id;
					 	$this->db->set('id',$gid); 
					 	$this->db->set('prinumber', $this->input->post('prinum')); 
						$this->db->insert('prilist');
				 return true;
			 }
			
			
		}else{
			
			return false;
		}
		
	}
	function admin_blacklistnumber(){
		$id=$this->db->query("SELECT COALESCE(MAX(`id`),0)+1 as id FROM blocknumbers")->row()->id;
		$this->db->set('id',$id);
		$this->db->set('number',$this->input->post('blacklist'));
		$this->db->insert("blocknumbers");
		$this->admin_activitylog('0',$this->input->post('blacklist')." Number has added to Blacklist");
		return true;
	}
	function blocknumber_list($ofset='0',$limit='20'){
		$q=' WHERE 1';$s='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		
		$s = ($this->session->userdata('search'))?$this->session->userdata('search'):'';
		
		$q.= (isset($s['blnumber']) && $s['blnumber']!='')?" AND number like '%".$s['blnumber']."%'":"";
		
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS * from blocknumbers  $q LIMIT $ofset,$limit
									   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function get_blocknumber($id){
		$sql=$this->db->query("select * from blocknumbers where id=$id")->row()->number;
		return $sql;
		
	}
	function blacknumberexists($str){
		$sql=$this->db->query("select * from blocknumbers where number='$str'");
		if($sql->num_rows()>0){
			return "exists";
		}else{
			return "available";
		}
	}
	function Delete_blocknumber($id){
		$this->admin_activitylog('0',$this->get_blocknumber($id)." Number has deleted from Blacklist");
		$s=$this->db->query("DELETE FROM blocknumbers where id=$id");
		return true;
	}
	function AdminLogInfo($ofset='0',$limit='20'){
		
		$q='where 1';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['uname']) && $s['uname']!='')?" and u.username like '%".$s['uname']."%'":"";
		$q.=(isset($s['businessname']) && $s['businessname']!='')?" and b.businessname like '%".$s['businessname']."%'":"";
		$q.=(isset($s['fdate']) && $s['fdate']!='')?" and date(a.date_time)>='".$s['fdate']."'":"";
		$q.=(isset($s['tdate']) && $s['tdate']!='')?" and date(a.date_time)<='".$s['tdate']."'":"";
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS a.sno,a.Ipaddress,a.action,a.date_time,u.username,b.businessname from admin_activitylog a
									  LEFT JOIN master_admin u on a.uid=u.uid 
									  LEFT JOIN business b on b.bid=a.bid $q ORDER BY a.sno desc LIMIT $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;	
	}
	function admin_activitylog($bid,$action){
		$gid=$this->db->query("SELECT COALESCE(MAX(`sno`),0)+1 as id FROM admin_activitylog")->row()->id;
		$this->db->set('sno',$gid); 
		$this->db->set('bid', $bid); 
		$this->db->set('uid', $this->session->userdata('uid')); 
		$this->db->set('action', $action); 
		$this->db->set('Ipaddress',$_SERVER['REMOTE_ADDR']); 
		$this->db->insert("admin_activitylog");	
	}
	function Domain_Check(){
		$id=$this->input->post('partner_id');
		$query1=($id!="")?" and partner_id!='".$id."'":"";
		$domain_name=$this->input->post('domain');
		$domain_email=$this->input->post('email');
		$q=$this->db->query("SELECT * FROM partner where domain_name='".$domain_name."' and email='".$domain_email."'$query1");
		if($q->num_rows()==0){
			
			$q1=$this->db->query("SELECT * FROM partner where domain_name='".$domain_name."'$query1");
			if($q1->num_rows()>0){
				return "domainname";
			}else{
				$q2=$this->db->query("SELECT * FROM partner where email='".$domain_email."'$query1");
					if($q2->num_rows()>0){
						return "partneremail";
					}else{
						return "Availble";
					}
				}
			
		}else{
			return "domainname and partneremail";
		}
	}
	Function AddPartner($id=''){
		
		$addressproff=($this->input->post('addressproof'))?"1":"0";
		
		if(isset($_FILES['logo']['name']) && $_FILES['logo']['size']>0){
				$uploads_dir = $this->config->item('upload_path');
				$tmp_name = $_FILES['logo']['tmp_name'];
				$name = rand().$_FILES['logo']['name'];
				move_uploaded_file($tmp_name,$uploads_dir."/".$name);
			}else{
				if($id!=""){
					$res=$this->get_PartnerValues($id);
					$name=$res[0]['logo'];
				 }else{
					 $name='';
				 }	
			}
		if(isset($_FILES['photocopy']['name']) && $_FILES['photocopy']['size']>0){
				$uploads_dir = $this->config->item('upload_path');
				$tmp_name = $_FILES['photocopy']['tmp_name'];
				$photocopy = rand().$_FILES['photocopy']['name'];
				move_uploaded_file($tmp_name,$uploads_dir."/".$photocopy);
			}else{
				if($id!=""){
					$res=$this->get_PartnerValues($id);
					$photocopy=$res[0]['photocopy'];
				 }else{
					 $photocopy='';
				 }	
			}
			$this->db->set('firstname',$this->input->post('firstname'));
			$this->db->set('lastname',$this->input->post('lastname'));
			$this->db->set('companyname',$this->input->post('companyname'));
			$this->db->set('mobilenumber',$this->input->post('mobilenumber'));
			$this->db->set('companyphone',$this->input->post('companyphone'));
			$this->db->set('industry',$this->input->post('industry'));
			$this->db->set('email',$this->input->post('partneremail'));
			if($id==""){
			$this->db->set('password',md5($this->input->post('password')));
			}
			$this->db->set('address',$this->input->post('address'));
			$this->db->set('city',$this->input->post('city'));
			$this->db->set('state',$this->input->post('state'));
			$this->db->set('pincode',$this->input->post('pincode'));
			$this->db->set('pancard',$this->input->post('pancard'));
			$this->db->set('photocopy',$photocopy);
			$this->db->set('bank',$this->input->post('bank'));
			$this->db->set('bankaddress',$this->input->post('bankaddress'));
			$this->db->set('addressproof',$addressproff);
			$this->db->set('accno',$this->input->post('accno'));
			$this->db->set('domain_name',$this->input->post('domainname'));
			$this->db->set('logo',$name);
			if($id!=""){
				$this->db->where('partner_id',$id);
				$this->db->update('partner');
				$this->admin_activitylog('0',$this->input->post('firstname')." partner Information Updated");
				$d=$id;
				$sql=$this->db->query("delete from partner_feature where partner_id=".$d);
				foreach($_POST['featureconfig'] as $key=>$val){
					$sql="REPLACE INTO partner_feature set partner_id='".$d."',feature_id='".$key."'";
					$this->db->query($sql);		
					}
				return true;	
			}else{
				$this->db->set('status',"0");
				$partner_id=$this->db->query("SELECT COALESCE(MAX(`partner_id`),0)+1 as id FROM partner")->row()->id;
				$this->db->set('partner_id',$partner_id);
				$this->db->insert("partner");
				$this->admin_activitylog('0',$this->input->post('firstname')." New Partner Created");
	
				
				$link=base_url()."Masteradmin/partnerAccept/".base64_encode($partner_id)."/".base64_encode($this->input->post('password'));
				$link1=base_url()."Masteradmin/partnerDenied/".base64_encode($partner_id)."/".base64_encode($this->input->post('password'));
				$message_body="Hi ".$this->input->post('firstname')."<br/><br/>
					You have been Added as a partner to MCube<br/><br/>
					if you think  this is a mistake <a href='".$link1."'>Click</a> here 
					otherwise <a href='".$link."'>Click</a> here to confirm
					<br/><br/><br/><br/>
				Regards<br/>
				MCube Team";
				
				$to  = $this->input->post('partneremail');
				$subject = 'Confirmation Mail for accepting  as a Partner to MCube';
				//~ $from='MCube <noreply@mcube.com>';
				//~ $message = $this->emailmodel->email_header().$message_body.$this->emailmodel->email_footer();
				//~ $headers = 'MIME-Version: 1.0' . "\n";
				//~ $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
				//~ $headers .= 'From:'.$from. "\n";
				//~ mail($to, $subject, $message, $headers);	
				$this->load->library('email');
				$this->email->from('noreply@mcube.com', 'MCube');
				$this->email->to($to);
				$this->email->subject($subject);
				$this->email->message($message_body);
				$this->email->send();	
				
				$d=$partner_id;
				$sql=$this->db->query("delete from partner_feature where partner_id=".$d);
				foreach($_POST['featureconfig'] as $key=>$val){
					$sql="REPLACE INTO partner_feature set partner_id='".$d."',feature_id='".$key."'";
					$this->db->query($sql);		
					}
				return $this->db->insert_id();
			}
			
			
		}
	function partnersList($ofset='0',$limit='20'){
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['partneremail']) && $s['partneremail']!='')?" and email like '%".$s['partneremail']."%'":"";
		$q.=(isset($s['domainname']) && $s['domainname']!='')?" and domain_name like '%".$s['domainname']."%'":"";
		$res=array();

	

		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS * from partner where status!=2 $q 
									   LIMIT $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;	
	}
	function DeletepartnersList($ofset='0',$limit='20'){
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['partneremail']) && $s['partneremail']!='')?" and email like '%".$s['partneremail']."%'":"";
		$q.=(isset($s['domainname']) && $s['domainname']!='')?" and domain_name like '%".$s['domainname']."%'":"";
		$res=array();
		
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS * from partner where status!=0 $q 
									   LIMIT $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;	
	}
	Function get_PartnerValues($id=''){
		if($id!=""){
			$sql=$this->db->query("SELECT * FROM partner WHERE partner_id=".$id);
			return $sql->result_array();
			
		}else{
			return array();
		}
	}
	Function get_Partnerval($id=''){
		if($id!=""){
			$sql=$this->db->query("SELECT * FROM partner WHERE partner_id=".$id);
			return $sql->row();
			
		}else{
			return array();
		}
	}
	function delete_partner($id){
		$this->db->set('status',2);
		$this->db->where('partner_id',$id);
		$this->db->update('partner');
		$part=$this->partner_info($id);
		$this->admin_activitylog('0',$part->firstname." partner is deleted");
		return true;
	}
	function undelete_partner($id){
		$this->db->set('status',1);
		$this->db->where('partner_id',$id);
		$this->db->update('partner');
		return true;
	}
	Function ChangePartnerStatus($partner_id){
		$res=$this->get_PartnerValues($partner_id);
		$status=($res[0]['status']!=0)?0:1;
		$this->db->set('status',$status);
		$this->db->where('partner_id',$partner_id);
		$this->db->update('partner');
		$this->admin_activitylog('0',($status!=0)?$res[0]['firstname'].' partner is enabled':$res[0]['firstname'].' partner is  disabled');
		return $status;
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
	function getpartners(){
		$rows=array();
		$res=$this->db->query("SELECT * from partner where status=1");
		if($res->num_rows()>0){
			foreach($res->result_array() as $r){
				$rows[$r['partner_id']]=$r['firstname'].$r['lastname'];
			}
		}
		return $rows;
	}
	function pritopartner($pri){
		$this->db->set('partner_id',$this->input->post('partner_id'));
		$this->db->where('prinumber',$pri);
		$this->db->update('prilist'); 	
		$part=$this->partner_info($this->input->post('partner_id'));
		$this->admin_activitylog(0,$pri." Assigned to Partner :".$part->firstname);
		return true;
	}
	function del_logo($id){
		$this->db->set('logo','');
		$this->db->where('partner_id',$id);
		$this->db->update('partner');
		return true;
	}
	function del_photocopy($id){
		$this->db->set('photocopy','');
		$this->db->where('partner_id',$id);
		$this->db->update('partner');
		return true;
	}
	function partnerDenied($id,$pass){
		$ids=base64_decode($id);
		$r=$this->db->query("select * from partner where partner_id=$ids and status=0");
		if($r->num_rows()>0){
			$this->db->query("DELETE from partner where partner_id=".$ids);
			return 1;
		}else{
			return 0;
		}
		
	}
	function partneraccept($id,$pass){
		$ids=base64_decode($id);
		$r=$this->db->query("select * from partner where partner_id=$ids and status=0");
		if($r->num_rows()>0){
			$res=$r->row();
			$message_body="Hi ".$res->firstname."<br/>
				Username:".$res->email."<br/>
				Password:".base64_decode($pass)."<br/>
				Regards<br/><br/>
				MCube Team";
			
			$to  = $this->input->post('partneremail');
			$subject = 'Partner Login Deatils';
			$from='MCube <noreply@mcube.com>';
			$message = $this->emailmodel->email_header().$message_body.$this->emailmodel->email_footer();
			//~ $headers = 'MIME-Version: 1.0' . "\n";
			//~ $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
			//~ $headers .= 'From:'.$from. "\n";
			//~ mail($to, $subject, $message, $headers);
			$this->load->library('email');
			$this->email->from('noreply@mcube.com', 'MCube');
			$this->email->to($to);
			$this->email->subject($subject);
			$this->email->message($message);
			$this->email->send();	
				
			$this->db->set("status",1);
			$this->db->where("partner_id",$ids);
			$this->db->update('partner');
			return 1;
		}else{
			return 0;
		}
		
	}
	function partner_features($id){
		
		$res=array();
		if($id!=""){
			$sql=$this->db->query("SELECT feature_id FROM `partner_feature` where partner_id=".$id);
			if($sql->num_rows()>0){
				foreach($sql->result_array() as $ress){
					$res[]=$ress['feature_id'];
				}
			}
		 }	
		return $res;
		
		
	}
	function get_busValues($bid){
		if($bid!=""){
			$sql=$this->db->query("SELECT * FROM business WHERE bid=".$bid);
			return $sql->result_array();
		}else{
			return array();
		}
		
	}
	
	function Listpartners(){
		$res=array();
		$sql=$this->db->query("SELECT * FROM `partner` where status=1");
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $ress){
					$res[$ress['partner_id']]=$ress['firstname'].$ress['lastname'];
				}
		}		
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
			$this->admin_activitylog('0',$this->input->post('login_businessname')." New Business created");
			return true;
		}else{
			$res=$this->commonmodel->register();
	         $this->admin_activitylog('0',$this->input->post('login_businessname')." Business Information Updated");
			return true;
			}
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
	function business_by_id($id){
		$s=$this->db->query("select * from business where bid=".$id. " and status=1 AND domain_id!=0");
		return $s->row();
	}
	function partner_info($p){
		$p=$this->db->query("SELECT * from partner where partner_id=$p");
		return $p->row();
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
						Password:".$newPass."<br/>
						Regards<br/>
						MCube Team";
				$to  = $b->contactemail;
				$subject = 'MCube New Password';
				$from='MCube <noreply@mcube.com>';
				$message = $this->emailmodel->email_header().$message_body.$this->emailmodel->email_footer();
				//~ $headers = 'MIME-Version: 1.0' . "\n";
				//~ $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
				//~ $headers .= 'From:'.$from. "\n";
				//~ mail($to, $subject, $message, $headers);
				$this->load->library('email');
				$this->email->from('noreply@mcube.com', 'MCube');
				$this->email->to($to);
				$this->email->subject($subject);
				$this->email->message($message);
				$this->email->send();	
				
				$this->admin_activitylog(0,"Password reset to business :".$b->businessname);
				return true;
			 }else{
				return false; 
				 
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
	 function addbill_config($bid){
	 //  $ex=explode("-",$this->input->post('billgenerate'));
	  
	  // $this->db->set('billing_cycle ',$this->input->post('paycycle'));
	  // $this->db->set('bill_generate_date',$ex[2]);
	   //$this->db->set('bill_due_date',$this->input->post('duedate'));
	   $this->db->set('discount_type',$this->input->post('distype'));
	  // $this->db->set('rental',$this->input->post('rental'));
	   if($this->input->post('distype')!=2){
			$this->db->set('discount_percentage',$this->input->post('disamount'));
		}else{
			$this->db->set('discount_amount',$this->input->post('disamount'));
			}
	   $this->db->set('taxid',$this->input->post('tax'));
	   if($bid!=""){
		    $this->db->where('bid',$bid);
		   $this->db->update('billconfig');
		   return true;
	   }else{
		    $this->db->set('bid',$this->input->post('businessuser'));
		   $this->db->insert('billconfig');
		   return true;
	   }
	   
   }
	 function generated_bills($ofset='0',$limit='20'){
	   $q='where b.latest=1';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['bname']) && $s['bname']!='')?" and bu.businessname like '%".$s['bname']."%'":"";
		$q.=(isset($s['datefrom']) && $s['datefrom']!='')?" and b.billing_form >='".$s['datefrom']."'":"";
		$q.=(isset($s['dateto']) && $s['dateto']!='')?" and b.billing_to <='".$s['dateto']."'":"";
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS 
									   b.*,bu.businessname FROM bill b 
									   left join business bu on bu.bid=b.bid		
										 $q LIMIT $ofset,$limit
									   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function check_business_user($bid){
		$sql=$this->db->query("SELECT * FROM billconfig where bid=".$bid);
		if($sql->num_rows()>0){
			return 0;
		}else{
			return 1;
		}
	}
	function bill_pdf($bill){
		
		$sql=$this->db->query("SELECT b.*,bu.* from bill b
							   left join business bu on bu.bid=b.bid
								where b.bill_id=".$bill);	
		if($sql->num_rows()>0){
			$res=$sql->row();
			$ss=$this->db->query("select * from bill_detail where bill_id=".$bill);
			
			$tax=$this->db->query("select * from tax where taxid=1")->row()->percentage;
			
			$data['arr']=array('bill_detail'=>$sql,'bill_section'=>$ss,
											'businessname'=>ucfirst($res->businessname),
											 'address'=>ucfirst($res->businessaddress),	
											 'city'=>ucfirst($res->city),	
											 'state'=>ucfirst($res->state),	
											 'zipcode'=>ucfirst($res->zipcode),
											 'bid'=>$res->bid,	
											 'billno'=>$bill,
											 'billgendate'=>$res->bill_generate_date,
											 'billing_period'=> $res->billing_form." to " .$res->billing_to,
											 'due_date'=>$res->due_date);
											 
							//print_r($data);exit;
						
						 $html = $this->load->view('sample', $data, true);
						
			
			$filename=ucfirst(substr($res->businessname,0,4).date('M').date('Y')).".pdf";
			pdf_create1($html, ucfirst(substr($res->businessname,0,4).date('M').date('Y')), $stream=TRUE, 
			$orientation='portrait');
			
		}
			
	}
	function getBilldetails($bill_id){
		 return $this->db->query("SELECT SQL_CALC_FOUND_ROWS 
									   b.*,bu.businessname,bu.bid FROM bill b 
									   left join business bu on bu.bid=b.bid		
										where b.bill_id=$bill_id")->result_array();
	}
	function billpayment($bill_id){
		$i=$this->input->post('payid');
		//ECHO $i;exit;
		$amts=0;
		$tot=$this->db->query("SELECT sum(`payment_amount`) as tot FROM 
			`bill_payment` WHERE bill_id=".$this->input->post('billid'))->row()->tot;
		for($k=0;$k<=$i;$k++){
			$mode=$this->input->post('paymode'.(($k!=0)?$k:''));
			$chequeno_dd=$this->input->post('cheno'.(($k!=0)?$k:''));
			$bank=$this->input->post('bname'.(($k!=0)?$k:''));
			$branch=$this->input->post('brname'.(($k!=0)?$k:''));
			$status=$this->input->post('status'.(($k!=0)?$k:''));
			$amt=$this->input->post('payment'.(($k!=0)?$k:''));
			
			$amts+=$amt;
			$payid=$this->db->query("SELECT COALESCE(MAX(`payment_id`),0)+1 as id FROM bill_payment")->row()->id;
			$this->db->set('payment_id',$payid);
			$this->db->set('bill_id',$this->input->post('billid'));
			$this->db->set('bid',$this->input->post('bid'));
			$this->db->set('payment_amount',$amt);
			$this->db->set('payment_mode',$mode);
			$this->db->set('payment_date',date('Y-m-d'));
			$this->db->set('chequeno_dd',$chequeno_dd);
			$this->db->set('bankname',$bank);
			$this->db->set('branchname',$branch);
			$this->db->set('cheque_date',date('Y-m-d'));
			$this->db->set('status',$status);
			$this->db->insert('bill_payment');
		}
			
			$amts=$tot+$amts;
			$dueamount=$this->input->post('netamt')-$amts;
			$this->db->query("update bill set 
			amount_paid=$amts,due_amount=$dueamount where bill_id 
			=". $this->input->post('billid'));
		
	}
	function paymentsList($bill_id,$ofset='0',$limit='20'){
	   
	   $res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS *	 
		from bill_payment where 
		bill_id=".$bill_id." LIMIT $ofset,$limit
									   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function listbusiness_config($ofset='0',$limit='20'){
	    $q='where 1';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['bname']) && $s['bname']!='')?" and bu.businessname like '%".$s['bname']."%'":"";
	   $res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS b.*,bu.*	 
		from billconfig b 
		left join business bu on bu.bid=b.bid $q LIMIT $ofset,$limit
									   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function editbill($pid){
		return $this->db->query("select * from bill_payment where 
		payment_id=".$pid)->result_array();
		
	}	
	function updatepayment($pid){
		$o=$this->editbill($pid);
		$old=$o[0]['payment_amount'];
		$new=$this->input->post('payment');
		$this->db->query("update bill set amount_paid =amount_paid-$old,due_amount 
		=due_amount+$old where bill_id 
		=".$this->input->post('billid'));
		$this->db->set('payment_amount',$this->input->post('payment'));
		$this->db->set('payment_mode',$this->input->post('paymode'));;
		$this->db->set('chequeno_dd',$this->input->post('cheno'));
		$this->db->set('bankname',$this->input->post('bname'));
		$this->db->set('branchname',$this->input->post('brname'));
		$this->db->where('payment_id',$pid);
		$this->db->update('bill_payment');
		$this->db->query("update bill set amount_paid =amount_paid+$new,due_amount 
		=due_amount-$new where bill_id 
		=".$this->input->post('billid'));
		
	}
	 function deletepayament($bill,$pid){
	   $e=$this->editbill($pid);
	   $old=$e[0]['payment_amount'];
	   $this->db->query("update bill set amount_paid =amount_paid-$old,due_amount 
		=due_amount+$old where bill_id 
		=".$bill);
		$this->db->query("Delete from bill_payment where 
		payment_id=".$pid);
   }
   function getuserbill($bid){
	  $startime=date('Y-m-d',strtotime('-1 month'));
	  $endtime=	date('Y-m-d',strtotime('+31 days',strtotime($startime)));	
	  $this->db->query("DELETE FROM bill where billing_form>='".$startime."' and billing_to<='".date('Y-m-d')."' and bid=".$bid);
	  $this->db->query("DELETE FROM bill_detail where startdate>='".$startime."' and startdate<='".$endtime."' and bid=".$bid);
	  $this->db->query("DELETE FROM bill_detail where startdate>='".$startime."' and startdate<='".$endtime."' and bid=".$bid);
	  $sql=$this->db->query("SELECT * FROM bill_history where startdate>='".$startime."' and startdate<='".$endtime."' and bid=".$bid);
	   $grossamt=0;$disamt=0;$tax=0;$balance=0;
	  if($sql->num_rows()>0){
		 
		  foreach($sql->result_array() as $rows){
				$bds=$this->db->query("SELECT COALESCE(MAX(`bd_id`),0)+1 as id FROM bill_detail")->row()->id;
				$this->db->set('bd_id',$bds);
				$this->db->set('bid',$bid);
				$this->db->set('landingnumber',$rows['landingnumber']);
				$this->db->set('package_name',$rows['package_name']);
				$this->db->set('pulse',$rows['used']);
				$this->db->set('rate',floatval($rows['rate']));
				$this->db->set('totalamount',($rows['call_cost']+$rows['addons_cost']+$rows['rental']));
				$this->db->set('climit',floatval($rows['climit']));
				$this->db->set('rental',floatval($rows['rental']));
				$this->db->set('used',$rows['used']);
				$this->db->set('addons_cost',floatval($rows['addons_cost']));
				$this->db->set('call_cost',$rows['call_cost']);
				$this->db->set('startdate',$startime);
				$this->db->set('old',$rows['old']);
				$this->db->insert('bill_detail');
				$grossamt+=$rows['call_cost']+$rows['addons_cost']+$rows['rental'];
			}
	 }
			$tax=$this->db->query("select * from tax where taxid=1")->row()->percentage;		
			$taxamt=($tax/100);
			$net_amt=$grossamt+($taxamt*$grossamt);		
			$billConfig=$this->db->query("SELECT * FROM billconfig WHERE bid=".$bid);
			$aft_dis=0;
			if($billConfig->num_rows()>0){
				$brow=$billConfig->row();
				if($brow->discount_type!=1){
					$aft_dis=$net_amt-$brow->discount_amount;		
				}else{
					$dis=($brow->discount_percentage/100);
					$disamt=($net_amt*$dis);
					$aft_dis=$net_amt-$disamt;
				}
			}
			$qa=$this->db->query("select * from bill where bid=".$bid." order by bill_generate_date desc limit 0,1 ");
			if($qa->num_rows()>0){
				$bill=$qa->row();
				$balance=($bill->due_amount!="")?$bill->due_amount:0;
				$this->db->query("update bill set latest=0 where bill_id='".$bill->bill_id."' and bid=".$bid); 	
			}
			
			$billno=$this->db->query("SELECT COALESCE(MAX(`bill_id`),0)+1 as id FROM bill")->row()->id;
			$this->db->set('bill_id',$billno);
			$this->db->set('bid',$bid);
			$this->db->set('bill_generate_date',date('y-m-d'));
			$this->db->set('due_date',date('Y-m-d',strtotime('+20 days')));
			$this->db->set('gross_amount',$grossamt);
			$this->db->set('discount',$disamt);
			$this->db->set('tax',($taxamt*$grossamt));
			$this->db->set('arrear',$balance);	
			$this->db->set('netamount',$aft_dis);
			$this->db->set('billing_form',$startime);
			$this->db->set('billing_to',date('y-m-d'));
			$this->db->set('latest','1');
			$this->db->insert('bill');
			
			$this->db->set('bill_id',$billno);
			$this->db->where('bid',$bid);
			$this->db->where('startdate',$startime);
			$this->db->update('bill_detail');
			$ss=$this->db->query("select * from bill_detail where bill_id=".$billno)->result_array();
			$bu=$this->db->query("select * from business where bid=".$bid)->row_array();
			$bill_section=$this->db->query("SELECT * FROM bill_detail where bid='".$bid."' and startdate='".$startime."'");
			$bill_details=$this->db->query("SELECT * FROM bill where bid='".$bid."' and billing_form='".$startime."'");
			
			$data['arr']=array('bill_detail'=>$bill_details,'bill_section'=>$bill_section,
								'businessname'=>ucfirst($bu['businessname']),
								 'address'=>ucfirst($bu['businessaddress']),	
								 'city'=>ucfirst($bu['city']),	
								 'state'=>ucfirst($bu['state']),	
								 'zipcode'=>ucfirst($bu['zipcode']),
								 'bid'=>$bu['bid'],	
								 'billno'=>$billno,
								 'billgendate'=>date('d/M/Y'),
								 'billing_period'=>$startime. " to " .date('Y-m-d'),
								 'due_date'=>date('Y-m-d',strtotime('+20 days')));
				
			
			 $html = $this->load->view('sample', $data, true);
			 pdf_create1($html, ucfirst(substr($bu['businessname'],0,4).date('M').date('Y')), $stream=TRUE, $orientation='portrait');
			
	 
	}	
	 function get_billconfig($bid){
	   return $this->db->query("select * from billconfig where 
	   bid=".$bid)->row();
	}
	function secondbill($bid){
		 $qa=$this->db->query("select * from bill where bid=".$bid." 
			  and latest=1");
			  $bill=$qa->row();
			  
			  $sql=$this->db->query("SELECT b.*,bu.* from bill b
							   left join business bu on bu.bid=b.bid
								where b.bill_id=".$bill->bill_id);	
			if($sql->num_rows()>0){
				$res=$sql->row();
				$ss=$this->db->query("select * from bill_detail where bill_id=".$bill->bill_id)->result_array();
				
				$prate=$this->db->query("SELECT rate from product_rate where 
				product_id=5 and bid=".$res->bid)->row();
				
				$b_c=$this->db->query("select * from billconfig where bid=".$res->bid)->row();
				
				$sqlc=$this->db->query("SELECT sum(pulse) as pulsecnt from 
				".$res->bid."_callhistory where starttime>='".$res->billing_form."'")->row();
				
				 $tax=$this->db->query("select * from tax where taxid=".$b_c->taxid)->row()->percentage;
				
				$data=array('res'=>$res,'conc'=>$b_c,'rate'=>$prate,'p'=>$sqlc,'tax'=>$tax,'assoc'=>$ss);	
				$html = $this->load->view('pdfreport', $data, true);
				pdf_create1($html, 
				ucfirst(substr($res->businessname,0,4).date('M').date('Y')), 
				$stream=TRUE, $orientation='portrait');
			}
		
	}
	function get_allunconfirmEmployees($bid,$ofset='0',$limit='20'){
	
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['empname']) && $s['empname']!='')?" and empname like '%".$s['empname']."%'":"";
		$q.=(isset($s['empemail']) && $s['empemail']!='')?" and empemail like '%".$s['empemail']."%'":"";
		
		$res=array();
		$res['data']=$this->db->query("select SQL_CALC_FOUND_ROWS * FROM ".$bid."_employee where status=3 $q LIMIT $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;
	}
	function getDetail($itemid='',$bid=''){
                      $sql=$this->db->query("SELECT g.gid,g.bid,g.groupname,g.url,e.empname as eid,g.pincode,g.allgroup,
					  g.misscall,g.timeout,g.connectowner,g.groupkey,g.replyattmsg,g.oncallaction,g.oneditaction,
					  g.record,g.replytocustomer,g.replytoexecutive,g.recordnotice,g.onhangup,g.leadaction,g.supportaction,
					  if(g.connectowner=1,'YES','NO') as connectwner,
					  g.eid as epid,if(g.record=1,'YES','NO') as records,
					  if(g.replytocustomer=1,'YES','NO') as replycus,
					  if(g.replytoexecutive=1,'YES','NO') as replyexe,
					  if(g.pincode=1,'YES','NO') as pcode,
					  p.landingnumber as addnumber,g.prinumber,g.status,g.sameexe,
					  g.keyword,(if(g.primary_rule=0,'All',cr.regionname)) as regionname,
					  g.primary_rule,g.bday,g.hdaytext,g.hdayaudio,g.replymessage,g.greetings,
					  r.rulename as rules,g.rules as rule,g.supportgrp 
					  FROM ".$bid."_groups g 
					  LEFT JOIN group_rules r on g.rules=r.rulesid 
					  LEFT JOIN ".$bid."_employee e on g.eid=e.eid
					  LEFT JOIN prinumber p on g.prinumber=p.number
					  LEFT JOIN ".$bid."_custom_region cr on g.primary_rule=cr.regionid
					  WHERE g.bid='".$bid."' 
					  AND g.gid='".$itemid."'");
					  		
				$rst =$sql->result_array();
			
				return $rst;
}
/*	$q='';
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		
		$q= (isset($s['empname']) && $s['empname']!='')?" AND e.empname like '%".$s['empname']."%'":"";*/
function groupemplist($bid,$gid){
	
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q =(isset($s['empname']) && $s['empname']!='')?" and e.empname like '%".$s['empname']."%'":"";
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS e.empnumber,a.empweight,a.empPriority,
								a.starttime,a.endtime,g.groupname,a.callcounter,e.empname,a.eid,a.gid ,
								if(a.isfailover=1,'yes','no') as failover,a.status,
								if(e.status='0',0,if(e.selfdisable='1','0','1')) as estatus,
								r.regionname as region	
							   FROM ".$bid."_group_emp a
							   LEFT join ".$bid."_groups g ON a.gid=g.gid
							   LEFT join ".$bid."_employee e ON a.eid=e.eid
							   LEFT JOIN ".$bid."_group_region r ON r.gregionid=a.area_code
							   WHERE a.gid=$gid  $q
							   ORDER BY e.empname
							   ")->result_array();
							 //  echo $this->db->last_query();exit;
       $res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}

function addemp_group($bid,$id){
	
		$err=0;
		$rule = $this->db->query("SELECT rules FROM ".$bid."_groups where gid='".$id."'")->row()->rules;

		$cnt = ($rule=='1')
			? $this->db->query("SELECT COALESCE(max(callcounter),0) as cnt FROM ".$bid."_group_emp where gid='".$id."'")->row()->cnt
			: '0';
		 $employee = array();
        $employee = $_POST['emp_ids'];
        print_r($employee);
		foreach($employee as $eids){
	
			$check=$this->db->query("select callid from ".$bid."_group_emp where eid=".$eids." and gid='".$id."'");
			
			if($check->num_rows()==0){
				 $err+1;
				
				$this->db->set('bid', $bid);                       
				$this->db->set('gid', $id);                       
				$this->db->set('eid', $eids); 
				$this->db->set('starttime', $this->input->post('starttime'.$eids));                       
				$this->db->set('endtime', $this->input->post('endtime'.$eids));                       
				$this->db->set('status',1);
				$this->db->set('callcounter',$cnt);
				if($this->input->post('area_code'.$eids)){
					$this->db->set('area_code', $this->input->post('area_code'.$eids)); 
				}
				if($this->input->post('empweight'.$eids)){
				$this->db->set('empweight', $this->input->post('empweight'.$eids)); 
				}
				if($this->input->post('empPriority'.$eids)){
				$this->db->set('empPriority', $this->input->post('empPriority'.$eids)); 
				}
				if($this->input->post('isfailover'.$eids)){
				$this->db->set('isfailover',$this->input->post('isfailover'.$eids));	
				} 
				if($this->input->post('pcode'.$eids)){
				$this->db->set('pincode', $this->input->post('pcode'.$eids)); 
				} 
				$this->db->insert($bid."_group_emp");
				$emp_name=$this->get_empname($eids);
				$gname=$this->db->query("SELECT groupname from 	".$bid."_groups where gid='".$id."'")->row()->groupname;
				$this->auditlog->auditlog_info('Group Employee',$emp_name->empname." added to the group ".$gname);
			}
		}
		if($rule!='1'){
			$query=$this->db->query("update ".$bid."_group_emp set callcounter=0 where gid='".$id."'");
		}
		if($err!=0){
			return 1;
		}
		else{
			return 0;
		}
	}
		function refreshcounter($gid,$bid){
		$sql=$this->db->query("UPDATE ".$bid."_group_emp SET callcounter = 0 WHERE gid=$gid");
		if($this->db->affected_rows() >0){
			$this->auditlog->auditlog_info('Group',$lgid." Call Counter Reset By ".$this->session->userdata('username'));
			return 1;
		}
		else
			return 0;
	}

	
function update_group($bid,$gid){
		$arr=array_keys($_POST);
		$ext='';
		if($_FILES['hdayaudio']['error']==0){
			$ext=pathinfo($_FILES['hdayaudio']['name'],PATHINFO_EXTENSION);
			$newName = "H".date('YmdHis').".".$ext;
			move_uploaded_file($_FILES['hdayaudio']['tmp_name'],$this->config->item('sound_path').$newName);
			$this->db->set('hdayaudio',$newName);
		}
		$ext='';
		if($_FILES['greetings']['error']==0){
			$ext=pathinfo($_FILES['greetings']['name'],PATHINFO_EXTENSION);
			$newName = "G".date('YmdHis').".".$ext;
			move_uploaded_file($_FILES['greetings']['tmp_name'],$this->config->item('sound_path').$newName);
			$this->db->set('greetings',$newName);
		}

		$connectowner=(isset($_POST['connectowner']))?"1":"0";
		$this->db->set('connectowner',$connectowner);
		$rply_check=(isset($_POST['replytocustomer']))?"1":"0";
		$this->db->set('replytocustomer',$rply_check);
		$rply_check1=(isset($_POST['replytoexecutive']))?"1":"0";
		$this->db->set('replytoexecutive',$rply_check1);
		$recordnotice=(isset($_POST['recordnotice']))?"1":"0";
		$this->db->set('recordnotice',$recordnotice);
		$record_conversation=(isset($_POST['record']))?"1":"0";
		$this->db->set('record',$record_conversation);
		$sameexe=(isset($_POST['sameexe']))?"1":"0";
		$this->db->set('sameexe',$sameexe);
		$misscall=(isset($_POST['misscall']))?"1":"0";
		$this->db->set('misscall',$misscall);
		$pincode=(isset($_POST['pincode']))?"1":"0";
		$this->db->set('pincode',$pincode);
		$allgroup=(isset($_POST['allgroup']))?"1":"0";
		$this->db->set('allgroup',$allgroup);
		$this->db->set('timeout',$this->input->post('timeout'));
		$this->db->set('eid',$this->input->post('Groupowner'));
		$this->db->set('leadaction',$this->input->post('leadaction'));
		$this->db->set('supportaction',$this->input->post('supportaction'));
		$this->db->set('replyattmsg',$this->input->post('replyattmsg'));
		$this->db->set('hdaytext',$this->input->post('hdaytext'));
		$this->db->set('keyword',$this->input->post('keyword'));
		$this->db->set('oncallaction',$this->input->post('oncallaction'));
		$this->db->set('oneditaction',$this->input->post('oneditaction'));
		$this->db->set('onhangup',$this->input->post('onhangup'));
		$this->db->set('supportgrp',$this->input->post('supportgrp'));
		if(isset($_POST['bday']))$this->db->set('bday', json_encode($_POST['bday']));
		$groupKey=base64_encode($bid.'_'.$gid);
		$this->db->set('groupkey',$groupKey);
		$this->db->where('gid',$gid);
		$this->db->update($bid."_groups"); 
		$this->db->query("UPDATE ".$bid."_group_emp SET callcounter='0' WHERE gid='".$gid."'");
		return true;
						
	}
	function get_emp_list($bid){
		$res=array();
		$sql=$this->db->query("select a.eid,a.empname,a.empnumber 
							   from ".$bid."_employee a
							   WHERE a.status!='2'
							   ORDER BY a.eid");
		foreach($sql->result_array() as $re)
		$res[$re['eid']]=$re['empname'].' ['.$re['empnumber'].']';
	//echo "<pre>";print_r($res);
		return $res;
	}
		function group_enteremplist($bid,$gid){
		$res=array();
		$res1=$this->db->query("select SQL_CALC_FOUND_ROWS a.empnumber,a.empweight,
								a.starttime,a.endtime,g.groupname,e.empname,a.eid,a.gid ,
								if(a.isfailover=1,'yes','no') as failover,a.status,
								r.regionname as region	
							   from ".$bid."_group_emp a
							   left join ".$bid."_groups g on a.gid=g.gid
							   left join ".$bid."_employee e on a.eid=e.eid
							   LEFT JOIN ".$bid."_group_region r on r.gregionid=a.area_code
							   where a.gid=$gid
							   ");
		if($res1->num_rows()>0){					   
			foreach($res1->result_array() as $row){
				$res[]=$row['eid'];
			}
		}
			//echo "<pre>";print_r($res);exit;
		return $res;
	}
	function editemp_group($bid,$gid){

		$rule = $this->db->query("SELECT rules FROM ".$bid."_groups where gid='".$_POST['gid']."'")->row()->rules;
		$cnt = ($rule=='1')
			? $this->db->query("SELECT COALESCE(max(callcounter),0) as cnt FROM ".$bid."_group_emp where gid='".$_POST['gid']."'")->row()->cnt
			: '0';

		$sql = "UPDATE ".$bid."_group_emp SET
				empweight	= '".(isset($_POST['empweight'])?$_POST['empweight']:0)."'
				,empPriority= '".(isset($_POST['empPriority'])?$_POST['empPriority']:0)."'
				,area_code	= '".(isset($_POST['area_code'])?$_POST['area_code']:0)."'
				,starttime	= '".$_POST['starttime']."'
				,endtime	= '".$_POST['endtime']."'
				,pincode	= '".(isset($_POST['pcode'])?$_POST['pcode']:'')."'
				,callcounter= '".$cnt."'
				,isfailover	= '".(isset($_POST['isfailover'])?$_POST['isfailover']:0)."'
				WHERE gid		= '".$_POST['gid']."'
				AND  eid		= '".$_POST['empid']."'";
		$this->db->query($sql);
		if($rule!='1'){
			$query=$this->db->query("update ".$bid."_group_emp set callcounter=0 where gid='".$_POST['gid']."'");
		}
		$emp_name=$this->get_empname($_POST['empid']);
		$gname=$this->db->query("SELECT groupname from 	".$bid."_groups where gid='".$_POST['gid']."'")->row()->groupname;
		$this->auditlog->auditlog_info('Group Employee',$emp_name->empname." weight changed for the group ".$gname);
		return 1;
	}
function get_group($bid,$gid)
	{	

		$sql=$this->db->query("SELECT p.package_id, c.eid as empcount, a.gid, a.bid, a.eid, a.`groupname` , a.`prinumber` , a.rules
							   FROM ".$bid."_groups a
							   LEFT JOIN prinumbers p ON a.prinumber = p.number
							   LEFT JOIN package c ON p.package_id = c.package_id
							   WHERE a.gid =$gid");
		if($sql->num_rows()>0)
		{
			$res=$sql->result_array();

		}	
		return $res;
	}
    function getGroupEmpDetail($gid,$bid,$eid){

		$res=array();
		$res=$this->db->query("select a.*,e.empname
							   from ".$bid."_group_emp a
							   left join ".$bid."_employee e on a.eid=e.eid
							   where a.gid='".$gid."' and a.eid='".$eid."'")->result_array();
		return $res['0'];
	}
	function get_areacodes($regionid,$bid){
		$res=array();
		$sql=$this->db->query("select * from ".$bid."_group_region WHERE regionid='".$regionid."'");
		$res['0']='[Select Region]';
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $re)
				$res[$re['gregionid']]=$re['regionname'];
		}
		return $res;
	}
  function get_GroupValues($bid,$id){
		if($bid!=""){
			$sql=$this->db->query("SELECT g.*,p.landingnumber as LandingNumber,gr.regionname as Regionname,r.rulename as Rules,e.empname as Groupowner FROM ".$bid."_groups g 
			                       left join prinumbers p on p.number=g.prinumber
			                       left join group_rules r on g.rules=r.rulesid
			                       left join ".$bid."_custom_region gr on gr.regionid=g.primary_rule
			                       left join ".$bid."_employee e on e.eid=g.eid
			                       WHERE g.bid=".$bid." AND g.gid=".$id);
			return $sql->result_array();
		}else{
			return array();
		}
		
	}

    function getAllLandingnum($bid,$ofset='0',$limit='20'){
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q =(isset($s['landingnumber']) && $s['landingnumber']!='')?" and p.landingnumber like '%".$s['landingnumber']."%'":"";
	 
		$res=array();
		$res['data']=$this->db->query("  SELECT  g.gid,g.groupname as GroupName,gr.regionname as Regionname,e.empname as Groupowner,r.rulename
		                                 as Rules,g.keyword,g.primary_rule as region,g.bday,g.connectowner,g.timeout,p.landingnumber as 
		                                 LandingNumber  from ".$bid."_groups g	
									     left join ".$bid."_custom_region gr on gr.regionid=g.primary_rule
									     left join group_rules r on g.rules=r.rulesid
										 left join prinumbers p on p.number=g.prinumber
										 left join ".$bid."_employee e on e.eid=g.eid
										  WHERE g.bid = ".$bid." $q
										  GROUP BY g.gid")->result_array();
	   	$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;

		return $res;
	}
     function RuleList(){

		$res=array();	
		$sql = $this->db->query("SELECT * FROM group_rules");
		  
		 if($sql->num_rows()>0) {
 			$ress=$sql->result_array();
			
			foreach($ress as $rec)
				$res[$rec['rulesid']] = $rec['rulename'];
	    }
	   			
	    return $res;
	}
		function getPriList($bid,$num){
		$res=array(""=>"Select",'0'=>'System');
		$rst=$this->db->query("SELECT p.status,p.number,p.landingnumber from prinumbers p
							   LEFT JOIN landingnumbers l on l.pri=p.pri
							   WHERE p.bid=".$bid)->result_array();
      
		if(count($rst)>0){
			foreach($rst as $re){
				if($re['status']==1|| $re['number']==$num){
					$res[$re['number']]=$re['landingnumber'];
				}
			}
		}

		return $res;
	}
function employeelist($bid){
		$query = "SELECT e.* FROM ".$bid."_employee e
								 LEFT JOIN ".$bid."_group_emp ge on e.eid=ge.eid
								 LEFT JOIN ".$bid."_groups g on g.gid=ge.gid
								 WHERE e.status=1 ";
		
		$query=$this->db->query($query);
	
		if($query->num_rows()>0)
		{
			foreach($query->result_array() as $rt)
			$res[$rt['eid']]=$rt['empname'];
		}

		return $res;
	}

	function PrimaryRuleList($bid){
		
		$res=array();	
		$sql = $this->db->query("SELECT * FROM ".$bid."_custom_region");
		$res['0']='All';
		if($sql->num_rows()>0){
 			$ress=$sql->result_array();			
			foreach($ress as $rec)
				$res[$rec['regionid']] = $rec['regionname'];
	    }		

	    return $res;
	}
	function delete_unconfirmBusinessEmployee($eid,$bid){
		$sql=$this->db->query("DELETE FROM user where eid=".$eid." and bid=".$bid);
		$sql1=$this->db->query("DELETE FROM ".$bid."_employee where eid=".$eid." and status=3");
		return true;
	}
	function addModule($id=''){
		$this->db->set('module_name',$this->input->post('mname'));
		$this->db->set('module_description',$this->input->post('mdesc'));
		if($id!=""){
				$this->db->where('module_id',$id);
				$this->db->update('package_module');
				return true;
		}else{
			$module_id=$this->db->query("SELECT COALESCE(MAX(`module_id`),0)+1 as id FROM package_module")->row()->id;
			$this->db->set('module_id',$module_id);
			$this->db->insert('package_module');
			return true;
		}
	}
	function get_module($id){
		$res=array();
		if($id!=""){
			$r=$this->db->query("SELECT * FROM package_module where module_id=".$id);
			return $r->row();
		}
		else{
			return $res;
		}
		
	}
	function getModuleList($ofset='0',$limit='20'){
		$q='where 1';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['mname']) && $s['mname']!='')?" and module_name like '%".$s['mname']."%'":"";
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM  package_module $q
										LIMIT $ofset,$limit
									   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function getAddons($ofset='0',$limit='20'){
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['fname']) && $s['fname']!='')?" and  feature_name like '%".$s['fname']."%'":"";
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM  features  $q
										LIMIT $ofset,$limit
									   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function modstatus($id){
		$sql = $this->get_module($id);
		if($sql->status=='1'){
			$status=0;
		}else{
			$status=1;
		}	
		$this->db->set('status',$status);
		$this->db->where('module_id',$id);
		$this->db->update('package_module');
		return true;
	}
	function mod_del($id){
		$sql = $this->get_module($id);
		if($sql->status=='3'){
			$status=1;
		}else{
			$status=3;
		}	
		$this->db->set('status',$status);
		$this->db->where('module_id',$id);
		$this->db->update('package_module');
		return true;
	}
	function get_feature($id){
		$res=array();
		if($id!=""){
			$sq=$this->db->query("SELECT * FROM  features where feature_id=".$id);
			return $sq->row();
		}else{
			return $res;
		}
	}
	function addAddon($id){
		$this->db->set('feature_name',$this->input->post('featurename'));
		$this->db->set('rate',$this->input->post('rate'));
	//	$this->db->set('validity',$this->input->post('validity'));
		if($id!=""){
				//$this->db->where('relatedto',$id);
				$this->db->where('feature_id',$id);
				$this->db->update('features');
				return true;
		}else{
			$module_id=$this->db->query("SELECT COALESCE(MAX(`feature_id`),0)+1 as id FROM  features")->row()->id;
			$this->db->set('feature_id',$module_id);
			$this->db->set('relatedto',$id);
			$this->db->insert('features');
			return true;
		}
	}
	function del_addon($mid,$aid){
		$res=$this->db->query("DELETE FROM features WHERE feature_id=".$aid);
		$this->db->query("DELETE FROM package_feature WHERE feature_id=".$aid);
		return true;
	}
	function get_allModules(){
		$res=$this->db->query("SELECT * FROM package_module where status=1");
		return $res->result_array();
	}
	function get_allfeatures(){
		$res=$this->db->query("SELECT * FROM features");
		return $res->result_array();
	}
	function addPackage($pid){
		//print_r($_POST['featureids']);
		//print_r($_POST['moduleids']);exit;
		$this->db->set('packagename',$this->input->post('pname'));
		//$this->db->set('startdate',$this->input->post('sdate'));
		//$this->db->set('endate',$this->input->post('edate'));
		//$this->db->set('validity',$this->input->post('validity'));
		$this->db->set('rental',$this->input->post('rental'));
		$this->db->set('creditlimit',$this->input->post('climit'));
		$this->db->set('freelimit',$this->input->post('flimit'));
		$this->db->set('rpi',$this->input->post('rpi'));
		$this->db->set('eid',$this->input->post('eids'));
		if($pid!=""){
			$this->db->where('package_id',$pid);
			$this->db->update('package');
		}else{
			$pid=$this->db->query("SELECT COALESCE(MAX(`package_id`),0)+1 as id FROM  package")->row()->id;
			$this->db->set('package_id',$pid);
			$this->db->insert('package');
		}
		
		$this->db->query("DELETE FROM package_feature WHERE package_id='".$pid."'");
		for($i=0;$i<sizeof($_POST['moduleids']);$i++){
			 $this->db->query("REPLACE INTO  package_feature set package_id='".$pid."',module_id='".$_POST['moduleids'][$i]."',feature_id='0',plimit=0");
		}
		for($j=0;$j<sizeof($_POST['featureids']);$j++){
			$this->db->query("REPLACE INTO  package_feature set package_id='".$pid."',module_id='0',feature_id='".$_POST['featureids'][$j]."',plimit=0");
	 	}
	}
	function getPackageList($ofset='0',$limit='20'){
		$q='where 1';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['pname']) && $s['pname']!='')?" and  packagename  like '%".$s['pname']."%'":"";
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM package $q LIMIT $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function get_package($pid){
		if($pid!=""){
		$sql=$this->db->query("SELECT * FROM package WHERE package_id=".$pid);
		return $sql->row();
		}else{
			return array();
		}	
	}
	function get_featureaddons($pid,$mid='',$fid){
		if($pid!=""){
			$M="and module_id=".$mid;
			$s=$this->db->query("SELECT * FROM  package_feature WHERE package_id=".$pid." and feature_id='".$fid."'".	$M);
			if($s->num_rows()>0){
			return 1;
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}
	function packagestatus($id){
		$sql = $this->get_package($id);
		if($sql->status=='1'){
			$status=0;
		}else{
			$status=1;
		}	
		$this->db->set('status',$status);
		$this->db->where('package_id',$id);
		$this->db->update('package');
		return true;
	}
	function package_del($id){
		$sql = $this->get_package($id);
		if($sql->status=='3'){
			$status=1;
		}else{
			$status=3;
		}	
		$this->db->set('status',$status);
		$this->db->where('package_id',$id);
		$this->db->update('package');
		return true;
	}
	function get_allpackages(){
		$sql=$this->db->query("select * from package where status=1");
		return $sql->result_array();
	}
	function convert_package($bid,$pid,$number){
		$module_arr=array("1"=>"0","2"=>"1","3"=>"2");
		$s=$this->db->query("select * from prinumbers where number='".$number."' and package_id='".$this->input->post('package')."' and bid='".$bid."'");
		if($s->num_rows()==0){
			$packinfo=$this->get_package($this->input->post('package'));
			$PriDetail=$this->db->query("SELECT * FROM prinumbers where number='".$number."'")->row();
			
			$this->db->set('bid', $bid); 
			$this->db->set('convertby',$this->session->userdata('uid'));
			$this->db->set('package_id', $this->input->post('package')); 
			$this->db->set('activated_date',date('Y-m-d h:i:s'));
			$this->db->set('converfrom',$pid);
			$this->db->set('status','1');
			$this->db->set('used','0');
			$this->db->set('rental',$packinfo->rental);
			$this->db->set('climit',$packinfo->creditlimit);
			$this->db->set('convertnote',$this->input->post('cnote'));
			$this->db->set('activatedby',$this->session->userdata('uid'));
			$this->db->set('number',$number);
			$this->db->insert('package_activate');
		
		
			$this->db->set('convertdate',date('Y-m-d h:i:s'));
			$this->db->set('convertby',$this->session->userdata('uid'));
			$this->db->set('status','0');
			$this->db->set('used',$PriDetail->used);
			$this->db->set('rental',$PriDetail->rental);
			$this->db->set('climit',$PriDetail->climit);
			$this->db->where('bid', $bid); 	
			$this->db->where('package_id', $pid); 
			$this->db->where('number',$number);
			$this->db->update('package_activate');
			
			$this->db->set('package_id', $this->input->post('package')); 
			$this->db->set('climit', $packinfo->creditlimit); 
			$this->db->set('used', '0'); 
			$this->db->set('type', $module_arr[$_POST['module']]); 
			$this->db->set('rental',$packinfo->rental);
			$this->db->where('number',$number); 
			$this->db->where('bid', $bid); 
			$this->db->update('prinumbers');
			
			
		//	echo "DELETE FROM  business_packageaddons where bid='".$bid."' and number='".$number."'";exit;
			$this->db->query("INSERT INTO package_history (bid, package_id,feature_id,startdate,number)
							   SELECT bid, package_id,feature_id,startdate,number 
							   FROM business_packageaddons where bid='".$bid."' and number='".$number."'");
							   
        	$this->db->query("DELETE FROM  business_packageaddons where bid='".$bid."' and number='".$number."'");
			$this->db->query("UPDATE landingnumbers SET status='1',module_id='".$this->input->post('module')."' WHERE number='".$this->input->post('landingnumber')."'");
				$s=$this->db->query("SELECT * FROM package_feature where package_id=".$this->input->post('package')." and feature_id!=0");
				if($s->num_rows()>0){
					foreach($s->result_array() as $rs){
							$this->db->query("REPLACE INTO  business_packageaddons set bid='".$this->input->post('businessuser')."',package_id='".$this->input->post('package')."',feature_id='".$rs['feature_id']."',startdate='".date('Y-m-d h:i:s')."',number='".$number."'");
					}
				}
				if(sizeof($_POST['featureids'])>0){
					for($i=0;$i<sizeof($_POST['featureids']);$i++){
						$this->db->query("REPLACE INTO  business_packageaddons set bid='".$this->input->post('businessuser')."',package_id='0',feature_id='".$_POST['featureids'][$i]."',startdate='".date('Y-m-d h:i:s')."',number='".$number."'");
					}
				}
			return true;
		}else{
			return false;	
		}
	}
	function landingDetails($l){
		$sql=$this->db->query("SELECT module_id from landingnumbers where number='".$l."'");
		if($sql->num_rows()>0){
			return $sql->row()->module_id;
		}else{
			return '';
		}
	}
	function get_baddons($bid,$pid,$number){
		
		$sql=$this->db->query("SELECT * FROM business_packageaddons where bid='".$bid."' and package_id='".$pid."' and number='".$number."'");
		//if($sql->num_rows()>0){
			$sql1=$this->db->query("SELECT * FROM business_packageaddons where bid='".$bid."' and package_id='0' and number='".$number."'");
			if($sql1->num_rows()>0){
				return array_merge($sql->result_array(),$sql1->result_array());
			}elseif($sql->num_rows()>0){
				return $sql->result_array();
			}else{
				return array();
			}
		
	}
	function get_baddons_history($bid,$pid,$number){
		
		$sql=$this->db->query("SELECT * FROM package_history where bid='".$bid."' and package_id='".$pid."' and number='".$number."'");
		//if($sql->num_rows()>0){
			$sql1=$this->db->query("SELECT * FROM package_history where bid='".$bid."' and package_id='0' and number='".$number."'");
			if($sql1->num_rows()>0){
				return array_merge($sql->result_array(),$sql1->result_array());
			}elseif($sql->num_rows()>0){
				return $sql->result_array();
			}else{
				return array();
			}
		
	}
	function feature_name($fid){
		if($fid!=""){
		$sql=$this->db->query("SELECT feature_name from features where feature_id=".$fid);
			if($sql->num_rows()>0){
				$res=$sql->row()->feature_name;
				return $res;
			}else{
				return '';		
			}
		}else{
			return '';
		}	
	}
	function Module_name($fid){
		$sql=$this->db->query("SELECT module_name from package_module where module_id='".$fid."'");
		if($sql->num_rows()>0){
			return $sql->row()->module_name;
		}else{
			return '';
		}
	}
	function getSalesEmp($ofset='0',$limit='20'){
		$q='where 1';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['emp']) && $s['emp']!='')?" and empname like '%".$s['emp']."%'":"";
		$res=array();
		$res['data']=$this->db->query("select SQL_CALC_FOUND_ROWS * from salesemp
										$q
										LIMIT $ofset,$limit
									   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function getadminRoles($ofset='0',$limit='20'){
		$q='where 1';
		$res=array();
		$res['data']=$this->db->query("select SQL_CALC_FOUND_ROWS * from adminroles
										$q
										LIMIT $ofset,$limit
									   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function getadminusers($ofset='0',$limit='20'){
		$q='where 1';
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS u.username,u.name,u.uid,r.rolename,u.status from master_admin u
										left join adminroles r on u.role_id=r.role_id $q
										LIMIT $ofset,$limit
									   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function get_salesEmp($emp){
		if($emp!=""){
			$s=$this->db->query("SELECT * FROM salesemp WHERE id=".$emp)->row();
			return $s;
		}else{
			return array();
		}
	}
	function addsalesEmp($id){
		$this->db->set('empname',$this->input->post('emp'));
		$this->db->set('joined_date',$this->input->post('jdate'));
		$this->db->set('contact',$this->input->post('cnumber'));
		$this->db->set('email',$this->input->post('email'));
		$this->db->set('status','1');
		if($id!=""){
			$this->db->where('id',$id);
			$this->db->update('salesemp');
		}else{
			$id=$this->db->query("SELECT COALESCE(MAX(`id`),0)+1 as id FROM salesemp")->row()->id;
			$this->db->set('id',$id);
			$this->db->insert('salesemp');
		}
		return true;
		
	}
	function salesemp($id){
		$sql = $this->get_salesEmp($id);
		if($sql->status=='1'){
			$status=0;
		}else{
			$status=1;
		}	
		$this->db->set('status',$status);
		$this->db->where('id',$id);
		$this->db->update('salesemp');
		return true;
	}
	function salesemp_del($id){
		$sql = $this->get_salesEmp($id);
		if($sql->status=='3'){
			$status=1;
		}else{
			$status=3;
		}	
		$this->db->set('status',$status);
		$this->db->where('id',$id);
		$this->db->update('salesemp');
		return true;
	}
	function user_role($bid){
		$s=array();
		$sql=$this->db->query("SELECT * FROM ".$bid."_user_role");
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $rows){
				$s[$rows['roleid']]=$rows['rolename'];
			}
		}
		return $s;
	}
	function BillConfig_user($bid){
		$sql=$this->db->query("SELECT * FROM billconfig WHERE bid='".$bid."'");
		if($sql->num_rows()>0){
				return $sql->row();
		}else{
			return array();
		}
		
	}
	function reset_usedLimit($number){
		$Pridetails=$this->getPridetails($number);
		if(($Pridetails->used*100/$Pridetails->climit)>80 ){
			$packInfo=$this->get_package($Pridetails->package_id);
			$nid=$this->db->query("SELECT COALESCE(MAX(`id`),0)+1 as id FROM  number_reset")->row()->id;
			$this->db->set('id',$nid);
			$this->db->set('number',$number);
			$this->db->set('used',$Pridetails->used);
			$this->db->set('resetby',$this->session->userdata('uid'));
			$this->db->set('rdate',date('Y-m-d H:i:s'));
			$this->db->insert('number_reset');
			
									
			$this->db->set('used','0');
			$this->db->where('number',$number);
			$this->db->update('prinumbers');
			
			
			 //~ $res=$this->db->query("SELECT * FROM number_reset where number='".$number."'");
			 //~ if($res->num_rows==0){
				//~ $this->db->set('number',$number);
				//~ $this->db->set('used',$Pridetails->used-$packInfo->freelimit);
				//~ $this->db->insert('number_reset');
			 //~ }else{
				 //~ //$used=$Pridetails->used-$packInfo->freelimit;
				 //~ $used=$Pridetails->used;
				 //~ $this->db->query("UPDATE number_reset set used=used+".$used." where number='".$number."'");
			 //~ }
			//~ $this->db->set('used','0');
			//~ $this->db->where('number',$number);
			//~ $this->db->update('prinumbers');
			 return true;
		}else{
			return false;
		}
	}
	function adminRole($role_id){
		if($role_id!=""){
			$this->db->set('rolename',$this->input->post('rolename'));
			$this->db->where('role_id',$role_id);
			$this->db->update('adminroles');
			
		}else{
			$role_id=$this->db->query("SELECT COALESCE(MAX(`role_id`),0)+1 as id FROM adminroles")->row()->id;
			$this->db->set('rolename',$this->input->post('rolename'));
			$this->db->set('role_id',$role_id);
			$this->db->insert('adminroles');
		}
		$this->db->query("DELETE FROM module_access where role_id='".$role_id."'");
			$b_a_access=(isset($_POST['business_add_access']))?'1':'0';
			$b_e_access=(isset($_POST['business_edit_access']))?'1':'0';
			$b_e_accessv=(isset($_POST['business_view_access']))?'1':'0';
			$b_en_access=(isset($_POST['business_enable_access']))?'1':'0';
			$b_d_access=(isset($_POST['business_delete_access']))?'1':'0';
			
			$p_a_access=(isset($_POST['partner_add_access']))?'1':'0';
			$p_e_access=(isset($_POST['partner_edit_access']))?'1':'0';
			$p_e_accessv=(isset($_POST['partner_view_access']))?'1':'0';
			$p_en_access=(isset($_POST['partner_enable_access']))?'1':'0';
			$p_d_access=(isset($_POST['partner_delete_access']))?'1':'0';
			
			$pk_a_access=(isset($_POST['package_add_access']))?'1':'0';
			$pk_e_access=(isset($_POST['package_edit_access']))?'1':'0';
			$pk_e_accessv=(isset($_POST['package_view_access']))?'1':'0';
			$pk_en_access=(isset($_POST['package_enable_access']))?'1':'0';
			$pk_d_access=(isset($_POST['package_delete_access']))?'1':'0';
			
			$a_a_access=(isset($_POST['addon_add_access']))?'1':'0';
			$a_e_access=(isset($_POST['addon_edit_access']))?'1':'0';
			$a_e_accessv=(isset($_POST['addon_view_access']))?'1':'0';
			$a_en_access=(isset($_POST['addon_enable_access']))?'1':'0';
			$a_d_access=(isset($_POST['addon_delete_access']))?'1':'0';
			
			$n_a_access=(isset($_POST['number_add_access']))?'1':'0';
			$n_e_access=(isset($_POST['number_edit_access']))?'1':'0';
			$n_e_accessv=(isset($_POST['number_view_access']))?'1':'0';
			$n_en_access=(isset($_POST['number_enable_access']))?'1':'0';
			$n_d_access=(isset($_POST['number_delete_access']))?'1':'0';
			
			
			$c_a_acess=(isset($_POST['credit_add_access']))?'1':'0';
			$c_e_acess=(isset($_POST['credit_edit_access']))?'1':'0';
			$c_d_acess=(isset($_POST['credit_delete_access']))?'1':'0';
			$c_v_acess=(isset($_POST['credit_view_access']))?'1':'0';
			$c_en_acess=(isset($_POST['credit_enable_access']))?'1':'0';
			
			
			$this->db->query("REPLACE INTO  module_access set role_id='".$role_id."',module_name='Business',add_access='".$b_a_access."',edit_access='".$b_e_access."',enable_access='".$b_en_access."',delete_access='".$b_d_access."',view_access='".$b_e_accessv."'");
			
			$this->db->query("REPLACE INTO  module_access set role_id='".$role_id."',module_name='Partner',add_access='".$p_a_access."',edit_access='".$p_e_access."',enable_access='".$p_en_access."',delete_access='".$p_d_access."',view_access='".$p_e_accessv."'");
						
			$this->db->query("REPLACE INTO  module_access set role_id='".$role_id."',module_name='Package',add_access='".$pk_a_access."',edit_access='".$pk_e_access."',enable_access='".$pk_en_access."',delete_access='".$pk_d_access."',view_access='".$pk_e_accessv."'");
			
			$this->db->query("REPLACE INTO  module_access set role_id='".$role_id."',module_name='Addon',add_access='".$a_a_access."',edit_access='".$a_e_access."',enable_access='".$a_en_access."',delete_access='".$a_d_access."',view_access='".$a_e_accessv."'");
			
			$this->db->query("REPLACE INTO  module_access set role_id='".$role_id."',module_name='Number',add_access='".$n_a_access."',edit_access='".$n_e_access."',enable_access='".$n_en_access."',delete_access='".$n_d_access."',view_access='".$n_e_accessv."'");
			
			$this->db->query("REPLACE INTO  module_access set role_id='".$role_id."',module_name='Creditassign',add_access='".$c_a_acess."',edit_access='".$c_d_acess."',enable_access='".$c_en_acess."',delete_access='".$c_d_acess."',view_access='".$c_v_acess."'");
	}
	function get_admin_role($role_id){
		$res=$this->db->query("SELECT * FROM adminroles where role_id='".$role_id."'");
		if($res->num_rows()>0){
			return $res->row();
		}else{
			return array();
		}
	}
	function get_module_role($role_id,$module_name){
		$res=$this->db->query("SELECT * FROM  module_access where role_id='".$role_id."' and module_name='".$module_name."'");
		if($res->num_rows()>0){
			return $res->row();
		}else{
			return array();
		}
	}
	function get_roles(){
		$res=$this->db->query("SELECT * FROM adminroles");
		if($res->num_rows()>0){
			$r=array();
			foreach($res->result_array() as $rows){
				$r[$rows['role_id']]=$rows['rolename'];
			}
			return $r;
		}else{
			return array();
		}
	}
	function CheckAdminUser($str){
		$con=($this->uri->segment(3)!="")?' and uid!="'.$this->uri->segment(3).'"':'';
		$res=$this->db->query("select * from master_admin where username='".$str."' $con");
		if($res->num_rows>0){
			return 1;
		}else{
			return 0;
		}
	}
	function addAdminuser($user_id){
		$this->db->set('username',$this->input->post('uname'));
		$this->db->set('name',$this->input->post('name'));
		$this->db->set('role_id',$this->input->post('role_id'));
		$this->db->set('role','0');
		$this->db->set('language','1');
		$this->db->set('status','1');
		if($user_id!=""){
			$this->db->where('uid',$user_id);
			$this->db->update('master_admin');
		}else{
			$uid=$this->db->query("SELECT COALESCE(MAX(`uid`),0)+1 as id FROM master_admin")->row()->id;
			$password="";for($i = 0; $i<=10 ; $i++){$password .= ($i%2==0)? chr(rand(97,122)) : rand(0,9);}
			$this->db->set('password',md5($password));
			$this->db->set('uid',$uid);
			$this->db->insert('master_admin');
			
			$message_body="Hi ".$this->input->post('name')."<br/>
							Password For Master Login :".$password." <br/>
							Regards<br/>
							MCube Team";				
					$to  = $this->input->post('uname'); // note the comma
					$subject = 'MCube Panel Master login Details';
					$from='"MCube" <noreply@mcube.com>';
					$message = $this->emailmodel->email_header().$message_body.$this->emailmodel->email_footer();
					//~ $headers	 = 'MIME-Version: 1.0' . "\n".
						//~ 'Content-type: text/html; charset=iso-8859-1' . "\n".
						//~ 'From:'.$from. "\n" .
						//~ 'Reply-To:"MCube" <support@vmc.in>'."\n" .
						//~ 'X-Mailer: PHP/' . phpversion();
					//~ mail($to, $subject, $message, $headers);
					$this->load->library('email');
					$this->email->from('noreply@mcube.com', 'MCube');
					$this->email->to($to);
					$this->email->subject($subject);
					$this->email->message($message);
					$this->email->send();
			
		}
	}
	function getAdminDetails($user_id){
		$res=$this->db->query("SELECT * FROM master_admin where uid='".$user_id."'");
		if($res->num_rows()>0){
			return $res->row();
		}else{
			return array();
		}
		
	}
	function getLbsnumber($number){
		$res=$this->db->query("SELECT * FROM lbs_numbers where number='".$number."'");
		if($res->num_rows()>0){
			return $res->row();
		}else{
			return array();
		}
		
	}
	function chgAdminstatus($uid){
		$res=$this->getAdminDetails($uid);
		echo $res->status;echo "<br/>";
		$status=($res->status==1)?0:1;
		//echo $status;exit;
		$this->db->set('status',$status);
		$this->db->where('uid',$uid);
		$this->db->update('master_admin');
		return true;
	}
	function get_access_module($role_id,$module,$access){
		$arr=array('0'=>'add_access','1'=>'edit_access','2'=>'delete_access','3'=>'enable_access','4'=>'view_access');
		$sql=$this->db->query("SELECT * FROM module_access where role_id='".$role_id."' and module_name='".$module."'");
		if($sql->num_rows()>0){
			$res=$sql->row();
			return ($res->$arr[$access]!=0)?$res->$arr[$access]:0;
		}else{
			return 0;
		}
	}
	function LastCalls(){
		$sql=$this->db->query("SELECT p.type,b.bid,p.landingnumber,b.businessname FROM prinumbers p
							   left join business b on p.bid=b.bid 
								where p.status=1");
		if($sql->num_rows()>0){
			return $sql->result_array();
		}else{
			return array();
		}						
	}
	function getLastCall($bid,$type){
		switch($type){
			case '0':
					$sql=$this->db->query("select starttime as lastcall from ".$bid."_callhistory order by callid desc limit 0,1");
					if($sql->num_rows()>0){
						$res=$sql->row();
						return $res->lastcall;
					}else{
						return '';
					}
			break;		
			case '1':
					$sql=$this->db->query("select datetime as lastcall from ".$bid."_ivrshistory order by hid desc limit 0,1");
					if($sql->num_rows()>0){
						$res=$sql->row();
						return $res->lastcall;
					}else{
						return '';
					}
			break;
			case '2':
					$sql=$this->db->query("select starttime as lastcall from ".$bid."_pbxreport order by callid desc limit 0,1");
					if($sql->num_rows()>0){
						$res=$sql->row();
						return $res->lastcall;
					}else{
						return '';
					}
			break;
		}
		
	}
	function DemoUsers($type){
		$sql=$this->db->query("SELECT businessname,city,state from business where act='".$type."' order by bid desc limit 0,10");
		if($sql->num_rows()>0){
			return $sql->result_array();
		}else{
			return array();
		}
	}	
	function CalCount($type){
		//$type=1 today
		//$type=2 current week
		//$type=3 current Month
		$callcount=0;
		$sql=$this->db->query("SELECT bid from business where act='0' and status=1");
		if($sql->num_rows()>0){
			$result=$sql->result_array();
			$s=0;$s1=0;$s2=0;
			foreach($result as $brows){
				if($type==1){
					$start_date=date('Y-m-d');
					$end_date=date('Y-m-d');
					$condtion="date(starttime)='".$start_date."'";
					$condtion1="date(datetime)='".$start_date."'";
					$condtion2="date(starttime)='".$start_date."'";
				}else if($type==2){
					
					$start_date=date('Y-m-d');
					$end_date=date('Y-m-d',strtotime('-7 days'));
					$condtion="date(starttime)<='".$start_date."' and date(starttime)>='".$end_date."'";
					$condtion1="date(datetime)<='".$start_date."' and date(datetime)>='".$end_date."'";
					$condtion2="date(starttime)<='".$start_date."' and date(starttime)>='".$end_date."'";
				}else{
					$start_date=date('Y-m-d');
					$end_date=date('Y-m-d',strtotime('-1 month'));
					$condtion="date(starttime)<='".$start_date."' and date(starttime)>='".$end_date."'";
					$condtion1="date(datetime)<='".$start_date."' and date(datetime)>='".$end_date."'";
					$condtion2="date(starttime)<='".$start_date."' and date(starttime)>='".$end_date."'";
				}
				$s=$this->db->query("SELECT count(*) as callcount FROM ".$brows['bid']."_callhistory WHERE $condtion")->row()->callcount; 
				$callcount+=$s;
				$s1=$this->db->query("SELECT count(*) as callcount FROM ".$brows['bid']."_ivrshistory WHERE $condtion1")->row()->callcount; 
				$callcount+=$s1;
				$s2=$this->db->query("SELECT count(*) as callcount FROM ".$brows['bid']."_pbxreport WHERE $condtion2")->row()->callcount; 
				$callcount+=$s2;
			}
		}
		return $callcount;
	}
	function log(){
		$res=$this->db->query("SELECT * FROM admin_activitylog ORDER BY sno desc limit 0,10");
		return $res->result_array();
	}
	function BlackListnumbers(){
		$res=$this->db->query("SELECT * FROM blocknumbers ORDER BY id desc limit 0,10");
		return $res->result_array();
	}
	function Unassigned_numbers(){
		$res=$this->db->query("SELECT * FROM landingnumbers where status=0");
		return $res->result_array();
	}
	function update_business($bid){
		$this->db->set('businessaddress1', $this->input->post('businessaddress1')); 
		$this->db->set('locality', $this->input->post('locality')); 
		$this->db->set('country', $this->input->post('country')); 
		$this->db->set('state', $this->input->post('state')); 
		$this->db->set('city', $this->input->post('city')); 
		$this->db->set('zipcode', $this->input->post('postalcode'));
		$this->db->set('businessname', $this->input->post('businessname')); 
		$this->db->set('contactname', $this->input->post('contactname')); 
		$this->db->set('contactemail', $this->input->post('contactemail')); 	
		$this->db->set('contactphone', $this->input->post('contactphone')); 
		$this->db->set('businessemail', $this->input->post('businessemail')); 
		$this->db->set('businessphone', $this->input->post('businessphone')); 
		$this->db->set('businessaddress', $this->input->post('businessaddress')); 
		$this->db->set('language', $this->input->post('language')); 
		if($this->input->post('parents')!=""){
			$this->db->set('pid', $this->input->post('pids')); 
		}else{
			$this->db->set('pid', 0); 
		}
		$this->db->where('bid',$bid);
		$this->db->update('business');
	}
	
	function AddLbsnumber($number){
		$this->admin_activitylog($this->input->post('number'),"Added to LBS");
		$sql="REPLACE INTO lbs_numbers  set number='".$this->input->post('number')."',status='1'";
		$this->db->query($sql);
	}
	function DdeleteLbs($number){
		$this->admin_activitylog($number,"Deleted from LBS");
		$sql="delete from lbs_numbers where number='".$number."'";
		$this->db->query($sql);
	}
     function ConfirmEmp($eid,$bid){
		$this->load->helper('mcube_helper');
		$sql=$this->db->query("SELECT * from ".$bid."_employee where bid='".$bid."' and eid='".$eid."'");
		$res=$sql->row();
		$dnd = (array)filter_dnd($res->empnumber);
		if($dnd['dnd']==0){
			$this->db->set('status','1');
			$this->db->where('eid',$eid);
			$this->db->update($bid.'_employee');
			if($res->login==1){
				$this->db->set('status','1');
				$this->db->where('eid',$eid);
				$this->db->where('bid',$bid);
				$this->db->update('user');
			}
			
		}else{
			$this->db->set('status','1');
			$this->db->where('eid',$eid);
			$this->db->update($bid.'_employee');
			if($res->login==1){
				$this->db->set('status','1');
				$this->db->where('eid',$eid);
				$this->db->where('bid',$bid);
				$this->db->update('user');
		}
		
	}
}
	function pRiUsage($month,$year){
		$m=($month<10)?'0'.$month:$month;
		$y=($year!='')?$year:date('Y');
		$datefrom=$y.'-'.$m.'-01';
		$daterange=$y.'-'.$m.'-'.$this->daysformonth($month);
		$csv_output = "";
		$header=array("Business Name","Pri Number","Landing Number","Pulse(15 sec)","Pulse(60 sec)","Assigned Date");
		$csv_output .=implode(",",$header)."\n";
		$sql=$this->db->query("SELECT * from business where status=1");
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $row){
				$sqls=$this->db->query("select * from prinumbers where bid='".$row['bid']."'");
				if($sqls->num_rows()>0){
					$i=1;
					foreach($sqls->result_array() as $rs){
						$data = array();
						$sr=$this->db->query("SELECT sum(pulse) as Pulse FROM `".$row['bid']."_callhistory` WHERE  gid='".$rs['associateid']."' and `starttime`>='".$datefrom."' and starttime<='".$daterange."'");
						if($sr->num_rows()>0){
							$r=$sr->row();
							$sec60=round($r->Pulse/60);
							$sec15=round($r->Pulse/15);
							$v='"'.$row['businessname'].'"';
							array_push($data,$v);
							$v='"'.$rs['pri'].'"';
							array_push($data,$v);
							$v='"'.$rs['landingnumber'].'"';
							array_push($data,$v);
							$v='"'.$sec15.'"';
							array_push($data,$v);
							$v='"'.$sec60.'"';
							array_push($data,$v);
							$v='"'.$rs['assigndate'].'"';
							array_push($data,$v);
							$i++;
							
						}
						$csv_output .=implode(",",$data)."\n";
					}
					
				}
			}
		}
		$data_file = 'reports/pulse.csv';
		$fp = fopen($data_file,'w');fwrite($fp,$csv_output);fclose($fp);
		$n='All';
		$message=' Please find the attachment for the '.$this->monthN($month).' '.$y.' of pri usage for pulse (60 sec and 15 sec) ';
		$content=$this->emailmodel->newEmailBody($message,$n);
		$config['protocol'] = 'mail';
		$config['wordwrap'] = FALSE;
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->from('noreply@mcube.com','MCube');
		$this->email->to('raj.m@vmc.in');
		$this->email->bcc('tapan.chatterjee@vmc.in');
		$this->email->subject('Pri Usage for the '.$this->monthN($month).' '.$y);
		$data['mailing']=$message;
		$msg=$this->load->view('sendMail',$data,true);
		$this->email->attach($data_file);
		$this->email->message($this->outlookfilter($msg));
		$this->email->send();
		return true;
	}
	function daysformonth($month){
		$array=array("1"=>"31",
					 "2"=>"28",
					 "3"=>"31",
					 "4"=>"30",
					 "5"=>"31",
					 "6"=>"30",
					 "7"=>"31",
					 "8"=>"31",
					 "9"=>"30",
					 "10"=>"31",
					 "11"=>"30",
					 "12"=>"31");
		return $array[$month];
	}
	function monthN($month){
		$array=array("1"=>"Jan",
					 "2"=>"Feb",
					 "3"=>"Mar",
					 "4"=>"Apr",
					 "5"=>"May",
					 "6"=>"Jun",
					 "7"=>"Jul",
					 "8"=>"Aug",
					 "9"=>"Sep",
					 "10"=>"Oct",
					 "11"=>"Nov",
					 "12"=>"Dec");
		return $array[$month];
	}
	function outlookfilter($text){    
        $text = str_replace("<br />","<br>",$text);
        $text = str_replace("&nbsp;","",$text);
        $text = str_replace("&#39;","'",$text);
        return $text;
	}
	
	/**************  Feedback Categories UI **********************/
	function addFCats($fcat){
		$this->db->set('category',$this->input->post('category'));
		$this->db->set('subcategory',nl2br($this->input->post('subcat')));
		$this->db->set('type',$this->input->post('type'));
		$this->db->set('label',$this->input->post('label'));
		$this->db->set('status','1');
		if($fcat != ""){
			$this->db->where('id',$fcat);
			$this->db->update('feedback');
		}else{
			$id=$this->db->query("SELECT COALESCE(MAX(`id`),0)+1 as id FROM feedback")->row()->id;
			$this->db->set('id',$id);
			$this->db->insert('feedback');
		}
		return 1;
	}
	
	function getFCategories($ofset='0',$limit='20'){
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS id,category,subcategory,type,status,label FROM feedback ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;	
	}
	function chgFCatstatus($id){
		$res=$this->getFCatDetails($id);
		$status=($res->status==1)?0:1;
		$this->db->set('status',$status);
		$this->db->where('id',$id);
		$this->db->update('feedback');
		return true;
	}
	
	function getFCatDetails($id){
		$res = $this->db->query("SELECT * FROM feedback where id='".$id."'");
		if($res->num_rows()>0){
			return $res->row();
		}else{
			return array();
		}
		
	}
	function getFeedbackData($ofset='0',$limit='20'){
		$res = array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM feedbackData LIMIT $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;	
	}
	function emps($bid){
		$sql=$this->db->query("SELECT * FROM ".$bid."_employee where status=1");
		if($sql->num_rows()>0){
			$res=array();
			foreach($sql->result_array() as $row){
				$res[$row['eid']]=$row['empname'];
			}
		}
		return $res;
		
	}
	function empD($eid,$bid){
		$sql=$this->db->query("SELECT * FROM ".$bid."_employee where status=1 and eid='".$eid."'");
		return $sql->row_array();
	}
	function upgBid($res,$pri){
	   switch($res->type){
			case '0':
					$this->db->set('prinumber','');
					$this->db->set('status','0');
					$this->db->where('prinumber',$pri);
					$this->db->update($res->bid.'_groups');
					break;
			case '1':
					$this->db->set('prinumber','');
					$this->db->set('status','0');
					$this->db->where('prinumber',$pri);
					$this->db->update('ivrs');		
				    break;
		   case '2':
					$this->db->set('prinumber','');
					$this->db->set('status','0');
					$this->db->where('prinumber',$pri);
					$this->db->update($res->bid.'_pbx');
					 break;
		   case '4':
					$this->db->set('number','');
					$this->db->set('status','0');
					$this->db->where('number',$pri);
					$this->db->update($res->bid.'_activitygroup');
					 break;
					
		}
	}
	function getReportHistory($ofset='0',$limit='20'){
		$q='where 1';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}/*else{
			$this->session->unset_userdata('search');
		}*/
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['business']) && $s['business']!='')?" and bid ='".$s['business']."'":"";
		$q.=(isset($s['bdate']) && $s['bdate']!='')?" and `Billing Month` ='".date('Y-m',strtotime($s['bdate']))."'":"";
		$res = array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM report_log $q LIMIT $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;	
		
	}
	function update_unassign_number($number){
		
		$this->db->set('number',$_POST['landingnumber']);
		$this->db->set('pri',$_POST['pri']);
		$this->db->set('region',$_POST['region']);
		$this->db->where('number',$number);
		$this->db->update('landingnumbers');
		return true;
	}
	function number_dis($number,$lno){
	 $res=$this->getPridetails($number);
	 $status=($res->status!=0)?'0':'1';
	// echo "UPDATE prinumbers set status='".$status."' where number='".$number."'";exit;
	 $this->db->query("UPDATE prinumbers set status='".$status."' where number='".$number."'");
		$bdetail=$this->get_busValues($res->bid);
		 $message =($status!=1)?$lno ." is disabeld from the client ".$bdetail[0]['businessname']:$lno ." is enabled and assigned to client ".$bdetail[0]['businessname'];
		$body=$this->emailmodel->newEmailBody($message,' All');
		$config['charset']    = 'utf-8';
		$config['newline']    = "\r\n";
		$config['mailtype'] = 'html'; // or html
		$config['validation'] = TRUE; // bool whether to validate email or not      
		$this->email->initialize($config);
		$this->email->from('<noreply@mcube.com>','Mcube');
		$this->email->to('pavan.br@vmc.in,vivek.sinha@vmc.in,raj.m@vmc.in');
		$this->email->cc('sundeep.misra@vmc.in,tapan.chatterjee@vmc.in');
		$this->email->subject($message);
		$this->email->message($body);  
		$this->email->send();
	 return $status;
	}
	function dnd_filter($bid){
		$bteails=$this->get_busValues($bid);
		$dnd=($bteails[0]['dnd_status']!=0)?0:1;
		$this->db->set('dnd_status',$dnd);
		$this->db->where('bid',$bid);
		$this->db->update('business');
		//echo $this->db->last_query();exit;
		
	}
	function fsetup($bid){
		$bteails=$this->get_busValues($bid);
		$dnd=($bteails[0]['followups']!=0)?0:1;
		$this->db->set('followups',$dnd);
		$this->db->where('bid',$bid);
		$this->db->update('business');
		//echo $this->db->last_query();exit;
		
	}
	

	function check_landingkey($key){
		$res=array();
		$sql=$this->db->query("SELECT number FROM prinumbers where landing_key='".$key."'");
		if($sql->num_rows>0)
		{
			return false;
		}
		return true;
	}
	function gen_landingkey(){
		$landingkey = $this->key_rand();
		while(!$this->check_landingkey($landingkey)){
			$landingkey = $this->key_rand();
		}
		return $landingkey ;
		
	}
	function key_rand(){
		//$apisecret = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
		$apisecret = md5(uniqid(rand(), true));
		return $apisecret;
	}
	function getDNDstatus($ofset='0',$limit='20'){
		$q='where 1';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['business']) && $s['business']!='')?" and b.businessname ='".$s['business']."'":"";
		$q.=(isset($s['emp']) && $s['emp']!='')?" and v.ename ='".$s['emp']."'":"";
		$res = array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS v.*,b.businessname FROM verifiedemployee v
									   LEFT JOIN business b on v.bid=b.bid	$q LIMIT $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;	
		
	}
	function MasDel($bid){
		$ssql=$this->db->query("select * from prinumber where bid='".$bid."'");
		$i=0;
		foreach($ssql->result_array() as $res){
		$i++;
			if($i<=10){
				echo $res['number'];
				$this->Prinumber_del($res['number']);
			}
		}
		exit;
	}
	function get_Numberdata($ofset,$limit){
		$q='where 1';$s='';
		if(isset($_POST['update_system'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$res = array();
		$slno=(isset($s['lno']) && $s['lno']!='')?$s['lno']:'';
		$sql=$this->db->query("select bid,associateid from prinumbers where landingnumber='".$slno."'");
		if($sql->num_rows()>0){
			$row=$sql->row();
			$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS date(starttime) as sdate,ceil(pulse/60) as pulse,count(*) as cnt
							from ".$row->bid."_callhistory 
							where date(starttime)>='".$s['dfrom']."' 
								and date(starttime)<='".$s['dto']."' 
								and gid=".$row->associateid."
								GROUP BY date(starttime),ceil(pulse/60) LIMIT $ofset,$limit")->result_array();
			$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
			$res['tcount']=$this->db->query("SELECT count(*) as cnt from ".$row->bid."_callhistory 
							where date(starttime)>='".$s['dfrom']."' 
								and date(starttime)<='".$s['dto']."' 
								and gid=".$row->associateid)->row()->cnt;
			return $res;	
		}
		return $res;
	}
	function supEscConfig($bid){
		$bteails=$this->get_busValues($bid);
		$supEsc=($bteails[0]['supEsc']!=0)?0:1;
		$this->db->set('supEsc',$supEsc);
		$this->db->where('bid',$bid);
		$this->db->update('business');
		//echo $this->db->last_query();exit;
		
	}
	function chkGreetings($pnumber,$type,$bid){
		if($bid != '' && $bid != 0){
			$sql = ''; $rst = '';
			if($type == 0){
				$sql = "SELECT greetings as greetings FROM ".$bid."_groups WHERE prinumber='".$pnumber."' AND status = 1 ";
			}else if($type == 1){
				$sql = "SELECT op.optsound as greetings FROM ".$bid."_ivrs_options op LEFT JOIN ".$bid."_ivrs i ON i.ivrsid = op.ivrsid WHERE i.prinumber='".$pnumber."' AND op.parentopt = 0 AND i.bid='".$bid."' AND i.status = 1 ";
			}else if($type == 2){
				$sql = "SELECT greetings as greetings FROM ".$bid."_pbx WHERE prinumber='".$pnumber."' AND status = 1 ";
			}
			if($sql !=  ''){
				$res = $this->db->query($sql);
				if($res->num_rows() > 0){
					$row = $res->row_array();
					$rst = $row['greetings'];
				}
			}
		}else{
			$rst = '';
		}
		return $rst;
	}
	
	function uploadfile($bid,$number,$type){
		$ext='';
		if($_FILES['greetings']['error']==0){
			$ext=pathinfo($_FILES['greetings']['name'],PATHINFO_EXTENSION); 
		    $newName = "G".date('YmdHis').".".$ext;
			move_uploaded_file($_FILES['greetings']['tmp_name'],$this->config->item('sound_path').$newName);
			 //$this->db->set('greetings',$newName);
		}
		if($type == 0){
			$sql = "UPDATE ".$bid."_groups SET greetings = '".$newName."'  WHERE prinumber='".$number."' AND status = 1 ";
		}else if($type == 1){
	        $sql = "UPDATE ".$bid."_ivrs_options op SET op.optsound = '".$newName."'";
					"INNER JOIN ".$bid."_ivrs i ON i.ivrsid = op.ivrsid
					WHERE i.prinumber='".$number."' AND op.parentopt = 0 AND i.bid='".$bid."' AND i.status = 1";
		}else if($type == 2){
			$sql = "UPDATE ".$bid."_pbx SET greetings = '".$newName."' WHERE prinumber='".$number."' AND status = 1 ";
		}

		 $this->db->query($sql);
				

	}
	
	
	function import($emp){
		$sql = $this->db->query("SELECT * FROM landingnumbers WHERE number ='".$emp['landingnumber']."'");
		if($sql->num_rows() > 0){
			return '0';
		}else{
			$this->db->set('number', $emp['landingnumber']); 
			$this->db->set('pri',$emp['pri']); 
			$this->db->set('region', $emp['region']);
			$this->db->set('status', '0');
			$res = $this->db->insert('landingnumbers');
			if($res == 1){
				
				$PDetails = $this->get_package($this->input->post('package'));
				$number = $this->db->query("SELECT COALESCE(MAX(`number`),0)+1 as id FROM prinumbers")->row()->id;
				if($this->input->post('businessuser')!=""){
					$this->db->set('bid', $this->input->post('businessuser')); 
				}
				$this->db->set('pri',$emp['pri']);
				$this->db->set('landingnumber', $emp['landingnumber']);
				$this->db->set('landing_key', $this->gen_landingkey()); 
				$this->db->set('climit', floatval($PDetails->creditlimit)); 
				$this->db->set('package_id', $this->input->post('package')); 
				$this->db->set('number',$number);
				$this->db->set('climit',$this->input->post('pclimit'));
				$this->db->set('flimit',$this->input->post('pfmins'));
				$this->db->set('assigndate',date('Y-m-d H:i:s'));
				$this->db->set('rental',$this->input->post('prental'));
				$this->db->set('rpi',$this->input->post('prate'));
				$this->db->set('ntype',$this->input->post('ntype'));
				$this->db->set('svdate',$this->input->post('svdate'));
				$this->db->set('ownership',$this->input->post('owner'));
				$this->db->set('payment_term',$this->input->post('pterm'));
				//$this->db->set('poolid', $this->input->post('poolid')); 
				$this->db->set('sms_limit',$this->input->post('slimit'));
				$this->db->set('parallel_limit',$this->input->post('plimit'));
				//$this->db->set('support',$this->input->post('support'));
				$this->db->insert('prinumbers');
				if($this->input->post('businessuser')!=""){
					$this->db->set('bid', $this->input->post('businessuser')); 
				}
				$this->db->set('package_id', $this->input->post('package')); 
				$this->db->set('activated_date',date('Y-m-d h:i:s'));
				$this->db->set('status','1');
				$this->db->set('rental',$PDetails->rental);
				$this->db->set('climit',$PDetails->creditlimit);
				$this->db->set('number',$number);
				$this->db->set('activatedby',$this->session->userdata('uid'));
				$this->db->insert('package_activate');
				if($this->input->post('businessuser')!=""){
					$this->admin_activitylog($this->input->post('businessuser'),$this->input->post('pri')." allotment changed");
				}
				$this->db->query("UPDATE landingnumbers SET status='1',module_id='".$this->input->post('module')."' WHERE number='".$emp['landingnumber']."'");
				$s=$this->db->query("SELECT * FROM package_feature where package_id=".$this->input->post('package')." and feature_id!=0 ");
				if($s->num_rows()>0){
					foreach($s->result_array() as $rs){
							$bid=($this->input->post('businessuser')!="")?$this->input->post('businessuser'):'0';
							$this->db->query("REPLACE INTO  business_packageaddons set bid='".$bid."',package_id='".$this->input->post('package')."',feature_id='".$rs['feature_id']."',startdate='".date('Y-m-d h:i:s')."',number='".$number."'");
					}
				}
			}
			return '1';
		}
		
	}

	/**************  Feedback Categories UI END **********************/
}

/* end of model*/
