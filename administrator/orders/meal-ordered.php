<?php
$path_depth = "../../";

include_once ("../head.htm");
$link_name = "Welcome";
$query="";
			if (isset($_REQUEST['enter']) && (int)$_REQUEST['enter'] == 3){
				if(trim($_POST['date_start']) != NULL ){
					list($dd,$mm,$yy)=@explode("/",trim($_POST['date_start']));
					$date_start=$yy."-".$mm."-".$dd;
					$query .= " and DATE(payment_date) >= '" . $date_start . "'";
				}
				
				if(trim($_POST['date_start']) != NULL  && trim($_POST['date_end']) != NULL ){
					list($dd,$mm,$yy)=@explode("/",trim($_POST['date_end']));
					$date_end=$yy."-".$mm."-".$dd;
					
					if(strtotime($date_end)>= $date_start )
						$query .= " and DATE(payment_date) <= '" . $date_end . "'";
				}
			}

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
				<td align="left" valign="middle" class="body_tab-middilebg">All Meals Ordered
					<?php
					if(trim($query) != NULL){
						echo "From ". $_POST['date_start'];
						
						if(trim($_POST['date_end']) != NULL)
							echo " -  ". $_POST['date_end'];
					}
					?>
					
					</td>
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
											<td align="right" valign="middle" class="content_employee" style="padding-right: 5px;">Date From:  </td>
											<td  align="left" valign="middle">
											<input type="text" id="datepicker" name="date_start" class="inputbox_employee-listing">
											&nbsp;&nbsp;&nbsp;&nbsp; To
											<input type="text" id="datepicker1" name="date_end" class="inputbox_employee-listing">
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
								</td>
							</tr>
						</table></td>
					</tr>
				</table></td>
			</tr>
			<?php
			
			
			
			//echo "select order_id from payment where  order_status=1 $query  order by order_id + 0 ASC";
			$result_payment=$db->fetch_all_array("select order_id from payment where  order_status=1 $query  order by order_id + 0 ASC");	
			$total_payment=count($result_payment);
			
			$meals_array=array();
			$snacks_array=array();
			
			for($p=0; $p < $total_payment; $p++ ){
				//**********************  Meals ordered *********************************************//	
				$result_meals=$db->fetch_all_array("select meal_id,meal_size,name,count(*) as total from order_meals o left join meals m on o.meal_id=m.id where order_id='" .$result_payment[$p]['order_id'] . "' and  type=1 GROUP BY meal_id,meal_size  order by name ASC");
				$total_meals=count($result_meals);
				
				for($m=0; $m < $total_meals; $m++ ){		
					if(isset($meals_array[$result_meals[$m]['meal_id']][$result_meals[$m]['meal_size']])){
						$meals_array[$result_meals[$m]['meal_id']][$result_meals[$m]['meal_size']]['qty'] += $result_meals[$m]['total'];			
					}else{			
						$meals_array[$result_meals[$m]['meal_id']][$result_meals[$m]['meal_size']]['qty']=$result_meals[$m]['total'];
						$meals_array[$result_meals[$m]['meal_id']][$result_meals[$m]['meal_size']]['name'] = $result_meals[$m]['name'];	
					}
				}
				//**********************  Snacks ordered *********************************************//	
				
				$result_snacks=$db->fetch_all_array("select meal_id,name,SUM(meal_size) as total from order_meals o left join snacks s on o.meal_id=s.id where order_id='" .$result_payment[$p]['order_id'] . "' and  type=2 GROUP BY meal_id  order by name ASC");
				$total_snacks=count($result_snacks);
				
				for($s=0; $s < $total_snacks; $s++ ){		
					if(isset($snacks_array[$result_snacks[$s]['meal_id']])){
						$snacks_array[$result_snacks[$s]['meal_id']]['qty'] += $result_snacks[$s]['total'];			
					}else{			
						$snacks_array[$result_snacks[$s]['meal_id']]['name'] = $result_snacks[$s]['name'];	
						$snacks_array[$result_snacks[$s]['meal_id']]['qty'] = $result_snacks[$s]['total'];	
					}
				}
				//************************************************************************************//
			}
			$total_meals_array=count($meals_array);
			$total_snacks_array=count($snacks_array);			
			?>
			<tr>
				<td align="left" valign="top">
				<table width="600" align="center" border="0" cellpadding="8" cellspacing="1">
					<tr>						
						<td class="text_numbering" colspan="3" align="right">&nbsp;</td>
					</tr>
					<tr>
						<td width="350"  align="center" valign="middle" class="table_heading">Meal Name</td>
						<td width="150"  align="center" valign="middle" class="table_heading">Size</td>
						<td width="100"  align="center" valign="middle" class="table_heading">Qty</td>
					</tr>
					<?php if($total_meals_array == 0){
					?>
					 
					<tr>
						<td colspan="3" align="center" bgcolor="#f5f7fa" valign="middle" height="50" class="message_error">Sorry, no meals has been ordered!</td>
					</tr>
					<?php }else{
						if($total_meals_array > 0){
							$j=0;
							foreach($meals_array as $key => $value ){		
								foreach($value as $qty => $qty_value ){ ?>
								<tr bgcolor="<?=$j++%2==0?$general_func->color2:$general_func->color1;?>">
									<td align="left" valign="middle" class="table_content-blue"><?=$value[$qty]['name']?></td>
									<td align="center" valign="middle" class="table_content-blue"><?=$qty?>g </td>
									<td align="center" valign="middle" class="table_content-blue"><?=$value[$qty]['qty']?></td>
								</tr>							
							<?php }
							}		
					 	}
					}
					?>
					
				</table>
				<br/>
				<table width="500" align="center" border="0" cellpadding="8" cellspacing="1">
					<tr>						
						<td class="text_numbering" colspan="3" align="right">&nbsp;</td>
					</tr>
					<tr>
						<td width="400"  align="left" valign="middle" class="table_heading">Snack Name</td>						
						<td width="100"  align="center" valign="middle" class="table_heading">Qty</td>
					</tr>
					<?php if($total_snacks_array == 0){ ?>					 
					<tr>
						<td colspan="2" align="center" bgcolor="#f5f7fa" valign="middle" height="50" class="message_error">Sorry, no snacks has been ordered!</td>
					</tr>
					<?php }else{
						if($total_snacks_array > 0){
							$j=0;
							foreach($snacks_array as $key => $value ){ ?>
								<tr bgcolor="<?=$j++%2==0?$general_func->color2:$general_func->color1;?>">									
									<td align="left" valign="middle" class="table_content-blue"><?=$value['name']?></td>
									<td align="center" valign="middle" class="table_content-blue"><?=$value['qty']?></td>
								</tr>							
							<?php
							}		
					 	}
					}
					?>
					
				</table><br/><br/>
				
				</td>
			</tr>
		</table></td>
	</tr>
</table>
<?php
include ("../foot.htm");
?>
