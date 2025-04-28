<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";

$data=array();
$return_url=$_REQUEST['return_url'];

if(isset($_REQUEST['action']) && $_REQUEST['action']=="EDIT"){
	$sql="select * from video_types where id=" .   intval(mysql_real_escape_string($_REQUEST['id'])) . " limit 1";
	$result=$db->fetch_all_array($sql);	
	$name=$result[0]['name'];
	$access_to=$result[0]['access_to'];
	
	$selected_members=array();
	if($access_to == 4){			
		$result_members=$db->fetch_all_array("select user_id from video_types_access_permission where video_type_id=" . intval(mysql_real_escape_string($_REQUEST['id'])) . "");
		$total_members=count($result_members);
		
		for($m=0; $m <$total_members; $m++ ){
			$selected_members[]=$result_members[$m]['user_id'];	
		}
	}		
	$button="Update";
}else{
	$name="";
	$access_to=0;
	$selected_members=array();
	$button="Add New";
}


if(isset($_POST['enter']) && $_POST['enter']=="yes" && trim($_POST['login_form_id'])==$_SESSION['login_form_id']){
	$name=filter_var(trim($_REQUEST['name']), FILTER_SANITIZE_STRING);
	$access_to=intval($_REQUEST['access_to']);	
	
	if($_POST['submit']=="Add New"){
		if($db->already_exist_inset("video_types","name",$name)){
			$_SESSION['msg']="Sorry, your specified video type is already taken!";
		}else{
			$data['name']=$name;
			$data['access_to']=$access_to;
			$data['date_added']=$current_date_time;			
			$inserted_id = $db->query_insert("video_types",$data);
			
			if(intval($access_to) == 4){
				$member_ids=$_REQUEST['member_id'];
				$total_members_chosen=count($member_ids);
				
				$sql_access="INSERT INTO video_types_access_permission(video_type_id,user_id) VALUES";				
				
				for($mem=0; $mem < $total_members_chosen; $mem++){
					$sql_access .= "('" . $inserted_id . "','" . $member_ids[$mem]. "' ), ";
				}				
				$sql_access =  substr($sql_access,0,-2).";";
				$db->query($sql_access);
			}			
			
			if($db->affected_rows > 0)
				$_SESSION['msg']="Video type successfully added!";
							
			$general_func->header_redirect($_SERVER['PHP_SELF']);
		}	

	}else{
		if($db->already_exist_update("video_types","id",$_REQUEST['id'],"name",$name)){
			$_SESSION['msg']="Sorry, your specified video types is already taken!";
		}else{
			$data['name']=$name;						
			$data['access_to']=$access_to;
			$data['date_modified']=$current_date_time;
					
			$db->query_update("video_types",$data,"id='".$_REQUEST['id'] ."'");
						
			$db->query_delete("video_types_access_permission","video_type_id='".$_REQUEST['id'] ."'");
			
		
			if(intval($access_to) == 4){
				$member_ids=$_REQUEST['member_id'];
				$total_members_chosen=count($member_ids);
				
				$sql_access="INSERT INTO video_types_access_permission(video_type_id,user_id) VALUES";				
				
				for($mem=0; $mem < $total_members_chosen; $mem++){
					$sql_access .= "('" . $_REQUEST['id'] . "','" . $member_ids[$mem]. "' ), ";
				}
				
				$sql_access =  substr($sql_access,0,-2).";";
				$db->query($sql_access);
			}			
			
			if($db->affected_rows > 0)
				$_SESSION['msg']="Video type successfully updated!";
				
			$general_func->header_redirect($return_url);
		}
	}
}	
?>
<script language="javascript" type="text/javascript"> 
function validate(){
		          
	if(!validate_text(document.ff.name,1,"Enter video type name"))
		return false;
		
	if(document.ff.access_to.selectedIndex == 0){
		alert("Please select user access");
		document.ff.access_to.focus();
		return false;
	}
	if(document.ff.access_to.value == 4){
		var checked = $( "input:checked" ).length;
       	if(parseInt(checked) == 0){
       		alert("Please select at least a member name");	
       		return false;
       	}		
	}		
}

function show_members(val){
	if(parseInt(val) == 4 ){	
		$("#member_div").show("slow");					
	}else{		
		$("#member_div").hide("slow");	
	}
}
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg"><?=$button?> Video Type</td>
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
                  <td width="15%" class="body_content-form" valign="top">Video Type Name:<font class="form_required-field"> *</font></td>
                  <td width="85%" valign="top"><input name="name" type="text" value="<?=$name?>" AUTOCOMPLETE=OFF class="form_inputbox" size="55" /></td>
                </tr>
               
                <tr>
                  <td width="15%" class="body_content-form" valign="top">User Access:<font class="form_required-field"> *</font></td>
                  <td width="85%" valign="top">
                  	<select name="access_to" class="inputbox_select" style="width: 150px;" onchange="show_members(this.value);">
                  		<option value="0" <?=$access_to=="0"?'selected="selected"':'';?>> Select One</option>
                  		<option value="1" <?=$access_to==1?'selected="selected"':'';?>> All Members</option>
                  		<option value="2" <?=$access_to==2?'selected="selected"':'';?>> Platinum Members</option> 
                  		<option value="3" <?=$access_to==3?'selected="selected"':'';?>> Standard Members</option> 
                  		<option value="4" <?=$access_to==4?'selected="selected"':'';?>> Specified Members</option>                  		
                  	</select>
                  </td>
                </tr>
                 <tr>
                  <td width="15%" class="body_content-form" valign="top"></td>
                  <td width="85%"  valign="top">
                  	<div id="member_div" style="display: <?=$access_to==4?'block':'none';?>;">
                  	<strong>Select member name </strong><font class="form_required-field"> *</font>
                  	<table width="100%" border="0" align="left" cellpadding="5" cellspacing="0">
                  	<?php
                  	$sql_members="select id, CONCAT(fname,' ',lname) as name from users order by fname ASC";
					$result_members=$db->fetch_all_array($sql_members);	
					$total_members=count($result_members);
					
					for($member=0; $member < $total_members; $member += 3 ){?>						
					<tr>
                  		<td><input type="checkbox" <?=in_array(trim($result_members[$member]['id']),$selected_members)?'checked="checked"':''; ?> name="member_id[]" value="<?=$result_members[$member]['id']?>" > <?=$result_members[$member]['name']?></td>
                  		<td>
                  			<?php if(trim($result_members[$member+1]['id']) != NULL){ ?>
                  				<input type="checkbox" <?=in_array(trim($result_members[$member+1]['id']),$selected_members)?'checked="checked"':''; ?> name="member_id[]"  value="<?=$result_members[$member+1]['id']?>" > <?=$result_members[$member+1]['name']?>								
                  			<?php } ?>
                  			</td>
                  		<td>                  			
                  			<?php if(trim($result_members[$member+2]['id']) != NULL){ ?>                  				
							<input type="checkbox" <?=in_array(trim($result_members[$member+2]['id']),$selected_members)?'checked="checked"':''; ?> name="member_id[]"  value="<?=$result_members[$member+2]['id']?>" > <?=$result_members[$member+2]['name']?>	
                  			<?php } ?>                  			
                  			</td>                 		            		
                  	</tr>	
					<?php } ?> 
                    </table>
                    </div></td>
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
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="back" onclick="location.href='<?=$general_func->admin_url?>training-videos/video-types.php'"  type="button" class="submit1" value="Back" /></td>
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