<?php
class payment extends controller
{
	var $data;
	function payment(){
		parent::controller();
		if(!$this->session->userdata('partnerlogged_in') && $this->uri->segment('2')!="index")redirect('/partner/index');
		$this->load->model('partnermodel','pmodel');
		$this->load->model('paymentmodel','pay');
		$this->data = $this->pmodel->init();
		$this->load->model('commonmodel');
	}
	function index(){
	}
	
	function billconfig(){
	if(isset($_POST)){
			
			$this->form_validation->set_rules('businessuser', 'Business Name', 'required|is_natural|callback_businessbillconfig');
			$this->form_validation->set_rules('paycycle', 'PayCycle', 'required|is_natural');
			$this->form_validation->set_rules('billgenerate', 'Billgenerate Date', 'required');
			$this->form_validation->set_rules('duedate', 'due Date', 'required|is_natural');
			$this->form_validation->set_rules('disamount', 'Discount Amount', 'required|numeric');
			$this->form_validation->set_rules('rental', 'Rental', 'required|numeric');
			if(!$this->form_validation->run() == FALSE){	
					$res=$this->pay->addbill_config();
					$this->session->set_flashdata('msgt', 'success');
						$this->session->set_flashdata('msg', $this->lang->line('successmsg'));
						redirect('payment/billconfig');
			
			}
			
		}
		$formFields=array();
		$formFields1=array();
		$data['module']['title'] ="Bill Configuration";
		$res=$this->pmodel->partner_business();
		$tax=$this->pmodel->tax_list();
		$options=array();
		foreach($res as $r){
			$options[$r['bid']]=$r['businessname'];
		}
		$options1=array();
		foreach($tax as $t){
			$options1[$t['taxid']]=$t['percentage'];
		}
		$days=array();
		for($i=1;$i<=30;$i++){
			$days[$i]= $i .(($i>1)?' Days':' Day');
		}
		$paycycle=array("1"=>"1 Month","3"=>"Quaterly","6"=>"Half yearly","12"=>"Yearly");
		$js = 'id="businessuser" class="required"';
		$js1 = 'id="paycycle" class="required"';
		$js2 = 'id="duedate" class="required"';
		$js3 = 'id="tax" class="required"';
		$cf=array('label'=>'Business Name',
				  'field'=>form_dropdown("businessuser",$options,'',$js));
							array_push($formFields,$cf);
		$cf=array('label'=>'Paycycle',
				  'field'=>form_dropdown("paycycle",$paycycle,'',$js1));
							array_push($formFields,$cf);
		$cf=array('label'=>'Billgenerate Date',
				  'field'=>form_input(array(
							  'name'        => 'billgenerate',
								'id'          => 'billgenerate',
								'value'       => '',
								'readonly'=>'true',	
								'class'		=>'required datepicker')
									));
							array_push($formFields,$cf);								
		$cf=array('label'=>'Due Date',
				  'field'=>form_dropdown("duedate",$days,'',$js2));
							array_push($formFields,$cf);
		
		$cf=array('label'=>'Discount Type',
				  'field'=>'<input type="radio" name="distype" id="distype" value="2" checked/>Fixed
							<input type="radio" name="distype" id="distype" value="1"/>Percentage'
								);
				array_push($formFields,$cf);
		$cf=array('label'=>'Discount Amount',
				  'field'=>form_input(array(
							  'name'        => 'disamount',
								'id'          => 'disamount',
								'value'       => '',
								'class'		=>'required')
									));
							array_push($formFields,$cf);					
		$cf=array('label'=>'Rental',
				  'field'=>form_input(array(
							  'name'        => 'rental',
								'id'          => 'rental',
								'value'       => '',
								'class'		=>'required')
									));
							array_push($formFields,$cf);					
		$cf=array('label'=>'Tax',
				  'field'=>form_dropdown("tax",$options1,'',$js3));
							array_push($formFields,$cf);					
							
							
															
		$data['form'] = array(
		            'form_attr'=>array('action'=>'payment/billconfig/','name'=>'billconfig'), 
					//~ 'open'=>form_open_multipart('payment/billconfig/',array('name'=>'billconfig','class'=>'form','id'=>'billconfig','method'=>'post'),array('bid'=>'')),
					'fields'=>$formFields,
					'fields1'=>$formFields1,
					'close'=>form_close()
				);
		$this->pmodel->viewLayout('form_view',$data);
	}
	function businessbillconfig($bid){
		$res=$this->pmodel->check_business_user($bid);
		if($res!=1){
			$this->form_validation->set_message('businessbillconfig', 'Already Bill is Configured to this Business User');
			return FALSE;
		}else{
			return true;
			}
	}
	function bills(){
		
		$data['module']['title'] ="Generated Bills";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('Bill Id'
						,'Business Name'
						,'Amount'
						,'Generated Date'
						,'Due Date'
						,'Action'
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->pay->generated_bills($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;
		foreach ($emp_list['data'] as $item){
			$active=($item['latest']!=0)?'<a href="payment/billpayment/'.$item['bill_id'].'"><img src="'.site_url('system/application/img/icons/payment.png').'"  /></a>':'';
			
			$rec[] = array(
				$item['bill_id'],
				$item['businessname']
				,$item['netamount']
				,$item['bill_generate_date']
				,$item['due_date']
				,'<a href="payment/pdffile/'.$item['bill_id'].'"><img src="'.site_url('system/application/img/icons/pdf.png').'" title="pdf" width="16" height="16" /></a>'.'&nbsp;'.$active.'&nbsp;<a href="payment/listpayments/'.$item['bill_id'].'"><img src="'.site_url('system/application/img/icons/paid_Money.png').'"/></a>'
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('partner/managePriList/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		//$data['addlinks']="group/add_group";		
		$data['paging'] = $this->pagination->create_links();
		$this->pmodel->data['html']['title'] .= " | Manage PriNumbers";
		$data['links']='<a href="partner/addPrinumber/"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>';
		$formFields1 = array();
		$cf=array('label'=>'<label for="groupname">'.$this->lang->line('level_groupname').' : </label>',
				  'field'=>form_input(array(
									  'name'        => 'groupname',
										'id'          => 'groupname',
										'value'       => $this->session->userdata('groupname'))));
						array_push($formFields1,$cf);
						
		$this->mastermodel->data['links'] = '<a href="group/add_group"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>';
		//$fieldset = $this->configmodel->getFields('3');
		$formFields = array();
		$formFields[] = array(
				'label'=>'<label for="f">BusinessName : </label>',
				'field'=>form_input(array(
						'name'      => 'bname',
						'id'        => 'bname',
						'value'     => $this->session->userdata('bname')
						))
						);
		$formFields[] = array(
				'label'=>'<label for="f">Date From : </label>',
				'field'=>form_input(array(
						'name'      => 'datefrom',
						'id'        => 'datefrom',
						'value'     => $this->session->userdata('datefrom'),
						'class'		=>'datepicker'
						))
						);
		$formFields[] = array(
				'label'=>'<label for="f">Date To: </label>',
				'field'=>form_input(array(
						'name'      => 'dateto',
						'id'        => 'dateto',
						'value'     => $this->session->userdata('dateto'),
						'class'		=>'datepicker'
						))
						);
			
		$data['form'] = array(
							'open'=>form_open_multipart('partner/managePriList/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->pmodel->viewLayout('list_view',$data);
	}
	function pdffile($bill){
		$bill_report=$this->pay->bill_pdf($bill);
	}
	function billpayment($bill_id){
		if($this->input->post('update_system')){
			$res=$this->pay->billpayment($bill_id);
			redirect('payment/bills');
		}
		$formFields=array();
		$formFields1=array();
		$data['module']['title'] ="Bill Payment";
		$options=array("cheque"=>"cheque","cash"=>"cash");
		$options1=array("1"=>"cleared","2"=>"uncleared");
		$js = 'id="paymode" class="required"';
		$js1 = 'id="status" class="required"';
		
		$billdetails=$this->pay->getBilldetails($bill_id);
		$cf=array('label'=>'Business Name ',
				  'field'=>": ".$billdetails[0]['businessname']);
							array_push($formFields,$cf);
		$cf=array('label'=>'Bill Amount ',
				  'field'=>": ".$billdetails[0]['netamount']);
							array_push($formFields,$cf);
		$cf=array('label'=>'Bill Generated Date ',
				  'field'=>": ".$billdetails[0]['bill_generate_date']);
							array_push($formFields,$cf);
		$cf=array('label'=>'Billing Period  ',
				  'field'=>": ".$billdetails[0]['billing_form']."  To ".$billdetails[0]['billing_to']);
							array_push($formFields,$cf);
		$cf=array('label'=>'Bill Due Date ',
				  'field'=>": ".$billdetails[0]['due_date']);
							array_push($formFields,$cf);
		$cf=array('label'=>'Due Amount',
				  'field'=>": ".$billdetails[0]['due_amount']);
							array_push($formFields,$cf);
	   
		$cf=array('label'=>'Payment',
				  'field'=>": ".form_input(array(
						'name'      => 'payment',
						'id'        => 'payment',
						'value'     => '',
						'class'=>'required'
						))."  <input type='button' id='addp' name='addp' value='addpayment'/>"
						
						);
						array_push($formFields,$cf);
		$cf=array('label'=>'Mode',
				  'field'=>": ".form_dropdown("paymode",$options,'',$js)
						
						);
		array_push($formFields,$cf);
		$cf=array('label'=>'status',
				  'field'=>": ".form_dropdown("status",$options1,'',$js1)
						
						);
		array_push($formFields,$cf);
		$cf=array('label'=>'cheque No',
				  'field'=>": ".form_input(array(
						'name'      => 'cheno',
						'id'        => 'cheno',
						'value'     => ''
						))
						
						);
		array_push($formFields,$cf);
		$cf=array('label'=>'Bank Name',
				  'field'=>": ".form_input(array(
						'name'      => 'bname',
						'id'        => 'bname',
						'value'     => ''
						))
						
						);
		array_push($formFields,$cf);
		$cf=array('label'=>'Branch Name',
				  'field'=>": ".form_input(array(
						'name'      => 'brname',
						'id'        => 'brname',
						'value'     => ''
						))
						
						);
		array_push($formFields,$cf);
		
	   
		$data['form'] = array(
		            'form_attr'=>array('action'=>'payment/billpayment/'.$bill_id,'name'=>'billpayment'),
					//~ 'open'=>form_open_multipart('payment/billpayment/'.$bill_id,array('name'=>'billpayment','class'=>'form','id'=>'billpayment','method'=>'post'),array('bid'=>$billdetails[0]['bid'],'payid'=>0,'netamt'=>$billdetails[0]['netamount'],'billid'=>$bill_id)),
					'fields'=>$formFields,
					'fields1'=>$formFields1,
					'close'=>form_close()
				);
		$this->pmodel->viewLayout('form_view',$data);
	}
	function listpayments($bill_id){
		$data['module']['title'] ="Payment Details";
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '30';
		$header = array('Bill Id'
						,'Paid Amount'
						,'Payment Mode'
						,'Cheque No'
						,'Bankname'
						,'Action'
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->pay->paymentsList($bill_id,$ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;
		foreach ($emp_list['data'] as $item){
			$rec[] = array(
				$item['payment_id'],
				$item['payment_amount']
				,$item['payment_mode']
				,$item['chequeno_dd']
				,$item['bankname']
				,'<a href="payment/editpayment/'.$item['bill_id'].'/'.$item['payment_id'].'"><span title="Edit" class="fa fa-edit"></span></a>'.'<a href="payment/deletepayment/'.$item['bill_id'].'/'.$item['payment_id'].'"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>'
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('payment/listpayments/'.$bill_id)
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>4					
				));
		//$data['addlinks']="group/add_group";		
		$data['paging'] = $this->pagination->create_links();
		$this->pmodel->data['html']['title'] .= " | Manage PriNumbers";
		$data['links']='<a href="partner/addPrinumber/"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>';
		$formFields1 = array();
		$cf=array('label'=>'<label for="groupname">'.$this->lang->line('level_groupname').' : </label>',
				  'field'=>form_input(array(
									  'name'        => 'groupname',
										'id'          => 'groupname',
										'value'       => $this->session->userdata('groupname'))));
						array_push($formFields1,$cf);
						
		$this->mastermodel->data['links'] = '<a href="group/add_group"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>';
		//$fieldset = $this->configmodel->getFields('3');
		$formFields = array();
		$formFields[] = array(
				'label'=>'<label for="f">BusinessName : </label>',
				'field'=>form_input(array(
						'name'      => 'bname',
						'id'        => 'bname',
						'value'     => $this->session->userdata('bname')
						))
						);
		$formFields[] = array(
				'label'=>'<label for="f">Date From : </label>',
				'field'=>form_input(array(
						'name'      => 'datefrom',
						'id'        => 'datefrom',
						'value'     => $this->session->userdata('datefrom'),
						'class'		=>'datepicker'
						))
						);
		$formFields[] = array(
				'label'=>'<label for="f">Date To: </label>',
				'field'=>form_input(array(
						'name'      => 'dateto',
						'id'        => 'dateto',
						'value'     => $this->session->userdata('dateto'),
						'class'		=>'datepicker'
						))
						);
			
		$data['form'] = array(
							'open'=>form_open_multipart('partner/listpayments/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->pmodel->viewLayout('list_view',$data);
	}
	function editpayment($bill_id,$payment_id){
		if($this->input->post('update_system')){
			$res=$this->pay->updatepayment($payment_id);
			redirect("payment/listpayments/".$bill_id);
		}
		$formFields=array();
		$formFields1=array();
		$data['module']['title'] ="Edit Paid Bill";
		$options=array("cheque"=>"cheque","cash"=>"cash");
		$options1=array("cleared"=>"cleared","uncleared"=>"uncleared");
		$js = 'id="paymode" class="required"';
		$js1 = 'id="status" class="required"';
		$billdetails=$this->pay->editbill($payment_id,$bill_id);
		$cf=array('label'=>'Payment',
				  'field'=>": ".form_input(array(
						'name'      => 'payment',
						'id'        => 'payment',
						'value'     => $billdetails[0]['payment_amount'],
						'class'=>'required'
						)));
						array_push($formFields,$cf);
		$cf=array('label'=>'Mode',
				  'field'=>": ".form_dropdown("paymode",$options,$billdetails[0]['payment_mode'],$js)
						
						);
		array_push($formFields,$cf);
		$cf=array('label'=>'status',
				  'field'=>": ".form_dropdown("status",$options1,$billdetails[0]['status'],$js1)
						
						);
		array_push($formFields,$cf);
		$cf=array('label'=>'cheque No',
				  'field'=>": ".form_input(array(
						'name'      => 'cheno',
						'id'        => 'cheno',
						'value'     => $billdetails[0]['chequeno_dd']
						))
						
						);
		array_push($formFields,$cf);
		$cf=array('label'=>'Bank Name',
				  'field'=>": ".form_input(array(
						'name'      => 'bname',
						'id'        => 'bname',
						'value'     => $billdetails[0]['bankname']
						))
						
						);
		array_push($formFields,$cf);
		$cf=array('label'=>'Branch Name',
				  'field'=>": ".form_input(array(
						'name'      => 'brname',
						'id'        => 'brname',
						'value'     => $billdetails[0]['branchname']
						))
						
						);
		array_push($formFields,$cf);
		
	   
		$data['form'] = array(
		            'form_attr'=>array('action'=>'payment/editpayment/'.$bill_id.'/'.$payment_id,'name'=>'billpayment'),
					//~ 'open'=>form_open_multipart('payment/editpayment/'.$bill_id.'/'.$payment_id,array('name'=>'billpayment','class'=>'form','id'=>'billpayment','method'=>'post'),array('billid'=>$bill_id)),
					'fields'=>$formFields,
					'fields1'=>$formFields1,
					'close'=>form_close()
				);
		$this->pmodel->viewLayout('form_view',$data);
	}
	function deletepayment($bill_id,$pid){
		$res=$this->pay->deletepayament($bill_id,$pid);
		redirect("payment/listpayments/".$bill_id);
	}
	function generateBill_byuser($bid){
		$res=$this->pay->getuserbill($bid);
		if($res==""){
			$flashdata = array('msgt' => 'error', 'msg' => 'complete Bill configuration and generate');
					$this->session->set_flashdata($flashdata);
					redirect('partner/Userslist');
			
		}
		
	}
}
/* end of Payment controller */

