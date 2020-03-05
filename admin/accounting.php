<?php

include("includes/config/dbinfo.inc.php");
include("includes/config/admin.inc.php");
include("includes/general_functions.php");
include("includes/accounting_functions.php");

verifyLogin();
if($sessionAdmin <> 1){
	$destination = "location:https://www.avgfulfillment.com/login.php?errmsg=You are not authorized to access this site.";
    header($destination);
	exit();
}

if(isset($_REQUEST['pg'])){ $pg = $_REQUEST['pg']; }else{ $pg = ''; }

switch ($pg) {

    case 'main':
        require("includes/accounting/main/accountingMain.php");
        break;
		
		case 'search':
        require("includes/accounting/search/search.php");
        break;
		
		case 'results':
        require("includes/accounting/search/results.php");
        break;
		
		case 'upload':
        require("includes/accounting/upload/uploadFulfillmentCosts.php");
        break;
		
    default:
        echo "invalid lookup";
        break;
}    
    
?>   
    