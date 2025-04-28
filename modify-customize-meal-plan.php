<?php
include_once ("includes/header.php");

$order_id=intval(mysql_real_escape_string($_REQUEST['order_id']));

if (!isset($_SESSION['user_login_type']) || trim($_SESSION['user_login_type']) != "users" || !isset($_SESSION['user_login_type'])) {
	$_SESSION['user_message'] = "Sorry, you have no permission to access this page!";
	$general_func -> header_redirect($general_func -> site_url);
}

if ($db_common -> user_has_an_order(intval($_SESSION['user_id'])) == 0) {
	$_SESSION['user_message'] = "Sorry, you have not made any order yet!";
	$general_func -> header_redirect($general_func -> site_url . "my-account/");
}

$meal_plan_category_id=$db_common->order_can_be_modified(intval($_SESSION['user_id']),$order_id);

if($meal_plan_category_id == 0){	
	$_SESSION['user_message'] = "Sorry, the order you are trying to modify is no longer exist!";
	$general_func -> header_redirect($general_func -> site_url . "my-account/");	
}



////***************  Customized your meal plan ***************************************//
//** Customized your meal plan and unset  fill the questionnaire and  select your meal plan **//
//***********************************************************************************************//
if (isset($_POST['enter']) && $_POST['enter'] == "customize_your_meal_plan_final" && trim($_POST['frm_customize_your_meal_plan']) == $_SESSION['frm_customize_your_meal_plan']) {
	
	if (isset($_SESSION['fill_the_questionnaire']))
		unset($_SESSION['fill_the_questionnaire']);

	if (isset($_SESSION['choose_your_meal_plan']))
		unset($_SESSION['choose_your_meal_plan']);
		
		
	/*unset($_SESSION['customize_your_meal_plan']);			
		
	$_SESSION['customize_your_meal_plan'] = array();*/
		
			
	$total_price=0;
		
	for ($day = 1; $day <= $_SESSION['customize_your_meal_plan']['no_of_days']; $day++) {
		for ($time = 1; $time <= $_SESSION['customize_your_meal_plan']['meal_per_day']; $time++) {
			$_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_id'] = intval($_REQUEST['meal_id_' . $day . '_' . $time]);
			$_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_size'] = intval($_REQUEST[$day . '_' . $time]);
		
			$sql_meals = "select id,name,(select meal_price from meals_sizes_prices where meal_id='" . $_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_id'] . "' and meal_size='" . $_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_size'] . "') as price from  meals where id='" . intval($_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_id']) . "' limit 1";
			$result_default_meals = $db -> fetch_all_array($sql_meals);
			
			$total_price += $result_default_meals[0]['price'];			
		
			$_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_name'] = $result_default_meals[0]['name'];
		}

		for ($time = 1; $time <= $_SESSION['customize_your_meal_plan']['snack_per_day']; $time++) {
			$_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_id'] = intval($_REQUEST['snack_id_' . $day . '_' . $time]);
			$_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_qty'] = intval($_REQUEST['snack_qty_' . $day . '_' . $time]);
			
			$sql_snacks = "select name,price from  snacks where id='" . intval($_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_id']) . "' limit 1";
			$result_snacks = $db -> fetch_all_array($sql_snacks);
			
			$total_price += ($result_snacks[0]['price'] * $_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_qty']);
			$_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_name'] = $result_snacks[0]['name']; 
		}
	}
	
	/*echo $total_price;
		echo "<br/>";*/

	if(floatval($general_func->meal_plan_amout_for_training_cost) > 0)
		$total_price += floatval($general_func->meal_plan_amout_for_training_cost);
	
	/*echo $total_price;
	exit;*/
	
	$sql_current_order = "select o.id,how_many_week_used,used_promo_code,promo_amount,promo_text,how_many_week,pickup_delivery,pickup_location_id,order_type,order_amount,order_start_date,name,o.status,suburb_id,notes,fname,lname,street_address,email_address,phone,program_length from orders o  left join meal_plan_category p on o.meal_plan_category_id=p.id";
	$sql_current_order .= " left join  users u on o.user_id=u.id";
	$sql_current_order .= " where user_id='" . intval($_SESSION['user_id']) . "' and current_order=1 limit 1";
	$result_current_order = $db -> fetch_all_array($sql_current_order);
		
	$result_suburb_info = $db -> fetch_all_array("select suburb_name,suburb_state,delivery_cost,suburb_postcode,delivery_day,payment_debit_day,order_cutoff_day,order_cutoff_time from suburb where id=" . intval($result_current_order[0]['suburb_id']) . " limit 1");
	$delivery_day = $general_func -> day_name($result_suburb_info[0]['delivery_day']);
	$payment_debit_day = $general_func -> day_name($result_suburb_info[0]['payment_debit_day']);
	$order_cutoff_day = $general_func -> day_name($result_suburb_info[0]['order_cutoff_day']) . " " . date("h:i A", strtotime($result_suburb_info[0]['order_cutoff_time']));
		
	
	if($result_current_order[0]['pickup_delivery']== 1){
		$delivery_cost=$result_suburb_info[0]['delivery_cost'];		
	}else{
		$delivery_cost=$general_func->pickup_cost;	
	}	
			
	
		
	if(intval($result_current_order[0]['program_length']) > 0){
		$rs_discounts = $db->fetch_all_array("select name,details,type,amt from discounts where id='" . intval($result_current_order[0]['program_length']). "' limit 1");
				
		//*****  calculate discount amount *********************//	
		if(intval($rs_discounts[0]['type']) == 1){
			$discount_amt= $rs_discounts[0]['amt']; 	
		}else{				
			if($result_current_order[0]['pickup_delivery']== 1)
				$discount_amt = ($delivery_cost * $rs_discounts[0]['amt'])/100; 
			else											
				$discount_amt = ($delivery_cost * $rs_discounts[0]['amt'])/100;
		}
		//*********************************************************//
			
		//************  calculate total price if discount applicable ******//
		if($delivery_cost > $discount_amt)
			$present_delivery_cost=$delivery_cost - $discount_amt;
		else {
			$present_delivery_cost=0;
		}
		$total_price += $present_delivery_cost;
	}else{
		$total_price += $delivery_cost;	
	}
		

		
	$order_can_be_modified=0;	
		
	$next_week_order=$db_common->order_is_for_next_week(intval($_SESSION['user_id']),$order_id);
	
	if($next_week_order == 5){
		$order_can_be_modified=1;
		$sql_current_order = "select o.id,how_many_week_used,used_promo_code,promo_amount,promo_text,how_many_week,pickup_delivery,pickup_location_id,order_type,order_amount,order_start_date,name,o.status,suburb_id,notes,fname,lname,street_address,email_address,phone,program_length from orders o  left join meal_plan_category p on o.meal_plan_category_id=p.id";
		$sql_current_order .= " left join  users u on o.user_id=u.id";
		$sql_current_order .= " where user_id='" . intval($_SESSION['user_id']) . "' and o.status=5 limit 1";
		$result_current_order = $db -> fetch_all_array($sql_current_order);
						
	}else{
		if($result_current_order[0]['status'] == 0){							
			//*********  order first week cutoffdaytime *****************//							
			$day_diff_between_delivery_and_cutoff_day=$result_suburb_info[0]['delivery_day'] -  $result_suburb_info[0]['order_cutoff_day'];		
							
			list($hour,$minute,$secound)=@explode(":",$result_suburb_info[0]['order_cutoff_time']);									
			list($year,$month,$day)=@explode("-",date("Y-m-d",strtotime($result_current_order[0]['order_start_date'])));
															
			//check whether is  it the current week order *********// 							
			$first_week_order_cutoff_day_time=mktime($hour,$minute,$secound,$month,$day-$day_diff_between_delivery_and_cutoff_day,$year);
								
			if($current_time_ms <= $first_week_order_cutoff_day_time){//*****  not yet started first cuttoff day
				$order_can_be_modified=1;	
			}else{
				$cut_off_date_time_ms=strtotime(date("Y-m-d",strtotime(strtolower($general_func -> day_name($result_suburb_info[0]['order_cutoff_day'])) . ' this week'))." ".$result_suburb_info[0]['order_cutoff_time']);	
									
				if($current_time_ms <= $cut_off_date_time_ms){
					$order_can_be_modified=1;
				}
			}								
		}else if($result_current_order[0]['status'] == 1){
						
			$cut_off_date_time_ms=strtotime(date("Y-m-d",strtotime(strtolower($general_func -> day_name($result_suburb_info[0]['order_cutoff_day'])) . ' this week'))." ".$result_suburb_info[0]['order_cutoff_time']);	
																				
			if($current_time_ms <= $cut_off_date_time_ms){
				$order_can_be_modified=1;
			}							
		}	
			
	}
					
					
	if($order_can_be_modified == 1){//*************  modify order
	
		$db->query("delete from order_meals where order_id='" . $order_id . "'");
	
		$sql_order_meals = "INSERT INTO order_meals(order_id,user_id,meal_id,which_day,meal_time,meal_size,type) VALUES";
		
		for($day=1; $day <= $_SESSION['customize_your_meal_plan']['no_of_days']; $day++){
			
			for($time=1; $time <= $_SESSION['customize_your_meal_plan']['meal_per_day']; $time++ ){
				$sql_order_meals .= "('" . $order_id . "','" . intval($_SESSION['user_id']) . "','" . $_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_id'] . "','" . $day . "','" . $time . "','" . $_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_size'] . "','1'), ";	
			}
			
			for($time=1; $time <= $_SESSION['customize_your_meal_plan']['snack_per_day']; $time++ ){
				$sql_order_meals .= "('" . $order_id . "','" . intval($_SESSION['user_id']) . "','" . intval($_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_id']) . "','" . $day . "','" . $time . "','" . intval($_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_qty']) . "','2'), ";	
			}		
		}	
		
		
		$sql_order_meals = substr($sql_order_meals, 0, -2) . ";";		
		
		$db -> query($sql_order_meals);

		$additional="";
			
			if($result_current_order[0]['program_length'] > 0){
				$rs_program_length = $db->fetch_all_array("select name,details from discounts where id='" . intval($result_current_order[0]['program_length']). "' limit 1");
				$additional .= ' <tr>
			    <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0 0 10px">
			        <tr>
			          <td width="100%" align="left" valign="top" style="padding:10px"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Program Length</h3>
			          <strong> ' . trim($rs_program_length[0]['name']) . '</strong><br/>
					   ' . nl2br($rs_program_length[0]['details']) . '
			           </td>		         
			        </tr> 
			      </table></td>
			  </tr>';
			}
		
			if($result_current_order[0]['pickup_delivery'] == 2){
				$rs_location = $db->fetch_all_array("select location,address,pickup_timing from pickup_locations where id='" . intval($result_current_order[0]['pickup_delivery']). "' limit 1");
				$additional .= ' <tr>
			    <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0 0 10px">
			        <tr>
			          <td width="100%" align="left" valign="top" style="padding:10px"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Pickup Location</h3>
			          <strong> ' . trim($rs_location[0]['location']) . '</strong><br/>
			           Address: ' . trim($rs_location[0]['address']) . '<br/>
					  Pickup Timing: <br/> ' . nl2br($rs_location[0]['pickup_timing']) . '
			           </td>		         
			        </tr> 
			      </table></td>
			  </tr>';
			}
		  	

		//*****************************  send email to admin and user *****************************************//
		$email_content = '<table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0">
			  <tr>
			    <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0; border-bottom:2px solid #666;">
			        <tr>
			          <td width="50%" align="left" valign="middle" style="padding:10px;"><img src="' . $general_func -> site_url . 'email_images/logo.jpg" style="float:left; width:150px; height:auto" alt="" /></td>
			          <td width="50%" align="left" valign="middle" style="padding:10px; color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif;"><strong>Ph:  ' . $general_func -> phone . '<br />
			            <a style="color:#333; text-decoration:none">' . $general_func -> email . '</a><br />
			            ' . nl2br($general_func -> site_address) . '</strong></td>
			        </tr>
			      </table></td>
			  </tr>			  
			  <tr>
			    <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0 0 10px">
			        <tr>
			          <td width="50%" align="left" valign="top" style="padding:10px"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Customer Information</h3>
			            Name :   ' . $result_current_order[0]['fname'] . ' ' . $result_current_order[0]['lname'] . '<br />
			            Address : ' . $result_current_order[0]['street_address'] . '<br />
			            Suburbs :   ' . $result_suburb_info[0]['suburb_name'] . ', ' . $result_suburb_info[0]['suburb_state'] . ', ' . $result_suburb_info[0]['suburb_postcode'] . '<br />
			           </td>
			          <td align="left" valign="top" style="padding:10px">Email : <a  style="color:#333; text-decoration:none">' . $result_current_order[0]['email_address'] . '</a><br />
			            Mobile : ' . $result_current_order[0]['phone'] . '</td>
			        </tr>		        
					
			        <tr>
			          <td width="50%" align="left" valign="top" style="padding:10px"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Order Details</h3>
			            Order No :  FNF - A000' . $order_id . '<br />			           
			            Order Amount :  $' . number_format($total_price, 2) . ' p/w (GST ' . $GST . '% included) </td>
			          <td align="left" valign="top" style="padding:10px">Cut off Date & Time :   ' . $order_cutoff_day . '<br />
			           Payment Debit Day :  ' . $payment_debit_day . '<br />
			           Delivery Date :  ' . $delivery_day . '</td>
			        </tr>
			      </table></td>
			  </tr>';
			  
			  $email_content .= ' <tr>
			    <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0 0 10px">
			        <tr>
			          <td width="100%" align="left" valign="top" style="padding:10px"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Additional Delivery Notes</h3>
			           ' . nl2br($result_current_order[0]['notes']) . '
			           </td>		         
			        </tr> 
			      </table></td>
			  </tr>';
			    
		 $email_content .=$additional;	
		 
				
		 $email_content .= '<tr>
		    <td align="left" valign="top" style="border-top:2px dashed #666; padding:10px 0 0"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0">
		        <tr>
		          <td align="left" valign="top" style="padding:10px"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Order Details</h3>';
				

		for($day=1; $day <= $_SESSION['customize_your_meal_plan']['no_of_days']; $day++){
			
				$email_content .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:20px 0">
		            <tr>
		                <td width="15%" align="left" valign="top" style="padding:5px"><div style="float:left; width:100px; height:100px; background:#e8e8e8; color:#575757; font:bold 20px/100px Arial, Helvetica, sans-serif; text-align:center; border:1px solid #e8e8e8;">Day ' . $day . '</div></td>
		                <td width="85%" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0">';
			
			
			for($time=1; $time <= $_SESSION['customize_your_meal_plan']['meal_per_day']; $time++ ){									
				$email_content .= '<tr>
		                      <td align="left" valign="top" width="20%" style="padding:5px"><strong>Meal ' . $time . '  :</strong></td>
		                      <td width="80%" style="padding:5px">' . $_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_name'] . '(<i>' . $_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_size'] . 'g</i>)</td>
		                    </tr>';
			}
			
			for($time=1; $time <= $_SESSION['customize_your_meal_plan']['snack_per_day']; $time++ ){						
				$email_content .= '<tr>
		                      <td align="left" valign="top" width="20%" style="padding:5px"><strong>Snack ' . $time . ' :</strong></td>
		                      <td width="80%" style="padding:5px">' . $_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_name'] . '(<i>Qty :' . intval($_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_qty']) . '</i>)</td>
		                    </tr>';
			}
			
			$email_content .= '</table></td>
		              </tr>
		            </table>';		
		}	
		

		$email_content .= '		
		 			</td>
		        </tr>
		      </table></td>
		  </tr>
		  <tr>
		    <td align="center" valign="middle" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; padding:20px 0">&copy; Copyright ' . date("Y") . ' Fit "N" Food</td>
		  </tr>
		</table>';
			
		
		$recipient_info = array();
		$recipient_info['recipient_subject'] = "Your current order has been modified at ". $general_func -> site_title;
		$recipient_info['recipient_content'] = $email_content;
		$recipient_info['recipient_email'] = $result_current_order[0]['email_address'];
		$sendmail -> send_email($recipient_info, $general_func -> email, $general_func -> site_title, $general_func -> site_url);
		//*****************************************************************************************************//
		$db -> query("update orders set order_type=3, meal_plan_category_id='" . intval($_SESSION['customize_your_meal_plan']['meal_plan_category_id']) . "', date_modified='" . $current_date_time . "', order_email_content='" . addslashes($email_content) . "', order_amount='" . $total_price . "' 	 where id='" . intval($order_id) . "'");
			
		if($next_week_order == 5)
			$_SESSION['user_message'] = "Your next week order has been modified!";
		else			
			$_SESSION['user_message'] = "Your current order has been modified!";
		
		unset($_SESSION['customize_your_meal_plan']);	
		$general_func -> header_redirect($general_func -> site_url . "order-listing/");	
	}else{//****************** create next week order
	
		$data_new_order=array();		
		$data_new_order['user_id'] = intval($_SESSION['user_id']);
		$data_new_order['order_type'] = 3;
		$data_new_order['order_amount'] = $total_price;
		$data_new_order['order_start_date'] = $result_current_order[0]['order_start_date'];
		$data_new_order['program_length'] =$result_current_order[0]['program_length'];
		$data_new_order['pickup_delivery'] = intval($result_current_order[0]['pickup_delivery']);
		$data_new_order['pickup_location_id'] = intval($result_current_order[0]['pickup_location_id']);	
		
		
		$data_new_order['used_promo_code'] = trim($result_current_order[0]['used_promo_code']);	 
		$data_new_order['promo_amount'] = trim($result_current_order[0]['promo_amount']);	 
		$data_new_order['promo_text'] = trim($result_current_order[0]['promo_text']);	 
		$data_new_order['how_many_week'] = trim($result_current_order[0]['how_many_week']);
		$data_new_order['how_many_week_used'] = trim($result_current_order[0]['how_many_week_used']);
			 
		 
		$data_new_order['notes'] = $result_current_order[0]['notes']; 	
		$data_new_order['training_cost'] = $general_func->meal_plan_amout_for_training_cost;
		$data_new_order['status'] = 5;
		$data_new_order['current_order'] = 0;
		$data_new_order['date_ordered'] = $current_date_time;
		$order_id = $db -> query_insert("orders", $data_new_order);	
	
		$sql_order_meals = "INSERT INTO order_meals(order_id,user_id,meal_id,which_day,meal_time,meal_size,type) VALUES";
		
		for($day=1; $day <= $_SESSION['customize_your_meal_plan']['no_of_days']; $day++){
			
			for($time=1; $time <= $_SESSION['customize_your_meal_plan']['meal_per_day']; $time++ ){
				$sql_order_meals .= "('" . $order_id . "','" . intval($_SESSION['user_id']) . "','" . $_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_id'] . "','" . $day . "','" . $time . "','" . $_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_size'] . "','1'), ";	
			}
			
			for($time=1; $time <= $_SESSION['customize_your_meal_plan']['snack_per_day']; $time++ ){
				$sql_order_meals .= "('" . $order_id . "','" . intval($_SESSION['user_id']) . "','" . intval($_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_id']) . "','" . $day . "','" . $time . "','" . intval($_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_qty']) . "','2'), ";	
			}		
		}	
		

		$sql_order_meals = substr($sql_order_meals, 0, -2) . ";";
		$db -> query($sql_order_meals);

		$additional="";
			
			if($result_current_order[0]['program_length'] > 0){
				$rs_program_length = $db->fetch_all_array("select name,details from discounts where id='" . intval($result_current_order[0]['program_length']). "' limit 1");
				$additional .= ' <tr>
			    <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0 0 10px">
			        <tr>
			          <td width="100%" align="left" valign="top" style="padding:10px"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Program Length</h3>
			          <strong> ' . trim($rs_program_length[0]['name']) . '</strong><br/>
					   ' . nl2br($rs_program_length[0]['details']) . '
			           </td>		         
			        </tr> 
			      </table></td>
			  </tr>';
			}
		
			if($result_current_order[0]['pickup_delivery'] == 2){
				$rs_location = $db->fetch_all_array("select location,address,pickup_timing from pickup_locations where id='" . intval($result_current_order[0]['pickup_delivery']). "' limit 1");
				$additional .= ' <tr>
			    <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0 0 10px">
			        <tr>
			          <td width="100%" align="left" valign="top" style="padding:10px"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Pickup Location</h3>
			          <strong> ' . trim($rs_location[0]['location']) . '</strong><br/>
			           Address: ' . trim($rs_location[0]['address']) . '<br/>
					  Pickup Timing: <br/> ' . nl2br($rs_location[0]['pickup_timing']) . '
			           </td>		         
			        </tr> 
			      </table></td>
			  </tr>';
			}
		  	
			

		//*****************************  send email to admin and user *****************************************//
		$email_content = '<table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0">
			  <tr>
			    <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0; border-bottom:2px solid #666;">
			        <tr>
			          <td width="50%" align="left" valign="middle" style="padding:10px;"><img src="' . $general_func -> site_url . 'email_images/logo.jpg" style="float:left; width:150px; height:auto" alt="" /></td>
			          <td width="50%" align="left" valign="middle" style="padding:10px; color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif;"><strong>Ph:  ' . $general_func -> phone . '<br />
			            <a style="color:#333; text-decoration:none">' . $general_func -> email . '</a><br />
			            ' . nl2br($general_func -> site_address) . '</strong></td>
			        </tr>
			      </table></td>
			  </tr>			  
			  <tr>
			    <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0 0 10px">
			        <tr>
			          <td width="50%" align="left" valign="top" style="padding:10px"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Customer Information</h3>
			            Name :   ' . $result_current_order[0]['fname'] . ' ' . $result_current_order[0]['lname'] . '<br />
			            Address : ' . $result_current_order[0]['street_address'] . '<br />
			            Suburbs :   ' . $result_suburb_info[0]['suburb_name'] . ', ' . $result_suburb_info[0]['suburb_state'] . ', ' . $result_suburb_info[0]['suburb_postcode'] . '<br />
			           </td>
			          <td align="left" valign="top" style="padding:10px">Email : <a  style="color:#333; text-decoration:none">' . $result_current_order[0]['email_address'] . '</a><br />
			            Mobile : ' . $result_current_order[0]['phone'] . '</td>
			        </tr>		        
					
			        <tr>
			          <td width="50%" align="left" valign="top" style="padding:10px"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Order Details</h3>
			            Order No :  FNF - A000' . $order_id . '<br />			            
			            Order Amount :  $' . number_format($total_price, 2) . ' p/w (GST ' . $GST . '% included) </td>
			          <td align="left" valign="top" style="padding:10px">Cut off Date & Time :   ' . $order_cutoff_day . '<br />
			           Payment Debit Day :  ' . $payment_debit_day . '<br />
			           Delivery Date :  ' . $delivery_day . '</td>
			        </tr>
			      </table></td>
			  </tr>';
			  
			  $email_content .= ' <tr>
			    <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0 0 10px">
			        <tr>
			          <td width="100%" align="left" valign="top" style="padding:10px"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Additional Delivery Notes</h3>
			           ' . nl2br($result_current_order[0]['notes']) . '
			           </td>		         
			        </tr> 
			      </table></td>
			  </tr>';
			    
		 $email_content .= $additional;
		  
		 $email_content .= '<tr>
		    <td align="left" valign="top" style="border-top:2px dashed #666; padding:10px 0 0"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0">
		        <tr>
		          <td align="left" valign="top" style="padding:10px"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Order Details</h3>';
				

		for($day=1; $day <= $_SESSION['customize_your_meal_plan']['no_of_days']; $day++){
			
				$email_content .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:20px 0">
		            <tr>
		                <td width="15%" align="left" valign="top" style="padding:5px"><div style="float:left; width:100px; height:100px; background:#e8e8e8; color:#575757; font:bold 20px/100px Arial, Helvetica, sans-serif; text-align:center; border:1px solid #e8e8e8;">Day ' . $day . '</div></td>
		                <td width="85%" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0">';
			
			
			for($time=1; $time <= $_SESSION['customize_your_meal_plan']['meal_per_day']; $time++ ){									
				$email_content .= '<tr>
		                      <td align="left" valign="top" width="20%" style="padding:5px"><strong>Meal ' . $time . '  :</strong></td>
		                      <td width="80%" style="padding:5px">' . $_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_name'] . '(<i>' . $_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_size'] . 'g</i>)</td>
		                    </tr>';
			}
			
			for($time=1; $time <= $_SESSION['customize_your_meal_plan']['snack_per_day']; $time++ ){						
				$email_content .= '<tr>
		                      <td align="left" valign="top" width="20%" style="padding:5px"><strong>Snack ' . $time . ' :</strong></td>
		                      <td width="80%" style="padding:5px">' . $_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_name'] . '(<i>Qty :' . intval($_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_qty']) . '</i>)</td>
		                    </tr>';
			}
			
			$email_content .= '</table></td>
		              </tr>
		            </table>';		
		}	
		

		$email_content .= '		
		 			</td>
		        </tr>
		      </table></td>
		  </tr>
		  <tr>
		    <td align="center" valign="middle" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; padding:20px 0">&copy; Copyright ' . date("Y") . ' Fit "N" Food</td>
		  </tr>
		</table>';
		
		
		$recipient_info = array();
		$recipient_info['recipient_subject'] = "Your next week order has been modified at ". $general_func -> site_title;
		$recipient_info['recipient_content'] = $email_content;
		$recipient_info['recipient_email'] = $result_current_order[0]['email_address'];
		$sendmail -> send_email($recipient_info, $general_func -> email, $general_func -> site_title, $general_func -> site_url);
		//*****************************************************************************************************//
		$db -> query("update orders set  meal_plan_category_id='" . intval($_SESSION['customize_your_meal_plan']['meal_plan_category_id']) . "', order_email_content='" . addslashes($email_content) . "' where id='" . intval($order_id) . "'");
			
		$_SESSION['user_message'] = "Your next week  order has been modified!";
		
		unset($_SESSION['customize_your_meal_plan']);	
		
		$general_func -> header_redirect($general_func -> site_url . "order-listing/");
	}
}

if (isset($_POST['enter']) && $_POST['enter'] == "customize_your_meal_plan" && trim($_POST['frm_customize_your_meal_plan']) == $_SESSION['frm_customize_your_meal_plan']) {
		
	unset($_SESSION['customize_your_meal_plan']);			
		
	$_SESSION['customize_your_meal_plan'] = array();

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
	
}else{
	unset($_SESSION['customize_your_meal_plan']);
	$_SESSION['customize_your_meal_plan'] = array();
	
	$no_of_days = 0;
	$meal_per_day = 0;
	$snack_per_day = 0;
	
	
	$display_price=mysql_result(mysql_query("select order_amount from orders where id='" . $order_id . "' limit 1"), 0,0);
	
	$sql_meals = "select which_day,meal_time,meal_size,meal_id from order_meals where order_id='" . $order_id . "' and type=1  order by which_day,meal_time ASC";
	$result_default_meals = $db -> fetch_all_array($sql_meals);
	$total_default_meals = count($result_default_meals);
	
	$default_meals = array();
	
	for ($i = 0; $i < $total_default_meals; $i++) {
	
		if ($result_default_meals[$i]['which_day'] > $no_of_days)
			$no_of_days = $result_default_meals[$i]['which_day'];
	
		if ($result_default_meals[$i]['meal_time'] > $meal_per_day)
			$meal_per_day = $result_default_meals[$i]['meal_time'];
	
		
		$_SESSION['customize_your_meal_plan']['customized_meal'][$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_id'] = $result_default_meals[$i]['meal_id'];
		$_SESSION['customize_your_meal_plan']['customized_meal'][$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_size'] = $result_default_meals[$i]['meal_size'];
	
	}
	
	$sql_snacks = "select which_day,meal_time,meal_size,meal_id from order_meals where order_id='" . $order_id . "' and type=2 order by which_day,meal_time ASC";
	$result_default_snacks = $db -> fetch_all_array($sql_snacks);
	$total_default_snacks = count($result_default_snacks);
	
	$default_snacks = array();
	
	for ($i = 0; $i < $total_default_snacks; $i++) {
		if ($result_default_snacks[$i]['meal_time'] > $snack_per_day)
			$snack_per_day = $result_default_snacks[$i]['meal_time'];		
		
		$_SESSION['customize_your_meal_plan']['customized_snacks'][$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['snack_id'] = $result_default_snacks[$i]['meal_id'];
		$_SESSION['customize_your_meal_plan']['customized_snacks'][$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['snack_qty']= $result_default_snacks[$i]['meal_size'];
	}
	
	$_SESSION['customize_your_meal_plan']['meal_plan_category_id'] = intval($meal_plan_category_id);
	$_SESSION['customize_your_meal_plan']['no_of_days'] = intval($no_of_days);
	$_SESSION['customize_your_meal_plan']['meal_per_day'] = intval($meal_per_day);
	$_SESSION['customize_your_meal_plan']['snack_per_day'] = intval($snack_per_day);	
	
	
}




$sql_content="select customize_meal_plan_page_left_heading,customize_meal_plan_page_left_content,customize_meal_plan_page_right_heading,customize_meal_plan_page_right_content from dynamic_pages where id=1 limit 1";
$result_content=$db->fetch_all_array($sql_content);

if(isset($_SESSION['customize_your_meal_plan']['meal_plan_category_id']))
	$meal_plan_category_id=$_SESSION['customize_your_meal_plan']['meal_plan_category_id'];

if(isset($_SESSION['customize_your_meal_plan']['no_of_days']))
	$no_of_days=$_SESSION['customize_your_meal_plan']['no_of_days'];

if(isset($_SESSION['customize_your_meal_plan']['meal_per_day']))
	$meal_per_day=$_SESSION['customize_your_meal_plan']['meal_per_day'];

if(isset($_SESSION['customize_your_meal_plan']['snack_per_day']))
	$snack_per_day=$_SESSION['customize_your_meal_plan']['snack_per_day'];
	
	
	

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
    <div class="order_listingBcmb">
			<ul>
				<li>
					<a href="my-account/">My Account »</a>
				</li>
				<li>
					My Orders
				</li>
			</ul>
		</div>
    <div class="whitePnl">
		<form name="customize_your_meal_plan" method="post" action="modify-customize-meal-plan/<?=$order_id?>" onsubmit="return validate_customized_your_meal_plan();">
			<input type="hidden" name="enter" value="customize_your_meal_plan" />
			<input type="hidden" name="frm_customize_your_meal_plan" value="<?=$_SESSION['frm_customize_your_meal_plan'] ?>" />
			<input type="hidden" name="total_price_display" id="total_price_display" value="0" />
			<input type="hidden" name="meal_plan_amout_for_training_cost" id="meal_plan_amout_for_training_cost" value="<?=$general_func->meal_plan_amout_for_training_cost?>" />
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
								<!--<option value="1" <?=intval($_SESSION['customize_your_meal_plan']['meal_per_day']) == 1 ? 'selected="selected"' : ''; ?>>1</option>
								<option value="2" <?=intval($_SESSION['customize_your_meal_plan']['meal_per_day']) == 2 ? 'selected="selected"' : ''; ?>>2</option>-->
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
				<form name="customize_your_meal_plan_final" method="post" action="modify-customize-meal-plan/<?=$order_id?>"  onsubmit="return meals_validate();">
					<input type="hidden" name="enter" value="customize_your_meal_plan_final" />
					<input type="hidden" name="frm_customize_your_meal_plan" value="<?=$_SESSION['frm_customize_your_meal_plan'] ?>" />

					<?php
					if(isset($_SESSION['customize_your_meal_plan']) && is_array($_SESSION['customize_your_meal_plan'])){					
						$result_meals=$db->fetch_all_array("select id,name from meals  where status=1 and id IN(select DISTINCT(meal_id) from meal_plan_category_meals where meal_plan_category_id='" . $_SESSION['customize_your_meal_plan']['meal_plan_category_id'] . "')   order by name ASC");
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
													<select onchange="display_price();" name="meal_id_<?=$day . "_" . $time ?>" id="meal_id_<?=$day . "_" . $time ?>">
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
													<select name="<?=$day . "_" . $time ?>" id="<?=$day . "_" . $time ?>" onchange="display_price();" >
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
													<select onchange="display_price();" name="snack_id_<?=$day . "_" . $time ?>" id="snack_id_<?=$day . "_" . $time ?>">
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
					<div class="checkout_row" style="border:none; margin-top:0">
						<div  style="padding-top:10px; color: #99bf13; text-align: center; font-size: 24px;">Total Meal plan price: $<label id="show_total"><?=number_format($display_price, 2)?></label></div>
						<div id="show_message" style="padding-top:10px; padding-bottom: 10px; color: #ff0000; text-align: center;"></div>
						<br class="clear">
					<div style="width:200px; margin:0 auto"><input name="submit" type="submit" value="Update Order" style="width:200px;" /></div>
					</div>
					<?php } ?>
				</form>
			</div>
		</div>
     </div>
	</div>
</div>
<?php

include_once ("includes/footer.php");
?>