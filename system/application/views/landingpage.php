

<div class="pagination"><? echo $paging;?></div>
	<? if(isset($tab) && $tab==true){?>
<div class="stabs">
	<span><a href="<?=$form['search_url']?>all">All</a></span>
	<? if(isset($tab1) && $tab1==true){?>
	<span><a href="<?=$form['search_url']?>basic">Basic</a></span>
	<span><a href="<?=$form['search_url']?>contact">Contact</a></span>
	<?php } ?>
<?php if(isset($form['search_names']) && sizeof($form['search_names'])>0){?>	
	<?php
		foreach($form['search_names'] as $s_name){
			echo "<span><a href='".$form['search_url'].$s_name['search_id']."'>".$s_name['search_name']."</a><a href='Report/del_search/".$s_name['search_id']."/".str_replace("/","~",$form['search_url'].'all')."' class='confirm tclose'>X</a></span>";
		}
	?>
<?php } ?>
</div>
<?php } ?>
<div class="listTable" id="makeMeScrollable">
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
</div>
<div class="pagination"><? echo $paging;?></div>
</div>


