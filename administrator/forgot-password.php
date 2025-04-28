<?php
include_once("../includes/configuration.php");



if(isset($_REQUEST['submit']) && trim($_REQUEST['submit']) == "Submit"){	
	$email=filter_var(trim($_REQUEST['email']), FILTER_SANITIZE_EMAIL);
	
	if($security_validator->validate($email,'email') == false){		
		$_SESSION['message']="Please enter a valid email address";	
		$general_func->header_redirect("index.php");		
	}else{
		$sql="select fname,lname,admin_user,admin_pass,email_address from admin where email_address='". $email ."'  limit 1";
		$result=$db->fetch_all_array($sql);
		
		if(count($result) == 1){			
			$email_content='<tr>
			    <td align="left" valign="top" style="background:#fff; padding:20px 10px 50px; font:16px/27px \'open_sanslight\'; color:#8d8d8d"><h2 style="color:#a3c52c; padding: 0 0 15px 0; font:24px/27px \'open_sansregular\';"><strong>Hello '. $result[0]['fname'].' '.$result[0]['lname'] .'!</strong></h2>
			     
			     <p>Your Login information is given below:</p>
			     <p>&nbsp;</p>
			      <p><strong>Username: </strong> '. $result[0]['admin_user'] .'<br />
					<strong>Password:</strong> '. $EncDec->decrypt_me($result[0]['admin_pass']) .'</p>
					<p><br /><br /><br /><br />By clicking <a href="'. $general_func->admin_url .'"> here </a> you can login '. $general_func->site_title.' website admin panel.</p>
					
					</td>
					
			  </tr>';		
			
			//*******************  send email to employer *******************//
			$recipient_info=array();
			$recipient_info['recipient_subject']=$general_func->site_title ." admin panel login information";
			$recipient_info['recipient_content']=$email_content;
			$recipient_info['recipient_email']=trim($result[0]['email_address']); 		
			$sendmail -> logininfo_to_admin($recipient_info, $general_func->email,$general_func->site_title, $general_func->site_url);				
			//***************************************************************//
					
			$_SESSION['message'] = "Your login information has been sent to your specified email address.";
			$general_func -> header_redirect($general_func->admin_url);	
		}else{
			$_SESSION['message'] = "Sorry, your specified email address was not found in our records, please try again.";
			$general_func -> header_redirect($general_func->admin_url);		
		}
	}
}
?>