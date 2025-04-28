<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";


if((int)$_SESSION['admin_access_level'] == 2){		
    $_SESSION['message']="Sorry, you do not have the permission to access this page!";
	$general_func->header_redirect($general_func->admin_url."home.php");
}



if(isset($_POST['enter']) && $_POST['enter']=="yes"){
	
	$data=array();
	
	$data['title']=$_REQUEST['title'];
	$data['keyword']=$_REQUEST['keyword'];
	$data['description']=$_REQUEST['description'];	
	$data['file_data']=$_REQUEST['file_data'];
	
	
	
	
	$db->query_update("static_pages",$data,"id='".$_REQUEST['s'] ."'");
	
	if($db->affected_rows > 0)
		$_SESSION['msg']="Data successfully saved!";
		
			
	$general_func->header_redirect("index.php?s=".$_REQUEST['s']);
	
}



$s=(isset($_REQUEST['s']) && (int)$_REQUEST['s'] >0)?(int)$_REQUEST['s'] :1;


$sql="select * from static_pages where id='" . $s . "'";
$result=$db->fetch_all_array($sql);
$file_id=$result[0]['id'];
$file_data=$result[0]['file_data'];
$link_name=$result[0]['link_name'];

$title=$result[0]['title'];
$description=$result[0]['description'];
$keyword=$result[0]['keyword'];



?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg another_bg_color">CONTENT FOR - [<?=strtoupper($link_name)?>]</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg"><form method="post" action="<?=$_SERVER['PHP_SELF']?>" name="ff">
        <input type="hidden" name="enter" value="yes" />
         <input type="hidden" name="s" value="<?=$s?>" />
        <table width="952" border="0" align="left" cellpadding="6" cellspacing="0">
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
            	<td height="28"  colspan="2" valign="middle" class="htext" style="padding-left: 60px; padding-bottom: 15px;"><strong>Page Meta Information</strong></td>
                		
            </tr>
             
            
            
          
          <tr>
            	<td width="20%" height="28" align="right" valign="middle" class="htext" style="text-align: right;">Title:</td>
                <td width="80%" class="BodyText-LGR" valign="top"><input type="text" AUTOCOMPLETE=OFF class="form_inputbox" name="title" value="<?=$title?>" size="93" /> </td>			
            </tr>
            <tr>
            	<td width="20%" height="28" align="right" valign="top" class="htext"  style="text-align: right;">Keywords:</td>
                <td width="80%" class="BodyText-LGR" valign="top"><textarea name="keyword"  AUTOCOMPLETE=OFF class="form_textarea" cols="90" rows="5"><?=$keyword?></textarea> </td>			
            </tr>
            <tr>
             	<td width="20%" height="28" align="right" valign="top" class="htext"  style="text-align: right;">Description:</td>
                <td width="80%" class="BodyText-LGR" valign="top"><textarea name="description"  AUTOCOMPLETE=OFF class="form_textarea"  cols="90" rows="5"><?=$description?></textarea> </td>			
            </tr>
             <?php if($s != 7){?>
             <tr>
            	<td height="28"  colspan="2" valign="middle" class="htext" style="padding: 40px 0 15px 60px;"><strong>Page Content Information</strong></td>
            </tr>            
           <tr>
          	<td align="center" colspan="2" style="padding-left: 20px;" >
                 <?php
					include("../fckeditor/fckeditor.php") ;
					$sBasePath ="fckeditor/";
					$oFCKeditor = new FCKeditor('file_data') ;
					$oFCKeditor->BasePath	= $sBasePath ;
					$oFCKeditor->Height = '400' ;
					$oFCKeditor->width = '400' ;
					$oFCKeditor->Value		= $file_data;
					$oFCKeditor->Create();
					?>
                
            </td>
          </tr>
          <?php } ?>
          <tr>
            <td align="left" valign="top" colspan="2" height="20px;"></td>
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
          	<td height="30" colspan="2"></td>
          </tr>
        </table>
      </form></td>
  </tr>
</table>
<?php
include("../foot.htm");
?>
