<?php
include_once("includes/configuration.php");

$type=trim($_REQUEST['forgotpassword_type']);
$email=trim($_REQUEST['email']);

if(isset($_REQUEST['submit']) && trim($_REQUEST['submit']) == "Send" && trim($_POST['before_login_form_id'])==$_SESSION['before_login_form_id']){	
	$email=filter_var(trim($_REQUEST['email']), FILTER_SANITIZE_EMAIL);
	
	if($security_validator->validate($email,'email') == false){		
		$_SESSION['user_message']="Please enter a valid email address";	
		$general_func->header_redirect($general_func->site_url);		
	}else{
		$sql="select fname,lname,email_address,password from $type where email_address='". $email ."'  limit 1";
		$result=$db->fetch_all_array($sql);
		
		$email_template=$db->fetch_all_array("select template_subject,template_content from email_template where id=2 limit 1");
					
		if(count($result) == 1){			
			$email_content=$email_template[0]['template_content'];
			$email_content = str_replace("#name#", $result[0]['fname'].' '.$result[0]['lname'], $email_content);
			$email_content = str_replace("#email_address#", $result[0]['email_address'], $email_content);
			$email_content = str_replace("#password#", $EncDec->decrypt_me($result[0]['password']) , $email_content);
			$email_content = str_replace("#login_url#", $general_func->site_url , $email_content);
			$email_content = str_replace("#site_title#", $general_func->site_title , $email_content);	
			
			
					
			//*******************  send email to employer *******************//
			$recipient_info=array();
			$recipient_info['recipient_subject']=$email_template[0]['template_subject'];
			$recipient_info['recipient_content']=$email_content;
			$recipient_info['recipient_email']=trim($result[0]['email_address']); 		
			$sendmail -> logininfo_to_user($recipient_info, $general_func->email,$general_func->site_title, $general_func->site_url);				
			//***************************************************************//
					
			$_SESSION['user_message'] = "Your login information has been sent to your specified email address.";
			$general_func -> header_redirect($general_func->site_url);	
		}else{
			$_SESSION['user_message'] = "Sorry, your specified email address was not found in our records, please try again.";
			$general_func -> header_redirect($general_func->site_url);		
		}
	}
}
?>