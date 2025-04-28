<?php
$path_depth = "../../";

include_once ("../head.htm");
$link_name = "Welcome";


$content_tab=array();			
$content_tab[0]="Meal Name";
$content_tab[1]="100g";
$content_tab[2]="150g";
$content_tab[3]="200g";

$header="";

for ($i =0; $i < count($content_tab); $i++){
	$header .= $content_tab[$i] . "\t";
}

$data="";
$data_snack="";

$_SESSION['print_header']=$header;
$_SESSION['print_snack_header']="\n\n\n Snack Name \t Qty \t";



$_SESSION['print_name']="current_week_ordered_meals.xls";

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
				<td align="left" valign="middle" class="body_tab-middilebg">Current Week Ordered Meals
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
					
				</table></td>
			</tr>
			<?php
			
			$first_date_of_the_search_week = date("Y-m-d",strtotime('monday this week'));	
			$last_date_of_the_search_week =date("Y-m-d",strtotime('sunday this week'));

			$query = "and week_start_date ='" . $first_date_of_the_search_week . "' and week_end_date='" . $last_date_of_the_search_week . "' ";
					
			
			$result_payment=$db->fetch_all_array("select order_id from payment where  1 $query  order by order_id + 0 ASC");	
			$total_payment=count($result_payment);
			
			$meals_array=array();
			$snacks_array=array();
			
			for($p=0; $p < $total_payment; $p++ ){
				//**********************  Meals ordered *********************************************//	
				
				$result_meals=$db->fetch_all_array("select meal_id,meal_size,name,count(*) as total from order_meals o left join meals m on o.meal_id=m.id where order_id='" .$result_payment[$p]['order_id'] . "' and  type=1 GROUP BY meal_id,meal_size  order by name ASC");
				$total_meals=count($result_meals);
				
				for($m=0; $m < $total_meals; $m++ ){
					
					if(!isset($meals_array[$result_meals[$m]['name']])){//**********  new meal array
						$meals_array[$result_meals[$m]['name']]['100']=0;
						$meals_array[$result_meals[$m]['name']]['150']=0;
						$meals_array[$result_meals[$m]['name']]['200']=0;
					}
									
					$meals_array[$result_meals[$m]['name']][intval($result_meals[$m]['meal_size'])] += $result_meals[$m]['total'];			
					
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
			
			//print_r ($meals_array);
			
						
			?>
			<tr>
				<td align="left" valign="top">
				<table width="650" align="center" border="0" cellpadding="8" cellspacing="1">
					<tr>						
						<td class="text_numbering" colspan="3" align="right">&nbsp;</td>
					</tr>
					<tr>
						<td width="350"  align="center" valign="middle" class="table_heading">Meal Name</td>
						<td width="100"  align="center" valign="middle" class="table_heading">100g</td>
						<td width="100"  align="center" valign="middle" class="table_heading">150g</td>
						<td width="100"  align="center" valign="middle" class="table_heading">200g</td>
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
								
							$line = '';
								
							$value = str_replace('"', '""', $key);
							$value = '"' . $value . '"' . "\t";
							$line .= $value;	
							
							$value = '"' . $meals_array[$key][100] . '"' . "\t";
							$line .= $value;	
							
							$value = '"' . $meals_array[$key][150] . '"' . "\t";
							$line .= $value;	
							
							$value = '"' . $meals_array[$key][200] . '"' . "\t";
							$line .= $value;		
										
							 ?>
								<tr bgcolor="<?=$j++%2==0?$general_func->color2:$general_func->color1;?>">
									<td align="left" valign="middle" class="table_content-blue"><?=$key?></td>
									<td align="center" valign="middle" class="table_content-blue"><?=$meals_array[$key][100]?></td>
									<td align="center" valign="middle" class="table_content-blue"><?=$meals_array[$key][150]?></td>
									<td align="center" valign="middle" class="table_content-blue"><?=$meals_array[$key][200]?></td>
								</tr>							
							<?php 
							$data .= trim($line)."\n";
							
							}		
					 	} 
					 	$data = str_replace("\r","",$data);
					
						$_SESSION['print_data']=$data; 
					 	?>
					 	<tr>
					 		<td  colspan="4" align="right"><img src="images/Excel-32-d.gif" alt="Export to excel" title="Export to excel" onclick="location.href='orders/excel-report-print.php'" style="cursor:pointer;"></td>
					 	</tr>
					 	<?php					 	
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
							foreach($snacks_array as $key => $value ){ 
								$line_snack = '';
								
								$value = str_replace('"', '""', $value['name']);
								$value = '"' . $value . '"' . "\t";
								$line_snack .= $value;	
								
								
								$value = str_replace('"', '""', $value['qty']);
								$value = '"' . $value . '"' . "\t";
								$line_snack .= $value;	
								
								
								?>
								<tr bgcolor="<?=$j++%2==0?$general_func->color2:$general_func->color1;?>">									
									<td align="left" valign="middle" class="table_content-blue"><?=$value['name']?></td>
									<td align="center" valign="middle" class="table_content-blue"><?=$value['qty']?></td>
								</tr>							
							<?php
							$data_snack .= trim($line_snack)."\n";
							
							}	
							$data_snack = str_replace("\r","",$data_snack);
							$_SESSION['print_data_snack']=$data_snack; 
								
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
