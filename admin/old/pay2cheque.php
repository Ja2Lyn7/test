<?php

require("includes/config/dbinfo.inc.php");
require("includes/config/admin.inc.php");
require("includes/general_functions.php");
require("includes/transaction_functions.php");
include("includes/pay2cheque_admin_functions.php");
include("includes/pay2cheque_merchant_functions.php");

verifyLogin();
if($sessionAdmin <> 1){
	$destination = "location:https://www.maxxpayments.com/login.php?errmsg=You are not authorized to access this site.";
    header($destination);
	exit();
}

if(isset($_REQUEST['pg'])){ $pg = $_REQUEST['pg']; }else{ $pg = ''; }



switch ($pg) {

    case 'main':
        require("includes/pay2cheque/main/pay2chequeMain.php");
        break;

    case 'admin':
        require("includes/pay2cheque/admin/chequeCredits.php");
        break;    
		
	case 'financial':
        require("includes/pay2cheque/main/pay2chequeFinancials.php");
        break;

    case 'creditdebit':
        require("includes/pay2cheque/admin/pay2chequeCreditDebit.php");
        break;     
    
    
    case 'merchant':
        require("includes/pay2cheque/merchant/uploadPay2ChequeCredits.php");
        break;
		
	case 'upload':
        require("includes/pay2cheque/admin/uploadData.php");
        break;       
    
    default;
        echo "invalid lookup";
        break;
}

