<?php
Class Syncmodel extends Model{
	function Syncmodel(){
		parent::Model();
	}
	
	function getBusiness(){
		$sql = "SELECT * FROM business";
		return $this->db->query($sql)->result_array();
	}
}
