<?php
error_reporting(2047);
ini_set("display_errors",1);

//Unset any lingering cookies
setcookie("sessionLoginId", "", time()-3600);
unset($_COOKIE['sessionLoginId']); 
		
setcookie("sessionUserId", "", time()-3600);
unset($_COOKIE['sessionUserId']);
		
setcookie("sessionAdmin", "", time()-3600);
unset($_COOKIE['sessionAdmin']);
		
setcookie("sessionCompanyName", "", time()-3600);
unset($_COOKIE['sessionCompanyName']);  

$username = '';
$password = '';
$errmsg = '';
$msg = '';

if(isset($_GET['errmsg'])){ $errmsg = $_GET['errmsg']; }

if(isset($_POST['post'])){
	
	$username = $_POST['username'];
	$password = $_POST['password'];

	if(($username == '') || ($password == '')) { $errmsg = $errmsg."*Enter your login details.<br>"; }
	
	if($errmsg == ''){
		
		include("admin/includes/config/dbinfo.inc.php");
		include("admin/includes/config/admin.inc.php");
		include("admin/includes/general_functions.php");
		
		$result=getLogin($_POST['username'], $_POST['password']);
	
		$result 				= explode("|",$result);
		$loginId 				= $result[0];
		$userId 				= $result[1];
		$admin 					= $result[2];
		$active 				= $result[3];
		$companyName 			= $result[4];
		
		if(($loginId == 0) || ($active == 0)){
			$errmsg = 'Invalid Login';
		} else{
			
			if($errmsg == ''){
			
			$body = "<form name='reload' action='login.php' method='post'>
				<input type='hidden' name='loginId' value='".$loginId."'/>
				<input type='hidden' name='userId' value='".$userId."'/>
				<input type='hidden' name='admin' value='".$admin."'/>
				<input type='hidden' name='companyName' value='".$companyName."'/>
				<input type='submit' style='visibility:hidden'>
				</form>
				<script type='text/javascript'>document.reload.submit();</script>";
			echo $body;
			}
		}
	}
}

if($errmsg <> ''){ $errmsg = "<p class=errmsg>".$errmsg."</p>"; } else { $errmsg = ''; }
if($msg <> ''){ $msg = "<p class=msg>".$msg."</p>"; } else { $msg = ''; }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>AVG Fulfillment</title>
<link href="styles/styles.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="container">
	
	<?php require("menu.php"); ?> 
    <?php require("header.php"); ?>
    
    <div id="content">
    
    	<h1>Welcome to AVG Fullfillment</h1>
        <p>Please use the form below to login</p>
    	<?php echo $errmsg; ?>	
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        	
            <table width="400" cellpadding="0" cellspacing="0" border="0">
            	<tr>
                	<td>Username:</td>
                    <td><input type="text" name="username" value="<?php $username; ?>" /></td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td><input type="password" name="password" /></td>
                </tr>
                <tr>
                    <td colspan="2" align="right"><input type="image" src="images/login.jpg" name="submit" value="submit" border="0" width="116" height="31" /></td>
               	</tr>
           	</table>
        
        <input type="hidden" name="post" value="yes">
		</form>
    
    </div>
    
    <?php require("footer.php"); ?>  
    
</div>
</body>
</html>  