<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";

$small=$path_depth . "photo/small/";
$original=$path_depth . "photo/";


if(isset($_GET['action']) && $_GET['action']=='delete'){		
		
	$payment_found=$db->num_rows("select id from payment where user_id='". intval(mysql_real_escape_string($_REQUEST['id'])) ."' limit 1");
	$orders_found=$db->num_rows("select id from orders  where user_id='". intval(mysql_real_escape_string($_REQUEST['id'])) ."' limit 1");
	
	if($payment_found == 0 && $orders_found == 0){		
		$sql="select photo from users where id=" . intval($_REQUEST['id']) . " limit 1";
		$result=$db->fetch_all_array($sql);
		
		if(count($result) > 0){
			@unlink($small.$result[0]['photo']);
			@unlink($original.$result[0]['photo']);		
		}
			
		$db->query_delete("video_types_access_permission","user_id='". intval($_REQUEST['id']) ."'");	
		$db->query_delete("call_history","user_id='". intval($_REQUEST['id']) ."'");
		$db->query_delete("users","id='". intval($_REQUEST['id']) ."'");
	
		$_SESSION['msg']="Your selected user account deleted!";
	}else{
		$meassage="Your selected user account can not be deleted as ";
		$meassage .= " user has already ordered his/her meal!";		
		$_SESSION['msg']=$meassage;			
	}	
		
	$general_func->header_redirect($_REQUEST['url']);
}
 
?>
<script language="JavaScript">

function validate_search(){
	if(!validate_text(document.frmsearch.cd,1,"Enter your search value.")){
		return false;
	}
}

function del(id,url,name){
	var a=confirm("Are you sure, you want to delete user: '" + name +"'\nAnd all data related to it?")
    if (a){
    	location.href="<?=$_SERVER['PHP_SELF']?>?id="+id+"&action=delete&url="+url;
    }  
} 
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg">Users</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg"><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="left" valign="top"><img src="images/spacer.gif" alt="" width="14" height="14" /></td>
              </tr>
              <?php if(isset($_SESSION['msg']) && trim($_SESSION['msg']) != NULL){?>
              <tr>
                <td class="message_error" align="center"><?=$_SESSION['msg']; $_SESSION['msg']="";?></td>
              </tr>
              <tr>
                <td  class="body_content-form" height="10"></td>
              </tr>
              <?php  } ?>
              <tr>
                <td align="left" valign="top"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                      <td align="center" valign="top"><table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
                          <form name="frmsearch"  method="post" action="<?=$_SERVER['PHP_SELF']?>" onsubmit="return validate_search();">
                            <input type="hidden" name="enter" value="3" />
                            <tr>
                            	<td width="100" align="right" valign="middle" class="content_employee" style="padding-right: 5px;">Search By:</td>
                              <td width="120" align="right" valign="middle" class="content_employee" style="padding-right: 5px;">
                              	<select name="search_by" class="inputbox_select" style="width: 130px; padding: 2px 0px;">
		            				<option value="1" <?=$_REQUEST['search_by']==1?'selected="selected"':'';?>>Surname</option>
		            				<option value="2" <?=$_REQUEST['search_by']==2?'selected="selected"':'';?>>First Name</option>				
		            
	            					</select></td>
                              <td width="200" align="left" valign="middle"><input type="text" name="cd"  value="<?=$_REQUEST['cd']?>" autocomplete="OFF" size="35" class="inputbox_employee-listing" /></td>
                              <td width="150" align="left" valign="middle"><table border="0" align="left" cellpadding="0" cellspacing="0">
                                  <tr>
                                    <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                                    <td align="left" valign="middle" class="body_tab-middilebg"><input name="button" type="submit" class="submit1" value="Search" /></td>
                                    <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                                  </tr>
                                </table></td>
                            </tr>
                          </form>
                        </table>
                        <p style="text-align:center;"><font class="text_numbering">
                          <?=$general_func->A_to_Z($_SERVER['PHP_SELF'])?>
                          </font></p></td>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
        </tr>
        <?php
				//**************************************************************************************//
				$url=$_SERVER['PHP_SELF']."?".(isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:'cc=cc');
				
				$recperpage=$general_func->admin_recoed_per_page;
				//$recperpage=5;
				
								
				$order_by="";
				$display_oder_type="ASC";		
	
				if(isset($_REQUEST['display_oder']) && trim($_REQUEST['display_oder']) != NULL){
					if(trim($_REQUEST['display_oder']) == "full_name"){//***********name
						if(trim($_REQUEST['display_oder_type']) == "ASC"){
							$display_oder_type="DESC";
							$order_by .="fname ASC";
						}else{
							$display_oder_type="ASC";
							$order_by .="fname DESC";
						}											
					}else if(trim($_REQUEST['display_oder']) == "status"){//***********status
						if(trim($_REQUEST['display_oder_type']) == "ASC"){
							$display_oder_type="DESC";
							$order_by .="status + 0 ASC";
						}else{
							$display_oder_type="ASC";
							$order_by .="status + 0 DESC";
						}						
					}else{
						$order_by .="fname ASC";
					}					
				}else{
					$order_by .="fname ASC";
				}
				
				$query="where 1";
				
				
				if(isset($_REQUEST['key']) && trim($_REQUEST['key']) != NULL)
					$query .=" and lname LIKE '" .trim($_REQUEST['key']). "%'";
				else if(isset($_REQUEST['enter']) && (int)$_REQUEST['enter']==3){
					$field=$_REQUEST['search_by']==1?'lname':'fname';
					$query .=" and $field LIKE '" .trim($_REQUEST['cd']). "%'";
				}
					
				
				
				$sql="select  id,CONCAT(fname,' ',lname) as name,email_address,password,street_address,status,cc_or_dd_created,cc_or_dd,facebook_id,google_id from users ";				
				$sql .=" $query order by $order_by";
				
				//-	----------------------------------/Pagination------------------------------
				
				if(isset($_GET['in_page'])&& $_GET['in_page']!="")
					$page=$_GET['in_page'];
				else
					$page=1;
				
				$total_count=$db->num_rows($sql);
				$sql=$sql." limit ".(($page-1)*$recperpage).", $recperpage";
				
					if($page>1)
					{
						$url_prev=stristr($url,"&in_page=".$page)==FALSE?$url."&page=".($page-1):str_replace("&in_page=".$page,"&in_page=".($page-1),$url);
						$prev="&nbsp;<a href='$url_prev' class='nav'>Prev</a>";
					}
					else
						$prev="&nbsp;Prev";
						
					if((($page)*$recperpage)<$total_count)
					{
						$url_next=stristr($url,"&in_page=".$page)==FALSE?$url."&in_page=".($page+1):str_replace("&in_page=".$page,"&in_page=".($page+1),$url);
						$next="&nbsp;<a href='$url_next' class='nav'>Next</a>";
					}
					else
						$next="&nbsp;Next";
						
					$page_temp=(($page)*$recperpage);
					$page_temp=$page_temp<$total_count?$page_temp:$total_count;
					$showing=" Showing ".(($page-1)*$recperpage+1)." - ".$page_temp." of ".$total_count." | ";
				 
				//-----------------------------------/Pagination------------------------------
				//*************************************************************************************************//
				$result=$db->fetch_all_array($sql);
			//*******************************************************************************************************************//
			?>
        <tr>
          <td align="left" valign="top"><table width="950" align="center" border="0" 
cellpadding="5" cellspacing="1">
              <tr>
                <td  class="text_numbering">                	
                	<table border="0"  cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                      <td align="left" valign="middle" class="body_tab-middilebg"><input name="button" type="button" class="submit1" value="Add New" onClick="location.href='<?=$general_func->admin_url?>users/users-new.php'" /></td>
                      <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                    </tr>
                  </table>
                
                  </td>
                 
                <td class="text_numbering" colspan="6" align="right"><?=$total_count?> user(s) found.</td>
              </tr>
              <tr>
                <td width="90"  align="left" valign="middle" class="table_heading">
                <a href="users/users.php?display_oder=full_name&display_oder_type=<?=$display_oder_type?>" class="header_link">Name</a></td>
  				<td width="160"  align="left" valign="middle" class="table_heading">Email Address</td>               
                  <td width="70"  align="center" valign="middle" class="table_heading">Password</td>
                <td width="100"  align="center" valign="middle" class="table_heading">Street Address</td>                
                <td width="50"  align="center" valign="middle" class="table_heading">Membership?</td>              
                 <td width="50"  align="center" valign="middle" class="table_heading">                 	
                 	 <a href="users/users.php?display_oder=status&display_oder_type=<?=$display_oder_type?>" class="header_link">Status</a></td>             
                <td width="160"  align="center" valign="middle" class="table_heading">Action</td>
              </tr>
              <?php if(count($result) == 0){?>
              <tr>
                <td colspan="7" align="center" bgcolor="#f5f7fa" valign="middle" height="50" class="message_error">Sorry, no users  found!</td>
              </tr>
              <?php }else{	
								
				
					for($j=0;$j<count($result);$j++){?>
              <tr bgcolor="<?=$j%2==0?$general_func->color2:$general_func->color1;?>">
                <td align="left" valign="middle" class="table_content-blue" style="cursor: pointer;" ondblclick="location.href='<?=$general_func->admin_url?>users/users-view.php?id=<?=$result[$j]['id']?>&action=VIEW&return_url=<?=urlencode($url)?>'"><?=$result[$j]['name']?></td>
				<td align="left" valign="middle" class="table_content-blue"><?php				
				echo $result[$j]['email_address'];				
				?>
				</td>               
                
                <td  align="left" valign="middle" class="table_content-blue"><?php
                if(trim($result[$j]['facebook_id']) != NULL){
                	echo "Facebook User";
				}else if(trim($result[$j]['google_id']) != NULL){
					echo "Google User";
				}else{
				  echo $EncDec->decrypt_me($result[$j]['password']);	
				}
              ?></td>
                <td  align="center" valign="middle" class="table_content-blue"><?=$result[$j]['street_address']?></td> 
                  <td  align="center" valign="middle" class="table_content-blue">
                  	<?php 
                  	if($db_common->user_has_a_paid_week(intval($result[$j]['id']),1) > 0){                  		
						if($db_common->user_has_a_paid_week(intval($result[$j]['id']),2,$first_date_of_the_last_week,$last_date_of_the_last_week)){
							echo '<img src="images/small_last_week_order.png" alt="" style="vertical-align: bottom;" />';	
						}
                  		echo "<strong style='color: #ff0000;'> Platinum </strong>";
                  		if($db_common->user_has_a_paid_week(intval($result[$j]['id']),3,$first_date_of_the_current_week,$last_date_of_the_current_week)){
							echo '<img src="images/small_current_week_order.png" alt="" style="vertical-align: bottom;" />';	
						}
					}else {
						echo "Standard";
					} ?>
                  	
                  	</td>           
                <td  align="center" valign="middle" class="table_content-blue"><?=$general_func->show_status($result[$j]['status'])?></td>
                
                <td  align="left" valign="middle" class="table_content-blue">
                	<a  href="<?=$general_func->admin_url?>users/call-history.php?id=<?=$result[$j]['id']?>&name=<?=urlencode($result[$j]['name'])?>&action=callHistory&return_url=<?=urlencode($url)?>"><img src="images/call-history.png"   /></a>
                	<?php if(trim($result[$j]['facebook_id']) == NULL && trim($result[$j]['google_id']) == NULL){ ?>
                	&nbsp;                	
                	<img src="images/edit.png" onclick="location.href='<?=$general_func->admin_url?>users/users-new.php?id=<?=$result[$j]['id']?>&action=EDIT&return_url=<?=urlencode($url)?>'" style="cursor:pointer;"  title="EDIT" alt="EDIT" />
                	<?php }?>                	
                	&nbsp;<img src="images/view-details.png" onclick="location.href='<?=$general_func->admin_url?>users/users-view.php?id=<?=$result[$j]['id']?>&action=VIEW&return_url=<?=urlencode($url)?>'" style="cursor:pointer;"  title="VIEW" alt="VIEW" />
                	&nbsp;<img src="images/delete.png" title="DELETE" alt="DELETE" onclick="del('<?=$result[$j]['id']?>','<?=urlencode($url)?>','<?=$result[$j]['name']?>')" style="cursor:pointer;" />
                	
                	<?php if($result[$j]['cc_or_dd_created'] == 1 && trim($result[$j]['cc_or_dd']) != NULL){ ?>
                		&nbsp;<img src="images/creadi.png" onclick="location.href='<?=$general_func->admin_url?>users/update-cc-dd-info.php?id=<?=$result[$j]['id']?>&action=VIEW&return_url=<?=urlencode($url)?>'" style="cursor:pointer;"  title="VIEW" alt="VIEW" />
					<?php }	?>
                	
                	 </td>
              </tr>
              <?php }
				}
			
			
	  		
		if ($total_count>$recperpage) {
		?>
		 <tr>
                <td colspan="7" align="center" valign="middle" height="4"></td>
              </tr>
		 <tr>
                <td colspan="7" align="center" valign="middle" height="30" class="table_content-blue">
                  <table width="795" height="20" border="0"  cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="295" align="left" valign="bottom" class="htext">&nbsp;Jump to page
                        <select name="in_page" style="width:45px;" onChange="javascript:location.href='<?php echo str_replace("&in_page=".$page,"",$url);?>&in_page='+this.value;">
                          <?php for($m=1; $m<=ceil($total_count/$recperpage); $m++) {?>
                          <option value="<?php echo $m;?>" <?php echo $page==$m?'selected':''; ?>><?php echo $m;?></option>
                          <?php }?>
                        </select>
                        of <?php echo ceil($total_count/$recperpage); ?> </td>
                      <td width="467" align="right" valign="bottom" class="htext" style="text-align: right;"> <?php echo " ".$showing." ".$prev." ".$next." &nbsp;";?></td>
                    </tr>
                  </table>
                  </td>
              </tr>
                  <!-- / show category -->
                  <?php  }?>
              <tr>
                <td colspan="7" align="center" valign="middle" height="30" class="table_content-blue"></td>
              </tr>
              <tr>
                <td colspan="3" align="left" valign="middle" height="30" class="htext" style="font-size: 11px; line-height: 20px; padding-bottom: 10px;">
                <strong>Platinum Member:</strong> Who has paid at least for a week.<br/>
                <strong>Standard Member:</strong> Not yet paid for any week.
                </td>
                <td colspan="4" style="font-size: 11px; line-height: 20px;">
                	<img src="images/current_week_order.png" alt="" style="vertical-align: bottom;" /> Paid for current week.
                	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                	<img src="images/last_week_order.png" alt="" style="vertical-align: bottom;" /> Paid for last week.
                
                </td>
              </tr>
              
                   
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>
<?php
include("../foot.htm");
?>
