<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";



$data=array();

$return_url=$_REQUEST['return_url'];


if(isset($_REQUEST['action']) && $_REQUEST['action']=="EDIT"){
	$sql="select * from articles where id=" . intval($_REQUEST['id']) . " limit 1";
	$result=$db->fetch_all_array($sql);
	
	$article_name=$result[0]['article_name'];	
	$article_details=$result[0]['article_details'];	
	$article_date_array=@explode("-",trim($result[0]['article_date']));
	
	$article_date=$article_date_array[2]."/".$article_date_array[1]."/".$article_date_array[0];
		
	$button="Update";
}else{
	$article_name="";
	$article_details="";
	$article_date="";	
	$button="Add New";
}


if(isset($_POST['enter']) && $_POST['enter']=="yes"  && trim($_POST['login_form_id'])==$_SESSION['login_form_id']){
			
	$article_name=filter_var(trim($_REQUEST['article_name']), FILTER_SANITIZE_STRING);	 
	$article_details=filter_var(trim($_REQUEST['article_details']), FILTER_SANITIZE_STRING); 
	$article_date=@explode("/",trim($_REQUEST['article_date']));	
	
	
	if($_POST['submit']=="Add New"){
		if($db->already_exist_inset("articles","article_name",$article_name)){
			$_SESSION['msg']="Sorry, your selected article name is already taken!";
		}else{
			$data['article_name']=$article_name;			
			$data['article_details']=$article_details;	
			$data['article_date']=$article_date[2]."-".$article_date[1]."-".$article_date[0];				
			$data['date_added']=$current_date_time;				
			
			$db->query_insert("articles",$data);				
			
			if($db->affected_rows > 0)
				$_SESSION['msg']="Article information successfully added.";
				
			$general_func->header_redirect($_SERVER['PHP_SELF']);
		}	

	}else{
		if($db->already_exist_update("articles","id",$_REQUEST['id'],"article_name",$article_name)){
			$_SESSION['msg']="Sorry, your specified article name is already taken!";
		}else{
			$data['article_name']=$article_name;			
			$data['article_details']=$article_details;	
			$data['article_date']=$article_date[2]."-".$article_date[1]."-".$article_date[0];			
			$data['date_modified']=$current_date_time;	

			$db->query_update("articles",$data,"id='".$_REQUEST['id'] ."'");
					
			
			if($db->affected_rows > 0)
				$_SESSION['msg']="Article information successfully updated.";
				
			$general_func->header_redirect($return_url);
		}
	}
}	

?>
<link href="jquery.datepick.css" rel="stylesheet">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="jquery.plugin.js"></script>
<script src="jquery.datepick.js"></script>
<script type="text/javascript">
	
function validate(){
	if(!validate_text(document.ff.article_name,1,"Enter article name"))
		return false;
		
	if(!validate_text(document.ff.article_details,1,"Enter article details"))
		return false;	
		
	if(!validate_text(document.ff.article_date,1,"Enter article date"))
		return false;
}

$(function() {
	$('#popupDatepicker').datepick();	
});
	
	
</script>



<table width="100%" border="0" cellspacing="0" cellpadding="0" >
   <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg"><?=$button?> Article</td>
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
                  <td width="21%" class="body_content-form" valign="top">Article Name:<font class="form_required-field"> *</font></td>
                  <td width="79%" valign="top"><input name="article_name" type="text" value="<?=$article_name?>" AUTOCOMPLETE=OFF class="form_inputbox" size="75" /></td>
                </tr>                 
                  <tr>
                  <td  class="body_content-form">Article Details:<font class="form_required-field"> *</font></td>
                  <td ><textarea name="article_details" class="form_textarea" cols="72" rows="6"><?=$article_details?></textarea></td>
                </tr>                    
                  <tr>
                  <td  class="body_content-form">Article Date:<font class="form_required-field"> *</font></td>
                  <td ><input type="text" id="popupDatepicker" AUTOCOMPLETE="OFF" name="article_date" value="<?=$article_date?>" readonly="readonly" class="form_inputbox" size="39"></td>
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
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="back" onclick="location.href='<?=$general_func->admin_url?>articles/articles.php'"  type="button" class="submit1" value="Back" /></td>
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