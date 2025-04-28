<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";


$data=array();
$return_url=$_REQUEST['return_url'];

$no_of_days=0;
$meal_per_day=0;
$snack_per_day=0;
              

$sql_order = "select o.id,program_length,pickup_delivery,order_type,order_amount,order_start_date,name,notes,CONCAT(fname,' ',lname) as user_name,email_address,o.status from orders o  left join meal_plan_category p on o.meal_plan_category_id=p.id";
$sql_order .= " left join users u on o.user_id=u.id";
$sql_order .= " where o.id='" .  intval($_REQUEST['id']) . "' limit 1 ";
$result_order=$db->fetch_all_array($sql_order);


$sql_meals="select which_day,meal_time,meal_size,m.name from order_meals d left join meals m on d.meal_id=m.id where d.order_id='" . intval($_REQUEST['id']) . "' and type=1  order by which_day,meal_time ASC";
$result_default_meals=$db->fetch_all_array($sql_meals);
$total_default_meals=count($result_default_meals);

$default_meals=array();

for($i=0; $i < $total_default_meals; $i++ ){
	
	if($result_default_meals[$i]['which_day'] > $no_of_days)
		$no_of_days=$result_default_meals[$i]['which_day'];
	
	if($result_default_meals[$i]['meal_time'] > $meal_per_day)
		$meal_per_day=$result_default_meals[$i]['meal_time'];
	
	
	$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_name']=$result_default_meals[$i]['name'];
	$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_size']=$result_default_meals[$i]['meal_size'];
}


$sql_snacks="select which_day,meal_time,meal_size,m.name from order_meals d left join snacks m on d.meal_id=m.id where d.order_id='" . intval($_REQUEST['id']) . "' and type=2 order by which_day,meal_time ASC";
$result_default_snacks=$db->fetch_all_array($sql_snacks);
$total_default_snacks=count($result_default_snacks);

$default_snacks=array();

for($i=0; $i < $total_default_snacks; $i++ ){
	
	if($result_default_snacks[$i]['meal_time'] > $snack_per_day)
		$snack_per_day=$result_default_snacks[$i]['meal_time'];
	
	$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['snack_name']=$result_default_snacks[$i]['name'];
	$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['qty']=$result_default_snacks[$i]['meal_size'];
}

?> 
<script type="text/javascript" src="<?=$general_func->site_url?>highslide/highslide.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$general_func->site_url?>highslide/highslide.css" />
<script type="text/javascript">
	hs.graphicsDir = '<?=$general_func->site_url?>highslide/graphics/';
	hs.wrapperClassName = 'wide-border';
</script>	
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg">View Order: FNF - A000<?=intval($_REQUEST['id'])?> </td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg">
        <table width="989" border="0" align="left" cellpadding="4" cellspacing="0">
          <tr>
            <td colspan="2" height="30"></td>
          </tr>
          <tr>
            <td width="32" align="left" valign="top"></td>
            <td width="797" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="6">
                <tr>
                  <td width="20%" class="body_content-form"><strong>User Name:</strong></td>
                  <td width="80%"><?=$result_order[0]['user_name'] ?></td>
                </tr>
                 <tr>
                  <td class="body_content-form"><strong>Plan Category:</strong></td>
                  <td><?=$result_order[0]['name'] ?></td>
                </tr>
               
                  <tr>
                  <td class="body_content-form"><strong>Order Type:</strong></td>
                  <td ><?php					
					if($result_order[0]['order_type'] == 1){
						echo "Meal Plan Selected";							
					}else if($result_order[0]['order_type'] == 2){
						echo "Filled the Questionnaire";	
					}else{
						echo "Customized Meal Plan";	
						
					}?></td>
                </tr> 
                 
                  <tr>
                  <td class="body_content-form"><strong>Amount:</strong></td>
                  <td >$<?=$result_order[0]['order_amount'] ?> p/w (GST <?=$GST?>% included)</td>
                </tr>                 
                
                <?php if(trim($result_order[0]['notes']) != NULL){ ?>
                 <tr>
                  <td class="body_content-form" valign="top"><strong>Delivery Notes:</strong></td>
                  <td valign="top"><?=nl2br($result_order[0]['notes'])?></td>
                </tr> 
					
                <?php } ?>
                 <tr>
                  <td class="body_content-form" valign="top"><strong>Pickup or Delivery:</strong></td>
                  <td valign="top"><?=$result_order[0]['pickup_delivery']==1?'Delivery':'Pickup'; ?></td>
                </tr> 
                
                  <tr>
                  <td class="body_content-form" valign="top"><strong>Program Length:</strong></td>
                  <td valign="top" style="line-height: 18px;"><?php
						$rs_program_length = $db->fetch_all_array("select name,details from discounts where id='" . intval($result_order[0]['program_length']). "' limit 1");
						echo $rs_program_length[0]['name']."<br/>";
						echo $rs_program_length[0]['details']?></td>
                </tr> 
                  <tr>
                  <td class="body_content-form"><strong>Status:</strong></td>
                  <td ><?=$general_func->order_status($result_order[0]['status'])?></td>
                </tr>            
             
                
                <tr>
            		<td colspan="2" height="5"></td>
          		</tr>
                
                <?php for($day=1; $day <=intval($no_of_days); $day++){ ?>
                 <tr>
                  <td class="body_content-form" style="padding-top: 20px;">&nbsp;</td>
                  <td ><strong> * Day  <?=$day?> *</strong></td>
				<?php 
					for($time=1; $time <= intval($meal_per_day); $time++ ){ ?>
					<tr>
                  		<td class="body_content-form"><strong>Meal <?=$time?> :</strong></td>
                  		<td ><?=$default_meals[$day][$time]['meal_name'] .' '. $default_meals[$day][$time]['meal_size']; ?>g </td>
                	</tr> 					
					<?php
					
					}
					 
                ?>
                
                <?php 
					for($time=1; $time <= intval($snack_per_day); $time++ ){ ?>
					<tr>
                  		<td class="body_content-form"><strong>Snack <?=$time?> :</strong></td>
                  		<td ><?=$default_snacks[$day][$time]['snack_name'] .' ('. $default_snacks[$day][$time]['qty']; ?>)</td>
                	</tr> 					
					<?php
					
					}
				}	 
                ?>  
              </table></td>
          </tr>
         <tr>
            <td colspan="2" height="20"><p>&nbsp;</p></td>
          </tr>
          <tr>
            <td colspan="2" height="30" align="center"><table width="879" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="31%"></td>
                  <td width="10%">&nbsp;</td>
                  <td width="5%"><table border="0" align="left" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                        <td align="left" valign="middle" class="body_tab-middilebg"><input name="back" onClick="location.href='<?=$return_url?>'"  type="button" class="submit1" value="Back" /></td>
                        <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                      </tr>
                    </table></td>
                  <td width="40%"></td>
                </tr>
              </table></td>
          </tr>
         
          <tr>
            <td colspan="2" height="30"></td>
          </tr>
        </table>
     </td>
  </tr>
</table>
<?php
include("../foot.htm");
?>
