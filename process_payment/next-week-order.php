<?php
error_reporting(0);
/*****************************************************/
/********** 1 = Monday & 7 = Sunday
 * Week 47 is from Monday November 17, 2014 until (and including) Sunday November 23, 2014
 * * run at 0:01 AM Monday (once a week) 
/*****************************************************/
include_once("../includes/configuration.php");


$sql_next="select id,user_id from orders where status=5";
$result_next=$db->fetch_all_array($sql_next);
$total_next=count($result_next);
	
if($total_next > 0){
	for($next=0; $next < $total_next; $next++ ){
		$sql="update orders set status=3, current_order=0 where user_id='" . $result_next[$next]['user_id'] . "' and current_order=1";	
		mysql_query($sql);
		
		$sql="update orders set status=0, current_order=1 where user_id='" . $result_next[$next]['user_id'] . "' and status=5";	
		mysql_query($sql);
	}
}

//$msg = "Cron file successfully executed  to convert next week order to current week order: ". date("d/m/Y h:i:s A");
//mail("mailuttam@webgrity.com","convert next week order to current week order executed",$msg);

?>