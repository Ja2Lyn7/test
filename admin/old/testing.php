<?php
error_reporting(2047);
ini_set("display_errors",1);

require("includes/config/dbinfo.inc.php");
require("includes/config/admin.inc.php");
require("includes/general_functions.php");

$errmsg = '';
$msg = '';

$companyName = '';
$contactName = '';
$email = '';
$clientTypeId = '';

$clientId = '';
$username = '';
$password = '';
$encryptedPass = '';
$loginTypeId = '';
$authorizationTypeId = '';
$admin = '';
$testmode = '';

if(isset($_POST['postNewClient']) && ($_POST['postNewClient']) == 'yes' ){
	
	$companyName 	= $_POST['companyName'];
	$contactName 	= $_POST['contactName'];
	$email 			= $_POST['email'];
	$clientTypeId	= $_POST['clientTypeId'];
	
	if(($companyName == '') || ($contactName == '') || ($email == '') || ($clientTypeId == '')){
		$errmsg = "All fields are required.";
	}else {
		
		$clientId = insertNewClient($companyName,$contactName,$email,$clientTypeId);
		$msg = "New client created.";
	}
}

if(isset($_POST['postNewUser']) && ($_POST['postNewUser']) == 'yes' ){
	$clientId = $_POST['clientId'];
	
	if($clientId == 'addnew'){
		
		echo "new client";
		//we need a location header redirect here to the new client page	
	
	}else{
		
		$username 				= $_POST['username'];
		$loginTypeId 			= $_POST['loginTypeId'];
		$authorizationTypeId 	= $_POST['authorizationTypeId'];
		$admin					= $_POST['admin'];
		$testmode				= $_POST['testmode'];
	
		if(($clientId == '') || ($loginTypeId == '') || ($authorizationTypeId == '') || ($admin == '') || ($testmode == '')){
			$errmsg = "All fields are required.";
		}else {
		
			echo $username."<br".$loginTypeId."<br".$authorizationTypeId."<br".$admin."<br".$testmode."<br";
			
			//Create a password
			$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*_";
			$password = substr( str_shuffle( $chars ), 0, 8 );
			$encryptedPass = md5($password);
	
			
			//$clientId = insertNewClient($companyName,$contactName,$email,$clientTypeId);
			
			//we need to email login details to the client
			$msg = "New user created. Please copy and save the following password and give to the user.<br><br><b>".$password."</b>";
		}
	}	
}

if($errmsg <> ''){ $errmsg = "<p class=errmsg>".$errmsg."</p>"; } else { $errmsg = ''; }
if($msg <> ''){ $msg = "<p class=msg>".$msg."</p>"; } else { $msg = ''; }
?>

<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Untitled Document</title>
</head>

<body>
<form name="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<b>add new client</b><br>
<?php echo $errmsg; ?>
<?php echo $msg; ?>
company name : <input type="text" name="companyName" value="<?php echo $companyName; ?>"><br>
contact name : <input type="text" name="contactName" value="<?php echo $contactName; ?>"><br>
email address : <input type="text" name="email" value="<?php echo $email; ?>"><br>

client type : <select name="clientTypeId">
<option></option>
<?php getClientTypes($clientTypeId); ?>
</select><br>
<input type="submit">
<input type="hidden" name="postNewClient" value="yes">
</form>
<br><br>

<b>create new user</b><br>
<form name="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
client : <select name="clientId">
<option></option>
<option value="addnew">+ add new</option>
<?php getClients($clientId); ?>
</select><br>

username : <input type="text" name="username" value="<?php echo $username; ?>"><br>

login type : <select name="loginTypeId">
<option></option>
<?php getLoginTypes($loginTypeId); ?>
</select><br>

authorization type : <select name="authorizationTypeId">
<option></option>
<?php getAuthorizationTypes($authorizationTypeId); ?>
</select><br>

admin : <select name="admin">
<option></option>
<option value="1">Yes</option>
<option value="0">No</option>
</select><br>

testmode : <select name="testmode">
<option></option>
<option value="1">Yes</option>
<option value="0">No</option>
</select><br>

<input type="submit">
<input type="hidden" name="postNewUser" value="yes">
</form>
<br><br>

</body>
</html>