<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";


$original=$path_depth . "banner_images/";
$data=array();
$return_url=$_REQUEST['return_url'];

if(isset($_GET['now']) && $_GET['now']=="DELETE"){
	$path=$_REQUEST['path'];
	$field=$_REQUEST['field'];
	
	@mysql_query("update banners set $field=' ' where id=" . (int) $_GET['id'] . "");	
	@unlink($original.$path);
			
	$redirect_path=basename($_SERVER['PHP_SELF']) . "?id=".$_GET['id']."&action=EDIT&return_url=".$return_url;
	$general_func->header_redirect($redirect_path);
}

if(isset($_REQUEST['action']) && $_REQUEST['action']=="EDIT"){
	$sql="select * from banners where id=" . (int) $_REQUEST['id'] . "";
	$result=$db->fetch_all_array($sql);	
	
	$page_id=$result[0]['page_id'];
	$banner_description=$result[0]['banner_description'];
	$banner_path=$result[0]['banner_path'];
	$banner_link=$result[0]['banner_link'];
	$banner_target=$result[0]['banner_target'];
		
	$button="Update";
}else{
	$page_id="";
	$banner_description="";
	$banner_path="";
	$banner_link="";
	$banner_target=1;
	
	$button="Add New";
}


if(isset($_POST['enter']) && $_POST['enter']=="yes" && trim($_POST['login_form_id'])==$_SESSION['login_form_id']){
	$banner_link=filter_var(trim($_REQUEST['banner_link']), FILTER_SANITIZE_URL);
	$banner_target=$_REQUEST['banner_target'];	
	$page_id=$_REQUEST['page_id'];
	$banner_description=filter_var(trim($_REQUEST['banner_description']), FILTER_SANITIZE_STRING);
	
	
	if($_POST['submit']=="Add New"){		
		if($db->already_exist_inset("banners","page_id",$page_id)){
			$_SESSION['msg']="Sorry, banner already uploaded for your specified page!";		
		}else{
			$data['page_id']=$page_id;
			$data['banner_description']=$banner_description;
			$data['banner_link']=$banner_link;
			$data['banner_target']=$banner_target;	
			$data['banner_type']=2;	
			$data['date_added']='now()';
				
			//*************************  Upload photo *************************************//
			if($_FILES['banner_path']['size'] >0 && $general_func->valid_file_type($_FILES["banner_path"]["name"],$_FILES["banner_path"]["type"])){
				
				$inserted_id = $db->query_insert("banners",$data);
				
				$uploaded_name=array();
						
				$userfile_name=$_FILES['banner_path']['name'];
				$userfile_tmp= $_FILES['banner_path']['tmp_name'];
				$userfile_size=$_FILES['banner_path']['size'];
				$userfile_type= $_FILES['banner_path']['type'];
							
				$path=$inserted_id."_".$general_func->remove_space_by_hypen($security_validator->sanitize_filename($userfile_name));	
				
				$img=$original.$path;
				move_uploaded_file($userfile_tmp, $img) or die();
							
				$uploaded_name['banner_path']=$path;
				$db->query_update("banners",$uploaded_name,'id='.$inserted_id);
				
				$_SESSION['msg']="Banner successfully added!";
			}else{
				$_SESSION['msg']="Please choose a banner image!";	
			}
			
		}
		
		$general_func->header_redirect($_SERVER['PHP_SELF']);
		
	}else{		
		if($db->already_exist_update("banners","id",$_REQUEST['id'],"page_id",$page_id)){
			$_SESSION['msg']="Sorry, banner already uploaded for your specified page!";		
		}else{			
			$data['banner_link']=$banner_link;
			$data['banner_target']=$banner_target;	
			$data['page_id']=$page_id;
			$data['banner_description']=$banner_description;	
			$db->query_update("banners",$data,"id='".$_REQUEST['id'] ."'");
				
			//*************************  Upload photo *************************************//
			if($_FILES['banner_path']['size'] >0 && $general_func->valid_file_type($_FILES["banner_path"]["name"],$_FILES["banner_path"]["type"])){
				@unlink($original.$_REQUEST['banner_path']);
					
				$uploaded_name=array();
						
				$userfile_name=$_FILES['banner_path']['name'];
				$userfile_tmp= $_FILES['banner_path']['tmp_name'];
				$userfile_size=$_FILES['banner_path']['size'];
				$userfile_type= $_FILES['banner_path']['type'];
							
				$path=$_REQUEST['id'] ."_".$general_func->remove_space_by_hypen($security_validator->sanitize_filename($userfile_name));	
				$img=$original.$path;
				move_uploaded_file($userfile_tmp, $img) or die();
							
				$uploaded_name['banner_path']=$path;
				$db->query_update("banners",$uploaded_name,'id='.$_REQUEST['id']);
			}
				
			//*************************  / Upload photos *************************************//
			
			if($db->affected_rows > 0)
				$_SESSION['msg']="Banner successfully updated!";
			
		}				
		$general_func->header_redirect($return_url);		
	}
}
?>
<script type="text/javascript" src="<?=$general_func->site_url?>highslide/highslide.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$general_func->site_url?>highslide/highslide.css" />

<script type="text/javascript">
	hs.graphicsDir = '<?=$general_func->site_url?>highslide/graphics/';
	hs.wrapperClassName = 'wide-border';
	
function validate(){		
	if(document.ff.page_id.selectedIndex == 0){
		alert("Please select a page name");
		document.ff.page_id.focus();
		return false;
	}
				
}
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg"><?=$button?> Inner Page Banner</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg">
    <form method="post" action="<?=$_SERVER['PHP_SELF']?>" name="ff" enctype="multipart/form-data"  onsubmit="return validate()">
        <input type="hidden" name="enter" value="yes" />
        <input type="hidden" name="id" value="<?=$_REQUEST['id']?>" />
        <input type="hidden" name="banner_path" value="<?=$banner_path?>" />        
        <input type="hidden" name="return_url" value="<?php echo $_REQUEST['return_url']?>" />
        <input type="hidden" name="login_form_id" value="<?=$_SESSION['login_form_id']?>" />
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
            <td align="left" valign="top" colspan="3"><table width="82%" border="0"  align="center" cellspacing="2" cellpadding="6">
               <tr>
                 	<td class="body_content-form" width="17%"> Page Name:<font class="form_required-field"> *</font></td>
                    <td width="83%">
                     <select name="page_id" class="cont-select" style="width: 200px;">
                     	<option value="">Select One</option>
	                     <?php
	                	$sql_pages="select id,link_name from static_pages where id<> 7 order by link_name ASC";
						$result_pages=$db->fetch_all_array($sql_pages);	
						$total_pages=count($result_pages);
						
						for($page=0; $page < $total_pages; $page++){?>
							<option value="<?=$result_pages[$page]['id']?>" <?=$page_id==$result_pages[$page]['id']?'selected="selected"':'';?> ><?=$result_pages[$page]['link_name']?></option>
						<?php } ?>	
	                    </select>
                          </td>
                    </tr>
                 <tr>
                  <td  class="body_content-form" valign="top"><?=trim($banner_path) != NULL?'Update':'Upload';?> Banner Image:<font class="form_required-field"> *</font></td>
                  <td  valign="top" style="font-size:11px;">
                  <?php if(trim($banner_path) != NULL){?>
                    <a href="<?=$general_func->site_url.substr($original,6).$banner_path?>" class="highslide" onclick="return hs.expand(this)"><img src="<?=$general_func->site_url.substr($original,6).$banner_path?>" border="0" width="200" /></a>&nbsp;&nbsp;
                    <?php if($button=="Update"){?>
                    <a href="banners/<?=basename($_SERVER['PHP_SELF'])?>?action=EDIT&now=DELETE&id=<?=$_REQUEST['id']?>&return_url=<?=$return_url?>&field=banner_path&path=<?=$banner_path?>" class="htext" ><strong>Delete</strong></a>
                    <?php }?>
                    <br/>
                    <br/>
                    <?php }	?>
                  <input name="banner_path" type="file"  class="form_inputbox" size="55" /><br/>
                  <strong>Banner width and height must be 1600px and 277px respectively.</strong></td>
                </tr>                
                 <tr>
                  <td  class="body_content-form" valign="top">Link:</td>
                  <td  valign="top"><input name="banner_link" type="text" value="<?=$banner_link?>" AUTOCOMPLETE=OFF class="form_inputbox" size="65" /> <br />(Enter full path, if you have any. e.g. http://test.com/test)</td>
                </tr>
                <tr>
                  <td  class="body_content-form" valign="top">Target:</td>
                  <td  valign="top"> <select name="banner_target"  class="inputbox_select" style="width: 140px; padding: 2px 1px 2px 0px;" >
                      <option value="">Choose One</option>
                      <option value="1" <?=$banner_target==1?'selected="selected"':'';?>>Same Window</option>
                      <option value="2" <?=$banner_target==2?'selected="selected"':'';?>>New Window</option>
                  </select></td>
                </tr>
                <tr>
                  <td colspan="2" class="body_content-form" height="10"></td>
                </tr>
                <tr>
                  <td  class="body_content-form">&nbsp;</td>
                  <td ><table width="261" border="0" align="left" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="41%"><table border="0" align="left" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="submit" type="submit" class="submit1" value="<?=$button?>" /></td>
                              <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                            </tr>
                          </table></td>
                        <td width="10%">&nbsp;</td>
                        <td width="49%"><table border="0" align="left" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="back" onclick="location.href='<?=$general_func->admin_url?>banners/inner-page-banners.php'"  type="button" class="submit1" value="Back" /></td>
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