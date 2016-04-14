<?php
class cronjob extends Controller {
	
	function cronjob(){	
		parent::controller();
		$this->load->model('cronjobmodel','CM');
		$this->load->model('configmodel','Config');
		$this->load->model('sysconfmodel','SYS');
		$this->SYS->load_languages();
	}
	
	function index(){
		//$re=$this->cronjobmodel->Business_User();
		echo "Internal use working fine";
	}
	function check_calltrack(){
		$busList = $this->CM->getAllBusiness();
		//$busList = array(array('bid'=>1));
		if(count($busList)>0){
			foreach($busList as $bus){
				$empList = $this->CM->getEmp($bus['bid']);
				$fieldset = $this->Config->getFields('6',$bus['bid']);
				$callids = $this->CM->getCalls($bus['bid']);
				$allCalls = array();
				foreach($callids as $call){
					array_push($allCalls,$this->Config->getDetail('6',$call['callid'],'',$bus['bid']));
				}
				if(count($empList)>0){
					foreach($empList as $emp){
						$roleDetail = $this->CM->empRole($emp['roleid'],$bus['bid']);
						$keys = array();
						$header = array('Sl');
						foreach($fieldset as $field){
							$checked = false;
							if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
								foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
								if($checked && in_array($field['fieldname'],array('callfrom','callername','starttime','endtime','dialstatus','gid','eid','source','filename','remark'))){
									array_push($keys,$field['fieldname']);
									array_push($header,(($field['customlabel']!="")
														?$field['customlabel']
														:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
								}
							}/*elseif($field['type']=='c' && $field['show']){
								foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
								if($checked){
									array_push($keys,'custom['.$field['fieldid'].']');
									array_push($header,$field['customlabel']);
								}
							}*/
						}
						$output = "<table border='1'>\n";
						$output .= "\t<tr>\n\t\t<th colspan='".count($header)."'>CallTrack Report</th>\n\t</tr>\n";
						$output .= "\t<tr>\n";foreach($header as $h) $output .= "\t\t<th>".$h."</th>\n";$output .= "</tr>\n";
						$i = 1;
						foreach($allCalls as $calls){
							if($roleDetail['role']['admin']=='1' || $emp['eid']==$calls['empid'] || $emp['eid']==$calls['geid']){
								$output .= "\t<tr>\n\t\t<td>".$i."</td>\n";
								foreach($keys as $k) $output .= "\t\t<td>".((isset($calls[$k]) && $calls[$k]!='')?$calls[$k]:"N/A")."</td>\n";
								$output .= "</tr>\n";
								$i++;
							}
						}
						$output .= "\t<tr>\n\t\t<td colspan='".count($header)."'>
						<table align='left' border='1' width='100%'>
							<tr><th colspan='2'>LEGEND</th></tr>
							<tr><th align='left'>ANSWER</th><td>Call is answered. A successful dial. The caller reached the callee.</td></tr>
							<tr><th align='left'>BUSY</th><td>Busy signal. The dial command reached its number but the number is busy.</td></tr>
							<tr><th align='left'>NOANSWER</th><td>No answer. The dial command reached its number, the number rang for too long, then the dial timed out.</td></tr>
							<tr><th align='left'>CANCEL</th><td>Call is cancelled. The dial command reached its number but the caller hung up before the callee picked up.</td></tr>
							<tr><th align='left'>CONGESTION</th><td>Congestion. This status is usually a sign that the dialled number is not recognised.</td></tr>
							<tr><th align='left'>CHANUNAVAIL</th><td>Channel unavailable.</td></tr>
							<tr><th align='left'>VOICEMSG</th><td>Caller left voice message</td></tr>
						</table>
						</td>\n\t</tr>\n";
						echo $output .= "</table>\n";
					}
				}
			}
		}
	}
	function calltrack(){
		//if($this->CM->checkLog()){exit;}
		//$busList = $this->CM->getAllBusiness();
		$busList = array(array('bid'=>1));
		if(count($busList)>0){
			foreach($busList as $bus){
				$empList = $this->CM->getEmp($bus['bid']);
				$fieldset = $this->Config->getFields('6',$bus['bid']);
				$callids = $this->CM->getCalls($bus['bid']);
				$allCalls = array();
				foreach($callids as $call){
					array_push($allCalls,$this->Config->getDetail('6',$call['callid'],'',$bus['bid']));
				}
				if(count($empList)>0){
					foreach($empList as $emp){
						$roleDetail = $this->CM->empRole($emp['roleid'],$bus['bid']);
						$keys = array();
						$header = array('Sl');
						foreach($fieldset as $field){
							$checked = false;
							if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
								foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
								if($checked && in_array($field['fieldname'],array('callfrom','callername','starttime','endtime','dialstatus','gid','eid','source','filename','remark'))){
									array_push($keys,$field['fieldname']);
									array_push($header,(($field['customlabel']!="")
														?$field['customlabel']
														:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
								}
							}/*elseif($field['type']=='c' && $field['show']){
								foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
								if($checked){
									array_push($keys,'custom['.$field['fieldid'].']');
									array_push($header,$field['customlabel']);
								}
							}*/
						}
						$output = "<table border='1'>\n";
						$output .= "\t<tr>\n\t\t<th colspan='".count($header)."'>CallTrack Report</th>\n\t</tr>\n";
						$output .= "\t<tr>\n";foreach($header as $h) $output .= "\t\t<th>".$h."</th>\n";$output .= "</tr>\n";
						$i = 1;
						foreach($allCalls as $calls){
							if($roleDetail['role']['admin']=='1' || $emp['eid']==$calls['empid'] || $emp['eid']==$calls['geid']){
								$output .= "\t<tr>\n\t\t<td>".$i."</td>\n";
								foreach($keys as $k) $output .= "\t\t<td>".((isset($calls[$k]) && $calls[$k]!='')?$calls[$k]:"N/A")."</td>\n";
								$output .= "</tr>\n";
								$i++;
							}
						}
						$output .= "\t<tr>\n\t\t<td colspan='".count($header)."'>
						<table align='left' border='1' width='100%'>
							<tr><th colspan='2'>LEGEND</th></tr>
							<tr><th align='left'>ANSWER</th><td>Call is answered. A successful dial. The caller reached the callee.</td></tr>
							<tr><th align='left'>BUSY</th><td>Busy signal. The dial command reached its number but the number is busy.</td></tr>
							<tr><th align='left'>NOANSWER</th><td>No answer. The dial command reached its number, the number rang for too long, then the dial timed out.</td></tr>
							<tr><th align='left'>CANCEL</th><td>Call is cancelled. The dial command reached its number but the caller hung up before the callee picked up.</td></tr>
							<tr><th align='left'>CONGESTION</th><td>Congestion. This status is usually a sign that the dialled number is not recognised.</td></tr>
							<tr><th align='left'>CHANUNAVAIL</th><td>Channel unavailable.</td></tr>
							<tr><th align='left'>VOICEMSG</th><td>Caller left voice message</td></tr>
						</table>
						</td>\n\t</tr>\n";
						 echo $output .= "</table>\n"; exit;
						if($i>'1'){
							$to=$emp['empemail'];
							$subject="CallTrack Report ".$emp['empemail'];
							$this->load->library('email');
							$this->email->from('noreply@mcube.com', 'MCube');
							$this->email->to($to);
							$this->email->subject($subject);
							$this->email->message($output);
							$this->email->send();	
							//MCubeMail($to,$subject,$output);	
						}
					}
				}
			}
		}
	}
	//~ function tt(){
		//~ $busList = $this->CM->getAllBusiness();
		//~ $ban = array();
		//~ foreach($ban as $b)unset($busList[$b]);
		//~ print_r($busList);
	//~ }
	
	function tt(){
		$busList = $this->CM->getAllBusiness();
		echo "<pre>";
		print_r($busList);
	}
	function weekly(){
		$busList = $this->CM->getAllBusiness();
		$ban = array();
		foreach($ban as $b)unset($busList[$b]);
		//$busList = array(array('bid'=>1));
		if(count($busList)>0){
			foreach($busList as $bus){
				$x = '0';
				$output = "<table border='0' cellpadding='5' width='600'>\n";
				$output .= "\t<tr>\n\t\t<th>Weekly Report for ".$bus['businessname']."</th>\n\t</tr>\n";
				// Employee wise call for last 7 days
				$list = array();
				$list = $this->CM->weeklyEmpWise($bus['bid']);
				if(count($list)>0){
					$x = '1';
					$output .= "\t<tr>\n\t\t<td>\n";
					$output .= "\t\t\t<table border='1' width='100%'>\n";
					$output .= "\t\t\t\t<tr><th colspan='3'>Employee Wise calls for Last 7 Days</th></tr>\n";
					$output .= "\t\t\t\t<tr><th>Employee Name</th><th>Total Call</th><th>Unique Call</th></tr>\n";
					foreach($list as $row){
						$output .= "\t\t\t\t<tr><td>".$row['empname']."</td><td>".$row['count']."</td><td>".$row['ucount']."</td></tr>\n";
					}
					$output .= "\t\t\t</table>\n";
					$output .= "\t\t</td>\n\t</tr>\n";
				}
				///////////////////////////////////////////
				// Group wise call for last 7 days
				$list = array();
				$list = $this->CM->weeklyGrWise($bus['bid']);
				if(count($list)>0){
					$x = '1';
					$output .= "\t<tr>\n\t\t<td>\n";
					$output .= "\t\t\t<table border='1' width='100%'>\n";
					$output .= "\t\t\t\t<tr><th colspan='2'>Group Wise calls for Last 7 Days</th></tr>\n";
					$output .= "\t\t\t\t<tr><th>Group Name</th><th>Total Call</th></tr>\n";
					foreach($list as $row){
						$output .= "\t\t\t\t<tr><td>".$row['groupname']."</td><td>".$row['count']."</td></tr>\n";
					}
					$output .= "\t\t\t</table>\n";
					$output .= "\t\t</td>\n\t</tr>\n";
				}
				///////////////////////////////////////////
				// Followups for next 7 days
				$list = array();
				$list = $this->CM->weeklyFollowups($bus['bid']);
				if(count($list)>0){
					$x = '1';
					$output .= "\t<tr>\n\t\t<td>\n";
					$output .= "\t\t\t<table border='1' width='100%'>\n";
					$output .= "\t\t\t\t<tr><th colspan='3'>Followups for Next 7 Days</th></tr>\n";
					$output .= "\t\t\t\t<tr><th>Followup For</th><th>Followup At</th><th>Module</th></tr>\n";
					foreach($list as $row){
						$output .= "\t\t\t\t<tr><td>".$row['callfrom']."</td><td>".$row['calltime']."</td><td>".$row['groupname']."</td></tr>\n";
					}
					$output .= "\t\t\t</table>\n";
					$output .= "\t\t</td>\n\t</tr>\n";
				}
				///////////////////////////////////////////
				// Returning Customer for last 7days
				$list = array();
				$list = $this->CM->weeklyReturningCustomer($bus['bid']);
				if(count($list)>0){
					$x = '1';
					$output .= "\t<tr>\n\t\t<td>\n";
					$output .= "\t\t\t<table border='1' width='100%'>\n";
					$output .= "\t\t\t\t<tr><th colspan='3'>Returning Customer for Last 7 Days</th></tr>\n";
					$output .= "\t\t\t\t<tr><th>Call From</th><th>Group Name</th><th>Employee</th></tr>\n";
					foreach($list as $row){
						$output .= "\t\t\t\t<tr><td>".$row['callfrom']."</td><td>".$row['groupname']."</td><td>".$row['empname']."</td></tr>\n";
					}
					$output .= "\t\t\t</table>\n";
					$output .= "\t\t</td>\n\t</tr>\n";
				}
				///////////////////////////////////////////
				// Recent Calls for last 7days
				$list = array();
				$list = $this->CM->weeklyRecentCalls($bus['bid']);
				if(count($list)>0){
					$x = '1';
					$output .= "\t<tr>\n\t\t<td>\n";
					$output .= "\t\t\t<table border='1' width='100%'>\n";
					$output .= "\t\t\t\t<tr><th colspan='3'>Recent Calls last 7 Days</th></tr>\n";
					$output .= "\t\t\t\t<tr><th>Call From</th><th>Group Name</th><th>Employee</th></tr>\n";
					foreach($list as $row){
						$output .= "\t\t\t\t<tr><td>".$row['callfrom']."</td><td>".$row['groupname']."</td><td>".$row['empname']."</td></tr>\n";
					}
					$output .= "\t\t\t</table>\n";
					$output .= "\t\t</td>\n\t</tr>\n";
				}
				///////////////////////////////////////////
				$output .= "</table>\n";
				if($x == '1'){
					$emplist = $this->CM->allAdmins($bus['bid']);
					if(count($emplist)>0){
						foreach($emplist as $emp){
							$this->load->library('email');
							$this->email->clear();
							$this->email->to($emp['empemail']);
							$this->email->from('noreply@mcube.com', 'MCube');
							$this->email->subject(" Weekly Report From MCube ");
							$this->email->message($output);
							$this->email->send();
							//~ $to = $emp['empemail'];
							//~ $subject = " Weekly Report From MCube ";
							//~ MCubeMail($to,$subject,$output);
						}
					}
				}
			}
		}
	}
	
	function billgenerate(){
		$res=$this->CM->Billgenerate();
		echo "Bill Generated Successfully";
	}	
	
	function Nbillgenerate(){
		$res=$this->CM->NewBilling();
		echo "Bill Generated Successfully";
	}	
	
	function callarchive(){
		$this->CM->callArchive();
	}
	function poll_activenumbers(){
		$act=$this->CM->active_landingnumbers();
		echo "Poll Completion Numbers are available";
	}
	function Deactive_numbers(){
		$res=$this->CM->Deactive_numbers();
		echo "Numbers relased";
	}
	function followups_remainder(){
		$busList = $this->CM->getAllBusiness();
		foreach($busList as $bus){
			$res = $this->CM->followupRemainder($bus['bid']);
		}
		echo "Today Follow ups reminded through mail ";
	}
	function followup_sms(){
		$busList = $this->CM->getAllBusiness();
		 foreach($busList as $bus){
			 $configure=$this->CM->follow_upsetting($bus['bid']);
			$smsBalance=$this->CM->smsBalance($bus['bid']);
			if($configure!=0){
				$res = $this->CM->SMSFollowup($bus['bid'],$smsBalance);
			}
		}
	}
	function priReset(){
		$res=$this->CM->pdetails();
		$i = 0;
		$output= "<table border=1>";
		foreach($res as $rows){
			if($i=='0'){
				$output.= "\n\t<tr>";
				foreach($rows as $field => $val) if($field!='bid') $output.="\n\t\t<th>".$field."</th>";
				$output.= "\n\t</tr>";
			}
			$output.="\n\t<tr style='background:".(($i%2==0) ? '#C9C9BC':'#FFFFFF')."'>";
			foreach($rows as $field => $val) if($field!='bid') $output.= "\n\t\t<td>".$val."</td>";
			$output.= "\n\t</tr>";
			$i++;
		}
		$output.='</table>';
		$c=$output;
		$to='Accounts<accounts.blr@vmc.in>';
		$subject="Number usage for the Month of ".date('M Y',strtotime('-1 month'));
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
					//~ ,'amit.singh@vmc.in');
		$to=array('tapan.chatterjee@vmc.in','support@vmc.in','accounts@vmc.in','accounts.ccblr@vmc.in');
		$to = implode(",",$to);
		$this->load->library('email');
		$this->email->from('noreply@mcube.com', 'MCube');
		$this->email->to($to);
		$this->email->subject($subject);
		$this->email->message($c);
		$this->email->send();	
		//MCubeMail($to,$subject,$c);
	}
	
	function getDndEmp(){
		//echo "<pre>";
		$busList = $this->CM->getAllBusiness();
		foreach($busList as $bus){
			$bid = $bus['bid'];
			$empList = $this->CM->getEmp($bid);
			if(count($empList)>0){
				foreach($empList as $emp){
					$eid = $emp['eid'];
					$vcode = "";
					//~ $data = array('bid'=>$bid,
								  //~ 'eid'=>$eid,
								  //~ 'ename'=>$emp['empname'],
								  //~ 'email'=>$emp['empemail'],
								  //~ 'number'=>$emp['empnumber'],
								  //~ 'dnd'=>'1');
					//~ $vcode = $this->CM->sendVerifyKey($data);
					if($emp['dnd']=='0'){
						$url = "http://180.179.200.180/filter.php?num=".$emp['empnumber'];
						$dnd = (array)json_decode(file_get_contents($url));
						if($dnd['dnd']=='1'){
							$data = array('bid'=>$bid,
										  'eid'=>$eid,
										  'ename'=>$emp['empname'],
										  'email'=>$emp['empemail'],
										  'number'=>$emp['empnumber'],
										  'dnd'=>'1');
							$this->CM->empUpdate($data);
							$vcode = $this->CM->sendVerifyKey($data);
						}
					}
					if($emp['dnd']=='1' && $emp['verify']!='1'){
						$data = array('bid'=>$bid,
										  'eid'=>$eid,
										  'ename'=>$emp['empname'],
										  'email'=>$emp['empemail'],
										  'number'=>$emp['empnumber'],
										  'dnd'=>'1');
						$vcode = $this->CM->sendVerifyKey($data);
					}
					if($vcode!=''){
						$sms['number'];
						$sms['message'] = "MCube number verification code is ".$vcode." for ".$emp['empemail'];
						$this->CM->sendSMS($sms);
					}
				}
			}
		}
	}
	function alert_Prinumbers(){
		$rs=$this->CM->alert_Prinumbers();
		echo "Mail Sent";
	}
	function support_escprocess(){
		$busList = $this->CM->getAllBusiness();
		 foreach($busList as $bus){
			 $configure=$this->CM->supEscsetting($bus['bid']);
			 $smsbal=$this->CM->smsBalance($bus['bid']);
			if($configure == 1){
				$res = $this->CM->supEscProcess($bus['bid'],$smsbal);
			}
		}
	}
	function support_autofollowup(){
		$busList = $this->CM->getAllBusiness();
		 foreach($busList as $bus){
			 $smsbal=$this->CM->smsBalance($bus['bid']);
			 $configure=$this->CM->supfollowupsetting($bus['bid']);
			 if($configure == 1){
			 $res = $this->CM->supAutoFollowup($bus['bid'],$smsbal);
			 }
		}
	}
    // New function of daily Report Mails includes Track, IVRS,PBX,Leads 
	function ReportMails(){
		if($this->CM->checkLog()){exit;}
		$busList = $this->CM->getAllBusiness();
	    var $output;
		//$busList = array(array('bid'=>1));
		if(count($busList)>0){
			foreach($busList as $bus){
				    $empList = $this->CM->getEmp($bus['bid']);
					$fieldset = $this->Config->getFields('6',$bus['bid']);
					$callids = $this->CM->getCalls($bus['bid']);
					$allCalls = array();
					foreach($callids as $call){
						array_push($allCalls,$this->Config->getDetail('6',$call['callid'],'',$bus['bid']));
					} 
					if(count($empList)>0){
						foreach($empList as $emp){
							$roleDetail = $this->CM->empRole($emp['roleid'],$bus['bid']);
							$keys = array();
							$header = array('Sl');
							foreach($fieldset as $field){
			
								$checked = false;
								if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
									foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
									if($checked && in_array($field['fieldname'],
								array('callfrom','callername','starttime','endtime','dialstatus','gid','eid','filename','remark'))){
										array_push($keys,$field['fieldname']);
								
										array_push($header,(($field['customlabel']!="")
															?$field['customlabel']
															:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
									}
									
								}
							}  
								array_push($header,'Source');
								array_push($keys,'source');
							//$list = $this->CM->getCallsbyGroup($bus['bid']);
						 if(count($allCalls)>0){
							$output .= "<table border='1'>\n";
							$output .= "\t<tr>\n\t\t<th colspan='".count($header)."'>CallTrack Report</th>\n\t</tr>\n";
							$output .= "\t<tr>\n";foreach($header as $h) $output .= "\t\t<th>".$h."</th>\n";$output .= "</tr>\n";
							$i = 1;
					   foreach($allCalls as $calls){
								if($roleDetail['role']['admin']=='1' || $emp['eid']==$calls['empid'] || $emp['eid']==$calls['geid']){
									$output .= "\t<tr>\n\t\t<td>".$i."</td>\n";
									foreach($keys as $k)  $output .= "\t\t<td>".((isset($calls[$k]) && $calls[$k]!='')?$calls[$k]:"N/A")."</td>\n";
									$output .= "</tr>\n";
									$i++;
								}	
						 }
							$output .= "\t<tr>\n\t\t<td colspan='".count($header)."'>
							<table align='left' border='1' width='100%'>
								<tr><th colspan='2'>LEGEND</th></tr>
							  <tr><th align='left'>ANSWER</th><td>Call is answered. A successful dial. The caller reached the call.</td></tr>
								<tr><th align='left'>BUSY</th><td>Busy signal. The dial command reached its number but the number is busy.</td></tr>
								<tr><th align='left'>NOANSWER</th><td>No answer. The dial command reached its number, the number rang for too long, then the dial timed out.</td></tr>
								<tr><th align='left'>CANCEL</th><td>Call is cancelled. The dial command reached its number but the caller hung up before the callee picked up.</td></tr>
								<tr><th align='left'>CONGESTION</th><td>Congestion. This status is usually a sign that the dialled number is not recognised.</td></tr>
								<tr><th align='left'>CHANUNAVAIL</th><td>Channel unavailable.</td></tr>
								<tr><th align='left'>VOICEMSG</th><td>Caller left voice message</td></tr>
							</table>
							</td>\n\t</tr>\n";
							 $output .= "</table>\n"; 
							}
						  }
						}
			        $lanList = $this->CM->getAllLanding($bus['bid']);  
		
					$fieldset = $this->Config->getFields('16',$bus['bid']);
					$callids = $this->CM->getCallsIvrs($bus['bid']);
					//echo "<pre>";print_r($callids);exit;
						$allCalls = array();
					foreach($callids['data'] as $call){
						array_push($allCalls,$this->Config->getDetail('16',$call['hid'],'',$bus['bid']));
					}	
					//echo "<pre>";print_r($allCalls);exit;
					if(count($empList)>0){
					foreach($empList as $emp){
					$roleDetail = $this->CM->empRole($emp['roleid'],$bus['bid']);
							$keys = array();
							$header = array('Sl');
							foreach($fieldset as $field){
								$checked = false;
								if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
									foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
									if($checked && in_array($field['fieldname'],array('ivrstitle','callername','callfrom','datetime','endtime','options'))){
										array_push($keys,$field['fieldname']);
										array_push($header,(($field['customlabel']!="")
															?$field['customlabel']
															:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
									}
								}
							}
							array_push($keys,'landingnumber');
							array_push($header,'Landingnumber');
							//$list = $this->CM->getCallsIvrsGrp($bus['bid']);
					 if(count($allCalls)>0){		
							$output .= "<table border='1'>\n";
							$output .= "<table border='1'>\n";
							if($roleDetail['role']['admin']=='1'){
							
							$output .= "</table>\n";
							$output .= "<table border='1'>\n";
							$output .= "\t<tr>\n\t\t<th colspan='".count($header)."'>Ivrs Report</th>\n\t</tr>\n";
							
							$output .= "\t<tr>\n";foreach($header as $h) $output .= "\t\t<th>".$h."</th>\n";$output .= "</tr>\n";
							foreach($allCalls as $calls){
									$i = 1;
									$output .= "\t<tr>\n\t\t<td>".$i."</td>\n";
									foreach($keys as $k)$output .= "\t\t<td>".((isset($calls[$k]) && $calls[$k]!='')?$calls[$k]:"N/A")."</td>\n";
									$output .= "</tr>\n";
									
									$i++;
							}
						}
					
							$output .= "</table>\n";
							$output .= "</table>\n";
						 }
						}
					  }
			
					$fieldset = $this->Config->getFields('24',$bus['bid']);
					$callids = $this->CM->getCallsPbx($bus['bid']);
					
					$allCalls = array();
					foreach($callids['data']  as $call){
						array_push($allCalls,$this->Config->getDetail('24',$call['callid'],'',$bus['bid']));
					}
					 //  echo "<pre>"; print_r($allCalls);exit;
					if(count($empList)>0){
						foreach($empList as $emp){
						
							$roleDetail = $this->CM->empRole($emp['roleid'],$bus['bid']);

							$keys = array();
							$header = array('Sl');
							foreach($fieldset as $field){
							 
								$checked = false;
								if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
									foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
									if($checked && in_array($field['fieldname'],array('pbxtitle','name','callfrom','starttime','endtime','extensions'))){
										array_push($keys,$field['fieldname']);
										array_push($header,(($field['customlabel']!="")
															?$field['customlabel']
															:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
									}
								}
							}array_push($keys,'landingnumber');
							array_push($header,'Landingnumber');
						 $list = $this->CM->getCallsPbxGrp($bus['bid']);
						 if(count($allCalls)>0){		
						 $output .= "<table border='1'>\n";
						 $output .= "<table border='1'>\n";
						 if($roleDetail['role']['admin']=='1'){
							$output .= "<table border='1'>\n";
							$output .= "\t<tr>\n\t\t<th colspan='".count($header)."'>Pbx Report</th>\n\t</tr>\n";
							$output .= "\t<tr>\n";foreach($header as $h) $output .= "\t\t<th>".$h."</th>\n";$output .= "</tr>\n";
							$i = 1;
							foreach($allCalls as $calls){
									$output .= "\t<tr>\n\t\t<td>".$i."</td>\n";
									foreach($keys as $k) $output .= "\t\t<td>".((isset($calls[$k]) && $calls[$k]!='')?$calls[$k]:"N/A")."</td>\n";
									$output .= "</tr>\n";
									$i++;
								}
							}
							$output .= "</table>\n";
							$output .= "</table>\n";
						  }
						}
					}

				$output .= "<table border='0' cellpadding='5' width='600'>\n";
				$list = array();
				$list = $this->CM->leadEmpCalls($bus['bid']);
				if(count($list)>0){
				    $output .= "\t<tr>\n\t\t<td>\n";
					$output .= "\t\t\t<table border='1' width='100%'>\n";
					$output .= "\t\t\t\t<tr><th colspan='3'>Employee Wise Lead Count</th></tr>\n";
					$output .= "\t\t\t\t<tr><th>Employee Name</th><th>Lead Count</th></tr>\n";
					foreach($list as $row){
						$output .= "\t\t\t\t<tr><td>".$row['empname']."</td><td>".$row['Leadcount']."</td></tr>\n";
					}
					$output .= "\t\t\t</table>\n";
					$output .= "\t\t</td>\n\t</tr>\n";
				}
				///////////////////////////////////////////
				$list = array();
		        $list = $this->CM->leadGrpCalls($bus['bid']);
				if(count($list)>0){
					$output .= "\t<tr>\n\t\t<td>\n";
					$output .= "\t\t\t<table border='1' width='100%'>\n";
					$output .= "\t\t\t\t<tr><th colspan='2'>Group Wise Lead Count</th></tr>\n";
					$output .= "\t\t\t\t<tr><th>Group Name</th><th>Lead Count</th></tr>\n";
					foreach($list as $row){
						$output .= "\t\t\t\t<tr><td>".$row['groupname']."</td><td>".$row['Leadcount']."</td></tr>\n";
					}
					$output .= "\t\t\t</table>\n";
					$output .= "\t\t</td>\n\t</tr>\n";
				    $output .= "</table>\n";
			 }
		 }
					if(count($empList)>0){
						foreach($empList as $emp){
							$this->email->clear();
							$this->email->to($emp['empemail']);
							$this->email->from('noreply@mcube.com', 'MCube');
							$this->email->subject(" Weekly Report From MCube ");
							$this->email->message($output);
							$this->email->send();

						}
					
				}	  
	  ////business end
   }

}
// END
function resetused(){
	$busList = $this->CM->getAllBusiness();

		foreach($busList as $bus){
			$bid = $bus['bid'];		
		  if($bus['autoreset']){
		
		   $res=$this->CM->reset_usedLimit($bid);
	
		}
	}
}

}



/* End of Controller */

