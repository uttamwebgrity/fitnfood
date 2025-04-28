<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";


$original = $path_depth . "video_files/";
$data=array();

$return_url=$_REQUEST['return_url'];


if (isset($_GET['now']) && $_GET['now'] == "DELETE") {
	$path = $_REQUEST['path'];
	$field = $_REQUEST['field'];
	@mysql_query("update videos set $field=' ' where id=" . intval(mysql_real_escape_string($_GET['id'])) . "");
	@unlink($original . $path);

	$redirect_path = basename($_SERVER['PHP_SELF']) . "?id=" . $_GET['id'] . "&action=EDIT&return_url=" . $return_url;
	$general_func -> header_redirect($redirect_path);
}



if(isset($_REQUEST['action']) && $_REQUEST['action']=="EDIT"){
	$sql="select * from videos where id=" . intval(mysql_real_escape_string($_REQUEST['id'])) . " limit 1";
	$result=$db->fetch_all_array($sql);
	
	$video_type_id=$result[0]['video_type_id'];	
	$video_name=$result[0]['video_name'];	
	$video_details=$result[0]['video_details'];	
	$video_code=$result[0]['video_code'];	
		
	$button="Update";
}else{
	$video_type_id="";
	$video_name="";
	$video_details="";
	$video_code="";	
	$button="Add New";
}


if(isset($_POST['enter']) && $_POST['enter']=="yes"  && trim($_POST['login_form_id'])==$_SESSION['login_form_id']){
			
	$video_name=filter_var(trim($_REQUEST['video_name']), FILTER_SANITIZE_STRING);	 
	$video_details=filter_var(trim($_REQUEST['video_details']), FILTER_SANITIZE_STRING);	
	$video_type_id=intval($_REQUEST['video_type_id']);	
	
	if($_POST['submit']=="Add New"){
		if($db->already_exist_inset("videos","video_type_id",$video_type_id,"video_name",$video_name)){
			$_SESSION['msg']="Sorry, your selected name is already taken!";
		}else{								
			if ($_FILES['video_code']['size'] > 0 && $general_func -> valid_specified_file_type_only($_FILES["video_code"]["name"],$_FILES["video_code"]["type"],"video/mp4","mp4")) {
				$data['video_type_id']=$video_type_id;	
				$data['video_name']=$video_name;			
				$data['video_details']=$video_details;					
				$data['date_added']=$current_date_time;	
				$inserted_id = $db->query_insert("videos",$data);			
					
					
				$uploaded_name = array();
				$userfile_name = $_FILES['video_code']['name'];
				$userfile_tmp = $_FILES['video_code']['tmp_name'];
				$userfile_size = $_FILES['video_code']['size'];
				$userfile_type = $_FILES['video_code']['type'];
	
				$path = $inserted_id . "_" . $general_func -> remove_space_by_hypen($security_validator -> sanitize_filename($userfile_name));
	
				$img = $original . $path;
				move_uploaded_file($userfile_tmp, $img) or die();
	
				$uploaded_name['video_code'] = $path;
				$db -> query_update("videos", $uploaded_name, 'id=' . $inserted_id);
	
				$_SESSION['msg']="MP4 video successfully uploaded.";
			} else {
				$_SESSION['msg']="Please upload your MP4 video.";
			}
				
			$general_func->header_redirect($_SERVER['PHP_SELF']);
		}	 

	}else{
		if($db->already_exist_update("videos","id",$_REQUEST['id'],"video_type_id",$video_type_id,"video_name",$video_name)){
			$_SESSION['msg']="Sorry, your specified name is already taken!";
		}else{
			$data['video_type_id']=$video_type_id;				
			$data['video_name']=$video_name;			
			$data['video_details']=$video_details;						
			$data['date_modified']=$current_date_time;	

			$db->query_update("videos",$data,"id='".$_REQUEST['id'] ."'");
			
			if ($_FILES['video_code']['size'] > 0 && $general_func -> valid_specified_file_type_only($_FILES["video_code"]["name"],$_FILES["video_code"]["type"],"video/mp4","mp4")) {
				@unlink($original . $_REQUEST['video_code']);
	
				$uploaded_name = array();
	
				$userfile_name = $_FILES['video_code']['name'];
				$userfile_tmp = $_FILES['video_code']['tmp_name'];
				$userfile_size = $_FILES['video_code']['size'];
				$userfile_type = $_FILES['video_code']['type'];
	
				$path = $_REQUEST['id'] . "_" . $general_func -> remove_space_by_hypen($security_validator -> sanitize_filename($userfile_name));
				$img = $original . $path;
				move_uploaded_file($userfile_tmp, $img) or die();
	
				$uploaded_name['video_code'] = $path;
				$db -> query_update("videos", $uploaded_name, 'id=' . $_REQUEST['id']);
			}
						
			if($db->affected_rows > 0)
				$_SESSION['msg']="Video information successfully updated.";
				
			$general_func->header_redirect($return_url);
		}
	}
}	

?>
<script type="text/javascript">
	
function validate(){	
	if(document.ff.video_type_id.selectedIndex == 0 ){
		alert("Please choose a video type");
		document.ff.video_type_id.focus();
		return false;		
	}
	if(!validate_text(document.ff.video_name,1,"Enter video name"))
		return false;
		
	if(!validate_text(document.ff.video_details,1,"Enter video details"))
		return false;
		
	
	/*
	if (!$('#video_code').val().match(/^[a-z0-9_-]+(\.mp4)$/i)) {
    	alert('Please select your MP4 file');
    	return false;
	}	*/		
}	
	
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
   <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg"><?=$button?> video</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg">
    <form enctype="multipart/form-data" method="post" action="<?=$_SERVER['PHP_SELF']?>"  name="ff" onsubmit="return validate();">
        <input type="hidden" name="enter" value="yes" />
        <input type="hidden" name="id" value="<?=$_REQUEST['id']?>" />
        <input type="hidden" name="login_form_id" value="<?=$_SESSION['login_form_id']?>" />       
        
        <input type="hidden" name="return_url" value="<?php echo $_REQUEST['return_url']?>" />
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
            <td align="left" valign="top" colspan="3"><table width="92%" border="0"  align="center" cellspacing="2" cellpadding="6">
                
                 <tr>
                  <td width="21%" class="body_content-form" valign="top">Video Type:<font class="form_required-field"> *</font></td>
                  <td width="21%" valign="top"> <select name="video_type_id" class="inputbox_select" style="width: 300px; padding: 2px 1px 2px 0px;">                         	
                          <option value="">Select One</option>
                          <?php
                          $sql_cat="select id,name from video_types order by name";
						  $result_cat=$db->fetch_all_array($sql_cat);
						  $total_cat=count($result_cat);
						  
						  for($cat=0; $cat < $total_cat; $cat++){ ?>
						  	<option value="<?=$result_cat[$cat]['id']?>" <?=intval($result_cat[$cat]['id'])==$video_type_id?'selected="selected"':'';?>><?=$result_cat[$cat]['name']?></option>	
						<?php } ?>
                        </select>
                  </td>
                </tr>
                <tr>
                  <td  class="body_content-form" valign="top">Video Name:<font class="form_required-field"> *</font></td>
                  <td width="79%" valign="top"><input name="video_name" type="text" value="<?=$video_name?>" AUTOCOMPLETE=OFF class="form_inputbox" size="75" /></td>
                </tr>                 
                  <tr>
                  <td  class="body_content-form">Video Details:<font class="form_required-field"> *</font></td>
                  <td ><textarea name="video_details" class="form_textarea" cols="70" rows="6"><?=$video_details?></textarea></td>
                </tr>
                
                 <tr>
                  <td width="17%" class="body_content-form" valign="top"><?=trim($video_code) != NULL ? 'Update' : 'Upload'; ?> MP4 File Image:<font class="form_required-field"> *</font></td>
                  <td  width="83%" valign="top" style="font-size:11px;">
                  <?php if(trim($video_code) != NULL){?>
                    <a target="_blank" href="<?=$general_func -> site_url . substr($original, 6) . $video_code ?>" >Download Video</a>&nbsp;&nbsp;
                    <?php if($button=="Update"){?>
                    <a href="training-videos/<?=basename($_SERVER['PHP_SELF']) ?>?action=EDIT&now=DELETE&id=<?=$_REQUEST['id'] ?>&return_url=<?=$return_url ?>&field=video_code&path=<?=$video_code ?>" class="htext" ><strong>Delete</strong></a>
                    <?php } ?>
                    <br/>
                    <br/>
                    <?php } ?>
                  <input name="video_code" id="video_code" type="file"  class="form_inputbox" size="55" /><br/>                  
                  	Only supported file type is <strong>MP4</strong>. 
                  </td>
                </tr>
                    
                 
                <tr>
                  <td colspan="2" class="body_content-form" height="10"></td>
                </tr>
                  <tr>
                  <td width="17%" class="body_content-form">&nbsp;</td>
                  <td width="83%"><table width="261" border="0" align="left" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="41%"><table border="0" align="left" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="submit" type="submit" class="submit1" value="<?=$button?>" /></td>
                              <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                            </tr>
                          </table></td>
                        <td width="10%">&nbsp;</td>
                        <td width="49%">
                        	 <?php if($button=="Update"){?>
                        	 	<table border="0" align="left" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="back" onclick="location.href='<?=$general_func->admin_url?>training-videos/videos.php'"  type="button" class="submit1" value="Back" /></td>
                              <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                            </tr>
                          </table>
                        	 	<?php } ?>
                        	
                        	</td>
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