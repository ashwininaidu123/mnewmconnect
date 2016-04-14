<?if(isset($file) && file_exists($file))require_once($file);?>
<script type="text/javascript">

var t = 0;
var IE = navigator.appName;
var OP = navigator.userAgent.indexOf('Opera');
var tmp = '';

function operaFix() {

   if (OP != -1) {
      document.getElementById('browser').style.left = -120 + 'px';
   }

}


function startBrowse() {

   tmp = document.getElementById('	').value;
   getFile();

}

function getFile() {

   // IF Netscape or Opera is used...
   //////////////////////////////////////////////////////////////////////////////////////////////
   if (OP != -1) {

   displayPath();

      if (tmp != document.getElementById('dummy_path').value && document.getElementById('dummy_path').value 

!= '') {

         clearTimeout(0);
         return;

      }

   setTimeout("getFile()", 20);

   // If IE is used...
   //////////////////////////////////////////////////////////////////////////////////////////////
   } else if (IE == "Microsoft Internet Explorer") {

      if (t == 3) {

         displayPath();

         clearTimeout(0);
         t = 0;
         return;

      }

   t++;
   setTimeout("getFile()", 20);


   // Or if some other, better browser is used... like Firefox for example :)
   //////////////////////////////////////////////////////////////////////////////////////////////
   } else {

      displayPath();

   }

}


function displayPath() {

   document.getElementById('dummy_path').value = document.getElementById('browser').value;

}

</script>



<style type="text/css">

#browser
   {
   position: absolute;
   left: -132px;
   opacity: 0;
   filter: alpha(opacity=0);
   cursor:pointer;
   }

#browser_box
   {
   width: 104px;
   height: 22px;
   position: relative;
   overflow: hidden;
   cursor:pointer;
   background: url(system/application/img/icons/browse.png) no-repeat;
   }

#browser_box:active
   {
	   cursor:pointer;
   background: url(system/application/img/icons/browse.png) no-repeat;
   }

#dummy_path
   {
   width: 350px;
   font-family: verdana;
   font-size: 10px;
   font-style: italic;
   color: #3a3c48;
   border: 1px solid #3a3c48;
   padding-left: 2px;
   cursor:pointer;
   background: #dcdce0;
   }

</style>


<script language="javascript" type="text/javascript">
	 $(document).ready(function(){
		 $("#FrmError").html('');
		 $('#configure').validate({
			errorPlacement: function(error, element) {
						//error.appendTo( element.parent().parent().parent() );
						$("#FrmError").append(error);
						$("#FrmError").append('<br/>');
						//$('#FromTab').append();

					}
		});	
		 $('#addmore').live('click',function(event){
			var i = $("#FromTab tr").length;
			$("#FromTab").append('<tr>'
						+'<td valign="top">&nbsp;<input name="cust['+i+'][lname]" placeholder="Label Name" class="required" style="width:100px;" type="text"></td>'
						+'<td valign="top"><select name="cust['+i+'][ftype]"><option value="">Select</option><option value="number">Numeric</option><option value="alphanumeric">Alpha Numeric</option><option value="alpha">Alpha Only</option></select> </td>'
						+'<td valign="top"><input type="checkbox" name="cust['+i+'][isreq]" value="1"/></th>'
						+'<td valign="top">&nbsp;<input name="cust['+i+'][order]" class="required" style="width:20px;" type="text"></td>'
						+'<th valign="top"><div id="browser_box"><input name="cust['+i+'][fname]" placeholder="Label Name" class="required" " id="browser"  onclick="startBrowse()" type="file"></div></td>'
						+'<td valign="top"><a href="javascript:void(null)" class="DelRow"><span title="Delete" class="glyphicon glyphicon-trash" id="delete"></span></a></td></tr>');		
		});
			
		$('.DelRow').live('click',function(event){
			$(this).parent().parent().remove();
		});
		 
		 
	 });
	
</script>	
<div id="box">
<h3><?
	$js = 'id="parentbid" ';
	echo "Add Acitivity";
	if(isset($form['parentids']) && sizeof($form['parentids'])>1) { 
		echo '&nbsp;&nbsp;&nbsp;'.form_dropdown("parentbid",$form['parentids'],$form['busid'],$js);
	}
	?></h3>
<?=$form['open']?>
<div id="FrmError"><?php echo validation_errors(); ?></div>
<? if(!isset($noshow)){?>
<table id="FromTab1" style="border:1px solid #000000;">
	<tr>
		<th><label>Activity Name :</label></th>
		<td><input type="text" name="acname" id="acname" class="required"/></td>
	</tr>
	<tr>
		<th><label>Keyword :</label></th>
		<td><input type="text" name="kw" id="kw" class="required"/></td>
	</tr>
</table>
<?php } ?>
<fieldset>
<legend><?=$module['title']?></legend>



<table id="FromTab" style="border:1px solid #000000;">
	<thead>
		<tr>
			<th>Label Name</th>
			<th>Field Type</th>
			<th>Is Required</th>
			<th>Order</th>
			<th>File</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><? echo form_input(array(
									  'name'        => 'cust[0][lname]',
										'placeholder'       => 'Label Name',
										'value'       => '',
										'class'		=>'required',
				  			) ,'',"style='width:100px;'"
				  		) ?></td>
			<td><?=form_dropdown('cust[0][ftype]',$fieldtype,'',' class="required",style="width:50px;"')?></td>
			<td><?=form_checkbox(array(
									  'name'        => 'cust[0][isreq]',
										'placeholder'       => 'Is Required',
										'value'=>'1'
							) ,'',"style='width:50px;'"
				  		)?></td>
			<td><? echo form_input(array(
									  'name'        => 'cust[0][order]',
										'value'       => '',
										'class'		=>'required',
				  			) ,'',"style='width:20px;'"
				  		) ?></td>	  		
			<td><div id="browser_box">
            
               <? echo form_input(array(
									  'name'        => 'cust[0][fname]',
										'value'       => '',
										'class'		=>'required',
										'type'=>'file'
				  		) ,''," id='browser' style='width:50px; onclick='startBrowse()'") ?>
            
        </div></td>
			<td><a href="javascript:void(0)"class='addCust'><span title="Add" id="addmore" class="glyphicon glyphicon-plus-sign"></span></a></td>
		</tr>
		
	</tbody>
	
</table>
</fieldset>
<?php 
/*if(isset($form['clone'])){
	if($form['clone']!=0){
		?>
		<input type="hidden" name="clone" id="clone" value="1" />
		<?php
	}
}*/
?>
<? if(!isset($form['submit'])){ ?>	
<table><tr><td><center>
<input id="button1" type="submit" class="btn btn-primary" name="update_system" value="<?=$this->lang->line('submit')?>" /> 
<input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
</center></td></tr></table>
<? } ?>
<?=$form['close']?>

</div>
