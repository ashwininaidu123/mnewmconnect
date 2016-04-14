<?php
  
class Dashboard extends Controller {
	var $data;
	function Dashboard(){
		parent::Controller();
		if(!$this->session->userdata('logged_in'))redirect('/site/login');
		$this->load->model('sysconfmodel');
		$this->load->model('systemmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('reportmodel');
		$this->load->model('colormodel');
		$this->load->model('dashboardmodel');
		$this->session->set_userdata('cbid',$this->session->userdata('bid'));
		$this->session->unset_userdata('filter');
	}

	public function __destruct() {
		$this->db->close();
	}
	function index(){
			$data=array(
			 'prop'=>$this->dashboardmodel->propvist(),
			 'totalcount'=>$this->dashboardmodel->totalcount(),
			 'sitevist'=>$this->dashboardmodel->sitevist(),
			 'visitcount'=>$this->dashboardmodel->visitcount(),
			 'likecount'=>$this->dashboardmodel->likecount(),
			 'feature'=>$this->feature_access());
		$this->sysconfmodel->viewLayout('dashboard',$data);
	}
	function feature_access(){
		$show=0;
		$data1=array();
		$checklist=$this->systemmodel->checked_featuremanage();
		if(in_array(17,$checklist))	
		$data1['mconnect']='1';
		return $data1;
	}
}
?>
