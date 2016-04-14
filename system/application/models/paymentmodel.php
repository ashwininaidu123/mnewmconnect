<?php
class paymentmodel extends Model {
	var $data,$partner_id=null;
    function paymentmodel(){
        parent::Model();
        $this->load->model('commonmodel');
        $this->load->model('auditlog');
        $this->load->model('emailmodel');
        $this->load->plugin('to_pdf');
		//$this->load->model('partnermodel','pmodel');
        $this->partner_id=$this->session->userdata('partner_id');
    }
   function addbill_config(){
	   $ex=explode("-",$this->input->post('billgenerate'));
	   $this->db->set('bid',$this->input->post('businessuser'));
	   $this->db->set('billing_cycle ',$this->input->post('paycycle'));
	   $this->db->set('bill_generate_date',$ex[2]);
	   $this->db->set('bill_due_date',$this->input->post('duedate'));
	   $this->db->set('discount_type',$this->input->post('distype'));
	   $this->db->set('rental',$this->input->post('rental'));
	   if($this->input->post('distype')!=2){
			$this->db->set('discount_percentage',$this->input->post('disamount'));
		}else{
			$this->db->set('discount_amount',$this->input->post('disamount'));
			}
	   $this->db->set('taxid',$this->input->post('tax'));
	   $this->db->insert('billconfig');
	   return true;
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
   function generated_bills($ofset='0',$limit='20'){
	   $q='';
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
										where bu.domain_id=".$this->session->userdata('partner_id') ." $q LIMIT $ofset,$limit
									   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function bill_pdf($bill){
		
		$sql=$this->db->query("SELECT b.*,bu.* from bill b
							   left join business bu on bu.bid=b.bid
								where b.bill_id=".$bill);	
		if($sql->num_rows()>0){
			$res=$sql->row();
			$ss=$this->db->query("select * from bill_detail where bill_id=".$bill)->result_array();
			
			$prate=$this->db->query("SELECT rate from product_rate where 
			product_id=5 and bid=".$res->bid)->row();
			
			$b_c=$this->db->query("select * from billconfig where bid=".$res->bid)->row();
			
			$sqlc=$this->db->query("SELECT sum(pulse) as pulsecnt from 
			".$res->bid."_callhistory where starttime>='".$res->billing_form."'")->row();
			
			$tax=$this->db->query("select * from tax where taxid=".$b_c->taxid)->row()->percentage;
			
			$data=array('res'=>$res,'conc'=>$b_c,'rate'=>$prate,'p'=>$sqlc,'tax'=>$tax,'assoc'=>$ss);	
			$html = $this->load->view('pdfreport', $data, true);
			$filename=ucfirst(substr($res->businessname,0,4).date('M').date('Y')).".pdf";
			$message_body="Hi Dinesh
				<br/>
						PFA
				
				
				";
				$message= $this->emailmodel->email_header().$message_body.$this->emailmodel->email_footer();
			pdf_create1($html, ucfirst(substr($res->businessname,0,4).date('M').date('Y')), $stream=TRUE, 
			$orientation='portrait');
			
		}
			
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
	function getBilldetails($bill_id){
		 return $this->db->query("SELECT SQL_CALC_FOUND_ROWS 
									   b.*,bu.businessname,bu.bid FROM bill b 
									   left join business bu on bu.bid=b.bid		
										where b.bill_id=$bill_id and bu.domain_id=".$this->session->userdata('partner_id'))->result_array();
		}
	function editbill($pid){
		return $this->db->query("select * from bill_payment where 
		payment_id=".$pid)->result_array();
		
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
   function get_billconfig($bid){
	   return $this->db->query("select * from billconfig where 
	   bid=".$bid)->row();
   }
    function getuserbill($bid){
	   $today=date('d');
	   $rs=$this->get_billconfig($bid);
	   if(!empty($rs)){
		  if($today==$rs->bill_generate_date){
			  
			  $tax=$this->db->query("select * from tax where taxid=".$rs->taxid)->row()->percentage;
			  $endtime=date('Y-m-d');
				$qa=$this->db->query("select * from bill where bid=".$bid." order by bill_generate_date desc limit 0,1 ");
					if($qa->num_rows()>0){
						$bill=$qa->row();
						$balance=($bill->due_amount!="")?$bill->due_amount:0;
					}
				if($rs->billing_cycle==1){						
					$startime=date('Y-m-d',strtotime('-1 month'));
				}else if($rs->billing_cycle==3){
					$startime=date('Y-m-d',strtotime('-3 months'));
				}else if($rs->billing_cycle==6){
					$startime=date('Y-m-d',strtotime('-6 months'));
				}else{
					$startime=date('Y-m-d',strtotime('-1 year'));
				}
				$sqlc=$this->db->query("SELECT COALESCE(sum(pulse),0) as pulsecnt from ".$bid."_callhistory where starttime>='".$startime."'")->row();
				$prate=$this->db->query("SELECT rate from product_rate where product_id=5 and bid=".$bid)->row();
				
				$billno=$this->db->query("SELECT COALESCE(MAX(`bill_id`),0)+1 as id FROM bill")->row()->id;
				$net_amount=$sqlc->pulsecnt*$prate->rate;	
				$taxamt=($tax/100);
				$disper=($rs->discount_type!=2)?$rs->discount_percentage:'';
				$disamt=($rs->discount_type!=1)?$rs->discount_amount:'';
					$dis= ($rs->discount_type==1) ? $rs->discount_percentage/100*$net_amount : $rs->discount_amount;
					$net_amt=$net_amount-$dis;
					$net_amt=$net_amt+$rs->rental;
					$net_amt=$net_amt+($taxamt*$net_amt);
						$date=date('Y-m-d');
						//echo "select * from bill where bill_generate_date='".$date."'";exit;
						if($this->db->query("select * from bill where bill_generate_date='".$date."'")->num_rows()==0){
						$this->db->set('bill_id',$billno);
						$this->db->set('bid',$bid);
						$this->db->set('bill_generate_date',date('y-m-d'));
						$this->db->set('due_date',date('Y-m-d',strtotime('+'.$rs->bill_due_date.' days')));
						$this->db->set('gross_amount',$sqlc->pulsecnt*$prate->rate);
						$this->db->set('discount',($rs->discount_type==1)?$dis:$rs->discount_amount);
						$this->db->set('tax',$taxamt*($sqlc->pulsecnt*$prate->rate));
						$this->db->set('netamount',$net_amt);
						$this->db->set('billing_form',$startime);
						$this->db->set('billing_to',date('y-m-d'));
						$this->db->set('latest','1');
						$this->db->insert('bill');
						$groups=$this->db->query("SELECT g.groupname, l.landingnumber,g.gid
										  FROM ".$bid."_groups g
										  LEFT JOIN prinumber l ON l.number = g.prinumber
										  WHERE g.status =1");
										  
							if($groups->num_rows()>0){
								
								foreach($groups->result_array() as $perg){
										$g_pulse=$this->db->query("SELECT COALESCE(sum(pulse),0) as pulsecnt from ".$bid."_callhistory where starttime>='".$startime."' and gid=".$perg['gid'])->row();
										$g_rate=$g_pulse->pulsecnt*$prate->rate;
										$bds=$this->db->query("SELECT COALESCE(MAX(`bd_id`),0)+1 as id FROM bill_detail")->row()->id;
										$this->db->set('bd_id',$bds);
										$this->db->set('bill_id',$billno);
										$this->db->set('landingnumber',$perg['landingnumber']);
										$this->db->set('pulse',($g_pulse->pulsecnt!="NULL")?$g_pulse->pulsecnt:'0');
										$this->db->set('rate',$prate->rate);
										$this->db->set('totalamount',$g_rate);
										$this->db->insert('bill_detail');
									}
									
							}	
						$ss=$this->db->query("select * from bill_detail where bill_id=".$billno)->result_array();
						$bu=$this->get_busValues($bid);
						$data['arr']=array('pulse'=>$sqlc,'rate'=>$prate,'tax'=>$tax,'balanace'=>(!empty($bill))?$balance:0,'billc'=>$rs,'rental'=>$rs->rental,
											'businessname'=>ucfirst($bu[0]['businessname']),
											 'address'=>ucfirst($bu[0]['businessaddress']),	
											 'city'=>ucfirst($bu[0]['city']),	
											 'state'=>ucfirst($bu[0]['state']),	
											 'zipcode'=>ucfirst($bu[0]['zipcode']),
											 'bid'=>$bu[0]['bid'],	
											 'billno'=>$billno,
											 'billgendate'=>date('d/M/Y'),
											 'billing_period'=>$startime. " to " .date('Y-m-d'),
											 'due_date'=>date('Y-m-d',strtotime('+'.$rs->bill_due_date.' days')									),
											  'bill_detail'=>$ss
											);
										
						$html = $this->load->view('sample', $data, true);
						if(!empty($bill)){
							$this->db->query("update bill set latest=0 where bill_id=".$bill->bill_id);
						}
						pdf_create($html, ucfirst(substr($bu[0]['businessname'],0,4).date('M').date('Y')), $stream=TRUE, $orientation='portrait');
						$filename=ucfirst(substr($bu[0]['businessname'],0,4).date('M').date('Y'));
						$message_body="Hi ".$bu[0]['businessname']."<br/>Please Find the attachment ";
						$message= $this->emailmodel->email_header().$message_body.$this->emailmodel->email_footer();
						$subject="MCube Payment Due";
						//$file="/home/mcube/pdffiles/".$filename.".pdf";
						$file=$this->config->item('pdf_files').$filename.".pdf";
						$file_size = filesize($file);
						$handle = fopen($file, "r");
						$content = fread($handle, $file_size);
						fclose($handle);
						$content = @chunk_split(base64_encode($content));
						$uid = md5(uniqid(time()));
						$name = $filename.".pdf";
						$from="noreply@vmc.in";
						$header = "From: MCube <".$from.">\n";
						$header .= "Reply-To: MCube <support@vmc.in>\n";
						$header .= "MIME-Version: 1.0\n";
						$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\n\n";
						$header .= "This is a multi-part message in MIME format.\n";
						$header .= "--".$uid."\n";
						$header .= "Content-type:text/plain; charset=iso-8859-1\n";
						$header .= "Content-Transfer-Encoding: 7bit\n\n";
						$header .= $message."\n\n";
						$header .= "--".$uid."\n";
						$header .= "Content-Type: application/octet-stream; name=\"".$name."\"\n"; // use different content types here
						$header .= "Content-Transfer-Encoding: base64\n";
						$header .= "Content-Disposition: attachment; filename=\"".$name."\"\n\n";
						$header .= $content."\n\n";
						$header .= "--".$uid."--";
						mail("tapan.chatterjee@vmc.in", $subject, $message, $headers);
						unlink($file);
						}else{
						$this->secondbill($bid);
						}
				
				
				
				
				
				
				
		  }else{
				$this->secondbill($bid);
			}
		}else{
			return false;
		}
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
}
/* end of model*/
