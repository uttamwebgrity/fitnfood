<?php
include_once("../includes/configuration.php");
$remember_me=(isset($_POST["remember_me"]) && (int)$_POST["remember_me"] == 1)?1:0;

$username=filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
$pawd=trim($_POST['pawd']);

if(isset($_POST['enter']) && (int)$_POST['enter']==1){
	if($username == NULL || $pawd == NULL){
		$_SESSION['message']="Sorry, User ID and Password should not be empty";	
		$general_func->header_redirect("index.php");
	}else{
		$sql="select * from admin where admin_user='". $username ."' and admin_pass='" . $EncDec->encrypt_me($pawd) . "' limit 1";
		$result=$db->fetch_all_array($sql);
		
				
		if(count($result) == 1){		
			$_SESSION['admin_user_id']=$result[0]['admin_id'];
			$_SESSION['admin_name']=$result[0]['fname'] ." ".$result[0]['lname'];
			$_SESSION['admin_user']=$result[0]['admin_user'];
			$_SESSION['admin_email_address']=$result[0]['email_address'];
			$_SESSION['admin_access_level']=$result[0]['access_level'];							
			$_SESSION['admin_login']="yes";	
			$_SESSION['login_form_id']=$general_func->genTicketString(10);
					
			
			if((int)$remember_me == 1){
		  		setcookie("cookie_user",$_POST['username']); 
				setcookie("cookie_pass",$_POST['pawd']);
		 	}else{
				setcookie("cookie_user",$_POST['username'],time()-3600);
				setcookie("cookie_pass",$_POST['pawd'],time()-3600);
			}			
			
			//**********Save admin login history **********************//
			$data=array();
			$data['login_date_time']='now()';
			$data['login_ip']=$_SERVER['REMOTE_ADDR'];	
			$db->query_insert("admin_login_hostory",$data);			
			//*********************************************************//		
		
			$path="home.php";
			if(isset($_SESSION['redirect_to']) && trim($_SESSION['redirect_to'])!=NULL){
				$path=$_SESSION['redirect_to']."?".$_SESSION['redirect_to_query_string'];
			}
			?>
			<script>
				location.href='<?=$path?>';				
			</script>		
			
			<?php
			
		}else{
			$_SESSION['message']="Error: Your username and/or password was incorrect!<br/>Check your username and password and try again!";
			$general_func->header_redirect("index.php");
		}	
	}
}else{
	echo "Hacking Attempt !";
	exit();
}
?>