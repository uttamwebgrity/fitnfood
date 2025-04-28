<?php
$msg = "Cron test successful ".  date("d/m/Y H:i:s e");
mail("mailuttam@webgrity.com","Cron test",$msg);
?>