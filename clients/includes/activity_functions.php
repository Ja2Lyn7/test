<?php

function verifyBlackList($emailAddress) {
    
    global $connection;
    //Call verifyBlacklist procedure
    $sql = "CALL verifyBlacklist(?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $p1 = $emailAddress;

    $stmt->bind_param('s', $p1);

    $stmt->execute();
    $stmt->bind_result($emailBlacklist);
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
   
    return $emailBlacklist;
}

function activityCountById($startDate,$endDate,$value,$searchBy,$id){

	global $connection;
	//Call activityCountById
   	$sql = "CALL activityCountById(?,?,?,?,?)";  
    
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('ssssi', $p1, $p2, $p3, $p4, $p5);
    $p1 = $startDate;
    $p2 = $endDate;
    $p3 = $value;
    $p4 = $searchBy;
	$p5 = $id;

    $stmt->execute();
    $stmt->bind_result($activityCount);

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

    return $activityCount;
}

function activitySearchById($startDate,$endDate,$start,$limit,$value,$searchBy,$id) {

    global $connection;

    $sql = "CALL activitySearchById(?,?,?,?,?,?,?)"; 
            
	$stmt = $connection->prepare($sql);
	if($connection->errno){
		die($connection->errno."::".$connection->error);
	}

	$p1 = $startDate;
	$p2 = $endDate;
	$p3 = $start;
	$p4 = $limit;
	$p5 = $value;
	$p6 = $searchBy;
	$p7 = $id;

	$stmt->bind_param('ssiissi', $p1, $p2, $p3, $p4, $p5, $p6, $p7);

	$stmt->execute();
	$stmt->bind_result($activityId,$fulfillmentNo,$status,$amount,$checkNo,$trackingNo,$error,$timestamp);
	
	echo '<table width="100%" cellspacing="0" cellpadding="5" align="center" border="0">
	<tr>
    	<td class="tableHeader">Activity id</td>
    	<td class="tableHeader">Fulfillment no.</td>
    	<td class="tableHeader" align="center">Status</td>
    	<td class="tableHeader">Amount</td>
		<td class="tableHeader">Check no</td>
    	<td class="tableHeader">Tracking no.</td>
    	<td class="tableHeader">Error</td>
    	<td class="tableHeader">&nbsp;</td>
	</tr>';
	
	//Get query results
    $i=0;
	while($stmt->fetch()){
        
		if($status == '1'){ 
			$image = 'approved'; 
		}elseif($status == '0'){   
			$image = 'declined';
		} else{
			$image = 'pending';
		}
		
		$timestamp = strtotime($timestamp);
		$timestamp = date('d/m/Y H:i', $timestamp);
		
		$lastRow = "<td class='tableBorder'>
			<form name=\"transactionDetails\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">
			<input type=\"image\" src=\"../../../../images/details.jpg\" name=\"submit\" value=\"submit\">
			<input type=\"hidden\" name=\"activityId\" value=\"".$activityId."\">
            <input type=\"hidden\" name=\"pg\" value=\"detail\">    
			<input type=\"hidden\" name=\"postDetails\" value=\"yes\">
			</form>
			</td>";
	
		$tableBody = "<tr class=tableBorder onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='tableBorder'\"><td class='tableBorder'>".$activityId."</td>
			<td class='tableBorder'>".$fulfillmentNo."</td>
			<td class='tableBorder' align='center'><img src=\"../../../../images/".$image.".jpg\" border='0' /></td>
			<td class='tableBorder'>$".$amount."</td>
			<td class='tableBorder'>".$checkNo."</td>
			<td class='tableBorder'>".$trackingNo."</td>
			<td class='tableBorder'>".$error."</td>
			".$lastRow."</tr>";
		echo $tableBody;
	
   		$i++;
	}
	$tableEnd = "</table>";
	echo $tableEnd;

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

function getActivityDetails($activityId) {
    
    global $connection;

    //Call getActivityDetails procedure
    $sql = "CALL getActivityDetails(?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }
	
    $p1 = $activityId;
    $stmt->bind_param('i', $p1);
    
    $stmt->execute();
    $stmt->bind_result($amount,$trackingNo,$status,$error,$timestamp,$fulfillmentNo,$firstname,$middleInitial,$lastname,$streetAddress,$apt,$city,$state,$postalcode,$country,$emailAddress,$phone,$ipAddress,$checkNo);

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
    
    $record=array();
	
    $record['activityId']		= $activityId;
	$record['amount']			= $amount;
    $record['trackingNo']		= $trackingNo;
    $record['status']			= $status;
    $record['error']				= $error;
    $record['timestamp']		= $timestamp;
    $record['fulfillmentNo']	= $fulfillmentNo;    
    $record['firstname']		= $firstname;
    $record['middleInitial']	= $middleInitial;
    $record['lastname']			= $lastname;
    $record['streetAddress']	= $streetAddress;
    $record['apt']				= $apt;
    $record['city']				= $city;
    $record['state']				= $state;
    $record['postalcode']		= $postalcode;
    $record['country']			= $country;
    $record['emailAddress']		= $emailAddress;
    $record['phone']				= $phone;
    $record['ipAddress']		= $ipAddress;
	$record['checkNo']			= $checkNo;
    return $record;
}

function getBatchDetails($activityId) {
    
    global $connection;

    //Call getBatchDetails procedure
    $sql = "CALL getBatchDetails(?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }
	
    $p1 = $activityId;
    $stmt->bind_param('i', $p1);
    
    $stmt->execute();
    $stmt->bind_result($batchId,$accepted,$processed,$completed);

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
    
    $batch=array();
	
    $batch['batchId']	= $batchId;
	$batch['accepted']	= $accepted;
    $batch['processed']	= $processed;
    $batch['completed']	= $completed;   
   
    return $batch;
}

function emailHistoryCount($emailAddress) {
	
    global $connection;
    
    $sql = "CALL emailHistoryCount(?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('s', $p1);
    $p1 = $emailAddress;
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

function getEmailHistory($emailAddress,$start,$limit) {

    global $connection;
    
    //Call getEmailHistory procedure
    $sql = "CALL getEmailHistory(?,?,?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('sii', $p1,$p2,$p3);
    $p1 = $emailAddress;
    $p2=$start;
    $p3=$limit;    
    $stmt->execute();
    $stmt->bind_result($activityId,$fulfillmentNo,$status,$amount,$checkNo,$trackingNo,$timestamp);

    //Get query results
    while($stmt->fetch()){
            
            if($status == 1){ 
                $image = 'approved';
            }elseif($status == 0){  
					$image = 'declined';
            }else{
                $image = 'pending';
            }

            $timestamp = strtotime($timestamp);
            $timestamp = date('m/d/Y g:ia', $timestamp);

            $tableBody = "<tr class='tableBorder' onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='tableBorder'\"><td class='tableBorder'>".$activityId."</td>
                    <td class='tableBorder'>".$fulfillmentNo."</td>
                    <td class='tableBorder'><img src=\"../../../../images/".$image.".jpg\" /></td>
					<td class='tableBorder'>".$amount."</td>
                    <td class='tableBorder'>".$checkNo."</td>
                    <td class='tableBorder'>".$trackingNo."</td>
                    <td class='tableBorder'>".$timestamp."</td></tr>";
            echo $tableBody;
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
}
?>