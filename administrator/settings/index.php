<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";

if(isset($_POST['enter']) && $_POST['enter']=="yes" && trim($_POST['login_form_id'])==$_SESSION['login_form_id']){
		
	$data=array();
	$data['option_value']=trim($_REQUEST['get_started_video']);
	$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='get_started_video'");	
		
	$data=array();
	$data['option_value']=trim($_REQUEST['pickup_cost']);
	$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='pickup_cost'");	
		
		
	$data=array();
	$data['option_value']=$security_validator->sanitize(trim($_REQUEST['get_started_content']));
	$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='get_started_content'");	
							
	
	$data=array();
	$data['option_value']=filter_var(trim($_REQUEST['site_title']), FILTER_SANITIZE_STRING);	
	$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='site_title'");
	
	$data=array();
	$data['option_value']=filter_var(trim($_REQUEST['delivery_email_reminder_status']), FILTER_SANITIZE_STRING);	
	$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='delivery_email_reminder_status'");
			
	if($security_validator->validate(trim($_REQUEST['trainer_referral_commission']),'float') == true){
		$data=array();
		$data['option_value']=$_REQUEST['trainer_referral_commission'];
		$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='trainer_referral_commission'");
	}	
	
	if($security_validator->validate(trim($_REQUEST['gym_referral_commission']),'float') == true){
		$data=array();
		$data['option_value']=$_REQUEST['gym_referral_commission'];
		$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='gym_referral_commission'");
	}
	
	
	if($security_validator->validate(trim($_REQUEST['trainer_training_commission']),'float') == true){
		$data=array();
		$data['option_value']=$_REQUEST['trainer_training_commission'];
		$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='trainer_training_commission'");
	}	
	
	
	if($security_validator->validate(trim($_REQUEST['meal_per_day_min']),'integer') == true){
		$data=array();
		$data['option_value']=trim($_REQUEST['meal_per_day_min']);
		$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='meal_per_day_min'");
	}
	
	if($security_validator->validate(trim($_REQUEST['meal_per_day_max']),'integer') == true){
		$data=array();
		$data['option_value']=trim($_REQUEST['meal_per_day_max']);
		$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='meal_per_day_max'");
	}	
	
	if($security_validator->validate(trim($_REQUEST['gym_training_commission']),'float') == true){
		$data=array();
		$data['option_value']=$_REQUEST['gym_training_commission'];
		$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='gym_training_commission'");
	}
	$options = array(
    	'options' => array(
        'default' => -3,         
        'min_range' => 0
    	),
    	'flags' => FILTER_FLAG_ALLOW_OCTAL,
	);
	
	if(filter_var(trim($_REQUEST['meal_plan_amout_for_training_cost']), FILTER_VALIDATE_FLOAT, $options) != -3){
		$data=array();
		$data['option_value']=$_REQUEST['meal_plan_amout_for_training_cost'];
		$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='meal_plan_amout_for_training_cost'");
	}
	
	if($security_validator->validate(trim($_REQUEST['admin_recoed_per_page']),'integer') == true){
		$data=array();
		$data['option_value']=$_REQUEST['admin_recoed_per_page'];
		$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='admin_recoed_per_page'");
	}
	
	if($security_validator->validate(trim($_REQUEST['front_end_recoed_per_page']),'integer') == true){
		$data=array();
		$data['option_value']=$_REQUEST['front_end_recoed_per_page'];
		$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='front_end_recoed_per_page'");
	}
	
	if($security_validator->validate(trim($_REQUEST['shopping_discount']),'float') == true){
		$data=array();
		$data['option_value']=$_REQUEST['shopping_discount'];
		$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='shopping_discount'");
	}
	if($security_validator->validate(trim($_REQUEST['delivery_email_reminder']),'integer') == true){
		$data=array();
		$data['option_value']=$_REQUEST['delivery_email_reminder'];
		$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='delivery_email_reminder'");
	}

	if($security_validator->validate(trim($_REQUEST['email']),'email') == true){
		$data=array();
		$data['option_value']=$_REQUEST['email'];
		$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='email'");
	}
	
	
	if($security_validator->validate(trim($_REQUEST['phone']),'integer') == true){
		$data=array();
		$data['option_value']=$_REQUEST['phone'];
		$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='phone'");
	}
		
		
	$data=array();
	$data['option_value']=filter_var(trim($_REQUEST['site_address']), FILTER_SANITIZE_STRING);
	$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='site_address'");	


		
	$data=array();
	$data['option_value']=filter_var(trim($_REQUEST['global_meta_title']), FILTER_SANITIZE_STRING);
	$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='global_meta_title'");
	
	$data=array();
	$data['option_value']=filter_var(trim($_REQUEST['global_meta_keywords']), FILTER_SANITIZE_STRING); 
	$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='global_meta_keywords'");
	
	$data=array();
	$data['option_value']=filter_var(trim($_REQUEST['global_meta_description']), FILTER_SANITIZE_STRING);
	$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='global_meta_description'");
	
	
	
	
	
	if($security_validator->validate(trim($_REQUEST['twitter']),'url') == true){
		$data=array();
		$data['option_value']=filter_var(trim($_REQUEST['twitter']), FILTER_SANITIZE_URL);
		$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='twitter'");
	}
	if($security_validator->validate(trim($_REQUEST['linkedin']),'url') == true){
		$data=array();
		$data['option_value']=filter_var(trim($_REQUEST['linkedin']), FILTER_SANITIZE_URL);
		$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='linkedin'");
	}	
	
	if($security_validator->validate(trim($_REQUEST['facebook']),'url') == true){
		$data=array();
		$data['option_value']=filter_var(trim($_REQUEST['facebook']), FILTER_SANITIZE_URL);
		$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='facebook'");
	}	
	
	if($security_validator->validate(trim($_REQUEST['instagram']),'url') == true){
		$data=array();
		$data['option_value']=filter_var(trim($_REQUEST['instagram']), FILTER_SANITIZE_URL);
		$db->query_update("tbl_options",$data,"admin_admin_id=1 and option_name='instagram'");
	}
	
	$_SESSION['msg']="General settings successfully saved.";
	$general_func->header_redirect($_SERVER['PHP_SELF']);
}




$sql="select option_name,option_value from tbl_options where admin_admin_id=1 and (option_name='site_title' or ";
$sql .=" option_name='meal_plan_amout_for_training_cost' or option_name='delivery_email_reminder_status' or option_name='trainer_training_commission' or option_name='gym_training_commission' or ";
$sql .=" option_name='trainer_referral_commission' or option_name='gym_referral_commission' or option_name='admin_recoed_per_page' or option_name='front_end_recoed_per_page' or ";
$sql .=" option_name='linkedin' or option_name='twitter' or option_name='instagram' or option_name='facebook' or option_name='shopping_discount' or option_name='delivery_email_reminder' or option_name='email' or ";
$sql .=" option_name='meal_per_day_min' or  option_name='meal_per_day_max' or option_name='pickup_cost' or  option_name='get_started_video' or option_name='get_started_content' or option_name='phone' or option_name='site_address' or option_name='global_meta_title' or option_name='global_meta_keywords' or option_name='global_meta_description')";


$result=$db->fetch_all_array($sql);

if(count($result) > 0){
	for($i=0; $i <count($result); $i++){
		$$result[$i]['option_name']=trim($result[$i]['option_value']);
	}
}else{
	$site_title="";
	$delivery_email_reminder="";
	$delivery_email_reminder_status=0;	
	$trainer_referral_commission="";
	$gym_referral_commission="";
	$trainer_training_commission="";
	$gym_training_commission="";
	$meal_plan_amout_for_training_cost="";
	
	$get_started_video="";
	$get_started_content="";
	
	$shopping_discount="";
	
	$email="";	
	
	$admin_recoed_per_page="";
	$front_end_recoed_per_page="";	
	
	$global_meta_title="";
	$global_meta_keywords="";
	$global_meta_description="";
	$site_address="";
	$phone="";
	$pickup_cost="";
	
	
	$twitter="";
	$linkedin="";
	$facebook="";
	
}

?>
<script language="javascript" type="text/javascript"> 
function validate(){
	if(!validate_text(document.ff.site_title,1,"Site title should not be blank"))
		return false;
	
	
		
	var meal_per_day_min=parseInt($('input[name="meal_per_day_min"]').val());
	var meal_per_day_max=parseInt($('input[name="meal_per_day_max"]').val());
	
	if(meal_per_day_min == 0 || meal_per_day_max == 0){
		alert("Meals per day from and to value must be greater than zero");
		return false;
	}
	
	if(meal_per_day_max < meal_per_day_min){
		alert("Meals per day to value must be greater than from value");
		return false;
	}
						
	if(!validate_text(document.ff.email,1,"Email should not be blank"))
		return false;	
		
	if(!validate_text(document.ff.pickup_cost,1,"Enter pickup cost"))
		return false;	
	 
	if(!validate_price(document.ff.pickup_cost,1,"Enter a valid pickup cost"))
			return false;				
			
	if(!validate_integer(document.ff.admin_recoed_per_page,1,"Admin recoed per page should not be blank and must be a valid [0-9] number"))
		return false;	
	
	
	if(!validate_text(document.ff.global_meta_title,1,"Global meta title should not be blank"))
		return false;		
			
}
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  
    <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg">General Settings</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
    
  <tr>
    <td align="left" valign="top" class="body_whitebg"><form method="post" action="<?=$_SERVER['PHP_SELF']?>" name="ff" onsubmit="return validate()">
    <input type="hidden" name="enter" value="yes" />
    <input type="hidden" name="login_form_id" value="<?=$_SESSION['login_form_id']?>" /> 
        <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
           <tr>
                  <td colspan="3" class="body_content-form" height="30"></td>
            </tr>
            <?php if(isset($_SESSION['msg']) && trim($_SESSION['msg']) != NULL){?>
			<tr>
                  <td colspan="3" class="message_error"><?=$_SESSION['msg']; $_SESSION['msg']="";?></td>
            </tr>
             <tr>
                  <td colspan="3" class="body_content-form" height="10"></td>
            </tr>
			 <?php  } ?>
            
            
          <tr>
            <td align="left" valign="top" colspan="3"><table width="95%" border="0"  align="center" cellspacing="2" cellpadding="6">
                <tr>
                  <td width="15%" class="body_content-form">Site title: <font class="form_required-field">*</font> </td>
                  <td width="85%"><input name="site_title" type="text" value="<?=$site_title?>" autocomplete="off" class="form_inputbox" size="60" /></td>
                </tr>
               
               <!--<tr>
                  <td width="15%" class="body_content-form">Shopping cart:  </td>
                  <td width="85%"><input name="shopping_discount" type="text" value="<?=$shopping_discount?>" autocomplete="off" class="form_inputbox" size="6" /> % discount for member.</td>
               </tr> 
                <tr>
                  <td width="15%" class="body_content-form">Order email reminder:  </td>
                  <td width="85%"><input name="delivery_email_reminder" type="text" value="<?=$delivery_email_reminder?>" autocomplete="off" class="form_inputbox" size="6" /> days before the cutoff day.</td>
                </tr> -->
                <tr>
                  <td width="15%" class="body_content-form">Order cuttoff day email reminder active?:  </td>
                  <td width="85%"><input name="delivery_email_reminder_status" type="checkbox" value="1" <?=$delivery_email_reminder_status==1?'checked="checked"':'';?> autocomplete="off" class="form_inputbox"  /></td>
                </tr>
                
                
                <tr>
                  <td width="15%" class="body_content-form">Commission on each referral ($):  </td>
                  <td width="85%"><input name="trainer_referral_commission" type="text" value="<?=$trainer_referral_commission?>" autocomplete="off" class="form_inputbox" size="6" /> to the trainer.</td>
                </tr>
                <tr>
                  <td width="15%" class="body_content-form">&nbsp; </td>
                  <td width="85%"><input name="gym_referral_commission" type="text" value="<?=$gym_referral_commission?>" autocomplete="off" class="form_inputbox" size="6" /> to the gym.</td>
                </tr>
                
                 <tr>
                  <td width="15%" class="body_content-form">Amount will be paid for each user ($):  </td>
                  <td width="85%"><input name="trainer_training_commission" type="text" value="<?=$trainer_training_commission?>" autocomplete="off" class="form_inputbox" size="6" /> to the trainer.</td>
                </tr>
                <tr>
                  <td width="15%" class="body_content-form">&nbsp; </td>
                  <td width="85%"><input name="gym_training_commission" type="text" value="<?=$gym_training_commission?>" autocomplete="off" class="form_inputbox" size="6" /> to the gym.</td>
                </tr>
                 <tr>
                  <td width="15%" class="body_content-form">Training cost will be added with the meal plan cost automatically ($):  </td>
                  <td width="85%"><input name="meal_plan_amout_for_training_cost" type="text" value="<?=$meal_plan_amout_for_training_cost?>" autocomplete="off" class="form_inputbox" size="6" /></td>
                </tr>
                
                 <tr>
                  <td width="15%" class="body_content-form">Pickup Location Cost($): <font class="form_required-field">*</font>  </td>
                  <td width="85%"><input name="pickup_cost" type="text" value="<?=$pickup_cost?>" autocomplete="off" class="form_inputbox" size="6" /></td>
                </tr>
                  <tr>
                  <td width="15%" class="body_content-form">Meals per Day: <font class="form_required-field">*</font>  </td>
                  <td width="85%">From: <input name="meal_per_day_min" type="text" value="<?=$meal_per_day_min?>" autocomplete="off" class="form_inputbox" size="6" />&nbsp;&nbsp;&nbsp;&nbsp;
                  	To: <input name="meal_per_day_max" type="text" value="<?=$meal_per_day_max?>" autocomplete="off" class="form_inputbox" size="6" />
                  </td>
                </tr>
               
                
                <tr>
                  <td width="15%" class="body_content-form">Email: <font class="form_required-field">*</font> </td>
                  <td width="85%"><input name="email" type="text" value="<?=$email?>" autocomplete="off" class="form_inputbox" size="60" /></td>
                </tr>
                
                 <tr>
                  <td width="15%" class="body_content-form">Phone:  </td>
                  <td width="85%"><input name="phone" type="text" value="<?=$phone?>" autocomplete="off" class="form_inputbox" size="60" /></td>
                </tr>
                 <tr>
                  <td width="15%" class="body_content-form" valign="top">Site Address:</td>
                  <td width="85%"  valign="top"><textarea name="site_address"  autocomplete="off" class="form_textarea" cols="90" rows="5"><?=$site_address?></textarea></td>
                </tr>
                
                <tr>
                  <td width="15%" class="body_content-form">Facebook: </td>
                  <td width="85%"><input name="facebook" type="text" value="<?=$facebook?>" autocomplete="off" class="form_inputbox" size="60" /></td>
                </tr>
                <tr>
                  <td width="15%" class="body_content-form">Instagram:  </td>
                  <td width="85%"><input name="twitter" type="text" value="<?=$twitter?>" autocomplete="off" class="form_inputbox" size="60" /></td>
                </tr>
             
              <tr>
                  <td width="15%" class="body_content-form">Google Plus:  </td>
                  <td width="85%"><input name="linkedin" type="text" value="<?=$linkedin?>" autocomplete="off" class="form_inputbox" size="60" /></td>
                </tr>
                
                 <tr>
                  <td width="15%" class="body_content-form">You Tube: </td>
                  <td width="85%"><input name="instagram" type="text" value="<?=$instagram?>" autocomplete="off" class="form_inputbox" size="60" /></td>
                </tr> 
                  <tr>
                  <td width="15%" class="body_content-form">Recoed per page:<font class="form_required-field"> * </font></td>
                  <td width="85%"><input name="admin_recoed_per_page" type="text" value="<?=$admin_recoed_per_page?>" autocomplete="off" class="form_inputbox"  size="5"/>&nbsp;&nbsp;<small>(Admin Panel)</small></td>
                </tr>  
                 
                 <tr>
                  <td width="15%" class="body_content-form" valign="top">Get started page video:</td>
                  <td width="85%"  valign="top"><textarea name="get_started_video"  autocomplete="off" class="form_textarea" cols="90" rows="5"><?=$get_started_video?></textarea></td>
                </tr>
                
                  <tr>
                  <td width="15%" class="body_content-form" valign="top">Get started page content:</td>
                  <td width="85%"  valign="top"><textarea name="get_started_content"  autocomplete="off" class="form_textarea" cols="90" rows="5"><?=$get_started_content?></textarea></td>
                </tr>
                 
                 <tr>
                  <td width="15%" class="body_content-form">Global meta title:<font class="form_required-field"> * </font></td>
                  <td width="85%"><input name="global_meta_title" type="text" value="<?=$global_meta_title?>" autocomplete="off" class="form_inputbox"  size="93"/></td>
                </tr>
                
                 <tr>
                  <td width="15%" class="body_content-form" valign="top">Global meta keywords:</td>
                  <td width="85%"  valign="top"><textarea name="global_meta_keywords"  autocomplete="off" class="form_textarea" cols="90" rows="5"><?=$global_meta_keywords?></textarea></td>
                </tr>
                
                 <tr>
                  <td width="15%" class="body_content-form"  valign="top">Global meta description:</td>
                  <td width="85%"  valign="top"><textarea name="global_meta_description"  autocomplete="off" class="form_textarea"  cols="90" rows="5"><?=$global_meta_description?></textarea></td>
                </tr>              
                
                 <tr>
                  <td colspan="2" class="body_content-form" height="10"></td>
                 </tr>
                <tr>
                  <td width="15%" class="body_content-form">&nbsp;</td>
            <td width="85%"><table border="0" align="left" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="button" type="submit" class="submit1" value="Save Changes" /></td>
                              <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                            </tr>
                        </table> </td>
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
