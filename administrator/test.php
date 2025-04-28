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
<title>
<?=$general_func->site_title?> :: Secure Area</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="../includes/validator.js"></script>
<script language="javascript">
function validate(){
	/*if(!validate_text(document.frmlogin.username,1,"Enter User ID")){
		document.frmlogin.username.value="";
        document.frmlogin.username.focus();
		return false;
	}	
	if(!validate_text(document.frmlogin.pawd,1,"Enter Password")){
		document.frmlogin.pawd.value="";
        document.frmlogin.pawd.focus();
		return false;
	}	*/	
}
</script>
</head>
<body>
<table width="464" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="left" valign="bottom"><table width="464" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td colspan="2" height="40"></td>
        </tr>
        <tr>
          <td valign="baseline" align="left"><img src="images/logo.jpg"  alt="<?=$general_func->site_title?>" title="<?=$general_func->site_title?>" style="margin-bottom: 10px;" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="8" align="left" valign="top"><img src="images/secure-area-left.jpg" alt="" width="8" height="36" /></td>
                <td align="center" valign="middle"  bgcolor="<?=$general_func->deep_bg?>" class="loginpage_secure-area">Secure Area</td>
                <td width="8" align="right" valign="top"><img src="images/secure-area-right.jpg" alt="" width="8" height="36" /></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td align="left" valign="top" class="body_whitebg"><form action="verified.php" method="post" name="frmlogin" onsubmit="return validate();">
              <input type="hidden" name="enter" value="1" />
              <table width="86%" border="0" align="center" cellpadding="0" cellspacing="0">
                <?php if(isset($_SESSION['message']) && trim($_SESSION['message']) != NULL){?>
                <tr>
                  <td height="67" align="center" valign="middle" class="loginpage_alert"><?=$_SESSION['message']?></td>
                </tr>
                <?php
		 $_SESSION['message']="";	
		 } else{?>
                <tr>
                  <td height="30" align="center" valign="middle" class="loginpage_alert"></td>
                </tr>
                <?php }?>
                <tr>
                  <td><table width="85%" border="0" align="center" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="33%" height="24" align="center" valign="middle" bgcolor="<?=$general_func->deep_bg?>" class="loginpage_user-area">Enter User ID :</td>
                        <td align="left" valign="middle"><input name="username"  type="text" class="loginpage_inputbox" autocomplete="off" value="<?=$username?>" size="30" /></td>
                      </tr>
                      <tr>
                        <td height="12" colspan="2" align="left" valign="top"><img src="images/spacer.gif" alt="" width="12" height="12" /></td>
                      </tr>
                      <tr>
                        <td width="33%" height="24" align="center" valign="middle" bgcolor="<?=$general_func->deep_bg?>" class="loginpage_user-area">Enter Password :</td>
                        <td align="left" valign="middle"><input name="pawd" type="password" autocomplete="off" value="<?=$pawd?>" class="loginpage_inputbox" /></td>
                      </tr>
                      <tr>
                        <td width="33%" align="left" valign="top">&nbsp;</td>
                        <td height="25" align="left" valign="bottom" class="loginpage_content-blue"><input type="checkbox" name="remember_me" value="1" <?=$remember_me == 1?'checked':'';?>>
                          Store my UserID on this computer</td>
                      </tr>
                      <tr>
                        <td height="45" colspan="2" align="left" valign="middle"><table border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                              <td align="center" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
                                  <tr>
                                    <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                                    <td align="left" valign="middle" class="body_tab-middilebg"><input name="submit" type="submit" class="submit1" value="Submit" /></td>
                                    <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                                  </tr>
                                </table></td>
                              <td width="12" align="center" valign="middle">&nbsp;</td>
                              <td align="center" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
                                  <tr>
                                    <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                                    <td align="left" valign="middle" class="body_tab-middilebg"><input name="reset" type="reset" class="submit1" value="Reset" /></td>
                                    <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                                  </tr>
                                </table></td>
                            </tr>
                          </table></td>
                      </tr>
                    </table></td>
                </tr>
              </table>
            </form></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top">&nbsp;</td>
  </tr>
</table>

</html>
