<?php
include_once ("includes/header.php");

//***************  chose your meal plan ***************************************//
//** chose your meal plan and unset  fill the questionnaire and  Customize your meal plan **//
//***********************************************************************************************//
if (isset($_POST['enter']) && $_POST['enter'] == "continue_order" && trim($_POST['frm_choose_your_meal_plan']) == $_SESSION['frm_choose_your_meal_plan']) {
	
	$_SESSION['choose_your_meal_plan']['exercising_to_speed_up']=trim($_REQUEST['exercising_to_speed_up']);
	
	if(trim($_SESSION['choose_your_meal_plan']['exercising_to_speed_up']) == "no"){
		if(isset($_SESSION['choose_your_meal_plan']['part_of_the_day_usually_train'])) 
			unset($_SESSION['choose_your_meal_plan']['part_of_the_day_usually_train']);		
	}else{
		$_SESSION['choose_your_meal_plan']['part_of_the_day_usually_train']=trim($_REQUEST['part_of_the_day_usually_train']);		
	}
	
	$general_func -> header_redirect($general_func -> site_url . "order-review/");
	
} else if (isset($_REQUEST['enter']) && $_REQUEST['enter'] == "choose_your_meal_plan" && (trim($_POST['frm_choose_your_meal_plan']) == $_SESSION['frm_choose_your_meal_plan'] || trim($_REQUEST['frm_choose_your_meal_plan']) == "yes" )) {
    $_SESSION['personalize_meal']=0;
 
	if (isset($_SESSION['fill_the_questionnaire']))
		unset($_SESSION['fill_the_questionnaire']);

	if (isset($_SESSION['customize_your_meal_plan']))
		unset($_SESSION['customize_your_meal_plan']);

	$_SESSION['choose_your_meal_plan'] = array();

	$_SESSION['choose_your_meal_plan']['category_meal_plan'] = intval($_REQUEST['category_meal_plan']);

	$result_meal_plan = $db -> fetch_all_array("select p.*,c.name as category_name,user_can_download_pdf from meal_plans p, meal_plan_category c where p.meal_plan_category_id=c.id and  p.id='" . intval($_REQUEST['category_meal_plan']) . "' limit 1 ");

	$_SESSION['choose_your_meal_plan']['meal_plan_category_id'] = intval($_REQUEST['meal_plan_category_id']);
	$_SESSION['choose_your_meal_plan']['meal_plan_category'] = $result_meal_plan[0]['category_name'];
	$_SESSION['choose_your_meal_plan']['user_can_download_pdf'] = $result_meal_plan[0]['user_can_download_pdf'];
	$_SESSION['choose_your_meal_plan']['category_meal_plan'] = intval($_REQUEST['category_meal_plan']);
	$_SESSION['choose_your_meal_plan']['meal_plan_name'] = $result_meal_plan[0]['name'];
	$_SESSION['choose_your_meal_plan']['meal_plan_details'] = nl2br($result_meal_plan[0]['details']);
	$_SESSION['choose_your_meal_plan']['no_of_days'] = intval($result_meal_plan[0]['no_of_days']);
	$_SESSION['choose_your_meal_plan']['meal_per_day'] = intval($result_meal_plan[0]['meal_per_day']);
	$_SESSION['choose_your_meal_plan']['snack_per_day'] = intval($result_meal_plan[0]['snack_per_day']);
		
	//***************  set default meals for customization ***************************************//	
	$_SESSION['default']['meal_plan_category_id']=intval($_REQUEST['meal_plan_category_id']);
	$_SESSION['default']['no_of_days']=intval($result_meal_plan[0]['no_of_days']);
	$_SESSION['default']['meals_per_day']=intval($result_meal_plan[0]['meal_per_day']);
	$_SESSION['default']['snacks_per_days']=intval($result_meal_plan[0]['snack_per_day']);
	//***********************************************************************************************//	
}

$total_price = 0;

if (isset($_SESSION['choose_your_meal_plan']['category_meal_plan']) && intval($_SESSION['choose_your_meal_plan']['category_meal_plan']) > 0) {
 	$_SESSION['personalize_meal']=0;
	$sql_meals = "select which_day,meal_time,meal_size,meal_id,show_nutritional_price,(select meal_price from meals_sizes_prices where meal_id=d.meal_id and meal_size=d.meal_size) as price,m.name,details,photo_name,energy,calories,protein,fat_total,carbohydrates,carbs_veggies,with_or_without_sauce from meal_plan_meals d left join meals m on d.meal_id=m.id where d.meal_plan_id='" . intval($_SESSION['choose_your_meal_plan']['category_meal_plan']) . "' and type=1 order by which_day,meal_time ASC";
	$result_default_meals = $db -> fetch_all_array($sql_meals);
	$total_default_meals = count($result_default_meals);
	$default_meals = array();
	for ($i = 0; $i < $total_default_meals; $i++) {
		$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_id'] = $result_default_meals[$i]['meal_id'];
		$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_name'] = $result_default_meals[$i]['name'];
		$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_size'] = $result_default_meals[$i]['meal_size'];
		$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['price'] = $result_default_meals[$i]['price'];
		$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['details'] = $result_default_meals[$i]['details'];
		$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['photo_name'] = $result_default_meals[$i]['photo_name'];
		$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['energy'] = $result_default_meals[$i]['energy'];
		$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['calories'] = $result_default_meals[$i]['calories'];
		$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['protein'] = $result_default_meals[$i]['protein'];
		$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['fat_total'] = $result_default_meals[$i]['fat_total'];
		$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['carbohydrates'] = $result_default_meals[$i]['carbohydrates'];
		$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['carbs_veggies'] = $result_default_meals[$i]['carbs_veggies'];
		$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['with_or_without_sauce'] = $result_default_meals[$i]['with_or_without_sauce'];
		$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_category_name'] = $result_default_meals[$i]['meal_category_name'];
		$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['show_nutritional_price'] = $result_default_meals[$i]['show_nutritional_price'];

		//***************  set default meals for customization ******************************************//			
		$_SESSION['default']['meals'][$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_id']=$result_default_meals[$i]['meal_id'];
		$_SESSION['default']['meals'][$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['size']=$result_default_meals[$i]['meal_size'];
		//***********************************************************************************************//	


		$total_price += $result_default_meals[$i]['price'];
	}

	$sql_snacks = "select which_day,meal_time,meal_id,price,name,details,photo_name,meal_size as qty from meal_plan_meals  d left join snacks s on d.meal_id=s.id where d.meal_plan_id='" . intval($_SESSION['choose_your_meal_plan']['category_meal_plan']) . "' and type=2 order by which_day,meal_time ASC";
	$result_default_snacks = $db -> fetch_all_array($sql_snacks);
	$total_default_snacks = count($result_default_snacks);

	$default_snacks = array();

	for ($i = 0; $i < $total_default_snacks; $i++) {
		$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['snack_id'] = $result_default_snacks[$i]['meal_id'];
		$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['price'] = $result_default_snacks[$i]['price'];
		$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['name'] = $result_default_snacks[$i]['name'];
		$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['details'] = $result_default_snacks[$i]['details'];
		$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['photo_name'] = $result_default_snacks[$i]['photo_name'];
		$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['qty'] = $result_default_snacks[$i]['qty'];
		$total_price += $result_default_snacks[$i]['price'] * $result_default_snacks[$i]['qty'];
		
		//***************  set default meals for customization ******************************************//			
		$_SESSION['default']['snacks'][$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['snack_id']=$result_default_snacks[$i]['meal_id'];	
		$_SESSION['default']['snacks'][$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['qty']=$result_default_snacks[$i]['qty'];	
		//***********************************************************************************************//	
	}
}


$exercising_to_speed_up="yes";
if(isset($_SESSION['choose_your_meal_plan']['exercising_to_speed_up']))
	$exercising_to_speed_up=trim($_SESSION['choose_your_meal_plan']['exercising_to_speed_up']);

$part_of_the_day_usually_train="morning";
if(isset($_SESSION['choose_your_meal_plan']['part_of_the_day_usually_train']))
	$part_of_the_day_usually_train=trim($_SESSION['choose_your_meal_plan']['part_of_the_day_usually_train']);

//print_r ($_SESSION['choose_your_meal_plan']);


$sql_content="select select_meal_plan_page_left_heading,select_meal_plan_page_left_content,select_meal_plan_page_right_heading,select_meal_plan_page_right_content,set_meal_plan_modification from dynamic_pages where id=1 limit 1";
$result_content=$db->fetch_all_array($sql_content);


?>

<script type="text/javascript">
	$(document).ready(function() {
		$(".getStdPnl2_container").slideToggle(1000);
		$(".dayPnl1_new li").mouseenter(function() {
			$(this).find(".tip_box").show();
		});

		$(".dayPnl1_new li").mouseleave(function() {
			$(this).find(".tip_box").hide();
		});

		$(".close_pop").click(function() {
			$(this).parent().parent().find(".tip_box").hide();
		});
	})
	function slideonlyone(thechosenone) {
		$('.accordion_content').each(function(index) {
			if($(this).attr("id") == thechosenone) {
				$(this).slideToggle(200);
				$(this).parent().find('.dayPnl2').toggleClass('active');
			} else {
				$(this).slideUp(200);
				$(this).parent().find('.dayPnl2').removeClass('active');
			}
		});
	}

</script>

<script type="text/javascript">
	function show_meal_plan_meals(val) {

		var error = 0;
		if(document.getElementById("meal_plan_category_id").value == '') {
			document.getElementById("meal_plan_category_id").style.border = "1px solid red";
			error++;
		} else {
			document.getElementById("meal_plan_category_id").style.border = "1px solid #CBD2BB";
		}

		if(document.getElementById("category_meal_plan").value == '') {
			document.getElementById("category_meal_plan").style.border = "1px solid red";
			error++;
		} else {
			document.getElementById("category_meal_plan").style.border = "1px solid #CBD2BB";
		}

		if(error > 0) {
			return false;
		} else {
			$("#plan_meals").slideToggle(1500);
			$.get("meal-plan-meals.php?meal_plan_category_id=" + document.getElementById("meal_plan_category_id").value + "&category_meal_plan=" + document.getElementById("category_meal_plan").value, function(data) {

				$("#plan_meals").html(data);
				$("#plan_meals").slideToggle(1500);
			});
		}
	}

	function collect_meal_plans(val) {
		$("#plan_meals").html('<p><?=$result_content[0]['select_meal_plan_page_right_content']?></p>');
		document.getElementById('category_meal_plan').options.length = 0;
		document.getElementById("category_meal_plan").options[0] = new Option("Choose your meal plan", "");

		if(parseInt(val) > 0) {
			$.get("meal-categorywise-meal-plans.php?id=" + val, function(data) {
				document.getElementById("category_meal_plan").options[0] = new Option("Choose your meal plan", "");
				
				var return_data_array = data.split("-_-");				
				
				var return_data = return_data_array[1].split("#!");
				var length = return_data.length - 1;

				for(var i = 0; i < length; i++) {
					var options_value = return_data[i].split("~_~");
					document.getElementById("category_meal_plan").options[i + 1] = new Option(options_value[1], options_value[0]);
				}
				
				if(return_data_array[0] == 1){
					$("#training_time_id").show(1000);					
				}else{
					$("#training_time_id").hide(1000);				
				}
				
			});
		}
	}

	function decide_training_part(val) {
		if(parseInt(val) == 1)
			$("#div_part_of_the_day_usually_train").show(1000);
		else
			$("#div_part_of_the_day_usually_train").hide(1000);
	}

</script>

<div class="inrBnr">
	<?php $db_common -> static_page_banner($dynamic_content['page_id']); ?>
</div>
<div class="mealPln right_pop">
	<div class="mainDiv2">
		<form name="choose_your_meal_plan" method="post" action="select-your-meal-plan/#meal" onsubmit="return validate_choose_your_meal_plan();">
		<div class="mealPlnColOne normal_select main_goal_drop_down">
			<input type="hidden" name="enter" value="continue_order" />
			<input type="hidden" name="frm_choose_your_meal_plan" value="<?=$_SESSION['frm_choose_your_meal_plan'] ?>" />
			
				
				<h1><?php echo $result_content[0]['select_meal_plan_page_left_heading']; ?></h1>
				<p>
					<?php echo nl2br($result_content[0]['select_meal_plan_page_left_content']); ?>
				</p>
				<br>
				<br>
				<p>
					<strong>Select your Main Goal</strong>
				</p>
				<label class="custom-select">
					<select name="meal_plan_category_id" id="meal_plan_category_id" class="selStlOne" onchange="collect_meal_plans(this.value);" >
						<option value="">Choose your main goal</option>
						<?php
						$sql_meal_plan_cat="select id,name from meal_plan_category where status=1 and id IN(select DISTINCT(meal_plan_category_id) from meal_plans where id IN (select DISTINCT(meal_plan_id) from meal_plan_meals)) order by display_order + 0 ASC";
						$result_meal_plan_cat=$db->fetch_all_array($sql_meal_plan_cat);
						$total_meal_plan_cat=count($result_meal_plan_cat);

						for($cat=0; $cat < $total_meal_plan_cat; $cat++){ ?>
						<option value="<?=$result_meal_plan_cat[$cat]['id'] ?>" <?=$_SESSION['choose_your_meal_plan']['meal_plan_category_id'] == $result_meal_plan_cat[$cat]['id'] ? 'selected="selected"' : ''; ?>><?=$result_meal_plan_cat[$cat]['name'] ?></option>
						<?php } ?>
					</select> </label>
					<p>
						<strong>Meal Plan</strong>
					</p>
					<label class="custom-select" style="padding-bottom: 20px;" >
						<select name="category_meal_plan" id="category_meal_plan" class="selStlOne" onchange="show_meal_plan_meals(this.value)" >
							<option value="">Choose your meal plan </option>
							<?php
							$sql_meal_plan="select id,name from meal_plans where status=1 and meal_plan_category_id='" . $_SESSION['choose_your_meal_plan']['meal_plan_category_id'] . "' and id IN(select DISTINCT(meal_plan_id) from meal_plan_meals) order by name ASC";
							$result_meal_plan=$db->fetch_all_array($sql_meal_plan);
							$total_meal_plan=count($result_meal_plan);

							for($plan=0; $plan < $total_meal_plan; $plan++){

						//***********************  calculate price ************************************//
								$price1 =0.00;

								$sql_meals1="select (select meal_price from meals_sizes_prices where meal_id=d.meal_id and meal_size=d.meal_size) as price from meal_plan_meals d left join meals m on d.meal_id=m.id where d.meal_plan_id='" . intval($result_meal_plan[$plan]['id']) . "' and type=1 ";
								$result_default_meals1=$db->fetch_all_array($sql_meals1);
								$total_default_meals1=count($result_default_meals1);

								$default_meals1=array();

								for($i=0; $i < $total_default_meals1; $i++ ){
									$price1 += $result_default_meals1[$i]['price'];
								}

								$sql_snacks1="select meal_size,price from meal_plan_meals d left join snacks m on d.meal_id=m.id where d.meal_plan_id='" . intval($result_meal_plan[$plan]['id']) . "' and type=2 order by which_day,meal_time ASC";
								$result_default_snacks1=$db->fetch_all_array($sql_snacks1);
								$total_default_snacks1=count($result_default_snacks1);

								$default_snacks1=array();

								for($i=0; $i < $total_default_snacks1; $i++ ){
									$price1 += intval($result_default_snacks1[$i]['meal_size']) * $result_default_snacks1[$i]['price'];
								}
								
								if($general_func->meal_plan_amout_for_training_cost > 0)
									$price1 += $general_func->meal_plan_amout_for_training_cost;
	
								
						//******************************************************************************//

								?>
								<option value="<?=$result_meal_plan[$plan]['id'] ?>" <?=$_SESSION['choose_your_meal_plan']['category_meal_plan'] == $result_meal_plan[$plan]['id'] ? 'selected="selected"' : ''; ?>><?=$result_meal_plan[$plan]['name'] ?> </option>
								<?php } ?>
							</select><!--  - $<?=number_format($price1, 2) ?> p/w --> </label>
							<div class="melPlnrFrmRght melPlnrFrmRght-type-two" id="training_time_id" style="display: <?=(isset($_SESSION['choose_your_meal_plan']['user_can_download_pdf']) && $_SESSION['choose_your_meal_plan']['user_can_download_pdf']==1)?'block;':'none;'; ?>">
								<ul>
									<li>Will you be exercising to speed up your results?<br>
										<input type="radio" id="r1" name="exercising_to_speed_up" value="yes" checked="checked" onclick="decide_training_part(1);" <?=$exercising_to_speed_up == "yes"?'checked="checked"':''; ?> />
										<label for="r1"><span></span>Yes</label>
										<input type="radio" id="r2"  name="exercising_to_speed_up" value="no" onclick="decide_training_part(0);" <?=$exercising_to_speed_up == "no"?'checked="checked"':''; ?>   />
										<label for="r2"><span></span>No</label>
									</li>
									<li id="div_part_of_the_day_usually_train" style="display: <?=$exercising_to_speed_up == "yes"?'block':'none'; ?>;">What part of the day would you usually train?<br>
										<input type="radio" id="r3" name="part_of_the_day_usually_train" value="morning" <?=$part_of_the_day_usually_train == "morning"?'checked="checked"':''; ?>  />
										<label for="r3"><span></span>Morning</label>
										<input type="radio" id="r4" name="part_of_the_day_usually_train" value="lunch_time" <?=$part_of_the_day_usually_train == "lunch_time"?'checked="checked"':''; ?> />
										<label for="r4"><span></span>Lunch Time</label>
										<input type="radio" id="r5" name="part_of_the_day_usually_train" value="after_work" <?=$part_of_the_day_usually_train == "after_work"?'checked="checked"':''; ?> />
										<label for="r5"><span></span>After Work</label>
										<input type="radio" id="r6" name="part_of_the_day_usually_train" value="evening" <?=$part_of_the_day_usually_train == "evening"?'checked="checked"':''; ?> />
										<label for="r6"><span></span>Evening</label>
									</li>
								</ul>
							</div>
						
					</div>
					<div class="mealPlnColTwo">
						<h1><?php echo $result_content[0]['select_meal_plan_page_right_heading']; ?></h1>

						<br class="clear">

						<div class="sedulePnl" id="plan_meals">
							<?php if(isset($_SESSION['choose_your_meal_plan']['category_meal_plan']) && intval($_SESSION['choose_your_meal_plan']['category_meal_plan']) > 0){
								for($day=1; $day <= intval($_SESSION['choose_your_meal_plan']['no_of_days']); $day++){ ?>
								<div class="dayPnl">
									<a class="dayPnl2" id="accordiontitle<?=$day ?>" href="javascript:slideonlyone('accordioncontent<?=$day ?>');"><h5><span></span>Day <?=$day ?></h5></a>
									<br class="clear">
									<div class="dayPnlTgl accordion_content" id="accordioncontent<?=$day ?>">
										<div class="dayPnl1 dayPnl1_new">
											<ul>
												<?php  for($time=1; $time <= intval($_SESSION['choose_your_meal_plan']['meal_per_day']); $time++ ){
													?>
													<li>
														<span>Meal <?=$time ?> :</span><span><?=$default_meals[$day][$time]['meal_name'] ?></span>
														<div class="tip_box" style="z-index: 99999;" >
															<div class="close_pop"></div>
															<div class="tip_angle"></div>
															<div class="tip_head">
																<?=$default_meals[$day][$time]['meal_name'] ?>
															</div>
															<div class="tip_row">
																<div class="tip_column_container">
																	<div class="tip_column">
																		<div class="tip_column_info_row">
																			<div class="info_tab">
																				Net Weight :
																			</div>
																			<div class="info_tab">
																				<?=$default_meals[$day][$time]['meal_size']; ?>g
																			</div>
																		</div>
																		<?php if($default_meals[$day][$time]['show_nutritional_price'] == 1){ ?>
																		<div class="tip_column_info_row">
																			<div class="info_tab">
																				Energy :
																			</div>
																			<div class="info_tab">
																				<?=$db_common->nutritional_value($default_meals[$day][$time]['energy'],$default_meals[$day][$time]['meal_size'])?> kcal
																			</div>
																		</div>
																		<div class="tip_column_info_row">
																			<div class="info_tab">
																				Protein :
																			</div>
																			<div class="info_tab">
																				<?=$db_common->nutritional_value($default_meals[$day][$time]['protein'],$default_meals[$day][$time]['meal_size']) ?> g
																			</div>
																		</div>
																		<?php } ?>
																		<div class="tip_column_info_row">
																			<div class="info_tab">
																				Carbs :
																			</div>
																			<div class="info_tab">
																				<?php if(intval($default_meals[$day][$time]['carbs_veggies']) == 3 || intval($default_meals[$day][$time]['carbs_veggies']) == 1){
																					?><img src="images/tip_yes.png" style="margin-top: 4px;" />
																					<?php }else{ ?><img src="images/tip_no.png" style="margin-top: 4px;" />
																					<?php } ?>
																				</div>
																			</div>
																			<div class="tip_column_info_row">
																				<div class="info_tab">
																					Sauce :
																				</div>
																				<div class="info_tab">
																					<?php if(intval($default_meals[$day][$time]['with_or_without_sauce']) == 1){
																						?><img src="images/tip_yes.png" style="margin-top: 4px;" />
																						<?php }else{ ?><img src="images/tip_no.png" style="margin-top: 4px;" />
																						<?php } ?>
																					</div>
																				</div>
																			</div>
																			<div class="tip_column">
																				<?php if($default_meals[$day][$time]['show_nutritional_price'] == 1){ ?>
																				<div class="tip_column_info_row">
																					<div class="info_tab">Calories :</div>
																					<div class="info_tab"><?=$db_common->nutritional_value($default_meals[$day][$time]['calories'],$default_meals[$day][$time]['meal_size'])?>g</div>
																				</div>
																				<div class="tip_column_info_row">
																					<div class="info_tab">
																						Carbs :
																					</div>
																					<div class="info_tab">
																						<?=$db_common->nutritional_value($default_meals[$day][$time]['carbohydrates'],$default_meals[$day][$time]['meal_size']) ?>g
																					</div>
																				</div>
																				<div class="tip_column_info_row">
																					<div class="info_tab">
																						Total Fat :
																					</div>
																					<div class="info_tab">
																						<?=$db_common->nutritional_value($default_meals[$day][$time]['fat_total'],$default_meals[$day][$time]['meal_size']) ?>g
																					</div>
																				</div>
																				<div class="tip_column_info_row">
																					<div class="info_tab">
																						Price :
																					</div>
																					<div class="info_tab">
																						$<?=$default_meals[$day][$time]['price'] ?>
																					</div>
																				</div>
																				<?php } ?>
																				<div class="tip_column_info_row">
																					<div class="info_tab">
																						Veggies :
																					</div>
																					<div class="info_tab">
																						<?php if(intval($default_meals[$day][$time]['carbs_veggies']) == 3 || intval($default_meals[$day][$time]['carbs_veggies']) == 2){
																							?><img src="images/tip_yes.png" style="margin-top: 5px;" />
																							<?php }else{ ?><img src="images/tip_no.png" style="margin-top: 5px;" />
																							<?php } ?>
																						</div>
																					</div>
																				</div>
																			</div>
																		</div>
																		<div class="tip_row">
																			<div class="tip_descrip">
																				<p>
																					<?php if(trim($default_meals[$day][$time]['photo_name']) != NULL){
																						?><img class="lefted_img" align="left" src="meal_main/small/<?=trim($default_meals[$day][$time]['photo_name']) ?>" width="120">
																						<?php } ?>

																						<?=nl2br($default_meals[$day][$time]['details']) ?>
																					</p>
																				</div>
																			</div>
																		</div>
																	</li>
																	<?php }
																		for($time=1; $time <= intval($_SESSION['choose_your_meal_plan']['snack_per_day']); $time++ ){
																		?>
																		<li>
																			<span>Snack <?=$time ?> :</span><span><?=$default_snacks[$day][$time]['name'] ?></span>
																			<div class="tip_box" style="z-index: 99999;" >
																				<div class="close_pop"></div>
																				<div class="tip_angle"></div>
																				<div class="tip_head">
																					<?=$default_snacks[$day][$time]['name'] ?>
																				</div>
																				<div class="tip_row">
																					<div class="tip_column_container">
																						<div class="tip_column">
																							<div class="tip_column_info_row">
																								<div class="info_tab">
																									Price :
																								</div>
																								<div class="info_tab">
																									$<?=$default_snacks[$day][$time]['price'] ?>
																								</div>
																							</div>
																						</div>
																						<div class="tip_column">
																							<div class="tip_column_info_row">
																								<div class="info_tab">
																									Qty :
																								</div>
																								<div class="info_tab">
																									<?=$default_snacks[$day][$time]['qty'] ?>
																								</div>
																							</div>
																						</div>
																					</div>
																				</div>
																				<div class="tip_row">
																					<div class="tip_descrip">
																						<p>
																							<?php if(trim($default_snacks[$day][$time]['photo_name']) != NULL){
																								?><img class="lefted_img" align="left" src="snack_main/small/<?=trim($default_snacks[$day][$time]['photo_name']) ?>" width="120">
																								<?php } ?>
																								<?=nl2br($default_snacks[$day][$time]['details']) ?>
																							</p>
																						</div>
																					</div>
																				</div>
																			</li>
																			<?php } ?>
																		</ul>
																	</div>
																</div>
															</div>
															<?php } ?>
															<br class="clear">
															<div class="checkout_row" style="border:none; margin-top:0"><div style="width:200px; margin:0 auto"><input name="submit" type="submit" value="Order Now" style="width:100%" /></div>
                                                            
                                                            <p style="color: #ff0000;font: 14px/18px 'open_sansregular';text-align: center; padding:20px 0 10px;"><?php echo $result_content[0]['set_meal_plan_modification']; ?></p>
                                <div class="dayPnlBtn" style="margin-top:0">	
									<input name="button" type="button" value="Modify Meal Plan" onclick="location.href='<?=$general_func->site_url?>customize-your-own/#meal'" style="width:90%; margin:0 5%" class="mdfyMealPln" />
								</div>
                                                            </div>
															<?php }else{																
																 echo '<p>' . nl2br($result_content[0]['select_meal_plan_page_right_content']).'</p>'; 
																}
															?>
														</div>
													</div>
													</form>
												</div>
											</div>
											<?php
											include_once ("includes/footer.php");
											?>