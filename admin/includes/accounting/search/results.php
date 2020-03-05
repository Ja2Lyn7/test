<?php
error_reporting(2047);
ini_set("display_errors",1);

$errmsg = '';
$msg = '';

verifyLogin();
if($sessionAdmin <> 1){
	$destination = "location:https://www.avgfulfillment.com/index.php?errmsg=You are not authorized to access this site.";
    header($destination);
	exit();
}

if(isset($_GET['pg'])){ $pg = $_GET['pg'] ; }else{ $pg = ''; }
if($errmsg <> ''){ $errmsg = "<p class=errmsg>".$errmsg."</p>"; } else { $errmsg = ''; }
if($msg <> ''){ $msg = "<p class=msg>".$msg."</p>"; } else { $msg = ''; }

if(isset($_REQUEST['postSearch'])){
	if(isset($_POST['clear'])){
		
		$startDate 	= '';
		$endDate 	= '';
		
		
		$value 		= '';
		$searchBy 	= '';
		
		$destination = "location:accounting.php?pg=search";
    	header($destination);
		exit();
	
	}else{

		if(isset($_REQUEST['startDate'])){ $startDate = $_REQUEST['startDate']; }else{ $startDate = ''; }
		if(isset($_REQUEST['endDate'])){ $endDate = $_REQUEST['endDate']; }else{ $endDate = ''; }
                
		if(isset($_REQUEST['selectedloginid'])){ $selectedloginid = $_REQUEST['selectedloginid']; }else{ $selectedloginid = ''; }
		
		if(($startDate == '') || ($endDate == '') || ($selectedloginid == '')){ 
			$errmsg = "You must selected a date range and client to view accounting records."; 
		
			$destination = "location:accounting.php?pg=search&errmsg=".$errmsg;
    		header($destination);
			exit();
		}
	}
}

if(isset($_POST['postDownload'])){
	$startDate		   = $_REQUEST['startDate'];
	$endDate		   = $_REQUEST['endDate'];
	$selectedloginid   = $_REQUEST['selectedloginid'];
    list($filename,$myFile)=downloadAccounting($startDate,$endDate,$selectedloginid);     

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
    
    	<h1>Accounting</h1>
    	<?php echo $errmsg; ?>	
        <?php echo $msg; ?>
        
        <?php 
        
		$count=accountingCount($startDate,$endDate,$selectedloginid);
		//echo "count is ".$count;

		if($count == 0){
			echo "<div style='height:400px;'><p style=\"color:#ff0000;padding:0px 0px 10px 10px;\"><i>There are no accounting details to display.</i></p></div>";
		} else{
            
            echo '<b>'.$startDate.' - '.$endDate.'</b><br>';
            
            $totals = accountingSummary($startDate,$endDate,$selectedloginid);
            
            $recordCount    = $totals['recordCount'];
	        $totalCost      = $totals['totalCost'];
            $transactionFee = $totals['transactionFee'];
            $totalFees      = $totals['totalFees'];
            $netRev         = $totals['netRev'];
            $loadCount      = $totals['loadCount'];
            $loadAmount     = $totals['loadAmount'];
            $loadfeeCount   = $totals['loadfeeCount'];
            $loadfeeAmount  = $totals['loadfeeAmount'];
        ?>
            
            <table width="100%" cellspacing="0" cellpadding="5" align="center" border="0">
	           <tr>
    	           <td class="tableHeader">Record Count</td>
                   <td class="tableHeader">Transaction Fees</td>
                   <td class="tableHeader">Shipping Cost</td>
    	           <td class="tableHeader">Net Revenue</td>
                   <td class="tableHeader">Deposit Count</td>
    	           <td class="tableHeader" align="center">Deposit Revenue</td>
	           </tr>
               <tr class=tableBorder onMouseOver="this.className='highlight'" onMouseOut="this.className='tableBorder'">
                   <td class='tableBorder'><b><?php echo $recordCount; ?></b></td>
                   <td class='tableBorder'><b>$<?php echo $totalFees; ?></b></td>
			       <td class='tableBorder'><b>(<?php echo $totalCost; ?>)</b></td>
                   <td class='tableBorder'><b>$<?php echo $netRev; ?></b></td>
                   <td class='tableBorder'><b><?php echo $loadCount; ?></b></td>
                   <td class='tableBorder'><b>$<?php echo $loadfeeAmount; ?></b></td>
               </tr>
            </table>
        <?php      
            echo '<br><br>';
    		accountingDownloadButton($startDate,$endDate,$selectedloginid);
			accountingSearch($startDate,$endDate,$selectedloginid);
		}
		?>
           
    </div>
    
    <?php require("footer.php"); ?>  
    
</div>
</body>
</html>