<?php
include_once("includes/configuration.php");

$query_type=intval($_REQUEST['query_type']);
$show="";

if($query_type ==1){
	$rs_discounts = $db->fetch_all_array("select address,pickup_timing from pickup_locations where id='" . intval($_REQUEST['id']). "' limit 1");
	
	$show .= "<strong>Address:</strong><br />". trim($rs_discounts[0]['address']). "<br/><br/>";	
	$show .= "<strong>Pickup Timing:</strong><br />" . nl2br(trim($rs_discounts[0]['pickup_timing']));
}
echo $show; 
?>