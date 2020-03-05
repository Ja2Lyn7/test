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

$emailAddress 	= '';
$c1 			= '';
$c2 			= '';

if(isset($_POST['post'])){
	
	if(isset($_POST['blacklistEmail'])){ $blacklistEmail = $_POST['blacklistEmail']; } else{ $blacklistEmail = 0; }
	if(isset($_POST['blacklistCard'])){ $blacklistCard = $_POST['blacklistCard']; } else{ $blacklistCard = 0; }
	
	$emailAddress = $_POST['emailAddress'];
	$c1 = $_POST['c1'];
	$c2 = $_POST['c2'];

	if(($blacklistEmail == '') && ($blacklistCard == '')){
		$errmsg = "<p style=\"color:#cc0000;font-weight:bold;\">You must select at least one option to blacklist.</p>";
	}else{
		
		if(($blacklistEmail <> '' ) && ($emailAddress == '')){
			$errmsg = "<p style=\"color:#cc0000;font-weight:bold;\">You must enter an email address to blacklist.</p>";
		}elseif($blacklistCard <> ''){
			if(($c1 == '') && ($c2 == '')){
				$errmsg = "<p style=\"color:#cc0000;font-weight:bold;\">You must enter a card number to blacklist.</p>";
			}
		}
	}
	
	if($errmsg == ''){
    	updateBlackList($blacklistEmail,$blacklistCard,$emailAddress,$c1,$c2);
        $msg = "<p style=\"color:#00cc00;font-weight:bold;\">The blacklist has been successfully updated.</p>";
	}
} else{
	$recordId = $_REQUEST['recordId'];
	$blacklist_array=getBlacklistDetails($recordId);
	
	$emailAddress 	= $blacklist_array['emailAddress'];
	$c1 			= $blacklist_array['bin'];
	$c2 			= $blacklist_array['lastFour'];
}

//Error messaging
if($errmsg <> ''){ $errmsg = "<p class=errmsg>".$errmsg."</p>"; } else { $errmsg = ''; }
if($msg <> ''){ $msg = "<p class=msg>".$msg."</p>"; } else { $msg = ''; }
?>
<html>
<body>
<div style="font-family:arial;font-size:12px;width:500px;">
<p style="font-family:arial;font-size:12px;">Please select which information you would like to add to the blacklist.</p>
<p style="font-family:arial;font-size:12px;"><?php echo $errmsg; echo $msg; ?></p>
<form name="blacklist" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table width="495" cellpadding="5" cellspacing="0" align="left" border="0" style="font-family:arial;font-size:12px;border:1px solid #cccccc;">
<tr><td><input type="checkbox" name="blacklistEmail" value="1" <?php if($blacklistEmail == 1){ ?> checked=checked <?php } ?>></td><td>Email address</td><td><b><input type="text" name="emailAddress" value="<?php echo $emailAddress; ?>"></b></td></tr>
<tr><td><input type="checkbox" name="blacklistCard" value="1" <?php if($blacklistCard == 1){ ?> checked=checked <?php } ?>></td>

<td>Card number</td>
<td><b><input type="text" name="c1" style="width:65px;" maxlength="6" value="<?php echo $c1; ?>" /> xxxxxx <input type="text" name="c2" style="width:40px;" maxlength="4" value="<?php echo $c2; ?>" /> <span style="font-size:9px;">Requires the bin and last 4 of the card number.</span></td></tr>
<tr>
<td align="right" colspan="3"><input type="submit" value="Submit" name="submit" /></td></tr>
</table>
<input type="hidden" name="pg" value="blacklist" />
<input type="hidden" name="post" value="yes" />
</form>
</body>
</html>