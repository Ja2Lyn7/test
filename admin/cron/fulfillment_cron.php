<?php
include("../includes/config/dbinfo.inc.php");
include("../includes/config/admin.inc.php");
include("../includes/fulfillment_cron_functions.php");
require("../../phpmailer/class.phpmailer.php");
require("../includes/general_functions.php");

//Fulfillment Configuration
$directory 	= FULFILLMENT_BATCH_DIR;

$today = date("dmY");
$recordCounter = 1;
$creditTotal = 0;
$fileArray=array();

$batchArray=getBatchIds();
$batchArray = explode(",",$batchArray);

if($batchArray[0] != NULL) {
	
	$filename = "CHECK" .$batchArray[0]. ".csv";
	$shipfilename = "SHIP" .$batchArray[0]. ".csv";
    
    $myFile = $directory.$filename;
    array_push($fileArray,$myFile);

    $fh = fopen($myFile, 'w');
    if ($fh==FALSE){
        die("Could not create" . $filename) ;
    }

	$line = "Payee name,Amount,Check number,Check date,Memo line,Note1,Note2,Address1,Address2,Address3,Address4,Tracking no";

    fwrite($fh, "$line\r\n");
	//echo $line;

 	for($i=0;$i<count($batchArray);$i++) {

		$startCount = $recordCounter;
		$startCredit = $creditTotal;
    	$batchId=$batchArray[$i];

		$vdr_info=writeOneFile($batchId,$fh,$recordCounter,$creditTotal);
    	$recordCounter=$vdr_info['recordCounter'];
    	$creditTotal=$vdr_info['creditTotal'];
		
		$accountName = 'AVGFulfillment';
		
		$batchRecordCount = $recordCounter-$startCount;
		$batchCreditTotal = $creditTotal-$startCredit;
	
		$i=$i+0;
 	}
 
    fclose($fh);
	
	$myShipFile = $directory.$shipfilename;
    array_push($fileArray,$myShipFile);

    $fh = fopen($myShipFile, 'w');
    if ($fh==FALSE){
        die("Could not create" . $shipfilename) ;
    }

    //no header line required

 	for($i=0;$i<count($batchArray);$i++) {

		$startCount = $recordCounter;
		$startCredit = $creditTotal;
    	$batchId=$batchArray[$i];

		$vdr_info=writeOneShipFile($batchId,$fh,$recordCounter,$creditTotal);
	
		$i=$i+0;
 	}
 
    fclose($fh);
	
	$emailCreditTotal = $creditTotal;
    
	
	$email_message_body= "File Name:" . $filename .  "<br>No. Of Transactions=" . ($recordCounter-1) .  "<br>Credit Amount=". $emailCreditTotal."<br><br>". EMAIL_SIGNATURE;
	
	//Send email
	$subject = "Batch files received - " . date("d.M H:i")." Avgfulfillment, Files=".count($fileArray);
	
	$htmlBody = date("Y.m.d H:i:s")."<br>".count($fileArray)." attachments<br><br>" . $email_message_body;
	$mail = new PHPMailer();
		
	$mail->Host = "mail.avgfulfillment.com";          
	$mail->From = "support@avgfulfillment.com";
	$mail->FromName = "Support";
	$mail->AddAddress("support@avgfulfillment.com");
	$mail->Username = "support@avgfulfillment.com";
	$mail->Password =  "mail!@2017";
	$mail->Port  =  "25";
	
	$mail->AddAttachment($myFile);
	$mail->AddAttachment($myShipFile);

	$mail->Subject = $subject;
	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	//$mail->WordWrap = 50;

	if(!$mail->Send()){
		 echo "mail error".$mail->ErrorInfo;
    } else {
		
		echo "mail sent";
		
		for($i=0;$i<count($batchArray);$i++) {
        	$myFile=$fileArray[$i];

        	$batchId=$batchArray[$i];
        	updateBatchRecord($batchId);
        	unlink($myFile);
			unlink($myShipFile);
    	}
	}
	
}else{
	//There were no batches to send today.
	$subject = "No files today";
	
	$htmlBody = "";
	$mail = new PHPMailer();
		                       
	$mail->Host = "mail.avgfulfillment.com";          
	$mail->From = "support@avgfulfillment.com";
	$mail->FromName = "Support";
	$mail->AddAddress("support@avgfulfillment.com");
	$mail->Username = "support@avgfulfillment.com";
	$mail->Password =  "mail!@2017";
	$mail->Port  =  "25";

	$mail->Subject = $subject;
	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	//$mail->WordWrap = 50;

	if(!$mail->Send()){
   		echo 'Message was not sent.';
   		echo 'Mailer error: ' . $mail->ErrorInfo;
	}else{
   		echo 'Mail Sent.';
	}
}
?>