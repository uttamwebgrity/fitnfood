<?php
include_once("includes/header.php");

if(isset($_REQUEST['login']) && trim($_REQUEST['login'])=="facebook"){		
	$general_func->header_redirect($general_func->site_url ."login-facebook.php");	
}else if(isset($_REQUEST['login']) && trim($_REQUEST['login'])=="google"){
	$general_func->header_redirect($general_func->site_url ."google-login.php");
}


$sql_content="select home_page_heading,home_page_content,home_page_meal_plan_category_heading from dynamic_pages where id=1 limit 1";
$result_content=$db->fetch_all_array($sql_content);

?>
<script>
$(document).ready(function(){
	$("#customize-your-own").click(function(){       
    	$.post( "set-personalize-meal.php", function( data ) {
			$(location).attr('href',"<?=$general_func->site_url?>customize-your-own/");
		});
    });
});
</script>
<div class="hmSlider">
	<div class="flexslider clearfix">
		<ul class="slides">    		
			<?php
			$sql_banners="select * from banners where banner_type=1  order by display_order + 0 ASC";
			$result_banners=$db->fetch_all_array($sql_banners);
			$total_banner=count($result_banners);

			for($banner=0; $banner < $total_banner; $banner++){
				
				if(trim($result_banners[$banner]['video_or_image']) == 2 && trim($result_banners[$banner]['banner_path']) != NULL ){ ?>
				<li><img src="banner_images/<?=trim($result_banners[$banner]['banner_path'])?>" class="sldrPic" alt=" " />
				<div class="mainDiv">
					<div class="hmSliderTxt">
						<?=trim($result_banners[$banner]['banner_description'])?>
						<?php if(trim($result_banners[$banner]['banner_link']) != NULL){?>
						<div class="hmSliderBtn"><a target="<?=(int)trim($result_banners[$banner]['banner_target'])==1?'_self':'_blank'?>" href="<?=trim($result_banners[$banner]['banner_link'])?>"><?php echo trim($result_banners[$banner]['link_name']) == NULL ?'Get Started':trim($result_banners[$banner]['link_name']); ?></a></div>
						<?php } ?>
					</div>
				</div>
				</li>	
				<?php }else{ ?>
					<li>	
		            <img src="images/ytVdoBg.jpg" alt="" class="sldrPic" />		
					<?php echo $result_banners[$banner]['embedded_video_code']; ?>
				 </li>						
				<?php }	 
			}?>
			
		</ul>
	</div>
	<div class="sldrSdwo">&nbsp;</div>
</div>
<div class="hmPanelOne">
	<div class="mainDiv">
		<h2><?=$result_content[0]['home_page_meal_plan_category_heading']?></h2>
		<div class="mainDiv2">
			<script>
				function validate_choose_your_meal_plan(frmid){					
					var error=0;	
					
					if(document.getElementById("category_meal_plan_"+frmid).value == ''){
						document.getElementById("category_meal_plan_"+frmid).style.border="1px solid red";
						error++;
					}else{
						document.getElementById("category_meal_plan_"+frmid).style.border="1px solid #CBD2BB";	
					}
					
					if(error>0)
						return false;
					else
						return true;
				}
			</script>			
			<?php
			$sql_meal_plan_cat="select id,name,seo_link,details,photo_name1,photo_name2,photo_name3 from meal_plan_category where status=1 and id IN(1,2,3)";
			$result_meal_plan_cat=$db->fetch_all_array($sql_meal_plan_cat);
			$total_meal_plan_cat=count($result_meal_plan_cat);
			?>
			<div class="tabSldr">
				<div class="tabSldrLst">
					<ul id="countrytabs">
						<?php for($link=0; $link < $total_meal_plan_cat; $link++){ ?>
						<li><a  style="cursor: pointer;" rel="food<?=$link?>" <?=$link==0?'class="selected"':'';?>><?=$result_meal_plan_cat[$link]['name']?></a></li>
						<?php }	?>		            	
					</ul>
				</div>
				<?php 
				reset($result_meal_plan_cat);
				for($link=0; $link < $total_meal_plan_cat; $link++){ ?>
				<div class="tabContent" id="food<?=$link?>">
					<h3><?=$result_meal_plan_cat[$link]['name']?></h3>
					<p><?=nl2br($result_meal_plan_cat[$link]['details'])?></p>
					<br class="clear" />
					<img src="category_images/<?=$result_meal_plan_cat[$link]['photo_name1']?>" alt="" /> <img src="category_images/<?=$result_meal_plan_cat[$link]['photo_name2']?>" alt="" /> <img src="category_images/<?=$result_meal_plan_cat[$link]['photo_name3']?>" alt="" /> <br class="clear" />
					<div class="chsMealPln normal_select">
						<form name="choose_your_meal_plan<?=$link?>" method="post" action="select-your-meal-plan/#meal" onsubmit="return validate_choose_your_meal_plan('<?=$link?>');">
							<input type="hidden" name="enter" value="choose_your_meal_plan" />
							<input type="hidden" name="frm_choose_your_meal_plan" value="<?=$_SESSION['frm_choose_your_meal_plan']?>" />
							<input type="hidden" name="meal_plan_category_id" value="<?=$result_meal_plan_cat[$link]['id']?>" />
							<label class="custom-select">
								<select name="category_meal_plan" id="category_meal_plan_<?=$link?>">
									<option value="">Choose your meal plan </option>
									<?php
									$sql_meal_plan="select id,name,seo_link from meal_plans where status=1 and meal_plan_category_id='" . $result_meal_plan_cat[$link]['id'] . "' and id IN(select DISTINCT(meal_plan_id) from meal_plan_meals) order by name ASC";
									$result_meal_plan=$db->fetch_all_array($sql_meal_plan);
									$total_meal_plan=count($result_meal_plan);
									
									for($plan=0; $plan < $total_meal_plan; $plan++){
										//***********************  calculate price ************************************//
										$price =0.00;
										
										$sql_meals="select (select meal_price from meals_sizes_prices where meal_id=d.meal_id and meal_size=d.meal_size) as price from meal_plan_meals d left join meals m on d.meal_id=m.id where d.meal_plan_id='" . intval($result_meal_plan[$plan]['id']) . "' and type=1 ";
										$result_default_meals=$db->fetch_all_array($sql_meals);
										$total_default_meals=count($result_default_meals);
										
										$default_meals=array();
										
										for($i=0; $i < $total_default_meals; $i++ ){
											$price += $result_default_meals[$i]['price'];
										}
										
																				
										$sql_snacks="select meal_size,price from meal_plan_meals d left join snacks m on d.meal_id=m.id where d.meal_plan_id='" . intval($result_meal_plan[$plan]['id']) . "' and type=2 order by which_day,meal_time ASC";
										$result_default_snacks=$db->fetch_all_array($sql_snacks);
										$total_default_snacks=count($result_default_snacks);
										
										$default_snacks=array();
										
										for($i=0; $i < $total_default_snacks; $i++ ){
											$price += intval($result_default_snacks[$i]['meal_size']) * $result_default_snacks[$i]['price'];
										}
										
										if($general_func->meal_plan_amout_for_training_cost > 0)
											$price += $general_func->meal_plan_amout_for_training_cost;
										
										//******************************************************************************//
										
										
										?>
									<option value="<?=$result_meal_plan[$plan]['id']?>"><?=$result_meal_plan[$plan]['name']?></option>									
									<?php }	?>
								</select><!-- - $<?=number_format($price,2)?> p/w -->
							</label>
							<input name="submit" type="submit" value="Proceed" />
						</form>	
					</div>
				</div>
				<?php } ?>        
				<a href="javascript:countries.cycleit('prev')"><img src="images/tabArwP.png" alt="" class="prv"  /></a> <a href="javascript:countries.cycleit('next')"><img src="images/tabArwN.png" alt="" class="nxt" /></a> 
				<script type="text/javascript">
					var countries=new ddtabcontent("countrytabs");
					countries.setpersist(true);
					countries.setselectedClassTarget("link");
					countries.init();
				</script> 
			</div>
		</div>
	</div>
    <br class="clear" />
    <br class="clear" />
    <a id="customize-your-own" style="cursor: pointer;" class="cstomzUrOwnHmBtn">Create your own plan</a>
</div>
<div class="hmPanelTwo">
	<div class="mainDiv2">		
		<h2><?php echo $result_content[0]['home_page_heading']; ?></h2>
		<p><?php echo nl2br($result_content[0]['home_page_content']); ?></p>
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
						<li class="normal_select ">
							<label class="custom-select">
								<select name="meal_plan_category" id="meal_plan_category" class="lg" >
									<option>What is your main goal?</option>
									<?php
									$sql_meal_plan_cat="select id,name from meal_plan_category where id IN(select DISTINCT(meal_plan_category_id) from categories_default_meals) and status=1 order by display_order + 0 ASC";
									$result_meal_plan_cat=$db->fetch_all_array($sql_meal_plan_cat);
									$total_meal_plan_cat=count($result_meal_plan_cat);

									for($c=0; $c < $total_meal_plan_cat; $c++){ ?>
									<option value="<?=$result_meal_plan_cat[$c]['id']?>"><?=$result_meal_plan_cat[$c]['name']?></option>
									<?php } ?>
								</select>
							</label>
						</li>
						<li class="normal_select for_gender_select"><span>Your Age</span>
							<input name="age" type="text" id="age" style="height: 43px;" />
							<span>Yrs</span>
							<label class="custom-select" style="float:right">
								<select name="gender" id="gender" class="sml">
									<option>Gender</option>
									<option value="male">Male</option>
									<option value="female">Female</option>
								</select>
							</label>

						</li>
						<li class="current_weight"><span>What is your current weight</span>
							<input name="weight" id="weight" type="text" />&nbsp;Kgs.</li>
						</ul>
					</div>
					<div class="melPlnrFrmRght">
						<ul>
							<li>Will you be exercising to speed up your results?<br>
								<input type="radio" id="r1" name="exercising_to_speed_up" value="yes" checked="checked" onclick="decide_training_part(1);" />
								<label for="r1"><span></span>Yes</label>
								<input type="radio" id="r2"  name="exercising_to_speed_up" value="no" onclick="decide_training_part(0);"   />
								<label for="r2"><span></span>No</label>
							</li>
							<li id="div_part_of_the_day_usually_train">What part of the day would you usually train?<br>
								<input type="radio" id="r3" name="part_of_the_day_usually_train" value="morning" checked="checked" />
								<label for="r3"><span></span>Morning</label>
								<input type="radio" id="r4" name="part_of_the_day_usually_train" value="lunch_time" />
								<label for="r4"><span></span>Lunch Time</label>
								<input type="radio" id="r5" name="part_of_the_day_usually_train" value="after_work" />
								<label for="r5"><span></span>After Work</label>
								<input type="radio" id="r6" name="part_of_the_day_usually_train" value="evening" />
								<label for="r6"><span></span>Evening</label>
							</li>
							<li>How many days per week would you like to eat the fit n food meals?<br>
								<input type="radio" id="r7" name="like_to_eat_how_many_days" value="5"  />
								<label for="r7"><span></span>5 days</label>
								<input type="radio" id="r8" name="like_to_eat_how_many_days" value="7" checked="checked" />
								<label for="r8"><span></span>7 days</label>
							</li>
						</ul>
					</div>
					<br class="clear">
					<div class="melPlnBtn">
						<input name="submit" type="submit" value="Get your meal plan" />
					</div>
				</form>
			</div>
		</div>
	</div>
	<?php
	$result_testimonials=$db->fetch_all_array("select name,details,embedded_video_link from testimonials order by RAND() limit 3");
	$total_testimonials=count($result_testimonials);

	if($total_testimonials > 0){	
		?>
		<div class="hmPanelThree">
			<div class="mainDiv2">
				<h2>Successful results from our members</h2>
				<div class="tabSldr2">
					<?php 
					for($i=0; $i < $total_testimonials; $i++){
						if(trim($result_testimonials[$i]['embedded_video_link']) != NULL){ ?>
						<div class="tabSldr2Vdo" id="vdo<?=$i?>"><iframe width="475" height="372" src="http://www.youtube.com/embed/<?=str_replace("http://youtu.be/","", trim($result_testimonials[$i]['embedded_video_link'])); ?>" frameborder="0" allowfullscreen></iframe></div>	
						<?php }	

					} ?>	

					<div class="tabSldr2Ldt">
						<ul id="flowertabs">        	
							<?php
							reset($result_testimonials);

							for($i=0; $i < $total_testimonials; $i++){?>
							<li><a href="#url" rel="vdo<?=$i?>"><img src="http://img.youtube.com/vi/<?=str_replace("http://youtu.be/","", trim($result_testimonials[$i]['embedded_video_link'])) ?>/default.jpg"  alt="" /><?php echo nl2br($result_testimonials[$i]['details']) ?><br>
								<span>-- <?php echo $result_testimonials[$i]['name'] ?></span></a></li>	
								<?php  } ?>	
							</ul>
						</div>
						<script type="text/javascript">
							var myflowers=new ddtabcontent("flowertabs");
							myflowers.setpersist(true);
							myflowers.setselectedClassTarget("link");
							myflowers.init();
						</script> 
					</div>
					<br class="clear">
					<div class="morTst"><input name="" type="submit" value="View More Results" onclick="location.href='<?=$general_func->site_url?>testimonials/'" /></div>
					<br class="clear">
					<div class="tabBotCont"><?php echo trim($dynamic_content['file_data']); ?></div>
				</div>
			</div>
			<?php
		}
		include_once("includes/footer.php");
		?>
