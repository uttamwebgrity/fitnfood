<?php
include_once("includes/configuration.php");

$email=trim($_REQUEST['email']);

$found=0;

if($email != NULL){		
	$result=mysql_query("select email_address from users where email_address='" .$email . "' limit 1");
	if(mysql_num_rows($result) == 1)
		$found=1;	
}

echo $found;
