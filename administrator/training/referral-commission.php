<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";
$data=array();
$return_url=$_REQUEST['return_url'];



if(isset($_REQUEST['action']) && $_REQUEST['action']=="VIEW"){
	$sql="select u.*,suburb_name,suburb_postcode,suburb_state,delivery_day,payment_debit_day,order_cutoff_day,order_cutoff_time  from trainers u ";
	$sql .=" left join suburb s on u.suburb_id=s.id ";
	$sql .=" where u.id=" . mysql_real_escape_string(trim($_REQUEST['id'])) . " limit 1";	
	$result=$db->fetch_all_array($sql);
}
$outstanding_amount = $result[0]['total_referral_commission'] - $result[0]['total_referral_commission_paid'];

if(isset($_POST['enter']) && $_POST['enter']=="yes" && trim($_POST['login_form_id'])==$_SESSION['login_form_id']){
	$now_pay=trim($_POST['now_pay']);
	
	if(!$validator->validate_price($now_pay)){
		$_SESSION['msg']="Enter a valid payment amount!";	
	}else if($now_pay > $outstanding_amount){
		$_SESSION['msg']="Now pay amount must not be greater than outstanding amount!";		
	}else{
		mysql_query("INSERT INTO trainers_reference_payment(trainer_id,amount,payment_date) VALUES('" . mysql_real_escape_string(trim($_REQUEST['id'])) . "','" . $now_pay . "',now())");	
		mysql_query("update trainers set total_referral_commission_paid=total_referral_commission_paid + '" . $now_pay . "' where id='" .  mysql_real_escape_string(trim($_REQUEST['id'])) . "'");
		$redirect_to=$general_func->admin_url ."training/referral-commission.php?id=" . $result[0]['id'] . "&action=VIEW&return_url=" . urlencode($return_url);
		$general_func->header_redirect($redirect_to);		
	}
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
          <td align="left" valign="middle" class="body_tab-middilebg">Referral Commission of Trainer '<?=$result[0]['fname']." ".$result[0]['lname']?>' </td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg">
        <table width="900" border="0" align="left" cellpadding="0" cellspacing="0">
         
          <?php if(isset($_SESSION['msg']) && trim($_SESSION['msg']) != NULL){?>
          <tr>
            <td colspan="2" class="message_error"><?=$_SESSION['msg'];$_SESSION['msg']=""; ?></td>
          </tr>
          <tr>
            <td colspan="2" class="body_content-form" height="30"></td>
          </tr>
          <?php  } ?>
            
          
          <tr>
            <td width="20" align="left" valign="top"></td>
            <td width="440" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="8">
                         
                <?php
             	if($outstanding_amount > 0){ ?>
             		 <tr>
			            <td colspan="2" height="30"></td>
			          </tr>
             	  <tr>
                  <td colspan="2"   class="body_content-form" style=" padding-bottom: 10px;"><strong>Make Payment</strong></td>
                 
                  </td>
                </tr>	
                <script>
                function validate_payment(){
                	var frm=document.ff;
                	
                	if(!validate_text(frm.now_pay,1,"Enter payment amount"))
						return false;
                	
                	if(!validate_price(frm.now_pay,1,"Enter a valid payment amount"))
						return false;
                }
                </script>
             		
				<form method="post" action="<?=$_SERVER['PHP_SELF']?>"  name="ff"  onsubmit="return validate_payment()">
        		<input type="hidden" name="enter" value="yes" />
        		<input type="hidden" name="return_url" value="<?=$return_url?>" />	
        		<input type="hidden" name="id" value="<?=mysql_real_escape_string($_REQUEST['id'])?>" />
        		<input type="hidden" name="action" value="<?=mysql_real_escape_string($_REQUEST['action'])?>" />
        		<input type="hidden" name="login_form_id" value="<?=$_SESSION['login_form_id']?>" />
        				
             		 <tr>
                  <td width="30%" class="body_content-form" valign="top">Referral Commission:</td>
                  <td width="50%" valign="top">$<?=$result[0]['total_referral_commission']?></td>
                </tr>
                <tr>
                  <td  class="body_content-form" valign="top">Commission Paid:</td>
                  <td  valign="top">$<?=$result[0]['total_referral_commission_paid']?></td>
                </tr>
                 <tr>
                  <td  class="body_content-form">Outstanding Commission:</td>
                  <td >$<?=number_format($outstanding_amount,2)?></td>
                </tr>
                <tr>
                  <td class="body_content-form">Now Pay ($):<font class="form_required-field"> *</font></td>
                  <td><input name="now_pay" value="" type="text" autocomplete="off" class="form_inputbox" size="10" />
                  </td>
                </tr>
               
                 <tr>
            <td colspan="2" height="30" align="center"><table width="440" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="36%">
                  	<table border="0" align="left" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                        <td align="left" valign="middle" class="body_tab-middilebg">
                        	<input type="button" class="submit1"  name="back" value="Back"  onclick="location.href='<?=$return_url?>'" />
                        	</td>
                        <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                      </tr>
                    </table>
                  	</td>
                  <td width="3%"></td>
                  <td width="61%">
                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                        <td align="left" valign="middle" class="body_tab-middilebg"><input name="submit" type="submit" class="submit1" value="Pay Now" /></td>
                        <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                      </tr>
                    </table>
                    </td>
                </tr>
              </table></td>
          </tr>
            
                 
                		
				</form>			
             			
             		
             	<?php } ?>
                 
                
              </table></td>
               <td width="440" align="left" valign="top">
               	<table width="100%" border="0" cellspacing="0" cellpadding="10">
                        
              </table>
               </td>
           
          </tr>
          
            <tr>
            <td colspan="4" height="30"></td>
          </tr>
          <tr>
            	<td width="20" align="left"  colspan="4" valign="top">&nbsp;</td>
            </tr>
            <tr>
            	<td width="20" align="left" valign="top"></td>
            	<td width="220" align="left"  colspan="3" valign="top" style="padding-top: 10px; float: left;">
            		<strong >Referral Commission History</strong>
            		
            		<table width="600" align="left" border="0" cellpadding="6" cellspacing="1" style="padding-top: 5px;">
            			<tr>
            				<td  width="300" class="table_heading">User/Member Name</td>
            				<td  width="300" class="table_heading" align="center">Commission</td>            				
            			</tr>
            			<?php
            			$sql_payment = "select CONCAT(fname,' ',lname) as name,referral_commission from  trainers_reference r left join users u on r.user_id=u.id where r.refered_code ='" . trim($result[0]['refered_code']) . "' order by fname ASC ";
						$result_payment = $db -> fetch_all_array($sql_payment);
						$total_payment = count($result_payment);
						$total=0.00;
						if( $total_payment > 0){
							 for($p=0; $p <$total_payment; $p++){?>
							 <tr bgcolor="<?=$p%2==0?$general_func->color2:$general_func->color1;?>">
            					<td class="table_content-blue"><?=$result_payment[$p]['name'] ?> </td>
            					<td class="table_content-blue" align="center"> $<?=$result_payment[$p]['referral_commission']?></td>
            					
            				</tr>								 	
							<?php 
							 $total +=$result_payment[$p]['referral_commission'];
							 }	
							
						}
						
            			?>
            			 <tr bgcolor="<?=$p%2==0?$general_func->color2:$general_func->color1;?>">
            					<td class="table_content-blue">&nbsp;</td>
            					<td class="table_content-blue" align="center"><strong>$<?=number_format($total, 2)?></strong></td>
            					
            				</tr>	
            			
            			
            		</table>
            		
            	</td>
            </tr>	
             <tr>
            <td colspan="4" height="30"></td>
          </tr>
          
          <tr>
            	<td width="20" align="left"  colspan="4" valign="top">&nbsp;</td>
            </tr>
            <tr>
            	<td width="20" align="left" valign="top"></td>
            	<td width="220" align="left"  colspan="3" valign="top" style="padding-top: 10px; float: left;">
            		<strong >Referral Commission Payment History</strong>
            		
            		<table width="600" align="left" border="0" cellpadding="6" cellspacing="1" style="padding-top: 5px;">
            			<tr>
            				<td width="300" class="table_heading">Payment Date</td>
            				<td  width="300" class="table_heading" align="center">Paid Amount</td>            				
            			</tr>
            			<?php
            			$sql_payment = "select amount,payment_date from trainers_reference_payment where trainer_id = '" . $result[0]['id'] . "' order by payment_date DESC";
						$result_payment = $db -> fetch_all_array($sql_payment);
						$total_payment = count($result_payment);
						$total=0.00;
						if( $total_payment > 0){
							 for($p=0; $p <$total_payment; $p++){?>
							 <tr bgcolor="<?=$p%2==0?$general_func->color2:$general_func->color1;?>">
            					<td class="table_content-blue"><?=date("jS M, Y",strtotime($result_payment[$p]['payment_date']))?> </td>
            					<td class="table_content-blue" align="center"> $<?=$result_payment[$p]['amount']?></td>
            					
            				</tr>								 	
							<?php 
							 $total +=$result_payment[$p]['amount'];
							 }	
							
						}
						
            			?>
            			 <tr bgcolor="<?=$p%2==0?$general_func->color2:$general_func->color1;?>">
            					<td class="table_content-blue">&nbsp;</td>
            					<td class="table_content-blue" align="center"><strong>$<?=number_format($total, 2)?></strong></td>
            					
            				</tr>	
            			
            			
            		</table>
            		
            	</td>
            </tr>	
             <tr>
            <td colspan="4" height="30"></td>
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