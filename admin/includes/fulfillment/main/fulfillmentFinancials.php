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

if(isset($_REQUEST['selectedloginid'])){ $selectedloginid = $_REQUEST['selectedloginid']; }else{ $selectedloginid = ''; }

//Error messaging
if($errmsg <> ''){ $errmsg = "<p class=errmsg>".$errmsg."</p>"; } else { $errmsg = ''; }
if($msg <> ''){ $msg = "<p class=msg>".$msg."</p>"; } else { $msg = ''; }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>AVG Fulfillment</title>
<link rel="stylesheet" href="../../../../styles/styles.css" type="text/css">
</head>

<body>
<div id="container">
	
	<?php require("menu.php"); ?> 
    <?php require("header.php"); ?>
    
    <div id="content">
    
    	<h1>View Financial Records</h1>
    	<?php echo $msg; ?>
        <?php echo $errmsg; ?>	
        
        <form name="selectUser" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<select name="selectedloginid" style="width:100px;">
		<option value=""></option>	
		<?php getLogins($selectedloginid); ?>
        </select>
        <br />
        <input type="image" src="../../../../images/filter.jpg" value="submit"  style="padding-top:10px;"/>
        <input type="hidden" name="postUser" value="yes" />
        <input type="hidden" name="pg" value="financial" />
        </form>
        
        <table width="100%" cellpadding="0" cellspacing="0" align="center" border="0">
			<?php $count=fulfillmentCount($selectedloginid);
			
    		if($count==0) {
    				echo "<tr><td valign=\"top\" colspan=\"5\"><p style=\"color:#ff0000;\"><i>There are no records to display.</i></p></td></tr>";
    		} else {
		
				$tableHeader = "<tr><td class='tableHeader'>Company</td>
				<td class='tableHeader'>TimeStamp</td>
				<td class='tableHeader'>Description</td>
				<td class='tableHeader'>Credit / (Debit)</td>
				<td class='tableHeader'>Remaining Balance</td></tr>";
				echo $tableHeader;
				
				list($start,$limit,$pagenum,$last)=pageLimits($count);
        			getFinancialListing($start,$limit,$selectedloginid);
		
				echo "<tr><td colspan=\"5\"><div style=\"padding:5px 10px 5px 10px;\">";
        		$nameValuePair="pg=$pg&selectedloginid=$selectedloginid";
        		pagination($pagenum,$last,$nameValuePair);
				echo "</div></td></tr>";
			}                           
    		?>
    	</table>
    </div>
    
    <?php require("footer.php"); ?>  
    
</div>
</body>
</html>