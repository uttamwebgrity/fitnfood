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
				<td align="left" valign="middle" class="body_tab-middilebg">Heard About Us</td>
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
											<td width="100" align="right" valign="middle" class="content_employee" style=" width:150px;   padding-right: 5px;">Gender:</td>
											<td width="400" align="left" valign="middle">
											<select name="gender" class="inputbox_select">
								<option value="" <?=$_REQUEST['gender']==""?'selected="selected"':'';?>>All</option>
		            				<option value="1" <?=$_REQUEST['gender']==1?'selected="selected"':'';?>>Male</option>
		            				<option value="2" <?=$_REQUEST['gender']==2?'selected="selected"':'';?>>Female</option>
		            				
	            					</select>
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
								<p style="text-align:center; height: 20px;" ></p></td>
							</tr>
						</table></td>
					</tr>
				</table></td>
			</tr>
			<?php
			//**************************************************************************************//
			$query="";
			
			if(isset($_REQUEST['gender']) && intval($_REQUEST['gender'])> 0){
				$query .=" and gender = '" .intval($_REQUEST['gender']). "'";
			}	
			
			if(isset($_REQUEST['date_start']) && trim($_REQUEST['date_start']) != NULL){
				 list($dd,$mm,$yy)=explode("/",trim($_REQUEST['date_start']));
				 $date_start=$yy ."-".$mm ."-".$dd;	
				 if(checkdate($mm, $dd, $yy)){				 					 
				 	$query .=" and date_added >= '" .$date_start. "'";	
				}
				
				if(isset($_REQUEST['date_end']) && trim($_REQUEST['date_end']) != NULL){
				 	list($dd,$mm,$yy)=explode("/",trim($_REQUEST['date_end']));
				 	$date_end=$yy ."-".$mm ."-".$dd;	
					
				 	if(checkdate($mm, $dd, $yy) && strtotime($date_end) >= strtotime($date_start)){
				 		$date_end=$yy ."-".$mm ."-".$dd;											 
				 		$query .=" and date_added <= '" .date_end. "'";	
					}
				}	
			}	
			//echo $query;
			 			
			$hear_about_us=$db -> fetch_all_array("select id,name from hear_about_us where status=1 order by name ASC ");
			$total_hear_about_us=count($hear_about_us);
			
			$heard_about_us_array=array();
			
			$total_used=0;
			
			for($i=0; $i < $total_hear_about_us; $i++ ){
				$heard_about_us_array[$i]['name']=$hear_about_us[$i]['name'];	
				$used=mysql_result(mysql_query("select count(*) from users where hear_about_us= '" . $hear_about_us[$i]['id'] . "' $query "),0,0);
				$total_used += $used;
				$heard_about_us_array[$i]['used']=$used;
			}
		
			
			
			
				
			?>
			<tr>
				<td align="left" valign="top">
				<table width="500" align="center" border="0"	cellpadding="8" cellspacing="1">
					
					<tr>
						<td width="200"  align="center" valign="middle" class="table_heading">Name</td>
						<td width="100"  align="center" valign="middle" class="table_heading">Used</td>
						<td width="100"  align="center" valign="middle" class="table_heading">% of Used</td>
					</tr>					
					<?php 
						for($j=0; $j < $total_hear_about_us; $j++ ){ ?>
 			<tr bgcolor="<?=$j%2==0?$general_func->color2:$general_func->color1;?>">
				<td align="left" valign="middle" class="table_content-blue"><?=$heard_about_us_array[$j]['name']?></td>
					<td align="center" valign="middle" class="table_content-blue"><?=$heard_about_us_array[$j]['used'] ?> times </td>
					<td align="center" valign="middle" class="table_content-blue"><?php
					
					$percentage=($heard_about_us_array[$j]['used'] * 100)/$total_used;
					
					if($percentage >0)
						$percentage =number_format($percentage, 2);
					 echo $percentage; ?>%</td>
					
					
					</tr>
					<?php }?>
					<tr>
					
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
