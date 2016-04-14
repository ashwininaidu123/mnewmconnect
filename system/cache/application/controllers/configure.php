<?php
class Configure extends Controller {
	var $data;
	function Configure(){
		parent::Controller();
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('commonmodel');
		$this->load->model('empmodel');
		$this->load->model('profilemodel');
		$this->load->model('configuremodel','cM');
		$this->load->helper('url');
		$this->load->library('validation');
		$this->load->library('form_validation');
	}
	function index(){
		$glnumber='';
		$pnumber='';
		foreach($this->cM->demoPris() as $prs){
			if($prs['type']==0){
				$glnumber=$prs['landingnumber'];
				$gn = $prs['number'];
			}
			if($prs['type']==2){
				$pnumber=$prs['landingnumber'];
				$pn = $prs['number'];
			}
		}	
		$empDetails=$this->empmodel->get_employee($this->session->userdata('eid'));
		$data['module']['title'] = "Configuration";
		$formFields = array();$formFields1 = array();
		$cf=array('label'=>"<label><div id='FrmError'></div></label>",
				  'field'=>'');
		array_push($formFields,$cf);
		$cf=array('label'=>"<label>Welcome Greetings : ".'<img src="system/application/img/icons/help.png" title="Please upload a mp3/wav file with a welcome message" ></label>',				  'field'=>form_input(array(
									  'name'        => 'greetings',
										'id'          => 'greetings',
										'value'       => '',
										'type'=>'file'
				  				)));
		array_push($formFields,$cf);
		$cf=array('label'=>"<label>Calltrack Name:  ".'<img src="system/application/img/icons/help.png" title="A unique Name to identify the Calltrack Group" ></label>',
				  'field'=>form_input(array(
									  'name'        => 'groupname',
										'id'          => 'groupname',
										'value'       => '',
										'class'		=>'required'
				  				) 
				  		)." Associated to ".$glnumber
						);
		array_push($formFields,$cf);
		$cf=array('label'=>"<label>PBX Name :  ".'<img src="system/application/img/icons/help.png" title="Unique Name to identify PBX Group" ></label>',
				  'field'=>form_input(array(
									  'name'        => 'pbxname',
										'id'          => 'pbxname',
										'value'       => '',
										'class'		=>'required'
				  			) 
				  		)." Associated to ".$pnumber
					);
					array_push($formFields,$cf);
						
		$cf=array('label'=>"<label>Employees : ".'<img src="system/application/img/icons/help.png" title="Add Employees With 3 Digit Extension to be added on PBX make Sure Mobile Number is valid and Correct " ></label>',
				  'field'=>'<a href="javascript:void(0)"><span title="Add" id="addmore" class="glyphicon glyphicon-plus-sign"></span></a>');
		array_push($formFields,$cf);
		$cf=array('label'=>form_input(array(
									  'name'        => 'emp[0][empname]',
										'placeholder'       => 'Name',
										'value'       => $empDetails[0]['empname'],
										'class'		=>'required',
										'readonly'=>true
				  			) ,'',"style='width:100px;'"
				  		).'&nbsp;&nbsp;'
				  		,
				  'field'=>form_input(array(
									  'name'        => 'emp[0][empemail]',
										'placeholder'       => 'Email',
										'value'       => $empDetails[0]['empemail'],
										'class'		=>'required email',
										'readonly'=>true
				  			) ,'',"style='width:150px;'"
				  		)."&nbsp;&nbsp;".form_input(array(
									  'name'        => 'emp[0][number]',
										'placeholder'       => 'Number',
										'value'       => $empDetails[0]['empnumber'],
										'class'		=>'required number',
										'readonly'=>true
				  			) ,'',"style='width:100px;'"
				  		)."&nbsp;&nbsp;".
				  		form_input(array(
									  'name'        => 'emp[0][ext]',
										'placeholder'       => 'Ext',
										'class'		=>'required number'
										
				  			) ,'',"style='width:50px;'"
				  		)."&nbsp;&nbsp;".
				  		form_radio(array(
									  'name'        => 'owner',
										'value'       => '0',
										'checked'	=> true
				  			)
				  		)." as Owner"
				  );
		array_push($formFields,$cf);
		$data['form'] = array(
		        'form_attr'=>array('action'=>'configure/add/','name'=>'configure'),
				'fields'=>$formFields,
				'fields1'=>$formFields1,
				'close'=>form_close()
			);
		$this->load->view('form_view',$data);
	}
	function add(){
		if($this->input->post('update_system')){
			$res=$this->cM->Configuration();
			$this->session->set_flashdata('msgt',($res!="")?'success':'error');
			$this->session->set_flashdata('msg',($res!="")?'Thank you For Creating a Demo Account ,Your Account will be active for 7 Days':'Error Occur Check in concern section' );
		}
		redirect('ManageCustom');
		
	}
	

}
