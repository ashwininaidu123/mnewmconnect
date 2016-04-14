<?php
class Pollmodel extends Model {
	var $data;
    function Pollmodel(){
        parent::Model();
        $this->load->model('auditlog');
        $this->load->model('emailmodel');
        $this->load->model('commonmodel');
        $this->load->model('ivrsmodel');
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
	function pollconfig(){
		$poll_id=$this->db->query("SELECT COALESCE(MAX(`poll_id`),0)+1 as id FROM ".$this->session->userdata('bid')."_poll")->row()->id;
		$this->db->set('poll_id',$poll_id);
		$this->db->set('prinumber',$this->input->post('pri'));
		$this->db->set('poll_title',$this->input->post('ptitle'));
		$this->db->set('startdate',$this->input->post('stime'));
		$this->db->set('end_date',$this->input->post('etime'));
		$this->db->set('poll_type',$this->input->post('ptype'));
		$this->db->set('status','1');
		$this->db->insert($this->session->userdata('bid')."_poll");
		$this->updatePri($this->input->post('pri'),1,$this->session->userdata('bid'),4,$poll_id);
		return $poll_id;
	}
	function polldetails($poll_id){
		$res=array();
		if($poll_id!=""){
			$s=$this->db->query("select * from ".$this->session->userdata('bid')."_poll where poll_id=".$poll_id);
			if($s->num_rows()>0){
				return $s->row();
			}else{
				return $res;
			}
		}else{
				return $res;
		}
	}
	function poll_options($poll_id,$option){
			$err=0;
			$o=($option!=1)?'pri':'option';
			for($i=0;$i<=$this->input->post('st');$i++){
				$option_id=$this->db->query("SELECT COALESCE(MAX(`option_id`),0)+1 as id FROM ".$this->session->userdata('bid')."_polloptions")->row()->id;
				$k=($i==0)?'':$i;
				
				if($option!=1){
					$rss=$this->db->query("select * from prinumbers where number=".$_POST[$o.$k]." and status=0");
					if($rss->num_rows()>0){
						if(isset($_POST[$o.$k])){
							$this->db->set('optionkey',$_POST[$o.$k]);
							$this->db->set('optionval',$_POST['pollentry'.$k]);
						}else{
							$i=$i+1;
							$this->db->set('optionkey',$_POST[$o.$i]);
							$this->db->set('optionval',$_POST['pollentry'.$i]);
						}
						$this->db->set('option_id',$option_id);
						$this->db->set('poll_id',$poll_id);
						$this->db->set('status','1');
						$this->db->insert($this->session->userdata('bid')."_polloptions");
						$this->updatePri((isset($_POST[$o.$k]))?$_POST[$o.$k]:$_POST[$o.$i],1,$this->session->userdata('bid'),4,$poll_id);
						
					}else{
						$err++;
					}
					
				}else{
					$qa=($k=="")?$_POST[$o.$k]:$_POST[$o.$i];
					$rss=$this->db->query("select * from ".$this->session->userdata('bid')."_polloptions where poll_id=".$poll_id." and optionkey=".$qa);
					if($rss->num_rows()==0){
					if(isset($_POST[$o.$k])){
							$this->db->set('optionkey',$_POST[$o.$k]);
							$this->db->set('optionval',$_POST['pollentry'.$k]);
						}else{
							$i=$i+1;
							$this->db->set('optionkey',$_POST[$o.$i]);
							$this->db->set('optionval',$_POST['pollentry'.$i]);
						}
							$this->db->set('option_id',$option_id);
							$this->db->set('poll_id',$poll_id);
							$this->db->set('status','1');
							
							$this->db->insert($this->session->userdata('bid')."_polloptions");
						//$this->updatePri((isset($_POST[$o.$k]))?$_POST[$o.$k]:$_POST[$o.$i],1,$this->session->userdata('bid'),4,$poll_id);
					}else{
						$err++;
					}
				}
				
			
			}
		   return $err;
		
	}
	function listpoll_options($poll_id){		
		$res=array();
		$p=$this->polldetails($poll_id);
		if($p->poll_type!=2){
			$res['data']=$this->db->query("SELECT * FROM ".$this->session->userdata('bid')."_polloptions where poll_id=".$poll_id)->result_array();
		}else{
			$res['data']=$this->db->query("SELECT opt.*,p.landingnumber as optionkey FROM ".$this->session->userdata('bid')."_polloptions opt
			left join prinumbers p on p.number=opt.optionkey where p.associateid=".$poll_id)->result_array();
		}
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;
	}
	function listpoll($ofset,$limit){
		$res=array();
		$q='where 1';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
			$q.=(isset($s['pollname']) && $s['pollname']!='')?" and poll_title like '%".$s['pollname']."%'":"";
			$q.=(isset($s['polltype']) && $s['polltype']!='')?" and poll_type like '%".$s['polltype']."%'":"";
		
		$res['data']=$this->db->query("SELECT * FROM ".$this->session->userdata('bid')."_poll $q")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;
	}
	function landing_number($id,$type){
		$res=array('0'=>'System');
		
		$sql=$this->db->query("select * from prinumbers where bid=".$this->session->userdata('bid')." and status=0")->result_array();
		foreach($sql as $re){
			$res[$re['number']]=$re['landingnumber'];
		}
		if($id!=""){
			$ses=$this->db->query("SELECT * FROM prinumbers where associateid=".$id." and bid=".$this->session->userdata('bid')." and type=$type");
			$se=$ses->row();
			if($ses->num_rows()>0){
				$res[$se->number]=$se->landingnumber;
			}else{
				$ses=$this->db->query("SELECT * FROM prinumbers where number=".$id." and bid=".$this->session->userdata('bid')." and type=$type");
				$se=$ses->row();
				if($ses->num_rows()>0){
				$res[$se->number]=$se->landingnumber;
				}
			}	
		}
		return $res;
		
	}
	function update_poll($pollid){
		if($this->input->post('ptype')!=2){
			
			if($this->input->post('old_pri')!=$this->input->post('pri')){
				$this->updatePri($this->input->post('old_pri'),0,$this->session->userdata('bid'),0,0);
				$this->updatePri($this->input->post('pri'),1,$this->session->userdata('bid'),4,$pollid);
				$this->db->set('prinumber',$this->input->post('pri'));
			}
			$this->db->set('poll_type ',$this->input->post('ptype'));
			$this->db->set('poll_title',$this->input->post('ptitle'));
			$this->db->set('startdate',$this->input->post('stime'));
			$this->db->set('end_date',$this->input->post('etime'));
			$this->db->where('poll_id',$pollid);
			$this->db->update($this->session->userdata('bid')."_poll");
			if($this->input->post('numbertype')!=$this->input->post('ptype')){
				 $rs=$this->db->query("SELECT * FROM ".$this->session->userdata('bid')."_polloptions WHERE poll_id=".$pollid);
				 foreach($rs as $r){
					$this->updatePri($r['optionkey'],0,$this->session->userdata('bid'),0,$pollid); 
				 }
				 $this->db->query("Delete from ".$this->session->userdata('bid')."_polloptions where poll_id=".$pollid);
			}
		}else{
			if($this->input->post('numbertype')!=$this->input->post('ptype')){
				 $this->db->query("Delete from ".$this->session->userdata('bid')."_polloptions where poll_id=".$pollid);
			}
			$this->updatePri($this->input->post('old_pri'),0,$this->session->userdata('bid'),0,0);
			$this->db->set('prinumber','');
			$this->db->set('poll_title',$this->input->post('ptitle'));
			$this->db->set('startdate',$this->input->post('stime'));
			$this->db->set('end_date',$this->input->post('etime'));
			$this->db->set('poll_type',$this->input->post('ptype'));
			$this->db->where('poll_id',$pollid);
			$this->db->update($this->session->userdata('bid')."_poll");
		}
		return true;
	}
	function gt_options($poll){
		$res=array();
		$rs=$this->db->query("SELECT * FROM ".$this->session->userdata('bid')."_polloptions where poll_id=".$poll);
		if($rs->num_rows()>0){
			return $rs->result_array();
		}else{
			return $res;
		}
	}
	function del_polloptions($poll_id,$option_id){
		$poll=$this->polldetails($poll_id);
		if($poll->poll_type!=1){
			$rs=$this->db->query("select optionkey from ".$this->session->userdata('bid')."_polloptions where option_id=".$option_id)->row()->optionkey;
			$this->updatePri($rs,0,$this->session->userdata('bid'),0,0);
			$this->db->query("DELETE FROM ".$this->session->userdata('bid')."_polloptions where option_id=".$option_id);
		}else{
			$this->db->query("DELETE FROM ".$this->session->userdata('bid')."_polloptions where option_id=".$option_id);
			}
		return true;	
	}
	function update_polloptions($poll_id){
		$err=0;
		$poll=$this->polldetails($poll_id);
		$rs=$this->gt_options($poll_id);
		if(!empty($rs)){
			foreach($rs as $opr){
				if($poll->poll_type!=1){
					
						if($opr['optionkey']!=$_POST['pri'.$opr['option_id']]){
							$rss=$this->db->query("select * from prinumbers where number=".$_POST['pri'.$opr['option_id']]." and status=0");
							if($rss->num_rows()>0){
								$this->updatePri($opr['optionkey'],0,$this->session->userdata('bid'),0,0);
								$this->updatePri($_POST['pri'.$opr['option_id']],1,$this->session->userdata('bid'),4,$poll_id);
								$this->db->set('optionkey',$_POST['pri'.$opr['option_id']]);
							}else{
								$err++;
							}
						}else{
							
							$this->db->set('optionkey',$_POST['pri'.$opr['option_id']]);
						}
						$this->db->set('optionval',$_POST['pollentry'.$opr['option_id']]);	
						$this->db->where('option_id',$opr['option_id']);
						$this->db->update($this->session->userdata('bid').'_polloptions');
					}else{
						$this->db->set('optionkey',$_POST['option'.$opr['option_id']]);
						$this->db->set('optionval',$_POST['pollentry'.$opr['option_id']]);	
						$this->db->where('option_id',$opr['option_id']);
						$this->db->update($this->session->userdata('bid').'_polloptions');
					}	
					
			}
		}
		if($this->input->post('newitems')!=""){
			
			for($i=1;$i<=$this->input->post('newitems');$i++){
				$option_id=$this->db->query("SELECT COALESCE(MAX(`option_id`),0)+1 as id FROM ".$this->session->userdata('bid')."_polloptions")->row()->id;
				
				if($this->input->post('poll_t')!=1){
					$rss=$this->db->query("select * from prinumbers where number=".$_POST['newpri'.$i]." and status=0");
					if($rss->num_rows()>0){
							$this->updatePri($_POST['newpri'.$i],1,$this->session->userdata('bid'),4,$poll_id);
							$this->db->set('optionkey',$_POST['newpri'.$i]);
							$this->db->set('option_id',$option_id);
							$this->db->set('poll_id',$poll_id);
							$this->db->set('optionval',$_POST['newpollentry'.$i]);	
							$this->db->set('status','1');
							$this->db->insert($this->session->userdata('bid')."_polloptions");
					}else{
						$err++;
					}
						
				}else{
					$rss=$this->db->query("select * from ".$this->session->userdata('bid')."_polloptions where poll_id=".$poll_id." and optionkey=".$_POST['newoption'.$i]);
					if($rss->num_rows()==0){
					$this->db->set('optionkey',$_POST['newoption'.$i]);
					$this->db->set('option_id',$option_id);
					$this->db->set('poll_id',$poll_id);
					$this->db->set('optionval',$_POST['newpollentry'.$i]);	
					$this->db->set('status','1');
					$this->db->insert($this->session->userdata('bid')."_polloptions");
					}else{
						$err++;
					}
						
				}
					
			}
			
			
		}

			return $err;;
	}
	function CHangeStatus($poll_id){
		$poll=$this->polldetails($poll_id);
		$status=($poll->status==0)?'1':'0';
		$this->db->set('status',$status);
		$this->db->where('poll_id',$poll_id);
		$this->db->update($this->session->userdata('bid')."_poll");
		return $status;
	}	
	function updatePri($pri,$status,$bid,$type,$pollid){
		$this->db->set('type',$type);
		$this->db->set('status',$status);
		$this->db->set('bid',$bid);
		$this->db->set('associateid',$pollid);
		$this->db->where('number',$pri);
		$this->db->update('prinumbers');
		//echo $this->db->last_query();exit;
	}
	function getpoll_count($poll_id){
		
	   $res=$this->db->query("SELECT opt.poll_id, opt.optionval, COALESCE(count(p.polloption ),0) AS cnt
FROM ".$this->session->userdata('bid')."_polloptions opt left join ".$this->session->userdata('bid')."_pollreport p on p.polloption=opt.option_id where opt.poll_id=".$poll_id." group by opt.option_id order by opt.optionkey");
	
		return $res->result_array();
	}	
	function getReport_Poll($pollid,$ofset='0',$limit='20'){
			$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['starttime']) && $s['starttime']!='')?" and p.datetime>='".$s['starttime']."'":"";
		$q.=(isset($s['endtime']) && $s['endtime']!='')?" and p.datetime<='".$s['endtime']."'":"";
		$q.=(isset($s['option']) && $s['option']!='')?" and opt.optionkey='".$s['option']."'":"";
		$res=array();
		$res['data']=$this->db->query(" SELECT SQL_CALC_FOUND_ROWS opt.poll_id, opt.optionval,p.datetime,opt.optionkey,p.callfrom
FROM ".$this->session->userdata('bid')."_pollreport p left join ".$this->session->userdata('bid')."_polloptions opt on opt.option_id=p.polloption
where opt.poll_id=".$pollid." $q LIMIT $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function getNumber($number){
			return $res=$this->db->query("SELECT landingnumber  FROM prinumbers where number=".$number)->row()->landingnumber;
	}
}	
	
	
	/* end of poll model */
