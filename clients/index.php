<?php
error_reporting(2047);
ini_set("display_errors",1);

require("includes/config/dbinfo.inc.php");
include("includes/config/admin.inc.php");
include("includes/general_functions.php");

$errmsg = '';
$msg = '';

verifyLogin();

if(isset($_GET['pg'])){ $pg = $_GET['pg'] ; }else{ $pg = ''; }
if($errmsg <> ''){ $errmsg = "<p class=errmsg>".$errmsg."</p>"; } else { $errmsg = ''; }
if($msg <> ''){ $msg = "<p class=msg>".$msg."</p>"; } else { $msg = ''; }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>AVG Fulfillment</title>
<link rel="stylesheet" href="../styles/styles.css" type="text/css">
</head>

<body>
<div id="container">
	
	<?php require("menu.php"); ?> 
    <?php require("header.php"); ?>
    
    <div id="content" style="height:500px;">
    
    	<h1>Welcome to AVG Fulfillment.</h1>
    	<?php echo $errmsg; ?>
        <?php echo $msg; ?>	
    
    </div>
    
    <?php require("footer.php"); ?>  
    
</div>
</body>
</html>