<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";


$data=array();
$return_url=$_REQUEST['return_url'];



$small=$path_depth ."meal_main/small/";
$original=$path_depth ."meal_main/";


$sql="select m.*,mc.name as meal_category from meals m";
$sql .=" left join meal_category mc on m.meal_category_id=mc.id";				
$sql .=" where m.id=" .  intval($_REQUEST['id'])  . " limit 1";
$result=$db->fetch_all_array($sql);	


?> 
<script type="text/javascript" src="<?=$general_func->site_url?>highslide/highslide.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$general_func->site_url?>highslide/highslide.css" />
<script type="text/javascript">
	hs.graphicsDir = '<?=$general_func->site_url?>highslide/graphics/';
	hs.wrapperClassName = 'wide-border';
</script>	
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg">View Meal</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg">
        <table width="989" border="0" align="left" cellpadding="4" cellspacing="0">
          <tr>
            <td colspan="2" height="30">
            
            
            
            <div class="mealPln right_pop">
	<div class="mainDiv2">
    
    
    	<div class="whitePnl">
		<form name="choose_your_meal_plan" method="post" action="" onsubmit="return validate_choose_your_meal_plan();">
		<div class="mealPlnColOne normal_select main_goal_drop_down">
			<input type="hidden" name="enter" value="continue_order" />
			
			
			<input type="hidden" name="order_id" value="<?=$order_id?>" />
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
						<option value="<?=$result_meal_plan_cat[$cat]['id'] ?>" <?=$meal_plan_category_id == $result_meal_plan_cat[$cat]['id'] ? 'selected="selected"' : ''; ?>><?=$result_meal_plan_cat[$cat]['name'] ?></option>
						<?php } ?>
					</select> </label>
					<p>
						<strong>Meal Plan</strong>
					</p>
					<label class="custom-select" style="padding-bottom: 20px;" >
						<select name="category_meal_plan" id="category_meal_plan" class="selStlOne" onchange="show_meal_plan_meals(this.value)" >
							<option value="">Choose your meal plan </option>
							<?php
							$sql_meal_plan="select id,name from meal_plans where status=1 and meal_plan_category_id='" . $meal_plan_category_id . "' and id IN(select DISTINCT(meal_plan_id) from meal_plan_meals) order by name ASC";
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
								<option value="<?=$result_meal_plan[$plan]['id'] ?>" <?=$category_meal_plan == $result_meal_plan[$plan]['id'] ? 'selected="selected"' : ''; ?>><?=$result_meal_plan[$plan]['name'] ?>  - $<?=number_format($price1, 2) ?> p/w</option>
								<?php } ?>
							</select> </label>
							<!--<div class="melPlnrFrmRght melPlnrFrmRght-type-two" id="training_time_id" style="display: <?=(isset($_SESSION['choose_your_meal_plan']['user_can_download_pdf']) && $_SESSION['choose_your_meal_plan']['user_can_download_pdf']==1)?'block;':'none;'; ?>">
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
							</div> -->
						
					</div>
					<div class="mealPlnColTwo">
						<h1><?php echo $result_content[0]['select_meal_plan_page_right_heading']; ?></h1>

						<br class="clear">

						<div class="sedulePnl" id="plan_meals">
							<?php if(isset($category_meal_plan) && intval($category_meal_plan) > 0){
								for($day=1; $day <= intval($no_of_days); $day++){ ?>
								<div class="dayPnl">
									<a class="dayPnl2" id="accordiontitle<?=$day ?>" href="javascript:slideonlyone('accordioncontent<?=$day ?>');"><h5><span></span>Day <?=$day ?></h5></a>
									<br class="clear">
									<div class="dayPnlTgl accordion_content" id="accordioncontent<?=$day ?>">
										<div class="dayPnl1 dayPnl1_new">
											<ul>
												<?php  for($time=1; $time <= intval($meal_per_day); $time++ ){
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
																				<?=$default_meals[$day][$time]['energy'] ?> kcal
																			</div>
																		</div>
																		<div class="tip_column_info_row">
																			<div class="info_tab">
																				Protein :
																			</div>
																			<div class="info_tab">
																				<?=$default_meals[$day][$time]['protein'] ?> g
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
																					<div class="info_tab">
																						Carbs :
																					</div>
																					<div class="info_tab">
																						<?=$default_meals[$day][$time]['carbohydrates'] ?>g
																					</div>
																				</div>
																				<div class="tip_column_info_row">
																					<div class="info_tab">
																						Total Fat :
																					</div>
																					<div class="info_tab">
																						<?=$default_meals[$day][$time]['fat_total'] ?>g
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
																		for($time=1; $time <= intval($snack_per_day); $time++ ){
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
															<div class="checkout_row" style="border:none; margin-top:0"><div style="width:200px; margin:0 auto"><input name="submit" type="submit" value="Update Order" style="width:100%" /></div></div>
															<?php }else{																
																 echo '<p>' . nl2br($result_content[0]['select_meal_plan_page_right_content']).'</p>'; 
																}
															?>
														</div>
													</div>
													</form>
		</div>										</div>
											</div>
            
            
            
            </td>
          </tr>
          <tr>
            <td width="32" align="left" valign="top"></td>
            <td width="797" align="left" valign="top"></td>
          </tr>
         <tr>
            <td colspan="2" height="20"><p>&nbsp;</p></td>
          </tr>
          <tr>
            <td colspan="2" height="30" align="center"></td>
          </tr>
         
          <tr>
            <td colspan="2" height="30"></td>
          </tr>
        </table>
     </td>
  </tr>
</table>
<?php
include("../foot.htm");
?>
