<?php
include_once("includes/header.php");
if(!isset($_SESSION['user_login_type']) || trim($_SESSION['user_login_type']) != "users" || !isset($_SESSION['user_login_type'])){	
	$_SESSION['user_message']="Sorry, you have no permission to access this page!";
	$general_func->header_redirect($general_func->site_url);
}


if (isset($_POST['enter']) && trim($_POST['enter']) == "payment_type" && trim($_POST['after_login_form_id']) == $_SESSION['after_login_form_id']) {
	$accountType = trim($_POST['accountType']);
	
	$returnURL=$general_func->site_url."add-payment-type.php";	
	$clNo=3000 + $_SESSION['user_id'];
	
	if(trim($accountType) != NULL){
		$_SESSION['payment_details']['account_type']=$accountType;
		$_SESSION['payment_details']['ebebit_payment_URL']="https://www.edebit.com.au/IS/". trim($accountType) ."Info.aspx?cd_crn=" . $edNo . "-" . $clNo."&returnURL=".$returnURL;
		$general_func -> header_redirect($general_func->site_url."add-payment-details.php");
	}	
}	


$sql_user="select CONCAT(fname,' ',lname) as name,email_address,street_address,phone  from users where id='" . $_SESSION['user_id']. "' limit 1";
$result_user_info=$db->fetch_all_array($sql_user);
?>
<div class="inrBnr">
	<?php $db_common->static_page_banner($dynamic_content['page_id']);?>										
</div>
<div class="bodyContent">
	<div class="mainDiv2">
  		<h3>Choose Payment Type</h3>
   		<div class="my_account">
	      	<form name="frm_order_payment" method="post" action="payment-type/">
			<input type="hidden" name="enter" value="payment_type" />
			<input type="hidden" name="after_login_form_id" value="<?=$_SESSION['after_login_form_id'] ?>" />		
			<div class="paymtInfoOne">	
				<h2 style="padding: 0 0 10px 0;">Your Personal Information</h2>			
				<ul class="paymtInfoLst">
					<li>
						<strong>Name :</strong><span><?=$result_user_info[0]['name']?></span>
					</li>
					<li>
						<strong>Email :</strong><span><?=$result_user_info[0]['email_address']?></span>
					</li>
					<li>
						<strong>Address : </strong><span><?=$result_user_info[0]['street_address']?></span>
					</li>				
					<li>
						<strong>Mobile : </strong><span><?=$result_user_info[0]['phone']?></span>
					</li>
				</ul>			
				<br class="clear">
				<h2 style="padding: 23px 0 10px;">Payment Method</h2>
				<p style="padding-top:0">
					<input type="radio" id="r1" name="accountType" value="CC" />
					<label for="r1"><span></span>Credit Card</label>			
					<input type="radio" id="r2" name="accountType" value="DD" checked="checked" />
					<label for="r2"><span></span>Bank Account</label>
				</p>				
			</div>		
			<br class="clear">
			<div class="checkout_row" style="border:none; margin-top:0">
				<input type="hidden" name="total_price" value="<?=$total_cost?>" />
				<input type="button" value="Back" name="back" onclick="location.href='<?=$general_func->site_url?>my-account/'">
				<input type="submit" value="Continue" name="submit" style="float:right">
			</div>
			</form>		      
    	</div>
  	</div>
</div>
<?php
include_once("includes/footer.php");
?>