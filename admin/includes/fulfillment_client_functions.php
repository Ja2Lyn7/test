<?php
function fulfillmentCount($selectedloginid) {
    global $connection;
    
    //Call fulfillmentCount procedure
    $sql = "CALL fulfillmentCount(?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('i', $p1);
    $p1 = $selectedloginid;
    $stmt->execute();
    $stmt->bind_result($count);

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
    
    return $count;
}

function getFinancialListing($start,$limit,$selectedloginid) {
   
    global $connection; 
        
	//Call getFinancialListing procedure
	$sql = "CALL getFinancialListing(?,?,?)";
	$stmt = $connection->prepare($sql);
	if($connection->errno){
		die($connection->errno."::".$connection->error);
	}

	$stmt->bind_param('iii', $p1, $p2, $p3);
	$p1 = $start;
	$p2 = $limit;
	$p3 = $selectedloginid;
	
	$stmt->execute();
	$stmt->bind_result($id,$description,$amount,$amountType,$remainingBalance,$timeStamp,$companyName);
	//Get query results
	while($stmt->fetch()){
        	
		$timeStamp = strtotime($timeStamp);
		$timeStamp = date('d/m/Y H:i', $timeStamp);
		
		if($amountType == 2){ $amount = "(".number_format($amount,2).")"; } else{ $amount = number_format($amount,2); }

		$tableBody = "<tr class=tableBorder onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='tableBorder'\">
			<td class='tableBorder'>".$companyName."</td>
			<td class='tableBorder'>".$timeStamp."</td>
			<td class='tableBorder'>".$description."</td>
			<td class='tableBorder'>".$amount."</td>
			<td align=\"right\" class='tableBorder'><b>$".number_format($remainingBalance,2)."</b></td></tr>";
		echo $tableBody;
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

function validateCSVRecord($data,$selectedloginid) {

    $fulfillmentNo = $data[0];

    $customerName 		= $data[1]." ".$data[2]." ".$data[3];
    $customerAddress 	= $data[4]." ".$data[5]." ".$data[6].", ".$data[7]." ".$data[8]." ".$data[9];
    $emailAddress 		= $data[10];
    $phoneNumber 		= $data[11];
    $creditAmount 		= $data[12];
    $creditAmount 		= str_replace("$","",$creditAmount);
    $creditAmount 		= str_replace(",","",$creditAmount);


    $firstname		=$data[1];
    $middleInitial	=$data[2];
    $lastname		=$data[3];
    $streetAddress	=$data[5];
    $city			=$data[6];
    $state			=$data[7];
    $postalcode		=$data[8];
    $country		=$data[9];
	
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

    if(strlen($firstname) > $aLength){ $errmsg1=$errmsg1."Invalid Firstname Length or Type";}
    if(strlen($middleInitial) > $bLength){ $errmsg1=$errmsg1."Invalid MiddleInitial Length or Type";}
    if(strlen($lastname) > $aLength){ $errmsg1=$errmsg1."Invalid lastname Length or Type";}

    if(strlen($streetAddress) > $cLength){ $errmsg1=$errmsg1."Invalid streetAddress Length or Type";}
    if(strlen($city) > $dLength){ $errmsg1=$errmsg1."Invalid City Length or Type";}
    if(strlen($state) > $bLength){ $errmsg1=$errmsg1."Invalid State Length or Type"; }
    if(strlen($postalcode) > $eLength){ $errmsg1=$errmsg1."Invalid postalcode Length or Type";}
    if(strlen($country) > $fLength){ $errmsg1=$errmsg1."Invalid country Length or Type"; }
    if(strlen($phoneNumber) > $bLength){ $errmsg1=$errmsg1."Invalid phoneNumber Length or Type"; }
    if(strlen($emailAddress) > $gLength){ $errmsg1=$errmsg1."Invalid EmailAddress Length or Type";}
    if(strlen($fulfillmentNo) > $bLength){ $errmsg1=$errmsg1."Invalid fulfillmentNo Length or Type"; }

    if( (strlen($creditAmount) > $iLength) || (is_numeric($creditAmount == FALSE)) ){ $errmsg1=$errmsg1."Invalid creditAmount Length or Type";}


    if(is_numeric($creditAmount == FALSE)){ $errmsg1=$errmsg1."Amount is not Numeric";}

    if($errmsg1 <> '') {
       $ok = 0;
       $badRecord = 1;
    }

   $isFulfillmentNoDuplicated=isFulfillmentNoDuplicated($fulfillmentNo,$selectedloginid);
   if($isFulfillmentNoDuplicated) {
       $ok = 0;
       $badRecord = 1;
       $errmsg1=$errmsg1."FulfillmentNo already exists";
   }

   //validate email

   if ( filter_var($emailAddress, FILTER_VALIDATE_EMAIL)==FALSE) {
   	$ok = 0;
	$badRecord = 1;
    $errmsg1=$errmsg1 . "Invalid Email";
   }

   $emailBlacklist=verifyBlacklist($emailAddress);
          
   	if($emailBlacklist <> ''){
		$ok = 0;
        $badRecord = 1;
        $errmsg1= $errmsg1 . "<br>" . "Blacklisted Email";
    }

   return array($ok,$badRecord,$errmsg1);

}

function isFulfillmentNoDuplicated($fulfillmentNo,$LoginId) {
     global $connection;

    //Call isFulfillmentNoDuplicated procedure
    $sql = "CALL isFulfillmentNoDuplicated(?,?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }


    $stmt->bind_param('si', $p1, $p2);
    $p1 = $fulfillmentNo;
    $p2 = $LoginId;
	
    $stmt->execute();
    $stmt->bind_result($isFulfillmentNoDuplicated);
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


    return $isFulfillmentNoDuplicated;
}

function checkBalance($selectedloginid,$totalCredit,$transactionCount) {
    global $connection;

    //Call checkBalance procedure
    $sql = "CALL checkBalance(?,?,?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('idi', $p1, $p2, $p3);
    $p1 = $selectedloginid;
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
    return $balanceInfo;
}

function trackBatch($selectedloginid,$filename) {
     global $connection;

    //Call trackBatch procedure
    $sql = "CALL trackBatch(?,?)";
    $stmt = $connection->prepare($sql);

    if($connection->errno){
	die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('is', $p1, $p2);
    $p1 = $selectedloginid;
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

    return $batchId;
}

function insertBatch($selectedloginid,$data,$batchId) {

    global $connection;

     //Call insertBatch procedure
    $sql = "CALL insertBatch(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
	die($connection->errno."::".$connection->error);
    }

    $p1 = $data['fulfillmentNo'];
    $p2 = $selectedloginid;
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
    $p15 = $_SERVER['REMOTE_ADDR'];
    $p16 = $batchId;
	
    $stmt->bind_param('sisssssssssssdsi', $p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8, $p9, $p10, $p11, $p12, $p13, $p14, $p15, $p16);

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

    return $recordId;
}

function updateBalance($selectedloginid,$totalFulfillment,$row) {
    global $connection;

    //Call updateBalance procedure
    $sql = "CALL updateBalance(?,?,?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
	die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('idi', $p1, $p2, $p3);
    $p1 = $selectedloginid;
    $p2 = $totalFulfillment;
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

    return $newBalance;

}
?>