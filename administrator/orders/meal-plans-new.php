<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";


$data=array();
$return_url=$_REQUEST['return_url'];


if(isset($_REQUEST['action']) && $_REQUEST['action']=="EDIT"){		
	$sql="select * from meal_plans where id=" .  intval($_REQUEST['id'])  . " limit 1";
	$result=$db->fetch_all_array($sql);	
	$name=$result[0]['name'];
	$meal_plan_category_id=$result[0]['meal_plan_category_id'];
	$details=$result[0]['details'];
	$no_of_days=$result[0]['no_of_days'];
	$meal_per_day=$result[0]['meal_per_day'];
	$snack_per_day=$result[0]['snack_per_day'];	
	$status=$result[0]['status'];
	
	$button="Update";
}else{
	$name="";
	$meal_plan_category_id="";
	$details="";
	$no_of_days="";
	$meal_per_day="";
	$snack_per_day="";	
	$status=1;	
	
	$button="Add New";
}


if(isset($_POST['enter']) && $_POST['enter']=="yes" && trim($_POST['login_form_id'])==$_SESSION['login_form_id']){		
	$name=filter_var(trim($_REQUEST['name']), FILTER_SANITIZE_STRING);	
	$meal_plan_category_id=intval($_REQUEST['meal_plan_category_id']); 
	$details=filter_var(trim($_REQUEST['details']), FILTER_SANITIZE_STRING); 
	$no_of_days=intval($_REQUEST['no_of_days']); 
	$meal_per_day=intval($_REQUEST['meal_per_day']);
	$snack_per_day=intval($_REQUEST['snack_per_day']);		
	$status=intval($_REQUEST['status']);	
		
	if($_POST['submit']=="Add New"){
		if($db->already_exist_inset("meal_plans","name",$name,"meal_plan_category_id",$meal_plan_category_id)){
			$_SESSION['msg']="Sorry, your specified meal plan is already taken!";
		}else{			
			$data['name']=$name;		
			$data['seo_link']=$general_func->create_seo_link($name);
				
			//*** check whether this name alreay exit ******//
			if($db->already_exist_inset("meal_plans","seo_link",$data['seo_link'])){//******* exit
				$data['seo_link']=$db->max_id("meal_plans","id") + 1 ."-".$data['seo_link'];
			}
			//*********************************************//
			
			$data['meal_plan_category_id']=$meal_plan_category_id;
			$data['details']=$details;			
			$data['no_of_days']=$no_of_days;
			$data['meal_per_day']=$meal_per_day;			
			$data['snack_per_day']=$snack_per_day;				
			$data['status']=$status;
			$data['date_added']=$current_date_time;				
			$inserted_id=$db->query_insert("meal_plans",$data);			
			
			//****************************  upload image ********************************//			
			$_SESSION['msg']="Meal plan successfully created, now choose your meals.";
			
			$general_func->header_redirect($general_func->admin_url ."meals/meal-plans-meal.php?id=" . $inserted_id. "&name=" . urlencode($name) . "&action=mealplanmeal");
		}		

	}else{
		if($db->already_exist_update("meal_plans","id",$_REQUEST['id'],"name",$name,"meal_plan_category_id",$meal_plan_category_id)){
			$_SESSION['msg']="Sorry, your specified meal plan is already taken!";
		}else{
			$data['name']=$name;
			$data['seo_link']=$general_func->create_seo_link($name);			
			//*** check whether this name alreay exit ******//
			if($db->already_exist_update("meal_plans","id",$_REQUEST['id'],"seo_link",$data['seo_link'])){//******* exit
				$data['seo_link']=$_REQUEST['id'] ."-".$data['seo_link'];
			}
			//*********************************************//
			$data['meal_plan_category_id']=$meal_plan_category_id;
			$data['details']=$details;			
			$data['no_of_days']=$no_of_days;
			$data['meal_per_day']=$meal_per_day;
			$data['snack_per_day']=$snack_per_day;			
			$data['status']=$status;			
			$data['date_modified']=$current_date_time;
			$db->query_update("meal_plans",$data,"id='".$_REQUEST['id'] ."'");
			
			
			if($db->affected_rows > 0)
				$_SESSION['msg']="Meal Plan successfully updated!";
			
			$general_func->header_redirect($general_func->admin_url."meals/meal-plans.php");				
		}	
	}
}	
?>

<script type="text/javascript" src="<?=$general_func->site_url?>highslide/highslide.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$general_func->site_url?>highslide/highslide.css" />
<script type="text/javascript">
	hs.graphicsDir = '<?=$general_func->site_url?>highslide/graphics/';
	hs.wrapperClassName = 'wide-border';

function validate(){	
	
	var frm=document.ff;
	if(!validate_text(frm.name,1,"Enter meal plan name"))
		return false;
			
	if(frm.meal_plan_category_id.selectedIndex == 0 ){
		alert("Please choose a meal plan category");
		document.ff.meal_plan_category_id.focus();
		return false;		
	}
	
	if(frm.no_of_days.selectedIndex == 0 ){
		alert("Please choose number of days");
		frm.no_of_days.focus();
		return false;		
	}	
	
	if(frm.meal_per_day.selectedIndex == 0 ){
		alert("Please choose meal per day");
		frm.meal_per_day.focus();
		return false;		
	}	
		
	if(frm.status.selectedIndex == 0 ){
		alert("Please choose a status");
		frm.status.focus();
		return false;		
	}			
}	
</script>			

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg"><?=$button?> Meal Plan</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg"><form method="post" action="<?=$_SERVER['PHP_SELF']?>"  name="ff" enctype="multipart/form-data"  onsubmit="return validate()">
        <input type="hidden" name="enter" value="yes" />
        <input type="hidden" name="id" value="<?=$_REQUEST['id']?>" />
        <input type="hidden" name="return_url" value="<?php echo $_REQUEST['return_url']?>" />
       <input type="hidden" name="photo_name" value="<?=$photo_name?>" />
       <input type="hidden" name="login_form_id" value="<?=$_SESSION['login_form_id']?>" />
      
        <table width="883" border="0" align="left" cellpadding="0" cellspacing="0">
          <tr>
            <td colspan="2" height="30"></td>
          </tr>
          <?php if(isset($_SESSION['msg']) && trim($_SESSION['msg']) != NULL){?>
          <tr>
            <td colspan="2" class="message_error"><?=$_SESSION['msg'];$_SESSION['msg']=""; ?></td>
          </tr>
          <tr>
            <td colspan="2" class="body_content-form" height="30"></td>
          </tr>
          <?php  } ?>
          <tr>
            <td width="73" align="left" valign="top"></td>
            <td width="780" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="10">
                 <tr>
                  <td  class="body_content-form">Meal Plan Name:<font class="form_required-field"> *</font></td>
                  <td width="80%"><input name="name" value="<?=$name?>" type="text" autocomplete="off" class="form_inputbox" size="55" />
                  </td>
                </tr>
                 <tr>
                  <td  class="body_content-form" valign="top">Meal Plan Category:<font class="form_required-field"> *</font></td>
                  <td  valign="top"> <select name="meal_plan_category_id" class="inputbox_select" style="width: 300px; padding: 2px 1px 2px 0px;">                         	
                          <option value="">Select One</option>
                          <?php
                          $sql_cat="select id,name from meal_plan_category order by  display_order + 0 ASC ";
						  $result_cat=$db->fetch_all_array($sql_cat);
						  $total_cat=count($result_cat);
						  
						  for($cat=0; $cat < $total_cat; $cat++){ ?>
						  	<option value="<?=$result_cat[$cat]['id']?>" <?=intval($result_cat[$cat]['id'])==$meal_plan_category_id?'selected="selected"':'';?>><?=$result_cat[$cat]['name']?></option>	
						<?php } ?>
                        </select>
                  </td>
                </tr>
                
                  <tr>
                  <td class="body_content-form">Description:</td>
                  <td ><textarea name="details" class="form_textarea" cols="70" rows="6"><?=$details?></textarea>
                  
                  </td>
                </tr>                 
                 <tr>
                  <td  class="body_content-form" valign="top">Number Of Days?:<font class="form_required-field"> *</font></td>
                  <td  valign="top"> <select name="no_of_days" class="inputbox_select" style="width: 300px; padding: 2px 1px 2px 0px;">                         	
                          <option value="">Select One</option>
                          <option value="5" <?=intval($no_of_days)==5?'selected="selected"':'';?>>5</option>
                          <option value="7" <?=intval($no_of_days)==7?'selected="selected"':'';?>>7</option>
                          
                        </select>
                  </td>
                </tr>
                <tr>
                  <td  class="body_content-form" valign="top">Meals per Day?:<font class="form_required-field"> *</font></td>
                  <td  valign="top"> <select name="meal_per_day" class="inputbox_select" style="width: 300px; padding: 2px 1px 2px 0px;">                         	
                          <option value="">Select One</option>
                          <!--<option value="1" <?=intval($meal_per_day)==1?'selected="selected"':'';?>>1</option>
                          <option value="2" <?=intval($meal_per_day)==2?'selected="selected"':'';?>>2</option>-->
                          <option value="3" <?=intval($meal_per_day)==3?'selected="selected"':'';?>>3</option>
                          <option value="4" <?=intval($meal_per_day)==4?'selected="selected"':'';?>>4</option>
                          <option value="5" <?=intval($meal_per_day)==5?'selected="selected"':'';?>>5</option>
                          <option value="6" <?=intval($meal_per_day)==6?'selected="selected"':'';?>>6</option>
                          
                        </select>
                  </td>
                </tr> 
                 <tr>
                  <td  class="body_content-form" valign="top">Snacks per Day?:</td>
                  <td  valign="top"> <select name="snack_per_day" class="inputbox_select" style="width: 300px; padding: 2px 1px 2px 0px;">                         	
                          <option value="">Select One</option>
                          <option value="1" <?=intval($snack_per_day)==1?'selected="selected"':'';?>>1</option>
                          <option value="2" <?=intval($snack_per_day)==2?'selected="selected"':'';?>>2</option>
                          <option value="3" <?=intval($snack_per_day)==3?'selected="selected"':'';?>>3</option>
                          <option value="4" <?=intval($snack_per_day)==4?'selected="selected"':'';?>>4</option>
                          <option value="5" <?=intval($snack_per_day)==5?'selected="selected"':'';?>>5</option>                         
                          
                        </select>
                  </td>
                </tr>     
                        
                <tr>
                  <td  class="body_content-form" valign="top">Status:<font class="form_required-field"> *</font></td>
                  <td valign="top"><select name="status"  class="inputbox_select" style="width: 100px;" >
                      	<option value="">Choose One</option>
                      	<option value="0" <?=$status==0?'selected="selected"':'';?>> Inactive</option>
                      	<option value="1" <?=$status==1?'selected="selected"':'';?>> Active</option>                   
                    </select>
                    <p>&nbsp; </p></td>
                </tr>
              </table></td>
            <td width="8" align="left" valign="top">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="4" height="30" align="center"><table width="879" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="33%"><table border="0" align="right" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                        <td align="left" valign="middle" class="body_tab-middilebg"><input name="submit" type="submit" class="submit1" value="<?=$button?>" /></td>
                        <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                      </tr>
                    </table></td>
                  <td width="4%"></td>
                  <td width="63%"><?php if($button !="Add New"){?>
                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                        <td align="left" valign="middle" class="body_tab-middilebg">
                        	<input type="button" class="submit1"  name="back" value="Back"  onclick="history.back();" /></td>
                        <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                      </tr>
                    </table>
                    <?php  }else 
							echo "&nbsp;";
						 ?></td>
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
