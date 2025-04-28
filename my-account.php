<?php
include_once("includes/header.php");
if(!isset($_SESSION['user_login_type']) || trim($_SESSION['user_login_type']) != "users" || !isset($_SESSION['user_login_type'])){	
	$_SESSION['user_message']="Sorry, you have no permission to access this page!";
	$general_func->header_redirect($general_func->site_url);
}


$sql_user="select CONCAT(fname,' ',lname) as name,email_address,google_photo,street_address,photo,phone,refered_code,edPI_created,cc_or_dd_created,cc_or_dd,debit_token,CONCAT(suburb_name,', ',suburb_postcode,', ',suburb_state) as suburb_name,facebook_id,google_id  from users u left join  suburb s on u.suburb_id=s.id  where u.id='" . $_SESSION['user_id']. "' limit 1";
$result_user_info=$db->fetch_all_array($sql_user);


?>
<div class="inrBnr">
	<?php $db_common->static_page_banner($dynamic_content['page_id']);?>										
</div>
<div class="bodyContent">
	<div class="mainDiv2">
  		<h3>My Account</h3><?php //print ($sql_user); ?>
   		<div class="my_account">
      		<div class="my_account_left">
	        	<div class="my_account_left_img_area">
	        		<?php					
					$display_height="";					
	        		if(trim($result_user_info[0]['photo']) == NULL){
	        			//check whether fb or google user
	        			if($result_user_info[0]['facebook_id']  != NULL || trim($result_user_info[0]['google_id']) != NULL){
	        				if($result_user_info[0]['facebook_id']  != NULL){
	        					$img_path="http://graph.facebook.com/". $result_user_info[0]['facebook_id'] . "/picture?type=large";
	        					list(, $height, , ) = getimagesize($img_path);	
								if($height > 192)
									$display_height='height="192"';	
	        				}else{
	        					$img_path=trim($result_user_info[0]['google_photo']);	
								list(, $height, , ) = getimagesize($img_path);	
								if($height > 192)
									$display_height='height="192"';	
	        				}
							
					   	}else{					   	
							$img_path="images/no-image.jpg";
					   	}
	        		}else{
						$img_path="photo/small/".trim($result_user_info[0]['photo']);
					}		        		
	        		 
	        		?>	        		
	        		<img <?php echo $display_height; ?> src="<?=$img_path?>"  alt="" /></div>
	        	<div class="submit_box"><span></span><input name="" type="submit" value="Update Profile" onclick="location.href='<?=$general_func->site_url?>update-profile/'" /></div>
	      	</div>
			<div class="my_account_right">
				<div class="text_area"><span>Name :</span><p><?=$result_user_info[0]['name']?></p></div>
		    	<div class="text_area"><span>Email :</span><p><?=$result_user_info[0]['email_address']?></p></div>
		    	<div class="text_area"><span>Address :</span><p><?=$result_user_info[0]['street_address']?></p></div>
		        <div class="text_area"><span>Suburb :</span><p><?=$result_user_info[0]['suburb_name']?></p></div>
		        <div class="text_area"><span>Mobile :</span><p><?=$result_user_info[0]['phone']?></p></div>  
		 	</div>
      		<div class="clear"></div>
          	<div class="my_account_button_tab_area">
		        <div class="my_account_button_tab"><a class="update_profile_tab" href="update-profile/">Update Profile</a></div>
		        <?php  if($result_user_info[0]['facebook_id']  == NULL && trim($result_user_info[0]['google_id']) == NULL){ ?>
		        	<div class="my_account_button_tab"><a class="update_password_tab" href="change-password/">Change Password</a></div>
		        <?php  }  ?>
		        <div class="my_account_button_tab"><a class="order_history_tab" href="order-listing/">My orders</a></div>
		        <div class="my_account_button_tab"><a href="payment-history/" class="payment_history_tab">Payment History</a></div>
		        <?php  if($result_user_info[0]['cc_or_dd_created'] == 1 && trim($result_user_info[0]['cc_or_dd']) != NULL){
		        	 		        	
					$accountType = trim($result_user_info[0]['cc_or_dd'])=="DD"?'CC':'DD';
					$returnURL=$general_func->site_url."payment-type-updated.php";
					$_SESSION['account']['current_payment_type']=trim($result_user_info[0]['cc_or_dd']);
					$_SESSION['account']['payment_type_will_be']=$accountType;
					$clNo=3000 + $_SESSION['user_id'];
		        	$_SESSION['account']['change_payment_type']="https://www.edebit.com.au/IS/". trim($accountType) ."Info.aspx?cd_crn=" . $edNo . "-" . $clNo."&returnURL=".$returnURL;
		 			
		        	
		        	?>
		        <div class="my_account_button_tab"><a href="update-cc-dd-info/" class="update_bank_info">Update <?=trim($result_user_info[0]['cc_or_dd'])=="DD"?'Bank Info.':'Credit Card'; ?></a></div>
				<div class="my_account_button_tab"><a href="<?=$general_func->site_url?>change-payment-type.php" class="update_bank_info">Change Payment Type</a></div>	
		        <?php  }  ?>
		        
		       <!--<div class="my_account_button_tab"><a href="call-history/" class="call_history_tab">Call History</a></div>-->
		        
		        <!--<div class="my_account_button_tab"><a class="video_tab">Video</a></div>
		        <div class="my_account_button_tab"><a class="training_tab">Traning</a></div>
		        -->		      
	      	</div>
	      	<?php
	      	if(intval($result_user_info[0]['edPI_created']) == 1 && (intval($result_user_info[0]['cc_or_dd_created']) == 0 || trim($result_user_info[0]['cc_or_dd']) == NULL || trim($result_user_info[0]['debit_token']) == NULL)){
	      		echo ' <p class="complete_profile">Your payment details has not been submitted yet, please <a href="' . $general_func->site_url .'payment-type/" > click here </a> and submit.</p>';
	      	}else if(intval($result_user_info[0]['edPI_created']) == 1 && intval($result_user_info[0]['cc_or_dd_created']) == 1 && trim($result_user_info[0]['cc_or_dd']) != NULL && trim($result_user_info[0]['debit_token']) != NULL){
	      		$placeholder_one = trim($result_user_info[0]['cc_or_dd'])=="DD"?'Bank Account':'Credit Card';
				$placeholder_two = trim($result_user_info[0]['cc_or_dd'])=="DD"?'Credit Card':'Bank Account';													
	      		echo '<p class="change_payment_method">If you want to change your current payment type from <span> ' . $placeholder_one . '</span> to <span> ' . $placeholder_two .'</span>, click on \'Change Payment Type\' button.</p>';
			}
	      	?>	      
    	</div>
  	</div>
</div>
<?php
include_once("includes/footer.php");
?>