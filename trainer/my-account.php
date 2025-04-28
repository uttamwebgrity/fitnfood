<?php
$path_depth="../";

include_once($path_depth. "includes/header.php");

if(!isset($_SESSION['user_login_type']) || trim($_SESSION['user_login_type']) != "trainers" || !isset($_SESSION['user_login_type'])){	
	$_SESSION['user_message']="Sorry, you have no permission to access this page!";
	$general_func->header_redirect($general_func->site_url);
}

$sql_user="select CONCAT(fname,' ',lname) as name,email_address,street_address,photo,phone,refered_code,CONCAT(suburb_name,', ',suburb_postcode,', ',suburb_state) as suburb_name  from trainers u left join  suburb s on u.suburb_id=s.id  where u.id='" . $_SESSION['user_id']. "' limit 1";
$result_user_info=$db->fetch_all_array($sql_user)

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
	        		if(trim($result_user_info[0]['photo']) == NULL)
						$img_path="images/no-image.jpg";
					else
						$img_path="trainers_photo/small/".trim($result_user_info[0]['photo']);	        		
	        		 
	        		?>	        		
	        		<img src="<?=$img_path?>" alt="" /></div>
	        	<div class="submit_box"><span></span><input name="" type="submit" value="Update Profile" onclick="location.href='<?=$_SESSION['user_path']?>update-profile/'" /></div>
	      	</div>
			<div class="my_account_right">
				<div class="text_area"><span>Name :</span><p><?=$result_user_info[0]['name']?></p></div>
		    	<div class="text_area"><span>Email :</span><p><?=$result_user_info[0]['email_address']?></p></div>
		    	<div class="text_area"><span>Address :</span><p><?=$result_user_info[0]['street_address']?></p></div>
		        <div class="text_area"><span>Suburb :</span><p><?=$result_user_info[0]['suburb_name']?></p></div>
		        <div class="text_area"><span>Mobile :</span><p><?=$result_user_info[0]['phone']?></p></div>  
		        <div class="text_area"><span>Referrer ID  :</span><p><?=$result_user_info[0]['refered_code']?></p></div>      
		 	</div>
      		<div class="clear"></div>
          	<div class="my_account_button_tab_area">
		        <div class="my_account_button_tab"><a class="update_profile_tab" href="<?=$_SESSION['user_path']?>update-profile/">Update Profile</a></div>
		        <div class="my_account_button_tab"><a class="update_password_tab" href="<?=$_SESSION['user_path']?>change-password/">Change Password</a></div>
		        <!--<div class="my_account_button_tab"><a class="order_history_tab">Order History</a></div>
		        <div class="my_account_button_tab"><a class="video_tab">Video</a></div>
		        <div class="my_account_button_tab"><a class="training_tab">Traning</a></div>
		        <div class="my_account_button_tab"><a class="payment_history_tab">Payment History</a></div>
		        <div class="my_account_button_tab"><a class="call_history_tab">Call History</a></div>-->
		        <div style="font-size: 15px; color: #FF0000;" ><a>Other Buttons Coming Soon...</a></div>
	      	</div>
    	</div>
  	</div>
</div>
<?php
include_once($path_depth . "includes/footer.php");
?>
