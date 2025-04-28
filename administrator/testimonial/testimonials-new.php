<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";



$data=array();

$return_url=$_REQUEST['return_url'];


if(isset($_REQUEST['action']) && $_REQUEST['action']=="EDIT"){
	$sql="select * from testimonials where id=" . intval($_REQUEST['id']) . " limit 1";
	$result=$db->fetch_all_array($sql);
	
	$name=$result[0]['name'];	
	$details=$result[0]['details'];	
	$embedded_video_link=$result[0]['embedded_video_link'];	
		
	$button="Update";
}else{
	$name="";
	$details="";
	$embedded_video_link="";	
	$button="Add New";
}


if(isset($_POST['enter']) && $_POST['enter']=="yes"  && trim($_POST['login_form_id'])==$_SESSION['login_form_id']){
			
	$name=filter_var(trim($_REQUEST['name']), FILTER_SANITIZE_STRING);	 
	$details=filter_var(trim($_REQUEST['details']), FILTER_SANITIZE_STRING); 
	$embedded_video_link=filter_var(trim($_REQUEST['embedded_video_link']), FILTER_SANITIZE_URL);	
	
	
	if($_POST['submit']=="Add New"){
		if($db->already_exist_inset("testimonials","name",$name)){
			$_SESSION['msg']="Sorry, your selected name is already taken!";
		}else{
			$data['name']=$name;			
			$data['details']=$details;	
			$data['embedded_video_link']=$embedded_video_link;				
			$data['date_added']=$current_date_time;				
			
			$db->query_insert("testimonials",$data);				
			
			if($db->affected_rows > 0)
				$_SESSION['msg']="Testimonial information successfully added.";
				
			$general_func->header_redirect($_SERVER['PHP_SELF']);
		}	

	}else{
		if($db->already_exist_update("testimonials","id",$_REQUEST['id'],"name",$name)){
			$_SESSION['msg']="Sorry, your specified name is already taken!";
		}else{
			$data['name']=$name;			
			$data['details']=$details;	
			$data['embedded_video_link']=$embedded_video_link;			
			$data['date_modified']=$current_date_time;	

			$db->query_update("testimonials",$data,"id='".$_REQUEST['id'] ."'");
					
			
			if($db->affected_rows > 0)
				$_SESSION['msg']="Testimonial information successfully updated.";
				
			$general_func->header_redirect($return_url);
		}
	}
}	

?>
<script type="text/javascript">
	
function validate(){
	if(!validate_text(document.ff.name,1,"Enter name"))
		return false;
	if(!validate_text(document.ff.details,1,"Enter testimonial details"))
		return false;		
}	
	
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
   <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg"><?=$button?> Testimonial</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg">
    <form method="post" action="<?=$_SERVER['PHP_SELF']?>" name="ff" onsubmit="return validate();">
        <input type="hidden" name="enter" value="yes" />
        <input type="hidden" name="id" value="<?=$_REQUEST['id']?>" />
        <input type="hidden" name="login_form_id" value="<?=$_SESSION['login_form_id']?>" />       
        
        <input type="hidden" name="return_url" value="<?php echo $_REQUEST['return_url']?>" />
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
            <td align="left" valign="top" colspan="3"><table width="92%" border="0"  align="center" cellspacing="2" cellpadding="6">
                <tr>
                  <td width="21%" class="body_content-form" valign="top">Name:<font class="form_required-field"> *</font></td>
                  <td width="79%" valign="top"><input name="name" type="text" value="<?=$name?>" AUTOCOMPLETE=OFF class="form_inputbox" size="75" /></td>
                </tr>                 
                  <tr>
                  <td  class="body_content-form">Testimonial Details:<font class="form_required-field"> *</font></td>
                  <td ><textarea name="details" class="form_textarea" cols="70" rows="6"><?=$details?></textarea></td>
                </tr>
                  <tr>
                  <td width="21%" class="body_content-form" valign="top">You Tube Share this Video Link:</td>
                  <td width="79%" valign="top"><input name="embedded_video_link" type="text" value="<?=$embedded_video_link?>" AUTOCOMPLETE=OFF class="form_inputbox" size="75" />
                  	 &nbsp;&nbsp;Need help? <a class="htext" href="<?=$general_func->admin_url?>testimonial/youtube_video_help.php" target="_blank">Click here</a>                   	
                  	
                  </td>
                </tr>       
                 
                <tr>
                  <td colspan="2" class="body_content-form" height="10"></td>
                </tr>
                  <tr>
                  <td width="17%" class="body_content-form">&nbsp;</td>
                  <td width="83%"><table width="261" border="0" align="left" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="41%"><table border="0" align="left" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="submit" type="submit" class="submit1" value="<?=$button?>" /></td>
                              <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                            </tr>
                          </table></td>
                        <td width="10%">&nbsp;</td>
                        <td width="49%">
                        	 <?php if($button=="Update"){?>
                        	 	<table border="0" align="left" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="back" onclick="location.href='<?=$general_func->admin_url?>testimonial/testimonials.php'"  type="button" class="submit1" value="Back" /></td>
                              <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                            </tr>
                          </table>
                        	 	<?php } ?>
                        	
                        	</td>
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