<?php
error_reporting(0);
/*****************************************************/
/********** 1 = Monday & 7 = Sunday
 * Week 47 is from Monday November 17, 2014 until (and including) Sunday November 23, 2014
 
/*****************************************************/
include_once("../includes/configuration.php");


$sql_next="select id,order_id,user_id  from order_cancel_from_next_week";
$result_next=$db->fetch_all_array($sql_next);
$total_next=count($result_next);
	
if($total_next > 0){
	for($next=0; $next < $total_next; $next++ ){
		$sql="update orders set status=3, current_order=0 where user_id='" . $result_next[$next]['user_id'] . "' and current_order=1";	
		mysql_query($sql);
		mysql_query("delete from order_cancel_from_next_week where id='" . $result_next[$next]['id'] . "' ");
	}
}

$msg = "Cron file successfully executed  to to cancel next week order: ". date("d/m/Y h:i:s A");
mail("mailuttam@webgrity.com","convert next week order to current week order executed",$msg);

?>