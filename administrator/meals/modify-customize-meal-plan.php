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
            
            
            
            <div class="mealPln normal_select">
	<div class="mainDiv2">
    
    <div class="whitePnl">
		<form name="customize_your_meal_plan" method="post" action="customize-your-own/#meal" onsubmit="return validate_customized_your_meal_plan();">
			<input type="hidden" name="enter" value="customize_your_meal_plan" />
			<input type="hidden" name="frm_customize_your_meal_plan" value="<?=$_SESSION['frm_customize_your_meal_plan'] ?>" />
			<div class="mealPlnColOne main_goal_drop_down">
				<h1><?php echo $result_content[0]['customize_meal_plan_page_left_heading']; ?></h1>
				<p><?php echo nl2br($result_content[0]['customize_meal_plan_page_left_content']); ?></p>
				<br />
				<p>
					<strong>Select your Main Goal</strong>
				</p>
				<label class="custom-select">
					<select name="meal_plan_category_id" id="meal_plan_category_id"  class="selStlOne" onchange="decide_training_timing(this.value);">
						<option value="">Select One</option>
						<?php
						$sql_cat="select id,name from meal_plan_category where id IN(select DISTINCT(meal_plan_category_id) from meal_plan_category_meals) order by  display_order + 0 ASC ";
						$result_cat=$db->fetch_all_array($sql_cat);
						$total_cat=count($result_cat);

						for($cat=0; $cat < $total_cat; $cat++){ ?>
						<option value="<?=$result_cat[$cat]['id'] ?>" <?=intval($result_cat[$cat]['id']) == $_SESSION['customize_your_meal_plan']['meal_plan_category_id'] ? 'selected="selected"' : ''; ?>><?=$result_cat[$cat]['name'] ?></option>
						<?php } ?>
					</select></label>
				<br class="clear">

				<p>
					<strong>No. of Days</strong>
				</p>
				<label class="custom-select">
					<select name="no_of_days" id="no_of_days" class="selStlThr" >
						<option value="">Select One</option>
						<option value="5" <?=intval($_SESSION['customize_your_meal_plan']['no_of_days']) == 5 ? 'selected="selected"' : ''; ?>>5</option>
						<option value="7" <?=intval($_SESSION['customize_your_meal_plan']['no_of_days']) == 7 ? 'selected="selected"' : ''; ?>>7</option>
					</select></label>
				<div class="short-dual-column-box">
					<div class="dual-column-field">
						<p>
							<strong>Meals per Day</strong>
						</p>
						<label class="custom-select">
							<select name="meal_per_day" id="meal_per_day" class="selStlThr">
								<option value="">Select One</option>
								<!-- <option value="1" <?=intval($_SESSION['customize_your_meal_plan']['meal_per_day']) == 1 ? 'selected="selected"' : ''; ?>>1</option>
								<option value="2" <?=intval($_SESSION['customize_your_meal_plan']['meal_per_day']) == 2 ? 'selected="selected"' : ''; ?>>2</option> -->
								<option value="3" <?=intval($_SESSION['customize_your_meal_plan']['meal_per_day']) == 3 ? 'selected="selected"' : ''; ?>>3</option>
								<option value="4" <?=intval($_SESSION['customize_your_meal_plan']['meal_per_day']) == 4 ? 'selected="selected"' : ''; ?>>4</option>
								<option value="5" <?=intval($_SESSION['customize_your_meal_plan']['meal_per_day']) == 5 ? 'selected="selected"' : ''; ?>>5</option>
								<option value="6" <?=intval($_SESSION['customize_your_meal_plan']['meal_per_day']) == 6 ? 'selected="selected"' : ''; ?>>6</option>
							</select> </label>
					</div>
					<div class="dual-column-field">
						<p>
							<strong>Snacks per Day</strong>
						</p>
						<label class="custom-select">
							<select name="snack_per_day" id="snack_per_day" class="selStlThr">
								<option value="">Select One</option>
								<option value="1" <?=intval($_SESSION['customize_your_meal_plan']['snack_per_day']) == 1 ? 'selected="selected"' : ''; ?>>1</option>
								<option value="2" <?=intval($_SESSION['customize_your_meal_plan']['snack_per_day']) == 2 ? 'selected="selected"' : ''; ?>>2</option>
								<option value="3" <?=intval($_SESSION['customize_your_meal_plan']['snack_per_day']) == 3 ? 'selected="selected"' : ''; ?>>3</option>
								<option value="4" <?=intval($_SESSION['customize_your_meal_plan']['snack_per_day']) == 4 ? 'selected="selected"' : ''; ?>>4</option>
								<option value="5" <?=intval($_SESSION['customize_your_meal_plan']['snack_per_day']) == 5 ? 'selected="selected"' : ''; ?>>5</option>
							</select> </label>
					</div>
				</div>

				<div class="melPlnrFrmRght melPlnrFrmRght-type-two" id="training_time_id" style="margin-top: 10px; display: <?=(isset($_SESSION['customize_your_meal_plan']['user_can_download_pdf']) && $_SESSION['customize_your_meal_plan']['user_can_download_pdf'] == 1) ? 'block;' : 'none;'; ?>">
					<ul>
						<li>
							Will you be exercising to speed up your results?
							<br>
							<input type="radio" id="r1" name="exercising_to_speed_up" value="yes" checked="checked" onclick="decide_training_part(1);" <?=$exercising_to_speed_up == "yes" ? 'checked="checked"' : ''; ?> />
							<label for="r1"><span></span>Yes</label>
							<input type="radio" id="r2"  name="exercising_to_speed_up" value="no" onclick="decide_training_part(0);"  <?=$exercising_to_speed_up == "no" ? 'checked="checked"' : ''; ?>   />
							<label for="r2"><span></span>No</label>
						</li>
						<li id="div_part_of_the_day_usually_train" style="display: <?=$exercising_to_speed_up == "yes" ? 'block' : 'none'; ?>;">
							What part of the day would you usually train?
							<br>
							<input type="radio" id="r3" name="part_of_the_day_usually_train" value="morning" <?=$part_of_the_day_usually_train == "morning" ? 'checked="checked"' : ''; ?> />
							<label for="r3"><span></span>Morning</label>
							<input type="radio" id="r4" name="part_of_the_day_usually_train" value="lunch_time" <?=$part_of_the_day_usually_train == "lunch_time" ? 'checked="checked"' : ''; ?> />
							<label for="r4"><span></span>Lunch Time</label>
							<input type="radio" id="r5" name="part_of_the_day_usually_train" value="after_work" <?=$part_of_the_day_usually_train == "after_work" ? 'checked="checked"' : ''; ?>  />
							<label for="r5"><span></span>After Work</label>
							<input type="radio" id="r6" name="part_of_the_day_usually_train" value="evening" <?=$part_of_the_day_usually_train == "evening" ? 'checked="checked"' : ''; ?> />
							<label for="r6"><span></span>Evening</label>
						</li>
					</ul>
				</div>

				<div class="checkout_row" style="border:none; margin-top:0">
					<div style="width:200px; margin:0 auto"><input name="submit" type="submit" value="Personalise Meals" /></div>
				</div>

			</div>
		</form><a name="meal"></a>
		<div class="mealPlnColTwo">
			<h1><?php echo $result_content[0]['customize_meal_plan_page_right_heading']; ?></h1>
			<p id="default_content" style="display: <?=isset($_SESSION['customize_your_meal_plan']) ? 'none' : 'block'; ?>;">
				<?php echo nl2br($result_content[0]['customize_meal_plan_page_right_content']); ?>
			</p>
			<br class="clear">
			<div class="sedulePnl" id="selected_meal" style="display: <?=isset($_SESSION['customize_your_meal_plan']) ? 'block' : 'none'; ?>;">
				<form name="customize_your_meal_plan_final" method="post" action="customize-your-own/" >
					<input type="hidden" name="enter" value="customize_your_meal_plan_final" />
					<input type="hidden" name="frm_customize_your_meal_plan" value="<?=$_SESSION['frm_customize_your_meal_plan'] ?>" />

					<?php
					if(isset($_SESSION['customize_your_meal_plan']) && is_array($_SESSION['customize_your_meal_plan'])){

					//and with_or_without_sauce='" . $_SESSION['customize_your_meal_plan']['with_or_without_sauce'] . "'  and carbs_veggies='" . $_SESSION['customize_your_meal_plan']['carbs_veggies'] . "'
					$result_meals=$db->fetch_all_array("select id,name from meals where status=1 and id IN(select DISTINCT(meal_id) from meal_plan_category_meals where meal_plan_category_id='" . $_SESSION['customize_your_meal_plan']['meal_plan_category_id'] . "')   order by name ASC");
					$total_meals=count($result_meals);

					if( $_SESSION['customize_your_meal_plan']['snack_per_day'] > 0){
					$result_snacks=$db->fetch_all_array("select id,name from snacks where status=1 and id IN(select DISTINCT(snack_id) from meal_plan_category_snacks where meal_plan_category_id='" . $_SESSION['customize_your_meal_plan']['meal_plan_category_id'] . "')   order by name ASC");
					$total_snacks=count($result_snacks);
					}

					for($day=1; $day <= $_SESSION['customize_your_meal_plan']['no_of_days']; $day++ ){?>
					<div class="dayPnl">
						<div class="dayPnl2 daynumber">
							<h5>Day <?=$day ?></h5>
						</div>
						<br class="clear">
						<div class="dayPnlTgl mealplan">
							<div class="selDayPnl1 new_sel_day">
								<ul>
									<?php for($time=1; $time <= $_SESSION['customize_your_meal_plan']['meal_per_day']; $time++ ){
$selected_meal=isset($_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_id'])?$_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_id']:'';
$selected_size=isset($_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_size'])?$_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_size']:100;
									?>
									<li>
										<span>Meal <?=$time ?> :</span>
										<div class="dual_column_select_box">
											<div class="dual_column_select_block">
												<label class="custom-select">
													<select name="meal_id_<?=$day . "_" . $time ?>" id="meal_id_<?=$day . "_" . $time ?>">
														<?php for($meal=0; $meal < $total_meals; $meal++){
														?>
														<option value="<?=$result_meals[$meal]['id'] ?>" <?=intval($result_meals[$meal]['id']) == $selected_meal ? 'selected="selected"' : ''; ?>><?=$result_meals[$meal]['name'] ?></option>
														<?php }
																reset($result_meals);
														?>
													</select> </label>
											</div>
											<div class="dual_column_select_block">
												<label class="custom-select">
													<select name="<?=$day . "_" . $time ?>">
														<option value="100" <?=intval($selected_size) == 100 ? 'selected="selected"' : ''; ?>>100gm</option>
														<option  value="150" <?=intval($selected_size) == 150 ? 'selected="selected"' : ''; ?>>150gm</option>
														<option  value="200" <?=intval($selected_size) == 200 ? 'selected="selected"' : ''; ?>>200gm</option>
													</select> </label>
											</div>
										</div>
									</li>
									<?php }
										for($time=1; $time <= $_SESSION['customize_your_meal_plan']['snack_per_day']; $time++ ){
										$selected_snack=isset($_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_id'])?$_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_id']:'';
										$selected_qty=isset($_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_qty'])?$_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_qty']:1;
									?>
									<li>
										<span>Snack <?=$time ?> :</span>
										<div class="dual_column_select_box">
											<div class="dual_column_select_block">
												<label class="custom-select">
													<select name="snack_id_<?=$day . "_" . $time ?>" id="snack_id_<?=$day . "_" . $time ?>">
														<?php for($snack=0; $snack < $total_snacks; $snack++){
														?>
														<option value="<?=$result_snacks[$snack]['id'] ?>" <?=intval($result_snacks[$snack]['id']) == $selected_snack ? 'selected="selected"' : ''; ?>><?=$result_snacks[$snack]['name'] ?></option>
														<?php }
																reset($result_snacks);
														?>
													</select> </label>
											</div>
											<div class="dual_column_select_block">
												<label><span style="width: auto; line-height: 33px; font-size: 13px; padding-right:5px "> Qty: </span>
													<input type="text" name="snack_qty_<?=$day . "_" . $time ?>" id="snack_qty_<?=$day . "_" . $time ?>" value="<?=$selected_qty ?>" style="text-align: center;" />
												</label>
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
					<div class="checkout_row" style="border:none; margin-top:0">
					<div style="width:200px; margin:0 auto"><input name="submit" type="submit" value="Order Now" style="width:200px;" /></div>
					</div>
					<?php } ?>
				</form>
			</div>
		</div>
     </div>
	</div>
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
