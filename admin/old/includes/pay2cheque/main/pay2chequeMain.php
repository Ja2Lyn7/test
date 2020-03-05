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

//Error messaging
if($errmsg <> ''){ $errmsg = "<p class=errmsg>".$errmsg."</p>"; } else { $errmsg = ''; }
if($msg <> ''){ $msg = "<p class=msg>".$msg."</p>"; } else { $msg = ''; }	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Maxx Payments</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<link rel="stylesheet" href="styles/styles.css" type="text/css">
<link href="layout.css" rel="stylesheet" type="text/css" />
<script src="rollover.js" type="text/javascript"></script>
<link type="text/css" href="css/ui-lightness/jquery-ui-1.8rc3.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8rc3.custom.min.js"></script>
<script>
var newwindow;
function popup(url){
	newwindow=window.open(url,'name','height=275,width=575');
	if (window.focus) {newwindow.focus()}
}
</script>
<script>
var newwindow;
function popupReceipt(url){
	newwindow=window.open(url,'name','height=600,width=600');
	if (window.focus) {newwindow.focus()}
}
</script>
</head>
<body>
<div id="content">
<?php require("menu.php"); ?>
<div id="header">Pay2Cheque</div>
<?php echo $errmsg; ?>
<?php echo $msg; ?>

<div id="main" style="height:200px;padding-left:10px;">
<table width="350" cellpadding="5" cellspacing="0" align="left" border="0" style="border:1px solid #555555;">
    <tr>
         <td class="searchHeader">Pay2Cheque options</td>
    </tr>
    <tr>
        <td valign="top"><a href="pay2cheque.php?pg=merchant">Add file or record</a></td>
   	</tr>
    <tr>
    	<td valign="top"><a href="pay2cheque.php?pg=admin">Review batches</a></td>
    </tr>
    <tr>
        <td valign="top"><a href="pay2cheque.php?pg=financial">View financial records</a></td>
    </tr>
    <tr>
        <td valign="top"><a href="pay2cheque.php?pg=creditdebit">Add financial adjustments</a></td>
    </tr>
    <tr>
        <td valign="top"><a href="pay2cheque.php?pg=upload">Update transaction tracking</a></td>
    </tr>
</table>
</div>
</div>
<?php require("footer.php"); ?>
</body>
</html>