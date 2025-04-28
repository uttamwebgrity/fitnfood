<?php
include_once ("includes/header.php");

//***************  chose your meal plan ***************************************//
//** chose your meal plan and unset  fill the questionnaire and  Customize your meal plan **//
//***********************************************************************************************//
if (isset($_POST['enter']) && $_POST['enter'] == "choose_your_meal_plan" && trim($_POST['frm_choose_your_meal_plan']) == $_SESSION['frm_choose_your_meal_plan']) {

	if (isset($_SESSION['fill_the_questionnaire']))
		unset($_SESSION['fill_the_questionnaire']);

	if (isset($_SESSION['customize_your_meal_plan']))
		unset($_SESSION['customize_your_meal_plan']);

	$_SESSION['choose_your_meal_plan'] = array();

	//$_SESSION['choose_your_meal_plan']['meal_plan_category']=@mysql_result(mysql_query("select name from meal_plan_category where id='" .intval($_REQUEST['meal_plan_category_id']). "' limit 1"),0,0);
	$_SESSION['choose_your_meal_plan']['category_meal_plan'] = intval($_REQUEST['category_meal_plan']);

	$result_meal_plan = $db -> fetch_all_array("select p.*,c.name as category_name from meal_plans p, meal_plan_category c where p.meal_plan_category_id=c.id and  p.id='" . intval(intval($_REQUEST['category_meal_plan'])) . "' limit 1 ");

	$_SESSION['choose_your_meal_plan']['meal_plan_category_id'] = intval($_REQUEST['meal_plan_category_id']);
	$_SESSION['choose_your_meal_plan']['meal_plan_category'] = $result_meal_plan[0]['category_name'];
	$_SESSION['choose_your_meal_plan']['category_meal_plan'] = intval($_REQUEST['category_meal_plan']);
	$_SESSION['choose_your_meal_plan']['meal_plan_name'] = $result_meal_plan[0]['name'];
	$_SESSION['choose_your_meal_plan']['meal_plan_details'] = nl2br($result_meal_plan[0]['details']);
	$_SESSION['choose_your_meal_plan']['no_of_days'] = intval($result_meal_plan[0]['no_of_days']);
	$_SESSION['choose_your_meal_plan']['meal_per_day'] = intval($result_meal_plan[0]['meal_per_day']);
}

$sql_meals = "select which_day,meal_time,meal_size,meal_id,(select meal_price from meals_sizes_prices where meal_id=d.meal_id and meal_size=d.meal_size) as price,m.name,details,photo_name,energy,calories,protein,fat_total,carbohydrates,carbs_veggies,with_or_without_sauce from meal_plan_meals d left join meals m on d.meal_id=m.id where d.meal_plan_id='" . intval($_SESSION['choose_your_meal_plan']['category_meal_plan']) . "' order by which_day,meal_time ASC";
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
}

$total_price = 0;

//print_r ($_SESSION['choose_your_meal_plan']);
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
	function validate_choose_your_meal_plan() {
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

		if(error > 0)
			return false;
		else
			return true;
	}

	function collect_meal_plans(val){	

		document.getElementById('category_meal_plan').options.length = 0;
		document.getElementById("category_meal_plan").options[0] = new Option("Choose your meal plan","");

		if(parseInt(val) > 0){	
			$.get("meal-categorywise-meal-plans.php?id="+ val, function(data) {
				document.getElementById("category_meal_plan").options[0] = new Option("Choose your meal plan","");			 	
				var return_data=data.split("#!");		
				var length=return_data.length - 1;		

				for(var i=0; i <length; i++ ){			
					var options_value=return_data[i].split("~_~");	
					document.getElementById("category_meal_plan").options[i+1] = new Option(options_value[1],options_value[0]);
				}					 	
			});	
		}
	}	
</script>

<div class="inrBnr">
	<?php $db_common -> static_page_banner($dynamic_content['page_id']); ?>
</div>
<div class="mealPln right_pop">
	<div class="mainDiv2">
		<div class="mealPlnColOne normal_select main_goal_drop_down">
			<form name="choose_your_meal_plan" method="post" action="select-your-meal-plan/#meal" onsubmit="return validate_choose_your_meal_plan();">
				<input type="hidden" name="enter" value="choose_your_meal_plan" />
				<input type="hidden" name="frm_choose_your_meal_plan" value="<?=$_SESSION['frm_choose_your_meal_plan'] ?>" />
				<h1>Lorem ipsum <strong>dolor sit amet</strong>, consectetur adipisicing elit</h1>
				<p>
					Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.
				</p>
				<br>
				<br>
				<p>
					<strong>Select your Main Goal</strong>
				</p>
				<label class="custom-select">
					<select name="meal_plan_category_id" id="meal_plan_category_id" class="selStlOne" onchange="collect_meal_plans(this.value); " >
						<option value="">Choose your main goal</option>
						<?php
						$sql_meal_plan_cat="select id,name from meal_plan_category where status=1 order by display_order + 0 ASC";
						$result_meal_plan_cat=$db->fetch_all_array($sql_meal_plan_cat);
						$total_meal_plan_cat=count($result_meal_plan_cat);

						for($cat=0; $cat < $total_meal_plan_cat; $cat++){ ?>
						<option value="<?=$result_meal_plan_cat[$cat]['id'] ?>" <?=$_SESSION['choose_your_meal_plan']['meal_plan_category_id'] == $result_meal_plan_cat[$cat]['id'] ? 'selected="selected"' : ''; ?>><?=$result_meal_plan_cat[$cat]['name'] ?></option>				
						<?php } ?>
					</select>
				</label>
				<p>
					<strong>Meal Plan</strong>
				</p>
				<label class="custom-select" style="padding-bottom: 20px;" >
					<select name="category_meal_plan" id="category_meal_plan" class="selStlOne">
						<option value="">Choose your meal plan </option>
						<?php
						$sql_meal_plan="select id,name from meal_plans where status=1 and meal_plan_category_id='" . $_SESSION['choose_your_meal_plan']['meal_plan_category_id'] . "' and id IN(select DISTINCT(meal_plan_id) from meal_plan_meals) order by name ASC";
						$result_meal_plan=$db->fetch_all_array($sql_meal_plan);
						$total_meal_plan=count($result_meal_plan);

						for($plan=0; $plan < $total_meal_plan; $plan++){?>
						<option value="<?=$result_meal_plan[$plan]['id']?>" <?=$_SESSION['choose_your_meal_plan']['category_meal_plan'] == $result_meal_plan[$plan]['id'] ? 'selected="selected"' : ''; ?>><?=$result_meal_plan[$plan]['name']?></option>									
						<?php } ?>
					</select>
				</label>
				<input name="submit" type="submit" value="Place My Order" />
			</form>	
		</div>
		<div class="mealPlnColTwo">
			<h1>What you will get in the meal plan</h1>			
			<?php  if(isset($_SESSION['choose_your_meal_plan']) && is_array($_SESSION['choose_your_meal_plan'])){ ?>			
			<p><?=$_SESSION['choose_your_meal_plan']['meal_plan_details'] ?><a name="meal"></a></p>
			<br class="clear">
			<script type="text/javascript">

				function slideonlyone(thechosenone) {
					$('.accordion_content').each(function(index) {
						if ($(this).attr("id") == thechosenone) {
							$(this).slideToggle(200);
							$(this).parent().find('.dayPnl2').toggleClass('active');
						}
						else {
							$(this).slideUp(200);
							$(this).parent().find('.dayPnl2').removeClass('active');
						}
					});
				}

			</script>
			<div class="sedulePnl">
				<?php for($day=1; $day <= $_SESSION['choose_your_meal_plan']['no_of_days']; $day++){ ?>	
				<div class="dayPnl">
					<a class="dayPnl2" id="accordiontitle<?=$day ?>" href="javascript:slideonlyone('accordioncontent<?=$day ?>');"><h5><span></span>Day <?=$day ?></h5></a>
					<br class="clear">
					<div class="dayPnlTgl accordion_content" id="accordioncontent<?=$day ?>">
						<div class="dayPnl1 dayPnl1_new">
							<ul>
								<?php  for($time=1; $time <= $_SESSION['choose_your_meal_plan']['meal_per_day']; $time++ ){?>
								<li>
									<span><?=$db_common -> meal_time($time); ?> :</span><span><?=$default_meals[$day][$time]['meal_name'] ?></span>
									<div class="tip_box" style="z-index: 99999;" >
										<div class="close_pop"></div>
										<div class="tip_angle"></div>						                     
										<div class="tip_head"><?=$default_meals[$day][$time]['meal_name'] ?></div>						                      
										<div class="tip_row">
											<div class="tip_column_container">
												<div class="tip_column">
													<div class="tip_column_info_row">
														<div class="info_tab">Net Weight :</div>
														<div class="info_tab"><?=$default_meals[$day][$time]['meal_size']; ?>g</div>
													</div>
													<div class="tip_column_info_row">
														<div class="info_tab">Energy :</div>
														<div class="info_tab"><?=$db_common->nutritional_value($default_meals[$day][$time]['energy'],$default_meals[$day][$time]['meal_size']) ?> kcal</div>
													</div>
													<div class="tip_column_info_row">
														<div class="info_tab">Protein :</div>
														<div class="info_tab"><?=$db_common->nutritional_value($default_meals[$day][$time]['protein'],$default_meals[$day][$time]['meal_size']) ?>g</div>
													</div>
													<div class="tip_column_info_row">
														<div class="info_tab">Carbs :</div>
														<div class="info_tab">
															<?php if(intval($default_meals[$day][$time]['carbs_veggies']) == 3 || intval($default_meals[$day][$time]['carbs_veggies']) == 1){ ?>
															<img src="images/tip_yes.png" style="margin-top: 4px;" />	
															<?php }else{ ?>
															<img src="images/tip_no.png" style="margin-top: 4px;" />
															<?php } ?>
														</div>
													</div>
													<div class="tip_column_info_row">
														<div class="info_tab">Sauce :</div>
														<div class="info_tab">
															<?php if(intval($default_meals[$day][$time]['with_or_without_sauce']) == 1){ ?>
															<img src="images/tip_yes.png" style="margin-top: 4px;" />	
															<?php }else{ ?>
															<img src="images/tip_no.png" style="margin-top: 4px;" />
															<?php } ?>
														</div>
													</div>
												</div>
												<div class="tip_column">
													<div class="tip_column_info_row">
														<div class="info_tab">Calories :</div>
														<div class="info_tab"><?=$db_common->nutritional_value($default_meals[$day][$time]['calories'],$default_meals[$day][$time]['meal_size'])?>g</div>
													</div>
													
													<div class="tip_column_info_row">
														<div class="info_tab">Carbs :</div>
														<div class="info_tab"><?=$db_common->nutritional_value($default_meals[$day][$time]['carbohydrates'],$default_meals[$day][$time]['meal_size']) ?>g</div>
													</div>
													<div class="tip_column_info_row">
														<div class="info_tab">Total Fat :</div>
														<div class="info_tab"><?=$db_common->nutritional_value($default_meals[$day][$time]['fat_total'],$default_meals[$day][$time]['meal_size']) ?>g</div>
													</div>
													<div class="tip_column_info_row">
														<div class="info_tab">Price :</div>
														<div class="info_tab">$<?=$default_meals[$day][$time]['price'] ?></div>
													</div>
													<div class="tip_column_info_row">
														<div class="info_tab">Veggies :</div>
														<div class="info_tab">
															<?php if(intval($default_meals[$day][$time]['carbs_veggies']) == 3 || intval($default_meals[$day][$time]['carbs_veggies']) == 2){ ?>
															<img src="images/tip_yes.png" style="margin-top: 5px;" />	
															<?php }else{ ?>
															<img src="images/tip_no.png" style="margin-top: 5px;" />
															<?php } ?>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="tip_row">
											<div class="tip_descrip">
												<p>
													<?php if(trim($default_meals[$day][$time]['photo_name']) != NULL){ ?>
													<img class="lefted_img" align="left" src="meal_main/small/<?=trim($default_meals[$day][$time]['photo_name']) ?>" width="120">						                            	
													<?php } ?>	

													<?=nl2br($default_meals[$day][$time]['details']) ?></p>
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
					<div class="dayPnlBtn">
						<input name="submit" type="submit" value="Order Now" onclick="location.href='<?=$general_func->site_url?>order-review/'" />
					</div>
				</div>
				<?php }else{

					echo '<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. </p>';

				}?>
			</div>
		</div>
	</div>
	<?php
	include_once ("includes/footer.php");
	?>