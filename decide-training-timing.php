<?php
include_once ("includes/configuration.php");
$id = intval($_REQUEST['id']);
$user_can_download_pdf=mysql_result(mysql_query("select user_can_download_pdf from meal_plan_category where id='" . $id . "'"),0,0);
echo intval($user_can_download_pdf);
?>