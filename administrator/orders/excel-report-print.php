<?php
session_start();
error_reporting(0);
$file_name=$_SESSION['print_name'];

$data=$_SESSION['print_data'];
$header=$_SESSION['print_header'];

header("Content-type: application/x-msdownload");
header("Content-Disposition: attachment; filename=$file_name");
header("Pragma: no-cache");
header("Expires: 0");

$print_me=$header ."\n". $data;

if(isset($_SESSION['print_data_snack']))
	$print_me .= $_SESSION['print_snack_header'] ."\n". $_SESSION['print_data_snack'];


print "$print_me";

unset($_SESSION['print_name']);
unset($_SESSION['print_data']);
unset($_SESSION['print_header']);
unset($_SESSION['print_snack_header']);
unset($_SESSION['print_data_snack']);
?>