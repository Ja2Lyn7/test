<?php
error_reporting(2047);
ini_set("display_errors",1);

//KEY URLS
//////////////////////////
define('ADMIN_LINK','https://www.avgfulfillment.com/admin/');
define('CLIENT_LINK','https://www.avgfulfillment.com/clients/');

//KEY SWITCHES
//////////////////////////
define('PAGEROWS','100');
define('TESTMODE',FALSE);
define('ENABLE_LOGGING',TRUE);
define('DISPLAY_LOG',FALSE);

// KEY DIRECTORIES
//Directory names must end with slash /
/////////////////////////////////////////////////
define('FULFILLMENT_DIR','/home/avgfulfillment/public_html/admin/Files/fulfillmentUploadedFiles/');
define('FULFILLMENT_TEMPLATE_DIR','/admin/Files/fulfillmentTemplates/');
define('FULFILLMENT_UPLOAD','/home/avgfulfillment/public_html/admin/Files/fulfillmentTracking/');
define('ACCOUNTING_UPLOAD','/home/avgfulfillment/public_html/admin/Files/accountingCosts/');

//FULFILLMENTCRON
define('FULFILLMENT_BATCH_DIR','/home/avgfulfillment/public_html/admin/Files/fulfillmentBatches/');
define('FULFILLMENT_EMAIL','support@avgfulfillment.com');

//REPORT DIRECTORY
define('REPORT_DIR','/home/avgfulfillment/public_html/admin/Files/Reports/');

define('EMAIL_SIGNATURE',"\nAvgFulfillment");
?>