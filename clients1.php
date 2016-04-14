<?
//if(!in_array($_SERVER['REMOTE_ADDR'],array('182.72.110.206','180.151.5.37'))) header("Location: /");
mysql_connect("192.168.75.3","root","581MprugU7!a42");
//mysql_connect("localhost","root","root");

mysql_select_db("m3");

$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
$offset = ($page-1)*$rows;


$sql="
SELECT SQL_CALC_FOUND_ROWS * FROM (
SELECT 
'MCube' as location,
p.bid as Bid,p.businessname as `BusinessName`,
p.landingnumber `LandingNumber`,
p.pri as `PRI`,
p.region as `Place`,
p.packagename  as `Package`,
p.rental as `Rental`,
(p.rental*12) as `AnnualSalesValue`,
p.climit as `CreditLimit`,
p.freelimit as `Talktime`,
p.ownership as `SIMOwner`,
(p.used + COALESCE(n.reset,0)) as `ThisMonthUsage`,
if(((p.used + COALESCE(n.reset,0)) - p.freelimit)>0,((p.used + COALESCE(n.reset,0)) - p.freelimit),0) as `ExtraUsage`,
p.payment_term as `PaymentTerm`,
DATE_FORMAT(p.assigndate,'%d-%m-%Y') as `ActivationDate`,
DATE_FORMAT(p.svdate,'%d-%m-%Y') as `ServiceStartDate`,

DATE_FORMAT(DATE_ADD(p.svdate,INTERVAL CEIL(CEIL(DATEDIFF( CURDATE(),p.svdate)/30)/p.payment_term)*p.payment_term MONTH),'%d-%m-%Y') `NextBillingDate`,
DATE_FORMAT(DATE_ADD(p.svdate,INTERVAL CEIL(CEIL(DATEDIFF( CURDATE(),p.svdate)/30)/p.payment_term)*p.payment_term MONTH),'%d') `NextBillingDay`,
DATE_FORMAT(DATE_ADD(p.svdate,INTERVAL CEIL(CEIL(DATEDIFF( CURDATE(),p.svdate)/30)/p.payment_term)*p.payment_term MONTH),'%M') `NextBillingMonth`,
DATE_FORMAT(DATE_ADD(p.svdate,INTERVAL CEIL(CEIL(DATEDIFF( CURDATE(),p.svdate)/30)/p.payment_term)*p.payment_term MONTH),'%Y') `NextBillingYear`,
DATE_FORMAT(DATE_ADD(p.svdate,INTERVAL CEIL(CEIL(DATEDIFF( CURDATE(),p.svdate)/30)/12) YEAR),'%d-%m-%Y') as `NextRenewalDate`,
(p.rental*p.payment_term) as `NextBillingAmount`,

p.contactname `ContactPerson`,
p.contactphone `ContactNumber`,
p.contactemail as `ContactEmail`,
p.executive as `ExecutiveName`,
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
) numberlist 
";

$rst = mysql_query($sql) or die(mysql_error());

$cnt = mysql_fetch_assoc(mysql_query("SELECT FOUND_ROWS() as cnt"));

$result["total"] = $cnt['cnt'];

$items = array();
if(mysql_num_rows($rst)>0){
	while($row = mysql_fetch_object($rst)){
		array_push($items, $row);
	}
}

echo json_encode($items);
?>
