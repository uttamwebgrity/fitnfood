<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";


if(isset($_GET['action']) && $_GET['action']=='delete'){
		
	$db->query_delete("locations_trainers","location_id='".$_REQUEST['id'] ."'");		
	$db->query_delete("locations","id='".$_REQUEST['id'] ."'");
	
	$_SESSION['msg']="Your selected training location deleted!";
	$general_func->header_redirect($_REQUEST['url']);
}
 
?>
<script language="JavaScript">

function validate_search(){
	if(!validate_text(document.frmsearch.cd,1,"Enter  training location.")){
		return false;
	}
}

function del(id,url,name){
	var a=confirm("Are you sure, you want to delete training location: '" + name +"'\nAnd all data related to it?")
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
          <td align="left" valign="middle" class="body_tab-middilebg">Training Location</td>
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
                      <td align="center" valign="top"><table width="501" border="0" align="center" cellpadding="0" cellspacing="0">
                          <form name="frmsearch"  method="post" action="<?=$_SERVER['PHP_SELF']?>" onsubmit="return validate_search();">
                            <input type="hidden" name="enter" value="3" />
                            <tr>
                              <td width="123" align="right" valign="middle" class="content_employee" style="padding-right: 5px;">Location Name:</td>
                              <td width="240" align="left" valign="middle"><input type="text" name="cd"  value="<?=$_REQUEST['cd']?>" autocomplete="OFF" size="35" class="inputbox_employee-listing" /></td>
                              <td width="138" align="left" valign="middle"><table border="0" align="left" cellpadding="0" cellspacing="0">
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
				
				
				$order_by="";
				$display_oder_type="ASC";
		
	
				if(isset($_REQUEST['display_oder']) && trim($_REQUEST['display_oder']) != NULL){
					if(trim($_REQUEST['display_oder']) == "full_name"){//***********name
						if(trim($_REQUEST['display_oder_type']) == "ASC"){
							$display_oder_type="DESC";
							$order_by .="location_name ASC";
						}else{
							$display_oder_type="ASC";
							$order_by .="location_name DESC";
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
						$order_by .="location_name ASC";
					} 
					
				}else{
					$order_by .="location_name ASC";
				}

				
				$query="where 1";
				
				
				if(isset($_REQUEST['key']) && trim($_REQUEST['key']) != NULL)
					$query .=" and location_name LIKE '" .trim($_REQUEST['key']). "%'";
				else if(isset($_REQUEST['enter']) && (int)$_REQUEST['enter']==3)
					$query .=" and location_name LIKE '" .trim($_REQUEST['cd']). "%'";
				
				
				$sql="select l.id,location_name,location_latitude,location_longitude,street_address,status,name from locations l left join location_types t on l.location_type_id=t.id";				
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
                      <td align="left" valign="middle" class="body_tab-middilebg"><input name="button" type="button" class="submit1" value="Add New" onClick="location.href='<?=$general_func->admin_url?>training/locations-new.php'" /></td>
                      <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                    </tr>
                  </table>
                
                  </td>
                  <td><img style="vertical-align: bottom; margin-right: 5px;" src="images/training_time_slot.png"  title="Time Slots" alt="Time Slots" /><strong>Time Slots</strong></td>
                 
                <td class="text_numbering" colspan="4" align="right"><?=$total_count?> location(s) found.</td>
              </tr>
              <tr>
                <td width="120"  align="left" valign="middle" class="table_heading">
                <a href="training/locations.php?display_oder=full_name&display_oder_type=<?=$display_oder_type?>" class="header_link">Location Name</a></td>
  				<td width="190"  align="left" valign="middle" class="table_heading">Location Type</td>               
                  <td width="110"  align="center" valign="middle" class="table_heading">Latitude &amp; longitude</td>
                <td width="100"  align="center" valign="middle" class="table_heading">Street Address</td>   
                 <td width="60"  align="center" valign="middle" class="table_heading">                 	
                 	 <a href="training/locations.php?display_oder=status&display_oder_type=<?=$display_oder_type?>" class="header_link">Status</a></td>             
                <td width="100"  align="center" valign="middle" class="table_heading">Action</td>
              </tr>
              <?php if(count($result) == 0){?>
              <tr>
                <td colspan="6" align="center" bgcolor="#f5f7fa" valign="middle" height="50" class="message_error">Sorry, no training location found!</td>
              </tr>
              <?php }else{
					for($j=0;$j<count($result);$j++){?>
              <tr bgcolor="<?=$j%2==0?$general_func->color2:$general_func->color1;?>">
                <td align="left" valign="middle" class="table_content-blue"><?=$result[$j]['location_name']?></td>
				<td align="left" valign="middle" class="table_content-blue"><?php				
				echo $result[$j]['name'];				
				?>
				</td>               
                
                <td  align="left" valign="middle" class="table_content-blue"><?=$result[$j]['location_latitude']?> &amp; <?=$result[$j]['location_longitude']?> </td>
                <td  align="center" valign="middle" class="table_content-blue"><?=$result[$j]['street_address']?></td>                
                <td  align="center" valign="middle" class="table_content-blue"><?=$general_func->show_status($result[$j]['status'])?></td>
                
                <td  align="center" valign="middle" class="table_content-blue">  
                	<img src="images/training_time_slot.png" onclick="location.href='<?=$general_func->admin_url?>training/locations-time-slot.php?id=<?=$result[$j]['id']?>&action=TIMESLOTS&return_url=<?=urlencode($url)?>'" style="cursor:pointer; vertical-align: middle;"  title="Time Slots" alt="Time Slots" />
                	&nbsp;&nbsp;                	
                	<img src="images/edit.png" onclick="location.href='<?=$general_func->admin_url?>training/locations-new.php?id=<?=$result[$j]['id']?>&action=EDIT&return_url=<?=urlencode($url)?>'" style="cursor:pointer; vertical-align: middle;"  title="EDIT" alt="EDIT" />
                	&nbsp;&nbsp;<img src="images/delete.png" title="DELETE" alt="DELETE" onclick="del('<?=$result[$j]['id']?>','<?=urlencode($url)?>','<?=$result[$j]['location_name']?>')" style="cursor:pointer; vertical-align: middle;" />
                	
                	 </td>
              </tr>
              <?php }
				}
			
			
	  		?>
              <tr>
                <td colspan="6" align="center" valign="middle" height="4"></td>
              </tr>
              <tr>
                <td colspan="6" align="center" valign="middle" height="30" class="table_content-blue"><?php 
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
                <td colspan="6" align="center" valign="middle" height="30" class="table_content-blue"></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>
<?php
include("../foot.htm");
?>
