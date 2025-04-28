<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";



$small=$path_depth ."snack_main/small/";
$original=$path_depth ."snack_main/";

$data=array();
$return_url=$_REQUEST['return_url'];


if(isset($_GET['now']) && $_GET['now']=="DELETE"){
	$path=$_REQUEST['path'];
	$field=$_REQUEST['field'];
		
	@mysql_query("update snacks set $field=' ' where id=" . (int) $_REQUEST['id'] . "");	
	
	@unlink($original.$path);
	@unlink($small.$path);		
	
	
	$redirect_path="meals/" . basename($_SERVER['PHP_SELF']) . "?id=".$_GET['id']."&action=EDIT&return_url=".$return_url;
	$general_func->header_redirect($redirect_path);	
}

if(isset($_REQUEST['action']) && $_REQUEST['action']=="EDIT"){		
	$sql="select * from snacks where id=" .  intval($_REQUEST['id'])  . " limit 1";
	$result=$db->fetch_all_array($sql);	
	$name=$result[0]['name'];
	$details=$result[0]['details'];
	$photo_name=$result[0]['photo_name'];
	$price=$result[0]['price'];
	
	$status=$result[0]['status'];
	
	$meal_plan_category_array=array();
	
	$sql_cat="select meal_plan_category_id from meal_plan_category_snacks where snack_id=" .  intval($_REQUEST['id'])  . "";
	$result_cat=$db->fetch_all_array($sql_cat);	
	$total_cat=count($result_cat);
	
	for($cat=0; $cat < $total_cat; $cat++){
		$meal_plan_category_array[]=$result_cat[$cat]['meal_plan_category_id'];	
	}
	
	$snacks_type_array = array();
	$snacks_type_array = explode(",", $result[0]['snacks_type']);	
	

	$button="Update";
}else{
	$name="";
	$details="";
	$photo_name="";
	$price="";	
	$status=1;		
	$meal_plan_category_array=array();
	$snacks_type_array = array();
	$button="Add New";
}


if(isset($_POST['enter']) && $_POST['enter']=="yes" && trim($_POST['login_form_id'])==$_SESSION['login_form_id']){
		
	$name=filter_var(trim($_REQUEST['name']), FILTER_SANITIZE_STRING);	 
	$details=filter_var(trim($_REQUEST['details']), FILTER_SANITIZE_STRING); 
	$meal_plan_category_id=$_REQUEST['meal_plan_category_id'];
	
	$price=trim($_REQUEST['price']);
	$status=intval($_REQUEST['status']);	
		
	if($_POST['submit']=="Add New"){
		if($db->already_exist_inset("snacks","name",$name)){
			$_SESSION['msg']="Sorry, your specified snack is already taken!";
		}else{			
			$data['name']=$name;		
			$data['seo_link']=$general_func->create_seo_link($name);
				
			//*** check whether this name alreay exit ******//
			if($db->already_exist_inset("snacks","seo_link",$data['seo_link'])){//******* exit
				$data['seo_link']=$db->max_id("snacks","id") + 1 ."-".$data['seo_link'];
			}
			//*********************************************//
			
			$data['details']=$details;			
			$data['price']=$price;

			$snacks_type_list = "";
			
			$snacks_type = $_REQUEST['snacks_type'];
			$total_type = count($snacks_type);

			if ($total_type > 0) {
				for ($t = 0; $t < $total_type; $t++) {
					$snacks_type_list .= "{" .$snacks_type[$t] . "},";
				}
				$snacks_type_list = substr($snacks_type_list, 0, -1);
			}			
			$data['snacks_type'] = $snacks_type_list;
		
			
			$data['status']=$status;
			$data['date_added']=$current_date_time;				
			$inserted_id=$db->query_insert("snacks",$data);
					
						
			$flag_category=0;
			$sql_category="INSERT INTO meal_plan_category_snacks(snack_id,meal_plan_category_id) VALUES";
			
			$count_category=count($meal_plan_category_id);
			
			for($cat=0; $cat <$count_category; $cat++){
				$sql_category .="('" . $inserted_id . "','" . $meal_plan_category_id[$cat] . "'), ";				
				$flag_category=1;
			}
			
			if($flag_category == 1){
				$sql_category =  substr($sql_category,0,-2).";";
				$db->query($sql_category);	
			}	
					
		
			//****************************  upload image ********************************//	
			if($_FILES['photo_name']['size'] >0 && $general_func->valid_file_type($_FILES["photo_name"]["name"],$_FILES["photo_name"]["type"])){			
				
				$uploaded_name=array();				
				$userfile_name=$_FILES['photo_name']['name'];
				$userfile_tmp= $_FILES['photo_name']['tmp_name'];
				$userfile_size=$_FILES['photo_name']['size'];
				$userfile_type= $_FILES['photo_name']['type'];
									
				$path=$inserted_id ."_".$general_func->remove_space_by_hypen($security_validator->sanitize_filename($userfile_name));
				$img=$original.$path;
				move_uploaded_file($userfile_tmp, $img) or die();
									
				$uploaded_name['photo_name']=$path;
				$db->query_update("snacks",$uploaded_name,'id='.$inserted_id);
									
				list($width, $height) = getimagesize($img);
							
				if($width > 188 || $height > 116){				
					$upload->uploaded_image_resize(188,116,$original,$small,$path);
				}else{
					copy($img,$small.$path); 
				}	
									
				if($width > 640 || $height > 480){
					$upload->uploaded_image_resize(640,480,$original,$original,$path);
				}	
			}		
			//****************************  upload image ********************************//			
			$_SESSION['msg']="Snack successfully added!";
			$general_func->header_redirect($_SERVER['PHP_SELF']);			
		}		

	}else{
		if($db->already_exist_update("snacks","id",$_REQUEST['id'],"name",$name)){
			$_SESSION['msg']="Sorry, your specified snack is already taken!";
		}else{		
				
			$data['name']=$name;
			$data['seo_link']=$general_func->create_seo_link($name);			
			//*** check whether this name alreay exit ******//
			if($db->already_exist_update("snacks","id",$_REQUEST['id'],"seo_link",$data['seo_link'])){//******* exit
				$data['seo_link']=$_REQUEST['id'] ."-".$data['seo_link'];
			}
			//*********************************************//
			$data['details']=$details;			
			$data['price']=$price;
			
			$snacks_type_list = "";			
			$snacks_type = $_REQUEST['snacks_type'];
			$total_type = count($snacks_type);

			if ($total_type > 0) {
				for ($t = 0; $t < $total_type; $t++) {
					$snacks_type_list .=  "{" .$snacks_type[$t] . "},";
				}
				$snacks_type_list = substr($snacks_type_list, 0, -1);
			}				
			
			$data['snacks_type'] = $snacks_type_list;
			
			$data['status']=$status;			
			$data['date_modified']=$current_date_time;
			$db->query_update("snacks",$data,"id='".$_REQUEST['id'] ."'");
			
			
			$db->query_delete("meal_plan_category_snacks","snack_id='".$_REQUEST['id'] ."'");			
			
			$flag_category=0;
			$sql_category="INSERT INTO meal_plan_category_snacks(snack_id,meal_plan_category_id) VALUES";
			
			$count_category=count($meal_plan_category_id);
			
			for($cat=0; $cat <$count_category; $cat++){
				$sql_category .="('" . $_REQUEST['id'] . "','" . $meal_plan_category_id[$cat] . "'), ";				
				$flag_category=1;
			}
			
			if($flag_category == 1){
				$sql_category =  substr($sql_category,0,-2).";";
				$db->query($sql_category);	
			}
			
			
			//****************************  upload image ********************************//	
			if($_FILES['photo_name']['size'] >0 && $general_func->valid_file_type($_FILES["photo_name"]["name"],$_FILES["photo_name"]["type"])){			
				
				@unlink($original.$_REQUEST['photo_name']);
				@unlink($small.$_REQUEST['photo_name']);
				
				$uploaded_name=array();				
				$userfile_name=$_FILES['photo_name']['name'];
				$userfile_tmp= $_FILES['photo_name']['tmp_name'];
				$userfile_size=$_FILES['photo_name']['size'];
				$userfile_type= $_FILES['photo_name']['type'];
									
				$path=$_REQUEST['id'] ."_".$general_func->remove_space_by_hypen($security_validator->sanitize_filename($userfile_name));
				$img=$original.$path;
				move_uploaded_file($userfile_tmp, $img) or die();
									
				$uploaded_name['photo_name']=$path;
				$db->query_update("snacks",$uploaded_name,'id='.$_REQUEST['id']);
									
				list($width, $height) = getimagesize($img);
							
				if($width > 188 || $height > 116){				
					$upload->uploaded_image_resize(188,116,$original,$small,$path);
				}else{
					copy($img,$small.$path); 
				}	
									
				if($width > 640 || $height > 480){
					$upload->uploaded_image_resize(640,480,$original,$original,$path);
				}	
			}
			
			if($db->affected_rows > 0)
				$_SESSION['msg']="Snack successfully updated!";
			
			$general_func->header_redirect($general_func->admin_url."meals/snacks.php");				
		}	
	}
}	
?>

<script type="text/javascript" src="<?=$general_func->site_url?>highslide/highslide.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$general_func->site_url?>highslide/highslide.css" />
<script type="text/javascript">
	hs.graphicsDir = '<?=$general_func->site_url?>highslide/graphics/';
	hs.wrapperClassName = 'wide-border';

function validate(){		
	
	var frm=document.ff;
	if(!validate_text(frm.name,1,"Enter snack name"))
		return false;
	
	var meal_plan_category_checked = $('input[name="meal_plan_category_id[]"]:checked').length;		
		
	if(meal_plan_category_checked == 0){
    	alert("Please select at least a meal plan category");	
       	return false;
    }		
			
	if(!validate_text(frm.price,1,"Enter snack price"))
		return false;
	
	 
	if(!validate_price(frm.price,1,"Enter a valid snack price"))
			return false;	
			
	
	var snacks_type_checked = $('input[name="snacks_type[]"]:checked').length;		
	
			
	if(snacks_type_checked == 0){
		alert("Please select at least one snack type");	
		return false;
	}		
			
	if(frm.status.selectedIndex == 0 ){
		alert("Please choose a status");
		frm.status.focus();
		return false;		
	}	
			
}	
</script>			

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg"><?=$button?> Snack</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg"><form method="post" action="<?=$_SERVER['PHP_SELF']?>"  name="ff" enctype="multipart/form-data"  onsubmit="return validate()">
        <input type="hidden" name="enter" value="yes" />
        <input type="hidden" name="id" value="<?=$_REQUEST['id']?>" />
        <input type="hidden" name="return_url" value="<?php echo $_REQUEST['return_url']?>" />
       <input type="hidden" name="photo_name" value="<?=$photo_name?>" />
       <input type="hidden" name="login_form_id" value="<?=$_SESSION['login_form_id']?>" />
      
        <table width="883" border="0" align="left" cellpadding="0" cellspacing="0">
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
            <td width="73" align="left" valign="top"></td>
            <td width="780" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="10">
                 <tr>
                  <td  class="body_content-form">Snack Name:<font class="form_required-field"> *</font></td>
                  <td width="80%"><input name="name" value="<?=$name?>" type="text" autocomplete="off" class="form_inputbox" size="55" />
                  </td>
                </tr>
                 <td  class="body_content-form" valign="top">Meal Plan Category:<font class="form_required-field"> *</font></td>
                  <td  valign="top"  class="htext" valign="top" style="line-height: 20px;"> 
                          <?php
                          $sql_cat="select id,name from meal_plan_category order by name";
						  $result_cat=$db->fetch_all_array($sql_cat);
						  $total_cat=count($result_cat);
						  $br=1;						  
						  for($cat=0; $cat < $total_cat; $cat++){ ?>
						  	<input type="checkbox" name="meal_plan_category_id[]" id="meal_plan_category_id"  value="<?=$result_cat[$cat]['id']?>" <?=in_array($result_cat[$cat]['id'], $meal_plan_category_array)?'checked="checked"':'';?>><?=$result_cat[$cat]['name']?>
						  	&nbsp;&nbsp;&nbsp;&nbsp;
						<?php 
							if($br++%4 == 0)
						   		echo "<br/>";
						  } ?>                        
                  </td>
                </tr>
                
                  <tr>
                  <td class="body_content-form">Snack Description:</td>
                  <td ><textarea name="details" class="form_textarea" cols="70" rows="6"><?=$details?></textarea>
                  
                  </td>
                </tr>                 
                
                <tr>
                  <td  class="body_content-form">Price ($):<font class="form_required-field"> *</font></td>
                  <td width="80%"><input name="price" value="<?=$price?>" type="text" autocomplete="off" class="form_inputbox" size="55" />
                  </td>
                </tr>
                <tr>
                  <td  class="body_content-form">Select snacks type<font class="form_required-field"> *</font></td>
                  <td width="80%">
                  	<?php
                  	$result_snacks_type=$db->fetch_all_array("select id,name from snacks_type order by name ASC");
					$total_snacks_type=count($result_snacks_type);
					
					for($snack=0; $snack < $total_snacks_type;  $snack++ ){ ?>
						 <input type="checkbox"  name="snacks_type[]" id="snacks_type" value="<?=$result_snacks_type[$snack]['id']?>"  <?=in_array("{" . $result_snacks_type[$snack]['id'] . "}", $snacks_type_array) ? 'checked="checked"' : ''; ?> ><?=$result_snacks_type[$snack]['name']?>
                  		&nbsp;&nbsp;&nbsp;&nbsp; 
					<?php } ?>            		          
                 </td>
                </tr>               
                  <tr>
                  	<td class="body_content-form"></td>
                  <td class="body_content-form"><strong>Supported file types are gif, jpg, jpeg, png</strong></td>
                
                </tr>
                 <tr>
                  <td  class="body_content-form" valign="top"><?=trim($photo_name) != NULL?'Update':'Upload';?>
                     Snack Photo:</td>
                  <td  valign="top">
                  <?php if(trim($photo_name) != NULL){?>
                    		<a href="<?=$general_func->site_url.substr($original,6).$photo_name?>" class="highslide" onclick="return hs.expand(this)"><img src="<?=$general_func->site_url.substr($small,6).$photo_name?>" border="0" /></a>&nbsp;&nbsp;
                    		<a href="meals/snacks-new.php?id=<?=$_REQUEST['id']?>&now=DELETE&field=photo_name&path=<?=$photo_name?>" class="htext" ><strong>Delete</strong></a>
                  			<br/>
                    <?php }	?>							
                	
                  <input type="file" name="photo_name" /></td>
                </tr>
                               
                <tr>
                  <td  class="body_content-form" valign="top">Status:<font class="form_required-field"> *</font></td>
                  <td valign="top"><select name="status"  class="inputbox_select" style="width: 100px;" >
                      	<option value="">Choose One</option>
                      	<option value="0" <?=$status==0?'selected="selected"':'';?>> Inactive</option>
                      	<option value="1" <?=$status==1?'selected="selected"':'';?>> Active</option>                   
                    </select>
                    <p>&nbsp; </p></td>
                </tr>
              </table></td>
            <td width="8" align="left" valign="top">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="4" height="30" align="center"><table width="879" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="33%"><table border="0" align="right" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                        <td align="left" valign="middle" class="body_tab-middilebg"><input name="submit" type="submit" class="submit1" value="<?=$button?>" /></td>
                        <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                      </tr>
                    </table></td>
                  <td width="4%"></td>
                  <td width="63%"><?php if($button !="Add New"){?>
                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                        <td align="left" valign="middle" class="body_tab-middilebg">
                        	<input type="button" class="submit1"  name="back" value="Back"  onclick="history.back();" /></td>
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
