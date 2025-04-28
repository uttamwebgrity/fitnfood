<?php 
include_once("../includes/configuration.php");

if(isset($_COOKIE['cookie_user']) && isset($_COOKIE['cookie_pass'])){
	$username=$_COOKIE['cookie_user'];
	$pawd=$_COOKIE['cookie_pass'];
	$remember_me=1;
}else{
	$username="";
	$pawd="";
	$remember_me=0;	
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$general_func->site_title?> :: Secure Area</title>
<link href="css/login.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="../includes/validator.js"></script>
<script language="javascript" src="../includes/jquery.js"></script>
<style>
/*input:-webkit-autofill {
    -webkit-box-shadow: 0 0 0px 1000px white inset;
}*/
	
</style>

<script language="javascript">
function validate(){
	if(!validate_text(document.frmlogin.username,1,"Enter User ID")){
		document.frmlogin.username.value="";
        document.frmlogin.username.focus();
		return false;
	}	
	if(!validate_text(document.frmlogin.pawd,1,"Enter Password")){
		document.frmlogin.pawd.value="";
        document.frmlogin.pawd.focus();
		return false;
	}	
}
function validate_forgot(){
	if(!validate_email(document.frmforgot.email,1,"Enter a valid email address")){		
		return false;
	}
}

$(document).ready(function() {
	$(".lgnPgLnk").click(function () { 		
     	$("#id_login").slideToggle(1000); 
     	$("#id_forgot").slideToggle(1000);     	
	}); 
});
</script>
</head>
<body class="lginPg">
	<div class="lginMain">
   		<div class="lgBxRw"><img src="images/logo.png" class="lginLogo" /></div>
      	<div class="lgBxRw">
      		<p class="lginError">
      		<?php if(isset($_SESSION['message']) && trim($_SESSION['message']) != NULL){            
               	echo $_SESSION['message']; $_SESSION['message']="";
			 }?>
		 	</p>
		</div>
      	<div class="lgBxRw">
        	<div class="lgnFldPnl" id="id_login" style="display: block;">
        	<form action="verified.php" method="post" name="frmlogin" onsubmit="return validate();">
            <input type="hidden" name="enter" value="1" />	
          		<input name="username" type="text" autocomplete="off" value="<?=$username?>" />
          		<input name="pawd" type="password" autocomplete="off" value="<?=$pawd?>" />
          		<div class="lgnSbmtPnl">
          			<label><input type="checkbox" name="remember_me" value="1" <?=$remember_me == 1?'checked':'';?> /><span>Store my UserID on this computer</span></label>
          			<input name="submit" type="submit" value="Submit" />
          		</div>
          		<div class="lgnPgLnk"><a style="cursor: pointer;">Forgot your Password?</a></div>
        	</form>
        	</div>
       		<div class="lgnFldPnl"  id="id_forgot" style="display: none;">
       		<form action="forgot-password.php" method="post" name="frmforgot" onsubmit="return validate_forgot();">	
          		<input name="email" type="email" value="" />
          		<div class="lgnSbmtPnl">
          			<input name="submit" type="submit" value="Submit" />
          		</div>
          		<div class="lgnPgLnk"><a style="cursor: pointer;">&laquo; Back to login page</a></div>
          	</form>	
        	</div>        	
		</div>
		<img src="images/secureArea.jpg" class="btmSqrImg" alt="Secure Area" />
	</div>
</body>
</html>
