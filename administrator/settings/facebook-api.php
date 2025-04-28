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
	if(!$validator->validate_text($_REQUEST['app_id'])){
		$_SESSION['msg'] .= "APP ID should not be blank! <br/>";
		$error_found=1;
	}	
		
	if(!$validator->validate_text($_REQUEST['app_secret'])){
		$_SESSION['msg'] .= "APP SECRET should not be blank! <br/>";
		$error_found=1;
	}	
		
	//******************************************************//
	if(intval($error_found) == 0){
		$data['app_id']=trim($_REQUEST['app_id']);
		$data['app_secret']=trim($_REQUEST['app_secret']);		
				
		$db->query_update("third_party_api",$data,"id=1");
		
		
		$_SESSION['facebook_id']=trim($_REQUEST['app_id']);
		$_SESSION['facebook_secret']=trim($_REQUEST['app_secret']);
					
		
		if($db->affected_rows > 0)
			$_SESSION['msg']="Facebook API details successfully updated!";	
	}
	
		
	$general_func->header_redirect($_SERVER['PHP_SELF']);
}
	

$sql="select * from third_party_api where id=1 limit 1";
$result=$db->fetch_all_array($sql);


if(count($result) == 1){
	$app_id=$result[0]['app_id'];
	$app_secret=$result[0]['app_secret'];	
}else{
	$app_id="";
	$app_secret="";
}
?>

<script language="javascript" type="text/javascript"> 
function validate(){
	if(!validate_text(document.ff.app_id,1,"APP ID should not be blank"))
		return false;
		
	if(!validate_text(document.ff.app_secret,1,"APP SECRET should not be blank"))
		return false;	
}
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
   
    <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg">Facebook API</td>
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
                  <td width="14%" class="body_content-form" valign="top">APP ID:<font class="form_required-field"> *</font></td>
                  <td width="86%" valign="top"><input name="app_id" value="<?=$app_id?>" type="text" autocomplete="off" class="form_inputbox" size="40" /></td>
                </tr>
                <tr>
                  <td width="14%" class="body_content-form" valign="top">APP SECRET:<font class="form_required-field"> * </font></td>
                  <td width="86%" valign="top"><input name="app_secret" value="<?=$app_secret?>" type="text" autocomplete="off" class="form_inputbox"  size="40"/></td>
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
