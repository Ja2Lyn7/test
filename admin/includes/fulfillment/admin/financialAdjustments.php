<?php
$errmsg = '';
$msg = '';
if(isset($_REQUEST['errmsg'])){ $errmsg = $_REQUEST['errmsg']; }
if(isset($_REQUEST['msg'])){ $msg = $_REQUEST['msg']; }

verifyLogin();
if($sessionAdmin <> 1){
	$destination = "location:https://www.avgfulfillment.com/index.php?errmsg=You are not authorized to access this site.";
    header($destination);
	exit();
}

if(isset($_GET['pg'])){ $pg = $_GET['pg'] ; }else{ $pg = ''; }	

$creditAmount = '';
$creditReason = '';
$debitAmount = '';
$debitReason = '';
$selectedloginId = '';

if(isset($_POST['postAdjustment'])){
    
	$selectedloginId	= $_POST['selectedloginId'];
	$creditAmount 		= $_POST['creditAmount'];
	$debitAmount 		= $_POST['debitAmount'];
	
	if(isset($_POST['creditReason'])){ $creditReason = $_POST['creditReason']; }
	if(isset($_POST['debitReason'])){ $debitReason = $_POST['debitReason']; }
	
	if($selectedloginId == ''){ $errmsg = "You must select a login."; 
	} else{
		if(($creditAmount == '') && ($debitAmount == '')){ $errmsg = "You must either enter funds to credit or debit."; }
		if(($creditAmount <> '') && ($creditReason == '')){ $errmsg = "You must select a reason for the credit."; }
		if(($debitAmount <> '') && ($debitReason == '')){ $errmsg = "You must select a reason for the debit."; }
	}
	
	if($errmsg == ''){
		if($creditAmount <> ''){ $doCredit = 1; } else{ $doCredit = 0; }
		if($debitAmount <> ''){ $doDebit = 1; } else{ $doDebit = 0; }
        	
		if($doCredit == 1){
			creditAdjustment($selectedloginId, str_replace(",","",$creditAmount), $creditReason);
		$msg = "The credits and/or debits have been applied to the account.";	
		}
			
		if($doDebit == 1){
			debitAdjustment($selectedloginId, str_replace(",","",$debitAmount), $debitReason);
		$msg = "The credits and/or debits have been applied to the account.";
		}
		
		$selectedloginId = '';
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
<title>AVG Fulfillment</title>
<link rel="stylesheet" href="../../../../styles/styles.css" type="text/css">
</head>

<body>
<div id="container">
	
	<?php require("menu.php"); ?> 
    <?php require("header.php"); ?>
    
    <div id="content" style="height:500px;">
    
    	<h1>Add Financial Adjustments</h1>
    	<?php echo $msg; ?>	
        <?php echo $errmsg; ?>	
        
        <form name="postAdjustment" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<table width="100%" cellspacing="0" cellpadding="5" align="left" border="0">
				<tr>
    				<td colspan="2">Select User 
                    
                    	<select name="selectedloginId" style="width:100px;">
						<option value=""></option>	
						<?php getLogins($selectedloginId); ?>
                        </select>
                    </td>
    			</tr>
    			<tr>
    				<td width="40%"><h1>Credits</h1></td>
                    <td width="60%"><h1>Debits</h1></td>
    			</tr>
    			<tr>
    				<td valign="top">Amount : $ <input type="text" name="creditAmount" value="<?php echo $creditAmount; ?>" style="width:100px; vertical-align:top;" /><br />Reason : <br /><input type="radio" name="creditReason" value="LOAD" <?php if($creditReason == 'LOAD'){ ?> checked="checked" <?php } ?> /> LOAD<br /><input type="radio" name="creditReason" value="Credit for a cancelled transaction" <?php if($creditReason == 'Credit for a cancelled transaction'){ ?> checked="checked" <?php } ?> /> Credit for a cancelled transaction</td>
                    
                    <td valign="top">Amount : $ <input type="text" name="debitAmount" value="<?php echo $debitAmount; ?>" style="width:100px;" /><br />Reason : <br /><input type="radio" name="debitReason" value="Transaction fee" <?php if($debitReason == 'Transaction Fee'){ ?> checked="checked" <?php } ?> /> Transaction Fee<br /><input type="radio" name="debitReason" value="Batch Fee" <?php if($debitReason == 'Batch Fee'){ ?> checked="checked" <?php } ?> /> Batch Fee<br /><input type="radio" name="debitReason" value="Stop Payment Fee" <?php if($debitReason == 'Stop Payment Fee'){ ?> checked="checked" <?php } ?> /> Stop Payment Fee<br /><input type="radio" name="debitReason" value="Rush Payment Fee" <?php if($debitReason == 'Rush Payment Fee'){ ?> checked="checked" <?php } ?> /> Rush Payment Fee</td>
    			</tr>
    			<tr>
    				<td colspan="2" align="right"><input type="image" src="../../../../images/submit.jpg" value="submit" /></td>
    			</tr>
			</table>

		<input type="hidden" name="postAdjustment" value="yes" />
		<input type="hidden" name="pg" value="adjustments" />
		</form>
    
    </div>
    
    <?php require("footer.php"); ?>  
    
</div>
</body>
</html>