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

if (isset($_POST['enter']) && $_POST['enter'] == "yes" && trim($_POST['login_form_id']) == $_SESSION['login_form_id']) {

	$fname = filter_var(trim($_REQUEST['fname']), FILTER_SANITIZE_STRING);
	$lname = filter_var(trim($_REQUEST['lname']), FILTER_SANITIZE_STRING);
	$email_address = filter_var(trim($_REQUEST['email_address']), FILTER_SANITIZE_EMAIL);
	$street_address = filter_var(trim($_REQUEST['street_address']), FILTER_SANITIZE_STRING);
	$suburb_id = intval($_REQUEST['suburb_id']);
	$phone = filter_var(trim($_REQUEST['phone']), FILTER_SANITIZE_NUMBER_INT);
	$refered_code = filter_var(trim($_REQUEST['refered_code']), FILTER_SANITIZE_STRING);

	if ($db -> already_exist_update("users", "id", $_SESSION['user_id'], "email_address", $email_address)) {
		$_SESSION['user_message'] = "Sorry, your specified email address is already taken!";
	} else {
		$data['fname'] = $fname;
		$data['lname'] = $lname;
		$data['seo_link'] = $general_func -> create_seo_link($fname . " " . $lname);

		if ($db -> already_exist_inset("users", "seo_link", $data['seo_link'])) {//******* exit
			$data['seo_link'] = $_SESSION['user_id'] . "-" . $data['seo_link'];
		}

		$data['email_address'] = $email_address;
		$data['street_address'] = $street_address;
		$data['suburb_id'] = $suburb_id;
		$data['phone'] = $phone;
		$data['refered_code'] = $refered_code;
		$data['date_modified'] = $current_date_time;

		$db -> query_update("users", $data, "id='" . $_SESSION['user_id'] . "'");

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
$refered_code = $result[0]['refered_code'];
$photo = $result[0]['photo'];
?>

<script type="text/javascript">
	$(document).ready(function() {

		$(".dayPnl1_new1 li").mouseenter(function() {
			$(this).find(".tip_box1").show();
		});

		$(".dayPnl1_new1 li").mouseleave(function() {
			$(this).find(".tip_box1").hide();
		});

		$(".close_pop").click(function() {
			$(this).parent().parent().find(".tip_box1").hide();
		});
	})
</script>

<link href="css/fonts.css" rel="stylesheet" type="text/css" />
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link href="css/responsive.css" rel="stylesheet" type="text/css" />
<div class="inrBnr">
	<?php $db_common -> static_page_banner($dynamic_content['page_id']); ?>
</div>
<div class="bodyContent">
	<div class="mainDiv2">
		<div class="order_listingBcmb">
			<ul>
				<li>
					<a href="#url">My Account</a> &raquo;
				</li>
				<li>Payment History</li>
			</ul>
		</div>

		
  
        <div class="pmtHsty2">
          <div class="pmtHsty">
            <ul class="pmtHstyLft">
              <li><span>Order No :</span>  FNF - A00015</li>
              <li><span>Order Amount :</span>  <b>$ 225</b></li>
              </ul>
            <ul class="pmtHstyRht">
              <li><span>Payment Week :</span>  1st Sept -- 6th Sept  2014</li>
              <li><span>Payment On :</span>  01.10. 2014</li>
              </ul>
            </div>
        </div>
        <div class="pmtHsty2">
          <div class="pmtHsty">
            <ul class="pmtHstyLft">
              <li><span>Order No :</span>  FNF - A00015</li>
              <li><span>Order Amount :</span>  <b>$ 225</b></li>
              </ul>
            <ul class="pmtHstyRht">
              <li><span>Payment Week :</span>  1st Sept -- 6th Sept  2014</li>
              <li><span>Payment On :</span>  01.10. 2014</li>
              </ul>
            </div>
        </div>
        <div class="pmtHsty2">
          <div class="pmtHsty">
            <ul class="pmtHstyLft">
              <li><span>Order No :</span>  FNF - A00015</li>
              <li><span>Order Amount :</span>  <b>$ 225</b></li>
              </ul>
            <ul class="pmtHstyRht">
              <li><span>Payment Week :</span>  1st Sept -- 6th Sept  2014</li>
              <li><span>Payment On :</span>  01.10. 2014</li>
              </ul>
            </div>
        </div>
        <div class="pmtHsty2">
          <div class="pmtHsty">
            <ul class="pmtHstyLft">
              <li><span>Order No :</span>  FNF - A00015</li>
              <li><span>Order Amount :</span>  <b>$ 225</b></li>
              </ul>
            <ul class="pmtHstyRht">
              <li><span>Payment Week :</span>  1st Sept -- 6th Sept  2014</li>
              <li><span>Payment On :</span>  01.10. 2014</li>
              </ul>
            </div>
        </div>
        
        <div class="peginationPnl">
   	  <div class="jumpToPnl">
      	<ul>
            <li>Jump to Page</li>
            <li><select name=""><option>1</option></select></li>
            <li>of  339</li>
        </ul>
      </div>
      <div class="showingPnl">
      	<ul>
            <li>Showing  1 -  50 of 16939 </li>
            <li>&laquo; Pre     <a href="#url">Next &raquo;</a></li>
        </ul>
      </div>
    </div>
        
	</div>
</div>
<?php
include_once ("includes/footer.php");
?>