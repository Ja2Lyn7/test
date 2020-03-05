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
if($errmsg <> ''){ $errmsg = "<p class=errmsg>".$errmsg."</p>"; } else { $errmsg = ''; }
if($msg <> ''){ $msg = "<p class=msg>".$msg."</p>"; } else { $msg = ''; }	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>AVG Fulfillment</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<link rel="stylesheet" href="../styles/styles.css" type="text/css" />
<link type="text/css" href="css/ui-lightness/jquery-ui-1.8rc3.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8rc3.custom.min.js"></script>
</head>
<body>

<div id="container">
	
	<?php require("menu.php"); ?> 
    <?php require("header.php"); ?>
    
    <div id="content" style="height:500px;">
    
    	<h1>Fulfillment Options</h1>
    	<?php echo $errmsg; ?>	
        
        <a href="fulfillment.php?pg=add"><img src="../../../../images/add.jpg" width="192" height="66" border="0" /></a>&nbsp;
   		<a href="fulfillment.php?pg=review"><img src="../../../../images/batches.jpg" width="173" height="65" border="0" /></a>&nbsp;
    	<a href="fulfillment.php?pg=financial"><img src="../../../../images/financials.jpg" width="223" height="65" border="0" /></a>&nbsp;
    	<a href="fulfillment.php?pg=adjustments"><img src="../../../../images/adjustments.jpg" width="249" height="67" border="0" /></a><br /><br />
    	<a href="fulfillment.php?pg=tracking"><img src="../../../../images/tracking.jpg" width="261" height="66" border="0" /></a>
       
    </div>
    
    <?php require("footer.php"); ?>  
    
</div>

</body>
</html>