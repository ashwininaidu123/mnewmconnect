<div id="box">
<?=$form['open']?>
<fieldset>
<legend><?=$module['title']?></legend>
<div class="col-md-12 col-sm-12 col-xs-12">
<?php foreach($form['fields'] as $field1){ ?>
	<div class="form-group">
	   <?=$field1['label']?>
		 <div class="col-sm-6 input-icon right"> 
		  <?=$field1['field']?>
		</div>
	</div>
<? }?>
  <div class="form-group col-sm-12" style="float:left" >
	<button type="submit" class="btn btn-primary" id="button1" name="update_system" data-dismiss="modal">Submit</button> &nbsp;&nbsp;<button type="reset" class="btn btn-default" id="button2" data-dismiss="modal">Reset</button>
	</div>
</div>

</fieldset>
</div>
<?=$form['close']?>
</div>
