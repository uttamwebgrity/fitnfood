<?php
$path_depth = "../../";

include_once($path_depth . "includes/configuration.php");

if(!isset($_SESSION['admin_login']) || $_SESSION['admin_login']!="yes"){
	$_SESSION['redirect_to']=substr($_SERVER['PHP_SELF'],strpos($_SERVER['PHP_SELF'],"administrator/") + 14);
   	$_SESSION['redirect_to_query_string']= $_SERVER['QUERY_STRING'];
	
    $_SESSION['message']="Please login to view this page!";
	$general_func->header_redirect($general_func->admin_url."index.php");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
</head>

<body onload="window.print();">
<?=mysql_result(mysql_query("select order_email_content from orders where id='" . intval($_REQUEST['id']). "'"),0,0)?>
</body>
</html>