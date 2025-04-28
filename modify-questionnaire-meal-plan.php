<?php
include_once("includes/header.php");

$order_id=intval($_REQUEST['order_id']);



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



//***************  customer chose fill the questionnaire ***************************************//
//** set fill the questionnaire and unset Select your meal plan and  Customize your meal plan **//
//***********************************************************************************************//


if(isset($_GET['action']) && $_GET['action']=="update_me" && isset($_SESSION['frm_get_your_meal_plan'])){ 
	
	
	$sql_current_order = "select o.id,how_many_week_used,used_promo_code,promo_amount,promo_text,how_many_week,pickup_delivery,pickup_location_id,order_type,order_amount,order_start_date,name,o.status,suburb_id,notes,fname,lname,street_address,email_address,phone,program_length from orders o  left join meal_plan_category p on o.meal_plan_category_id=p.id";
	$sql_current_order .= " left join  users u on o.user_id=u.id";
	$sql_current_order .= " where user_id='" . intval($_SESSION['user_id']) . "' and current_order=1 limit 1";
	$result_current_order = $db -> fetch_all_array($sql_current_order);
		
	$result_suburb_info = $db -> fetch_all_array("select suburb_name,suburb_state,delivery_cost,suburb_postcode,delivery_day,payment_debit_day,order_cutoff_day,order_cutoff_time from suburb where id=" . intval($result_current_order[0]['suburb_id']) . " limit 1");
	$delivery_day = $general_func -> day_name($result_suburb_info[0]['delivery_day']);
	$payment_debit_day = $general_func -> day_name($result_suburb_info[0]['payment_debit_day']);
	$order_cutoff_day = $general_func -> day_name($result_suburb_info[0]['order_cutoff_day']) . " " . date("h:i A", strtotime($result_suburb_info[0]['order_cutoff_time']));
		
	
	$total_price=$_GET['price'];
	
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
	
		$size_of_meal="";
		
		if(trim($_SESSION['fill_the_questionnaire']['gender']) == "male"){
			if(floatval($_SESSION['fill_the_questionnaire']['weight']) < 51)
				$size_of_meal="100";
			else if(floatval($_SESSION['fill_the_questionnaire']['weight']) > 50 && floatval($_SESSION['fill_the_questionnaire']['weight']) < 76)
				$size_of_meal="150";
			else
				$size_of_meal="200";
		}else{
			if(floatval($_SESSION['fill_the_questionnaire']['weight']) < 61)
				$size_of_meal="100";
			else
				$size_of_meal="150";
		}
		
		$sql_order_meals = "INSERT INTO order_meals(order_id,user_id,meal_id,which_day,meal_time,meal_size,type) VALUES";

		$sql_meals="select qty,meal_id,which_day,meal_time,type  from categories_default_meals where meal_plan_category_id='" . $_SESSION['fill_the_questionnaire']['meal_plan_category_id'] . "' and which_day <= '" . $_SESSION['fill_the_questionnaire']['like_to_eat_how_many_days'] . "' order by which_day,meal_time ASC";
		$result_default_meals=$db->fetch_all_array($sql_meals);
		$total_default_meals=count($result_default_meals);

		for ($i = 0; $i < $total_default_meals; $i++) {
			$meal_size=$result_default_meals[$i]['type']==2?$result_default_meals[$i]['qty']:$size_of_meal;
			
			$sql_order_meals .= "('" . $order_id . "','" . intval($_SESSION['user_id']) . "','" . $result_default_meals[$i]['meal_id'] . "','" . $result_default_meals[$i]['which_day'] . "','" . $result_default_meals[$i]['meal_time'] . "','" . $meal_size . "','" . $result_default_meals[$i]['type'] . "'), ";
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
		  
		   $email_content .= ' <tr>
		    <td align="left" valign="top" style="border-top:2px dashed #666; padding:10px 0 0"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0">
		        <tr>
		          <td align="left" valign="top" style="padding:10px"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Order Details</h3>';
				

		$sql_meals="select which_day,meal_time,m.name from categories_default_meals d left join meals m on d.meal_id=m.id where d.meal_plan_category_id='" . $_SESSION['fill_the_questionnaire']['meal_plan_category_id'] . "' and type=1 order by which_day,meal_time ASC";
		$result_default_meals=$db->fetch_all_array($sql_meals);
		$total_default_meals=count($result_default_meals);

		$default_meals = array();

		for ($i = 0; $i < $total_default_meals; $i++) {
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_name'] = $result_default_meals[$i]['name'];
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_size'] = $size_of_meal;
		}

		$sql_snacks="select which_day,meal_time,meal_id,name,qty from categories_default_meals d left join snacks s on d.meal_id=s.id where d.meal_plan_category_id='" . $_SESSION['fill_the_questionnaire']['meal_plan_category_id'] . "' and type=2 order by which_day,meal_time ASC";
		$result_default_snacks=$db->fetch_all_array($sql_snacks);
		$total_default_snacks=count($result_default_snacks);


		$default_snacks = array();

		for ($i = 0; $i < $total_default_snacks; $i++) {
			$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['name'] = $result_default_snacks[$i]['name'];
			$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['qty'] = $result_default_snacks[$i]['qty'];
		}

		for ($day = 1; $day <= $_SESSION['fill_the_questionnaire']['like_to_eat_how_many_days']; $day++) {

			$email_content .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:20px 0">
		            <tr>
		                <td width="15%" align="left" valign="top" style="padding:5px"><div style="float:left; width:100px; height:100px; background:#e8e8e8; color:#575757; font:bold 20px/100px Arial, Helvetica, sans-serif; text-align:center; border:1px solid #e8e8e8;">Day ' . $day . '</div></td>
		                <td width="85%" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0">';

			for($time=1; $time <=$_SESSION['fill_the_questionnaire']['meals_per_day']; $time++ ){
				$email_content .= '<tr>
		                      <td align="left" valign="top" width="20%" style="padding:5px"><strong>Meal ' . $time . '  :</strong></td>
		                      <td width="80%" style="padding:5px">' . $default_meals[$day][$time]['meal_name'] . '(<i>' . $default_meals[$day][$time]['meal_size'] . 'g</i>)</td>
		                    </tr>';
			}

			for($time=1; $time <=$_SESSION['fill_the_questionnaire']['snacks_per_day']; $time++ ){
				$email_content .= '<tr>
		                      <td align="left" valign="top" width="20%" style="padding:5px"><strong>Snack ' . $time . ' :</strong></td>
		                      <td width="80%" style="padding:5px">' . $default_snacks[$day][$time]['name'] . '(<i>Qty :' . $default_snacks[$day][$time]['qty'] . '</i>)</td>
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
		$db -> query("update orders set  meal_plan_category_id='" . intval($_SESSION['fill_the_questionnaire']['meal_plan_category_id']) . "', date_modified='" . $current_date_time . "', order_email_content='" . addslashes($email_content) . "', order_amount='" . $total_price . "' 	 where id='" . intval($order_id) . "'");
			
		if($next_week_order == 5)
			$_SESSION['user_message'] = "Your next week order has been modified!";
		else			
			$_SESSION['user_message'] = "Your current order has been modified!";
			
		$general_func -> header_redirect($general_func -> site_url . "order-listing/");
	}else{//****************** create next week order
		
		$data_new_order=array();		
		$data_new_order['user_id'] = intval($_SESSION['user_id']);
		$data_new_order['order_type'] = 2;
		$data_new_order['order_amount'] = $total_price;
		$data_new_order['order_start_date'] = $result_current_order[0]['order_start_date'];
		$data_new_order['program_length'] =$result_current_order[0]['program_length']; 
		$data_new_order['notes'] = $result_current_order[0]['notes']; 	
		$data_new_order['pickup_delivery'] = intval($result_current_order[0]['pickup_delivery']);
		$data_new_order['pickup_location_id'] = intval($result_current_order[0]['pickup_location_id']);	
		
		$data_new_order['used_promo_code'] = trim($result_current_order[0]['used_promo_code']);	 
		$data_new_order['promo_amount'] = trim($result_current_order[0]['promo_amount']);	 
		$data_new_order['promo_text'] = trim($result_current_order[0]['promo_text']);	 
		$data_new_order['how_many_week'] = trim($result_current_order[0]['how_many_week']);
		$data_new_order['how_many_week_used'] = trim($result_current_order[0]['how_many_week_used']);
		
		
		
		$data_new_order['training_cost'] = $general_func->meal_plan_amout_for_training_cost;		
		$data_new_order['status'] = 5;
		$data_new_order['current_order'] = 0;
		$data_new_order['date_ordered'] = $current_date_time;
		$order_id = $db -> query_insert("orders", $data_new_order);		
	
		$size_of_meal="";
		
		if(trim($_SESSION['fill_the_questionnaire']['gender']) == "male"){
			if(floatval($_SESSION['fill_the_questionnaire']['weight']) < 51)
				$size_of_meal="100";
			else if(floatval($_SESSION['fill_the_questionnaire']['weight']) > 50 && floatval($_SESSION['fill_the_questionnaire']['weight']) < 76)
				$size_of_meal="150";
			else
				$size_of_meal="200";
		}else{
			if(floatval($_SESSION['fill_the_questionnaire']['weight']) < 61)
				$size_of_meal="100";
			else
				$size_of_meal="150";
		}
		
		$sql_order_meals = "INSERT INTO order_meals(order_id,user_id,meal_id,which_day,meal_time,meal_size,type) VALUES";

		$sql_meals="select qty,meal_id,which_day,meal_time,type  from categories_default_meals where meal_plan_category_id='" . $_SESSION['fill_the_questionnaire']['meal_plan_category_id'] . "' and which_day <= '" . $_SESSION['fill_the_questionnaire']['like_to_eat_how_many_days'] . "' order by which_day,meal_time ASC";
		$result_default_meals=$db->fetch_all_array($sql_meals);
		$total_default_meals=count($result_default_meals);

		for ($i = 0; $i < $total_default_meals; $i++) {
			$meal_size=$result_default_meals[$i]['type']==2?$result_default_meals[$i]['qty']:$size_of_meal;
			
			$sql_order_meals .= "('" . $order_id . "','" . intval($_SESSION['user_id']) . "','" . $result_default_meals[$i]['meal_id'] . "','" . $result_default_meals[$i]['which_day'] . "','" . $result_default_meals[$i]['meal_time'] . "','" . $meal_size . "','" . $result_default_meals[$i]['type'] . "'), ";
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
					  Pickup Timing:  <br/> ' . nl2br($rs_location[0]['pickup_timing']) . '
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
		   
		   
		   $email_content .= ' <tr>
		    <td align="left" valign="top" style="border-top:2px dashed #666; padding:10px 0 0"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0">
		        <tr>
		          <td align="left" valign="top" style="padding:10px"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Order Details</h3>';
				

		$sql_meals="select which_day,meal_time,m.name from categories_default_meals d left join meals m on d.meal_id=m.id where d.meal_plan_category_id='" . $_SESSION['fill_the_questionnaire']['meal_plan_category_id'] . "' and type=1 order by which_day,meal_time ASC";
		$result_default_meals=$db->fetch_all_array($sql_meals);
		$total_default_meals=count($result_default_meals);

		$default_meals = array();

		for ($i = 0; $i < $total_default_meals; $i++) {
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_name'] = $result_default_meals[$i]['name'];
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_size'] = $size_of_meal;
		}

		$sql_snacks="select which_day,meal_time,meal_id,name,qty from categories_default_meals d left join snacks s on d.meal_id=s.id where d.meal_plan_category_id='" . $_SESSION['fill_the_questionnaire']['meal_plan_category_id'] . "' and type=2 order by which_day,meal_time ASC";
		$result_default_snacks=$db->fetch_all_array($sql_snacks);
		$total_default_snacks=count($result_default_snacks);


		$default_snacks = array();

		for ($i = 0; $i < $total_default_snacks; $i++) {
			$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['name'] = $result_default_snacks[$i]['name'];
			$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['qty'] = $result_default_snacks[$i]['qty'];
		}

		for ($day = 1; $day <= $_SESSION['fill_the_questionnaire']['like_to_eat_how_many_days']; $day++) {

			$email_content .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:20px 0">
		            <tr>
		                <td width="15%" align="left" valign="top" style="padding:5px"><div style="float:left; width:100px; height:100px; background:#e8e8e8; color:#575757; font:bold 20px/100px Arial, Helvetica, sans-serif; text-align:center; border:1px solid #e8e8e8;">Day ' . $day . '</div></td>
		                <td width="85%" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0">';

			for($time=1; $time <=$_SESSION['fill_the_questionnaire']['meals_per_day']; $time++ ){
				$email_content .= '<tr>
		                      <td align="left" valign="top" width="20%" style="padding:5px"><strong>Meal ' . $time . '  :</strong></td>
		                      <td width="80%" style="padding:5px">' . $default_meals[$day][$time]['meal_name'] . '(<i>' . $default_meals[$day][$time]['meal_size'] . 'g</i>)</td>
		                    </tr>';
			}

			for($time=1; $time <=$_SESSION['fill_the_questionnaire']['snacks_per_day']; $time++ ){
				$email_content .= '<tr>
		                      <td align="left" valign="top" width="20%" style="padding:5px"><strong>Snack ' . $time . ' :</strong></td>
		                      <td width="80%" style="padding:5px">' . $default_snacks[$day][$time]['name'] . '(<i>Qty :' . $default_snacks[$day][$time]['qty'] . '</i>)</td>
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
		$db -> query("update orders set meal_plan_category_id='" . intval($_SESSION['fill_the_questionnaire']['meal_plan_category_id']) . "', order_email_content='" . addslashes($email_content) . "' where id='" . intval($order_id) . "'");
			
		$_SESSION['user_message'] = "Your next week  order has been modified!";
		
		$general_func -> header_redirect($general_func -> site_url . "order-listing/");
		
	}
} 

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
</script>
<style type="text/css">
.melPlnrFrmRght ul li input[type="radio"] + label span{ background:url(images/radioBtnBg-type-two.png) no-repeat left top; }
.getStdPnl2{ background:#f5f5f5 }
</style>



<div class="inrBnr">
	<?php $db_common->static_page_banner($dynamic_content['page_id']);?>								
</div>								
<div class="hmPanelTwo" style="position:relative; background:#fff">
	<div class="mainDiv2">
    <div class="order_listingBcmb">
			<ul>
				<li>
					<a href="my-account/">My Account &raquo;</a>
				</li>
				<li>
					<a href="order-listing/">My Orders &raquo;</a>
				</li>
				<li>
					Modify Order
				</li>
			</ul>
		</div>
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
			<form name="frm_get_your_meal_plan" method="post" action="modify-questionnaire-meal-plan/<?=$order_id?>" onsubmit=" return validate_get_your_meal_plan();">
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
						<strong><span>Net Weight:</span> <?=$meal_size?>g</strong>
						<strong><span>Meal Plan Price:</span> <label id="plan_price"></label> </strong>
						</h4>
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
																	<div class="info_tab"><?=$db_common->nutritional_value($default_meals[$day][$time]['protein'],$meal_size)?> g</div>
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
												  for($time=1; $time <=$_SESSION['fill_the_questionnaire']['snacks_per_day']; $time++ ){ ?>
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
								<?php 
									
								}

								$price += floatval($general_func->meal_plan_amout_for_training_cost);
								$meal_price=number_format($price, 2);
								
								  ?>
								<script>
									$("#plan_price").html("$<?=$meal_price?>");
									
								</script>
									
								
								<br class="clear">								
								<div class="dayPnlBtn">	
									<input name="submit" type="submit" value="Update Order" onclick="location.href='<?=$general_func->site_url?>modify-questionnaire-meal-plan/update/<?=$order_id?>/<?=$price?>'" style="width:100%" />
								
								</div>
								<p style="color: #ff0000;font: 14px/18px 'open_sansregular';text-align: center; padding:10px 0;"><?php echo $result_content[0]['set_meal_plan_modification']; ?></p>
                                <div class="dayPnlBtn" style="margin-top:0">	
									<input name="submit" type="submit" value="Modify Meal Plan" onclick="location.href='<?=$general_func->site_url?>modify-customize-meal-plan/<?=$order_id?>'" style="width:90%; margin:0 5%" class="mdfyMealPln" />
								</div>
							</div>    
						</div>
					</div>
				</div>
				<?php }	?>
			</div>
			
			<?php
			include_once("includes/footer.php");
			?>