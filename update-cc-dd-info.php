<?php
include_once ("includes/header.php");
if (!isset($_SESSION['user_login_type']) || trim($_SESSION['user_login_type']) != "users" || !isset($_SESSION['user_login_type'])) {
	$_SESSION['user_message'] = "Sorry, you have no permission to access this page!";
	$general_func -> header_redirect($general_func -> site_url);
}


$sql = "select * from users where id=" . intval($_SESSION['user_id']) . " limit 1";
$result = $db -> fetch_all_array($sql);

$returnURL=$general_func->site_url."cc-dd-updation-success.php";
$update_URL="https://www.edebit.com.au/IS/". trim($result[0]['cc_or_dd']) ."Info.aspx?cd_crn=" . $edNo . "-" . (intval($_SESSION['user_id']) + 3000)."&returnURL=".$returnURL


?>
<div class="inrBnr">
	<?php $db_common -> static_page_banner($dynamic_content['page_id']); ?>
</div>
<div class="bodyContent">
	<div class="mainDiv2">
		
		<div class="order_listingBcmb">
    	<ul>
        	<li><a href="my-account/">My Account &raquo;</a></li>
            <li>Update <?=trim($result[0]['cc_or_dd'])=="DD"?'Bank Info.':'Credit Card Info.'; ?></li>
        </ul>
    </div>
    <br class="clear" />
		<div class="paymentInfo" style="height: 370px;">		
			<iframe width="100%" height="100%" frameborder="0"   src="<?=$update_URL?>" ></iframe> 
		</div>
	</div>
</div>
<?php
include_once ("includes/footer.php");
?>