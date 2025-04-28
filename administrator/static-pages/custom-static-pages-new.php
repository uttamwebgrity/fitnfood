<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";

$data=array();
$return_url=$_REQUEST['return_url'];

if(isset($_REQUEST['action']) && $_REQUEST['action']=="EDIT"){
	$sql="select * from static_pages where id=" . intval($_REQUEST['id']) . " limit 1";
	$result=$db->fetch_all_array($sql);
		
	$page_heading=$result[0]['page_heading'];
	$page_name=$result[0]['page_name'];
	$seo_link=$result[0]['seo_link'];
	$title=$result[0]['title'];
	$keyword=$result[0]['keyword'];
	$description=$result[0]['description'];
	
	$link_path=$result[0]['link_path'];
	$page_target=$result[0]['page_target'];
	
	$page_position=array();
	$page_position=@explode(",",$result[0]['page_position']);	
	
	$display_order=$result[0]['display_order'];
	$parent_id=$result[0]['parent_id'];
	$file_data=$result[0]['file_data'];
	
	$button="Update";
}else{
	$page_name="";
	$page_heading="";
	$title="";
	$keyword="";
	$description="";
	$seo_link="";
	
	$link_path="";
	$page_target="";
	
	$page_position=array();
	$display_order=$db->max_id("static_pages","display_order") + 1;
	$parent_id="";
	$file_data="";
		
	$button="Add New";
}


if(isset($_POST['enter']) && $_POST['enter']=="yes" && trim($_POST['login_form_id'])==$_SESSION['login_form_id']){	

	$page_heading=filter_var(trim($_REQUEST['page_heading']), FILTER_SANITIZE_STRING);
	$title=filter_var(trim($_REQUEST['title']), FILTER_SANITIZE_STRING);
	$keyword=filter_var(trim($_REQUEST['keyword']), FILTER_SANITIZE_STRING);
	$description=filter_var(trim($_REQUEST['description']), FILTER_SANITIZE_STRING);	
	$link_path=filter_var(trim($_REQUEST['link_path']), FILTER_SANITIZE_URL); 
	$page_target=$_REQUEST['page_target'];	
	$page_position=$_REQUEST['page_position'];
	$display_order=filter_var(trim($_REQUEST['display_order']), FILTER_SANITIZE_NUMBER_INT); 
	$parent_id=$_REQUEST['parent_id'];
	$file_data=$security_validator->replace_data_before_inserted(trim($_REQUEST['file_data']));	
	$page_position=@implode(",",$_REQUEST['page_position']);	
		
	
	if($_POST['submit']=="Add New"){
		if($db->already_exist_inset("static_pages","page_heading",$page_heading)){
			$_SESSION['msg']="Sorry, your specified page is already taken!";
		}else{
			$data['page_heading']=$page_heading;
			$data['link_name']=$page_heading;	
			$data['title']=$title;
			$data['keyword']=$keyword;
			$data['description']=$description;			
			$data['link_path']=$link_path;
			$data['page_target']=$page_target;
			$data['page_position']=$page_position;
			$data['display_order']=$display_order;
			$data['parent_id']=$parent_id;
			$data['file_data']=$file_data;
		
			$data['seo_link']=$general_func->create_seo_link($page_heading);
			
			//*** check whether this name alreay exit ******//
			if($db->already_exist_inset("static_pages","seo_link",$data['seo_link'])){//******* exit
				$data['seo_link']=$db->max_id("static_pages","id") + 1 ."-".$data['seo_link'];
			}
			//*********************************************//
			$filename = $path_depth . $data['seo_link'].".php";
			$data['page_name']=$data['seo_link'].".php";
			
			$db->query_insert("static_pages",$data);
			
			if(!file_exists($filename)){
				//********************  create the physical page *****************//
				
				$page_content.="<?php
								include_once(\"includes/header.php\");
								?>
								<div class=\"inrBnr\">
									<?php \$db_common->static_page_banner(\$dynamic_content['page_id']);?>									
								</div>
								
								<div class=\"bodyContent\">
								  <div class=\"mainDiv2\">
								  	<h1><?php echo trim(\$dynamic_content['title']); ?></h1>
								    <p><?php echo trim(\$dynamic_content['file_data']); ?></p>
								  </div>
								</div>
								<?php
								include_once(\"includes/footer.php\");
								?>";
				
							
				if (!$handle = fopen($filename, 'w')){
					print "Cannot open file ($filename)";
					exit;
				}
				
				if (!fwrite($handle, $page_content)){
					print "Cannot write to file ($filename)";
					exit;
				}
				fclose($handle);		
				
				//***************************************************************//					
			}
			
			$_SESSION['msg']=$page_heading . " page successfully created!";
			$general_func->header_redirect($_SERVER['PHP_SELF']);
		}	

	}else{
		
		if($db->already_exist_update("static_pages","id",$_REQUEST['id'],"page_heading",$page_heading)){
			$_SESSION['msg']="Sorry, your specified page  is already taken!";			
		}else{			
			$data['page_heading']=$page_heading;
			$data['link_name']=$page_heading;			
			$data['title']=$title;
			$data['keyword']=$keyword;
			$data['description']=$description;			
			$data['link_path']=$link_path;
			$data['page_target']=$page_target;
			$data['page_position']=$page_position;
			$data['display_order']=$display_order;
			$data['parent_id']=$parent_id;
			$data['file_data']=$file_data;			
			
			$data['seo_link']=$general_func->create_seo_link($page_heading);
			
			
			
			//*** check whether this name alreay exit ******//
			if($db->already_exist_inset("static_pages","id",$_REQUEST['id'],"seo_link",$data['seo_link'])){//******* exit
				$data['seo_link']==$_REQUEST['id']."-".$data['seo_link'];
			}
			//*********************************************/
			
			if(trim($data['seo_link']) != trim($_REQUEST['old_seo_link'])){//************** rename the old page			
				$filename = $path_depth . $data['seo_link'].".php";
				$data['page_name']=$data['seo_link'].".php";				
				$old_filename=$path_depth . trim($_REQUEST['old_seo_link']).".php";
							
				rename($old_filename,$filename);
			}	
			
			
			$db->query_update("static_pages",$data,"id='".$_REQUEST['id'] ."'");
					
						
			if($db->affected_rows > 0)
				$_SESSION['msg']=$page_heading . " page successfully updated.";
			
			
			$general_func->header_redirect($return_url);
		}
	}
}	

?>
<script language="javascript" type="text/javascript"> 
function validate(){
	if(!validate_text(document.ff.page_heading,1,"Enter Page/Link Name"))
		return false;
	if(!validate_text(document.ff.title,1,"Enter Page Title"))
		return false;
	
	if(document.ff.link_path.value != ""){
		if(document.ff.page_target.selectedIndex == 0){
			alert("Select External Page Target");
			return false;
		}			
	}	
	
	var check=false;
	var frm=document.ff;
	if(parseInt(frm.page_position.length)){
		for(var i=0;i<frm.page_position.length;i++){
			if(frm.page_position[i].checked == true){
				check=true;
				break;
			}	
		}
		
		if(check == false){
			alert("Please select at least a page position");
			return false;
		}	
		
	}else{
		if(frm.page_position.checked == false){
			alert("Please select at least a page position");
			return false;
		}	
	}			
	
	if(!validate_text(document.ff.display_order,1,"Enter display order"))
		return false;
			
	if(!validate_integer(document.ff.display_order,1,"Display order must be a number"))
		return false;

				
}

</script>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg"><?=$button?>
            Static Page</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg"><form method="post" action="<?=$_SERVER['PHP_SELF']?>"  name="ff" onsubmit="return validate()">
        <input type="hidden" name="enter" value="yes" />
        <input type="hidden" name="id" value="<?=$_REQUEST['id']?>" />
         <input type="hidden" name="login_form_id" value="<?=$_SESSION['login_form_id']?>" />
          <input type="hidden" name="return_url" value="<?=$return_url?>" />
         
        <input type="hidden" name="old_page_name" value="<?php echo $page_name; ?>" />
        <input type="hidden" name="old_seo_link" value="<?php echo $seo_link; ?>" />
        
        <table width="986" border="0" align="left" cellpadding="0" cellspacing="0">
          <tr>
            <td colspan="2" height="30"></td>
          </tr>
          <?php if(isset($_SESSION['msg']) && trim($_SESSION['msg']) != NULL){?>
          <tr>
            <td colspan="2" class="message_error"><?=$_SESSION['msg'];$_SESSION['msg']=""; ?></td>
          </tr>
          <tr>
            <td colspan="2" class="body_content-form" height="30"></td>
          </tr>
          <?php  } ?>
          <tr>
            <td width="76" align="left" valign="top"></td>
            <td width="797" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="10">
                
                <tr>
                  <td width="17%" class="body_content-form">Page/Link Name:<font class="form_required-field"> *</font></td>
                  <td width="83%"><input name="page_heading" value="<?=$page_heading?>" type="text" autocomplete="off" class="form_inputbox" size="73" /> ( e.g. Home, About Us, etc.)</td>
                </tr>
                <tr>
                  <td width="17%" class="body_content-form">Page Title:<font class="form_required-field"> *</font></td>
                  <td width="83%"><input name="title" value="<?=$title?>" type="text" autocomplete="off" class="form_inputbox" size="73" /> ( e.g. Welcome to <?=$general_func->site_title?>)</td>
                </tr>
                 <tr>
                  <td width="17%" class="body_content-form">Meta keywords:</td>
                  <td width="83%"><textarea name="keyword" class="form_textarea" cols="70" rows="6"><?=$keyword?></textarea></td>
                </tr> 
               
                <tr>
                  <td width="17%" class="body_content-form">Meta Description:</td>
                  <td width="83%"><textarea name="description" class="form_textarea" cols="70" rows="6"><?=$description?></textarea></td>
                </tr>
                <tr>
                  <td width="17%" class="body_content-form" valign="top">External Links:</td>
                  <td width="83%" valign="top"><input name="link_path" type="text" value="<?=$link_path?>" AUTOCOMPLETE=OFF class="form_inputbox" size="65" /> <br />(Enter full path, if you have any. e.g. http://test.com/test)</td>
                </tr>
                
                 
                <tr>
                  <td width="17%" class="body_content-form" valign="top">Target (if external inserted):</td>
                  <td width="83%" valign="top"> <select name="page_target"  class="inputbox_select" style="width: 200px; padding: 2px 1px 2px 0px;" >
                      <option value="">Choose One</option>
                      <option value="1" <?=$page_target==1?'selected="selected"':'';?>>Same Window</option>
                      <option value="2" <?=$page_target==2?'selected="selected"':'';?>>New Window</option>
                  </select></td>
                </tr>
                
                 <tr>
                  <td width="17%" class="body_content-form" valign="top">Page Position:<font class="form_required-field"> *</font></td>
                  <td width="83%" valign="middle">
                  	<!--<input type="checkbox" name="page_position[]" id="page_position" value="1" <?=in_array(1,$page_position)?'checked="checked"':'';?>>Header&nbsp;&nbsp;&nbsp;&nbsp;-->
                  	<input type="checkbox" name="page_position[]" id="page_position" value="4" <?=in_array(4,$page_position)?'checked="checked"':'';?>>Footer&nbsp;&nbsp;&nbsp;&nbsp;
                  	<input type="checkbox" name="page_position[]" id="page_position" value="5" <?=in_array(5,$page_position)?'checked="checked"':'';?>>Do not show on menu
                  	</td>
                </tr> 
            
                <tr>
                  <td width="17%" class="body_content-form">Display Order:<font class="form_required-field"> *</font></td>
                  <td width="83%"><input name="display_order" value="<?=$display_order?>" type="text" autocomplete="off" class="form_inputbox" size="5" /></td>
                </tr>
               
                <tr>
                 	<td class="body_content-form" width="17%">Parent Page:</td>
                    <td width="83%">
                     <select name="parent_id" class="cont-select" style="width: 200px;">
                     	<option value="0">Parent Page</option>
	                     <?php
	                	$sql_pages="select id,link_name from static_pages where parent_id = 0 order by link_name ASC";
						$result_pages=$db->fetch_all_array($sql_pages);	
						$total_pages=count($result_pages);
						
						for($page=0; $page < $total_pages; $page++){?>
							<option value="<?=$result_pages[$page]['id']?>" <?=$parent_id==$result_pages[$page]['id']?'selected="selected"':'';?> ><?=$result_pages[$page]['link_name']?></option>
						<?php } ?>	
	                    </select>
                          </td>
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
              
            </table></td>
            <td width="113" align="left" valign="top" height="30">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="4" height="30" align="center"><table width="879" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="32%"><table border="0" align="right" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                        <td align="left" valign="middle" class="body_tab-middilebg"><input name="submit" type="submit" class="submit1" value="<?=$button?>" /></td>
                        <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                      </tr>
                  </table></td>
                  <td width="5%"></td>
                  <td width="63%"><?php if($button !="Add New"){?>
                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                        <td align="left" valign="middle" class="body_tab-middilebg"><input name="back" onclick="location.href='<?=$return_url?>'"  type="button" class="submit1" value="Back" /></td>
                        <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                      </tr>
                    </table>
                    <?php  }else 
							echo "&nbsp;";
						 ?></td>
                </tr>
              </table></td>
          </tr>
          <tr>
            <td colspan="4" height="30"></td>
          </tr>
        </table>
      </form></td>
  </tr>
</table>
<?php
include("../foot.htm");
?>