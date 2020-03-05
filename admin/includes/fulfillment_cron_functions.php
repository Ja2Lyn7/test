<?php
function getBatchIds() {

    global $connection;

    $batchArray = '';

    //Call getBatchIds procedure
    $sql = "CALL getBatchIds()";

    $stmt = $connection->prepare($sql);

    if($connection->errno){
            die($connection->errno."::".$connection->error);
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
    return $batchArray;

}

function writeOneFile($batchId,$fh,$row,$creditTotal){

    global $connection;
    
    $data=array();
    
    //Call getRecords procedure
    $sql = "CALL getRecords(?)";
    $stmt = $connection->prepare($sql);

    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('i', $p1);
    $p1 = $batchId;

    $stmt->execute();
    $stmt->bind_result($recordId,$firstname,$lastname,$streetAddress,$address2,$city,$state,$postalcode,$creditAmount,$checkNo);
    
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
		
		$checkNo = "000000".$checkNo;
		$checkNo = substr($checkNo,-6);
		
		$creditTotal = $creditTotal+$creditAmount;
		$line =  $payee.",".$creditAmount.",".$checkNo.",".$date.",".$memo.",".$note1.",".$note2.",".$customerAddress.",".$address3.",,";
    	
		$fwrite=fwrite($fh, "$line\r\n");
		//echo $line;

        if($fwrite==FALSE) {
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

    $data['creditTotal']=$creditTotal;
    $data['recordCounter']=$recordCounter;

    return $data;
    
}

function writeOneShipFile($batchId,$fh,$row,$creditTotal){

    global $connection;
    
    $data=array();
    
    //Call getRecords procedure
    $sql = "CALL getRecords(?)";
    $stmt = $connection->prepare($sql);

    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('i', $p1);
    $p1 = $batchId;

    $stmt->execute();
    $stmt->bind_result($recordId,$firstname,$lastname,$streetAddress,$address2,$city,$state,$postalcode,$creditAmount,$checkNo);
    
    //Get query results

    while($stmt->fetch()){
    	$row = $row+1;
		
		$payee = $firstname." ".$lastname;
		$payee = substr($payee,0,100);
		
		$date = date('m/d/Y');
		
		$memo = "000000".$recordId;
		$memo = substr($memo,-6);
		$note1 = $memo;
		$note2 = $memo;
		
		$checkNo = "000000".$checkNo;
		$checkNo = substr($checkNo,-6);
		
		if($address2 <> ''){ $address2 = '"'.$address2.'"'; }
		
		$creditTotal = $creditTotal+$creditAmount;
		$line =  '"'.$payee.'",1,'.$checkNo.',"'.$date.'",'.$memo.','.$note1.','.$note2.',"'.$streetAddress.'",'.$address2.',"'.$city.'","'.$state.'",'.$postalcode;
    	
		$fwrite=fwrite($fh, "$line\r\n");
		//echo $line;

        if($fwrite==FALSE) {
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

    $data['creditTotal']=$creditTotal;
    $data['recordCounter']=$recordCounter;

    return $data;
    
}

function updateBatchRecord($batchId) {

    global $connection;

    $sql = "CALL updateBatchRecord(?)";
    $stmt = $connection->prepare($sql);

    if($connection->errno){
            die($connection->errno."::".$connection->error);
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

}
?>
