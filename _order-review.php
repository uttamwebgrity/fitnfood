<?php
include_once ("includes/header.php");

$data=array();

//print  count($_SESSION['choose_your_meal_plan']);

//*** If nothing is set, send to get started page **********************//
if (!isset($_SESSION['fill_the_questionnaire']) && !isset($_SESSION['choose_your_meal_plan']) && !isset($_SESSION['customize_your_meal_plan']))
	$general_func -> header_redirect($general_func -> site_url . "get-started/");


//********************  send to payment page *********************************//

if(isset($_POST['enter']) && trim($_POST['enter']) == "oder_your_meal_plan" && trim($_POST['before_login_form_id'])==$_SESSION['before_login_form_id']){

	$address=filter_var(trim($_POST['address']), FILTER_SANITIZE_STRING);
	
	$phone=trim($_POST['phone']);
	$program_length=intval($_POST['program_length']);
	$delivery_notes=trim($_POST['delivery_notes']);	
	$order_start_date=intval($_POST['order_start_date']);
	
	if(isset($_POST['suburb_id']) && trim($_POST['suburb_id']) != NULL){
		$suburbs=filter_var(trim($_POST['suburb_id']), FILTER_SANITIZE_STRING);		
		$queryString_array=array();	
		$queryString_array=explode(",",trim($suburbs));
		$array_size=sizeof($queryString_array);
				
		$result_suburb_info=$db->fetch_all_array("select id from suburb where suburb_name ='" . trim($queryString_array[0]) . "' and suburb_state = '" . trim($queryString_array[1]) . "' and suburb_postcode  ='" . trim($queryString_array[2]) . "' limit 1");
	}

	if(!isset($_SESSION['user_id'])){//*************  Register member
 		$fname=filter_var(trim($_POST['fname']), FILTER_SANITIZE_STRING);	 
		$lname=filter_var(trim($_POST['lname']), FILTER_SANITIZE_STRING);
		$email_address=filter_var(trim($_POST['email_address']), FILTER_SANITIZE_EMAIL);
		$password=trim($_POST['password']);	
		$refered_code=filter_var(trim($_POST['refered_code']), FILTER_SANITIZE_STRING);
		
		 
		if($security_validator->validate($email_address,'email') == false){		
				$_SESSION['user_message']="Please enter a valid email address";	
				$general_func->header_redirect($general_func->site_url."order-review/");		
		}else{	
			if($db->already_exist_inset("users","email_address",$email_address)){
				$_SESSION['user_message']="Sorry, your specified email address is already taken!";
				$general_func->header_redirect($general_func->site_url."order-review/");			
			}else{				
				$data['fname']=$fname;	
				$data['lname']=$lname;			
				$data['seo_link']=$general_func->create_seo_link($fname." ".$lname);		
					
				if($db->already_exist_inset("users","seo_link",$data['seo_link'])){//******* exit
					$data['seo_link']=$db->max_id("users","id") + 1 ."-".$data['seo_link'];
				}
												
				$data['email_address']=$email_address;
				$data['password']=$EncDec->encrypt_me($password);				
				$data['street_address']=$address;
				
				$data['phone']=$phone;		
				$data['suburb_id']=$result_suburb_info[0]['id'];				
				$data['refered_code']=$refered_code; 	
				$data['last_login_date']=$current_date_time; 			
							
				$data['status']=1;
				$data['date_added']=$current_date_time;
				$inserted_id=$db->query_insert("users",$data);
				
				$_SESSION['user_message']="Thank you for your registration with us your account has been created,<br/>
					 your login information has been sent to your specified email address.";
					 
					 
				$email_template=$db->fetch_all_array("select template_subject,template_content from email_template where id=1 limit 1");
					 
					 
				$email_content=$email_template[0]['template_content'];
				$email_content = str_replace("#name#", $fname.' '.$lname, $email_content);
				$email_content = str_replace("#email_address#", $email_address, $email_content);
				$email_content = str_replace("#password#", $password , $email_content);
					
				
					
				//*******************  send email to member *******************//
				$recipient_info=array();
				$recipient_info['recipient_subject']=$email_template[0]['template_subject'];
				$recipient_info['recipient_content']=$email_content;
				$recipient_info['recipient_email']=$email_address;		
				$sendmail -> register_welcome_to_user($recipient_info, $general_func->email,$general_func->site_title, $general_func->site_url);				
				//***************************************************************//	 
				$_SESSION['user_id']=$inserted_id;
				$_SESSION['user_fname']=$fname;
				$_SESSION['user_lname']=$lname;
				$_SESSION['user_email_address']=$email_address;
				$_SESSION['user_seo_link']=$data['seo_link'];	
				$_SESSION['user_login_type']= "users"; 	
				$_SESSION['user_login']= "yes";
				$_SESSION['after_login_form_id']=$general_func->genTicketString(10);
			}		
		}	
	}else if(isset($_POST['suburb_id']) && trim($_POST['suburb_id']) != NULL){
		$data['street_address']=$address;		
		$data['phone']=$phone;	
		$data['suburb_id']=$result_suburb_info[0]['id'];
		$data['date_modified']=$current_date_time;				
		$db->query_update("users",$data,"id='".$_SESSION['user_id'] ."'");
	}
	//*****************  Check whether user has any existing order in the system? *********************//	
	if($db_common -> user_has_an_active_order(intval($_SESSION['user_id']),2) > 0){		
		if (isset($_SESSION['fill_the_questionnaire']))
			unset($_SESSION['fill_the_questionnaire']);	
				
		if (isset($_SESSION['choose_your_meal_plan']))
			unset($_SESSION['choose_your_meal_plan']);	
		
		if (isset($_SESSION['customize_your_meal_plan']))
			unset($_SESSION['customize_your_meal_plan']);	
					
		if (isset($_SESSION['payment']))
			unset($_SESSION['payment']);		
		
		$_SESSION['user_message']="Sorry, you can not make another order with us. <br/> If you want to update your existing order then you can make it from your order listing page.";
				
		$general_func->header_redirect($general_func->site_url."order-listing/");
	}
	//**************************************************************************************************// 

	$_SESSION['payment']=array();
	$_SESSION['payment']['meal_plan_price']=trim($_POST['total_price']);
	$_SESSION['payment']['program_length']=$program_length;
	$_SESSION['payment']['delivery_notes']=$delivery_notes;
	$_SESSION['payment']['order_start_date']=$order_start_date;	
	
	$general_func->header_redirect($general_func->site_url."payment/");
	
}
//***************************************************************************//

if (isset($_SESSION['user_id']) && intval($_SESSION['user_id']) > 0) {
	//*** Gather user information *********************************//
	$sql_user = "select * from users where id=" . intval($_SESSION['user_id']) . " limit 1";
	$result_user = $db -> fetch_all_array($sql_user);

	//*** Gather user suburb information  **************************//
	if(intval($result_user[0]['suburb_id']) > 0)	
		$result_suburb_info = $db -> fetch_all_array("select suburb_postcode,delivery_day,payment_debit_day,order_cutoff_day,order_cutoff_time from suburb where id=" . intval($result_user[0]['suburb_id']) . " limit 1");
}


$_SESSION['return_to_front_end']=$general_func -> site_url . "order-review/";

$total_price = 0;
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

	$(document).ready(function() {
		$(".dayPnl1_new li").mouseenter(function() {
			$(this).find(".tip_box").show();
		});

		$(".dayPnl1_new li").mouseleave(function() {
			$(this).find(".tip_box").hide();
		});

		$(".close_pop").click(function() {
			$(this).parent().parent().find(".tip_box").hide();
		});
	});

	function discounted_price(discount_id) {
		//var actual_price = $("#actual_price").text();
		if(parseInt(discount_id) > 0) {
			$.get("discount.php?id=" + discount_id + "&query_type=1", function(data) {
				//var return_data = data.split("~_~");

				/*if(actual_price == return_data[0]) {
					$("#discounted_price").html('Total weekly price : <span class="present_price"> $' + parseFloat(actual_price).toFixed(2) + '  </span>');
					$("#total_price").val(actual_price);
				} else {
					$("#discounted_price").html('Total weekly price : <span class="old_price">$' + parseFloat(actual_price).toFixed(2) + '</span><span class="present_price">$' + parseFloat(return_data[0]).toFixed(2) + '</span>');
					$("#total_price").val(return_data[0]);
				} */
				$("#qoute").html(data);
				$("#qoute").show(1000);
			});
		} else {
			//$("#discounted_price").html('Total weekly price1 : <span class="present_price"> $' + parseFloat(actual_price).toFixed(2) + '  </span>');
			//$("#total_price").val(actual_price);
			$("#qoute").html("");
			$("#qoute").hide(1000);
		}
	}

	function postcode_check(val) {
		if(val == "") {
			$("#validate_phone").show();
			document.getElementById("validate_phone").innerHTML = "Please enter an australian postcode.";
		} else {
			$("#validate_phone").hide();
			$.get("get-zipcode-info.php?zipcode=" + val, function(data) {
				if(parseInt(data) == 0) {
					$("#validate_phone").show();
					document.getElementById("validate_phone").innerHTML = "Please enter a valid australian postcode.";
				}
			});
		}
	}
	
	function email_check(val) {
		if(val == "") {
			$("#validate_email").show();
			document.getElementById("validate_email").innerHTML = "Please enter your email address.";
		} else {
			$("#validate_email").hide();
			$.get("check-email-duplicate.php?email=" + val, function(data) {
				if(parseInt(data) == 1) {
					$("#validate_email").show();
					document.getElementById("validate_email").innerHTML = "Sorry, your specified email address is already taken!";
				}
			});
		}
	}

	function lookup(suburb_id) {				
		if(suburb_id.length == 0) {
			// Hide the suggestion box.
			$('#suggestions').hide();
		} else {
			$.post("suburb-filter.php", {queryString: ""+suburb_id+""}, function(data){
				if(data.length >0) {
					$('#suggestions').show();
					$('#autoSuggestionsList').html(data);
				}
			});
		}
	} // lookup
	
	function fill(thisValue) {				
		$('#suburb_id').val(thisValue);
		setTimeout("$('#suggestions').hide();", 200);
		suburbs_delivery(thisValue);
		
	}



	function suburbs_delivery(val) {		
		if(val == "") {
			document.getElementById('order_start_date').options.length = 0;
			document.getElementById("order_start_date").options[0] = new Option("Select Start Date", "");
			$("#delivery_day").val("");
			$("#payment_debit_day").val("");
			$("#order_cutoff_day").val("");
			$("#post_code").val("");
		} else {			
			$.get("suburbs-delivery-info.php?suburbs=" + val, function(data) {
				var return_data = data.split("#!");
				var start_dates = return_data[0].split("~_~");
				var delivery_dates = return_data[1].split("~_~");

				document.getElementById("order_start_date").options[1] = new Option(start_dates[1], start_dates[0]);
				document.getElementById("order_start_date").options[2] = new Option(start_dates[3], start_dates[2]);
				document.getElementById("order_start_date").options[3] = new Option(start_dates[5], start_dates[4]);
				document.getElementById("order_start_date").options[4] = new Option(start_dates[7], start_dates[6]);

				$("#delivery_day").val(delivery_dates[0]);
				$("#payment_debit_day").val(delivery_dates[1]);
				$("#order_cutoff_day").val(delivery_dates[2]);
				$("#post_code").val(delivery_dates[3]);
			});
		}
	}

	function validate_order_your_meal_plan() {
		var error = 0;
				
		if(document.getElementById("program_length").value == '') {
			document.getElementById("program_length").style.border = "1px solid red";
			error++;
		} else {
			document.getElementById("program_length").style.border = "1px solid #DADCDD";
		}
				
		<?php if (!isset($_SESSION['user_id'])) {?>
			
			if(document.getElementById("ofname").value == '') {
				document.getElementById("ofname").style.border = "1px solid red";
				error++;
			} else {
				document.getElementById("ofname").style.border = "1px solid #DADCDD";
			}
			
			if(document.getElementById("olname").value == '') {
				document.getElementById("olname").style.border = "1px solid red";
				error++;
			} else {
				document.getElementById("olname").style.border = "1px solid #DADCDD";
			}	
			
			if(document.getElementById("oemail_address").value == '') {
				document.getElementById("oemail_address").style.border = "1px solid red";
				error++;
			} else {
				document.getElementById("oemail_address").style.border = "1px solid #DADCDD";
			}	
			
			if(document.getElementById("opassword").value == '') {
				document.getElementById("opassword").style.border = "1px solid red";
				error++;
			} else {
				document.getElementById("opassword").style.border = "1px solid #DADCDD";
			}	
			
			if(document.getElementById("ocpassword").value == '') {
				document.getElementById("ocpassword").style.border = "1px solid red";
				error++;
			} else {
				document.getElementById("ocpassword").style.border = "1px solid #DADCDD";
			}	
			
			
			if(document.getElementById("opassword").value != document.getElementById("ocpassword").value) {				
				$("#opassword_cpassword_msg").show();
				document.getElementById("opassword_cpassword_msg").innerHTML = "Password and confirm password must be same.";
				document.getElementById("opassword").focus();
				error++;
			} else {
				document.getElementById("opassword_cpassword_msg").innerHTML = "";
				$("#opassword_cpassword_msg").hide();
			}
			
			
		<?php } ?>
		
		
		if(document.getElementById("address").value == '') {
			document.getElementById("address").style.border = "1px solid red";
			error++;
		} else {
			document.getElementById("address").style.border = "1px solid #DADCDD";
		}
		
		if(document.getElementById("phone").value == '') {
			document.getElementById("phone").style.border = "1px solid red";
			error++;
		} else {
			document.getElementById("phone").style.border = "1px solid #DADCDD";
		}
		
		<?php if(!isset($_SESSION['user_id']) || intval($result_user[0]['suburb_id']) == 0){?>
		
		if(document.getElementById("suburb_id").value == '') {
			document.getElementById("suburb_id").style.border = "1px solid red";
			error++;
		} else {
			document.getElementById("suburb_id").style.border = "1px solid #DADCDD";
		}
			
		if(!validate_integer_without_msg(document.getElementById("post_code"))) {
			document.getElementById("post_code").style.border = "1px solid red";
			error++;
		} else {
			document.getElementById("post_code").style.border = "1px solid #CBD2BB";
		}
     			
		<?php } ?>	
			
		if(document.frm_order_meal_plan.order_start_date.selectedIndex == 0) {
			document.getElementById("order_start_date").style.border = "1px solid red";
			error++;
		} else {
			document.getElementById("order_start_date").style.border = "1px solid #CBD2BB";
		}

		
		if(error > 0)
			return false;
		else
			return true;
	}
</script>

<div class="inrBnr">
	<?php $db_common -> static_page_banner($dynamic_content['page_id']); ?>
</div>
<div class="bodyContent">	
	<div class="mainDiv2">
		<h3>Review Your Order</h3>
		<div class="mealPln for_review_order">
			<div class="mainDiv2">
				<form name="frm_order_meal_plan" method="post" action="order-review/" onsubmit="return validate_order_your_meal_plan();">
					<input type="hidden" name="enter" value="oder_your_meal_plan" />
					<input type="hidden" name="before_login_form_id" value="<?=$_SESSION['before_login_form_id'] ?>" />	
					<div class="mealPlnColTwo">
						<div class="sedulePnl">			
							<?php
					//*************  Fill the questionnaire *******************************//
							if(isset($_SESSION['fill_the_questionnaire']) && is_array($_SESSION['fill_the_questionnaire']) && count($_SESSION['fill_the_questionnaire']) > 4 ){
									
									//print_r ($_SESSION['fill_the_questionnaire']);
									
										
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

								for($day=1; $day <=$_SESSION['fill_the_questionnaire']['like_to_eat_how_many_days']; $day++){ ?>
								<div class="dayPnl">
									<div class="dayPnl2 daynumber">
										<h5>Day <?=$day ?></h5>
									</div>
									<br class="clear">
									<div class="dayPnlTgl mealplan">
										<div class="dayPnl1 dayPnl1_new">
											<ul>
												<?php  for($time=1; $time <=$_SESSION['fill_the_questionnaire']['meals_per_day']; $time++ ){
													?>
													<li>
														<span>Meal <?=$time; ?> :</span><span><?=$default_meals[$day][$time]['meal_name'] ?></span>
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
																				Category :
																			</div>
																			<div class="info_tab">
																				<?=$default_meals[$day][$time]['meal_category_name'] ?>
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
																	<?php $total_price += $default_meals[$day][$time]['price'];
																		}
																		for($time=1; $time <=$_SESSION['fill_the_questionnaire']['snacks_per_day']; $time++ ){
																	?>
																	<li>
																		<span>Snack <?=$time; ?> :</span><span><?=$default_snacks[$day][$time]['name'] ?></span>
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
																							<div class="info_tab">Price :</div>
																							<div class="info_tab">$<?=$default_snacks[$day][$time]['price'] ?></div>
																						</div>
																					</div>	
																					<div class="tip_column">																
																						<div class="tip_column_info_row">
																							<div class="info_tab">Qty :</div>
																							<div class="info_tab"><?=$default_snacks[$day][$time]['qty'] ?></div>
																						</div>
																					</div>															
																				</div>
																			</div> 
																			<div class="tip_row">
																				<div class="tip_descrip">
																					<p>
																						<?php if(trim($default_snacks[$day][$time]['photo_name']) != NULL){ ?>
																						<img class="lefted_img" align="left" src="snack_main/small/<?=trim($default_snacks[$day][$time]['photo_name']) ?>" width="120">						                            	
																						<?php } ?>	
																						<?=nl2br($default_snacks[$day][$time]['details']) ?></p>
																					</div>
																				</div>
																			</div>
																		</li>
																		<?php $total_price += $default_snacks[$day][$time]['price'] * $default_snacks[$day][$time]['qty'];
																			}
																	?>
																</ul>
															</div>
														</div>
													</div>
													<?php } ?>

						<!--<div class="dayPnl extra_accor">
						<div class="dayPnl2 daynumber"><h5>Extra</h5></div>
						<br class="clear">
						<div class="dayPnlTgl mealplan">
						<div class="dayPnl1 dayPnl1_new">
						<ul>
						<li><span>Breakfast :</span><span>Bacon, Kale 150gms.</span></li>
						<li><span>Lunch :</span><span>Lamb Bolognese 150gms</span></li>
						<li><span>Dinner :</span><span>Salmon Fillet Fried Rice 150gms</span></li>
						</ul>
						</div>
						</div>
					</div>-->
					<?php }else if(isset($_SESSION['choose_your_meal_plan']) && is_array($_SESSION['choose_your_meal_plan']) && count($_SESSION['choose_your_meal_plan']) > 4){
							//print_r ($_SESSION['choose_your_meal_plan']);


						//******************* Choose your meal plan ****************************************//
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
						}

						for($day=1; $day <= $_SESSION['choose_your_meal_plan']['no_of_days']; $day++){
 ?>	
						<div class="dayPnl">
							<div class="dayPnl2 daynumber"><h5>Day <?=$day ?></h5></div>
							<br class="clear">
							<div class="dayPnlTgl mealplan">
								<div class="dayPnl1 dayPnl1_new">
									<ul>
										<?php  for($time=1; $time <= $_SESSION['choose_your_meal_plan']['meal_per_day']; $time++ ){?>
										<li>
											<span>Meal <?=$time ?> :</span><span><?=$default_meals[$day][$time]['meal_name'] ?></span>
											<div class="tip_box" style="z-index: 99999;" >
												<div class="close_pop"></div>
												<div class="tip_angle"></div>						                     
												<div class="tip_head"><?=$default_meals[$day][$time]['meal_name'] ?></div>						                      
												<div class="tip_row">
													<div class="tip_column_container">
														<div class="tip_column">
															<?php if($default_meals[$day][$time]['show_nutritional_price'] == 1){ ?>
															<div class="tip_column_info_row">
																<div class="info_tab">Net Weight :</div>
																<div class="info_tab"><?=$default_meals[$day][$time]['meal_size']; ?>g</div>
															</div>
															<div class="tip_column_info_row">
																<div class="info_tab">Energy :</div>
																<div class="info_tab"><?=$default_meals[$day][$time]['energy'] ?> kcal</div>
															</div>
															<div class="tip_column_info_row">
																<div class="info_tab">Protein :</div>
																<div class="info_tab"><?=$default_meals[$day][$time]['protein'] ?> g</div>
															</div>
															<?php } ?>
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
															<?php if($default_meals[$day][$time]['show_nutritional_price'] == 1){ ?>
															<div class="tip_column_info_row">
																<div class="info_tab">Carbs :</div>
																<div class="info_tab"><?=$default_meals[$day][$time]['carbohydrates'] ?>g</div>
															</div>
															<div class="tip_column_info_row">
																<div class="info_tab">Total Fat :</div>
																<div class="info_tab"><?=$default_meals[$day][$time]['fat_total'] ?>g</div>
															</div>
															<div class="tip_column_info_row">
																<div class="info_tab">Price :</div>
																<div class="info_tab">$<?=$default_meals[$day][$time]['price'] ?></div>
															</div>
															<?php } ?>
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
											<?php
												$total_price += $default_meals[$day][$time]['price'];

												}

												for($time=1; $time <= $_SESSION['choose_your_meal_plan']['snack_per_day']; $time++ ){
											?>
										<li>
											<span>Snack <?=$time ?> :</span><span><?=$default_snacks[$day][$time]['name'] ?></span>
											<div class="tip_box" style="z-index: 99999;" >
												<div class="close_pop"></div>
												<div class="tip_angle"></div>						                     
												<div class="tip_head"><?=$default_snacks[$day][$time]['name'] ?></div>						                      
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
											<?php
												$total_price += $default_snacks[$day][$time]['price'] * $default_snacks[$day][$time]['qty'];

												}
 ?>
									</ul>
								</div>
							</div>
						</div>	
						<?php }
							}else if(isset($_SESSION['customize_your_meal_plan']) && is_array($_SESSION['customize_your_meal_plan']) && count($_SESSION['customize_your_meal_plan']) > 3){
							
							//echo count($_SESSION['customize_your_meal_plan']);
						//print_r ($_SESSION['customize_your_meal_plan']);
							for($day=1; $day <= $_SESSION['customize_your_meal_plan']['no_of_days']; $day++){
 ?>
						<div class="dayPnl">
							<div class="dayPnl2 daynumber"><h5>Day <?=$day ?></h5></div>
							<br class="clear">
							<div class="dayPnlTgl mealplan">
								<div class="dayPnl1 dayPnl1_new">
									<ul>
										<?php  for($time=1; $time <= $_SESSION['customize_your_meal_plan']['meal_per_day']; $time++ ){
											$sql_meals = "select id,show_nutritional_price,(select meal_price from meals_sizes_prices where meal_id='" . $_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_id'] . "' and meal_size='" . $_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_size'] . "') as price,name,details,photo_name,energy,calories,protein,fat_total,carbohydrates,carbs_veggies,with_or_without_sauce from  meals where id='" . intval($_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_id']) . "' limit 1";
											$result_default_meals = $db -> fetch_all_array($sql_meals);
											?>
											<li>
												<span>Meal <?=$time ?> :</span><span><?=$result_default_meals[0]['name'] ?></span>
												<div class="tip_box" style="z-index: 99999;" >
													<div class="close_pop"></div>
													<div class="tip_angle"></div>						                     
													<div class="tip_head"><?=$result_default_meals[0]['name'] ?></div>						                      
													<div class="tip_row">
														<div class="tip_column_container">
															<div class="tip_column">
																<?php if(intval($result_default_meals[0]['show_nutritional_price']) == 1){ ?>
																<div class="tip_column_info_row">
																	<div class="info_tab">Net Weight :</div>
																	<div class="info_tab"><?=$_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_size']; ?>g</div>
																</div>
																<div class="tip_column_info_row">
																	<div class="info_tab">Energy :</div>
																	<div class="info_tab"><?=$result_default_meals[0]['energy'] ?> kcal</div>
																</div>
																<div class="tip_column_info_row">
																	<div class="info_tab">Protein :</div>
																	<div class="info_tab"><?=$result_default_meals[0]['protein'] ?> g</div>
																</div>	

																<?php  } ?>														
																
																<div class="tip_column_info_row">
																	<div class="info_tab">Carbs :</div>
																	<div class="info_tab">
																		<?php if(intval($result_default_meals[0]['carbs_veggies']) == 3 || intval($result_default_meals[0]['carbs_veggies']) == 1){ ?>
																		<img src="images/tip_yes.png" style="margin-top: 4px;" />	
																		<?php }else{ ?>
																		<img src="images/tip_no.png" style="margin-top: 4px;" />
																		<?php } ?>
																	</div>
																</div>
																<div class="tip_column_info_row">
																	<div class="info_tab">Sauce :</div>
																	<div class="info_tab">
																		<?php if(intval($result_default_meals[0]['with_or_without_sauce']) == 1){ ?>
																		<img src="images/tip_yes.png" style="margin-top: 4px;" />	
																		<?php }else{ ?>
																		<img src="images/tip_no.png" style="margin-top: 4px;" />
																		<?php } ?>
																	</div>
																</div>
															</div>
															<div class="tip_column">
																<?php if(intval($result_default_meals[0]['show_nutritional_price']) == 1){ ?>
																<div class="tip_column_info_row">
																	<div class="info_tab">Carbs :</div>
																	<div class="info_tab"><?=$result_default_meals[0]['carbohydrates'] ?>g</div>
																</div>
																<div class="tip_column_info_row">
																	<div class="info_tab">Total Fat :</div>
																	<div class="info_tab"><?=$result_default_meals[0]['fat_total'] ?>g</div>
																</div>
																<div class="tip_column_info_row">
																	<div class="info_tab">Price :</div>
																	<div class="info_tab">$<?=$result_default_meals[0]['price'] ?></div>
																</div>
																<?php } ?>
																<div class="tip_column_info_row">
																	<div class="info_tab">Veggies :</div>
																	<div class="info_tab">
																		<?php if(intval($result_default_meals[0]['carbs_veggies']) == 3 || intval($result_default_meals[0]['carbs_veggies']) == 2){ ?>
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
																<?php if(trim($result_default_meals[0]['photo_name']) != NULL){ ?>
																<img class="lefted_img" align="left" src="meal_main/small/<?=trim($result_default_meals[0]['photo_name']) ?>" width="120">						                            	
																<?php } ?>	

																<?=nl2br($result_default_meals[0]['details']) ?></p>
															</div>
														</div>
													</div>			                        		
												</li>
												<?php
													$total_price += $result_default_meals[0]['price'];

													}
 ?>
											
											
											<?php  for($time=1; $time <= $_SESSION['customize_your_meal_plan']['snack_per_day']; $time++ ){
												$sql_snacks = "select name,details,photo_name,price from  snacks where id='" . intval($_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_id']) . "' limit 1";
												$result_snacks = $db -> fetch_all_array($sql_snacks);
												?>
												<li>
													<span>Snack <?=$time ?> :</span><span><?=$result_snacks[0]['name'] ?></span>
													<div class="tip_box" style="z-index: 99999;" >
														<div class="close_pop"></div>
														<div class="tip_angle"></div>						                     
														<div class="tip_head"><?=$result_snacks[0]['name'] ?></div>						                      
														<div class="tip_row">
															<div class="tip_column_container">
																<div class="tip_column">															
																	<div class="tip_column_info_row">
																		<div class="info_tab">Price :</div>
																		<div class="info_tab">$<?=$result_snacks[0]['price'] ?></div>
																	</div>
																</div>	
																<div class="tip_column">																
																	<div class="tip_column_info_row">
																		<div class="info_tab">Qty :</div>
																		<div class="info_tab"><?=intval($_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_qty']) ?></div>
																	</div>
																</div>															
															</div>
														</div>
														<div class="tip_row">
															<div class="tip_descrip">
																<p>
																	<?php if(trim($result_snacks[0]['photo_name']) != NULL){ ?>
																	<img class="lefted_img" align="left" src="snack_main/small/<?=trim($result_snacks[0]['photo_name']) ?>" width="120">						                            	
																	<?php } ?>	
																	<?=nl2br($result_snacks[0]['details']) ?></p>
																</div>
															</div>
														</div>			                        		
													</li>
													<?php
														$total_price += $result_snacks[0]['price'] * intval($_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_qty']);

														}
 ?>


											</ul>
										</div>
									</div>
								</div>						

								<?php }

	}else{
	//*** Nothing is set, send to get started page **********************//
	$general_func->header_redirect($general_func->site_url . "get-started/");
	}

	if($general_func->meal_plan_amout_for_training_cost > 0)
	$total_price +=$general_func->meal_plan_amout_for_training_cost;
							?>						
						</div>
						<div class="new_content_row_container">
							<ul class="choseDlvryPikup">
                            	<li><input type="radio" checked="checked" value="yes" name="Delivery" id="r1">
                                <label for="r1"><span></span>Delivery</label></li>
                                <li><input type="radio" value="no" name="Delivery" id="r2">
                                <label for="r2"><span></span>Pickup</label></li>
                            </ul>
                            <div class="mealPlnColOne normal_select">
                            <p style="padding-right: 10px; text-align:left;"><strong>Pickup Location</strong></p>
									<label class="custom-select">
										<select name="pickup_location" id="pickup_location" class="selStlTwo">
											<option value="">Choose One</option>
										</select></label>
                            <br class="clear" />
                            <p style="padding-right: 10px; text-align:left;"><strong>Address:</strong><br />19 Lake temple Road. Kolkata 700008.</p>
                            <p style="padding-right: 10px; text-align:left;"><strong>Pickup Timing:</strong><br />6pm - 7pm<br />10pm - 10:30pm<br />12am - 5am</p>
                            </div>
                            
                            
                            
                            <div class="new_content_row">
								<div class="mealPlnColOne normal_select">
									<p style="padding-right: 10px;"><strong>Program Length</strong></p>
									<label class="custom-select">
										<select name="program_length" id="program_length" class="selStlTwo" onchange="discounted_price(this.value);">
											<option value="">Choose One</option>
											<?php
											$sql_discount = "select id,name from discounts where status=1 ORDER BY CAST(`name` AS SIGNED) ASC";
											$result_discount = $db -> fetch_all_array($sql_discount);
											$total_discount=count($result_discount);

											for($d=0; $d < $total_discount; $d++){?>
											<option value="<?=$result_discount[$d]['id']?>" <?=(isset($_SESSION['payment']['program_length']) && $_SESSION['payment']['program_length']==$result_discount[$d]['id'])?'selected="selected"':'';?>><?=$result_discount[$d]['name'] ?></option>

											<?php } ?>						        	
										</select></label>
									</div>								
									<p class="qoute" id="qoute" style="display: none;"></p>
									<label id="actual_price" style="display: none; height: 0px;"><?=$total_price ?></label>
									<h2 id="discounted_price">Total weekly price : <span class="present_price">$<?=number_format($total_price, 2) ?> </span>(GST <?=$GST?>% included)</h2>
									<input type="hidden" name="total_price" id="total_price" value="<?=$total_price ?>" />

								</div>
								<div class="new_content_row">
									<h2>Additional Delivery Notes</h2>
									<div class="form_row">
										<textarea name="delivery_notes"><?=$_SESSION['payment']['delivery_notes']?></textarea>
									</div>
								</div>
							</div>
						</div>
						<!-- order rvw form -->
						<div class="order_rvw_form">
							<h3>Customer Information</h3>
							<div class="update_profile_form">
								<?php  if(!isset($_SESSION['user_id'])){ ?>
								
								<div class="form_row">
									<div class="form_section">
										<label>First Name</label>
										<input type="text" name="fname" id="ofname" value="" />
									</div>
									<div class="form_section">
										<label>Last Name</label>
										<input type="text" name="lname" id="olname"  value="" />
									</div>
								</div>
								<div class="form_row">
									<div class="form_section">
										<label>Email</label>
										<input type="email" name="email_address" id="oemail_address"  value="" onblur="email_check(this.value);"  />
									</div>
									<div class="form_section">
										<label>Referrer ID (if applicable)</label>
										<input type="text" name="refered_code" value="" />
									</div>
								</div>
								<div class="form_row" style="padding: 0px;">
									<div class="alert_message1" id="validate_email" style="display: none; float: left;"></div>
								</div>	
								<div class="form_row">
									<div class="form_section">
										<label>Password</label>
										<input type="password" name="password" id="opassword"  value="" />
									</div>
									<div class="form_section">
										<label>Confirm Password</label>
										<input type="password" name="cpassword" id="ocpassword"  value="" />
									</div>
								</div>
								
								<div class="form_row" style="padding: 0px;">
									<div class="alert_message1" id="opassword_cpassword_msg" style="display: none; float: left;"></div>
								</div>									
								
								<div class="form_row">									
									<p class="form-note">Already a Member? <a style="cursor: pointer;" class="show_hide" data-reveal-id="popup1">Please Sign In</a></p>
									
									<!-- <div class="form_section">
										<input type="submit" value="Sign In" />
									</div> -->
								</div>
								<?php } ?>
								<div class="form_row">
									<div class="form_section">
                                    <label>Mobile</label>
									<input name="phone" id="phone"  class="inptFldOne" value="<?=$result_user[0]['phone'] ?>" />
                                    </div>
								</div>								
								
								
								<div class="form_row">
									<div class="form_section">
                                    <label>Street Address</label>
									<input name="address" id="address"  class="inptFldOne" value="<?=$result_user[0]['street_address'] ?>" />
                                    </div>
								</div>									
								<?php if(!isset($_SESSION['user_id']) || intval($result_user[0]['suburb_id']) == 0){?>
								<div class="form_row">									
									<div class="form_section">
										<label>Suburb<strong>(Australia only)</strong></label>
										<input type="text" name="suburb_id" id="suburb_id" value="" autocomplete="off"  onkeyup="lookup(this.value);"  />
									</div>
									<div class="suggestionsBox"  id="suggestions" style="display: none;">
										<div class="suggestionList" id="autoSuggestionsList" style="width:200px; padding:5px;"></div>
									</div>									
									<div class="form_section">
										<label>Post Code</label>
										<input type="text" name="post_code" id="post_code" value="<?=$result_suburb_info[0]['suburb_postcode'] ?>" readonly="readonly" class="disabled"   />
									</div>
								</div>
								<?php } ?>
								<div class="form_row" style="padding: 0px;">
									<div class="alert_message1" id="validate_phone" style="display: none;"></div>
								</div>	
								<!-- row -->
								<!-- row -->
								<div class="form_row">
									<div class="form_section normal_select">
										<label>Start Date</label>
										<label class="custom-select">
											<select name="order_start_date" id="order_start_date" class="gap">>
												<option value="">Select Start Date</option>	
												<?php											
												
												if(isset($_SESSION['user_id']) && intval($result_user[0]['suburb_id']) > 0){
												
												//*** Decide current week or next week **************************//
												//$result_suburb_info[0]['order_cutoff_day']

												$day_of_the_week=date("w") == 0 ?7:date("w");

												if($day_of_the_week > $result_suburb_info[0]['order_cutoff_day'] ||  ($day_of_the_week == $result_suburb_info[0]['order_cutoff_day'] && strtotime(date("H:i:s")) > strtotime($result_suburb_info[0]['order_cutoff_time']))){
													//** Next week order *****//
													$one_week_time=86400 * 7;
													$first_start_date=strtotime('next '.$general_func->day_name($result_suburb_info[0]['delivery_day'])) + $one_week_time;

													$four_week_time=$one_week_time*4;
													$total_time=$first_start_date + $four_week_time;
													for($i=$first_start_date; $i < $total_time; $i +=$one_week_time ){ ?>
													<option value="<?=$i ?>" <?=(isset($_SESSION['payment']['order_start_date']) && $_SESSION['payment']['order_start_date']==$i)?'selected="selected"':'';?>><?=date("jS M. l, Y ", $i) ?></option>												
													<?php }

														}else{
														//** Current week order **//
														$first_start_date=strtotime('next '.$general_func->day_name($result_suburb_info[0]['delivery_day']));

														$one_week_time=86400 * 7;
														$four_week_time=$one_week_time*4;
														$total_time=$first_start_date + $four_week_time;

														for($i=$first_start_date; $i < $total_time; $i +=$one_week_time ){
 														?>
													<option value="<?=$i?>" <?=(isset($_SESSION['payment']['order_start_date']) && $_SESSION['payment']['order_start_date']==$i)?'selected="selected"':'';?>><?=date("jS M. l, Y ", $i) ?></option>												
													<?php }

														}
														//***************************************************************//
													}		
												?>
											</select> </label>
										</div>
										<div class="form_section">
											<label>Delivery Day</label>
											<input type="text" name="delivery_day" id="delivery_day" value="<?=(isset($_SESSION['user_id']) && intval($result_user[0]['suburb_id']) > 0)?$general_func -> day_name($result_suburb_info[0]['delivery_day']):'';?>" readonly="readonly" class="disabled" />
										</div>
									</div>
									<!-- row -->
									<!-- row -->
									<div class="form_row">
										<div class="form_section">
											<label>Payment Debit Day</label>
											<input type="text" name="payment_debit_day" id="payment_debit_day" value="<?=(isset($_SESSION['user_id']) && intval($result_user[0]['suburb_id']) > 0)?$general_func -> day_name($result_suburb_info[0]['payment_debit_day']):'';?>" readonly="readonly" class="disabled" />
										</div>
										<div class="form_section">
											<label>Order Cutoff Day</label>
											<input type="text" name="order_cutoff_day" id="order_cutoff_day" value="<?=(isset($_SESSION['user_id']) && intval($result_user[0]['suburb_id']) > 0)?$general_func -> day_name($result_suburb_info[0]['order_cutoff_day']) . " " . date("h:i A", strtotime($result_suburb_info[0]['order_cutoff_time'])):''; ?>" readonly="readonly" class="disabled" />
										</div>
									</div>
									<!-- row -->
								</div>
							</div>
							<!-- order rvw form -->
							<!-- checkout row -->
							<div class="checkout_row" style="border:none">
								<?php
								if (isset($_SESSION['fill_the_questionnaire']))
									$modify_order_page = $general_func -> site_url . "get-started/";
								else if (isset($_SESSION['choose_your_meal_plan']))
									$modify_order_page = $general_func -> site_url . "select-your-meal-plan/";
								else if (isset($_SESSION['customize_your_meal_plan']))
									$modify_order_page = $general_func -> site_url . "customize-your-own/";
								?>				

								<input type="button" value="Modify Order" class="checkout_left_but" onclick="location.href='<?=$modify_order_page ?>'" />
								<input type="submit" value="Checkout" class="checkout_right_but" />
							</div>
							<!-- checkout row -->
						</form>
					</div>
	  </div>
			</div>
		</div>
		<?php
		include_once ("includes/footer.php");
		?>