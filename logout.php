<?php
include_once("includes/configuration.php");

/*session_unset();
session_destroy();
session_start(); */

unset($_SESSION['user_id']);	
unset($_SESSION['user_fname']);	
unset($_SESSION['user_lname']);	
unset($_SESSION['user_email_address']);	
unset($_SESSION['user_seo_link']);	
unset($_SESSION['user_login_type']);	
unset($_SESSION['user_login']);	
unset($_SESSION['after_login_form_id']);
unset($_SESSION['return_to_front_end']);
unset($_SESSION['user_path']);


$_SESSION['user_message'] = "You have successfully logged out!";
header("location:" . $general_func->site_url);
exit();
?>
