<?php
include_once ("includes/header.php");

////***************  Customized your meal plan ***************************************//
//** Customized your meal plan and unset  fill the questionnaire and  select your meal plan **//
//***********************************************************************************************//
if (isset($_POST['enter']) && $_POST['enter'] == "customize_your_meal_plan_final" && trim($_POST['frm_customize_your_meal_plan']) == $_SESSION['frm_customize_your_meal_plan']) {

	if (isset($_SESSION['fill_the_questionnaire']))
		unset($_SESSION['fill_the_questionnaire']);

	if (isset($_SESSION['choose_your_meal_plan']))
		unset($_SESSION['choose_your_meal_plan']);

	for ($day = 1; $day <= $_SESSION['customize_your_meal_plan']['no_of_days']; $day++) {
		for ($time = 1; $time <= $_SESSION['customize_your_meal_plan']['meal_per_day']; $time++) {
			$_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_id'] = intval($_REQUEST['meal_id_' . $day . '_' . $time]);
			$_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_size'] = intval($_REQUEST[$day . '_' . $time]);
		}

		for ($time = 1; $time <= $_SESSION['customize_your_meal_plan']['snack_per_day']; $time++) {
			$_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_id'] = intval($_REQUEST['snack_id_' . $day . '_' . $time]);
			$_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_qty'] = intval($_REQUEST['snack_qty_' . $day . '_' . $time]);
		}
	}

	$general_func -> header_redirect($general_func -> site_url . "order-review/");

}

if (isset($_POST['enter']) && $_POST['enter'] == "customize_your_meal_plan" && trim($_POST['frm_customize_your_meal_plan']) == $_SESSION['frm_customize_your_meal_plan']) {
	$_SESSION['customize_your_meal_plan'] = array();
	$_SESSION['personalize_meal']=1;

	$_SESSION['customize_your_meal_plan']['meal_plan_category_id'] = intval($_REQUEST['meal_plan_category_id']);
	$_SESSION['customize_your_meal_plan']['no_of_days'] = intval($_REQUEST['no_of_days']);
	$_SESSION['customize_your_meal_plan']['meal_per_day'] = intval($_REQUEST['meal_per_day']);
	$_SESSION['customize_your_meal_plan']['snack_per_day'] = intval($_REQUEST['snack_per_day']);

	$_SESSION['customize_your_meal_plan']['exercising_to_speed_up'] = trim($_REQUEST['exercising_to_speed_up']);

	if (trim($_SESSION['customize_your_meal_plan']['exercising_to_speed_up']) == "no") {
		if (isset($_SESSION['customize_your_meal_plan']['part_of_the_day_usually_train']))
			unset($_SESSION['customize_your_meal_plan']['part_of_the_day_usually_train']);
	} else {
		$_SESSION['customize_your_meal_plan']['part_of_the_day_usually_train'] = trim($_REQUEST['part_of_the_day_usually_train']);
	}
}

$exercising_to_speed_up = "yes";
$part_of_the_day_usually_train = "morning";
$user_can_download_pdf=0;

if(isset($_SESSION['default'])){
	$_SESSION['customize_your_meal_plan']['exercising_to_speed_up']=$_SESSION['fill_the_questionnaire']['exercising_to_speed_up'];
	$_SESSION['customize_your_meal_plan']['part_of_the_day_usually_train']=$_SESSION['fill_the_questionnaire']['part_of_the_day_usually_train'];
	$user_can_download_pdf=1;
}

if (isset($_SESSION['customize_your_meal_plan']['exercising_to_speed_up']))
	$exercising_to_speed_up = trim($_SESSION['customize_your_meal_plan']['exercising_to_speed_up']);


if (isset($_SESSION['customize_your_meal_plan']['part_of_the_day_usually_train']))
	$part_of_the_day_usually_train = trim($_SESSION['customize_your_meal_plan']['part_of_the_day_usually_train']);



if(isset($_SESSION['customize_your_meal_plan']['user_can_download_pdf']) && $_SESSION['customize_your_meal_plan']['user_can_download_pdf'] == 1)
	$user_can_download_pdf=1;





$sql_content="select customize_meal_plan_page_left_heading,customize_meal_plan_page_left_content,customize_meal_plan_page_right_heading,customize_meal_plan_page_right_content,meal_price_variations_chart,customize_meal_plan_page_alert from dynamic_pages where id=1 limit 1";
$result_content=$db->fetch_all_array($sql_content);




?>
<script type="text/javascript" src="ddaccordion_js/ddaccordion.js"></script>
<script type="text/javascript">
	ddaccordion.init({
		headerclass : "daynumber", //Shared CSS class name of headers group
		contentclass : "mealplan", //Shared CSS class name of contents group
		revealtype : "click", //Reveal content when user clicks or onmouseover the header? Valid value: "click", "clickgo", or "mouseover"
		mouseoverdelay : 200, //if revealtype="mouseover", set delay in milliseconds before header expands onMouseover
		collapseprev : true, //Collapse previous content (so only one open at any time)? true/false
		defaultexpanded : [], //index of content(s) open by default [index1, index2, etc] [] denotes no content
		onemustopen : false, //Specify whether at least one header should be open always (so never all headers closed)
		animatedefault : false, //Should contents open by default be animated into view?
		persiststate : true, //persist state of opened contents within browser session?
		toggleclass : ["", ""], //Two CSS classes to be applied to the header when it's collapsed and expanded, respectively ["class1", "class2"]
		togglehtml : ["prefix", "<img src='images/icons/dyPlus.png' class='statusicon' />", "<img src='images/icons/dyMinus.png' class='statusicon' />"], //Additional HTML added to the header when it's collapsed and expanded, respectively  ["position", "html1", "html2"] (see docs)
		animatespeed : "slow", //speed of animation: integer in milliseconds (ie: 200), or keywords "fast", "normal", or "slow"
		oninit : function(headers, expandedindices) { //custom code to run when headers have initalized
			//do nothing
		},
		onopenclose : function(header, index, state, isuseractivated) { //custom code to run whenever a header is opened or closed
			//do nothing
		}
	})

	function validate_customized_your_meal_plan() {
		var error = 0;

		var meal_plan_category_id = $.trim($("#meal_plan_category_id").val());
		var no_of_days = $.trim($("#no_of_days").val());
		var meal_per_day = $.trim($("#meal_per_day").val());
		/*var carbs_veggies = $.trim($("#carbs_veggies").val());
		 var with_or_without_sauce = $.trim($("#with_or_without_sauce").val());*/

		if(meal_plan_category_id == '') {
			document.getElementById("meal_plan_category_id").style.border = "1px solid red";
			error++;
		} else {
			document.getElementById("meal_plan_category_id").style.border = "1px solid #CBD2BB";
		}

		if(no_of_days == '') {
			document.getElementById("no_of_days").style.border = "1px solid red";
			error++;
		} else {
			document.getElementById("no_of_days").style.border = "1px solid #CBD2BB";
		}

		if(meal_per_day == '') {
			document.getElementById("meal_per_day").style.border = "1px solid red";
			error++;
		} else {
			document.getElementById("meal_per_day").style.border = "1px solid #CBD2BB";
		}

		if(error > 0)
			return false;
		else
			return true;
	}

	function decide_training_part(val) {
		if(parseInt(val) == 1)
			$("#div_part_of_the_day_usually_train").show(1000);
		else
			$("#div_part_of_the_day_usually_train").hide(1000);
	}

	function decide_training_timing(val) {		
		if(parseInt(val) > 0) {
			$.get("decide-training-timing.php?id=" + val, function(data) {
				if(data == 1) {
					$("#training_time_id").show(1000);
				} else {
					$("#training_time_id").hide(1000);
				}
			});
		}
	}




function meals_validate(){
	var error=0;
	
	var no_of_days=parseInt(document.customize_your_meal_plan.no_of_days.value);
	var meal_per_day=parseInt(document.customize_your_meal_plan.meal_per_day.value);
	
	var snack_per_day=parseInt(document.customize_your_meal_plan.snack_per_day.value);
	
	 var message="Please choose your ";
	 
	 var message_added=0; 
	
	for(var day=1; day <= no_of_days; day++ ){
		for(var time=1; time <= meal_per_day; time++ ){						
			if(document.getElementById("meal_id_"+ day + "_" +time).value == ""){
      			document.getElementById("meal_id_"+ day + "_" +time).style.border="1px solid #ff5657";
      			if(message_added == 0){
      				message += " day " + day;
      				message_added=1;
      			}
      			
				error++;
      		}else{
      			document.getElementById("meal_id_"+ day + "_" +time).style.border="1px solid #D8D9DA";	
      		}      		
		}
		
		
		for(var time=1; time <= snack_per_day; time++ ){			
			if(document.getElementById("snack_id_"+ day + "_" +time).value == ""){
      			document.getElementById("snack_id_"+ day + "_" +time).style.border="1px solid #ff5657";
      			if(message_added == 0){
      				message += " day " + day;
      				message_added=1;
      			}
      			
				error++;
      		}else{
      			document.getElementById("snack_id_"+ day + "_" +time).style.border="1px solid #D8D9DA";	
      		}      		
		}
			
	}
	message += " meals"; 
	
	
	if(error >0){
		$("#show_message").html(message);
		return false;
	}else{		
		return true;	
	}	
}	

function display_price(){
	$("#show_total").text(0);
	
	var no_of_days=parseInt(document.customize_your_meal_plan.no_of_days.value);	
	var meal_per_day=parseInt(document.customize_your_meal_plan.meal_per_day.value);		
	var snack_per_day=parseInt(document.customize_your_meal_plan.snack_per_day.value);
	
	for(var day=1; day <= no_of_days; day++ ){	
		for(var time=1; time <= meal_per_day; time++ ){						
			if(document.getElementById("meal_id_" + day + "_" + time).value != ""){
				var meal_id = document.getElementById("meal_id_" + day + "_" + time).value;				
				var meal_qty = document.getElementById(day + "_" + time).value;				
				
				$.get("calculate-price.php?meal_id=" + meal_id + "&meal_qty="+ meal_qty +"&type=meal" , function(data) {					
					$("#show_total").html((parseFloat($("#show_total").text()) +  parseFloat(data)).toFixed(2));
				});	 		 				
			}
		}		
		
		for(var time=1; time <= snack_per_day; time++ ){					
			if(document.getElementById("snack_id_"+ day + "_" +time).value != ""){				
      			var snack_id=document.getElementById("snack_id_"+ day + "_" +time).value;
				var snack_qty=document.getElementById("snack_qty_"+ day + "_" +time).value;						
				$.get("calculate-price.php?snack_id=" + snack_id + "&snack_qty="+ snack_qty +"&type=snack" , function(data) {					
					$("#show_total").html((parseFloat($("#show_total").text()) +  parseFloat(data)).toFixed(2)) ;
				});						
      		}       		    		     		
		}
				
	}	
	$("#show_total").html((parseFloat($("#show_total").text()) +  parseFloat($("#meal_plan_amout_for_training_cost").val())).toFixed(2)) ;	
	
}

</script>
<div class="inrBnr">
	<?php $db_common -> static_page_banner($dynamic_content['page_id']); ?>
</div>
<div class="mealPln normal_select">
	<div class="mainDiv2">
		<?php if(trim($result_content[0]['meal_price_variations_chart']) != NULL){ ?>
		<div class="variationsChart">
			<a target="_blank" href="eating_schedule/<?=trim($result_content[0]['meal_price_variations_chart'])?>"><input type="submit" onclick="" value="Menu and Meal Prices" name=""></a></div>
           <br class="clear" />     
			
		<?php } ?>
    	
            
		<form name="customize_your_meal_plan" method="post" action="customize-your-own/#meal" onsubmit="return validate_customized_your_meal_plan();">
			<input type="hidden" name="enter" value="customize_your_meal_plan" />
			<input type="hidden" name="frm_customize_your_meal_plan" value="<?=$_SESSION['frm_customize_your_meal_plan'] ?>" />
			<input type="hidden" name="total_price_display" id="total_price_display" value="0" />
			<input type="hidden" name="meal_plan_amout_for_training_cost" id="meal_plan_amout_for_training_cost" value="<?=$general_func->meal_plan_amout_for_training_cost?>" />
			
			<div class="mealPlnColOne main_goal_drop_down">
				<h1><?php echo $result_content[0]['customize_meal_plan_page_left_heading']; ?></h1>
				<p><?php if(isset($_SESSION['personalize_meal']) && intval($_SESSION['personalize_meal']) ==0){
					echo nl2br($result_content[0]['customize_meal_plan_page_alert']);
				}else{ 
					 echo nl2br($result_content[0]['customize_meal_plan_page_left_content']);
				} ?>
				</p>
				<br />
				<div style="display: <?=(isset($_SESSION['personalize_meal']) && intval($_SESSION['personalize_meal'])==0)?'none':'block';?>">	
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
							
						if(! isset($_SESSION['customize_your_meal_plan']['meal_plan_category_id']) && isset($_SESSION['default']['meal_plan_category_id']))
							$_SESSION['customize_your_meal_plan']['meal_plan_category_id']=$_SESSION['default']['meal_plan_category_id'];	
												
						
												
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
						<?php						
						if(! isset($_SESSION['customize_your_meal_plan']['no_of_days']) && isset($_SESSION['default']['no_of_days']))
							$_SESSION['customize_your_meal_plan']['no_of_days']=$_SESSION['default']['no_of_days'];
						?>	
											
						<option value="5" <?=intval($_SESSION['customize_your_meal_plan']['no_of_days']) == 5 ? 'selected="selected"' : ''; ?>>5</option>
						<option value="7" <?=intval($_SESSION['customize_your_meal_plan']['no_of_days']) == 7 ? 'selected="selected"' : ''; ?>>7</option>
					</select></label>
				<div class="short-dual-column-box">
					<div class="dual-column-field">
						<p>
							<strong>Meals per Day</strong>
						</p>
						<label class="custom-select">
							<?php							
								if(! isset($_SESSION['customize_your_meal_plan']['meal_per_day']) && isset($_SESSION['default']['meals_per_day']))
									 $_SESSION['customize_your_meal_plan']['meal_per_day']=$_SESSION['default']['meals_per_day'];
								?>
							<select name="meal_per_day" id="meal_per_day" class="selStlThr">
								<option value="">Select One</option>
								<?php for($start=$meal_per_day_min; $start <= $meal_per_day_max; $start++ ){ ?>
								<option value="<?=$start?>" <?=intval($_SESSION['customize_your_meal_plan']['meal_per_day']) == $start ? 'selected="selected"' : ''; ?>><?=$start?></option>	
								<?php } ?>
							</select> </label>
					</div>
					<div class="dual-column-field">
						<p>
							<strong>Snacks per Day</strong>
						</p>
						<label class="custom-select">
							<select name="snack_per_day" id="snack_per_day" class="selStlThr">
								<option value="">Select One</option>
								<?php
								if(! isset($_SESSION['customize_your_meal_plan']['snack_per_day']) && isset($_SESSION['default']['snacks_per_days']))
									 $_SESSION['customize_your_meal_plan']['snack_per_day']=$_SESSION['default']['snacks_per_days'];
								?>
								<option value="0" <?=intval($_SESSION['customize_your_meal_plan']['snack_per_day']) == 0 ? 'selected="selected"' : ''; ?>>0</option>
								<option value="1" <?=intval($_SESSION['customize_your_meal_plan']['snack_per_day']) == 1 ? 'selected="selected"' : ''; ?>>1</option>
								<option value="2" <?=intval($_SESSION['customize_your_meal_plan']['snack_per_day']) == 2 ? 'selected="selected"' : ''; ?>>2</option>
								<option value="3" <?=intval($_SESSION['customize_your_meal_plan']['snack_per_day']) == 3 ? 'selected="selected"' : ''; ?>>3</option>
								<option value="4" <?=intval($_SESSION['customize_your_meal_plan']['snack_per_day']) == 4 ? 'selected="selected"' : ''; ?>>4</option>
								<option value="5" <?=intval($_SESSION['customize_your_meal_plan']['snack_per_day']) == 5 ? 'selected="selected"' : ''; ?>>5</option>
							</select> </label>
					</div>
				</div>
				<div class="melPlnrFrmRght melPlnrFrmRght-type-two" id="training_time_id" style="margin-top: 10px; display: <?=$user_can_download_pdf == 1? 'block;' : 'none;'; ?>">
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
				
				
			</div>
		</form><a name="meal"></a>
		<div class="mealPlnColTwo">
            <h1><?php echo $result_content[0]['customize_meal_plan_page_right_heading']; ?></h1>
			<p id="default_content" style="display: <?=isset($_SESSION['customize_your_meal_plan'])? 'none' : 'block'; ?>;">
				<?php echo nl2br($result_content[0]['customize_meal_plan_page_right_content']); ?>
			</p>
			<br class="clear">
			<div class="sedulePnl" id="selected_meal" style="display: <?=isset($_SESSION['customize_your_meal_plan'])? 'block': 'none';?>;">
				<form name="customize_your_meal_plan_final" method="post" action="customize-your-own/" onsubmit="return meals_validate();" >
					<input type="hidden" name="enter" value="customize_your_meal_plan_final" />
					<input type="hidden" name="frm_customize_your_meal_plan" value="<?=$_SESSION['frm_customize_your_meal_plan'] ?>" />

					<?php
					if(isset($_SESSION['customize_your_meal_plan']) && is_array($_SESSION['customize_your_meal_plan'])){
					$display_price=$general_func->meal_plan_amout_for_training_cost;
					//and with_or_without_sauce='" . $_SESSION['customize_your_meal_plan']['with_or_without_sauce'] . "'  and carbs_veggies='" . $_SESSION['customize_your_meal_plan']['carbs_veggies'] . "'
					$result_meals=$db->fetch_all_array("select id,name from meals where status=1 and id IN(select DISTINCT(meal_id) from meal_plan_category_meals where status=1 and meal_plan_category_id='" . $_SESSION['customize_your_meal_plan']['meal_plan_category_id'] . "')   order by name ASC");
					$total_meals=count($result_meals);

					if( $_SESSION['customize_your_meal_plan']['snack_per_day'] > 0){
					$result_snacks=$db->fetch_all_array("select id,name from snacks where status=1 and id IN(select DISTINCT(snack_id) from meal_plan_category_snacks where meal_plan_category_id='" . $_SESSION['customize_your_meal_plan']['meal_plan_category_id'] . "')   order by name ASC");
					$total_snacks=count($result_snacks);
					}
					
					//print_r ($_SESSION['default']['snacks']);
					for($day=1; $day <= $_SESSION['customize_your_meal_plan']['no_of_days']; $day++ ){?>
					<div class="dayPnl">
						<div class="dayPnl2 daynumber" >
							<h5>Day <?=$day ?></h5>
						</div>
						<br class="clear">
						<div class="dayPnlTgl mealplan">
							<div class="selDayPnl1 new_sel_day">
								<ul>
									<?php for($time=1; $time <= $_SESSION['customize_your_meal_plan']['meal_per_day']; $time++ ){
											
										if(! isset($_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_id']) && isset($_SESSION['default']['meals'][$day][$time]['meal_id'])){
											$_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_id']=$_SESSION['default']['meals'][$day][$time]['meal_id'];
											$_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_size']=$_SESSION['default']['meals'][$day][$time]['size'];
										}			
										
										$selected_meal=isset($_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_id'])?$_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_id']:'';
										$selected_size=isset($_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_size'])?$_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_size']:100;
										
										
										if(intval($selected_meal) > 0){
											 $display_price += floatval(mysql_result(mysql_query("select meal_price from meals_sizes_prices where meal_id = '" . mysql_real_escape_string($selected_meal)."' and meal_size = '" . mysql_real_escape_string($selected_size)."'"),0,0));	
																					
}
								?>
									<li>
										<span>Meal <?=$time ?> :</span>
										<div class="dual_column_select_box">
											<div class="dual_column_select_block">
												<label class="custom-select">
													<select onchange="display_price();" name="meal_id_<?=$day . "_" . $time ?>" id="meal_id_<?=$day . "_" . $time ?>"  >
														<option value="">Select your meal</option>
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
													<select name="<?=$day . "_" . $time ?>"  id="<?=$day . "_" . $time ?>" onchange="display_price();">
														<option value="100" <?=intval($selected_size) == 100 ? 'selected="selected"' : ''; ?>>100gm</option>
														<option  value="150" <?=intval($selected_size) == 150 ? 'selected="selected"' : ''; ?>>150gm</option>
														<option  value="200" <?=intval($selected_size) == 200 ? 'selected="selected"' : ''; ?>>200gm</option>
													</select> </label>
											</div>
										</div>
									</li>
									<?php }
										for($time=1; $time <= $_SESSION['customize_your_meal_plan']['snack_per_day']; $time++ ){											
											
										if(! isset($_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_id']) && isset($_SESSION['default']['snacks'][$day][$time]['snack_id'])){
											$_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_id']=$_SESSION['default']['snacks'][$day][$time]['snack_id'];
											$_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_qty']=$_SESSION['default']['snacks'][$day][$time]['qty'];	
										}	
											
																						
										$selected_snack=isset($_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_id'])?$_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_id']:'';
										$selected_qty=isset($_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_qty'])?$_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_qty']:1;
										
										if(intval($selected_snack) > 0){											
											$display_price += floatval(mysql_result(mysql_query("select price from snacks where id = '" . mysql_real_escape_string($selected_snack)."'"),0,0)) * $selected_qty;
										}
										
									?>
									<li>
										<span>Snack <?=$time ?> :</span>
										<div class="dual_column_select_box">
											<div class="dual_column_select_block">
												<label class="custom-select">
													<select name="snack_id_<?=$day . "_" . $time ?>" id="snack_id_<?=$day . "_" . $time ?>" onchange="display_price();">
														<option value="">Select your snack</option>
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
													<input onblur="display_price();" type="text" name="snack_qty_<?=$day . "_" . $time ?>" id="snack_qty_<?=$day . "_" . $time ?>" value="<?=$selected_qty ?>" style="text-align: center;" />
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
					<div  style="padding-top:10px; color: #99bf13; text-align: center; font-size: 24px;">Total Meal plan price: $<label id="show_total"><?=number_format($display_price, 2)?></label></div>
					<div id="show_message" style="padding-top:10px; color: #ff0000; text-align: center;"></div>
					<div class="checkout_row" style="border:none; margin-top:0">
					<div style="width:200px; margin:0 auto"><input name="submit" type="submit" value="Order Now" style="width:200px;" /></div>
					</div>
					<?php } ?>
				</form>
			</div>
		</div>
	</div>
</div>
<?php
unset($_SESSION['default']);

include_once ("includes/footer.php");
?>