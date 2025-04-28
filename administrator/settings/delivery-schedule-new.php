<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";


$data=array();

$return_url=$_REQUEST['return_url'];

if(isset($_REQUEST['action']) && $_REQUEST['action']=="EDIT"){
	$sql="select * from suburb where id=" .   intval($_REQUEST['id']) . " limit 1";
	$result=$db->fetch_all_array($sql);
	
	$suburb_name=$result[0]['suburb_name'];	
	$delivery_day=$result[0]['delivery_day'];
	$payment_debit_day=$result[0]['payment_debit_day'];
	$order_cutoff_day=$result[0]['order_cutoff_day'];
	$delivery_cost=$result[0]['delivery_cost'];
	$status=$result[0]['status'];	
	
	list($order_cutoff_time_hour, $order_cutoff_time_minute, $order_cutoff_time_second)=explode(":",$result[0]['order_cutoff_time']);
	
	$order_cutoff_time=$result[0]['order_cutoff_time'];
		
	$button="Update";
}else{
	$suburb_name="";
	$delivery_day="";
	$payment_debit_day="";
	$order_cutoff_day="";
	$order_cutoff_time="";
	$delivery_cost="";
	$status="";	

	$button="Add New";
}


if(isset($_POST['enter']) && $_POST['enter']=="yes" && trim($_POST['login_form_id'])==$_SESSION['login_form_id']){
	
	$delivery_day=intval($_REQUEST['delivery_day']);
	$payment_debit_day=intval($_REQUEST['payment_debit_day']);
	$order_cutoff_day=intval($_REQUEST['order_cutoff_day']);
	$order_cutoff_time_hour=intval($_REQUEST['order_cutoff_time_hour']);
	$order_cutoff_time_minute=intval($_REQUEST['order_cutoff_time_minute']);
	$order_cutoff_time_second=intval($_REQUEST['order_cutoff_time_second']);	
	$delivery_cost=trim($_REQUEST['delivery_cost']);
	$status=intval($_REQUEST['status']);
	
			
	$data['delivery_day']=$delivery_day;
	$data['payment_debit_day']=$payment_debit_day;
	$data['order_cutoff_day']=$order_cutoff_day;
	$data['order_cutoff_time']=$order_cutoff_time_hour . ":" . $order_cutoff_time_minute . ":" . $order_cutoff_time_second;			
	$data['delivery_cost']=$delivery_cost;
	$data['status']=$status;		
		
	$db->query_update("suburb",$data,"id='".$_REQUEST['id'] ."'");
			
	if($db->affected_rows > 0)
		$_SESSION['msg']="Suburb delivery schedule successfully updated!";
				
	$general_func->header_redirect($return_url);		
}	


?>
<script language="javascript" type="text/javascript"> 
function validate(){
	if(document.ff.delivery_day.selectedIndex == 0){
		alert("Please select the suburb delivery day");
		document.ff.delivery_day.focus();
		return false;
	}
	
	if(document.ff.payment_debit_day.selectedIndex == 0){
		alert("Please select the suburb payment debit day");
		document.ff.payment_debit_day.focus();
		return false;
	}
	
	if(document.ff.order_cutoff_day.selectedIndex == 0){
		alert("Please select the suburb cutoff day");
		document.ff.order_cutoff_day.focus();
		return false;
	}
	
	if(parseInt(document.ff.delivery_day.value) <= parseInt(document.ff.payment_debit_day.value)){
		alert("Delivery day must be after the payment debit day");	
		return false;
	}
	
	if(parseInt(document.ff.payment_debit_day.value) <= parseInt(document.ff.order_cutoff_day.value)){
		alert("Payment debit day must be after the cutoff day");	
		return false;
	}	
	
	if(document.ff.order_cutoff_time_hour.selectedIndex == 0){
		alert("Please select the suburb cutoff time hour");
		document.ff.order_cutoff_time_hour.focus();
		return false;
	}
	
	if(document.ff.order_cutoff_time_minute.selectedIndex == 0){
		alert("Please select the suburb cutoff time minute");
		document.ff.order_cutoff_time_minute.focus();
		return false;
	}
	
	if(document.ff.order_cutoff_time_second.selectedIndex == 0){
		alert("Please select the suburb cutoff time second");
		document.ff.order_cutoff_time_second.focus();
		return false;
	}
	if(!validate_price(document.ff.delivery_cost,1,"Enter a valid delivery cost"))
		return false;

}
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg"><?=$button?>  Delivery Schedule</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg">
    <form method="post" action="<?=$_SERVER['PHP_SELF']?>" name="ff" onsubmit="return validate()">
        <input type="hidden" name="enter" value="yes" />
        <input type="hidden" name="id" value="<?=$_REQUEST['id']?>" />
        <input type="hidden" name="return_url" value="<?php echo $_REQUEST['return_url']?>" />
         <input type="hidden" name="login_form_id" value="<?=$_SESSION['login_form_id']?>" />
          <input type="hidden" name="login_form_id" value="<?=$_SESSION['login_form_id']?>" />
        <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td colspan="3" class="body_content-form" height="30"></td>
          </tr>
          <?php if(isset($_SESSION['msg']) && trim($_SESSION['msg']) != NULL){?>
          <tr>
            <td colspan="3" class="message_error"><?=$_SESSION['msg'];$_SESSION['msg']=""; ?></td>
          </tr>
          <tr>
            <td colspan="3" class="body_content-form" height="30"></td>
          </tr>
          <?php  } ?>
          <tr>
            <td align="left" valign="top" colspan="3"><table width="79%" border="0"  align="center" cellspacing="2" cellpadding="6">
                
                
               <tr>
                  <td width="15%" class="body_content-form" valign="top">Suburb:</td>
                  <td width="85%" valign="top"><?=ucwords(strtolower($result[0]['suburb_name'])).", ".$result[0]['suburb_state'].", ".$result[0]['suburb_postcode']?>
                  </td>
                </tr>
                
                <tr>
                  <td width="15%" class="body_content-form" valign="top">Delivery Day:<font class="form_required-field"> *</font></td>
                  <td width="85%" valign="top">
                  	<select name="delivery_day" class="inputbox_select" style="width: 200px;">
                  		<option value="" <?=$delivery_day==""?'selected="selected"':'';?>> Select One</option>
                  		<?php for($i=3; $i <=7; $i++){ ?>
                  			<option value="<?=$i?>" <?=$delivery_day==$i?'selected="selected"':'';?>><?=$general_func->day_name($i)?></option>
							
                  		<?php } ?>
                  	</select>
                  	
                  </td>
                </tr>
                <tr>
                  <td  class="body_content-form" valign="top">Payment Debit Day:<font class="form_required-field"> *</font></td>
                  <td  valign="top">
                  	<select name="payment_debit_day" class="inputbox_select" style="width: 200px;">
                  		<option value="" <?=$payment_debit_day==""?'selected="selected"':'';?>> Select One</option>
                  		<?php for($i=1; $i <=2; $i++){ ?>
                  			<option value="<?=$i?>" <?=$payment_debit_day==$i?'selected="selected"':'';?>><?=$general_func->day_name($i)?></option>
							
                  		<?php } ?>
                  	</select>
                  	
                  </td>
                </tr>
                <tr>
                  <td  class="body_content-form" valign="top">Cutoff Day:<font class="form_required-field"> *</font></td>
                  <td  valign="top">
                  	<select name="order_cutoff_day" class="inputbox_select" style="width: 200px;">
                  		<option value="" <?=$order_cutoff_day==""?'selected="selected"':'';?>> Select One</option>
                  		<?php for($i=1; $i <=2; $i++){ ?>
                  			<option value="<?=$i?>" <?=$order_cutoff_day==$i?'selected="selected"':'';?>><?=$general_func->day_name($i)?></option>
							
                  		<?php } ?>
                  	</select>
                  	
                  </td>
                </tr>
                <tr>
                  <td  class="body_content-form" valign="top">Cutoff Time:<font class="form_required-field"> *</font></td>
                  <td  valign="top">
                  	<select name="order_cutoff_time_hour" class="inputbox_select" style="width: 60px;">
                  		<option value="" <?=$order_cutoff_time_hour==""?'selected="selected"':'';?>> Select One</option>
                  		<?php for($i=0; $i <=23; $i++){ 
                  			$disp=$i <10? '0'.$i:$i;
                  			?>
                  			<option value="<?=$i?>" <?=$order_cutoff_time_hour==$i?'selected="selected"':'';?>><?=$disp?></option>
							
                  		<?php } ?>
                  	</select>
                  	&nbsp;&nbsp;
                  	<select name="order_cutoff_time_minute" class="inputbox_select" style="width: 60px;">
                  		<option value="" <?=$order_cutoff_time_minute==""?'selected="selected"':'';?>> Select One</option>
                  		<?php for($i=0; $i <=59; $i++){ 
                  			$disp=$i <10? '0'.$i:$i;
                  			?>
                  			<option value="<?=$i?>" <?=$order_cutoff_time_minute==$i?'selected="selected"':'';?>><?=$disp?></option>
							
                  		<?php } ?>
                  	</select>
                  	&nbsp;&nbsp;
                  	<select name="order_cutoff_time_second" class="inputbox_select" style="width: 60px;">
                  		<option value="" <?=$order_cutoff_time_second==""?'selected="selected"':'';?>> Select One</option>
                  		<?php for($i=0; $i <=59; $i++){ 
                  			$disp=$i <10? '0'.$i:$i;
                  			?>
                  			<option value="<?=$i?>" <?=$order_cutoff_time_second==$i?'selected="selected"':'';?>><?=$disp?></option>
							
                  		<?php } ?>
                  	</select>
                  	
                  </td>
                </tr>
                <tr>
                  <td  class="body_content-form">Deliver Cost ($):</td>
                  <td><input name="delivery_cost" value="<?=$delivery_cost?>" type="text" autocomplete="off" class="form_inputbox" size="36" />
                  </td>
                </tr>
                
                 <tr>
                  <td  class="body_content-form" valign="top">Status:<font class="form_required-field"> *</font></td>
                  <td  valign="top"> <select name="status" class="inputbox_select" style="width: 205px; padding: 2px 1px 2px 0px;">                         	
                          <option value="">Select One</option>
                          <option value="0" <?=intval($status)==0?'selected="selected"':'';?>>Inactive</option>
                          <option value="1" <?=intval($status)==1?'selected="selected"':'';?>>Active</option>                          
                        </select>
                  </td>
                </tr>
                <tr>
                  <td colspan="2" class="body_content-form" height="10"></td>
                </tr>
                <tr>
                  <td width="15%" class="body_content-form">&nbsp;</td>
                  <td width="85%"><table width="261" border="0" align="left" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="41%"><table border="0" align="left" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="submit" type="submit" class="submit1" value="<?=$button?>" /></td>
                              <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                            </tr>
                          </table></td>
                        <td width="10%">&nbsp;</td>
                        <td width="49%"><table border="0" align="left" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="back" onclick="location.href='<?=$general_func->admin_url?>settings/delivery-schedule.php'"  type="button" class="submit1" value="Back" /></td>
                              <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                            </tr>
                          </table></td>
                      </tr>
                    </table></td>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td height="32" align="left" valign="top">&nbsp;</td>
            <td align="left" valign="top">&nbsp;</td>
            <td align="left" valign="top">&nbsp;</td>
          </tr>
        </table>
      </form></td>
  </tr>
</table>
<?php
include("../foot.htm");
?>