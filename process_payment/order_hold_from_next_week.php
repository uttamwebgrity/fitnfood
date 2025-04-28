<?php
error_reporting(0);
/*****************************************************/
/********** 1 = Monday & 7 = Sunday
 * Week 47 is from Monday November 17, 2014 until (and including) Sunday November 23, 2014
 every day at 1AM
/*****************************************************/
include_once("../includes/configuration.php");


$sql_next="select id,order_id,user_id from order_hold_from_next_week where hold_from <= '" . $today_date . "'";

$result_next=$db->fetch_all_array($sql_next);
$total_next=count($result_next);
	
if($total_next > 0){
	for($next=0; $next < $total_next; $next++ ){
		$sql="update orders set status=2 where user_id='" . $result_next[$next]['user_id'] . "' and current_order=1";	
		mysql_query($sql);
		mysql_query("delete from order_hold_from_next_week where id='" . $result_next[$next]['id'] . "' ");
	}
}

$msg = "Cron file successfully executed  to  hold next week order: ". date("d/m/Y h:i:s A");
mail("mailuttam@webgrity.com","convert next week order to hold order executed",$msg);

?>