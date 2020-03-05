<?php
$errmsg = '';
$msg = '';
if(isset($_REQUEST['errmsg'])){ $errmsg = $_REQUEST['errmsg']; }
if(isset($_REQUEST['msg'])){ $msg = $_REQUEST['msg']; }

verifyLogin();

if(isset($_GET['pg'])){ $pg = $_GET['pg'] ; }else{ $pg = ''; }

$selectedloginid = '';
$errmsg1 = '';
$badRecord = 0;
$ok = 1;
$financeError = '';

if(isset($_POST['postFile'])){

    $selectedloginid 	= $_REQUEST['selectedloginid'];
    $target_path 		= FULFILLMENT_DIR;
	$target_path 		= $target_path . basename( $_FILES['uploadedFile']['name']); 

	if($selectedloginid == ''){
		$errmsg = "You must select a client to assign the file to.";
	}else{

		if(move_uploaded_file($_FILES['uploadedFile']['tmp_name'], $target_path)) {
       		
		} else{
        	$errmsg = "There was an error uploading the file, please try again!";
		}
	}
}

if(isset($_POST['postFulfillment'])){

    $selectedloginid 	= $_POST['selectedloginid'];
    $fulfillment_data	=array();
    $filename 			= $_POST['filename'];

	$batchId			= trackBatch($selectedloginid, $filename);
	
	$row = $_POST['numberRows'];
	
	for($i=1;$i<=$row;$i++){

		$fulfillmentNo = "fulfillmentNo_".$i;
		$fulfillment_data['fulfillmentNo'] = $_POST[$fulfillmentNo];
	
		$firstname = "firstname_".$i;
		$fulfillment_data['firstname'] = $_POST[$firstname];
			
		$middleInitial = "middleInitial_".$i;
		$fulfillment_data['middleInitial'] = $_POST[$middleInitial];
			
		$lastname = "lastname_".$i;
		$fulfillment_data['lastname'] = $_POST[$lastname];
		
		$streetAddress = "streetAddress_".$i;
		$fulfillment_data['streetAddress'] = $_POST[$streetAddress];
			
		$address2 = "address2_".$i;
		$fulfillment_data['address2'] = $_POST[$address2];
			
		$city = "city_".$i;
		$fulfillment_data['city'] = $_POST[$city];
		
		$state = "state_".$i;
		$fulfillment_data['state'] = $_POST[$state];
			
		$postalcode = "postalcode_".$i;
		$fulfillment_data['postalcode'] = $_POST[$postalcode];
			
		$country = "country_".$i;
		$fulfillment_data['country'] = $_POST[$country];
			
		$emailAddress = "emailAddress_".$i;
		$fulfillment_data['emailAddress'] = $_POST[$emailAddress];
		
		$phoneNumber = "phoneNumber_".$i;
		$fulfillment_data['phoneNumber'] = $_POST[$phoneNumber];
		
		$creditAmount = "creditAmount_".$i;
		$fulfillment_data['creditAmount'] = $_POST[$creditAmount];
			
        $recordId=insertBatch($selectedloginid, $fulfillment_data, $batchId);
	}
		
	$totalCredit = $_POST['totalCredit'];
		
    $newBalance = updateBalance($selectedloginid, $totalCredit, $row);
	$msg = "This batch has been uploaded. You have ".number_format($newBalance,2)." remaining.";
		
	$merchantRefNo = '';
	$firstname = '';
	$middleInitial = '';
	$lastname = '';
	$streetAddress = '';
	$address2 = '';
	$city = '';
	$state = '';
	$postalcode = '';
	$country = '';
	$emailAddress = '';
	$phone = '';
	$amount = '';
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
<link rel="stylesheet" href="../../../../styles/styles.css" type="text/css">
</head>

<body>

<div id="container">
	
	<?php require("menu.php"); ?> 
    <?php require("header.php"); ?>
    
    <div id="content" style="height:100%;">
    
    	<h1>Add File or Record</h1>
    	<?php echo $msg; ?>
		<?php echo $errmsg; ?>
        
        <?php if((isset($_POST['postFile'])) && $errmsg == ''){ ?>
        <p>Please review the records below. If an item is correct you will see an "OK" next to the row. If the entire file is good you will be able to select "upload." Otherwise you will see the error message next to the record with issues. Please correct the record on the original .csv file and re-upload that file.</p>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<table width="100%" cellpadding="0" cellspacing="0" align="center" border="0">
    		<tr>
    			<td class="tableHeader">Fulfillment no.</td>
        		<td class="tableHeader">Customer name</td>
        		<td class="tableHeader">Address</td>
        		<td class="tableHeader">Email address</td>
        		<td class="tableHeader">Phone number</td>
        		<td class="tableHeader">Credit amount</td>
        		<td class="tableHeader">&nbsp;</td>
  			</tr>
    		<?php
			$row = 0;
			$totalCredit = '';
    		$isFulfillmentNoDuplicated = '';
    		$mixed_fulfillmentNo = array();
    		//$unique_merchantRefNo = array
               
    		$handle = fopen($target_path, "r");
			while(($data = fgetcsv($handle, 1000, ",")) !== FALSE){
		
				if($row>0){
        			$num = count($data);
            		//13 is the number of fields in fulfillment csv file
				
            		if($num==13){
                                      
            			$fulfillmentNo 		= $data[0];
                		$customerName 		= $data[1]." ".$data[2]." ".$data[3];
                		$customerAddress 	= $data[4]." ".$data[5]."<br>".$data[6].", ".$data[7]." ".$data[8];
                		$emailAddress 		= $data[10];
                		$phoneNumber 		= ereg_replace("[^0-9]", "", $data[11]);
                		$creditAmount 		= $data[12];
                		$creditAmount 		= str_replace("$","",$creditAmount);
                		$creditAmount 		= str_replace(",","",$creditAmount);

                		//avoiding repeat of errors printing
                		$ok = 1;
                		$badRecord = 0;
                		$errmsg1 = '';
                		list($ok,$badRecord,$errmsg1)=validateCSVRecord($data,$selectedloginid);								
						
                		//$unique_merchantRefNo=  array_unique($mixed_merchantRefNo);
                		if(in_array($fulfillmentNo,$mixed_fulfillmentNo)) {
                			//echo $merchantRefNo." : ".print_r($mixed_merchantRefNo)."<br>";
                    		$badRecord=1;
                    		$errmsg1=$errmsg1."FulfillmentNo NOT UNIQUE within this file";
                		}
                		array_push($mixed_fulfillmentNo, $data[0]);
						
        				?>
                		<tr class=tableBorder onMouseOver="this.className='highlight'" onMouseOut="this.className='tableBorder'">
                   			<td class="tableBorder"><?php echo $fulfillmentNo; ?></td>
                    		<td class="tableBorder"><?php echo $customerName; ?></td>
                    		<td class="tableBorder"><?php echo $customerAddress; ?></td>
                    		<td class="tableBorder"><?php echo $emailAddress; ?></td>
                    		<td class="tableBorder"><?php echo $phoneNumber; ?></td>
                    		<td class="tableBorder"><?php echo $creditAmount; ?></td>
                			<td class="tableBorder">
							<?php 
							if($errmsg1 == ''){ $status = '<span style="font-weight:bold;">OK</span>';
                			} else {
                				$status = '<span style="font-weight:bold;color:#cc0000;">'.$errmsg1.'</span>';
                			} echo $status; ?>
                			</td>
         				</tr>
                		<input type="hidden" name="row" value="<?php echo $row; ?>" />
                		<input type="hidden" name="fulfillmentNo_<?php echo $row; ?>" value="<?php echo $fulfillmentNo; ?>" />
                		<input type="hidden" name="firstname_<?php echo $row; ?>" value="<?php echo $data[1]; ?>" />
                		<input type="hidden" name="middleInitial_<?php echo $row; ?>" value="<?php echo $data[2]; ?>" />
                		<input type="hidden" name="lastname_<?php echo $row; ?>" value="<?php echo $data[3]; ?>" />
                		<input type="hidden" name="streetAddress_<?php echo $row; ?>" value="<?php echo $data[4]; ?>" />
                		<input type="hidden" name="address2_<?php echo $row; ?>" value="<?php echo $data[5]; ?>" />
                		<input type="hidden" name="city_<?php echo $row; ?>" value="<?php echo $data[6]; ?>" />
                		<input type="hidden" name="state_<?php echo $row; ?>" value="<?php echo $data[7]; ?>" />
                		<input type="hidden" name="postalcode_<?php echo $row; ?>" value="<?php echo $data[8]; ?>" />
                		<input type="hidden" name="country_<?php echo $row; ?>" value="<?php echo $data[9]; ?>" />
                		<input type="hidden" name="emailAddress_<?php echo $row; ?>" value="<?php echo $data[10]; ?>" />
                		<input type="hidden" name="phoneNumber_<?php echo $row; ?>" value="<?php echo $data[11]; ?>" />
                		<input type="hidden" name="creditAmount_<?php echo $row; ?>" value="<?php echo $creditAmount; ?>" />
						<?php 
                                
						$totalCredit = $totalCredit + $creditAmount;

            		} else {
            			$ok = 0;
                		$badRecord = 1;
                		$errmsg= "Invalid File Format";
                		$errmsg = "<p class=errmsg>".$errmsg."</p>";

                		break;
          			}
				}
    		$row++;
    		}
    		fclose($handle);
    		?>
   		<tr>
    			<td class="tableHeader" style="border-top:1px solid #8d8d8d">Totals</td>
        		<td class="tableHeader" style="border-top:1px solid #8d8d8d"><?php echo $row-1; ?> records</td>
        		<td class="tableHeader" style="border-top:1px solid #8d8d8d">&nbsp;</td>
        		<td class="tableHeader" style="border-top:1px solid #8d8d8d">&nbsp;</td>
        		<td class="tableHeader" style="border-top:1px solid #8d8d8d">&nbsp;</td>
        		<td class="tableHeader" style="border-top:1px solid #8d8d8d">$<?php echo number_format($totalCredit,2); ?> USD</td>
        		<td class="tableHeader" style="border-top:1px solid #8d8d8d">&nbsp;</td>
     		</tr>
     		<tr>
        		<td></td>
        		<td></td>
        		<td></td>
        		<td><?php echo '<span style="font-weight:bold;color:#cc0000;">'.$errmsg.'</span>'; ?></td>
        		<td></td>
        		<td></td>
        		<td></td>
     		</tr>
		</table>
        
        <?php
		$transactionCount = $row-1;
		$balanceInfo=checkBalance($selectedloginid, $totalCredit, $transactionCount);
		$balanceInfo = explode("|",$balanceInfo);
		$remainingBalance = $balanceInfo[0];
		$transactionFees = $balanceInfo[1];
		if(($totalCredit + 0) > ($remainingBalance-$transactionFees)){
			$ok = 0;
			$badRecord = 1;
			echo "<p class=errmsg style=\"padding-left:10px;padding-right:10px;text-align:left;\">***You do not have enough available balance to process this batch ($".number_format($remainingBalance)." remaining - this includes a $25,000 reserve. To use this reserve, please send an email to support@avgfulfillment.com). Please reach to you account manager in order to add to your available balance or remove some transactions from this file and re-upload.***</p><p style=\"padding-left:10px;text-align:right;\"><a href=\"javascript:history.go(-1);\">&laquo; back</a></p>";
			$financeError = 1;
		}
		?>
		<p style="text-align:right;padding:0px 10px 10px 10px;"> 
		<?php 
		if(($badRecord == 0) && ($errmsg == '')){
			
			$msg="<br>Please review the records above and click \"upload credits\"  to confirm that the records are correct.<br>";
 			echo $msg;?>
			<input type="image" src="../../../../images/upload.jpg" name="submit" value="upload credits" style="padding-top:10px;" />
    		<?php } else{ if($financeError <> 1){ ?><span style="font-weight:bold;color:#cc0000;">The file contains errors. Please correct on the .csv file and re-upload the file.</span><br /><a href="javascript:history.go(-1);">&laquo; back</a></p><?php } } ?></p>
		<input type="hidden" name="numberRows" value="<?php echo $row-1; ?>" />
		<input type="hidden" name="totalCredit" value="<?php echo $totalCredit; ?>" />
		<input type="hidden" name="filename" value="<?php echo basename( $_FILES['uploadedFile']['name']); ?>" />
		<input type="hidden" name="postFulfillment" value="yes" />
		<input type="hidden" name="selectedloginid" value="<?php echo $selectedloginid;?>" />    
		<input type="hidden" name="pg" value="add" />            
		</form>
	<?php } else { ?>
        <div style="height:500px;">
        <form name="uploadFile" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
        <p><b>Upload fulfillmlent .csv file</b></p>
        Please choose a file to upload : <input name="uploadedFile" type="file" /><br />
        <input type="image" src="../../../../images/upload.jpg" value="submit" style="padding-top:10px;" />
        <p><a href="<?php echo FULFILLMENT_TEMPLATE_DIR;?>fulfillmentTemplate.csv">Download Fulfillment Template</a></p>
        <input type="hidden" name="MAX_FILE_SIZE" value="100000" />
		<input type="hidden" name="postFile" value="yes" />
        <input type="hidden" name="selectedloginid" value="<?php echo $sessionLoginId; ?>" />
        <input type="hidden" name="pg" value="add" />
        </form>
        </div>

	<?php } ?>
        
    </div>
    
    <?php require("footer.php"); ?>  
    
</div>

</body>
</html>