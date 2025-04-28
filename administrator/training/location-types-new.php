<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";


$data=array();

$return_url=$_REQUEST['return_url'];

if(isset($_REQUEST['action']) && $_REQUEST['action']=="EDIT"){
	$sql="select * from location_types where id=" .   intval($_REQUEST['id']) . " limit 1";
	$result=$db->fetch_all_array($sql);	
	$name=$result[0]['name'];
	$marker_name=$result[0]['marker_name'];	
	$only_for_platinum_members=$result[0]['only_for_platinum_members'];	
	
	
	$alreary_added_markers=array();
	
	
	
	$result_markers=$db->fetch_all_array("SELECT GROUP_CONCAT(  `marker_name` SEPARATOR  ',' ) as markers FROM  `location_types` where marker_name != '" . $marker_name . "' ");
	
	if(count($result_markers) == 1){
		$alreary_added_markers=@explode(",",$result_markers[0]['markers']);
	}
		
	$button="Update";
}else{
	$name="";
	$marker_name="";
	$only_for_platinum_members=0;
	
	$alreary_added_markers=array();
	
	$result_markers=$db->fetch_all_array("SELECT GROUP_CONCAT(  `marker_name` SEPARATOR  ',' ) as markers FROM  `location_types`");
	
	if(count($result_markers) == 1){
		$alreary_added_markers=@explode(",",$result_markers[0]['markers']);
	}
	
	$button="Add New";
}



if(isset($_POST['enter']) && $_POST['enter']=="yes" && trim($_POST['login_form_id'])==$_SESSION['login_form_id']){
	$name=filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
	$marker_name=trim($_POST['marker_name']);
	$only_for_platinum_members=(isset($_POST['only_for_platinum_members']) && intval($_POST['only_for_platinum_members'])==1)?1:0;
	
	
	
	if($_POST['submit']=="Add New"){
		if($db->already_exist_inset("location_types","name",$name)){
			$_SESSION['msg']="Sorry, your specified location type is already taken!";
		}else{
			$data['name']=$name;
			$data['marker_name']=$marker_name;
			$data['only_for_platinum_members']=$only_for_platinum_members;		
			$data['date_added']=$current_date_time;
			
			$inserted_id = $db->query_insert("location_types",$data);			
			
			if($db->affected_rows > 0)
				$_SESSION['msg']="Location type successfully added!";
							
			$general_func->header_redirect($_SERVER['PHP_SELF']);
		}	

	}else{
		if($db->already_exist_update("location_types","id",$_REQUEST['id'],"name",$name)){
			$_SESSION['msg']="Sorry, your specified location types is already taken!";
		}else{
			$data['name']=$name;						
			$data['marker_name']=$marker_name;
			$data['only_for_platinum_members']=$only_for_platinum_members;		
			$data['date_modified']=$current_date_time;
		
			$db->query_update("location_types",$data,"id='".$_REQUEST['id'] ."'");
						
			if($db->affected_rows > 0)
				$_SESSION['msg']="Location type successfully updated!";
				
			$general_func->header_redirect($return_url);
		}
	}
}	
?>
<script language="javascript" type="text/javascript"> 
function validate(){
		          
	if(!validate_text(document.ff.name,1,"Enter location type name"))
		return false;
			
	var checked = $( "input:[type=radio]:checked" ).length;
   
   	if(parseInt(checked) == 0){
    	alert("Please choose a location type marker");	
       	return false;
	}	
}
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg"><?=$button?> Location Type</td>
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
                  <td width="20%" class="body_content-form" valign="top">Location Type Name:<font class="form_required-field"> *</font></td>
                  <td width="80%" valign="top"><input name="name" type="text" value="<?=$name?>" AUTOCOMPLETE=OFF class="form_inputbox" size="55" /></td>
                </tr>
               <tr>
                  <td class="body_content-form" valign="top">Location Only For Platinum Members?: </td>
                  <td valign="top"><input type="checkbox" name="only_for_platinum_members" value="1" <?=$only_for_platinum_members==1?'checked="checked"':'';?>  /></td>
                </tr>            
                <tr>
                  <td  class="body_content-form" valign="top">Location Marker:<font class="form_required-field"> *</font></td>
                  <td  valign="top">
                  	<?php 
                  	$total_markers=0;                  	
                  	for($i=1; $i <=20; $i++ ){                   		
						$current_marker="pointer-".$i;
						
						if(in_array($current_marker, $alreary_added_markers))
							continue;
						
						$total_markers++;
                  		?>
	                  	<input type="radio" name="marker_name" id="marker_name" value="pointer-<?=$i?>" <?=$marker_name=="pointer-".$i?'checked="checked"':'';?> /> <img src="../markers/pointer-<?=$i?>.png" />
	                  	&nbsp; &nbsp;&nbsp;&nbsp;						
                  	<?php 
                  	if($total_markers%4 == 0 ){
                  		echo "<br/><br/><br/><br/>";
						
                  	}
                  	 } ?>
                            	
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
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="back" onclick="location.href='<?=$general_func->admin_url?>training/location-types.php'"  type="button" class="submit1" value="Back" /></td>
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