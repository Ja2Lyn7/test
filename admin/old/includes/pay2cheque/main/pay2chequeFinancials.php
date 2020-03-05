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

if(isset($_REQUEST['pay2checkClient'])){ $pay2checkClient = $_REQUEST['pay2checkClient']; }else{ $pay2checkClient = ''; }

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
</head>

<body>
<div id="content">
<?php require("menu.php"); ?>
<div id="header">Pay2Cheque <img src="../../../images/arrow.jpg" align="absmiddle" /> View Financial Records</div>
<?php echo $msg; ?>
<?php echo $errmsg; ?>
<div id="main">
<table width="1180" cellpadding="10" cellspacing="0" align="center" border="0">
	<tr>
    	<td valign="top" width="800">
			<table width="100%" cellpadding="5" cellspacing="0" align="center" border="0" style="border:1px solid #555555;">
				<tr>
					<td colspan="7" valign="top" class="searchHeader">
        				<div style="float:left;">Financial History</div>
          				<div style="float:right;">
							<form name="pay2cardClient" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
							<?php pay2CheckClientsDDL($pay2checkClient) ?>
                			<input type="submit" value="go" />
                			<input type="hidden" name="postClient" value="yes" />
                			<input type="hidden" name="pg" value="financial" />
                			</form>
         				</div>
					</td>
				</tr>
				<?php $transactionCount=pay2checkAdminTransactionCount($pay2checkClient);
	
    			if($transactionCount==0) {
    				echo "<tr><td valign=\"top\" colspan=\"7\"><p style=\"color:#ff0000;\"><i>There are no records to display.</i></p></td></tr>";
    			} else {
		
					$tableHeader = "<tr><td class='header' style='border-right:1px solid #ffffff;'>Financial Id</td>
					<td class='header' style='border-right:1px solid #ffffff;'>Company</td>
					<td class='header' style='border-right:1px solid #ffffff;'>TimeStamp</td>
					<td class='header' style='border-right:1px solid #ffffff;'>Description</td>
					<td class='header' style='border-right:1px solid #ffffff;'>Debit</td>
					<td class='header' style='border-right:1px solid #ffffff;'>Credit</td>
					<td class='header'>Remaining Balance</td></tr>";
					echo $tableHeader;
					list($start,$limit,$pagenum,$last)=pageLimits($transactionCount);
        			getAdminPay2CheckTransactionListing($start,$limit,$pay2checkClient);
		
					echo "<tr><td colspan=\"7\"><div style=\"padding:5px 10px 5px 10px;\">";
        			$nameValuePair="pg=$pg&pay2checkClient=$pay2checkClient";
        			pagination($pagenum,$last,$nameValuePair);
					echo "</div></td></tr>";
				}                           
    		?>
    		</table>
        </td>
        <td valign="top" width="10"></td>
    	<td valign="top" width="370">
        <?php if($pay2checkClient <> '' ){ ?>
        
        	<table width="100%" cellpadding="5" cellspacing="0" align="center" border="0" style="border:1px solid #555555;">
				<tr>
					<td colspan="4" valign="top" class="searchHeader">Cheque counts</td>
				</tr>
                <?php
                $tableHeader = "<tr><td class='header' style='border-right:1px solid #ffffff;'>Month</td>
					<td class='header' style='border-right:1px solid #ffffff;'>1-100</td>
					<td class='header' style='border-right:1px solid #ffffff;'>101-200</td>
					<td class='header'>201+</td></tr>";
				echo $tableHeader;
				
				getCheckCounts($pay2checkClient);
		}
		?>
        	</table>
        </td>
    </tr>
</table>
</div>
</div>
<?php require("footer.php"); ?>
</body>
</html>