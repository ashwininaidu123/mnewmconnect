<script>
$(function() {

	jQuery.validator.addMethod("alpha", function(value, element) {
		return this.optional(element) || value == value.match(/^[a-z A-Z]+$/);
		},"Numeric is not allowed.");
	jQuery.validator.addMethod("alphanumeric", function(value, element) {
		return this.optional(element) || value == value.match(/^[a-z0-9A-Z]+$/);
		},"Only Characters, Numbers Allowed.");
	jQuery.validator.addMethod("mobile", function(value, element) {
		return this.optional(element) || /^[7-9][0-9]{9}$/.test(value);
		}, "Should start with 7,8,9 and should have 10 digits");  
	jQuery.validator.addMethod("numeric", function(value, element) {
		return this.optional(element) ||value == value.match(/^[0-9]+$/);
		}, "Should be digits"); 
		
	jQuery.validator.addMethod("uniqueUserName", function(value, element) {
		 $.ajax({
			  type: "POST",
			   url: "user/check_username/",
			 data: "username="+$('#empemail').val(), 
			  dataType:"text",
		   success: function(msg){
					return x = (msg=='1')?false:true;
		   }
		 })
	}, "");
		 $('#to').autocomplete("Email/autoComplete/", {
	        width: 200,
	        max: 10
	    });
		$('.sentDelete').live('click',function(event){
			var nurl=$(this).attr('href');
			$.alerts.okButton = 'Yes';
			$.alerts.cancelButton = 'No';
			jConfirm('<b>Are you sure to Delete </b>','Alert', function(r) {
							if(r){
								
								$.ajax({ 
								type: "POST",  
								url: nurl,  
								success: function(msg){ 
										 $.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
										 $.unblockUI();
										 window.location.href=window.location.href
									}
								});
							}
							else{
								alert("dssaDADSD");
							}
						});
						
				return false;		
			
		});	
		
			
			
		$('.EmailTemp').live('click',function(event){
			var nurl=$(this).attr('href');
			$.alerts.okButton = 'Yes';
			$.alerts.cancelButton = 'No';
			jConfirm('<b>Are you sure to Delete </b>','Alert', function(r) {
							if(r){
								
								$.ajax({ 
								type: "POST",  
								url: nurl,  
								success: function(msg){ 
										 $.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
										 $.unblockUI();
										 window.location.href=window.location.href
									}
								});
							}
						});
						
				return false;		
			
		});	
		$('.unconfirmEmP').live('click',function(event){
			var nurl=$(this).attr('href');
			$.alerts.okButton = 'Yes';
			$.alerts.cancelButton = 'No';
			jConfirm('<b>Are you sure to Delete Unconfirm Employee</b>','Alert', function(r) {
							if(r){
								
								$.ajax({ 
								type: "POST",  
								url: nurl,  
								success: function(msg){ 
										 $.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
										 $.unblockUI();
										 window.location.href=window.location.href
									}
								});
							}
						});
						
				return false;		
		
		});
		$('#eprovider').live('change',function(event){
			var val=$('#eprovider').val();
			if(val!=''){
				if(val=='gmail'){
					$('#smtp').val('ssl://smtp.gmail.com');
					$('#port').val('465');
					$('#smtp').attr('readonly', true);
					$('#port').attr('readonly', true);
					
				}else if(val=='yahoo'){
					$('#smtp').val('ssl://smtp.mail.yahoo.com');
					$('#port').val('465');
					$('#smtp').attr('readonly', true);
					$('#port').attr('readonly', true);
				}else{
					$('#smtp').val('');
					$('#port').val('');
					$('#smtp').attr('readonly', false);
					$('#port').attr('readonly', false);
					
				}
			}else{
					$('#smtp').val('');
					$('#port').val('');
					$('#smtp').attr('readonly', false);
					$('#port').attr('readonly', false);
			}
		});
		$('#eConfig').validate({
			errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
			}
			
		});
	  $('#grempId').live('change',function(event){
	  var r=$('#grempId').val();
	  var im = $('#autoAssign').val();
	  if(im =='auto')
		  var suburl = "/"+$('#autoAssign').val();
	  else if(im=='singleLead')
		 var suburl = '';
	  else
		var suburl = '';
	  $.ajax({  
			type: "POST",  
			url: "<?=base_url()?>leads/get_grEmployees/"+r+suburl,  
			success: function(msg){
				$('#assignemp option').each(function(i, option){ $(option).remove(); });
				$('#assignemp').append(msg);
				}
			});
		});
	  $('#supgrId').live('change',function(event){
	  var r=$('#supgrId').val();
	  var im = $('#autoAssign').val();
	  if(im =='auto')
		  var suburl = "/"+$('#autoAssign').val();
	  else if(im=='single')
		 var suburl = '';
	  else
		var suburl = '';
	  $.ajax({  
			type: "POST",  
			url: "<?=base_url()?>support/getSupGrpEmp/"+r+suburl,  
			success: function(msg){
				$('#supEmpid option').each(function(i, option){ $(option).remove(); });
				$('#supEmpid').append(msg);
				}
			});
	});
	$('#tkt_level').live('change',function(event){
	  var r=$('#tkt_level').val();
	  $.ajax({  
			type: "POST",  
			url: "<?=base_url()?>support/getSupLevelTime/"+r,  
			success: function(msg){
				$('#tkt_esc_time').val(msg);
				}
			});
	});
		
	$('#login').live('click',function(event){
		var checkeds = $(this).attr("checked");
		if(checkeds){
			$('#empemail').addClass('required');
		}else{
			$('#empemail').removeClass('required');
		}
	});
	$("#button3").live('click',function(event) {
		$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
		var modid=parseInt($('#moduleid').val());
		var nxt=parseInt($('#nexT').val());
		
	$.ajax({  
			type: "POST",  
			url: "customfield/custommange/"+modid,  
			data:$("#customform").serialize()+'&update_system=update_system', 
			success: function(msg){ 
			$("#modulePage").load('<?=base_url()."customfield/empmod/";?>'+nxt);
			$('#modulename').val(nxt);
			$.unblockUI();	
			}
		});
	});
	 $('#prinumber').live('change',function(event){
    	 var mod="3";
    	 var seg="<?php echo $this->uri->segment('2');?>";
    	$.ajax({  
			type: "POST",  
			url: "group/Moduleaddons_number/"+$('#prinumber').val()+"/"+mod+"/"+seg,  
			data:'update_system=update_system', 
			success: function(msg){ 
				var str=$.trim(msg);
				if(str!=''){
						var strs=str.split(',');
						for(var i=0;i<strs.length;i++){
							$('#'+$.trim(strs[i])).attr("disabled", "disabled");
						}
						
				}else{
						var form = $("#addgroup");
						form.find(':disabled').each(function() {
							//alert("dd");
							$(this).removeAttr('disabled');
						});
				}
				//alert(msg);return false;
				//$('.appended_rows').remove();
				//$('#FromTab').append(msg);
			}
		});
	});
	
	$("#button4").live('click',function(event) {
		$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
		var modid=parseInt($('#moduleid').val());
		var pre=parseInt($('#preV').val());
		
	$.ajax({  
			type: "POST",  
			url: "customfield/custommange/"+modid,  
			data:$("#customform").serialize()+'&update_system=update_system', 
			success: function(msg){ 
			$("#modulePage").load('<?=base_url()."customfield/empmod/";?>'+pre);
			$('#modulename').val(pre);
			$.unblockUI();	
			}
		});
	});
	
	$("#button5").live('click',function(event) {
		$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
		var modid=parseInt($('#moduleid').val());
		
	$.ajax({  
			type: "POST",  
			url: "customfield/custommange/"+modid,  
			data:$("#customform").serialize()+'&update_system=update_system', 
			success: function(msg){ 
			$("#modulePage").load('<?=base_url()."customfield/empmod/";?>'+modid);
			$('#modulename').val(modid);
			$.unblockUI();	
			}
		});
	});
/*
	modid=modid-1;
	$("#modulePage").load('<?=base_url()."customfield/empmod/";?>'+modid);
	$('#modulename').val(modid);
*/

		
		$('#edit_actreport').validate({
			errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
			}
		});	
		$('#addcompany').validate({
			rules:{
				companyname:{
					required:true
				},
				owner:{
					required:true
				}
			},messages:{
				companyname:{
					required:"Company Name is required"
					},
				owner:{
					required:"Owner is required"
					},
			},
			errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
			}
			
		});
		$('#editreport').validate({
			rules:{
				callername:{
					alpha:true
				},
				caller_email:{
					email:true
				},
				callerbusiness:{
						alpha:true
				}
				
				
			},messages:{
				
			},errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
			}
			
			
		});
		$('#addussd').validate({
			rules:{
				optioncode:{
					required:true
				},
				optiontext:{
					required:true
				}		
				
			},messages:{
				optioncode:{
					required:"Please enter optioncode"
				},
				optiontext:{
					required:"Please enter optiontes"
				}	
			},errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
			}
			,submitHandler: function(form){
				$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
				form.submit();
			}
		});
		
		/*end*/
		
		
		
		
		/* keyword validation */
		
		$('#keyword').validate({
			rules:{
				code_id:"required",
				keyword:{
					required:true,
					alpha:true
				},
				default_msg:{
					required:true
				},
				keyword_use:{
					required:true
				}
				
			},
			messages:{
				keyword:{
					required:"<?php echo $this->lang->line('err_keyword');?>"
				},
				default_msg:{
					required:"<?php echo $this->lang->line('err_defaultmes');?>"
				},
				keyword_use:{
					required:"<?php echo $this->lang->line('err_keyword_use');?>"
				}
			},
			errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
			}
			
			
			});
		
		
		
		/* subkeyword validation */
		$('#subkeyword').validate({
			rules:{
				subkeyword:{
					required:true,
					alpha:true
				},
				customvalue:{
					required:true
				},
				replymsg:{
					required:true
				}
			},
			messages:{
				subkeyword:{
					required:"<?php echo $this->lang->line('err_subkeyword');?>"
				},
				customvalue:{
					required:"<?php echo $this->lang->line('err_custom');?>"
				},
				replymsg:{
					required:"<?php echo $this->lang->line('err_reply');?>"
				}
			},
			errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
			}
		});
		
		
		
		
		/* end */
		
		
		/* custom fields */
		$('#customform').validate({
			 errorPlacement: function(error, element) {
				error.appendTo( element.parent().parent().next() );
			}
		});
			
		
		/*end*/
		/*  BUSINESS Profile------------*/
			$('#businessprofile').validate({
				rules:{
					businessname:{
						required:true
					},
					businessaddress:{
						required:true
					},
					businessphone:{
						required:true,
						number:true	
					},
					businessemail:{
						required:true,
						email:true
					},
					language:"required",
					contactemail:{
						required:true,
						email:true
					},
					contactphone:{
						required:true,
						mobile:true
					},
					locality:{
						required:true
					},
					country:{
						required:true
					},
					state:{
						required:true
					},city:{
						required:true
					},zipcode:{
						required:true,
						numeric:true
					},
					webaddress:{
						url:true
					}
						
					
					
				},messages:{
					businessname:{
						required:"<?php echo $this->lang->line('err_bname');?>"
					},businessaddress:{
						required:"<?php echo $this->lang->line('err_badd');?>"
						},businessphone:{
						required:"<?php echo $this->lang->line('err_bphone');?>",
						mobile:"<?php echo $this->lang->line('err_mobile');?>"
					},businessemail:{
						required:"<?php echo $this->lang->line('err_bemail');?>"
					},
					contactemail:{
						required:"<?php echo $this->lang->line('err_cemail');?>"
					},
					contactphone:{
						required:"<?php echo $this->lang->line('err_cphone');?>",
						mobile:"<?php echo $this->lang->line('err_mobile');?>"
					}
					,locality:{
						required:"<?php echo $this->lang->line('err_locality');?>"
					},
					country:{
						required:"<?php echo $this->lang->line('err_country');?>"
					},state:{
						required:"<?php echo $this->lang->line('err_state');?>"
					},city:{
						required:"<?php echo $this->lang->line('err_city');?>"
					},
					zipcode:{
						required:"<?php echo $this->lang->line('err_Zipcode');?>",
						numeric:"<?php echo $this->lang->line('err_Ziperr');?>"
					},
					webaddress:{
						url:"<?php echo $this->lang->line('err_url');?>"		
					}

					
						

				},
				 errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
				}
				
				});
		
		
		
		
		/* ------end---------------------*/
		
		 $("#addemp").validate({
			 rules:{
				 empname:{
					 required:true
				 },
				 empnumber:{
					 required:true,
					 //mobile:true
					 number:true
				 },
				 empemail:{
					  email:true
					},
				extension:{
					minlength:3,
					maxlength: 4,
					number:true
				},

				 roleid:{
					 required:true,
					  number:true
					 }			 
			 },
			 messages:{
				 empname:{
						required:"<?php echo $this->lang->line('err_empneed');?>"
				 },
				 empnumber:{
						required:"<?php echo $this->lang->line('err_empeid');?>"
				},
				 empemail:{
					 required:"<?php echo $this->lang->line('err_empemail');?>",
					 },
				 roleid:"<?php echo $this->lang->line('err_role');?>"
				

			 },
			 errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
			}
			
			 
		 });
		 $("#popaddemp").validate({
			 rules:{
				 empname:{
					 required:true
				 },
				 empnumber:{
					 required:true,
					 //mobile:true
					 number:true
				 },
				 roleid:"required"	 
			 },
			 messages:{
				 empname:{
						required:"<?php echo $this->lang->line('err_empneed');?>"
				 },
				 empnumber:{
						required:"<?php echo $this->lang->line('err_empeid');?>"
				},
				 roleid:"<?php echo $this->lang->line('err_role');?>"
			 },
			 errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
			},submitHandler: function(){
				
					$.ajax({  
							type: "POST",  
							url: "Employee/addemp_popup/",  
							data:$("#popaddemp").serialize()+'&update_system=update_system', 
							success: function(msg){ 
								$('#empemail').parent().next().html("");
								if($.trim(msg)!="fail"){
									$('#eid').children().remove().end().append(msg) ;
									$('#popupDiv').html("");
									$.unblockUI();
								}else{
									$('#empemail').parent().next().html("<label class='error'>Email Already in use</label>");
									return false;
								}
							}
						});
			}
		 });	 
		
		$("#addActivityG").validate({
			rules:{
			groupname:{
					required:true
					},
			number:{
				required:true	
			},
		},messages:{
				groupname:{
					required:"<?=$this->lang->line('err_groupneed')?>"
				},	
			}	,errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
			}	
			
		});
		$("#addgroup").validate({
			rules:{
				eid:"required",
				groupname:{
					required:true
					},
				rules:{
					required:true
				},	
				addnumber:{
					required:true,
					mobile:true,
					numeric:true
				},
				timeout:{
					required:true,
					min:15
				},
				hdayaudio: {
					accept:'wav|mp3'
				},
				greetings: {
					accept:'wav|mp3'
				},
				cid:{
					required:true
				},
				oncallaction:{
					url:true	
				},
				oneditaction:{
					url:true
				},
				onhangup:{
					url:true
				},
			}
			,messages:{
				groupname:{
					required:"<?=$this->lang->line('err_groupneed')?>"
				},
				rules:{
					required:"<?php echo $this->lang->line('err_grouprules'); ?>"

				},
				addnumber:{
					required:"<?php echo $this->lang->line('err_groupaddnumber');?>",
					numeric:"<?php echo $this->lang->line('err_mobile');?>"
				},
				timeout:{
					required:"Time Out is required"
				},
				cid:{
						required:"Company Name is required"
				}
			}
			,errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
			},submitHandler: function(form){
				if($('#clone').val()==''){
					if($('#atoall').is(":checked") && $('#gids').val()==''){
						$.get('group/glist', function(data){
								$("#groupsDiv").html('<b class="gListSubmit">&nbsp;&nbsp;&nbsp;&nbsp;</b>' + data);
								$("#groupsDiv").css( {
									backgroundColor: '#FFF', 
									borderColor: '#ccc',
									'border-radius': '10px', 
									opacity: .9,
									color: '#000',
									overflow:'auto',
									cursor:'default'
								});
								//$('#POST').val($("#addgroup").serialize());
						});
						$.blockUI({ message: $('#groupsDiv') });
						return false;
						
						
						
					}
					
					
						$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
						form.submit();
						return true;
					
				}else if($('#oldprinumber').val()==$('#prinumber').val()){
					$.alerts.okButton = 'Yes';
					$.alerts.cancelButton = 'No';
					jConfirm('You have selected landing number from the group being cloned. This will result in deleting the group being cloned. Do you want to delete the group?','Delete confirmation', function(r) {
						if(r){
							$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
							form.submit();
						}
					});
				}else{
					$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
					form.submit();
				}
			}
		});	

	$('.gListSubmit').live('click',function(event){
		$('embed').show();
		$.unblockUI();
		var gids = new Array();
		$('#glistFrm :checkbox:checked').each(function(index, elm){gids.push($(elm).val());});
		$('#gids').val(gids);
		$("#groupsDiv").html('');
		$('#addgroup').submit();
	});
	
	$('.ChangeStatus').click(function(){

			var url="Employee/change_status/"+this.id
			$.get(url, function(data){
				window.parent.location.href = window.parent.location.href;
			});

	});
	
	$('.changeStatus').click(function(){

		var url="Masteradmin/change_status/"+this.id
			$.get(url, function(data){
				window.parent.location.href = window.parent.location.href;
			});

	});
	function my_implode_js(separator,array){
       var temp = '';
       for(var i=0;i<array.length;i++){
           temp +=  array[i] 
           if(i!=array.length-1){
                temp += separator  ; 
           }
       }//end of the for loop

       return temp;
	}
	
/*
	$('.masupdate').live('click',function(event){
		var a = new Array();
		var source=$(this).attr('href');
		$("input[type=checkbox]").each(function(){
			if($(this).is(":checked")){
				a.push($(this).val());
			}
		});
		if(a.length>0){
			$.get(this.href+"/"+a[0], function(data){
				$("#popupDiv").html('<b id="closeId">&nbsp;&nbsp;&nbsp;&nbsp;</b>' + data);
				$("#popupDiv").css( {
					backgroundColor: '#FFF', 
					borderColor: '#ccc',
					'border-radius': '10px', 
					opacity: .9,
					color: '#000',
					overflow:'auto',
					cursor:'default'
				});
				$('#ids').val(a);
			});
			$.blockUI({ message: $('#popupDiv') });
			return false;
		}else{
			jAlert('Please Select atleast one  Group');
			 return false;
			}
		
	});
*/
	$('.grp_emp').live('click',function(event){
		var str=$(this).attr('id');
		if($(this).is(":checked")){
			$('#eid'+str).removeAttr('disabled');
			$('#starttime'+str).removeAttr('disabled');
			$('#endtime'+str).removeAttr('disabled');
			$('#isfailover'+str).removeAttr('disabled');
			$('#area_code'+str).removeAttr('disabled');
			$('#empweight'+str).removeAttr('disabled');
			$('#empPriority'+str).removeAttr('disabled');
			$('#pcode'+str).removeAttr('disabled');
		}else{
			$('#eid'+str).attr('disabled',true);
			$('#starttime'+str).attr('disabled',true);
			$('#endtime'+str).attr('disabled',true);
			$('#isfailover'+str).attr('disabled',true);
			$('#area_code'+str).attr('disabled',true);
			$('#empweight'+str).attr('disabled',true);
			$('#empPriority'+str).attr('disabled',true);
			$('#pcode'+str).attr('disabled',true);	
		}
		
	});
	
	
	//~ $('.blkAssign').live('click',function(event){
		//~ var a = new Array();
		//~ //var nurl=$('.blkAssign').attr('href');
		//~ //alert(nurl);
		//~ $("input[type=checkbox]").each(function(){
			//~ if($(this).is(":checked")){
				//~ a.push($(this).val());
			//~ }
		//~ });
		//~ if(a.length==0){
			//~ jAlert('Please Select atleast one item to Assign');
			 //~ return false;
		//~ }else{
			//~ event.preventDefault();
			//~ event.stopPropagation();
			//~ $('embed').hide();
			//~ $.get(this.href, function(data){
				//~ $("#popupDiv").html('<b id="closeId">&nbsp;&nbsp;&nbsp;&nbsp;</b>' + data);
				//~ $("#popupDiv").css( {
					//~ backgroundColor: '#FFF', 
					//~ borderColor: '#ccc',
					//~ 'border-radius': '10px', 
					//~ opacity: .9,
					//~ color: '#000',
					//~ overflow:'auto',
					//~ cursor:'default'
				//~ });
				//~ $('#ids').val(a);
			//~ });
			//~ $.blockUI({ message: $('#popupDiv') });
			//~ return false;
		//~ }
	//~ });
	$('.blkStatus').live('click',function(event){
		var a = new Array();
		//var nurl=$('.blkAssign').attr('href');
		//alert(nurl);
		$("input[type=checkbox]").each(function(){
			if($(this).is(":checked")){
				a.push($(this).val());
			}
		});
		if(a.length==0){
			jAlert('Please Select atleast one item to Assign');
			 return false;
		}else{
			event.preventDefault();
			event.stopPropagation();
			$('embed').hide();
			$.get(this.href, function(data){
				$("#popupDiv").html('<b id="closeId">&nbsp;&nbsp;&nbsp;&nbsp;</b>' + data);
				$("#popupDiv").css( {
					backgroundColor: '#FFF', 
					borderColor: '#ccc',
					'border-radius': '10px', 
					opacity: .9,
					color: '#000',
					overflow:'auto',
					cursor:'default'
				});
				$('#ids').val(a);
			});
			$.blockUI({ message: $('#popupDiv') });
			return false;
		}
	});
	$('#keyword_use').change(function(){
		var fw = "<?php
			$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
			$fieldset = $this->configmodel->getFields('7');
			foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['fieldname']=='fowardto_type'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked) echo addslashes(str_replace("\n"," ","<tr id=\"fdUse\"><th><label for=\"".$field['fieldname']."\">".(($field['customlabel']!="")?$field['customlabel']:$this->lang->line('level_'.$field['fieldname']))." : </label></th><td>".(form_dropdown('fowardto_type',$this->keywordmodel->forward(),isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:'','id="fowardto_type"'))."</td></tr>"));			
				}
			}
		?>";
		
		
		
		if($('#keyword_use').val()==2){
		
		$(this).parent().parent().parent().append(fw);
		}
		else{
			$("#fdUse").remove();
			$("#fdtype").remove();
		}
	});
	$('#fowardto_type').live('change',function(){
		
		if($('#fowardto_type').val()!="")
		{
			if($('#fowardto_type').val()=="group"){
				$(this).parent().parent().next().remove();

				var fw="<?php echo addslashes(str_replace("\n"," ","<tr id=\"fdtype\"><th><label>Group : </label></th><td>".form_dropdown('group',$this->systemmodel->get_groups(),'','id="group"')."</td></tr>")); ?>";

				$(this).parent().parent().parent().append(fw);
			}
			if($('#fowardto_type').val()=="employee"){
				
				$(this).parent().parent().next().remove();
				var fw="<?php echo addslashes(str_replace("\n"," ","<tr id=\"fdtype\"><th><label>Employee : </label></th><td>".form_dropdown('employee',$this->systemmodel->get_emp_list(),'','id="employee"')."</td></tr>")); ?>";
				$(this).parent().parent().parent().append(fw);
			}
		}
		else{
			$("#fdtype").remove();
		}
		
	});
	$('#autoadd').validate({
		rules:{
			filename:{
				accept:'txt|csv'
				}
		}
		,errorPlacement: function(error, element) {
			error.appendTo( element.parent().next() );
		}
	});
	$('#cfrm_number').validate({
		rules:{
			
		}
		,errorPlacement: function(error, element) {
			error.appendTo( element.parent().next() );
		}
	});
	$('#blocknumbers').validate({
		rules:{
			blacklist:{
				required:true,
				number:true
			}
		}
		,errorPlacement: function(error, element) {
			error.appendTo( element.parent().next() );
		}
	});
	$('#qrconfig').validate({
		rules:{
			
		},
		errorPlacement: function(error, element) {
			error.appendTo( element.parent().next() );
		},submitHandler: function(form){
			var selector_checked = $('input[type="checkbox"]:checked').length; 
			$('.qrused').parent().next().html("");
			if(selector_checked==0){
					$('.qrused').parent().next().append("Please select one qruse");
					return false;
			}else{
				form.submit();
			}
		}
	});
	if($('#rules').val()=='3'){
		$('#url').removeAttr('disabled');
		$('#url').addClass('required url');
	}else{
		$('#url').attr('disabled','true');
	}
	$('#rules').change(function(){
		if($(this).val()=='3'){
			$('#url').removeAttr('disabled');
			$('#url').addClass('required url');
			$('#url').val('http://');
		}else{
			$('#url').val('');
			$('#url').attr('disabled','true');
		}
	})
	
	$('.addVar').live('click',function(event){
			$(this).parent().parent().find('textarea').val($(this).parent().parent().find('textarea').val()+this.rel);
			//$('#replymessage').val($('#replymessage').val()+ this.rel);
			//$('#labeloptions').val($('#labeloptions').val()+ this.rel);
	});
	$('.delC').live('click',function(event){
		var nurl=$(this).attr('href');
		var rels=$(this).attr('rel');
		var str=nurl.split("/");
			$.alerts.okButton = 'Yes';
			$.alerts.cancelButton = 'No';
			jConfirm('<b>Are you sure to Delete </b>','Alert', function(r) {
							if(r){
								
								$.ajax({ 
								type: "POST",  
								url: nurl,  
								success: function(msg){ 
										$.get('activity/acCustom/'+rels+'/'+str[5], function(data){
												$("#popupDiv").html('<b id="closeId">&nbsp;&nbsp;&nbsp;&nbsp;</b>' + data);
												$("#popupDiv").css( {
													backgroundColor: '#FFF', 
													borderColor: '#ccc',
													'border-radius': '10px', 
													opacity: .9,
													color: '#000',
													overflow:'auto',
													cursor:'default'
												});
										});
									}
								});
							}
						});
						
				return false;	
		
	});
	
	$('.AcEmp').live('click',function(event){
		var nurl=$(this).attr('href');
		var str=nurl.split("/");
			$.alerts.okButton = 'Yes';
			$.alerts.cancelButton = 'No';
			jConfirm('<b>Are you sure to Delete </b>','Alert', function(r) {
							if(r){
								
								$.ajax({ 
								type: "POST",  
								url: nurl,  
								success: function(msg){ 
										$.get('activity/activity_member/'+str[6]+'/'+str[7], function(data){
												$("#popupDiv").html('<b id="closeId">&nbsp;&nbsp;&nbsp;&nbsp;</b>' + data);
												$("#popupDiv").css( {
													backgroundColor: '#FFF', 
													borderColor: '#ccc',
													'border-radius': '10px', 
													opacity: .9,
													color: '#000',
													overflow:'auto',
													cursor:'default'
												});
										});
									}
								});
							}
						});
						
				return false;	
	});

	$('#otp').live('click',function(){
		if(!$(this).attr("checked")){
			$.alerts.okButton = '&nbsp;Confirm&nbsp;';
			$.alerts.cancelButton = '&nbsp;Enable OTP&nbsp;';
			jConfirm('<b>You have disabled your '
						+"'One Time Password (OTP)'"
						+'.This can impact security of your data if someone knows your password</b>',
						'OTP confirmation',
						function(r) {
						if(!r){
							//$('#businessprofile').submit();
							$('#otp').attr("checked",true);
						}else{
							$('#otp').attr("checked",false);
						}
						
					});
			//$(this).attr("checked",true);		
		}
		
	});
	$('#groupId').live('change',function(event){
	  var r=$('#groupId').val();
	  $.ajax({  
			type: "POST",  
			url: "<?=base_url()?>leads/get_grEmployees/"+r,  
			success: function(msg){
				$('#employeeId option').each(function(i, option){ $(option).remove(); });
				$('#employeeId').append(msg);
				}
			});
		});
});
function textCounter(textarea,counterID,maxLen) {
	cnt = document.getElementById(counterID);
	
		if (textarea.value.length>maxLen)
		{
			textarea.value=textarea.value.substring(0,maxLen);
		}
		cnt.innerHTML=maxLen-textarea.value.length;
}
function DelGroup(data,url){
	$.ajax({  
			type: "POST",  
			url: "group/Clone_Group_Add/",  
			data:data+'&update_system=update_system&urls='+url, 
			success: function(msg){
				$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
				window.location="<?php echo Base_url();?>group/manage_group";	
			}
		});	
			
	return false;
}

    
function deletefile(){
	document.form.fileupload1.value = '';
	    $('#hdayaudio').attr('name', 'fileupload1');
		$('#hdayaudio').attr('id', 'fileupload1');
		$('#fileupload1').attr('id', 'hdayaudio');
	    $("#fileupload1").hide();
		$("#hdayaudio").show();
		$("#addimg").show();
		$("#closepng").css("display","none");
		
}
function deletefile1(){
	document.form.fileupload.value = '';
	    $('#greetings').attr('name', 'fileupload');
		$('#greetings').attr('id', 'fileupload');
		$('#fileupload').attr('id', 'greetings');
		$("#fileupload").hide();
		$("#greetings").show();
		$("#addimg1").show();
		$("#closepng1").css("display","none");
}
//~ function deletevoice(){
	//~ document.form.fileupload.value = '';
	    //~ $('#voicemessage').attr('name', 'voiceupload');
		//~ $('#voicemessage').attr('id', 'voiceupload');
		//~ $('#voiceupload').attr('id', 'voicemessage');
		//~ $("#voiceupload").hide();
		//~ $("#voicemessage").show();
		//~ $("#addvoice").show();
		//~ $("#closevoice1").css("display","none");
//~ }

     $( window ).load(function() {
		$("#closepng").hide();
		$("#fileupload1").hide();
		$("#fileupload").hide();
    	$("#closepng1").hide();
     	$("#delimg").show();
    	$("#delimg1").show();
    		$("#delvoice").show();
    		$("#closevoice1").hide();
    		$("#voiceupload").hide();
	 });


</script>
<link rel="stylesheet" href="system/application/css/colorpicker.css" type="text/css" />
<script type="text/javascript" src="system/application/js/colorpicker.js"></script>
<script type="text/javascript" src="system/application/js/eye.js"></script>
