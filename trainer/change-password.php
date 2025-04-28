<?php
$path_depth="../";

include_once($path_depth. "includes/header.php");

if(!isset($_SESSION['user_login_type']) || trim($_SESSION['user_login_type']) != "trainers" || !isset($_SESSION['user_login_type'])){	
	$_SESSION['user_message']="Sorry, you have no permission to access this page!";
	$general_func->header_redirect($general_func->site_url);
}

 

if(isset($_POST['enter']) && $_POST['enter']=="yes" && trim($_POST['login_form_id'])==$_SESSION['login_form_id']){
	
	$sql="select id from trainers where id='". $_SESSION['user_id'] ."' and password='". $EncDec->encrypt_me(trim($_REQUEST['old_password'])) ."' and status=1 limit 1";
	$result=$db->fetch_all_array($sql);
	
	if(count($result) == 1){
		$data=array();	
		$data['password']=$EncDec->encrypt_me(trim($_REQUEST['new_password']));
		$data['date_modified']=$current_date_time;			
			
		$db->query_update("trainers",$data,"id='".$_SESSION['user_id'] ."'");
				
		$_SESSION['user_message'] = "Your Password has been changed!";
		$general_func -> header_redirect($general_func->site_url . $_SESSION['user_path']  ."my-account/");
		
	}else{		
		$_SESSION['user_message']="Sorry, your specified old password was wrong!";
		$general_func -> header_redirect($general_func->site_url . $_SESSION['user_path']  ."my-account/");	
	}	
}



?>
<div class="inrBnr">
	<?php $db_common->static_page_banner($dynamic_content['page_id']);?>										
</div>
<div class="bodyContent">
  <div class="mainDiv2">
  	<h3>Change Password</h3>
    <div class="my_account">     
    
      <script>
      	function password_validate(){ 
      		var error=0;
      		    		
      		if($("#old_password").val().trim() == ""){
      			document.getElementById("old_password").style.border="1px solid red";
				error++;
      		}else{
      			document.getElementById("old_password").style.border="1px solid #D8D9DA";	
      		}
      		
      		if($("#new_password").val().trim() == ""){
      			document.getElementById("new_password").style.border="1px solid red";
				error++;
      		}else{
      			document.getElementById("new_password").style.border="1px solid #D8D9DA";	
      		}	
      		      		      		
      		if($("#confirm_password").val().trim() == ""){
      			document.getElementById("confirm_password").style.border="1px solid red";
				error++;
      		}else{
      			document.getElementById("confirm_password").style.border="1px solid #D8D9DA";	
      		}	
      		      		
      		if(document.getElementById("new_password").value != document.getElementById("confirm_password").value){
				$("#password_cpassword_msg").show();
				document.getElementById("password_cpassword_msg").innerHTML="New password and confirm password must be same.";		
				error++;
			}else{
				document.getElementById("password_cpassword_msg").innerHTML="";	
				$("#password_cpassword_msg").hide();
			}
      		     			
      		
      		if(error>0)
				return false;
			else
				return true;
      	}
      </script>
      
      
      <form method="post" action="<?=$_SESSION['user_path']?>change-password/"  name="ff" onsubmit="return password_validate()">
        <input type="hidden" name="enter" value="yes" />     
         <input type="hidden" name="login_form_id" value="<?=$_SESSION['login_form_id']?>" />	
      <div class="my_account_left">      
        <div class="update_profile_form">        
          <div class="form_block">
            <div class="form_row">
              <label>Old Password :</label>
              <input id="old_password" name="old_password"  type="password" />
            </div>
            <div class="form_row">
              <label>New Password :</label>
              <input id="new_password" name="new_password"  type="password"  />
            </div>            
          </div>
          <div class="form_block">
            
            <div class="form_row">
              <label>Confirm Password :</label>
              <input name="confirm_password"  type="password"  id="confirm_password"  />
             
            </div>
             <div class="alert_message" id="password_cpassword_msg" style="display: none;"></div>
            <div class="form_row">
              <input type="submit" name="submit" value="Change Password" />
            </div>
          </div>
        
        </div>
      </div>
       </form>
      <div class="clear"></div>
    </div>
  </div>
</div>
<?php
include_once("includes/footer.php");
?>