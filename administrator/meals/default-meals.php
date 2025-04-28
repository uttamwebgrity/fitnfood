<?php
$path_depth = "../../";

include_once ("../head.htm");
$link_name = "Welcome";

$small = $path_depth . "meal_main/small/";
$original = $path_depth . "meal_main/";

if (isset($_REQUEST['meal_plan_category_id']) && intval($_REQUEST['meal_plan_category_id']) > 0){
	$_SESSION['default_meal_plan_category_id'] = intval($_REQUEST['meal_plan_category_id']);	
}

if(isset($_POST['enter']) && $_POST['enter']=="yes" && trim($_POST['login_form_id'])==$_SESSION['login_form_id']){
		
	$db->query_delete("categories_default_meals","meal_plan_category_id='". $_SESSION['default_meal_plan_category_id'] ."'");
		
	$sql_meals="INSERT INTO categories_default_meals(meal_plan_category_id,meal_id,which_day,meal_time,type,qty) VALUES";
		
	for($day=1; $day <=7; $day++ ){ 
		for($time=1; $time <=$_REQUEST['meals_per_day']; $time++ ){
			$sql_meals .= "('" . $_SESSION['default_meal_plan_category_id'] . "','" . $_REQUEST['meal_id_'.$day.'_'.$time] . "','" . $day . "','" . $time . "',1,0), ";
		}
		for($time=1; $time <=$_REQUEST['snacks_per_day']; $time++ ){
			$sql_meals .= "('" . $_SESSION['default_meal_plan_category_id'] . "','" . $_REQUEST['snack_id_'.$day.'_'.$time] . "','" . $day . "','" . $time . "',2,'" . $_REQUEST['snack_qty_'.$day.'_'.$time] . "' ), ";
		}
		
	}		
	
	$sql_meals =  substr($sql_meals,0,-2).";";	
	$db->query($sql_meals);	
	
	$_SESSION['msg']="Default Meals for Category - " . $_SESSION['default_meal_plan_category_name'] . " has been updated!";
	
	$general_func->header_redirect($general_func->admin_url . "meals/default-meals.php?meal_plan_category_id=" . $_SESSION['default_meal_plan_category_id']);		
	
}


$result_default_meals=$db->fetch_all_array("select * from categories_default_meals where meal_plan_category_id='" . $_SESSION['default_meal_plan_category_id'] . "'  order by which_day,meal_time ASC");
$total_default_meals=count($result_default_meals);

$default_meals=array();

for($i=0; $i < $total_default_meals; $i++ ){
	if($result_default_meals[$i]['type'] == 1){
		$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_id']=$result_default_meals[$i]['meal_id'];		
	}else{
		$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['snack_id']=$result_default_meals[$i]['meal_id'];
		$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['snack_qty']=$result_default_meals[$i]['qty'];			
	}
}


//*****************  Plan layout ******************************************//
$meal_plan_info=$db->fetch_all_array("select name,meals_per_day,snacks_per_day,snacks_type from meal_plan_category where id='" . intval($_SESSION['default_meal_plan_category_id']) . "' limit 1");




$sql_layout="select which_day,meal_time,meal_category_id,carbs_veggies,with_or_without_sauce,m.name from meal_plan_layout d left join meal_category m on d.meal_category_id=m.id where d.meal_plan_category_id='" . intval($_SESSION['default_meal_plan_category_id']) . "' order by which_day,meal_time ASC";
$result_layout=$db->fetch_all_array($sql_layout);
$total_layout=count($result_layout);

$default_plan_layout=array();
									
for($i=0; $i < $total_layout; $i++ ){
	$default_plan_layout[$result_layout[$i]['which_day']][$result_layout[$i]['meal_time']]['meal_category']=$result_layout[$i]['name'];
	$default_plan_layout[$result_layout[$i]['which_day']][$result_layout[$i]['meal_time']]['meal_category_id']=$result_layout[$i]['meal_category_id'];
	$default_plan_layout[$result_layout[$i]['which_day']][$result_layout[$i]['meal_time']]['carbs_veggies']=$result_layout[$i]['carbs_veggies'];
	$default_plan_layout[$result_layout[$i]['which_day']][$result_layout[$i]['meal_time']]['with_or_without_sauce']=$result_layout[$i]['with_or_without_sauce'];
} 
//*************************************************************************//


$snacks_type_array = array();	
$snacks_type_array = explode(",", $meal_plan_info[0]['snacks_type']);
$total_snacks_type=count($snacks_type_array);
$snacks_query="";
$snacks_found=0;
					
for($snack=0; $snack < $total_snacks_type; $snack++ ){
	$snacks_query .=" snacks_type LIKE '%{" . $snacks_type_array[$snack]. "}%' or ";
	$snacks_found=1;
}

$snacks_query = substr($snacks_query,0, -3);

if($snacks_found ==1)
	$snacks_query ="( ". $snacks_query . " )";				

//print_r ($default_plan_layout);
?>
<script language="JavaScript">
function default_validate(){
	
	var meals_per_day=document.frmdefault.meals_per_day.value;
	var snacks_per_day=document.frmdefault.snacks_per_day.value;
	
	
	
	var error=0;
	
	for(var day=1; day <=7; day++ ){
		for(var time=1; time <= meals_per_day; time++ ){			
			if(document.getElementById("meal_id_"+ day + "_" +time).value == ""){
      			document.getElementById("meal_id_"+ day + "_" +time).style.border="1px solid #ff5657";
				error++;
      		}else{
      			document.getElementById("meal_id_"+ day + "_" +time).style.border="1px solid #D8D9DA";	
      		}
		}
		
		for(var time=1; time <= snacks_per_day; time++ ){			
			if(document.getElementById("snack_id_"+ day + "_" +time).value == ""){
      			document.getElementById("snack_id_"+ day + "_" +time).style.border="1px solid #ff5657";
				error++;
      		}else{
      			document.getElementById("snack_id_"+ day + "_" +time).style.border="1px solid #D8D9DA";	
      		}
      		var val=document.getElementById("snack_qty_"+ day + "_" +time).value;
      		
      		if(parseInt(val) > 0){
      			document.getElementById("snack_qty_"+ day + "_" +time).style.border="1px solid #D8D9DA";      			
      		}else{
      			document.getElementById("snack_qty_"+ day + "_" +time).style.border="1px solid #ff5657";
				error++;
      		}
		}
			
	}
	
	if(error>0)
		return false;
	else
		return true;	
}	
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td align="left" valign="top">
		<table border="0" align="left" cellpadding="0" cellspacing="0">
			<tr>
				<td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
				<td align="left" valign="middle" class="body_tab-middilebg"> Default Meals for <i>"<?=$meal_plan_info[0]['name']?>"</i>&nbsp; Meal Plan Layout</td>
				<td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
			</tr>
		</table></td>
	</tr>
	<tr>
		<td align="left" valign="top" class="body_whitebg">
		<form method="post" action="<?=$_SERVER['PHP_SELF']?>"  name="frmdefault" onsubmit="return default_validate()">
        <input type="hidden" name="enter" value="yes" /> 
       <input type="hidden" name="login_form_id" value="<?=$_SESSION['login_form_id']?>" />	
       <input type="hidden" name="meals_per_day" value="<?=intval($meal_plan_info[0]['meals_per_day'])?>" />
       <input type="hidden" name="snacks_per_day" value="<?=intval($meal_plan_info[0]['snacks_per_day'])?>" />       	
       
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td align="left" valign="top">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td align="left" valign="top"><img src="images/spacer.gif" alt="" width="14" height="14" /></td>
					</tr>
					<?php if(isset($_SESSION['msg']) && trim($_SESSION['msg']) != NULL){
					?>
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
			$color = 1;
			for($day=1; $day <=7; $day++ ){ ?>
				<tr>
				<td align="left" valign="top">
				<table width="650" align="center" bgcolor="<?=$color++%2==0?$general_func->color2:$general_func->color1;?>" border="0" cellpadding="6" cellspacing="1">
					<tr>
						<td colspan="3" class="bold_heading">Day <?=$day?></td>
					</tr>
					<?php for($time=1; $time <=intval($meal_plan_info[0]['meals_per_day']); $time++ ){?>
					<tr style="padding: 10px;">
						<td width="100" align="left" valign="middle" class="table_content-blue">Meal <?=$time?> :</td>
						<td width="450" align="left" valign="middle" class="table_content-blue">
							<?php							
							$result_meals=$db->fetch_all_array("select id,name from meals where status=1 and id IN(select DISTINCT(meal_id) from meal_plan_category_meals where meal_plan_category_id='" . intval($_SESSION['default_meal_plan_category_id']) . "' ) and meal_category_id='" . $default_plan_layout[$day][$time]['meal_category_id'] . "' and with_or_without_sauce='" . $default_plan_layout[$day][$time]['with_or_without_sauce'] . "' and carbs_veggies='" . $default_plan_layout[$day][$time]['carbs_veggies'] . "' order by name ASC");
							$total_meals=count($result_meals);
							?>							
							<select name="meal_id_<?=$day."_".$time?>" id="meal_id_<?=$day."_".$time?>" class="inputbox_select" style="width: 350px; padding: 2px 1px 2px 0px;">                         	
                          <option value=""><?=$default_plan_layout[$day][$time]['meal_category']?></option>
                          <?php	  for($meal=0; $meal < $total_meals; $meal++){ ?>
						  	<option value="<?=$result_meals[$meal]['id']?>" <?=$default_meals[$day][$time]['meal_id']==$result_meals[$meal]['id']?'selected="selected"':'';?>><?=$result_meals[$meal]['name']?></option>	
						<?php } 
						reset($result_meals);
						?>
                        </select>
                        </td>						
                  	</tr>
					<?php } 
				
					
					for($time=1; $time <=intval($meal_plan_info[0]['snacks_per_day']); $time++ ){ ?>
					<tr style="padding: 10px;">
						<td width="100" align="left" valign="middle" class="table_content-blue">Snack <?=$time?> :</td>
						<td width="450" align="left" valign="middle" class="table_content-blue">
							
							<?php							
							$result_snacks=$db->fetch_all_array("select id,name from snacks where status=1 and id IN(select DISTINCT(snack_id) from meal_plan_category_snacks where meal_plan_category_id='" . intval($_SESSION['default_meal_plan_category_id']) . "' ) and $snacks_query order by name ASC");
							$total_snacks=count($result_snacks);
							?>							
							<select name="snack_id_<?=$day."_".$time?>" id="snack_id_<?=$day."_".$time?>" class="inputbox_select" style="width: 273px; padding: 2px 1px 2px 0px;">                         	
                          <option value="">Select Snack <?=$time?></option>
                          <?php	  for($snack=0; $snack < $total_snacks; $snack++){ ?>
						  	<option value="<?=$result_snacks[$snack]['id']?>" <?=$default_meals[$day][$time]['snack_id']==$result_snacks[$snack]['id']?'selected="selected"':'';?>><?=$result_snacks[$snack]['name']?></option>	
						<?php } 
						reset($result_snacks);
						?>
                        </select>
                        Qty:
                        <input type="text" name="snack_qty_<?=$day."_".$time?>" id="snack_qty_<?=$day."_".$time?>" value="<?=$default_meals[$day][$time]['snack_qty']?>" AUTOCOMPLETE=OFF class="form_inputbox" size="5" style="text-align: center;" />
                        </td>						
                  	</tr>
					<?php  } ?>	
				</table></td>
			</tr>
			<tr>
				<td height="30px;"></td>
			</tr> 				
			<?php } ?>
			
			 <tr>
            <td colspan="4" height="30" align="center"><table width="879" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="35%">&nbsp;</td>
                  <td width="25%" align="left"><table border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                        <td align="left" valign="middle" class="body_tab-middilebg"><input name="submit" type="submit" class="submit1" value="Submit" /></td>
                        <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                      </tr>
                    </table></td>
                  <td width="40%">&nbsp;</td>
                </tr>
              </table></td>
          </tr>
			<tr>
				<td height="20px;"></td>
			</tr> 
			
		</table>
		</form>
		</td>
	</tr>
</table>
<?php
include ("../foot.htm");
?>
