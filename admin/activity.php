<?php

include("includes/config/dbinfo.inc.php");
include("includes/config/admin.inc.php");
include("includes/general_functions.php");
include("includes/activity_functions.php");

verifyLogin();
if($sessionAdmin <> 1){
	$destination = "location:https://www.avgfulfillment.com/login.php?errmsg=You are not authorized to access this site.";
    header($destination);
	exit();
}

if(isset($_REQUEST['pg'])){ $pg = $_REQUEST['pg']; }else{ $pg = ''; }

switch ($pg) {

    case 'main':
        require("includes/activity/search/searchForm.php");
        break;
    
    case 'search':
        require("includes/activity/search/searchResults.php");
        break;        
    
    case 'detail':
        require("includes/activity/detail/activityDetail.php");
        break;        

    case 'blacklist':
        require("includes/activity/detail/addToBlackList.php");
        break;         
		
    default:
        echo "invalid lookup";
        break;
}    
    
?>   
    