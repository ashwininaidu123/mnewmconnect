<?php
Class Configmodel extends Model{
	function Configmodel(){
		 parent::Model();
         $this->load->model('emailmodel');
	}
	function getFields($modid,$bid='',$source=''){
		$bid = ($bid=='')?$this->session->userdata('bid'):$bid;
		$modid = ($modid=='46') ? '26' : $modid;
		$DB2 = (@in_array($bid,array('257','538'))) ? $this->load->database('download', TRUE) : $this->load->database('download1', TRUE);
		$addon = ($source) ? "" : "  AND f.addon=0 ";
		$sql = "SELECT * FROM ( 
				SELECT 's' as type,f.addon,f.fieldid,f.modid,
				f.fieldname,'' as fieldtype,'' as fieldKey,'' as options,
				'' as defaultvalue,f.is_required as is_required,f.is_hidden,COALESCE(l.display_order,0) as display_order,
				l.customlabel,COALESCE(l.show,1) as `show`,COALESCE(l.listing,1) as `listing`
				FROM systemfields f
				LEFT JOIN (
					SELECT * FROM ".$bid."_custom_label
					WHERE fieldtype='s'
				) as l
				on (f.fieldid=l.fieldid AND l.modid=f.modid)
				WHERE f.modid='".$modid."'  AND f.is_hidden=0 ".$addon."
				UNION
				SELECT 'c' as type,'0',f.fieldid,f.modid,
				f.fieldname,f.fieldtype,f.field_key as fieldKey,f.options,
				f.defaultvalue,f.is_required as is_required,'',COALESCE(l.display_order,0) as display_order,
				l.customlabel,COALESCE(l.show,1) as `show`,COALESCE(l.listing,1) as `listing`
				FROM ".$bid."_customfields f
				LEFT JOIN (
					SELECT * FROM ".$bid."_custom_label
					WHERE fieldtype='c'
				) as l
				on (f.fieldid=l.fieldid AND l.modid=f.modid)
				WHERE f.modid='".$modid."') as t ORDER BY display_order ASC";
		$ret = $DB2->query($sql)->result_array();
		$DB2->close();
		return $ret;
		
	}

	function getDetail($modid='',$itemid='',$formname='',$bid=''){
		$bid = ($bid=='')?$this->session->userdata('bid'):$bid;
		$modid = ($modid=='46') ? '26' : $modid;
		$DB2 = (@in_array($bid,array('257','538'))) ? $this->load->database('download', TRUE) : $this->load->database('download1', TRUE);
		switch($modid){
			case '1'://Business Detail
				$sql = "SELECT *,if(language=1,'English','English') as lang FROM business WHERE bid='".$itemid."'";
				$rst = (array)$DB2->query($sql)->row();
				break;
			case '2'://Employee Detail
				$sql="select a.*,e.empname as reporttoname,b.rolename  as role
					  from ".$bid."_employee a
					  LEFT JOIN  ".$bid."_employee e on a.reportto=e.eid
					  ,".$bid."_user_role b 
					  where a.roleid=b.roleid AND a.eid='".$itemid."'";
				$rst = (array)$DB2->query($sql)->row();
				break;	
			case '3'://Group Detail
				$sql="SELECT g.*,e.empname as eid,sg.groupname as supportgroup,
					  if(g.connectowner=1,'YES','NO') as connectwner,
					  g.eid as epid,if(g.record=1,'YES','NO') as records,
					  if(g.replytocustomer=1,'YES','NO') as replycus,
					  if(g.replytoexecutive=1,'YES','NO') as replyexe,
					  if(g.pincode=1,'YES','NO') as pcode,
					  p.landingnumber as addnumber,(if(g.primary_rule=0,'All',cr.regionname)) as regionname,
					  r.rulename as rules,g.rules as rule
					  FROM ".$bid."_groups g 
					  LEFT JOIN group_rules r on g.rules=r.rulesid 
					  LEFT JOIN ".$bid."_employee e on g.eid=e.eid 
					  LEFT JOIN ".$bid."_support_groups sg on g.supportgrp=sg.gid 
					  LEFT JOIN prinumber p on g.prinumber=p.number
					  LEFT JOIN ".$bid."_custom_region cr on g.primary_rule=cr.regionid
					  WHERE g.bid='".$bid."' 
					  AND g.gid='".$itemid."'";
					  
				$rst = (array)$DB2->query($sql)->row();
				break;
	        case '4':
				$sql = "SELECT i.*,p.landingnumber as ivrsnumber
						,o.optid,o.optsound as filename,o.targettype
						FROM ".$bid."_ivrs i 
						LEFT JOIN ".$bid."_ivrs_options o ON i.ivrsid=o.ivrsid
						LEFT JOIN prinumber p on i.prinumber=p.number
						WHERE o.parentopt=0 AND i.ivrsid='".$itemid."'
						AND i.bid='".$bid."'";
				$rst = (array)$DB2->query($sql)->row();
				break;
			case '5':
				$sql = "SELECT * FROM ".$bid."_ivrs_options 
						WHERE bid='".$bid."'
						AND optid='".$itemid."'";
				$rst = (array)$DB2->query($sql)->row();
				break;
			case '6':
				$sql="SELECT a.*,l.number as lead,p.landingnumber as landingnumber,s.number as suptkt,
					b.bid,b.businessname,ac.empname AS assignto,
					a.assignto AS asto,a.callback,
					IF(a.pulse>0,ceil(a.pulse/60),a.pulse) AS pulse,
					a.pulse AS duration,c.empnumber,
					c.empname as eid,c.eid AS empid,d.eid AS geid ,
					d.groupname AS gid,d.gid AS grid,c.empemail,
					IF(rg.area!='',area,'N/A') AS region 
					FROM ".$bid."_callhistory a
					LEFT JOIN ".$bid."_employee c ON a.eid=c.eid
					LEFT JOIN ".$bid."_groups d ON a.gid=d.gid
					LEFT JOIN prinumbers p ON d.prinumber=p.number
					LEFT JOIN ".$bid."_employee ac ON a.assignto=ac.eid
					LEFT JOIN ".$bid."_leads l ON l.leadid=a.leadid 
					LEFT JOIN ".$bid."_support_tickets s ON s.tktid=a.tktid
					LEFT JOIN business b ON a.bid=b.bid
					LEFT JOIN series_list1 rg ON 
					(rg.scode = substr(a.callfrom,1,4)
					OR rg.scode = substr(a.callfrom,1,3)
					OR rg.scode = substr(a.callfrom,1,2))
					WHERE a.callid='".$itemid."'";
					$rst = (array)$DB2->query($sql)->row(); 
					if(count($rst)>0){
						$rst['bid']=base64_encode($rst['bid']);
					}
				break;
			case '7':
				$sql="select a.status,a.`keyword_id`,a.`keyword`,a.`default_msg`,
					  b.code as code_id,c.keyworduse as keyword_use
					  from keword a,shortcode b,keyword_use c
					  where a.`code_id`=b.codeid and a.bid='".$bid."'
					  and a.keyword_use=c.keyword_useid and a.keyword_id='".$itemid."'";	
				$rst = (array)$DB2->query($sql)->row(); 
				break;
			case '8':
				$sql = "SELECT * FROM sms_template
						WHERE bid='".$bid."'
						AND templateid='".$itemid."'";
				$rst = (array)$DB2->query($sql)->row();
				break;
			case '9':
				$sql = "SELECT * FROM sounds
						WHERE bid='".$bid."'
						AND soundid='".$itemid."'";
				$rst = (array)$DB2->query($sql)->row();
				break;
			case '10':
				$sql = "SELECT p.pbid,p.pbname,p.datetime,u.username as createby FROM phonebook p
						LEFT JOIN user u on p.createby=u.uid
						WHERE p.bid='".$bid."'
						AND p.pbid='".$itemid."'";
				$rst = (array)$DB2->query($sql)->row();
				break;
			case '11':
				$sql = "SELECT * FROM sms_content
						WHERE bid='".$bid."'
						AND contentid='".$itemid."'";
				$rst = (array)$DB2->query($sql)->row();
				break;
			case '12':
				$sql = "SELECT * FROM broadcast
						WHERE bid='".$bid."'
						AND drid='".$itemid."'";
				$rst = (array)$DB2->query($sql)->row();
				break;
			case '13':
				$sql="SELECT * from subkeywordid 
					  where `subkeyword_id`='".$itemid."' 
					  AND bid='".$bid."'";	
				$rst = (array)$DB2->query($sql)->row(); 
				break;
			case '14':
				$sql="SELECT snid,senderid,datetime,if(status=1,'Enabled','Disabled')as status 
					  from senderid where `snid`='".$itemid."' 
					  AND bid='".$bid."'";	
				$rst = (array)$DB2->query($sql)->row(); 
				break;
			case '15':
				$sql="select a.`incid`,a.`from`,a.`keyword`,
					  a.`subkeyword`,a.`date_time`,b.empname as eid,
					  c.code as code_id from ".$bid."_keywordinbox a
					  ,shortcode c,".$bid."_employee b 
					  where a.eid=b.eid and a.`code_id`=c.codeid";
				$rst = (array)$DB2->query($sql)->row(); 
				break;
			case '16':
				//~ $sql="select h.*,ll.number as lead,s.number as suptkt,l.landingnumber as landingnumber,e.eid,
					  //~ REPLACE(h.options,',','<br>->') as options,e.empname as employee,
					  //~ i.title as ivrstitle
					  //~ from ".$bid."_ivrshistory h
					  //~ LEFT JOIN ".$bid."_employee e on h.eid=e.eid
					  //~ LEFT JOIN ".$bid."_ivrs i on i.ivrsid=h.ivrsid
					  //~ -- LEFT JOIN ".$bid."_leads ll ON ll.leadid=h.leadid 
					  //~ -- LEFT JOIN ".$bid."_support_tickets s ON s.tktid=h.tktid
					  //~ LEFT JOIN prinumbers l on i.prinumber=l.number
					  //~ WHERE h.hid='".$itemid."'";
				$sql="select h.*,l.landingnumber as landingnumber,e.eid,
					  REPLACE(h.options,',','<br>->') as options,e.empname as employee,
					  i.title as ivrstitle
					  from ".$bid."_ivrshistory h
					  LEFT JOIN ".$bid."_employee e on h.eid=e.eid
					  LEFT JOIN ".$bid."_ivrs i on i.ivrsid=h.ivrsid
					  LEFT JOIN prinumbers l on i.prinumber=l.number
					  WHERE h.hid='".$itemid."'";
				$rst = (array)$DB2->query($sql)->row(); 
				break;
			case '17' :
				if($formname!='addussdpopup'){
					$sql="SELECT * from keyword_ussd where `ussd_id`='".$itemid."' AND bid='".$bid."'";	
				}else{
					$sql="SELECT * from keyword_ussd where `parentopt`='".$itemid."' AND bid='".$bid."'";	
				}
				$rst = (array)$DB2->query($sql)->row(); 
				break;
			case '18':
				$sql="SELECT * FROM leadgeneration where `leadid`='".$itemid."' AND bid='".$bid."'";
				$rst = (array)$DB2->query($sql)->row(); 
				break;
			case '20':
				$sql="SELECT i.*,if(i.record=1,'YES','NO') as records,
					  p.landingnumber,
					  e.empname
					  FROM ".$bid."_pbx i
					  LEFT JOIN prinumber p on i.prinumber=p.number
					  LEFT JOIN ".$bid."_employee e on i.operator=e.eid
					  where i.pbxid='".$itemid."' 
					  AND i.bid='".$bid."'";
				$rst = (array)$DB2->query($sql)->row(); 
				break;
			case '21':
				$sql="SELECT i.*,p.landingnumber as prinumber FROM ".$bid."_scn i
					LEFT JOIN prinumber p on i.prinumber=p.number
					where i.`scnid`='".$itemid."' AND i.bid='".$bid."'";
				$rst = (array)$DB2->query($sql)->row(); 
				break;
			case '22':
				$sql="SELECT * FROM ".$bid."_scnmembers
					where `mid`='".$itemid."'";
				$rst = (array)$DB2->query($sql)->row(); 
				break;
			case 'callarchive':
				$sql="select a.*,
					if(a.pulse>0,ceil(a.pulse/60),a.pulse) as pulse,a.pulse as duration,
					c.empname as eid,c.eid as empid,
					d.eid as geid ,d.groupname as gid,d.gid as grid,c.empemail,
					IF(rg.area!='',area,'N/A') AS region
					from ".$bid."_callarchive a
					LEFT JOIN ".$bid."_employee c on a.eid=c.eid
					LEFT JOIN ".$bid."_employee ac ON a.assignto=ac.eid
					LEFT JOIN ".$bid."_groups d on a.gid=d.gid
					LEFT JOIN series_list1 rg ON 
					(rg.scode = substr(a.callfrom,1,4)
					OR rg.scode = substr(a.callfrom,1,3)
					OR rg.scode = substr(a.callfrom,1,2))
					 WHERE a.callid='".$itemid."'";	
				$rst = (array)$DB2->query($sql)->row(); 
				$modid = 6;
				break;
			case '23':
					$sql="SELECT * FROM ".$bid."_contact where number='".$itemid."'";
					$rst = (array)$DB2->query($sql)->row(); 
					break;	
			case '24':
				//~ $sql="SELECT h.*,ll.number as lead,s.number as suptkt,l.landingnumber as landingnumber,'' as asto,
					  //~ REPLACE(h.extensions,',','<br>->') as extensions,
					  //~ i.title as pbxtitle
					  //~ FROM ".$bid."_pbxreport h
					  //~ LEFT JOIN 1_pbx i on i.pbxid=h.pbxid
					  //~ -- LEFT JOIN ".$bid."_leads ll ON ll.leadid=h.leadid 
					 //~ -- LEFT JOIN ".$bid."_support_tickets s ON s.tktid=h.tktid
	                  //~ LEFT JOIN prinumbers l on i.prinumber=l.number 
					  //~ WHERE h.callid='".$itemid."'";
				$sql="SELECT h.*,l.landingnumber as landingnumber,'' as asto,
					  REPLACE(h.extensions,',','<br>->') as extensions,
					  i.title as pbxtitle
					  FROM ".$bid."_pbxreport h
					  LEFT JOIN 1_pbx i on i.pbxid=h.pbxid
	                  LEFT JOIN prinumbers l on i.prinumber=l.number 
					  WHERE h.callid='".$itemid."'";
				$rst = (array)$DB2->query($sql)->row(); 
				break;
			case '25':
					$sql="SELECT c.*,e.empname,g.groupname FROM ".$bid."_c2c c 
					LEFT JOIN ".$bid."_employee e ON c.eid = e.eid 
					LEFT JOIN ".$bid."_groups g ON c.gid = g.gid 
					WHERE c.callid='".$itemid."'";
					$rst = (array)$DB2->query($sql)->row(); 
					break;			
			case '27':
					$sql="SELECT * FROM email_settings where bid='".$itemid."'";
					$rst = (array)$DB2->query($sql)->row(); 
					break;	
			case '26':
					$sql="SELECT l.*,e1.empname as enteredempname,
						e2.empname as convertedemp,g.groupname,ls.type,l.duplicate,e3.empname as leadowner,e.empname as assignempname FROM ".$bid."_leads l 
						LEFT JOIN ".$bid."_employee e ON l.assignto = e.eid 
						LEFT JOIN ".$bid."_employee e1 ON l.enteredby = e1.eid 
						LEFT JOIN ".$bid."_employee e2 ON l.convertedby = e2.eid 
						LEFT JOIN ".$bid."_employee e3 ON l.leadowner = e3.eid 
						LEFT JOIN ".$bid."_leads_groups g ON l.gid = g.gid 
						LEFT JOIN ".$bid."_leads_status ls ON l.lead_status = ls.id 
						WHERE l.leadid='".$itemid."'";
					$rst = (array)$DB2->query($sql)->row(); 
					break;
					
			case '28':
					$sql="SELECT * FROM ".$bid."_holiday where id='".$itemid."'";
					$rst = (array)$DB2->query($sql)->row(); 
					break;
			case '29':$sql="SELECT e.empname as empname, f.*,f.callid as detId,f.type as source
					   FROM ".$bid."_followup f
					   LEFT JOIN ".$bid."_employee e ON e.eid=f.eid 
					   WHERE 1 AND f.id='".$itemid."'";
				      $rst = (array)$DB2->query($sql)->row(); 
					  break;
			case '30':
					$sql="SELECT c.comName,p.landingnumber as landingNumber,c.landingNumber as prinumber FROM ".$bid."_community c LEFT JOIN prinumbers p ON p.number = c.landingNumber where c.cid='".$itemid."'";
					$rst = (array)$DB2->query($sql)->row(); 
					break;
			case '31':
					$sql="SELECT * FROM ".$bid."_activitygroup where id='".$itemid."'";
					$rst = (array)$DB2->query($sql)->row(); 
					break;	
			case '32':
					$sql="SELECT r.*,e.empname,g.groupname,at.activity_name FROM ".$bid."_activityreport r
						   left join ".$bid."_employee e on r.eid=e.eid	
						   left join ".$bid."_activitygroup g on r.agid=g.id
							left join ".$bid."_activity at on r.actid=at.acid
							where r.aid='".$itemid."'";
					$rst = (array)$DB2->query($sql)->row(); 
					break;
			case '33':
					$sql="SELECT * FROM ".$bid."_campaign c where c.campaign_id='".$itemid."'";
					$rst = (array)$DB2->query($sql)->row(); 
					break;	

			case '34':
					$sql="SELECT *,c.campaign_name,cr.campaign_id FROM ".$bid."_campaign_report cr  
						  LEFT JOIN ".$bid."_campaign c ON cr.campaign_id = c.campaign_id 
						  WHERE callid='".$itemid."'";	
					$rst = (array)$DB2->query($sql)->row(); 
					break;
			case '35':
					$sql="SELECT c.*,e.empname as created_by,e.eid as empid FROM ".$bid."_call_logs c 
						LEFT JOIN ".$bid."_employee e on c.created_by=e.eid 
						 WHERE c.call_id='".$itemid."'";	
					$rst = (array)$DB2->query($sql)->row(); 
					break;
			case '36':
					$sql="SELECT eb.*,e.empname as empname,e.eid as empid FROM ".$bid."_emp_break eb
						  LEFT JOIN ".$bid."_employee e on eb.eid=e.eid 
						  WHERE eb.id='".$itemid."'";	
					$rst = (array)$DB2->query($sql)->row(); 
					break;
			case '37':
					$sql="SELECT lg.*,e.empname FROM ".$bid."_leads_groups lg
					      LEFT JOIN ".$bid."_employee e on lg.eid=e.eid 
						  WHERE lg.gid='".$itemid."'";	
					$rst = (array)$DB2->query($sql)->row(); 
                  break;
			case '38':
					$sql="SELECT sg.*,e.empname FROM ".$bid."_support_groups sg
					      LEFT JOIN ".$bid."_employee e on sg.eid=e.eid 
						  WHERE sg.gid='".$itemid."'";	
					$rst = (array)$DB2->query($sql)->row();
					break;
			case '39':
					$sql="SELECT * FROM ".$bid."_support_followup WHERE id='".$itemid."'";
					$rst = (array)$DB2->query($sql)->row(); 
					break;
			case '40':
					$sql="SELECT t.*,e.empname as assignempname,e1.empname as enteredempname,
					g.groupname,ss.status,sc.type,l.level,h.filename,'' as asto FROM ".$bid."_support_tickets t 
					LEFT JOIN ".$bid."_employee e ON t.assignto = e.eid 
					LEFT JOIN ".$bid."_employee e1 ON t.enteredby = e1.eid 
					LEFT JOIN ".$bid."_callhistory h ON h.tktid = t.ticket_id  
					LEFT JOIN ".$bid."_support_groups g ON t.gid = g.gid 
					LEFT JOIN ".$bid."_support_levels l ON t.tkt_level = l.id 
					LEFT JOIN ".$bid."_support_status ss ON t.tkt_status = ss.sid
					LEFT JOIN support_criticality sc ON t.tkt_criticality = sc.id 
					WHERE t.tktid='".$itemid."'";
					$rst = (array)$DB2->query($sql)->row(); 
					break;
			case '41':
					$sql="SELECT ig.*,e.empname as interviewer,e.eid FROM ".$bid."_intw_groups ig 
					LEFT JOIN ".$bid."_employee e ON ig.interviewer = e.eid 
					WHERE ig.gid='".$itemid."'";
					$rst = (array)$DB2->query($sql)->row(); 
					break;	
			case '42':
					$sql="SELECT qb.* FROM ".$bid."_intw_ques_bank qb 
					WHERE qb.qb_id='".$itemid."'";
					$rst = (array)$DB2->query($sql)->row(); 
					break;	
			case '43':
					$sql="SELECT q.*, pq.question AS parent FROM ".$bid."_intw_questions q 
					LEFT JOIN ".$bid."_intw_questions pq ON q.rel_id = pq.qid 
					WHERE q.qid='".$itemid."'";
					$rst = (array)$DB2->query($sql)->row(); 
					break;	
			case '44':
					$sql="SELECT obcg.groupname,obcg.group_desc,obcg.gid,e.empname,obcg.eid,obcg.group_rule FROM ".$bid."_obc_groups obcg
					      LEFT JOIN ".$bid."_employee e on obcg.eid=e.eid 
						  WHERE obcg.gid='".$itemid."'";	
					$rst = (array)$DB2->query($sql)->row(); 
					break;
		    case '45':
					$sql="SELECT name,email,contact_no FROM ".$bid."_obc_contacts where contact_no='".$itemid."'" ;
					$rst = (array)$DB2->query($sql)->row();
					break;
			//~ case '46':
					//~ $sql="SELECT l.*,e.empname as assignempname,e1.empname as enteredempname,g.groupname,ls.type FROM ".$bid."_leads l 
						//~ LEFT JOIN ".$bid."_employee e ON l.assignto = e.eid 
						//~ LEFT JOIN ".$bid."_employee e1 ON l.enteredby = e1.eid 
						//~ LEFT JOIN ".$bid."_leads_groups g ON l.gid = g.gid 
						//~ LEFT JOIN ".$bid."_leads_status ls ON l.lead_status = ls.id 
						//~ WHERE l.leadid='".$itemid."'";
					//~ $rst = (array)$this->db->query($sql)->row(); 
					//~ break;
			case '47':
					$sql="SELECT o.*,e.empname as eid FROM ".$bid."_outbound o 
						LEFT JOIN ".$bid."_employee e ON o.eid = e.eid 
						WHERE o.callid='".$itemid."'";
					$rst = (array)$this->db->query($sql)->row(); 
					break;
		    case '48':
				    $sql="SELECT o.*,se.empname as employee,p.propertyname,e.site_image,e.site_image1,e.site_image2,n.landingnumber as tracknum FROM ".$bid."_site o 
					LEFT JOIN ".$bid."_site_image e ON o.siteid = e.siteid 
					LEFT JOIN  prinumber n ON n.number = o.tracknum
					LEFT JOIN  ".$bid."_property p ON p.propertyid = o.pid
					LEFT JOIN ".$bid."_employee se on se.eid = o.site_employee
					WHERE o.bid='".$bid."' AND o.siteid='".$itemid."'";
					$rst = (array)$this->db->query($sql)->row(); 
					break;	
		    case '49':
					$sql="SELECT o.*,e.loc_image1,e.loc_image2,e.loc_image3 FROM ".$bid."_mc_location o
					LEFT JOIN ".$bid."_loc_image e ON o.locid = e.locid 
						WHERE o.locid='".$itemid."'";
					$rst = (array)$this->db->query($sql)->row(); 
					break;
		   case '50':
					$sql="SELECT o.*,e.sitename,p.propertyname as property FROM offers o
					      LEFT JOIN ".$bid."_site e ON o.siteid = e.siteid 
					      LEFT JOIN ".$bid."_property p on p.propertyid = o.propertyname
						WHERE o.offerid='".$itemid."'";
					$rst = (array)$this->db->query($sql)->row(); 
					break;	
		   case '52':
					$sql="SELECT * FROM ".$bid."_property WHERE propertyid='".$itemid."'";
					$rst = (array)$this->db->query($sql)->row(); 
					break;	
			case 'click':
					$sql="SELECT * FROM ".$bid."_outboundcalls where callid='".$itemid."'";
					$rst = (array)$DB2->query($sql)->row(); 
					break;
		}
		$sql = "SELECT * FROM ".$bid."_customfieldsvalue 
				WHERE bid='".$bid."' 
				AND modid='".$modid."'
				AND dataid='".$itemid."'";
		$rst1 = $DB2->query($sql)->result_array();
		foreach($rst1 as $rec) $rst['custom['.$rec['fieldid'].']'] = $rec['value'];
		$DB2->close();
		return $rst;
	}
	function createField($field,$value='',$search=''){
		switch($field['fieldtype']){
			default:
			case 'datetime':
				$ret =form_input(array(
							'name'      => 'custom['.$field['fieldid'].']',
							'id'	    => 'custom_'.$field['fieldid'],
							'class'     => ($field['is_required'])?'required datepicker':'datepicker',
							'value'     => ($search=='search')?'':(($value!='')?$value:$field['defaultvalue'])
						));
			break;
			case 'text':
				$ret =form_input(array(
							'name'      => 'custom['.$field['fieldid'].']',
							'id'	    => 'custom_'.$field['fieldid'],
							'class'     => ($field['is_required'])?'required form-control':'form-control',
							'value'     => ($search=='search')?'':(($value!='')?$value:$field['defaultvalue'])
						));
			break;
			case 'textarea':
				$ret =form_textarea(array(
							'name'      => 'custom['.$field['fieldid'].']',
							'class'     => ($field['is_required'])?'required form-control':'form-control',
							'value'     => ($search=='search')?'':(($value!='')?$value:$field['defaultvalue'])
							
						));
			break;
			case 'checkbox':
				$opts = explode("\n",str_replace("\r","",$field['options']));
				$ret = "";
				foreach($opts as $opt){
					$newopt=explode("|",$opt);
					$nopt=(sizeof($newopt)>0)?$newopt[0]:$opt;
					$valarr = ($value!='')? explode(',',$value):array();
					$ret .= form_checkbox(array(
								'name'      => 'custom['.$field['fieldid'].'][]',
								'value'=>$nopt,
								'checked'=>in_array($nopt,$valarr),
								'class'     => ($field['is_required'])?'required':''
							)).$nopt.' ';
				}
			break;
			case 'radio':
				$opts = explode("\n",str_replace("\r","",$field['options']));
				$ret = "";
				foreach($opts as $opt){
					$newopt=explode("|",$opt);
					$nopt=(sizeof($newopt)>0)?$newopt[0]:$opt;
					$valarr = ($value!='')? explode(',',$value):array();
					$ret .= form_radio(array(
								'name'      => 'custom['.$field['fieldid'].'][]',
								'id'      	=> 'custom_'.$field['fieldid'].'_'.$opt,
								'value'=>$nopt,
								'checked'=>in_array($nopt,$valarr),
								'class'     => ($field['is_required'])?'required':''
							)).$nopt.' ';
				}
			break;
			case 'dropdown':
				$opts = explode("\n",str_replace("\r","",$field['options']));
				$ps[' ']="Select";
				foreach($opts as $opt){
					$newopt=explode("|",$opt);
					$nopt=(sizeof($newopt)>0)?$newopt[0]:$opt;
					$ps[$nopt]=$nopt;
				}
				$val = ($value!='')? $value : '';
				$ret = form_dropdown('custom['.$field['fieldid'].']',$ps,$val,'class="form-control'.(($field['is_required'])?'required form-control':'form-control').'"');
			break;
		}
		return $ret;
	}
	function createFieldAdvance($field,$value='',$search=''){
		switch($field['fieldtype']){
			default:
			case 'datetime':
				$ret =form_input(array(
							'name'      => $field['fieldKey'],
							'id'	    => $field['fieldKey'],
							'class'     => ($field['is_required'])?'required form-control datepicker_leads':'datepicker_leads form-control',
							'value'     => ($search=='search')?'':(($value!='')?$value:$field['defaultvalue'])
						));
			break;
			case 'text':
				$ret =form_input(array(
							'name'      => $field['fieldKey'],
							'id'	    => $field['fieldKey'],
							'class'     => ($field['is_required'])?'required form-control':'form-control',
							'value'     => ($search=='search')?'':(($value!='')?$value:$field['defaultvalue'])
						));
			break;
			case 'textarea':
				$ret =form_textarea(array(
							'name'      => $field['fieldKey'],
							'class'     => ($field['is_required'])?'required form-control':'form-control',
							'value'     => ($search=='search')?'':(($value!='')?$value:$field['defaultvalue'])
							
						));
			break;
			case 'checkbox':
				$opts = explode("\n",str_replace("\r","",$field['options']));
				$ret = "";
				foreach($opts as $opt){
					$newopt=explode("|",$opt);
					$nopt=(sizeof($newopt)>0)?$newopt[0]:$opt;
					$valarr = ($value!='')? explode(',',$value):array();
					$ret .= form_checkbox(array(
								'name'      => 	$field['fieldKey']."[]",
								'value'		=>	$nopt,
								'checked'	=>	in_array($nopt,$valarr),
								'class'     => 	($field['is_required'])?'required':''
							)).$nopt.' ';
				}
			break;
			case 'radio':
				$opts = explode("\n",str_replace("\r","",$field['options']));
				$ret = "";
				foreach($opts as $opt){
					$newopt=explode("|",$opt);
					$nopt=(sizeof($newopt)>0)?$newopt[0]:$opt;
					$valarr = ($value!='')? explode(',',$value):array();
					$ret .= form_radio(array(
								'name'      => $field['fieldKey']."[]",
								'id'      	=> 'custom_'.$field['fieldid'].'_'.$opt,
								'value'=>$nopt,
								'checked'=>in_array($nopt,$valarr),
								'class'     => ($field['is_required'])?'required':''
							)).$nopt.' ';
				}
			break;
			case 'dropdown':
				$opts = explode("\n",str_replace("\r","",$field['options']));
				$ps[' ']="Select";
				foreach($opts as $opt){
					$newopt=explode("|",$opt);
					$nopt=(sizeof($newopt)>0)?$newopt[0]:$opt;
					$ps[$nopt]=$nopt;
				}
				$val = ($value!='')? $value : '';
				$ret = form_dropdown($field['fieldKey'],$ps,$val,'class="auto '.(($field['is_required'])?'required form-control':'form-control').'"');
			break;
		}
		return $ret;
	}
	//~ function customSearch($array,$modid,$bid){
		//~ $x = '0';
		//~ if(is_array($array))foreach($array as $a){if($a !='' AND $a !=' ') $x = '1';}
		//~ $ids = array();
		//~ $data = false;
		//~ if($x=='1'){
			//~ if(($bid == 1 || $bid == 47  || $bid == 257)){
				//~ $sql = 'SELECT callid as id FROM '.$bid."_".$this->lang->line('table_'.$modid).' WHERE 1 ';
				//~ foreach($array as $key=>$val){
					//~ if($val!='' && $val!=' '){
						//~ if($data!=true) $data = true;
						//~ $sql1 = "SELECT field_key FROM ".$bid."_customfields WHERE fieldid='".$key."'";
						//~ $fieldkey = $this->db->query($sql1)->row()->field_key;
						//~ if($fieldkey != ''){
							//~ $sql .= " AND ".$fieldkey ." LIKE '%".$val."%' ";
						//~ }
					//~ }
				//~ }
				//~ foreach($this->db->query($sql)->result_array() as $id){
					//~ $ids[] = "'".$id['id']."'";
				//~ }
				//~ $ids = array_unique($ids);
			//~ }else{
				//~ $sql = 'SELECT dataid FROM '.$bid.'_customfieldsvalue WHERE modid="'.$modid.'" ';
				//~ foreach($array as $key=>$val){
					//~ if($val!='' && $val!=' '){
						//~ if($data!=true) $data = true;
						//~ $q = $sql." AND (fieldid='".$key."' AND value like '%".$val."%')";
						//~ foreach($this->db->query($q)->result_array() as $id){
							//~ $ids[] = "'".$id['dataid']."'";
						//~ }
					//~ }
				//~ }
				//~ $ids = array_unique($ids);
			//~ }
			//~ return (count($ids)>0) ? implode(",",$ids) : (($data==true) ? false : true);
		//~ }
		//~ return true;
	//~ }
	//~ function customSearch_ADV($cid,$modid,$bid,$val){
		//~ if($cid!=''){
			//~ $data = false;
			//~ $sql = '';
			//~ $ids = array();
			//~ if(($modid == 6 || $modid = 26) && ($bid == 1 || $bid == 47  || $bid == 257)){
				//~ $sql = ($modid == 6) ? 'SELECT callid as id FROM '.$bid.'_callhistory WHERE ' :  'SELECT leadid as id FROM '.$bid.'_leads WHERE ';
				//~ if($val!='' && $val!=' '){
					//~ if($data!=true) $data = true;
					//~ $sql1 = "SELECT field_key FROM ".$bid."_customfields WHERE fieldid='".$cid."'";
					//~ $fieldkey = $this->db->query($sql1)->row()->field_key;
					//~ if($fieldkey != ''){
						//~ $sql .= $fieldkey ." ".$val;
					//~ }
				//~ }
				//~ foreach($this->db->query($sql)->result_array() as $id){
					//~ $ids[] = "'".$id['id']."'";
				//~ }
				//~ $ids = array_unique($ids);
			//~ }else{
				//~ $sql = 'SELECT dataid FROM '.$bid.'_customfieldsvalue WHERE modid="'.$modid.'" ';
				//~ if($data!=true) $data = true;
				//~ $q = $sql." AND (fieldid='".$cid."' AND value ".$val.")";
				//~ foreach($this->db->query($q)->result_array() as $id){
					//~ $ids[] = "'".$id['dataid']."'";
				//~ }
			//~ }
			//~ $ids = @implode(',',$ids);
			//~ return ($ids!="") ? $ids : (($data==true) ? false : true);
		//~ }
		//~ return true;
	//~ }
	function UpdateContact($data){
		$data['number'] = substr($data['number'],-10,10);
		$sql=$this->db->query("SELECT contid from ".$data['bid']."_contact where number='".$data['number']."'");
		if($sql->num_rows()>0){
			$r=$sql->row();
			$contid = $r->contid;
		}else{
			$contid=$this->db->query("SELECT COALESCE(MAX(`contid`),0)+1 as id FROM ".$data['bid']."_contact")->row()->id;
		}
	 	$sql ="REPLACE INTO ".$data['bid']."_contact SET
			   contid		='".$contid."',
			   bid			='".$data['bid']."',
			   name			='".$data['name']."',
			   number		='".$data['number']."',
			   email		='".$data['email']."'";
		$this->db->query($sql);
		$sql=$this->db->query("SELECT contid from directory where number='".$data['number']."'");
		if($sql->num_rows()>0){
			$r=$sql->row();
			$contid = $r->contid;
		}else{
			$contid=$this->db->query("SELECT COALESCE(MAX(`contid`),0)+1 as id FROM directory")->row()->id;
		}
		$sql ="REPLACE INTO directory SET
			   contid		='".$contid."',
			   name			='".$data['name']."',
			   number		='".$data['number']."',
			   email		='".$data['email']."'";
		$this->db->query($sql);
		$sql = "UPDATE ".$data['bid']."_callhistory SET
				callername	='".$data['name']."',
				caller_email='".$data['email']."'
				WHERE callfrom = '".$data['number']."'";
		$this->db->query($sql);
		$sql = "UPDATE ".$data['bid']."_ivrshistory SET
				name		='".$data['name']."',
				email		='".$data['email']."'
				WHERE callfrom='".$data['number']."'";
		$this->db->query($sql);
		$sql = "UPDATE ".$data['bid']."_pbxreport SET
				name		='".$data['name']."',
				email		='".$data['email']."'
				WHERE callfrom='".$data['number']."'";
		$this->db->query($sql);
		$sql = "UPDATE ".$data['bid']."_leads SET
				name		='".$data['name']."',
				email		='".$data['email']."'
				WHERE number ='".$data['number']."'";
		$this->db->query($sql);
	}
	
	function supportAlert($alert){
		$message = $alert['message'];
		$to = 'support@vmc.in';
		$subject = $alert['subject'];
		$this->load->library('email');
		$this->email->from('noreply@mcube.com', 'MCube');
		$this->email->to($to);
		$this->email->subject($subject);
		$this->email->message($message);
		$this->email->send();	
	}
	function etemplate($id){
		$bid=$this->session->userdata('bid');
		$this->db->set('template_name',$this->input->post('tname'));
		$this->db->set('content',$this->input->post('content'));
		$this->db->set('status','1');
		if($id == '' || $id == '0'){
			$id=$this->db->query("SELECT COALESCE(MAX(`template_id`),0)+1 as id FROM ".$bid."_emailtemplate")->row()->id;
			$this->db->set('template_id',$id);
			$this->db->insert($bid."_emailtemplate");
		}else{
			$this->db->where('template_id',$id);
			$this->db->update($bid."_emailtemplate");
		}
		return $id;
	}
	function Setemplate($id){
		$bid = $this->session->userdata('bid');
		$this->db->set('template_name',$this->input->post('tname'));
		$this->db->set('content',$this->input->post('content'));
		$this->db->set('status','1');
		if($id == '' || $id == '0'){
			$id = $this->db->query("SELECT COALESCE(MAX(`template_id`),0)+1 as id FROM ".$bid."_smstemplate")->row()->id;
			$this->db->set('template_id',$id);
			$this->db->insert($bid."_smstemplate");
		}else{
			$this->db->where('template_id',$id);
			$this->db->update($bid."_smstemplate");
		}
		return $id;
	}
	function sentmails($bid,$f,$t,$s){
		$id=$this->db->query("SELECT COALESCE(MAX(`id`),0)+1 as id FROM ".$bid."_sentmails")->row()->id;
		$this->db->set('id',$id);
		$this->db->set('from',$f);
		$this->db->set('to',$t);
		$this->db->set('eid',$this->session->userdata('eid'));
		$this->db->set('bcc',$t);
		$this->db->set('subject',$this->input->post('subject'));
		$this->db->set('description',$this->input->post('content'));
		$this->db->set('status',$s);
		$this->db->insert($bid."_sentmails");
	}
	function deleteSentEmail($id){
		$bid=$this->session->userdata('bid');
		$this->db->set('status','2');
		$this->db->where('id',$id);
		$this->db->update($bid."_sentmails");
		return true;
	}
	function delete_Template($id){
		$bid=$this->session->userdata('bid');
		$this->db->set('status','2');
		$this->db->where('template_id',$id);
		$this->db->update($bid."_emailtemplate");
		return true;
	}
	function delete_SMSTemplate($id){
		$bid=$this->session->userdata('bid');
		$this->db->set('status','2');
		$this->db->where('template_id',$id);
		$this->db->update($bid."_smstemplate");
		return true;
	}
	function template_names(){
		$bid=$this->session->userdata('bid');
		$id=$this->db->query("SELECT template_id,template_name FROM ".$bid."_emailtemplate where status=1");
		$res=array();
		if($id->num_rows()>0){
			$res['']="Select";
			foreach($id->result_array() as $rows){
				$res[$rows['template_id']]=$rows['template_name'];
			}
		}
		return $res;
	}
	function callBalance($bid){
		$sql = "SELECT balance as credit FROM call_bal WHERE bid='".$bid."'";
		$balance=$this->db->query($sql)->row()->credit;
		return $balance;
	}	
	function smsBalance($bid){
		$sql = "SELECT balance as credit FROM sms_bal WHERE bid='".$bid."'";
		$balance=$this->db->query($sql)->row()->credit;
		return $balance;
	}
	function leadusageCheck($bid,$type){
		$sqlty = ($type =='group') ? 'grplimit' : (($type =='employee') ? 'emplimit' : (($type =='lead') ? 'leadlimit' : ''));
		$lechk = $this->db->query("SELECT ".$sqlty." as used,type FROM  business_lead_use WHERE bid='".$bid."' LIMIT 0,1");
		$res = array();
		if($lechk->num_rows()>0){
			$res = $lechk->result_array();
			return $res[0];
		}
	}
	function obcusageCheck($bid,$type){
		$sqlty = ($type =='group') ? 'grplimit' : (($type =='employee') ? 'emplimit' : 'cntlimit');
		$lechk = $this->db->query("SELECT ".$sqlty." as used,type FROM  business_support_use WHERE bid='".$bid."' LIMIT 0,1");
		$res = array();
		if($lechk->num_rows()>0){
			$res = $lechk->result_array();
			return $res[0];
		}
	}
	function supusageCheck($bid,$type){
		$sqlty = ($type =='group') ? 'grplimit' : (($type =='employee') ? 'emplimit' : (($type =='support') ? 'supportlimit' : ''));
		$lechk = $this->db->query("SELECT ".$sqlty." as used,type FROM  business_support_use WHERE bid='".$bid."' LIMIT 0,1");
		$res = array();
		if($lechk->num_rows()>0){
			$res = $lechk->result_array();
			return $res[0];
		}
	}
	function smsDeduct($bid,$count){
		$sql = "UPDATE credit_use SET cr_used=cr_used+".$count." WHERE bid='".$bid."'";
		$balance=$this->db->query($sql);
		return;
	}
	function getFieldKey($fieldid,$bid){
		 $fieldkey = $this->db->query("SELECT field_key FROM ".$bid."_customfields WHERE fieldid='".$fieldid."'")->row()->field_key;
		return $fieldkey;
	}
	public function callconvert($source){
		$bid = $source['bid'];
		if($this->input->post('convertlead') || $this->input->post('updatelead')){
			$dis_type = 0;
			$process = "FALSE";
			$return = '0';
			if(isset($_POST['convertlead'])){
				$leads_use = $this->leadusageCheck($bid,'lead');
				if($leads_use['type'] == 1 && $leads_use['used'] == 0){
					$process = "FALSE";
					$return = '1';
				}else{
					$assempid = '0';
					if(($this->input->post('lgid') == '' && $this->input->post('lassignto') == '0')){
						$assempid = $this->input->post('assignto');
					}else{
						if($this->input->post('lassignto') != '' && $this->input->post('lassignto') !=0){
							$assempid = $this->input->post('lassignto');
							$dis_type = 1;
						}else{
							if($this->input->post('lgid') != ''){
								$rule = $this->db->query("SELECT group_rule as rule FROM ".$bid."_leads_groups WHERE gid='".$this->input->post('lgid')."'")->row()->rule;
								if($rule == '2'){
									$resultemp = $this->db->query("SELECT e.eid,COALESCE(((weight/(SELECT sum(weight) FROM ".$bid."_leads_grpemp WHERE gid=ge.gid))-(counter/(SELECT sum(counter) FROM ".$bid."_leads_grpemp WHERE gid=ge.gid))),0) as pc FROM ".$bid."_employee e LEFT JOIN ".$bid."_leads_grpemp ge on e.eid=ge.eid WHERE ge.gid='".$this->input->post('lgid')."' AND ge.status = 1 AND e.status = 1 ORDER BY pc DESC LIMIT 0,1")->result_array();
									if(count($resultemp) > 0)
										$assempid = $resultemp[0]['eid'];
								}elseif($rule == '1'){
									$eid = $this->db->query("SELECT ge.eid FROM ".$bid."_leads_grpemp ge LEFT JOIN ".$bid."_employee e ON ge.eid = e.eid WHERE ge.gid='".$this->input->post('lgid')."' AND ge.bid='".$bid."' AND ge.status = 1 AND e.status = 1 ORDER BY ge.counter ASC LIMIT 0,1")->result_array();
									if(count($eid) > 0){
										$assempid = $eid[0]['eid'];
									}
								}
								$dis_type = 2;
							}
						}
					}
					$leadstat = ($this->input->post('convertlead') != '' ) ? $this->input->post('convertlead') : '2';
					$leadsql = $this->db->query("SELECT leadid FROM ".$bid."_leads WHERE number='".$this->input->post('number')."' AND gid='".$this->input->post('lgid')."' AND lead_status='".$leadstat."'");
					if($leadsql->num_rows() == 0){
						$leadid = $this->db->query("SELECT COALESCE(MAX(`leadid`),0)+1 as id FROM ".$bid."_leads")->row()->id;
						if(isset($_POST['lgid']) && $_POST['lgid'] != ''){
							$this->db->set('gid',$this->input->post('lgid'));
						}
						$this->db->set('leadid',$leadid);
						$this->db->set('bid',$bid);
						$this->db->set('assignto',$assempid);
						$this->db->set('enteredby',$this->session->userdata('eid'));
						$this->db->set('leadowner',$this->session->userdata('eid'));
						$this->db->set('convertedby',$this->session->userdata('eid'));
						if($source['type'] == 'calltrack'){
							$this->db->set('name',$this->input->post('callername'));
							$this->db->set('email',$this->input->post('caller_email'));
							$this->db->set('caller_add',$this->input->post('calleraddress'));
							$this->db->set('caller_bus',$this->input->post('callerbusiness'));
							$this->db->set('refId',$this->input->post('refid'));
						}elseif($source['type'] == 'ivrs' || $source['type'] == 'pbx'){
							$this->db->set('name',$this->input->post('name'));
							$this->db->set('email',$this->input->post('email'));
						}
						$this->db->set('number',$this->input->post('number'));
						$this->db->set('source',$source['type']);
						$this->db->set('keyword',$source['keyword']);
						$this->db->set('createdon',date("Y-m-d H:i:s"));
						$this->db->set('lastmodified',date("Y-m-d H:i:s"));
						$this->db->set('convertedon',date("Y-m-d H:i:s"));
						$this->db->set('lead_status',$leadstat);
						$this->db->set('dis_type',$dis_type);
						$this->db->set('alert_type',$_POST['lalerttype']);
						$this->db->set('status',1);
						$this->db->insert($bid."_leads");
						if($leads_use['type'] == 1)
							$this->db->query("UPDATE business_lead_use SET `leadlimit`=(`leadlimit`-1) WHERE bid='".$bid."'");
						if($this->input->post('lgid') != '' && $dis_type == 2)
							$this->db->query("UPDATE ".$bid."_leads_grpemp SET `counter`=(`counter`+1) WHERE eid='".$assempid."' AND gid='".$this->input->post('lgid')."'");
						if($this->input->post('remark') != ''){
							$this->db->set('leadid',$leadid);
							$this->db->set('bid',$bid);
							$this->db->set('eid',$this->session->userdata('eid'));
							$this->db->set('cdate',date("Y-m-d H:i:s"));	
							$this->db->set('remark',$this->input->post('remark'));
							$this->db->insert($bid."_leads_remarks");
						}
					}else{
						$leadDetails = $leadsql->result_array();
						$leadid = $leadDetails[0]['leadid'];
						if($leadid != '') {
							$this->db->set('lastmodified',date("Y-m-d H:i:s"));
							$this->db->set('convertedby',$this->session->userdata('eid'));
							$this->db->where('leadid',$leadid);
							$this->db->update($bid."_leads");
							if($this->input->post('remark') != ''){
								$this->db->set('leadid',$leadid);
								$this->db->set('bid',$bid);
								$this->db->set('eid',$this->session->userdata('eid'));
								$this->db->set('cdate',date("Y-m-d H:i:s"));	
								$this->db->set('remark',$this->input->post('remark'));
								$this->db->insert($bid."_leads_remarks");
							}
						}
					}
					/* As per discussion with team changed to all groups on 26Th June 2014 Padma*/
					if($source['type'] == 'calltrack'){
						$this->db->query("UPDATE ".$bid."_callhistory SET `leadid`='".$leadid."' WHERE callfrom='".$this->input->post('number')."'");
					}elseif($source['type'] == 'ivrs'){
						$this->db->query("UPDATE ".$bid."_ivrshistory SET `leadid`='".$leadid."' WHERE callfrom='".$this->input->post('number')."'");
					}elseif($source['type'] == 'pbx'){
						$this->db->query("UPDATE ".$bid."_pbxreport SET `leadid`='".$leadid."' WHERE callfrom='".$this->input->post('number')."'");
					}
					$process = "TRUE";
				}
			}
			if($this->input->post('updatelead') == 1){
				if($source['type'] == 'calltrack'){
					$sql = $this->db->query("SELECT c.leadid FROM ".$bid."_callhistory c WHERE callid='".$source['id']."'");
				}elseif($source['type'] == 'ivrs'){
					$sql = $this->db->query("SELECT c.leadid FROM ".$bid."_ivrshistory c WHERE hid='".$source['id']."'");
				}elseif($source['type'] == 'pbx'){
					$sql = $this->db->query("SELECT c.leadid FROM ".$bid."_pbxreport c WHERE callid='".$source['id']."'");
				}
				if($sql->num_rows() > 0){
					$rst = $sql->result_array();
					$leadid = $rst[0]['leadid'];
					$this->db->set('name',($source['type'] == 'calltrack') ? $this->input->post('callername') : $this->input->post('name'));
					$this->db->set('email',($source['type'] == 'calltrack') ? $this->input->post('caller_email') : $this->input->post('email'));
					$this->db->set('caller_add',$this->input->post('calleraddress'));
					$this->db->set('caller_bus',$this->input->post('callerbusiness'));
					$this->db->set('lastmodified',date("Y-m-d H:i:s"));
					$this->db->set('convertedby',$this->session->userdata('eid'));
					$this->db->where('leadid',$leadid);	
					$this->db->update($bid."_leads");
					if($this->input->post('remark') != ''){
						$this->db->set('leadid',$leadid);
						$this->db->set('bid',$bid);
						$this->db->set('eid',$this->session->userdata('eid'));
						$this->db->set('cdate',date("Y-m-d H:i:s"));	
						$this->db->set('remark',$this->input->post('remark'));
						$this->db->insert($bid."_leads_remarks");
					}
					$process = "TRUE";
				}	
			}
			if($process == "TRUE"){
				if(! isset($assempid)){
					$assempid = $this->db->query("SELECT assignto as id FROM ".$bid."_leads WHERE leadid='".$leadid."'")->row()->id;
				}
				$sql = $this->db->query("SELECT empemail,empnumber,empname FROM ".$bid."_employee WHERE eid='".$assempid."'")->result_array();
				//EMail
				if(isset($_POST['lalerttype']) && ($this->input->post('lalerttype') =='3' || $this->input->post('lalerttype') =='1')){
					$message = ($this->input->post('convertlead') == 1) ? "You have assigned to New Lead. The New Lead Details are <br/>" : "Your Lead has been Updated. The Updated Lead Details are <br/>";
					$message .= "<br/>Name:".($source['type'] == 'calltrack') ? $this->input->post('callername') : $this->input->post('name');
					$message .= "<br/>Email:".($source['type'] == 'calltrack') ? $this->input->post('caller_email') : $this->input->post('email');
					$message .= "<br/>Number:".$this->input->post('number');
					$message .= "<br/>Address:".$this->input->post('calleraddress');
					$message .= "<br/>Business:".$this->input->post('callerbusiness');
					$message .= "<br/>Remarks:".$this->input->post('remark');
					$message .= "<br/>Assigned By:".$this->empgetname($this->session->userdata('eid'));
					$body = $this->emailmodel->newEmailBody($message,$sql[0]['empname']);
					$to  = $sql[0]['empemail']; // note the comma
					$subject = ($this->input->post('convertlead')) ? 'Assigned Lead details' : 'Updated Lead Details';
					$this->load->library('email');
					$this->email->from('noreply@mcube.com', 'MCube');
					$this->email->to($to);
					$this->email->subject($subject);
					$this->email->message($body);
					//$this->email->send();
				}
				//SMS
				if(isset($_POST['lalerttype']) && ($this->input->post('lalerttype') =='3' || $this->input->post('lalerttype') =='2')){
					$smsbal = $this->configmodel->smsBalance($bid);
					if($smsbal > 0){
						$message = ($this->input->post('convertlead') == 1) ? "Your New assigned lead details are " : "Your Updated lead details are ";
						$message .= " no:".$this->input->post('number')." and Name:".$this->input->post('callername').".Powered By Mcube";
						$api = "http://115.249.28.90/sms/sendSMS.php?from=vmc.in";
						$sms = $api."&to=".substr($sql[0]['empnumber'],-10,10)."&text=".urlencode($message);
						//$sms = file($sms);
						$this->configmodel->smsDeduct($bid,'1');
					}else{
						$return = "2";
					}
				}
			}
		}
		if($this->input->post('convertsuptkt') || $this->input->post('updatesuptkt')){
			$process = "FALSE";
			$dis_type = 0;
			if($this->input->post('convertsuptkt') == 1){
				$tkt_use = $this->configmodel->supusageCheck($bid,'support');
				if($tkt_use['type'] == 1 && $tkt_use['used'] == 0){
					$process = "FALSE";
					$return = '3';
				}else{		
					$sgid = $this->input->post('sgid');		
					if(($this->input->post('sgid') == '' && $this->input->post('sassignto') == '')){
						$assempid = $this->input->post('assignto');
					}else{
						if($this->input->post('sassignto') != '' && $this->input->post('sassignto') !=0){
							$assempid = $this->input->post('sassignto');
							$dis_type = 1;
						}else{
							if($this->input->post('sgid') != ''){
								$rule = $this->db->query("SELECT group_rule as rule FROM ".$bid."_support_groups WHERE gid='".$sgid."'")->row()->rule;
								if($rule == '2'){
									$resultemp = $this->db->query("SELECT e.eid,COALESCE(((weight/(SELECT sum(weight) FROM ".$bid."_support_grpemp WHERE gid=ge.gid))-(counter/(SELECT sum(counter) FROM ".$bid."_support_grpemp WHERE gid=ge.gid))),0) as pc FROM ".$bid."_employee e LEFT JOIN ".$bid."_support_grpemp ge on e.eid=ge.eid AND ge.status = 1 AND e.status = 1 WHERE ge.gid='".$sgid."' ORDER BY pc DESC LIMIT 0,1")->result_array();
									if(count($resultemp) > 0)
										$assempid = $resultemp[0]['eid'];
								}elseif($rule == '1'){
									$eid = $this->db->query("SELECT ge.eid FROM ".$bid."_support_grpemp ge LEFT JOIN ".$bid."_employee e ON ge.eid = e.eid WHERE ge.gid='".$sgid."' AND ge.bid='".$bid."' AND ge.status = 1 AND e.status = 1 ORDER BY ge.counter ASC LIMIT 0,1")->row()->eid;
									if($eid != '')
										$assempid = $eid;
								}
								$dis_type = 2;
							}
						}
					}
					$tktid = $this->db->query("SELECT COALESCE(MAX(`tktid`),0)+1 as id FROM ".$bid."_support_tickets")->row()->id;
					$ticket_id = $this->db->query("SELECT COALESCE(MAX(`ticket_id`),0)+1 as tktid FROM ".$bid."_support_tickets")->row()->tktid;
					if(isset($_POST['sgid']) && $_POST['sgid'] != '')
						$this->db->set('gid',$sgid);
					$this->db->set('tktid',$tktid);
					$this->db->set('bid',$bid);
					$this->db->set('assignto',$assempid);
					$this->db->set('enteredby',$this->session->userdata('eid'));
					$this->db->set('name',($source['type'] == 'calltrack') ? $this->input->post('callername') : $this->input->post('name'));
					$this->db->set('email',($source['type'] == 'calltrack') ? $this->input->post('caller_email') : $this->input->post('email'));
					$this->db->set('caller_add',$this->input->post('calleraddress'));
					$this->db->set('caller_bus',$this->input->post('callerbusiness'));
					$this->db->set('tkt_level',$this->input->post('tkt_level'));
					$this->db->set('tkt_esc_time',$this->input->post('tkt_esc_time'));
					$this->db->set('tkt_status','1');
					//$this->db->set('dialstatus',$itemDetail['dialstatus']);
					//$this->db->set('filename',$itemDetail['filename']);
					//$this->db->set('remark',$this->input->post('remark'));				
					$this->db->set('number',$this->input->post('number'));
					$this->db->set('source',$source['type']);
					$this->db->set('ticket_id',$ticket_id);
					$this->db->set('dis_type',$dis_type);
					$this->db->set('refId',$this->input->post('refid'));
					$this->db->set('createdon',date("Y-m-d H:i:s"));
					$this->db->set('status',1);
					$this->db->insert($bid."_support_tickets");
					if($tkt_use['type'] == 1)
						$this->db->query("UPDATE business_support_use SET `supportlimit`=(`supportlimit`-1) WHERE bid='".$bid."'");
					($this->input->post('sgid') != '') ? $this->db->query("UPDATE ".$bid."_support_grpemp SET `counter`=(`counter`+1) WHERE eid='".$assempid."' AND gid='".$this->input->post('sgid')."'") : '';
					if($this->input->post('remark') != ''){
						$this->db->set('tktid',$tktid);
						$this->db->set('bid',$bid);
						$this->db->set('eid',$this->session->userdata('eid'));
						$this->db->set('cdate',date("Y-m-d H:i:s"));	
						$this->db->set('remark',$this->input->post('remark'));
						$this->db->insert($bid."_support_remarks");
					}
					/* As per discussion with team changed to all groups on 26Th June 2014 Padma*/
					if($source['type'] == 'calltrack'){
						$this->db->query("UPDATE ".$bid."_callhistory SET `tktid`='".$tktid."' WHERE callfrom='".$this->input->post('number')."'");
					}elseif($source['type'] == 'ivrs'){
						$this->db->query("UPDATE ".$bid."_ivrshistory SET `tktid`='".$tktid."' WHERE callfrom='".$this->input->post('number')."'");
					}elseif($source['type'] == 'pbx'){
						$this->db->query("UPDATE ".$bid."_pbxreport SET `tktid`='".$tktid."' WHERE callfrom='".$this->input->post('number')."'");
					}
					$process = "TRUE";
				}
			}
			if($this->input->post('updatesuptkt') == 1){
				if($type == 'calltrack'){
					$sql = $this->db->query("SELECT c.tktid FROM ".$bid."_callhistory c WHERE callid='".$source['id']."'");
				}elseif($type == 'ivrs'){
					$sql = $this->db->query("SELECT c.tktid FROM ".$bid."_ivrshistory c WHERE hid='".$source['id']."'");
				}elseif($type == 'pbx'){
					$sql = $this->db->query("SELECT c.tktid FROM ".$bid."_pbxreport c WHERE callid='".$source['id']."'");
				}
				if($sql->num_rows() > 0){
					$rst = $sql->result_array();
					$tktid = $rst[0]['tktid'];
					$remark = $rst[0]['remark'];
					$this->db->set('name',($source['type'] == 'calltrack') ? $this->input->post('callername') : $this->input->post('name'));
					$this->db->set('email',($source['type'] == 'calltrack') ? $this->input->post('caller_email') : $this->input->post('email'));
					$this->db->set('caller_add',$this->input->post('calleraddress'));
					$this->db->set('caller_bus',$this->input->post('callerbusiness'));
					//$this->db->set('enteredby',$this->session->userdata('eid'));
					$this->db->set('lastmodified',date("Y-m-d H:i:s"));
					$this->db->where('tktid',$tktid);	
					$this->db->update($bid."_support_tickets");
					if($this->input->post('remark') != ''){
						$this->db->set('tktid',$tktid);
						$this->db->set('bid',$bid);
						$this->db->set('eid',$this->session->userdata('eid'));
						$this->db->set('cdate',date("Y-m-d H:i:s"));	
						$this->db->set('remark',$this->input->post('remark'));
						$this->db->insert($bid."_support_remarks");
					}
					$process = "TRUE";
				}	
			}
			if($process == "TRUE"){
				$sql = $this->db->query("SELECT empemail,empnumber,empname FROM ".$bid."_employee WHERE eid='".$assempid."'")->result_array();
				//EMail
				if(isset($_POST['salerttype']) && ($this->input->post('salerttype') =='3' || $this->input->post('salerttype') =='1')){
					$message = ($this->input->post('convertsuptkt') == 1) ? "You have assigned to New support ticket. The New ticket Details are <br/>" : "Your support ticket has been Updated. The Updated support ticket Details are <br/>";
					$message .= "<br/>Name:".($source['type'] == 'calltrack') ? $this->input->post('callername') : $this->input->post('name');
					$message .= "<br/>Email:".($source['type'] == 'calltrack') ? $this->input->post('caller_email') : $this->input->post('email');
					$message .= "<br/>Number:".$itemDetail['callfrom'];
					$message .= "<br/>Address:".$this->input->post('calleraddress');
					$message .= "<br/>Business:".$this->input->post('callerbusiness');
					$message .= "<br/>Remarks:".$this->input->post('remark');
					$message .= "<br/>Assigned By:".$this->empgetname($this->session->userdata('eid'));
					$body = $this->emailmodel->newEmailBody($message,$sql[0]['empname']);
					$to  = $sql[0]['empemail']; // note the comma
					$subject = ($this->input->post('convertsuptkt') == 1) ? 'Assigned Support Ticket details' : 'Updated Support Ticket Details';
					$this->load->library('email');
					$this->email->from('noreply@mcube.com', 'MCube');
					$this->email->to($to);
					$this->email->subject($subject);
					$this->email->message($body);
					$this->email->send();
				}
				//SMS
				if(isset($_POST['salerttype']) && ($this->input->post('salerttype') =='3' || $this->input->post('salerttype') =='2')){
					$smsbal = $this->configmodel->smsBalance($bid);
					if($smsbal > 0){
						$message = ($this->input->post('convertsuptkt')) ? "Your New assigned support ticket details are " : "Your Updated support details are ";
						$message .= " no:".$itemDetail['callfrom']." and Name:".$this->input->post('callername').". Powered By Mcube";
						$api = "http://115.249.28.90/sms/sendSMS.php?from=vmc.in";
						$sms = $api."&to=".substr($sql[0]['empnumber'],-10,10)."&text=".urlencode($message);
						$sms = file($sms);
						$this->configmodel->smsDeduct($bid,'1');
					}else{
						$return = "2";
					}
				}
			}
		}
		 return $return;
	}

	function empgetname($eid){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$empname=$this->db->query("SELECT empname FROM ".$bid."_employee WHERE eid='".$eid."'")->row()->empname;
		return ($empname != '') ? $empname : '';
	}

}
/* end  */
?>
