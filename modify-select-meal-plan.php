<?php
include_once ("includes/header.php");


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

//$_SESSION['update_order_no']=$order_id;

//***************  chose your meal plan ***************************************//
//** chose your meal plan and unset  fill the questionnaire and  Customize your meal plan **//
//***********************************************************************************************//
if (isset($_POST['enter']) && $_POST['enter'] == "continue_order" && trim($_POST['frm_choose_your_meal_plan']) == $_SESSION['frm_choose_your_meal_plan']) {
	
	
	if(!isset($_SESSION['choose_your_meal_plan']) && count($_SESSION['choose_your_meal_plan']) == 0){
		$_SESSION['user_message'] = "Sorry, you did not modify anything!";
		$general_func -> header_redirect($general_func -> site_url . "order-listing/");			
	}else{			
			
		$sql_current_order = "select o.id,how_many_week_used,used_promo_code,promo_amount,promo_text,how_many_week,pickup_delivery,pickup_location_id,order_type,order_amount,order_start_date,name,o.status,suburb_id,notes,fname,lname,street_address,email_address,phone,program_length from orders o  left join meal_plan_category p on o.meal_plan_category_id=p.id";
		$sql_current_order .= " left join  users u on o.user_id=u.id";
		$sql_current_order .= " where user_id='" . intval($_SESSION['user_id']) . "' and current_order=1 limit 1";
		$result_current_order = $db -> fetch_all_array($sql_current_order);
		
		$result_suburb_info = $db -> fetch_all_array("select suburb_name,suburb_state,delivery_cost,suburb_postcode,delivery_day,payment_debit_day,order_cutoff_day,order_cutoff_time from suburb where id=" . intval($result_current_order[0]['suburb_id']) . " limit 1");
		$delivery_day = $general_func -> day_name($result_suburb_info[0]['delivery_day']);
		$payment_debit_day = $general_func -> day_name($result_suburb_info[0]['payment_debit_day']);
		$order_cutoff_day = $general_func -> day_name($result_suburb_info[0]['order_cutoff_day']) . " " . date("h:i A", strtotime($result_suburb_info[0]['order_cutoff_time']));
		
		
		
		$total_price=$_POST['total_price'];
		
		
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
		 
			$sql_order_meals = "INSERT INTO order_meals(order_id,user_id,meal_plan_id,meal_id,which_day,meal_time,meal_size,type) VALUES";

			$sql_meals = "select meal_id,which_day,meal_time,meal_size,type from meal_plan_meals where meal_plan_id='" . intval($_SESSION['choose_your_meal_plan']['category_meal_plan']) . "' order by which_day,meal_time ASC";
			$result_default_meals = $db -> fetch_all_array($sql_meals);
			$total_default_meals = count($result_default_meals);
	
			for ($i = 0; $i < $total_default_meals; $i++) {
				$sql_order_meals .= "('" . $order_id . "','" . intval($_SESSION['user_id']) . "','" . intval($_SESSION['choose_your_meal_plan']['category_meal_plan']) . "','" . $result_default_meals[$i]['meal_id'] . "','" . $result_default_meals[$i]['which_day'] . "','" . $result_default_meals[$i]['meal_time'] . "','" . $result_default_meals[$i]['meal_size'] . "','" . $result_default_meals[$i]['type'] . "'), ";
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
	
						$sql_meals = "select which_day,meal_time,meal_size,meal_id,m.name from meal_plan_meals d left join meals m on d.meal_id=m.id where d.meal_plan_id='" . intval($_SESSION['choose_your_meal_plan']['category_meal_plan']) . "' and type=1 order by which_day,meal_time ASC";
						$result_default_meals = $db -> fetch_all_array($sql_meals);
						$total_default_meals = count($result_default_meals);
				
						$default_meals = array();
				
						for ($i = 0; $i < $total_default_meals; $i++) {
							$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_name'] = $result_default_meals[$i]['name'];
							$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_size'] = $result_default_meals[$i]['meal_size'];
						}
				
						$sql_snacks = "select which_day,meal_time,name,meal_size as qty from meal_plan_meals  d left join snacks s on d.meal_id=s.id where d.meal_plan_id='" . intval($_SESSION['choose_your_meal_plan']['category_meal_plan']) . "' and type=2 order by which_day,meal_time ASC";
						$result_default_snacks = $db -> fetch_all_array($sql_snacks);
						$total_default_snacks = count($result_default_snacks);
				
						$default_snacks = array();
				
						for ($i = 0; $i < $total_default_snacks; $i++) {
							$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['name'] = $result_default_snacks[$i]['name'];
							$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['qty'] = $result_default_snacks[$i]['qty'];
						}
				
						for ($day = 1; $day <= $_SESSION['choose_your_meal_plan']['no_of_days']; $day++) {
				
							$email_content .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:20px 0">
						            <tr>
						                <td width="15%" align="left" valign="top" style="padding:5px"><div style="float:left; width:100px; height:100px; background:#e8e8e8; color:#575757; font:bold 20px/100px Arial, Helvetica, sans-serif; text-align:center; border:1px solid #e8e8e8;">Day ' . $day . '</div></td>
						                <td width="85%" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0">
						                  ';
				
							for ($time = 1; $time <= $_SESSION['choose_your_meal_plan']['meal_per_day']; $time++) {
								$email_content .= '<tr>
						                      <td align="left" valign="top" width="20%" style="padding:5px"><strong>Meal ' . $time . '  :</strong></td>
						                      <td width="80%" style="padding:5px">' . $default_meals[$day][$time]['meal_name'] . '(<i>' . $default_meals[$day][$time]['meal_size'] . 'g</i>)</td>
						                    </tr>';
							}
				
							for ($time = 1; $time <= $_SESSION['choose_your_meal_plan']['snack_per_day']; $time++) {
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
			$db -> query("update orders set  meal_plan_category_id='" . intval($_SESSION['choose_your_meal_plan']['meal_plan_category_id']) . "', date_modified='" . $current_date_time . "', order_email_content='" . addslashes($email_content) . "', order_amount='" . $total_price . "' 	 where id='" . intval($order_id) . "'");
			
			if($next_week_order == 5)
				$_SESSION['user_message'] = "Your next week order has been modified!";
			else			
				$_SESSION['user_message'] = "Your current order has been modified!";
			
			
				
			$general_func -> header_redirect($general_func -> site_url . "order-listing/");
				
		}else{//****************** create next week order			
			$data_new_order=array();		
			$data_new_order['user_id'] = intval($_SESSION['user_id']);
			$data_new_order['order_type'] = 1;
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
			
			
		 
			$sql_order_meals = "INSERT INTO order_meals(order_id,user_id,meal_plan_id,meal_id,which_day,meal_time,meal_size,type) VALUES";

			$sql_meals = "select meal_id,which_day,meal_time,meal_size,type from meal_plan_meals where meal_plan_id='" . intval($_SESSION['choose_your_meal_plan']['category_meal_plan']) . "' order by which_day,meal_time ASC";
			$result_default_meals = $db -> fetch_all_array($sql_meals);
			$total_default_meals = count($result_default_meals);
	
			for ($i = 0; $i < $total_default_meals; $i++) {
				$sql_order_meals .= "('" . $order_id . "','" . intval($_SESSION['user_id']) . "','" . intval($_SESSION['choose_your_meal_plan']['category_meal_plan']) . "','" . $result_default_meals[$i]['meal_id'] . "','" . $result_default_meals[$i]['which_day'] . "','" . $result_default_meals[$i]['meal_time'] . "','" . $result_default_meals[$i]['meal_size'] . "','" . $result_default_meals[$i]['type'] . "'), ";
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
			           New Order No :  FNF - A000' . $order_id . '<br />			            
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
	
						$sql_meals = "select which_day,meal_time,meal_size,meal_id,m.name from meal_plan_meals d left join meals m on d.meal_id=m.id where d.meal_plan_id='" . intval($_SESSION['choose_your_meal_plan']['category_meal_plan']) . "' and type=1 order by which_day,meal_time ASC";
						$result_default_meals = $db -> fetch_all_array($sql_meals);
						$total_default_meals = count($result_default_meals);
				
						$default_meals = array();
				
						for ($i = 0; $i < $total_default_meals; $i++) {
							$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_name'] = $result_default_meals[$i]['name'];
							$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_size'] = $result_default_meals[$i]['meal_size'];
						}
				
						$sql_snacks = "select which_day,meal_time,name,meal_size as qty from meal_plan_meals  d left join snacks s on d.meal_id=s.id where d.meal_plan_id='" . intval($_SESSION['choose_your_meal_plan']['category_meal_plan']) . "' and type=2 order by which_day,meal_time ASC";
						$result_default_snacks = $db -> fetch_all_array($sql_snacks);
						$total_default_snacks = count($result_default_snacks);
				
						$default_snacks = array();
				
						for ($i = 0; $i < $total_default_snacks; $i++) {
							$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['name'] = $result_default_snacks[$i]['name'];
							$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['qty'] = $result_default_snacks[$i]['qty'];
						}
				
						for ($day = 1; $day <= $_SESSION['choose_your_meal_plan']['no_of_days']; $day++) {
				
							$email_content .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:20px 0">
						            <tr>
						                <td width="15%" align="left" valign="top" style="padding:5px"><div style="float:left; width:100px; height:100px; background:#e8e8e8; color:#575757; font:bold 20px/100px Arial, Helvetica, sans-serif; text-align:center; border:1px solid #e8e8e8;">Day ' . $day . '</div></td>
						                <td width="85%" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0">
						                  ';
				
							for ($time = 1; $time <= $_SESSION['choose_your_meal_plan']['meal_per_day']; $time++) {
								$email_content .= '<tr>
						                      <td align="left" valign="top" width="20%" style="padding:5px"><strong>Meal ' . $time . '  :</strong></td>
						                      <td width="80%" style="padding:5px">' . $default_meals[$day][$time]['meal_name'] . '(<i>' . $default_meals[$day][$time]['meal_size'] . 'g</i>)</td>
						                    </tr>';
							}
				
							for ($time = 1; $time <= $_SESSION['choose_your_meal_plan']['snack_per_day']; $time++) {
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
			$db -> query("update orders set  meal_plan_category_id='" . intval($_SESSION['choose_your_meal_plan']['meal_plan_category_id']) . "', order_email_content='" . addslashes($email_content) . "' where id='" . intval($order_id) . "'");
			
			$_SESSION['user_message'] = "Your next week  order has been modified!";
			$general_func -> header_redirect($general_func -> site_url . "order-listing/");
		}
	}
	
} else{
	$total_price = 0;
	$no_of_days = 0;
	$meal_per_day = 0;
	$snack_per_day = 0;
	
	$category_meal_plan=mysql_result(mysql_query("select meal_plan_id from order_meals where order_id='" . $order_id . "' limit 1"),0,0);

	$sql_meals = "select which_day,meal_time,meal_size,meal_id,show_nutritional_price,(select meal_price from meals_sizes_prices where meal_id=d.meal_id and meal_size=d.meal_size) as price,m.name,details,photo_name,energy,calories,protein,fat_total,carbohydrates,carbs_veggies,with_or_without_sauce from meal_plan_meals d left join meals m on d.meal_id=m.id where d.meal_plan_id='" . intval($category_meal_plan) . "' and type=1 order by which_day,meal_time ASC";
	$result_default_meals = $db -> fetch_all_array($sql_meals);
	$total_default_meals = count($result_default_meals);
	$default_meals = array();
	for ($i = 0; $i < $total_default_meals; $i++) {
			
		if ($result_default_meals[$i]['which_day'] > $no_of_days)
			$no_of_days = $result_default_meals[$i]['which_day'];

		if ($result_default_meals[$i]['meal_time'] > $meal_per_day)
			$meal_per_day = $result_default_meals[$i]['meal_time'];
							
			
		
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

		$total_price += $result_default_meals[$i]['price'];
	}

	$sql_snacks = "select which_day,meal_time,meal_id,price,name,details,photo_name,meal_size as qty from meal_plan_meals  d left join snacks s on d.meal_id=s.id where d.meal_plan_id='" . intval($category_meal_plan) . "' and type=2 order by which_day,meal_time ASC";
	$result_default_snacks = $db -> fetch_all_array($sql_snacks);
	$total_default_snacks = count($result_default_snacks);

	$default_snacks = array();

	for ($i = 0; $i < $total_default_snacks; $i++) {
		
		if ($result_default_snacks[$i]['meal_time'] > $snack_per_day)
			$snack_per_day = $result_default_snacks[$i]['meal_time'];
		
		$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['snack_id'] = $result_default_snacks[$i]['meal_id'];
		$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['price'] = $result_default_snacks[$i]['price'];
		$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['name'] = $result_default_snacks[$i]['name'];
		$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['details'] = $result_default_snacks[$i]['details'];
		$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['photo_name'] = $result_default_snacks[$i]['photo_name'];
		$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['qty'] = $result_default_snacks[$i]['qty'];
		$total_price += $result_default_snacks[$i]['price'] * $result_default_snacks[$i]['qty'];
	}
}

//print_r ($_SESSION['choose_your_meal_plan']);


$sql_content="select select_meal_plan_page_left_heading,select_meal_plan_page_left_content,select_meal_plan_page_right_heading,select_meal_plan_page_right_content,set_meal_plan_modification from dynamic_pages where id=1 limit 1";
$result_content=$db->fetch_all_array($sql_content);


unset($_SESSION['choose_your_meal_plan']);

?>

<script type="text/javascript">
	$(document).ready(function() {
		$(".getStdPnl2_container").slideToggle(1000);
		$(".dayPnl1_new li").mouseenter(function() {
			$(this).find(".tip_box").show();
		});

		$(".dayPnl1_new li").mouseleave(function() {
			$(this).find(".tip_box").hide();
		});

		$(".close_pop").click(function() {
			$(this).parent().parent().find(".tip_box").hide();
		});
	})
	function slideonlyone(thechosenone) {
		$('.accordion_content').each(function(index) {
			if($(this).attr("id") == thechosenone) {
				$(this).slideToggle(200);
				$(this).parent().find('.dayPnl2').toggleClass('active');
			} else {
				$(this).slideUp(200);
				$(this).parent().find('.dayPnl2').removeClass('active');
			}
		});
	}

</script>

<script type="text/javascript">
	function show_meal_plan_meals(val) {

		var error = 0;
		if(document.getElementById("meal_plan_category_id").value == '') {
			document.getElementById("meal_plan_category_id").style.border = "1px solid red";
			error++;
		} else {
			document.getElementById("meal_plan_category_id").style.border = "1px solid #CBD2BB";
		}

		if(document.getElementById("category_meal_plan").value == '') {
			document.getElementById("category_meal_plan").style.border = "1px solid red";
			error++;
		} else {
			document.getElementById("category_meal_plan").style.border = "1px solid #CBD2BB";
		}

		if(error > 0) {
			return false;
		} else {
			$("#plan_meals").slideToggle(1500);
			$.get("modify-meal-plan-meals.php?meal_plan_category_id=" + document.getElementById("meal_plan_category_id").value + "&category_meal_plan=" + document.getElementById("category_meal_plan").value, function(data) {

				$("#plan_meals").html(data);
				$("#plan_meals").slideToggle(1500);
			});
		}
	}

	function collect_meal_plans(val) {
		$("#plan_meals").html('<p><?=$result_content[0]['select_meal_plan_page_right_content']?></p>');
		document.getElementById('category_meal_plan').options.length = 0;
		document.getElementById("category_meal_plan").options[0] = new Option("Choose your meal plan", "");

		if(parseInt(val) > 0) {
			$.get("meal-categorywise-meal-plans.php?id=" + val, function(data) {
				document.getElementById("category_meal_plan").options[0] = new Option("Choose your meal plan", "");
				
				var return_data_array = data.split("-_-");				
				
				var return_data = return_data_array[1].split("#!");
				var length = return_data.length - 1;

				for(var i = 0; i < length; i++) {
					var options_value = return_data[i].split("~_~");
					document.getElementById("category_meal_plan").options[i + 1] = new Option(options_value[1], options_value[0]);
				}
				
				if(return_data_array[0] == 1){
					$("#training_time_id").show(1000);					
				}else{
					$("#training_time_id").hide(1000);				
				}
				
			});
		}
	}

	function decide_training_part(val) {
		if(parseInt(val) == 1)
			$("#div_part_of_the_day_usually_train").show(1000);
		else
			$("#div_part_of_the_day_usually_train").hide(1000);
	}

</script>

<div class="inrBnr">
	<?php $db_common -> static_page_banner($dynamic_content['page_id']); ?>
</div>
<div class="mealPln right_pop">
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
																				<?=$db_common->nutritional_value($default_meals[$day][$time]['energy'],$default_meals[$day][$time]['meal_size']) ?> kcal
																			</div>
																		</div>
																		<div class="tip_column_info_row">
																			<div class="info_tab">
																				Protein :
																			</div>
																			<div class="info_tab">
																				<?=$db_common->nutritional_value($default_meals[$day][$time]['protein'],$default_meals[$day][$time]['meal_size'])?>g
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
																					<div class="info_tab">Calories :</div>
																					<div class="info_tab"><?=$db_common->nutritional_value($default_meals[$day][$time]['calories'],$default_meals[$day][$time]['meal_size'])?>g</div>
																				</div>
																				
																				<div class="tip_column_info_row">
																					<div class="info_tab">
																						Carbs :
																					</div>
																					<div class="info_tab">
																						<?=$db_common->nutritional_value($default_meals[$day][$time]['carbohydrates'],$default_meals[$day][$time]['meal_size']) ?>g
																					</div>
																				</div>
																				<div class="tip_column_info_row">
																					<div class="info_tab">
																						Total Fat :
																					</div>
																					<div class="info_tab">
																						<?=$db_common->nutritional_value($default_meals[$day][$time]['fat_total'],$default_meals[$day][$time]['meal_size']) ?>g
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
															 
                                                            <p style="color: #ff0000;font: 14px/18px 'open_sansregular';text-align: center; padding:20px 0 10px;"><?php echo $result_content[0]['set_meal_plan_modification']; ?></p>
							                                <div class="dayPnlBtn" style="margin-top:0">	
																<input name="button" type="button" value="Modify Meal Plan" onclick="location.href='<?=$general_func->site_url?>modify-customize-meal-plan/<?=$order_id?>'" style="width:90%; margin:0 5%" class="mdfyMealPln" />
															</div>
																						
															<?php }else{																
																 echo '<p>' . nl2br($result_content[0]['select_meal_plan_page_right_content']).'</p>'; 
																}
															?>
														</div>
													</div>
													</form>
		</div>										</div>
											</div>
											<?php
											include_once ("includes/footer.php");
											?>