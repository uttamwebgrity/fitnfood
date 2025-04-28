<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";



$small=$path_depth ."snack_main/small/";
$original=$path_depth ."snack_main/";


if(isset($_GET['action']) && $_GET['action']=='delete'){
		
	$default_meals_found=0;
	$meal_plans_found=0;
		
	/*$default_meals_found=$db->num_rows("select id from  categories_default_meals  where meal_id='".$_REQUEST['id'] ."' limit 1");
	$meal_plans_found=$db->num_rows("select id from  meal_plan_meals where meal_id='".$_REQUEST['id'] ."' limit 1");*/
	
	if($default_meals_found == 0 && $meal_plans_found == 0){
		$sql="select photo_name from snacks where id=" . (int) $_REQUEST['id'] . " limit 1";
		$result=$db->fetch_all_array($sql);
			
		if(count($result) > 0){
			@unlink($small.$result[0]['photo_name']);
			@unlink($original.$result[0]['photo_name']);		
		}
		
		$db->query_delete("meal_plan_category_snacks","snack_id='".$_REQUEST['id'] ."'");	
		$db->query_delete("snacks","id='".$_REQUEST['id'] ."'");
	
		$_SESSION['msg']="Your selected snacks deleted!";
	}else{
		$meassage="Your selected snack can not be deleted as ";		
		$meassage .= $default_meals_found == 1?' default meal categories and ':'';		
		$meassage .= $meal_plans_found == 1?' meal plans and ':'';		
		$meassage = substr($meassage,0, -4);		
		$meassage .= " are already using this snack!";		
		$_SESSION['msg']=$meassage;			
	}
	
	$general_func->header_redirect($_REQUEST['url']);
}
 
?>
<script language="JavaScript">

function validate_search(){
	if(!validate_text(document.frmsearch.cd,1,"Enter snack name.")){
		return false;
	}
}

function del(id,url,name){
	var a=confirm("Are you sure, you want to delete snack: '" + name +"'\nAnd all data related to it?")
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
          <td align="left" valign="middle" class="body_tab-middilebg">Snacks</td>
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
                              <td width="123" align="right" valign="middle" class="content_employee" style="padding-right: 5px;">Snack Name:</td>
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
							$order_by .="status + 0 ASC";
						}else{
							$display_oder_type="ASC";
							$order_by .="status + 0 DESC";
						}
					}else if(trim($_REQUEST['display_oder']) == "name"){//***********status
						if(trim($_REQUEST['display_oder_type']) == "ASC"){
							$display_oder_type="DESC";
							$order_by .="name  ASC";
						}else{
							$display_oder_type="ASC";
							$order_by .="name  DESC";
						}					
					}else{
						$order_by .="modified DESC";
					} 
					
				}else{
					$order_by .="date_modified DESC";
				}

				
				$query="where 1";
				
				
				if(isset($_REQUEST['key']) && trim($_REQUEST['key']) != NULL)
					$query .=" and name LIKE '" .trim($_REQUEST['key']). "%'";
				else if(isset($_REQUEST['enter']) && (int)$_REQUEST['enter']==3)
					$query .=" and name LIKE '" .trim($_REQUEST['cd']). "%'";
				
				
				$sql="select id,name,price,status,details from snacks";
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
                      <td align="left" valign="middle" class="body_tab-middilebg"><input name="button" type="button" class="submit1" value="Add New" onClick="location.href='<?=$general_func->admin_url?>meals/snacks-new.php'" /></td>
                      <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                    </tr>
                 </table>
                  
                  </td>
                  <td > </td>
                <td class="text_numbering" colspan="6" align="right"><?=$total_count?>
                  snack(s) found.</td>
              </tr>
              <tr>
                <td width="170"  align="left" valign="middle" class="table_heading">
                <a href="meals/snacks.php?display_oder=name&display_oder_type=<?=$display_oder_type?>" class="header_link">Snack Name</a></td>
   				<td width="70"  align="center" valign="middle" class="table_heading">Price</td>
   				<td width="250"  align="center" valign="middle" class="table_heading">Details</td>
   				<td width="210"  align="left" valign="middle" class="table_heading">Plan Category</td>
   				
                 <td width="50"  align="center" valign="middle" class="table_heading">
                 	
                 	 <a href="meals/snacks.php?display_oder=status&display_oder_type=<?=$display_oder_type?>" class="header_link">Status</a></td>             
                <td width="100"  align="center" valign="middle" class="table_heading">Action</td>
              </tr>
              <?php if(count($result) == 0){?>
              <tr>
                <td colspan="5" align="center" bgcolor="#f5f7fa" valign="middle" height="50" class="message_error">Sorry, no snacks  found!</td>
              </tr>
              <?php }else{
					for($j=0;$j<count($result);$j++){?>
              <tr bgcolor="<?=$j%2==0?$general_func->color2:$general_func->color1;?>">
                <td align="left" valign="middle" class="table_content-blue"><?=$result[$j]['name']?></td>
				<td align="center" valign="middle" class="table_content-blue">$<?=$result[$j]['price']?> </td> 
				<td align="center" valign="middle" class="table_content-blue"><?=$result[$j]['details']?></td> 
				<td align="center" valign="middle" class="table_content-blue"><?=$db_common->plan_category($result[$j]['id'],2);?></td> 
				<td  align="left" valign="middle" class="table_content-blue"><?=$result[$j]['status']==1?'Active':'Inactive';?></td>
                <td  align="center" valign="middle" class="table_content-blue">
                
                	<img src="images/edit.png" onclick="location.href='<?=$general_func->admin_url?>meals/snacks-new.php?id=<?=$result[$j]['id']?>&action=EDIT&return_url=<?=urlencode($url)?>'" style="cursor:pointer;"  title="EDIT" alt="EDIT" /> 
                	&nbsp;&nbsp; <img src="images/delete.png" title="DELETE" alt="DELETE" onclick="del('<?=$result[$j]['id']?>','<?=urlencode($url)?>','<?=$result[$j]['name']?>')" style="cursor:pointer;" />
                	
                	 </td>
              </tr>
              <?php }
				}
			
			
	  		?>
              <tr>
                <td colspan="5" align="center" valign="middle" height="4"></td>
              </tr>
              <tr>
                <td colspan="5" align="center" valign="middle" height="30" class="table_content-blue"><?php 
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
                <td colspan="5" align="center" valign="middle" height="30" class="table_content-blue"></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>
<?php
include("../foot.htm");
?>
