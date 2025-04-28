<?php
include_once("../../includes/configuration.php");
$result_payment=$db->fetch_all_array("select order_id from payment where  order_status=1  order by order_id + 0 ASC");	
$total_payment=count($result_payment);

$meals_array=array();
$snacks_array=array();

for($p=0; $p < $total_payment; $p++ ){
	//**********************  Meals ordered *********************************************//	
	$result_meals=$db->fetch_all_array("select meal_id,meal_size,name,count(*) as total from order_meals o left join meals m on o.meal_id=m.id where order_id='" .$result_payment[$p]['order_id'] . "' and  type=1 GROUP BY meal_id,meal_size  order by name ASC");
	$total_meals=count($result_meals);
	
	for($m=0; $m < $total_meals; $m++ ){		
		if(isset($meals_array[$result_meals[$m]['meal_id']][$result_meals[$m]['meal_size']])){
			$meals_array[$result_meals[$m]['meal_id']][$result_meals[$m]['meal_size']]['qty'] += $result_meals[$m]['total'];			
		}else{			
			$meals_array[$result_meals[$m]['meal_id']][$result_meals[$m]['meal_size']]['qty']=$result_meals[$m]['total'];
			$meals_array[$result_meals[$m]['meal_id']][$result_meals[$m]['meal_size']]['name'] = $result_meals[$m]['name'];	
		}
	}
	//**********************  Snacks ordered *********************************************//	
	
	$result_snacks=$db->fetch_all_array("select meal_id,name,SUM(meal_size) as total from order_meals o left join snacks s on o.meal_id=s.id where order_id='" .$result_payment[$p]['order_id'] . "' and  type=2 GROUP BY meal_id  order by name ASC");
	$total_snacks=count($result_snacks);
	
	for($s=0; $s < $total_snacks; $s++ ){		
		if(isset($snacks_array[$result_snacks[$s]['meal_id']])){
			$snacks_array[$result_snacks[$s]['meal_id']]['qty'] += $result_snacks[$s]['total'];			
		}else{			
			$snacks_array[$result_snacks[$s]['meal_id']]['name'] = $result_snacks[$s]['name'];	
			$snacks_array[$result_snacks[$s]['meal_id']]['qty'] = $result_snacks[$s]['total'];	
		}
	}
	//************************************************************************************//
}


$total_meals_array=count($meals_array);
if($total_meals_array > 0){
	foreach($meals_array as $key => $value ){		
		foreach($value as $qty => $qty_value ){
			echo $qty;
			echo ":";
			echo $value[$qty]['name'];
			echo ":";
			echo $value[$qty]['qty'];
			echo "<br/>";			
		}		
	}	
}


$total_snacks_array=count($snacks_array);
if($total_snacks_array > 0){
	foreach($snacks_array as $key => $value ){
		echo $key;
		echo ":";
		echo $value['name'];
		echo ":";
		echo $value['qty'];
		echo "<br/>";			
	}		
}
?>