<?php
include_once("../../includes/configuration.php");

if(!isset($_SESSION['admin_login']) || $_SESSION['admin_login']!="yes"){
	$_SESSION['redirect_to']=substr($_SERVER['PHP_SELF'],strpos($_SERVER['PHP_SELF'],"administrator/") + 14);
   	$_SESSION['redirect_to_query_string']= $_SERVER['QUERY_STRING'];
	
    $_SESSION['message']="Please login to view this page!";
	$general_func->header_redirect("../index.php");
}



if(isset($_REQUEST['enter']) && $_REQUEST['enter']==2 && trim($_POST['login_form_id'])==$_SESSION['login_form_id']){
	
	$send_to=$_REQUEST['send_to'];	
	$subject=filter_var(trim($_REQUEST['subject']), FILTER_SANITIZE_STRING);	
	$message=html_entity_decode ( trim($_REQUEST['message']) , ENT_QUOTES , 'UTF-8' ); 

	
	//******************  keep record ****************************************************//
	$data=array();
	$data['send_to']=$send_to;
	$data['subject']=$subject;
	$data['message']=$message;
	$data['send_date']='now()';
	$id=$db->query_insert("tbl_newsletters",$data);
	
	
	$sql="select DISTINCT(email_address) as subscriber_email from tbl_subscribers";
	$result=$db->fetch_all_array($sql);
	
	
	$send_to_emails="";
	
	$newsletter_email_content= '<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title>' . $general_func->site_title . '</title>
				<style type="text/css"></style>
				
				</head>
				
				<body>
				<table width="620" border="0" cellspacing="0" cellpadding="0">
				  <tr>
				    <td align="left" valign="top" style="padding:0; margin:0; border-bottom:1px solid #dcdcdc; padding:10px 0 10px 10px;">
				    	<table width="100%" border="0" cellspacing="0" cellpadding="0">
				          <tr>
				            <td align="left"  valign="top" style="padding:0; margin:0;"><a href="'. $general_func->site_url .'"><img src="'. $general_func->site_url .'email_images/logo.png" alt="' . $general_func->site_title . '"  border="0" /></a></td>
				         </tr>
				        </table>
				
				    </td>
				  </tr>
				  <tr>
				   <td align="left" valign="top" style="padding:0; margin:0; border-bottom:1px solid #dcdcdc; padding:10px 0 10px 10px;">
				  ';
									
									
	$newsletter_email_content .= str_replace("/cms_images/",$general_func->site_url."cms_images/",$message);
	
	$newsletter_email_content .= '
					</td>
					</tr>
				
					
					<tr>
							    <td align="left" valign="top" style="padding:10px; margin:0; border-top:1px solid #dcdcdc;">
							    <p style="font:normal 12px/12px Tahoma, Geneva, sans-serif; color:#9d9d9d; float:left;">Copyright &copy; 2014 
  <span>Work on Wellbeing Ltd,</span> All rights reserved </p>
							    </td>
							  </tr>
							</table>
							
							</body>
							</html>';
	
	
	for($i=0; $i<count($result); $i++){
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .=  'Content-type: text/html; charset=UTF-8' . "\r\n";
		$headers .= "From: ".$general_func->site_title." <".$general_func->admin_url.">\r\n";
		
		@mail($result[$i]['subscriber_email'],$subject,stripslashes($newsletter_email_content),$headers);
		
		
		//@mail("mailuttam@webgrity.com",$subject,stripslashes($newsletter_email_content),$headers);
		
		
		$send_to_emails .= $result[$i]['subscriber_email'] ."_~_";
	}
	
	

	
	
	$data=array();
	$data['send_to_emails']=$send_to_emails;
	$db->query_update("tbl_newsletters",$data,"id='".$id ."'");
	
	
	$_SESSION['msg']="Newsletter email has been successfully sent.";
	$general_func->header_redirect($general_func->admin_url ."newsletter/newsletter.php");
} 
?>
