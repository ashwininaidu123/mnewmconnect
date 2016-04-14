<?php
class Admin extends controller
{
	var $data,$roleDetail;
	function Admin(){
		parent::controller();
		if(!$this->session->userdata('logged_in'))redirect('/site/login');
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('systemmodel');
		$this->load->model('adminmodel');
		$this->roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
	}
	function index(){
		redirect('Admin/editprofile');
	}
	function acc_config(){
		$roleDetail = $this->roleDetail;
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		if(isset($_POST['update_system'])){
			$res = $this->adminmodel->acc_settings($bid);
			if($res == 1){
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg', "Account Settings Changed Successfully");
				$this->auditlog->auditlog_info('Account Settings', "Account Settings changed by ".$this->session->userdata('username'));
			}
		}
		$itemDetail = $this->adminmodel->getAccSettings($bid);
		$formFields = array();
		$formFields[] = array('label'=>"<label class='col-sm-4 text-right'>Outbound Call Feature: </label> ",
				  'field'=>form_checkbox(array(
										'name'        => 'obc_feature',
										'id'          => 'obc_feature',
										'value'       => '1',
										'class'		  => '',
										'checked'	  => (isset($itemDetail['obc_feature']) && $itemDetail['obc_feature'] == 1) ? 'TRUE' : ''
				  				) )
					);
		$formFields[] = array('label'=>"<label class='col-sm-4 text-right'>Download Notification: </label> ",
				  'field'=>form_checkbox(array(
										'name'        => 'down_notify',
										'id'          => 'down_notify',
										'value'       => '1',
										'class'		  => '',
										'checked'	  => (isset($itemDetail['down_notify']) && $itemDetail['down_notify'] == 1) ? 'TRUE' : ''
				  				) )
					);
		$formFields[] =array("label"=>"<label for='followup' class='col-sm-4 text-right'>Support Auto Followup :</label>"
									,"field"=>form_checkbox(array(
										'name'        => 'sup_followup',
										'id'          => 'sup_followup',
										'class'		  => '',
										'value'       => '1',
										'checked'	  => (isset($itemDetail['sup_followup']) && $itemDetail['sup_followup'] == 1) ? 'TRUE' : ''
				  				) )
							);
		$formFields[] =array("label"=>"<label for='followup' class='col-sm-4 text-right'>Support Followup Interval :</label>"
									,"field"=>form_input(array(
												'name'      => 'sup_interval',
												'id'        => 'sup_interval',
												'value'		=> isset($itemDetail['sup_interval']) ? $itemDetail['sup_interval'] : '',
												'class'		=> 'form-control',
												'style'		=> 'width:150px;'
												
											))."&nbsp;&nbsp;(Hours)&nbsp;&nbsp;");	
		$data['module']['title'] = "Account Configuration";
		$data['form'] = array(
		            'form_attr'=>array('action'=>'AccountSettings/','name'=>'accconfig','id'=>'accconfig','enctype'=>"multipart/form-data"),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
}
?>
