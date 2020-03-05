<?php

require("includes/config/dbinfo.inc.php");
require("includes/config/admin.inc.php");
require("includes/general_functions.php");
include("includes/activity_functions.php");
include("includes/fulfillment_admin_functions.php");
include("includes/fulfillment_client_functions.php");

verifyLogin();
if($sessionAdmin <> 1){
	$destination = "location:https://www.avgfulfillment.com/login.php?errmsg=You are not authorized to access this site.";
    header($destination);
	exit();
}

if(isset($_REQUEST['pg'])){ $pg = $_REQUEST['pg']; }else{ $pg = ''; }



switch ($pg) {

    case 'main':
        require("includes/fulfillment/main/fulfillmentMain.php");
        break;

    case 'review':
        require("includes/fulfillment/admin/reviewBatches.php");
        break;    
		
	case 'financial':
        require("includes/fulfillment/main/fulfillmentFinancials.php");
        break;

    case 'adjustments':
        require("includes/fulfillment/admin/financialAdjustments.php");
        break;     
    
    
    case 'add':
        require("includes/fulfillment/clients/addRecords.php");
        break;
		
	case 'tracking':
        require("includes/fulfillment/admin/updateTracking.php");
        break;       
    
    default;
        echo "invalid lookup";
        break;
}

