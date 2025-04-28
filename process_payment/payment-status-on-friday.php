<?php
error_reporting(0);
/*****************************************************/
/********** 1 = Monday & 7 = Sunday
 * Week 47 is from Monday November 17, 2014 until (and including) Sunday November 23, 2014
/*****************************************************/
include_once("../includes/configuration.php");
$todays_date=date("Y-m-d");

$first_date_of_the_week=date("Y-m-d",strtotime('monday this week'));
$last_date_of_the_week=date("Y-m-d",strtotime('sunday this week'));	

$sql="select p.id as payment_id,p.user_id as user_id,phone,o.order_amount,order_id,week_start_date,week_end_date,CONCAT(fname,' ',lname) as user_name,street_address,email_address,refered_code,notes,order_email_content from payment p";
$sql .=" left join users u on p.user_id=u.id left join orders o on p.order_id=o.id where order_status=0 and DATE(payment_date) >= '". $first_date_of_the_week ."' and DATE(payment_date) <= '". $last_date_of_the_week ."'";

$result=$db->fetch_all_array($sql);
$total_users=count($result);


for($user=0; $user < $total_users; $user++ ){
	
	$edTStat_url = "https://www.edebit.com.au/IS/edTStat.ashx?edNo=" . $edNo . "&id=" . $result[$user]['payment_id'];
	$ch = curl_init($edTStat_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$edTStat_data = curl_exec($ch);
	curl_close($ch); 
	
	$status_data=array();
	$status_data=explode("&",$edTStat_data);
	
	$status_state=array();
	$status_data=explode("=",$status_data[0]);
	
	if(trim($status_data[1]) != NULL &&  strtolower(trim($status_data[1])) == "successful"){//********** Success			
		mysql_query("update  payment set order_status=1 where id='" . $result[$user]['payment_id'] . "'");
		mysql_query("update  orders set status=1 where id='" . $result[$user]['order_id'] . "' and current_order=1");		
		//******************  Send Email to admin and user ********************************//
		
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
		            Name : ' . $result[$user]['user_name'] . '<br />
		            Address : ' . $result[$user]['street_address'] . '</td>
		          <td align="left" valign="top" style="padding:10px">Email : <a  style="color:#333; text-decoration:none">' . $result[$user]['email_address'] . '</a><br />
		            Mobile : ' . $result[$user]['phone'] . '</td>
		        </tr>
		        <tr>
		          <td width="50%" align="left" valign="top" style="padding:10px"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Order Details</h3>
		            Order No :  FNF - A000' . $result[$user]['order_id'] . '<br />
		            Order Amount :  $' . $result[$user]['order_amount'] . '<br />
		            Order Status :  Successful </td>
		            
		          <td align="left" valign="top" style="padding:10px">Order Week :  ' . date("jS M, Y ",strtotime($result[$user]['week_start_date'])) . ' - ' . date("jS M, Y ",strtotime($result[$user]['week_end_date'])) . '
		          <br />  Payment On:  ' . date("jS M, Y ",strtotime($todays_date)) . '<br />
		           </td>
		        </tr>
		      </table></td>
		  </tr>  
		   <tr>
			    <td align="center" valign="middle" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; padding:20px 0">&copy; Copyright ' . date("Y") . ' Fit "N" Food</td>
			</tr>
		</table>';
		
		
		$recipient_info = array();
		$recipient_info['recipient_subject'] = "Your payment has been made at " . $general_func -> site_title . " Site for this week";
		$recipient_info['recipient_content'] = $email_content;
		$recipient_info['recipient_email'] = $result[$user]['email_address'];
		$sendmail -> payment_success_send_email($recipient_info, $general_func -> email, $general_func -> site_title, $general_func -> site_url);
		//********************************************************************************//	
		
		//*********************  trainer commission ****************************************//
		if(trim($result[$user]['refered_code']) != NULL){
			//if(mysql_num_rows(mysql_query("select id from trainers_reference where refered_code='" . $result[$user]['refered_code'] . "' and user_id='" . $result[$user]['user_id'] . "' limit 1")) == 0){
			$trainer_type=mysql_result(mysql_query("select trainer_type from trainers where refered_code='" . $result[$user]['refered_code'] . "' limit 1"),0,0);		
			
			if($trainer_type == 1)
				$referral_commission=$general_func->gym_referral_commission;
			else
				$referral_commission=$general_func->trainer_referral_commission;
					
			mysql_query("INSERT INTO trainers_reference(refered_code,user_id,trainer_type,referral_commission) VALUES('" . $result[$user]['refered_code'] . "','" . $result[$user]['user_id'] . "','" . $trainer_type . "','" . $referral_commission . "')");	
			mysql_query("update trainers set total_referral_commission=total_referral_commission + '" . $referral_commission . "' where refered_code='" . $result[$user]['refered_code'] . "'");
			//}
		}
		//**********************************************************************************//
		
			
	}else if(trim($status_data[1]) != NULL &&  strtolower(trim($status_data[1])) == "failed"){//********** failed
		mysql_query("update  payment set order_status=2 where id='" . $result[$user]['payment_id'] . "'");			
		//******************  Send Email to admin and user ********************************//		
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
		            Name : ' . $result[$user]['user_name'] . '<br />
		            Address : ' . $result[$user]['street_address'] . '</td>
		          <td align="left" valign="top" style="padding:10px">Email : <a  style="color:#333; text-decoration:none">' . $result[$user]['email_address'] . '</a><br />
		            Mobile : ' . $result[$user]['phone'] . '</td>
		        </tr>
		        <tr>
		          <td width="50%" align="left" valign="top" style="padding:10px"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Order Details</h3>
		            Order No :  FNF - A000' . $result[$user]['order_id'] . '<br />
		            Order Amount :  $' . $result[$user]['order_amount'] . '<br />
		            Order Status :  Failed </td>
		            
		          <td align="left" valign="top" style="padding:10px">Order Week :  ' . date("jS M, Y ",strtotime($result[$user]['week_start_date'])) . ' - ' . date("jS M, Y ",strtotime($result[$user]['week_end_date'])) . '
		          <br />   Payment On: Failed <br />
		           </td>
		        </tr>
		      </table></td>
		  </tr>  
		   <tr>
			    <td align="center" valign="middle" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; padding:20px 0">&copy; Copyright ' . date("Y") . ' Fit "N" Food</td>
			</tr>
		</table>';
		
		
		$recipient_info = array();
		$recipient_info['recipient_subject'] = "Your payment has been failed at " . $general_func -> site_title . " Site for this week";
		$recipient_info['recipient_content'] = $email_content;
		$recipient_info['recipient_email'] = $result[$user]['email_address'];
		$sendmail -> payment_failed_send_email($recipient_info, $general_func -> email, $general_func -> site_title, $general_func -> site_url);
		//********************************************************************************//		
	}	
}


/* $msg = "Cron file successfully executed for gathering debit status on friday: ". date("d/m/Y h:i:s A");
mail("mailuttam@webgrity.com","debit status gathered",$msg); */
?>