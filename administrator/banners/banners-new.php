<?php
$path_depth = "../../";

include_once ("../head.htm");
$link_name = "Welcome";

$original = $path_depth . "banner_images/";
$data = array();
$return_url = $_REQUEST['return_url'];

if (isset($_GET['now']) && $_GET['now'] == "DELETE") {
	$path = $_REQUEST['path'];
	$field = $_REQUEST['field'];
	@mysql_query("update banners set $field=' ' where id=" . (int)$_GET['id'] . "");
	@unlink($original . $path);

	$redirect_path = basename($_SERVER['PHP_SELF']) . "?id=" . $_GET['id'] . "&action=EDIT&return_url=" . $return_url;
	$general_func -> header_redirect($redirect_path);
}

if (isset($_REQUEST['action']) && $_REQUEST['action'] == "EDIT") {
	$sql = "select * from banners where id=" . (int)$_REQUEST['id'] . " limit 1";
	$result = $db -> fetch_all_array($sql);

	$banner_path = $result[0]['banner_path'];
	$banner_link = $result[0]['banner_link'];
	$banner_target = $result[0]['banner_target'];
	$banner_description = $result[0]['banner_description'];
	$link_name = $result[0]['link_name'];
	$display_order = $result[0]['display_order'];
	$video_or_image = $result[0]['video_or_image'];
	$embedded_video_code = $result[0]['embedded_video_code'];

	$button = "Update";
} else {
	$banner_path = "";
	$banner_link = "";
	$banner_target = 1;
	$banner_description = "";
	$display_order = $db -> max_id("banners", "display_order") + 1;
	$video_or_image = 2;
	$link_name="Get Started";
	$embedded_video_code = "";
	$button = "Add New";
}

if (isset($_POST['enter']) && $_POST['enter'] == "yes" && trim($_POST['login_form_id']) == $_SESSION['login_form_id']) {
	$banner_link = filter_var(trim($_REQUEST['banner_link']), FILTER_SANITIZE_URL);
	$banner_target = trim($_REQUEST['banner_target']);
	$link_name=filter_var(trim($_REQUEST['link_name']), FILTER_SANITIZE_STRING);
	$banner_description = trim($_REQUEST['banner_description']);
	$display_order = filter_var(trim($_REQUEST['display_order']), FILTER_SANITIZE_NUMBER_INT);
	$video_or_image = filter_var(trim($_REQUEST['video_or_image']), FILTER_SANITIZE_NUMBER_INT);
	$embedded_video_code = trim($_REQUEST['embedded_video_code']);

	if ($_POST['submit'] == "Add New") {		
		if($video_or_image == 2){
			//*************************  Upload photo *************************************//
			if ($_FILES['banner_path']['size'] > 0 && $general_func -> valid_file_type($_FILES["banner_path"]["name"], $_FILES["banner_path"]["type"])) {
				$data['banner_link'] = $banner_link;
				$data['banner_target'] = $banner_target;
				$data['banner_description'] = $banner_description;
				$data['link_name'] = $link_name;
				
				$data['display_order'] = $display_order;
				$data['video_or_image'] = $video_or_image;
				$data['date_added'] = 'now()';
				$inserted_id = $db -> query_insert("banners", $data);
	
				$uploaded_name = array();
				$userfile_name = $_FILES['banner_path']['name'];
				$userfile_tmp = $_FILES['banner_path']['tmp_name'];
				$userfile_size = $_FILES['banner_path']['size'];
				$userfile_type = $_FILES['banner_path']['type'];
	
				$path = $inserted_id . "_" . $general_func -> remove_space_by_hypen($security_validator -> sanitize_filename($userfile_name));
	
				$img = $original . $path;
				move_uploaded_file($userfile_tmp, $img) or die();
	
				$uploaded_name['banner_path'] = $path;
				$db -> query_update("banners", $uploaded_name, 'id=' . $inserted_id);
	
				$_SESSION['msg'] = "Banner successfully added!";
			} else {
				$_SESSION['msg'] = "Please choose a banner image!";
			}
			
		}else{
			$data['display_order'] = $display_order;
			$data['video_or_image'] = $video_or_image;
			$data['embedded_video_code'] = $embedded_video_code;
			$data['date_added'] = 'now()';
			$inserted_id = $db -> query_insert("banners", $data);
			$_SESSION['msg'] = "Banner successfully added!";
		}

		$general_func -> header_redirect($_SERVER['PHP_SELF']);

	} else {
		$data['banner_link'] = $banner_link;
		$data['banner_target'] = $banner_target;
		$data['banner_description'] = $banner_description;
		$data['link_name'] = $link_name;
		$data['display_order'] = $display_order;
		$data['video_or_image'] = $video_or_image;
		$data['embedded_video_code'] = $embedded_video_code;

		$db -> query_update("banners", $data, "id='" . $_REQUEST['id'] . "'");

		if($video_or_image == 2){
			//*************************  Upload photo *************************************//
			if ($_FILES['banner_path']['size'] > 0 && $general_func -> valid_file_type($_FILES["banner_path"]["name"], $_FILES["banner_path"]["type"])) {
				@unlink($original . $_REQUEST['banner_path']);
	
				$uploaded_name = array();
	
				$userfile_name = $_FILES['banner_path']['name'];
				$userfile_tmp = $_FILES['banner_path']['tmp_name'];
				$userfile_size = $_FILES['banner_path']['size'];
				$userfile_type = $_FILES['banner_path']['type'];
	
				$path = $_REQUEST['id'] . "_" . $general_func -> remove_space_by_hypen($security_validator -> sanitize_filename($userfile_name));
				$img = $original . $path;
				move_uploaded_file($userfile_tmp, $img) or die();
	
				$uploaded_name['banner_path'] = $path;
				$db -> query_update("banners", $uploaded_name, 'id=' . $_REQUEST['id']);
			}
	
			//*************************  / Upload photos *************************************//
		}	
		
		if ($db -> affected_rows > 0)
			$_SESSION['msg'] = "Banner successfully updated!";

		$general_func -> header_redirect($return_url);
	}
}
?>
<script type="text/javascript" src="<?=$general_func -> site_url ?>highslide/highslide.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$general_func -> site_url ?>highslide/highslide.css" />
<script language="javascript" type="text/javascript">
	function validate() {

		if (document.ff.video_or_image.selectedIndex == 0) {
			alert("Please select a banner type");
			document.ff.video_or_image.focus();
			return false;
		}
		if (parseInt(document.ff.video_or_image.value) == 1) {
			if (!validate_text(document.ff.embedded_video_code, 1, "Enter Youtube video embed code"))
				return false;

		} else {
			if (!validate_text(document.ff.banner_description, 1, "Enter banner description"))
				return false;
		}

		if (!validate_text(document.ff.display_order, 1, "Enter category display order"))
			return false;
		if (!validate_integer(document.ff.display_order, 1, "Display order must be a number"))
			return false;
	}
	
	function show_hide(val){
		if(parseInt(val) == 1){
			$("#div_video_banner").show("slow");
			$("#div_image_banner").hide("slow");	
		}else{
			$("#div_image_banner").show("slow");
			$("#div_video_banner").hide("slow");	
		}		
	}	
</script>

<script type="text/javascript">
		hs.graphicsDir = '<?=$general_func -> site_url ?>highslide/graphics/';
	hs.wrapperClassName = 'wide-border';
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg"><?=$button ?> Home Page Banner</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg">
    <form method="post" action="<?=$_SERVER['PHP_SELF'] ?>" name="ff" enctype="multipart/form-data"  onsubmit="return validate()">
        <input type="hidden" name="enter" value="yes" />
        <input type="hidden" name="id" value="<?=$_REQUEST['id'] ?>" />
        <input type="hidden" name="banner_path" value="<?=$banner_path ?>" />
         <input type="hidden" name="login_form_id" value="<?=$_SESSION['login_form_id'] ?>" />
        
        <input type="hidden" name="return_url" value="<?php echo $_REQUEST['return_url']?>" />
        <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td colspan="3" class="body_content-form" height="30"></td>
          </tr>
          <?php if(isset($_SESSION['msg']) && trim($_SESSION['msg']) != NULL){?>
          <tr>
            <td colspan="3" class="message_error"><?=$_SESSION['msg'];
			$_SESSION['msg'] = "";
 ?></td>
          </tr>
          <tr>
            <td colspan="3" class="body_content-form" height="30"></td>
          </tr>
          <?php  } ?>
          <tr>
            <td align="left" valign="top" colspan="3"><table width="82%" border="0"  align="center" cellspacing="2" cellpadding="6">
               
               <tr>
                 	<td class="body_content-form" width="17%">Banner Type:<font class="form_required-field"> *</font></td>
                    <td width="83%">
                     <select name="video_or_image" class="inputbox_select" style="width: 200px;" onchange="show_hide(this.value);">
                     	<option value="">Select One</option>	                    
							<option value="2" <?=$video_or_image == 2 ? 'selected="selected"' : ''; ?> >Image Banner</option>
							<option value="1" <?=$video_or_image == 1 ? 'selected="selected"' : ''; ?> >Video Banner</option>					
	                    </select>
                          </td>
                    </tr> 
                     <tr>
                 	<td colspan="2"  width="820"> <div id="div_video_banner" style="display: <?=$video_or_image == 1 ? 'block' : 'none'; ?>" >
                 		<table width="820" border="0"  align="center" cellspacing="2" cellpadding="6">
                 			  <tr>
                  <td width="17%" class="body_content-form" valign="top">Youtube Video Embed Code: <font class="form_required-field"> *</font></td>
                  <td width="83%"  valign="top"> <textarea name="embedded_video_code"  autocomplete="off" class="form_textarea" cols="100" rows="6"><?=$embedded_video_code ?></textarea>
                  		 &nbsp;&nbsp;Need help? <a class="htext" href="<?=$general_func->admin_url?>testimonial/youtube_video_help2.php" target="_blank">Click here</a>  
                  </td>
                </tr>
                 		</table></div>	 
                    	</td>
                 </tr>  
                 <tr>
                 	<td colspan="2"  width="820"><div id="div_image_banner" style="display: <?=$video_or_image == 1 ? 'none' : 'block'; ?>" >
                 		<table width="820" border="0"  align="center" cellspacing="2" cellpadding="6">
                 			<tr>
                  <td width="17%" class="body_content-form" valign="top"><?=trim($banner_path) != NULL ? 'Update' : 'Upload'; ?> Banner Image:<font class="form_required-field"> *</font></td>
                  <td  width="83%" valign="top" style="font-size:11px;">
                  <?php if(trim($banner_path) != NULL){?>
                    <a href="<?=$general_func -> site_url . substr($original, 6) . $banner_path ?>" class="highslide" onclick="return hs.expand(this)"><img src="<?=$general_func -> site_url . substr($original, 6) . $banner_path ?>" border="0" width="200" /></a>&nbsp;&nbsp;
                    <?php if($button=="Update"){?>
                    <a href="banners/<?=basename($_SERVER['PHP_SELF']) ?>?action=EDIT&now=DELETE&id=<?=$_REQUEST['id'] ?>&return_url=<?=$return_url ?>&field=banner_path&path=<?=$banner_path ?>" class="htext" ><strong>Delete</strong></a>
                    <?php } ?>
                    <br/>
                    <br/>
                    <?php } ?>
                  <input name="banner_path" type="file"  class="form_inputbox" size="55" /><br/>                  
                  	Supported file types are <strong>gif, jpg, jpeg and png</strong>, banner width and height must be <strong>1800px and 900px</strong> respectively. 
                  </td>
                </tr>
               
               <tr>
                  <td  class="body_content-form" valign="top">Description: <font class="form_required-field"> *</font></td>
                  <td  valign="top"> <textarea name="banner_description"  autocomplete="off" class="form_textarea" cols="100" rows="6"><?=$banner_description ?></textarea></td>
                </tr>
                <tr>
                  <td class="body_content-form" valign="top">Link:</td>
                  <td valign="top"><input name="banner_link" type="text" value="<?=$banner_link ?>" AUTOCOMPLETE=OFF class="form_inputbox" size="65" /> <br />(Enter full path, if you have any. e.g. http://test.com/test)</td>
                </tr>
                <tr>
                  <td class="body_content-form" valign="top">Link Name:</td>
                  <td valign="top"><input name="link_name" type="text" value="<?=$link_name ?>" AUTOCOMPLETE=OFF class="form_inputbox" size="65" /> </td>
                </tr>
                
                <tr>
                  <td class="body_content-form" valign="top">Target:</td>
                  <td  valign="top"> <select name="banner_target"  class="inputbox_select" style="width: 140px; padding: 2px 1px 2px 0px;" >
                      <option value="">Choose One</option>
                      <option value="1" <?=$banner_target == 1 ? 'selected="selected"' : ''; ?>>Same Window</option>
                      <option value="2" <?=$banner_target == 2 ? 'selected="selected"' : ''; ?>>New Window</option>
                  </select></td>
                </tr>
                 		</table></div>	                		
                 	</td>
                 </tr>   
                    
               
                
                <tr>
                  <td class="body_content-form" valign="top">Display Order:<font class="form_required-field"> *</font></td>
                  <td  valign="top"><input name="display_order" type="text" value="<?=$display_order ?>" AUTOCOMPLETE=OFF class="form_inputbox" size="10" /></td>
                </tr>
                <tr>
                  <td colspan="2" class="body_content-form" height="5"></td>
                </tr>
                <tr>
                  <td class="body_content-form">&nbsp;</td>
                  <td ><table width="261" border="0" align="left" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="41%"><table border="0" align="left" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="submit" type="submit" class="submit1" value="<?=$button ?>" /></td>
                              <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                            </tr>
                          </table></td>
                        <td width="10%">&nbsp;</td>
                        <td width="49%"><table border="0" align="left" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="back" onclick="location.href='<?=$general_func -> admin_url ?>banners/banners.php'"  type="button" class="submit1" value="Back" /></td>
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
include ("../foot.htm");
?>