<?php
include_once("includes/configuration.php");

$type=trim($_REQUEST['login_type']);
$email=trim($_REQUEST['email']);
$password=trim($_REQUEST['password']);
 


if(isset($_REQUEST['submit']) && trim($_REQUEST['submit']) == "Login" && trim($_POST['before_login_form_id'])==$_SESSION['before_login_form_id']){
	
		
	$email=filter_var(trim($_REQUEST['email']), FILTER_SANITIZE_EMAIL);
	
	if($security_validator->validate($email,'email') == false){		
		$_SESSION['user_message']="Please enter a valid email address";	
		$general_func->header_redirect($general_func->site_url);		
	}else{
		$sql="select id,fname,lname,email_address,seo_link 	 from $type where email_address='". $email ."' and password='". $EncDec->encrypt_me($password) ."' and status=1 limit 1";
		$result=$db->fetch_all_array($sql);
		
		if(count($result) == 1){
			$_SESSION['user_id']=$result[0]['id'];
			$_SESSION['user_fname']=$result[0]['fname'];
			$_SESSION['user_lname']=$result[0]['lname'];
			$_SESSION['user_email_address']=$result[0]['email_address'];
			$_SESSION['user_seo_link']=$result[0]['seo_link'];	
			$_SESSION['user_login_type']= $type; 	
			$_SESSION['user_login']= "yes";
			$_SESSION['after_login_form_id']=$general_func->genTicketString(10);
			
			if(trim($type) == "trainers")
				$_SESSION['user_path']= "trainer/";
			else
				$_SESSION['user_path']= "";	
			
			
			//***************** save last login date *****************************//
			$db->query("update $type set last_login_date='" . $current_date_time . "' where id='" . $_SESSION['user_id'] . "'");
			//******************************************************************//
			
			if($type == "users"){
				if(isset($_SESSION['return_to_front_end']) && trim($_SESSION['return_to_front_end'])!=NULL){
					$general_func->header_redirect($_SESSION['return_to_front_end']);		
				}else{
					$general_func->header_redirect($general_func->site_url . "my-account/");
				}
			}else{
				if(isset($_SESSION['return_to_front_end']) && trim($_SESSION['return_to_front_end'])!=NULL){
					$general_func->header_redirect($_SESSION['return_to_front_end']);		
				}else{
					$general_func->header_redirect($general_func->site_url . "trainer/my-account/");
				}
			}			 	
		}else{			
			$_SESSION['user_message'] = "Access denied. Incorrect Username and/or Password!";	
			$general_func->header_redirect($general_func->site_url);
		}	
		
	}	
}	
?>	
	