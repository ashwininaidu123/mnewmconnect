<?php
class Adminmodel extends Model {
	var $data;
    function Adminmodel(){
        parent::Model();
        //$this->load->model('auditlog');
    }
    function init(){
		
    }
    function acc_settings($bid){
		$arr = array_keys($_POST);
		$sql = " REPLACE INTO account_settings SET ";
		for($k=0;$k<count($arr);$k++){
			if($arr[$k] != 'update_system'){
				$sql .= $arr[$k]." = '".$_POST[$arr[$k]]."' , ";
			}
		}
		$sql .= " bid = ".$bid;
		$result = $this->db->query($sql);
		return $result;
	}
    function getAccSettings($bid){
		$sql = "SELECT * FROM account_settings WHERE bid='".$bid."'";
		$result = $this->db->query($sql)->row_array();
		return $result;
	}
}
?>
