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

if(isset($_POST['postReview'])){
	$batchId = $_POST['batchId'];

}

if(isset($_POST['postProcess'])){
	$batchId = $_POST['batchId'];

}

if(isset($_POST['postApproval'])){
	$recordsArray = $_POST['recordsArray'];
	$recordsArray = explode(",",$recordsArray);
	
	for($i=0;$i<count($recordsArray);$i++){
		$recordId = $recordsArray[$i];
		$checkbox = "approve_".$recordId;
		if(isset($_POST[$checkbox])){ $approveCheckbox = $_POST[$checkbox]; } else{ $approveCheckbox = 0; }
                updateCheckBatchApproval($recordId, $approveCheckbox);
		$msg = "The approved records in the batch will be processed during the next upload period. Anything that was declined has been marked in the merchant's system.";
	}
}

if(isset($_POST['postComplete'])){
	$recordsArray = $_POST['recordsArray'];
	$recordsArray = explode(",",$recordsArray);
	
	for($i=0;$i<count($recordsArray);$i++){
		$recordId = $recordsArray[$i];
		$checkbox = "approve_".$recordId;
		$transactionId = $_POST['tId_'.$recordId];
		
		if(isset($_POST[$checkbox])){ $approveCheckbox = $_POST[$checkbox]; } else{ $approveCheckbox = 0; }
                updateCheckBatchComplete($recordId, $transactionId, $approveCheckbox);
		$msg = "Records have been completed and approved for Pay2Card. Anything that was declined has been marked in the merchant's system.";
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
<div id="header">Pay2Cheque <img src="../../../images/arrow.jpg" align="absmiddle" /> Manage Pay2Cheque Batches</div>
<?php echo $msg; ?>
<?php echo $errmsg; ?>
<div id="main">
<table width="100%" cellpadding="5" cellspacing="0" align="center" border="0">
	<tr>
    	<td valign="top">
        	<?php if(isset($_POST['postReview'])){ ?>
            <form method="post" name="postApproval" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <table width="100%" cellpadding="5" cellspacing="0" align="left" border="0" style="border:1px solid #555555;">
            	<tr>
                	<td class="header" width="60">Approved</td>
                	<td class="header">MerchantRefNo</td>
                    <td class="header">Customer name</td>
                    <td class="header">Address</td>
                    <td class="header">Email address</td>
                    <td class="header">Phone number</td>
                    <td class="header">Credit amount</td>
                </tr>
                <?php $response='';
                      $response=getCheckRecordsForReview($batchId); 
                      list($recordsArray,$recordCount,$totalCredit)= explode("|", $response) 
                ?>
                <tr>
                	<td class="header">&nbsp;</td>
                	<td class="header">Totals</td>
                    <td class="header"><?php echo $recordCount; ?> records</td>
                    <td class="header">&nbsp;</td>
                    <td class="header">&nbsp;</td>
                    <td class="header">&nbsp;</td>
                    <td class="header">$<?php echo number_format($totalCredit,2); ?></td>
                </tr>
            </table>
            <p style="padding-left:6px;"><input type="checkbox" value="" onclick="checkall(this);">check all/uncheck all</p>
            <p style="text-align:right;"><input type="image" src="images/button_approve.gif" name="submit" value="approve batch" /></p>
            <input type="hidden" name="postApproval" value="yes" />
            <input type="hidden" name="recordsArray" value="<?php echo $recordsArray; ?>" />
            <input type="hidden" name="pg" value="admin" />            
            </form>
            <?php } elseif(isset($_POST['postProcess'])){ ?>
            <form method="post" name="postComplete" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <table width="100%" cellpadding="5" cellspacing="0" align="left" border="0" style="border:1px solid #555555;">
            	<tr>
                	<td class="header" width="60">Approved</td>
                	<td class="header">Record Id</td>
                    <td class="header">Customer name</td>
                    <td class="header">Address</td>
                    <td class="header">Email address</td>
                    <td class="header">Phone number</td>
                    <td class="header">Credit amount</td>
                    <td class="header">Transaction Id</td>
                </tr>
                <?php   $response='';
                        $response=getCheckRecordsProcessed($batchId);
                        list($recordsArray,$recordCount,$totalCredit)= explode("|", $response);
                ?>
                <tr>
                	<td class="header">&nbsp;</td>
                	<td class="header">Totals</td>
                    <td class="header"><?php echo $recordCount; ?> records</td>
                    <td class="header">&nbsp;</td>
                    <td class="header">&nbsp;</td>
                    <td class="header">&nbsp;</td>
                    <td class="header">$<?php echo number_format($totalCredit,2); ?></td>
                    <td class="header">&nbsp;</td>
                </tr>
            </table>
            <p style="padding-left:6px;"><input type="checkbox" value="" onclick="checkall(this);">check all/uncheck all</p>
            <p style="text-align:right;"><input type="image" src="images/button_approve.gif" name="submit" value="approve batch" /></p>
            <input type="hidden" name="postComplete" value="yes" />
            <input type="hidden" name="recordsArray" value="<?php echo $recordsArray; ?>" />
            <input type="hidden" name="pg" value="admin" />                        
            </form>
    	</td>
 	</tr>
</table>
<?php } else{ ?>
<table width="100%" cellpadding="0" cellspacing="0" align="left" border="0">
	<tr>
    	<td valign="top"><?php getCheckBatchesForReview(); ?></td>
	</tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
    <tr>
    	<td valign="top"><?php getCheckBatchesSent(); ?></td>
    </tr>
</table>
</td>
</tr>
</table>           
<?php } ?>
</div>
</div>
<?php require("footer.php"); ?>
</body>
</html>
