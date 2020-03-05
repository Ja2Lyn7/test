<?php

include("includes/config/dbinfo.inc.php");
include("includes/config/admin.inc.php");
include("includes/general_functions.php");
include("includes/activity_functions.php");

verifyLogin();

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
		
    default:
        echo "invalid lookup";
        break;
}    
    
?>   
    