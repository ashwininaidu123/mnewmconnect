//~ ddsmoothmenu.init({
	//~ mainmenuid: "topmenu", //menu DIV id
	//~ orientation: 'h', //Horizontal or vertical menu: Set to "h" or "v"
	//~ classname: 'ddsmoothmenu', //class added to menu's outer DIV
	//~ //customtheme: ["#1c5a80", "#18374a"],
	//~ contentsource: "markup" //"markup" or ["container_id", "path_to_menu_file"]
//~ });
//~ 
//~ ddsmoothmenu.init({
	//~ mainmenuid: "topmenu1", //menu DIV id
	//~ orientation: 'h', //Horizontal or vertical menu: Set to "h" or "v"
	//~ classname: 'toplinksM', //class added to menu's outer DIV
	//~ //customtheme: ["#1c5a80", "#18374a"],
	//~ contentsource: "markup" //"markup" or ["container_id", "path_to_menu_file"]
//~ });

//~ $(function() {
	//~ addons = $('#addons').val();
	//~ if(addons!=''){
		//~ addon = addons.split(',');
		//~ $.each(addon,function(i,v){
			//~ if(v.length>0){
				//~ //alert($('#'+v).val())
				//~ $('#'+v).attr('disabled','disabled');
			//~ }
		//~ });
	//~ }
//~ });
	
	
//~ jQuery(document).bind("keyup keydown", function(e){
	//~ if(e.ctrlKey){// && e.keycode == 80
		//~ return false;
	//~ }
//~ });
$(function() {
	$('.blk_submit').live('click',function(event){
		$("#blk_ddd").validate({ 
			rules: { 
					"formfields[]": { 
							required: true, 
							minlength: 1 
					} 
			}, 
			messages: { 
					"formfields[]": "Please select at least One field"
			},errorPlacement: function(error, element) {
                        error.appendTo( element.parent().next() );
                }
		}); 
	});
	
	$('.blk_calls').live('click',function(event){
		var a = new Array();
		$("input[type=checkbox]").each(function(){
			if($(this).is(":checked")){
				a.push($(this).val());
			}
		});
		if(a.length==0){
			jAlert('Please Select atleast one Download');
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
				$('#call_ids').val(a);
			});
			$.blockUI({ message: $('#popupDiv') });
			return false;
		}
	});
	
	
	//~ $('.blk_assign').live('click',function(event){
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
			//~ 
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
	//~ $('#nor_search').hide();
	//~ $('#adv_search').hide();
	//~ $('#nor_s').live('click',function(event){
		//~ $('#adv_search').hide();
		//~ $('#nor_search').show();
	//~ });
	//~ $('#adv_s').live('click',function(event){
		//~ $('#nor_search').hide();
		//~ $('#adv_search').show();
	//~ });
	$('.remRow').live('click',function(event){
		//$("#addmore").css('display','inline');
		var j=$('#fsizec').val();
		j--;
		$('#fsizec').val(j);
			$(this).parent().parent().remove();
	});
	
	$('#Etemp').validate({
			
	});
	function GetBaseUrl() {
		try {
			var url = location.href;
			var start = url.indexOf('//');
			if (start < 0)
			{ start = 0 } else { start = start + 2; };
			var end = url.indexOf('/', start);
			if (end < 0) end = url.length - start;
			var baseURL = url.substring(start, end);
			return baseURL;
		}
		catch (arg) {
			return null;
		}
	} 
	$('#tid').live('change',function(event){
		var val=$('#tid').val();
		$.ajax({ 
			type: "POST",  
			url: 'https://'+GetBaseUrl()+'/Email/TemplateC/'+val,  
			success: function(msg){ 
				CKEDITOR.instances['content'].setData(msg);
			}
		});
	});	
	$('#parentbid').live('change',function(event){
		$.ajax({
			type: "POST",
			url: "group/systemOperationset/",
			data: "bid="+$('#parentbid').val(), 
			dataType:"text",
			success: function(msg){
				window.location.href=window.location.href;
			}
		})
	});
	
	jQuery.validator.addMethod("numeric", function(value, element) {
		return this.optional(element) ||value == value.match(/^[0-9]+$/);
	}, "Should be digits"); 
		
	$('.word_count').each(function(){ 
		var length = $(this).val().length;  
		$(this).parent().prev().append('<label class="counter">'+length + '/1  Char'+'</label>');  
		$(this).keyup(function(){
			if($(this).val().length>140){
				$(this).val($(this).val().substring(0,140));
			}
			var new_length = $(this).val().length;  
			$(this).parent().prev().find('.counter').html( new_length + '/' + Math.ceil(new_length/140) + ' Char');  
		});  
	}); 
	
	//$("#myTable").tablesorter();
	$("#myTable tr td:last").css('width','150');
	$("#myTable tr td:last").css('textalign','center');
	//$("#myTable").tablesorter( {sortList: [[0,0], [1,0]], widgets: ['zebra']} );
	//alert('Tapan');
	//~ $("#SearchButton").click(function() {
			//~ alert("SCASD");
		//~ $( ".searchBox" ).toggle( 'blind', {}, 500 );
		//~ return false;
	//~ });
	//~ 
	//~ $("#SearchButton1").click(function() {
		//~ $('#type').val();
		//~ $( ".searchBox" ).toggle( 'blind', {}, 500 );
		//~ return false;
	//~ });
	$( ".datepicker" ).datepicker({
		dateFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true,
		minDate: -60,
		maxDate: +0
	});
	
	$( ".datepicker_leads" ).datepicker({
		dateFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true,
	    minDate: -60,
		maxDate: +0
	});
	$( ".datepicker1" ).datepicker({
		dateFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true,
		minDate: +0
	});
	
	$( ".monthyear" ).datepicker({
		dateFormat: 'yy-mm',
		changeMonth: true,
		changeYear: true,
		maxDate: +0
	});
	$('.datetimepicker').datetimepicker({
		dateFormat: 'yy-mm-dd',
		ampm: false
	});
	$('.timepicker').timepicker({});
	
	$('.paging').click(function() {
		$.blockUI({ message: $('<b id="closeId">&nbsp;&nbsp;&nbsp;&nbsp;</b><img src="system/application/img/wait.gif">') });
	});
	
	////////////////// POPUP ///////////////////////
	       $("#sendsms").validate({ 
			   
						});	
					   
		   $('#addlead').validate({
                rules:{
                        name:{
                                required:true
                            },
                        number:{
                                required:true,
                                number:true
                        },
                        gid:{
                                required:true,
                                number:true
                        },
                       // assignto:{
                              //  required:true,
                              //  number:true
                       // }
                },messages:{
					 name:{
							required:"Please Enter Name"
					 },
					 number:{
							required:"Please Enter Number"
					 },
					 gid:{
							required:"Please Select Group"
					 },
					// assignto:{
							//required:"Please Select Employee"
					// }
				 },errorPlacement: function(error, element) {
                        error.appendTo( element.parent().next() );
                }
                ,submitHandler: function(form){
					$.ajax({
						type:"POST",  
						url:"/leads/leadDuplicate/",  
						data:"number="+$('#number').val()+"&email="+$('#email').val(),
						success: function(msg){
							var str=jQuery.trim(msg);
							if(str != 'no'){
								var nurl=$(this).attr('href');
								$.alerts.okButton = 'Yes';
								$.alerts.cancelButton = 'No';
								jConfirm('The Lead already existed. Do you want to add Duplicate?','Alert', function(r) {
									if(r){
										$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
										$('#parentId').val(str);
										$('#duplicate').val(1);
										form.submit();
									}
								});
							}else{
								$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
								$('#duplicate').val(0);
								$('#parentId').val(0);
								form.submit();
							}
					}
				})
			}
        });
		   $('#addleadgrp').validate({
                rules:{
                        groupname:{
                                required:true,
                            },
                        eid:{
                                required:true,
                                number:true
                            },
                        grule:{
                                required:true,
                                number:true
                        }
                },messages:{
					 groupname:{
							required:"Please Enter Group Name"
					 },
					 eid:{
							required:"Please Select Group Owner"
					 },
					 grule:{
							required:"Please Select Group Rule"
					 }
				 },errorPlacement: function(error, element) {
                        error.appendTo( element.parent().next() );
                }


        });
        $('#editlead').validate({
                rules:{
                      name:{
                           required:true,
                      }, number:{
                           required:true,
                           number:true
                      },
                 },messages:{
					 name:{
							required:"Please Enter Name"
					 },
					 number:{
							required:"Please enter number"
					}
				},errorPlacement: function(error, element) {
                        error.appendTo( element.parent().next() );
                },submitHandler: function(form){
					var numval;
					numval = $('#number').val();
					if(isNaN(numval)){
						numval = $('#callfrom').val();
					}
					$.ajax({
						type:"POST",  
						url:"/leads/leadDupliCheck/",  
						data:"number="+numval+"&email="+$('#email').val()+"&leadid="+$('#leadid').val(),
						success: function(msg){
							var str=jQuery.trim(msg);
							if(str!="no"){
								var nurl=$(this).attr('href');
								$.alerts.okButton = 'Yes';
								$.alerts.cancelButton = 'No'; 
								jConfirm('The Lead already existed. Do you want to add Duplicate?','Alert', function(r) {
									if(r){
										$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
										$('#duplicate').val(1);
										$('#parentId').val(str);
										form.submit();
									}
								});
							}else{
								$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
								//$('#duplicate').val(0);
								form.submit();
							}
					}
				})
			}
        });
		   $('#addSupportGrp').validate({
                rules:{
                        groupname:{
                                required:true,
                            },
                        eid:{
                                required:true,
                                number:true
                            },
                        grule:{
                                required:true,
                                number:true
                        }
                },messages:{
					 groupname:{
							required:"Please Enter Group Name"
					 },
					 eid:{
							required:"Please Select Group Owner"
					 },
					 grule:{
							required:"Please Select Group Rule"
					 }
				 },errorPlacement: function(error, element) {
                        error.appendTo( element.parent().next() );
                }


        });
        $('#addSupportTkt').validate({
                rules:{
                        name:{
                                required:true
                            },
                        //~ email:{
								//~ required: true,
                                //~ email:true
                            //~ },
                        number:{
                                required:true,
                                number:true
                        },
                        gid:{
                                required:true,
                                number:true
                        },
                       // assignto:{
                              //  required:true,
                              //  number:true
                       // }
                },messages:{
					 name:{
							required:"Please Enter Name"
					 },
					 number:{
							required:"Please Enter Number"
					 },
					 //~ email:{
						    //~ required:"Please Enter Email",
							//~ required:"Please Enter Valid Email"
					 //~ },
					 gid:{
							required:"Please Select Group"
					 },
					// assignto:{
							//required:"Please Select Employee"
					// }
				 },errorPlacement: function(error, element) {
                        error.appendTo( element.parent().next() );
                }


        });
        $('#edittkt').validate({
                rules:{
                      name:{
                           required:true,
                      }, number:{
                           required:true,
                           number:true
                      },
                 },messages:{
					 name:{
							required:"Please Enter Name"
					 },
					 number:{
							required:"Please enter number"
					}
				},errorPlacement: function(error, element) {
                        error.appendTo( element.parent().next() );
                }
        });

        $('#addobcgrp').validate({
                rules:{
                        groupname:{
                                required:true,
                            },
                        eid:{
                                required:true,
                                number:true
                            },
                        grule:{
                                required:true,
                                number:true
                        }
                },messages:{
					 groupname:{
							required:"Please Enter Group Name"
					 },
					 eid:{
							required:"Please Select Group Owner"
					 },
					 grule:{
						    required:"Please Select Group Rule"
				     }
				 },errorPlacement: function(error, element) {
                        error.appendTo( element.parent().next() );
                }


        });
         

		$('#conadd').validate({
		rules:{
			
			name:{
				required:true,
				},
			email:{
				required:true,
				email:true
				},
			contact_no:{
				required:true,
				mobile:true
				},
			filename:{
				accept:'csv'
			}
			
			},
		messages:{
			
			name:{
				required:"Please Enter Contact Name"
				},
			email:{
				required:"Please Enter Contact Email"
				},
			contact_no:{
				required:"Please Enter Contact Number"
				},
			filename:{
				accept:'Select .csv file'
			}
		},
		errorPlacement: function(error, element) {
								error.appendTo( element.parent().next() );
						}
	});
		
		$('#followup').validate({
		rules:{
			notify_time:{
				digits : true
			}
		},
		messages:{
			notify_time:{
				required:"Please Enter valid limit "
			},
		},
		errorPlacement: 
			function(error, element) {
				error.appendTo( element.parent().next() );
			}
		});
		
	
		

        $('#intwgrpadd').validate({
                rules:{
                      group_name:{
                           required:true,
                      }, intw_id:{
                           required:true,
                           number:true
                      }, eid:{
                           required:true,
                      },
                 },messages:{
					 group_name:{
						required:"Please Enter Group Name"
					 },
					 intw_id:{
						required:"Please Refresh the page for Interview Id"
					 },
					 eid:{
						required:"Please Select Interviewer"
					 }
				},errorPlacement: function(error, element) {
                     error.appendTo( element.parent().next() );
                }
        });
        $('#qbadd').validate({
                rules:{
                      name:{
                           required:true,
                      }, 
                 },messages:{
					 name:{
						required:"Please Enter Group Name"
					 },
				},errorPlacement: function(error, element) {
                     error.appendTo( element.parent().next() );
                }
        });

	$('#Adminblocknumbers').validate({
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
	$('#billconfig').validate({
		rules:{
			
		}
	});
	$('#billpayment').validate({
		//~ submitHandler: function(form){
			//~ var netamount=$('#netamt').val();
			//~ var act=0;
			//~ var ps=$('#payid').val();
			//~ 
			//~ if(ps!=0){
				//~ var l='';
				//~ for(var i=0;i<=ps;i++){
					//~ l=((i!=0)?i:'');
					//~ act+=parseFloat($('#payment'+l).val());
					//~ 
				//~ }
			//~ }else{
				//~ 
			//~ }
			//~ 
		//~ }
	});
	$('#distype').live('click',function(){
		if($(this).val()==1){
			$('#disamount').val('');
			$('#disamount').attr('maxlength', '4');
		}else{
			$('#disamount').val('');
			$('#disamount').attr('maxlength', '5');
		}
	});
	$('#api_reload').live('click',function(){
	$.alerts.okButton = 'Yes';
		$.alerts.cancelButton = 'No';
		jConfirm('<b>Are You Sure You want to change API Key?</b>','Api Confirmation', function(r) {
			if(r){
				$.ajax({
					  type: "POST",
					  url: "Business/gen_apisecret/",
					  dataType:"text",
					  success: function(msg){
							var str=jQuery.trim(msg);
							 if(str!=0){
								$('#apisecret').val(str);
								return false; 
							 }
						  }
				 });
			}
		});
	});
	
	$('#missedgroup').validate({
		
	});
	$('#AddBusinessUser').validate({
		rules:{
			login_businessname:{
				required:true
			},cname:{
				required:true
			},cemail:{
				required:true,
				email:true
			},login_username:{
				required:true,
				email:true,
				equalTo: "#cemail",
			},waddress:{
				url:true
			},cphone:{
				required:true
			},baddress:{
				required:true
			},city:{
				required:true
			},state:{
				required:true
			},country:{
				required:true
			},locality:{
				required:true
			},zipcode:{
				required:true
			}
		 },messages:{
			login_businessname:{
				required:"Business Name is required"
			},cname:{
				required:"Contact Name is required"
			},cemail:{
				required:"Email is required",
				email:"Please enter valid email address"
			},login_username:{
				required:"Confirm Email is required",
				email:"Please enter valid email address",
				equalTo:"Email and confirm email should be same"
			}	
		},
		errorPlacement: function(error, element) {
			error.appendTo( element.parent().next() );
		},submitHandler: function(form){
			 $.ajax({
				  type: "POST",
				  url: "partner/checkBusinessuser/",
				  data: "email="+$('#cemail').val()+"&bid="+$('#bid').val(), 
				  dataType:"text",
				  success: function(msg){
						$('#cemail').parent().next().html("");
						 var str=jQuery.trim(msg);
						 if(str!=0){
							$('#cemail').parent().next().append("Email is already in use");
							return false; 
						 }else{
							 form.submit();
						 }
					  }
			 });
		}
	});	
	$('#addbusinessuser').validate({
		rules:{
			login_businessname:{
				required:true
			},cname:{
				required:true
			},cemail:{
				required:true,
				email:true
			},login_username:{
				required:true,
				email:true,
				equalTo: "#cemail",
			},waddress:{
				url:true
			},cphone:{
				required:true
			},baddress:{
				required:true
			},city:{
				required:true
			},state:{
				required:true
			},country:{
				required:true
			},locality:{
				required:true
			},zipcode:{
				required:true
			}
		 },messages:{
			login_businessname:{
				required:"Business Name is required"
			},cname:{
				required:"Contact Name is required"
			},cemail:{
				required:"Email is required",
				email:"Please enter valid email address"
			},login_username:{
				required:"Confirm Email is required",
				email:"Please enter valid email address",
				equalTo:"Email and confirm email should be same"
			}	
		},
		errorPlacement: function(error, element) {
			error.appendTo( element.parent().next() );
		},submitHandler: function(form){
			 $.ajax({
				  type: "POST",
				  url: "Masteradmin/checkBusinessuser/",
				  data: "email="+$('#cemail').val()+"&bid="+$('#bid').val(), 
				  dataType:"text",
				  success: function(msg){
						$('#cemail').parent().next().html("");
						 var str=jQuery.trim(msg);
						 if(str!=0){
							$('#cemail').parent().next().append("Email is already in use");
							return false; 
						 }else{
							 form.submit();
						 }
					  }
			 });
		}
	});	
	$('#Addpartner').validate({
		rules:{
			confirmemail:{
				required:true,
				email:true,
				equalTo: "#partneremail",
			},
			cdomainname:{
				required:true,
				equalTo: "#domainname",
			}
			
		},messages:{
			confirmemail:{
				equalTo:"Email and confirm email should be same"
			}
		},errorPlacement: function(error, element) {
			error.appendTo( element.parent().next() );
		},submitHandler: function(form){
			$.ajax({
				type: "POST",
				url: "Masteradmin/checkDomain_exists/",
				data: "domain="+$('#domainname').val()+"&email="+$('#partneremail').val()+"&partner_id="+$('#patner_id').val(), 
				dataType:"text",
				success: function(msg){
					var str=jQuery.trim(msg);
					if(str!="Availble"){
						$('#partneremail').parent().next().html("");
						$('#domainname').parent().next().html("");
						if(str=="partneremail"){
							$('#partneremail').parent().next().append("Email is already in use");
							return false;
						}
						if(str=="domainname"){
							$('#domainname').parent().next().append("Domain Name is already in use");
							return false;
						}
						if(str=="domainname and partneremail"){
							$('#domainname').parent().next().append("Domain Name and Email are already in use");
							return false;
						}
					}else{
						$('#Addpartner').attr("action","Masteradmin/Addpartner/"+$('#patner_id').val());
						form.submit();
					}
				}
			})
			return false;
		}
	});
	$('.signout').live('click',function(event){
		$.alerts.okButton = 'Yes';
		$.alerts.cancelButton = 'No';
		jConfirm('<b>Are you sure to signout from here</b>','Signout confirmation', function(r) {
			if(r){
				$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
				window.location.href=$('.signout').attr('href');
				return true;
			}
		});
		return false;		
	});
	
	
	$(".callPopup").live('click',function(event) {
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
				cursor:		'default'
			});
		});
		$.blockUI({ message: $('#popupDiv') });
		return false;
	});
	$("#closeId").live('click',function(event){
		$('embed').show();
		$.unblockUI();
		$("#popupDiv").html('');
	});
	
	$(".confirm").easyconfirm({locale: {
		title: 'Confirmation',
		text: 'Are you sure,You want to perform this job?',
		button: ['No','Yes'],
		closeText: 'close'
	}});
	$(".deleteClass1").live('click',function(event) {
		event.preventDefault();
		event.stopPropagation();
		$('embed').hide();
		var id=$(".deleteClass1").attr('id');
		var links=this.href;
		$.alerts.okButton = 'Yes';
		$.alerts.cancelButton = 'No';
		jConfirm('Are you sure to undelete group?','Confirmation Dialog', function(r) {
			if(r){
				//$.blockUI({ message: $('<b id="closeId">&nbsp;&nbsp;&nbsp;&nbsp;</b><img src="system/application/img/wait.gif">') });		
				$.get(links, function(data){
					if(data!=1){
						jConfirm(data,'Confirmation Dialog', function(r) {
							if(r){
									event.preventDefault();
									event.stopPropagation();
									$('embed').hide();
									$.blockUI({ message:$('#popupDiv').load("group/Undelte_emplist/"+id)});
								
							}
							
						});
						
					}else{
						window.location="group/manage_group";
						
					}
				});
			}
		});
		return false;
	
	});
	$(".deleteClass").live('click',function(event) {
		event.preventDefault();
		event.stopPropagation();
		$('embed').hide();
		var links=this.href;
		$.alerts.okButton = 'Yes';
		$.alerts.cancelButton = 'No';
		jConfirm('Are you sure?','Confirmation Dialog', function(r) {
			if(r){
				$.blockUI({ message: $('<b id="closeId">&nbsp;&nbsp;&nbsp;&nbsp;</b><img src="system/application/img/wait.gif">') });		
				$.get(links, function(data){
					$.unblockUI();
					window.parent.location.href = window.parent.location.href;
				});
			}
		});

		
	});
	$('.tooTip').bt({
		padding: 10,
		width: 150,
		spikeLength: 70,
		spikeGirth: 8,
		cornerRadius: 10,
		fill: '#000000',
		strokeWidth: 1,
		strokeStyle: '#f88e47',
		cssStyles: {color: '#FFFFFF', fontWeight: 'bold'}
	});
	
	$('.qrused').click(function(){
		var id=this.id;
		//alert($(this).is(":checked"));
		if($(this).is(":checked") && id!=3){
			$.get("qrcode/Appenedefields/"+id, function(data){
				$('fieldset').last().after('<fieldset id="use_'+id+'">'+data+'</fieldset>');				
			});
		}else{
			$('#use_'+id).remove();
		}
	});
	$('.qrused').each(function(){
		var id=this.id;
		var qrid=$('input[name$="qrid"]').val()
		if($(this).is(":checked") && id!=3){//alert('Tapan');
			$.get("qrcode/Appenedefields/"+id+"/"+qrid, function(data){
				$('fieldset').last().after('<fieldset id="use_'+id+'">'+data+'</fieldset>');				
			});
		}
	})
	$('#addp').live('click',function(){
		var ids=$('#payid').val();
		ids++;
		var html='<tr><td>Payment</td><td>:<input type="text" name="payment'+ids+'" id="payment'+ids+'" class="required" /></td></tr><tr><td>Mode</td><td>: <select name="paymode'+ids+'" id="paymode'+ids+'" class="required"><option value="cheque">cheque</option><option value="cash">cash</option></select></td></tr><tr><td>status</td><td>: <select name="status'+ids+'" id="status'+ids+'" class="required"><option value="1">cleared</option><option value="2">uncleared</option></select></td></tr><tr><td>cheque No</td><td>: <input type="text" name="cheno'+ids+'" value="" id="cheno'+ids+'"/></td></tr><tr><td>Bank Name</td><td>: <input type="text" name="bname'+ids+'"  value="" id="bname'+ids+'"   /></td></tr><tr><td>Branch Name</td><td>: <input type="text" name="brname'+ids+'"  value="" id="brname'+ids+'"   /></td></tr>';
		$(this).parent().parent().parent().last().append(html);
		$('#payid').val(ids);
		
	});
	
	$('.callconnect').live('click',function(){
		event.preventDefault();
		event.stopPropagation();
		var links=this.href;
		var c = this
		$(this).html('Connecting...');
		//$.blockUI({ message: $('<b id="closeId">&nbsp;&nbsp;&nbsp;&nbsp;</b><img src="system/application/img/wait.gif"><h2>Call is Connection</h2>') });		
		$.get(links, function(data){
			//$.unblockUI();
			//window.parent.location.href = window.parent.location.href;
			c.href= "";
			$(c).html('<img src="system/application/img/icons/lock.png" title="Record in process" />');
		});
	})
	
	$('.clickToConnect').live('click',function(event){
		event.preventDefault();
		event.stopPropagation();
		var links=this.href;
		$.alerts.okButton = 'Yes';
		$.alerts.cancelButton = 'No';
		jConfirm('Are you sure you want to connect the Number?','Connect Confermation', function(r) {
			if(r){
				$.blockUI({ message: $('<b id="closeId">&nbsp;&nbsp;&nbsp;&nbsp;</b><br><br><br><img src="system/application/img/loading.gif"><br><br><br><h2>Connecting...</h2>') });
				$.get(links, function(data){
					$.unblockUI();
					if(data==1){
						$.blockUI({ message: $('<b id="closeId">&nbsp;&nbsp;&nbsp;&nbsp;</b><br><br><br><img src="system/application/img/loading.gif"><br><br><br><h2>Your call is in progress...</h2>') });
						//$.unblockUI();
					}else{
						$.blockUI({ message: $('<b id="closeId">&nbsp;&nbsp;&nbsp;&nbsp;</b><br><br><h2>Request fail,'+ data +'</h2><br><br>') });
					}
				});
			}
		});
		return false;	
	});
	function getSmsBalance(){
		var nurl="/Email/smsBalance";
		 var result = null;
		 $.ajax({
			url: nurl,
			type: 'get',
			dataType: 'html',
			 async: false,
			  cache:false,
			success:function(data)
			{
				result = parseInt(data);
			} 
		 });
		return result;
	}
	$('.clickToSMS').live('click',function(event){
		var balance=0;
		balance=getSmsBalance();
		if(balance>0){
			$.get(this.href, function(data){
				$("#popupDiv").html('<b id="closeId">&nbsp;&nbsp;&nbsp;&nbsp;</b>' + data);
				$("#popupDiv").css( {
					backgroundColor: '#FFF', 
					borderColor: '#ccc',
					'border-radius': '10px', 
					opacity: .9,
					color: '#000',
					overflow:'auto',
					cursor:		'default'
				});
			});
			$.blockUI({ message: $('#popupDiv') });
			return false;
			
			
		}else{
			jAlert("You Don't have enough credit to send SMS please contact Administrator");
			return false;
			
		}
	});
	$('.clicktoFields').live('click',function(event){
		/*var balance=0;
		balance=getSmsBalance();
		if(balance>0){*/
			$.get(this.href, function(data){
				$("#popupDiv").html('<b id="closeId">&nbsp;&nbsp;&nbsp;&nbsp;</b>' + data);
				$("#popupDiv").css( {
					backgroundColor: '#FFF', 
					borderColor: '#ccc',
					'border-radius': '10px', 
					opacity: .9,
					color: '#000',
					overflow:'auto',
					cursor:		'default'
				});
			});
			$.blockUI({ message: $('#popupDiv') });
			return false;
			
			
		/*}else{
			jAlert("You Don't have enough credit to send SMS please contact Administrator");
			return false;
			
		}*/
	});
	//~ $('.SenDSMS').live('click',function(event){
		//~ var smsBal=parseInt($('#smsBal').val());
		//~ $("#sendField").validate({ 
			//~ rules: { 
					//~ "formfields[]": { 
							//~ required: true, 
							//~ minlength: 1 
					//~ } 
			//~ }, 
			//~ messages: { 
					//~ "formfields[]": "Please select at least One field to send SMS"
			//~ },errorPlacement: function(error, element) {
                        //~ error.appendTo( element.parent().next() );
                //~ }
		//~ }); 
		//~ if(smsBal>0){
			//~ return true;
		//~ }else{
			//~ jAlert("You Don't have enough credit to send SMS please contact Administrator");
			//~ return false;
		//~ }
	//~ });
	$('.sendEmail').live('click',function(event){
		var Emailconfig=parseInt($('#Emailconfig').val());
		$("#sendField").validate({ 
			rules: { 
					"formfields[]": { 
							required: true, 
							minlength: 1 
					} 
			}, 
			messages: { 
					"formfields[]": "Please select at least One field to send Email"
			},errorPlacement: function(error, element) {
                        error.appendTo( element.parent().next() );
                }
		}); 
		if(Emailconfig>0){
			return true;
		}else{
			jAlert("Your Email Configuration is not Complete ,Please contact adminstrator");
			return false;
		}
		
	});
	$('#asto').live('change',function(event){
		$('#enumber').html('');
		var enumber=null;
		$.ajax({
			url: '/Report/Edetail/'+$('#asto').val(),
			type: 'get',
			dataType: 'html',
			 async: false,
			  cache:false,
			success:function(data){
				enumber = data;
			} 
		 });
		$('#enumber').html(enumber);
		
		
	});
	
	
	//~ $('.blkSMs').live('click',function(event){
		//~ var a = new Array();
		//~ var nurl=$('.blkSMs').attr('href');
		//~ var source=$('.blkSMs').attr('rel');
		//~ $("input[class=blk_check]").each(function(){
			//~ if($(this).is(":checked")){
				//~ a.push($(this).val());
			//~ }
		//~ });
		//~ if(a.length>0){
			//~ var s='0';
			//~ var sms_balance=0;
			//~ sms_balance=getSmsBalance();
			//~ if(a.length<=sms_balance){
				//~ event.preventDefault();
				//~ event.stopPropagation();
				//~ $('embed').hide();
				//~ $.get(nurl+"/"+source, function(data){
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
					//~ $('#to').val(a);
					//~ $('#source').val(source);
				//~ });
				//~ $.blockUI({ message: $('#popupDiv') });
				//~ return false;
				//~ 
				//~ 
			//~ }else{
				//~ jAlert("You Don't have sufficient Credit to Send SMS");
				//~ return false;
			//~ }
			//~ 
			//~ return false;
		//~ }else{
			//~ jAlert('Please Select atleast one  to send SMS');
			 //~ return false;
			//~ }
		//~ 
	//~ });	
	$('#temp_id').live('change',function(event){
		var tid=$('#temp_id').val();
		$('#sms_content').val('');
		$.get("Email/smsContent/"+tid, function(data){
			$('#sms_content').val($.trim(data));
		});
	});
	$('#convertlead').live('click',function(event){
		var blk="none";
		if($(this).is(":checked")){
			blk="block";
		}
		$('#grLabel').css('display',blk);
		$('#grempId').css('display',blk);
		$('#assignLabel').css('display',blk);
		$('#assignemp').css('display',blk);
		$('#alertLabel').css('display',blk);
		$('#alerttype').css('display',blk);
		
	});
	$('#updatelead').live('click',function(event){
		var blk="none";
		if($(this).is(":checked")){
			blk="block";
		}
		$('#alertLabel').css('display',blk);
		$('#alerttype').css('display',blk);
		
	});
	$('#convertsuptkt').live('click',function(event){
		var blk="none";
		if($(this).is(":checked")){
			blk="block";
		}
		$('#supgrLabel').css('display',blk);
		$('#supgrId').css('display',blk);
		$('#supassignLabel').css('display',blk);
		$('#supEmpid').css('display',blk);
		$('#suplevelLabel').css('display',blk);
		$('#tkt_level').css('display',blk);
		$('#suptimeLabel').css('display',blk);
		$('#tkt_esc_time').css('display',blk);
		$('#supalertLabel').css('display',blk);
		$('#supalerttype').css('display',blk);
		
	});
	$('#updatesuptkt').live('click',function(event){
		var blk="none";
		if($(this).is(":checked")){
			blk="block";
		}
		$('#supalertLabel').css('display',blk);
		$('#supalerttype').css('display',blk);
	});
	$('.blkemail').live('click',function(event){
		var a = new Array();
		var source=$('.blkemail').attr('rel');
		$("input[type=checkbox]").each(function(){
			if($(this).is(":checked")){
				a.push($(this).val());
			}
		});
		if(a.length>0){
			var s='0';
			n = window.open('/Email/compose/'+s+'/'+source, 'Counter', 'toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=950,height=480,resizable=1');
			return false;
		}else{
			jAlert('Please Select atleast one  to send Email');
			 return false;
			}
		
	});	
	
	
	
});

//$(function() { 
    //$("table.shortable") 
    //.tablesorter({widthFixed: true, widgets: ['zebra']});
    //.tablesorterPager({container: $("#pager")}); 
//}); 

(function( $ ) {
	$.widget( "ui.combobox", {
		_create: function() {
			var self = this,
				select = this.element.hide(),
				selected = select.children( ":selected" ),
				value = selected.val() ? selected.text() : "";
			var input = this.input = $( "<input>" )
				.insertAfter( select )
				.val( value )
				.autocomplete({
					delay: 0,
					minLength: 0,
					source: function( request, response ) {
						var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
						response( select.children( "option" ).map(function() {
							var text = $( this ).text();
							if ( this.value && ( !request.term || matcher.test(text) ) )
								return {
									label: text.replace(
										new RegExp(
											"(?![^&;]+;)(?!<[^<>]*)(" +
											$.ui.autocomplete.escapeRegex(request.term) +
											")(?![^<>]*>)(?![^&;]+;)", "gi"
										), "<strong>$1</strong>" ),
									value: text,
									option: this
								};
						}) );
					},
					select: function( event, ui ) {
						ui.item.option.selected = true;
						self._trigger( "selected", event, {
							item: ui.item.option
						});
					},
					change: function( event, ui ) {
						if ( !ui.item ) {
							var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
								valid = false;
							select.children( "option" ).each(function() {
								if ( $( this ).text().match( matcher ) ) {
									this.selected = valid = true;
									return false;
								}
							});
							if ( !valid ) {
								// remove invalid value, as it didn't match anything
								$( this ).val( "" );
								select.val( "" );
								input.data( "autocomplete" ).term = "";
								return false;
							}
						}
					}
				})
				.addClass( "ui-widget ui-widget-content ui-corner-left" );

			input.data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
					.data( "item.autocomplete", item )
					.append( "<a>" + item.label + "</a>" )
					.appendTo( ul );
			};

			this.button = $( "<button type='button'>&nbsp;</button>" )
				.attr( "tabIndex", -1 )
				.attr( "title", "Show All Items" )
				.insertAfter( input )
				.button({
					icons: {
						primary: "ui-icon-triangle-1-s"
					},
					text: false
				})
				.removeClass( "ui-corner-all" )
				.addClass( "ui-corner-right ui-button-icon" )
				.click(function() {
					// close if already visible
					if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
						input.autocomplete( "close" );
						return;
					}

					// pass empty string as value to search for, displaying all results
					input.autocomplete( "search", "" );
					input.focus();
				});
		},

		destroy: function() {
			this.input.remove();
			this.button.remove();
			this.element.show();
			$.Widget.prototype.destroy.call( this );
		}
	});
})( jQuery );

$(function() {
	$( "select.auto" ).combobox();
});
