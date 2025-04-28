<?php
include_once ("includes/configuration.php");

$id = intval($_REQUEST['id']);


$user_can_download_pdf=mysql_result(mysql_query("select user_can_download_pdf from meal_plan_category where id='" . $id . "'"),0,0);

$return_value = intval($user_can_download_pdf) . "-_-";

$sql_meal_plan = "select id,name from meal_plans where status=1 and meal_plan_category_id='" . $id . "' and id IN(select DISTINCT(meal_plan_id) from meal_plan_meals) order by name ASC";
$result_meal_plan = $db -> fetch_all_array($sql_meal_plan);
$total_meal_plan = count($result_meal_plan);



for ($plan = 0; $plan < $total_meal_plan; $plan++) {

	$price = 0.00;

	$sql_meals = "select (select meal_price from meals_sizes_prices where meal_id=d.meal_id and meal_size=d.meal_size) as price from meal_plan_meals d left join meals m on d.meal_id=m.id where d.meal_plan_id='" . intval($result_meal_plan[$plan]['id']) . "' and type=1 ";
	$result_default_meals = $db -> fetch_all_array($sql_meals);
	$total_default_meals = count($result_default_meals);

	$default_meals = array();

	for ($i = 0; $i < $total_default_meals; $i++) {
		$price += $result_default_meals[$i]['price'];
	}

	$sql_snacks = "select meal_size,price from meal_plan_meals d left join snacks m on d.meal_id=m.id where d.meal_plan_id='" . intval($result_meal_plan[$plan]['id']) . "' and type=2 order by which_day,meal_time ASC";
	$result_default_snacks = $db -> fetch_all_array($sql_snacks);
	$total_default_snacks = count($result_default_snacks);

	$default_snacks = array();

	for ($i = 0; $i < $total_default_snacks; $i++) {
		$price += intval($result_default_snacks[$i]['meal_size']) * $result_default_snacks[$i]['price'];
	}

	if($general_func->meal_plan_amout_for_training_cost > 0)
		$price += $general_func->meal_plan_amout_for_training_cost;
	
	
	$return_value .= $result_meal_plan[$plan]['id'] . "~_~" . $result_meal_plan[$plan]['name'] . " - $ " . number_format($price,2) . " p/w #!";
}
echo $return_value;

?>