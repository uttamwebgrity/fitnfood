<?php
include_once ("includes/configuration.php");
//$.get("calculate-price.php?meal_id=" + meal_id + "&meal_qty="+ meal_qty +"&type=meal"+ , function(data) {
//$.get("calculate-price.php?snack_id=" + snack_id + "&snack_qty="+ meal_qty +"&type=snack"+ , function(data) {
$type = trim($_REQUEST['type']);

if($type == "meal"){
	 $meal_id=intval($_REQUEST['meal_id']);	
	$meal_qty=intval($_REQUEST['meal_qty']);
	$price= mysql_result(mysql_query("select meal_price from meals_sizes_prices where meal_id = '" . mysql_real_escape_string($meal_id)."' and meal_size = '" . mysql_real_escape_string($meal_qty)."'"),0,0);	
	 
	 echo round($price,2);
}else if($type == "snack"){
	$snack_id=intval($_REQUEST['snack_id']);	
	$snack_qty=intval($_REQUEST['snack_qty'])== 0?1:intval($_REQUEST['snack_qty']);	
	$price=mysql_result(mysql_query("select price from snacks where id = '" . mysql_real_escape_string($snack_id)."'"),0,0);
	echo round(($price * $snack_qty),2);
}


?>