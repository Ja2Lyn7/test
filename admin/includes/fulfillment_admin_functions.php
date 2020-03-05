<?php
function debitAdjustment($selectedloginid,$debitAmount,$debitReason) {

    global $connection;
    
    $sql = "CALL debitAdjustment(?,?,?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('ids', $p1, $p2, $p3);
    $p1 = $selectedloginid;
    $p2 = $debitAmount;
    $p3 = $debitReason;
	
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

function creditAdjustment($selectedloginid,$creditAmount,$creditReason) {

    global $connection;
    
    $sql = "CALL creditAdjustment(?,?,?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('ids', $p1, $p2, $p3);
    $p1 = $selectedloginid;
    $p2 = $creditAmount;
    $p3 = $creditReason;
	
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

function getBatchesForReview(){
    
    global $connection;
    
	//Table header
    $tableHeader = "<table width='100%' cellspacing='0' cellpadding='0' align='left' border='0'>
			<tr><td class='tableHeader'>Batch Id</td>
            <td class='tableHeader'>Company name</td>
            <td class='tableHeader'>DateTime</td>
            <td class='tableHeader'>&nbsp;</td>
            </tr>";
    echo $tableHeader;

    //Call getBatchesForReview procedure
    $sql = "CALL getBatchesForReview()";
    $stmt = $connection->prepare($sql);

    $stmt->execute();
    $stmt->bind_result($batchId,$companyName,$timestamp);

    //Get query results
    $i=0;
    while($stmt->fetch()){

            $timestamp = strtotime($timestamp);
            $timestamp = date('m/d/Y g:ia', $timestamp);

            $tableBody = "<tr class=tableBorder onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='tableBorder'\">
                    <td class='tableBorder'>".$batchId."</td>
                    <td class='tableBorder'>".$companyName."</td>
                    <td class='tableBorder'>".$timestamp."</td>
                    <td align='center' class='tableBorder' valign='middle'><form name=\"postReview\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">
                    <input type=\"hidden\" name=\"batchId\" value=\"".$batchId."\">
                    <input type=\"hidden\" name=\"postReview\" value=\"yes\">
                    <input type=\"hidden\" name=\"pg\" value=\"review\">                    
                    <input type=\"image\" src=\"../../../../images/review.jpg\" value=\"submit\" name=\"submit\">
                    </form></td>
                    </tr>";
            echo $tableBody;

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

function getBatchesProcessed() {
    
    global $connection;
    
    //Table header
    $tableHeader = "<table width='100%' cellspacing='0' cellpadding='0' align='left' border='0'>
			<tr><td class='tableHeader'>Batch Id</td>
            <td class='tableHeader'>Company name</td>
            <td class='tableHeader'>DateTime</td>
            <td class='tableHeader'>&nbsp;</td>
            </tr>";
    echo $tableHeader;

    $i=0;

    //Call getBatchesProcessed procedure
    $sql = "CALL getBatchesProcessed()";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->execute();
    $stmt->bind_result($batchId,$companyName,$timestamp);

    //Get query results
    while($stmt->fetch()){
			
            $timestamp = strtotime($timestamp);
            $timestamp = date('m/d/Y g:ia', $timestamp);

            $tableBody = "<tr class='tableBorder' onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='tableBorder'\">
                    <td class='tableBorder'>".$batchId."</td>
                    <td class='tableBorder'>".$companyName."</td>
                    <td class='tableBorder'>".$timestamp."</td>
                    <td align='center' class='tableBorder'><form name=\"postProcess\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">
                    <input type=\"hidden\" name=\"batchId\" value=\"".$batchId."\">
                    <input type=\"hidden\" name=\"postProcess\" value=\"yes\">
                    <input type=\"hidden\" name=\"pg\" value=\"review\">
                    <input type=\"image\" src=\"../../../../images/review.jpg\" value=\"submit\" name=\"submit\">
                    </form></td>
                    </tr>";
            echo $tableBody;

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

function getRecordsForReview($batchId){

    $i=0;
    $recordCount = '';
    $totalCredit = '';
    $recordsArray = '';
    
    global $connection;

    //Call getRecordsForReview procedure
    $sql = "CALL getRecordsForReview(?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('i', $p1);
    $p1 = $batchId;
    $stmt->execute();
    $stmt->bind_result($recordId,$fulfillmentNo,$firstname,$middleInitial,$lastname,$streetAddress,$apt,$city,$state,$postalcode,$country,$emailAddress,$phone,$amount);
    //Get query results
    while($stmt->fetch()){
          
            $customerName = $firstname." ".$middleInitial." ".$lastname;
            $customerAddress = $streetAddress." ".$apt."<br>".$city.", ".$state." ".$postalcode." ".$country;

            $displayAmount = number_format($amount,2);

            $tableBody = "<tr>
                    <td class='tableBorder'><input type=\"checkbox\" name=\"approve_".$recordId."\" value=\"1\" checked=\"checked\"></td>
            <td class='tableBorder'>".$fulfillmentNo."</td>
            <td class='tableBorder'>".$customerName."</td>
            <td class='tableBorder'>".$customerAddress."</td>
            <td class='tableBorder'>".$emailAddress."</td>
            <td class='tableBorder'>".$phone."</td>
            <td class='tableBorder'>$".$displayAmount."</td>
        </tr>";
            echo $tableBody;

            $recordCount = $recordCount+1;
            $totalCredit = $totalCredit+$amount;
            $recordsArray = $recordsArray.$recordId.",";

            $i++;
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
    $recordsArray = rtrim($recordsArray,",");
    
    return "$recordsArray|$recordCount|$totalCredit";

}

function getRecordsProcessed($batchId) {
    
    $i=0;
    $recordCount = '';
    $totalCredit = '';
    $recordsArray = '';

    global $connection;
    //Call getRecordsProcessed procedure
    $sql = "CALL getRecordsProcessed(?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('i', $p1);
    $p1 = $batchId;
	
    $stmt->execute();
    $stmt->bind_result($recordId,$fulfillmentNo,$firstname,$middleInitial,$lastname,$streetAddress,$apt,$city,$state,$postalcode,$country,$emailAddress,$phone,$amount,$trackingNo);
    //Get query results
    while($stmt->fetch()){

            $customerName = $firstname." ".$middleInitial." ".$lastname;
            $customerAddress = $streetAddress." ".$apt."<br>".$city.", ".$state." ".$postalcode." ".$country;

            $displayAmount = number_format($amount,2);

            $tableBody = "<tr>
                    <td class='tableBorder'><input type=\"checkbox\" name=\"approve_".$recordId."\" value=\"1\" checked=\"checked\"></td>
            <td class='tableBorder'>".$recordId."</td>
            <td class='tableBorder'>".$customerName."</td>
            <td class='tableBorder'>".$customerAddress."</td>
            <td class='tableBorder'>$".$displayAmount."</td>
			<td class='tableBorder'><input type=\"text\" name=\"tId_".$recordId."\" value=\"".$trackingNo."\"></td>
        </tr>";
            echo $tableBody;

            $recordCount = $recordCount+1;
            $totalCredit = $totalCredit+$amount;
            $recordsArray = $recordsArray.$recordId.",";

            $i++;
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
    $recordsArray = rtrim($recordsArray,",");   
    
    return "$recordsArray|$recordCount|$totalCredit";
}

function updateBatchAccepted($recordId,$approveCheckbox) {
   
    global $connection;

    //Call updateBatchAccepted procedure
    $sql = "CALL updateBatchAccepted(?,?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('ii', $p1, $p2);
    $p1 = $recordId;
    $p2 = $approveCheckbox;

    $stmt->execute();

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
    
}

function updateBatchCompleted($recordId,$trackingNo,$approveCheckbox) {
    
    global $connection;
    
    //Call updateBatchCompleted procedure
    $sql = "CALL updateBatchCompleted(?,?,?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('iis', $p1, $p2, $p3);
    $p1 = $recordId;
    $p2 = $approveCheckbox;
	$p3 = $trackingNo;

    $stmt->execute();

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
}

function insertTrackingNumbers($id,$trackingNo) {

    global $connection;

    //Call insertTrackingNumbers procedure
    $sql = "CALL insertTrackingNumbers(?,?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
	die($connection->errno."::".$connection->error);
    }

    $p1 = $id;
    $p2 = $trackingNo;
	
    $stmt->bind_param('is', $p1, $p2);

    $stmt->execute();

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
}
?>