<?php
class Executive extends controller{
	var $data;
	function Executive(){
		parent::controller();
		if(!$this->session->userdata('exe_in') && $this->uri->segment('2')!="index")redirect('/Executive/index');
		$this->load->model('executivemodel','exmodel');
		$this->data = $this->exmodel->init();
		$this->load->model('commonmodel');
	}
	function index(){
		if($this->input->post('submit')){
				$this->load->helper('url');
				$this->load->library('validation');
				$rules['login_username']= "required|min_length[4]|max_length[100]|Email";
				$rules['login_password']= "required|min_length[4]|max_length[32]";		
				$rules['validator']			= "required|callback_check_captcha";		
				$this->validation->set_rules($rules);
				$fields['login_username'] = 'Username';
				$fields['login_password'] = 'Password';
				
				$this->validation->set_fields($fields);
						
				if ($this->validation->run() == false) {
					
					//If you are using OBSession you can uncomment these lines
					$flashdata = array('msgt' => 'error', 'msg' => 'Invalid Username/Password');
					$this->session->set_flashdata($flashdata);
					redirect('/Executive/index');
				} else {
					if($this->simplelogin->exlogin($this->input->post('login_username'), $this->input->post('login_password'))=='1'){
						redirect('/Executive/available');
					}
					else{		
						if($this->session->userdata('flash:new:msg')!=""){
							redirect('Executive/index');
						}else{
							$flashdata = array('msgt' => 'error', 'msg' => 'Invalid Username/Password');
							$this->session->set_flashdata($flashdata);
							redirect('Executive/index');	
						}
						
					}
					
				}
		}
		$this->simplelogin->logout();
		$this->data['html']['title'] .= " | Executive Login";
		$this->load->view('siteheader',$this->data);
		$this->load->view('exlogin',$this->data);
		$this->load->view('footer',$this->data);
	}
	function check_captcha($str){
		if($str!=$_SESSION['security_code']){
		$this->form_validation->set_message('check_captcha', 'The '.$str.' is not valid security code');
			return FALSE;
		}else{
			return true;
		}
	}
	function AddNumber($id=''){
		if($this->input->post('update_system')){
			if($_FILES['cfile']['size']>0){
				$this->form_validation->set_rules('cfile', 'Filename', 'callback_file_extensions');
				if (!$this->form_validation->run() == FALSE){
					$res=$this->exmodel->addnumbers();
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', "Numbers added Successfully");
					redirect('Executive/available');
				}
			}else{
				$this->form_validation->set_rules('number', 'Number', 'required');
				$this->form_validation->set_rules('type', 'Type', 'required');
				$this->form_validation->set_rules('operator', 'Operator', 'required');
				$this->form_validation->set_rules('region', 'Region', 'required');
				$this->form_validation->set_rules('pool', 'pool', 'required');
				if (!$this->form_validation->run() == FALSE){
					$res=$this->exmodel->EditNumber();
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', "Number added Successfully");
					redirect('Executive/available');
				}
			}
		}
		$data['file'] = "";
		$this->exmodel->data['html']['title'] .= " | Add Number";
		$data['module']['title'] = "Add Number";
		$formFields = array();$formFields1 = array();
		$formFields[]=array('label'=>"<label>Number :</label>",
				  'field'=>form_input(array(
									  'name'        => 'number',
										'id'          => 'number',
										'value'       => '',
										'class'		=>'required'
				  				)
				  		)
							);
		$type=array(""=>"Select","1"=>"Normal","2"=>"VIP","3"=>"Silver","4"=>"Gold");					
		$formFields[]=array('label'=>"<label>Type :</label>",
				  'field'=>form_dropdown('type',$type,''," id='type' class='required'"));
		$operators=array(""=>"select","Reliance"=>"Reliance","Airtel"=>"Airtel","Vadofone"=>"vadofone","TataDocomo"=>"Tata Docomo");
		$formFields[]=array('label'=>"<label>Operator :</label>",
				  'field'=>form_dropdown('operator',$operators,''," id='operator' class='required'"));		
		$formFields[]=array('label'=>"<label>Region :</label>",
				  'field'=>form_input(array(
									  'name'        => 'region',
										'id'        => 'region',
										'value'     => '',
										'class'		=>'required'
				  				)
				  		)
							);		    
		$formFields[]=array('label'=>"<label>Pool:</label>",
				  'field'=>form_input(array(
									  'name'        => 'pool',
										'id'        => 'pool',
										'value'     => '',
										'class'		=>'required'
				  				)
				  		)
							);		    
		$formFields[]=array('label'=>"<label></label>",
				  'field'=>"or"	);		    
		  $formFields[]=array('label'=>"<label>File :</label>",
		  'field'=>form_input(array(
							  'name'          => 'cfile',
								'id'          => 'cfile',
								'value'       => '',
								'type'		  => 'file'
						)
				)
					);
		  $formFields[]=array('label'=>"<label></label>",
		  'field'=>"Sample file <a href='".site_url()."download.php?file=numbers.csv'>Click here</a> to download"
					);
					
					
					
			$data['form'] = array(
			        'form_attr'=>array('action'=>'Executive/AddNumber/','name'=>'AddNumbers'),
					//~ 'open'=>form_open_multipart('Executive/AddNumber/',array('name'=>'AddNumbers','class'=>'form','id'=>'AddNumbers','method'=>'post')),
					'fields'=>$formFields,
					'fields1'=>$formFields1,
					'adv_search'=>array(),
					'close'=>form_close()
				);
		$this->exmodel->viewLayout('form_view',$data);
		
	}
	function logout(){
		//Logout
		$this->simplelogin->logout();
		redirect('/Executive');
	}
	function file_extensions($str){
		$allow_types=array("text/csv","csv","application/vnd.ms-excel");
		if($_FILES['filename']['size']>0){
			if(!in_array($_FILES['cfile']['type'],$allow_types)){
				$this->form_validation->set_message('file_extensions', 'File Extension Not Allowed');
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			return true;
		}
	}
	function available($page=''){
		$formFields=array();
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$this->exmodel->data['file'] = "system/application/js/ex.js";
		$limit = '30';
		$header = array("<a href='javascript://'><span id='c_all' class='glyphicon glyphicon-gok'></span></a>","id","Number","Type","Operator","Region","Pool","Availability Date","Last Updated","Status");
		$number_type = array(""=>"Select","1"=>"Normal","2"=>"VIP","3"=>"Silver","4"=>"Gold");
		$status = array(""=>"Select","0"=>"Available","1"=>"Blocked","2"=>"Not Available");
		$data['itemlist']['header'] = $header;
		$emp_list=$this->exmodel->availableNumbers($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;
		foreach ($emp_list['data'] as $item){
			$rec[] = array(
				'<input type="checkbox" name="avnumbers[]" id="avnumbers" class="anumbers" value="'.$item['number'].'"/>' 
				,$i
				,$item['number']
				,$number_type[$item['type']]
				,$item['operator']
				,$item['region']
				,$item['simtaken']
				,$item['available_dt']
				,$item['lastupdate_dt']
				,'<a href="Executive/editnumber/'.$item['number'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Edit" class="fa fa-edit"></span></a>'
				
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Executive/available/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$formFields[] = array('label'=>'<label for="filename">Number&nbsp;&nbsp;:</label>',
							   'field'=>form_input(array(
										'name'      => 'number',
										'id'        => 'number',
										'class'     => '',
										'type'	  => 'text')
										)
								);
		$formFields[] = array('label'=>'<label for="filename">Type&nbsp;&nbsp;: </label>',
						   'field'=>form_dropdown('type',$number_type,'','id="type" class="auto"')
							);
		$formFields[] = array('label'=>'<label for="filename">Operator&nbsp;&nbsp;: </label>',
						   'field'=>form_input(array(
										'name'      => 'operator',
										'id'        => 'operator',
										'class'     => '',
										'type'	  => 'text')
										)
							);
		$formFields[] = array('label'=>'<label for="filename">Region&nbsp;&nbsp;: </label>',
						   'field'=>form_input(array(
										'name'      => 'region',
										'id'        => 'region',
										'class'     => '',
										'type'	  => 'text')
										)
							);
		$formFields[] = array('label'=>'<label for="filename">Sim Taken From&nbsp;&nbsp;: </label>',
						   'field'=>form_input(array(
										'name'      => 'simtaken',
										'id'        => 'simtaken',
										'class'     => '',
										'type'	  => 'text')
										)
							);
		$data['module']['title'] = "Available Numbers [".$data['itemlist']['count']."]";
		
		$data['links']=($this->session->userdata('isadmin') == 1)?'<a href="Executive/Addnumber/"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>&nbsp;':'';
		$data['links'].='<a href="Executive/SendNumbers" class="snumbers"><span title="Send Mail Request" class="fa fa-envelope"></span></a>&nbsp;';
		$data['links'].='<a href="Executive/blknumber/" class="block"><img src="system/application/img/icons/block.png" title="Blocking Request" width="16" height="16" /></a>&nbsp;';
		$formFields1 = array();
		$data['form'] = array(
							'open'=>form_open_multipart('Executive/available/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'close'=>form_close(),
							'adv_search'=>array(),
							'title'=>$this->lang->line('level_search')
							);
		$this->exmodel->viewLayout('list_view',$data);
	}
	function Addexecutive($id=''){	
		if($id != ''){
			$itemDetail = $this->exmodel->execDetails($id);
		}
		if($this->input->post('update_system')){
			$id = isset($_POST['id']) ? $_POST['id'] : '';
			$this->form_validation->set_rules('name', 'name', 'required');
			if($id == ''){
				$this->form_validation->set_rules('email', 'email', 'required|email');
				$this->form_validation->set_rules('password', 'password', 'required');
			}
			//$this->form_validation->set_rules('number', 'number', 'required');
			if (!$this->form_validation->run() == FALSE){
				$res = $this->exmodel->addexecutive($id);
				if($res){
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', "Executive added Successfully");
					redirect('Executive/listExecutives');
				}else{
					$this->session->set_flashdata('msgt', 'error');
					$this->session->set_flashdata('msg', "Error while adding Executive ");
					redirect('Executive/Addexecutive');
				}
			}
		}
		$data['file'] = "";
		$this->exmodel->data['html']['title'] .= " | Add Executive";
		$data['module']['title'] = "Add Executive";
		$formFields = array();

		$formFields[] = array('label'=>'<label for="filename">Name:</label>',
							   'field'=>form_input(array(
										'name'      => 'name',
										'id'        => 'name',
										'class'     => 'required',
										'value'		=> (isset($itemDetail['name']) ? $itemDetail['name'] : ''),
										'type'	    => 'text')
										)
								);
		 if($id!=''){							
		$formFields[] = array('label'=>'<label for="filename">Email:</label>',
							   'field'=>form_input(array(
										'name'      => 'email',
										'id'        => 'email',
										'class'     => 'required',
										'value'		=> (isset($itemDetail['email']) ? $itemDetail['email'] : ''),
										'readonly'  => (isset($id) ? 'true' : 'false'),
										'type'	    => 'text')
										)
								);
		}else{
			$formFields[] = array('label'=>'<label for="filename">Email:</label>',
							   'field'=>form_input(array(
										'name'      => 'email',
										'id'        => 'email',
										'class'     => 'required',
										'value'		=> (isset($itemDetail['email']) ? $itemDetail['email'] : ''),
										'type'	    => 'text')
										)
								);
		}						
		$formFields[] = array('label'=>'<label for="filename">Password: </label>',
						   'field'=>form_input(array(
										'name'      => 'password',
										'id'        => 'password',
										'class'     => '',
										'type'	  => 'password')
										)
								);
		$formFields[] = array('label'=>'<label for="filename">Number: </label>',
						   'field'=>form_input(array(
										'name'      => 'number',
										'id'        => 'number',
										'class'     => '',
										'value'		=> (isset($itemDetail['number']) ? $itemDetail['number'] : ''),
										'type'	  => 'text')
										)
							);
							
		if(isset($itemDetail['loginaccess']) && $itemDetail['loginaccess'] == 1 ){
			$formFields[] = array('label'=>'<label for="filename">Login Access: </label>',
							   'field'=>form_input(array(
											'name'      => 'loginaccess',
											'id'        => 'loginaccess',
											'class'     => '',
											'value'		=> '1',
											'checked'   => 'checked',
											'type'	  => 'checkbox')
											)
								);
		}else{
			$formFields[] = array('label'=>'<label for="filename">Login Access: </label>',
							   'field'=>form_input(array(
											'name'      => 'loginaccess',
											'id'        => 'loginaccess',
											'class'     => '',
											'value'		=> '1',
											'type'	  => 'checkbox')
											)
								);
		}
		 if($this->session->userdata('isadmin') == 1){
			 $formFields[] = array('label'=>'<label for="filename">Is Admin: </label>',
							   'field'=>form_input(array(
											'name'      => 'isadmin',
											'id'        => 'isadmin',
											'class'     => '',
											'value'		=> '1',
											'type'	  => 'checkbox')
											)
								);
			 
			 
		 }
		$data['form'] = array(
		            'form_attr'=>array('action'=>'Executive/Addexecutive'.$id,'name'=>'addExec'),
					//~ 'open'=>form_open_multipart(site_url('Executive/Addexecutive'),array('name'=>'addExec','class'=>'form','id'=>'addExec','method'=>'post'),array("id"=>$id)),
					'fields'=>$formFields,
					'close'=>form_close(),
					'title'=>$this->lang->line('level_search')
					);

		$this->exmodel->viewLayout('form_view',$data);
	}
	function SendNumbers(){
		$type=array(""=>"Select","1"=>"Normal","2"=>"VIP","3"=>"Silver","4"=>"Gold");					
		$eid=$this->session->userdata('eid');
		$itemDetail=$this->exmodel->execDetails($eid);
		if($this->input->post('submit')){
			$content='Please find Below numbers <br/><br/>';
			$content.='<table border="1"  style="width:300px;font-size: 13px; line-height: 18px; color: #444444; margin-top: 0px; margin-bottom: 18px; font-family: Helvetica Neue, Arial, Helvetica, Geneva, sans-serif; text-indent:1cm;margin-left:40px;">
						<tr>
							<th>Number</th>
							<th>Type</th>
						</tr>
					  ';
			$nd=explode(",",$_POST['ids']);		  
			foreach($nd as $numbers){
				$nDetail=$this->exmodel->getNumberDetails($numbers);
				$content.='<tr>
							<td>'.$numbers.'</td>
							<td>'.$type[$nDetail->type].'</td>
						</tr>';
				
				
			}
			$content.='</table>';
			
			$body=$this->emailmodel->newEmailBody($content,' Sir,');
			$config['protocol']    = 'smtp';
			$config['smtp_host']    = 'smtpout.asia.secureserver.net';
			$config['smtp_port']    = '25';
			$config['smtp_user']    = $itemDetail['email'];
			$config['smtp_pass']    = base64_decode($itemDetail['emailpass']);
			$config['charset']    = 'utf-8';
			$config['newline']    = "\r\n";
			$config['mailtype'] = 'html'; // or html
			$config['validation'] = TRUE; // bool whether to validate email or not   
			$this->email->initialize($config);
			$this->email->from($itemDetail['email'],$itemDetail['email']);
			$this->email->bcc($this->input->post('cemail'));
			$this->email->subject($this->input->post('subject'));
			$this->email->message($body);  
			$this->email->send();
			$flashdata = array('msgt' => 'success', 'msg' => 'Mail Sent Successfully to '.$this->input->post('cemail'));
							$this->session->set_flashdata($flashdata);
							redirect('Executive/available');
			
		}
		
		
		
		if(isset($itemDetail['emailpass']) && $itemDetail['emailpass']!=""){
			$content='<TABLE>
							<tr>
								<th><label>Client Email :</label></th>
								<td>
									<input type="text" name="cemail" id="cemail" class="required email" />
								</td>
								<td></td>
							</tr>
							<tr>
								<th><label>Subject :</label></th>
								<td>
									<input type="hidden" name="ids" id="ids" />
									<input type="text" name="subject" id="subject" class="required" />
								</td>
								<td></td>
							</tr>
							</TABLE>
							<table><tr><td><center>
						<input id="button1" type="submit" name="submit" value='.$this->lang->line('submit').' /> 
						<input id="button2" type="reset" value='.$this->lang->line('reset').' />
						</center></td></tr></table>
							';
		}else{
			$content="Your E-mail is Not Configured,Please Configure Your E-mail in Email Configuration Tab";
		}
				$message= "<script type='text/javascript'>
					$(function() {
							
						$('#numbersend').validate({
								errorPlacement: function(error, element) {
									error.appendTo( element.parent().next() );
								}		
							});
				});
				</script>";
				$message.= '<div id="box">
				<h3>Send Numbers to Client</h3>
				<form action="Executive/SendNumbers/" class="form" id="numbersend" name="numbersend" method="POST">
				<fieldset id="priseries">
						<legend>Send Numbers to Client</legend>
						'.$content.'
						
						</form>
				</fieldset>

				</div>';
				echo $message;
		
		
	}
	function blknumber(){
		if($this->input->post('submit')){
			$res=$this->exmodel->blkNumber();
			if($res!=0){
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg', "Selected Numbers are Requested to Block ,Please wait for Approval");
			}else{
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', "Fail to Block the Numbers");		
			}
			redirect('Executive/available');
		}
		
		echo "<script type='text/javascript'>
			$(function() {
					
				$('#bnumber').validate({
						errorPlacement: function(error, element) {
							error.appendTo( element.parent().next() );
						}		
					});
		});
		</script>";
		echo '<div id="box">
		<h3>Block Number</h3>
		<form action="Executive/blknumber/" class="form" id="bnumber" name="bnumber" method="POST">
		<fieldset id="priseries">
				<legend>Block Number</legend>
				<TABLE>
					<tr>
						<th><label>Client Name :</label></th>
						<td>
							<input type="text" name="client" id="client" class="required" />
						</td>
						<td></td>
					</tr>
					<tr>
						<th><label>Blocking Request For :</label></th>
						<td>
							<input type="hidden" name="ids" id="ids" />
							'.form_dropdown('blkfor',$this->exmodel->getExecutives(),'','id="blkfor" class="required"').'
						</td>
						<td></td>
					</tr>
					<tr>
						<th colspan="2"><b>Note </b>: Requested Number(s) will be
						sent for blocking  if available with the operator.once number(s) blocked confirmation mail will sent.</th>
						<td></td>
					</tr>
					
				</TABLE>
				<table><tr><td><center>
				<input id="button1" type="submit" name="submit" value='.$this->lang->line('submit').' /> 
				<input id="button2" type="reset" value='.$this->lang->line('reset').' />
				</center></td></tr></table>
				</form>
		</fieldset>

</div>';
	}
	function unblknumber(){
		if($this->input->post('submit')){
			$res=$this->exmodel->unblkNumber();
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg', "Selected Numbers are UnBlocked");
			
			redirect('Executive/blockNumbers');
		}
		echo "<script type='text/javascript'>
			$(function() {
			
			$('#ubnumber').validate({
					errorPlacement: function(error, element) {
						error.appendTo( element.parent().next() );
					}		
				});
		});
		</script>";
		$status = array(""=>"Select","0"=>"Available","2"=>"Not Available");
		echo '<div id="box">
		<h3>Unblock Number</h3>
		<form action="Executive/unblknumber/" class="form" id="ubnumber" name="ubnumber" method="POST">
		<fieldset id="priseries">
				<legend>Unblock Number</legend>
				<TABLE>
					<tr>
						<th><label>Status :</label></th>
						<td><input type="hidden" name="ids" id="ids" />
							'.form_dropdown('status',$status,'','id="status" class="required" ').'
						</td>
						<td></td>
					</tr>
				</TABLE>
				<table><tr><td><center>
				<input id="button1" type="submit" name="submit" value='.$this->lang->line('submit').' /> 
				<input id="button2" type="reset" value='.$this->lang->line('reset').' />
				</center></td></tr></table>
				</form>
		</fieldset>

</div>';
	}
	function editnumber($number){
		if($this->input->post('submit')){
			$res=$this->exmodel->status_update($number);
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg', "Numbers Status updated");
			redirect('Executive/available');
		}
		$itemDetail=$this->exmodel->getNumberDetails($number);
		$number_type = array(""=>"Select","1"=>"Normal","2"=>"VIP","3"=>"Silver","4"=>"Gold");
		echo "<script type='text/javascript'>
			$(function() {
				$( '.datepicker' ).datepicker({
				dateFormat: 'yy-mm-dd',
				changeMonth: true,
				changeYear: true
			});
				$('#ednumber').validate({
					errorPlacement: function(error, element) {
						error.appendTo( element.parent().next() );
					}		
				});
		});
		</script>";
		$status = array(""=>"Select","0"=>"Available","2"=>"Not Available");
		
		echo '<div id="box">
		<h3>Edit Number</h3>
		<form action="Executive/editnumber/'.$number.'" class="form" id="ednumber" name="ednumber" method="POST">
		<fieldset id="priseries">
				<legend>Edit Number</legend>
				<TABLE>
					<tr>
						<th><label>Number :</label></th>
						<td>
							<input type="text" name="number" id="number" class="required" value="'.$itemDetail->number.'"/>
						</td>
						<td></td>
					</tr>
					<tr>
						<th><label>type :</label></th>
						<td>
							'.form_dropdown('type',$number_type,$itemDetail->type,'id="type" class="required" ').'
						</td>
						<td></td>
					</tr>
					<tr>
						<th><label>Region :</label></th>
						<td>
							<input type="text" name="region" id="region" class="required" value="'.$itemDetail->region.'"/>
						</td>
						<td></td>
					</tr>
					<tr>
						<th><label>Sim Taken From :</label></th>
						<td>
							<input type="text" name="simtaken" id="simtaken" class="required" value="'.$itemDetail->simtaken.'"/>
						</td>
						<td></td>
					</tr>
					<tr>
						<th><label>Operator :</label></th>
						<td>
							<input type="text" name="operator" id="operator" class="required" value="'.$itemDetail->operator.'"/>
						</td>
						<td></td>
					</tr>
					<tr>
						<th><label>Pool :</label></th>
						<td>
							<input type="text" name="pool" id="pool" class="required" value="'.$itemDetail->simtaken.'"/>
						</td>
						<td></td>
					</tr>
					<tr>
						<th><label>Availability Date :</label></th>
						<td>
							<input type="text" name="available_dt" id="available_dt" class="required datepicker" value="'.$itemDetail->available_dt.'"/>
						</td>
						<td></td>
					</tr>
					<tr>
						<th><label>Status :</label></th>
						<td>
							'.form_dropdown('status',$status,$itemDetail->status,'id="status" class="required" ').'
						</td>
						<td></td>
					</tr>
					
				</TABLE>
				<table><tr><td><center>
				<input id="button1" type="submit" name="submit" value='.$this->lang->line('submit').' /> 
				<input id="button2" type="reset" value='.$this->lang->line('reset').' />
				</center></td></tr></table>
				</form>
		</fieldset>

</div>';
		
		
		
		
	}
	function listExecutives($page=''){
		$formFields=array();
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '15';
		$header = array("#","Name","Email","Number","Actions");
		$data['itemlist']['header'] = $header;
		$exec_list=$this->exmodel->listexec($ofset,$limit);
		$rec = array();
		if(count($exec_list['data'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;
		foreach ($exec_list['data'] as $item){
			$act = '<a href="Executive/Addexecutive/'.$item['eid'].'"><span title="Edit" class="fa fa-edit"></span></a>';
			$img = ($item['status'] == 1) ? 'class="fa fa-unlock' : 'class="fa fa-lock' ;           
			$act .= '<a href="'.base_url().'Executive/statuschange/'.$item['eid'].'"> <span "'.$img.'"></span></a>';
			$rec[] = array(
				$i,
				$item['name']
				,$item['email']
				,$item['number']
				,$act
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $exec_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Executive/listExecutives/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$formFields[] = array('label'=>'<label for="filename">Name:</label>',
							   'field'=>form_input(array(
										'name'      => 'name',
										'id'        => 'name',
										'class'     => '',
										'type'	  => 'text')
										)
								);
		$formFields[] = array('label'=>'<label for="filename">Email: </label>',
						   'field'=>form_input(array(
										'name'      => 'email',
										'id'        => 'email',
										'class'     => '',
										'type'	  => 'text')
										)
							);
		$formFields[] = array('label'=>'<label for="filename">Number: </label>',
						   'field'=>form_input(array(
										'name'      => 'number',
										'id'        => 'number',
										'class'     => '',
										'type'	  => 'text')
										)
							);
		$data['module']['title'] = "Executives [".$data['itemlist']['count']."]";
		$data['links']='<a href="Executive/Addexecutive/"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>';
		$formFields1 = array();
		$data['form'] = array(
							'open'=>form_open_multipart('Executive/listExecutives/',array('name'=>'listExec','class'=>'form','id'=>'listExec','method'=>'post')),
							'form_field'=>$formFields,
							'adv_search'=>array(),
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->exmodel->viewLayout('list_view',$data);
	}
	function blockNumbers(){
		$formFields=array();
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$this->exmodel->data['file'] = "system/application/js/ex.js";
		$limit = '30';
		$header = array("<a href='javascript://'><span id='c_all' class='glyphicon glyphicon-gok'></span></a>","id","Number","Type","Operator","Region","Sim Taken From","Blocked By","Blocked For","Client","Availability Date","Blocked Date","Last Update");
		$number_type = array(""=>"Select","1"=>"Normal","2"=>"VIP","3"=>"Silver","4"=>"Gold");
		$status = array(""=>"Select","0"=>"Available","1"=>"Blocked","2"=>"Not Available");
		$data['itemlist']['header'] = $header;
		$emp_list=$this->exmodel->Numbersblk($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;
		foreach ($emp_list['data'] as $item){
			$rec[] = array(
				'<input type="checkbox" name="avnumbers[]" id="avnumbers" class="anumbers" value="'.$item['number'].'"/>' 
				,$i
				,$item['number']
				,$number_type[$item['type']]
				,$item['operator']
				,$item['region']
				,$item['simtaken']
				,$item['blockedby']
				,$item['blockedfor']
				,$item['clientname']
				,$item['available_dt']
				,$item['blocked_dt']
				,$item['lastupdate_dt']
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Executive/available/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$formFields[] = array('label'=>'<label for="filename">Number&nbsp;&nbsp;:</label>',
							   'field'=>form_input(array(
										'name'      => 'number',
										'id'        => 'number',
										'class'	  => 'form-control',
										'type'	  => 'text')
										)
								);
		$formFields[] = array('label'=>'<label for="filename">Type&nbsp;&nbsp;: </label>',
						   'field'=>form_dropdown('type',$number_type,'','id="type" class="auto"')
							);
		$formFields[] = array('label'=>'<label for="filename">Operator&nbsp;&nbsp;: </label>',
						   'field'=>form_input(array(
										'name'      => 'operator',
										'id'        => 'operator',
										'class'	  => 'form-control',
										'type'	  => 'text')
										)
							);
		$formFields[] = array('label'=>'<label for="filename">Region&nbsp;&nbsp;: </label>',
						   'field'=>form_input(array(
										'name'      => 'region',
										'id'        => 'region',
										'class'	  => 'form-control',
										'type'	  => 'text')
										)
							);
		$formFields[] = array('label'=>'<label for="filename">Sim Taken From&nbsp;&nbsp;: </label>',
						   'field'=>form_input(array(
										'name'      => 'simtaken',
										'id'        => 'simtaken',
									    'class'	  => 'form-control',
										'type'	  => 'text')
										)
							);						
		$formFields[] = array('label'=>'<label for="filename">Blocked For&nbsp;&nbsp;: </label>',
						   'field'=>form_dropdown('blkfor',$this->exmodel->getExecutives(),'','id="type", class= "form-control"')
							);
		$formFields[] = array('label'=>'<label for="filename">Blocked By&nbsp;&nbsp;: </label>',
						   'field'=>form_dropdown('blkby',$this->exmodel->getExecutives(),'','id="type" class= "form-control"')
							);
		$formFields[] = array('label'=>'<label for="filename">Blocked Date From&nbsp;&nbsp;: </label>',
						   'field'=>form_input(array(
										'name'      => 'bdatefrom',
										'id'        => 'bdatefrom',
										'class'     => 'datepicker form-control',
										'type'	  => 'text')
										)
							);
		$formFields[] = array('label'=>'<label for="filename">Blocked Date To&nbsp;&nbsp;: </label>',
						   'field'=>form_input(array(
										'name'      => 'bdateto',
										'id'        => 'bdateto',
										'class'     => 'datepicker form-control',
										'type'	  => 'text')
										)
							);
		$data['module']['title'] = "Blocked Numbers [".$data['itemlist']['count']."]";
		$data['links']='<a href="Executive/Addnumber/"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>&nbsp;';
		if($this->session->userdata('isadmin') == 1){
		$data['links'].='<a href="Executive/unblknumber/" class="unblock"><img src="system/application/img/icons/unblock.png" title="Unblock Number" width="16" height="16" /></a>&nbsp;';
		} 
		$formFields1 = array();
		$data['form'] = array(
							'open'=>form_open_multipart('Executive/blockNumbers/',array('name'=>'manageemp','class'=>'form-horizontal','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'close'=>form_close(),
							'adv_search'=>array(),
							'title'=>$this->lang->line('level_search')
							);
		$this->exmodel->viewLayout('list_view',$data);
		
		
	}
	function blkrequest(){
		$formFields=array();
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$this->exmodel->data['file'] = "system/application/js/ex.js";
		$limit = '30';
		$header = array("<a href='javascript://'><span id='c_all' class='glyphicon glyphicon-gok'></span></a>","id","Number","Type","Operator","Region","Sim Taken From","Blocked By","Blocked For","Client","Availability Date","Blocked Date","Last Update");
		$number_type = array(""=>"Select","1"=>"Normal","2"=>"VIP","3"=>"Silver","4"=>"Gold");
		$status = array(""=>"Select","0"=>"Available","1"=>"Blocked","2"=>"Not Available");
		$data['itemlist']['header'] = $header;
		$emp_list=$this->exmodel->reqblk($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;
		foreach ($emp_list['data'] as $item){
			$rec[] = array(
				'<input type="checkbox" name="avnumbers[]" id="avnumbers" class="anumbers" value="'.$item['number'].'"/>' 
				,$i
				,$item['number']
				,$number_type[$item['type']]
				,$item['operator']
				,$item['region']
				,$item['simtaken']
				,$item['blockedby']
				,$item['blockedfor']
				,$item['clientname']
				,$item['available_dt']
				,$item['blocked_dt']
				,$item['lastupdate_dt']
				
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Executive/available/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$formFields[] = array('label'=>'<label for="filename">Number&nbsp;&nbsp;:</label>',
							   'field'=>form_input(array(
										'name'      => 'number',
										'id'        => 'number',
										'class'     => '',
										'type'	  => 'text')
										)
								);
		$formFields[] = array('label'=>'<label for="filename">Type&nbsp;&nbsp;: </label>',
						   'field'=>form_dropdown('type',$number_type,'','id="type" class="auto"')
							);
		$formFields[] = array('label'=>'<label for="filename">Operator&nbsp;&nbsp;: </label>',
						   'field'=>form_input(array(
										'name'      => 'operator',
										'id'        => 'operator',
										'class'     => '',
										'type'	  => 'text')
										)
							);
		$formFields[] = array('label'=>'<label for="filename">Region&nbsp;&nbsp;: </label>',
						   'field'=>form_input(array(
										'name'      => 'region',
										'id'        => 'region',
										'class'     => '',
										'type'	  => 'text')
										)
							);
        $formFields[] = array('label'=>'<label for="filename">Sim Taken From&nbsp;&nbsp;: </label>',
						   'field'=>form_input(array(
										'name'      => 'simtaken',
										'id'        => 'simtaken',
										'class'     => '',
										'type'	  => 'text')
										)
							);							
		$formFields[] = array('label'=>'<label for="filename">Blocked For&nbsp;&nbsp;: </label>',
						   'field'=>form_dropdown('blkfor',$this->exmodel->getExecutives(),'','id="type" class="auto"')
							);
		$formFields[] = array('label'=>'<label for="filename">Blocked By&nbsp;&nbsp;: </label>',
						   'field'=>form_dropdown('blkby',$this->exmodel->getExecutives(),'','id="type" class="auto"')
							);
		$formFields[] = array('label'=>'<label for="filename">Blocked Date From&nbsp;&nbsp;: </label>',
						   'field'=>form_input(array(
										'name'      => 'bdatefrom',
										'id'        => 'bdatefrom',
										'class'     => 'datepicker',
										'type'	  => 'text')
										)
							);
		$formFields[] = array('label'=>'<label for="filename">Blocked Date To&nbsp;&nbsp;: </label>',
						   'field'=>form_input(array(
										'name'      => 'bdateto',
										'id'        => 'bdateto',
										'class'     => 'datepicker',
										'type'	  => 'text')
										)
							);
		$data['module']['title'] = "Requested Blocked Numbers [".$data['itemlist']['count']."]";
		$data['links']='<a href="Executive/Addnumber/"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>&nbsp;';
		$data['links'].='<a href="'.site_url().'Executive/cblknumber" class="confirmBlock"><img src="system/application/img/icons/block.png" title="Block Number" width="16" height="16" /></a>&nbsp;';
		$data['links'].='<a href="Executive/unblknumber/" class="unblock"><img src="system/application/img/icons/unblock.png" title="Unblock Number" width="16" height="16" /></a>&nbsp;';
		$formFields1 = array();
		$data['form'] = array(
							'open'=>form_open_multipart('Executive/blkrequest/',array('name'=>'manageemp','class'=>'form-horizontal','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'close'=>form_close(),
							'adv_search'=>array(),
							'title'=>$this->lang->line('level_search')
							);
		$this->exmodel->viewLayout('list_view',$data);
		
		
	}
	function cblknumber(){
		if($this->session->userdata('numbers')){
			$res=$this->exmodel->cBlk();
			 $this->session->set_userdata('numbers','');
			 $this->session->set_flashdata('msgt', 'success');
			 $this->session->set_flashdata('msg', "Numbers are blocked Successfully");
			 redirect('Executive/blkrequest');
		}
	}
	function NotAvailble(){
		$formFields=array();
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$this->exmodel->data['file'] = "system/application/js/ex.js";
		$limit = '30';
		$header = array("Number","Type","Operator","Region","Sim Taken From","Last Update");
		$number_type = array(""=>"Select","1"=>"Normal","2"=>"VIP","3"=>"Silver","4"=>"Gold");
		$status = array(""=>"Select","0"=>"Available","1"=>"Blocked","2"=>"Not Available");
		$data['itemlist']['header'] = $header;
		$emp_list=$this->exmodel->notAvailable($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;
		foreach ($emp_list['data'] as $item){
			$rec[] = array(
				$item['number']
				,$number_type[$item['type']]
				,$item['operator']
				,$item['region']
				,$item['simtaken']
				,$item['lastupdate_dt']
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Executive/available/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$formFields[] = array('label'=>'<label for="filename">Number&nbsp;&nbsp;:</label>',
							   'field'=>form_input(array(
										'name'      => 'number',
										'id'        => 'number',
										'class'     => '',
										'type'	  => 'text')
										)
								);
		$formFields[] = array('label'=>'<label for="filename">Type&nbsp;&nbsp;: </label>',
						   'field'=>form_dropdown('type',$number_type,'','id="type" class="auto"')
							);
		$formFields[] = array('label'=>'<label for="filename">Operator&nbsp;&nbsp;: </label>',
						   'field'=>form_input(array(
										'name'      => 'operator',
										'id'        => 'operator',
										'class'     => '',
										'type'	  => 'text')
										)
							);
		$formFields[] = array('label'=>'<label for="filename">Region&nbsp;&nbsp;: </label>',
						   'field'=>form_input(array(
										'name'      => 'region',
										'id'        => 'region',
										'class'     => '',
										'type'	  => 'text')
										)
							);	
       $formFields[] = array('label'=>'<label for="filename">Sim Taken From&nbsp;&nbsp;: </label>',
						   'field'=>form_input(array(
										'name'      => 'simtaken',
										'id'        => 'simtaken',
										'class'     => '',
										'type'	  => 'text')
										)
							);															
		
		$data['module']['title'] = "Notavailable Numbers [".$data['itemlist']['count']."]";
		$data['links']='';
		$data['form'] = array(
							'open'=>form_open_multipart('Executive/NotAvailble/',array('name'=>'manageemp','class'=>'form-horizontal','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'adv_search'=>array(),
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->exmodel->viewLayout('list_view',$data);
		
		
	}

	function statuschange($id){
		$exec_list=$this->exmodel->statusChange($id);
		redirect('Executive/listExecutives');
	}
	function sessSet(){
		
		 $this->session->set_userdata('numbers',$_POST['num']);
		 echo $this->session->userdata('numbers');
	}
	function emailsetting(){
		if($this->input->post('update_system')){
			$update=$this->exmodel->update_pass();
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg', "Configuration set Successfully");
			redirect('Executive/emailsetting');
		}
		
		
		$eid=$this->session->userdata('eid');
		$itemDetail=$this->exmodel->execDetails($eid);
		
		$this->exmodel->data['html']['title'] .= " | Email Configuration";
		$data['module']['title'] = "Email Configuration";
		$this->exmodel->data['file'] = "system/application/js/ex.js";
		$formFields = array();$formFields1 = array();
		$formFields[]=array('label'=>"<label>Password :</label>",
				  'field'=>form_input(array(
									  'name'        => 'password',
										'id'          => 'password',
										'value'       => (isset($itemDetail['emailpass']) ? base64_decode($itemDetail['emailpass']) : ''),
										'class'		=>'required',
										'type'=>'password'
										
				  				)
				  		)
							);
	
			$data['form'] = array(
			        'form_attr'=>array('action'=>'Executive/emailsetting/','name'=>'esetting'), 
					//~ 'open'=>form_open_multipart('Executive/emailsetting/',array('name'=>'esetting','class'=>'form','id'=>'esetting','method'=>'post')),
					'fields'=>$formFields,
					'adv_search'=>array(),
					'fields1'=>$formFields1,
					'close'=>form_close()
				);
		$this->exmodel->viewLayout('form_view',$data);
		
	}
}

/* end of master admin controller */
