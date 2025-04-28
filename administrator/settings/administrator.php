<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";

$error_found=0;

if((int)$_SESSION['admin_access_level'] != 1){		
    $_SESSION['message']="Sorry, you do not have the permission to access this page!";
	$general_func->header_redirect($general_func->admin_url."home.php");
}

if(isset($_POST['enter']) && $_POST['enter']=="yes"){
	$data=array();
	$_SESSION['msg']="";
	//**************  validation checking **************************//
	if(!$validator->validate_text($_REQUEST['admin_user'])){
		$_SESSION['msg'] .= "Username should not be blank! <br/>";
		$error_found=1;
	}	
	if(!$validator->allLetterandNumber($_REQUEST['admin_user'])){
		$_SESSION['msg'] .= "Your username is not valid. Only alphabet characters and numbers are acceptable! <br/>";
		$error_found=1;
	}
		
		
	if(!$validator->validate_text($_REQUEST['fname'])){
		$_SESSION['msg'] .= "First name should not be blank! <br/>";
		$error_found=1;
	}	
	if(!$validator->allLetter($_REQUEST['fname'])){
		$_SESSION['msg'] .= "First name must have alphabet characters only! <br/>";
		$error_found=1;
	}	
	
	if(!$validator->validate_text($_REQUEST['lname'])){
		$_SESSION['msg'] .= "Last name should not be blank! <br/>";
		$error_found=1;
	}	
	if(!$validator->allLetter($_REQUEST['lname'])){
		$_SESSION['msg'] .= "Last name must have alphabet characters only! <br/>";
		$error_found=1;
	}
	
	$email_address=filter_var(trim($_REQUEST['email_address']), FILTER_SANITIZE_EMAIL);	
	if($security_validator->validate($email_address,'email') == false){
		$_SESSION['msg'] .= "Please enter a valid email address<br/>";
		$error_found=1;
	}		
	
	
	if(trim($_REQUEST['pass']) != NULL){			
		if(!$validator->passwordvalidate($_REQUEST['pass'],6,12)){
			$_SESSION['msg'] .= "Password must be of length between 6 to 12<br/>";
			$error_found=1;
		}		
	}		
	//******************************************************//
	if(intval($error_found) == 0){
		$data['admin_user']=$_REQUEST['admin_user'];
		$data['fname']=$_REQUEST['fname'];
		$data['lname']=$_REQUEST['lname'];
		$data['email_address']=$_REQUEST['email_address'];				
		
		if(trim($_REQUEST['pass']) != NULL){
			$data['admin_pass']=$EncDec->encrypt_me(trim($_REQUEST['pass']));
		}	
				
		$db->query_update("admin",$data,"admin_id=1");
		
		
		$_SESSION['admin_user']=$_REQUEST['admin_user'];
		$_SESSION['admin_name']=$_REQUEST['fname'] ." ".$_REQUEST['lname'];
					
		
		if($db->affected_rows > 0)
			$_SESSION['msg']="Administrator settings successfully updated!";	
	}
	
		
	$general_func->header_redirect($_SERVER['PHP_SELF']);
}
	




$sql="select * from admin where admin_id=1 limit 1";
$result=$db->fetch_all_array($sql);


if(count($result) == 1){
	$admin_id=$result[0]['admin_id'];
	$admin_user=$result[0]['admin_user'];
	$admin_pass=$result[0]['admin_pass'];
	$fname=$result[0]['fname'];
	$lname=$result[0]['lname'];
	$email_address=$result[0]['email_address'];
}else{
	$admin_id="";
	$admin_user="";
	$admin_pass="";
	$fname="";
	$lname="";
	$email_address="";	
}
?>

<script language="javascript" type="text/javascript"> 
function validate(){
	if(!validate_text(document.ff.fname,1,"First name should not be blank"))
		return false;
		
	if(!allLetter(document.ff.fname,"First name must have alphabet characters only"))
		return false;				
		
	if(!validate_text(document.ff.lname,1,"Last name should not be blank"))
		return false;
		
	if(!allLetter(document.ff.lname,"Last name must have alphabet characters only"))
		return false;		
		
	if(!validate_text(document.ff.admin_user,1,"Username should not be blank"))
		return false;
	
	if(!allLetterandNumber(document.ff.admin_user,"Your username is not valid. Only alphabet characters and numbers are acceptable"))
		return false;	
	
	if(!validate_email(document.ff.email_address,1,"Enter a valid email address")){		
		return false;
	}	
		
		
	if(document.ff.pass.value != ""){
		if(!validate_text(document.ff.pass,1,"Enter your new password"))
			return false;
					
		if(!passid_validation(document.ff.pass,6,12))
			return false;	
		
		if(!validate_text(document.ff.pass1,1,"Enter your new password again"))
			return false;
		
		if(document.ff.pass.value != document.ff.pass1.value){
			alert("New password and confirm password must be same!");
			document.ff.pass1.select();
			document.ff.pass1.focus();
			return false;
		}	
	}	
}
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
   
    <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg">Administrator Settings</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  
  <tr>
    <td align="left" valign="top" class="body_whitebg"><form method="post" action="<?=$_SERVER['PHP_SELF']?>" name="ff" onsubmit="return validate()">
    <input type="hidden" name="enter" value="yes" />
        <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
           <tr>
                  <td colspan="3" class="body_content-form" height="30"></td>
            </tr>
            <?php if(isset($_SESSION['msg']) && trim($_SESSION['msg']) != NULL){?>
			<tr>
                  <td colspan="3" class="message_error"><?=$_SESSION['msg']; $_SESSION['msg']="";?></td>
            </tr>
             <tr>
                  <td colspan="3" class="body_content-form" height="10"></td>
            </tr>
			 <?php  } ?>
            
          <tr>
            <td align="left" valign="top" colspan="3"><table width="90%" border="0"  align="center" cellspacing="2" cellpadding="6">
                <tr>
                  <td width="14%" class="body_content-form" valign="top">First Name:<font class="form_required-field"> *</font></td>
                  <td width="86%" valign="top"><input name="fname" value="<?=$fname?>" type="text" autocomplete="off" class="form_inputbox" size="40" /></td>
                </tr>
                <tr>
                  <td width="14%" class="body_content-form" valign="top">Last Name:<font class="form_required-field"> * </font></td>
                  <td width="86%" valign="top"><input name="lname" value="<?=$lname?>" type="text" autocomplete="off" class="form_inputbox"  size="40"/></td>
                </tr>                
                <tr>
                  <td width="14%" class="body_content-form" valign="top">Username:<font class="form_required-field"> *</font> </td>
                  <td width="86%" valign="top"><input name="admin_user" value="<?=$admin_user?>" type="text" autocomplete="off" class="form_inputbox" size="40" /></td>
                </tr>
                 <tr>
                  <td width="14%" class="body_content-form" valign="top">Email Address:<font class="form_required-field"> *</font> </td>
                  <td width="86%" valign="top"><input name="email_address" value="<?=$email_address?>" type="email" autocomplete="off" class="form_inputbox" size="40" /></td>
                </tr>
                <tr>
                  <td width="14%" class="body_content-form" valign="top">New Password:</td>
                  <td width="86%" valign="top"><input name="pass" type="password" autocomplete="off" class="form_inputbox"  size="40"/>
                    <br/>
                    <span class="small_font">If you would like to change the password type a new one. Otherwise leave this blank.</span></td>
                </tr>
                <tr>
                  <td width="14%" class="body_content-form" valign="top">&nbsp;</td>
                  <td width="86%" valign="top"><input name="pass1" type="password" autocomplete="off" class="form_inputbox"  size="40"/>
                    <br/>
                     <span class="small_font">Type your new password again.</span></td>
                </tr>
                <tr>
                  <td colspan="2" class="body_content-form" height="10"></td>
                </tr>
                <tr>
                  <td width="14%" class="body_content-form">&nbsp;</td>
                  <td width="86%"><table border="0" align="left" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                        <td align="left" valign="middle" class="body_tab-middilebg"><input name="button" type="submit" class="submit1" value="Save Changes" /></td>
                        <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                      </tr>
                    </table></td>
                </tr>
              </table></td>
          </tr>
          <tr>
            <td height="32" align="left" valign="top">&nbsp;</td>
            <td align="left" valign="top">&nbsp;</td>
            <td align="left" valign="top">&nbsp;</td>
          </tr>
        </table>
      </form></td>
  </tr>
</table>
<?php
include("../foot.htm");
?>
