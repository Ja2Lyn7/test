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

$errmsg = '';
$msg = '';
$startDate = '';
$endDate = '';
$selectedloginid = '';

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
    
    	<h1>Accounting</h1>
    	<?php echo $errmsg; ?>	
        <?php echo $msg; ?>
        
      	<form name="searchOptions" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    	<table width="100%" cellpadding="0" cellspacing="0" align="center" border="0">
        	<tr>
            	<td width="50%" valign="top">
        <p>Date range<br />
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
        <p>Client<br /><select name="selectedloginid" style="width:100px;">
		<option value=""></option>	
		<?php getLogins($selectedloginid); ?>
        </select></p>
        		</td>
           	</tr>
       		<tr>
            	<td valign="top" colspan="2">
    	 			<p><input type="image" src="../../../../images/searchbutton.jpg" name="submit" value="submit" />&nbsp;&nbsp;<input type="image" src="../../../../images/clear.jpg" name="clear" value="clear" /></p>
        		</td>
    		</tr>
      	</table> 
        <input type="hidden" name="postSearch" value="yes" />
		<input type="hidden" name="pg" value="results" />
		</form>  
       
    </div>
    
    <?php require("footer.php"); ?>  
    
</div>

</body>
</html>