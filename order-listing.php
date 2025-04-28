<?php
include_once ("includes/header.php");

if (!isset($_SESSION['user_login_type']) || trim($_SESSION['user_login_type']) != "users" || !isset($_SESSION['user_login_type'])) {
	$_SESSION['user_message'] = "Sorry, you have no permission to access this page!";
	$general_func -> header_redirect($general_func -> site_url);
}

if ($db_common -> user_has_an_order(intval($_SESSION['user_id'])) == 0) {
	$_SESSION['user_message'] = "Sorry, you have not made any order yet!";
	$general_func -> header_redirect($general_func -> site_url . "my-account/");
}

if(isset($_REQUEST['action']) && trim($_REQUEST['action']) == "cancel_next_week" && trim($_REQUEST['after_login_form_id'])==$_SESSION['after_login_form_id']){
  	
	$current_order_id=mysql_result(mysql_query("SELECT id FROM orders WHERE user_id='" . intval($_SESSION['user_id']) . "' and current_order=1"), 0,0);
		
	if(mysql_num_rows(mysql_query("select id from order_cancel_from_next_week WHERE user_id='" . intval($_SESSION['user_id']) . "' and order_id='" . intval($current_order_id) . "'")) == 0){
		
		$db->query("INSERT INTO order_cancel_from_next_week (order_id,user_id)
  			SELECT id,user_id
  			FROM orders WHERE user_id='" . intval($_SESSION['user_id']) . "' and current_order=1");		
	}
		
	$_SESSION['user_message'] = "Your current order will automatically be cancelled from next week!";
	$general_func -> header_redirect($general_func -> site_url . "order-listing/");	
}


if(isset($_REQUEST['action']) && trim($_REQUEST['action']) == "cancel" && trim($_REQUEST['after_login_form_id'])==$_SESSION['after_login_form_id']){
  	$db -> query("update orders set status=3, current_order=0 where user_id='" . intval($_SESSION['user_id']) . "' and status < 3 limit 1");	
	$_SESSION['user_message'] = "Your current order has been successfully cancelled!";
	$general_func -> header_redirect($general_func -> site_url . "order-listing/");	
}

 
 

if(isset($_REQUEST['action']) && trim($_REQUEST['action']) == "hold" && trim($_REQUEST['after_login_form_id'])==$_SESSION['after_login_form_id']){
			
	list($month,$day,$year)=@explode("/",trim($_GET['date_form']));	
		
  	$db->query("INSERT INTO order_hold_from_next_week (order_id,user_id)
  	SELECT id,user_id
  	FROM orders WHERE user_id='" . intval($_SESSION['user_id']) . "' and current_order=1");		
	
	$hold_from=$year."-".$month."-".$day;	
	$db->query("update order_hold_from_next_week set hold_from='" . $hold_from . "' where id='" . mysql_insert_id() . "'");
	
	
  	//$db -> query("update orders set status=2 where user_id='" . intval($_SESSION['user_id']) . "' and status = 1 limit 1");	
	$_SESSION['user_message'] = "Your current order will be hold from ". date("jS M, Y ", strtotime($hold_from));
	$general_func -> header_redirect($general_func -> site_url . "order-listing/");	
} 


if(isset($_REQUEST['action']) && trim($_REQUEST['action']) == "reactive" && trim($_REQUEST['after_login_form_id'])==$_SESSION['after_login_form_id']){
  	$db -> query("update orders set status=1 where user_id='" . intval($_SESSION['user_id']) . "' and status = 2 limit 1");	
	$_SESSION['user_message'] = "Your hold order has been reactivated!";
	$general_func -> header_redirect($general_func -> site_url . "order-listing/");	
}



$sql_current_order = "select o.id,program_length,pickup_delivery,order_type,order_amount,order_start_date,name,o.status,suburb_id from orders o  left join meal_plan_category p on o.meal_plan_category_id=p.id";
$sql_current_order .= " left join  users u on o.user_id=u.id";
$sql_current_order .= " where user_id='" . intval($_SESSION['user_id']) . "' and current_order=1 limit 1";
$result_current_order = $db -> fetch_all_array($sql_current_order);


$sql_next_week_order = "select o.id,program_length,pickup_delivery,order_type,order_amount,order_start_date,name,o.status,suburb_id from orders o  left join meal_plan_category p on o.meal_plan_category_id=p.id";
$sql_next_week_order .= " left join  users u on o.user_id=u.id";
$sql_next_week_order .= " where user_id='" . intval($_SESSION['user_id']) . "' and o.status=5 limit 1";
$result_next_week_order = $db -> fetch_all_array($sql_next_week_order);


$sql_old_order = "select o.id,program_length,pickup_delivery,order_type,order_amount,order_start_date,name,o.status,suburb_id from orders o  left join meal_plan_category p on o.meal_plan_category_id=p.id";
$sql_old_order .= " left join  users u on o.user_id=u.id";
$sql_old_order .= " where user_id='" . intval($_SESSION['user_id']) . "' and current_order=0 and o.status=3  order by date_ordered DESC";
$result_old_order = $db -> fetch_all_array($sql_old_order);
$total_old_orders=count($result_old_order);




$result_suburb_info = $db -> fetch_all_array("select suburb_name,suburb_state,delivery_cost,suburb_postcode,delivery_day,payment_debit_day,order_cutoff_day,order_cutoff_time from suburb where id=" . intval($result_current_order[0]['suburb_id']) . " limit 1");
$delivery_day = $general_func -> day_name($result_suburb_info[0]['delivery_day']);
$payment_debit_day = $general_func -> day_name($result_suburb_info[0]['payment_debit_day']);
$order_cutoff_day = $general_func -> day_name($result_suburb_info[0]['order_cutoff_day']) . " " . date("h:i A", strtotime($result_suburb_info[0]['order_cutoff_time']));


if(count($result_current_order) == 1){

	$order_can_be_modified=0;	
							
	if($result_current_order[0]['status'] == 0){							
		//*********  order first week cutoffdaytime *****************//							
		$day_diff_between_delivery_and_cutoff_day=$result_suburb_info[0]['delivery_day'] -  $result_suburb_info[0]['order_cutoff_day'];		
								
		list($hour,$minute,$secound)=@explode(":",$result_suburb_info[0]['order_cutoff_time']);									
		list($year,$month,$day)=@explode("-",date("Y-m-d",strtotime($result_current_order[0]['order_start_date'])));
															
		//check whether is  it the current week order *********// 							
		$first_week_order_cutoff_day_time=mktime($hour,$minute,$secound,$month,$day-$day_diff_between_delivery_and_cutoff_day,$year);
								
		if($current_time_ms <= $first_week_order_cutoff_day_time){//*****  not yet started first cuttoff day
			$order_can_be_modified=1;	
		}else{
			$cut_off_date_time_ms=strtotime(date("Y-m-d",strtotime(strtolower($general_func -> day_name($result_suburb_info[0]['order_cutoff_day'])) . ' this week'))." ".$result_suburb_info[0]['order_cutoff_time']);	
			if($current_time_ms <= $cut_off_date_time_ms){
				$order_can_be_modified=1;
			}
		}								
	}else if($result_current_order[0]['status'] == 1){
								
		$cut_off_date_time_ms=strtotime(date("Y-m-d",strtotime(strtolower($general_func -> day_name($result_suburb_info[0]['order_cutoff_day'])) . ' this week'))." ".$result_suburb_info[0]['order_cutoff_time']);	
																				
		if($current_time_ms <= $cut_off_date_time_ms){
			$order_can_be_modified=1;
		}							
	}		

}	

if($order_can_be_modified ==1){
	$minDate=1;	
}else{	
	$minDate=$todays_day=(7 - date("N"))+1;	
}

//print_r ($_SESSION);

?>

<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css" type="text/css" />
    
<script type="text/javascript">
	$(document).ready(function() {
		$(".confirm_hold").easyconfirm("");
		$("#alert_hold").click(function() {
			
			var date_from=$("#datepicker").val();
			if(date_from ==""){
				document.getElementById("datepicker").style.border = "1px solid red";				
			}else{
				location.href="<?=$_SERVER['PHP_SELF']?>?date_form=" + date_from + "&action=hold&after_login_form_id=<?=$_SESSION['after_login_form_id']?>";
			}			
		});
		
		
		$(".confirm_reactive").easyconfirm("");
		$("#alert_reactive").click(function() {
			location.href="<?=$_SERVER['PHP_SELF']?>?action=reactive&after_login_form_id=<?=$_SESSION['after_login_form_id']?>";
		});
		
		
		$(".confirm_delete").easyconfirm("");
		$("#alert_delete").click(function() {
			location.href="<?=$_SERVER['PHP_SELF']?>?action=cancel&after_login_form_id=<?=$_SESSION['after_login_form_id']?>";
		});
		
		$(".confirm_delete_next_week").easyconfirm("");
		$("#alert_delete_next_week").click(function() {
			location.href="<?=$_SERVER['PHP_SELF']?>?action=cancel_next_week&after_login_form_id=<?=$_SESSION['after_login_form_id']?>";
		});
		
		
		
	});
	</script>
	<script src="js/jquery.easy-confirm-dialog.js"></script>


<!--<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
<script type="text/javascript" src="http://code.jquery.com/jquery-1.10.2.js"></script>
<script type="text/javascript" src="http://code.jquery.com/ui/1.11.2/jquery-ui.js"></script>-->
<?php if(count($result_current_order) == 1){ ?>
<script>
$(function() {
$( "#datepicker" ).datepicker({ minDate: <?=$minDate?>, maxDate: "+1Y +10D" });


});
</script>
<?php } ?>
<style type="text/css">
.ui-datepicker .ui-datepicker-next:before{ content:"" !important; }
.ui-datepicker .ui-datepicker-prev:before{ content:"" !important; }
</style>

<div class="inrBnr">
	<?php $db_common -> static_page_banner($dynamic_content['page_id']); ?>
</div>
<div class="bodyContent">
	<div class="mainDiv2">
		<div class="order_listingBcmb">
            <ul>
				<li>
					<a href="my-account/">My Account &raquo;</a>
				</li>
				<li>
					My Orders
				</li>
			</ul>
            <h6>Current Date &amp; Time: <span><?php echo date("jS M. D, Y h:i A",time()); ?></span></h6>
		</div>
		<?php if(count($result_current_order) == 1){ ?>
		<div class="order_listing">

			<!-- row -->
			<div class="order_listing_row" style="background: #ffffff;">
				<div class="order_listingCrntTag">
					Current
				</div>
				<div class="order_listing_col">
					<ul>
						<li>
							Order No : <span>FNF - A000<?=intval($result_current_order[0]['id']) ?></span>
						</li>
						<li>
							Order Start Date : <span>
								
								<?php
								if(strtotime($result_current_order[0]['order_start_date']) >= strtotime($today_date)){
									echo date("jS M, Y", strtotime($result_current_order[0]['order_start_date']));	
								}else if($result_suburb_info[0]['delivery_day'] == date("N")){
									echo date("jS M, Y", strtotime($today_date));
								}else{
									echo date("jS M, Y",strtotime('next '. strtolower($general_func -> day_name($result_suburb_info[0]['delivery_day']))));
								}
								?></span>
						</li>
						<li>
							Order Amount : <strong>$<?=$result_current_order[0]['order_amount'] ?>  p/w (GST <?=$GST?>% included)</strong>
						</li>
						
					</ul>
				</div>
				<div class="order_listing_col">
					<ul>
						<li>
							Cut off Date : <span><?=$order_cutoff_day ?></span>
						</li>
						<li>
							Payment Date : <span><?=$payment_debit_day ?></span>
						</li>
						
							<?php if($result_current_order[0]['pickup_delivery']==1){?>
								<li>
									Delivery Date : <span><?=$delivery_day ?></span>
								</li>							
								<?php } ?>
						
						<li>
							Pickup or Delivery: <span><?=$result_current_order[0]['pickup_delivery']==1?'Delivery':'Pickup'; ?></span>
						</li>						
					</ul>
				</div>
				<div class="order_listing_RtCol">
					<div class="order_listingInfo">
						<?=$db_common -> order_day_length(intval($result_current_order[0]['id'])) ?> days/week
					</div>
					<div class="order_listing_DtlsBtn">
						<a href="<?=$general_func -> site_url ?>order-details/<?=$result_current_order[0]['id'] ?>">Details</a>
					</div>
					<br class="clear" />
					<div class="order_listing_DtlsBtn" style="color: #a3c52c; font-weight: bold;">
						<?php
						$rs_program_length = $db->fetch_all_array("select name from discounts where id='" . intval($result_current_order[0]['program_length']). "' limit 1");
						echo $rs_program_length[0]['name'] ?>
					</div>
					
					
				</div>
				<div class="order_listing_rowBtnPnl">
					<p>
						Status : 
						<?php
						if($result_current_order[0]['status'] == 0)
						 echo '<span>Not yet started</span>';
						else if($result_current_order[0]['status'] == 1)
							 echo '<span style="color:#a3c52c">Active</span>';
						else if($result_current_order[0]['status'] == 2)
							 echo '<span style="color:#f88d19">On Hold</span>';
						else
							 echo '<span style="color:#ff5557">Cancelled</span>';											
												
						?>
						
					</p>
					<ul>
						<?php if($result_current_order[0]['status'] == 1){?>
						<li class="olIco2">							
                           <input type="text" id="datepicker">
                            <a style="cursor: pointer;" class="confirm_hold" id="alert_hold" title="Are you sure you want to hold order: FNF - A000<?=intval($result_current_order[0]['id']) ?>?">Hold</a>
						</li>							
						<?php  } ?>
						
						<li class="olIco4">
							<a target="_blank" href="<?=$general_func -> site_url ?>orders-print.php?id=<?=$result_current_order[0]['id'] ?>">Print</a>
						</li>
						
						<?php
						//******  1-select a meal plan/2-choose the questionnaire/3-customize you own ******/
						$order_type="";
						if($result_current_order[0]['order_type'] == 1){
							$order_type="modify-select-meal-plan/".$result_current_order[0]['id'];
						}else if($result_current_order[0]['order_type'] == 2){
							$order_type="modify-questionnaire-meal-plan/".$result_current_order[0]['id'];
						}else{
						 	$order_type="modify-customize-meal-plan/".$result_current_order[0]['id'];
						 }
						
						
						
						 if(($result_current_order[0]['status'] == 0 || $result_current_order[0]['status'] == 1) && count($result_next_week_order) == 0 ){ ?>				
						
						<li class="olIco1">
							<a href="<?=$general_func -> site_url.$order_type ?>">Modify</a>
						</li>
						
						
						<?php }  if($result_current_order[0]['status'] == 2){?>
						<li class="olIco2">
							<a style="cursor: pointer;" class="confirm_reactive" id="alert_reactive" title="Are you sure you want to reactive order: FNF - A000<?=intval($result_current_order[0]['id']) ?>?">Make Active</a>
						</li>							
						<?php }
						if(($result_current_order[0]['status'] == 0 || $result_current_order[0]['status'] == 1) && count($result_next_week_order) == 0 ){
								
							$minimum_order_weeks=mysql_result(mysql_query("select minimum_order_weeks from users where id='" . intval($_SESSION['user_id']) . "' limit 1"), 0,0);		
							$total_ordered_weeks=mysql_result(mysql_query("select count(*) as total from payment where user_id='" . intval($_SESSION['user_id']) . "' and order_status=1"),0,0);		
							if($total_ordered_weeks >= $minimum_order_weeks || (isset($_SESSION['admin_login_user_behalf']) && trim($_SESSION['admin_login_user_behalf']) == "yes")){
							 ?>	
						<li class="olIco3">
							<a style="cursor: pointer;" class="<?=$order_can_be_modified==1?'confirm_delete':'confirm_delete_next_week'?>" id="<?=$order_can_be_modified==1?'alert_delete':'alert_delete_next_week'?>" title="Are you sure you want to cancel your order: FNF - A000<?=intval($result_current_order[0]['id']) ?> <?=$order_can_be_modified==1?'':'from next week'?>?">Cancel</a>
						</li>
						<?php }
							} ?>
					</ul>
				</div>
				<br class="clear" />
			</div>
			<!-- row -->

		</div>
		<?php }
		if(count($result_next_week_order) == 1){ ?>
			
			<div class="order_listingPrvOdr">

			<!-- row -->
			<div class="order_listing_row" style="background: #ffffff;">
				<div class="order_listingNextweek">
					Next Week 
				</div>
				<div class="order_listing_col">
					<ul>
						<li>
							Order No : <span>FNF - A000<?=intval($result_next_week_order[0]['id']) ?></span>
						</li>
						
						<li>
							Order Amount : <strong>$<?=$result_next_week_order[0]['order_amount'] ?>  p/w (GST <?=$GST?>% included)</strong>
						</li>
					</ul>
				</div>
				<div class="order_listing_col">
					<ul>
						<li>
							Cut off Date : <span><?=$order_cutoff_day ?></span>
						</li>
						<li>
							Payment Date : <span><?=$payment_debit_day ?></span>
						</li>
						<?php if($result_next_week_order[0]['pickup_delivery']==1){?>
						<li>
							Delivery Date : <span><?=$delivery_day ?></span>
						</li>							
						<?php } ?>
						
						<li>
							Pickup or Delivery: <span><?=$result_next_week_order[0]['pickup_delivery']==1?'Delivery':'Pickup'; ?></span>
						</li>
					</ul>
				</div>
				<div class="order_listing_RtCol">
					<div class="order_listingInfo">
						<?=$db_common -> order_day_length(intval($result_next_week_order[0]['id'])) ?> days/week
					</div>
					<div class="order_listing_DtlsBtn">
						<a href="<?=$general_func -> site_url ?>order-details/<?=$result_next_week_order[0]['id'] ?>">Details</a>
					</div>
					<br class="clear" />
					<div class="order_listing_DtlsBtn" style="color: #a3c52c; font-weight: bold;">
						<?php
						$rs_program_length = $db->fetch_all_array("select name from discounts where id='" . intval($result_next_week_order[0]['program_length']). "' limit 1");
						echo $rs_program_length[0]['name'] ?>
					</div>
				</div>
				<div class="order_listing_rowBtnPnl">
					<p>Status : Next Week order</p>
					<ul>					
						
						<li class="olIco4">
							<a target="_blank" href="<?=$general_func -> site_url ?>orders-print.php?id=<?=$result_next_week_order[0]['id'] ?>">Print</a>
						</li>
						
						<?php
						//******  1-select a meal plan/2-choose the questionnaire/3-customize you own ******/
						$order_type="";
						if($result_next_week_order[0]['order_type'] == 1){
							$order_type="modify-select-meal-plan/".$result_next_week_order[0]['id'];
						}else if($result_next_week_order[0]['order_type'] == 2){
							$order_type="modify-questionnaire-meal-plan/".$result_next_week_order[0]['id'];
						}else{
						 	$order_type="modify-customize-meal-plan/".$result_next_week_order[0]['id'];
						 }
						 ?>				
						
						<li class="olIco1">
							<a href="<?=$general_func -> site_url.$order_type ?>">Modify</a>
						</li>	
						
					</ul>
				</div>
				<br class="clear" />
			</div>
			<!-- row -->

		</div>
			
			
		<?php }


		if($total_old_orders > 0){
		 ?>
		<div class="order_listingPrvOdr">
		<h4>Previous Order</h4>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<?php for($old=0; $old <$total_old_orders; $old++ ){ ?>
			<tr>
				<td align="left" valign="top">
					<div class="order_listing_row">
						<div class="order_listing_col">
							<ul>
								<li>	
									Order No : <span>FNF - A000<?=intval($result_old_order[$old]['id']) ?></span>
								</li>
								
								<li>
									Order Amount : <strong>$<?=$result_old_order[$old]['order_amount'] ?> p/w (GST <?=$GST?>% included)</strong>
								</li>
							</ul>
						</div>
						<div class="order_listing_col">
							<ul>
								<li>
									Cut off Date : <span><?=$order_cutoff_day ?></span>
								</li>
								<li>
									Payment Date : <span><?=$payment_debit_day ?></span>
								</li>
								<?php if($result_old_order[0]['pickup_delivery']==1){?>
								<li>
									Delivery Date : <span><?=$delivery_day ?></span>
								</li>							
								<?php } ?>								
								<li>
							Pickup or Delivery: <span><?=$result_old_order[0]['pickup_delivery']==1?'Delivery':'Pickup'; ?></span>
						</li>
							</ul>
						</div>
						<div class="order_listing_RtCol">
							<div class="order_listingInfo"><?=$db_common -> order_day_length(intval($result_old_order[$old]['id'])) ?> days/week</div>
							<div class="order_listing_DtlsBtn"><a href="<?=$general_func -> site_url ?>order-details/<?=$result_old_order[$old]['id'] ?>">Details</a></div>
							<br class="clear" />
					<div class="order_listing_DtlsBtn" style="color: #a3c52c; font-weight: bold;">
						<?php
						$rs_program_length = $db->fetch_all_array("select name from discounts where id='" . intval($result_old_order[0]['program_length']). "' limit 1");
						echo $rs_program_length[0]['name'] ?>
					</div>
						
						</div>
						<div class="order_listing_rowBtnPnl">
					<p>
						Status : 
						<?php
						if($result_old_order[$old]['status'] == 0)
						 echo '<span>Not yet started</span>';
						else if($result_old_order[$old]['status'] == 1)
							 echo '<span style="color:#a3c52c">Active</span>';
						else if($result_old_order[$old]['status'] == 2)
							 echo '<span style="color:#f88d19">On Hold</span>';
						else
							 echo '<span style="color:#ff5557">Cancelled</span>';
						?>
					</p>
					<ul>
						<li class="olIco4">
							<a target="_blank" href="<?=$general_func -> site_url ?>orders-print.php?id=<?=$result_old_order[$old]['id'] ?>">Print</a>
						</li>
					</ul>
				</div>
						<br class="clear" />
					</div>
				</td>
			</tr>
			<?php }	?>
		</table>

		</div>
		<?php 	} ?>

	</div>
</div>
<?php
include_once ("includes/footer.php");
?>