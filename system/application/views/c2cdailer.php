<script src="system/application/js/application.js"></script>
<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
			<h4><?php echo "Click2 Connect Dailer";?></h4>
		</div>
		<div class="panel-body">
			<div class="row">					
				<div class="col-md-12 col-sm-12 col-xs-12">
					<?php echo $form['open'];?>
					<div class="form-group">
						<label class="col-sm-4 text-right" for="'.$field['fieldname'].'"><i>Enter your number without a </br>preceding 0 or country code.</i></label>
						 <div class="col-sm-8 input-icon right">
						   <input type="text" name="code" id="code" readonly value="+91" style="width:40px;" />
						   <input type="text" name="number" id="number" value="" maxlength="10" style="padding:5px;width:200px;" class="required" />
						   <span id="error" style='color:#CC0000;font-size:10px;'></span>
						 </div>
					</div><br/><br/><br/>
					<div class="form-group text-center">
						<input id="callme" type="submit" class="btn btn-primary" name="callme" value="Call Me" /> 
					 </div>
					<?php echo $form['close'];?>
				</div>
			</div>
		</div>
	</div>
</div>	
