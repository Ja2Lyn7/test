<?php
//Admin URL or FAIL url or continues in case on regular login
verifyLogin();

if($sessionAdmin <> 1){
	$destination = "location:https://www.maxxpayments.com/login.php?errmsg=You are not authorized to access this site.";
    header($destination);
	exit();
}

$errmsg = '';
$msg = '';
if(isset($_REQUEST['errmsg'])){ $errmsg = $_REQUEST['errmsg']; }
if(isset($_REQUEST['msg'])){ $msg = $_REQUEST['msg']; }

$errmsg1 = '';
$badRecord = 0;
$ok = 1;
$pay2checkClient = '';
$financeError = '';

//Admin URL or FAIL url or continues in case on regular login
verifyLogin();

//sets global variable $sessionFintraxAllowed


if(isset($_POST['postFile'])){

    $pay2checkClient = $_REQUEST['pay2checkClient'];
    $target_path = P2CH_DIR;
	$target_path = $target_path . basename( $_FILES['uploadedFile']['name']); 
	
	if($pay2checkClient == ''){
		$errmsg = "You must select a merchant to assign the file to.";
	}else{

		if(move_uploaded_file($_FILES['uploadedFile']['tmp_name'], $target_path)) {
       		$msg = "The file ".  basename( $_FILES['uploadedFile']['name'])." has been uploaded";
		} else{
        	$errmsg = "There was an error uploading the file, please try again!";
		}
	}
}

if(isset($_POST['postCredits'])){

    $pay2checkClient = $_POST['pay2checkClient'];
    $transaction_data=array();
    $filename = $_POST['filename'];

	$batchId=trackCheckBatch($pay2checkClient, $filename);
	
	$row = $_POST['numberRows'];
	for($i=1;$i<=$row;$i++){

		$merchantRefNo = "merchantRefNo_".$i;
		$transaction_data['merchantRefNo'] = $_POST[$merchantRefNo];
	
		$firstname = "firstname_".$i;
		$transaction_data['firstname'] = $_POST[$firstname];
			
		$middleInitial = "middleInitial_".$i;
		$transaction_data['middleInitial'] = $_POST[$middleInitial];
			
		$lastname = "lastname_".$i;
		$transaction_data['lastname'] = $_POST[$lastname];
		
		$streetAddress = "streetAddress_".$i;
		$transaction_data['streetAddress'] = $_POST[$streetAddress];
			
		$address2 = "address2_".$i;
		$transaction_data['address2'] = $_POST[$address2];
			
		$city = "city_".$i;
		$transaction_data['city'] = $_POST[$city];
		
		$state = "state_".$i;
		$transaction_data['state'] = $_POST[$state];
			
		$postalcode = "postalcode_".$i;
		$transaction_data['postalcode'] = $_POST[$postalcode];
			
		$country = "country_".$i;
		$transaction_data['country'] = $_POST[$country];
			
		$emailAddress = "emailAddress_".$i;
		$transaction_data['emailAddress'] = $_POST[$emailAddress];
		
		$phoneNumber = "phoneNumber_".$i;
		$transaction_data['phoneNumber'] = $_POST[$phoneNumber];
		
		$creditAmount = "creditAmount_".$i;
		$transaction_data['creditAmount'] = $_POST[$creditAmount];
			
		$currencyAbbr = "currencyAbbr_".$i;
		$transaction_data['currencyAbbr'] = $_POST[$currencyAbbr];

        $recordId=insertCheckBatch($pay2checkClient, $transaction_data, $batchId);
	}
		
	$totalCredit = $_POST['totalCredit'];
		
    $newBalance = updateCheckBalance($pay2checkClient, $totalCredit, $row);
	$msg = "This batch has been uploaded. You have ".$newBalance." remaining.";
		
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
<title>Maxx Payments</title>
<link rel="stylesheet" href="styles/styles.css" type="text/css">
</head>

<body>
<div id="content">
<?php require("menu.php"); ?>
<div id="header">Pay2Cheque <img src="../../../images/arrow.jpg" align="absmiddle" /> Add a new file or record</div>
<?php echo $msg; ?>
<?php echo $errmsg; ?>
<div id="main">
<?php if(isset($_POST['postFile'])){ ?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table width="1180" cellpadding="5" cellspacing="0" align="center" border="0" style="border:1px #555555;">
	<tr>
    	<td valign="top" colspan="10"><p><b>Please review the records below. If an item is correct you will see an "OK" next to the row. If the entire file is good you will be able to select "upload credits." Otherwise you will see the error message next to the record with issues. Please correct the record on the original .csv file and re-upload that file.</b></p></td>
    </tr>
    <tr>
    	<td class="header">MerchantRefNo</td>
        <td class="header">Customer name</td>
        <td class="header">Address</td>
        <td class="header">Email address</td>
        <td class="header">Phone number</td>
        <td class="header">Credit amount</td>
        <td class="header">&nbsp;</td>
  	</tr>
    <?php
	$row = 0;
	$totalCredit = '';
    $isMerchantRefNoDuplicated = '';
    $mixed_merchantRefNo = array();
    //$unique_merchantRefNo = array
               
    $handle = fopen($target_path, "r");
	while(($data = fgetcsv($handle, 1000, ",")) !== FALSE){
		
		if($row>0){
        	$num = count($data);
            //13 is the number of fields in pay2check csv file
				
            if($num==13){
                                      
            	$merchantRefNo = $data[0];
                $customerName = $data[1]." ".$data[2]." ".$data[3];
                $customerAddress = $data[4]." ".$data[5]." ".$data[6].", ".$data[7]." ".$data[8]." ".$data[9];
                $emailAddress = $data[10];
                $phoneNumber = ereg_replace("[^0-9]", "", $data[11]);
                $creditAmount = $data[12];
                $creditAmount = str_replace("$","",$creditAmount);
                $creditAmount = str_replace(",","",$creditAmount);

                //avoiding repeat of errors printing
                $ok = 1;
                $badRecord = 0;
                $errmsg1 = '';
                list($ok,$badRecord,$errmsg1)=validateCSVRecord($data,$pay2checkClient);								
						
                //$unique_merchantRefNo=  array_unique($mixed_merchantRefNo);
                if(in_array($merchantRefNo,$mixed_merchantRefNo)) {
                	//echo $merchantRefNo." : ".print_r($mixed_merchantRefNo)."<br>";
                    $badRecord=1;
                    $errmsg1=$errmsg1."MerchantRefNo NOT UNIQUE within this file";
                }
                array_push($mixed_merchantRefNo, $data[0]);

        		?>
                <tr>
                   	<td><?php echo $merchantRefNo; ?></td>
                    <td><?php echo $customerName; ?></td>
                    <td><?php echo $customerAddress; ?></td>
                    <td><?php echo $emailAddress; ?></td>
                    <td><?php echo $phoneNumber; ?></td>
                    <td><?php echo number_format($creditAmount,2); ?> USD</td>
                	<td>
					<?php 
					if($errmsg1 == ''){ $status = '<span style="font-weight:bold;">OK</span>';
                	} else {
                		$status = '<span style="font-weight:bold;color:#cc0000;">'.$errmsg1.'</span>';
                	} echo $status; ?>
                	</td>
         		</tr>
                <input type="hidden" name="row" value="<?php echo $row; ?>" />
                <input type="hidden" name="merchantRefNo_<?php echo $row; ?>" value="<?php echo $merchantRefNo; ?>" />
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
                <input type="hidden" name="currencyAbbr_<?php echo $row; ?>" value="USD" />
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
    	<td class="header">Totals</td>
        <td class="header"><?php echo $row-1; ?> records</td>
        <td class="header">&nbsp;</td>
        <td class="header">&nbsp;</td>
        <td class="header">&nbsp;</td>
        <td class="header">$<?php echo number_format($totalCredit,2); ?> USD</td>
        <td class="header">&nbsp;</td>
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
<p>&nbsp;</p>
<?php
$transactionCount = $row-1;
$balanceInfo=checkCheckBalance($pay2checkClient, $totalCredit, $transactionCount);
$balanceInfo = explode("|",$balanceInfo);
$remainingBalance = $balanceInfo[0];
$transactionFees = $balanceInfo[1];
if($totalCredit > ($remainingBalance-$transactionFees)){
	$ok = 0;
	$badRecord = 1;
	echo "<p class=errmsg style=\"padding-left:10px;padding-right:10px;text-align:left;\">***You do not have enough available balance to process this credit batch (".$remainingBalance." remaining). Please reach to you account manager in order to add to your available balance or remove some transactions from this file and re-upload.</p><p style=\"padding-left:10px;text-align:right;\"><a href=\"javascript:history.go(-1);\">&laquo; back</a></p>";
	$financeError = 1;
}
?>
<p style="text-align:right;padding:0px 10px 10px 10px;"> 
<?php 
if(($badRecord == 0) && ($errmsg == '')){
	$msg="<br>Please review the records above and click \"upload credits\"  to confirm that the records are correct.<br>";
 	echo $msg;?>
	<input type="image" src="images/button_credits.gif" name="submit" value="upload credits" />
    <?php } else{ if($financeError <> 1){ ?><span style="font-weight:bold;color:#cc0000;">The file contains errors. Please correct on the .csv file and re-upload the file.</span><br /><a href="javascript:history.go(-1);">&laquo; back</a></p><?php } } ?></p>
<input type="hidden" name="numberRows" value="<?php echo $row-1; ?>" />
<input type="hidden" name="totalCredit" value="<?php echo $totalCredit; ?>" />
<input type="hidden" name="filename" value="<?php echo basename( $_FILES['uploadedFile']['name']); ?>" />
<input type="hidden" name="postCredits" value="yes" />
<input type="hidden" name="pay2checkClient" value="<?php echo $pay2checkClient;?>" />    
<input type="hidden" name="pg" value="merchant" />            
</form>
<?php } else { ?>
<table width="1180" cellspacing="0" cellpadding="5" align="center" border="0">
	<tr>
    	<td valign="top" width="300">
        	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
            <table width="300" cellpadding="5" cellspacing="0" align="left" border="0" style="border:1px solid #555555;">
				<tr>
					<td valign="top" class="searchHeader">Upload pay2cheque .csv file</td>
				</tr>
				<tr>
					<td><i>Please choose a file to upload.</i></td>
				</tr>
				<tr>
					<td><input name="uploadedFile" type="file" /></td>
				</tr>
                <tr>
                   	<td>Select merchant:<?php pay2CheckClientsDDL($pay2checkClient); ?> </td>
                </tr>    
                <tr>
					<td><input type="image" src="images/button_upload.gif" value="upload file" name="submit" /></td>
				</tr>
                <tr>
                  	<td style="padding-top:25px;" align="right"><a href="<?php echo P2CH_TEMPLATE_DIR;?>pay2chequeTemplate.csv">Download Pay2Cheque Template</a></td>
                </tr>           
			</table>
			<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
			<input type="hidden" name="postFile" value="yes" />
            <input type="hidden" name="pg" value="merchant" />                                                            
			</form>
     	</td>
  	</tr>
</table>
<?php } ?>
</div>
</div>
<?php require("footer.php"); ?>
</body>
</html>