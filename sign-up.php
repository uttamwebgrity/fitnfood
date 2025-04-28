<?php
include_once("includes/configuration.php");

$fname=filter_var(trim($_REQUEST['fname']), FILTER_SANITIZE_STRING);	 
$lname=filter_var(trim($_REQUEST['lname']), FILTER_SANITIZE_STRING);
$email_address=filter_var(trim($_REQUEST['email_address']), FILTER_SANITIZE_EMAIL);
$password=trim($_REQUEST['password']);	
$refered_code=filter_var(trim($_REQUEST['refered_code']), FILTER_SANITIZE_STRING);
$gender=intval($_REQUEST['gender']);
$hear_about_us=intval($_REQUEST['hear_about_us']);

$newsletters=(isset($_REQUEST['newsletters']) && intval($_REQUEST['newsletters']) == 1)?1:0;
	 
if(isset($_REQUEST['signup']) && trim($_REQUEST['signup']) == "users" && trim($_POST['before_login_form_id'])==$_SESSION['before_login_form_id']){
			
	if($security_validator->validate($email_address,'email') == false){		
		$_SESSION['user_message']="Please enter a valid email address";	
		$general_func->header_redirect($general_func->site_url);		
	}else{	
		if($db->already_exist_inset("users","email_address",$email_address)){
			$_SESSION['user_message']="Sorry, your specified email address is already taken!";
			if(isset($_SESSION['return_to_front_end']) && trim($_SESSION['return_to_front_end'])!=NULL){
				$general_func->header_redirect($_SESSION['return_to_front_end']);			
			}else{
				$general_func->header_redirect($general_func->site_url);
			}
		}else if(trim($refered_code) != NULL && ! $db_common->refered_code_present(trim($refered_code))){	
			$_SESSION['user_message']="Sorry, your provided trainer referrer number number does not exist!";
			if(isset($_SESSION['return_to_front_end']) && trim($_SESSION['return_to_front_end'])!=NULL){
				$general_func->header_redirect($_SESSION['return_to_front_end']);			
			}else{
				$general_func->header_redirect($general_func->site_url);
			}			
		}else{
			$data=array();
			$data['fname']=$fname;	
			$data['lname']=$lname;			
			$data['seo_link']=$general_func->create_seo_link($fname." ".$lname);		
			
			if($db->already_exist_inset("users","seo_link",$data['seo_link'])){//******* exit
				$data['seo_link']=$db->max_id("users","id") + 1 ."-".$data['seo_link'];
			}
			
			$data['refered_code']=$refered_code;	
			$data['newsletters']=$newsletters;
			
			$data['gender']=$gender;		
			$data['hear_about_us']=$hear_about_us;				
			$data['email_address']=$email_address;
			$data['password']=$EncDec->encrypt_me($password);			
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
			$_SESSION['user_login_using_fb']= 0;
			$_SESSION['user_login_using_google']= 0;
			$_SESSION['user_path']= "";	
			$_SESSION['after_login_form_id']=$general_func->genTicketString(10);
		
			if(isset($_SESSION['return_to_front_end']) && trim($_SESSION['return_to_front_end'])!=NULL){
				$general_func->header_redirect($_SESSION['return_to_front_end']);			
			}else{
				$general_func->header_redirect($general_func->site_url . "my-account/");
			}
		}		
	}	
}	

?>