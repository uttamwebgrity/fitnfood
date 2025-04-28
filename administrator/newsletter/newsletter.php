<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";



//*********************  order delete *************************************//
if(isset($_GET['action']) && $_GET['action']=="delete")
{
	//delete from payment,cart
	@mysql_query("delete from tbl_newsletters where id=" . (int) $_GET['id']."");
	
	$_SESSION['msg']="The selected newsletter has been deleted!";
	$general_func->header_redirect($_SERVER['PHP_SELF']);
}
//*************************************************************************//

//*********************  resend *************************************//
if(isset($_GET['action']) && $_GET['action']=="edit"){
	//delete from payment,cart
	$rs_nl=mysql_fetch_object(mysql_query("select * from tbl_newsletters where id=" . (int) $_GET['id']." "));
	
	$subject=$rs_nl->subject;
	$email_address=$rs_nl->email_address;
	$message=$rs_nl->message;
	
	$button=" Resend  ";
}
else{
	$subject="";
	$message='';
	$email_address="";
	$button="  Send  ";
}
//*************************************************************************//



		
//**************************************************************************************//
		$url=basename(__FILE__)."?".(isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:'cc=cc');
		$recperpage=30;
		
		
		$sql="select id,subject,send_date,send_to from tbl_newsletters   order by send_date DESC";
		//-----------------------------------/Pagination------------------------------
		
		//print $sql;
		if(isset($_GET['in_page'])&& $_GET['in_page']!="")
			$page=$_GET['in_page'];
		else
			$page=1;
		
		$total_count=$db->num_rows($sql);
		$sql=$sql." limit ".(($page-1)*$recperpage).", $recperpage";
		
			if($page>1)
			{
				$url_prev=stristr($url,"&in_page=".$page)==FALSE?$url."&page=".($page-1):str_replace("&in_page=".$page,"&in_page=".($page-1),$url);
				$prev="<a href='$url_prev' class='nav'>Prev</a>";
			}
			else
				$prev="Prev";
				
			if((($page)*$recperpage)<$total_count)
			{
				$url_next=stristr($url,"&in_page=".$page)==FALSE?$url."&in_page=".($page+1):str_replace("&in_page=".$page,"&in_page=".($page+1),$url);
				$next="<a href='$url_next' class='nav'>Next</a>";
			}
			else
				$next="Next";
				
			$page_temp=(($page)*$recperpage);
			$page_temp=$page_temp<$total_count?$page_temp:$total_count;
			$showing=" Showing ".(($page-1)*$recperpage+1)." - ".$page_temp." of ".$total_count." | ";
		 
		//-----------------------------------/Pagination------------------------------
		//*************************************************************************************************//
		$result=$db->fetch_all_array($sql);
		//*******************************************************************************************************************//
 ?>
<script language="JavaScript">

function delete_confirmed(id){
	var decide=confirm("Are you sure you want to delete this newsletter?")
    if (decide)   {
    	location.href="<?=$_SERVER['PHP_SELF']?>?id="+id+"&action=delete"
    }  
}
	
//**********************************************************************//
</script>
<style type="text/css">
<!--
.style1 {font-weight: bold}
-->
</style>

 
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg">NEWSLETTER EMAIL</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg">
    
    <table width="791" border="0" align="center" cellpadding="6" cellspacing="0" >
	  	
        <tr>
		  <td width="779"> <br/>
          
          <table width="762" border="0" cellspacing="0" align="center" cellpadding="3"  style="border:0px #5b5265 solid;"> 
			<form method="post" action="<?=$general_func->admin_url?>newsletter/nsubject_sub.php" name="ff" onSubmit="return validate()">
				<input type="hidden" name="enter" value="2" />
				  <input type="hidden" name="login_form_id" value="<?=$_SESSION['login_form_id']?>" />
                
				<script language="javascript">
					function validate(){
						if(!validate_text(document.ff.subject,1,"Please enter subject")){
							document.ff.subject.value="";
							document.ff.subject.focus();
							return false;
						}
					}	
					</script>
				
				<?php
                  if (strlen(trim($_SESSION['msg']))>5){
                ?>
          <tr>
            <td width="38%" height="28" align="right" valign="middle" class="htext">&nbsp;</td>
            <td width="76%" class="htext" valign="top"><font color="#bd0000"><?php echo $_SESSION['msg']; $_SESSION['msg']=""; ?></font></td>
          </tr>
          <?php } ?>
          
          
                <tr>
				  <td width="38%" align="right"><font class="htext"><font class="form_required-field"> *</font> Send To :</font></td>
				  <td width="76%" align="left">
				  	<select name="send_to" style="width: 180px;">
				  		<option value="0" selected="selected">All Subscribers</option>
				  					  		
				  		
				  	</select>
				  	
				  &nbsp;</td>
				</tr>
				<tr>
				  <td width="38%" align="right"><font class="htext"><font class="form_required-field"> *</font> Subject :</font></td>
				  <td width="76%" align="left"><input name="subject" type="text" value="<?php echo $subject; ?>"   class="form_inputbox" size="76"  maxlength="255"/>
				  &nbsp;</td>
				</tr>
				<tr>              
            		<td align="center" colspan="2">
					
			    <?php
					include("../fckeditor/fckeditor.php") ;
					$sBasePath ="fckeditor/";
					$oFCKeditor = new FCKeditor('message') ;
					$oFCKeditor->BasePath	= $sBasePath ;
					$oFCKeditor->Height = '400' ;
					$oFCKeditor->width = '400' ;
					$oFCKeditor->Value		= $message;
					$oFCKeditor->Create();
					?>				</tr>			
				
                <tr>
          	<td colspan="4" height="30" align="center">
            <table width="879" border="0" align="center" cellpadding="0" cellspacing="0">
<tr>
                  <td width="30%"></td>
      <td width="14%">
      <table border="0" align="left" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="5" align="left" valign="top"><img src="../administrator/images/button-curve-left.png" alt="" width="5" height="22" /></td>
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="submit" type="submit" class="submit1" value="<?=$button?>" /></td>
                              <td width="5" align="right" valign="top"><img src="../administrator/images/button-curve-right.png" alt="" width="5" height="22" /></td>
                            </tr>
                  </table></td>
                  <td width="1%" idth="5%">&nbsp;</td>
      <td width="55%">                   
                        <?php if($_GET['action']=="edit"){?>
  <table border="0" align="left" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="5" align="left" valign="top"><img src="../administrator/images/button-curve-left.png" alt="" width="5" height="22" /></td>
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="back" onclick="location.href='<?=$general_func->admin_url?>newsletter/newsletter.php'"  type="button" class="submit1" value="Back" /></td>
                              <td width="5" align="right" valign="top"><img src="../administrator/images/button-curve-right.png" alt="" width="5" height="22" /></td>
                            </tr>
                          </table>					
						<?php  }else 
							echo "&nbsp;";
						 ?></td>
                </tr>
                    </table></td>
          </tr>
		    </form>
		</table></td>
		</tr>
        	
	  </table>
      <form name="frmcategory" action="<?=$_SERVER['PHP_SELF']?>" method="post">
	<input type="hidden" name="action" value="Delete All" />
	<input type="hidden" name="return_url" value="<?=$url?>">
	   <table border="0" width="814" cellspacing="1" align="center" cellpadding="4"  class="filegroup1">
          
          
        <tr>
			
			<td  colspan="4" class="htext"  align="right" ><?=$total_count?> record(s) found.&nbsp;</td>
		</tr>
		<tr>
			<td width="100" class="table_heading">Send To</td>			
            <td width="200" class="table_heading">Subject</td>
            <td width="100" class="table_heading" >Date</td> 
			<td width="200" class="table_heading" align="center">Action</td>
		</tr>
		<?php
		if(!count($result)){
			?>
			<tr>
			  <td  bgcolor="<?=$j%2==0?$general_func->color2:$general_func->color1;?>" colspan="4" align="center" class="message1"><br/>Sorry, no record found.<br/><br/>			  </td>
			</tr>
			
			<?php
		}else{
			for($j=0;$j<count($result);$j++){
				?>
				<tr bgcolor="<?=$j%2==0?$general_func->color2:$general_func->color1;?>">
				 
				       <td  class="table_content-blue"><?php
				       if($result[$j]['send_to'] == 0)
				       	 echo "All Subscribers";
					   else if($result[$j]['send_to'] == 1)
				       	 echo "Individuals";
					   else if($result[$j]['send_to'] == 2)
				       	 echo "Employees"; 
					   else {
						   echo "Organisations";
					   }
						
				       
				       ?></td>
                <td  class="table_content-blue"><?=$result[$j]['subject']?></td>
                 <td  class="table_content-blue"><?=date("m/d/Y h:i A",strtotime($result[$j]['send_date']))?></td>
                 
               
                  <td  class="display_data" align="center">
                 <input name="button" type="button" class="submit_button" onClick="location.href='<?=$general_func->admin_url?>newsletter/newsletter.php?id=<?php echo $result[$j]['id']?>&action=edit'" value="Copy">&nbsp;
					<input type="button" value="View" class="submit_button"  onClick="location.href='<?=$general_func->admin_url?>newsletter/view_newsletter.php?id=<?php echo $result[$j]['id']?>&return_url=<?=urlencode($url)?>'">&nbsp;
					<input type="button" value="Delete" class="submit_button" onClick="javascript:delete_confirmed('<?php echo $result[$j]['id']?>')" /> </td>
		  </tr>
				<?php
				//print $total_tithes ;
			}
			 }
		?>
	  </table>
	  </form>
      	  <?php 
		if ($total_count>$recperpage) { 
		?>
		<table width="761" height="20" border="0"  align="center"  cellpadding="0" cellspacing="0">
<tr>
				<td width="295" align="left" valign="bottom" class="htext">
						&nbsp;Jump to page 
				<select name="in_page" style="width:45px;" onChange="javascript:location.href='<?php echo str_replace("&in_page=".$page,"",$url);?>&in_page='+this.value;">
				  <?php for($m=1; $m<=ceil($total_count/$recperpage); $m++) {?>
				  <option value="<?php echo $m;?>" <?php echo $page==$m?'selected':''; ?>><?php echo $m;?></option>
				  <?php }?>
				</select>
				of 
		  <?php echo ceil($total_count/$recperpage); ?>	  </td>
		  <td width="466" align="right" valign="bottom" class="htext"><?php echo " ".$showing." ".$prev." ".$next." &nbsp;";?></td>
		  </tr>
	  </table>

<!-- / show category -->
		<?php  }?>
      <br/>
      <br/>

    
    
    </td>
  </tr>
</table>
<?php
include("../foot.htm");
?>
