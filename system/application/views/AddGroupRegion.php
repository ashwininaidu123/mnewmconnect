<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" href="system/application/css/jquery.multiselect.css" />
<link rel="stylesheet" type="text/css" href="system/application/css/jquery.multiselect.filter.css" />
<script type="text/javascript" src="system/application/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="system/application/js/jquery.multiselect.filter.js"></script>


 <div id="main-content">
            <div class="page-title"> <i class="icon-custom-left"></i>
                <h4>	
			<?php
			if(!empty($groupregions)){
				echo $this->lang->line('level_Edit_groupregion'); 
				$action="customfield/AddGroup_Region/".$groupregions[0]['regionid']."/".$groupregions[0]['gregionid'];
			}else{ 
				echo $this->lang->line('label_addgroupregion'); 
				$action='customfield/AddGroup_Region/'.$rid;
			}?>
		
                </h4>
            </div>
           <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
							<div class="row" >
					           <div class="col-md-12 col-sm-12 col-xs-12">
								   
			<?php
			$attributes = array('class' => 'form-horizontal form icon-validation', 'id' =>'groupregion','name'=>'region');		
			 echo form_open($action,$attributes);
			
			?>
				<div class="form-group">
				<label class="col-sm-4 text-right">
					<?php echo $this->lang->line('level_groupregionname');?>:</label>
					<div class="col-sm-6 input-icon right">
					<?php
							$data = array(
									'name'        => 'regionname',
									'id'          => 'regionname',
									'value'       => ((!empty($groupregions))?$groupregions[0]['regionname']:''),
									'class'       => 'required alphanumeric form-control',
									);
							echo form_input($data)." <img src='system/application/img/icons/help.png' title='Sub Region Name based on telecom circle or std code.'>";?>
					
					</div>
			</div>
				<div class="form-group">
				<label class="col-sm-4 text-right">
					<?php echo $this->lang->line('level_codename');?>:</label>
				   <div class="col-sm-6 input-icon right">
					   
						<select multiple='multiple' class="muliselect form-control" id="codes[]" name="multiselect_codes[]">
						<?
						//	print_r($codelist);
							foreach($codes as $val=>$opt){
								$option = "<option value='$val'";
								$option .= (in_array($val,$codelist))? ' selected="selected" ':"";
								$option .= ">$opt</option>";
								echo $option;
							}
						?>
						</select> <img src='system/application/img/icons/help.png' title='Select one or more telecom cirlces and or std codes that would identify this subregion based on incoming call.'>
						<?php
							//echo form_dropdown("",$codes,$groupregions,"id='first' class='muliselect'  multiple='multiple'");
						?>
				</div>
			</div>
          	 <div class="form-group col-sm-12 text-center">
				   <input id="button1" type="submit" class="btn btn-primary" name="update_system" value="<?=$this->lang->line('submit') ?>" /> 
                   <input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
              </div>
			<?php echo form_close();?>
		</div>

<script type="text/javascript">
$("select").multiselect();
</script>
<script type="text/javascript">
$("select").multiselectfilter();
</script>
