<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";



$small=$path_depth ."meal_main/small/";
$original=$path_depth ."meal_main/";

$data=array();
$return_url=$_REQUEST['return_url'];


if(isset($_GET['now']) && $_GET['now']=="DELETE"){
	$path=$_REQUEST['path'];
	$field=$_REQUEST['field'];
		
	@mysql_query("update meals set $field=' ' where id=" . (int) $_REQUEST['id'] . "");	
	
	@unlink($original.$path);
	@unlink($small.$path);		
	
	
	$redirect_path=basename($_SERVER['PHP_SELF']) . "?id=".$_GET['id']."&action=EDIT&return_url=".$return_url;
	$general_func->header_redirect($redirect_path);	
}

if(isset($_REQUEST['action']) && $_REQUEST['action']=="EDIT"){		
	$sql="select * from meals where id=" .  intval($_REQUEST['id'])  . " limit 1";
	$result=$db->fetch_all_array($sql);	
	$name=$result[0]['name'];
	$details=$result[0]['details'];
	$photo_name=$result[0]['photo_name'];
	$energy=$result[0]['energy'];
	$calories=$result[0]['calories'];
	$protein=$result[0]['protein'];
	$fat_total=$result[0]['fat_total'];
	$carbohydrates=$result[0]['carbohydrates'];
	$carbs_veggies=$result[0]['carbs_veggies'];
	$with_or_without_sauce=$result[0]['with_or_without_sauce'];	
	$meal_category_id=$result[0]['meal_category_id'];
	$snacks=$result[0]['snacks'];
	$show_nutritional_price=$result[0]['show_nutritional_price'];
	$status=$result[0]['status'];
	
	$meal_plan_category_array=array();
	
	$sql_cat="select meal_plan_category_id from meal_plan_category_meals where meal_id=" .  intval($_REQUEST['id'])  . "";
	$result_cat=$db->fetch_all_array($sql_cat);	
	$total_cat=count($result_cat);
	
	for($cat=0; $cat < $total_cat; $cat++){
		$meal_plan_category_array[]=$result_cat[$cat]['meal_plan_category_id'];	
	}
	
	
	$sql_size_price="select * from meals_sizes_prices where meal_id=" .  intval($_REQUEST['id'])  . "";
	$result_size_price=$db->fetch_all_array($sql_size_price);	
	$total_price=count($result_size_price);
	
	$price_of_100g=0;
	$price_of_100g_value="";
	
	$price_of_150g=0;
	$price_of_150g_value="";
	
	$price_of_200g=0;
	$price_of_200g_value="";
	
	for($p=0; $p < $total_price; $p++){
		$checkbox="price_of_".$result_size_price[$p]['meal_size']."g";
		$value="price_of_". $result_size_price[$p]['meal_size'] . "g_value";
				
		$$checkbox=1;
		$$value=$result_size_price[$p]['meal_price'];		
	}
		

	$button="Update";
}else{
	$name="";
	$details="";
	$photo_name="";
	$energy="";
	$calories="";
	$protein="";
	$fat_total="";
	$carbohydrates="";
	$carbs_veggies="";
	$with_or_without_sauce="";	
	$meal_category_id="";
	$snacks=0;
	$status=1;	
	$show_nutritional_price=1;
	
	$price_of_100g=0;
	$price_of_100g_value="";
	
	$price_of_150g=0;
	$price_of_150g_value="";
	
	$price_of_200g=0;
	$price_of_200g_value="";
	
	$meal_plan_category_array=array();
	
	$button="Add New";
}


if(isset($_POST['enter']) && $_POST['enter']=="yes" && trim($_POST['login_form_id'])==$_SESSION['login_form_id']){
	
			
	$name=filter_var(trim($_REQUEST['name']), FILTER_SANITIZE_STRING);	 
	$details=filter_var(trim($_REQUEST['details']), FILTER_SANITIZE_STRING); 
	$energy=filter_var(trim($_REQUEST['energy']), FILTER_SANITIZE_STRING);	
	$calories=filter_var(trim($_REQUEST['calories']), FILTER_SANITIZE_STRING);	
	$protein=filter_var(trim($_REQUEST['protein']), FILTER_SANITIZE_STRING);	
	$fat_total=filter_var(trim($_REQUEST['fat_total']), FILTER_SANITIZE_STRING);	
	$carbohydrates=filter_var(trim($_REQUEST['carbohydrates']), FILTER_SANITIZE_STRING);	
	$carbs_veggies=intval($_REQUEST['carbs_veggies']);
	$with_or_without_sauce=intval($_REQUEST['with_or_without_sauce']);	
	$meal_category_id=intval($_REQUEST['meal_category_id']);
	$meal_plan_category_id=$_REQUEST['meal_plan_category_id'];	
	$snacks=(isset($_REQUEST['snacks']) && intval($_REQUEST['snacks']) == 1)?1:0;
	$show_nutritional_price=(isset($_REQUEST['show_nutritional_price']) && intval($_REQUEST['show_nutritional_price']) == 1)?1:0;		
	$status=intval($_REQUEST['status']);	
		
	if($_POST['submit']=="Add New"){
		if($db->already_exist_inset("meals","name",$name)){
			$_SESSION['msg']="Sorry, your specified meal is already taken!";
		}else{			
			$data['name']=$name;		
			$data['seo_link']=$general_func->create_seo_link($name);
				
			//*** check whether this name alreay exit ******//
			if($db->already_exist_inset("meals","seo_link",$data['seo_link'])){//******* exit
				$data['seo_link']=$db->max_id("meals","id") + 1 ."-".$data['seo_link'];
			}
			//*********************************************//
			
			$data['details']=$details;			
			$data['energy']=$energy;
			$data['calories']=$calories;
			
			$data['protein']=$protein;
			$data['fat_total']=$fat_total;
			$data['carbohydrates']=$carbohydrates;
			$data['carbs_veggies']=$carbs_veggies;
			$data['with_or_without_sauce']=$with_or_without_sauce;			
			$data['meal_category_id']=$meal_category_id;			
			$data['snacks']=$snacks;
			$data['show_nutritional_price']=$show_nutritional_price;
			$data['date_added']=$current_date_time;				
			$inserted_id=$db->query_insert("meals",$data);
					
						
			$flag_category=0;
			$sql_category="INSERT INTO meal_plan_category_meals(meal_id,meal_plan_category_id) VALUES";
			
			$count_category=count($meal_plan_category_id);
			
			for($cat=0; $cat <$count_category; $cat++){
				$sql_category .="('" . $inserted_id . "','" . $meal_plan_category_id[$cat] . "'), ";				
				$flag_category=1;
			}
			
			if($flag_category == 1){
				$sql_category =  substr($sql_category,0,-2).";";
				$db->query($sql_category);	
			}	
		
			
			
			$flag_price=0;
			$sql_prices="INSERT INTO meals_sizes_prices(meal_id,meal_size,meal_price) VALUES";
			
			if(isset($_REQUEST['price_of_100g']) &&  intval($_REQUEST['price_of_100g']) ==1){
				$sql_prices .= "('" . $inserted_id . "','100','" . trim($_REQUEST['price_of_100g_value']) . "' ), ";
				$flag_price=1;				
			}
			
			if(isset($_REQUEST['price_of_150g']) &&  intval($_REQUEST['price_of_150g']) ==1){
				$sql_prices .= "('" . $inserted_id . "','150','" . trim($_REQUEST['price_of_150g_value']) . "' ), ";
				$flag_price=1;					
			}
			if(isset($_REQUEST['price_of_200g']) &&  intval($_REQUEST['price_of_200g']) ==1){
				$sql_prices .= "('" . $inserted_id . "','200','" . trim($_REQUEST['price_of_200g_value']) . "' ), ";
				$flag_price=1;					
			}
			
			if($flag_price == 1){
				$sql_prices =  substr($sql_prices,0,-2).";";
				$db->query($sql_prices);	
			}	
		
		
			//****************************  upload image ********************************//	
			if($_FILES['photo_name']['size'] >0 && $general_func->valid_file_type($_FILES["photo_name"]["name"],$_FILES["photo_name"]["type"])){			
				
				$uploaded_name=array();				
				$userfile_name=$_FILES['photo_name']['name'];
				$userfile_tmp= $_FILES['photo_name']['tmp_name'];
				$userfile_size=$_FILES['photo_name']['size'];
				$userfile_type= $_FILES['photo_name']['type'];
									
				$path=$inserted_id ."_".$general_func->remove_space_by_hypen($security_validator->sanitize_filename($userfile_name));
				$img=$original.$path;
				move_uploaded_file($userfile_tmp, $img) or die();
									
				$uploaded_name['photo_name']=$path;
				$db->query_update("meals",$uploaded_name,'id='.$inserted_id);
									
				list($width, $height) = getimagesize($img);
							
				if($width > 188 || $height > 116){				
					$upload->uploaded_image_resize(188,116,$original,$small,$path);
				}else{
					copy($img,$small.$path); 
				}	
									
				if($width > 640 || $height > 480){
					$upload->uploaded_image_resize(640,480,$original,$original,$path);
				}	
			}		
			//****************************  upload image ********************************//			
			$_SESSION['msg']="Meal successfully created!";
			$general_func->header_redirect($_SERVER['PHP_SELF']);			
		}		

	}else{
		if($db->already_exist_update("meals","id",$_REQUEST['id'],"name",$name)){
			$_SESSION['msg']="Sorry, your specified meal is already taken!";
		}else{		
				
			$data['name']=$name;
			$data['seo_link']=$general_func->create_seo_link($name);			
			//*** check whether this name alreay exit ******//
			if($db->already_exist_update("meals","id",$_REQUEST['id'],"seo_link",$data['seo_link'])){//******* exit
				$data['seo_link']=$_REQUEST['id'] ."-".$data['seo_link'];
			}
			//*********************************************//
			$data['details']=$details;			
			$data['energy']=$energy;
			$data['calories']=$calories;			
			$data['protein']=$protein;
			$data['fat_total']=$fat_total;
			$data['carbohydrates']=$carbohydrates;
			$data['carbs_veggies']=$carbs_veggies;
			$data['with_or_without_sauce']=$with_or_without_sauce;			
			$data['meal_category_id']=$meal_category_id;
			$data['snacks']=$snacks;
			$data['show_nutritional_price']=$show_nutritional_price;
			
			$data['status']=$status;			
			$data['date_modified']=$current_date_time;
			$db->query_update("meals",$data,"id='".$_REQUEST['id'] ."'");
			
			
			$db->query_delete("meal_plan_category_meals","meal_id='".$_REQUEST['id'] ."'");			
			
			$flag_category=0;
			$sql_category="INSERT INTO meal_plan_category_meals(meal_id,meal_plan_category_id) VALUES";
			
			$count_category=count($meal_plan_category_id);
			
			for($cat=0; $cat <$count_category; $cat++){
				$sql_category .="('" . $_REQUEST['id'] . "','" . $meal_plan_category_id[$cat] . "'), ";				
				$flag_category=1;
			}
			
			if($flag_category == 1){
				$sql_category =  substr($sql_category,0,-2).";";
				$db->query($sql_category);	
			}	
								
			
			$db->query_delete("meals_sizes_prices","meal_id='".$_REQUEST['id'] ."'");
			
			
			$flag_price=0;
			$sql_prices="INSERT INTO meals_sizes_prices(meal_id,meal_size,meal_price) VALUES";
			
			if(isset($_REQUEST['price_of_100g']) &&  intval($_REQUEST['price_of_100g']) ==1){
				$sql_prices .= "('" . $_REQUEST['id'] . "','100','" . trim($_REQUEST['price_of_100g_value']) . "' ), ";
				$flag_price=1;				
			}
			
			if(isset($_REQUEST['price_of_150g']) &&  intval($_REQUEST['price_of_150g']) ==1){
				$sql_prices .= "('" . $_REQUEST['id'] . "','150','" . trim($_REQUEST['price_of_150g_value']) . "' ), ";
				$flag_price=1;					
			}
			if(isset($_REQUEST['price_of_200g']) &&  intval($_REQUEST['price_of_200g']) ==1){
				$sql_prices .= "('" . $_REQUEST['id'] . "','200','" . trim($_REQUEST['price_of_200g_value']) . "' ), ";
				$flag_price=1;					
			}
			
			if($flag_price == 1){
				$sql_prices =  substr($sql_prices,0,-2).";";
				$db->query($sql_prices);	
			}	
			
			
			//****************************  upload image ********************************//	
			if($_FILES['photo_name']['size'] >0 && $general_func->valid_file_type($_FILES["photo_name"]["name"],$_FILES["photo_name"]["type"])){			
				
				@unlink($original.$_REQUEST['photo_name']);
				@unlink($small.$_REQUEST['photo_name']);
				
				$uploaded_name=array();				
				$userfile_name=$_FILES['photo_name']['name'];
				$userfile_tmp= $_FILES['photo_name']['tmp_name'];
				$userfile_size=$_FILES['photo_name']['size'];
				$userfile_type= $_FILES['photo_name']['type'];
									
				$path=$_REQUEST['id'] ."_".$general_func->remove_space_by_hypen($security_validator->sanitize_filename($userfile_name));
				$img=$original.$path;
				move_uploaded_file($userfile_tmp, $img) or die();
									
				$uploaded_name['photo_name']=$path;
				$db->query_update("meals",$uploaded_name,'id='.$_REQUEST['id']);
									
				list($width, $height) = getimagesize($img);
							
				if($width > 188 || $height > 116){				
					$upload->uploaded_image_resize(188,116,$original,$small,$path);
				}else{
					copy($img,$small.$path); 
				}	
									
				if($width > 640 || $height > 480){
					$upload->uploaded_image_resize(640,480,$original,$original,$path);
				}	
			}
			
			if($db->affected_rows > 0)
				$_SESSION['msg']="Meal successfully updated!";
			
			$general_func->header_redirect($general_func->admin_url."meals/meals.php");				
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
	if(!validate_text(frm.name,1,"Enter meal name"))
		return false;
	
	var meal_plan_category_checked = $('input[name="meal_plan_category_id[]"]:checked').length;		
		
   	if(meal_plan_category_checked == 0){
    	alert("Please select at least a meal plan category");	
       	return false;
    }		
				
	if(frm.meal_category_id.selectedIndex == 0 ){
		alert("Please choose a meal category");
		document.ff.meal_category_id.focus();
		return false;		
	}
		
	if(frm.carbs_veggies.selectedIndex == 0 ){
		alert("Please choose whether meal is a carbs and/or veggies");
		frm.carbs_veggies.focus();
		return false;		
	}	
	
	if(frm.with_or_without_sauce.selectedIndex == 0 ){
		alert("Please choose whether meal is with sauce or without sauce");
		frm.with_or_without_sauce.focus();
		return false;		
	}		
		
	if(frm.price_of_100g.checked == false || frm.price_of_150g.checked == false || frm.price_of_200g.checked == false){
		alert("Please check all the prices of the meal");
		return false;	
	}
	
	if(frm.price_of_100g.checked == true){
		if(!validate_price(frm.price_of_100g_value,1,"Enter a valid 100gm meal price"))
			return false;
	}	
		
		
	if(frm.price_of_150g.checked == true){
		if(!validate_price(frm.price_of_150g_value,1,"Enter a valid 150gm meal price"))
			return false;
	}
	
	if(frm.price_of_200g.checked == true){
		if(!validate_price(frm.price_of_200g_value,1,"Enter a valid 200gm meal price"))
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
          <td align="left" valign="middle" class="body_tab-middilebg"><?=$button?> Meal</td>
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
                  <td  class="body_content-form">Meal Name:<font class="form_required-field"> *</font></td>
                  <td width="80%"><input name="name" value="<?=$name?>" type="text" autocomplete="off" class="form_inputbox" size="55" />
                  </td>
                </tr>
                 <td  class="body_content-form" valign="top">Meal Plan Category:<font class="form_required-field"> *</font></td>
                  <td  valign="top"  class="htext" valign="top" style="line-height: 20px;"> 
                          <?php
                          $sql_cat="select id,name from meal_plan_category order by name";
						  $result_cat=$db->fetch_all_array($sql_cat);
						  $total_cat=count($result_cat);
						  $br=1;						  
						  for($cat=0; $cat < $total_cat; $cat++){ ?>
						  	<input type="checkbox" name="meal_plan_category_id[]" id="meal_plan_category_id"  value="<?=$result_cat[$cat]['id']?>" <?=in_array($result_cat[$cat]['id'], $meal_plan_category_array)?'checked="checked"':'';?>><?=$result_cat[$cat]['name']?>
						  	&nbsp;&nbsp;&nbsp;&nbsp;
						<?php 
							if($br++%4 == 0)
						   		echo "<br/>";
						  } ?>                        
                  </td>
                </tr>
                
                
                 <tr>
                  <td  class="body_content-form" valign="top">Meal Category:<font class="form_required-field"> *</font></td>
                  <td  valign="top"> <select name="meal_category_id" class="inputbox_select" style="width: 300px; padding: 2px 1px 2px 0px;">                         	
                          <option value="">Select One</option>
                          <?php
                          $sql_cat="select id,name from meal_category order by name";
						  $result_cat=$db->fetch_all_array($sql_cat);
						  $total_cat=count($result_cat);
						  
						  for($cat=0; $cat < $total_cat; $cat++){ ?>
						  	<option value="<?=$result_cat[$cat]['id']?>" <?=intval($result_cat[$cat]['id'])==$meal_category_id?'selected="selected"':'';?>><?=$result_cat[$cat]['name']?></option>	
						<?php } ?>
                        </select>
                  </td>
                </tr>
                 <tr>
                 
                
                  <tr>
                  <td class="body_content-form">Meal Description:</td>
                  <td ><textarea name="details" class="form_textarea" cols="70" rows="6"><?=$details?></textarea>
                  
                  </td>
                </tr>                 
                
                <tr>
                  <td  class="body_content-form">Energy (kcal):</td>
                  <td width="80%"><input name="energy" value="<?=$energy?>" type="text" autocomplete="off" class="form_inputbox" size="55" />
                  </td>
                </tr>
                <tr>
                  <td  class="body_content-form">Calories:</td>
                  <td width="80%"><input name="calories" value="<?=$calories?>" type="text" autocomplete="off" class="form_inputbox" size="55" />
                  </td>
                </tr>
                <tr>
                  <td  class="body_content-form">Protein:</td>
                  <td width="80%"><input name="protein" value="<?=$protein?>" type="text" autocomplete="off" class="form_inputbox" size="55" />
                  </td>
                </tr>
                <tr>
                  <td  class="body_content-form">Fat Total:</td>
                  <td width="80%"><input name="fat_total" value="<?=$fat_total?>" type="text" autocomplete="off" class="form_inputbox" size="55" />
                  </td>
                </tr>
                <tr>
                  <td  class="body_content-form">Carbohydrates:</td>
                  <td width="80%"><input name="carbohydrates" value="<?=$carbohydrates?>" type="text" autocomplete="off" class="form_inputbox" size="55" />
                  </td>
                </tr>
                   <tr>
                  <td  class="body_content-form" valign="top">Carbs and/or Veggies?:<font class="form_required-field"> *</font></td>
                  <td  valign="top"> <select name="carbs_veggies" class="inputbox_select" style="width: 300px; padding: 2px 1px 2px 0px;">                         	
                          <option value="">Select One</option>
                          <option value="1" <?=intval($carbs_veggies)==1?'selected="selected"':'';?>>Carbs</option>
                          <option value="2" <?=intval($carbs_veggies)==2?'selected="selected"':'';?>>Veggies</option>
                           <option value="3" <?=intval($carbs_veggies)==3?'selected="selected"':'';?>>Carbs and Veggies</option>
                        </select>
                  </td>
                </tr>
                   <tr>
                  <td  class="body_content-form" valign="top">With or Without Sauce?:<font class="form_required-field"> *</font></td>
                  <td  valign="top"> <select name="with_or_without_sauce" class="inputbox_select" style="width: 300px; padding: 2px 1px 2px 0px;">                         	
                          <option value="">Select One</option>
                          <option value="1" <?=intval($with_or_without_sauce)==1?'selected="selected"':'';?>>With Sauce</option>
                          <option value="2" <?=intval($with_or_without_sauce)==2?'selected="selected"':'';?>>Without Sauce</option>                          
                        </select>
                  </td>
                </tr>
              
               	 <tr>
                  <td  class="body_content-form" valign="top">Price:<font class="form_required-field"> *</font></td>
                  <td width="80%"  valign="top" style="line-height: 40px;">
                  	<input type="checkbox" name="price_of_100g" value="1" <?=$price_of_100g==1?'checked="checked"':'';?> /> 100gm &nbsp;&nbsp;
                  	$<input name="price_of_100g_value" value="<?=$price_of_100g_value?>" type="text" autocomplete="off" class="form_inputbox" size="15" /> <br/>
                  	
                  		<input type="checkbox" name="price_of_150g" value="1" <?=$price_of_150g==1?'checked="checked"':'';?> /> 150gm &nbsp;&nbsp;
                  	$<input name="price_of_150g_value" value="<?=$price_of_150g_value?>" type="text" autocomplete="off" class="form_inputbox" size="15" /><br/>
                  	
                  		<input type="checkbox" name="price_of_200g" value="1" <?=$price_of_200g==1?'checked="checked"':'';?> /> 200gm &nbsp;&nbsp;
                  	$<input name="price_of_200g_value" value="<?=$price_of_200g_value?>" type="text" autocomplete="off" class="form_inputbox" size="15" />
                  	
                  </td>
                </tr>
               
                  <tr>
                  	<td class="body_content-form"></td>
                  <td class="body_content-form"><strong>Supported file types are gif, jpg, jpeg, png</strong></td>
                
                </tr>
                 <tr>
                  <td  class="body_content-form" valign="top"><?=trim($photo_name) != NULL?'Update':'Upload';?>
                     Meal Photo:</td>
                  <td  valign="top">
                  <?php if(trim($photo_name) != NULL){?>
                    		<a href="<?=$general_func->site_url.substr($original,6).$photo_name?>" class="highslide" onclick="return hs.expand(this)"><img src="<?=$general_func->site_url.substr($small,6).$photo_name?>" border="0" /></a>&nbsp;&nbsp;
                    		<a href="meals/meals-new.php?id=<?=$_REQUEST['id']?>&now=DELETE&field=photo_name&path=<?=$photo_name?>" class="htext" ><strong>Delete</strong></a>
                  			<br/>
                    <?php }	?>							
                	
                  <input type="file" name="photo_name" /></td>
                </tr>
                <tr>
                  <td class="body_content-form" valign="top">Like to show nutritional value and price to user?:</td>
                  <td valign="top"><input name="show_nutritional_price" type="checkbox" value="1" <?=$show_nutritional_price==1?'checked="checked"':'';?> AUTOCOMPLETE=OFF class="form_inputbox"  /></td>
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
