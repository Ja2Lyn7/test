<!----------menu start----------------->

<?php
if(isset($_REQUEST['pg'])){ $pg = $_REQUEST['pg']; }else{ $pg = ''; }

$string = $_SERVER['REQUEST_URI'];
$string = explode("/",$string);
$string = explode(".",$string[2]);
$string = $string[0];

if($string == 'index'){ $pgIndex = "<td valign='middle' align='center' class='selected' style='border-right:1px solid #ffffff;'><a href='index.php' class='selected'>Home</a></td>"; } else{ $pgIndex = "<td valign='middle' align='center' style='border-right:1px solid #ffffff;'><a href='index.php' class='menu'>Home</a></td>"; }
if($string == 'dashboard'){ $pgDash = "<td valign='middle' align='center' class='selected' style='border-right:1px solid #ffffff;'><a href='dashboard.php?pg=main' class='selected'>Dashboard</a></td>"; } else{ $pgDash = "<td valign='middle' align='center' style='border-right:1px solid #ffffff;'><a href='dashboard.php?pg=main' class='menu'>Dashboard</a></td>"; }
if($string == 'transactions'){ $pgTransactions = "<td valign='middle' align='center' class='selected' style='border-right:1px solid #ffffff;'><a href='transactions.php?pg=main' class='selected'>Transactions</a></td>"; } else{ $pgTransactions = "<td valign='middle' align='center' style='border-right:1px solid #ffffff;'><a href='transactions.php?pg=main' class='menu'>Transactions</a></td>"; }
if($string == 'pay2card'){ $pgPay2Card = "<td valign='middle' align='center' class='selected' style='border-right:1px solid #ffffff;'><a href='pay2card.php?pg=main' class='selected'>Pay2Card</a></td>"; } else{ $pgPay2Card = "<td valign='middle' align='center' style='border-right:1px solid #ffffff;'><a href='pay2card.php?pg=main' class='menu'>Pay2Card</a></td>"; }
if($string == 'pay2cheque'){ $pgPay2Cheque = "<td valign='middle' align='center' class='selected' style='border-right:1px solid #ffffff;'><a href='pay2cheque.php?pg=main' class='selected'>Pay2Cheque</a></td>"; } else{ $pgPay2Cheque = "<td valign='middle' align='center' style='border-right:1px solid #ffffff;'><a href='pay2cheque.php?pg=main' class='menu'>Pay2Cheque</a></td>"; }

if($string == 'pay2cup'){ $pgPay2Cup = "<td valign='middle' align='center' class='selected' style='border-right:1px solid #ffffff;'><a href='pay2cup.php?pg=main' class='selected'>Pay2CUP</a></td>"; } else{ $pgPay2Cup = "<td valign='middle' align='center' style='border-right:1px solid #ffffff;'><a href='pay2cup.php?pg=main' class='menu'>Pay2CUP</a></td>"; }

if($string == 'ach'){ $pgAch = "<td valign='middle' align='center' class='selected' style='border-right:1px solid #ffffff;'><a href='ach.php?pg=main' class='selected'>ACH</a></td>"; } else{ $pgAch = "<td valign='middle' align='center' style='border-right:1px solid #ffffff;'><a href='ach.php?pg=main' class='menu'>ACH</a></td>"; }
if($string == 'checkitsme'){ $pgCheckItsMe = "<td valign='middle' align='center' class='selected' style='border-right:1px solid #ffffff;'><a href='checkitsme.php?pg=main' class='selected'>CheckItsMe</a></td>"; } else{ $pgCheckItsMe = "<td valign='middle' align='center' style='border-right:1px solid #ffffff;'><a href='checkitsme.php?pg=main' class='menu'>CheckItsMe</a></td>"; }
if($string == 'manager'){ $administration = "<td valign='middle' align='center' class='selected' style='border-right:1px solid #ffffff;'><a href='manager.php?pg=main' class='selected'>Administration</a></td>"; } else{ $administration = "<td valign='middle' align='center' style='border-right:1px solid #ffffff;'><a href='manager.php?pg=main' class='menu'>Administration</a></td>"; }
if($pg == 'logout') { userLogout(); }
?>
<div id="logo"><img src="images/logo.jpg" width="417" height="106" /></div>
<div id="data">IP address : <?php echo $_SERVER['REMOTE_ADDR']; ?><br />Server time : <?php 
			$now = date("Y-m-d H:i:s");
			$serverTime = strtotime($now);
			$serverTime = date('l, F d, Y g:ia', $serverTime);
			echo $serverTime." (UTC)";                        
			?><br />Last currency update : 
			<?php 
			$updateDate=getLastCurrencyUpdate();
			$updateDate = strtotime($updateDate);
			$updateDate = date('l, F d, Y g:ia', $updateDate);
			echo $updateDate." (UTC)"; 
			?>
            </div>
<div id="menu">
<table width="100%" cellspacing="0" cellpadding="0" align="center" border="0">
	<tr>
    	<?php echo $pgIndex; ?>
        <?php echo $pgDash; ?>
        <?php echo $pgTransactions; ?>
        <?php if($sessionFintraxAllowed == 1){ echo $pgPay2Card; } ?>
        <?php if($sessionCheckAllowed == 1){ echo $pgPay2Cheque; } ?>
         <?php if($sessionCupAllowed == 1){ echo $pgPay2Cup; } ?>
        <?php if($sessionAchAllowed == 1){ echo $pgAch; } ?>
        <?php if($sessionEvsAllowed == 1){ echo $pgCheckItsMe; } ?>
        <?php if($sessionLoginId == 1){ echo $administration; } ?>
        <td valign="middle" align="center"><a href="index.php?pg=logout" class="menu">Logout</a></td>
  	</tr>
</table>
</div>
            <?php
                if((ENABLE_LOGGING) && (DISPLAY_LOG)) echo "<center><h2>DISPLAY_LOG=TRUE</h2></center>"; 
            ?>    
<!----------menu end----------------->        