<?php
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
include_once("includes/configuration.php");

$payment_type_updated=0;

if (isset($_SESSION['payment_details']['account_type']) && isset($_SESSION['payment_details']['ebebit_payment_URL']) && isset($_GET['Result']) && strtolower(trim($_GET['Result'])) == "s"){
		
	$clNo=3000 + $_SESSION['user_id'];
		
	$edTKI_url = "https://www.edebit.com.au/IS/edTKI.ashx?cd_crn=" . $edNo . "-" . $clNo;
	$ch = curl_init($edTKI_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$edTKI_data = curl_exec($ch);
	curl_close($ch);
		
	$return_data=array();
	$return_data=@explode("&", $edTKI_data);
		
	$token_data=array();
	$token_data=@explode("=", $return_data[0]);
		
	$update_query="cc_or_dd_created=1,cc_or_dd='" . trim($_SESSION['payment_details']['account_type']) . "'";
		
	if($token_data[1] != NULL){
		$update_query .= " ,debit_token='" . trim($token_data[1]) . "' ";	
	}else{
		$update_query .= " ,debit_token='' ";		
	}		
	//****************************************************************************//		
	$db -> query("update users set $update_query where id='" . intval($_SESSION['user_id']) . "'");
	
	$payment_type_updated=1;
}	
		
?>
<link href="css/reset.css" rel="stylesheet" type="text/css">
<link href="css/fonts.css" rel="stylesheet" type="text/css">
<link href="css/style.css" rel="stylesheet" type="text/css">
<link href="css/responsive.css" rel="stylesheet" type="text/css">
<link href="css/font-awesome.css" rel="stylesheet" type="text/css">

<?php if($payment_type_updated ==1 ){ ?>
<div class="new_paymentInfo">
	<p>Your payment details has been updated.</p>
	<?php			
	if (isset($_SESSION['payment_details']['account_type']))
		unset($_SESSION['payment_details']['account_type']);	
		
	if (isset($_SESSION['payment_details']['ebebit_payment_URL']))
		unset($_SESSION['payment_details']['ebebit_payment_URL']);	
	?>	
</div>
<div style="float:right; border:none;" class="checkout_row">
	<input  type="button" value="My Account" name="myaccount" onclick="window.open('<?=$general_func -> site_url?>my-account/', '_top');" > 
</div>					
<?php }else{  ?>
<div class="new_paymentInfo">
	<p>Sorry, your payment details was not updated. Please try again.</p>
	<br class="clear">
	<div style="float:right; border:none;" class="checkout_row">
		<input  type="button" value="Continue" name="continue" onclick="window.open('<?=$general_func -> site_url?>payment-type/', '_top');" > 
	</div>	
</div>	
<?php }	?>	
	