<?php
$errmsg = '';
$msg = '';
if(isset($_REQUEST['errmsg'])){ $errmsg = $_REQUEST['errmsg']; }
if(isset($_REQUEST['msg'])){ $msg = $_REQUEST['msg']; }

verifyLogin();
if($sessionAdmin <> 1){
	$destination = "location:https://www.avgfulfillment.com/index.php?errmsg=You are not authorized to access this site.";
    header($destination);
	exit();
}

if(isset($_GET['pg'])){ $pg = $_GET['pg'] ; }else{ $pg = ''; }
if(isset($_REQUEST['selectedloginid'])){ $selectedloginid = $_REQUEST['selectedloginid']; }else{ $selectedloginid = ''; }

if(isset($_POST['postFile'])){

    $target_path = ACCOUNTING_UPLOAD;
	$target_path = $target_path . basename( $_FILES['uploadedFile']['name']); 
	
	if(move_uploaded_file($_FILES['uploadedFile']['tmp_name'], $target_path)) {
    	$msg = "The file ".  basename( $_FILES['uploadedFile']['name'])." has been uploaded";
		
		$row = 0;
		
		$handle = fopen($target_path, "r");
		while(($data = fgetcsv($handle, 1000, ",")) !== FALSE){
			
			if($row>0){
        		//$num = count($data);
				
				$trackingNo = $data[7];
				$cost		= $data[19];
				
				insertFulfillmentCosts($trackingNo,$cost);
			}
			$row++;
		}
		fclose($handle);
	} else{
       	$errmsg = "There was an error uploading the file, please try again!";
	}
}

///Error messaging
if($errmsg <> ''){ $errmsg = "<p class=errmsg>".$errmsg."</p>"; } else { $errmsg = ''; }
if($msg <> ''){ $msg = "<p class=msg>".$msg."</p>"; } else { $msg = ''; }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>AVG Fulfillment</title>
<link rel="stylesheet" href="../styles/styles.css" type="text/css">
</head>

<body>
<div id="container">
	
	<?php require("menu.php"); ?> 
    <?php require("header.php"); ?>
    
    <div id="content" style="height:500px;">
    
    	<h1>Upload Fulfillment Costs</h1>
    	<?php echo $errmsg; ?>
        <?php echo $msg; ?>
        
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
		<p>Please choose a file to upload.</p>
		<input name="uploadedFile" type="file" />
        <p><input type="image" src="../../../../images/upload.jpg" value="upload file" name="submit" /></p>
		<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
		<input type="hidden" name="postFile" value="yes" />
        <input type="hidden" name="pg" value="upload" />                                                            
		</form>
    
    </div>
    
    <?php require("footer.php"); ?>  
    
</div>
</body>
</html>
