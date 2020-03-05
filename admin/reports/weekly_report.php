<?php
include("../includes/config/dbinfo.inc.php");
include("../includes/config/admin.inc.php");
include("../includes/report_functions.php");
require("../../phpmailer/class.phpmailer.php");
require("../includes/general_functions.php");

//Report Configuration
$directory 	= REPORT_DIR;

$start = strftime('%Y-%m-%d',(strtotime('7 days ago')));
$end = strftime('%Y-%m-%d',(strtotime('1 day ago')));
//$start = '2018-01-01';
//$end = '2018-12-31';

$filename = $start . ' to ' . $end . '.csv';
$myFile = $directory.$filename;
$fh = fopen($myFile, 'w');
if ($fh==FALSE){
    die("Could not create" . $filename) ;
}

$line = 'Date, Description, Amount, Balance';
fwrite($fh, "$line\r\n");
echo $line . "<br>";

if($connection->errno){
    die($connection->errno."::".$connection->error);
}

$sql = "
  SELECT remainingBalance
  FROM accounting
  WHERE DATE(timestamp) < '$start'
  ORDER BY timestamp DESC
  LIMIT 1"
;

$result = $connection->query($sql);

while($row = $result->fetch_assoc()) {
    $line = $start . ', Starting Balance,, ' .  $row['remainingBalance'];
    $startingBalance = $row['remainingBalance'];
    fwrite($fh, "$line\r\n");
    echo $line . "<br>";
}

$sql = "
  SELECT 
	DATE(activity.timestamp) AS Date,
	checkNo AS Description, 
	amount AS Amount
  FROM activity
	LEFT JOIN customers ON activity.customerId = customers.id
	LEFT JOIN checkNumber ON activity.id = checkNumber.activityId
  WHERE
    activity.timestamp >= DATE('$start')
	AND activity.timestamp <  DATE('$end')
ORDER BY activity.timestamp ASC"
;

$result = $connection->query($sql);

$array = [];
while($row = $result->fetch_assoc()) {
    $row['Description'] = 'Check No. ' . $row['Description'];
    $row['Amount'] = '-' . $row['Amount'];
    $array[] = $row;

    $row['Description'] = 'Transaction Fee';
    $row['Amount'] = '-40.00';

    $array[] = $row;
}

$sql = "
  SELECT 
	DATE(timestamp) AS Date,
	Description AS Description, 
	amount AS Amount,
	amountTypeId
  FROM accounting
  WHERE
    timestamp >= DATE('$start')
	AND timestamp <  DATE('$end')
	AND description NOT LIKE 'Starting alance'
	AND description NOT LIKE 'Batch Amount'
	AND description NOT LIKE 'Fulfillment Fees'
ORDER BY timestamp ASC"
;

$result = $connection->query($sql);

while($row = $result->fetch_assoc()) {
    $row['Amount'] = $row['amountTypeId'] == 2 ? '-' . $row['Amount'] : $row['Amount'];
    unset($row['amountTypeId']);
    $array[] = $row;
}

array_multisort(array_column($array, 'Date'), SORT_ASC, $array);
$array = $array;

$newBalance = $startingBalance;
foreach($array as $data) {
    $newBalance = $newBalance + $data['Amount'];
    $line = $data['Date'] . ', ' . $data['Description'] . ', ' . $data['Amount'] . ', ' . $newBalance;
    fwrite($fh, "$line\r\n");
    echo $line . "<br>";
}

$line = $end . ', Ending Balance, , ' . $newBalance;
fwrite($fh, "$line\r\n");
echo $line . "<br>";

$connection->close();
fclose($fh);

$email_message_body= "File Name:" . $filename .  "<br>". EMAIL_SIGNATURE;
	
//Send email
$subject = "Weekly Report for " . $start . " to " . $end;
	
$htmlBody = date("Y.m.d H:i:s")."<br><br>" . $email_message_body;
$mail = new PHPMailer();
		
$mail->Host = "mail.avgfulfillment.com";
$mail->From = "support@avgfulfillment.com";
$mail->FromName = "Support";
$mail->AddAddress("support@avgfulfillment.com");
$mail->Username = "support@avgfulfillment.com";
$mail->Password =  "mail!@2017";
$mail->Port  =  "25";
	
$mail->AddAttachment($myFile);

$mail->Subject = $subject;
$mail->Body = $htmlBody;
$mail->isHTML(true);
//$mail->WordWrap = 50;

if(!$mail->Send()){
    echo "mail error".$mail->ErrorInfo;
} else {
    echo "mail sent";

    unlink($myFile);
}
?>