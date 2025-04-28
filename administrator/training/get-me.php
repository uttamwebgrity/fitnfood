<?php
include_once("../../includes/configuration.php");

$id = intval(trim($_REQUEST['id']));
$rs = mysql_fetch_object(mysql_query("select * from location_time_slots where id='" . $id . "' limit 1"));

list($start_hour,$start_min,)=@explode(":",$rs->start_time);
list($end_hour,$end_min,)=@explode(":",$rs->end_time);
echo $data=$rs->id . "~_~".$rs->trainer_id . "~_~". $rs->which_day . "~_~". $start_hour . "~_~". $start_min . "~_~". $end_hour . "~_~". $end_min;

?>