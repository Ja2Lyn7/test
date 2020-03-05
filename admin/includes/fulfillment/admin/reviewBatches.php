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
if(isset($_REQUEST['batchId'])){ $batchId = $_REQUEST['batchId']; }else{ $batchId = ''; }

if(isset($_POST['postAccepted'])){
	
	$recordsArray = $_POST['recordsArray'];
	$recordsArray = explode(",",$recordsArray);
	
	for($i=0;$i<count($recordsArray);$i++){
		$recordId = $recordsArray[$i];
		$checkbox = "approve_".$recordId;
		if(isset($_POST[$checkbox])){ $approveCheckbox = $_POST[$checkbox]; } else{ $approveCheckbox = 0; }
                updateBatchAccepted($recordId, $approveCheckbox);
		$msg = "The approved records in the batch will be processed during the next upload period. Anything that was declined has been marked in the merchant's system.";
	}
}

if(isset($_POST['postCompleted'])){
	
	$recordsArray = $_POST['recordsArray'];
	$recordsArray = explode(",",$recordsArray);
	
	for($i=0;$i<count($recordsArray);$i++){
		$recordId = $recordsArray[$i];
		$checkbox = "approve_".$recordId;
		$trackingNo = $_POST['tId_'.$recordId];
		
		if(isset($_POST[$checkbox])){ $approveCheckbox = $_POST[$checkbox]; } else{ $approveCheckbox = 0; }
        	updateBatchCompleted($recordId, $trackingNo, $approveCheckbox);
		$msg = "Fulfillment records have been completed. Anything that was declined will show up in the activity list.";
	}
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
<script type="text/javascript">
function checkall(el){
	var ip = document.getElementsByTagName('input'), i = ip.length - 1;
	for (i; i > -1; --i){
		if(ip[i].type && ip[i].type.toLowerCase() === 'checkbox'){
			ip[i].checked = el.checked;
		}
	}
}
</script>
</head>

<body>
<div id="container">
	
	<?php require("menu.php"); ?> 
    <?php require("header.php"); ?>
    
    <div id="content" style="height:500px;">
    
    	<h1>Review Batches</h1>
    	<?php echo $errmsg; ?>
        <?php echo $msg; ?>	
        
        
        <?php if(isset($_POST['postReview'])){ ?>
            
            <form method="post" name="postAccepted" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <table width="100%" cellpadding="0" cellspacing="0" align="left" border="0">
            	<tr>
                	<td class="tableHeader">&nbsp;</td>
                	<td class="tableHeader">FulfillmentNo</td>
                	<td class="tableHeader">Customer name</td>
                    <td class="tableHeader">Address</td>
                    <td class="tableHeader">Email address</td>
                    <td class="tableHeader">Phone number</td>
                    <td class="tableHeader">Credit amount</td>
                </tr>
                <?php $response='';
                      $response=getRecordsForReview($batchId); 
                      list($recordsArray,$recordCount,$totalCredit)= explode("|", $response) 
                ?>
                <tr>
                	<td class="tableHeader" style="border-top:1px solid #8d8d8d">&nbsp;</td>
                	<td class="tableHeader" style="border-top:1px solid #8d8d8d">Totals</td>
                    <td class="tableHeader" style="border-top:1px solid #8d8d8d"><?php echo $recordCount; ?> records</td>
                    <td class="tableHeader" style="border-top:1px solid #8d8d8d">&nbsp;</td>
                    <td class="tableHeader" style="border-top:1px solid #8d8d8d">&nbsp;</td>
                    <td class="tableHeader" style="border-top:1px solid #8d8d8d">&nbsp;</td>
                    <td class="tableHeader" style="border-top:1px solid #8d8d8d">$<?php echo number_format($totalCredit,2); ?></td>
                </tr>
            </table>
            <p style="padding-left:6px;padding-top:25px;"><input type="checkbox" value="" onclick="checkall(this);">check all/uncheck all</p>
            <p style="text-align:right;"><input type="image" src="../../../../images/approve.jpg" name="submit" value="approve batch" /></p>
            <input type="hidden" name="postAccepted" value="yes" />
            <input type="hidden" name="recordsArray" value="<?php echo $recordsArray; ?>" />
            <input type="hidden" name="pg" value="review" />            
            </form>
            
      	<?php } elseif(isset($_POST['postProcess'])){ ?>
        
            <form method="post" name="postCompleted" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <table width="100%" cellpadding="0" cellspacing="0" align="left" border="0">
            	<tr>
                	<td class="tableHeader">&nbsp;</td>
                	<td class="tableHeader">Activity Id</td>
                    <td class="tableHeader">Customer name</td>
                    <td class="tableHeader">Address</td>
                    <td class="tableHeader">Credit amount</td>
                    <td class="tableHeader">Tracking No</td>
                </tr>
                <?php   $response='';
                        $response=getRecordsProcessed($batchId);
                        list($recordsArray,$recordCount,$totalCredit)= explode("|", $response);
                ?>
                <tr>
                	<td class="tableHeader" style="border-top:1px solid #8d8d8d">&nbsp;</td>
                	<td class="tableHeader" style="border-top:1px solid #8d8d8d">Totals</td>
                    <td class="tableHeader" style="border-top:1px solid #8d8d8d"><?php echo $recordCount; ?> records</td>
                    <td class="tableHeader" style="border-top:1px solid #8d8d8d">&nbsp;</td>
                    <td class="tableHeader" style="border-top:1px solid #8d8d8d">$<?php echo number_format($totalCredit,2); ?></td>
                    <td class="tableHeader" style="border-top:1px solid #8d8d8d">&nbsp;</td>
                </tr>
            </table>
            <p style="padding-left:6px;padding-top:25px;"><input type="checkbox" value="" onclick="checkall(this);">check all/uncheck all</p>
            <p style="text-align:right;"><input type="image" src="../../../../images/approve.jpg" name="submit" value="approve batch" /></p>
            <input type="hidden" name="postCompleted" value="yes" />
            <input type="hidden" name="recordsArray" value="<?php echo $recordsArray; ?>" />
            <input type="hidden" name="pg" value="review" />                        
            </form>

 		<?php } else{ ?>
			
            <h1 style="padding-top:25px;">Batches for review</h1>
			<?php getBatchesForReview(); ?>
			
            <p>&nbsp;</p>
            
			<h1>Batches shipped</h1>
			<?php getBatchesProcessed(); ?>
          
		<?php } ?>
    
    </div>
    
    <?php //require("footer.php"); ?>  
    
</div>
</body>
</html>
