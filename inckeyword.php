<?php
$con = mysql_connect("localhost","root","");
mysql_select_db("m3", $con);
if(isset($_REQUEST))
{
	$cod=trim($_REQUEST['code']);
	$from=trim($_REQUEST['mobile']);
	$msg=trim($_REQUEST['msg']);
	$com_key=preg_split("/[\s]+/",$msg);


	$keyword=$com_key[0];
	$sub_keyword=$com_key[1];
	$message=$com_key[2];
	$ins=mysql_query("INSERT INTO `incoming_master`(`lead_id`,`code`,`keyword`,`subkeyword`,`from`,`message`,`date_time`)VALUES(NULL,$cod,'$keyword','$sub_keyword','$from','$message',CURRENT_TIMESTAMP)");
	$last_id=mysql_insert_id();
	
	$get_codeid=mysql_query("select codeid from shortcode where code=$cod");
	$codid=mysql_fetch_array($get_codeid); 	
	
	
	$get_businessid=mysql_query("select * from keword where `keyword`='$keyword' and code_id=".$codid['codeid']);
	if(mysql_num_rows($get_businessid)>0){
		
	$res=mysql_fetch_array($get_businessid);
	$reply_msg=$res['default_msg'];

	$get_subkeyword=mysql_query("select * from subkeywordid where subkeyword='$sub_keyword' and keyword_id=$res[keyword_id] and code_id=$codid[codeid]");
	if(mysql_num_rows($get_subkeyword)>0){
	$subres=mysql_fetch_array($get_subkeyword);
	$reply_msg=$subres['replymsg'];
	}
	//echo $res['keyword_id']."   ".$subres['subkeyword_id']."   ".$codid['codeid'];
	switch($res['fowardto_type']){
		
		case 'employee':
				mysql_query("INSERT INTO ".$res['bid']."_keywordinbox(`incid`,`bid`,`code_id`,`lead_id`,`keyword_id`,`subkey_id`,`from`,`keyword`,`subkeyword`,`date_time`,`eid`)VALUES(NULL,$res[bid],$codid[codeid],$last_id,$res[keyword_id],$subres[subkeyword_id],$from,'$keyword','$sub_keyword',CURRENT_TIMESTAMP,$res[forwardto_id])"); 		
				echo $reply_msg;
				break;
		case 'group':
						
						$get_emps=mysql_query("select * from ".$res['bid']."_group_emp where gid=".$res['forwardto_id']);
						if(mysql_num_rows($get_emps)>0){
							$empss=array();
							while($emps=mysql_fetch_array($get_emps))
							{
								$empss[]=$emps['eid'];
								
							}
							$final=array();
							for($i=0;$i<sizeof($empss);$i++)
							{
									$select_e=mysql_query("select * from ".$res['bid']."_keywordinbox where eid=".$empss[$i]);
									$final[$empss[$i]]=mysql_num_rows($select_e);
								
							}
							asort($final);
							$x=current($final); 
							$rr=array_search($x,$final);
							mysql_query("INSERT INTO ".$res['bid']."_keywordinbox(`incid`,`bid`,`code_id`,`lead_id`,`keyword_id`,`subkey_id`,`from`,`keyword`,`subkeyword`,`date_time`,`eid`)VALUES(NULL,$res[bid],$codid[codeid],$last_id,$res[keyword_id],$subres[subkeyword_id],$from,'$keyword','$sub_keyword',CURRENT_TIMESTAMP,$rr)"); 		
								echo $reply_msg;
							}
						
				break;		
		
		
		
	} 
	
	
	
	
	
	 	
	}
	
	
	

	
	

	
	
	
}









?>
