<?if(isset($file) && file_exists($file))require_once($file);?>
 <div id="main-content">
            <div class="page-title"><i class="icon-custom-left"></i>
              <h3><?
	$js = 'id="parentbid" ';
	echo $module['title'];
	if(isset($form['parentids']) && sizeof($form['parentids'])>1) { 
		echo '&nbsp;&nbsp;&nbsp;'.form_dropdown("parentbid",$form['parentids'],$form['busid'],$js);
	}
	?></h3>
            </div>
           <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
							<div class="row" >
					           <div class="col-md-12 col-sm-12 col-xs-12">

<?=$form['open']?>

<div class="sterror"><?php echo validation_errors(); ?></div>
<table id="FromTab">
<?	
	if(is_array($form['fields']))
	foreach($form['fields'] as $field){ ?>
	<tr>
		 <div class="form-group">
		<?=isset($field['label'])?$field['label']:''?>
		<div class="col-sm-6 input-icon right">
		<?=isset($field['field'])?$field['field']:''?>
		  </div>
    </div>
	</tr>
<? }?>
<? if(isset($form['fields1'])){
	foreach($form['fields1'] as $field){?>
	<tr class='appended_rows'>
		 <div class="form-group">
		<?=$field['label']?>
			<div class="col-sm-6 input-icon right">
	    <?=$field['field']?>
	    	  </div>
    </div>
	</tr>
<? 
	}
} 	?>

</table>

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
 <div class="form-group col-sm-12 text-center">
				   <input id="button1" type="submit" class="btn btn-primary" name="update_system" value="<?=$this->lang->line('submit') ?>" /> 
                   <input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
              </div>
	
<? } ?>
<?=$form['close']?>

</div>
<?php if(isset($file) && file_exists($file))require_once($file);?>
<link rel="stylesheet" type="text/css" href="<?=site_url('system/application/css/jquery.multiselect.css')?>"/>
<link rel="stylesheet" type="text/css" href="<?=site_url('system/application/css/jquery.multiselect.filter.css')?>" />

<script type="text/javascript" src="<?=site_url('system/application/js/ui/jquery.multiselect.js')?>"></script>
<script type="text/javascript" src="<?=site_url('system/application/js/ui/jquery.multiselect.filter.js')?>"></script>

<style>
ul.hlist{
	width:100%;
	list-style:none;
}
ul.hlist li.item{
	float:left;
	padding-left:20px;
}
a.remRow,a.add_more_adv{
	color:#CC0000;
	text-decoration:underline;
}
</style>  

  
<div id="box">
<h3><?

	$js = 'id="parentbid" ';
	echo $module['title'];
	if(isset($form['parentids']) && sizeof($form['parentids'])>1) { 
		echo '&nbsp;&nbsp;&nbsp;'.form_dropdown("parentbid",$form['parentids'],$form['busid'],$js);
	}
	?>
<span>
<input type='hidden' name="mod_Id" id="mod_Id" value="<?=sizeof($form['adv_search'])?>"/>
<input type='hidden' name="fsizec" id="fsizec" value=""/>
<?php echo $links;?>
<? if(!isset($nosearch)){?>
<a id="SearchButton"><span title="Search" class="glyphicon glyphicon-search"></span></a>
<? }?>
</span>
</h3>

<? if(!isset($nosearch)){?><br>
<script type="text/javascript"> $(function() { $( "#searchTabs" ).tabs();}); </script>
<div class="searchBox" >
<div id="searchTabs" >
<ul>
	<li><a href="#normal_search">Search</a></li>
	<?php if(!empty($form['adv_search'])) { ?> <li><a href="#advance_search">Advance Search</a></li><? }?>
</ul>

<div id="normal_search">
<?=$form['open']?>
<table align ="center" id="nor_search">
	<?php
	foreach($form['form_field'] as $field1){?>
		<tr>
			<th><?=$field1['label']?></th>
			<td><?=$field1['field']?></td>
			<td></td>
		</tr>
	<? }?>
	<tr>
		<td colspan="3"><center><input id="button1" type="submit" name="submit" value="<?=$this->lang->line('level_search')?>" /></center></td>
	</tr>
</table>
<?=$form['close']?>
	
</div>
<?php if(!empty($form['adv_search'])) { ?>
<div id="advance_search">
<?=$form['open']?>
<table align ="center" id="adv_search">
	<tr>
		<td colspan="4">
			<ul class="hlist">
				<li class="item"><input type="radio" name="timespan" value="all" checked/>All</li>
				<li class="item"><input type="radio" name="timespan" value="today"/>Today</li>
				<li class="item"><input type="radio" name="timespan" value="last7"/>Last 7 Days</li>
				<li class="item"><input type="radio" name="timespan" value="month"/>This Month</li>
			</ul>
		</td>
	</tr>
	
	<tr>
	<td colspan="4">
			<ul class="hlist">
				<li class="item"><label>Groups : </label></li>
				<li class="item">
					<select multiple='multiple' class="muliselect multilist" id="gid[]">
					<?
					//	print_r($codelist);
						foreach($form['groups'] as $val=>$opt){
							$option = "<option value='$val'";
							$option .= ">$opt</option>";
							echo $option;
						}
					?>
					</select> 
				</li>
				<li class="item"><label>Employees : </label></li>
				<li class="item">
					<select multiple='multiple' class="muliselect multilist" id="eids[]" name="eids[]">
					<?
					//	print_r($codelist);
						foreach($form['employees'] as $val=>$opt){
							$option = "<option value='$val'";
							$option .= ">$opt</option>";
							echo $option;
						}
					?>
					</select> 
				</li>

	</td>
	</tr>
	
	<tr id="addmore">
		<td colspan="4"><a href="javascript:void(0)" class="add_more_adv">Add More</a></td>
	</tr>
	<tr id="searchF">
		<td>Field :
			<select name="field_d[]" id="field_d">
		<?php
			foreach($form['adv_search'] as $field=>$field1){?>
				<option value="<?=$field?>"><?=$field1?></option>
		<? }?>
		</select>
		</td>
		<td>
			<select style="width:150px;" name='equ[]' id='equ[]'>
				<option value="1">Like</option>
				<option value="2">Not Like</option>
				<option value="3"> = </option>
				<option value="4"> != </option>
				<option value="5"> > </option>
				<option value="6"> < </option>
				<option value="7"> >= </option>
				<option value="8"> <= </option>
		
			</select>
		</td>
		<td>Value : <input type="text" name="fval[]" id="fval[]" value=""/> </td>
		<td>Condition :
			<select style="width:150px;" name='cond[]' id='cond[]'>
				<option value="and">AND</option>
				<option value="or">OR</option>
				<option value="not">NOT</option>
				<option value="equal">EQUAL</option>
		
			</select>
		</td>
	</tr>
	
	<tr id="sbtn">
		<td  colspan="4"><center>
<!--
			<input id="button1" type="radio" 
		name="Adv_submit" checked value="<?=$this->lang->line('level_search')?>" 
		/><?=$this->lang->line('level_search')?>
-->
		<? if($form['save_search']<3) { ?>
		<input id="button1" type="checkbox" name="sav_search" 
		value="1" /> <?php } ?>Save Search
		<input type="text" name="searchname" maxlength="12" id="searchname" value="search<?=$form['save_search']+1?>"/>
		</center></td>
	</tr>
	<tr>
	  <td colspan="4"><center>
	  <input id="button1" type="submit" 
		name="Adv_submit" value="<?=$this->lang->line('level_search')?>" 
		/>
	  </center></td>
	
	
	</tr>
</table>
<?=$form['close']?>
</div>
<? } ?>

</div>
</div>
<? }?>

<div class="pagination"><? echo $paging;?></div>
	<? if(isset($tab) && $tab==true){?>
<div class="stabs">
	<span><a href="<?=$form['search_url']?>all">All</a></span>
	<span><a href="<?=$form['search_url']?>basic">Basic</a></span>
	<span><a href="<?=$form['search_url']?>contact">Contact</a></span>
<?php if(isset($form['search_names']) && sizeof($form['search_names'])>0){?>	
	<?php
		foreach($form['search_names'] as $s_name){
			echo "<span><a href='".$form['search_url'].$s_name['search_id']."'>".$s_name['search_name']."</a><a href='Report/del_search/".$s_name['search_id']."/".str_replace("/","~",$form['search_url'].'all')."' class='confirm tclose'>X</a></span>";
		}
	?>
<?php } ?>
</div>
<?php } ?>

<table class="shortable" id="myTable">
<thead>
	<tr>
<? foreach($itemlist['header'] as $hd){ ?>
		<th><?=$hd?></th>
<? }?>
	</tr>
</thead><tbody>
<?php
$i=0;
foreach($itemlist['rec'] as $item){ ?>
	<tr class="<?=($i%2==0)?'even':''?>">
	<? foreach($item as $it){?><td><?=$it?></td><? }?>
	</tr>
<? $i++;}?>
</tbody>
</table>
<div class="pagination"><? echo $paging;?></div>
</div>


