<?php
error_reporting(2047);
ini_set("display_errors",1);

$errmsg = '';
$msg = '';

verifyLogin();

if(isset($_GET['pg'])){ $pg = $_GET['pg'] ; }else{ $pg = ''; }
if($errmsg <> ''){ $errmsg = "<p class=errmsg>".$errmsg."</p>"; } else { $errmsg = ''; }
if($msg <> ''){ $msg = "<p class=msg>".$msg."</p>"; } else { $msg = ''; }

if(isset($_REQUEST['postSearch'])){
	if(isset($_POST['clear'])){
		
		$startDate 	= '';
		$endDate 	= '';
		
		
		$value 		= '';
		$searchBy 	= '';
		
		$destination = "location:activity.php?pg=main";
    	header($destination);
		exit();
	
	}else{

		if(isset($_REQUEST['startDate'])){ $startDate = $_REQUEST['startDate']; }else{ $startDate = ''; }
		if(isset($_REQUEST['endDate'])){ $endDate = $_REQUEST['endDate']; }else{ $endDate = ''; }
                
		if(isset($_REQUEST['value'])){ $value = $_REQUEST['value']; }else{ $value = ''; }
		if(isset($_REQUEST['searchBy'])){ $searchBy = $_REQUEST['searchBy']; }else{ $searchBy = ''; }
		
		if(($value <> '') && ($searchBy == '')){ $errmsg = "You must select the option for the search value."; }
		if(($value == '') && ($searchBy <> '')){ $errmsg = "You must enter a value to search by the option choosen."; }
		
		if(($startDate == '') && ($endDate == '') && ($value == '')  && ($searchBy == '')){       
        	$today = date('Y-m-d');
            $startDate = $today;
			$endDate = $today;
        }
	}
}

if(isset($_POST['postDownload'])){
	$startDate		= $_REQUEST['startDate'];
	$endDate		= $_REQUEST['endDate'];
	$searchValue	= $_REQUEST['searchValue'];
	$searchBy		= $_REQUEST['searchBy'];
	$byType			= $_REQUEST['byType'];
	$searchStatus	= $_REQUEST['searchStatus'];
	$searchId		= $_REQUEST['searchId'];
	$bin			= $_REQUEST['bin'];
	$lastFour		= $_REQUEST['lastFour'];
	$searchType		= $_REQUEST['searchType'];

    list($filename,$myFile)=downloadAdminTransactions($startDate,$endDate,$searchValue,$searchBy,$byType,$searchStatus,$searchId,$bin,$lastFour,$searchType);     

    header("Content-disposition: attachment; filename=$filename");
    header("Content-type: application/octet-stream");
    readfile("$myFile");
    exit();
}

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

<body>
<div id="container">
	
	<?php require("menu.php"); ?> 
    <?php require("header.php"); ?>
    
    <div id="content" style="height:100%;">
    
    	<h1>Activity</h1>
    	<?php echo $errmsg; ?>	
        <?php echo $msg; ?>
        
        <table width="100%" cellspacing="0" cellpadding="0" align="center" border="0">
			<tr>
				<td width="25" class="tableBorder"><img src="../../../../images/approved.jpg" border="0" /></td>
                <td class="tableBorder" width="100">Approved</td>
				<td width="25" class="tableBorder"><img src="../../../../images/declined.jpg" border="0" /></td>
                <td class="tableBorder" width="100">Declined</td>			
				<td width="25" class="tableBorder"><img src="../../../../images/pending.jpg" border="0" /></td>
                <td class="tableBorder">In Process</td>
			</tr>
		</table>
        
        <?php 
		$count=activityCountById($startDate,$endDate,$value,$searchBy,'4');
		//echo "count is ".$count;

		if($count == 0){
			echo "<div style='height:400px;'><p style=\"color:#ff0000;padding:0px 0px 10px 10px;\"><i>There is no activity to display.</i></p></div>";
		} else{
	
    		list($start,$limit,$pagenum,$last)=pageLimits($count);

    		//printDownloadButton($startDate,$endDate,$value,$searchBy);
			activitySearchById($startDate,$endDate,$start,$limit,$value,$searchBy,'4');

        	$nameValuePair="pg=$pg&postSearch=yes&startDate=$startDate&endDate=$endDate&searchBy=$searchBy&value=$value";
        	echo "<div style=\"width:100%;padding-top:5px;padding-bottom:5px;margin-left:10px;\">";
			pagination($pagenum,$last,$nameValuePair);
			echo "</div>";

		}
		?>
           
    </div>
    
    <?php require("footer.php"); ?>  
    
</div>
</body>
</html>