<?php
require 'includes/configuration.php';

$facebook = new Facebook(array(
            'appId' => APP_ID,
            'secret' => APP_SECRET,
            ));

$user = $facebook->getUser();
 

if ($user) {
	try {
    	// Proceed knowing you have a logged in user who's authenticated.
    	$user_profile = $facebook->api('/me');
  	}catch (FacebookApiException $e) {
    	error_log($e);
    	$user = null;
  	}


    if (!empty($user_profile )) { 
		     
        $id= trim($user_profile['id']);		
		$fname= trim($user_profile['first_name']);	
		$lname= trim($user_profile['last_name']);
		$email_address= trim($user_profile['email']);
	
		$sql="select * from users where facebook_id='" . intval(mysql_real_escape_string($id)). "' limit 1";		
		$result=$db->fetch_all_array($sql);
		
		if(count($result) == 0){//************** new user 
			
			$data=array();
			$data['fname']=$fname;	
			$data['lname']=$lname;			
			$data['seo_link']=$general_func->create_seo_link($fname." ".$lname);		
			
			if($db->already_exist_inset("users","seo_link",$data['seo_link'])){//******* exit
				$data['seo_link']=$db->max_id("users","id") + 1 ."-".$data['seo_link'];
			}						
			$data['email_address']=$email_address;
			$data['facebook_id']=$id;			
			$data['status']=1;
			$data['date_added']=$current_date_time;
			$inserted_id=$db->query_insert("users",$data);
		
		
			$_SESSION['user_id']=$inserted_id;
			$_SESSION['user_fname']=$fname;
			$_SESSION['user_lname']=$lname;
			$_SESSION['user_email_address']=$email_address;
			$_SESSION['user_seo_link']=$data['seo_link'];	
			$_SESSION['user_login_type']= "users"; 	
			$_SESSION['user_login']= "yes";
			$_SESSION['user_login_using_fb']= 1;
			$_SESSION['user_fb_id']= $id;
			$_SESSION['user_login_using_google']= 0;
			$_SESSION['user_path']= "";	
			$_SESSION['after_login_form_id']=$general_func->genTicketString(10);
			
			if(isset($_SESSION['return_to_front_end']) && trim($_SESSION['return_to_front_end'])!=NULL){
				$general_func->header_redirect($_SESSION['return_to_front_end']);			
			}else{
				$general_func->header_redirect($general_func->site_url . "my-account/");
			}					
		}else{//****** Existing facebook user				
			//***************  if user changed anyting in FB ***********************//
			$changed_anyting=0;
			
			$updated_date=array();
			
			if(strtolower(trim($fname)) != strtolower(trim($result[0]['fname']))){
				$changed_anyting=1;	
				$updated_date['fname']=$fname;
				$_SESSION['user_fname']=$fname;
			}else{
				$_SESSION['user_fname']=$result[0]['fname'];	
			}
			
			if(strtolower(trim($lname)) != strtolower(trim($result[0]['lname']))){
				$changed_anyting=1;	
				$updated_date['lname']=$lname;
				$_SESSION['user_lname']=$lname;
			}else{
				$_SESSION['user_lname']=$result[0]['lname'];
			}
			
			if(strtolower(trim($email_address)) != strtolower(trim($result[0]['email_address']))){
				$changed_anyting=1;	
				$updated_date['email_address']=$email_address;
				$_SESSION['user_email_address']=$email_address;				
			}else{
				$_SESSION['user_email_address']=$result[0]['email_address'];
			}
			
			
			if($changed_anyting == 1){					
				$db -> query_update("users", $updated_date, "id='" . $result[0]['id'] . "'");
			}
			
			//*********************************************************************//
									
			$_SESSION['user_id']=$result[0]['id'];
			$_SESSION['user_fname']=$result[0]['fname'];
			$_SESSION['user_lname']=$result[0]['lname'];
			$_SESSION['user_email_address']=$result[0]['email_address'];
			$_SESSION['user_seo_link']=$result[0]['seo_link'];	
			$_SESSION['user_login_type']= "users"; 		
			$_SESSION['user_login']= "yes";
			$_SESSION['user_login_using_fb']= 1;
			$_SESSION['user_fb_id']= $id;
			$_SESSION['user_login_using_google']= 0;
			$_SESSION['after_login_form_id']=$general_func->genTicketString(10);			
			$_SESSION['user_path']= "";	
			
			$db->query("update users set last_login_date='" . $current_date_time . "' where id='" . $_SESSION['user_id'] . "'");
			
			if(isset($_SESSION['return_to_front_end']) && trim($_SESSION['return_to_front_end'])!=NULL){
				$general_func->header_redirect($_SESSION['return_to_front_end']);		
			}else{
				$general_func->header_redirect($general_func->site_url . "my-account/");
			}
		}				
       
    } else {
        
        die("There was an error.");
    }
} else {
    # There's no active session, let's generate one 
        
	$login_url = $facebook->getLoginUrl(array( 'scope' => 'email','public_profile'));	
    header("Location: " . $login_url);
	exit();
}
?>
