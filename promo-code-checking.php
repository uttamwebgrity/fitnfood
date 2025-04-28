<?php
include_once("includes/configuration.php");

$promo_code=trim($_REQUEST['promo_code']);
$show="";

if(trim($promo_code) == NULL){
	$show="1~_~"."Please enter the promo code.";
}else{
	$rs_promo = $db->fetch_all_array("select * from  promo_codes where promo_code='" . mysql_real_escape_string(trim($promo_code)) . "' limit 1");
		
	if(count($rs_promo) == 0){
		$show="1~_~"."Sorry, your specified promo code does not exists.";	
	}else if(strtotime($rs_promo[0]['end_date']) < strtotime($today_date)){
		$show="1~_~"."Sorry, your specified promo code has been expired.";	
	}else if(intval($rs_promo[0]['user_id']) > 0 &&  !isset($_SESSION['user_id'])){
		$show="1~_~"."Sorry, please login and enter this promo code again.";	
	}else if(intval($rs_promo[0]['user_id']) > 0 &&  intval($_SESSION['user_id']) !=  intval($rs_promo[0]['user_id'])){
		$show="1~_~"."Sorry, you can not use this promo code.";	
	}else{
		
		$how_many_weeks="";
		
		if(intval($rs_promo[0]['how_many_week']) > 0){
			$how_many_weeks=" first ".intval($rs_promo[0]['how_many_week']). " weeks ";	
		}
		
		if(intval($rs_promo[0]['discount_type']) == 1){
			$show="0~_~"."Your will get $".$rs_promo[0]['discount_amount'] ." off of your " .  $how_many_weeks."  order amount." ;		
		}else if(intval($rs_promo[0]['discount_type']) == 2){
			$show="0~_~"."Your will get ".$rs_promo[0]['discount_amount'] ."% off of your  " . $how_many_weeks."  order amount." ;		
		}else{
			$show="0~_~"." You will get following items with your " . $how_many_weeks ." delivery: <br/><br/> ". nl2br($rs_promo[0]['gift_items']);		
		}
	}	
}
echo $show; 
?>