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

$sql_user = "select * from users where id=" . intval($_SESSION['user_id']) . " limit 1";
$result_user = $db -> fetch_all_array($sql_user);

$result_suburb_info = $db -> fetch_all_array("select suburb_name,delivery_cost,suburb_postcode,delivery_day,payment_debit_day,order_cutoff_day,order_cutoff_time from suburb where id=" . intval($result_user[0]['suburb_id']) . " limit 1");





if (isset($_POST['enter']) && trim($_POST['enter']) == "make_payment" && trim($_POST['after_login_form_id']) == $_SESSION['after_login_form_id']) {

	$accountType = trim($_POST['accountType']);
	
	//***************** insert member personal information in edebit ***********************//
	$clNo = 3000 + $_SESSION['user_id'];

	$edPI_url = "https://www.edebit.com.au/IS/edPI.ashx?edNo=" . $edNo . "&clNo=" . $clNo . "&cl1stName=" . urlencode($result_user[0]['fname']) . "&cl2ndName=" . urlencode($result_user[0]['lname']) . "";
	$edPI_url .= "&clAddr=" . urlencode($result_user[0]['street_address']) . "&clCity=" . urlencode(ucfirst(strtolower($result_suburb_info[0]['suburb_name']))) . "&clState=" . urlencode($result_suburb_info[0]['suburb_state']) . "&clPCode=" . $result_suburb_info[0]['suburb_postcode'] . "";
	$edPI_url .= "&clTel=" .  str_replace(" ","", trim($result_user[0]['phone'])) . "&clEmail=" . $result_user[0]['email_address'] . "&clDlName=&clDlNo=&clDlState=&accountType=" . $accountType . "&clMktNo=";
	$ch = curl_init($edPI_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$data = curl_exec($ch);
	curl_close($ch);
	
	//$data="s";

	if (strtolower($data) == "s") {
		$edPW_url = "https://www.edebit.com.au/IS/edPW.ashx?edno=" . $edNo;
		$ch = curl_init($edPW_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$edPW_data = curl_exec($ch);
		curl_close($ch);

		$edKI_url = "https://www.edebit.com.au/IS/edKI.ashx?cd_crn=" . $edNo . "-" . $clNo;
		$ch = curl_init($edKI_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$edKI_data = curl_exec($ch);
		curl_close($ch);

		$edReg_url = "https://www.edebit.com.au/IS/edReg.ashx?cd_crn=" . $edNo . "-" . $clNo . "&accountType=" . $accountType . "&" . $edKI_data . "&" . $edPW_data;
		$ch = curl_init($edReg_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_exec($ch);
		curl_close($ch);

		//**********************  edebit account created ****************************//
		$db -> query("update users set edPI_created=1 where id='" . intval($_SESSION['user_id']) . "'");
		//**************************************************************************//	
		$_SESSION['payment']['total_price']=trim($_POST['total_price']);
		$_SESSION['payment']['accountType']=$accountType;
		
		$returnURL=$general_func->site_url."payment-success.php";
			
		if(intval($result_user[0]['cc_or_dd_created']) == 0){
			$_SESSION['payment']['ebebit_payment_URL']="https://www.edebit.com.au/IS/". trim($accountType) ."Info.aspx?cd_crn=" . $edNo . "-" . $clNo."&returnURL=".$returnURL;
		 	$general_func -> header_redirect($general_func->site_url."make-payment.php");
		}else{
			$general_func -> header_redirect($general_func->site_url."order-success.php?Result=S");	
		}	
		
		//$general_func -> header_redirect($general_func->site_url."order-success.php?Result=S");		
	} else {		
		$_SESSION['user_message'] = $data;
		$general_func -> header_redirect($general_func -> site_url . "order-review/");
	}
	//**************************************************************************************//

}
//print_r ($_SESSION['payment']);
?>


<div class="inrBnr">
	<?php
	$db_common -> static_page_banner($dynamic_content['page_id']);
	?>
</div>
<div class="paymtInfo">
	<div class="mainDiv2">
		<form name="frm_order_payment" method="post" action="payment/">
		<input type="hidden" name="enter" value="make_payment" />
		<input type="hidden" name="after_login_form_id" value="<?=$_SESSION['after_login_form_id'] ?>" />	
		<h1>Payment Information</h1>
		<div class="paymtInfoOne">			
			<h2>Your Personal Information</h2>
			<ul class="paymtInfoLst">
				<li>
					<strong>Name :</strong><span><?=$result_user[0]['fname']." ".$result_user[0]['lname']?></span>
				</li>
				<li>
					<strong>Email :</strong><span><?=$result_user[0]['email_address']?></span>
				</li>
				<li>
					<strong>Address : </strong><span><?=$result_user[0]['street_address']?></span>
				</li>
				
				<li>
					<strong>Mobile : </strong><span><?=$result_user[0]['phone']?></span>
				</li>
			</ul>
			<?php if(intval($result_user[0]['cc_or_dd_created']) == 0){ ?>
			<br class="clear">
			<h2>Payment Method</h2>
			<p style="padding-top:0">
				<input type="radio" id="r1" name="accountType" value="CC" />
				<label for="r1"><span></span>Credit Card</label>			
				<input type="radio" id="r2" name="accountType" value="DD" checked="checked" />
				<label for="r2"><span></span>Bank Account</label>
			</p>				
			<?php } ?>			
		</div>
		<div class="paymtInfoTwo">
			<h2>Your Payment Information</h2>
			<p>
				<strong>Program Length</strong>
				<br>
				<?php						
				$rs_discounts = $db->fetch_all_array("select name,details,type,amt from discounts where id='" . intval($_SESSION['payment']['program_length']). "' limit 1");
				echo trim($rs_discounts[0]['name']);
				if(intval($rs_discounts[0]['type']) == 1){
					$discount_amt= $rs_discounts[0]['amt']; 	
				}else{
					if(isset($_SESSION['payment']['pickup_delivery']) && $_SESSION['payment']['pickup_delivery']== 1)
						$discount_amt = ($result_suburb_info[0]['delivery_cost'] * $rs_discounts[0]['amt'])/100; 
					else											
						$discount_amt = ($general_func->pickup_cost * $rs_discounts[0]['amt'])/100; 
				}								
				?>				
			</p>
			<p>
				<strong>Meal Plan Price</strong>
				<br>
				<span class="present_price">$<?=number_format($_SESSION['payment']['meal_plan_price'],2)?></span> p/w (GST <?=$GST?>% included)
			</p>
			<p>
				<strong><?=(isset($_SESSION['payment']['pickup_delivery']) && $_SESSION['payment']['pickup_delivery']== 1)?'Delivery':'Pickup';?> Charges</strong>
				<br>
				<?php
					if(isset($_SESSION['payment']['pickup_delivery']) && $_SESSION['payment']['pickup_delivery']== 1){
						$delivery_cost = $result_suburb_info[0]['delivery_cost']; 
						unset($_SESSION['payment']['pickup_location_id']);						
					}else{											
						$delivery_cost = $general_func->pickup_cost;
					}	 
				
				
				 if($discount_amt > 0){
				 	if($delivery_cost > $discount_amt)
						$present_delivery_cost=$delivery_cost - $discount_amt;
					else {
						$present_delivery_cost=0;
					}
				 
				 	?>
					<span class="old_price">$<?=number_format($delivery_cost,2)?></span>
					<span class="present_price">$<?=number_format($present_delivery_cost,2)?></span>
										
				<?php }else{
						$present_delivery_cost=$delivery_cost; ?>						
					<span class="present_price">$<?=number_format($present_delivery_cost,2)?></span>
				<?php }?>				
				 p/w
			</p>
			<?php
			
			
			if(trim($_SESSION['payment']['promo_code'])  != NULL){				
				$rs_promo = $db->fetch_all_array("select * from  promo_codes where promo_code='" . mysql_real_escape_string(trim($_SESSION['payment']['promo_code'])) . "' limit 1");
				$promo_code_can_be_used=0;
				
				if(count($rs_promo) == 1 && strtotime($rs_promo[0]['end_date']) > strtotime($today_date)){
					$promo_code_can_be_used=1;					
					if(intval($rs_promo[0]['user_id']) > 0 &&  intval($_SESSION['user_id']) !=  intval($rs_promo[0]['user_id'])){
						$promo_code_can_be_used=0;
					}
					
					$rs_total_used = $db->fetch_all_array("select total_used from promo_codes_used where user_id='" . intval($_SESSION['user_id']) . "' and promo_code='" . mysql_real_escape_string(trim($_SESSION['payment']['promo_code'])) . "' limit  1");
					
					$how_many_week_used=0;
					
					if(count($rs_total_used) == 1)
						$how_many_week_used=$rs_total_used[0]['total_used'];
					
					if(intval($rs_promo[0]['how_many_week']) > 0 && $how_many_week_used >= intval($rs_promo[0]['how_many_week'])){
						$promo_code_can_be_used=0;		
					}	
				}
				
				if($promo_code_can_be_used == 1){//*************  promo code will be used
					$discount_from_promo_code=0;
					$show="";
				
					$how_many_weeks="";
		
					if(intval($rs_promo[0]['how_many_week']) > 0){
						$how_many_weeks=" first ".intval($rs_promo[0]['how_many_week']). " weeks ";	
					}
				
					if(intval($rs_promo[0]['discount_type']) == 1){
						$discount_from_promo_code =$rs_promo[0]['discount_amount'];
						$show="Your will get $".$rs_promo[0]['discount_amount'] ." off of your " .  $how_many_weeks."  order amount." ;		
						$show_on_email="Your received $".$rs_promo[0]['discount_amount'] ." off of your " .  $how_many_weeks."  order amount." ;		
						
					}else if( intval($rs_promo[0]['discount_type']) == 2){
						$discount_from_promo_code = ($_SESSION['payment']['meal_plan_price']* $rs_promo[0]['discount_amount'])/100; 
						$show="Your will get ".$rs_promo[0]['discount_amount'] ."% off of your  " . $how_many_weeks."  order amount." ;	
						$show_on_email="Your received ".$rs_promo[0]['discount_amount'] ."% off of your  " . $how_many_weeks."  order amount." ;			
					}else{
						$discount_from_promo_code=0;
						$show="You will get following items with your " . $how_many_weeks ." delivery: <br/> ". nl2br($rs_promo[0]['gift_items']);	
						$show_on_email="We offered following items with your " . $how_many_weeks ." delivery: <br/> ". nl2br($rs_promo[0]['gift_items']);					
					}
					
					$_SESSION['payment']['promo_amount']=$discount_from_promo_code;	
					$_SESSION['payment']['promo_text']=$show_on_email;
					$_SESSION['payment']['how_many_week']=intval($rs_promo[0]['how_many_week']);
									
					?>
					<p>
					<strong>Promo Code Offer</strong>
					<br>
					<?=$show?>
				</p>				
					<?php								
				}
				
			}
			?>
			
			
			<p>
				<strong>Total Price</strong>
				<br>
				<?php
				$total_cost= ($_SESSION['payment']['meal_plan_price'] + $present_delivery_cost) - $discount_from_promo_code ; 
				?>				
				<span class="present_price">$<?=number_format($total_cost,2)?></span>  p/w (GST <?=$GST?>% included)
			</p>
		</div>
		<br class="clear">
		<div class="checkout_row" style="border:none; margin-top:0">
			<input type="hidden" name="total_price" value="<?=$total_cost?>" />
			<input type="button" value="Back" name="back" onclick="location.href='<?=$general_func->site_url?>order-review/'">
			<input type="submit" value="Continue" name="submit" style="float:right">
		</div>
		</form>
	</div>
</div>
<?php
include_once ("includes/footer.php");
?>