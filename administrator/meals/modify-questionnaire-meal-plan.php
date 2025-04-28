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
            
            
            
            <div class="hmPanelTwo" style="position:relative; background:#fff">
	<div class="mainDiv2">
    
  	<br class="clear" />
		<?php /*?><h2 style="font-size:28px"><?php echo $result_content[0]['get_started_page_heading']; ?></h2>
		<p><?php echo nl2br($result_content[0]['get_started_page_content']); ?></p><?php */?>
		<div class="melPlnrFrm">
			<script>
				function validate_get_your_meal_plan(){
					var error=0;		

					if(document.frm_get_your_meal_plan.meal_plan_category.selectedIndex == 0){
						document.getElementById("meal_plan_category").style.border="1px solid red";
						error++;
					}else{						
						document.getElementById("meal_plan_category").style.border="1px solid #CBD2BB";	
					}

					if(document.getElementById("age").value == ''){
						document.getElementById("age").style.border="1px solid red";
						error++;
					}else{
						document.getElementById("age").style.border="1px solid #CBD2BB";	
					}

					if(!validate_integer_without_msg(document.getElementById("age"))){
						document.getElementById("age").style.border="1px solid red";
						error++;
					}else{
						document.getElementById("age").style.border="1px solid #CBD2BB";	
					}

					if(document.frm_get_your_meal_plan.gender.selectedIndex == 0){
						document.getElementById("gender").style.border="1px solid red";
						error++;
					}else{
						document.getElementById("gender").style.border="1px solid #CBD2BB";	
					}
					
					if(document.getElementById("weight").value == ''){
						document.getElementById("weight").style.border="1px solid red";
						error++;
					}else{
						document.getElementById("weight").style.border="1px solid #CBD2BB";	
					}
					
					if(!validate_numeric_without_msg(document.getElementById("weight"))){
						document.getElementById("weight").style.border="1px solid red";
						error++;
					}else{
						document.getElementById("weight").style.border="1px solid #CBD2BB";	
					}
										
					if(error>0){
						return false;
					}else{
						return true;	
					}
				}

				function decide_training_part(val){
					if(parseInt(val) == 1)
						$("#div_part_of_the_day_usually_train").show(1000); 
					else
						$("#div_part_of_the_day_usually_train").hide(1000); 
				}

			</script>
			<form name="frm_get_your_meal_plan" method="post" action="get-started/#meal" onsubmit=" return validate_get_your_meal_plan();">
				<input type="hidden" name="enter" value="get_your_meal_plan" />
				<input type="hidden" name="frm_get_your_meal_plan" value="<?=$_SESSION['frm_get_your_meal_plan']?>" />	
				<div class="melPlnrFrmLft">
					<ul>
						<li class="normal_select">
							<label class="custom-select">
								<select name="meal_plan_category" id="meal_plan_category" class="lg" >
									<option>What is your main goal?</option>
									<?php
									$sql_meal_plan_cat="select id,name from meal_plan_category where id IN(select DISTINCT(meal_plan_category_id) from categories_default_meals) and status=1 order by display_order + 0 ASC";
									$result_meal_plan_cat=$db->fetch_all_array($sql_meal_plan_cat);
									$total_meal_plan_cat=count($result_meal_plan_cat);

									for($c=0; $c < $total_meal_plan_cat; $c++){ ?>
									<option value="<?=$result_meal_plan_cat[$c]['id']?>" <?=(isset($_SESSION['fill_the_questionnaire']['meal_plan_category_id']) && intval($_SESSION['fill_the_questionnaire']['meal_plan_category_id']) == $result_meal_plan_cat[$c]['id'])?'selected="selected"':''; ?> ><?=$result_meal_plan_cat[$c]['name']?></option>
									<?php } ?>
								</select>
							</label>
						</li>
						<li class="normal_select for_gender_select">
							<span>Your Age</span>
							<input name="age" type="text" style="height: 43px;"  id="age" value="<?=trim($_SESSION['fill_the_questionnaire']['age'])?>" /><span>Yrs</span>
							<label class="custom-select" style="float:right">
								<select name="gender" id="gender" class="sml">
									<option>Gender</option>
									<option value="male" <?=(isset($_SESSION['fill_the_questionnaire']['gender']) && trim($_SESSION['fill_the_questionnaire']['gender']) == "male")?'selected="selected"':''; ?>>Male</option>
									<option value="female" <?=(isset($_SESSION['fill_the_questionnaire']['gender']) && trim($_SESSION['fill_the_questionnaire']['gender']) == "female")?'selected="selected"':''; ?>>Female</option>
								</select>
							</label>
						</li>
						<li><span>What is your current weight</span> <input name="weight" id="weight" type="text"  value="<?=trim($_SESSION['fill_the_questionnaire']['weight'])?>"/>&nbsp;Kgs.</li>
					</ul>
				</div>
				<div class="melPlnrFrmRght">
					<ul>
						<li>Will you be exercising to speed up your results? <?=$fill_the_questionnaire?><br>
							<input type="radio" id="r1" name="exercising_to_speed_up" value="yes" onclick="decide_training_part(1);" <?=$exercising_to_speed_up == "yes"?'checked="checked"':''; ?>    />
							<label for="r1"><span></span>Yes</label>
							<input type="radio" id="r2"  name="exercising_to_speed_up" value="no" onclick="decide_training_part(0);" <?=$exercising_to_speed_up == "no"?'checked="checked"':''; ?>   />
							<label for="r2"><span></span>No</label>
						</li>
						<li id="div_part_of_the_day_usually_train" style="display: <?=$exercising_to_speed_up == "yes"?'block':'none'; ?>;">What part of the day would you usually train?<br>
							<input type="radio" id="r3" name="part_of_the_day_usually_train" value="morning" <?=$part_of_the_day_usually_train == "morning"?'checked="checked"':''; ?> />
							<label for="r3"><span></span>Morning</label>
							<input type="radio" id="r4" name="part_of_the_day_usually_train" value="lunch_time" <?=$part_of_the_day_usually_train == "lunch_time"?'checked="checked"':''; ?> />
							<label for="r4"><span></span>Lunch Time</label>
							<input type="radio" id="r5" name="part_of_the_day_usually_train" value="after_work" <?=$part_of_the_day_usually_train == "after_work"?'checked="checked"':''; ?> />
							<label for="r5"><span></span>After Work</label>
							<input type="radio" id="r6" name="part_of_the_day_usually_train" value="evening" <?=$part_of_the_day_usually_train == "evening"?'checked="checked"':''; ?> />
							<label for="r6"><span></span>Evening</label>
						</li>
						<li>How many days per week would you like to eat the fit n food meals?<br>
							<input type="radio" id="r7" name="like_to_eat_how_many_days" value="5" <?=$like_to_eat_how_many_days == 5?'checked="checked"':''; ?> />
							<label for="r7"><span></span>5 days</label>
							<input type="radio" id="r8" name="like_to_eat_how_many_days" value="7" <?=$like_to_eat_how_many_days == 7?'checked="checked"':''; ?>  />
							<label for="r8"><span></span>7 days</label>
						</li>
					</ul>
				</div>
				<br class="clear"><a name="meal"></a>
				<div class="melPlnBtn"><input name="submit" type="submit" value="Get your meal plan" /></div>
			</form>
		</div>
	</div>
	<!--<img src="images/icons/downArw.png" class="getStdArw" alt="" />-->
	<?php
	if(isset($_SESSION['fill_the_questionnaire']) && is_array($_SESSION['fill_the_questionnaire'])){		
		$meal_size="";
		if(trim($_SESSION['fill_the_questionnaire']['gender']) == "male"){
			if(floatval($_SESSION['fill_the_questionnaire']['weight']) < 51)
				$meal_size="100";
			else if(floatval($_SESSION['fill_the_questionnaire']['weight']) > 50 && floatval($_SESSION['fill_the_questionnaire']['weight']) < 76)
				$meal_size="150";
			else
				$meal_size="200";
		}else{
			if(floatval($_SESSION['fill_the_questionnaire']['weight']) < 61)			
				$meal_size="100";
			else
				$meal_size="150";
		}

		$warning=0;
		
		if(intval($_SESSION['fill_the_questionnaire']['age']) < 16 || intval($_SESSION['fill_the_questionnaire']['age']) > 50){
			$warning=1;
		}	
		
		
		$sql_meals="select which_day,meal_time,meal_id,show_nutritional_price,(select meal_price from meals_sizes_prices where meal_id=d.meal_id and meal_size='" . $meal_size . "') as price,m.name,details,photo_name,energy,calories,protein,fat_total,carbohydrates,carbs_veggies,with_or_without_sauce,c.name as meal_category_name from categories_default_meals d left join meals m on d.meal_id=m.id left join meal_category c on m.meal_category_id=c.id where d.meal_plan_category_id='" . $_SESSION['fill_the_questionnaire']['meal_plan_category_id'] . "' and type=1 order by which_day,meal_time ASC";
		$result_default_meals=$db->fetch_all_array($sql_meals);
		$total_default_meals=count($result_default_meals);
		
		$default_meals=array();
		
		for($i=0; $i < $total_default_meals; $i++ ){
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_id']=$result_default_meals[$i]['meal_id'];	
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_name']=$result_default_meals[$i]['name'];
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['details']=$result_default_meals[$i]['details'];
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['photo_name']=$result_default_meals[$i]['photo_name'];
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['energy']=$result_default_meals[$i]['energy'];
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['calories']=$result_default_meals[$i]['calories'];
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['protein']=$result_default_meals[$i]['protein'];
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['fat_total']=$result_default_meals[$i]['fat_total'];
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['carbohydrates']=$result_default_meals[$i]['carbohydrates'];
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['carbs_veggies']=$result_default_meals[$i]['carbs_veggies'];
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['with_or_without_sauce']=$result_default_meals[$i]['with_or_without_sauce'];
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_category_name']=$result_default_meals[$i]['meal_category_name'];
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['price']=$result_default_meals[$i]['price'];
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['show_nutritional_price']=$result_default_meals[$i]['show_nutritional_price'];
		}

		$sql_snacks="select which_day,meal_time,meal_id,price,name,details,photo_name,qty from categories_default_meals d left join snacks s on d.meal_id=s.id where d.meal_plan_category_id='" . $_SESSION['fill_the_questionnaire']['meal_plan_category_id'] . "' and type=2 order by which_day,meal_time ASC";
		$result_default_snacks=$db->fetch_all_array($sql_snacks);
		$total_default_snacks=count($result_default_snacks);
		
		$default_snacks=array();
		
		for($i=0; $i < $total_default_snacks; $i++ ){
			$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['snack_id']=$result_default_snacks[$i]['meal_id'];	
			$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['price']=$result_default_snacks[$i]['price'];	
			$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['name']=$result_default_snacks[$i]['name'];	
			$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['details']=$result_default_snacks[$i]['details'];	
			$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['photo_name']=$result_default_snacks[$i]['photo_name'];	
			$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['qty']=$result_default_snacks[$i]['qty'];	
		}


		?>
		<script type="text/javascript">
			$(document).ready(function(){
				$(".getStdPnl2_container").slideToggle(1000);
				$(".dayPnl1_new li").mouseenter(function(){
					$(this).find(".tip_box").show();
				});

				$(".dayPnl1_new li").mouseleave(function(){
					$(this).find(".tip_box").hide();
				});


				$(".close_pop").click(function(){
					$(this).parent().parent().find(".tip_box").hide();
				});
			})
		</script>
		
		<div class="getStdPnl2_container">	
			<div class="getStdPnl2">
				<div class="mainDiv2">
					<h3 <?=$warning ==1?'style="padding-bottom:20px;"':''; ?>>Here is your suggested Meal Plans</h3>
					<?php if($warning ==1){ ?>
					<p style="color: #ff0000;font: 14px/18px 'open_sansregular';text-align: center; padding: 0 0 20px 0px;">* You must seek medical advice *</p>	
					<?php  } ?>				
					<h4><strong><span>Food Type:</span> <?=$_SESSION['fill_the_questionnaire']['meal_plan_category']?></strong> <strong>
						<?php	
						if($_SESSION['fill_the_questionnaire']['user_can_download_pdf'] == 1){											
							$result_pdf=$db->fetch_all_array("select pdf_file_name from meal_schedule_pdf where eating_schedule=" .  intval($_SESSION['fill_the_questionnaire']['eating_schedule']) . " limit 1");
											
							if(trim($result_pdf[0]['pdf_file_name']) != NULL){ ?>
								<span>Download Eating Schedule:</span> 
							 	<a target="_blank" href="<?=$general_func->site_url."eating_schedule/".trim($result_pdf[0]['pdf_file_name'])?>" ><img src="images/file-pdf.png" /></a>
						<?php }
						} ?>
						</strong> 
						<strong><span>Net Weight:</span> <?=$meal_size?>g</strong></h4>
						<br class="clear">
						<div class="sedulePnl">					
							<?php 							
							for($day=1; $day <=$_SESSION['fill_the_questionnaire']['like_to_eat_how_many_days']; $day++){ ?>
							<div class="dayPnl">
								<div class="dayPnl2 daynumber"><h5>Day <?=$day?></h5></div>
								<br class="clear">
								<div class="dayPnlTgl mealplan">
									<div class="dayPnl1 dayPnl1_new">
										<ul>
											<?php  for($time=1; $time <=$_SESSION['fill_the_questionnaire']['meals_per_day']; $time++ ){?>
											<li>
												<span>Meal <?=$time?> :</span><span><?=$default_meals[$day][$time]['meal_name']?></span>
												<div class="tip_box" style="z-index: 99999;" >
													<div class="close_pop"></div>
													<div class="tip_angle"></div>						                     
													<div class="tip_head"><?=$default_meals[$day][$time]['meal_name']?></div>						                      
													<div class="tip_row">
														<div class="tip_column_container">
															<div class="tip_column">
																<div class="tip_column_info_row">
																	<div class="info_tab">Category :</div>
																	<div class="info_tab"><?=$default_meals[$day][$time]['meal_category_name']?></div>
																</div>
																<?php if($default_meals[$day][$time]['show_nutritional_price'] == 1){ ?>
																<div class="tip_column_info_row">
																	<div class="info_tab">Energy :</div>
																	<div class="info_tab"><?=$default_meals[$day][$time]['energy']?> kcal</div>
																</div>
																<div class="tip_column_info_row">
																	<div class="info_tab">Protein :</div>
																	<div class="info_tab"><?=$default_meals[$day][$time]['protein']?> g</div>
																</div>	
																<?php }?>
																
																
																<div class="tip_column_info_row">
																	<div class="info_tab">Carbs :</div>
																	<div class="info_tab">
																		<?php if(intval($default_meals[$day][$time]['carbs_veggies']) == 3 || intval($default_meals[$day][$time]['carbs_veggies']) == 1){ ?>
																		<img src="images/tip_yes.png" style="margin-top: 4px;" />	
																		<?php }else{ ?>
																		<img src="images/tip_no.png" style="margin-top: 4px;" />
																		<?php }  ?>
																	</div>
																</div>
																<div class="tip_column_info_row">
																	<div class="info_tab">Sauce :</div>
																	<div class="info_tab">
																		<?php if(intval($default_meals[$day][$time]['with_or_without_sauce']) == 1){ ?>
																		<img src="images/tip_yes.png" style="margin-top: 4px;" />	
																		<?php }else{ ?>
																		<img src="images/tip_no.png" style="margin-top: 4px;" />
																		<?php }  ?>
																	</div>
																</div>
															</div>
															<div class="tip_column">
																<?php if($default_meals[$day][$time]['show_nutritional_price'] == 1){ ?>
																<div class="tip_column_info_row">
																	<div class="info_tab">Carbs :</div>
																	<div class="info_tab"><?=$default_meals[$day][$time]['carbohydrates']?>g</div>
																</div>
																<div class="tip_column_info_row">
																	<div class="info_tab">Total Fat :</div>
																	<div class="info_tab"><?=$default_meals[$day][$time]['fat_total']?>g</div>
																</div>
																<div class="tip_column_info_row">
																	<div class="info_tab">Price :</div>
																	<div class="info_tab">$<?=$default_meals[$day][$time]['price']?></div>
																</div>
																<?php } ?>
																<div class="tip_column_info_row">
																	<div class="info_tab">Veggies :</div>
																	<div class="info_tab">
																		<?php if(intval($default_meals[$day][$time]['carbs_veggies']) == 3 || intval($default_meals[$day][$time]['carbs_veggies']) == 2){ ?>
																		<img src="images/tip_yes.png" style="margin-top: 5px;" />	
																		<?php }else{ ?>
																		<img src="images/tip_no.png" style="margin-top: 5px;" />
																		<?php }  ?>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<div class="tip_row">
														<div class="tip_descrip">
															<p>
																<?php if(trim($default_meals[$day][$time]['photo_name']) != NULL){ ?>
																<img class="lefted_img" align="left" src="meal_main/small/<?=trim($default_meals[$day][$time]['photo_name'])?>" width="120">						                            	
																<?php }  ?>	

																<?=nl2br($default_meals[$day][$time]['details'])?></p>
															</div>
														</div>
													</div>			                        		
												</li>
												<?php } 
												  for($time=1; $time <=$_SESSION['fill_the_questionnaire']['snacks_per_day']; $time++ ){?>
												<li>
													<span>Snack <?=$time?> :</span><span><?=$default_snacks[$day][$time]['name']?></span>
													<div class="tip_box" style="z-index: 99999;" >
														<div class="close_pop"></div>
														<div class="tip_angle"></div>						                     
														<div class="tip_head"><?=$default_snacks[$day][$time]['name']?></div>						                      
														 <div class="tip_row">
														<div class="tip_column_container">
															<div class="tip_column">															
																<div class="tip_column_info_row">
																	<div class="info_tab">Price :</div>
																	<div class="info_tab">$<?=$default_snacks[$day][$time]['price']?></div>
																</div>
															</div>	
															<div class="tip_column">																
																<div class="tip_column_info_row">
																	<div class="info_tab">Qty :</div>
																	<div class="info_tab"><?=$default_snacks[$day][$time]['qty']?></div>
																</div>
															</div>															
														</div>
														</div> 
														  
														  <div class="tip_row">
															<div class="tip_descrip">
																<p>
																	<?php if(trim($default_snacks[$day][$time]['photo_name']) != NULL){ ?>
																	<img class="lefted_img" align="left" src="snack_main/small/<?=trim($default_snacks[$day][$time]['photo_name'])?>" width="120">						                            	
																	<?php }  ?>	
																	<?=nl2br($default_snacks[$day][$time]['details'])?></p>
																</div>
															</div>
														</div>			                        		
													</li>
												<?php } ?>
												
											</ul>
										</div>
									</div>
								</div>		
								<?php }  ?>
								<br class="clear">								
								<div class="dayPnlBtn">	
									<input name="submit" type="submit" value="Order Now" onclick="location.href='order-review/'" style="width:100%" />
								
								</div>
							</div>    
						</div>
					</div>
				</div>
				<?php }	?>
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
