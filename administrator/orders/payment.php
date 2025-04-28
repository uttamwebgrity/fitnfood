<?php
$path_depth = "../../";

include_once ("../head.htm");
$link_name = "Welcome";

?>
 <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
 <script>
$(function() {
	$( "#datepicker" ).datepicker();
	$( "#datepicker1" ).datepicker();
	$( "#datepicker" ).datepicker( "option", "dateFormat", "dd/mm/yy" );
	$( "#datepicker1" ).datepicker( "option", "dateFormat",  "dd/mm/yy" );
});
</script>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td align="left" valign="top">
		<table border="0" align="left" cellpadding="0" cellspacing="0">
			<tr>
				<td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
				<td align="left" valign="middle" class="body_tab-middilebg">All Week Order/Payments</td>
				<td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
			</tr>
		</table></td>
	</tr>
	<tr>
		<td align="left" valign="top" class="body_whitebg">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td align="left" valign="top">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td align="left" valign="top"><img src="images/spacer.gif" alt="" width="14" height="14" /></td>
					</tr>
					<?php if(isset($_SESSION['msg']) && trim($_SESSION['msg']) != NULL){?>
					<tr>
						<td class="message_error" align="center"><?=$_SESSION['msg'];
						$_SESSION['msg'] = "";
						?></td>
					</tr>
					<tr>
						<td  class="body_content-form" height="10"></td>
					</tr>
					<?php  } ?>
					<tr>
						<td align="left" valign="top">
						<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
							<tr>
								<td align="center" valign="top">
								<table width="601" border="0" align="center" cellpadding="4" cellspacing="0">
									<form name="frmsearch"  method="post" action="<?=$_SERVER['PHP_SELF'] ?>" onsubmit="return validate_search();">
										<input type="hidden" name="enter" value="3" />
										<tr>
											<td width="100" align="right" valign="middle" class="content_employee" style="padding-right: 5px;">User Name:</td>
											<td width="400" align="left" valign="middle">
											<input type="text" name="cd"  value="<?=$_REQUEST['cd'] ?>" autocomplete="OFF" size="35" class="inputbox_employee-listing" />
											</td>
											<td width="100" align="left" valign="middle">
											</td>
										</tr>
										<tr>
											<td align="right" valign="middle" class="content_employee" style="padding-right: 5px;">Date From:  </td>
											<td  align="left" valign="middle">
											<input type="text" id="datepicker" name="date_start" value="<?=$_POST['date_start']?>" class="inputbox_employee-listing">
											&nbsp;&nbsp;&nbsp;&nbsp; To
											<input type="text" id="datepicker1" name="date_end" value="<?=$_POST['date_end']?>" class="inputbox_employee-listing">
											</td>
											<td width="138" align="left" valign="middle">
											<table border="0" align="left" cellpadding="0" cellspacing="0">
												<tr>
													<td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
													<td align="left" valign="middle" class="body_tab-middilebg">
													<input name="button" type="submit" class="submit1" value="Search" />
													</td>
													<td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
												</tr>
											</table></td>
										</tr>
									</form>
								</table>
								<p style="text-align:center;">
									<font class="text_numbering"> <?=$general_func -> A_to_Z($_SERVER['PHP_SELF']) ?> </font>
								</p></td>
							</tr>
						</table></td>
					</tr>
				</table></td>
			</tr>
			<?php
			//**************************************************************************************//
			$url = $_SERVER['PHP_SELF'] . "?" . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : 'cc=cc');

			//$recperpage = $general_func -> admin_recoed_per_page;

			$order_by = "";
			$display_oder_type = "ASC";

			if (isset($_REQUEST['display_oder']) && trim($_REQUEST['display_oder']) != NULL) {
				if (trim($_REQUEST['display_oder']) == "order_amount") {//***********status
					if (trim($_REQUEST['display_oder_type']) == "ASC") {
						$display_oder_type = "DESC";
						$order_by .= "order_amount ASC";
					} else {
						$display_oder_type = "ASC";
						$order_by .= "order_amount DESC";
					}
				} else if (trim($_REQUEST['display_oder']) == "week_start_date") {//***********name
					if (trim($_REQUEST['display_oder_type']) == "ASC") {
						$display_oder_type = "DESC";
						$order_by .= "week_start_date  ASC";
					} else {
						$display_oder_type = "ASC";
						$order_by .= "week_start_date DESC";
					}
				} else if (trim($_REQUEST['display_oder']) == "payment_date") {//***********amount
					if (trim($_REQUEST['display_oder_type']) == "ASC") {
						$display_oder_type = "DESC";
						$order_by .= "payment_date  ASC";
					} else {
						$display_oder_type = "ASC";
						$order_by .= "payment_date DESC";
					}
				} else if (trim($_REQUEST['display_oder']) == "order_id") {//***********startdate
					if (trim($_REQUEST['display_oder_type']) == "ASC") {
						$display_oder_type = "DESC";
						$order_by .= "order_id  ASC";
					} else {
						$display_oder_type = "ASC";
						$order_by .= "order_id DESC";
					}
				} else if (trim($_REQUEST['display_oder']) == "user_name") {//***********ordertype
					if (trim($_REQUEST['display_oder_type']) == "ASC") {
						$display_oder_type = "DESC";
						$order_by .= "user_name  ASC";
					} else {
						$display_oder_type = "ASC";
						$order_by .= "user_name DESC";
					}
				} else if (trim($_REQUEST['display_oder']) == "email_address") {//***********status
					if (trim($_REQUEST['display_oder_type']) == "ASC") {
						$display_oder_type = "DESC";
						$order_by .= "email_address  ASC";
					} else {
						$display_oder_type = "ASC";
						$order_by .= "email_address  DESC";
					}
				} else {
					$order_by .= "order_status ASC";
				}

			} else {
				$order_by .= " CAST(order_status as UNSIGNED)";
			}
			
						

			$query = "where 1";

			if (isset($_REQUEST['key']) && trim($_REQUEST['key']) != NULL){
				$query .= " and fname LIKE '" . trim($_REQUEST['key']) . "%'";
			}else if (isset($_REQUEST['enter']) && (int)$_REQUEST['enter'] == 3){
				$query .= " and fname LIKE '" . trim($_REQUEST['cd']) . "%'";
				
				if(trim($_POST['date_start']) != NULL ){
					list($dd,$mm,$yy)=@explode("/",trim($_POST['date_start']));
					$date_start=$yy."-".$mm."-".$dd;
					$query .= " and payment_date >= '" . $date_start . "'";
				}
				
				if(trim($_POST['date_start']) != NULL  && trim($_POST['date_end']) != NULL ){
					list($dd,$mm,$yy)=@explode("/",trim($_POST['date_end']));
					$date_end=$yy."-".$mm."-".$dd;
					
					if(strtotime($date_end)>= $date_start )
						$query .= " and payment_date <= '" . $date_end . "'";
				}
			}else if(isset($_REQUEST['filter']) && trim($_REQUEST['filter']) != NULL){
				$query .= " and order_status = " . (intval($_REQUEST['filter']) - 1) . "";
				
			}
			//print_r ($_REQUEST);
			//*************************************************************************************************//
			$sql = "select order_status,order_amount,training_cost,week_start_date,week_end_date,payment_date,order_id,CONCAT(fname,' ',lname) as user_name,email_address from payment p left join users u on p.user_id =u.id";
			$sql .= " $query order by $order_by";
		
			$result = $db -> fetch_all_array($sql);
			//*******************************************************************************************************************//
			
			$total_ordered_amount=0;
			$total_training_amount=0;			
			?>
			<tr>
				<td align="left" valign="top">
				<table width="1000" align="center" border="0"
				cellpadding="5" cellspacing="1">
					<tr>
						<td  class="text_numbering" colspan="4" >Filter by: 
							<select name="search_by" class="inputbox_select"  onchange="location.href='<?=$_SERVER['PHP_SELF'] ?>?filter='+ this.value" style="width: 180px; padding: 2px 0px;">
								<option value="" <?=$_REQUEST['filter']==""?'selected="selected"':'';?>>All Orders</option>
		            				<option value="2" <?=$_REQUEST['filter']==2?'selected="selected"':'';?>>Successful Payment</option>
		            				<option value="3" <?=$_REQUEST['filter']==3?'selected="selected"':'';?>>Failed Payment</option>
		            				<option value="1" <?=$_REQUEST['filter']==1?'selected="selected"':'';?>>Not yet Processed</option>								
		            
	            					</select>
							</td>
						
						<td class="text_numbering" colspan="4" align="right"><?=count($result) ?>
						order(s) found.</td>
					</tr>
					<tr>
						<td width="70"  align="center" valign="middle" class="table_heading"><a href="orders/payment.php?display_oder=order_id&display_oder_type=<?=$display_oder_type ?>" class="header_link">Order No.</a></td>
						<td width="170"  align="center" valign="middle" class="table_heading"><a href="orders/payment.php?display_oder=user_name&display_oder_type=<?=$display_oder_type ?>" class="header_link">Customer Name</a></td>
						<td width="160"  align="center" valign="middle" class="table_heading"><a href="orders/payment.php?display_oder=email_address&display_oder_type=<?=$display_oder_type ?>" class="header_link">Customer Email</a></td>
						<td width="230"  align="center" valign="middle" class="table_heading"><a href="orders/payment.php?display_oder=week_start_date&display_oder_type=<?=$display_oder_type ?>" class="header_link">Order Week</a></td>
						<td width="130"  align="center" valign="middle" class="table_heading"><a href="orders/payment.php?display_oder=payment_date&display_oder_type=<?=$display_oder_type ?>" class="header_link">Payment Date</a></td>
						<td width="80"  align="center" valign="middle" class="table_heading"><a href="orders/payment.php?display_oder=order_amount&display_oder_type=<?=$display_oder_type ?>" class="header_link">Amount</a></td>
						<td width="60"  align="center" valign="middle" class="table_heading">Status</td>
						<td width="60"  align="center" valign="middle" class="table_heading">Action</td>

						
					</tr>
					<?php if(count($result) == 0){
					?>
					<tr>
						<td colspan="8" align="center" bgcolor="#f5f7fa" valign="middle" height="50" class="message_error">Sorry, no orders  found!</td>
					</tr>
					<?php }else{
						for($j=0;$j<count($result);$j++){
							$status="";
					?>
<tr style="background-color: <?php
if($result[$j]['order_status'] == 0){
	echo "#c8fce5;";
	$status="Not yet Processed";
}else if($result[$j]['order_status'] == 2){
	echo "#fcc8d0;";	
	$status="Failed";
}else{
	echo $j % 2 == 0 ? $general_func -> color2 : $general_func -> color1;
	$status="Successful";
	
	$total_ordered_amount += $result[$j]['order_amount'];
	$total_training_amount += $result[$j]['training_cost'];
	
} ?>" >
<td align="left" valign="middle" class="table_content-blue">FNF - A000<?=$result[$j]['order_id'] ?></td>
					<td align="left" valign="middle" class="table_content-blue"><?=$result[$j]['user_name'] ?> </td>
					<td align="left" valign="middle" class="table_content-blue"><?=$result[$j]['email_address'] ?></td>
					<td align="center" valign="middle" class="table_content-blue"><?=date("jS M, Y",strtotime($result[$j]['week_start_date'])) ?> - <?=date("jS M, Y",strtotime($result[$j]['week_end_date'])) ?></td>

					<td align="center" valign="middle" class="table_content-blue"><?=date("jS M, Y",strtotime($result[$j]['payment_date'])) ?></td>
					<td align="center" valign="middle" class="table_content-blue">$<?=$result[$j]['order_amount'] ?></td>
					<td align="center" valign="middle" class="table_content-blue"><?=$status ?></td>
					<td  align="center" valign="middle" class="table_content-blue">
					<a target="_blank" href="<?=$general_func -> admin_url ?>orders/orders-print.php?id=<?=$result[$j]['order_id'] ?>"><img src="images/printer_ico.png"  title="PRINT" alt="PRINT" /></a>
					</td>
					
					</tr>
					<?php }
						}
					?>
					<tr>
					<td colspan="8" align="left" valign="middle" height="4" style="line-height: 20px; font-weight: bold;">
						Total Ordered: $<?php echo number_format($total_ordered_amount,2)?><br/>
						Total Training Cost: $<?php echo number_format($total_training_amount,2)?><br/>
						Total Income: $<?php echo number_format(($total_ordered_amount - $total_training_amount),2)?>
						
					</td>
					</tr>
					
					<tr>
					<td colspan="8" align="center" valign="middle" height="30" class="table_content-blue"></td>
					</tr>
				</table></td>
			</tr>
		</table></td>
	</tr>
</table>
<?php
include ("../foot.htm");
?>
