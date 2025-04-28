<?php
include_once ("includes/header.php");
if (!isset($_SESSION['user_login_type']) || trim($_SESSION['user_login_type']) != "users" || !isset($_SESSION['user_login_type'])){
	$_SESSION['user_message'] = "Sorry, you have no permission to access this page!";
	$general_func -> header_redirect($general_func -> site_url);
}else if (!isset($_SESSION['account']['current_payment_type']) || !isset($_SESSION['account']['change_payment_type'])){
	$_SESSION['user_message'] = "Sorry, you have no permission to access this page!";
	$general_func -> header_redirect($general_func -> site_url."my-account/");	
}	




?>
<div class="inrBnr">
	<?php
	$db_common -> static_page_banner($dynamic_content['page_id']);
	?>
</div>
<div class="paymtInfo">
	<div class="mainDiv2">	
		<h1>Update Your Payment Type to <?=trim($accountType)=="DD"?'Bank Account':'Credit Card'; ?> </h1>
		<div class="paymentInfo" style="height: 370px;">				
<iframe width="100%" height="100%" frameborder="0"   src="<?=$_SESSION['account']['change_payment_type']?>" ></iframe> 
		</div>
		
		<br class="clear">
			<br class="clear">
				
	</div>
</div>
<?php
include_once ("includes/footer.php");
?>