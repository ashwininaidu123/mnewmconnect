<?php
class Qrcode extends controller
{
	var $data,$roleDetail;
	function Qrcode()
	{
		parent::controller();
		//if(!$this->session->userdata('logged_in'))redirect('/user');
		if(!$this->session->userdata('logged_in'))redirect('/site/login');
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('systemmodel');
		$this->load->model('groupmodel');
		$this->load->model('qrmodel');
		$this->roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
	}
	function index(){
		redirect('qrcode/Qrlist');
	}
	function feature_access()
	{
		$show=0;
		$checklist=$this->systemmodel->checked_featuremanage();
		if(in_array(6,$checklist)){
			$show=1;
			}
		return $show;
	}
	function Qrlist(){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$data['module']['title'] =$this->lang->line('label_qrtracklist');
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('#'
						,$this->lang->line('lang_qrcodetitle')
						,$this->lang->line('lang_qrcodeuse')
						,$this->lang->line('lang_qrimage')
						,$this->lang->line('level_Action')
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->qrmodel->manageQRList($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;
		foreach ($emp_list['data'] as $item){
			$imgname1=$this->qrmodel->get_downloadimage($item['qrid']);
			$image_properties = array(
          'src' => Base_url().'qrcode/temp/'.$item['imagename'],
          'width' => '41',
          'height' => '41','title'=>'Click to enlarge');
			$rec[] = array(
					$i,
					$item['qrtitle']
					,$this->qrmodel->qruse_names($item['qruse']),
					'<a href="'.Base_url().'qrcode/Showimg/'.$item['imagename'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.img($image_properties).'</a>'
					,'<a href="'.site_url('qrcode/QrcodeConfig/'.$item['qrid']).'">
						<span title="Edit" class="fa fa-edit"></span>
					 </a>
					<a href="'.site_url('qrcode/Delete_Qrcode/'.$item['qrid']).'" class="delete">
						<span  title="Delete" class="glyphicon glyphicon-trash"></span>
					 </a>
					<a href="'.site_url('qrcode/download_image/'.$imgname1).'">
						<img src="'.site_url('system/application/img/icons/download.png').'" title="Download" width="16" height="16" />
					 </a>
					
				  '
				);
			
			
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('qrcode/manageQRList/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " |".$this->lang->line('label_qrtracklist');;
		$data['links']='';
		$formFields1 = array();
		$cf=array('label'=>$this->lang->line('lang_qrcodetitle'),
				  'field'=>form_input(array(
									  'name'        => 'title',
										'id'          => 'title',
										'value'       => '',
										
				  				)
				  		)
							);
		array_push($formFields1,$cf);
				
		$data['form'] = array(
							'open'=>form_open_multipart('qrcode/Qrlist/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields1,
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function Showimg($img){
		$data=array('img'=>$img);
		$this->load->view('showimg',$data);
		
		
	}
	function Deletedlist(){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$data['module']['title'] =$this->lang->line('label_qrtracklist');
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('#'
						,$this->lang->line('lang_qrcodetitle')
						,$this->lang->line('lang_qrcodeuse')
						,$this->lang->line('lang_qrimage')
						,$this->lang->line('level_Action')
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->qrmodel->manageDelQRList($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;
		foreach ($emp_list['data'] as $item){
			$image_properties = array(
          'src' => Base_url().'qrcode/temp/'.$item['imagename'],
          'width' => '41',
          'height' => '41',);
			$rec[] = array(
					$i,
					$item['qrtitle']
					,$this->qrmodel->qruse_names($item['qruse']),
					img($image_properties)
					,'<a href="'.site_url('qrcode/unDelete_Qrcode/'.$item['qrid']).'">
						<img src="'.site_url('system/application/img/icons/undelete.png').'" title="Delete" width="16" height="16" />
					 </a>
					
				  '
				);
			
			
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('qrcode/Deletedlist/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " |".$this->lang->line('label_qrtracklist');;
		$data['links']='';
		$formFields1 = array();
		$cf=array('label'=>$this->lang->line('lang_qrcodetitle'),
				  'field'=>form_input(array(
									  'name'        => 'title',
										'id'          => 'title',
										'value'       => '',
										
				  				)
				  		)
							);
		array_push($formFields1,$cf);
		$data['form'] = array(
							'open'=>form_open_multipart('qrcode/Deletedlist/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields1,
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function Delete_Qrcode($id){
		$update_Qr=$this->qrmodel->Delete_qrcode($id);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', $this->lang->line('Delete_status'));
		redirect('qrcode/Qrlist');
		
	}
	function unDelete_Qrcode($id){
		$update_Qr=$this->qrmodel->unDelete_qrcode($id);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', $this->lang->line('unDelete_status'));
		redirect('qrcode/Qrlist');
		
	}
	function Appenedefields($id,$qrid=''){
		$res=$this->qrmodel->getqrcodedetails($qrid);
		
		$str= ($id==1)
			?"<legend>Click to call</legend>
				<table>
					 <tr>
						<th><label>".$this->lang->line('mod_3')->groupname." : </label></th>
						<td>".form_dropdown('gid',$this->systemmodel->get_groups(),($qrid!="")?$res->gid:'','id="gid" class="required"')." <img src='system/application/img/icons/help.png' title='Select Group to forward the call to for tracking.'>"."</td>
						<td></td>
					 </tr>	
				</table>"
			:(
				($id==2)
				?"<legend>Website</legend>
					<table>
						 <tr>
							<th><label>".$this->lang->line('lang_website')." : </label></th>
							<td>".form_input(array(
													'name'      =>'website_name',
													'id'        =>'website_name',
													'class'		=>'required',
													'value'		=>($qrid!="")?$res->webaddress:''
													))." <img src='system/application/img/icons/help.png' title='URL where the user will be forwarded to.'>"."</td>
						 <td></td>
						 </tr>	
					</table>"
				:(
				($id==4)
					?"<legend>Deal of the Day</legend>
						<table>".
						$this->getfields($id,$qrid)
						."</table>"
					:(
					($id==5)
						?"<legend>Website</legend>
							<table>
								 <tr>
									<th><label>".$this->lang->line('lang_video')." : </label></th>
									<td>".form_input(array(
															'name'      =>'video',
															'id'        =>'video',
															'class'		=>'required',
															'value'		=>($qrid!="")?$res->video:''
															))." <img src='system/application/img/icons/help.png' title='You tube video showcasing your business. The QR Scan will show embedded youtube video.'>"."</td>
								 <td></td>
								 </tr>	
							</table>"
						:(
						($id==6)
							?"<legend>Map Address</legend>
								<table>
									 <tr>
										<th><label>".$this->lang->line('lang_mapaddress')." : </label></th>
										<td>".form_input(array(
																'name'      =>'qraddress',
																'id'        =>'qraddress',
																'class'		=>'required',
																'value'		=>($qrid!="")?$res->qraddress:''
																))." <img src='system/application/img/icons/help.png' title='Enter full address to show Google Map for the address of your business.'>"."</td>
									 <td></td>
									 </tr>	
								</table>"
							:''
						)
					)
				)
			);			
			echo $str;
		
		
	}
	function getfields($id,$qrid){
		$fieldset = $this->configmodel->getFields('19');
		$formFields = array();
		$roleDetail = $this->roleDetail;
		$qrdeals=$this->qrmodel->getqrdealinfo($qrid);
		//print_r($qrdeals);
		if(!$roleDetail['modules']['19']['opt_add']) redirect('Employee/access_denied');
		$str='';
		
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){
							$str.="<tr>
										<th><label for=".$field['fieldname'].">".(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										)." : </label></th>
										<td>".
												(($field['fieldname']=="description" || $field['fieldname']=="address"|| $field['fieldname']=="replymessage")?
												form_textarea(array(
														  'name'      => $field['fieldname'],
														  'id'        => $field['fieldname'],
														  'class'        =>'required',
														  'value'     => (!empty($qrdeals))?$qrdeals->$field['fieldname']:''
														))
												:form_input(array(
														  'name'      => $field['fieldname'],
														  'id'        => $field['fieldname'],
														  'class'     =>($field['fieldname']=='validupto')?'datepicker_leads':'required',
														  'value'     => (!empty($qrdeals))?$qrdeals->$field['fieldname']:''
														)))." <img src='system/application/img/icons/help.png' title='".$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname']."'>"."</td>
										<td></td>
										</tr>";
							
				}
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$str.="<tr>
										<th><label for=".$field['fieldname'].">".(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										)." : </label></th>
										<td>".$this->configmodel->createField($field,
											isset($itemDetail['custom['.$field['fieldid'].']'])?
											$itemDetail['custom['.$field['fieldid'].']']:'')."</td>
										<td></td>
										
									</tr>";
						}
			}
		}
		return $str;
	}


	function QrcodeConfig($id=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		if($this->input->post('update_system')){
			//print_r($this->rgb2hex2rgb($_POST['colorpickerField1']));
			//print_r($_POST);exit;
		  if($id==""){	
				$res=$this->qrmodel->qruseinto($id='');
				$ur=Base_url()."q/r/".base64_encode($res);
				$url=Base_url()."qrcode/qr.php?data=".$ur."&color=".$this->input->post('colorpickerField1')."&url=".Base_url()."qrcode/updatimage/".$res;
				redirect($url);
			}else{
				$res=$this->qrmodel->qruseinto($id);
				$ur=Base_url()."q/r/".base64_encode($id);
				//echo $ur;exit;
				$url=Base_url()."qrcode/qr.php?data=".$ur."&color=".$this->input->post('colorpickerField1')."&url=".Base_url()."qrcode/updatimage/".$res;
				redirect($url);
			}
		}
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$getd=$this->qrmodel->getqrcodedetails($id);
		$data['module']['title'] = $this->lang->line('label_qrtrack');
		$formFields = array();
		$cf=array('label'=>'<label>'.$this->lang->line('lang_qrcodetitle').'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="Name identifying the the QR for tracking.">&nbsp;&nbsp;: </label>',
				  'field'=>form_input(array(
													'name'      => 'qrtitle',
													'id'        =>'qrtitle',
													'class'		=>'required',
													'value'     =>($id!="")?$getd->qrtitle:''))
													
													);					
							
		array_push($formFields,$cf);
		$cf=array('label'=>'<label>'.$this->lang->line('lang_qrcodesource').'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="Identifier of the QR Placement. This will help identify the source of QR Code Scan.">&nbsp;&nbsp;:</label>',
				  'field'=>form_input(array(
													'name'      => 'source',
													'id'        =>'source',
													'value'     =>($id!="")?$getd->source:''))
													
													);					
							
		array_push($formFields,$cf);
		$cf=array('label'=>'<label>'.$this->lang->line('lang_qrdescription').'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="Short description, to be displayed on the landing page after the QR Scan.">&nbsp;&nbsp;: </label>',
				  'field'=>form_textarea(array(
													'name'      =>'description',
													'id'        =>'description',
													'value'     =>($id!="")?$getd->description:''))
													
													);					
							
		array_push($formFields,$cf);
		$cf=array('label'=>'<label>'.$this->lang->line('lang_qrcompanylogo').'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="Jpeg image of the logo to be placed on the landing page after the QR Scan.">&nbsp;&nbsp;: </label>',
				  'field'=>
								((isset($getd->company_logo))?
									(($getd->company_logo!="")?
								img(Base_url()."qrcode/company_logos/".$getd->company_logo ,true)."<br/><a href='qrcode/removeclogo/".$id."'>Remove</a>"
								:form_input(array(
											'name'      => 'clogo',
											'id'        =>'clogo',
											'type'     =>'file'))
											)					
								:form_input(array(
											'name'      => 'clogo',
											'id'        =>'clogo',
											'type'     =>'file'))
											)
											
											);					
							
		array_push($formFields,$cf);

		$str='';
		foreach($this->qrmodel->qr_use() as $rows)
		{
			$datas = array(
			'name'        => 'qruse[]',
			'id'          => $rows['qruseid'],
			'value'          => $rows['qruseid'],
			'class'		  =>'qrused',
			'checked'	  =>(($id!="")?(in_array($rows['qruseid'],explode(",",$getd->qruse))?true:''):'')			
			);
			$str.=form_checkbox($datas)." $rows[name]"."<br/>";
		}

		$cf=array('label'=>'<label>'.$this->lang->line('lang_qrcodeuse').'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="Actions to be taken on QR Scan.">&nbsp;&nbsp;: </label>',
				  'field'=>$str
							);
						array_push($formFields,$cf);
						
		$cf=array('label'=>'<label>QR Color&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="QR Color.">&nbsp;&nbsp; : </label>',
				  'field'=>'<input type="text" maxlength="6" size="6" id="colorpickerField1" name="colorpickerField1" value="00ff00" />'
							);
						array_push($formFields,$cf);

		
		if($id!=""){
		$cf=array('label'=>"<label>".$this->lang->line('lang_qrimage')." : </label>",
				  'field'=>img(Base_url()."qrcode/temp/".$getd->imagename,true)."<br/><a href='qrcode/download_image/".$getd->imagename."'>Download</a>"
				  );
				array_push($formFields,$cf);
		}			
	
		$data['form'] = array(
		            'form_attr'=>array('action'=>'qrcode/QrcodeConfig/'.$id,'name'=>'qrconfig'),
					//~ 'open'=>form_open_multipart('qrcode/QrcodeConfig/'.$id,
							//~ array('name'=>'qrconfig','id'=>'qrconfig','class'=>'form','method'=>'post')
							//~ ,array('qrid'=>$id)
							//~ ),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function updatimage($id,$filename){
		$res=$this->qrmodel->updateimg($id,$filename);
		redirect("qrcode/QrcodeConfig/".$id);
	}
	function download_image($imgname){
		$path1=$_SERVER['DOCUMENT_ROOT'];
			$fullfile1 =chmod($path1."/qrcode/temp/". $imgname,0777);
			$file =$path1."/qrcode/temp/". $imgname;
			if (file_exists($file)) {
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename='.basename($file));
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($file));
				ob_clean();
				flush();
				readfile($file);
				exit;
			}
	}
	function removeclogo($qrid){
		$res=$this->qrmodel->removecompany_logo($qrid);
		redirect("qrcode/QrcodeConfig/".$qrid);
		}
	function Qrleadgenerate($id=''){
		if($this->input->post('update_system')){
			$res=$this->qrmodel->update_leadgenerate($id);
			redirect('qrcode/qreport');
		}
		
		
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$roleDetail = $this->roleDetail;
		//print_r($roleDetail['modules']['2']);exit;
		if(!$roleDetail['modules']['18']['opt_add']) redirect('Employee/access_denied');
		//$roless=$this->empmodel->get_roles();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_Leadgeneration');
		$data['module']['title'] = $this->lang->line('label_Leadgeneration');
		$fieldset = $this->configmodel->getFields('18');
		$formFields = array();$formFields1 = array();
		$itemDetail = $this->configmodel->getDetail('18',$id);
		//echo "";print_r($itemDetail);
		
		
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='datetime'){
				$cf = array('label'=>$field['fieldname'],
							'field'=>(($field['fieldname']=="query" )?
										form_textarea(array(
												  'name'      => $field['fieldname'],
												  'id'        => $field['fieldname'],
												  'class'        =>'required',
												  'value'     =>  isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:''
												))
										:form_input(array(
												  'name'      => $field['fieldname'],
												  'id'        => $field['fieldname'],
												  'class'     =>($field['fieldname']=='validupto')?'datepicker_leads':'required',
												  'value'     =>  isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:''
												))));
				array_push($formFields,$cf);							
			}elseif($field['type']=='c' && $field['show']){
				$cf = array('label'=>$field['customlabel'],
							'field'=>$this->configmodel->createField($field,
										isset($itemDetail['custom['.$field['fieldid'].']'])?
										$itemDetail['custom['.$field['fieldid'].']']:'')
					);
				array_push($formFields,$cf);
			}
			
		}
		$data['form'] = array(
		            'form_attr'=>array('action'=>'qrcode/Qrleadgenerate/'.$id,'name'=>'leadgenrate'),
					//~ 'open'=>form_open_multipart('qrcode/Qrleadgenerate/'.$id,array('name'=>'leadgenrate','class'=>'form','id'=>'leadgenrate','method'=>'post')),
					'fields'=>$formFields,
					'fields1'=>$formFields1,
					'close'=>form_close()
				);
			$this->sysconfmodel->viewLayout('form_view',$data);
		
		
		
	}	
	function deleteQrlead($id){
		$res=$this->qrmodel->deleteleadgenrate($id);
		redirect('qrcode/qreport');
	}
	function qreport(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['18']['opt_view']) redirect('Employee/access_denied');
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$bid = $this->session->userdata('bid');
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_qrreport');
		$data['module']['title'] = $this->lang->line('label_qrreport');
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '20';
		$data['itemlist'] = $this->qrmodel->qrreport($ofset,$limit);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('qrcode/qreport')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['links'] = '';
		$fieldset = $this->configmodel->getFields('18');
		//print_r($fieldset);exit;
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] ){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>form_input(array(
											'name'      => $field['fieldname'],
											'id'        => $field['fieldname'],
											'value'     => $this->session->userdata($field['fieldname'])))
											);
			}
		}
		$data['form'] = array(
							'open'=>form_open_multipart('qrcode/qreport',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		
		$this->sysconfmodel->viewLayout('list_view',$data);
		
	}
	function scanreport(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['18']['opt_view']) redirect('Employee/access_denied');
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$bid = $this->session->userdata('bid');
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_qrscaneport');
		$data['module']['title'] = $this->lang->line('label_qrscaneport');
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '20';
		$data['itemlist'] = $this->qrmodel->qrscanreport($ofset,$limit);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('qrcode/scanreport')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['links'] = '';
		$data['nosearch']=true;
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function scanreport2(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['18']['opt_view']) redirect('Employee/access_denied');
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$bid = $this->session->userdata('bid');
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_qrscaneport');
		$data['module']['title'] = $this->lang->line('label_qrscaneport');
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '20';
		$data['itemlist'] = $this->qrmodel->qrscanreport2($ofset,$limit);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('qrcode/scanreport2/'.$this->uri->segment(3))
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['links'] = '';
		$data['nosearch']=true;
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function scanreport3(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['18']['opt_view']) redirect('Employee/access_denied');
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$bid = $this->session->userdata('bid');
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_qrscaneport');
		$data['module']['title'] = $this->lang->line('label_qrscaneport');
		$ofset = ($this->uri->segment(5)!=null)?$this->uri->segment(5):0;
		$limit = '20';
		$data['itemlist'] = $this->qrmodel->qrscanreport3($ofset,$limit);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('qrcode/scanreport3/'.$this->uri->segment(3).'/'.$this->uri->segment(4))
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['links'] = '';
		$data['nosearch']=true;
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
}
