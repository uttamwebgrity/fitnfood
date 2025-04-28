<?php
include_once("../includes/configuration.php");

if(!isset($_SESSION['admin_login']) || $_SESSION['admin_login']!="yes"){
	$_SESSION['message']="Please login to view this page!";
	$general_func->header_redirect("index.php");
}


session_unset();
session_destroy();
session_start();


$_SESSION['message']="Successfully Logged out.";
$general_func->header_redirect("index.php");
?>
