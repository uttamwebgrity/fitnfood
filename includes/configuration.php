<?php
ob_start();
session_start();
error_reporting(0);


require_once("classes/database.class.php");
require_once("classes/database.FNF.class.php");

require_once("classes/general.class.php");
require_once("classes/validator.class.php");
include_once("classes/encrypt-decrypt.class.php");


include_once("classes/upload.class.php");
include_once("classes/send_mail.class.php");
include_once("classes/security.validator.class.php");

include_once("facebook/facebook.php");


//**************************************************************************************************************//
$db = new Database(); //******************  Database class
$db_common = new FNF(); //******************  HAPPYINSTYLE Database class
$security_validator=new Security_validator();

$general_func = new General(); //********* General class
$validator = new Validator(); //********* General class
$EncDec = new EncryptDecrypt(); //********* EncryptDecrypt class

$upload = new uploadclass();
$sendmail = new send_mail();


//**********************  General value *******************************************//
$sql_general="select option_name,option_value from tbl_options where admin_admin_id=1 and (option_name='site_title' or  option_name='admin_recoed_per_page' or option_name='front_end_recoed_per_page'";
$sql_general .=" or option_name='linkedin' or option_name='twitter' or option_name='instagram' or option_name='facebook' or option_name='register_without_cc_details' or option_name='linkedin'  or option_name='mail_us'  or  option_name='address' or option_name='phone' or option_name='email'";
$sql_general .=" or option_name='meal_per_day_min' or option_name='meal_per_day_max' or option_name='trainer_referral_commission'  or option_name='gym_referral_commission' or option_name='pickup_cost' or option_name='get_started_video' or option_name='delivery_email_reminder_status' or option_name='get_started_content' or option_name='phone'  or option_name='site_address' or option_name='testimonials'  or option_name='meal_plan_amout_for_training_cost' or option_name='home_page_listing'  or option_name='global_meta_title' or option_name='global_meta_keywords'  or option_name='global_meta_description')";
$result_general=$db->fetch_all_array($sql_general);
$total_options=count($result_general); 



if( $total_options > 0){
	for($i=0; $i <$total_options; $i++){
		$$result_general[$i]['option_name']=trim($result_general[$i]['option_value']);
	}
} 
 
 
$general_func->site_title=$site_title; 

$general_func->site_url="http://uttam/fitnfood/website/";
$general_func->admin_url="http://uttam/fitnfood/website/administrator/";

/*$general_func->site_url="http://beta.fitnfood.com.au/";
$general_func->admin_url="http://beta.fitnfood.com.au/administrator/";*/

$general_func->record_per_page=20;

$general_func->admin_recoed_per_page=$admin_recoed_per_page;
$general_func->front_end_recoed_per_page=$front_end_recoed_per_page;
$general_func->home_page_listing=$home_page_listing;

$general_func->global_meta_title=$global_meta_title;
$general_func->global_meta_keywords=$global_meta_keywords;
$general_func->global_meta_description=$global_meta_description;

$general_func->trainer_referral_commission=$trainer_referral_commission;
$general_func->gym_referral_commission=$gym_referral_commission;

$general_func->site_address=$site_address;
$general_func->phone=$phone;
$general_func->email=$email;

$general_func->facebook=$facebook;
$general_func->twitter=$twitter;
$general_func->google=$linkedin;
$general_func->youtube=$instagram;
$general_func->pickup_cost=$pickup_cost;



$general_func->mail_us=$mail_us;


$general_func->testimonials=$testimonials;

$general_func->register_without_cc_details=$register_without_cc_details;


$general_func->meal_plan_amout_for_training_cost=floatval($meal_plan_amout_for_training_cost);




$result_FB=$db->fetch_all_array("select app_id,app_secret from third_party_api where id =1 limit 1");


define('APP_ID', $result_FB[0]['app_id']);
define('APP_SECRET', $result_FB[0]['app_secret']);



date_default_timezone_set("Australia/Sydney");
$today_date=date("Y-m-d");	
$current_time_ms=time();
$current_date_time=date("Y-m-d H:i:s");

//************** edebit info. ***************************************//
$edNo=100100;//200704 
//*******************************************************************//

$GST=10;
?>