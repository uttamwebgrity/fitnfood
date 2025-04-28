<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";


$data=array();



if(isset($_POST['enter']) && $_POST['enter']=="yes" && trim($_POST['login_form_id'])==$_SESSION['login_form_id']){	
	$data['template_subject']=$_REQUEST['template_subject'];
	$data['template_content']=$_REQUEST['template_content'];	
	$db->query_update("email_template",$data,"id=".$_REQUEST['id']);	
	$_SESSION['msg']=$_REQUEST['name'] . " - Template successfully updated.";	
	$general_func->header_redirect($_SERVER['PHP_SELF']."?id=" . $_REQUEST['id'] . "&name=" . $_REQUEST['name']);
	
}	

$sql="select template_subject,template_content  from email_template where id='" .$_REQUEST['id']."'";
$result=$db->fetch_all_array($sql);
$template_content=$result[0]['template_content'];
$template_subject=$result[0]['template_subject'];
?>

<script type="text/javascript">
function validate(){
	if(!validate_text(document.ff.template_subject,1,"Enter email subject"))
		return false;
}
</script>
		
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg">Edit <?=$_REQUEST['name']?></td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg"><form method="post" action="<?=$_SERVER['PHP_SELF']?>" name="ff" onsubmit="return validate()">
        <input type="hidden" name="enter" value="yes" />
         <input type="hidden" name="id" value="<?=$_REQUEST['id']?>" />
          <input type="hidden" name="name" value="<?=$_REQUEST['name']?>" />
          <input type="hidden" name="login_form_id" value="<?=$_SESSION['login_form_id']?>" />
        <table width="883" border="0" align="left" cellpadding="0" cellspacing="0">
          <tr>
          	<td height="30" colspan="2"></td>
          </tr>
          <?php if(isset($_SESSION['msg']) && trim($_SESSION['msg']) != NULL){?>
          <tr>
            <td class="message_error" colspan="2"><?=$_SESSION['msg'];$_SESSION['msg']=""; ?></td>
          </tr>
          <tr>
            <td class="body_content-form" height="30" colspan="2"></td>
          </tr>
          <?php  } ?>
          <tr>
                  <td width="14%" class="body_content-form" align="right">Subject:<font class="form_required-field"> *</font></td>
                  <td width="86%" align="left">&nbsp;<input name="template_subject" value="<?=$template_subject?>" type="text" autocomplete="off" class="form_inputbox" size="100" />
                  </td>
                </tr>
           <tr>
            <td class="body_content-form" height="10" colspan="2"></td>
          </tr>
          
           <tr>
          	<td align="center" colspan="2" style="padding-left: 20px;" ><?php
					include("../fckeditor/fckeditor.php") ;
					$sBasePath ="fckeditor/";
					$oFCKeditor = new FCKeditor('template_content') ;
					$oFCKeditor->BasePath	= $sBasePath ;
					$oFCKeditor->Height = '400' ;
					$oFCKeditor->width = '400' ;
					$oFCKeditor->Value		= $template_content;
					$oFCKeditor->Create();
					?>
            
            </td>
          </tr>
          <tr>
            <td align="left" valign="top"  colspan="2" height="20px;"></td>
          </tr>
           <tr>
          	<td height="30" align="center" colspan="2"><table width="879" border="0" align="center" cellpadding="0" cellspacing="0">
<tr>
                  <td width="31%"></td>
                        <td width="24%"><table border="0" align="right" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="submit" type="submit" class="submit1" value="Update" /></td>
                              <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                            </tr>
                  </table></td>
                  <td idth="5%">&nbsp;</td>
                        <td width="40%">&nbsp;</td>
                </tr>
                    </table></td>
          </tr>
           <tr>
          	<td height="30"></td>
          </tr>
        </table>
      </form></td>
  </tr>
</table>
<?php
include("../foot.htm");
?>
