<? //if(!in_array($_SERVER['REMOTE_ADDR'],array('182.72.110.206','180.151.5.37'))) header("Location: /");?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Numbers List</title>
	<link rel="stylesheet" type="text/css" href="easyui.css">
	<script type="text/javascript" src="jquery-1.4.4.min.js"></script>
	<script type="text/javascript" src="jquery.easyui.min.js"></script>
	<script type="text/javascript">
		//~ $(function() {
			//~ $('input[type=text]').keyup(function() {
			  //~ doSearch();
			//~ });
		//~ });
		
		function doSearch(){
			$('#tt').datagrid('load',{
				location: $('#location').val()
				,BusinessName: $('#BusinessName').val()
				,LandingNumber: $('#LandingNumber').val()
				,PRI: $('#PRI').val()
				,Place: $('#Place').val()
				,Rental: $('#Rental').val()
				,AnnualSalesValue: $('#AnnualSalesValue').val()
				,CreditLimit: $('#CreditLimit').val()
				,Talktime: $('#Talktime').val()
				,SIMOwner: $('#SIMOwner').val()
				,ThisMonthUsage: $('#ThisMonthUsage').val()
				,ExtraUsage: $('#ExtraUsage').val()
				,PaymentTerm: $('#PaymentTerm').val()
				,ActivationDate: $('#ActivationDate').val()
				,ServiceStartDate: $('#ServiceStartDate').val()
				,NextBillingDate: $('#NextBillingDate').val()
				,NextBillingDay: $('#NextBillingDay').val()
				,NextBillingMonth: $('#NextBillingMonth').val()
				,NextBillingYear: $('#NextBillingYear').val()
				,NextRenewalDate: $('#NextRenewalDate').val()
				,NextBillingAmount: $('#NextBillingAmount').val()
				,ContactPerson: $('#ContactPerson').val()
				,ContactNumber: $('#ContactNumber').val()
				,ContactEmail: $('#ContactEmail').val()
				,ExecutiveName: $('#ExecutiveName').val()
			});
		}
	</script>
</head>
<body>
	<table id="tt" class="easyui-datagrid" style="width:1300px;height:620px;"
			url="clients2.php"
			title="Numbers List" iconCls="icon-search" toolbar="#tb"
			rownumbers="true" pagination="true">
		<thead>
			<tr>
				<th field="location">Location</th>
				<th field="Bid" sortable="true">Bid</th>
				<th field="BusinessName">Business Name</th>
				<th field="LandingNumber">Landing Number</th>
				<th field="PRI">PRI</th>
				<th field="Place">Reion</th>
				<th field="Rental">Rental</th>
				<th field="AnnualSalesValue">Annual Sales Value</th>
				<th field="CreditLimit">Credit Limit</th>
				<th field="Talktime">Talktime</th>
				<th field="SIMOwner">SIM Owner</th>
				<th field="ThisMonthUsage">This Month Usage</th>
				<th field="ExtraUsage">Extra Usage</th>
				<th field="PaymentTerm">Payment Term(Months)</th>
				<th field="ActivationDate">Activation Date</th>
				<th field="ServiceStartDate">Service Start Date</th>
				<th field="NextBillingDate">Next Billing Date</th>
				<th field="NextBillingDay">Next Billing Day</th>
				<th field="NextBillingMonth">Next Billing Month</th>
				<th field="NextBillingYear">Next Billing Year</th>
				<th field="NextRenewalDate">Next Renewal Date</th>
				<th field="NextBillingAmount">Next Billing Amount(Rental)</th>
				<th field="ContactPerson">Contact Person</th>
				<th field="ContactNumber">Contact Number</th>
				<th field="ContactEmail">Contact Email</th>
				<th field="ExecutiveName">Executive Name</th>
				<th field="Status">Status</th>
			</tr>
		</thead>
	</table>
	<div id="tb" style="padding:3px">
		<table  style="width:1180px;">
		<tr>
			<th>Business Name:</th><td><input type="text" id="BusinessName"></td>
			<th>Landing Number:</th><td><input type="text" id="LandingNumber"></td>
			<th>PRI:</th><td><input type="text" id="PRI"></td>
		</tr>
		<tr>
			<th>Reion:</th><td><input type="text" id="Place"></td>
			<th>Rental:</th><td><input type="text" id="Rental"></td>
			<th>Annual Sales Value:</th><td><input type="text" id="AnnualSalesValue"></td>
		</tr>
		<tr>
			<th>Credit Limit:</th><td><input type="text" id="CreditLimit"></td>
			<th>Talktime:</th><td><input type="text" id="Talktime"></td>
			<th>SIM Owner:</th><td><input type="text" id="SIMOwner"></td>
		</tr>
		<tr>
			<th>This Month Usage:</th><td><input type="text" id="ThisMonthUsage"></td>
			<th>Extra Usage:</th><td><input type="text" id="ExtraUsage"></td>
			<th>Payment Term(Months):</th><td><input type="text" id="PaymentTerm"></td>
		</tr>
		<tr>
			
			<th>Activation Date:</th><td><input type="text" id="ActivationDate"></td>
			<th>Service Start Date:</th><td><input type="text" id="ServiceStartDate"></td>
			<th>Next Billing Date:</th><td><input type="text" id="NextBillingDate"></td>
		</tr>
		<tr>
			<th>Next Billing Day:</th><td><input type="text" id="NextBillingDay"></td>
			<th>Next Billing Month:</th><td><input type="text" id="NextBillingMonth"></td>
			<th>Next Billing Year:</th><td><input type="text" id="NextBillingYear"></td>
		</tr>
		<tr>
			<th>Next Renewal Date:</th><td><input type="text" id="NextRenewalDate"></td>
			<th>Next Billing Amount(Rental):</th><td><input type="text" id="NextBillingAmount"></td>
			<th>Contact Person:</th><td><input type="text" id="ContactPerson"></td>
		</tr>
		<tr>
			<th>Contact Number:</th><td><input type="text" id="ContactNumber"></td>
			<th>Contact Email:</th><td><input type="text" id="ContactEmail"></td>
			<th>ExecutiveName:</th><td><input type="text" id="ExecutiveName"></td>
		</tr>
		<tr>
			<th>Location:</th><td><input type="text" id="location"></td>
		</tr>
		<tr><td colspan="6" align="center"><a href="#" class="easyui-linkbutton" plain="true" onclick="doSearch()">Search</a></td></tr>
		</table>
		
	</div>
</body>
</html>



<style type="text/css">
	
*{
	font-size:12px;
}
body {
	font-family:verdana,helvetica,arial,sans-serif;
	padding:10px;
	font-size:12px;
	margin:0;
}
th{
	text-align:right;
}
.easyui-linkbutton{
	font-weight:bold;
	background-color:#E0ECFF;
}
</style>
