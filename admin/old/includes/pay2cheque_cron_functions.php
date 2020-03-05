<?php
function getCheckBatchIds() {

    global $connection;
    myLog("getCheckBatchIds()");

    $batchArray = '';

    //Call getCheckBatchIds procedure
    $sql = "CALL getCheckBatchIds()";

    $stmt = $connection->prepare($sql);

    if($connection->errno){
            $errmsg=$connection->errno."::".$connection->error;
            myLog("getCheckBatchIds() errmsg=$errmsg","ERROR");
            die($errmsg);
    }

    $stmt->execute();
    $stmt->bind_result($batchIds);
    //Get query results

    while($stmt->fetch()){
            $batchArray = $batchArray.$batchIds.",";
    }

    $stmt->free_result();
    $stmt->close();
		
    while ($connection->next_result()) {
            //free each result.
            $result = $connection->use_result();
            if ($result instanceof mysqli_result) {
    		$result->free();
            }
    }

    $batchArray = rtrim($batchArray,",");

    myLog("getCheckBatchIds() batchArray=" . $batchArray);
    return $batchArray;

}

function writeOneFile($batchId,$fh,$row,$creditTotal){

    global $connection;
    myLog("writeOneFile() batchId=$batchId");
    
    $data=array();
    
    //Call getCheckRecords procedure
    $sql = "CALL getCheckRecords(?)";
    $stmt = $connection->prepare($sql);

    if($connection->errno){
            $errmsg=$connection->errno."::".$connection->error;
            myLog("writeOneFile() errmsg=$errmsg","ERROR");
            die("writeOneFile() errmsg=$errmsg");
    }

    $stmt->bind_param('i', $p1);
    $p1 = $batchId;

    $stmt->execute();
    $stmt->bind_result($recordId,$firstname,$lastname,$streetAddress,$address2,$city,$state,$postalcode,$creditAmount,$memo,$note1,$note2,$checkId);
    
    //Get query results

    while($stmt->fetch()){
    	$row = $row+1;
		
		$payee = $firstname." ".$lastname;
		$payee = substr($payee,0,100);
		
		$date = date('m/d/Y');
		
		if($address2 == ''){
			$customerAddress = ",,".$streetAddress;
		}else{
			$customerAddress = $streetAddress.",".$address2;
		}
		
		$address3 = $city." ".$state." ".$postalcode;
		
		$memo = "000000".$recordId;
		$memo = substr($memo,-6);
		
		$note1 = '';
		$note2 = '';
		
		$checkId = "000000".$checkId;
		$checkId = substr($checkId,-6);
		
		$creditTotal = $creditTotal+$creditAmount;
		$line =  $payee.",".$creditAmount.",".$checkId.",".$date.",".$memo.",".$note1.",".$note2.",".$customerAddress.",".$address3.",,";
    	
		$fwrite=fwrite($fh, "$line\r\n");
		//echo $line;

        if($fwrite==FALSE) {
        	myLog("writeOneFile() unable to write OneFile","ERROR");
            die("unable to write OneFile");
        }
    }

    $stmt->free_result();
    $stmt->close();

    while ($connection->next_result()) {
        //free each result.
            $result = $connection->use_result();
            if ($result instanceof mysqli_result) {
    		$result->free();
            }
    }
  

    $recordCounter=$row;
    myLog("writeOneFile() Total Records $recordCounter");

    $data['creditTotal']=$creditTotal;
    $data['recordCounter']=$recordCounter;

    myLog("writeOneFile() data=" . var_export($data,TRUE));
    return $data;
    
}

function writeOneShipFile($batchId,$fh,$row,$creditTotal){

    global $connection;
    myLog("writeOneFile() batchId=$batchId");
    
    $data=array();
    
    //Call getCheckRecords procedure
    $sql = "CALL getCheckRecords(?)";
    $stmt = $connection->prepare($sql);

    if($connection->errno){
            $errmsg=$connection->errno."::".$connection->error;
            myLog("writeOneFile() errmsg=$errmsg","ERROR");
            die("writeOneFile() errmsg=$errmsg");
    }

    $stmt->bind_param('i', $p1);
    $p1 = $batchId;

    $stmt->execute();
    $stmt->bind_result($recordId,$firstname,$lastname,$streetAddress,$address2,$city,$state,$postalcode,$creditAmount,$memo,$note1,$note2,$checkId);
    
    //Get query results

    while($stmt->fetch()){
    	$row = $row+1;
		
		$payee = $firstname." ".$lastname;
		$payee = substr($payee,0,100);
		
		$date = date('m/d/Y');
		
		$memo = "000000".$recordId;
		$memo = substr($memo,-6);
		$note1 = $memo;
		
		$checkId = "000000".$checkId;
		$checkId = substr($checkId,-6);
		
		if($address2 <> ''){ $address2 = '"'.$address2.'"'; }
		
		$creditTotal = $creditTotal+$creditAmount;
		$line =  '"'.$payee.'",1,'.$checkId.',"'.$date.'",'.$memo.','.$note1.','.$note2.',"'.$streetAddress.'",'.$address2.',"'.$city.'","'.$state.'",'.$postalcode;
    	
		$fwrite=fwrite($fh, "$line\r\n");
		//echo $line;

        if($fwrite==FALSE) {
        	myLog("writeOneFile() unable to write OneFile","ERROR");
            die("unable to write OneFile");
        }
    }

    $stmt->free_result();
    $stmt->close();

    while ($connection->next_result()) {
        //free each result.
            $result = $connection->use_result();
            if ($result instanceof mysqli_result) {
    		$result->free();
            }
    }
  

    $recordCounter=$row;
    myLog("writeOneFile() Total Records $recordCounter");

    $data['creditTotal']=$creditTotal;
    $data['recordCounter']=$recordCounter;

    myLog("writeOneFile() data=" . var_export($data,TRUE));
    return $data;
    
}

function getCheckBatchAccountInfo($batchId) {

    global $connection;
    myLog("getCheckBatchAccount()");

    //Call getCheckBatchAccount procedure
    $sql = "CALL getCheckBatchAccount(?)";
    $stmt = $connection->prepare($sql);

    if($connection->errno){
            $errmsg=$connection->errno."::".$connection->error;
            myLog("getCheckBatchAccount() errmsg=$errmsg","ERROR");
            die($errmsg);
    }

    $accountInfo=array();
    $p1 = $batchId;
    $stmt->bind_param('i', $p1);

    $stmt->execute();
    $stmt->bind_result($accountId,$accountName);

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

    myLog("getCheckBatchAccount() accountId=$accountId, Account Name:$accountName");

    $accountInfo['id']=$accountId;
    $accountInfo['name']=$accountName;

    return  $accountInfo;

}

function updateCheckBatchRecord($batchId) {

    global $connection;
    myLog("updateCheckBatchRecord() batchId=$batchId");

    $sql = "CALL updateCheckBatchRecord(?)";
    $stmt = $connection->prepare($sql);

    if($connection->errno){
            $errmsg=$connection->errno."::".$connection->error;
            myLog("updateCheckBatchRecord() errmsg=$errmsg","ERROR");
            die($errmsg);
    }

    $p1 = $batchId;
    $stmt->bind_param('i', $p1);

    $stmt->execute();

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

    myLog("updateCheckBatchRecord() end");
}
?>
