<?php
include_once("../../includes/configuration.php");

if(!isset($_SESSION['admin_login']) || $_SESSION['admin_login']!="yes"){
	$_SESSION['redirect_to']=substr($_SERVER['PHP_SELF'],strpos($_SERVER['PHP_SELF'],"administrator/") + 14);
   	$_SESSION['redirect_to_query_string']= $_SERVER['QUERY_STRING'];
	
    $_SESSION['message']="Please login to view this page!";
	$general_func->header_redirect("../index.php");
}



if(isset($_REQUEST['enter']) && $_REQUEST['enter']==2){
	
	$subject=trim($_REQUEST['subject']);
	$message=trim($_REQUEST['message']); 

	
	//******************  keep record ****************************************************//
	$data=array();
	$data['send_to']=intval($_REQUEST['send_to']);
	$data['subject']=$subject;
	$data['message']=$message;
	$data['send_date']='now()';
	$id=$db->query_insert("newsletters",$data);
	
	
	//if($data['send_to'] == 2)
	$sql="select DISTINCT(email_address) as subscriber_email from subscribers where unsubscribe=0";
	$result=$db->fetch_all_array($sql);	
		
	
	$send_to_emails="";	
	$total_subscribers=count($result);
		
	for($i=0; $i<$total_subscribers; $i++){
	
		$newsletter_email_content= '<table width="620" border="0" cellspacing="0" cellpadding="0">
			  <tr>
			    <td align="left" valign="top" style="padding:0; margin:0; border-bottom:1px solid #dcdcdc; padding:10px 0 10px 10px;">
			    	<table width="100%" border="0" cellspacing="0" cellpadding="0">
			          <tr>
			            <td align="left" valign="top" style="padding:0; margin:0;"><img src="'. $general_func->site_url .'email_images/logo.jpg" width="107" height="110" alt="" /></td>
			            <td align="right" valign="top" style="margin:0;"><img src="'. $general_func->site_url .'email_images/headerBg.jpg" width="115" height="93" alt="" /></td>
			          </tr>
			        </table>			
			    </td>
			  </tr>
			  <tr>
			    <td align="left" valign="top" style="padding:20px; margin:0;">';											
									
	$newsletter_email_content .= str_replace("/cms_images/","http://happy-in-style.com/cms_images/",$message);
	
	$newsletter_email_content .= '<div style="float:left;width:100%;padding:100px 0 0 0">
									If you do not like to receive this newsletter again in future, please click here to <a href="' . $general_func->site_url .'/unsubscribe.php?email_address=' . $result[$i]['subscriber_email'] . '" style="color:#b85d04;text-decoration:none">Unsubscribe</a>
								</div>	
								 </td>
			  </tr>
			  <tr>
			    <td align="left" valign="top" style="padding:10px; margin:0; border-top:1px solid #dcdcdc;">
			    <p style="font:normal 12px/12px Tahoma, Geneva, sans-serif; color:#9d9d9d; float:left;">Copyright &copy; 2013 <a href="http://happy-in-style.com/" target="_blank" style="color:#cf81a7; text-decoration:none;">happy-in-style.com</a>. All rights reserved</p>
			    <img src="'. $general_func->site_url .'email_images/footerBg.jpg" width="250" height="34" alt="" style="margin:0; padding:0; float:right;" /></td>
			  </tr>
			</table>';
	
	
	
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		$headers .= "From: ".$general_func->site_title." <".$general_func->admin_url.">\r\n";
		
		@mail($result[$i]['subscriber_email'],$subject,stripslashes($newsletter_email_content),$headers);
		
			
		$send_to_emails .= $result[$i]['subscriber_email'] ."_~_";
	}
	
			
	$data=array();
	$data['send_to_emails']=$send_to_emails;
	$db->query_update("newsletters",$data,"id='".$id ."'");
	
	
	$_SESSION['msg']="Newsletter email has been successfully sent.";
	$general_func->header_redirect($general_func->admin_url ."newsletter/newsletter.php");
} 
?>
