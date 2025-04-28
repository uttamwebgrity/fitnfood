<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";

$data=array();
$return_url=$_REQUEST['return_url'];


if(isset($_REQUEST['action']) && $_REQUEST['action']=="EDIT"){		
	$sql="select * from locations where id=" . intval($_REQUEST['id'])  . " limit 1";
	$result=$db->fetch_all_array($sql);
		
	$location_type_id=$result[0]['location_type_id']; 
  	$location_name=$result[0]['location_name']; 
 	$suburb_id=$result[0]['suburb_id']; 
  	$street_address=$result[0]['street_address']; 
  	$location_latitude=$result[0]['location_latitude']; 
  	$location_longitude=$result[0]['location_longitude']; 
	$status=$result[0]['status']; 		
	$button="Update";
}else{		
	$id="";
  	$location_type_id="";
  	$location_name=""; 
 	$suburb_id="";
  	$street_address="";
  	$location_latitude=""; 
  	$location_longitude="";  
	$status=1;		
	$button="Add New";
}


if(isset($_POST['enter']) && $_POST['enter']=="yes" && trim($_POST['login_form_id'])==$_SESSION['login_form_id']){
	
	$location_type_id=intval($_REQUEST['location_type_id']);
	$location_name=filter_var(trim($_REQUEST['location_name']), FILTER_SANITIZE_STRING);
	$suburb_id=intval($_REQUEST['suburb_id']);				 
	$street_address=filter_var(trim($_REQUEST['street_address']), FILTER_SANITIZE_STRING);
	$location_latitude=$security_validator->sanitize(trim($_REQUEST['location_latitude']));	
	$location_longitude=$security_validator->sanitize(trim($_REQUEST['location_longitude']));			
	$status=intval($_REQUEST['status']);	
	
	if($_POST['submit']=="Add New"){
		if($db->already_exist_inset("locations","location_name",$location_name)){
			$_SESSION['msg']="Sorry, your specified location name is already taken!";		
		}else{			
			$data['location_type_id']=$location_type_id;	
			$data['location_name']=$location_name;			
			$data['suburb_id']=$suburb_id;	
			$data['street_address']=$street_address;			
			$data['location_latitude']=$location_latitude;
			$data['location_longitude']=$location_longitude;			
			$data['status']=$status;
			$data['date_added']=$current_date_time;
			
			
			$inserted_id=$db->query_insert("locations",$data);	
						
			$_SESSION['msg']="Training location successfully created!";
			$general_func->header_redirect($_SERVER['PHP_SELF']);
		}	

	}else{
		if($db->already_exist_update("locations","id",$_REQUEST['id'],"location_name",$location_name)){
			$_SESSION['msg']="Sorry, your specified location name is already taken!";		
		}else{
			$data['location_type_id']=$location_type_id;	
			$data['location_name']=$location_name;			
			$data['suburb_id']=$suburb_id;	
			$data['street_address']=$street_address;			
			$data['location_latitude']=$location_latitude;
			$data['location_longitude']=$location_longitude;	
			$data['status']=$status;
			$data['date_modified']=$current_date_time;			
			
			$db->query_update("locations",$data,"id='".$_REQUEST['id'] ."'");		
						
			if($db->affected_rows > 0)
				$_SESSION['msg']="Training location successfully updated!";
			
			$general_func->header_redirect($return_url);
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
		
	if(document.ff.location_type_id.selectedIndex == 0){
		alert("Please select a location type");
		document.ff.location_type_id.focus();
		return false;
	}
	
	if(!validate_text(document.ff.location_name,1,"Enter location name"))
		return false;	
		
	if(document.ff.suburb_id.selectedIndex == 0){
		alert("Please select a suburb");
		document.ff.suburb_id.focus();
		return false;
	}	
	
	if(!validate_text(document.ff.street_address,1,"Enter street address"))
		return false;	
		
	if(!validate_text(document.ff.location_latitude,1,"Enter location latitude"))
		return false;	
		
	if(!validate_text(document.ff.location_longitude,1,"Enter location longitude"))
		return false;
		
	if(document.ff.status.selectedIndex == 0){
		alert("Please select a status");
		document.ff.status.focus();
		return false;
	}
}	


</script>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg"><?=$button?>
            Location</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg"> 
    	<form method="post" action="<?=$_SERVER['PHP_SELF']?>"  name="ff" enctype="multipart/form-data" onsubmit="return validate()">
        <input type="hidden" name="enter" value="yes" />
        <input type="hidden" name="id" value="<?=$_REQUEST['id']?>" />
        <input type="hidden" name="return_url" value="<?php echo $_REQUEST['return_url']?>" />
        <input type="hidden" name="photo" value="<?=$photo?>" />
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
                  <td  width="20%" class="body_content-form">Location Type :<font class="form_required-field"> *</font></td>
                  <td width="80%">
                  	<select name="location_type_id" class="inputbox_select" style="width: 300px;">
	            	<option  value="" <?=$location_type_id==""?'selected="selected"':'';?>>Select One</option>
		            <?php 
		            $sql_suburb="select id,name,only_for_platinum_members from  location_types order by only_for_platinum_members,name ASC";
		            $result_suburb=$db->fetch_all_array($sql_suburb);
					$total_suburb=count($result_suburb);
		            
		            for($s=0; $s < $total_suburb; $s++){ ?>
		            	<option value="<?=$result_suburb[$s]['id']?>" <?=$location_type_id==$result_suburb[$s]['id']?'selected="selected"':'';?>><?=$result_suburb[$s]['name']?> (<?=$result_suburb[$s]['only_for_platinum_members']==1?'For platinum members':'For all members';?>)</option>				
		            <?php } ?>
	            	</select>
                  </td>
                </tr>
                
                 <tr>
                  <td  class="body_content-form" valign="top">Location Name:<font class="form_required-field"> *</font></td>
                  <td  valign="top"><input name="location_name" value="<?=$location_name?>" type="text" autocomplete="off" class="form_inputbox" size="55" /></td>
                </tr>
                <tr>
                  <td  class="body_content-form">Suburb:<font class="form_required-field"> *</font></td>
                  <td >
                  	<select name="suburb_id" class="inputbox_select" style="width: 300px;">
	            	<option  value="" <?=$suburb_id==""?'selected="selected"':'';?>>Select One</option>
		            <?php 
		            $sql_suburb="select id,suburb_name,suburb_state,suburb_postcode from suburb order by suburb_name ASC";
		            $result_suburb=$db->fetch_all_array($sql_suburb);
					$total_suburb=count($result_suburb);
		            
		            for($s=0; $s < $total_suburb; $s++){ ?>
		            	<option value="<?=$result_suburb[$s]['id']?>" <?=$suburb_id==$result_suburb[$s]['id']?'selected="selected"':'';?>><?=ucwords(strtolower($result_suburb[$s]['suburb_name'])).", ".$result_suburb[$s]['suburb_state'].", ".$result_suburb[$s]['suburb_postcode']?></option>				
		            <?php } ?>
	            	</select>
                  </td>
                </tr>
                <tr>
                  <td width="20%" class="body_content-form" valign="top">Street Address:<font class="form_required-field"> *</font></td>
                  <td width="80%" valign="top"><input name="street_address" value="<?=$street_address?>" type="text" autocomplete="off" class="form_inputbox" size="55" /></td>
                </tr>                 
                
                 <tr>
                  <td width="20%" class="body_content-form" valign="top">Latitude:<font class="form_required-field"> *</font></td>
                  <td width="80%" valign="top"><input name="location_latitude" value="<?=$location_latitude?>" type="text" autocomplete="off" class="form_inputbox" size="55" /></td>
                </tr>
                 <tr>
                  <td width="20%" class="body_content-form" valign="top">Longitude:<font class="form_required-field"> *</font></td>
                  <td width="80%" valign="top"><input name="location_longitude" value="<?=$location_longitude?>" type="text" autocomplete="off" class="form_inputbox" size="55" /></td>
                </tr>
                <tr>                              
                  <td class="body_content-form" valign="top">Status:<font class="form_required-field"> *</font></td>
                  <td  valign="top"><select name="status"  class="inputbox_select" style="width: 100px;" >
                      <option value="">Choose One</option>
                      <option value="1" <?=$status==1?'selected="selected"':'';?>>Active</option>
                      <option value="0" <?=$status==0?'selected="selected"':'';?>>Inactive</option>
                    </select>
                    <p>&nbsp; </p></td>
                </tr>
                </tr>             
                 
              </table></td>
            <td width="8" align="left" valign="top">&nbsp;</td>
          </tr>       
          
            <tr>
            <td colspan="4" height="30" align="center"><table width="879" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="36%"><table border="0" align="right" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                        <td align="left" valign="middle" class="body_tab-middilebg"><input name="submit" type="submit" class="submit1" value="<?=$button?>" /></td>
                        <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                      </tr>
                    </table></td>
                  <td width="3%"></td>
                  <td width="61%"><?php if($button !="Add New"){?>
                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                        <td align="left" valign="middle" class="body_tab-middilebg">
                        	<input type="button" class="submit1"  name="back" value="Back"  onclick="history.back();" />
                        	
                        	<!--<input name="back" onclick="location.href='<?=$return_url?>'"  type="button" class="submit1" value="Back" />--></td>
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
                
              </table></td>
            <td width="8" align="left" valign="top">&nbsp;</td>
          </tr>
          
          
          
          
        </table>
      </form></td>
  </tr>
</table>
<?php
include("../foot.htm");
?>
