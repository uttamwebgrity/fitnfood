<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";

if(isset($_GET['action']) && $_GET['action']=='delete'){	
	$db->query_delete("meal_plan_meals","meal_plan_id='".$_REQUEST['id'] ."'");		
	$db->query_delete("meal_plans","id='".$_REQUEST['id'] ."'");	
	$_SESSION['msg']="Your selected meal plan successfully deleted!";
	$general_func->header_redirect($_REQUEST['url']);
}
 
?>
<script language="JavaScript">

function validate_search(){
	if(!validate_text(document.frmsearch.cd,1,"Enter meal plan name.")){
		return false;
	}
}

function del(id,url,name){
	var a=confirm("Are you sure, you want to delete meal plan: '" + name +"'\nAnd all data related to it?")
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
          <td align="left" valign="middle" class="body_tab-middilebg">Meal plans</td>
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
                              <td width="123" align="right" valign="middle" class="content_employee" style="padding-right: 5px;">Meal Plan Name:</td>
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
					if(trim($_REQUEST['display_oder']) == "status"){//***********status
						if(trim($_REQUEST['display_oder_type']) == "ASC"){
							$display_oder_type="DESC";
							$order_by .="m.status + 0 ASC";
						}else{
							$display_oder_type="ASC";
							$order_by .="m.status + 0 DESC";
						}
					}else if(trim($_REQUEST['display_oder']) == "name"){//***********status
						if(trim($_REQUEST['display_oder_type']) == "ASC"){
							$display_oder_type="DESC";
							$order_by .="m.name  ASC";
						}else{
							$display_oder_type="ASC";
							$order_by .="m.name  DESC";
						}	
					}else if(trim($_REQUEST['display_oder']) == "plan_category"){//***********status
						if(trim($_REQUEST['display_oder_type']) == "ASC"){
							$display_oder_type="DESC";
							$order_by .="plan_category  ASC";
						}else{
							$display_oder_type="ASC";
							$order_by .="plan_category  DESC";
						}				
						
					}else{
						$order_by .="m.modified DESC";
					} 
					
				}else{
					$order_by .="m.date_modified DESC";
				}

				
				$query="where 1";
				
				
				if(isset($_REQUEST['key']) && trim($_REQUEST['key']) != NULL)
					$query .=" and m.name LIKE '" .trim($_REQUEST['key']). "%'";
				else if(isset($_REQUEST['enter']) && (int)$_REQUEST['enter']==3)
					$query .=" and m.name LIKE '" .trim($_REQUEST['cd']). "%'";
				
				
				$sql="select m.*,pc.name as plan_category from meal_plans m";
				$sql .=" left join meal_plan_category pc on m.meal_plan_category_id=pc.id";				
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
          <td align="left" valign="top"><table width="880" align="center" border="0" 
cellpadding="5" cellspacing="1">
              <tr>
                <td  class="text_numbering">
                	
                	<table border="0"  cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                      <td align="left" valign="middle" class="body_tab-middilebg"><input name="button" type="button" class="submit1" value="Add New" onClick="location.href='<?=$general_func->admin_url?>meals/meal-plans-new.php'" /></td>
                      <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                    </tr>
                 </table>
                  
                  </td>
                  <td > </td>
                <td class="text_numbering" colspan="6" align="right"><?=$total_count?>
                  meal plan(s) found.</td>
              </tr>
              <tr>
                <td width="300"  align="left" valign="middle" class="table_heading">
                <a href="meals/meal-plans.php?display_oder=name&display_oder_type=<?=$display_oder_type?>" class="header_link">Plan Name</a></td>
   				<td width="80"  align="center" valign="middle" class="table_heading"># of Days</td>
   				<td width="80"  align="center" valign="middle" class="table_heading">Meal/Day </td>
   				<td width="80"  align="center" valign="middle" class="table_heading">Snack/Day </td>
   				<td width="100"  align="center" valign="middle" class="table_heading">
   					<a href="meals/meal-plans.php?display_oder=plan_category&display_oder_type=<?=$display_oder_type?>" class="header_link">Plan Category</a></td>
                <td width="50"  align="center" valign="middle" class="table_heading">Meals </td>
                 <td width="50"  align="center" valign="middle" class="table_heading">
                 	
                 	 <a href="meals/meal-plans.php?display_oder=status&display_oder_type=<?=$display_oder_type?>" class="header_link">Status</a></td>             
                <td width="120"  align="center" valign="middle" class="table_heading">Action</td>
              </tr>
              <?php if(count($result) == 0){?>
              <tr>
                <td colspan="8" align="center" bgcolor="#f5f7fa" valign="middle" height="50" class="message_error">Sorry, no meal plans  found!</td>
              </tr>
              <?php }else{
					for($j=0;$j<count($result);$j++){?>
              <tr bgcolor="<?=$j%2==0?$general_func->color2:$general_func->color1;?>">
                <td align="left" valign="middle" class="table_content-blue"><?=$result[$j]['name']?></td>
				<td align="center" valign="middle" class="table_content-blue"><?=$result[$j]['no_of_days']?> </td> 
				<td align="center" valign="middle" class="table_content-blue"><?=$result[$j]['meal_per_day']?></td> 
				<td align="center" valign="middle" class="table_content-blue"><?=$result[$j]['snack_per_day']?></td>
				<td align="center" valign="middle" class="table_content-blue"><?=$result[$j]['plan_category']?></td> 
				<td align="center" valign="middle" class="table_content-blue">
					<a  href="<?=$general_func->admin_url?>meals/meal-plans-meal.php?id=<?=$result[$j]['id']?>&name=<?=urlencode($result[$j]['name'])?>&action=mealplanmeal"><img src="images/icons/<?=$db->num_rows("select id from meal_plan_meals where meal_plan_id='". $result[$j]['id'] ."' limit 1")==1?'dsbdIco_green':'dsbdIco9_light';?>.png" /></a>
				</td>               
                                            
                <td  align="left" valign="middle" class="table_content-blue"><?=$result[$j]['status']==1?'Active':'Inactive';?></td>
                
                <td  align="center" valign="middle" class="table_content-blue">
                
                	<img src="images/edit.png" onclick="location.href='<?=$general_func->admin_url?>meals/meal-plans-new.php?id=<?=$result[$j]['id']?>&action=EDIT&return_url=<?=urlencode($url)?>'" style="cursor:pointer;"  title="EDIT" alt="EDIT" /> 
                	
                	&nbsp;&nbsp; <img src="images/view-details.png" onclick="location.href='<?=$general_func->admin_url?>meals/meal-plans-view.php?id=<?=$result[$j]['id']?>&action=VIEW&return_url=<?=urlencode($url)?>'" style="cursor:pointer;"  title="VIEW" alt="VIEW" />
                	
                	&nbsp;&nbsp; <img src="images/delete.png" title="DELETE" alt="DELETE" onclick="del('<?=$result[$j]['id']?>','<?=urlencode($url)?>','<?=$result[$j]['name']?>')" style="cursor:pointer;" />
                	
                	 </td>
              </tr>
              <?php }
				}
			
			
	  		?>
              <tr>
                <td colspan="7" align="center" valign="middle" height="4"></td>
              </tr>
              <tr>
                <td colspan="7" align="center" valign="middle" height="30" class="table_content-blue"><?php 
		if ($total_count>$recperpage) {
		?>
                  <table width="900" height="20" border="0"  cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="295" align="left" valign="bottom" class="htext">&nbsp;Jump to page
                        <select name="in_page" style="width:45px;" onChange="javascript:location.href='<?php echo str_replace("&in_page=".$page,"",$url);?>&in_page='+this.value;">
                          <?php for($m=1; $m<=ceil($total_count/$recperpage); $m++) {?>
                          <option value="<?php echo $m;?>" <?php echo $page==$m?'selected':''; ?>><?php echo $m;?></option>
                          <?php }?>
                        </select>
                        of <?php echo ceil($total_count/$recperpage); ?> </td>
                      <td width="467" class="htext" align="right"  style="text-align: right;" ><?php echo " ".$showing." ".$prev." ".$next." &nbsp;";?></td>
                    </tr>
                  </table>
                  <!-- / show category -->
                  <?php  }?></td>
              </tr>
              <tr>
                <td colspan="7" align="center" valign="middle" height="30" class="table_content-blue"></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>
<?php
include("../foot.htm");
?>
