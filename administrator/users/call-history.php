<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";


$data=array();


if(isset($_REQUEST['name']) && trim($_REQUEST['name']) !=NULL){
	$_SESSION['user_name']=urldecode(trim($_REQUEST['name']));	
}	

if(isset($_REQUEST['return_url']) && trim($_REQUEST['return_url']) !=NULL){
	$_SESSION['scheduled_return_url']=trim($_REQUEST['return_url']);	
}		
	
if(isset($_REQUEST['id']) && trim($_REQUEST['id']) !=NULL){
	$_SESSION['user_id']=trim($_REQUEST['id']);	
}	


if(isset($_GET['action']) && $_GET['action']=='delete'){			
	
	@mysql_query("delete from  call_history  where id=" . intval($_REQUEST['call_id']) . "");
	$_SESSION['msg']="Your selected call history deleted!";
	$general_func->header_redirect($_SERVER['PHP_SELF']);
}
 


if(isset($_REQUEST['action']) && $_REQUEST['action']=="EDIT"){		
	
	$sql="select * from call_history where id=" . intval($_REQUEST['call_id'])  . " and user_id=" . intval($_SESSION['user_id'])  . "  limit 1";
	$result=$db->fetch_all_array($sql);	
		
	$call_details=$result[0]['call_details'];	
	$button="Update";
}else{			
	$call_details="";	
	$button="Add New";
}

if(isset($_POST['enter']) && $_POST['enter']=="yes" && trim($_POST['login_form_id'])==$_SESSION['login_form_id']){
	
	$call_details=filter_var(trim($_REQUEST['call_details']), FILTER_SANITIZE_STRING);	 
	
	if($_POST['submit']=="Add New"){		
		$data['call_details']=$call_details;
		$data['user_id']=trim($_SESSION['user_id']);
		$data['date_added']=$current_date_time;
		$_SESSION['msg']="Call history successfully recorded!";
		
		$db->query_insert("call_history",$data);		
		$general_func->header_redirect($_SERVER['PHP_SELF']);			

	}else{		
		$data['call_details']= $call_details;
		$data['user_id']=trim($_SESSION['user_id']);
		$data['date_modified']=$current_date_time;
		
		$_SESSION['msg']="Call history successfully updated!";		
		$db->query_update("call_history",$data,"id='".$_REQUEST['call_id'] ."'");
					
		$general_func->header_redirect($_SERVER['PHP_SELF']);
	}	
}	
?>

<script type="text/javascript">	

function validate(){
	if(!validate_text(document.ff.call_details,1,"Enter call details"))
		return false;
}	

function del(id){
	var a=confirm("Are you sure, you want to delete your selected call history?");
    if (a){
    	location.href="<?=$_SERVER['PHP_SELF']?>?call_id="+id+"&action=delete";
    }  
} 

</script>			

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg">Call history of user '<?=$_SESSION['user_name']?>'</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg"><form method="post" action="<?=$_SERVER['PHP_SELF']?>"  name="ff"  onsubmit="return validate()">
        <input type="hidden" name="enter" value="yes" />
        <input type="hidden" name="call_id" value="<?=$_REQUEST['call_id']?>" /> 
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
                  <td class="body_content-form" valign="top">Call Details :<font class="form_required-field"> *</font></td>
                  <td  class="body_content-form"  valign="top"><textarea rows="6" cols="70" name="call_details" class="form_textarea"><?=$call_details?></textarea></td>
                </tr>
                 
              </table></td>
            <td width="8" align="left" valign="top">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="4" height="30" align="center"><table width="879" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="43%"><table border="0" align="right" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                        <td align="left" valign="middle" class="body_tab-middilebg"><input name="submit" type="submit" class="submit1" value="<?=$button?>" /></td>
                        <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                      </tr>
                    </table></td>
                  <td width="4%"></td>
                  <td width="53%"><?php if($button !="Add New"){?>
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
        </table>
      </form></td>
  </tr>
  <tr>
  	<td style="height: 20px;"></td>
  </tr>
  <tr>
    <td align="left" valign="top" ><table width="100%" border="0" cellspacing="0" cellpadding="0">
        
        <?php
		  $sql_call="select * from call_history where user_id= '" . $_SESSION['user_id']  . "'   order by date_added DESC";
          $result_call=$db->fetch_all_array($sql_call);
		  $total_call=count($result_call);
			//*******************************************************************************************************************//
			?>
        <tr>
          <td align="left" valign="top"><table width="900" align="center" border="0" 
cellpadding="10" cellspacing="1">
            
              <tr>
                <td width="650"  align="left" valign="middle" class="table_heading">Call Details</td>  
                <td width="150"  align="left" valign="middle" class="table_heading">Date Called</td>
                          
                <td width="80"  align="center" valign="middle" class="table_heading">Action</td>
              </tr>
              <?php if($total_call == 0){?>
              <tr>
                <td colspan="3" align="center" bgcolor="#f5f7fa" valign="middle" height="50" class="message_error">Sorry, no call has been made yet!</td>
              </tr>
              <?php }else{
					 for($call=0; $call<$total_call; $call++){ ?>
              <tr bgcolor="<?=$call%2==0?$general_func->color2:$general_func->color1;?>">
                <td align="left" valign="middle" class="table_content-blue"><?=nl2br($result_call[$call]['call_details'])?></td>				            
                <td  align="left" valign="middle" class="table_content-blue"><?=date("M d, Y h:i A",strtotime($result_call[$call]['date_added']))?></td> 
                
               
                <td  align="center" valign="middle" class="table_content-blue">
                	
                	<img src="images/edit.png" onclick="location.href='<?=$general_func->admin_url?>users/call-history.php?call_id=<?=$result_call[$call]['id']?>&action=EDIT'" style="cursor:pointer;"  title="EDIT" alt="EDIT" />
                	
                	&nbsp;&nbsp;&nbsp;&nbsp; <img src="images/delete.png" title="DELETE" alt="DELETE" onclick="del('<?=$result_call[$call]['id']?>');" style="cursor:pointer;" />
                	
                	 </td>
              </tr>
              <?php }
				}
			
			
	  		?>
              <tr>
                <td colspan="8" align="center" valign="middle" height="4"></td>
              </tr>
              <tr>
                <td colspan="8" align="center" valign="middle" height="30" class="table_content-blue"><?php 
		if ($total_count>$recperpage) {
		?>
                  <table width="795" height="20" border="0"  cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="295" align="left" valign="bottom" class="htext">&nbsp;Jump to page
                        <select name="in_page" style="width:45px;" onChange="javascript:location.href='<?php echo str_replace("&in_page=".$page,"",$url);?>&in_page='+this.value;">
                          <?php for($m=1; $m<=ceil($total_count/$recperpage); $m++) {?>
                          <option value="<?php echo $m;?>" <?php echo $page==$m?'selected':''; ?>><?php echo $m;?></option>
                          <?php }?>
                        </select>
                        of <?php echo ceil($total_count/$recperpage); ?> </td>
                      <td width="467" align="right" valign="bottom" class="htext"><?php echo " ".$showing." ".$prev." ".$next." &nbsp;";?></td>
                    </tr>
                  </table>
                  <!-- / show category -->
                  <?php  }?></td>
              </tr>
              <tr>
                <td colspan="8" align="center" valign="middle" height="30" class="table_content-blue"></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
  
</table>
<?php
include("../foot.htm");
?>
