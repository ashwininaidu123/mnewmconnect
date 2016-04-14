<?php
	include_once 'open_flash_chart_object.php';
	$url1=site_url('system/application/');
?>
<div id="main-content">
	<div class="page-title"> <i class="icon-custom-left"></i>
		<h4>Call Analytics</h4>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<form action="Report/callanalytics" method="post" name="callanc" id="callanc">
						<table align="center" width="90%" >
							<tr>
								<td align="center">
									Start Date :  <input type="text"  name="stime" id="stime" value="<?=$stime?>" class="datepicker " />	End Date :  <input type="text"  name="etime" id="etime" class="datepicker " value="<?=$etime?>"/> <input type="submit" class="btn btn-primary" value="submit" id="button1" name="submit"/>
								</td>
							</tr>	
							<tr>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td align="center">
									<?php
										open_flash_chart_object( 950, 420, 'Report/callbytime/', false,$url1);
									?>
								</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td align="center">
									<?php
										open_flash_chart_object( 950, 420, 'Report/callbyweek/', false,$url1);
									?>
								</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td align="center">
									<?php
										open_flash_chart_object( 500, 500, 'Report/callbyregion/', false,$url1);
									?>
								</td>
							</tr>
						</table>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
