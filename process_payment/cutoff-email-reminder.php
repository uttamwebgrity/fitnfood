<?php
error_reporting(0);
/*****************************************************/
/********** 1 = Monday & 7 = Sunday
 * Week 47 is from Monday November 17, 2014 until (and including) Sunday November 23, 2014
/*****************************************************/
include_once("../includes/configuration.php");

if(intval($delivery_email_reminder_status) == 1){
	
	$todays_date=date("Y-m-d");
	$todays_day=date("N");
	
	if($todays_day > 5){//*** today is Sat & Sun
		$search_cutoffday= $todays_day - 5;	
	}else{//**** Mon, Tue Wed, Thu & Fri
		$search_cutoffday= $todays_day + 2;		
	}
	
	//************ need to consider hold orders
	
	$sql="select CONCAT(fname,' ',lname) as name,email_address,order_cutoff_time from orders o";
	$sql .=" left join users u on o.user_id=u.id ";
	$sql .=" left join suburb s on u.suburb_id=s.id ";
	$sql .=" where order_cutoff_day='" . $search_cutoffday . "' and current_order=1 and (o.status=0 or o.status=1) and DATE_SUB(order_start_date, INTERVAL 9 DAY) < '" . $todays_date . "' and edPI_created=1 and cc_or_dd_created=1 and debit_token IS NOT NULL";
		
	$result=$db->fetch_all_array($sql);
	$total_users=count($result);
	
	
	if($total_users > 0){
		$email_template=$db->fetch_all_array("select template_subject,template_content from email_template where id=3 limit 1");
		$order_cutoff_day = $general_func -> day_name($search_cutoffday) . " " . date("h:i A", strtotime($result[0]['order_cutoff_time']));		 
		
				
		for($user=0; $user < $total_users; $user++ ){
			$email_content=$email_template[0]['template_content'];
			$email_content = str_replace("#name#",$result[$user]['name'], $email_content);
			$email_content = str_replace("#cutoffdaytime#", $order_cutoff_day, $email_content);
				
			
			$recipient_info=array();
			$recipient_info['recipient_subject']=$email_template[0]['template_subject'];
			$recipient_info['recipient_content']=$email_content;
			$recipient_info['recipient_email']=$result[$user]['email_address'];		
			$sendmail -> cutoff_reminder($recipient_info, $general_func->email,$general_func->site_title, $general_func->site_url);				
		}	
	}	
}

//$msg = "Cron file successfully executed  for cutoff email reminder: ". date("d/m/Y h:i:s A");
//mail("mailuttam@webgrity.com","cutoff email reminder executed",$msg);

?>