<?php
$path_depth = "../../";

include_once ("../head.htm");
$link_name = "Welcome";

if(isset($_GET['order_status'])){
	$_SESSION['search_order_status']=$_GET['order_status'];
}



?>
<script language="JavaScript">
		function validate_search(){
	if(!validate_text(document.frmsearch.cd,1,"Enter user name.")){
	return false;
	}
	}

	
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td align="left" valign="top">
		<table border="0" align="left" cellpadding="0" cellspacing="0">
			<tr>
				<td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
				<td align="left" valign="middle" class="body_tab-middilebg">Orders</td>
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
								<table width="501" border="0" align="center" cellpadding="0" cellspacing="0">
									<form name="frmsearch"  method="post" action="<?=$_SERVER['PHP_SELF'] ?>" onsubmit="return validate_search();">
										<input type="hidden" name="enter" value="3" />
										<tr>
											<td width="123" align="right" valign="middle" class="content_employee" style="padding-right: 5px;">User Name:</td>
											<td width="240" align="left" valign="middle">
											<input type="text" name="cd"  value="<?=$_REQUEST['cd'] ?>" autocomplete="OFF" size="35" class="inputbox_employee-listing" />
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

			$recperpage = $general_func -> admin_recoed_per_page;

			$order_by = "";
			$display_oder_type = "ASC";

			if (isset($_REQUEST['display_oder']) && trim($_REQUEST['display_oder']) != NULL) {
				if (trim($_REQUEST['display_oder']) == "status") {//***********status
					if (trim($_REQUEST['display_oder_type']) == "ASC") {
						$display_oder_type = "DESC";
						$order_by .= "o.status + 0 ASC";
					} else {
						$display_oder_type = "ASC";
						$order_by .= "o.status + 0 DESC";
					}
				} else if (trim($_REQUEST['display_oder']) == "name") {//***********name
					if (trim($_REQUEST['display_oder_type']) == "ASC") {
						$display_oder_type = "DESC";
						$order_by .= "fname  ASC";
					} else {
						$display_oder_type = "ASC";
						$order_by .= "fname DESC";
					}
				} else if (trim($_REQUEST['display_oder']) == "amount") {//***********amount
					if (trim($_REQUEST['display_oder_type']) == "ASC") {
						$display_oder_type = "DESC";
						$order_by .= "order_amount  ASC";
					} else {
						$display_oder_type = "ASC";
						$order_by .= "order_amount DESC";
					}
				} else if (trim($_REQUEST['display_oder']) == "startdate") {//***********startdate
					if (trim($_REQUEST['display_oder_type']) == "ASC") {
						$display_oder_type = "DESC";
						$order_by .= "order_start_date  ASC";
					} else {
						$display_oder_type = "ASC";
						$order_by .= "order_start_date DESC";
					}
				} else if (trim($_REQUEST['display_oder']) == "ordertype") {//***********ordertype
					if (trim($_REQUEST['display_oder_type']) == "ASC") {
						$display_oder_type = "DESC";
						$order_by .= "order_type  ASC";
					} else {
						$display_oder_type = "ASC";
						$order_by .= "order_type DESC";
					}
				} else if (trim($_REQUEST['display_oder']) == "plan_category") {//***********status
					if (trim($_REQUEST['display_oder_type']) == "ASC") {
						$display_oder_type = "DESC";
						$order_by .= "name  ASC";
					} else {
						$display_oder_type = "ASC";
						$order_by .= "name  DESC";
					}
				} else {
					$order_by .= "order_start_date DESC";
				}

			} else {
				$order_by .= "order_start_date DESC";
			}
			if(intval($_SESSION['search_order_status']) == 1){
				$query = " where (o.status=0 or o.status=1)";
			}else{
				$query = " where o.status=" . intval($_SESSION['search_order_status']);
			}
				

			if (isset($_REQUEST['key']) && trim($_REQUEST['key']) != NULL)
				$query .= " and fname LIKE '" . trim($_REQUEST['key']) . "%'";
			else if (isset($_REQUEST['enter']) && (int)$_REQUEST['enter'] == 3)
				$query .= " and fname LIKE '" . trim($_REQUEST['cd']) . "%'";

			$sql = "select o.id,suburb_id,order_type,order_amount,order_start_date,name,CONCAT(fname,' ',lname) as user_name,email_address,o.status from orders o  left join meal_plan_category p on o.meal_plan_category_id=p.id";
			$sql .= " left join users u on o.user_id=u.id";
			$sql .= " $query order by $order_by";

			//-	----------------------------------/Pagination------------------------------

			if (isset($_GET['in_page']) && $_GET['in_page'] != "")
				$page = $_GET['in_page'];
			else
				$page = 1;

			$total_count = $db -> num_rows($sql);
			$sql = $sql . " limit " . (($page - 1) * $recperpage) . ", $recperpage";

			if ($page > 1) {
				$url_prev = stristr($url, "&in_page=" . $page) == FALSE ? $url . "&page=" . ($page - 1) : str_replace("&in_page=" . $page, "&in_page=" . ($page - 1), $url);
				$prev = "&nbsp;<a href='$url_prev' class='nav'>Prev</a>";
			} else
				$prev = "&nbsp;Prev";

			if ((($page) * $recperpage) < $total_count) {
				$url_next = stristr($url, "&in_page=" . $page) == FALSE ? $url . "&in_page=" . ($page + 1) : str_replace("&in_page=" . $page, "&in_page=" . ($page + 1), $url);
				$next = "&nbsp;<a href='$url_next' class='nav'>Next</a>";
			} else
				$next = "&nbsp;Next";

			$page_temp = (($page) * $recperpage);
			$page_temp = $page_temp < $total_count ? $page_temp : $total_count;
			$showing = " Showing " . (($page - 1) * $recperpage + 1) . " - " . $page_temp . " of " . $total_count . " | ";

			//-----------------------------------/Pagination------------------------------
			//*************************************************************************************************//
			$result = $db -> fetch_all_array($sql);
			//*******************************************************************************************************************//
			?>
			<tr>
				<td align="left" valign="top">
				<table width="1000" align="center" border="0"
				cellpadding="5" cellspacing="1">
					<tr>
						<td  class="text_numbering" colspan="2"><?=$general_func->order_status_heading($_SESSION['search_order_status'])?></td>
						
						<td class="text_numbering" colspan="6" align="right"><?=$total_count ?>
						order(s) found.</td>
					</tr>
					<tr>
						<td width="150"  align="center" valign="middle" class="table_heading"><a href="orders/orders.php?display_oder=name&display_oder_type=<?=$display_oder_type ?>" class="header_link">User Name</a></td>
						<td width="150"  align="center" valign="middle" class="table_heading">Email Address</td>
						<td width="100"  align="center" valign="middle" class="table_heading"><a href="orders/orders.php?display_oder=plan_category&display_oder_type=<?=$display_oder_type ?>" class="header_link">Plan Category</a></td>
						<td width="150"  align="center" valign="middle" class="table_heading"><a href="orders/orders.php?display_oder=ordertype&display_oder_type=<?=$display_oder_type ?>" class="header_link">Order Type</a></td>
						<?php if(intval($_SESSION['search_order_status']) == 1){ ?>
						<td width="150"  align="center" valign="middle" class="table_heading">Start Date</td>
						<?php } ?>
						<td width="100"  align="center" valign="middle" class="table_heading"><a href="orders/orders.php?display_oder=amount&display_oder_type=<?=$display_oder_type ?>" class="header_link">Order Amount</a></td>
						<td width="100"  align="center" valign="middle" class="table_heading"><a href="orders/orders.php?display_oder=status&display_oder_type=<?=$display_oder_type ?>" class="header_link">Status</a></td>
						<td width="100"  align="center" valign="middle" class="table_heading">Action</td>
					</tr>
					<?php if(count($result) == 0){
					?>
					<tr>
						<td colspan="8" align="center" bgcolor="#f5f7fa" valign="middle" height="50" class="message_error">Sorry, no orders  found!</td>
					</tr>
					<?php }else{
						for($j=0;$j<count($result);$j++){
					?>
<tr bgcolor="<?=$j % 2 == 0 ? $general_func -> color2 : $general_func -> color1; ?>">
<td align="left" valign="middle" class="table_content-blue"><?=$result[$j]['user_name'] ?></td>
					<td align="left" valign="middle" class="table_content-blue"><?=$result[$j]['email_address'] ?> </td>
					<td align="left" valign="middle" class="table_content-blue"><?=$result[$j]['name'] ?></td>
					<td align="left" valign="middle" class="table_content-blue"><?php					
					if($result[$j]['order_type'] == 1){
						echo "Meal Plan Selected";							
					}else if($result[$j]['order_type'] == 2){
						echo "Filled the Questionnaire";	
					}else{
						echo "Customized Meal Plan";	
						
					}?></td>
					<?php if(intval($_SESSION['search_order_status']) == 1){ ?>
		
					<td align="left" valign="middle" class="table_content-blue"><?php
					
					$result_suburb_info = $db -> fetch_all_array("select delivery_day from suburb where id=" . intval($result[$j]['suburb_id']) . " limit 1");
					$delivery_day = strtolower($general_func -> day_name($result_suburb_info[0]['delivery_day']));
					
					
					if(strtotime($result[$j]['order_start_date']) >= strtotime($today_date)){
						echo date("jS M, Y", strtotime($result[$j]['order_start_date']));	
					}else if($result_suburb_info[0]['delivery_day'] == date("N")){
						echo date("jS M, Y", strtotime($today_date));
					}else{
						echo date("jS M, Y",strtotime('next '. $delivery_day));
					}
					
					 ?></td>
					<?php } ?>
					<td align="left" valign="middle" class="table_content-blue">$<?=$result[$j]['order_amount'] ?> p/w (GST <?=$GST?>% included)</td>

					<td  align="center" valign="middle" class="table_content-blue"><?=$general_func->order_status($result[$j]['status'])?></td>

					<td  align="center" valign="middle" class="table_content-blue">

					<a target="_blank" href="<?=$general_func -> admin_url ?>orders/orders-print.php?id=<?=$result[$j]['id'] ?>"><img src="images/printer_ico.png"  title="PRINT" alt="PRINT" /></a>



					&nbsp;&nbsp; <img src="images/view-details.png" onclick="location.href='<?=$general_func -> admin_url ?>orders/orders-view.php?id=<?=$result[$j]['id'] ?>&action=VIEW&return_url=<?=urlencode($url) ?>'" style="cursor:pointer;"  title="VIEW" alt="VIEW" />

					

					</td>
					</tr>
					<?php }
						}
					?>
					<tr>
					<td colspan="9" align="center" valign="middle" height="4"></td>
					</tr>
					<tr>
					<td colspan="9" align="center" valign="middle" height="30" class="table_content-blue"><?php
					if ($total_count>$recperpage) {
					?>
					<table width="795" height="20" border="0"  cellpadding="0" cellspacing="0">
					<tr>
					<td width="295" align="left" valign="bottom" class="htext">&nbsp;Jump to page
					<select name="in_page" style="width:45px;" onChange="javascript:location.href='<?php echo str_replace("&in_page=" . $page, "", $url); ?>&in_page='+this.value;">
					<?php for($m=1; $m<=ceil($total_count/$recperpage); $m++) {?>
					<option value="<?php echo $m; ?>" <?php echo $page == $m ? 'selected' : ''; ?>><?php echo $m; ?></option>
					<?php } ?>
					</select>
					of <?php echo ceil($total_count / $recperpage); ?> </td>
					<td width="467" align="right" valign="bottom" class="htext"><?php echo " " . $showing . " " . $prev . " " . $next . " &nbsp;"; ?></td>
					</tr>
					</table>
					<!-- / show category -->
					<?php  } ?></td>
					</tr>
					<tr>
					<td colspan="9" align="center" valign="middle" height="30" class="table_content-blue"></td>
					</tr>
				</table></td>
			</tr>
		</table></td>
	</tr>
</table>
<?php
include ("../foot.htm");
?>
