<?php
function pay2checkAdminTransactionCount($pay2checkClient) {
    myLog("pay2checkAdminTransactionCount() pay2checkClient=$pay2checkClient");
    global $connection;
    
    //Call adminPay2checkCount procedure
    $sql = "CALL adminPay2checkCount(?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('i', $p1);
    $p1 = $pay2checkClient;
    $stmt->execute();
    $stmt->bind_result($pay2checkCount);

    //Get query results
    $stmt->fetch();
    $stmt->free_result();
    $stmt->close();

    while ($connection->next_result()) {
            //free each result.
            $result = $connection->use_result();
            if ($result instanceof mysqli_result) {
                    $result->free();
            }
    }   
    
    return $pay2checkCount;
}

function getAdminPay2CheckTransactionListing($start,$limit,$pay2checkClient) {
    
        myLog("getAdminPay2CheckTransactionListing $start,$limit,$pay2checkClient");
        global $connection; 
        
	//Call getAdminPay2Check procedure
	$sql = "CALL getAdminPay2Check(?,?,?)";
	$stmt = $connection->prepare($sql);
	if($connection->errno){
		die($connection->errno."::".$connection->error);
	}

	$stmt->bind_param('iii', $p1, $p2, $p3);
	$p1 = $start;
	$p2 = $limit;
	$p3 = $pay2checkClient;
	
	$stmt->execute();
	$stmt->bind_result($listFinancialId,$listDescription,$listDebit,$listCredit,$listRemainingBalance,$listDateTimeStamp,$listCompanyName);
	//Get query results
	while($stmt->fetch()){
		if($i % 2 == 0){ $class = "even"; } else{ $class = "odd"; }
        	
		$listDateTimeStamp = strtotime($listDateTimeStamp);
		$listDateTimeStamp = date('d/m/Y H:i', $listDateTimeStamp);

		$tableBody = "<tr class=".$class." onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='".$class."'\"><td>".$listFinancialId."</td>
			<td>".$listCompanyName."</td>
			<td>".$listDateTimeStamp."</td>
			<td>".$listDescription."</td>
			<td>".$listDebit."</td>
			<td>".$listCredit."</td>
			<td align=\"right\"><b>$".number_format($listRemainingBalance,2)."</b></td></tr>";
		echo $tableBody;
	
   	$i++;
	}
	//$tableEnd = "</table></td></tr>";
	//echo $tableEnd;

	$stmt->free_result();
	$stmt->close();
		
	while ($connection->next_result()) {
		//free each result.
		$result = $connection->use_result();
		if ($result instanceof mysqli_result) {
			$result->free();
		}						
	}
        
        
}

function validateCSVRecord($data,$pay2checkClient) {

    $merchantRefNo = $data[0];

    $customerName = $data[1]." ".$data[2]." ".$data[3];
    $customerAddress = $data[4]." ".$data[5]." ".$data[6].", ".$data[7]." ".$data[8]." ".$data[9];
    $emailAddress = $data[10];
    $phoneNumber = $data[11];
    $creditAmount = $data[12];
    $creditAmount = str_replace("$","",$creditAmount);
    $creditAmount = str_replace(",","",$creditAmount);


    $firstname=$data[1];
    $middleInitial=$data[2];
    $lastname=$data[3];
    $streetAddres=$data[5];
    $city=$data[6];
    $state=$data[7];
    $postalcode=$data[8];
    $country=$data[9];
	
    $errmsg1='';
    $ok = 1;
    $badRecord=0;

    //Required Fields
    if(($data[0] == '') || ($data[1] == '') || ($data[3] == '') || ($data[4] == '') || ($data[6] == '') || ($data[7] == '') || ($data[8] == '') || ($data[9] == '') || ($data[10] == '') || ($data[11] == '') || ($data[12] == '')){
		$ok = 0;
		$badRecord = 1;
		$errmsg1 = $errmsg1."REQUIRED FIELDS ARE MISSING";
    }
	
    ////////////////////////////////////////////////////////
    //Length Checking
    $aLength = 50;
    $bLength = 25;
    $cLength = 150;
    $dLength = 75;
    $eLength = 10;
    $fLength = 2;
    $gLength = 250;
    $hLength = 100;
    $iLength = 11;
    $jLength = 3;
    $kLength = 16;
    $lLength = 4;
    $mLength = 255;

    if(strlen($firstname) > $aLength){ $errcode = 39; $errmsg1=$errmsg1."Invalid Firstname Length or Type";}
    if(strlen($middleInitial) > $bLength){ $errcode = 39; $errmsg1=$errmsg1."Invalid MiddleInitial Length or Type";}
    if(strlen($lastname) > $aLength){ $errcode = 39;  $errmsg1=$errmsg1."Invalid lastname Length or Type";}

    if(strlen($streetAddress) > $cLength){ $errcode = 39; $errmsg1=$errmsg1."Invalid streetAddress Length or Type";}
    if(strlen($city) > $dLength){ $errcode = 39; 	$errmsg1=$errmsg1."Invalid City Length or Type";}
    if(strlen($state) > $bLength){ $errcode = 39;	$errmsg1=$errmsg1."Invalid State Length or Type"; }
    if(strlen($postalcode) > $eLength){ $errcode = 39;	$errmsg1=$errmsg1."Invalid postalcode Length or Type";}
    if(strlen($country) > $fLength){ $errcode = 39;	$errmsg1=$errmsg1."Invalid country Length or Type"; }
    if(strlen($phoneNumber) > $bLength){ $errcode = 39;	$errmsg1=$errmsg1."Invalid phoneNumber Length or Type"; }
    if(strlen($emailAddress) > $gLength){ $errcode = 39; $errmsg1=$errmsg1."Invalid EmailAddress Length or Type";}
    if(strlen($merchantRefNo) > $bLength){ $errcode = 39;$errmsg1=$errmsg1."Invalid merchantRefNo Length or Type"; }

    if( (strlen($creditAmount) > $iLength) || (is_numeric($creditAmount == FALSE)) ){ $errcode = 39;  $errmsg1=$errmsg1."Invalid creditAmount Length or Type";}


    if(is_numeric($creditAmount == FALSE)){ $errcode = 39; $errmsg1=$errmsg1."Amount is not Numeric";}

    if($errcode==39) {
       $ok = 0;
       $badRecord = 1;
    }

   $isMerchantRefNoDuplicated=isMerchantRefNoDuplicated($merchantRefNo,$pay2checkClient);
   if($isMerchantRefNoDuplicated) {
       $ok = 0;
       $badRecord = 1;
       $errmsg1=$errmsg1."MerchantRefNo already exists";
   }

   //validate email

   if ( filter_var($emailAddress, FILTER_VALIDATE_EMAIL)==FALSE) {
   	$ok = 0;
	$badRecord = 1;
    $errmsg1=$errmsg1 . "Invalid Email";
   }

   if ( !(TESTMODE) ) {
	   
	   $ccNumber = '4111111111111111';
	
	list($emailBlacklist,$cardBlacklist)=verifyBlacklist($emailAddress, substr($ccNumber,0,6), substr($ccNumber, -4));
          
          if($emailBlacklist){
		    $ok = 0;
                    $badRecord = 1;
                    $errmsg1= $errmsg1 . "<br>" . "Black Listed Email";
          }
   }

   return array($ok,$badRecord,$errmsg1);

}

function isMerchantRefNoDuplicated($merchantRefNo,$LoginId) {
     global $connection;

    //Call isMerchantrefNoDuplicated procedure
    $sql = "CALL isMerchantRefNoDuplicated(?,?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }


    $stmt->bind_param('si', $p1, $p2);
    $p1 = $merchantRefNo;
    $p2 = $LoginId;
    $stmt->execute();
    $stmt->bind_result($isMerchantRefNoDuplicated);
    //Get query results
    $stmt->fetch();
    $stmt->free_result();
    $stmt->close();

    while ($connection->next_result()) {
	//free each result.
	$result = $connection->use_result();
	if ($result instanceof mysqli_result) {
		$result->free();
	}
    }


    return $isMerchantRefNoDuplicated;
}

function checkCheckBalance($LoginId,$totalCredit,$transactionCount) {
    global $connection;
    
    myLog("checkCheckBalance: LoginId=$LoginId, totalCredit=$totalCredit");

    //Call checkCheckBalance procedure
    $sql = "CALL checkCheckBalance(?,?,?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('idi', $p1, $p2, $p3);
    $p1 = $LoginId;
    $p2 = $totalCredit;
    $p3 = $transactionCount;
    $stmt->execute();
    $stmt->bind_result($remainingBalance,$transactionFees);
    //Get query results
    $stmt->fetch();
    $stmt->close();

    while ($connection->next_result()) {
            //free each result.
            $result = $connection->use_result();
            if ($result instanceof mysqli_result) {
    		$result->free();
            }
    }

	$balanceInfo = $remainingBalance."|".$transactionFees;
    myLog("checkBalance: balanceInfo=$balanceInfo");
    return $balanceInfo;
}

function trackCheckBatch($LoginId,$filename) {
     global $connection;

    myLog("trackCheckBatch: LoginId=$LoginId , FileName=$filename");
    //Call trackCheckBatch procedure
    $sql = "CALL trackCheckBatch(?,?)";
    $stmt = $connection->prepare($sql);

    if($connection->errno){
	die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('is', $p1, $p2);
    $p1 = $LoginId;
    $p2 = $filename;
    $stmt->execute();
    $stmt->bind_result($batchId);
    //Get query results
    $stmt->fetch();
    $stmt->close();

    while ($connection->next_result()) {
	//free each result.
	$result = $connection->use_result();
	if ($result instanceof mysqli_result) {
		$result->free();
	}
    }

    myLog("trackBatch: batchId=$batchId");
    return $batchId;
}

function insertCheckBatch($LoginId,$data,$batchId) {

    global $connection;
    myLog("insertCheckBatch: LoginId=$LoginId , batchId=$batchId");
  

     //Call insertCheckBatch procedure
    $sql = "CALL insertCheckBatch(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
	die($connection->errno."::".$connection->error);
    }

    $p1 = $data['merchantRefNo'];
    $p2 = $LoginId;
    $p3 = addslashes($data['firstname']);
    $p4 = addslashes($data['middleInitial']);
    $p5 = addslashes($data['lastname']);
    $p6 = addslashes($data['streetAddress']);
    $p7 = addslashes($data['address2']);
    $p8 = addslashes($data['city']);
    $p9 = addslashes($data['state']);
    $p10 = addslashes($data['postalcode']);
    $p11 = addslashes($data['country']);
    $p12 = addslashes($data['emailAddress']);
    $p13 = str_replace("+","",addslashes($data['phoneNumber']));
    $p14 = $data['creditAmount'];
    $p15 = $data['currencyAbbr'];
    $p16 = $_SERVER['REMOTE_ADDR'];
    $p17 = 'https://www.maxxpayments.com/merchants';
    $p18 = 'https://www.maxxpayments.com/merchants';
    $p19 = 4;
    $p20 = $data['firstname']." ".$data['middleInitial']." ".$data['lastname'];
    $p21 = $batchId;
	
    $stmt->bind_param('sisssssssssssdssssisi', $p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8, $p9, $p10, $p11, $p12, $p13, $p14, $p15, $p16, $p17, $p18, $p19, $p20, $p21);

    $stmt->execute();
    $stmt->bind_result($recordId);

    //Get query results
    $stmt->fetch();
    $stmt->free_result();
    $stmt->close();

    while ($connection->next_result()) {
	//free each result.
	$result = $connection->use_result();
	if ($result instanceof mysqli_result) {
		$result->free();
	}
    }

    myLog("insertCheckBatch: recordId=$recordId");
    return $recordId;
}

function updateCheckBalance($LoginId,$totalCredit,$row) {
    global $connection;

    myLog("updateCheckBalance: LoginId=$LoginId, totalCredit=$totalCredit");
    //Call updateCheckBalance procedure
    $sql = "CALL updateCheckBalance(?,?,?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
	die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('idi', $p1, $p2, $p3);
    $p1 = $LoginId;
    $p2 = $totalCredit;
	$p3 = $row;
	
    $stmt->execute();
	$stmt->bind_result($newBalance);
    //Get query results
    $stmt->fetch();
    $stmt->close();

    while ($connection->next_result()) {
            //free each result.
            $result = $connection->use_result();
            if ($result instanceof mysqli_result) {
    		$result->free();
            }
    }

     myLog("updateCheckBalance: newBalance=$newBalance");
    return $newBalance;

}

function getCheckCounts($pay2checkClient) {
    
        myLog("getCheckCounts $pay2checkClient");
        global $connection; 
        
	//Call getCheckCounts procedure
	$sql = "CALL getCheckCounts(?)";
	$stmt = $connection->prepare($sql);
	if($connection->errno){
		die($connection->errno."::".$connection->error);
	}

	$stmt->bind_param('i', $p1);
	$p1 = $pay2checkClient;
	
	$stmt->execute();
	$stmt->bind_result($listCheckCount,$listStartDate);
	//Get query results
	while($stmt->fetch()){
		if($i % 2 == 0){ $class = "even"; } else{ $class = "odd"; }
        	
		$listDate = strtotime($listStartDate);
		$listDate = date('m-Y', $listDate);
		
		$list100 = '';
		$list200 = '';
		$listmore = '';
		
		if(($listCheckCount <= 100) && ($listCheckCount > 0)){
			$list100 = $listCheckCount;
		}else{
			$list100 = 100;
			$listCheckCount = $listCheckCount-100;
			
			if(($listCheckCount <= 100) && ($listCheckCount > 0)){
				$list200 = $listCheckCount;
			}else{
				$list200 = 100;
				$listCheckCount = $listCheckCount-100;
				
				if($listCheckCount > 0){
					$listmore = $listCheckCount;
				}
			}
		}

		$tableBody = "<tr class=".$class." onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='".$class."'\"><td>".$listDate."</td>
			<td>".$list100."</td>
			<td>".$list200."</td>
			<td>".$listmore."</td></tr>";
		echo $tableBody;
	
   	$i++;
	}
	//$tableEnd = "</table></td></tr>";
	//echo $tableEnd;

	$stmt->free_result();
	$stmt->close();
		
	while ($connection->next_result()) {
		//free each result.
		$result = $connection->use_result();
		if ($result instanceof mysqli_result) {
			$result->free();
		}						
	}
        
        
}
?>