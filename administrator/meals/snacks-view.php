<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";


$data=array();
$return_url=$_REQUEST['return_url'];



$small=$path_depth ."meal_main/small/";
$original=$path_depth ."meal_main/";


$sql="select m.*,mc.name as meal_category from meals m";
$sql .=" left join meal_category mc on m.meal_category_id=mc.id";				
$sql .=" where m.id=" .  intval($_REQUEST['id'])  . " limit 1";
$result=$db->fetch_all_array($sql);	


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
          <td align="left" valign="middle" class="body_tab-middilebg">View Meal</td>
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
            <td width="797" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="12">
                <tr>
                  <td width="20%" class="body_content-form"><strong>Meal Name:</strong></td>
                  <td width="80%"><?=$result[0]['name']?></td>
                </tr>
                 <tr>
                  <td class="body_content-form"><strong>Meal Category:</strong></td>
                  <td><?=$result[0]['meal_category']?></td>
                </tr>              
                 
                  <tr>
                  <td class="body_content-form"><strong>Meal Description:</strong></td>
                  <td ><?=nl2br($result[0]['details'])?></td>
                </tr>               
                 <tr>
                  <td class="body_content-form"><strong>Energy:</strong></td>
                  <td><?=$result[0]['energy']?></td>
                </tr>
                <tr>
                  <td class="body_content-form"><strong>Calories:</strong></td>
                  <td><?=$result[0]['calories']?></td>
                </tr>
                <tr>
                  <td class="body_content-form"><strong>Protein:</strong></td>
                  <td><?=$result[0]['protein']?></td>
                </tr>
                <tr>
                  <td class="body_content-form"><strong>Fat Total:</strong></td>
                  <td><?=$result[0]['fat_total']?></td>
                </tr>
                <tr>
                  <td class="body_content-form"><strong>Carbohydrates:</strong></td>
                  <td><?=$result[0]['carbohydrates']?></td>
                </tr>
                <tr>
                  <td class="body_content-form"><strong>Carbs and/or Veggies?:</strong></td>
                  <td><?php
				if($result[0]['carbs_veggies'] == 1)
					echo "Carbs";
				else if($result[0]['carbs_veggies'] == 2)
					echo "Veggies";
				else
					echo "Carbs and Veggies";	
				
				?></td>
                </tr>
                <tr>
                  <td class="body_content-form"><strong>With or Without Sauce?:</strong></td>
                  <td><?=$result[0]['with_or_without_sauce']==1?'With Sauce':'Without Sauce';?></td>
                </tr>
                <tr>
                  <td class="body_content-form" valign="top"><strong>Price:</strong></td>
                  <td valign="top" style="line-height: 30px;"><?php
                  	$sql_size_price="select * from meals_sizes_prices where meal_id=" .  intval($_REQUEST['id'])  . " order by meal_size";
					$result_size_price=$db->fetch_all_array($sql_size_price);	
					$total_price=count($result_size_price);					
					
					for($p=0; $p < $total_price; $p++){
						echo $p+1 .") " . $result_size_price[$p]['meal_size']."gm: $";
						echo $result_size_price[$p]['meal_price'];
						echo "<br/>";		
					}
                  	?></td>
                </tr>
                <tr>
                  <td class="body_content-form" valign="top"><strong>Meal Photo:</strong></td>
                  <td valign="top">                   	
                  	 <?php if(trim($result[0]['photo_name']) != NULL){?>
                    		<a href="<?=$general_func->site_url.substr($original,6).$result[0]['photo_name']?>" class="highslide" onclick="return hs.expand(this)"><img src="<?=$general_func->site_url.substr($small,6).$result[0]['photo_name']?>" border="0" /></a>&nbsp;&nbsp;
                    <?php }	?>	</td>
                </tr>
               
               <tr>
                  <td class="body_content-form"><strong>Meal Status:</strong></td>
                  <td><?=$result[0]['status']==1?'Active':'Inactive';?></td>
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
