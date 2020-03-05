<?php

require("includes/config/dbinfo.inc.php");
require("includes/config/admin.inc.php");
require("includes/general_functions.php");
include("includes/activity_functions.php");
include("includes/fulfillment_client_functions.php");

verifyLogin();

if(isset($_REQUEST['pg'])){ $pg = $_REQUEST['pg']; }else{ $pg = ''; }



switch ($pg) {

    case 'main':
        require("includes/fulfillment/main/fulfillmentMain.php");
        break;
		
	case 'financial':
        require("includes/fulfillment/main/fulfillmentFinancials.php");
        break;
    
    case 'add':
        require("includes/fulfillment/clients/addRecords.php");
        break;    
    
    default;
        echo "invalid lookup";
        break;
}

