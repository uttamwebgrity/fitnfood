<?php
include_once ("includes/configuration.php");
if (!isset($_SESSION['user_login_type']) || trim($_SESSION['user_login_type']) != "users" || !isset($_SESSION['user_login_type'])) {
	$_SESSION['user_message'] = "Sorry, you have no permission to access this page!";
	$general_func -> header_redirect($general_func -> site_url);
}

if ($db_common -> user_has_an_order(intval($_SESSION['user_id'])) == 0) {
	$_SESSION['user_message'] = "Sorry, you have not made any order yet!";
	$general_func -> header_redirect($general_func -> site_url . "my-account/");
}

$meal_plan_category_id=$db_common->order_can_be_modified(intval($_SESSION['update_order_no']),$order_id);

if($meal_plan_category_id == 0){	
	$_SESSION['user_message'] = "Sorry, the order you are trying to modify is no longer exist!";
	$general_func -> header_redirect($general_func -> site_url . "my-account/");	
}

?>