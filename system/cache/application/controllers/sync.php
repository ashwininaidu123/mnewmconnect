<?php
class Sync extends Controller {
	function Sync(){
		parent::controller();
		$this->load->model('syncmodel');
	}
	
	function index(){
		echo "This is for DB Sync";
	}
	
	function auth($key){
		return ($key=='tapan')? true:false;
	}
	
	function getBusiness(){
		if(!$this->auth($_POST['key'])){
			echo json_encode(array("msg"=>"Unauthorised Request"));exit;
		}
		$rec = $this->syncmodel->getBusiness();
		echo json_encode($rec);
	}
	
	function savefile(){
		//$fp =fopen("process.log","a");fwrite($fp,$_FILES['file']['name']);fclose($fp);
		if(!$this->auth($_POST['key'])){
			echo json_encode(array("msg"=>"Unauthorised Request"));exit;
		}
		
		$ret = true;
		if($_FILES['file']['error']==0){
			$serverid = $_POST['serverid'];
			$ret = move_uploaded_file($_FILES['file']['tmp_name'],$this->config->item('sound_path').$_FILES['file']['name']);
		}else{
			$ret = false;
		}
		$msg = array("msg"	=>  ($ret)? "File upliad Success" : "File upliad failed",
					 "value"=>	($ret)? "1" : "0"
					);
		echo json_encode($msg);
	}
}
?>
