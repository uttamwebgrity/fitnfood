<?php
include_once ("includes/configuration.php");

$id = intval($_REQUEST['category_meal_plan']);

if (isset($_SESSION['fill_the_questionnaire']))
	unset($_SESSION['fill_the_questionnaire']);

if (isset($_SESSION['customize_your_meal_plan']))
	unset($_SESSION['customize_your_meal_plan']);

$_SESSION['personalize_meal']=0;
$_SESSION['choose_your_meal_plan'] = array();

$_SESSION['choose_your_meal_plan']['category_meal_plan'] = intval($_REQUEST['category_meal_plan']);

$result_meal_plan = $db -> fetch_all_array("select p.*,c.name as category_name from meal_plans p, meal_plan_category c where p.meal_plan_category_id=c.id and  p.id='" . intval($_REQUEST['category_meal_plan']) . "' limit 1 ");

$_SESSION['choose_your_meal_plan']['meal_plan_category_id'] = intval($_REQUEST['meal_plan_category_id']);
$_SESSION['choose_your_meal_plan']['meal_plan_category'] = $result_meal_plan[0]['category_name'];
$_SESSION['choose_your_meal_plan']['category_meal_plan'] = intval($_REQUEST['category_meal_plan']);
$_SESSION['choose_your_meal_plan']['meal_plan_name'] = $result_meal_plan[0]['name'];
$_SESSION['choose_your_meal_plan']['meal_plan_details'] = nl2br($result_meal_plan[0]['details']);
$_SESSION['choose_your_meal_plan']['no_of_days'] = intval($result_meal_plan[0]['no_of_days']);
$_SESSION['choose_your_meal_plan']['meal_per_day'] = intval($result_meal_plan[0]['meal_per_day']);
$_SESSION['choose_your_meal_plan']['snack_per_day'] = intval($result_meal_plan[0]['snack_per_day']);



//***************  set default meals for customization ***************************************//	
$_SESSION['default']['meal_plan_category_id'] = intval($_REQUEST['meal_plan_category_id']);
$_SESSION['default']['no_of_days'] = intval($result_meal_plan[0]['no_of_days']);
$_SESSION['default']['meals_per_day'] = intval($result_meal_plan[0]['meal_per_day']);
$_SESSION['default']['snacks_per_days'] = intval($result_meal_plan[0]['snack_per_day']);
//***********************************************************************************************//	



$sql_meals = "select which_day,meal_time,meal_size,meal_id,show_nutritional_price,(select meal_price from meals_sizes_prices where meal_id=d.meal_id and meal_size=d.meal_size) as price,m.name,details,photo_name,energy,calories,protein,fat_total,carbohydrates,carbs_veggies,with_or_without_sauce from meal_plan_meals d left join meals m on d.meal_id=m.id where d.meal_plan_id='" . $id . "' and type=1 order by which_day,meal_time ASC";
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
	
}

$sql_snacks = "select which_day,meal_time,meal_id,price,name,details,photo_name,meal_size as qty from meal_plan_meals  d left join snacks s on d.meal_id=s.id where d.meal_plan_id='" . $id . "' and type=2 order by which_day,meal_time ASC";
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

	//***************  set default meals for customization ******************************************//			
	$_SESSION['default']['snacks'][$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['snack_id']=$result_default_snacks[$i]['meal_id'];	
	$_SESSION['default']['snacks'][$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['qty']=$result_default_snacks[$i]['qty'];	
	//***********************************************************************************************//	
}


$sql_content="select set_meal_plan_modification from dynamic_pages where id=1 limit 1";
$result_content=$db->fetch_all_array($sql_content);

?>
<script type="text/javascript">
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
<?php for($day=1; $day <= intval($_SESSION['choose_your_meal_plan']['no_of_days']); $day++){ ?>
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
											<?=$db_common->nutritional_value($default_meals[$day][$time]['energy'],$default_meals[$day][$time]['meal_size']) ?> kcal
										</div>
									</div>
									<div class="tip_column_info_row">
										<div class="info_tab">
											Protein :
										</div>
										<div class="info_tab">
											<?=$db_common->nutritional_value($default_meals[$day][$time]['protein'],$default_meals[$day][$time]['meal_size']) ?>g
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
<div class="checkout_row" style="border:none; margin-top:0"><div style="width:200px; margin:0 auto">
	<input name="submit" type="submit" value="Order Now" onclick="location.href='<?=$general_func->site_url?>order-review/'" style="width:100%" />
</div>
       <p style="color: #ff0000;font: 14px/18px 'open_sansregular';text-align: center; padding:20px 0 10px;"><?php echo $result_content[0]['set_meal_plan_modification']; ?></p>
                                <div class="dayPnlBtn" style="margin-top:0">	
									<input name="button" type="button" value="Modify Meal Plan" onclick="location.href='<?=$general_func->site_url?>customize-your-own/#meal'" style="width:90%; margin:0 5%" class="mdfyMealPln" />
								</div>
                                         

</div>
