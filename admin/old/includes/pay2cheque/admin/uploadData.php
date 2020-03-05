<?php
//Admin URL or FAIL url or continues in case on regular login
verifyLogin();

if($sessionAdmin <> 1){
	$destination = "location:https://www.maxxpayments.com/login.php?errmsg=You are not authorized to access this site.";
    header($destination);
	exit();
}

$errmsg = '';
$msg = '';
if(isset($_REQUEST['errmsg'])){ $errmsg = $_REQUEST['errmsg']; }
if(isset($_REQUEST['msg'])){ $msg = $_REQUEST['msg']; }

if(isset($_POST['postFile'])){

    $target_path = P2CH_UPLOAD;
	$target_path = $target_path . basename( $_FILES['uploadedFile']['name']); 
	
	if(move_uploaded_file($_FILES['uploadedFile']['tmp_name'], $target_path)) {
    	$msg = "The file ".  basename( $_FILES['uploadedFile']['name'])." has been uploaded";
		
		$row = 0;
		
		$handle = fopen($target_path, "r");
		while(($data = fgetcsv($handle, 1000, ",")) !== FALSE){
			
			if($row>=0){
        		$num = count($data);

				if($num==13){
					
					$checkId 		= $data[2];
					$transactionId 	= $data[12];
					
					insertTransactionIds($checkId,$transactionId);
				}
			}
			$row++;
		}
		fclose($handle);
	} else{
       	$errmsg = "There was an error uploading the file, please try again!";
	}
}

//Error messaging
if($errmsg <> ''){ $errmsg = "<p class=errmsg>".$errmsg."</p>"; } else { $errmsg = ''; }
if($msg <> ''){ $msg = "<p class=msg>".$msg."</p>"; } else { $msg = ''; }	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Maxx Payments</title>
<link rel="stylesheet" href="styles/styles.css" type="text/css">
<script type="text/javascript">
function checkall(el){
	var ip = document.getElementsByTagName('input'), i = ip.length - 1;
	for (i; i > -1; --i){
		if(ip[i].type && ip[i].type.toLowerCase() === 'checkbox'){
			ip[i].checked = el.checked;
		}
	}
}
</script>
</head>

<body>
<div id="content">
<?php require("menu.php"); ?>
<div id="header">Pay2Cheque <img src="../../../images/arrow.jpg" align="absmiddle" /> Upload transaction tracking data</div>
<?php echo $msg; ?>
<?php echo $errmsg; ?>
<div id="main">
<table width="1180" cellspacing="0" cellpadding="5" align="center" border="0">
	<tr>
    	<td valign="top" width="300">
        	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
            <table width="300" cellpadding="5" cellspacing="0" align="center" border="0" style="border:1px solid #555555;">
				<tr>
					<td valign="top" class="searchHeader">Upload transaction Ids</td>
				</tr>
				<tr>
					<td><i>Please choose a file to upload.</i></td>
				</tr>
				<tr>
					<td><input name="uploadedFile" type="file" /></td>
				</tr>   
                <tr>
					<td><input type="image" src="images/button_upload.gif" value="upload file" name="submit" /></td>
				</tr>          
			</table>
			<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
			<input type="hidden" name="postFile" value="yes" />
            <input type="hidden" name="pg" value="upload" />                                                            
			</form>
     	</td>
        <td style="width:50px;"></td>
        <td valign="top" width="600">&nbsp;</td>
        <td></td>
  	</tr>
</table>    
</div>
</div>
<?php require("footer.php"); ?>
</body>
</html>
