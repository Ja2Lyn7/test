<?php

function getClientTypes($selectedid){
	
	global $connection; 
    
    //Call getClientTypes procedure
    $sql = "CALL getClientTypes()";
    $stmt = $connection->prepare($sql);

    if($connection->errno){
	die($connection->errno."::".$connection->error);
    }

    $stmt->execute();
    $stmt->bind_result($id,$clientType);
		
    //Get query results
   	while($stmt->fetch()){
		$selected = '';
		if($id == $selectedid){ $selected = "selected=selected"; }
		echo "<option value=\"".$id."\" ".$selected.">".$clientType."</option>";
	}
			
    $stmt->free_result();
    $stmt->close();
		
    while ($connection->next_result()){
		//free each result.
		$result = $connection->use_result();
		if ($result instanceof mysqli_result){
			$result->free();
		}
    }
    
}

function getClients($selectedid){
	
	global $connection; 
    
    //Call getClients procedure
    $sql = "CALL getClients()";
    $stmt = $connection->prepare($sql);

    if($connection->errno){
	die($connection->errno."::".$connection->error);
    }

    $stmt->execute();
    $stmt->bind_result($id,$companyName);
		
    //Get query results
   	while($stmt->fetch()){
		$selected = '';
		if($id == $selectedid){ $selected = "selected=selected"; }
		echo "<option value=\"".$id."\" ".$selected.">".$companyName."</option>";
	}
			
    $stmt->free_result();
    $stmt->close();
		
    while ($connection->next_result()){
		//free each result.
		$result = $connection->use_result();
		if ($result instanceof mysqli_result){
			$result->free();
		}
    }
    
}

function getLoginTypes($selectedid){
	
	global $connection; 
    
    //Call getLoginTypes procedure
    $sql = "CALL getLoginTypes()";
    $stmt = $connection->prepare($sql);

    if($connection->errno){
	die($connection->errno."::".$connection->error);
    }

    $stmt->execute();
    $stmt->bind_result($id,$loginType);
		
    //Get query results
   	while($stmt->fetch()){
		$selected = '';
		if($id == $selectedid){ $selected = "selected=selected"; }
		echo "<option value=\"".$id."\" ".$selected.">".$loginType."</option>";
	}
			
    $stmt->free_result();
    $stmt->close();
		
    while ($connection->next_result()){
		//free each result.
		$result = $connection->use_result();
		if ($result instanceof mysqli_result){
			$result->free();
		}
    }
    
}

function getAuthorizationTypes($selectedid){
	
	global $connection; 
    
    //Call getAuthorizationTypes procedure
    $sql = "CALL getAuthorizationTypes()";
    $stmt = $connection->prepare($sql);

    if($connection->errno){
	die($connection->errno."::".$connection->error);
    }

    $stmt->execute();
    $stmt->bind_result($id,$authorizationType);
		
    //Get query results
   	while($stmt->fetch()){
		$selected = '';
		if($id == $selectedid){ $selected = "selected=selected"; }
		echo "<option value=\"".$id."\" ".$selected.">".$authorizationType."</option>";
	}
			
    $stmt->free_result();
    $stmt->close();
		
    while ($connection->next_result()){
		//free each result.
		$result = $connection->use_result();
		if ($result instanceof mysqli_result){
			$result->free();
		}
    }
    
}

function insertNewClient($companyName,$contactName,$email,$clientTypeId) {

    global $connection;
  	
	//Call insertNewClient procedure
    $sql = "CALL insertNewClient(?,?,?,?)";
    $stmt = $connection->prepare($sql);
    
	if($connection->errno){
	die($connection->errno."::".$connection->error);
    }

    $p1 = addslashes($companyName);
    $p2 = addslashes($contactName);
    $p3 = addslashes($email);
    $p4 = $clientTypeId;
	
    $stmt->bind_param('sssi',$p1,$p2,$p3,$p4);

    $stmt->execute();
    $stmt->bind_result($id);

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

    return $id;
}

function getLogin($username,$password) {
	
    myLog("getLogin username=$username");
    global $connection;

    //Call validationLogin procedure
    $sql = "CALL validateLogin(?,?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('ss', $p1, $p2);
    $p1 = $username;
    $p2 = md5($password);
	
    $stmt->execute();
    $stmt->bind_result($loginId, $userId, $userTypeId, $loginTypeId, $authorizationTypeId, $admin, $testmode, $active, $companyName, $allowFintrax,$allowEvs,$allowAch,$allowCheck,$allowCup,$clientSite);			
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
    
    $response = $loginId."|".$userId."|".$userTypeId."|".$loginTypeId."|".$authorizationTypeId."|".$admin."|".$testmode."|".$active."|".$companyName."|".$allowFintrax."|".$allowEvs."|".$allowAch."|".$allowCheck."|".$allowCup."|".$clientSite;

    myLog("getLogin loginId=$loginId, userId=$userId, active=$active");

    return $response;
        
} 

?>