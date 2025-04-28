<?php
error_reporting(0);
/*****************************************************/
/********** 1 = Monday & 7 = Sunday
 * Week 47 is from Monday November 17, 2014 until (and including) Sunday November 23, 2014
/*****************************************************/
include_once("../includes/configuration.php");
$todays_date=date("Y-m-d");
$todays_day=date("N");

if($todays_day > 1 && $todays_day < 7){//*** Except Monday and Sunday
	$first_date_of_the_week=date("Y-m-d",strtotime('monday this week'));
	$last_date_of_the_week=date("Y-m-d",strtotime('sunday this week'));	
	
	$sql="select o.id,user_id,order_amount,suburb_id,debit_token,training_cost from orders o";
	$sql .=" left join users u on o.user_id=u.id ";
	$sql .=" left join suburb s on u.suburb_id=s.id ";
	$sql .=" where payment_debit_day='" . $todays_day . "' and current_order=1 and (o.status=0 or o.status=1) and order_start_date <= '" . $last_date_of_the_week . "' and edPI_created=1 and cc_or_dd_created=1 and debit_token IS NOT NULL";
	
	$result=$db->fetch_all_array($sql);
	$total_users=count($result);
	
	for($user=0; $user < $total_users; $user++ ){
		//***** insert data into transaction table ********************//
		$data=array();
		$data['user_id']=$result[$user]['user_id'];
		$data['order_id']=$result[$user]['id'];
		$data['order_amount']=$result[$user]['order_amount'];
		$data['training_cost']=$result[$user]['training_cost'];		
		$data['week_start_date']=$first_date_of_the_week;
		$data['week_end_date']=$last_date_of_the_week;
		$data['payment_date']=$current_date_time;
		$data['order_status']=0;
		$transaction_id=$db->query_insert("payment",$data);
		
		$payment_desc="Payment for order FNF - A000"  . $result[$user]['id'];		
		$edTR_url = "https://www.edebit.com.au/IS/edTR.ashx?edNo=" . $edNo . "&clNo=" . (3000 + $result[$user]['user_id']) . "&token=" . $result[$user]['debit_token'] . "&amt=" .  $result[$user]['order_amount'] . "&desc=" . urlencode($payment_desc) . "&id=". $transaction_id;
		$ch = curl_init($edTR_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$edTR_data = curl_exec($ch);
		curl_close($ch);
		//*************************************************************//
	}
}

//$msg = "Cron file successfully executed for sending debit request: ". date("d/m/Y h:i:s A");
//mail("mailuttam@webgrity.com","Debit request executed",$msg);
?>