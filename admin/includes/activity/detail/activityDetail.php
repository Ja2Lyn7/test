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

if(isset($_REQUEST['postDetails'])){

	$activityId = $_REQUEST['activityId'];
    $activityDetails=getActivityDetails($activityId);
	
	$amount 		= $activityDetails['amount'];
	$trackingNo 	= $activityDetails['trackingNo'];
	$status 		= $activityDetails['status'];
	$error 			= $activityDetails['error'];
	$timestamp 		= $activityDetails['timestamp'];
	$fulfillmentNo	= $activityDetails['fulfillmentNo'];
	$firstname 		= $activityDetails['firstname'];
	$middleInitial 	= $activityDetails['middleInitial'];
	$lastname 		= $activityDetails['lastname'];
	$streetAddress 	= $activityDetails['streetAddress'];
	$apt 			= $activityDetails['apt'];
	$city 			= $activityDetails['city'];
	$state 			= $activityDetails['state'];
	$postalcode 	= $activityDetails['postalcode'];
	$country 		= $activityDetails['country'];
	$emailAddress 	= $activityDetails['emailAddress'];
	$phone 			= $activityDetails['phone'];
	$ipAddress 		= $activityDetails['ipAddress'];
	$checkNo 		= $activityDetails['checkNo'];
}

$blacklist=verifyBlackList($emailAddress); 
if($blacklist <> '') { 
	$blacklist = "<span style=\"color:#cc0000;font-weight:bold;\">BLACKLISTED</span>";
}else{ 
	$blacklist = '&nbsp;&nbsp;<a href="javascript:popup(\'transactions.php?pg=blacklist&recordId='.$recordId.'\');" style="font-size:9px;">add to blacklist</a>';
}

$batchDetails=getBatchDetails($activityId); 

$batchId 	= $batchDetails['batchId'];
$accepted 	= $batchDetails['accepted'];
$processed	= $batchDetails['processed'];
$completed 	= $batchDetails['completed'];

if($accepted == '1'){ 
	$accepted = 'approved'; 
}elseif($accepted == '0'){   
	$accepted = 'declined';
} else{
	$accepted = 'pending';
}

if($processed == '1'){ 
	$processed = 'approved'; 
}elseif($processed == '0'){   
	$processed = 'declined';
} else{
	$processed = 'pending';
}

if($completed == '1'){ 
	$completed = 'approved'; 
}elseif($completed == '0'){   
	$completed = 'declined';
} else{
	$completed = 'pending';
}

$timestamp = strtotime($activityDetails['timestamp']);
$timestamp = date('m/d/Y g:ia', $timestamp);

if($status == '1'){ 
	$image = 'approved'; 
}elseif($status == '0'){   
	$image = 'declined';
} else{
	$image = 'pending';
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
<div id="container">
	
	<?php require("menu.php"); ?> 
    <?php require("header.php"); ?>
    
    <div id="content">
    
    	<h1>Activity Details</h1>
    	<?php echo $errmsg; ?>	
        <?php echo $msg; ?>
        
        <table width="100%" cellpadding="0" cellspacing="0" align="center" border="0">
        	<tr>
            	<td class="tableHeader">Activity id</td>
                <td class="tableHeader">Fulfillment no</td>
                <td class="tableHeader" align="center">Status</td>
                <td class="tableHeader">IP address</td>
                <td class="tableHeader">Date/Time</td>
                <?php if($status == 0){ ?>
                <td class="tableHeader">Decline reason:</td>
                <?php }  ?> 
         	</tr>
            <tr>
            	<td class="tableBorder"><?php echo $activityId; ?></td>
                <td class="tableBorder"><?php echo $fulfillmentNo; ?></td>
                <td class="tableBorder" align="center"><img src="../../../../images/<?php echo $image; ?>.jpg" border="0" /></td>
                <td class="tableBorder"><?php echo $ipAddress; ?></td>
                <td class="tableBorder"><?php echo $timestamp; ?></td>
                <?php if($status == 0){ ?>
                <td class="tableBorder"><?php echo $error; ?></td>
                <?php }  ?>
    		</tr>
      	</table>
        
        <h1>Fulfillment Information</h1>
   	
		<table width="100%" cellpadding="0" cellspacing="0" align="center" border="0">
    		<tr>
            	<td class="tableHeader">Amount</td>
                <td class="tableHeader">Check no.</td>
                <td class="tableHeader" width="350">Tracking no.</td>
            </tr>
            <tr>
        		<td class="tableBorder">$<?php echo $amount; ?></td>
        		<td class="tableBorder"><?php echo $checkNo; ?></td>
        		<td class="tableBorder"><?php echo $trackingNo; ?></td>
    		</tr>
     	</table>
        
        <h1>Customer Information</h1>
        
        <table width="100%" cellpadding="0" cellspacing="0" align="center" border="0">
    		<tr>
        		<td class="tableHeader">Name</td>
                <td class="tableHeader">Street address</td>
                <td class="tableHeader">Phone</td>
                <td class="tableHeader">Email</td>
           	</tr>
            <tr>
            	<td class="tableBorder" valign="top"><?php echo $firstname; ?> <?php echo $middleInitial; ?> <?php echo $lastname; ?></td>
                <td class="tableBorder" valign="top">
        		<?php 
        		echo $streetAddress."<br>";
        		if((isset($apt)) && ($apt <> '')){ 
        			echo $apt."<br>";
        		 }
         		echo $city." ".$state .", ".$postalcode."<br>";
         		echo $country."<br>";
         		?>
        		</td>
        		<td class="tableBorder" valign="top"><?php echo $phone; ?></td>
                <td class="tableBorder" valign="top"><?php echo $emailAddress; ?> <?php echo $blacklist; ?></td>
    		</tr>
    	</table>
        
        <h1>Batch Information</h1>
        
        <table width="100%" cellpadding="0" cellspacing="0" align="center" border="0">
    		<tr>
        		<td class="tableHeader">Batch id</td>
                <td class="tableHeader" align="center">Accepted</td>
                <td class="tableHeader" align="center">Processed</td>
                <td class="tableHeader" align="center">Completed</td>
            </tr>
            <tr>
        		<td class="tableBorder"><?php echo $batchId; ?></td>
                <td class="tableBorder" align="center"><img src="../../../../images/<?php echo $accepted; ?>.jpg" border="0" /></td>
                <td class="tableBorder" align="center"><img src="../../../../images/<?php echo $processed; ?>.jpg" border="0" /></td>
                <td class="tableBorder" align="center"><img src="../../../../images/<?php echo $completed; ?>.jpg" border="0" /></td>
    		</tr>
    	</table> 
        
        <h1>Fulfillment History <span style="font-size:10px;font-weight:normal;">(by email address)</span></h1>
        
        <table width="100%" cellpadding="0" cellspacing="0" align="center" border="0">     
			<tr>
    			<td class="tableHeader">Activity id</td>
    			<td class="tableHeader">Fulfillment no.</td>
    			<td class="tableHeader" align="center">Status</td>
    			<td class="tableHeader">Amount</td>
				<td class="tableHeader">Check no</td>
    			<td class="tableHeader">Tracking no.</td>
    			<td class="tableHeader">Date/Time</td>
			</tr>
                		<?php $count=emailHistoryCount($emailAddress); 

                		if($count == 1){
                			echo "<tr><td valign=\"top\"><p style=\"color:#ff0000;\" colspan=\"7\"><i>There is no additional activity for this customer.</i></p></td></tr>";
                		} else{
                			list($start,$limit,$pagenum,$last)=pageLimits($count);
                    		getEmailHistory($emailAddress,$start,$limit);
                    		echo "<tr><td valign=\"top\" colspan=\"7\">";
                    		$nameValuePair="pg=$pg&activityId=$activityId&postDetails=yes";
                    		//echo "<tr><td>";
                    		pagination($pagenum,$last,$nameValuePair);
                    		echo"</td></tr>";
						} ?>
					</table>
       			</td>
   			</tr>
		</table>
        
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
        
        <div align="right" style="float:right;"><a href="javascript:history.go(-1);">&laquo; back</a></div>
    </div>
    
    <?php require("footer.php"); ?>  
    
</div>
</body>
</html>