<?php
//include_once ("includes/header.php");
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
include_once("includes/configuration.php");


$sql_user = "select * from users where id=" . intval($_SESSION['user_id']) . " limit 1";
$result_user = $db -> fetch_all_array($sql_user);

$clNo = 3000 + $_SESSION['user_id'];

$payment_status=0;

if(strtolower(trim($_REQUEST['Result']))=="s" && intval($result_user[0]['cc_or_dd_created']) == 1){
	//**************************  collect cc_or_dd token *************************//
	$edTKI_url = "https://www.edebit.com.au/IS/edTKI.ashx?cd_crn=" . $edNo . "-" . $clNo;	
	$ch = curl_init($edTKI_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$edTKI_data = curl_exec($ch);
	curl_close($ch);
		
	$return_data=array();
	$return_data=@explode("&", $edTKI_data);
		
	$token_data=array();
	$token_data=@explode("=", $return_data[0]);
		
	if($token_data[1] != NULL){
		$update_query .= "debit_token='" . trim($token_data[1]) . "' ";	
		$db -> query("update users set $update_query where id='" . intval($_SESSION['user_id']) . "'");
	}		
}	
		
	
?>
 <link href="css/reset.css" rel="stylesheet" type="text/css">
  <link href="css/fonts.css" rel="stylesheet" type="text/css">
  <link href="css/style.css" rel="stylesheet" type="text/css">
  <link href="css/responsive.css" rel="stylesheet" type="text/css">
  <link href="css/font-awesome.css" rel="stylesheet" type="text/css">

	<?php if(strtolower(trim($_REQUEST['Result']))=="s"){ ?>
		<div class="new_paymentInfo">
		<p>Your <?=trim($result_user[0]['cc_or_dd'])=="DD"?'bank info.':'credit card info.'; ?> has been updated!</p>
		</div>
		<div style="float:right; border:none;" class="checkout_row">
			<input  type="button" value="My Account" name="myaccount" onclick="window.open('<?=$general_func -> site_url?>my-account/', '_top');" > 
		</div>
		<?php	
	 }else{  ?>
	 	<div class="new_paymentInfo">
		<p>Sorry, your <?=trim($result_user[0]['cc_or_dd'])=="DD"?'bank info.':'credit card info.'; ?> has not been updated. please try again.</p>
		<br class="clear">
		<div style="float:right; border:none;" class="checkout_row">
			<input  type="button" value="Try Again!" name="continue" onclick="window.open('<?=$general_func -> site_url?>update-cc-dd-info/', '_top');" > 
		</div>	
		</div>	
	<?php }	?>		
	