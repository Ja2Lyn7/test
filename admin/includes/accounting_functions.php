<?php
function insertFulfillmentCosts($trackingNo,$cost) {

    global $connection;

    //Call insertFulfillmentCosts procedure
    $sql = "CALL insertFulfillmentCosts(?,?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
	die($connection->errno."::".$connection->error);
    }

    $p1 = $trackingNo;
	$p2 = $cost;
	
    $stmt->bind_param('sd', $p1, $p2);

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

function accountingCount($startDate,$endDate,$selectedloginid){

	global $connection;
	//Call accountingCount
   	$sql = "CALL accountingCount(?,?,?)";  
    
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('ssi', $p1, $p2, $p3);
    $p1 = $startDate;
    $p2 = $endDate;
    $p3 = $selectedloginid;

    $stmt->execute();
    $stmt->bind_result($accountingCount);

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

    return $accountingCount;
}

function accountingSearch($startDate,$endDate,$selectedloginid) {

    global $connection;

    $sql = "CALL accountingSearch(?,?,?)"; 
            
	$stmt = $connection->prepare($sql);
	if($connection->errno){
		die($connection->errno."::".$connection->error);
	}

	$p1 = $startDate;
	$p2 = $endDate;
	$p3 = $selectedloginid;
    
	$stmt->bind_param('ssi', $p1, $p2, $p3);

	$stmt->execute();
	$stmt->bind_result($activityId, $checkNo, $cost, $status, $timestamp);
	
	echo '<table width="100%" cellspacing="0" cellpadding="5" align="center" border="0">
	<tr>
    	<td class="tableHeader">Activity id</td>
        <td class="tableHeader">Check no</td>
        <td class="tableHeader">Transaction Fee</td>
    	<td class="tableHeader">Shipping Cost</td>
        <td class="tableHeader">Net Revenue</td>
    	<td class="tableHeader" align="center">Status</td>
    	<td class="tableHeader">Date/Time</td>
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
        
        $transactionFee = '40.00';
        
        if($cost == ''){
            $netRevenue = '';
            $cost = '';
        }else{
            $netRevenue = $transactionFee - $cost;
            $cost = '('.number_format($cost,2).')';
        }
        
		$timestamp = strtotime($timestamp);
		$timestamp = date('d/m/Y H:i', $timestamp);
	
		$tableBody = "<tr class=tableBorder onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='tableBorder'\"><td class='tableBorder'>".$activityId."</td>
            <td class='tableBorder'>".$checkNo."</td>
			<td class='tableBorder'>".number_format($transactionFee,2)."</td>
            <td class='tableBorder'>".$cost."</td>
            <td class='tableBorder'>".$netRevenue."</td>
			<td class='tableBorder' align='center'><img src=\"../../../../images/".$image.".jpg\" border='0' /></td>
			<td class='tableBorder'>".$timestamp."</td></tr>";
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

function downloadAccounting($startDate,$endDate,$selectedloginid) {
    
    $today = date('Y-m-d');
    
    $directory = "downloads";
    $filename = "AVGFulfillment_".$today.".csv";
    $myFile = $directory."/".$filename;
    $fh = fopen($myFile, 'w') or die("Error!!");
    $line =  "Id, checkNo, name, transaction fee, cost, net rev, status, datetime";
    fwrite($fh, "$line\r\n");
    
    global $connection;

    $sql = "CALL accountingDownload(?,?,?)"; 
            
	$stmt = $connection->prepare($sql);
	if($connection->errno){
		die($connection->errno."::".$connection->error);
	}

	$p1 = $startDate;
	$p2 = $endDate;
	$p3 = $selectedloginid;
    
	$stmt->bind_param('ssi', $p1, $p2, $p3);

	$stmt->execute();
	$stmt->bind_result($activityId, $checkNo, $firstname, $lastname, $cost, $status, $timestamp);

    //Get query results
   
    while($stmt->fetch()){
        
        if($cost == ''){
            $netRevenue = '';
            $cost = '';
        }else{
            $netRevenue = "40.00" - $cost;
            $cost = '('.number_format($cost,2).')';
        }
        
        $line =  $activityId.",".$checkNo.",".$firstname." ".$lastname.",40.00,".$cost.",".$netRevenue.",".$status.",".$timestamp;

        fwrite($fh, "$line\r\n");
    }

    // Close file
    fclose($fh);
    
    $stmt->free_result();
    $stmt->close();

    while ($connection->next_result()) {
            //free each result.
            $result = $connection->use_result();
            if ($result instanceof mysqli_result) {
                    $result->free();
            }						
    }
 
    return array($filename,$myFile);
}

function accountingDownloadButton($startDate,$endDate,$selectedloginid){
            
		$formDownload = "<table width=200 cellpadding=0 cellspacing=0 align=right border=0 style=\"padding-right:10px;padding-bottom:10px;\"><tr><td><form name=\"formDowload\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" >
			<input type=\"image\" src=\"images/button_download.gif\" name=\"download\" value=\"download\" align=right>
			<input type=\"hidden\" name=\"startDate\" value=\"".$startDate."\">
			<input type=\"hidden\" name=\"endDate\" value=\"".$endDate."\">
			<input type=\"hidden\" name=\"selectedloginid\" value=\"".$selectedloginid."\">                    
            <input type=\"hidden\" name=\"pg\" value=\"results\">
            <input type=\"hidden\" name=\"postDownload\" value=\"yes\">
		</form></td></tr></table>";
    
        echo $formDownload;   
}

function accountingSummary($startDate,$endDate,$selectedloginid){

	global $connection;
	//Call accountingCount
   	$sql = "CALL accountingSummary(?,?,?)";  
    
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('ssi', $p1, $p2, $p3);
    $p1 = $startDate;
    $p2 = $endDate;
    $p3 = $selectedloginid;

    $stmt->execute();
    $stmt->bind_result($recordCount, $totalCost, $transactionFee, $totalFees, $netRev, $loadCount, $loadAmount, $loadfeeCount, $loadfeeAmount);

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
    
    $totals=array();
	
    $totals['recordCount']		= $recordCount;
	$totals['totalCost']		= $totalCost;
    $totals['transactionFee']   = $transactionFee;
    $totals['totalFees']        = $totalFees;
    $totals['netRev']           = $netRev;
    $totals['loadCount']        = $loadCount;
    $totals['loadAmount']       = $loadAmount;
    $totals['loadfeeCount']     = $loadfeeCount;
    $totals['loadfeeAmount']    = $loadfeeAmount;

    return $totals;
}
?>