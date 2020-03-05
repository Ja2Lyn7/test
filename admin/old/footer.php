<?php
$pgIndexFooter = "<a href='index.php'>Home</a> | "; 
$pgDashFooter = "<a href='dashboard.php?pg=main'>Dashboard</a> | ";
$pgTransactionsFooter = "<a href='transactions.php?pg=main'>Transactions</a> | "; 
$pgPay2CardFooter = "<a href='pay2card.php?pg=main'>Pay2Card</a> | "; 
$pgPay2ChequeFooter = "<a href='pay2cheque.php?pg=main'>Pay2Cheque</a> | ";
$pgPay2CupFooter = "<a href='pay2cup.php?pg=main'>Pay2CUP</a> | ";  
$pgAchFooter = "<a href='ach.php?pg=main'>ACH</a> | "; 
$pgCheckItsMeFooter = "<a href='checkitsme.php?pg=main'>CheckItsMe</a> | "; 
$managerFooter = "<a href='manager.php?pg=main'>Administration</a> | "; 
$logoutFooter = "<a href='index.php?pg=logout'>Logout</a>";
?>

<div id="copyright">
<?php 
echo $pgIndexFooter . $pgDashFooter . $pgTransactionsFooter; 

if($sessionFintraxAllowed == 1){
	echo $pgPay2CardFooter;
}

if($sessionCheckAllowed == 1){
	echo $pgPay2ChequeFooter;
}

if($sessionCupAllowed == 1){
	echo $pgPay2CupFooter;
}

if($sessionAchAllowed == 1){
	echo $pgAchFooter;
}

if($sessionEvsAllowed == 1){
	$pgCheckItsMeFooter;
}

if($sessionLoginId == 1){
	echo $managerFooter;
}

echo $logoutFooter; ?>
<br />
&copy; Copyright 2013-2015. MaxxPayments.com. All Rights Reserved.
</div>