<?php
function pay2CheckClientsDDL($pay2checkClient) {
    
    global $connection;
    
    //Call getPay2CheckClients procedure
    $sql = "CALL getPay2CheckClients()";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->execute();
    $stmt->bind_result($listLoginId,$listUserName,$listCompanyName);

?>
<select name="pay2checkClient">
<option value=""></option>
<?php //Get query results
    while($stmt->fetch()){
            $selected = '';
            if($listLoginId == $pay2checkClient){ $selected = "selected=selected"; }
            echo "<option value=\"".$listLoginId."\" ".$selected.">".$listCompanyName . "--" .$listUserName . "</option>";
    }					

?>
</select>
<?php $stmt->free_result();
    $stmt->close();

    while ($connection->next_result()) {
            //free each result.
            $result = $connection->use_result();
            if ($result instanceof mysqli_result) {
                    $result->free();
            }			
    }    
}

function updateCheckFinancials($pay2checkClient,$doCredit,$creditAmount,$creditReason,$doDebit,$debitAmount,$debitReason) {

    myLog("updateCheckFinancials() pay2checkClient=$pay2checkClient");
    global $connection;
    
    $sql = "CALL updateCheckFinancials(?,?,?,?,?,?,?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('iidsids', $p1, $p2, $p3, $p4, $p5, $p6, $p7);
    $p1 = $pay2checkClient;
    $p2 = $doCredit;
    $p3 = $creditAmount;
    $p4 = $creditReason;
    $p5 = $doDebit;
    $p6 = $debitAmount;
    $p7 = $debitReason;
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

    
    myLog("updateCheckFinancials() ends");
}

function getCheckRecordsForReview($batchId){

    myLog("getCheckRecordsForReview() batchId=$batchId");
    $i=0;
    $recordCount = '';
    $totalCredit = '';
    $recordsArray = '';
    
    global $connection;

    //Call getChecRecordsForReview procedure
    $sql = "CALL getCheckRecordsForReview(?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('i', $p1);
    $p1 = $batchId;
    $stmt->execute();
    $stmt->bind_result($listRecordId,$listMerchantRefNo,$listFirstName,$listMiddleInitial,$listLastName,$listStreetAddress,$listAddress2,$listCity,$listState,$listPostalcode,$listCountry,$listEmailAddress,$listPhone,$listAmount);
    //Get query results
    while($stmt->fetch()){
            if($i % 2 == 0){ $class = "even"; } else{ $class = "odd"; }

            $listCustomerName = $listFirstName." ".$listMiddleInitial." ".$listLastName;
            $listCustomerAddress = $listStreetAddress." ".$listAddress2." ".$listCity.", ".$listState." ".$listPostalcode." ".$listCountry;

            $displayAmount = number_format($listAmount,2);

            $tableBody = "<tr>
                    <td><input type=\"checkbox\" name=\"approve_".$listRecordId."\" value=\"1\" checked=\"checked\"></td>
            <td>".$listMerchantRefNo."</td>
            <td>".$listCustomerName."</td>
            <td>".$listCustomerAddress."</td>
            <td>".$listEmailAddress."</td>
            <td>".$listPhone."</td>
            <td>$".$displayAmount."</td>
        </tr>";
            echo $tableBody;

            $recordCount = $recordCount+1;
            $totalCredit = $totalCredit+$listAmount;
            $recordsArray = $recordsArray.$listRecordId.",";

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
    
    myLog("getCheckRecordsForReview() recordCount=$recordCount totalCredit=$totalCredit");
    return "$recordsArray|$recordCount|$totalCredit";

}

function getCheckRecordsProcessed($batchId) {
    
    
    myLog("getCheckRecordsProcessed() batchId=$batchId");
    $i=0;
    $recordCount = '';
    $totalCredit = '';
    $recordsArray = '';

    global $connection;
    //Call getCheckRecordsProcessed procedure
    $sql = "CALL getCheckRecordsProcessed(?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('i', $p1);
    $p1 = $batchId;
    $stmt->execute();
    $stmt->bind_result($listRecordId,$listMerchantRefNo,$listFirstName,$listMiddleInitial,$listLastName,$listStreetAddress,$listAddress2,$listCity,$listState,$listPostalcode,$listCountry,$listEmailAddress,$listPhone,$listAmount,$listTransactionId);
    //Get query results
    while($stmt->fetch()){
            if($i % 2 == 0){ $class = "even"; } else{ $class = "odd"; }

            $listCustomerName = $listFirstName." ".$listMiddleInitial." ".$listLastName;
            $listCustomerAddress = $listStreetAddress." ".$listAddress2." ".$listCity.", ".$listState." ".$listPostalcode." ".$listCountry;

            $displayAmount = number_format($listAmount,2);

            $tableBody = "<tr>
                    <td><input type=\"checkbox\" name=\"approve_".$listRecordId."\" value=\"1\" checked=\"checked\"></td>
            <td>".$listRecordId."</td>
            <td>".$listCustomerName."</td>
            <td>".$listCustomerAddress."</td>
            <td>".$listEmailAddress."</td>
            <td>".$listPhone."</td>
            <td>$".$displayAmount."</td>
			<td><input type=\"text\" name=\"tId_".$listRecordId."\" value=\"".$listTransactionId."\"></td>
        </tr>";
            echo $tableBody;

            $recordCount = $recordCount+1;
            $totalCredit = $totalCredit+$listAmount;
            $recordsArray = $recordsArray.$listRecordId.",";

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
    
    myLog("getCheckRecordsPorcessed() recordCount=$recordCount totalCredit=$totalCredit");
    return "$recordsArray|$recordCount|$totalCredit";
}

function getCheckBatchesForReview(){
    
    global $connection;
    //Table header
    $tableHeader = "<table width='500' cellspacing='0' cellpadding='5' align='left' border='0' style='border:1px solid #555555;'>
            <tr><td class='searchHeader' colspan='4'>Batches for review</td></tr>
			<tr><td class='header' style='border-right:1px solid #ffffff;width:60px;'>Batch Id</td>
            <td class='header' style='border-right:1px solid #ffffff;'>Company name</td>
            <td class='header' style='border-right:1px solid #ffffff;width:125px;'>DateTime</td>
            <td class='header' style='width:75px;' align='center'>&nbsp;</td>
            </tr>";
    echo $tableHeader;

    //Call getCheckBatchesForReview procedure
    $sql = "CALL getCheckBatchesForReview()";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->execute();
    $stmt->bind_result($listBatchId,$listCompanyName,$listDateTimeStamp);

    //Get query results
    $i=0;
    while($stmt->fetch()){
            if($i % 2 == 0){ $class = "even"; } else{ $class = "odd"; }

            $listDateTimeStamp = strtotime($listDateTimeStamp);
            $listDateTimeStamp = date('m/d/Y g:ia', $listDateTimeStamp);

            $tableBody = "<tr class=".$class." onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='".$class."'\">
                    <td style='border-right:1px solid #555555;'>".$listBatchId."</td>
                    <td style='border-right:1px solid #555555;'>".$listCompanyName."</td>
                    <td style='border-right:1px solid #555555;'>".$listDateTimeStamp."</td>
                    <td align='center'><form name=\"postReview\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">
                    <input type=\"hidden\" name=\"batchId\" value=\"".$listBatchId."\">
                    <input type=\"hidden\" name=\"postReview\" value=\"yes\">
                    <input type=\"hidden\" name=\"pg\" value=\"admin\">                    
                    <input type=\"image\" src=\"images/button_review.gif\" value=\"submit\" name=\"submit\">
                    </form></td>
                    </tr>";
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

function getCheckBatchesSent() {
    
    myLog("getCheckBatchesSent() start");
    global $connection;
    
    //Table header
    $tableHeader = "<table width='500' cellspacing='0' cellpadding='5' align='left' border='0' style='border:1px solid #555555;'>
            <tr><td class='searchHeader' colspan='4'>Batches sent to processor</td></tr>
			<tr><td class='header' style='border-right:1px solid #ffffff;width:60px;'>Batch Id</td>
            <td class='header' style='border-right:1px solid #ffffff;'>Company name</td>
            <td class='header' style='border-right:1px solid #ffffff;width:125px;'>DateTime</td>
            <td class='header' style='width:75px;' align='center'>&nbsp;</td>
            </tr>";
    echo $tableHeader;

    $i=0;

    //Call getCheckBatchesSent procedure
    $sql = "CALL getCheckBatchesSent()";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->execute();
    $stmt->bind_result($listBatchId,$listCompanyName,$listDateTimeStamp);

    //Get query results
    while($stmt->fetch()){
            if($i % 2 == 0){ $class = "even"; } else{ $class = "odd"; }
			
            $listDateTimeStamp = strtotime($listDateTimeStamp);
            $listDateTimeStamp = date('m/d/Y g:ia', $listDateTimeStamp);

            $tableBody = "<tr class=".$class." onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='".$class."'\">
                    <td style='border-right:1px solid #555555;'>".$listBatchId."</td>
                    <td style='border-right:1px solid #555555;'>".$listCompanyName."</td>
                    <td style='border-right:1px solid #555555;'>".$listDateTimeStamp."</td>
                    <td align='center'><form name=\"postProcess\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">
                    <input type=\"hidden\" name=\"batchId\" value=\"".$listBatchId."\">
                    <input type=\"hidden\" name=\"postProcess\" value=\"yes\">
                    <input type=\"hidden\" name=\"pg\" value=\"admin\">
                    <input type=\"image\" src=\"images/button_review.gif\" value=\"submit\" name=\"submit\">
                    </form></td>
                    </tr>";
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

function updateCheckBatchApproval($recordId,$approveCheckbox) {

    myLog("updateCheckBatchApproval() recordId=$recordId");    
    global $connection;

    //Call updateCheckBatchApproval procedure
    $sql = "CALL updateCheckBatchApproval(?,?)";
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
    
    myLog("updateCheckBatchApproval() ends");
}

function updateCheckBatchComplete($recordId,$transactionId,$approveCheckbox) {
    
    myLog("updateBatchComplete recordId=$recordId");
    global $connection;
    
    //Call updateCheckBatchComplete procedure
    $sql = "CALL updateCheckBatchComplete(?,?,?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('iis', $p1, $p2, $p3);
    $p1 = $recordId;
    $p2 = $approveCheckbox;
	$p3 = $transactionId;

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

    myLog("updateCheckBatchComplete ends");
}

function insertTransactionIds($checkId,$transactionId) {

    global $connection;
    myLog("insertTransactionIds: checkId=$checkId");
  

     //Call insertTransactionIds procedure
    $sql = "CALL insertTransactionIds(?,?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
	die($connection->errno."::".$connection->error);
    }

    $p1 = $checkId;
    $p2 = $transactionId;
	
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