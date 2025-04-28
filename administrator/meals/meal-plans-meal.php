<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";


$data=array();

if(isset($_REQUEST['name']) && trim($_REQUEST['name']) !=NULL){
	$_SESSION['meal_plan_name']=urldecode(trim($_REQUEST['name']));	
}	

if(isset($_REQUEST['return_url']) && trim($_REQUEST['return_url']) !=NULL){
	$_SESSION['scheduled_return_url']=trim($_REQUEST['return_url']);	
}		
	
if(isset($_REQUEST['id']) && trim($_REQUEST['id']) !=NULL){
	$_SESSION['meal_plan_id']=trim($_REQUEST['id']);	
}	


if(isset($_GET['action']) && $_GET['action']=='delete'){			
	
	@mysql_query("delete from  call_history  where id=" . intval($_REQUEST['call_id']) . "");
	$_SESSION['msg']="Your selected call history deleted!";
	$general_func->header_redirect($_SERVER['PHP_SELF']);
}
 
 
$result_meal_pan=$db->fetch_all_array("select name,no_of_days,meal_per_day,snack_per_day,meal_plan_category_id from meal_plans where id='" .  $_SESSION['meal_plan_id'] . "' limit 1 ");

 
if(isset($_POST['enter']) && $_POST['enter']=="yes" && trim($_POST['login_form_id'])==$_SESSION['login_form_id']){
				 
	$no_of_days=$_POST['no_of_days'];
	$meal_per_day=$_POST['meal_per_day'];
	$snack_per_day=$_POST['snack_per_day'];		
		
	$db->query_delete("meal_plan_meals","meal_plan_id='". $_SESSION['meal_plan_id'] ."'");
		
	$sql_meals="INSERT INTO meal_plan_meals(meal_plan_id,meal_id,which_day,meal_time,meal_size,type) VALUES";
		
	for($day=1; $day <= $no_of_days; $day++ ){ 
		for($time=1; $time <= $meal_per_day; $time++ ){			
			$sql_meals .= "('" . $_SESSION['meal_plan_id'] . "','" . $_REQUEST['meal_id_'.$day.'_'.$time] . "','" . $day . "','" . $time . "','" . $_REQUEST[$day.'_'.$time] . "',1), ";
		}
		
		for($time=1; $time <= $snack_per_day; $time++ ){			
			$sql_meals .= "('" . $_SESSION['meal_plan_id'] . "','" . $_REQUEST['snack_id_'.$day.'_'.$time] . "','" . $day . "','" . $time . "','" . $_REQUEST['snack_qty_'.$day.'_'.$time] . "',2), ";
		}
		
	}		
	
	$sql_meals =  substr($sql_meals,0,-2).";";		
	$db->query($sql_meals);	
	
	$_SESSION['msg']="Meals for  - " . $_SESSION['meal_plan_name'] . " has been updated!";
	
	$general_func->header_redirect($general_func->admin_url . "meals/meal-plans.php");	
	
}



$result_default_meals=$db->fetch_all_array("select * from meal_plan_meals where meal_plan_id='" . $_SESSION['meal_plan_id'] . "' order by which_day,meal_time ASC");
$total_default_meals=count($result_default_meals);

$default_meals=array();

for($i=0; $i < $total_default_meals; $i++ ){
	if($result_default_meals[$i]['type'] == 1){
		$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_id']=$result_default_meals[$i]['meal_id'];	
		$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_size']=$result_default_meals[$i]['meal_size'];	
	}else{
		$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['snack_id']=$result_default_meals[$i]['meal_id'];	
		$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['snack_qty']=$result_default_meals[$i]['meal_size'];	
	}	
}


$result_meals=$db->fetch_all_array("select id,name from meals where status=1 and id IN(select DISTINCT(meal_id) from meal_plan_category_meals where meal_plan_category_id='" . intval($result_meal_pan[0]['meal_plan_category_id']) . "')  order by name ASC");
$total_meals=count($result_meals);


$result_snacks=$db->fetch_all_array("select id,name from snacks where status=1 and id IN(select DISTINCT(snack_id) from meal_plan_category_snacks where meal_plan_category_id='" . intval($result_meal_pan[0]['meal_plan_category_id']) . "' ) order by name ASC");
$total_snacks=count($result_snacks);


?>

<script language="JavaScript">
function meals_validate(){
	var error=0;
	
	var no_of_days=parseInt(document.frmmeal.no_of_days.value);
	var meal_per_day=parseInt(document.frmmeal.meal_per_day.value);
	
	var snack_per_day=parseInt(document.frmmeal.snack_per_day.value);
	
	
	
	for(var day=1; day <= no_of_days; day++ ){
		for(var time=1; time <= meal_per_day; time++ ){						
			if(document.getElementById("meal_id_"+ day + "_" +time).value == ""){
      			document.getElementById("meal_id_"+ day + "_" +time).style.border="1px solid #ff5657";
				error++;
      		}else{
      			document.getElementById("meal_id_"+ day + "_" +time).style.border="1px solid #D8D9DA";	
      		}
      		
      		if(document.getElementById("price_100_"+ day + "_" +time).checked == false && document.getElementById("price_150_"+ day + "_" +time).checked == false &&  document.getElementById("price_200_"+ day + "_" +time).checked == false){
      			document.getElementById(day + "_" +time).style.border="1px solid #ff5657";
				error++;      			
      		}else{      				
				document.getElementById(day + "_" +time).style.border="1px solid #D8D9DA";	      			
      		} 
		}
		
		
		for(var time=1; time <= snack_per_day; time++ ){			
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
				<td align="left" valign="middle" class="body_tab-middilebg"> Meals for - <i><?=$_SESSION['meal_plan_name']?></i></td>
				<td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
			</tr>
		</table></td>
	</tr>
	<tr>
		<td align="left" valign="top" class="body_whitebg">
		<form method="post" action="<?=$_SERVER['PHP_SELF']?>"  name="frmmeal" onsubmit="return meals_validate()">
        <input type="hidden" name="enter" value="yes" /> 
       <input type="hidden" name="login_form_id" value="<?=$_SESSION['login_form_id']?>" />	
        <input type="hidden" name="no_of_days" value="<?=intval($result_meal_pan[0]['no_of_days'])?>" />
        <input type="hidden" name="meal_per_day" value="<?=intval($result_meal_pan[0]['meal_per_day'])?>" />
         <input type="hidden" name="snack_per_day" value="<?=intval($result_meal_pan[0]['snack_per_day'])?>" />
        		
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
			for($day=1; $day <= intval($result_meal_pan[0]['no_of_days']); $day++ ){ ?>
				<tr>
				<td align="left" valign="top">
				<table width="850" align="center" bgcolor="<?=$color++%2==0?$general_func->color2:$general_func->color1;?>" border="0" cellpadding="6" cellspacing="6">
					<tr>
						<td colspan="3" class="bold_heading">Day <?=$day?></td>
					</tr>
					<?php for($time=1; $time <=intval($result_meal_pan[0]['meal_per_day']); $time++ ){?>
					<tr style="padding: 10px;">
						<td width="100" align="left" valign="middle" class="table_content-blue">Meal <?=$time?></td>
						<td width="450" align="center" valign="middle" class="table_content-blue">
							<select name="meal_id_<?=$day."_".$time?>" id="meal_id_<?=$day."_".$time?>" class="inputbox_select" style="width: 400px; padding: 2px 1px 2px 0px;">                         	
                          <option value="">Select One</option>
                          <?php	  for($meal=0; $meal < $total_meals; $meal++){ ?>
						  	<option value="<?=$result_meals[$meal]['id']?>" <?=intval($result_meals[$meal]['id'])==$default_meals[$day][$time]['meal_id']?'selected="selected"':'';?>><?=$result_meals[$meal]['name']?></option>	
						<?php } 
						reset($result_meals);
						?>
                        </select>
                        </td>
                        <td id="<?=$day."_".$time?>">
                        	<input type="radio" name="<?=$day."_".$time?>" id="price_100_<?=$day."_".$time?>"  value="100" <?=$default_meals[$day][$time]['meal_size']==100?'checked="checked"':'';?> /> 100gm &nbsp;&nbsp;
							<input type="radio" name="<?=$day."_".$time?>" id="price_150_<?=$day."_".$time?>"  value="150" <?=$default_meals[$day][$time]['meal_size']==150?'checked="checked"':'';?> /> 150gm &nbsp;&nbsp;
                  			<input type="radio" name="<?=$day."_".$time?>" id="price_200_<?=$day."_".$time?>"  value="200" <?=$default_meals[$day][$time]['meal_size']==200?'checked="checked"':'';?> /> 200gm &nbsp;&nbsp;
                  	   </td>						
                  </tr>
				<?php } ?>	
				
				<?php for($time=1; $time <=intval($result_meal_pan[0]['snack_per_day']); $time++ ){?>
					<tr style="padding: 10px;">
						<td width="100" align="left" valign="middle" class="table_content-blue">Snack <?=$time?></td>
						<td width="450" align="center" valign="middle" class="table_content-blue">
							<select name="snack_id_<?=$day."_".$time?>" id="snack_id_<?=$day."_".$time?>" class="inputbox_select" style="width: 400px; padding: 2px 1px 2px 0px;">                         	
                          <option value="">Select One</option>
                          <?php	  for($snack=0; $snack < $total_snacks; $snack++){ ?>
						  	<option value="<?=$result_snacks[$snack]['id']?>" <?=intval($result_snacks[$snack]['id'])==$default_meals[$day][$time]['snack_id']?'selected="selected"':'';?>><?=$result_snacks[$snack]['name']?></option>	
						<?php } 
						reset($result_snacks);
						?>
                        </select>
                        </td>
                        <td>
                        	 Qty:
                        	<input type="text" name="snack_qty_<?=$day."_".$time?>" id="snack_qty_<?=$day."_".$time?>" value="<?=$default_meals[$day][$time]['snack_qty']?>" AUTOCOMPLETE=OFF class="form_inputbox" size="5" style="text-align: center;" />
                  	   </td>						
                  </tr>
				<?php } ?>	
								
				</table></td>
			</tr>
			<tr>
				<td height="30px;"></td>
			</tr> 				
			<?php } ?>
			
			 <tr>
            <td colspan="4" height="30" align="center"><table width="879" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="19%">&nbsp;</td>
                  <td width="11%" align="left"><table border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                        <td align="left" valign="middle" class="body_tab-middilebg"><input name="submit" type="submit" class="submit1" value="Submit" /></td>
                        <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                      </tr>
                    </table></td>
                  <td width="70%"> <table border="0" align="left" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                        <td align="left" valign="middle" class="body_tab-middilebg">
                        	<input type="button" class="submit1"  name="back" value="Back"  onclick="location.href='<?=$general_func->admin_url?>meals/meal-plans.php'" /></td>
                        <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                      </tr>
                    </table></td>
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
include("../foot.htm");
?>
