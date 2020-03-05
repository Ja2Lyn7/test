<?php
error_reporting(2047);
ini_set("display_errors",1);

include("admin/includes/config/dbinfo.inc.php");
include("admin/includes/config/admin.inc.php");
include("admin/includes/general_functions.php");

insertTracking($_POST['loginId'], $_SERVER['REMOTE_ADDR']);
userLogin($_POST['loginId'], $_POST['userId'], $_POST['admin'], $_POST['companyName']);

if($_POST['admin'] == 1){
	$destination = "location:" . ADMIN_LINK . "index.php";
}else{
	$destination = "location:" . CLIENT_LINK . "index.php";
}
header($destination);
?>