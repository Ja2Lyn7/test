<?php
if(isset($_REQUEST['pg'])){ $pg = $_REQUEST['pg']; }else{ $pg = ''; }

$string = $_SERVER['REQUEST_URI'];
$string = explode("/",$string);
$string = explode(".",$string[2]);
$string = $string[0];

if($pg == 'logout') { userLogout(); }
?>

<div id="menu">

	<div id="search">
    	<form method="post" action="#">
        	
            <input type="image" src="../images/search.jpg" name="submit" value="submit" border="0" id="searchbutton" /></td>
            <input type="hidden" name="post" value="yes">
		</form>
    
    </div>
    <div>&nbsp;</div>
    <a href='activity.php?pg=main'><img src="../images/activity<?php if($string == 'activity'){ ?>_selected<?php } ?>.jpg" width="268" height="54" class="menuItem"><br>
    <?php if($sessionLoginId == 4){ ?>
    <a href='fulfillment.php?pg=main'><img src="../images/fulfillment<?php if($string == 'fulfillment'){ ?>_selected<?php } ?>.jpg" width="268" height="54" class="menuItem"></a><br>
    <?php } ?>
    <?php if($string == 'fulfillment'){ ?>
    
    <a href='fulfillment.php?pg=add'><img src="../images/addmenu<?php if($pg == 'add'){ ?>_selected<?php } ?>.jpg" width="268" height="54" class="menuItem"></a><br>
    
    <a href='fulfillment.php?pg=financial'><img src="../images/financialsmenu<?php if($pg == 'financial'){ ?>_selected<?php } ?>.jpg" width="268" height="54" class="menuItem"></a><br>
	
	<?php } ?>
    
    <a href='index.php?pg=logout'><img src="../images/logoutmenu.jpg" width="268" height="54" border="0"></a>
	
</div>