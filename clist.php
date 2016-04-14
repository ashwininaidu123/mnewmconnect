<?
if(!in_array($_SERVER['REMOTE_ADDR'],array('182.72.110.206'))) header("Location: /");
mysql_connect("192.168.75.3","root","581MprugU7!a42");

mysql_select_db("m3");
$sql="SELECT b.businessname,p.landingnumber FROM 
prinumbers p
LEFT JOIN `business` b on p.bid=b.bid
WHERE b.bid is not null
ORDER BY b.bid";

$rst = mysql_query($sql) or die(mysql_error());
if(mysql_num_rows($rst)>0){
	$i = 0;
	echo "<table border=1>";
	while($rec = mysql_fetch_assoc($rst)){
		if($i=='0'){
			echo "<tr style='background:FFFFCC;'>";
			foreach($rec as $field => $val)	echo "<th>".$field."</th>";
			echo "</tr>";
		}
		echo "<tr style='background:".(($i%2==0) ? '#C9C9BC':'#FFFFFF')."'>";
		foreach($rec as $field => $val)	echo "<td nowrap>".$val."</td>";
		echo "</tr>";
		$i++;
	}
	echo "</table>";
}
?>
<style type="text/css">tr:hover{background:FFCCCC;}</style>
