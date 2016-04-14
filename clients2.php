<?
//if(!in_array($_SERVER['REMOTE_ADDR'],array('182.72.110.206','180.151.5.37'))) header("Location: /");
//mysql_connect("192.168.75.3","root","581MprugU7!a42");
mysql_connect("localhost","root","root");

mysql_select_db("m3");

$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 2;
$offset = ($page-1)*$rows;

//$_POST['CreditLimit'] = '2000';

$q  = " WHERE 1";
$q .= (isset($_POST['location']) && $_POST['location']!='') ? " AND numberlist.`location` = '%".mysql_real_escape_string($_POST['location'])."%'" : "";
$q .= (isset($_POST['BusinessName']) && $_POST['BusinessName']!='') ? " AND numberlist.`Business Name` like '%".mysql_real_escape_string($_POST['BusinessName'])."%'" : "";
$q .= (isset($_POST['LandingNumber']) && $_POST['LandingNumber']!='') ? " AND numberlist.`Landing Number` like '%".mysql_real_escape_string($_POST['LandingNumber'])."%'" : "";
$q .= (isset($_POST['PRI']) && $_POST['PRI']!='') ? " AND numberlist.`PRI` like '%".mysql_real_escape_string($_POST['PRI'])."%'" : "";
$q .= (isset($_POST['Place']) && $_POST['Place']!='') ? " AND numberlist.`Place` like '%".mysql_real_escape_string($_POST['Place'])."%'" : "";
$q .= (isset($_POST['Rental']) && $_POST['Rental']!='') ? " AND numberlist.`Rental` like '%".mysql_real_escape_string($_POST['Rental'])."%'" : "";
$q .= (isset($_POST['AnnualSalesValue']) && $_POST['AnnualSalesValue']!='') ? " AND numberlist.`Annual Sales Value` like '%".mysql_real_escape_string($_POST['AnnualSalesValue'])."%'" : "";
$q .= (isset($_POST['CreditLimit']) && $_POST['CreditLimit']!='') ? " AND numberlist.`Credit Limit` = '".$_POST['CreditLimit']."'" : "";
$q .= (isset($_POST['Talktime']) && $_POST['Talktime']!='') ? " AND numberlist.`Talktime` like '%".mysql_real_escape_string($_POST['Talktime'])."%'" : "";
$q .= (isset($_POST['SIMOwner']) && $_POST['SIMOwner']!='') ? " AND numberlist.`SIM Owner` like '%".mysql_real_escape_string($_POST['SIMOwner'])."%'" : "";
$q .= (isset($_POST['ExtraUsage']) && $_POST['ExtraUsage']!='') ? " AND numberlist.`Extra Usage` like '%".mysql_real_escape_string($_POST['ExtraUsage'])."%'" : "";
$q .= (isset($_POST['PaymentTerm']) && $_POST['PaymentTerm']!='') ? " AND numberlist.`Payment Term` like '%".mysql_real_escape_string($_POST['PaymentTerm'])."%'" : "";
$q .= (isset($_POST['ActivationDate']) && $_POST['ActivationDate']!='') ? " AND numberlist.`Activation Date` like '%".mysql_real_escape_string($_POST['ActivationDate'])."%'" : "";
$q .= (isset($_POST['ServiceStartDate']) && $_POST['ServiceStartDate']!='') ? " AND numberlist.`Service Start Date` like '%".mysql_real_escape_string($_POST['ServiceStartDate'])."%'" : "";
$q .= (isset($_POST['NextBillingDate']) && $_POST['NextBillingDate']!='') ? " AND numberlist.`Next Billing Date` like '%".mysql_real_escape_string($_POST['NextBillingDate'])."%'" : "";
$q .= (isset($_POST['NextBillingDay']) && $_POST['NextBillingDay']!='') ? " AND numberlist.`Next Billing Day` like '%".mysql_real_escape_string($_POST['NextBillingDay'])."%'" : "";
$q .= (isset($_POST['NextBillingMonth']) && $_POST['NextBillingMonth']!='') ? " AND numberlist.`Next Billing Month` like '%".mysql_real_escape_string($_POST['NextBillingMonth'])."%'" : "";
$q .= (isset($_POST['NextBillingYear']) && $_POST['NextBillingYear']!='') ? " AND numberlist.`Next Billing Year` like '%".mysql_real_escape_string($_POST['NextBillingYear'])."%'" : "";
$q .= (isset($_POST['NextRenewalDate']) && $_POST['NextRenewalDate']!='') ? " AND numberlist.`Next Renewal Date` like '%".mysql_real_escape_string($_POST['NextRenewalDate'])."%'" : "";
$q .= (isset($_POST['NextBillingAmount']) && $_POST['NextBillingAmount']!='') ? " AND numberlist.`Next Billing Amount` like '%".mysql_real_escape_string($_POST['NextBillingAmount'])."%'" : "";
$q .= (isset($_POST['ContactPerson']) && $_POST['ContactPerson']!='') ? " AND numberlist.`Contact Person` like '%".mysql_real_escape_string($_POST['ContactPerson'])."%'" : "";
$q .= (isset($_POST['ContactNumber']) && $_POST['ContactNumber']!='') ? " AND numberlist.`Contact Number` like '%".mysql_real_escape_string($_POST['ContactNumber'])."%'" : "";
$q .= (isset($_POST['ContactEmail']) && $_POST['ContactEmail']!='') ? " AND numberlist.`Contact Email` like '%".mysql_real_escape_string($_POST['ContactEmail'])."%'" : "";
$q .= (isset($_POST['ExecutiveName']) && $_POST['ExecutiveName']!='') ? " AND numberlist.`Executive Name` like '%".mysql_real_escape_string($_POST['ExecutiveName'])."%'" : "";


$sql="
SELECT SQL_CALC_FOUND_ROWS * FROM (
SELECT 
'MCube' as location,
p.bid as Bid,p.businessname as `Business Name`,
p.landingnumber `Landing Number`,
p.pri as `PRI`,
p.region as `Place`,
p.packagename  as `Package`,
p.rental as `Rental`,
(p.rental*12) as `Annual Sales Value`,
p.climit as `Credit Limit`,
p.freelimit as `Talktime`,
p.ownership as `SIM Owner`,
(p.used + COALESCE(n.reset,0)) as `This Month Usage`,
if(((p.used + COALESCE(n.reset,0)) - p.freelimit)>0,((p.used + COALESCE(n.reset,0)) - p.freelimit),0) as `Extra Usage`,
p.payment_term as `Payment Term`,
DATE_FORMAT(p.assigndate,'%d-%m-%Y') as `Activation Date`,
DATE_FORMAT(p.svdate,'%d-%m-%Y') as `Service Start Date`,

DATE_FORMAT(DATE_ADD(p.svdate,INTERVAL CEIL(CEIL(DATEDIFF( CURDATE(),p.svdate)/30)/p.payment_term)*p.payment_term MONTH),'%d-%m-%Y') `Next Billing Date`,
DATE_FORMAT(DATE_ADD(p.svdate,INTERVAL CEIL(CEIL(DATEDIFF( CURDATE(),p.svdate)/30)/p.payment_term)*p.payment_term MONTH),'%d') `Next Billing Day`,
DATE_FORMAT(DATE_ADD(p.svdate,INTERVAL CEIL(CEIL(DATEDIFF( CURDATE(),p.svdate)/30)/p.payment_term)*p.payment_term MONTH),'%M') `Next Billing Month`,
DATE_FORMAT(DATE_ADD(p.svdate,INTERVAL CEIL(CEIL(DATEDIFF( CURDATE(),p.svdate)/30)/p.payment_term)*p.payment_term MONTH),'%Y') `Next Billing Year`,
DATE_FORMAT(DATE_ADD(p.svdate,INTERVAL CEIL(CEIL(DATEDIFF( CURDATE(),p.svdate)/30)/12) YEAR),'%d-%m-%Y') as `Next Renewal Date`,
(p.rental*p.payment_term) as `Next Billing Amount`,

p.contactname `Contact Person`,
p.contactphone `Contact Number`,
p.contactemail as `Contact Email`,
p.executive as `Executive Name`,
if(p.status=1,'Active','Inactive') as `Status`
FROM
(SELECT 
p.number,                       b.businessname,                 p.landingnumber,
b.contactname,					p.payment_term,					p.pri,
l.region,						pp.packagename,         		p.rental,
p.climit,               		p.flimit as freelimit,     		p.ownership,
b.contactphone,					p.used,                         p.assigndate,
p.svdate,               		b.contactemail,					b.status,
p.bid,if(b.relatedto='1',par.firstname,se.empname) as executive
FROM `prinumbers` p
LEFT JOIN business b on p.bid=b.bid
LEFT JOIN package pp on p.package_id=pp.package_id
LEFT JOIN landingnumbers l on p.landingnumber=l.number
LEFT JOIN salesemp se on (b.relatedto='2' AND b.employee=se.id)
LEFT JOIN partner par on (b.relatedto='1' AND b.employee=par.partner_id)
WHERE b.bid is not null
ORDER BY b.bid,p.assigndate) p
LEFT JOIN 
(SELECT number,COALESCE(sum(used),0) as `reset` FROM `number_reset`
WHERE 1 AND DATE_FORMAT(`rdate`,'%Y-%m')=DATE_FORMAT(CURDATE(), '%Y-%m')
GROUP BY number) n on p.number=n.number
ORDER BY p.businessname
) numberlist $q
LIMIT $offset,$rows
";

$rst = mysql_query($sql) or die(mysql_error());

$cnt = mysql_fetch_assoc(mysql_query("SELECT FOUND_ROWS() as cnt"));

$result["total"] = $cnt['cnt'];

$items = array();
if(mysql_num_rows($rst)>0){
	//~ $i = 0;
	//~ echo "<table border=1>";
	//~ while($rec = mysql_fetch_assoc($rst)){
		//~ if($i=='0'){
			//~ echo "<tr style='background-color:FFFFCC;'>";
			//~ foreach($rec as $field => $val)	echo "<th>".$field."</th>";
			//~ echo "</tr>";
		//~ }
		//~ echo "<tr style='background-color:".(($i%2==0) ? '#C9C9BC':'#FFFFFF')."'>";
		//~ foreach($rec as $field => $val)	echo "<td nowrap>".$val."</td>";
		//~ echo "</tr>";
		//~ $i++;
	//~ }
	//~ echo "</table>";
	
	
	while($row = mysql_fetch_object($rst)){
		array_push($items, $row);
	}
}
$result["rows"] = $items;
	
echo json_encode($result);
?>
