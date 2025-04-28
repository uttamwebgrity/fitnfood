<?php
include_once("includes/configuration.php");

$meal_plan_category_id=intval($_REQUEST['meal_plan_category_id']);
$no_of_days=intval($_REQUEST['no_of_days']);
$meal_per_day=intval($_REQUEST['meal_per_day']);



$result_meals=$db->fetch_all_array("select id,name from meals where status=1 and meal_plan_category_id='" . $meal_plan_category_id . "'  and snacks=0  order by name ASC");
$total_meals=count($result_meals);


if( $meal_per_day == 5){
	$result_snacks=$db->fetch_all_array("select id,name from meals where status=1 and  meal_plan_category_id='" . $meal_plan_category_id . "'  and snacks=1 order by name ASC");
	$total_snacks=count($result_snacks);	
}



for($day=1; $day <= $no_of_days; $day++ ){?>
<div class="dayPnl">
	<div class="dayPnl2 daynumber">
		<h5>Day <?=$day?></h5>
	</div>
	<br class="clear">
	<div class="dayPnlTgl mealplan">
		<div class="selDayPnl1 new_sel_day">
							<ul>

								<li>
									<span>Breakfast :</span>

									<div class="dual_column_select_box">

										<div class="dual_column_select_block">
											<label class="custom-select">
												<select name="">
													<option>Bacon, Kale 100gms. $10</option>
												</select></label>
										</div>

										<div class="dual_column_select_block">
											<label class="custom-select">
												<select name="">
													<option>250gm</option>
												</select></label>
										</div>

									</div>

								</li>

								<li>
									<span>Lunch :</span>
									<div class="dual_column_select_box">

										<div class="dual_column_select_block">
											<label class="custom-select">
												<select name="">
													<option>Lamb Bolognese 150gms $15</option>
												</select></label>
										</div>

										<div class="dual_column_select_block">
											<label class="custom-select">
												<select name="">
													<option>250gm</option>
												</select></label>
										</div>

									</div>
								</li>

								<li>
									<span>Dinner :</span>
									<div class="dual_column_select_box">

										<div class="dual_column_select_block">
											<label class="custom-select">
												<select name="">
													<option>Salmon Fillet Fried Rice 2000gms $22</option>
												</select></label>
										</div>

										<div class="dual_column_select_block">
											<label class="custom-select">
												<select name="">
													<option>250gm</option>
												</select></label>
										</div>

									</div>
								</li>

								<li>
									<span>Snacks 1 :</span>
									<div class="dual_column_select_box">

										<div class="dual_column_select_block">
											<label class="custom-select">
												<select name="">
													<option>Doughnut $3</option>
												</select></label>
										</div>

										<div class="dual_column_select_block">
											<label class="custom-select">
												<select name="">
													<option>250gm</option>
												</select></label>
										</div>

									</div>
								</li>

								<li>
									<span>Snacks 2 :</span>
									<div class="dual_column_select_box">

										<div class="dual_column_select_block">
											<label class="custom-select">
												<select name="">
													<option>Waffle $5</option>
												</select></label>
										</div>

										<div class="dual_column_select_block">
											<label class="custom-select">
												<select name="">
													<option>250gm</option>
												</select></label>
										</div>

									</div>
								</li>

							</ul>
						</div>
					</div>
				</div>	
	
<?php }	?>				
<br class="clear">
<div class="dayPnlBtn" style="float:right">
	<input name="" type="submit" value="Place My Order" onclick="location.href='<?=$general_func->site_url?>order-review/'"  />
</div>