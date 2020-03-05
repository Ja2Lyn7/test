<?php
error_reporting(2047);
ini_set("display_errors",1);

$errmsg = '';
$msg = '';
$startDate = '';
$endDate = '';
$searchBy = '';
$value = '';

verifyLogin();
if($sessionAdmin <> 1){
	$destination = "location:https://www.avgfulfillment.com/index.php?errmsg=You are not authorized to access this site.";
    header($destination);
	exit();
}

if(isset($_GET['pg'])){ $pg = $_GET['pg'] ; }else{ $pg = ''; }
if($errmsg <> ''){ $errmsg = "<p class=errmsg>".$errmsg."</p>"; } else { $errmsg = ''; }
if($msg <> ''){ $msg = "<p class=msg>".$msg."</p>"; } else { $msg = ''; }

//Error messaging
if($errmsg <> ''){ $errmsg = "<p class=errmsg>".$errmsg."</p>"; } else { $errmsg = ''; }
if($msg <> ''){ $msg = "<p class=msg>".$msg."</p>"; } else { $msg = ''; }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>AVG Fulfillment</title>
<link rel="stylesheet" href="../styles/styles.css" type="text/css">
<link type="text/css" href="css/ui-lightness/jquery-ui-1.8rc3.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8rc3.custom.min.js"></script>
</head>

<body>
<div id="container">
	
	<?php require("menu.php"); ?> 
    <?php require("header.php"); ?>
    
    <div id="content" style="height:500px;">
    
    	<h1>Activity</h1>
    	<?php echo $errmsg; ?>	
        <?php echo $msg; ?>
        
        <form name="searchOptions" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    	<table width="100%" cellpadding="0" cellspacing="0" align="center" border="0">
        	<tr>
            	<td width="50%" valign="top">
        <p>Search by date range<br />
        Start Date&nbsp;
        <script type="text/javascript">
        $(function() {
        $("#datepicker").datepicker();
        });
        </script>
        <input type="text" name="startDate" id="datepicker" value="<?php echo $startDate; ?>" /><br /><br />
        End Date&nbsp;&nbsp;
        <script type="text/javascript">
        $(function() {
        $("#datepicker2").datepicker();
        });
        </script>
        <input type="text" name="endDate" id="datepicker2" value="<?php echo $endDate; ?>" />
    	</p>
        		</td>
                <td valign="top">
        <p>Search by value<br /><input type="text" name="value" value="<?php echo $value; ?>" /><br /><br />
       	<input type="radio" name="searchBy" value="fulfillmentNo" <?php if($searchBy == 'fulfillmentNo'){ ?> checked="checked" <?php } ?> />Fulfillment number<br />
       	<input type="radio" name="searchBy" value="trackingNo" <?php if($searchBy == 'trackingNo'){ ?> checked="checked" <?php } ?> />Tracking number<br />
        <input type="radio" name="searchBy" value="email" <?php if($searchBy == 'email'){ ?> checked="checked" <?php } ?>  />Email address</p>
        		</td>
           	</tr>
       		<tr>
            	<td valign="top" colspan="2">
    	 			<p><input type="image" src="../../../../images/searchbutton.jpg" name="submit" value="submit" />&nbsp;&nbsp;<input type="image" src="../../../../images/clear.jpg" name="clear" value="clear" /></p>
        		</td>
    		</tr>
      	</table> 
        <input type="hidden" name="postSearch" value="yes" />
		<input type="hidden" name="pg" value="search" />
		</form>
           
    </div>
    
    <?php require("footer.php"); ?>  
    
</div>
</body>
</html>

