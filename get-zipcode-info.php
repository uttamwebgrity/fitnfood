<?php
include_once("includes/configuration.php");

$zipcode=trim($_REQUEST['zipcode']);

$found=0;

if($zipcode != NULL){		
	$result=mysql_query("select suburb_postcode from suburb where suburb_postcode='" .$zipcode . "' limit 1");
	if(mysql_num_rows($result) == 1)
		$found=1;	
}

echo $found;
