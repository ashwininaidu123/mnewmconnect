<? if(isset($file) && file_exists($file))require_once($file);?>
<div id="box">
<h3><?=$module['title']?></h3>
<?=$form['open']?>
<fieldset>
<legend><?=$module['title']?></legend>
<div class="sterror"><?php echo validation_errors(); ?></div>
<table>
	<tr>
		<td><label>Poll Type:</label> </td>
		<td align="left">
			<input type="radio" name="polltype" id="polltype" value="1" checked/>Single Number
			<input type="radio" name="polltype" id="polltype" value="2"/>Multi Number Number
		</td>
		<td></td>
	</tr>
</table>

<table><tr><td><center>
<input id="button1" type="submit" class="btn btn-primary" name="update_system" value="next" /> 
<input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
</center></td></tr></table>
<?=$form['close']?>
</div>
