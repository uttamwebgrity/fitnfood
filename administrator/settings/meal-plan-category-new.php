<?php
$path_depth = "../../";

include_once ("../head.htm");
$link_name = "Welcome";

$original = $path_depth . "category_images/";

$data = array();

$return_url = $_REQUEST['return_url'];

if (isset($_REQUEST['action']) && $_REQUEST['action'] == "EDIT") {
	$sql = "select * from meal_plan_category where id=" . intval($_REQUEST['id']) . " limit 1";
	$result = $db -> fetch_all_array($sql);

	$name = $result[0]['name'];
	$details = $result[0]['details'];
	$meals_per_day = $result[0]['meals_per_day'];
	$snacks_per_day = $result[0]['snacks_per_day'];
	$photo_name1 = $result[0]['photo_name1'];
	$photo_name2 = $result[0]['photo_name2'];
	$photo_name3 = $result[0]['photo_name3'];
	$carbs_veggies = $result[0]['carbs_veggies'];
	$with_or_without_sauce = $result[0]['with_or_without_sauce'];
	$snacks_incl = $result[0]['snacks_incl'];
	$user_can_download_pdf = $result[0]['user_can_download_pdf'];
	$snacks_type_array = array();

	if ($snacks_incl == 2) {
		$snacks_type_array = explode(",", $result[0]['snacks_type']);
	}

	$plan_layout = array();

	$result_plan_layout = $db -> fetch_all_array("select * from meal_plan_layout where meal_plan_category_id='" . intval($_REQUEST['id']) . "' order by which_day,meal_time ASC");
	$total_plan_layout = count($result_plan_layout);

	for ($i = 0; $i < $total_plan_layout; $i++) {
		$plan_layout[$result_plan_layout[$i]['which_day']][$result_plan_layout[$i]['meal_time']]['meal_category_id'] = $result_plan_layout[$i]['meal_category_id'];
		$plan_layout[$result_plan_layout[$i]['which_day']][$result_plan_layout[$i]['meal_time']]['carbs_veggies'] = $result_plan_layout[$i]['carbs_veggies'];
		$plan_layout[$result_plan_layout[$i]['which_day']][$result_plan_layout[$i]['meal_time']]['with_or_without_sauce'] = $result_plan_layout[$i]['with_or_without_sauce'];
	}

	//print_r ($plan_layout);

	$display_order = $result[0]['display_order'];
	$status = $result[0]['status'];

	$button = "Update";
} else {
	$name = "";
	$details = "";
	$photo_name1 = "";
	$photo_name2 = "";
	$photo_name3 = "";	
	$snacks_incl = "";
	$meals_per_day = "";
	$snacks_per_day="";
	$user_can_download_pdf=1;

	$snacks_type_array = array();
	$plan_layout = array();

	$display_order = $db -> max_id("meal_plan_category", "display_order") + 1;
	$status = 1;

	$button = "Add New";
}

if (isset($_POST['enter']) && $_POST['enter'] == "yes" && trim($_POST['login_form_id']) == $_SESSION['login_form_id']) {
	$name = filter_var(trim($_REQUEST['name']), FILTER_SANITIZE_STRING);
	$details = trim($_REQUEST['details']);
	$meals_per_day = intval($_REQUEST['meals_per_day']);
	$snacks_per_day = intval($_REQUEST['snacks_per_day']);
	
		
	$snacks_incl = intval($_REQUEST['snacks_incl']);
	$user_can_download_pdf= intval($_REQUEST['user_can_download_pdf']);
	$display_order = intval($_REQUEST['display_order']);
	$status = intval($_REQUEST['status']);

	if ($_POST['submit'] == "Add New") {
		if ($db -> already_exist_inset("meal_plan_category", "name", $name)) {
			$_SESSION['msg'] = "Sorry, your specified category is already taken!";
		} else {
			$data['name'] = $name;
			$data['seo_link'] = $general_func -> create_seo_link($name);

			//*** check whether this name alreay exit ******//
			if ($db -> already_exist_inset("meal_plan_category", "seo_link", $data['seo_link'])) {//******* exit
				$data['seo_link'] = $db -> max_id("meal_plan_category", "id") + 1 . "-" . $data['seo_link'];
			}
			//*********************************************//

			$data['details'] = $details;
			$data['meals_per_day'] = $meals_per_day;						
			$data['snacks_incl'] = $snacks_incl;

			$snacks_type_list = "";
			if ($snacks_incl == 2) {
				$snacks_type = $_REQUEST['snacks_type'];
				$total_type = count($snacks_type);

				if ($total_type > 0) {
					for ($t = 0; $t < $total_type; $t++) {
						$snacks_type_list .= $snacks_type[$t] . ",";
					}
					$snacks_type_list = substr($snacks_type_list, 0, -1);
				}
				$data['snacks_per_day'] = $snacks_per_day;		
			}else{
				$data['snacks_per_day'] = 0;						
			}
			$data['snacks_type'] = $snacks_type_list;			
			$data['user_can_download_pdf'] = $user_can_download_pdf;
			$data['display_order'] = $display_order;
			$data['status'] = $status;
			$data['date_added'] = $current_date_time;

			$inserted_id = $db -> query_insert("meal_plan_category", $data);

			if ($meals_per_day > 0) {
				$sql_plan_layout = "INSERT INTO meal_plan_layout(meal_plan_category_id,meal_category_id,carbs_veggies,with_or_without_sauce,which_day,meal_time) VALUES";

				for ($day = 1; $day <= 7; $day++) {
					for ($time = 1; $time <= $meals_per_day; $time++) {
						$sql_plan_layout .= "('" . $inserted_id . "','" . $_REQUEST['meal_category_id_' . $day . '_' . $time] . "','" . $_REQUEST['carbs_veggies_' . $day . '_' . $time] . "','" . $_REQUEST['with_or_without_sauce_' . $day . '_' . $time] . "','" . $day . "','" . $time . "' ), ";
					}
				}

				$sql_plan_layout = substr($sql_plan_layout, 0, -2) . ";";
				$db -> query($sql_plan_layout);
			}

			if ($db -> affected_rows > 0)
				$_SESSION['msg'] = "Category successfully added!";

			$general_func -> header_redirect($_SERVER['PHP_SELF']);
		}

	} else {

		if ($db -> already_exist_update("meal_plan_category", "id", $_REQUEST['id'], "name", $name)) {
			$_SESSION['msg'] = "Sorry, your specified category is already taken!";
		} else {
			$data['name'] = $name;
			$data['seo_link'] = $general_func -> create_seo_link($name);
			//*** check whether this name alreay exit ******//
			if ($db -> already_exist_update("category", "id", $_REQUEST['id'], "seo_link", $data['seo_link'])) {//******* exit
				$data['seo_link'] = $_REQUEST['id'] . "-" . $data['seo_link'];
			}
			//*********************************************//

			$data['details'] = $details;
			$data['meals_per_day'] = $meals_per_day;								
			$data['snacks_incl'] = $snacks_incl;

			$snacks_type_list = "";
			if ($snacks_incl == 2) {
				$snacks_type = $_REQUEST['snacks_type'];
				$total_type = count($snacks_type);

				if ($total_type > 0) {
					for ($t = 0; $t < $total_type; $t++) {
						$snacks_type_list .= $snacks_type[$t] . ",";
					}
					$snacks_type_list = substr($snacks_type_list, 0, -1);
				}
				$data['snacks_per_day'] = $snacks_per_day;	
			}else{
				$data['snacks_per_day'] = 0;
			}

			$data['snacks_type'] = $snacks_type_list;
			$data['user_can_download_pdf'] = $user_can_download_pdf;
			$data['display_order'] = $display_order;
			$data['status'] = $status;
			$data['date_modified'] = $current_date_time;

			$db -> query_update("meal_plan_category", $data, "id='" . $_REQUEST['id'] . "'");

			$db -> query_delete("meal_plan_layout", "meal_plan_category_id='" . $_REQUEST['id'] . "'");

			if ($meals_per_day > 0) {
				$sql_plan_layout = "INSERT INTO meal_plan_layout(meal_plan_category_id,meal_category_id,carbs_veggies,with_or_without_sauce,which_day,meal_time) VALUES";

				for ($day = 1; $day <= 7; $day++) {
					for ($time = 1; $time <= $meals_per_day; $time++) {
						$sql_plan_layout .= "('" . $_REQUEST['id'] . "','" . $_REQUEST['meal_category_id_' . $day . '_' . $time] . "','" . $_REQUEST['carbs_veggies_' . $day . '_' . $time] . "','" . $_REQUEST['with_or_without_sauce_' . $day . '_' . $time] . "','" . $day . "','" . $time . "' ), ";
					}
				}

				$sql_plan_layout = substr($sql_plan_layout, 0, -2) . ";";
				$db -> query($sql_plan_layout);
			}

			if ($_REQUEST['id'] == 1 || $_REQUEST['id'] == 2 || $_REQUEST['id'] == 3) {
				//*************************  Upload photo *************************************//
				for ($p = 1; $p <= 3; $p++) {
					if ($_FILES['photo_name' . $p]['size'] > 0 && $general_func -> valid_file_type($_FILES["photo_name" . $p]["name"], $_FILES["photo_name" . $p]["type"])) {

						@unlink($original . $_REQUEST['photo_name' . $p]);

						$uploaded_name = array();
						$userfile_name = $_FILES['photo_name' . $p]['name'];
						$userfile_tmp = $_FILES['photo_name' . $p]['tmp_name'];
						$userfile_size = $_FILES['photo_name' . $p]['size'];
						$userfile_type = $_FILES['photo_name' . $p]['type'];

						$path = time() . "_" . $general_func -> remove_space_by_hypen($security_validator -> sanitize_filename($userfile_name));

						$img = $original . $path;
						move_uploaded_file($userfile_tmp, $img) or die();

						$uploaded_name['photo_name' . $p] = $path;
						$db -> query_update("meal_plan_category", $uploaded_name, 'id=' . $_REQUEST['id']);
					}
				}
			}

			if ($db -> affected_rows > 0)
				$_SESSION['msg'] = "Category successfully updated!";

			$general_func -> header_redirect($return_url);
		}

	}
}
?>
<script type="text/javascript" src="<?=$general_func -> site_url ?>highslide/highslide.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$general_func -> site_url ?>highslide/highslide.css" />
<script language="javascript" type="text/javascript"> 
function validate(){	
	var frm=document.ff;	
	if(!validate_text(frm.name,1,"Enter category name"))
		return false;
		
	if(frm.meals_per_day.selectedIndex == 0){
		alert("Please select Meals Per Day");
		frm.meals_per_day.focus();
		return false;
	}
	
			
	var meals_per_day=frm.meals_per_day.value;
	
	var error=0;
	
	for(var day=1; day <=7; day++ ){
		for(var time=1; time <= meals_per_day; time++ ){			
			if(document.getElementById("meal_category_id_"+ day + "_" +time).value == ""){
      			document.getElementById("meal_category_id_"+ day + "_" +time).style.border="1px solid #ff5657";
				error++;
      		}else{
      			document.getElementById("meal_category_id_"+ day + "_" +time).style.border="1px solid #D8D9DA";	
      		}
      		
      		
      		if(document.getElementById("carbs_veggies_"+ day + "_" +time).value == ""){
      			document.getElementById("carbs_veggies_"+ day + "_" +time).style.border="1px solid #ff5657";
				error++;
      		}else{
      			document.getElementById("carbs_veggies_"+ day + "_" +time).style.border="1px solid #D8D9DA";	
      		}
      		
      		
      		if(document.getElementById("with_or_without_sauce_"+ day + "_" +time).value == ""){
      			document.getElementById("with_or_without_sauce_"+ day + "_" +time).style.border="1px solid #ff5657";
				error++;
      		}else{
      			document.getElementById("with_or_without_sauce_"+ day + "_" +time).style.border="1px solid #D8D9DA";	
      		}
      		
      		
		}	
	}
	
	if(error>0)
		return false;
	
	if(frm.snacks_incl.selectedIndex == 0){
		alert("Please select Snacks incl?");
		frm.snacks_incl.focus();
		return false;
	}
	if(frm.snacks_incl.selectedIndex == 2){		
		var snacks_type_checked = $('input[name="snacks_type[]"]:checked').length;
				
		if(snacks_type_checked == 0){
			alert("Please select at least one snack type");	
			return false;
		}		
					
		if(frm.snacks_per_day.selectedIndex == 0){
			alert("Please select Snacks Per Day");
			frm.snacks_per_day.focus();
			return false;
		}		
	}
	
	
	if(frm.status.selectedIndex == 0){
		alert("Please select status");
		frm.status.focus();
		return false;
	}
		
	if(!validate_integer(document.ff.display_order,1,"Display order must be a number"))
		return false;	
		
}

hs.graphicsDir = '<?=$general_func -> site_url ?>highslide/graphics/';
hs.wrapperClassName = 'wide-border';


function show_snacks(val){
	if(parseInt(val) == 2 ){	
		$("#snacks_div").show("slow");					
	}else{		
		$("#snacks_div").hide("slow");	
	}
}

function meal_per_day(val){	
	for(var day=1; day <= 7; day++ ){
		for(var time=1; time <= 6; time++ ){			
			if(val >= time){
				$("#meal_"+ day + "_"+ time).show();	
			}else{
				$("#meal_"+ day + "_"+ time).hide();					
			}
		}
	}
}
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg"><?=$button ?> Meal Plan Category</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg">
    <form enctype="multipart/form-data" method="post" action="<?=$_SERVER['PHP_SELF'] ?>" name="ff"  onsubmit="return validate()">
        <input type="hidden" name="enter" value="yes" />
        <input type="hidden" name="id" value="<?=$_REQUEST['id'] ?>" />
        <input type="hidden" name="return_url" value="<?php echo $_REQUEST['return_url']?>" />
         <input type="hidden" name="login_form_id" value="<?=$_SESSION['login_form_id'] ?>" />
         <input type="hidden" name="photo_name1" value="<?=$photo_name1 ?>" />
         <input type="hidden" name="photo_name2" value="<?=$photo_name2 ?>" />
         <input type="hidden" name="photo_name3" value="<?=$photo_name3 ?>" />
         
        <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td colspan="3" class="body_content-form" height="30"></td>
          </tr>
          <?php if(isset($_SESSION['msg']) && trim($_SESSION['msg']) != NULL){?>
          <tr>
            <td colspan="3" class="message_error"><?=$_SESSION['msg'];
			$_SESSION['msg'] = "";
 ?></td>
          </tr>
          <tr>
            <td colspan="3" class="body_content-form" height="30"></td>
          </tr>
          <?php  } ?>
          <tr>
            <td align="left" valign="top" colspan="3"><table width="79%" border="0"  align="center" cellspacing="2" cellpadding="6">
                <tr>
                  <td width="20%" class="body_content-form" valign="top">Category Name:<font class="form_required-field"> *</font></td>
                  <td width="80%" valign="top"><input name="name" type="text" value="<?=$name ?>" AUTOCOMPLETE=OFF class="form_inputbox" size="55" /></td>
                </tr>
                 <tr>
                  <td  class="body_content-form" valign="top">Description: </td>
                  <td  valign="top"> <textarea name="details"  autocomplete="off" class="form_textarea" cols="100" rows="6"><?=$details ?></textarea></td>
                </tr>
                 <tr>
                  <td  class="body_content-form" valign="top">Meals Per Day (A)?:<font class="form_required-field"> *</font> </td>
                  <td  valign="top"><select name="meals_per_day" class="inputbox_select" style="width: 300px; padding: 2px 1px 2px 0px;" onchange="meal_per_day(this.value)">                         	
                          <option value="">Select One</option>                          
                          <!--<option value="1" <?=intval($meals_per_day) == 1 ? 'selected="selected"' : ''; ?>>1</option>
                          <option value="2" <?=intval($meals_per_day) == 2 ? 'selected="selected"' : ''; ?>>2</option>-->
                          <option value="3" <?=intval($meals_per_day) == 3 ? 'selected="selected"' : ''; ?>>3</option>
                          <option value="4" <?=intval($meals_per_day) == 4 ? 'selected="selected"' : ''; ?>>4</option>
                          <option value="5" <?=intval($meals_per_day) == 5 ? 'selected="selected"' : ''; ?>>5</option>
                          <option value="6" <?=intval($meals_per_day) == 6 ? 'selected="selected"' : ''; ?>>6</option>
                        </select>
                  </td>
                </tr>
                 <tr>
				 	<td height="10"></td>
				 </tr>
                  
                 <?php 
          		$sql_cat="select id,name from meal_category order by name";
				$result_cat=$db->fetch_all_array($sql_cat);
				$total_cat=count($result_cat);					  
                 
                 for($day=1; $day <=7; $day++ ){ ?>
              	<tr>
                	<td  class="body_content-form" valign="top">&nbsp;</td>
                  	<td  valign="top"><strong> *** Day <?=$day ?> *** </strong></td>
                </tr>
                <tr>
                	<td  class="body_content-form" valign="top">
                		<td align="left">
                	<div  class="meal_plan"> 
	                <?php for($time=1; $time <=6; $time++ ){ ?>
	                	<ul id="meal_<?=$day ?>_<?=$time ?>" style="display:<?=$meals_per_day >= $time ? 'block' : 'none'; ?>;" >
	                  		<li> <span>Meal <?=$time ?></span>
	                  		 <select  name="meal_category_id_<?=$day . "_" . $time ?>" id="meal_category_id_<?=$day . "_" . $time ?>" class="inputbox_select" style="width: 200px; padding: 2px 1px 2px 0px;">                         	
	                          <option value="">Select Category</option>
	                          <?php
							  for($cat=0; $cat < $total_cat; $cat++){ ?>
							  	<option value="<?=$result_cat[$cat]['id'] ?>" <?=intval($result_cat[$cat]['id']) == $plan_layout[$day][$time]['meal_category_id'] ? 'selected="selected"' : ''; ?>><?=$result_cat[$cat]['name'] ?></option>	
							<?php } ?>
	                        </select>
	                        &nbsp;&nbsp;
	                        <select name="carbs_veggies_<?=$day . "_" . $time ?>" id="carbs_veggies_<?=$day . "_" . $time ?>" class="inputbox_select" style="width: 130px; padding: 2px 1px 2px 0px;">                         	
	                          <option value="">Select One</option>                          
	                          <option value="1" <?=intval($plan_layout[$day][$time]['carbs_veggies']) == 1 ? 'selected="selected"' : ''; ?>>Carbs</option>
	                          <option value="2" <?=intval($plan_layout[$day][$time]['carbs_veggies']) == 2 ? 'selected="selected"' : ''; ?>>Veggies</option>
	                           <option value="3" <?=intval($plan_layout[$day][$time]['carbs_veggies']) == 3 ? 'selected="selected"' : ''; ?>>Carbs and Veggies</option>
	                        </select>
                         	&nbsp;&nbsp;
	                      	<select name="with_or_without_sauce_<?=$day . "_" . $time ?>" id="with_or_without_sauce_<?=$day . "_" . $time ?>" class="inputbox_select" style="width: 120px; padding: 2px 1px 2px 0px;">                         	
	                          <option value="">Select One</option>
	                          <option value="1" <?=intval($plan_layout[$day][$time]['with_or_without_sauce']) == 1 ? 'selected="selected"' : ''; ?>>With Sauce</option>
	                          <option value="2" <?=intval($plan_layout[$day][$time]['with_or_without_sauce']) == 2 ? 'selected="selected"' : ''; ?>>Without Sauce</option>                          
	                        </select>
	                  	</li>
	                </ul>
					<?php } ?> 
					</div>
					</td>
				</tr>
				<?php } ?>				 
				 <tr>
				 	<td height="20"></td>
				 </tr> 
                <tr>
                  <td width="15%" class="body_content-form" valign="top">Snacks incl?(F)?:<font class="form_required-field"> *</font></td>
                  <td width="85%" valign="top">
                  	<select name="snacks_incl" class="inputbox_select" style="width: 150px;" onchange="show_snacks(this.value);">
                  		<option value="0" <?=$snacks_incl == "0" ? 'selected="selected"' : ''; ?>> Select One</option>
                  		<option value="1" <?=$snacks_incl == 1 ? 'selected="selected"' : ''; ?>> No Snack</option>
                  		<option value="2" <?=$snacks_incl == 2 ? 'selected="selected"' : ''; ?>> Snack Present</option>                  		
                  	</select>
                  	
                  </td>
                </tr>
                 <tr>
                  <td width="15%" class="body_content-form" valign="top"></td>
                  <td width="85%"  valign="top">
                  	<div id="snacks_div" style="display: <?=$snacks_incl == 2 ? 'block' : 'none'; ?>;">
                  	<strong>Select snacks type </strong><font class="form_required-field"> *</font>
                  	<table width="100%" border="0" align="left" cellpadding="5" cellspacing="0">                  					
					<tr>
					<?php
                  	$result_snacks_type=$db->fetch_all_array("select id,name from snacks_type order by name ASC");
					$total_snacks_type=count($result_snacks_type);
					
					for($snack=0; $snack < $total_snacks_type;  $snack++ ){ ?>						
                  		<td><input type="checkbox"  name="snacks_type[]" id="snacks_type" value="<?=$result_snacks_type[$snack]['id']?>"  <?=in_array($result_snacks_type[$snack]['id'], $snacks_type_array) ? 'checked="checked"' : ''; ?> ><?=$result_snacks_type[$snack]['name']?></td>
                  	<?php } ?>                   	
                  	</tr>
                  	<tr>
               		<td colspan="<?=$total_snacks_type?>"><select name="snacks_per_day" class="inputbox_select" style="width: 300px; padding: 2px 1px 2px 0px;">                         	
                          <option value="">Snacks Per Day?</option>                          
                          <option value="1" <?=intval($snacks_per_day) == 1 ? 'selected="selected"' : ''; ?>>1</option>
                          <option value="2" <?=intval($snacks_per_day) == 2 ? 'selected="selected"' : ''; ?>>2</option>
                          <option value="3" <?=intval($snacks_per_day) == 3 ? 'selected="selected"' : ''; ?>>3</option>
                          <option value="4" <?=intval($snacks_per_day) == 4 ? 'selected="selected"' : ''; ?>>4</option>
                          <option value="5" <?=intval($snacks_per_day) == 5 ? 'selected="selected"' : ''; ?>>5</option>                          
                        </select>
                  </td>
                </tr>
                  							
                    </table>
                   </div></td>
                </tr> 
                
                 
                
                <?php if( $button == "Update" && ($_REQUEST['id'] == 1 ||  $_REQUEST['id'] == 2 ||  $_REQUEST['id'] == 3)){?>
               <tr>
                  <td  class="body_content-form" valign="top">Category Image: </td>
                  <td  valign="top">Supported file types are <strong>gif, jpg, jpeg and png</strong>, image width and height must be <strong>290px and 170px</strong> respectively. </td>
                </tr>
               <tr>
                  <td  class="body_content-form" valign="top"><?=trim($photo_name1) != NULL ? 'Update' : 'Upload'; ?> Image 1:</td>
                  <td  valign="top" style="font-size:11px;">
                  <?php if(trim($photo_name1) != NULL){?>
                    <a href="<?=$general_func -> site_url . substr($original, 6) . $photo_name1 ?>" class="highslide" onclick="return hs.expand(this)"><img src="<?=$general_func -> site_url . substr($original, 6) . $photo_name1 ?>" border="0" width="200" /></a>&nbsp;&nbsp;
                   <br/>                    
                    <?php } ?>
                  <input name="photo_name1" type="file"  class="form_inputbox" size="55" /><br/>                  
                  	
                  </td>
                </tr>
                <tr>
                  <td  class="body_content-form" valign="top"><?=trim($photo_name2) != NULL ? 'Update' : 'Upload'; ?> Image 2:</td>
                  <td  valign="top" style="font-size:11px;">
                  <?php if(trim($photo_name2) != NULL){?>
                    <a href="<?=$general_func -> site_url . substr($original, 6) . $photo_name2 ?>" class="highslide" onclick="return hs.expand(this)"><img src="<?=$general_func -> site_url . substr($original, 6) . $photo_name2 ?>" border="0" width="200" /></a>&nbsp;&nbsp;
                   <br/>                    
                    <?php } ?>
                  <input name="photo_name2" type="file"  class="form_inputbox" size="55" /><br/>                  
                  	
                  </td>
                </tr>
               
               <tr>
                  <td  class="body_content-form" valign="top"><?=trim($photo_name3) != NULL ? 'Update' : 'Upload'; ?> Image 3:</td>
                  <td  valign="top" style="font-size:11px;">
                  <?php if(trim($photo_name3) != NULL){?>
                    <a href="<?=$general_func -> site_url . substr($original, 6) . $photo_name3 ?>" class="highslide" onclick="return hs.expand(this)"><img src="<?=$general_func -> site_url . substr($original, 6) . $photo_name3 ?>" border="0" width="200" /></a>&nbsp;&nbsp;
                   <br/>                    
                    <?php } ?>
                  <input name="photo_name3" type="file"  class="form_inputbox" size="55" /><br/>                  
                  	
                  </td>
                </tr>               
                <?php } ?>

				 <tr>
                  <td class="body_content-form" valign="top">Can user download eating schedule PDF?:</td>
                  <td valign="top"><input name="user_can_download_pdf" type="checkbox" value="1" <?=$user_can_download_pdf==1?'checked="checked"':'';?> AUTOCOMPLETE=OFF class="form_inputbox"  /></td>
                </tr>

                 <tr>
                  <td class="body_content-form" valign="top">Display Order:<font class="form_required-field"> *</font></td>
                  <td valign="top"><input name="display_order" type="text" value="<?=$display_order ?>" AUTOCOMPLETE=OFF class="form_inputbox" size="15" /></td>
                </tr>
               
                <tr>
                  <td class="body_content-form" valign="top">Status:<font class="form_required-field"> *</font></td>
                  <td  valign="top">
                  	<select name="status" class="inputbox_select" style="width: 150px;">
                  		<option value="" <?=$status == "" ? 'selected="selected"' : ''; ?>> Select One</option>
                  		<option value="1" <?=$status == 1 ? 'selected="selected"' : ''; ?>> Active</option>
                  		<option value="0" <?=$status == 0 ? 'selected="selected"' : ''; ?>> Inactive</option>
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
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="submit" type="submit" class="submit1" value="<?=$button ?>" /></td>
                              <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                            </tr>
                          </table></td>
                        <td width="10%">&nbsp;</td>
                        <td width="49%"><table border="0" align="left" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="back" onclick="location.href='<?=$general_func -> admin_url ?>settings/meal-plan-category.php'"  type="button" class="submit1" value="Back" /></td>
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
include ("../foot.htm");
?>