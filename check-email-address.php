<?php
include_once("includes/configuration.php");

$type=trim($_REQUEST['type']);
$email=trim($_REQUEST['email']);


if(trim($type) == "trainer"){
	$result=$db->fetch_all_array("select email_address from  trainers where email_address='" . $email . "' limit 1");
	if(count($result) == 1){
		echo "1";
	}else{
		echo "0";		
	}
}else if(trim($type) == "member"){
	$result=$db->fetch_all_array("select email_address from  users where email_address='" . $email . "' limit 1");
	if(count($result) == 1){
		echo "1";
	}else{
		echo "0";		
	}	
}
?>