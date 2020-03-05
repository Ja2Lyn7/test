<?php
error_reporting(2047);
ini_set("display_errors",1);

//KEY URLS
//////////////////////////
define('ADMIN_LINK','https://www.paymentexchange.com/admin/');
define('CLIENT_LINK','https://www.paymentexchange.com/merchants/');

//KEY SWITCHES
//////////////////////////
define('PAGEROWS','100');
define('TESTMODE',FALSE);
//define('SECUREIP','192.168.0.1');
define('ENABLE_LOGGING',TRUE);
define('DISPLAY_LOG',FALSE);
define('LOG_EMAIL','support@paymentexchange');
define('LOG_FOLDER','log/admin.log');
define('LOG_LEVEL','E_ERROR');


// KEY DIRECTORIES
//Directory names must end with slash /
/////////////////////////////////////////////////
define('P2C_DIR','/usr/share/nginx/html/admin/Files/p2cUploadedFiles/');
define('P2C_TEMPLATE_DIR','Files/p2cTemplates/');
define('P2CH_DIR','/usr/share/nginx/html/admin/Files/p2chUploadedFiles/');
define('P2CH_TEMPLATE_DIR','Files/p2chTemplates/');
define('P2CH_UPLOAD','/usr/share/nginx/html/admin/Files/p2chTransactions/');
define('DOCUMENT_DIR','Files/documents/');

//CHARGEBACKCRON
define('CHARGEBACK_AGE','2');

//FINTRAXCRON
define('FINTRAX_CLIENT_CODE','UK.CED.001');
define('FINTRAX_BATCH_DIR','/home/paymentexchange.com/html/admin/Files/p2cBatches/');
define('FINTRAX_EMAIL','support@paymentexchange.com');

define('EMAIL_SIGNATURE',"\nMaxx Payments Merchant System");

//XE.NET CRON
define('XE_URL','http://www.xe.com/dfs/datafeed2.cgi?paymundo');
define('XE_DIR','/home/paymentexchange.com/html/admin/currency/');

//PAY2CHEQUECRON
define('PAY2CHECK_BATCH_DIR','/usr/share/nginx/html/admin/Files/p2chBatches/');
define('PAY2CHECK_EMAIL','support@maxxpayments.com');

define('P2CEMAIL_SIGNATURE',"\nMaxx Payments Merchant System");
?>