<?php
include_once("includes/configuration.php");


if( isset($_POST['captcha_input']) && trim($_POST['captcha_input']) != NULL && isset($_POST['enter']) && trim($_POST['enter']) == "question" && trim($_POST['before_login_form_id'])==$_SESSION['before_login_form_id']){
		
	if($_POST['secreate']!=$_POST['captcha_input'] || $_POST['captcha_input']==""){	
		$_SESSION['user_message']="Please insert the same letters and numbers you see in the image.";
	}else{
		$question_name=filter_var(trim($_POST['question_name']), FILTER_SANITIZE_STRING);	 
		$question_phone=filter_var(trim($_POST['question_phone']), FILTER_SANITIZE_STRING);
		$question_message=filter_var(trim($_POST['question_message']), FILTER_SANITIZE_STRING);
		$question_email=filter_var(trim($_POST['question_email']), FILTER_SANITIZE_EMAIL);
		
		
		$email_content = '<table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0">
			 	  
			  <tr>
			    <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="color:#333333; font:normal 13px/18px Arial, Helvetica, sans-serif; margin:0 0 10px">
			        <tr>
			          <td width="50%" align="left" valign="top" style="padding:10px; line-height: 20px;"><h3 style="color:#000; font:bold 14px/18px Arial, Helvetica, sans-serif; margin:0; padding:0">Ask a Question Details</h3>
			            Name :   ' . $question_name . ' <br />
			            Email : ' . $question_email . '<br />
			            Phone :   ' . $question_phone . '<br />
			            Message : <br />
			            ' . nl2br($question_message) . '<br />
			           </td>
			         
			        </tr>
			      </table></td>
			  </tr>
			  </table>';
			  
		
		$recipient_info=array();
		$recipient_info['recipient_subject']="Fit N Food user ask a Question";
		$recipient_info['recipient_content']=$email_content;
		$recipient_info['recipient_email']=$question_email;		
		$sendmail -> sent_question($recipient_info, $general_func->email,$general_func->site_title, $general_func->site_url);
		
		$_SESSION['user_message']="Thank you for submitting your question.";
		
		//*****************  Store data into database *****************//
		$data=array();
		$data['name']=$question_name;			
		$data['email']=$question_email;
		$data['phone']=$question_phone;
		$data['message']=$question_message;		
		$data['date_added']=$current_date_time;				
		$db->query_insert("ask_a_question",$data);	
		//*************************************************************//	
	}	
	
	$general_func->header_redirect($general_func->site_url);					
}	
	
?>