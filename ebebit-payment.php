<?php
include_once ("includes/header.php");

//*** If nothing is set, send to get started page **********************//
if (!isset($_SESSION['fill_the_questionnaire']) && !isset($_SESSION['choose_your_meal_plan']) && !isset($_SESSION['customize_your_meal_plan']))
	$general_func -> header_redirect($general_func -> site_url . "get-started/");

if (!isset($_SESSION['payment'])) {
	$_SESSION['user_message'] = "Please review your order page and continue.";
	$general_func -> header_redirect($general_func -> site_url . "order-review/");
}

if (!isset($_SESSION['user_login_type']) || trim($_SESSION['user_login_type']) != "users") {
	$_SESSION['user_message'] = "Sorry, you have no permission to access this page!";
	$general_func -> header_redirect($general_func -> site_url);
}

if (!isset($_SESSION['payment']['ebebit_payment_URL']) || trim($_SESSION['payment']['ebebit_payment_URL']) == NULL ) {
	$_SESSION['user_message'] = "Sorry, you have no permission to access this page!";
	$general_func -> header_redirect($general_func -> site_url);
}


?>
<div class="inrBnr">
	<?php
	$db_common -> static_page_banner($dynamic_content['page_id']);
	?>
</div>
<div class="paymtInfo">
	<div class="mainDiv2">
		<h1>Provide your account information</h1>
		<div class="paymentInfo">
			
<iframe width="400" height="215" frameborder="0" scrolling="no"  src="<?=$_SESSION['payment']['ebebit_payment_URL']?>" ></iframe> 
		</div>		
		<br class="clear">
	</div>
</div>
<?php
include_once ("includes/footer.php");
?>