<?php
include_once("includes/configuration.php");

$query_type=intval($_REQUEST['query_type']);

if($query_type ==1){
	$rs_discounts = $db->fetch_all_array("select details from discounts where id='" . intval($_REQUEST['id']). "' limit 1");
	echo trim($rs_discounts[0]['details']);
}
?>