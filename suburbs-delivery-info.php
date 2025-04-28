<?php
include_once("includes/configuration.php");

$suburbs=$_REQUEST['suburbs'];

$queryString_array=array();
	
$queryString_array=explode(",",trim($suburbs));
$array_size=sizeof($queryString_array);



$result_suburb_info=$db->fetch_all_array("select suburb_postcode,delivery_day,payment_debit_day,order_cutoff_day,order_cutoff_time from suburb where suburb_name ='" . trim($queryString_array[0]) . "' and suburb_state = '" . trim($queryString_array[1]) . "' and suburb_postcode  ='" . trim($queryString_array[2]) . "' limit 1");

$return_value="";

$drop_down="";


$day_of_the_week=date("w") == 0 ?7:date("w");
										
if($day_of_the_week > $result_suburb_info[0]['order_cutoff_day'] ||  ($day_of_the_week == $result_suburb_info[0]['order_cutoff_day'] && strtotime(date("H:i:s")) > strtotime($result_suburb_info[0]['order_cutoff_time']))){
	//** Next week order *****//
	$one_week_time=86400 * 7;
	$first_start_date=strtotime('next '.$general_func->day_name($result_suburb_info[0]['delivery_day'])) + $one_week_time;
												
	$four_week_time=$one_week_time*4;
	$total_time=$first_start_date + $four_week_time;
	for($i=$first_start_date; $i < $total_time; $i +=$one_week_time ){
		$drop_down .= $i . "~_~" . date("jS M. l, Y ",$i) . "~_~" ;													
	}											
}else{
	//** Current week order **//											
	$first_start_date=strtotime('next '.$general_func->day_name($result_suburb_info[0]['delivery_day']));
											
	$one_week_time=86400 * 7;
	$four_week_time=$one_week_time*4;
	$total_time=$first_start_date + $four_week_time;
											
	for($i=$first_start_date; $i < $total_time; $i +=$one_week_time ){
		$drop_down .= $i . "~_~ " . date("jS M. l, Y ",$i)  . "~_~" ;														
	}
}	
$return_value .= $drop_down;
$return_value .= "#!";
$return_value .= $general_func->day_name($result_suburb_info[0]['delivery_day']) . "~_~" .  $general_func->day_name($result_suburb_info[0]['payment_debit_day'])  . "~_~" . $general_func->day_name($result_suburb_info[0]['order_cutoff_day']) ." ". date("h:i A",strtotime($result_suburb_info[0]['order_cutoff_time'])). "~_~" .  $result_suburb_info[0]['suburb_postcode'];

echo $return_value;
?>