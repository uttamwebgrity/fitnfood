<?php
class send_mail{	
    //****************  mail header ****************************************//
	function mail_header($site_title,$site_url){
		$header='<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title>' . $site_title . '</title>
				<style type="text/css">
					*{ margin:0; padding:0; }
					@font-face {
					 font-family: \'open_sanslight\';
					 src: url(\'' .$site_url . 'fonts/opensans-light-webfont.eot\');
					 src: url(\'' .$site_url . 'fonts/opensans-light-webfont.eot?#iefix\') format(\'embedded-opentype\'),  url(\'' .$site_url . 'fonts/opensans-light-webfont.woff\') format(\'woff\'),  url(\'' .$site_url . 'fonts/opensans-light-webfont.ttf\') format(\'truetype\'),  url(\'' .$site_url . 'fonts/opensans-light-webfont.svg#open_sanslight\') format(\'svg\');
					 font-weight: normal;
					 font-style: normal;
					}
					 @font-face {
					 font-family: \'open_sansregular\';
					 src: url(\'' .$site_url . 'fonts/opensans-regular-webfont.eot\');
					 src: url(\'' .$site_url . 'fonts/opensans-regular-webfont.eot?#iefix\') format(\'embedded-opentype\'),  url(\'' .$site_url . 'fonts/opensans-regular-webfont.woff\') format(\'woff\'),  url(\'' .$site_url . 'fonts/opensans-regular-webfont.ttf\') format(\'truetype\'),  url(\'' .$site_url . 'fonts/opensans-regular-webfont.svg#open_sansregular\') format(\'svg\');
					 font-weight: normal;
					 font-style: normal;
					}
					p{
						margin:0; padding:0;
						 font-weight: 13px;
						 font-face: arial;								
						padding:15px 0 0 10px;
					}
				</style>
				
				</head>
				
				<body>
				<table width="960px" border="0" cellspacing="0" cellpadding="0">
				  <tr>
				    <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
				        <tr>
				         <td align="left" valign="top"><a href="#url"><img src="' .$site_url . 'email_images/emlTmpltHdr.jpg" alt="" style="border:none" /></a></td>
				         </tr>
				      </table></td>
				  </tr>
				  <tr>
				    <td style="font-family: Arial, Helvetica, sans-serif;">
				  ';
		return ($header);
	}
	
	//********************* mail footer *************************************//
	function mail_footer($site_title,$site_url){
					$footer='
						 </td>
					</tr>
					<tr>
							    <td align="center" valign="top" style="background:#a3c52c; border-bottom:9px solid #568804"><table width="100%" height="78px" border="0" cellspacing="0" cellpadding="0">
							        <tr>
							          <td align="left" valign="top"><p style="font:16px/22px \'open_sansregular\'; color:#fff; padding:15px 0 0 10px;">T +61 1300 362 925  | <a href="mailto:enquiry@fitnfood.com.au" style="color:#fff; text-decoration:none">enquiry@fitnfood.com.au</a><br>
							              f11/101 Rookwood Rd, Yagoona NSW 2199, Australia</p></td>
							          <td align="right" valign="top"><ul style="float:right; padding:25px 10px 0 0;">
							              <li style="float:left; padding:0 0 0 12px; list-style:none"><a href="#url"><img src="' .$site_url . 'email_images/ftrIco1.png" alt="" style="border:none" /></a></li>
							              <li style="float:left; padding:0 0 0 12px; list-style:none"><a href="#url"><img src="' .$site_url . 'email_images/ftrIco2.png" alt="" style="border:none" /></a></li>
							              <li style="float:left; padding:0 0 0 12px; list-style:none"><a href="#url"><img src="' .$site_url . 'email_images/ftrIco3.png" alt="" style="border:none" /></a></li>
							              <li style="float:left; padding:0 0 0 12px; list-style:none"><a href="#url"><img src="' .$site_url . 'email_images/ftrIco4.png" alt="" style="border:none" /></a></li>
							            </ul></td>
							        </tr>
							      </table></td>
							  </tr>
							</table>							
							</body>
							</html>';
		return ($footer);
	}
	
	public function make_link($url,$text=''){
		return "<a href=\"".$url."\" >".($text==''?$url:$text)."</a>";
	}


	
	function register_welcome_to_user($recipient_info,$admin_email_id,$site_title,$site_url){		
		$subject=$recipient_info['recipient_subject'];			
		
		$message=$this->mail_header($site_title,$site_url);
		$message .=$recipient_info['recipient_content'];
		$message .=	$this->mail_footer($site_title,$site_url);
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		$headers .= "From: Fit N Food <". $admin_email_id .">\r\n";		
		
		@mail($recipient_info['recipient_email'],$subject,$message,$headers);
		
		/*print $recipient_info['recipient_email'];
		print $message;
		exit;*/
	}
	
	
	
	function logininfo_to_admin($recipient_info,$admin_email_id,$site_title,$site_url){		
		$subject=$recipient_info['recipient_subject'];			
		
		$message=$this->mail_header($site_title,$site_url);
		$message .=$recipient_info['recipient_content'];
		$message .=	$this->mail_footer($site_title,$site_url);
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		$headers .= "From: Fit N Food <". $admin_email_id .">\r\n";		
		
		@mail($recipient_info['recipient_email'],$subject,$message,$headers);
		
		/*print $recipient_info['recipient_email'];
		print $message;
		exit;	*/
	}
	
	
	function logininfo_to_user($recipient_info,$admin_email_id,$site_title,$site_url){		
		$subject=$recipient_info['recipient_subject'];			
		
		$message=$this->mail_header($site_title,$site_url);
		$message .=$recipient_info['recipient_content'];
		$message .=	$this->mail_footer($site_title,$site_url);
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		$headers .= "From: Fit N Food <". $admin_email_id .">\r\n";		
		
		@mail($recipient_info['recipient_email'],$subject,$message,$headers);
		
		/*print $recipient_info['recipient_email'];
		print $message;
		exit;*/	
	}
	
		
	function sent_question($recipient_info,$admin_email_id,$site_title,$site_url){		
		$subject=$recipient_info['recipient_subject'];			
		
		$message=$this->mail_header($site_title,$site_url);
		$message .=$recipient_info['recipient_content'];
		$message .=	$this->mail_footer($site_title,$site_url);
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		$headers .= "From: Fit N Food <". $admin_email_id .">\r\n";		
		
		@mail($admin_email_id,$subject,$message,$headers);
		
		/*print $admin_email_id;
		print $message;
		exit;*/
	}
	
	
	
	function send_email($recipient_info,$admin_email_id,$site_title,$site_url){		
		$subject=$recipient_info['recipient_subject'];		
		$message =$recipient_info['recipient_content'];
		$message_admin =$recipient_info['recipient_content'];		
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		$headers .= "From: Fit N Food <". $admin_email_id .">\r\n";			
		@mail($recipient_info['recipient_email'],$subject,$message,$headers);
		
		$admin_subject="This is a copy of " . $site_title . " customer order details";
		@mail($admin_email_id,$admin_subject,$message_admin,$headers);
		@mail("mailuttam@webgrity.com",$admin_subject,$message_admin,$headers);
		/*print $recipient_info['recipient_email'];
		print $message;
		exit;*/
	}

	function cutoff_reminder($recipient_info,$admin_email_id,$site_title,$site_url){
		$subject=$recipient_info['recipient_subject'];			
		
		$message=$this->mail_header($site_title,$site_url);
		$message .=$recipient_info['recipient_content'];
		$message .=	$this->mail_footer($site_title,$site_url);
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		$headers .= "From: Fit N Food <". $admin_email_id .">\r\n";		
		
		@mail($recipient_info['recipient_email'],$subject,$message,$headers);
			
		 /* print $recipient_info['recipient_email'];
		print $message;  */
		
		//exit;
	}

	function payment_success_send_email($recipient_info,$admin_email_id,$site_title,$site_url){		
		$subject=$recipient_info['recipient_subject'];		
		$message =$recipient_info['recipient_content'];
		$message_admin =$recipient_info['recipient_content'];		
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		$headers .= "From: Fit N Food <". $admin_email_id .">\r\n";			
		@mail($recipient_info['recipient_email'],$subject,$message,$headers);
		
		$admin_subject="This is a copy of customer payment details";
		@mail($admin_email_id,$admin_subject,$message_admin,$headers);		
		@mail("mailuttam@webgrity.com",$admin_subject,$message_admin,$headers);
		/*print $recipient_info['recipient_email'];
		print $message;
		exit;*/
	}
	function payment_failed_send_email($recipient_info,$admin_email_id,$site_title,$site_url){		
		$subject=$recipient_info['recipient_subject'];		
		$message =$recipient_info['recipient_content'];
		$message_admin =$recipient_info['recipient_content'];		
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		$headers .= "From: Fit N Food <". $admin_email_id .">\r\n";		
		@mail($recipient_info['recipient_email'],$subject,$message,$headers);
		
		$admin_subject="This is a copy of customer failed payment details";
		@mail($admin_email_id,$admin_subject,$message_admin,$headers);
		
		/*print $recipient_info['recipient_email'];
		print $message;
		exit;*/
	}


}
?>