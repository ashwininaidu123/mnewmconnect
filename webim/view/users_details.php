<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * All rights reserved. The contents of this file are subject to the terms of
 * the Eclipse Public License v1.0 which accompanies this distribution, and
 * is available at http://www.eclipse.org/legal/epl-v10.html
 * 
 * Alternatively, the contents of this file may be used under the terms of
 * the GNU General Public License Version 2 or later (the "GPL"), in which case
 * the provisions of the GPL are applicable instead of those above. If you wish
 * to allow use of your version of this file only under the terms of the GPL, and
 * not to allow others to use your version of this file under the terms of the
 * EPL, indicate your decision by deleting the provisions above and replace them
 * with the notice and other provisions required by the GPL.
 * 
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

require_once("inc_menu.php");
$page['title'] = getlocal("clients.title");
$page['menuid'] = "users";

function tpl_header() { global $page, $webimroot, $jsver;
?>	
<script type="text/javascript" language="javascript" src="<?php echo $webimroot ?>/js/<?php echo $jsver ?>/common.js"></script>
<script type="text/javascript" language="javascript"><!--
var localized = new Array(
    "<?php echo getlocal("pending.table.speak") ?>",
    "<?php echo getlocal("pending.table.view") ?>",
    "<?php echo getlocal("pending.table.ban") ?>",
    "<?php echo htmlspecialchars(getlocal("pending.menu.show")) ?>",
    "<?php echo htmlspecialchars(getlocal("pending.menu.hide")) ?>",
    "<?php echo htmlspecialchars(getlocal("pending.popup_notification")) ?>"
);
var updaterOptions = {
	url:"<?php echo $webimroot ?>/operator/update.php",wroot:"<?php echo $webimroot ?>",
	agentservl:"<?php echo $webimroot ?>/operator/agent.php", frequency:<?php echo $page['frequency'] ?>, istatus:<?php echo $page['istatus'] ?>,  
	noclients:"<?php echo getlocal("clients.no_clients") ?>", havemenu: <?php echo $page['havemenu'] ?>, showpopup: <?php echo $page['showpopup'] ?>, showonline: <?php echo $page['showonline'] ?> };
//--></script>
<script type="text/javascript" language="javascript" src="<?php echo $webimroot ?>/js/<?php echo $jsver ?>/users.js"></script>
<?php
}

function tpl_content() { global $page, $webimroot,$visitors_details;
?>

<div>
<div id="togglediv">
<a href="#" id="togglemenu"></a>
</div>
<?php echo getlocal("clients.visitor.desc") ?>
</div>
<br/>

<table class="details" border="0">
<thead>
<tr>
	<th class="first"><?php echo getlocal("pending.table.head.name") ?></th>
	<th><?php echo getlocal("pending.table.head.company") ?></th>
    <th><?php echo getlocal("pending.table.head.phone") ?></th>
    <th><?php echo getlocal("pending.table.head.email") ?></th>
</tr>
</thead>
<tbody>
<?php
	for($i=0;$i<count($visitors_details);$i++){
		if($visitors_details[$i]['userName'] != ''){
			echo "<tr><td class='visitor'>".$visitors_details[$i]['userName']."</td><td class='visitor'>".$visitors_details[$i]['company']."</td>";
			echo "<td class='visitor'>".$visitors_details[$i]['phone']."</td><td class='visitor'>".$visitors_details[$i]['email']."</td><tr/>";
	    }
	}
?>
</tbody>
</table>

<?php 
} /* content */

require_once('inc_main.php');
?>
