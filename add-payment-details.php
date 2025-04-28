<?php
include_once ("includes/header.php");

//*** If nothing is set, send to get started page **********************//
if (!isset($_SESSION['user_login_type']) || trim($_SESSION['user_login_type']) != "users") {
	$_SESSION['user_message'] = "Sorry, you have no permission to access this page!";
	$general_func -> header_redirect($general_func -> site_url);
}

if (!isset($_SESSION['payment_details']['ebebit_payment_URL']) || trim($_SESSION['payment_details']['ebebit_payment_URL']) == NULL ) {
	$_SESSION['user_message'] = "Sorry, you have no permission to access this page!";
	$general_func -> header_redirect($general_func -> site_url);
}

$sql_content="select payment_page_heading from dynamic_pages where id=1 limit 1";
$result_content=$db->fetch_all_array($sql_content);


?>
<div class="inrBnr">
	<?php
	$db_common -> static_page_banner($dynamic_content['page_id']);
	?>
</div>
<div class="paymtInfo">
	<div class="mainDiv2">	
		<h1><?=$result_content[0]['payment_page_heading']?></h1>
		<div class="paymentInfo" style="height: 370px;">				
<iframe width="100%" height="100%" frameborder="0"   src="<?=$_SESSION['payment_details']['ebebit_payment_URL']?>" ></iframe> 
		</div>		
		<br class="clear">
			<br class="clear">
				
	</div>
</div>
<?php
include_once ("includes/footer.php");
?>