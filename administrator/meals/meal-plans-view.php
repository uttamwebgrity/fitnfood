<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";


$data=array();
$return_url=$_REQUEST['return_url'];


$result_meal_plan=$db->fetch_all_array("select p.*,c.name as category_name from meal_plans p, meal_plan_category c where p.meal_plan_category_id=c.id and  p.id='" .  intval($_REQUEST['id']) . "' limit 1 ");



$sql_meals="select which_day,meal_time,meal_size,meal_id,(select meal_price from meals_sizes_prices where meal_id=d.meal_id and meal_size=d.meal_size) as price,m.name from meal_plan_meals d left join meals m on d.meal_id=m.id where d.meal_plan_id='" . intval($_REQUEST['id']) . "' and type=1  order by which_day,meal_time ASC";
$result_default_meals=$db->fetch_all_array($sql_meals);
$total_default_meals=count($result_default_meals);

$default_meals=array();

for($i=0; $i < $total_default_meals; $i++ ){
	$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_name']=$result_default_meals[$i]['name'];
	$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['meal_size']=$result_default_meals[$i]['meal_size'];
	$default_meals[$result_default_meals[$i]['which_day']][$result_default_meals[$i]['meal_time']]['price']=$result_default_meals[$i]['price'];
}



$sql_snacks="select which_day,meal_time,meal_size,meal_id,price,m.name from meal_plan_meals d left join snacks m on d.meal_id=m.id where d.meal_plan_id='" . intval($_REQUEST['id']) . "' and type=2 order by which_day,meal_time ASC";
$result_default_snacks=$db->fetch_all_array($sql_snacks);
$total_default_snacks=count($result_default_snacks);

$default_snacks=array();

for($i=0; $i < $total_default_snacks; $i++ ){
	$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['snack_name']=$result_default_snacks[$i]['name'];
	$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['qty']=$result_default_snacks[$i]['meal_size'];
	$default_snacks[$result_default_snacks[$i]['which_day']][$result_default_snacks[$i]['meal_time']]['price']=intval($result_default_snacks[$i]['meal_size']) * $result_default_snacks[$i]['price'];
}



$total_price=0;


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
          <td align="left" valign="middle" class="body_tab-middilebg">View Meal Plan - <?=$result_meal_plan[0]['name']?></td>
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
                  <td width="20%" class="body_content-form"><strong>Main Goal:</strong></td>
                  <td width="80%"><?=$result_meal_plan[0]['category_name']?></td>
                </tr>
                 <tr>
                  <td class="body_content-form"><strong>Meal Plan:</strong></td>
                  <td><?=$result_meal_plan[0]['name']?></td>
                </tr>
               
                  <tr>
                  <td class="body_content-form"><strong>Meal Plan Description:</strong></td>
                  <td ><?=nl2br($result_meal_plan[0]['details'])?></td>
                </tr> 
                <tr>
            		<td colspan="2" height="5"></td>
          		</tr>
                
                <?php for($day=1; $day <=intval($result_meal_plan[0]['no_of_days']); $day++){ ?>
                 <tr>
                  <td class="body_content-form" style="padding-top: 20px;">&nbsp;</td>
                  <td ><strong> * Day  <?=$day?> *</strong></td>
				<?php 
					for($time=1; $time <= intval($result_meal_plan[0]['meal_per_day']); $time++ ){ ?>
					<tr>
                  		<td class="body_content-form"><strong>Meal <?=$time?> :</strong></td>
                  		<td ><?=$default_meals[$day][$time]['meal_name'] .' '. $default_meals[$day][$time]['meal_size']; ?>g - $<?=$default_meals[$day][$time]['price']?> </td>
                	</tr> 					
					<?php
					$total_price += $default_meals[$day][$time]['price'];
					}
					 
                ?>
                
                <?php 
					for($time=1; $time <= intval($result_meal_plan[0]['snack_per_day']); $time++ ){ ?>
					<tr>
                  		<td class="body_content-form"><strong>Snack <?=$time?> :</strong></td>
                  		<td ><?=$default_snacks[$day][$time]['snack_name'] .' ('. $default_snacks[$day][$time]['qty']; ?>) - $<?=number_format($default_snacks[$day][$time]['price'],2)?> </td>
                	</tr> 					
					<?php
					$total_price += $default_snacks[$day][$time]['price'];
					}
				}	 
                ?>
                <tr>
            		<td colspan="2" height="5"><p>&nbsp;</p></td>
          		</tr>
                 <tr>
                  <td width="20%" class="body_content-form"><strong>Price:</strong></td>
                  <td width="80%"><strong>$<?=number_format($total_price,2)?> p/w</strong></td>
                </tr>
                             
                      
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
