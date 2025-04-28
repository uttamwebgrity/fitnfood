<?php
/*
 * Copyright 2011 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * https://code.google.com/apis/console/
 */
require 'includes/configuration.php';
require_once 'src/Google_Client.php';
require_once 'src/contrib/Google_Oauth2Service.php';


$client = new Google_Client();
$client->setApplicationName("Google UserInfo PHP Starter Application");

$oauth2 = new Google_Oauth2Service($client);

if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['token'] = $client->getAccessToken();
  $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
  return;
}

if (isset($_SESSION['token'])) {
 $client->setAccessToken($_SESSION['token']);
}

if (isset($_REQUEST['logout'])) {
  unset($_SESSION['token']);
  $client->revokeToken();
}

if ($client->getAccessToken()) {
  	$user = $oauth2->userinfo->get();
	$_SESSION['token'] = $client->getAccessToken();
	
 	$id= trim($user['id']);		
	$fname= filter_var(trim($user['given_name']), FILTER_SANITIZE_STRING);
	$lname= filter_var(trim($user['family_name']), FILTER_SANITIZE_STRING);
	$email_address= filter_var(trim($user['email']), FILTER_SANITIZE_EMAIL);
	$img = filter_var($user['picture'], FILTER_VALIDATE_URL);
	
	$sql="select * from users where google_id='" . $id . "' limit 1";		
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
		$data['google_photo']=$img;
		
		$data['google_id']=$id;			
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
		$_SESSION['user_login_using_fb']= 0;
		$_SESSION['user_google_id']= $id;
		$_SESSION['user_login_using_google']=1;
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
			
		if(strtolower(trim($lname)) !=strtolower(trim($result[0]['lname']))){
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
		
		if(trim($img) != trim($result[0]['google_photo'])){
			$changed_anyting=1;	
			$updated_date['google_photo']=$img;
			
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
		$_SESSION['user_login_using_fb']= 0;
		$_SESSION['user_google_id']= $id;
		$_SESSION['user_login_using_google']= 1;
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
  $authUrl = $client->createAuthUrl();
}
if(isset($authUrl)) {
	$general_func->header_redirect($authUrl);
}



?>