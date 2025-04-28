<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";


$small=$path_depth . "photo/small/";
$original=$path_depth . "photo/";


$data=array();
$return_url=$_REQUEST['return_url'];

if(isset($_GET['now']) && $_GET['now']=="DELETE"){
	$path=$_REQUEST['path'];
	$field=$_REQUEST['field'];
		
	@mysql_query("update users set $field=' ' where id=" . (int) $_GET['id'] . "");	
	
	@unlink($original.$path);
	@unlink($small.$path);	
	
	
	$redirect_path=basename($_SERVER['PHP_SELF']) . "?id=".$_GET['id']."&action=EDIT&return_url=".$return_url;
	$general_func->header_redirect($redirect_path);
}




if(isset($_REQUEST['action']) && $_REQUEST['action']=="EDIT"){		
	$sql="select * from users where id=" . intval($_REQUEST['id'])  . " limit 1";
	$result=$db->fetch_all_array($sql);			
	$fname=$result[0]['fname']; 	
	$lname=$result[0]['lname']; 	
	$email_address=$result[0]['email_address']; 	
	$password=$EncDec->decrypt_me($result[0]['password']); 	
	$street_address=$result[0]['street_address']; 	
	$suburb_id=$result[0]['suburb_id']; 	
	$photo=$result[0]['photo']; 
	$gender=$result[0]['gender']; 
	$hear_about_us=$result[0]['hear_about_us']; 		
	$phone=$result[0]['phone']; 		
	$status=$result[0]['status']; 	
	
	
	$button="Update";
}else{		
	$fname="";
	$lname="";
	$email_address="";
	$password="";
	$street_address="";
	$suburb_id="";
	$photo="";
	$phone="";
	$gender=""; 	
	$hear_about_us=""; 
	$status=1;	
	
	$button="Add New";
}


if(isset($_POST['enter']) && $_POST['enter']=="yes" && trim($_POST['login_form_id'])==$_SESSION['login_form_id']){
	
	$fname=filter_var(trim($_REQUEST['fname']), FILTER_SANITIZE_STRING);	 
	$lname=filter_var(trim($_REQUEST['lname']), FILTER_SANITIZE_STRING);
	$email_address=filter_var(trim($_REQUEST['email_address']), FILTER_SANITIZE_EMAIL);
	$password=trim($_REQUEST['password']);			 
	$street_address=filter_var(trim($_REQUEST['street_address']), FILTER_SANITIZE_STRING);	 
	$suburb_id=intval($_REQUEST['suburb_id']);
	
	$gender=intval($_REQUEST['gender']);
	$hear_about_us=intval($_REQUEST['hear_about_us']);
	
		 
	$phone=filter_var(trim($_REQUEST['phone']), FILTER_SANITIZE_STRING);	 
	$status=intval($_REQUEST['status']);	
	
	if($_POST['submit']=="Add New"){
		if($db->already_exist_inset("users","email_address",$email_address)){
			$_SESSION['msg']="Sorry, your specified email address is already taken!";		
		}else{			
			$data['fname']=$fname;	
			$data['lname']=$lname;			
			$data['seo_link']=$general_func->create_seo_link($fname." ".$lname);		
			
			if($db->already_exist_inset("users","seo_link",$data['seo_link'])){//******* exit
				$data['seo_link']=$db->max_id("users","id") + 1 ."-".$data['seo_link'];
			}
			
			$data['email_address']=$email_address;
			$data['password']=$EncDec->encrypt_me($password);			
			$data['street_address']=$street_address;
			$data['suburb_id']=$suburb_id;	
			
			$data['gender']=$gender;		
			$data['hear_about_us']=$hear_about_us;
							
			$data['phone']=$phone;
			$data['status']=$status;
			$data['date_added']=$current_date_time;
			$inserted_id=$db->query_insert("users",$data);
			
			//*************************  Upload photo *************************************//
			if($_FILES['photo']['size'] >0 && $general_func->valid_file_type($_FILES["photo"]["name"],$_FILES["photo"]["type"])){			
						
				$uploaded_name=array();
					
				$userfile_name=$_FILES['photo']['name'];
				$userfile_tmp= $_FILES['photo']['tmp_name'];
				$userfile_size=$_FILES['photo']['size'];
				$userfile_type= $_FILES['photo']['type'];
								
				$path=$inserted_id ."_".$general_func->remove_space_by_hypen($security_validator->sanitize_filename($userfile_name));
				$img=$original.$path;
				move_uploaded_file($userfile_tmp, $img) or die();
								
				$uploaded_name['photo']=$path;
				$db->query_update("users",$uploaded_name,'id='.$inserted_id);
					
										
				list($width, $height) = getimagesize($img);
				
				if($width > 214 || $height > 214){
					$upload->uploaded_image_resize(214,214,$original,$small,$path);	
				}else{
					copy($img,$small.$path);			
				}
						
				if($width > 800 || $height > 700){
					$upload->uploaded_image_resize(800,700,$original,$original,$path);
				}
			}			
						
			$_SESSION['msg']="User profile successfully created!";
			$general_func->header_redirect($_SERVER['PHP_SELF']);
		}	

	}else{
		if($db->already_exist_update("users","id",$_REQUEST['id'],"email_address",$email_address)){
			$_SESSION['msg']="Sorry, your specified email address is already taken!";		
		}else{
			$data['fname']=$fname;	
			$data['lname']=$lname;			
			$data['seo_link']=$general_func->create_seo_link($fname." ".$lname);		
			
			if($db->already_exist_inset("users","seo_link",$data['seo_link'])){//******* exit
				$data['seo_link']=$_REQUEST['id']."-".$data['seo_link'];
			}
			
			$data['email_address']=$email_address;
			$data['password']=$EncDec->encrypt_me($password);			
			$data['street_address']=$street_address;
			$data['suburb_id']=$suburb_id;
			$data['gender']=$gender;		
			$data['hear_about_us']=$hear_about_us;			
			$data['phone']=$phone;
			$data['status']=$status;
			$data['date_modified']=$current_date_time;			
			
			$db->query_update("users",$data,"id='".$_REQUEST['id'] ."'");
			
			
			//*************************  Upload photo *************************************//
			if($_FILES['photo']['size'] >0 && $general_func->valid_file_type($_FILES["photo"]["name"],$_FILES["photo"]["type"])){
				@unlink($original.$_REQUEST['photo']);
				@unlink($small.$_REQUEST['photo']);				
						
				$uploaded_name=array();
					
				$userfile_name=$_FILES['photo']['name'];
				$userfile_tmp= $_FILES['photo']['tmp_name'];
				$userfile_size=$_FILES['photo']['size'];
				$userfile_type= $_FILES['photo']['type'];
								
				$path=$_REQUEST['id'] ."_".$general_func->remove_space_by_hypen($security_validator->sanitize_filename($userfile_name));
				$img=$original.$path;
				move_uploaded_file($userfile_tmp, $img) or die();
								
				$uploaded_name['photo']=$path;
				$db->query_update("users",$uploaded_name,'id='.$_REQUEST['id']);
					
										
				list($width, $height) = getimagesize($img);
				
				if($width > 214 || $height > 214){
					$upload->uploaded_image_resize(214,214,$original,$small,$path);	
				}else{
					copy($img,$small.$path);			
				}
						
				if($width > 800 || $height > 700){
					$upload->uploaded_image_resize(800,700,$original,$original,$path);
				}
			}			
				
			//********************   update information at edebit also ***********************************//
			$rs_extra_info = $db->fetch_all_array("select cc_or_dd,suburb_state,suburb_postcode  from users u left join suburb s on u.suburb_id=s.id where u.id='" . $_REQUEST['id'] . "' limit 1");
			$clNo = 3000 + $_REQUEST['id'];
	
			$edPI_url = "https://www.edebit.com.au/IS/edPI.ashx?edNo=" . $edNo . "&clNo=" . $clNo . "&cl1stName=" . urlencode($fname) . "&cl2ndName=" . urlencode($lname) . "";
			$edPI_url .= "&clAddr=" . urlencode($street_address) . "&clCity=&clState=" . urlencode($rs_extra_info[0]['suburb_state']) . "&clPCode=" . $rs_extra_info[0]['suburb_postcode'] . "";
			$edPI_url .= "&clTel=" .  str_replace(" ","", trim($phone)) . "&clEmail=" . $email_address . "&clDlName=&clDlNo=&clDlState=&accountType=" . $rs_extra_info[0]['cc_or_dd'] . "&clMktNo=";
			
			$ch = curl_init($edPI_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$data = curl_exec($ch);
			curl_close($ch);		
			//********************************************************************************************//	
						
						
			if($db->affected_rows > 0)
				$_SESSION['msg']="User profile successfully updated!";
			
			$general_func->header_redirect($return_url);
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
			
	if(!validate_text(document.ff.fname,1,"Enter First Name"))
		return false;
		
	if(!validate_text(document.ff.lname,1,"Enter Last Name"))
		return false;	
	
	if(document.ff.gender.selectedIndex == 0){
		alert("Please select user gender");
		document.ff.gender.focus();
		return false;
	}	
				
		
	if(!validate_email(document.ff.email_address,1,"Enter Email Address"))
		return false;	
		
	if(!validate_text(document.ff.password,1,"Enter Password"))
		return false;
		
	if(document.ff.suburb_id.selectedIndex == 0){
		alert("Please select a suburb");
		document.ff.suburb_id.focus();
		return false;
	}	
	
	
	if(document.ff.hear_about_us.selectedIndex == 0){
		alert("Please select how user heard about you");
		document.ff.hear_about_us.focus();
		return false;
	}	
	
	
	if(document.ff.status.selectedIndex == 0){
		alert("Please select a status");
		document.ff.status.focus();
		return false;
	}
}	


</script>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg"><?=$button?>
            User</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg"> 
    	<form method="post" action="<?=$_SERVER['PHP_SELF']?>"  name="ff" enctype="multipart/form-data" onsubmit="return validate()">
        <input type="hidden" name="enter" value="yes" />
        <input type="hidden" name="id" value="<?=$_REQUEST['id']?>" />
        <input type="hidden" name="return_url" value="<?php echo $_REQUEST['return_url']?>" />
        <input type="hidden" name="photo" value="<?=$photo?>" />
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
                  <td width="20%" class="body_content-form" valign="top">First Name:<font class="form_required-field"> *</font></td>
                  <td width="80%" valign="top"><input name="fname" value="<?=$fname?>" type="text" autocomplete="off" class="form_inputbox" size="55" /></td>
                </tr>
                <tr>
                  <td width="20%" class="body_content-form" valign="top">Last Name:<font class="form_required-field"> *</font></td>
                  <td width="80%" valign="top"><input name="lname" value="<?=$lname?>" type="text" autocomplete="off" class="form_inputbox" size="55" /></td>
                </tr>
                <tr>
                  <td  class="body_content-form">Gender:<font class="form_required-field"> *</font></td>
                  <td >
                  	<select name="gender" class="inputbox_select" style="width: 300px;">
	            	<option  value="" <?=$gender==""?'selected="selected"':'';?>>Select One</option>
		            <option value="1" <?=$gender==1?'selected="selected"':'';?>>Male</option>				
		             <option value="2" <?=$gender==2?'selected="selected"':'';?>>Female</option>	
	            	</select>
                  </td>
                </tr>
                
                 <tr>
                  <td  class="body_content-form">Email Address:<font class="form_required-field"> *</font></td>
                  <td ><input name="email_address" value="<?=$email_address?>" type="email" autocomplete="off" class="form_inputbox" size="55" />
                  </td>
                </tr>
                <tr>
                  <td class="body_content-form">Password:<font class="form_required-field"> *</font></td>
                  <td><input name="password" value="<?=$password?>" type="password" autocomplete="off" class="form_inputbox" size="55" />
                  </td>
                </tr>
               
                 
                <tr>
                  <td  class="body_content-form">Suburb:<font class="form_required-field"> *</font></td>
                  <td >
                  	<select name="suburb_id" class="inputbox_select" style="width: 300px;">
	            	<option  value="" <?=$suburb_id==""?'selected="selected"':'';?>>Select One</option>
		            <?php 
		            $sql_suburb="select id,suburb_name,suburb_state,suburb_postcode from suburb order by suburb_name ASC";
		            $result_suburb=$db->fetch_all_array($sql_suburb);
					$total_suburb=count($result_suburb);
		            
		            for($s=0; $s < $total_suburb; $s++){ ?>
		            	<option value="<?=$result_suburb[$s]['id']?>" <?=$suburb_id==$result_suburb[$s]['id']?'selected="selected"':'';?>><?=ucwords(strtolower($result_suburb[$s]['suburb_name'])).", ".$result_suburb[$s]['suburb_state'].", ".$result_suburb[$s]['suburb_postcode']?></option>				
		            <?php } ?>
	            	</select>
                  </td>
                </tr>
                 <tr>
                  <td  class="body_content-form">Street Address:</td>
                  <td ><input name="street_address" value="<?=$street_address?>" type="text" autocomplete="off" class="form_inputbox" size="55" />
                  </td>
                </tr>    
                 
                  <tr>
                  <td  class="body_content-form">Mobile No.:</td>
                  <td ><input name="phone" value="<?=$phone?>" type="text" autocomplete="off" class="form_inputbox" size="55" />
                  </td>
                </tr>                  
                <tr>
                  <td class="body_content-form" valign="top"><?=trim($photo) == NULL?'Upload':'Update';?> Photo :</td>
                  <td  valign="top"><?php if(trim($photo) != NULL){?>
                    <a href="<?=$general_func->site_url.substr($original,6).$photo?>" class="highslide" onclick="return hs.expand(this)"><img src="<?=$general_func->site_url.substr($small,6).$photo?>" border="0" /></a>&nbsp;&nbsp;
                    <?php if($button=="Update"){?>
                    <a href="users/users-new.php?action=EDIT&now=DELETE&id=<?=$_REQUEST['id']?>&return_url=<?=$return_url?>&field=photo&path=<?=$photo?>" class="htext" ><u>Delete</u></a>
                    <?php }?>
                    <br/>
                    <br/>
                    <?php }	?>
                    <input name="photo" type="file"  class="form_inputbox" size="70" /></td>
                </tr>
                 <tr>
                  <td  class="body_content-form">How user heard about us?:<font class="form_required-field"> *</font></td>
                  <td >
                  	<select name="hear_about_us" class="inputbox_select" style="width: 300px;">
	            	<option  value="" <?=$hear_about_us==""?'selected="selected"':'';?>>Select One</option>
		            <?php 
		            $sql_suburb="select id,name from hear_about_us order by name ASC";
		            $result_suburb=$db->fetch_all_array($sql_suburb);
					$total_suburb=count($result_suburb);
		            
		            for($s=0; $s < $total_suburb; $s++){ ?>
		            	<option value="<?=$result_suburb[$s]['id']?>" <?=$hear_about_us==$result_suburb[$s]['id']?'selected="selected"':'';?>><?=$result_suburb[$s]['name']?></option>				
		            <?php } ?>
	            	</select>
                  </td>
                </tr>
                
                
                <tr>                              
                  <td class="body_content-form" valign="top">Status:<font class="form_required-field"> *</font></td>
                  <td  valign="top"><select name="status"  class="inputbox_select" style="width: 100px;" >
                      <option value="">Choose One</option>
                      <option value="1" <?=$status==1?'selected="selected"':'';?>>Active</option>
                      <option value="0" <?=$status==0?'selected="selected"':'';?>>Inactive</option>
                    </select>
                    <p>&nbsp; </p></td>
                </tr>
                </tr>             
                 
              </table></td>
            <td width="8" align="left" valign="top">&nbsp;</td>
          </tr>
          
          
            <tr>
            <td colspan="4" height="30" align="center"><table width="879" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="36%"><table border="0" align="right" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                        <td align="left" valign="middle" class="body_tab-middilebg"><input name="submit" type="submit" class="submit1" value="<?=$button?>" /></td>
                        <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                      </tr>
                    </table></td>
                  <td width="3%"></td>
                  <td width="61%"><?php if($button !="Add New"){?>
                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                        <td align="left" valign="middle" class="body_tab-middilebg">
                        	<input type="button" class="submit1"  name="back" value="Back"  onclick="history.back();" />
                        	
                        	<!--<input name="back" onclick="location.href='<?=$return_url?>'"  type="button" class="submit1" value="Back" />--></td>
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
                
              </table></td>
            <td width="8" align="left" valign="top">&nbsp;</td>
          </tr>
          
          
          
          
        </table>
      </form></td>
  </tr>
</table>
<?php
include("../foot.htm");
?>
