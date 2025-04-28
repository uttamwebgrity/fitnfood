<?php
include_once ("includes/header.php");
if (!isset($_SESSION['user_login_type']) || trim($_SESSION['user_login_type']) != "users" || !isset($_SESSION['user_login_type'])) {
	$_SESSION['user_message'] = "Sorry, you have no permission to access this page!";
	$general_func -> header_redirect($general_func -> site_url);
}

$order_id = intval($_REQUEST['order_id']);

$sql_current_order = "select o.id,program_length,pickup_delivery,order_type,order_amount,order_start_date,name,o.status,suburb_id,current_order from orders o  left join meal_plan_category p on o.meal_plan_category_id=p.id";
$sql_current_order .= " left join  users u on o.user_id=u.id";
$sql_current_order .= " where user_id='" . intval($_SESSION['user_id']) . "' and o.id='" . intval($order_id) . "' limit 1";
$result_current_order = $db -> fetch_all_array($sql_current_order);

$result_suburb_info = $db -> fetch_all_array("select suburb_name,suburb_state,delivery_cost,suburb_postcode,delivery_day,payment_debit_day,order_cutoff_day,order_cutoff_time from suburb where id=" . intval($result_current_order[0]['suburb_id']) . " limit 1");
$delivery_day = $general_func -> day_name($result_suburb_info[0]['delivery_day']);
$payment_debit_day = $general_func -> day_name($result_suburb_info[0]['payment_debit_day']);
$order_cutoff_day = $general_func -> day_name($result_suburb_info[0]['order_cutoff_day']) . " " . date("h:i A", strtotime($result_suburb_info[0]['order_cutoff_time']));
?>

<script type="text/javascript">
	$(document).ready(function() {

		$(".dayPnl1_new1 li").mouseenter(function() {
			$(this).find(".tip_box1").show();
		});

		$(".dayPnl1_new1 li").mouseleave(function() {
			$(this).find(".tip_box1").hide();
		});

		$(".close_pop").click(function() {
			$(this).parent().parent().find(".tip_box1").hide();
		});
	})
</script>

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
					<a href="my-account/">My Account &raquo;</a>
				</li>
				<li>
					<a href="order-listing/">Order Listing &raquo;</a>
				</li>
				<li>
					Order Details
				</li>
			</ul>
		</div>
		<div class="order_listing">
			<!-- row -->
			<div class="order_listing_row" style="background: #ffffff;">
				<div class="order_listing_col">
					<ul>
						<li>
							Order No : <span>FNF - A000<?=intval($result_current_order[0]['id']) ?></span>
						</li>		
						
						<?php if($result_current_order[0]['current_order'] == 1){ ?>
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
								?>
								</span>
						</li>
							
						<?php } ?>
						
						<li>
							Order Amount : <strong>$<?=$result_current_order[0]['order_amount'] ?> p/w (GST <?=$GST?>% included)</strong>
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

				</div>
				<div class="order_listing_rowBtnPnl" style="border-top: none; color: #555353;
font: 17px/27px 'open_sansregular'; padding-left: 30px;">
				
							Program Length : 							
								<?php
						$rs_program_length = $db->fetch_all_array("select name from discounts where id='" . intval($result_current_order[0]['program_length']). "' limit 1");
						echo $rs_program_length[0]['name'] ?>
								
				</div>	
				<div class="order_listing_rowBtnPnl">
					<p>
						Status :
						<?php
						if ($result_current_order[0]['status'] == 0)
							echo '<span>Not yet started</span>';
						else if ($result_current_order[0]['status'] == 1)
							echo '<span style="color:#a3c52c">Active</span>';
						else if ($result_current_order[0]['status'] == 2)
							echo '<span style="color:#f88d19">On Hold</span>';
						else if ($result_current_order[0]['status'] == 5)
							echo '<span style="color:#f88d19">Next Week Order</span>';	
						else
							echo '<span style="color:#ff5557">Cancelled</span>';
						?>
					</p>
					<ul>
						<li class="olIco4">
							<a target="_blank" href="<?=$general_func -> site_url ?>orders-print.php?id=<?=$result_current_order[0]['id'] ?>">Print</a>
						</li>

					</ul>
				</div>
				<br class="clear" />
			</div>
			<!-- row -->

		</div>
		<br class="clear" />
		<?php
		$no_of_days = 0;
		$meal_per_day = 0;
		$snack_per_day = 0;

		$sql_meals = "select which_day,meal_time,meal_size,meal_id,show_nutritional_price,m.name,details,photo_name,energy,calories,protein,fat_total,carbohydrates,carbs_veggies,with_or_without_sauce from order_meals d left join meals m on d.meal_id=m.id where d.order_id='" . $order_id . "' and type=1  order by which_day,meal_time ASC";
		$result_default_meals = $db -> fetch_all_array($sql_meals);
		$total_default_meals = count($result_default_meals);

		$default_meals = array();

		for ($i = 0; $i < $total_default_meals; $i++) {

			if ($result_default_meals[$i]['which_day'] > $no_of_days)
				$no_of_days = $result_default_meals[$i]['which_day'];

			if ($result_default_meals[$i]['meal_time'] > $meal_per_day)
				$meal_per_day = $result_default_meals[$i]['meal_time'];

			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_name'] = $result_default_meals[$i]['name'];
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_size'] = $result_default_meals[$i]['meal_size'];
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['details'] = $result_default_meals[$i]['details'];
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['photo_name'] = $result_default_meals[$i]['photo_name'];
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['energy'] = $result_default_meals[$i]['energy'];
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['calories'] = $result_default_meals[$i]['calories'];
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['protein'] = $result_default_meals[$i]['protein'];
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['fat_total'] = $result_default_meals[$i]['fat_total'];
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['carbohydrates'] = $result_default_meals[$i]['carbohydrates'];
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['carbs_veggies'] = $result_default_meals[$i]['carbs_veggies'];
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['with_or_without_sauce'] = $result_default_meals[$i]['with_or_without_sauce'];
			$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['show_nutritional_price'] = $result_default_meals[$i]['show_nutritional_price'];

		}

		$sql_snacks = "select which_day,meal_time,meal_size,m.name,details,photo_name from order_meals d left join snacks m on d.meal_id=m.id where d.order_id='" . $order_id . "' and type=2 order by which_day,meal_time ASC";
		$result_default_snacks = $db -> fetch_all_array($sql_snacks);
		$total_default_snacks = count($result_default_snacks);

		$default_snacks = array();

		for ($i = 0; $i < $total_default_snacks; $i++) {

			if ($result_default_snacks[$i]['meal_time'] > $snack_per_day)
				$snack_per_day = $result_default_snacks[$i]['meal_time'];

			$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['snack_name'] = $result_default_snacks[$i]['name'];
			$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['qty'] = $result_default_snacks[$i]['meal_size'];
			$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['details'] = $result_default_snacks[$i]['details'];
			$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['photo_name'] = $result_default_snacks[$i]['photo_name'];
		}
		?>
		<h5 class="orderDtlsHd">Order Details</h5>
		<div class="orderDtls">
			<?php for($day=1; $day <=intval($no_of_days); $day++){ ?>
			<div class="orderDtlsLst dayPnl1_new1">
				<div class="oDtlDate">
					<h6>Day
					<br />
					<span><?=$day ?></span></h6>
				</div>
				<ul>
					<?php
for($time=1; $time <= intval($meal_per_day); $time++ ){
					?>
					<li style="cursor: pointer;">
						<span>Meal <?=$time ?> :</span><?=$default_meals[$day][$time]['meal_name'] ?> <!-- tip box -->
						<div class="tip_box1">
							<div class="close_pop"></div>
							<div class="tip_angle"></div>
							<div class="tip_head">
								<?=$default_meals[$day][$time]['meal_name'] ?>
							</div>

							<!-- row -->
							<div class="tip_row">
								<div class="tip_column_container">
									<div class="tip_column">
										<?php if($default_meals[$day][$time]['show_nutritional_price'] == 1){
										?>
										<div class="tip_column_info_row">
											<div class="info_tab">
												Net Weight :
											</div>
											<div class="info_tab">
												<?=$default_meals[$day][$time]['meal_size']; ?>g
											</div>
										</div>
										<div class="tip_column_info_row">
										<div class="info_tab">Energy :</div>
										<div class="info_tab"><?=$db_common->nutritional_value($default_meals[$day][$time]['energy'],$default_meals[$day][$time]['meal_size']) ?> kcal</div>
										</div>
										<div class="tip_column_info_row">
										<div class="info_tab">Protein :</div>
										<div class="info_tab"><?=$db_common->nutritional_value($default_meals[$day][$time]['protein'],$default_meals[$day][$time]['meal_size']) ?>g</div>
										</div>
										<?php } ?>
										<div class="tip_column_info_row">
											<div class="info_tab">
												Carbs :
											</div>
											<div class="info_tab">
												<?php if(intval($default_meals[$day][$time]['carbs_veggies']) == 3 || intval($default_meals[$day][$time]['carbs_veggies']) == 1){
												?><img src="images/tip_yes.png" style="margin-top: 4px;" />
												<?php }else{ ?><img src="images/tip_no.png" style="margin-top: 4px;" />
												<?php } ?>
											</div>
										</div>
										<div class="tip_column_info_row">
											<div class="info_tab">
												Sauce :
											</div>
											<div class="info_tab">
												<?php if(intval($default_meals[$day][$time]['with_or_without_sauce']) == 1){
												?><img src="images/tip_yes.png" style="margin-top: 4px;" />
												<?php }else{ ?><img src="images/tip_no.png" style="margin-top: 4px;" />
												<?php } ?>
											</div>
										</div>
									</div>
									<div class="tip_column">
										<?php if($default_meals[$day][$time]['show_nutritional_price'] == 1){
										?>
										<div class="tip_column_info_row">
											<div class="info_tab">Calories :</div>
											<div class="info_tab"><?=$db_common->nutritional_value($default_meals[$day][$time]['calories'],$default_meals[$day][$time]['meal_size'])?>g</div>
										</div>
										<div class="tip_column_info_row">
											<div class="info_tab">
												Carbs :
											</div>
											<div class="info_tab">
												<?=$db_common->nutritional_value($default_meals[$day][$time]['carbohydrates'],$default_meals[$day][$time]['meal_size']) ?>g
											</div>
										</div>
										<div class="tip_column_info_row">
										<div class="info_tab">Total Fat :</div>
										<div class="info_tab"><?=$db_common->nutritional_value($default_meals[$day][$time]['fat_total'],$default_meals[$day][$time]['meal_size']) ?>g</div>
										</div>

										<?php } ?>
										<div class="tip_column_info_row">
											<div class="info_tab">
												Veggies :
											</div>
											<div class="info_tab">
												<?php if(intval($default_meals[$day][$time]['carbs_veggies']) == 3 || intval($default_meals[$day][$time]['carbs_veggies']) == 2){
												?><img src="images/tip_yes.png" style="margin-top: 5px;" />
												<?php }else{ ?><img src="images/tip_no.png" style="margin-top: 5px;" />
												<?php } ?>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- row -->

							<!-- row -->
							<div class="tip_row">
								<div class="tip_descrip">
									<p>
										<?php if(trim($default_meals[$day][$time]['photo_name']) != NULL){
										?><img class="lefted_img" align="left" src="meal_main/small/<?=trim($default_meals[$day][$time]['photo_name']) ?>" width="120">
										<?php } ?>

										<?=nl2br($default_meals[$day][$time]['details']) ?>
									</p>
								</div>
							</div>
							<!-- row -->

						</div>
						<!-- tip box -->
					</li>
					<?php }
						for($time=1; $time <= intval($snack_per_day); $time++ ){
					?>
					<li style="cursor: pointer;">
						<span>Snack <?=$time ?> :</span><?=$default_snacks[$day][$time]['snack_name'] ?> <!-- tip box -->
						<div class="tip_box1">
							<div class="close_pop"></div>
							<div class="tip_angle"></div>
							<div class="tip_head">
								<?=$default_snacks[$day][$time]['snack_name'] ?> <i> (Qty: <?=$default_snacks[$day][$time]['qty'] ?>)</i> 
							</div>					

							<!-- row -->
							<div class="tip_row">
								<div class="tip_descrip">
									<p>
										<?php if(trim($default_snacks[$day][$time]['photo_name']) != NULL){
										?><img class="lefted_img" align="left" src="snack_main/small/<?=trim($default_snacks[$day][$time]['photo_name']) ?>" width="120">
										<?php } ?>
										<?=nl2br($default_snacks[$day][$time]['details']) ?>
									</p>
								</div>
							</div>
							<!-- row -->

						</div>
						<!-- tip box -->
					</li>

					<?php  } ?>

				</ul>
			</div>
			<?php } ?>
		</div>
		
		<?php 
		$sql_payment="select order_id,order_amount,week_start_date,week_end_date,payment_date from  payment where order_id='" . $order_id . "' and order_status=1 order by payment_date DESC";
		$result_payment=$db->fetch_all_array($sql_payment);
		$total_payment=count($result_payment);
		if( $total_payment > 0){ ?>
		<br class="clear" />
		<h5 class="orderDtlsHd">Payment Details</h5>
		<?php for($p=0; $p <$total_payment; $p++){ ?>
		<div class="pmtHsty">
			<ul class="pmtHstyLft">
               	<li style="margin: 3px;"><span>Order No :</span>  FNF - A000<?=$result_payment[$p]['order_id']?></li>
                 <li style="margin: 3px;"><span>Order Amount :</span>  <b>$<?=$result_payment[$p]['order_amount']?></b> p/w (GST <?=$GST?>% included)</li>
            </ul>
            <ul class="pmtHstyRht">
              	<li style="margin: 3px;"><span>Payment Week :</span>  <?=date("jS M, Y ",strtotime($result_payment[$p]['week_start_date']))?> -- <?=date("jS M, Y ",strtotime($result_payment[$p]['week_end_date']))?></li>
				<li style="margin: 3px;"><span>Payment On :</span>  <?=date("jS M, Y ",strtotime($result_payment[$p]['payment_date']))?></li>
              </ul>
		</div>				
		<?php } 
		} ?>
	</div>
</div>
<?php
include_once ("includes/footer.php");
?>