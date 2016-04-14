<?php
Class cronjobmodel extends Model
{
	function cronjobmodel(){
		parent::Model();
		$this->load->model('emailmodel');
		$this->load->plugin('to_pdf');
	}
	Function Business_User(){
		$query=$this->db->query("SELECT * FROM business where status=1");
		if($query->num_rows()>0){
			$rows=$query->result_array();
			foreach($rows as $row){
				$bid=$row['bid'];
				$ALLemp=$this->GETEmployees($bid);
			 }
		}
	}
	Function GETEmployees($bid){
		$date=date('Y-m-d');
		$sql=$this->db->query("SELECT e.*,if(g.gid is null,0,1) as groupowner FROM ".$bid."_employee e
								LEFT JOIN ".$bid."_groups g on e.eid=g.eid
								WHERE e.status='1'
								AND e.login='1'
								GROUP BY e.eid");
		if($sql->num_rows()>0){
			$r=$sql->result_array();
			foreach($r as  $rows){
					$sql= "SELECT h.gid,h.eid,h.callfrom,h.starttime,h.endtime,if(h.pulse>0,ceil(h.pulse/60),h.pulse) as pulse,g.groupname,e.empname FROM ".$bid."_callhistory h
						LEFT JOIN ".$bid."_groups g on h.gid=g.gid
						LEFT JOIN ".$bid."_employee e on h.eid=e.eid
						WHERE date(h.starttime)='$date'";
					if($rows['eid']=="1"){
						$sql.="";
						
					}elseif($rows['groupowner']){
						$sql.= " AND h.gid in (SELECT gid FROM ".$bid."_groups WHERE eid='".$rows['eid']."')";
					}else{
						$sql.= " AND h.eid='".$rows['eid']."'";
					}	
					$qus=$this->db->query($sql);
					$message="Hi ".$rows['empname']."<br/>";
						
						$message.="<table cellpadding='3' cellspacing='3'  border='1' width='500'>
									<tr>
										<th>GroupName</th>
										<th>Employee</th>
										<th>CallFrom </th>
										<th>StartTime </th>
										<th>EndTime </th>
										<th>Pulse</th>
									</tr>";
					if($qus->num_rows()>0){
						foreach($qus as $Callre){
								$message.="<tr>
											<td>".$callre['groupname']."</td>
											<td>".$callre['empname']."</td>
											<td>".$callre['callfrom']."</td>
											<td>".$callre['starttime']."</td>
											<td>".$callre['endtime']."</td>
											<td>".$callre['pulse']."</td>
										</tr>";
								}
								$message.="</table>";
					}else{
						$message.="<tr>
								<td colspan='6'>No Calls</td>
							</tr>";
							$message.="</table>";
					}
					$this->SendMailToUser($rows['empemail'],$message);
				}
			}
			
		}
	Function SendMailToUser($email,$message){
				$to=$email;
				$subject="CallTrack Report ";
				$emailbody=$this->emailmodel->email_header().$message.$this->emailmodel->email_footer();
				$headers  = 'MIME-Version: 1.0' . "\n";
				$headers .= 'Content-type: text/html; charset=UTF-8' . "\n";
				$headers .= 'To: <'.$email.'>' . "\n";
				$headers .= 'From: MCube <noreply@mcube.com>' . "\n";
				$headers .= 'BCC:tapan.chatterjee@indopia.com' . "\n";
				$mail = mail($to, $subject, $emailbody, $headers);
				//MCubeMail($to,$subject,$emailbody);
		}
	function getAllBusiness(){
		$sql = "SELECT * FROM business WHERE status='1'";
		$rst = $this->db->query($sql)->result_array();
		$rec = array();
		if(count($rst)>0){
			foreach($rst as $r){
				$rec[$r['bid']] = $r;
			}
		}
		return $rec;
	}
    function getEmp($bid){
		$sql = "SELECT * FROM ".$bid."_employee WHERE status='1' AND login='1'";
		$rst = $this->db->query($sql);
		return $rec = ($rst->num_rows()>0) ? $rst->result_array() : array();
	}
	
	function allAdmins($bid){
		$sql = "SELECT empemail FROM ".$bid."_employee WHERE status='1' AND roleid='1'";
		$rst = $this->db->query($sql);
		$rec = ($rst->num_rows()>0) ? $rst->result_array() : array();
		return $rec;
	}
	function empRole($roleid,$bid){
		$detail['role'] = (array)$this->db
					->query("SELECT * FROM ".$bid."_user_role
							WHERE roleid='".$roleid."'
							AND bid='".$bid."'")
					->row();
		$modules = $this->db
					->query("SELECT m.modid,m.modname,m.moddesc,COALESCE(o.opt_add,0) as opt_add,
							COALESCE(o.opt_view,0) as opt_view,COALESCE(opt_delete,0) as opt_delete FROM module m
							LEFT JOIN (SELECT * FROM ".$bid."_role_mod_opt
							WHERE roleid='".$roleid."'
							AND bid='".$bid."') as o
							ON m.modid=o.modid")
					->result_array();
		foreach ($modules as $mod)$detail['modules'][$mod['modid']] = $mod;
		$detail['system'] = $this->db
					->query("SELECT a.*,f.fieldname FROM ".$bid."_role_access a
							LEFT JOIN systemfields f on a.fieldid=f.fieldid
							WHERE a.roleid='".$roleid."'
							AND a.bid='".$bid."'
							AND a.fieldtype='s'")
					->result_array();
		$detail['custom'] = $this->db
					->query("SELECT * FROM ".$bid."_role_access
							WHERE roleid='".$roleid."'
							AND bid='".$bid."'
							AND fieldtype='c'")
					->result_array();
		return $detail;
	}
	
    function getCalls($bid=''){
		$bid = ($bid!='') ? $bid : $this->session->userdata('bid');
		$sql = "SELECT callid FROM ".$bid."_callhistory WHERE date(starttime)=SUBDATE( CURDATE(), INTERVAL 1 DAY) ORDER BY starttime DESC";
		$rst = $this->db->query($sql);
		return $rec = ($rst->num_rows()>0) ? $rst->result_array() : array();
	}
	function getCalls_email($bid='',$date){
		$bid = ($bid!='') ? $bid : $this->session->userdata('bid');
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		$q = ($type == '0')? " AND pulse > '0'": "";
		$sql = "SELECT callid FROM ".$bid."_callhistory WHERE date(starttime)='".$date."' ".$q." ORDER BY starttime DESC";
		$rst = $this->db->query($sql);
		return $rec = ($rst->num_rows()>0) ? $rst->result_array() : array();
	}
	function Billgenerate(){
	   $today=date('d');
	   $busers=$this->CM->getAllBusiness();
	   if(!empty($busers)){
		   $balance=0;
		   $disper='';
		   $disamt='';$header='';$net_amt=0;$balance=0;$html='';$grossamt=0;
			foreach($busers as $bu){
				
				$sql=$this->db->query("select * from billconfig where bid=".$bu['bid']);
				if($sql->num_rows()>0){
					$rs=$sql->row();
					$tax=$this->db->query("select * from tax where taxid=".$rs->taxid)->row()->percentage;
					if($today==$rs->bill_generate_date){
						$endtime=date('Y-m-d');
						$qa=$this->db->query("select * from bill where bid=".$bu['bid']." order by bill_generate_date desc limit 0,1 ");
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
						//echo "SELECT sum(pulse) as pulsecnt from ".$bu['bid']."_callhistory where starttime>='".$startime."'";
						/**----- calltrack-------*/
						
						$sqlc=$this->db->query("SELECT COALESCE(sum(pulse),0) as pulsecnt from ".$bu['bid']."_callhistory where starttime>='".$startime."'")->row();
						$prate=$this->db->query("SELECT rate from product_rate where product_id=5 and bid=".$bu['bid'])->row();
						/**-----end of calltrack----*/
						/** ------PBx-------*/
						$pbx=$this->db->query("SELECT COALESCE(sum(pulse),0) as pulsecnt from ".$bu['bid']."_pbxreport  where starttime>='".$startime."'")->row();
						$pbxrate=$this->db->query("SELECT rate from product_rate where product_id=3 and bid=".$bu['bid'])->row();
						/** ------End ofPBx-------*/
						/** ------ivrs-------*/
						$ivrs=$this->db->query("SELECT COUNT( * ) AS count FROM ".$bu['bid']."_ivrshistory where datetime>='".$startime."'")->row();   
						$ivrs_rate=$this->db->query("SELECT rate from product_rate where product_id=4 and bid=".$bu['bid'])->row();
						
						/** ------end of ivrs-------*/
						
						
						$billno=$this->db->query("SELECT COALESCE(MAX(`bill_id`),0)+1 as id FROM bill")->row()->id;
						$grossamt=$sqlc->pulsecnt*$prate->rate+$pbx->pulsecnt*$pbxrate->rate+$ivrs->count*$ivrs_rate->rate;
						$net_amt=$sqlc->pulsecnt*$prate->rate+$balance;	
						$taxamt=($tax/100);
						$net_amt=$net_amt+$rs->rental;
						$net_amt=$net_amt+($taxamt*$net_amt);
						$dis= ($rs->discount_type==1) ? $rs->discount_percentage/100*$net_amt:$rs->discount_amount;
						$net_amt=$net_amt-$dis; 

						$date=date('Y-m-d');
						//echo "select * from bill where bill_generate_date='".$date."'";exit;
						if($this->db->query("select * from bill where bid=".$bu['bid']." and  bill_generate_date='".$date."'")->num_rows()==0){
						$this->db->set('bill_id',$billno);
						$this->db->set('bid',$bu['bid']);
						$this->db->set('bill_generate_date',date('y-m-d'));
						$this->db->set('due_date',date('Y-m-d',strtotime('+'.$rs->bill_due_date.' days')));
						$this->db->set('gross_amount',$grossamt);
						$this->db->set('discount',($rs->discount_type==1)?$dis:$rs->discount_amount);
						$this->db->set('tax',$taxamt*($grossamt+$rs->rental));
						if($balance!=0){
						$this->db->set('arrear',$balance);	
						}
						$this->db->set('netamount',$net_amt);
						$this->db->set('billing_form',$startime);
						$this->db->set('billing_to',date('y-m-d'));
						$this->db->set('latest','1');
						$this->db->insert('bill');
						$groups=$this->db->query("SELECT g.groupname, l.landingnumber,g.gid
										  FROM ".$bu['bid']."_groups g
										  LEFT JOIN prinumber l ON l.number = g.prinumber
										  WHERE g.status =1");
							if($groups->num_rows()>0){
								foreach($groups->result_array() as $perg){
										$g_pulse=$this->db->query("SELECT COALESCE(sum(pulse),0) as pulsecnt from ".$bu['bid']."_callhistory where starttime>='".$startime."' and gid=".$perg['gid'])->row();
										$g_rate=$g_pulse->pulsecnt*$prate->rate;
										$bds=$this->db->query("SELECT COALESCE(MAX(`bd_id`),0)+1 as id FROM bill_detail")->row()->id;
										$this->db->set('bd_id',$bds);
										$this->db->set('bill_id',$billno);
										$this->db->set('landingnumber',$perg['landingnumber']);
										$this->db->set('pulse',($g_pulse->pulsecnt!="NULL")?$g_pulse->leads_Contact:'0');
										$this->db->set('rate',$prate->rate);
										$this->db->set('totalamount',$g_rate);
										$this->db->insert('bill_detail');
									}
									
							}	
					/*  pbx */
							$pbx=$this->db->query("SELECT p.title, l.landingnumber,p.pbxid
												 FROM ".$bu['bid']."_pbx p
												 LEFT JOIN prinumber l ON l.number = p.prinumber
												where p.status=1");
							if($pbx->num_rows()>0){
								foreach($pbx->result_array() as $px){
										
										$p_pulse=$this->db->query("SELECT COALESCE(sum(pulse),0) as pulsecnt from ".$bu['bid']."_pbxreport where starttime>='".$startime."' and pbxid=".$px['pbxid'])->row();
										$pb_rate=$p_pulse->pulsecnt*$pbxrate->rate;
										$bds=$this->db->query("SELECT COALESCE(MAX(`bd_id`),0)+1 as id FROM bill_detail")->row()->id;
										$this->db->set('bd_id',$bds);
										$this->db->set('bill_id',$billno);
										$this->db->set('landingnumber',$px['landingnumber']);
										$this->db->set('pulse',($p_pulse->pulsecnt!="0")?$p_pulse->pulsecnt:'0');
										$this->db->set('rate',$pbxrate->rate);
										$this->db->set('totalamount',$pb_rate);
										$this->db->insert('bill_detail');
									}
									
							}	
					/* end */
					
					/* ivrs */
						$ivrs=$this->db->query("SELECT iv.title, l.landingnumber, iv.ivrsid
											FROM ".$bu['bid']."_ivrs iv
											LEFT JOIN prinumber l ON l.number = iv.prinumber
											WHERE iv.status =2 and iv.bid=".$bu['bid']);
							if($ivrs->num_rows()>0){
								foreach($ivrs->result_array() as $ivr){
										
										$iv=$this->db->query("SELECT COUNT( * ) AS count FROM ".$bu['bid']."_ivrshistory where datetime>='".$startime."' and ivrsid=".$ivr['ivrsid'])->row();
										$iv_rate=$iv->count*$ivrs_rate->rate;
										$bds=$this->db->query("SELECT COALESCE(MAX(`bd_id`),0)+1 as id FROM bill_detail")->row()->id;
										$this->db->set('bd_id',$bds);
										$this->db->set('bill_id',$billno);
										$this->db->set('landingnumber',$ivr['landingnumber']);
										$this->db->set('pulse',($iv->count!="0")?$iv->count:'0');
										$this->db->set('rate',$ivrs_rate->rate);
										$this->db->set('totalamount',$iv_rate);
										$this->db->insert('bill_detail');
									}
									
							}
					
					/* end   */

						$ss=$this->db->query("select * from bill_detail where bill_id=".$billno)->result_array();
						
						$data['arr']=array('pulse'=>$sqlc,'rate'=>$prate,'tax'=>$tax,'balanace'=>$balance,'billc'=>$rs,'rental'=>$rs->rental,'pbx'=>$pbx,'pbxr'=>$pbxrate,'ivrs'=>$ivrs,'ivrsr'=>$ivrs_rate,
											'businessname'=>ucfirst($bu['businessname']),
											 'address'=>ucfirst($bu['businessaddress']),	
											 'city'=>ucfirst($bu['city']),	
											 'state'=>ucfirst($bu['state']),	
											 'zipcode'=>ucfirst($bu['zipcode']),
											 'bid'=>$bu['bid'],	
											 'billno'=>$billno,
											 'billgendate'=>date('d/M/Y'),
											 'billing_period'=>$startime. " to " .date('Y-m-d'),
											 'due_date'=>date('Y-m-d',strtotime('+'.$rs->bill_due_date.' days')									),
											  'bill_detail'=>$ss
											);
											
						//echo "<br>";
						//print_r($data);
						$html = $this->load->view('sample', $data, true);
						if(!empty($bill)){
							$this->db->query("update bill set latest=0 where bill_id=".$bill->bill_id);
						}

						pdf_create($html, ucfirst(substr($bu['businessname'],0,4).date('M').date('Y')), $stream=TRUE, $orientation='portrait');
						

						$filename=ucfirst(substr($bu['businessname'],0,4).date('M').date('Y'));
						$message_body="Hi ".$bu['businessname']."
							<br/>
									Please Find the attachment ,
							<br/>
									Password to open the attachment ".ucfirst(substr($bu['businessname'],0,4).date('M').date('Y'))."		
									
							";
						$message= $this->emailmodel->email_header().$message_body.$this->emailmodel->email_footer();


						$fromAddr = 'MCube<noreply@mcube.com>'; // the address to show in From field.
						$recipientAddr = 'tapan.chatterjee@vmc.in';
						$subjectStr = 'Bill Generated please pay before due date';
						
						$filePath = $this->config->item('pdf_files').$filename.".pdf";
						$fileName = basename($filePath);
						$fileType = 'text/pdf';
						$mineBoundaryStr='otecuncocehccj8234acnoc231';

$headers= <<<EEEEEEEEEEEEEE
From: $fromAddr
MIME-Version: 1.0
Content-Type: multipart/mixed; boundary="$mineBoundaryStr"

EEEEEEEEEEEEEE;

// Add a multipart boundary above the plain message 
$mailBodyEncodedText = <<<TTTTTTTTTTTTTTTTT
This is a multi-part message in MIME format.

--{$mineBoundaryStr}
Content-type: text/html; charset=iso-8859-1. \n
Content-Transfer-Encoding: quoted-printable

$message

TTTTTTTTTTTTTTTTT;

$file = fopen($filePath,'rb'); 
$data1 = fread($file,filesize($filePath)); 
fclose($file);
$data1 = chunk_split(base64_encode($data1));

// file attachment part
$mailBodyEncodedText .= <<<FFFFFFFFFFFFFFFFFFFFF
--$mineBoundaryStr
Content-Type: $fileType;
 name=$fileName
Content-Disposition: attachment;
 filename="$fileName"
Content-Transfer-Encoding: base64
$data1
--$mineBoundaryStr--

FFFFFFFFFFFFFFFFFFFFF;

if (mail( $recipientAddr , $subjectStr , $mailBodyEncodedText, $headers )) {
  unlink($filePath);
}					

					
						}
					}
				}
			}
		 }
	}
	function NewBilling(){
		   $today=date('d');
		   $startime=date('Y-m-d',strtotime('-1 month'));
		 
		 //  if($today=="18"){
						$tax=$this->db->query("select * from tax where taxid=1")->row()->percentage;
					   $busers=$this->CM->getAllBusiness();
					   if(!empty($busers)){
						   $balance=0;
						   $disper='';
						   $disamt=0;$header='';$net_amt=0;$balance=0;$html='';$grossamt=0;$disamt=0;$aft_dis=0;$totused=0;$costlimit_pb=0;
						   $continue='';
							foreach($busers as $bu){
								 $Bconfig=$this->db->query("SELECT * FROM  billconfig where bid=".$bu['bid']);
								 if($Bconfig->num_rows()>0){
									 
								 }else{
									 
								 }
								//~ 
								$sqls_pb=$this->db->query("SELECT pri.landingnumber,pri.used,pri.number,pri.package_id,
														pac.packagename,pac.rental,pac.freelimit,pac.rpi,pri.climit,
														if(pri.used>=pac.freelimit,(pri.used - pac.freelimit),0) as extuse
														FROM prinumbers pri
														LEFT JOIN package pac on pri.package_id=pac.package_id
														WHERE pri.bid='".$bu['bid']."' AND pri.status='1'");
														
								if($sqls_pb->num_rows()>0){
									$costlimit_pb=0;$addonCost_pb=0;$extraLimit=0;$totused=0;$aft_dis=0;$totused=0;
									foreach($sqls_pb->result_array() as $rows_pb){
										$qs=$this->db->query("SELECT used FROM number_reset WHERE number=".$rows_pb['number']);
											if($qs->num_rows()>0){
												$rqo=$qs->row();
												$extraLimit=$rqo->used;
											}else{
												$extraLimit=0;
											}
										if($rows_pb['extuse']>0){
											$costlimit_pb=($extraLimit>0)?$rows_pb['used']:$rows_pb['extuse'];
										}else{
											$costlimit_pb=0;
										}
										//echo $extraLimit."<br/>".$costlimit_pb."<br/>";
										$totused=$costlimit_pb+$extraLimit;
										//echo $totused."<br/>";
										$costlimit_pb=$totused*$rows_pb['rpi'];
										
										$pack_adons=$this->pack_adons($rows_pb['landingnumber'],$rows_pb['package_id']);
										$addonCost_pb=$this->addOnsCost($rows_pb['number'],$bu['bid'],$pack_adons);
										
										$bds=$this->db->query("SELECT COALESCE(MAX(`bd_id`),0)+1 as id FROM bill_detail")->row()->id;
										
										//echo $packInfo->rental;exit;
										$this->db->set('bd_id',$bds);
										$this->db->set('bid',$bu['bid']);
										//$this->db->set('bill_id',$billno);
										$this->db->set('landingnumber',$rows_pb['landingnumber']);
										$this->db->set('package_name',($rows_pb['packagename']!='')?$rows_pb['packagename']:'');
										$this->db->set('pulse',$rows_pb['used']);
										$this->db->set('rate',floatval($rows_pb['rpi']));
										$this->db->set('totalamount',($costlimit_pb+$addonCost_pb+$rows_pb['rental']));
										$this->db->set('climit',floatval($rows_pb['climit']));
										$this->db->set('rental',floatval($rows_pb['rental']));
										$this->db->set('used',$totused);
										$this->db->set('addons_cost',floatval($addonCost_pb));
										$this->db->set('call_cost',$costlimit_pb);
										$this->db->set('startdate',$startime);
										$this->db->insert('bill_detail');
										$bds1=$this->db->query("SELECT COALESCE(MAX(`bd_id`),0)+1 as id FROM bill_history")->row()->id;
											
										$this->db->set('bd_id',$bds1);
										$this->db->set('bid',$bu['bid']);
										//$this->db->set('bill_id',$billno);
										$this->db->set('landingnumber',$rows_pb['landingnumber']);
										$this->db->set('package_name',($rows_pb['packagename']!='')?$rows_pb['packagename']:'');
										$this->db->set('pulse',$rows_pb['used']);
										$this->db->set('rate',floatval($rows_pb['rpi']));
										$this->db->set('totalamount',($costlimit_pb+$addonCost_pb+$rows_pb['rental']));
										$this->db->set('climit',floatval($rows_pb['climit']));
										$this->db->set('rental',floatval($rows_pb['rental']));
										$this->db->set('used',$totused);
										$this->db->set('addons_cost',floatval($addonCost_pb));
										$this->db->set('call_cost',$costlimit_pb);
										$this->db->set('startdate',$startime);
										$this->db->insert('bill_history');
										
										
										$grossamt+=($costlimit_pb+$addonCost_pb+$rows_pb['rental']);
										
										$this->db->set('used','0');
										$this->db->where('landingnumber',$rows_pb['landingnumber']);
										$this->db->update('prinumbers');
										
										$this->db->set('used','0');
										$this->db->where('number',$rows_pb['number']);
										$this->db->update('number_reset');
										
										
									}//exit;
								}else{
									$grossamt=0;
								}
								/*  Previous Package Details */
							
								$Previous_packs=$this->db->query("SELECT pack.packagename,pack.package_id,pack.freelimit as freeLimit,pact.rental,pact.climit as creditlimit ,pact.number,pack.rpi,pact.used,prin.landingnumber ,prin.pri,if(pact.used>=pack.freelimit,(pact.used - pack.freelimit),0) as extuse  FROM package pack
								LEFT JOIN package_activate pact on pack.package_id=pact.package_id
								LEFT JOIN prinumbers prin on pact.number=prin.number
								where pact.bid='".$bu['bid']."' and pact.status='0' and pact.convertdate>=date('".$startime."') order by pact.convertdate desc limit 0,1");
								if($Previous_packs->num_rows()>0){
									$costlimit_pb=0;$addonCost_pb=0;$aft_dis=0;
									foreach($Previous_packs->result_array() as $rows_pb_old){
										if($rows_pb_old['extuse']>0){
											$costlimit_pb=($rows_pb_old['used']-$rows_pb_old['freeLimit'])*$rows_pb_old['rpi'];
										}
										$pack_adons=$this->pack_adons($rows_pb_old['landingnumber'],$rows_pb_old['package_id']);
										$addonCost_pb=$this->addOnsCost_old($rows_pb_old['number'],$bu['bid'],$pack_adons);
										
										$bds=$this->db->query("SELECT COALESCE(MAX(`bd_id`),0)+1 as id FROM bill_detail")->row()->id;
										
										//echo $packInfo->rental;exit;
										$this->db->set('bd_id',$bds);
										$this->db->set('bid',$bu['bid']);
										//$this->db->set('bill_id',$billno);
										$this->db->set('landingnumber',$rows_pb_old['landingnumber']);
										$this->db->set('package_name',($rows_pb_old['packagename']!='')?$rows_pb_old['packagename']:'');
										$this->db->set('pulse',$rows_pb_old['used']);
										$this->db->set('rate',floatval($rows_pb_old['rpi']));
										$this->db->set('totalamount',($costlimit_pb+$addonCost_pb+$rows_pb_old['rental']));
										$this->db->set('climit',floatval($rows_pb_old['creditlimit']));
										$this->db->set('rental',floatval($rows_pb_old['rental']));
										$this->db->set('used',$rows_pb_old['extuse']);
										$this->db->set('addons_cost',floatval($addonCost_pb));
										$this->db->set('call_cost',$costlimit_pb);
										$this->db->set('startdate',$startime);
										$this->db->set('old','1');
										$this->db->insert('bill_detail');	
										$bds1=$this->db->query("SELECT COALESCE(MAX(`bd_id`),0)+1 as id FROM bill_history")->row()->id;
											
										$this->db->set('bd_id',$bds1);
										$this->db->set('bid',$bu['bid']);
										//$this->db->set('bill_id',$billno);
										$this->db->set('landingnumber',$rows_pb_old['landingnumber']);
										$this->db->set('package_name',($rows_pb_old['packagename']!='')?$rows_pb_old['packagename']:'');
										$this->db->set('pulse',$rows_pb_old['used']);
										$this->db->set('rate',floatval($rows_pb_old['rpi']));
										$this->db->set('totalamount',($costlimit_pb+$addonCost_pb+$rows_pb_old['rental']));
										$this->db->set('climit',floatval($rows_pb_old['creditlimit']));
										$this->db->set('rental',floatval($rows_pb_old['rental']));
										$this->db->set('used',$rows_pb_old['extuse']);
										$this->db->set('addons_cost',floatval($addonCost_pb));
										$this->db->set('call_cost',$costlimit_pb);
										$this->db->set('old','1');
										$this->db->set('startdate',$startime);
										$this->db->insert('bill_history');
										$grossamt+=($costlimit_pb+$addonCost_pb+$rows_pb_old['rental']);
									
										
									}
								}
								/* -------------------end -----------*/
								$qa=$this->db->query("select * from bill where bid=".$bu['bid']." order by bill_generate_date desc limit 0,1 ");
								if($qa->num_rows()>0){
									$bill=$qa->row();
									$balance=($bill->due_amount!="")?$bill->due_amount:0;
									$this->db->query("update bill set latest=0 where bill_id='".$bill->bill_id."' and bid=".$bu['bid']); 	
								}
								$taxamt=($tax/100);
								$net_amt=$grossamt+($taxamt*$grossamt);
								//$aft_dis=0;
								$billConfig=$this->db->query("SELECT * FROM billconfig WHERE bid=".$bu['bid']);
								if($billConfig->num_rows()>0){
										$brow=$billConfig->row();
										if($brow->discount_type!=1){
											$net_amt=$net_amt-$brow->discount_amount;		
										}else{
											$dis=($brow->discount_percentage/100);
											$disamt=($net_amt*$dis);
											$net_amt=$net_amt-$disamt;
										}
								}
								$billno=$this->db->query("SELECT COALESCE(MAX(`bill_id`),0)+1 as id FROM bill")->row()->id;
								$this->db->set('bill_id',$billno);
								$this->db->set('bid',$bu['bid']);
								$this->db->set('bill_generate_date',date('y-m-d'));
								$this->db->set('due_date',date('Y-m-d',strtotime('+20 days')));
								$this->db->set('gross_amount',$grossamt);
								$this->db->set('discount',$disamt);
								$this->db->set('tax',($taxamt*$grossamt));
								$this->db->set('arrear',$balance);	
								$this->db->set('netamount',$net_amt);
								$this->db->set('billing_form',$startime);
								$this->db->set('billing_to',date('y-m-d'));
								$this->db->set('latest','1');
								$this->db->insert('bill');
								
								$this->db->set('bill_id',$billno);
								$this->db->where('bid',$bu['bid']);
								$this->db->where('startdate',$startime);
								$this->db->update('bill_detail');
							
							$ss=$this->db->query("select * from bill_detail where bill_id=".$billno)->result_array();
						
						$bill_section=$this->db->query("SELECT * FROM bill_detail where bid='".$bu['bid']."' and startdate='".$startime."'");
						$bill_details=$this->db->query("SELECT * FROM bill where bid='".$bu['bid']."' and billing_form='".$startime."'");
						$data['arr']=array('bill_detail'=>$bill_details,'bill_section'=>$bill_section,
											'businessname'=>ucfirst($bu['businessname']),
											 'address'=>ucfirst($bu['businessaddress']),	
											 'city'=>ucfirst($bu['city']),	
											 'state'=>ucfirst($bu['state']),	
											 'zipcode'=>ucfirst($bu['zipcode']),
											 'bid'=>$bu['bid'],	
											 'billno'=>$billno,
											 'billgendate'=>date('d/M/Y'),
											 'billing_period'=>$startime,
											 'due_date'=>date('Y-m-d',strtotime('+20 days')));
							
						
						 $html = $this->load->view('sample', $data, true);
						if(!empty($bill)){
							$this->db->query("update bill set latest=0 where bill_id=".$bill->bill_id);
						}

						pdf_create($html, ucfirst(substr($bu['businessname'],0,4).date('M').date('Y')), $stream=TRUE, $orientation='portrait');
						

						$filename=ucfirst(substr($bu['businessname'],0,4).date('M').date('Y'));
						$message_body="Hi ".$bu['businessname']."
							<br/>
									Please Find the attachment ,
							<br/>
									Password to open the attachment ".ucfirst(substr($bu['businessname'],0,4).date('M').date('Y'))."		
									
							";
						$message= $this->emailmodel->email_header().$message_body.$this->emailmodel->email_footer();


						$fromAddr = 'MCube<noreply@mcube.com>'; // the address to show in From field.
						$recipientAddr = 'tapan.chatterjee@vmc.in';
						$subjectStr = 'Bill Generated please pay before due date';
						
						$filePath = $this->config->item('pdf_files').$filename.".pdf";
						$fileName = basename($filePath);
						$fileType = 'text/pdf';
						$mineBoundaryStr='otecuncocehccj8234acnoc231';

$headers= <<<EEEEEEEEEEEEEE
From: $fromAddr
MIME-Version: 1.0
Content-Type: multipart/mixed; boundary="$mineBoundaryStr"

EEEEEEEEEEEEEE;

// Add a multipart boundary above the plain message 
$mailBodyEncodedText = <<<TTTTTTTTTTTTTTTTT
This is a multi-part message in MIME format.

--{$mineBoundaryStr}
Content-type: text/html; charset=iso-8859-1. \n
Content-Transfer-Encoding: quoted-printable

$message

TTTTTTTTTTTTTTTTT;

$file = fopen($filePath,'rb'); 
$data1 = fread($file,filesize($filePath)); 
fclose($file);
$data1 = chunk_split(base64_encode($data1));

// file attachment part
$mailBodyEncodedText .= <<<FFFFFFFFFFFFFFFFFFFFF
--$mineBoundaryStr
Content-Type: $fileType;
 name=$fileName
Content-Disposition: attachment;
 filename="$fileName"
Content-Transfer-Encoding: base64
$data1
--$mineBoundaryStr--

FFFFFFFFFFFFFFFFFFFFF;

//~ if (mail( $recipientAddr , $subjectStr , $mailBodyEncodedText, $headers )) {
  //~ unlink($filePath);
//~ }					
											
	
								
						}
					}	
			//	}	
	}	
	
	function callArchive(){
		$busList = $this->getAllBusiness();
		foreach($busList as $bus){
			$sql = "INSERT INTO ".$bus['bid']."_callarchive (SELECT * FROM `".$bus['bid']."_callhistory` WHERE date(`starttime`)<=DATE_SUB(CURRENT_DATE(),INTERVAL 60 DAY))";
			$this->db->query($sql);
			if($this->db->affected_rows()>0){
				$sql1 = "DELETE FROM `".$bus['bid']."_callhistory` WHERE date(`starttime`)<=DATE_SUB(CURRENT_DATE(),INTERVAL 60 DAY);";
				$this->db->query($sql1);
			}
		}
	}
	function active_landingnumbers(){
		$today=date('Y-m-d H:i:s');
		$busList = $this->getAllBusiness();
		foreach($busList as $bus){
			$sql=$this->db->query("SELECT * FROM ".$bus['bid']."_poll");
			if($sql->num_rows()>0){
				foreach($sql->result_array() as $rows){
					if($rows['poll_type']!=2){
						if($rows['end_date']<=$today){
							$this->updatePri($rows['prinumber'],0,$bus['bid'],0,0);
							}
					}else{
						$reqs=$this->db->query("SELECT * FROM ".$bus['bid']."_polloptions WHERE poll_id=".$rows['poll_id']);
						if($reqs->num_rows()>0){
							  foreach($reqs->result_array() as $row1){
									  if($rows['end_date']<=$today){
										$this->updatePri($row1['optionkey'],0,$bus['bid'],0,0);  
									}
							  }
					     }
					  }
					}
				}
			}
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
	function packageInfo($pack){
		$sql=$this->db->query("SELECT * FROM package where package_id='".$pack."'");
		if($sql->num_rows()>0){
			return $sql->row();
		}else{
			return array();
		}
	}
	function addOnsCost($number,$bid,$padons){
		$sql=$this->db->query("SELECT COALESCE(sum(f.rate),0) as adCost from features f
							   left join business_packageaddons b on f.feature_id=b.feature_id 
								where b.number='".$number."' and b.bid='".$bid."' and f.feature_id NOT IN(".$padons.")")->row()->adCost;
								
		return $sql;
		
	}
	function addOnsCost_old($number,$bid,$padons){
		$sql=$this->db->query("SELECT COALESCE(sum(f.rate),0) as adCost from features f
							   left join package_history b on f.feature_id=b.feature_id 
								where b.number='".$number."' and b.bid='".$bid."' and f.feature_id NOT IN(".$padons.")")->row()->adCost;
								
		return $sql;
		
	}
	function pack_adons($number,$pid){
		$mod_id=$this->db->query("SELECT module_id FROM landingnumbers WHERE number='".$number."'")->row()->module_id;
		
		$addons=$this->db->query("SELECT feature_id FROM package_feature WHERE feature_id!=0 and package_id='".$pid."' and module_id='".$mod_id."'");
		$ads=array();
		foreach($addons->result_array() as $adrows){
			$ads[]="'".$adrows['feature_id']."'";
		}
		return implode(",",$ads);
	}
	function Deactive_numbers(){
		$sqL=$this->db->query("SELECT * FROM business where act=1 AND date(registrationdate)<='".date('Y-m-d')."'");
		if($sqL->num_rows()>0){
			foreach($sqL->result_array() as $rows){
				$s=$this->db->query("SELECT pri.landingnumber,pri.used,pri.number,pri.package_id,
									pac.packagename,pac.rental,pac.freelimit,pac.rpi,pri.climit,
									if(pri.used>=pac.freelimit,(pri.used - pac.freelimit),0) as extuse
									FROM prinumbers pri
									LEFT JOIN package pac on pri.package_id=pac.package_id
									WHERE pri.bid='".$rows['bid']."'");
				if($s->num_rows()>0){
					//echo $s->num_rows()." Total";
					$arr = $s->result_array();
					foreach($arr as $srow){
						$this->db->query("update prinumbers set bid='0',used='0',status='0',type='0',associateid='0' where number='".$srow['number']."'");
						//echo "<br>".$this->db->last_query();
						$this->db->query("update package_activate set bid='0' where number='".$srow['number']."'");
						//echo "<br>".$this->db->last_query();
						$this->db->query("update business_packageaddons set bid='0' where number='".$srow['number']."'");
						//echo "<br>".$this->db->last_query();
						$this->db->query("update  ".$rows['bid']."_groups set status='0',prinumber=''");
						//echo "<br>".$this->db->last_query();
						$this->db->query("update  ".$rows['bid']."_pbx set status='0',prinumber=''");
						//echo "<br>".$this->db->last_query();
						$this->db->query("update  business set status='0' where bid='".$rows['bid']."'");
						//echo "<br>".$this->db->last_query();
						$this->db->query("update  user set status='0' where bid='".$rows['bid']."'");
						//echo "<br>".$this->db->last_query();
						
					}
				}									
			}
		}
	}
	/* Employee follow up mail */
	
	function followupRemainder($bid){
		$employees = $this->getEmp($bid);
		if($employees[0]['eid'] != ''){
			for($emp =0;$emp <count($employees);$emp++){
				$sqL=$this->db->query("
								SELECT f.eid as eid,f.type,f.followupdate as followupdate,h.callfrom as callfrom,h.callername as callername FROM ".$bid."_followup f
								LEFT JOIN ".$bid."_callhistory h ON f.callid = h.callid
								WHERE CAST(f.followupdate AS DATE) = CURRENT_DATE() AND f.eid='".$employees[$emp]['eid']."' and f.type='calltrack' AND (f.alert=1 or f.alert=3)
								UNION
								SELECT f.eid as eid,f.type,f.followupdate as followupdate,p.callfrom as callfrom,p.name as callername FROM ".$bid."_followup f
								LEFT JOIN ".$bid."_pbxreport p ON f.callid = p.callid
								WHERE CAST(f.followupdate AS DATE) = CURRENT_DATE() AND f.eid='".$employees[$emp]['eid']."' and f.type='pbx' AND (f.alert=1 or f.alert=3)
								UNION
								SELECT f.eid as eid,f.type,f.followupdate as followupdate,i.callfrom as callfrom,i.name as callername FROM ".$bid."_followup f
								LEFT JOIN ".$bid."_ivrshistory i ON f.callid = i.hid
								WHERE CAST(f.followupdate AS DATE) = CURRENT_DATE() AND f.eid='".$employees[$emp]['eid']."' and f.type='ivrs' AND (f.alert=1 or f.alert=3)
								UNION
								SELECT lf.eid as eid,'leads' as type,lf.followupdate as followupdate,l.number AS callfrom,l.name as callername FROM ".$bid."_leads_followup lf 
								LEFT JOIN ".$bid."_leads l on lf.callid = l.leadid
								WHERE CAST(lf.followupdate AS DATE) = CURRENT_DATE() AND lf.eid='".$employees[$emp]['eid']."' AND (lf.alert=1 or lf.alert=3)");
				if($sqL->num_rows()>0){
					$message = " ";
					$message.="<table cellpadding='3' cellspacing='3'  border='1' width='500'>
								 <tr>
									<th>Call From</th>
									<th>Caller Name</th>
									<th>Followup </th>
									<th>Source </th>
								</tr>";
					$sms_msg = "Today's call Followups:";
					$i = 0;
					$smsarray = array();
					foreach($sqL->result_array() as $rows){
						$message.="<tr>
									<td>".$rows['callfrom']."</td>
									<td>".$rows['callername']."</td>
									<td>".$rows['followupdate']."</td>
									<td>".$rows['type']."</td>
								</tr>";
						
					}
					$smsarray[$i] = $sms_msg;
					$message.="</table>";
					$this->followupMail($employees[$emp]['empemail'],$message,$employees[$emp]['empname']);
					/*for($s=0;$s<count($smsarray);$s++){
						$api = "http://115.249.28.90/sms/sendSMS.php?from=vmc.in";
						$sms = $api."&to=".substr($employees[$emp]['empnumber'],-10,10)."&text=".urlencode($sms_msg);
						$sms = file($sms);}*/
				}
			}
		} 
	}
	function SMSFollowup($bid,$smsBalance){
		$date = date('Y-m-d H:i:s');
		//~ $currentDate = strtotime($date);
		//~ $futureDate = $currentDate+(60*120);
		//~ $formatDate = date("Y-m-d H:i:s", $futureDate);
		$employees = $this->getEmp($bid);
		if($employees[0]['eid'] != ''){
			for($emp =0;$emp <count($employees);$emp++){
				$sqL=$this->db->query("SELECT f.eid as eid,f.type,f.followupdate as followupdate,h.callfrom as callfrom,h.callername as callername,f.comment as message,f.callid as callid,f.type as source FROM ".$bid."_followup f
				LEFT JOIN ".$bid."_callhistory h ON f.callid = h.callid
				WHERE (f.followupdate BETWEEN now() AND (DATE_ADD(now(), INTERVAL f.reach_time MINUTE))) AND f.alert_status=0 AND f.eid='".$employees[$emp]['eid']."' and f.type='calltrack' AND (f.alert=2 or f.alert=3)
				UNION
				SELECT f.eid as eid,f.type,f.followupdate as followupdate,p.callfrom as callfrom,p.name as callername,f.comment as message ,f.callid as callid,f.type as source FROM ".$bid."_followup f
				LEFT JOIN ".$bid."_pbxreport p ON f.callid = p.callid
				WHERE (f.followupdate BETWEEN now() AND (DATE_ADD(now(), INTERVAL f.reach_time MINUTE))) AND f.alert_status=0 AND f.eid='".$employees[$emp]['eid']."' and f.type='pbx' AND (f.alert=2 or f.alert=3)
				UNION
				SELECT f.eid as eid,f.type,f.followupdate as followupdate,i.callfrom as callfrom,i.name as callername,f.comment as message 
				,f.callid as callid,f.type as source FROM ".$bid."_followup f
				LEFT JOIN ".$bid."_ivrshistory i ON f.callid = i.hid
				WHERE (f.followupdate BETWEEN now() AND (DATE_ADD(now(), INTERVAL f.reach_time MINUTE))) AND f.alert_status=0 AND f.eid='".$employees[$emp]['eid']."' and f.type='ivrs' AND (f.alert=2 or f.alert=3)
				UNION
				SELECT lf.eid as eid,lf.type,lf.followupdate as followupdate,l.number AS callfrom,l.name as callername,lf.comment as message,lf.leadid as callid,lf.type as source FROM ".$bid."_followup lf 
				LEFT JOIN ".$bid."_leads l on lf.callid = l.leadid
				WHERE (lf.followupdate BETWEEN now() AND (DATE_ADD(now(), INTERVAL lf.reach_time MINUTE))) AND lf.alert_status=0 AND lf.eid='".$employees[$emp]['eid']."' AND (lf.alert=2 or lf.alert=3)");
				if($sqL->num_rows()>0){
					foreach($sqL->result_array() as $rows){
						if($smsBalance>0 && (preg_match('/^[7-9][0-9]{9}$/',substr($employees[$emp]['empnumber'],-10,10)))){
							
							$api = "http://115.249.28.90/sms/sendSMS.php?from=vmc.in";
							$sms = $api."&to=".substr($employees[$emp]['empnumber'],-10,10)."&text=".urlencode($rows['message']);
							$sms = file_get_contents($sms);
							if($sms!=""){
								if($rows['source']!="leads"){
									$this->db->query("UPDATE ".$bid."_followup set alert_status=1 where callid='".$rows['callid']."'");
								}else{
									$this->db->query("UPDATE ".$bid."_followup set alert_status=1 where leadid='".$rows['callid']."'");
								}
								$c=$this->db->query("SELECT * FROM credit_use WHERE bid='".$bid."'");
								if($c->num_rows()==0){
									$this->db->set('bid',$bid);
									$this->db->set('cr_used',$use);
									$this->db->insert('credit_use');
								}else{
									$this->db->query("UPDATE credit_use set cr_used=cr_used+1 where bid='".$bid."'");
								}
							}
						 }
					 }
				}
			}
		} 
		
	}
	Function followupMail($email,$message,$empname){
		$to = $email;
		$subject = "MCube Followups";
		$body=$this->emailmodel->newEmailBody($message,$empname);
		$this->load->library('email');
		$this->email->from('noreply@mcube.com', 'MCube');
		$this->email->to($to);
		$this->email->subject($subject);
		$this->email->message($body);
		$mail = $this->email->send();	
		//MCubeMail($to,$subject,$body);
		if($mail)
			 echo "Successfully sent";
		 else
			 echo 'error';
   }
	
	function getAllBusinessIds(){
		$sql = "SELECT bid FROM business WHERE status='1'";
		$rst = $this->db->query($sql);
		return $rec = ($rst->num_rows()>0) ? $rst->result_array() : array();
	}
	function pdetails(){
		if(date('d')=='01'){
			//~ $sql = "SELECT count(*) as cnt FROM report_log WHERE `Billing Month`=DATE_FORMAT(SUBDATE( CURDATE(), INTERVAL 1 MONTH), '%Y-%m')";
			//~ $cnt = $this->db->query($sql)->row()->cnt;
			//~ if($cnt=='0'){
				$sql=$this->db->query("INSERT INTO report_log
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
										DATE_FORMAT(SUBDATE( CURDATE(), INTERVAL 1 MONTH), '%Y-%m') as `Billing Month`
										FROM
										(SELECT 
										p.number,			b.businessname,			p.landingnumber,
										p.pri,				pp.packagename,			p.climit,	p.flimit,
										p.used,				p.assigndate,			p.svdate,	p.bid
										FROM `prinumbers` p
										LEFT JOIN business b on p.bid=b.bid
										LEFT JOIN package pp on p.package_id=pp.package_id
										WHERE b.bid is not null
										ORDER BY b.bid,p.assigndate) p
										LEFT JOIN 
										(SELECT number,COALESCE(sum(used),0) as `reset` FROM `number_reset`
										WHERE 1 AND DATE_FORMAT(`rdate`,'%Y-%m')=DATE_FORMAT(SUBDATE( CURDATE(), INTERVAL 1 MONTH), '%Y-%m')
										GROUP BY number) n on p.number=n.number");
				
				$sql = "UPDATE prinumbers SET used='0'";
				$this->db->query($sql);
			//~ }
		}
		$sql = "SELECT * FROM report_log WHERE `Billing Month`=DATE_FORMAT(SUBDATE( CURDATE(), INTERVAL 1 MONTH), '%Y-%m')";
		$rst = $this->db->query($sql);
		$rec = ($rst->num_rows()>0) ? $rst->result_array() : array();
		
		return $rec;
	}
	function smsBalance($bid){
		$sql = "SELECT balance as credit FROM sms_bal WHERE bid='".$bid."'";
		$balance=$this->db->query($sql)->row()->credit;
		return $balance;
	}
	function follow_upsetting($bid){
		$sql = "SELECT followups FROM business WHERE bid='".$bid."'";
		$followups=$this->db->query($sql)->row()->followups;
		return $followups;
	}
	function supEscsetting($bid){
		$sql = "SELECT supEsc FROM business WHERE bid='".$bid."'";
		$escprocess=$this->db->query($sql)->row()->supEsc;
		return $escprocess;
	}
	function checkLog(){
		$sql = "SELECT count(*) as cnt FROM mail_log WHERE date(reportdate)=CURRENT_DATE() AND type='1'";
		$cnt = $this->db->query($sql)->row()->cnt;
		if($cnt>0){
			return true;
		}else{
			$this->db->query("INSERT INTO mail_log SET type='1'");
			return false;
		}
	}
	function empUpdate($data){
		$verify = ($data['dnd']=='1') ? '0' : '1';
		$sql = "UPDATE ".$data['bid']."_employee SET
				dnd		='".$data['dnd']."',
				verify	='".$verify."'
				WHERE eid='".$data['eid']."'";
		$this->db->query($sql);
	}
	function sendVerifyKey($data){
		$sql = "SELECT * FROM verifiedemployee WHERE 
				bid='".$data['bid']."'
				AND eid='".$data['eid']."'
				AND number='".$data['number']."'";
		$rst = $this->db->query($sql);
		if($rst->num_rows()=='0'){
			$vcode = "";for($i = 0; $i<=6 ; $i++){$vcode .= ($i%2==0)? strtoupper(chr(rand(97,122))) : rand(0,9);}
			$sql = "INSERT INTO verifiedemployee SET
					bid='".$data['bid']."',
					eid='".$data['eid']."',
					ename='".$data['ename']."',
					email='".$data['email']."',
					number='".$data['number']."',
					vcode='".$vcode."',
					request_date='".date('Y-m-d H:i:s')."',
					status='0'";
			$rst = $this->db->query($sql);
		}else{
			$vcode = "";
		}
		return $vcode;
	}
	
	function sendSMS($sms){
		$api = "http://115.249.28.90/sms/sendSMS.php?from=vmc.in";
		$sms = $api."&to=".substr($sms['number'],-10,10)."&text=".urlencode($sms['message']);
		$sms = file_get_contents($sms);
	}
	function alert_Prinumbers(){
		$sql=$this->db->query("SELECT p.*,b.* FROM prinumbers p
							   LEFT JOIN business b on p.bid=b.bid 
							   where p.status=1 and p.bid!=0
							   AND p.used > (p.climit*0.8)");
		$output='
				Below List of numbers reached 80% of the credit limit <br/><br/>
				<table cellpadding="5" cellspacing="5" border="1" width="500" align="center">
					<tr>
						<th>Sno</th>
						<th>Landing Number</th>
						<th>Business Name</th>
						<th>Used</th>
						<th>Limit</th>
					</tr>';
		$i=0;$email=0;
		foreach($sql->result_array() as $r){
			$email++;
			$i++;
			$output.='<tr>
				<td>'.$i.'</td>
				<td>'.$r['landingnumber'].'</td>
				<td>'.$r['businessname'].'</td>
				<td>'.$r['used'].'</td>
				<td>'.$r['climit'].'</td>
				</tr>';
		}
		$output.='</table>';
		
		if($email>0){
			 //~ $to=array('tapan.chatterjee@vmc.in'
					//~ ,'support@vmc.in'
					//~ ,'raj.m@vmc.in' 
					//~ ,'vivek.sinha@vmc.in'
					//~ ,'yogendra.sharma@vmc.in'
					//~ ,'vipin.dixit@vmc.in'
					//~ ,'narendra.sharma@vmc.in'
					//~ ,'alok.gupta@vmc.in'
					//~ ,'pavan.br@vmc.in'
					//~ ,'accounts@vmc.in'
					//~ ,'accounts.ccblr@vmc.in'
					//~ ,'amit.singh@vmc.in'
			 //~ );
			 $to=array('tapan.chatterjee@vmc.in','support@vmc.in','accounts@vmc.in','accounts.ccblr@vmc.in');
			//$to[]=(base_url()=='https://mcube.vmc.in/')?'raj.m@vmc.in':'vivek.sinha@vmc.in';
			$subject='Alert of Landing numbers usage reached up to 80% of credit limit';
			$body=$this->emailmodel->newEmailBody($output,'');
			$this->load->library('email');
			$this->email->from('noreply@mcube.com', 'MCube');
			$this->email->to($to);
			$this->email->subject($subject);
			$this->email->message($body);
			$this->email->send();
			$to = implode(",",$to);
			//MCubeMail($to,$subject,$body);
			}
	}	
	
	function weeklyEmpWise($bid){
		$res=array();
		$q='';
		$date=date('Y-m-d',strtotime('-6 days'));
		
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND h.dialstatus='ANSWER' ";
		}
		
		$sql=$this->db->query("SELECT e.eid,e.empname,COALESCE(h.cnt,0) as count 
								FROM ".$bid."_employee e
								LEFT JOIN (SELECT count(h.callid) as cnt,h.eid 
								FROM ".$bid."_callhistory h
								WHERE h.status!=2 and date(h.starttime)>='".$date."' 
								".$q." GROUP BY h.eid) h on e.eid=h.eid 
								WHERE h.cnt>0
								ORDER BY e.empname");
		//echo $this->db->last_query();
		if($sql->num_rows()>0){
			$res=$sql->result_array();
			//$uncount=0;
			foreach($res as $ikey=>$r){
				$esql=$this->db->query("SELECT h.callid 
								FROM ".$bid."_callhistory h
								WHERE h.status!=2 and date(h.starttime)>='".$date."' 
								AND h.eid = '".$r['eid']."' ".$q."
								GROUP BY h.callfrom,h.gid,h.eid ");
				if($esql->num_rows()>0){
					$eres=$esql->row();
					$res[$ikey]['ucount'] = $esql->num_rows();

				}
				else{$res[$ikey]['ucount'] = 0;}			
			}
		}
		return $res;
	}
	
	function weeklyGrWise($bid){
		$res=array();
		$q='';
		$date=date('Y-m-d',strtotime('-6 days'));
		
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND h.dialstatus='ANSWER' ";
		}
		
		$sql=$this->db->query("SELECT g.gid,g.groupname,COALESCE(h.cnt,0) as count 
								FROM ".$bid."_groups g
								LEFT JOIN (SELECT count(h.callid) as cnt,h.gid 
								FROM ".$bid."_callhistory h
								WHERE h.status!=2 and date(h.starttime)>='".$date."' 
								".$q." GROUP BY h.gid) h on g.gid=h.gid 
								WHERE h.cnt>0
								ORDER BY g.groupname ");
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}
		return $res;
	}
	////////////////////////////////////////////////////////////////
	function weeklyReturningCustomer($bid){
		$res=array();
		$res=array();
		$q = " WHERE c.status!=2  AND date(c.starttime)>=(DATE_SUB(CURRENT_DATE(),INTERVAL 6 DAY))";
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND h.dialstatus='ANSWER' ";
		}
		$sql=$this->db->query("SELECT c.callfrom,c.callid,g.groupname,e.empname
							   FROM ".$bid."_callhistory c
							   LEFT JOIN ".$bid."_groups g on c.gid=g.gid
							   LEFT JOIN ".$bid."_employee e on c.eid=e.eid
							   ".$q."
							   GROUP BY c.callfrom,c.gid,c.eid
							   HAVING count(c.callid)>1 
							   ORDER BY c.starttime DESC");
		//echo $this->db->last_query();
		if($sql->num_rows()>0)
		{
			$res=$sql->result_array();
		}	
		return $res;
	}
	
	function weeklyRecentCalls($bid){
		$res=array();
		$query = "SELECT c.callfrom as callfrom,g.groupname as groupname,e.empname as empname,c.starttime as starttime 
				FROM `".$bid."_callhistory` c
				LEFT JOIN ".$bid."_groups g on c.gid=g.gid
				LEFT JOIN ".$bid."_employee e on c.eid=e.eid
				WHERE c.status!=2 AND date(c.starttime)>=(DATE_SUB(CURRENT_DATE(),INTERVAL 6 DAY))";
	
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		if($type == '0'){
			$query .= " AND h.dialstatus='ANSWER' ";
		}
		$query .= " UNION ALL ";
		$query .= "SELECT pr.callfrom as callfrom,p.title as groupname,'' as empname,pr.starttime as starttime 
				FROM `".$bid."_pbxreport` pr
				LEFT JOIN ".$bid."_pbx p on p.pbxid=pr.pbxid
				WHERE date(pr.starttime)>=(DATE_SUB(CURRENT_DATE(),INTERVAL 6 DAY))";
		$query.=" UNION ALL ";			
		$query.="SELECT i.callfrom as callfrom,iv.title as groupname,'' as empname,i.datetime as starttime 
				FROM `".$bid."_ivrshistory` i
				LEFT JOIN ".$bid."_ivrs iv on i.ivrsid=iv.ivrsid and iv.bid=".$bid."
				WHERE date(i.datetime)>=(DATE_SUB(CURRENT_DATE(),INTERVAL 6 DAY))";
					
		$query .= "	ORDER BY starttime DESC";
		$sql=$this->db->query($query);
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}	
		return $res;
	}
	//////////////////////////////////////
	
	function weeklyFollowups($bid){
		$res=array();
		$q='';
		$date=date('Y-m-d',strtotime('-6 days'));
		
		$sql = 	"SELECT SQL_CALC_FOUND_ROWS * FROM (
				SELECT f.id as callid,
				if(f.type='calltrack',ch.callfrom,
				(if(f.type='ivrs',ih.callfrom,
				(if(f.type='pbx',ph.callfrom,
				(if(f.type='leads',l.number,''))))))) as callfrom,
				if(f.type='calltrack',ch.callid,
				(if(f.type='ivrs',ih.hid,
				(if(f.type='pbx',ph.callid,
				(if(f.type='leads',ph.leadid,''))))))) as dataid,
				if(f.type='calltrack',ch.callername,
				(if(f.type='ivrs',ih.name,
				(if(f.type='pbx',ph.name,
				(if(f.type='leads',l.name,''))))))) as callername,
				f.type as groupname,
				f.followupdate as calltime,'' as status
				FROM ".$bid."_followup f
				LEFT JOIN ".$bid."_callhistory ch on (f.type='calltrack' AND f.callid=ch.callid)
				LEFT JOIN ".$bid."_ivrshistory ih on (f.type='ivrs' AND f.callid=ih.hid)
				LEFT JOIN ".$bid."_pbxreport ph on (f.type='pbx' AND f.callid=ph.callid)
				LEFT JOIN ".$bid."_leads l on (f.type='leads' AND f.callid=l.leadid)
				WHERE 1 AND date(f.followupdate)>='".$date."'
				) a WHERE 1 ";
				//~ UNION 
				//~ SELECT f.id as callid,l.number as callfrom,
				//~ f.leadid as dataid,
				//~ l.name as callername,'lead' as groupname,
				//~ f.followupdate as calltime,'' as status
				//~ FROM ".$bid."_followup f
				//~ LEFT JOIN ".$bid."_leads l on (f.callid=l.leadid)
				//~ WHERE 1 AND date(f.followupdate)>='".$date."'
				
		$sql .=	" ORDER BY a.calltime ASC ";
		
		$sql=$this->db->query($sql);
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}
		return $res;
	}
	function supEscProcess($bid,$smsbal){
		$date = date('Y-m-d H:i:s');
		$employees = $this->getEmpReportto($bid);
		if($employees[0]['eid'] != ''){
			for($emp =0;$emp <count($employees);$emp++){
				$eid = $employees[$emp]['eid'];
				$reportId = $employees[$emp]['reportto'];
				$maxStatus=$this->db->query("SELECT COALESCE(MAX(`sid`),1) as sid FROM ".$bid."_support_status")->row()->sid;
				//$sql = $this->db->query("SELECT tktid,gid,tkt_esc_time,tkt_level FROM ".$bid."_support_tickets t WHERE NOW()>= (DATE_ADD(t.createdon,INTERVAL t.tkt_esc_time HOUR)) AND assignto = '".$eid."' AND status != 4 ");
				$sql = $this->db->query("SELECT tktid,gid,tkt_esc_time,tkt_level,ticket_id FROM ".$bid."_support_tickets t WHERE  ((DATE_ADD(t.createdon,INTERVAL t.tkt_esc_time HOUR)) BETWEEN NOW() AND DATE_ADD(NOW(),INTERVAL 5 MINUTE)) AND assignto = '".$eid."' AND status != '".$maxStatus."'");
				if($sql->num_rows()>0){
					foreach($sql->result_array() as $rows){
						$reportChk = $this->db->query("SELECT g.eid FROM ".$bid."_support_groups g LEFT JOIN ".$bid."_support_grpemp ge ON g.gid = ge.gid WHERE (g.eid='".$reportId."' OR ge.eid='".$reportId."') AND g.gid='".$rows['gid']."'");
						if($reportChk->num_rows()>0){
							$assignto = $reportId;
							$tktlevel = $this->db->query("SELECT * FROM ".$bid."_support_levels WHERE id = (".$rows['tkt_level']." + 1)")->row();
							$this->db->set('assignto',$assignto);
							$this->db->set('tkt_level',$tktlevel->id);
							$this->db->set('tkt_esc_time',($rows['tkt_esc_time'] + $tktlevel->time));
							$this->db->set('lastmodified',$date);
							$this->db->where('tktid',$rows['tktid']);
							$this->db->update($bid.'_support_tickets');
							$reportEmpdetails = $this->empDetails($reportId,$bid);
							if($smsbal>0 && (preg_match('/^[7-9][0-9]{9}$/',substr($employees[$emp]['empnumber'],-10,10)))){
								$message = "Support Ticket (".$rows['ticket_id'].") has reassigned to your reporting person ".$reportEmpdetails->empname;				
								$api = "http://115.249.28.90/sms/sendSMS.php?from=vmc.in";
								$sms = $api."&to=".substr($employees[$emp]['empnumber'],-10,10)."&text=".urlencode($message);
								$sms = file_get_contents($sms);
								if($sms!=""){
									$c=$this->db->query("SELECT * FROM credit_use WHERE bid='".$bid."'");
									if($c->num_rows()==0){
										$this->db->set('bid',$bid);
										$this->db->set('cr_used',$use);
										$this->db->insert('credit_use');
									}else{
										$this->db->query("UPDATE credit_use set cr_used=cr_used+1 where bid='".$bid."'");
									}
								}
							}
							if($smsbal>0 && (preg_match('/^[7-9][0-9]{9}$/',substr($reportEmpdetails->empnumber,-10,10)))){
								$message = "New Support Ticket (".$rows['ticket_id'].") has assigned to you from ".$employees[$emp]['empname'];				
								$api = "http://115.249.28.90/sms/sendSMS.php?from=vmc.in";
								$sms = $api."&to=".substr($reportEmpdetails->empnumber,-10,10)."&text=".urlencode($message);
								$sms = file_get_contents($sms);
								if($sms!=""){
									$c=$this->db->query("SELECT * FROM credit_use WHERE bid='".$bid."'");
									if($c->num_rows()==0){
										$this->db->set('bid',$bid);
										$this->db->set('cr_used',$use);
										$this->db->insert('credit_use');
									}else{
										$this->db->query("UPDATE credit_use set cr_used=cr_used+1 where bid='".$bid."'");
									}
								}
							}
							$tktDetails = $this->tktDetails($rows['tktid'],$bid);
							$msg = "You have assigned to New Support Ticket (".$rows['ticket_id']."). Ticket Details are <br/>";
							$message = "<br/>Name:".$tktDetails->name;
							$message .= "<br/>Email:".$tktDetails->email;
							$message .= "<br/>Number:".$tktDetails->number;
							$message .= "<br/>Address:".$tktDetails->caller_add;
							$message .= "<br/>Business:".$tktDetails->caller_bus;
							$msg = $msg." ".$message;
							$body = $this->emailmodel->newEmailBody($msg,$reportEmpdetails->empname);
							$to  = $reportEmpdetails->empemail; 
							$subject = ' Assigned Support Ticket details ';
							$this->load->library('email');
							$this->email->from('noreply@mcube.com', 'MCube');
							$this->email->to($to);
							$this->email->subject($subject);
							$this->email->message($body);
							$this->email->send();
							//MCubeMail($to,$subject,$body);	
							$msg1 = "Support Ticket (".$rows['ticket_id'].") has reassigned to your reporting person ".$reportEmpdetails->empname;
							$msg1 = $msg1." ".$message;
							$body = $this->emailmodel->newEmailBody($msg1,$employees[$emp]['empname']);
							$to  = $employees[$emp]['empemail']; 
							$subject = 'Reassigned Support Ticket details ';
							$this->load->library('email');
							$this->email->from('noreply@mcube.com', 'MCube');
							$this->email->to($to);
							$this->email->subject($subject);
							$this->email->message($body);
							$this->email->send();
							//MCubeMail($to,$subject,$body);	
						}
						
					 }
				}
			}
		}
	}
	function getEmpReportto($bid){
		$sql = "SELECT * FROM ".$bid."_employee WHERE status='1' AND reportto != '0'";
		$rst = $this->db->query($sql);
		return $rec = ($rst->num_rows()>0) ? $rst->result_array() : array();
	}
	function empDetails($eid,$bid){
		$sql = "SELECT empname,empemail,empnumber FROM ".$bid."_employee WHERE eid='".$eid."'";
		$rst = $this->db->query($sql);
		return $rec = ($rst->num_rows()>0) ? $rst->row() : array();
	}
	function tktDetails($tktid,$bid){
		$sql = "SELECT name,email,number,caller_add,caller_bus FROM ".$bid."_support_tickets WHERE tktid='".$tktid."'";
		$rst = $this->db->query($sql);
		return $rec = ($rst->num_rows()>0) ? $rst->row() : array();
	}
	function supfollowupsetting($bid){
		$sql = "SELECT followup FROM support_configure WHERE bid='".$bid."'";
		$follow = $this->db->query($sql);
		if($follow->num_rows() > 0){
			$followupset = $follow->row()->followup;
			return $followupset;
		}else{
			return '0';
		}
	}
	function supAutoFollowup($bid,$smsbal){
		$date = date('Y-m-d H:i:s');
		$maxStatus=$this->db->query("SELECT COALESCE(MAX(`sid`),1) as sid FROM ".$bid."_support_status")->row()->sid;
		$sql = $this->db->query("SELECT t.tktid,CEIL((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(t.createdon))/(3600*sc.time_interval)) as cnt FROM ".$bid."_support_tickets t LEFT JOIN support_configure sc ON sc.bid= t.bid WHERE  (CEIL((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(t.createdon))/(3600*sc.time_interval))) AND t.auto_followup=1 AND t.tkt_status !='".$maxStatus."'");
		if($sql->num_rows() >0 ){
			$rst = $sql->result_array();
			for($i=0;$i<=count($rst);$i++){
				$tktdetails = $this->db->query("SELECT t.tktid,t.ticket_id,t.name,t.number,e.empnumber,e.empemail FROM ".$bid."_support_tickets t LEFT JOIN ".$bid."_employee e ON t.assignto = e.eid WHERE tktid='".$rst[$i]['tktid']."'")->row_array();
				if($smsbal>0 && (preg_match('/^[7-9][0-9]{9}$/',substr($tktdetails['empnumber'],-10,10)))){
					$message = "You have a Pending Ticket (".$tktdetails['ticket_id'].") to be resolved. ";	
					//~ $message .= "Name:".$tktdetails['name'];
					//~ $message .= "Number:".$tktdetails['number'];			
					$api = "http://115.249.28.90/sms/sendSMS.php?from=vmc.in";
					$sms = $api."&to=".substr($tktdetails['empnumber'],-10,10)."&text=".urlencode($message);
					$sms = file_get_contents($sms);
					if($sms!=""){
						$c=$this->db->query("SELECT * FROM credit_use WHERE bid='".$bid."'");
						if($c->num_rows()==0){
							$this->db->set('bid',$bid);
							$this->db->set('cr_used',$use);
							$this->db->insert('credit_use');
						}else{
							$this->db->query("UPDATE credit_use SET cr_used=cr_used+1 WHERE bid='".$bid."'");
						}
					}
				}
				/* Email */
				$msg = "You have a Pending Ticket (".$tktdetails['ticket_id'].") to be resolved. <br/>";
				$msg .= " Name: ".$tktdetails['name']."<br/>";
				$msg .= " Number: ".$tktdetails['number']."<br/>";
				$body = $this->emailmodel->newEmailBody($msg);
				$to  = $tktdetails['empemail']; 
				$subject = ' Pending Ticket Followup ';
				$this->load->library('email');
				$this->email->from('noreply@mcube.com', 'MCube');
				$this->email->to($to);
				$this->email->subject($subject);
				$this->email->message($body);
				$this->email->send();
				//MCubeMail($to,$subject,$body);	
				$this->db->query("UPDATE ".$bid."_support_tickets SET followup_cnt= ".$rst[$i]['cnt']." WHERE tktid='".$rst[$i]['tktid']."'");
				
			}
		}
	}
		/* End of employee cron mail */
	function getAllLanding($bid){
		$sql = "SELECT landingnumber,type,associateid FROM prinumbers 
		        WHERE bid=".$bid." AND status='1'";
		$rst['data'] = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$rst['count'] = $rst1->row()->cnt;
		return $rst;
	}
	/*function getCallsMail($bid='',$gid){
		$bid = ($bid!='') ? $bid : $this->session->userdata('bid');
		$res=array();
		$res['data']=$this->db->query("SELECT callid,eid as empid FROM ".$bid."_callhistory WHERE date(starttime)=SUBDATE( CURDATE(), INTERVAL 1 DAY) 
									   AND gid='".$gid."' ORDER BY starttime DESC")->result_array();
	   	$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;    
	}*/
	
	function getCallsbyGroup($bid=''){
		$bid = ($bid!='') ? $bid : $this->session->userdata('bid');
		$ret = array();
		$data = array();
		$sql = "SELECT SQL_CALC_FOUND_ROWS g.gid,g.groupname,g.keyword,count(a.callid) as total FROM 
				".$bid."_groups g 
				LEFT JOIN (
				SELECT callid,gid FROM ".$bid."_callhistory 
				WHERE date(starttime)=SUBDATE( CURDATE(), INTERVAL 1 DAY) 
				UNION
				SELECT callid,gid FROM ".$bid."_callarchive 
			  WHERE date(starttime)=SUBDATE( CURDATE(), INTERVAL 1 DAY)
				) a ON a.gid=g.gid
			
	GROUP BY g.gid";
		
		$rst = $this->db->query($sql)->result_array();

		
		foreach($rst as $rec){
			$data[$rec['gid']] = $rec;
		}

		$sql = "SELECT g.gid,count(a.callid) as total FROM 
				".$bid."_groups g 
				LEFT JOIN (
				SELECT callid,gid FROM ".$bid."_callhistory 
						  WHERE date(starttime)=SUBDATE( CURDATE(), INTERVAL 1 DAY)
						  	AND pulse!='0'
				UNION
				SELECT callid,gid FROM ".$bid."_callarchive 
						  WHERE date(starttime)=SUBDATE( CURDATE(), INTERVAL 1 DAY) 
				AND pulse!='0'
				) a ON a.gid=g.gid
				
					GROUP BY g.gid";
		
		$rst = $this->db->query($sql)->result_array();
		foreach($rst as $rec){
			$data[$rec['gid']]['answeredcall'] = $rec['total'];
		}
		
		$sql = "SELECT g.gid,count(a.callid) as total FROM 
				".$bid."_groups g 
				LEFT JOIN (
				SELECT callid,gid FROM ".$bid."_callhistory 
				 WHERE date(starttime)=SUBDATE( CURDATE(), INTERVAL 1 DAY)
				AND pulse='0'
				UNION
				SELECT callid,gid FROM ".$bid."_callarchive 
				WHERE date(starttime)=SUBDATE( CURDATE(), INTERVAL 1 DAY) 
				AND pulse='0'
				) a ON a.gid=g.gid
		
					GROUP BY g.gid";
		
		$rst = $this->db->query($sql)->result_array();
		foreach($rst as $rec){
			$data[$rec['gid']]['missedcall'] = $rec['total'];
		}
		
		$sql = "SELECT g.gid,count(a.callid) as total FROM 
				".$bid."_groups g 
				LEFT JOIN (
				SELECT callid,gid FROM ".$bid."_callhistory 
				WHERE date(starttime)=SUBDATE( CURDATE(), INTERVAL 1 DAY)
				GROUP BY callfrom,gid
				UNION
				SELECT callid,gid FROM ".$bid."_callarchive 
			    WHERE date(starttime)=SUBDATE( CURDATE(), INTERVAL 1 DAY)
				GROUP BY callfrom,gid
				) a ON a.gid=g.gid
				
				GROUP BY g.gid";
			
		
		$rst = $this->db->query($sql)->result_array();
		foreach($rst as $rec){
			$data[$rec['gid']]['uniquecall'] = $rec['total'];
		}
		$ret = $data;
		//echo "<pre>";print_r($ret);exit;
		return $ret;
	
	}
	//~ function getCalls($bid=''){
		//~ $bid = ($bid!='') ? $bid : $this->session->userdata('bid');
		//~ $res=array();
		//~ $res['data']=$this->db->query("SELECT callid FROM ".$bid."_callhistory WHERE date(starttime)=SUBDATE( CURDATE(), INTERVAL 1 DAY) ORDER BY starttime DESC")->result_array();
	   	//~ $res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		//~ return $res;    
	//~ }

	
	function getCallsIvrs($bid=''){
		$bid = ($bid!='') ? $bid : $this->session->userdata('bid');
		$res=array();
		$res['data']=$this->db->query("SELECT i.hid as hid,ih.title FROM ".$bid."_ivrshistory i
		                               LEFT JOIN ".$bid."_ivrs ih on i.ivrsid=ih.ivrsid 
									   WHERE date(i.datetime)=SUBDATE( CURDATE(), INTERVAL 1 DAY) 
									   ORDER BY i.datetime DESC")->result_array();
	   	$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
	 //echo "<pre>";print_r($res);exit;
		return $res;
	}
	function getCallsIvrsGrp($bid=''){
		$bid = ($bid!='') ? $bid : $this->session->userdata('bid');
		$res=array();
		$res['data']=$this->db->query("SELECT count(ih.title) as count,i.hid as hid,ih.title FROM ".$bid."_ivrshistory i
		                               LEFT JOIN ".$bid."_ivrs ih on i.ivrsid=ih.ivrsid 
									   WHERE date(i.datetime)=SUBDATE( CURDATE(), INTERVAL 1 DAY) 
									   ORDER BY i.datetime DESC")->result_array();
	   	$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
	 //echo "<pre>";print_r($res);exit;
		return $res;
	}
	function getCallsPbx($bid=''){
		$bid = ($bid!='') ? $bid : $this->session->userdata('bid');
		$res=array();
		$res['data'] =$this->db->query("SELECT p.callid as callid,r.title FROM ".$bid."_pbxreport p
		                                    LEFT JOIN ".$bid."_pbx r on r.pbxid=p.pbxid 
		                                    WHERE date(starttime)=SUBDATE( CURDATE(), INTERVAL 1 DAY)
		                              
		                                    ORDER BY starttime DESC")->result_array();
	   	$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
	    //echo "<pre>";print_r($res);exit;
		return $res;
	}
	function getCallsPbxGrp($bid=''){
		$bid = ($bid!='') ? $bid : $this->session->userdata('bid');
		$res=array();
		$res['data'] =$this->db->query("SELECT count(p.callid) as count,p.callid as callid,r.title FROM ".$bid."_pbxreport p
		                                    LEFT JOIN ".$bid."_pbx r on r.pbxid=p.pbxid 
		                                    WHERE date(starttime)=SUBDATE( CURDATE(), INTERVAL 1 DAY) 
		                                  
		                                    ORDER BY starttime DESC")->result_array();
	   	$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
	    //echo "<pre>";print_r($res);exit;
		return $res;
	}
   function leadGrpCalls($bid=''){
		    $res =$this->db->query("SELECT  g.groupname,a.gid,count(a.leadid) as Leadcount 
							   FROM ".$bid."_leads a
							   LEFT JOIN ".$bid."_leads_groups g on a.gid=g.gid
							   WHERE date(createdon)=SUBDATE( CURDATE(), INTERVAL 1 DAY) GROUP BY a.gid
							   ")->result_array();
      return $res;
	}
   function leadEmpCalls($bid=''){
		    $res =$this->db->query("SELECT  e.empname,a.gid,count(a.leadid) as Leadcount, a.assignto as eid
							   FROM ".$bid."_leads a
							   LEFT JOIN ".$bid."_leads_groups g on a.gid=g.gid
							   LEFT JOIN ".$bid."_employee e on e.eid=a.assignto
							   WHERE date(createdon)=SUBDATE( CURDATE(), INTERVAL 1 DAY) GROUP BY eid
							   ")->result_array();
       return $res;
	}
	function extDetials($extnumber,$bid=''){
		$bid = ($bid!='') ? $bid : $this->session->userdata('bid');
		$res=array();
		$res['data'] =$this->db->query("SELECT count(p.callid) as count,p.callid as callid,r.title FROM ".$bid."_pbxreport p
		                                    LEFT JOIN ".$bid."_pbx r on r.pbxid=p.pbxid 
		                                    WHERE date(starttime)=SUBDATE( CURDATE(), INTERVAL 1 DAY)
		                                    GROUP BY p.pbxid 
		                                    ORDER BY starttime DESC")->result_array();
	   	$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
	    //echo "<pre>";print_r($res);exit;
		return $res;
	}
	function getCallsPbxWeekly($bid=''){
		$bid = ($bid!='') ? $bid : $this->session->userdata('bid');
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		$q = ($type == '0')? " AND pulse > '0'": "";
		$sql = "SELECT callid FROM ".$bid."_pbxreport WHERE date(starttime) > SUBDATE( CURDATE(), INTERVAL 7 DAY) ".$q." ORDER BY starttime DESC";
		$rst = $this->db->query($sql);
	    return $rec = ($rst->num_rows()>0) ? $rst->result_array() : array();
	}
	
	function reset_usedLimit($bid){
		$Pridetails=$this->getPridetails($bid);
		  	 foreach($Pridetails as $pri){
		  		$used=$pri['used']; 
		  		$climit=$pri['climit'];
		  	    $package_id=$pri['package_id'];
                $v =($used*'100');             
                $v1 =($v/$climit);
                $v2 =($v1>='80')?'1':'0'; 
		
	     if($v2){ 
			$packInfo=$this->get_package($package_id);
			//echo "<pre>"; print_r($packInfo); exit;
			$nid=$this->db->query("SELECT COALESCE(MAX(`id`),0)+1 as id FROM  number_reset")->row()->id;
			$this->db->set('id',$nid);
			$this->db->set('number',$pri['number']);
			$this->db->set('used',$used);
			//$this->db->set('resetby',$this->session->userdata('uid'));
			$this->db->set('rdate',date('Y-m-d H:i:s'));
			$this->db->insert('number_reset');
			$this->db->set('used','0');
			$this->db->where('number',$pri['number']);
			$this->db->update('prinumbers');
		
		       }
  	    }
		return true;
	}
	
	function getPridetails($bid)
	{
		$res=$this->db->query("SELECT * FROM prinumbers WHERE bid=".$bid)->result_array();
		return $res;
	}
	function get_package($pid){
		
		$res=$this->db->query("SELECT * FROM package WHERE package_id=".$pid)->result_array();
		return $res;
		
	}
	
}



/* End of cronjob model------*/
