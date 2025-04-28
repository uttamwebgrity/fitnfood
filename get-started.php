<?php
include_once("includes/header.php");

//***************  customer chose fill the questionnaire ***************************************//
//** set fill the questionnaire and unset Select your meal plan and  Customize your meal plan **//
//***********************************************************************************************//

if(isset($_POST['enter']) && $_POST['enter']=="get_your_meal_plan" && trim($_POST['frm_get_your_meal_plan'])==$_SESSION['frm_get_your_meal_plan']){
		
	if(isset($_SESSION['choose_your_meal_plan']))
		unset($_SESSION['choose_your_meal_plan']);
			
	if(isset($_SESSION['customize_your_meal_plan']))
		unset($_SESSION['customize_your_meal_plan']);		
		
	$_SESSION['fill_the_questionnaire']=array();		
		
	$_SESSION['fill_the_questionnaire']['meal_plan_category_id']=intval($_REQUEST['meal_plan_category']);
	$row_plan_info=mysql_fetch_object(mysql_query("select name,meals_per_day,snacks_per_day,user_can_download_pdf from meal_plan_category where id='" .intval($_REQUEST['meal_plan_category']). "' limit 1"));
	$_SESSION['fill_the_questionnaire']['meal_plan_category']=$row_plan_info->name;
	$_SESSION['fill_the_questionnaire']['meals_per_day']=$row_plan_info->meals_per_day;
	$_SESSION['fill_the_questionnaire']['snacks_per_day']=$row_plan_info->snacks_per_day;	
	$_SESSION['fill_the_questionnaire']['user_can_download_pdf']=$row_plan_info->user_can_download_pdf;
	$_SESSION['fill_the_questionnaire']['age']=intval($_REQUEST['age']);
	$_SESSION['fill_the_questionnaire']['gender']=trim($_REQUEST['gender']);
	$_SESSION['fill_the_questionnaire']['weight']=trim($_REQUEST['weight']);

	if(trim($_REQUEST['exercising_to_speed_up']) == "no"){
		$_SESSION['fill_the_questionnaire']['eating_schedule']="5";
	}else{
		if(trim($_REQUEST['part_of_the_day_usually_train']) == "morning")
			$_SESSION['fill_the_questionnaire']['eating_schedule']="1";	
		else if(trim($_REQUEST['part_of_the_day_usually_train']) == "lunch_time")
			$_SESSION['fill_the_questionnaire']['eating_schedule']="2";		
		else if(trim($_REQUEST['part_of_the_day_usually_train']) == "after_work")
			$_SESSION['fill_the_questionnaire']['eating_schedule']="3";	
		else
			$_SESSION['fill_the_questionnaire']['eating_schedule']="4";	
	} 

	$_SESSION['fill_the_questionnaire']['exercising_to_speed_up']=trim($_REQUEST['exercising_to_speed_up']);
	$_SESSION['fill_the_questionnaire']['part_of_the_day_usually_train']=trim($_REQUEST['part_of_the_day_usually_train']);
	$_SESSION['fill_the_questionnaire']['like_to_eat_how_many_days']=intval($_REQUEST['like_to_eat_how_many_days']);
}

$exercising_to_speed_up="yes";
if(isset($_SESSION['fill_the_questionnaire']['exercising_to_speed_up']))
	$exercising_to_speed_up=trim($_SESSION['fill_the_questionnaire']['exercising_to_speed_up']);

$part_of_the_day_usually_train="morning";
if(isset($_SESSION['fill_the_questionnaire']['part_of_the_day_usually_train']))
	$part_of_the_day_usually_train=trim($_SESSION['fill_the_questionnaire']['part_of_the_day_usually_train']);

$like_to_eat_how_many_days=7;
if(isset($_SESSION['fill_the_questionnaire']['like_to_eat_how_many_days']))
	$like_to_eat_how_many_days=intval($_SESSION['fill_the_questionnaire']['like_to_eat_how_many_days']);



$sql_content="select get_started_page_heading,get_started_page_content,set_meal_plan_modification  from dynamic_pages where id=1 limit 1";
$result_content=$db->fetch_all_array($sql_content);

?>
<script type="text/javascript" src="ddaccordion_js/ddaccordion.js"></script>
<script type="text/javascript">

	ddaccordion.init({
	headerclass: "daynumber", //Shared CSS class name of headers group
	contentclass: "mealplan", //Shared CSS class name of contents group
	revealtype: "click", //Reveal content when user clicks or onmouseover the header? Valid value: "click", "clickgo", or "mouseover"
	mouseoverdelay: 200, //if revealtype="mouseover", set delay in milliseconds before header expands onMouseover
	collapseprev: true, //Collapse previous content (so only one open at any time)? true/false 
	defaultexpanded: [], //index of content(s) open by default [index1, index2, etc] [] denotes no content
	onemustopen: false, //Specify whether at least one header should be open always (so never all headers closed)
	animatedefault: false, //Should contents open by default be animated into view?
	persiststate: true, //persist state of opened contents within browser session?
	toggleclass: ["", ""], //Two CSS classes to be applied to the header when it's collapsed and expanded, respectively ["class1", "class2"]
	togglehtml: ["prefix", "<img src='images/icons/dyPlus.png' class='statusicon' />", "<img src='images/icons/dyMinus.png' class='statusicon' />"], //Additional HTML added to the header when it's collapsed and expanded, respectively  ["position", "html1", "html2"] (see docs)
	animatespeed: "slow", //speed of animation: integer in milliseconds (ie: 200), or keywords "fast", "normal", or "slow"
	oninit:function(headers, expandedindices){ //custom code to run when headers have initalized
		//do nothing
	},
	onopenclose:function(header, index, state, isuseractivated){ //custom code to run whenever a header is opened or closed
		//do nothing
	}
})

$(document).ready(function(){
	$("#customize-your-own").click(function(){       
    	$.post( "set-personalize-meal.php", function( data ) {
			$(location).attr('href',"<?=$general_func->site_url?>customize-your-own/");
		});
    });
});
</script>
<div class="inrBnr">
	<?php $db_common->static_page_banner($dynamic_content['page_id']);?>								
</div>								
<div class="hmPanelTwo" style="position:relative">
	<div class="mainDiv2">
   
  <br class="clear" />
		<h2 style="font-size:28px"><?php echo $result_content[0]['get_started_page_heading']; ?></h2>
		<p><?php echo nl2br($result_content[0]['get_started_page_content']); ?></p>
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
	<img src="images/icons/downArw.png" class="getStdArw" alt="" />
	<?php
	if(isset($_SESSION['fill_the_questionnaire']) && is_array($_SESSION['fill_the_questionnaire'])){
		$_SESSION['personalize_meal']=0;
		//***************  set default meals for customization ***************************************//
		$_SESSION['default']['meal_plan_category_id']=$_SESSION['fill_the_questionnaire']['meal_plan_category_id'];
		$_SESSION['default']['no_of_days']=$_SESSION['fill_the_questionnaire']['like_to_eat_how_many_days'];
		$_SESSION['default']['meals_per_day']=$_SESSION['fill_the_questionnaire']['meals_per_day'];
		$_SESSION['default']['snacks_per_days']=$_SESSION['fill_the_questionnaire']['snacks_per_day'];		
		//***********************************************************************************************//	
			
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
		
			//***************  set default meals for customization ******************************************//			
			$_SESSION['default']['meals'][$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_id']=$result_default_meals[$i]['meal_id'];
			$_SESSION['default']['meals'][$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['size']=$meal_size;
			//***********************************************************************************************//	
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
		
			//***************  set default meals for customization ******************************************//			
			$_SESSION['default']['snacks'][$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['snack_id']=$result_default_snacks[$i]['meal_id'];	
			$_SESSION['default']['snacks'][$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['qty']=$result_default_snacks[$i]['qty'];	
			//***********************************************************************************************//	
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
						<strong><span>Net Weight:</span> <?=$meal_size?>g</strong>
						<strong><span>Meal Plan Price:</span> <label id="plan_price"></label> </strong></h4>
						<br class="clear">
						<div class="sedulePnl">					
							<?php 	
							$price=0;	
													
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
																	<div class="info_tab"><?=$db_common->nutritional_value($default_meals[$day][$time]['energy'],$meal_size)?> kcal</div>
																</div>
																<div class="tip_column_info_row">
																	<div class="info_tab">Protein :</div>
																	<div class="info_tab"><?=$db_common->nutritional_value($default_meals[$day][$time]['protein'],$meal_size)?>g</div>
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
																	<div class="info_tab">Calories :</div>
																	<div class="info_tab"><?=$db_common->nutritional_value($default_meals[$day][$time]['calories'],$meal_size)?>g</div>
																</div>
																<div class="tip_column_info_row">
																	<div class="info_tab">Carbs :</div>
																	<div class="info_tab"><?=$db_common->nutritional_value($default_meals[$day][$time]['carbohydrates'],$meal_size)?>g</div>
																</div>
																<div class="tip_column_info_row">
																	<div class="info_tab">Total Fat :</div>
																	<div class="info_tab"><?=$db_common->nutritional_value($default_meals[$day][$time]['fat_total'],$meal_size)?>g</div>
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
												<?php
													$price += $default_meals[$day][$time]['price'];
												 } 
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
												<?php 
													$price += ($default_snacks[$day][$time]['price'] * $default_snacks[$day][$time]['qty']);
													} ?>
												
											</ul>
										</div>
									</div>
								</div>		
								<?php }
								$price += floatval($general_func->meal_plan_amout_for_training_cost);
								$meal_price=number_format($price, 2);
								  ?>
								  <script>
									$("#plan_price").html("$<?=$meal_price?>");
									 
								</script>
								<br class="clear">								
								<div class="dayPnlBtn">	
									<input name="submit" type="submit" value="Order Now" onclick="location.href='<?=$general_func->site_url?>order-review/'" style="width:100%" />
								</div>
                                <p style="color: #ff0000;font: 14px/18px 'open_sansregular';text-align: center; padding:10px 0;"><?php echo $result_content[0]['set_meal_plan_modification']; ?></p>
                                <div class="dayPnlBtn" style="margin-top:0">	
									<input name="submit" type="submit" value="Modify Meal Plan" onclick="location.href='<?=$general_func->site_url?>customize-your-own/#meal'" style="width:90%; margin:0 5%" class="mdfyMealPln" />
								</div>
							</div>    
						</div>
					</div>
				</div>
				<?php }	?>
			</div>
			<div class="getStdPnl">
				<div class="mainDiv2">
					<div class="getStdPnlLft"><img src="images/icons/sltYrPln.png" alt="" /><br class="clear"><a href="select-your-meal-plan/" class="getStdBtn">Select your meal plan</a></div>
					<div class="getStdPnlRht"><img src="images/icons/customYrOwn.png" alt="" /><br class="clear"><a style="cursor: pointer;" id="customize-your-own"  class="getStdBtn">Customize your own</a></div>
				</div>
			</div>
			<?php
			include_once("includes/footer.php");
			?>