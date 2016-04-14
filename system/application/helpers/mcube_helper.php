<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('editor'))
{
	function editor(){
		$content='<script type="text/javascript">
					tinyMCE.init({
					// General options
					mode : "textareas",
					theme : "advanced",
					plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",
					 
					// Theme options
					theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					theme_advanced_resizing : false,
					});
					</script>';

					return $content;
			//print_r($this->session->all_userdata());
	}
	function filter_dnd($mobile){
		$dnd=array();
		$curl_handle=curl_init();
		$url = "http://180.179.200.180/filter.php?num=".$mobile;
		curl_setopt($curl_handle,CURLOPT_URL,$url);
		curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
		curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
		$buffer = curl_exec($curl_handle);
		curl_close($curl_handle);
		if (!empty($buffer))
		{
			 $dnd = json_decode($buffer);
		}
		return $dnd;
	}
	function sms_send($number,$message){
		 //$api = "http://180.179.200.180/mcubesms.php?from=vmc.in";
		  $api     = "http://115.249.28.90/sms/sendSMS.php?from=VMCIND";
		if(preg_match('/^[7-9][0-9]{9}$/',substr($number,-10,10))){
			$remsg = $message;
			$reply = $api."&to=".substr($number,-10,10)."&text=".urlencode($remsg);
			$ret = file($reply);
			$fp =fopen("NDNC.txt","a");fwrite($fp,"\n".'['.date('Y-m-d H:i:s').'] reply:'.$reply.'===>'.serialize($ret));fclose($fp);
			return $ret;
			}
	  }
	  function save_search_count($bid,$modid,$eid){
			$ci =& get_instance();
		    $ci->load->database();
		    $sql=$ci->db->query("SELECT count(*) as cnt from ".$bid."_savesearch where eid='".$eid."' and modid='".$modid."'");
		    $row=(array)$sql->row();
		    return $row['cnt'];
	  }
	  function Save_search_data($bid,$eid,$data,$modid){
			$ci =& get_instance();
		    $ci->load->database();
		    $search_id=$ci->db->query("SELECT COALESCE(MAX(`search_id`),0)+1 as id FROM ".$bid."_savesearch")->row()->id;
		    $ci->db->set('search_id',$search_id);
		    $ci->db->set('search_name',$_POST['searchname']);
		    $ci->db->set('content',$data);
		    $ci->db->set('eid',$eid);
		    $ci->db->set('status','1');
		    $ci->db->set('modid',$modid);
		    $ci->db->insert($bid."_savesearch");
	  }	
	  function get_save_searchnames($bid,$modid,$eid){
			$ci =& get_instance();
		    $ci->load->database();
		    $search=$ci->db->query("SELECT * FROM ".$bid."_savesearch where eid='".$eid."' and modid='".$modid."'");
		    $row=$search->result_array();
		    return $row;
	  }	
	  function get_save_searchrow($bid,$modid,$eid,$search_id){
			$ci =& get_instance();
		    $ci->load->database();
		    $search=$ci->db->query("SELECT * FROM ".$bid."_savesearch where eid='".$eid."' and modid='".$modid."' and search_id='".$search_id."'");
		    $row=(array)$search->row();
		    return $row;
	  }	
	  function remove_search($search_id,$bid){
			$ci =& get_instance();
		    $ci->load->database();
		   $search=$ci->db->query("Delete from ".$bid."_savesearch where search_id='".$search_id."'");
		   return 1;
	  }
	  function MCubeMail($to,$subject,$body){
		$data = "";
		$api = "http://124.153.117.98/mcubemail/sendmail.php";
		$data.= "&to=".urlencode($to);
		//$data.= "&to=".urlencode(implode(",",$this->_recipients));
		$data.= "&subject=".urlencode($subject);
		$data.= "&message=".urlencode($body);
		//$api.= "&header=".urlencode($this->_header_str);

		$objURL = curl_init($api);
		curl_setopt($objURL, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($objURL,CURLOPT_POST,1);
		curl_setopt($objURL, CURLOPT_POSTFIELDS,$data);
		$retval = trim(curl_exec($objURL));
		curl_close($objURL);
		//$ret = serialize($retval);
		//print_r($ret);

		//if(file($api) == '1' ) return true;
		//else return false;
		return true;
	  }	  
}


?>
