<?php
include_once ("includes/header.php");
if (!isset($_SESSION['user_login_type']) || trim($_SESSION['user_login_type']) != "users" || !isset($_SESSION['user_login_type'])) {
	$_SESSION['user_message'] = "Sorry, you have no permission to access this page!";
	$general_func -> header_redirect($general_func -> site_url);
}

$small = "photo/small/";
$original = "photo/";

if (isset($_POST['enter']) && $_POST['enter'] == "photo" && trim($_POST['login_form_id']) == $_SESSION['login_form_id']) {
	if ($_FILES['photo']['size'] > 0 && $general_func -> valid_file_type($_FILES["photo"]["name"], $_FILES["photo"]["type"])) {
		@unlink($original . $_REQUEST['old_photo']);
		@unlink($small . $_REQUEST['photo']);

		$uploaded_name = array();

		$userfile_name = $_FILES['photo']['name'];
		$userfile_tmp = $_FILES['photo']['tmp_name'];
		$userfile_size = $_FILES['photo']['size'];
		$userfile_type = $_FILES['photo']['type'];

		$path = $_SESSION['user_id'] . "_" . $general_func -> remove_space_by_hypen($security_validator -> sanitize_filename($userfile_name));
		$img = $original . $path;
		move_uploaded_file($userfile_tmp, $img) or die();

		$uploaded_name['photo'] = $path;
		$db -> query_update("users", $uploaded_name, 'id=' . $_SESSION['user_id']);

		list($width, $height) = getimagesize($img);

		if ($width > 214 || $height > 214) {
			$upload -> uploaded_image_resize(214, 214, $original, $small, $path);
		} else {
			copy($img, $small . $path);
		}

		if ($width > 800 || $height > 700) {
			$upload -> uploaded_image_resize(800, 700, $original, $original, $path);
		}
	}
}

$user_has_an_order = $db_common -> user_has_an_order(intval($_SESSION['user_id']));

if (isset($_POST['enter']) && $_POST['enter'] == "yes" && trim($_POST['login_form_id']) == $_SESSION['login_form_id']) {

	$fname = filter_var(trim($_REQUEST['fname']), FILTER_SANITIZE_STRING);
	$lname = filter_var(trim($_REQUEST['lname']), FILTER_SANITIZE_STRING);
	$email_address = filter_var(trim($_REQUEST['email_address']), FILTER_SANITIZE_EMAIL);
	$street_address = filter_var(trim($_REQUEST['street_address']), FILTER_SANITIZE_STRING);
	$gender=intval($_REQUEST['gender']);
	$hear_about_us=intval($_REQUEST['hear_about_us']);
	
	if ($user_has_an_order == 0)
		$suburb_id = intval($_REQUEST['suburb_id']);

	$phone = filter_var(trim($_REQUEST['phone']), FILTER_SANITIZE_STRING);
	$refered_code = filter_var(trim($_REQUEST['refered_code']), FILTER_SANITIZE_STRING);

	if ($db -> already_exist_update("users", "id", $_SESSION['user_id'], "email_address", $email_address)) {
		$_SESSION['user_message'] = "Sorry, your specified email address is already taken!";
	}else if(trim($refered_code) != NULL && ! $db_common->refered_code_present(trim($refered_code))){
		$_SESSION['user_message']="Sorry, your provided trainer referrer number number does not exist!";		
	} else {
		$data['fname'] = $fname;
		$data['lname'] = $lname;
		$data['seo_link'] = $general_func -> create_seo_link($fname . " " . $lname);

		if ($db -> already_exist_inset("users", "seo_link", $data['seo_link'])) {//******* exit
			$data['seo_link'] = $_SESSION['user_id'] . "-" . $data['seo_link'];
		}

		$data['email_address'] = $email_address;
		$data['street_address'] = $street_address;
		$data['gender']=$gender;		
		$data['hear_about_us']=$hear_about_us;			

		if ($user_has_an_order == 0)
			$data['suburb_id'] = $suburb_id;

		$data['phone'] = $phone;
		$data['refered_code'] = $refered_code;
		$data['date_modified'] = $current_date_time;

		$db -> query_update("users", $data, "id='" . $_SESSION['user_id'] . "'");
				
		//********************   update information at edebit also ***********************************//
		$rs_extra_info = $db->fetch_all_array("select cc_or_dd,suburb_state,suburb_postcode  from users u left join suburb s on u.suburb_id=s.id where u.id='" . $_SESSION['user_id'] . "' limit 1");
		$clNo = 3000 + $_SESSION['user_id'];

		$edPI_url = "https://www.edebit.com.au/IS/edPI.ashx?edNo=" . $edNo . "&clNo=" . $clNo . "&cl1stName=" . urlencode($fname) . "&cl2ndName=" . urlencode($lname) . "";
		$edPI_url .= "&clAddr=" . urlencode($street_address) . "&clCity=&clState=" . urlencode($rs_extra_info[0]['suburb_state']) . "&clPCode=" . $rs_extra_info[0]['suburb_postcode'] . "";
		$edPI_url .= "&clTel=" .  str_replace(" ","", trim($phone)) . "&clEmail=" . $email_address . "&clDlName=&clDlNo=&clDlState=&accountType=" . $rs_extra_info[0]['cc_or_dd'] . "&clMktNo=";
		
		$ch = curl_init($edPI_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec($ch);
		curl_close($ch);		
		//********************************************************************************************//
		

		if ($db -> affected_rows > 0)
			$_SESSION['user_message'] = " Your profile successfully updated!";

		$general_func -> header_redirect($general_func -> site_url . "my-account/");
	}
}

$sql = "select * from users where id=" . intval($_SESSION['user_id']) . " limit 1";
$result = $db -> fetch_all_array($sql);
$fname = $result[0]['fname'];
$lname = $result[0]['lname'];
$email_address = $result[0]['email_address'];
$street_address = $result[0]['street_address'];
$suburb_id = $result[0]['suburb_id'];
$phone = $result[0]['phone'];
$gender = $result[0]['gender'];
$hear_about_us = $result[0]['hear_about_us'];

$refered_code = $result[0]['refered_code'];

?>
<div class="inrBnr">
	<?php $db_common -> static_page_banner($dynamic_content['page_id']); ?>
</div>
<div class="bodyContent">
	<div class="mainDiv2">
		
		<div class="order_listingBcmb">
    	<ul>
        	<li><a href="my-account/">My Account &raquo;</a></li>
            <li>Update Profile</li>
        </ul>
    </div>
    <br class="clear" />
		<div class="my_account">
			<script type="text/javascript">
				$(document).ready(function() {

					$('.upload_photo_click').click(function() {
						$(this).parent().find('.upload_form').slideDown("");
					});

					$('.close_upload_photo').click(function() {
						$(this).parent().parent().parent().find('.upload_form').slideUp("");
					});
				});
			</script>
			<div class="my_account_left">
				<div class="my_account_left_img_area">
					<?php					
					$display_height="";					
	        		if(trim($result[0]['photo']) == NULL){
	        			//check whether fb or google user
	        			if($result[0]['facebook_id']  != NULL || trim($result[0]['google_id']) != NULL){
	        				if($result[0]['facebook_id']  != NULL){
	        					$img_path="http://graph.facebook.com/". $result[0]['facebook_id'] . "/picture?type=large";
	        					list(, $height, , ) = getimagesize($img_path);	
								if($height > 192)
									$display_height='height="192"';	
	        				}else{
	        					$img_path=trim($result[0]['google_photo']);	
								list(, $height, , ) = getimagesize($img_path);	
								if($height > 192)
									$display_height='height="192"';	
	        				}
					   	}else{					   	
							$img_path="images/no-image.jpg";
					   	}
	        		}else{
						$img_path="photo/small/".trim($result[0]['photo']);
					}		        		
	        		 
	        		?>	        		
	        		<img <?php echo $display_height; ?> src="<?=$img_path?>"  alt="" />				
					
				</div>
				<div class="submit_box for_upload_photo upload_photo_click">
					<span></span>
					<input name="" type="button" value="Upload Photo" />
				</div>
				<form method="post" action="update-profile/" enctype="multipart/form-data" name="frm_photo">
					<input type="hidden" name="enter" value="photo" />
					<input type="hidden" name="login_form_id" value="<?=$_SESSION['login_form_id'] ?>" />
					<input type="hidden" name="old_photo" value="<?=trim($photo) ?>" />
					<div class="upload_form">
						<div class="form_row">
							<script>
								function openFileOption() {
									document.getElementById("file1").click();
								}


								$(document).ready(function() {
									$('#file1').change(function(evt) {
										$('#file_one_value').html($(this).val());
									});
								});
							</script>
							<label>Change Photo</label>
							<div class="other_field_container">
								<input type="file" id="file1" name="photo" style="display:none;" />
								<div class="show_uploaded_file" id="file_one_value"></div>
								<a onclick="openFileOption();return;" class="other_field_browse">Browse</a>
							</div>
						</div>
						<div class="form_row">
							<input type="submit" value="Submit" />
							<input type="button" value="Close" class="close_upload_photo" />
						</div>
					</div>
				</form>
			</div>
			<script>
				function profile_validate(){
var error=0;

if($("#fname").val().trim() == ""){
document.getElementById("fname").style.border="1px solid red";
error++;
}else{
document.getElementById("fname").style.border="1px solid #D8D9DA";
}

if($("#lname").val().trim() == ""){
document.getElementById("lname").style.border="1px solid red";
error++;
}else{
document.getElementById("lname").style.border="1px solid #D8D9DA";
}

if($("#email_address").val().trim() == "" || ! validate_email_without_msg(document.getElementById("email_address"))){
document.getElementById("email_address").style.border="1px solid red";
error++;
}else{
document.getElementById("email_address").style.border="1px solid #D8D9DA";
}
if(document.getElementById("gender_r").value =="") {
          		document.getElementById("gender_r").style.border = "1px solid red";
				error++;  	
            }else{
            	document.getElementById("gender_r").style.border = "1px solid #DADCDD";	
            }

			 if(document.getElementById("hear_about_us").value =="") {
          		document.getElementById("hear_about_us").style.border = "1px solid red";
				error++;  	
            }else{
            	document.getElementById("hear_about_us").style.border = "1px solid #DADCDD";	
            }


<?php if($user_has_an_order == 0){ ?>
	if(document.ff.suburb_id.selectedIndex == 0) {
		document.getElementById("suburb_id").style.border = "1px solid red";
		error++;
	} else {
		document.getElementById("suburb_id").style.border = "1px solid #D8D9DA";
	}
<?php } ?>
	if($("#phone").val().trim() == "") {
		document.getElementById("phone").style.border = "1px solid red";
		error++;
	} else {
		document.getElementById("phone").style.border = "1px solid #D8D9DA";
	}

	if($("#street_address").val().trim() == "") {
		document.getElementById("street_address").style.border = "1px solid red";
		error++;
	} else {
		document.getElementById("street_address").style.border = "1px solid #D8D9DA";
	}

	if(error > 0)
		return false;
	else
		return true;
	}
	</script>
			<form method="post" action="<?=$_SERVER['PHP_SELF'] ?>"  name="ff" onsubmit="return profile_validate()">
				<input type="hidden" name="enter" value="yes" />
				<input type="hidden" name="login_form_id" value="<?=$_SESSION['login_form_id'] ?>" />
				<div class="my_account_right">
					<div class="update_profile_form">
						<div class="form_block">
							<div class="form_row">
								<label>First Name :</label>
								<input type="text" id="fname" name="fname" value="<?=$fname ?>" />
							</div>
							<div class="form_row">
								<label>Last Name :</label>
								<input type="text" id="lname" name="lname" value="<?=$lname ?>" />
							</div>
							<div class="form_row">
								<label>Email :</label>
								<input type="email" id="email_address" name="email_address" value="<?=$email_address ?>"  />
							</div>
							<div class="form_row normal_select">
								<label>Gender :</label>
								<label class="custom-select">
									<select name="gender" id="gender_r"> 
										<option value="" selected>Gender</option>
                            			<option value="1" <?=$gender==1?'selected="selected"':'';?>>Male</option>
                            			<option value="2" <?=$gender==2?'selected="selected"':'';?>>Female</option>
									</select> </label>
							</div>
							
							<div class="form_row">
								<label>Trainer Reference No.<strong>if any</strong></label>
								<input type="text" name="refered_code" value="<?=$refered_code ?>" />								
							</div>	
						</div>
						<div class="form_block">
							<div class="form_row normal_select">
								<label>Suburb :</label>
								<label class="custom-select">
									<select name="suburb_id" id="suburb_id"   <?=$user_has_an_order > 0 ? 'class="disabled" disabled="disabled"' : ''; ?>> 
										<option  value="" <?=$suburb_id == "" ? 'selected="selected"' : ''; ?>>Select One</option>
										<?php
										$sql_suburb="select id,suburb_name,suburb_state,suburb_postcode from suburb where status=1 order by suburb_name ASC";
										$result_suburb=$db->fetch_all_array($sql_suburb);
										$total_suburb=count($result_suburb);

										for($s=0; $s < $total_suburb; $s++){ ?>
										<option value="<?=$result_suburb[$s]['id'] ?>" <?=$suburb_id == $result_suburb[$s]['id'] ? 'selected="selected"' : ''; ?>><?=ucwords(strtolower($result_suburb[$s]['suburb_name'])) . ", " . $result_suburb[$s]['suburb_state'] . ", " . $result_suburb[$s]['suburb_postcode'] ?></option>
										<?php } ?>
									</select> </label>
							</div>
							<div class="form_row">
								<label>Address :</label>
								<input type="text" name="street_address" id="street_address" value="<?=$street_address ?>" />
								
							</div>
							<div class="form_row">
								<label>Mobile :</label>
								<input type="text" name="phone" id="phone" value="<?=$phone ?>" />
							</div>
							<div class="form_row normal_select">
								<label>How did you hear about us? :</label>
								<label class="custom-select">
									<select id="hear_about_us" name="hear_about_us"> 
										<option  value="" <?=$hear_about_us == "" ? 'selected="selected"' : ''; ?>>Select One</option>
										  <?php 
								            $sql_suburb="select id,name from hear_about_us where status=1 order by name ASC";
								            $result_suburb=$db->fetch_all_array($sql_suburb);
											$total_suburb=count($result_suburb);
								            
								            for($s=0; $s < $total_suburb; $s++){ ?>
								            	<option value="<?=$result_suburb[$s]['id']?>" <?=$hear_about_us==$result_suburb[$s]['id']?'selected="selected"':'';?>><?=$result_suburb[$s]['name']?></option>				
								            <?php } ?>
									</select> </label>
							</div>
							
							
							<div class="form_row">
								<input type="submit" name="submit" value="Update Profile" />
							</div>
						</div>

					</div>
				</div>
			</form>
			<div class="clear"></div>
		</div>
	</div>
</div>
<?php
include_once ("includes/footer.php");
?>