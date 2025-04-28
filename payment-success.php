<?php
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
include_once("includes/configuration.php");



$user_can_download_pdf=0;
$part_of_the_day_usually_train=5;

$sql_user = "select * from users where id=" . intval($_SESSION['user_id']) . " limit 1";
$result_user = $db -> fetch_all_array($sql_user);

$clNo = 3000 + $_SESSION['user_id'];


$payment_status=0;


//if (isset($_GET['Result']) && strtolower(trim($_GET['Result'])) == "s") {
	
	if(intval($result_user[0]['cc_or_dd_created']) == 0){
		//**************************  collect cc_or_dd token *************************//
		$edTKI_url = "https://www.edebit.com.au/IS/edTKI.ashx?cd_crn=" . $edNo . "-" . $clNo;
		$ch = curl_init($edTKI_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$edTKI_data = curl_exec($ch);
		curl_close($ch);
		
		$return_data=array();
		$return_data=@explode("&", $edTKI_data);
		
		$token_data=array();
		$token_data=@explode("=", $return_data[0]);
		
		$update_query="cc_or_dd_created=1,cc_or_dd='" . $_SESSION['payment']['accountType'] . "'";
		
		if($token_data[1] != NULL){
			$update_query .= " ,debit_token='" . trim($token_data[1]) . "' ";	
		}		
		//****************************************************************************//		
		$db -> query("update users set $update_query where id='" . intval($_SESSION['user_id']) . "'");
	}	
		
	$order_type = 1;

	if (isset($_SESSION['fill_the_questionnaire']) && count($_SESSION['fill_the_questionnaire']) > 0) {
		$order_type = 2;
	} else if (isset($_SESSION['choose_your_meal_plan']) && count($_SESSION['choose_your_meal_plan']) > 0) {
		$order_type = 1;
	} else if (isset($_SESSION['customize_your_meal_plan']) && count($_SESSION['customize_your_meal_plan']) > 0) {
		$order_type = 3;
	} else {
		$general_func -> header_redirect($general_func -> site_url . "get-started/");
	}

	$result_suburb_info = $db -> fetch_all_array("select suburb_name,suburb_state,delivery_cost,suburb_postcode,delivery_day,payment_debit_day,order_cutoff_day,order_cutoff_time from suburb where id=" . intval($result_user[0]['suburb_id']) . " limit 1");
	$delivery_day = $general_func -> day_name($result_suburb_info[0]['delivery_day']);
	$payment_debit_day = $general_func -> day_name($result_suburb_info[0]['payment_debit_day']);
	$order_cutoff_day = $general_func -> day_name($result_suburb_info[0]['order_cutoff_day']) . " " . date("h:i A", strtotime($result_suburb_info[0]['order_cutoff_time']));
		
	//**************** place order into database ************************************//
	$data = array();
	$data['user_id'] = intval($_SESSION['user_id']);
	$data['order_type'] = $order_type;
	$data['order_amount'] = $_SESSION['payment']['total_price'];
	$data['order_start_date'] = date("Y-m-d", trim($_SESSION['payment']['order_start_date']));

	$data['program_length'] = intval($_SESSION['payment']['program_length']);
	$data['pickup_delivery'] = intval($_SESSION['payment']['pickup_delivery']);
	$data['pickup_location_id'] = intval($_SESSION['payment']['pickup_location_id']);
	$data['training_cost'] = $general_func->meal_plan_amout_for_training_cost;
	
	if(isset($_SESSION['payment']['promo_code']) && isset($_SESSION['payment']['promo_amount']) && isset($_SESSION['payment']['how_many_week']) && isset($_SESSION['payment']['promo_text']) ){
		$data['used_promo_code'] = trim($_SESSION['payment']['promo_code']);		
		$data['promo_amount'] = trim($_SESSION['payment']['promo_amount']);	
		$data['promo_text'] = trim($_SESSION['payment']['promo_text']);
		$data['how_many_week'] = trim($_SESSION['payment']['how_many_week']);	
	}
	
	$data['notes'] = $_SESSION['payment']['delivery_notes'];	
	$data['status'] = 0;
	$data['date_ordered'] = $current_date_time;
	$order_id = $db -> query_insert("orders", $data);
	//*******************************************************************************//
	
	//****************  if order week greater than 0 *********************************//
	if(intval($_SESSION['payment']['program_length']) > 0){
		$minimum_order_weeks=mysql_result(mysql_query("select minimum_order_weeks from discounts where id='" . intval($_SESSION['payment']['program_length']) . "' limit 1"), 0,0);		
		mysql_query("update users set minimum_order_weeks='" . $minimum_order_weeks ."' where id='" . intval($_SESSION['user_id']). "'");
	}
	//*******************************************************************************//
	
	$additional="";
	
	if(isset($_SESSION['payment']['program_length']) && intval($_SESSION['payment']['program_length']) > 0){
			$rs_program_length = $db->fetch_all_array("select name,details from discounts where id='" . intval($_SESSION['payment']['program_length']). "' limit 1");
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
		
		if(isset($_SESSION['payment']['pickup_delivery']) && intval($_SESSION['payment']['pickup_delivery']) == 2){
			$rs_location = $db->fetch_all_array("select location,address,pickup_timing from pickup_locations where id='" . intval($_SESSION['payment']['pickup_location_id']). "' limit 1");
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
		  	

	if ($order_type == 1) {//******* select your meal plan

		$sql_order_meals = "INSERT INTO order_meals(order_id,user_id,meal_plan_id,meal_id,which_day,meal_time,meal_size,type) VALUES";

		$sql_meals = "select meal_id,which_day,meal_time,meal_size,type from meal_plan_meals where meal_plan_id='" . intval($_SESSION['choose_your_meal_plan']['category_meal_plan']) . "' order by which_day,meal_time ASC";
		$result_default_meals = $db -> fetch_all_array($sql_meals);
		$total_default_meals = count($result_default_meals);

		for ($i = 0; $i < $total_default_meals; $i++) {
			$sql_order_meals .= "('" . $order_id . "','" . intval($_SESSION['user_id']) . "','" . intval($_SESSION['choose_your_meal_plan']['category_meal_plan']) . "','" . $result_default_meals[$i]['meal_id'] . "','" . $result_default_meals[$i]['which_day'] . "','" . $result_default_meals[$i]['meal_time'] . "','" . $result_default_meals[$i]['meal_size'] . "','" . $result_default_meals[$i]['type'] . "'), ";
		}

		$sql_order_meals = substr($sql_order_meals, 0, -2) . ";";
		$db -> query($sql_order_meals);

		
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
		            Name :   ' . $result_user[0]['fname'] . ' ' . $result_user[0]['lname'] . '<br />
		            Address : ' . $result_user[0]['street_address'] . '<br />
		            Suburbs :   ' . $result_suburb_info[0]['suburb_name'] . ', ' . $result_suburb_info[0]['suburb_state'] . ', ' . $result_suburb_info[0]['suburb_postcode'] . '<br />
		           </td>
		          <td align="left" valign="top" style="padding:10px">Email : <a  style="color:#333; text-decoration:none">' . $result_user[0]['email_address'] . '</a><br />
		            Mobile : ' . $result_user[0]['phone'] . '</td>
		        </tr>		        
				
		        <tr>
		          <td width="50%" align="left" valign="top" style="padding:10px"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Order Details</h3>
		            Order No :  FNF - A000' . $order_id . '<br />		           
		            Order Amount :  $' . number_format($_SESSION['payment']['total_price'], 2) . ' p/w (GST ' . $GST . '% included) </td>
		          <td align="left" valign="top" style="padding:10px">Cut off Date & Time :   ' . $order_cutoff_day . '<br />
		           Payment Debit Day :  ' . $payment_debit_day . '<br />';
				   
					if($_SESSION['payment']['pickup_delivery']== 1){
				   		$email_content .= 'Delivery Date :' . $delivery_day . ' &nbsp;';
					}
				   
		            $email_content .= '</td>
		        </tr>
		      </table></td>
		  </tr>';
		  
		  
		  if(isset($_SESSION['payment']['promo_code']) && isset($_SESSION['payment']['promo_amount']) && isset($_SESSION['payment']['how_many_week']) && isset($_SESSION['payment']['promo_text']) ){
		  	 $email_content .= '  <span><tr>
			    <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0 0 10px">
			        <tr>
			          <td width="100%" align="left" valign="top" style="padding:10px"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Promo Code</h3>
			           	Used Promo Code :  ' . $_SESSION['payment']['promo_code'] . '<br />		           
		              Discount/Offer :  ' . $_SESSION['payment']['promo_text'] . '
			           </td>		         
			        </tr> 
			      </table></td>
			  </tr>  <span>';	
		  }
		  
		  
		  $email_content .= ' <tr>
		    <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0 0 10px">
		        <tr>
		          <td width="100%" align="left" valign="top" style="padding:10px"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Additional Delivery Notes</h3>
		           ' . nl2br($_SESSION['payment']['delivery_notes']) . '
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
		$recipient_info['recipient_subject'] = $general_func -> site_title . " order details";
		$recipient_info['recipient_content'] = $email_content;
		$recipient_info['recipient_email'] = $result_user[0]['email_address'];
		$sendmail -> send_email($recipient_info, $general_func -> email, $general_func -> site_title, $general_func -> site_url);
		//*****************************************************************************************************//
		
		$user_can_download_pdf = mysql_result(mysql_query("select user_can_download_pdf from meal_plan_category where id='" . $_SESSION['choose_your_meal_plan']['meal_plan_category_id']. "' limit 1"),0,0);
		
		mysql_query("update orders set meal_plan_category_id='" . intval($_SESSION['choose_your_meal_plan']['meal_plan_category_id']) . "' ,order_email_content='" . addslashes($email_content) . "' where id='" . intval($order_id) . "'");
		

		if(strtolower(trim($_SESSION['choose_your_meal_plan']['exercising_to_speed_up'])) == "yes"){
			if(trim($_SESSION['choose_your_meal_plan']['part_of_the_day_usually_train']) =="Morning")
				$part_of_the_day_usually_train=1;	
			else if(trim($_SESSION['choose_your_meal_plan']['part_of_the_day_usually_train']) =="Lunch Time")
				$part_of_the_day_usually_train=2;		
			else if(trim($_SESSION['choose_your_meal_plan']['part_of_the_day_usually_train']) =="After Work")
				$part_of_the_day_usually_train=3;		
			else if(trim($_SESSION['choose_your_meal_plan']['part_of_the_day_usually_train']) =="Evening")
				$part_of_the_day_usually_train=4;	
			else
				$part_of_the_day_usually_train=5;
		}
	}else if ($order_type == 3) { //******* customize your own

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
		            Name :   ' . $result_user[0]['fname'] . ' ' . $result_user[0]['lname'] . '<br />
		            Address : ' . $result_user[0]['street_address'] . '<br />
		            Suburbs :   ' . $result_suburb_info[0]['suburb_name'] . ', ' . $result_suburb_info[0]['suburb_state'] . ', ' . $result_suburb_info[0]['suburb_postcode'] . '<br />
		           </td>
		          <td align="left" valign="top" style="padding:10px">Email : <a  style="color:#333; text-decoration:none">' . $result_user[0]['email_address'] . '</a><br />
		            Mobile : ' . $result_user[0]['phone'] . '</td>
		        </tr>		        
				
		        <tr>
		          <td width="50%" align="left" valign="top" style="padding:10px"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Order Details</h3>
		            Order No :  FNF - A000' . $order_id . '<br />		           
		            Order Amount :  $' . number_format($_SESSION['payment']['total_price'], 2) . ' p/w (GST ' . $GST . '% included)</td>
		          <td align="left" valign="top" style="padding:10px">Cut off Date & Time :   ' . $order_cutoff_day . '<br />
		           Payment Debit Day :  ' . $payment_debit_day . '<br />';
				   
				   if($_SESSION['payment']['pickup_delivery']== 1){
				   		$email_content .= 'Delivery Date :' . $delivery_day . ' &nbsp;';
					}
				   
		            $email_content .= '</td>
		        </tr>
		      </table></td>
		  </tr>';
			
		  if(isset($_SESSION['payment']['promo_code']) && isset($_SESSION['payment']['promo_amount']) && isset($_SESSION['payment']['how_many_week']) && isset($_SESSION['payment']['promo_text']) ){
		  	 $email_content .= '  <span><tr>
			    <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0 0 10px">
			        <tr>
			          <td width="100%" align="left" valign="top" style="padding:10px"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Promo Code</h3>
			           	Used Promo Code :  ' . $_SESSION['payment']['promo_code'] . '<br />		           
		              Discount/Offer :  ' . $_SESSION['payment']['promo_text'] . '
			           </td>		         
			        </tr> 
			      </table></td>
			  </tr>  <span>';	
		  }
		  		   
		           
				   
				
		  $email_content .= ' <tr>
		    <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0 0 10px">
		        <tr>
		          <td width="100%" align="left" valign="top" style="padding:10px"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Additional Delivery Notes</h3>
		           ' . nl2br($_SESSION['payment']['delivery_notes']) . '
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
					
				$sql_meals = "select name from meals where  id='" . intval($_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_id']) . "' limit 1";
				$result_default_meals = $db -> fetch_all_array($sql_meals);
								
				$email_content .= '<tr>
		                      <td align="left" valign="top" width="20%" style="padding:5px"><strong>Meal ' . $time . '  :</strong></td>
		                      <td width="80%" style="padding:5px">' . $result_default_meals[0]['name'] . '(<i>' . $_SESSION['customize_your_meal_plan']['customized_meal'][$day][$time]['meal_size'] . 'g</i>)</td>
		                    </tr>';
			}
			
			for($time=1; $time <= $_SESSION['customize_your_meal_plan']['snack_per_day']; $time++ ){
				$sql_snacks = "select name from  snacks where id='" . intval($_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_id']) . "' limit 1";
				$result_snacks = $db -> fetch_all_array($sql_snacks);			
				$email_content .= '<tr>
		                      <td align="left" valign="top" width="20%" style="padding:5px"><strong>Snack ' . $time . ' :</strong></td>
		                      <td width="80%" style="padding:5px">' . $result_snacks[0]['name'] . '(<i>Qty :' . intval($_SESSION['customize_your_meal_plan']['customized_snacks'][$day][$time]['snack_qty']) . '</i>)</td>
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
		$recipient_info['recipient_subject'] = $general_func -> site_title . " order details";
		$recipient_info['recipient_content'] = $email_content;
		$recipient_info['recipient_email'] = $result_user[0]['email_address'];
		$sendmail -> send_email($recipient_info, $general_func -> email, $general_func -> site_title, $general_func -> site_url);
		//*****************************************************************************************************//
		
		$user_can_download_pdf = mysql_result(mysql_query("select user_can_download_pdf from meal_plan_category where id='" . $_SESSION['customize_your_meal_plan']['meal_plan_category_id']. "' limit 1"),0,0);
		mysql_query("update orders set meal_plan_category_id='" . intval($_SESSION['customize_your_meal_plan']['meal_plan_category_id']) . "' ,order_email_content='" . addslashes($email_content) . "' where id='" . intval($order_id) . "'");

		if(strtolower(trim($_SESSION['customize_your_meal_plan']['exercising_to_speed_up'])) == "yes"){
			if(trim($_SESSION['customize_your_meal_plan']['part_of_the_day_usually_train']) =="Morning")
				$part_of_the_day_usually_train=1;	
			else if(trim($_SESSION['customize_your_meal_plan']['part_of_the_day_usually_train']) =="Lunch Time")
				$part_of_the_day_usually_train=2;		
			else if(trim($_SESSION['customize_your_meal_plan']['part_of_the_day_usually_train']) =="After Work")
				$part_of_the_day_usually_train=3;		
			else if(trim($_SESSION['customize_your_meal_plan']['part_of_the_day_usually_train']) =="Evening")
				$part_of_the_day_usually_train=4;	
			else
				$part_of_the_day_usually_train=5;
		}
	}else if($order_type == 2){
			
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
		            Name :   ' . $result_user[0]['fname'] . ' ' . $result_user[0]['lname'] . '<br />
		            Address : ' . $result_user[0]['street_address'] . '<br />
		            Suburbs :   ' . $result_suburb_info[0]['suburb_name'] . ', ' . $result_suburb_info[0]['suburb_state'] . ', ' . $result_suburb_info[0]['suburb_postcode'] . '<br />
		           </td>
		          <td align="left" valign="top" style="padding:10px">Email : <a  style="color:#333; text-decoration:none">' . $result_user[0]['email_address'] . '</a><br />
		            Mobile : ' . $result_user[0]['phone'] . '</td>
		        </tr>		        
				
		        <tr>
		          <td width="50%" align="left" valign="top" style="padding:10px"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Order Details</h3>
		            Order No :  FNF - A000' . $order_id . '<br />		            
		            Order Amount :  $' . number_format($_SESSION['payment']['total_price'], 2) . ' p/w (GST ' . $GST . '% included)</td>
		          <td align="left" valign="top" style="padding:10px">Cut off Date & Time :   ' . $order_cutoff_day . '<br />
		           Payment Debit Day :  ' . $payment_debit_day . '<br />';
				   
				    if($_SESSION['payment']['pickup_delivery']== 1){
				   		$email_content .= 'Delivery Date :' . $delivery_day . ' &nbsp;';
					}
				   
		            $email_content .= '</td>
		        </tr>
		      </table></td>
		  </tr>';
		     
		 if(isset($_SESSION['payment']['promo_code']) && isset($_SESSION['payment']['promo_amount']) && isset($_SESSION['payment']['how_many_week']) && isset($_SESSION['payment']['promo_text']) ){
		  	 $email_content .= '  <span><tr>
			    <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0 0 10px">
			        <tr>
			          <td width="100%" align="left" valign="top" style="padding:10px"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Promo Code</h3>
			           	Used Promo Code :  ' . $_SESSION['payment']['promo_code'] . '<br />		           
		              Discount/Offer :  ' . $_SESSION['payment']['promo_text'] . '
			           </td>		         
			        </tr> 
			      </table></td>
			  </tr>  <span>';	
		  }
		     
		  
		  $email_content .= ' <tr>
		    <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0 0 10px">
		        <tr>
		          <td width="100%" align="left" valign="top" style="padding:10px"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Additional Delivery Notes</h3>
		           ' . nl2br($_SESSION['payment']['delivery_notes']) . '
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
		$recipient_info['recipient_subject'] = $general_func -> site_title . " order details";
		$recipient_info['recipient_content'] = $email_content;
		$recipient_info['recipient_email'] = $result_user[0]['email_address'];
		$sendmail -> send_email($recipient_info, $general_func -> email, $general_func -> site_title, $general_func -> site_url);
		//*****************************************************************************************************//
		
		$user_can_download_pdf = $_SESSION['fill_the_questionnaire']['user_can_download_pdf'];

		mysql_query("update orders set meal_plan_category_id='" . intval($_SESSION['fill_the_questionnaire']['meal_plan_category_id']) . "' ,order_email_content='" . addslashes($email_content) . "' where id='" . intval($order_id) . "'");

		if(strtolower(trim($_SESSION['fill_the_questionnaire']['exercising_to_speed_up'])) == "yes"){
			if(trim($_SESSION['fill_the_questionnaire']['part_of_the_day_usually_train']) =="Morning")
				$part_of_the_day_usually_train=1;	
			else if(trim($_SESSION['fill_the_questionnaire']['part_of_the_day_usually_train']) =="Lunch Time")
				$part_of_the_day_usually_train=2;		
			else if(trim($_SESSION['fill_the_questionnaire']['part_of_the_day_usually_train']) =="After Work")
				$part_of_the_day_usually_train=3;		
			else if(trim($_SESSION['fill_the_questionnaire']['part_of_the_day_usually_train']) =="Evening")
				$part_of_the_day_usually_train=4;	
			else
				$part_of_the_day_usually_train=5;
		}		
	}

	$payment_status=1;
//} 


$pdf_file_name=mysql_result(mysql_query("select pdf_file_name from meal_schedule_pdf where eating_schedule='" . $part_of_the_day_usually_train. "' limit 1"),0,0);

?>
 <link href="css/reset.css" rel="stylesheet" type="text/css">
  <link href="css/fonts.css" rel="stylesheet" type="text/css">
  <link href="css/style.css" rel="stylesheet" type="text/css">
  <link href="css/responsive.css" rel="stylesheet" type="text/css">
  <link href="css/font-awesome.css" rel="stylesheet" type="text/css">

	<?php if($payment_status ==1 ){ ?>
		<div class="new_paymentInfo">
		<p>Thank you for placing your order with <?=$general_func -> site_title?>. Your order number is FNF - A000<?=$order_id?> that you can use for further assistance.</p>
			<p>A copy of your order details has been sent at <?=$result_user[0]['email_address']?>. </p>
			<p> You can also do the following things from your dashboard section.
				<ul class="lstStlOne">
					<!--<li>Resend your order copy at <?=$result_user[0]['email_address']?>.</li>-->
					<li>Take a printout copy of your order.</li>
					<?php if($user_can_download_pdf == 1 && trim($pdf_file_name) != NULL){ ?>
					<li>Download your eating schedule copy.</li>
					<?php } ?>
					<!--<li>Modify, hold and cancelled your order.</li>-->
					<li>Hold and cancel your current order and recreate another order again.</li>
					<li>Modify your personal information.</li>
					<li>Update your <?=$_SESSION['payment']['accountType']=="DD"?'bank account':'credit card';?> information.</li>
				</ul>
			</p>
			
			<?php
		if($user_can_download_pdf == 1 && trim($pdf_file_name) != NULL){ ?>
			
				<p>Please do remember to <a>download</a> your eating schedule PDF copy.</p>	
			
		<?php }  ?>		
		
		<?php		
		
		if (isset($_SESSION['fill_the_questionnaire']))
			unset($_SESSION['fill_the_questionnaire']);	
		
		
		if (isset($_SESSION['choose_your_meal_plan']))
			unset($_SESSION['choose_your_meal_plan']);	
		
		if (isset($_SESSION['customize_your_meal_plan']))
			unset($_SESSION['customize_your_meal_plan']);	
			
		if (isset($_SESSION['payment']))
			unset($_SESSION['payment']); 
		
		?>	
		</div>
		
		
		<div style="float:right; border:none;" class="checkout_row">
			<input  type="button" value="My Account" name="myaccount" onclick="window.open('<?=$general_func -> site_url?>my-account/', '_top');" > 
		<?php
		if($user_can_download_pdf == 1 && trim($pdf_file_name) != NULL){
			$path_PDF=$general_func -> site_url."eating_schedule/".trim($pdf_file_name);
			 ?>
		
			<input type="button" value="Download Eating Schedule" name="download" style="width:auto; float:right" onclick="window.open('<?=$path_PDF?>', '_blank');">
		</div>
		<?php }		
	 }else{  ?>
	 	<div class="new_paymentInfo">
		<p>Sorry, your order was not completed. please try again.</p>
		<br class="clear">
		<div style="float:right; border:none;" class="checkout_row">
			<input  type="button" value="Continue" name="continue" onclick="window.open('<?=$general_func -> site_url?>payment/', '_top');" > 
		</div>	
		</div>	
	<?php }	?>		
	