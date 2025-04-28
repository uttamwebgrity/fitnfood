<?php
$path_depth = "../../";

include_once ("../head.htm");
$link_name = "Welcome";

$original = $path_depth . "category_images/";

$data = array();

$return_url = $_REQUEST['return_url'];

if (isset($_REQUEST['action']) && $_REQUEST['action'] == "EDIT") {
	$sql = "select * from pickup_locations where id=" . intval($_REQUEST['id']) . " limit 1";
	$result = $db -> fetch_all_array($sql);

	$location = $result[0]['location'];
	$address = $result[0]['address'];
	$pickup_timing = $result[0]['pickup_timing'];
	$status = $result[0]['status'];

	$button = "Update";
} else {
	$location = "";
	$address =  "";
	$pickup_timing = "";	
	$status = 1;

	$button = "Add New";
}

if (isset($_POST['enter']) && $_POST['enter'] == "yes" && trim($_POST['login_form_id']) == $_SESSION['login_form_id']) {
	$location = filter_var(trim($_REQUEST['location']), FILTER_SANITIZE_STRING);
	$address= trim($_REQUEST['address']);
	$pickup_timing = trim($_REQUEST['pickup_timing']);	
	$status = intval($_REQUEST['status']);

	if ($_POST['submit'] == "Add New") {
		if ($db -> already_exist_inset("pickup_locations", "location", $location)) {
			$_SESSION['msg'] = "Sorry, your specified pickup location is already taken!";
		} else {
			$data['location'] = $location;			
			$data['address'] = $address;
			$data['pickup_timing'] = $pickup_timing;						
			
			$data['status'] = $status;
			$data['date_added'] = $current_date_time;

			$inserted_id = $db -> query_insert("pickup_locations", $data);

			if ($db -> affected_rows > 0)
				$_SESSION['msg'] = "Pickup location successfully added!";

			$general_func -> header_redirect($_SERVER['PHP_SELF']);
		}

	} else {

		if ($db -> already_exist_update("pickup_locations", "id", $_REQUEST['id'], "location", $location)) {
			$_SESSION['msg'] = "Sorry, your specified pickup location is already taken!";
		} else {
			$data['location'] = $location;			
			$data['address'] = $address;
			$data['pickup_timing'] = $pickup_timing;
			$data['status'] = $status;
			$data['date_modified'] = $current_date_time;

			$db -> query_update("pickup_locations", $data, "id='" . $_REQUEST['id'] . "'");
		
			if ($db -> affected_rows > 0)
				$_SESSION['msg'] = "Pickup location successfully updated!";

			$general_func -> header_redirect($return_url);
		}

	}
}
?>
<script language="javascript" type="text/javascript"> 
function validate(){	
	var frm=document.ff;	
	if(!validate_text(frm.location,1,"Enter location name"))
		return false;
	
	if(!validate_text(frm.address,1,"Enter location address"))
		return false;	
	
	if(!validate_text(frm.pickup_timing,1,"Enter pickup timing"))
		return false
	
	if(frm.status.selectedIndex == 0){
		alert("Please select status");
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
          <td align="left" valign="middle" class="body_tab-middilebg"><?=$button ?> Pickup Location</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg">
    <form method="post" action="<?=$_SERVER['PHP_SELF'] ?>" name="ff"  onsubmit="return validate()">
        <input type="hidden" name="enter" value="yes" />
        <input type="hidden" name="id" value="<?=$_REQUEST['id'] ?>" />
        <input type="hidden" name="return_url" value="<?php echo $_REQUEST['return_url']?>" />
         <input type="hidden" name="login_form_id" value="<?=$_SESSION['login_form_id'] ?>" /> 
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
                  <td width="20%" class="body_content-form" valign="top">Location:<font class="form_required-field"> *</font></td>
                  <td width="80%" valign="top"><input name="location" type="text" value="<?=$location?>" AUTOCOMPLETE=OFF class="form_inputbox" size="55" /></td>
                </tr>
                
                 <tr>
                  <td width="20%" class="body_content-form" valign="top">Street Address:<font class="form_required-field"> *</font></td>
                  <td width="80%" valign="top"><input name="address" type="text" value="<?=$address?>" AUTOCOMPLETE=OFF class="form_inputbox" size="55" /></td>
                </tr>
                 <tr>
                  <td  class="body_content-form" valign="top">Pickup Timing: <font class="form_required-field"> *</font></td>
                  <td  valign="top"> <textarea name="pickup_timing"  autocomplete="off" class="form_textarea" cols="70" rows="10"><?=$pickup_timing?></textarea></td>
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
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="" type="button" class="submit1" value="Back" onclick="location.href='<?=$general_func -> admin_url ?>settings/pickup-location.php' "   /></td>
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