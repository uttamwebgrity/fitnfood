<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";

$original=$path_depth . "eating_schedule/";

if(isset($_POST['enter']) && $_POST['enter']=="yes" && trim($_POST['login_form_id'])==$_SESSION['login_form_id']){	
		
	$data=array();
	$data['home_page_heading']=$security_validator->sanitize(trim($_POST['home_page_heading']));
	$data['home_page_content']=$security_validator->sanitize(trim($_POST['home_page_content']));
	$data['home_page_meal_plan_category_heading']=$security_validator->sanitize(trim($_POST['home_page_meal_plan_category_heading']));
	
	
	$data['get_started_page_heading']=$security_validator->sanitize(trim($_POST['get_started_page_heading']));
	$data['get_started_page_content']=$security_validator->sanitize(trim($_POST['get_started_page_content']));
	
	$data['select_meal_plan_page_left_heading']=$security_validator->sanitize(trim($_POST['select_meal_plan_page_left_heading']));
	$data['select_meal_plan_page_left_content']=$security_validator->sanitize(trim($_POST['select_meal_plan_page_left_content']));
	$data['select_meal_plan_page_right_heading']=$security_validator->sanitize(trim($_POST['select_meal_plan_page_right_heading']));
	$data['select_meal_plan_page_right_content']=$security_validator->sanitize(trim($_POST['select_meal_plan_page_right_content']));
		
	$data['customize_meal_plan_page_left_heading']=$security_validator->sanitize(trim($_POST['customize_meal_plan_page_left_heading']));
	$data['customize_meal_plan_page_left_content']=$security_validator->sanitize(trim($_POST['customize_meal_plan_page_left_content']));
	$data['customize_meal_plan_page_right_heading']=$security_validator->sanitize(trim($_POST['customize_meal_plan_page_right_heading']));
	$data['customize_meal_plan_page_right_content']=$security_validator->sanitize(trim($_POST['customize_meal_plan_page_right_content']));
	$data['customize_meal_plan_page_alert']=$security_validator->sanitize(trim($_POST['customize_meal_plan_page_alert']));
		
	
	$data['payment_page_heading']=$security_validator->sanitize(trim($_POST['payment_page_heading']));	
	$data['set_meal_plan_modification']=$security_validator->sanitize(trim($_POST['set_meal_plan_modification']));
	$data['date_modified']=$current_date_time;		
	
	$db->query_update("dynamic_pages",$data,"id=1");	
	
	if($_FILES['meal_price_variations_chart']['size'] >0 && $general_func->valid_file_type($_FILES["meal_price_variations_chart"]["name"],$_FILES["meal_price_variations_chart"]["type"])){
								
		@unlink($original.$_REQUEST['meal_price_variations_chart']);
							
		$uploaded_name=array();					
		$userfile_name=$_FILES['meal_price_variations_chart']['name'];
		$userfile_tmp= $_FILES['meal_price_variations_chart']['tmp_name'];
		$userfile_size=$_FILES['meal_price_variations_chart']['size'];
		$userfile_type= $_FILES['meal_price_variations_chart']['type'];
									
		$path=time()."_".$general_func->remove_space_by_hypen($security_validator->sanitize_filename($userfile_name));	
		$img=$original.$path;
		move_uploaded_file($userfile_tmp, $img) or die();
									
		$uploaded_name['meal_price_variations_chart']=$path;
		$db->query_update("dynamic_pages",$uploaded_name,"id=1");
	}
	
	$_SESSION['msg']=" Dynamic page  content successfully updated!";				
	$general_func->header_redirect($_SERVER['PHP_SELF']);
	
}else{
	$sql="select * from dynamic_pages where id=1 limit 1";
	$result=$db->fetch_all_array($sql);
		
	$home_page_heading=$result[0]['home_page_heading'];
	$home_page_content=$result[0]['home_page_content'];
	$home_page_meal_plan_category_heading=$result[0]['home_page_meal_plan_category_heading'];
	
	
	$get_started_page_heading=$result[0]['get_started_page_heading'];
	$get_started_page_content=$result[0]['get_started_page_content'];
	
	$select_meal_plan_page_left_heading=$result[0]['select_meal_plan_page_left_heading'];
	$select_meal_plan_page_left_content=$result[0]['select_meal_plan_page_left_content'];
	$select_meal_plan_page_right_heading=$result[0]['select_meal_plan_page_right_heading'];
	$select_meal_plan_page_right_content=$result[0]['select_meal_plan_page_right_content'];
	
	$customize_meal_plan_page_left_heading=$result[0]['customize_meal_plan_page_left_heading'];
	$customize_meal_plan_page_left_content=$result[0]['customize_meal_plan_page_left_content'];
	$customize_meal_plan_page_right_heading=$result[0]['customize_meal_plan_page_right_heading'];
	$customize_meal_plan_page_right_content=$result[0]['customize_meal_plan_page_right_content'];	
	$customize_meal_plan_page_alert=$result[0]['customize_meal_plan_page_alert'];	
	
	
	
	$payment_page_heading=$result[0]['payment_page_heading'];		
	
	$set_meal_plan_modification=$result[0]['set_meal_plan_modification'];
	$meal_price_variations_chart=$result[0]['meal_price_variations_chart'];		
		
}


?>
<script language="javascript" type="text/javascript"> 
function validate(){
	if(!validate_text(document.ff.home_page_heading,1,"Enter home page heading"))
		return false;
	if(!validate_text(document.ff.home_page_content,1,"Enter home page content"))
		return false;
		
	if(!validate_text(document.ff.home_page_meal_plan_category_heading,1,"Enter home page meal plan category heading"))
		return false;	
		
		
	if(!validate_text(document.ff.get_started_page_heading,1,"Enter get started page heading"))
		return false;
	if(!validate_text(document.ff.get_started_page_content,1,"Enter get started page content"))
		return false;		
		
	if(!validate_text(document.ff.select_meal_plan_page_left_heading,1,"Enter select meal plan page left side heading"))
		return false;
	if(!validate_text(document.ff.select_meal_plan_page_left_content,1,"Enter select meal plan page left side content"))
		return false;
	if(!validate_text(document.ff.select_meal_plan_page_right_heading,1,"Enter select meal plan page right side heading"))
		return false;
	if(!validate_text(document.ff.select_meal_plan_page_right_content,1,"Enter select meal plan page right side content"))
		return false;		
			
	if(!validate_text(document.ff.customize_meal_plan_page_left_heading,1,"Enter customize meal plan page left side heading"))
		return false;
	if(!validate_text(document.ff.customize_meal_plan_page_left_content,1,"Enter customize meal plan page left side content"))
		return false;
	if(!validate_text(document.ff.customize_meal_plan_page_right_heading,1,"Enter customize meal plan page right side heading"))
		return false;
	if(!validate_text(document.ff.customize_meal_plan_page_right_content,1,"Enter customize meal plan page right side content"))
		return false;		
	if(!validate_text(document.ff.customize_meal_plan_page_alert,1,"Enter If customers coming from admin created/system defined meal section"))
		return false;	
	
	
	
	if(!validate_text(document.ff.payment_page_heading,1,"Enter payment page heading"))
		return false;	
		
	if(!validate_text(document.ff.set_meal_plan_modification,1,"Enter set meal plan modification text"))
		return false;	
			
		
		
	
}

</script>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg">Modify Dynamic Pages Content</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg"><form enctype="multipart/form-data" method="post" action="<?=$_SERVER['PHP_SELF']?>"  name="ff" onsubmit="return validate()">
        <input type="hidden" name="enter" value="yes" />        
         <input type="hidden" name="login_form_id" value="<?=$_SESSION['login_form_id']?>" />
         <input type="hidden" name="meal_price_variations_chart" value="<?=$meal_price_variations_chart?>" />
         
        <table width="986" border="0" align="left" cellpadding="0" cellspacing="0">
         
          <?php if(isset($_SESSION['msg']) && trim($_SESSION['msg']) != NULL){?>
          <tr>
            <td colspan="2" class="message_error"><?=$_SESSION['msg'];$_SESSION['msg']=""; ?></td>
          </tr>
          <tr>
            <td colspan="2" class="body_content-form" height="30"></td>
          </tr>
          <?php  } ?>
          
          <tr>
          	 <td width="76" align="left" valign="top"></td>
          	 <td width="797" valign="top" class="htext" align="center">
          	 <table width="100%" border="0" cellspacing="0" cellpadding="10">
          	 	<tr>
          	 		<td  align="center" style="line-height: 20px;">
          	 			<p>Following things will not be accepted in the content</p>
                        <ul style="width:200px; text-align:left;">
          	 				<li>Javascript</li>
          	 				<li>HTML tags</li>
          	 				<li>CSS tags</li>
          	 				<li>Comments</li>          	 				
          	 			</ul>          	 		
          	 			
          	 		</td>
          	 		<td>
          	 			</td>
          	 	</tr>          		
			</table>			     			
          			</td>
          
          <td width="113" align="left" valign="top" height="30">&nbsp;</td>
          </tr>	
          <tr>
            <td width="76" align="left" valign="top"></td>
            <td width="797" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="10">
              
                 <tr>
                  <td colspan="2"  class="body_content_form_heading">Home Page</td>
                 
                </tr>
                <tr>
                  <td width="17%" class="body_content-form">Heading:<font class="form_required-field"> *</font></td>
                  <td width="83%" ><input name="home_page_heading" value="<?=$home_page_heading?>" type="text" autocomplete="off" class="form_inputbox" size="73" /> </td>
                </tr>
                
                 <tr>
                  <td  class="body_content-form">Content:<font class="form_required-field"> *</font></td>
                  <td ><textarea name="home_page_content" class="form_textarea" cols="70" rows="6"><?=$home_page_content?></textarea></td>
                </tr>
                 <tr>
                  <td width="17%" class="body_content-form">Meal Plan Category Heading:<font class="form_required-field"> *</font></td>
                  <td width="83%" ><input name="home_page_meal_plan_category_heading" value="<?=$home_page_meal_plan_category_heading?>" type="text" autocomplete="off" class="form_inputbox" size="73" /> </td>
                </tr>
                
                <tr>
                  <td colspan="2"  class="body_content_form_heading" style="padding-top: 20px;">Get Started</td>
                 
                </tr>
                <tr>
                  <td width="17%" class="body_content-form">Heading:<font class="form_required-field"> *</font></td>
                  <td width="83%" ><input name="get_started_page_heading" value="<?=$get_started_page_heading?>" type="text" autocomplete="off" class="form_inputbox" size="73" /> </td>
                </tr>
                
                 <tr>
                  <td  class="body_content-form">Content:<font class="form_required-field"> *</font></td>
                  <td ><textarea name="get_started_page_content" class="form_textarea" cols="70" rows="6"><?=$get_started_page_content?></textarea></td>
                </tr>
                  <tr>
                  <td colspan="2"  class="body_content_form_heading" style="padding-top: 20px;">Select Your Meal Plan</td>
                 
                </tr>                
                
                 <tr>
                  <td  class="body_content-form"><strong> Left Side</strong></td>
                  <td ></td>                 
                </tr>               
               
                <tr>
                  <td width="17%" class="body_content-form">Heading:<font class="form_required-field"> *</font></td>
                  <td width="83%" ><input name="select_meal_plan_page_left_heading" value="<?=$select_meal_plan_page_left_heading?>" type="text" autocomplete="off" class="form_inputbox" size="73" /> </td>
                </tr>
                
                 <tr>
                  <td  class="body_content-form">Content:<font class="form_required-field"> *</font></td>
                  <td ><textarea name="select_meal_plan_page_left_content" class="form_textarea" cols="70" rows="6"><?=$select_meal_plan_page_left_content?></textarea></td>
                </tr> 
                
                 <tr>
                  <td  class="body_content-form"><strong> Right Side</strong></td>
                  <td ></td>                 
                </tr>               
               
                <tr>
                  <td width="17%" class="body_content-form">Heading:<font class="form_required-field"> *</font></td>
                  <td width="83%" ><input name="select_meal_plan_page_right_heading" value="<?=$select_meal_plan_page_right_heading?>" type="text" autocomplete="off" class="form_inputbox" size="73" /> </td>
                </tr>
                
                 <tr>
                  <td  class="body_content-form">Content:<font class="form_required-field"> *</font></td>
                  <td ><textarea name="select_meal_plan_page_right_content" class="form_textarea" cols="70" rows="6"><?=$select_meal_plan_page_right_content?></textarea></td>
                </tr> 
                
                
                
                 <tr>
                  <td colspan="2"  class="body_content_form_heading" style="padding-top: 20px;">Customize your Meal plan</td>
                 
                </tr>                
                
                 <tr>
                  <td  class="body_content-form"><strong> Left Side</strong></td>
                  <td ></td>                 
                </tr>               
               
                <tr>
                  <td width="17%" class="body_content-form">Heading:<font class="form_required-field"> *</font></td>
                  <td width="83%" ><input name="customize_meal_plan_page_left_heading" value="<?=$customize_meal_plan_page_left_heading?>" type="text" autocomplete="off" class="form_inputbox" size="73" /> </td>
                </tr>
                
                 <tr>
                  <td  class="body_content-form">Content:<font class="form_required-field"> *</font></td>
                  <td ><textarea name="customize_meal_plan_page_left_content" class="form_textarea" cols="70" rows="6"><?=$customize_meal_plan_page_left_content?></textarea></td>
                </tr> 
                
                 <tr>
                  <td  class="body_content-form"><strong> Right Side</strong></td>
                  <td ></td>                 
                </tr>               
               
                <tr>
                  <td width="17%" class="body_content-form">Heading:<font class="form_required-field"> *</font></td>
                  <td width="83%" ><input name="customize_meal_plan_page_right_heading" value="<?=$customize_meal_plan_page_right_heading?>" type="text" autocomplete="off" class="form_inputbox" size="73" /> </td>
                </tr>
                
                 <tr>
                  <td  class="body_content-form">Content:<font class="form_required-field"> *</font></td>
                  <td ><textarea name="customize_meal_plan_page_right_content" class="form_textarea" cols="70" rows="6"><?=$customize_meal_plan_page_right_content?></textarea></td>
                </tr>      
                <tr>
                  <td  class="body_content-form" valign="top">Upload Meal Price Variations Chart: </td>
                  <td  valign="top"> 
                  <input name="meal_price_variations_chart" type="file"   class="form_inputbox" size="55"  /> &nbsp;<?php if(trim($meal_price_variations_chart) != NULL){?>
                    <a target="_blank" href="<?=$general_func->site_url.substr($original,6).$meal_price_variations_chart?>" >VIEW CHART</a>&nbsp;&nbsp;
                    <?php }	?><br />
                   <strong style="padding-bottom: 10px;">(File type should be jpg, png, gif and PDF)</strong> 
                    </td>
                </tr> 
                  <tr>
                  <td  class="body_content-form">If customer coming from admin created/system defined meal section :<font class="form_required-field"> *</font></td>
                  <td ><textarea name="customize_meal_plan_page_alert" class="form_textarea" cols="70" rows="6"><?=$customize_meal_plan_page_alert?></textarea></td>
                </tr>     
                
                <tr>
                  <td colspan="2"  class="body_content_form_heading">Payment Page</td>                 
                </tr>
                <tr>
                  <td width="17%" class="body_content-form">Heading:<font class="form_required-field"> *</font></td>
                  <td width="83%" ><input name="payment_page_heading" value="<?=$payment_page_heading?>" type="text" autocomplete="off" class="form_inputbox" size="73" /> </td>
                </tr>
                  
                
                 <tr>
                  <td colspan="2"  class="body_content_form_heading">Set meal plan modification text</td>                 
                </tr>
                <tr>
                  <td width="17%" class="body_content-form">Content:<font class="form_required-field"> *</font></td>
                  <td width="83%" ><input name="set_meal_plan_modification" value="<?=$set_meal_plan_modification?>" type="text" autocomplete="off" class="form_inputbox" size="73" /> </td>
                </tr>                       
              
            </table></td>
            <td width="113" align="left" valign="top" height="30">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="4" height="30" align="center"><table width="879" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="32%"><table border="0" align="right" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                        <td align="left" valign="middle" class="body_tab-middilebg"><input name="submit" type="submit" class="submit1" value="Update" /></td>
                        <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                      </tr>
                  </table></td>
                  <td width="5%"></td>
                  <td width="63%">&nbsp;</td>
                </tr>
              </table></td>
          </tr>
          <tr>
            <td colspan="4" height="30"></td>
          </tr>
        </table>
      </form></td>
  </tr>
</table>
<?php
include("../foot.htm");
?>