<?php
class Sitenotavailable extends Controller {
	function Sitenotavailable(){
		parent::controller();
		$this->load->model('sysconfmodel');
	}
	
	function index(){
		$this->load->view('siteerror');
	}
}
?>
