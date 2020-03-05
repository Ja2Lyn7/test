<?php
//Admin URL or FAIL url or continues in case on regular login
verifyLogin();

if($sessionAdmin <> 1){
	$destination = "location:https://www.maxxpayments.com/login.php?errmsg=You are not authorized to access this site.";
    header($destination);
	exit();
}

$errmsg = '';
$msg = '';
if(isset($_REQUEST['errmsg'])){ $errmsg = $_REQUEST['errmsg']; }
if(isset($_REQUEST['msg'])){ $msg = $_REQUEST['msg']; }

$creditAmount = '';
$creditReason = '';
$debitAmount = '';
$debitReason = '';

if(isset($_POST['postAdmin'])){
    
	$pay2checkClient	= $_POST['pay2checkClient'];
	$creditAmount = $_POST['creditAmount'];
	if(isset($_POST['creditReason'])){ $creditReason = $_POST['creditReason']; }
	$debitAmount = $_POST['debitAmount'];
	if(isset($_POST['debitReason'])){ $debitReason = $_POST['debitReason']; }
	
	if($pay2checkClient == ''){ $errmsg = "You must select a client to credit or debit funds from."; 
	} else{
		if(($creditAmount == '') && ($debitAmount == '')){ $errmsg = "You must either enter funds to credit or debit from this account."; }
		if(($creditAmount <> '') && ($creditReason == '')){ $errmsg = "You must select a reason for the credit"; }
		if(($debitAmount <> '') && ($debitReason == '')){ $errmsg = "You must select a reason for the debit"; }
	}
	
	if($errmsg == ''){
		if($creditAmount <> ''){ $doCredit = 1; } else{ $doCredit = 0; }
		if($debitAmount <> ''){ $doDebit = 1; } else{ $doDebit = 0; }
                updateCheckFinancials($pay2checkClient, $doCredit, str_replace(",","",$creditAmount), $creditReason, $doDebit, str_replace(",","",$debitAmount), $debitReason);
		$msg = "The credits and/or debits have been applied to the account.";
		
		$pay2checkClient = '';
		$creditAmount = '';
		$creditReason = '';
		$debitAmount = '';
		$debitReason = '';
	}
} 

//Error messaging
if($errmsg <> ''){ $errmsg = "<p class=errmsg>".$errmsg."</p>"; } else { $errmsg = ''; }
if($msg <> ''){ $msg = "<p class=msg>".$msg."</p>"; } else { $msg = ''; }	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Maxx Payments</title>
<link rel="stylesheet" href="styles/styles.css" type="text/css">

<script type="text/javascript">
function checkall(el){
	var ip = document.getElementsByTagName('input'), i = ip.length - 1;
	for (i; i > -1; --i){
		if(ip[i].type && ip[i].type.toLowerCase() === 'checkbox'){
			ip[i].checked = el.checked;
		}
	}
}
</script>
</head>

<body>
<div id="content">
<?php require("menu.php"); ?>
<div id="header">Pay2Cheque <img src="../../../images/arrow.jpg" align="absmiddle" /> Add Financial Adjustments</div>
<?php echo $msg; ?>
<?php echo $errmsg; ?>
<div id="main" style="height:500px;padding-left:10px;">
<form name="pay2checkAdmin" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table width="300" cellspacing="0" cellpadding="5" align="left" border="0" style="border:1px solid #555555;">
	<tr>
    	<td class="searchHeader">Select Client</td>
    </tr>
    <tr>
    	<td><?php pay2CheckClientsDDL($pay2checkClient); ?></td>
    </tr>
    <tr>
    	<td class="header">Credits</td>
    </tr>
    <tr>
    	<td>Amount : $ <input type="text" name="creditAmount" value="<?php echo $creditAmount; ?>" style="width:100px;" /><br />Reason : <br /><input type="radio" name="creditReason" value="Deposit" <?php if($creditReason == 'Deposit'){ ?> checked="checked" <?php } ?> />Deposit<br /><input type="radio" name="creditReason" value="Credit for a declined Pay2Cheque transaction" <?php if($creditReason == 'Credit for a declined Pay2Cheque transaction'){ ?> checked="checked" <?php } ?> />Credit for a declined Pay2Cheque transaction</td>
    </tr>
    <tr>
    	<td class="header">Debits</td>
    </tr>
    <tr>
    	<td>Amount : $ <input type="text" name="debitAmount" value="<?php echo $debitAmount; ?>" style="width:100px;" /><br />Reason : <br /><input type="radio" name="debitReason" value="Transaction Fees" <?php if($debitReason == 'Transaction Fees'){ ?> checked="checked" <?php } ?> />Transaction Fees<br /><input type="radio" name="debitReason" value="Batch Fee" <?php if($debitReason == 'Batch Fee'){ ?> checked="checked" <?php } ?> />Batch Fee<br /><input type="radio" name="debitReason" value="Stop Payment Fee" <?php if($debitReason == 'Stop Payment Fee'){ ?> checked="checked" <?php } ?> />Stop Payment Fee<br /><input type="radio" name="debitReason" value="Rush Payment Fee" <?php if($debitReason == 'Rush Payment Fee'){ ?> checked="checked" <?php } ?> />Rush Payment Fee<br /><input type="radio" name="debitReason" value="Transfer to Pay2Card Account" <?php if($debitReason == 'Transfer to Pay2Card Account'){ ?> checked="checked" <?php } ?> />Transfer to Pay2Card Account</td>
    </tr>
    <tr>
    	<td class="header" align="right"><input type="image" src="images/button_functions.gif" value="submit" /></td>
    </tr>
</table>
<input type="hidden" name="postAdmin" value="yes" />
<input type="hidden" name="pg" value="creditdebit" />
</form>
</div>
</div>
<?php require("footer.php"); ?>
</body>
</html>