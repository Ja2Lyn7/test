<?php

function getLogin($username,$password) {
	
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
    $stmt->bind_result($loginId, $userId, $admin, $active, $companyName);			
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
    
    $response = $loginId."|".$userId."|".$admin."|".$active."|".$companyName;

    return $response;
        
} 

function insertTracking($loginId,$remoteIp) {

    global $connection;
    //Call insertTracking procedure
    $sql = "CALL insertTracking(?,?)";
    $stmt = $connection->prepare($sql);
    if($connection->errno){
            die($connection->errno."::".$connection->error);
    }

    $stmt->bind_param('is', $p1, $p2);
    $p1 = $loginId;
    $p2 = $remoteIp;
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

function userLogin($loginId,$userId,$admin,$companyName){

    global $sessionLoginId;
    global $sessionUserId;
    global $sessionAdmin;
    global $sessionCompanyName;
    
    setcookie("sessionLoginId", $loginId, time()+36000);
    setcookie("sessionUserId", $userId, time()+36000);
    setcookie("sessionAdmin", $admin, time()+36000);
    setcookie("sessionCompanyName", $companyName, time()+36000);
    
}

function userLogout() {
    
    global $sessionLoginId;
    global $sessionUserId;
    global $sessionAdmin;
    global $sessionCompanyName;
    
    setcookie("sessionLoginId", "", time()-3600);
    unset($_COOKIE['sessionLoginId']); 

    setcookie("sessionUserId", "", time()-3600);
    unset($_COOKIE['sessionUserId']);

    setcookie("sessionAdmin", "", time()-3600);
    unset($_COOKIE['sessionAdmin']);

    setcookie("sessionCompanyName", "", time()-3600);
    unset($_COOKIE['sessionCompanyName']);  

    $destination = "location:https://www.avgfulfillment.com/index.php?errmsg=You have been successfully logged out of your account.";
    header($destination);
}

function verifyLogin() {
	
    global $connection;

    global $sessionLoginId;
    global $sessionUserId;
    global $sessionAdmin;
    global $sessionCompanyName;

    if(isset($_COOKIE['sessionLoginId'])){
    	$sessionLoginId = $_COOKIE['sessionLoginId'];
		$sessionUserId = $_COOKIE['sessionUserId'];
		$sessionAdmin = $_COOKIE['sessionAdmin'];
		$sessionCompanyName = $_COOKIE['sessionCompanyName'];
        
		//Verify the user is currently active. If not, eject from site.

		//Call verifyLogin procedure
		$sql = "CALL verifyLogin(?)";
		$stmt = $connection->prepare($sql);
		if($connection->errno){
			die($connection->errno."::".$connection->error);
		}

		$stmt->bind_param('i', $p1);
		$p1 = $sessionLoginId;
		$stmt->execute();
		$stmt->bind_result($active);

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

		if($active == '0'){
			$destination = "location:https://www.avgfulfillment.com/index.php?errmsg=You are not authorized to access this site.";
			header($destination);
			exit();
		}

    } else{
        //no session
		$destination = "location:https://www.avgfulfillment.com/index.php?errmsg=You are not logged in or your session has expired.";
		header($destination);
		exit();
    }
}

function getLogins($selectedid) {
 
    global $connection; 
    
    //Call getLogins procedure
    $sql = "CALL getLogins()";
    $stmt = $connection->prepare($sql);

    if($connection->errno){
	die($connection->errno."::".$connection->error);
    }

    $stmt->execute();
    $stmt->bind_result($id,$login);
		
    //Get query results
   	while($stmt->fetch()){
		$selected = '';
		if($id == $selectedid){ $selected = "selected=selected"; }
		echo "<option value=\"".$id."\" ".$selected.">".$login."</option>";
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

function pageLimits($rowCount) {
   
	if(isset($_GET['pagenum'])){ $pagenum = $_GET['pagenum']; }else{ $pagenum = 1; }

    $pagerows=(int)PAGEROWS;
        
    $last = ceil($rowCount / $pagerows);
	if ($pagenum < 1){ $pagenum = 1; } 
	elseif ($pagenum > $last) { $pagenum = $last; }
	
	$start = ($pagenum - 1) * $pagerows;
	$limit = $pagerows;
    
   	return array($start,$limit,$pagenum,$last);
}

function pagination($pagenum,$last,$nameValuePair) {

	if($pagenum == 1){ $firstPage = ''; } 
	else{
		$previous = $pagenum-1;
		$firstPage = "<a href='".$_SERVER['PHP_SELF']."?" . $nameValuePair. "&pagenum=1'>&laquo;First</a> <a href='".$_SERVER['PHP_SELF']."?" . $nameValuePair. "&pagenum=".$previous."'> &lsaquo;Previous</a>"; 
	}
	if($pagenum == $last){ $lastPage = ''; }
	else{
		$next = $pagenum+1;
		$lastPage = "<a href='".$_SERVER['PHP_SELF']."?".$nameValuePair . "&pagenum=".$next."'>Next&rsaquo;</a> <a href='".$_SERVER['PHP_SELF']."?" .$nameValuePair . "&pagenum=".$last."'>Last&raquo;</a>";
	}
	if($last > 1){
		$pagination = "
			<table width='100%' cellspacing='0' cellpadding='1' align='center' border='0'>
                        <tr>
			<td align=\"left\">--Page ".$pagenum." of ".$last."--</td>
			<td align=\"right\">".$firstPage;
			for($i=1;$i<=$last;$i++){ 
				if($pagenum <> $i){
					$pagination .= "<a href='".$_SERVER['PHP_SELF']."?".$nameValuePair."&pagenum=".$i."'> ".$i."</a> |"; 
				} else{
					$pagination .= $i." |";
				}
			}
			$pagination .= $lastPage."</td></tr></table>";

                 echo $pagination;
	}
    
}
?>