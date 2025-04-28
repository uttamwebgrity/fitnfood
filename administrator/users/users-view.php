<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";




$data=array();
$return_url=$_REQUEST['return_url'];


$small=$path_depth . "photo/small/";
$original=$path_depth . "photo/";


if(isset($_REQUEST['action']) && $_REQUEST['action']=="VIEW"){
	$sql="select u.*,suburb_name,suburb_postcode,suburb_state,delivery_day,payment_debit_day,order_cutoff_day,order_cutoff_time,h.name as hear_us  from users u ";
	$sql .=" left join suburb s on u.suburb_id=s.id left join hear_about_us h on u.hear_about_us=h.id ";
	$sql .=" where u.id=" . (int) $_REQUEST['id'] . " limit 1";	
	$result=$db->fetch_all_array($sql);
	
	$sql_current_order = "select o.id,order_type,order_amount,order_start_date,name,o.status,suburb_id from orders o  left join meal_plan_category p on o.meal_plan_category_id=p.id";
	$sql_current_order .= " left join  users u on o.user_id=u.id";
	$sql_current_order .= " where user_id='" . intval($_REQUEST['id']) . "' and current_order=1 limit 1";
	$result_current_order = $db -> fetch_all_array($sql_current_order);
}
//print_r ($outfitter_videos);
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
          <td align="left" valign="middle" class="body_tab-middilebg">View User</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table>
      <span style="float: right;">
      <img src="images/current_week_order.png" alt="" style="vertical-align: bottom;" /> Paid for current week.
                	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                	<img src="images/last_week_order.png" alt="" style="vertical-align: bottom;" /> Paid for last week.</span>
      </td>
      
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg">
        <table width="900" border="0" align="left" cellpadding="0" cellspacing="0">
          <tr>
            <td colspan="4" height="30"></td>
          </tr>
          
            
          
          <tr>
            <td width="20" align="left" valign="top"></td>
            <td width="440" align="left" valign="top">
            	<table width="100%" border="0" cellspacing="0" cellpadding="10">                
               		<tr>
                  		<td colspan="2"   class="body_content-form" style=" padding-bottom: 10px;"><strong>General Information</strong></td>
                 	</tr>
                 <tr>
                  <td width="30%" class="body_content-form" valign="top">Name:</td>
                  <td width="70%" valign="top"><?=$result[0]['fname']." ".$result[0]['lname']?></td>
                </tr>  
                 <tr>
                  <td width="30%" class="body_content-form" valign="top">Gender:</td>
                  <td width="70%" valign="top"><?php
                  
                  if(trim($result[0]['gender']) !=  NULL){
                    echo trim($result[0]['gender'])==1?'Male':'Female';	
					
                  }?></td>
                </tr>              
                <tr>
                  <td class="body_content-form">Email Address:</td>
                  <td ><?=$result[0]['email_address']?></td>
                </tr>
                <?php if(trim($result[0]['facebook_id']) == NULL && trim($result[0]['google_id']) == NULL){ ?>
                 <tr>
                  <td  class="body_content-form" valign="top">Password:</td>
                  <td valign="top" style="line-height: 18px;"><?=$EncDec->decrypt_me($result[0]['password'])?></td>
                </tr>
                <?php } ?>
                 <tr>
                  <td class="body_content-form" valign="top">Mobile No.:</td>
                  <td class="body_content-form"  valign="top"><?=$result[0]['phone']?></td>
                </tr>
                
                
                <tr>
                  <td  class="body_content-form" valign="top">Refered Code:</td>
                  <td valign="top" style="line-height: 18px;"><?=$result[0]['refered_code']?></td>
                </tr>
                 
                <?php if(trim($result[0]['photo']) != NULL){?>
                <tr>
                  <td class="body_content-form" valign="top">Photo:</td>
                  <td  valign="top">
                    <a href="<?=$general_func->site_url.substr($original,6).$result[0]['photo']?>" class="highslide" onclick="return hs.expand(this)"><img src="<?=$general_func->site_url.substr($small,6).$result[0]['photo']?>" border="0" /></a>
                   
                    </td>
                </tr>
                <?php }	?>
               
                <tr>
                  <td  class="body_content-form" valign="top">Membership?:</td>
                  <td valign="top" style="line-height: 18px;">
                  	<?php 
                  	if($db_common->user_has_a_paid_week(intval($_REQUEST['id']),1) > 0){
                  		echo "<strong style='color: #ff0000;'> Platinum </strong>";                  		
					}else {
						echo "Standard";
					} ?>
                  	
                  </td>
                </tr>
               
                
                
                                
                
              </table></td>
               <td width="440" align="left" valign="top">
               	<table width="100%" border="0" cellspacing="0" cellpadding="10">
                  <tr>
                  <td colspan="2"   class="body_content-form"  style=" padding-bottom: 10px;" ><strong>Delivery Schedule &amp; Address Information </strong></td>
                 </tr>
                 <tr>
                  <td width="30%" class="body_content-form" valign="top">Street Address:</td>
                  <td width="70%" valign="top"><?=$result[0]['street_address']?></td>
                </tr>
                <tr>
                  <td class="body_content-form">Suburb:</td>
                  <td><?=ucwords(strtolower($result[0]['suburb_name']))?></td>
                </tr>
                <tr>
                  <td class="body_content-form">Post Code:</td>
                  <td><?=$result[0]['suburb_postcode']?></td>
                </tr>
                <tr>
                  <td class="body_content-form">State:</td>
                  <td><?=$result[0]['suburb_state']?></td>
                </tr>
                <?php if(trim($result[0]['suburb_name']) != NULL){ ?>
                <tr>
                  <td  class="body_content-form">Delivery Day:</td>
                  <td><?=$general_func->day_name($result[0]['delivery_day'])?></td>
                </tr>
                <tr>
                  <td  class="body_content-form">Payment Debit Day:</td>
                  <td><?=$general_func->day_name($result[0]['payment_debit_day'])?></td>
                </tr>               
                <tr>
                  <td  class="body_content-form">Cutoff Day &amp; Time:</td>
                  <td><?=$general_func->day_name($result[0]['order_cutoff_day']) ." ". date("h:i A",strtotime($result[0]['order_cutoff_time']));?></td>
                </tr> 
               <?php  }

				if(trim($result[0]['hear_us']) != NULL){ ?>
					 <tr>
                  <td  class="body_content-form">Heard About Us?:</td>
                  <td><?=$result[0]['hear_us']?></td>
                </tr> 
				<?php  }	 
				 ?>
               
               
               
               
              </table>
               </td>
           
          </tr>
           <tr>
            	<td width="20" align="left"  colspan="4" valign="top">&nbsp;</td>
            </tr>	
            <tr>
            	<td width="20" align="left" valign="top"></td>
            <td colspan="3" >
            	<?php if(count($result_current_order) == 1){ ?>
            		<table width="100%" border="0" cellspacing="0" cellpadding="10">
                  <tr>
                  <td colspan="2"   class="body_content-form"  style=" padding-bottom: 10px;" ><strong>Current Order </strong></td>
                 </tr>
                 <tr>
                  <td width="15%" class="body_content-form" valign="top">Order No :</td>
                  <td width="85%" valign="top">FNF - A000<?=intval($result_current_order[0]['id']) ?></td>
                </tr>
                <tr>
                  <td class="body_content-form">Start Date:</td>
                  <td><?php                
                  	if(strtotime($result_current_order[0]['order_start_date']) >= strtotime($today_date)){
						echo date("jS M. D, Y", strtotime($result_current_order[0]['order_start_date']));	
					}else if($result_suburb_info[0]['delivery_day'] == date("N")){
						echo date("jS M. D, Y", strtotime($today_date));
					}else{
						echo date("jS M. D, Y",strtotime('next '. strtolower($general_func -> day_name($result_suburb_info[0]['delivery_day']))));
					}?></td>
                </tr>
                <tr>
                  <td class="body_content-form">Order Amount:</td>
                  <td>$<?=$result_current_order[0]['order_amount'] ?>  p/w (GST <?=$GST?>% included)</td>
                </tr>
                <tr>
                  <td class="body_content-form">No. of days</td>
                  <td valign="top"><?=$db_common -> order_day_length(intval($result_current_order[0]['id'])) ?> days/week <a target="_blank" href="<?=$general_func->admin_url?>users/orders-print.php?user_id=<?=$_REQUEST['id']?>&id=<?=$result_current_order[0]['id'] ?>"><img src="images/printer_ico.png" alt="PRINT" style="vertical-align: bottom;" /></a> </td>
                </tr> 
                <tr>
                  <td class="body_content-form">&nbsp;</td>
                  <td valign="top"> 
                  	<table border="0" align="left" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                        <td align="left" valign="middle" class="body_tab-middilebg"><a style="text-decoration: none;" target="_blank" href="<?=$general_func->site_url?>order-listing.php?modify_user=<?=intval($_REQUEST['id'])?>"><input name="back"  type="button" class="submit1" value="Modify/Cancel/Hold Order" /> </a></td>
                        <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                      </tr>
                    </table>
                  	 </td>
                </tr> 
                               
              </table>
		<?php }?>
            	
            </td>
          </tr>
          <tr>
            	<td width="20" align="left"  colspan="4" valign="top">&nbsp;</td>
            </tr>
            <tr>
            	<td width="20" align="left" valign="top"></td>
            	<td width="20" align="left"  colspan="3" valign="top" style="padding-top: 10px;">
            		<strong >Payment/order History</strong>
            		
            		<table width="800" align="left" border="0" cellpadding="5" cellspacing="1" style="padding-top: 5px;">
            			<tr>
            				<td class="table_heading">Order No </td>
            				<td class="table_heading" align="center">Payment Week </td>
            				<td class="table_heading" align="center">Payment Date </td>
            				<td class="table_heading" align="center">Order Amount </td>
            				<td class="table_heading">&nbsp; </td>
            			</tr>
            			<?php
            			$sql_payment = "select order_id,order_amount,week_start_date,week_end_date,payment_date from  payment where user_id ='" . intval($_REQUEST['id']) . "' and order_status=1 order by payment_date DESC";
						$result_payment = $db -> fetch_all_array($sql_payment);
						$total_payment = count($result_payment);
						if( $total_payment > 0){
							 for($p=0; $p <$total_payment; $p++){?>
							 <tr>
            					<td class="table_content-blue">
            						<?php
            						if(strtotime($result_payment[$p]['week_start_date']) == strtotime($first_date_of_the_current_week)){
										echo '<img src="images/small_current_week_order.png" alt="" style="vertical-align: bottom;" />';	
									}else if(strtotime($result_payment[$p]['week_start_date']) == strtotime($first_date_of_the_last_week)){
										echo '<img src="images/small_last_week_order.png" alt="" style="vertical-align: bottom;" />';	
									}	
            						?>             						
            						FNF - A000<?=$result_payment[$p]['order_id'] ?> </td>
            					<td class="table_content-blue" align="center"> <?=date("jS M, Y ", strtotime($result_payment[$p]['week_start_date'])) ?> -- <?=date("jS M, Y ", strtotime($result_payment[$p]['week_end_date'])) ?> </td>
            					<td class="table_content-blue" align="center"><?=date("jS M, Y ", strtotime($result_payment[$p]['payment_date'])) ?></td>
            					<td class="table_content-blue" align="center">$<?=$result_payment[$p]['order_amount'] ?></b> p/w </td>
            					<td class="table_content-blue" align="center"><a target="_blank" href="<?=$general_func->admin_url?>users/orders-print.php?user_id=<?=$_REQUEST['id']?>&id=<?=$result_payment[$p]['order_id'] ?>"><img src="images/printer_ico.png" alt="PRINT" style="vertical-align: bottom;" /></a></td>
            				</tr>								 	
							<?php }	
							
						}else{?>
							<tr>
                				<td colspan="5" align="center" bgcolor="#f5f7fa" valign="middle" height="50" class="message_error">Sorry, no payment has been made yet!</td>
              				</tr>
							
						<?php }
						
            			?>
            			
            			
            		</table>
            		
            	</td>
            </tr>	
            
            <tr>
            	<td width="20" align="left"  colspan="4" valign="top">&nbsp;</td>
            </tr>
            <tr>
            	<td width="20" align="left" valign="top"></td>
            	<td width="20" align="left"  colspan="3" valign="top" style="padding-top: 10px; color: #FF0000;" >
            		<strong >Failed or Not yet Processed Payment/order History</strong>
            		
            		<table width="800" align="left" border="0" cellpadding="5" cellspacing="1" style="padding-top: 5px;">
            			<tr>
            				<td class="table_heading">Order No </td>
            				<td class="table_heading" align="center">Payment Week </td>
            				<td class="table_heading" align="center">Payment Date </td>
            				<td class="table_heading" align="center">Order Amount </td>
            				<td class="table_heading">&nbsp; </td>
            			</tr>
            			<?php
            			$sql_payment = "select order_id,order_amount,week_start_date,week_end_date,payment_date from  payment where user_id ='" . intval($_REQUEST['id']) . "' and order_status=0 order by payment_date DESC";
						$result_payment = $db -> fetch_all_array($sql_payment);
						$total_payment = count($result_payment);
						if( $total_payment > 0){
							 for($p=0; $p <$total_payment; $p++){?>
							 <tr>
            					<td class="table_content-blue">
            						<?php
            						if(strtotime($result_payment[$p]['week_start_date']) == strtotime($first_date_of_the_current_week)){
										echo '<img src="images/small_current_week_order.png" alt="" style="vertical-align: bottom;" />';	
									}else if(strtotime($result_payment[$p]['week_start_date']) == strtotime($first_date_of_the_last_week)){
										echo '<img src="images/small_last_week_order.png" alt="" style="vertical-align: bottom;" />';	
									}	
            						?>             						
            						FNF - A000<?=$result_payment[$p]['order_id'] ?> </td>
            					<td class="table_content-blue" align="center"> <?=date("jS M, Y ", strtotime($result_payment[$p]['week_start_date'])) ?> -- <?=date("jS M, Y ", strtotime($result_payment[$p]['week_end_date'])) ?> </td>
            					<td class="table_content-blue" align="center"><?=date("jS M, Y ", strtotime($result_payment[$p]['payment_date'])) ?></td>
            					<td class="table_content-blue" align="center">$<?=$result_payment[$p]['order_amount'] ?></b> p/w </td>
            					<td class="table_content-blue" align="center"><a target="_blank" href="<?=$general_func->admin_url?>users/orders-print.php?user_id=<?=$_REQUEST['id']?>&id=<?=$result_payment[$p]['order_id'] ?>"><img src="images/printer_ico.png" alt="PRINT" style="vertical-align: bottom;" /></a></td>
            				</tr>								 	
							<?php }	
							
						}else{?>
							<tr>
                				<td colspan="5" align="center" bgcolor="#f5f7fa" valign="middle" height="50" class="message_error">Sorry, no payment has been made yet!</td>
              				</tr>
							
						<?php }
						
            			?>
            			
            			
            		</table>
            		
            	</td>
            </tr>	
            <tr>
            	<td width="20" align="left"  colspan="4" valign="top">&nbsp;</td>
            </tr>	
           <tr>
            <td colspan="4" height="30" align="center"><table width="879" border="0" align="center" cellpadding="0" cellspacing="0">
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
            <td colspan="4" height="30"></td>
          </tr>
            
        </table>
     </td>
  </tr>
</table>
<?php
include("../foot.htm");
?>
