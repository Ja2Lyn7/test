<?php

require("includes/config/dbinfo.inc.php");
include("includes/config/admin.inc.php");
include("includes/general_functions.php");
include("includes/chargeback_functions.php");

verifyLogin();
if($sessionAdmin <> 1){
	$destination = "location:https://www.maxxpayments.com/login.php?errmsg=You are not authorized to access this site.";
    header($destination);
	exit();
}

if(isset($_GET['pg'])){ $pg = $_GET['pg'] ; }else{ $pg = ''; }

if($pg == 'downloadlist'){
	list($filename,$myFile)=downloadErrorCodes();     

    header("Content-disposition: attachment; filename=$filename");
    header("Content-type: application/octet-stream");
    readfile("$myFile");
    exit();
}
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
<div id="header">Welcome to the Maxx Payments Admin Site</div>
<div id="main">
<?php if($sessionTestmode == 1){ ?>
<div id="test"><p style="text-align:center;"><span style="font-size:16px;">Your account is currently in testmode.</span><br />After you run your tests,<br />please contact us at <a href="mailto:support@maxxpayments.com" style="color:#ffffff;">support@maxxpayments.com</a><br />to review your account and set live.</p></div>
<?php } ?>
<table width="100%" cellpadding="10" cellspacing="0" align="center" border="0" height="100%">
    <tr>
		<td valign="top">Welcome to Maxx Payments Merchant System, your source for transaction reporting. Please use the menu above to make your selections.<br /></td>
	</tr>
    <tr>
    	<td valign="top">
        	<table width="1175" cellspacing="0" cellpadding="5" align="left" border="0" style="border:1px solid #555555;">
            	<tr>
                	<td valign="top" class="searchHeader" colspan="2">Error code list</td>
                </tr>
                <tr>
                	<td valign="top" width="250"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?pg=downloadlist">Download .csv of available error codes</a></td>
                	<td valign="top">Last updated on <?php $errorTime=getErrorCodeTime(); echo $errorTime; ?> UTC</td>
           		</tr>
            </table>
       	</td>
   	</tr>
	<?php getAdminChargebacks(); ?>
</table>
</div>
</div>
<?php require("footer.php"); ?>
</body>
</html>