<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";

$original=$path_depth . "eating_schedule/";

$data=array();

$return_url=$_REQUEST['return_url'];

if(isset($_REQUEST['action']) && $_REQUEST['action']=="EDIT"){
	$sql="select * from meal_schedule_pdf where id=" .  intval($_REQUEST['id']) . " limit 1";
	$result=$db->fetch_all_array($sql);
	$pdf_file_name=$result[0]['pdf_file_name'];	
	$button="Add New";
}


if(isset($_POST['enter']) && $_POST['enter']=="yes" && trim($_POST['login_form_id'])==$_SESSION['login_form_id']){
	
	//*************************  Upload PDF *************************************//
	if($_FILES['pdf_file_name']['size'] >0 && $general_func->valid_file_type_only($_FILES["pdf_file_name"]["name"],$_FILES["pdf_file_name"]["type"],"pdf")){
								
		@unlink($original.$_REQUEST['pdf_file_name']);
							
		$uploaded_name=array();					
		$userfile_name=$_FILES['pdf_file_name']['name'];
		$userfile_tmp= $_FILES['pdf_file_name']['tmp_name'];
		$userfile_size=$_FILES['pdf_file_name']['size'];
		$userfile_type= $_FILES['pdf_file_name']['type'];
									
		$path=time()."_".$general_func->remove_space_by_hypen($security_validator->sanitize_filename($userfile_name));	
		$img=$original.$path;
		move_uploaded_file($userfile_tmp, $img) or die();
									
		$uploaded_name['pdf_file_name']=$path;
		$db->query_update("meal_schedule_pdf",$uploaded_name,'id='.$_REQUEST['id']);
		$_SESSION['msg']="PDF file successfully updated!";				
	}else{
		$_SESSION['msg']="Only PDF file can be uploaded!";			
	}
			
	$general_func->header_redirect($return_url);
	
}	


?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg"><?=$button?> Eating Schedule PDF</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg">
    <form enctype="multipart/form-data" method="post" action="<?=$_SERVER['PHP_SELF']?>" name="ff"  onsubmit="return validate()">
        <input type="hidden" name="enter" value="yes" />
        <input type="hidden" name="id" value="<?=$_REQUEST['id']?>" />
        <input type="hidden" name="return_url" value="<?php echo $_REQUEST['return_url']?>" />
         <input type="hidden" name="login_form_id" value="<?=$_SESSION['login_form_id']?>" />
         <input type="hidden" name="photo_name1" value="<?=$photo_name1?>" />
         <input type="hidden" name="photo_name2" value="<?=$photo_name2?>" />
         <input type="hidden" name="photo_name3" value="<?=$photo_name3?>" />
         
        <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td colspan="3" class="body_content-form" height="30"></td>
          </tr>
          <?php if(isset($_SESSION['msg']) && trim($_SESSION['msg']) != NULL){?>
          <tr>
            <td colspan="3" class="message_error"><?=$_SESSION['msg'];$_SESSION['msg']=""; ?></td>
          </tr>
          <tr>
            <td colspan="3" class="body_content-form" height="30"></td>
          </tr>
          <?php  } ?>
          <tr>
            <td align="left" valign="top" colspan="3"><table width="79%" border="0"  align="center" cellspacing="2" cellpadding="6">
                <tr>
                  <td width="20%" class="body_content-form" valign="top">PDF for:<font class="form_required-field"> *</font></td>
                  <td width="80%" valign="top">Eating Schedule <?=intval($_REQUEST['id'])?></td>
                </tr>
               <tr>
                  <td  class="body_content-form" valign="top">Upload File: </td>
                  <td  valign="top"> <?php if(trim($pdf_file_name) != NULL){?>
                    <a target="_blank" href="<?=$general_func->site_url.substr($original,6).$pdf_file_name?>" ><img src="images/pdf.png" /></a>&nbsp;&nbsp;
                   <br/>                    
                    <?php }	?>
                  <input name="pdf_file_name" type="file"   class="form_inputbox" size="55" /><br/>  </td>
                </tr>
               
                <tr>
                  <td colspan="2" class="body_content-form" height="10"></td>
                </tr>
                <tr>
                  <td width="15%" class="body_content-form">&nbsp;</td>
                  <td width="85%"><table width="261" border="0" align="left" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="41%"><table border="0" align="left" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="submit" type="submit" class="submit1" value="Upload" /></td>
                              <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                            </tr>
                          </table></td>
                        <td width="10%">&nbsp;</td>
                        <td width="49%"><table border="0" align="left" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="back" onclick="location.href='<?=$general_func->admin_url?>settings/eating-sehedule-file.php'"  type="button" class="submit1" value="Back" /></td>
                              <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                            </tr>
                          </table></td>
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