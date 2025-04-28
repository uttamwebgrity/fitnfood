<?php
include_once ("includes/header.php");

if (!isset($_SESSION['user_login_type']) || trim($_SESSION['user_login_type']) != "users" || !isset($_SESSION['user_login_type'])) {
	$_SESSION['user_message'] = "Sorry, you have no permission to access this page!";
	$general_func -> header_redirect($general_func -> site_url);
}

$sql_payment = "select order_id,order_amount,week_start_date,week_end_date,payment_date from  payment where user_id ='" . intval($_SESSION['user_id']) . "' and order_status=1 order by payment_date DESC";
$result_payment = $db -> fetch_all_array($sql_payment);
$total_payment = count($result_payment);


?>

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
				<li>
					<a href="my-account/">My Account &raquo;</a>
				</li>
				</li>
				<li>
					Payment History
				</li>
			</ul>
		</div>
		<?php 	if( $total_payment > 0){
			  for($p=0; $p <$total_payment; $p++){ ?>
<div class="pmtHsty2">
<div class="pmtHsty">
<ul class="pmtHstyLft">
<li style="margin: 3px;"><span>Order No :</span>  FNF - A000<?=$result_payment[$p]['order_id'] ?></li>
		<li style="margin: 3px;"><span>Order Amount :</span>  <b>$<?=$result_payment[$p]['order_amount'] ?></b> p/w (GST <?=$GST?>% included)</li>
		</ul>
		<ul class="pmtHstyRht">
		<li style="margin: 3px;"><span>Payment Week :</span>  <?=date("jS M, Y ", strtotime($result_payment[$p]['week_start_date'])) ?> -- <?=date("jS M, Y ", strtotime($result_payment[$p]['week_end_date'])) ?></li>
		<li style="margin: 3px;"><span>Payment On :</span>  <?=date("jS M, Y ", strtotime($result_payment[$p]['payment_date'])) ?></li>
		</ul>
		</div>
		</div>
		<?php }
			}else{
				echo '<p> Sorry, no payment has been made yet! </p>';
				
			}
 ?>
	</div>
</div>
<?php
include_once ("includes/footer.php");
?>