<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";


$original=$path_depth . "banner_images/";



if(isset($_GET['action']) && $_GET['action']=='delete'){
	$sql="select banner_path from banners where page_id='".$_REQUEST['id'] ."' limit 1";	
	$result=$db->fetch_all_array($sql);
	
	if(count($result) ==1){
		@unlink($original.$result[0]['banner_path']);
	}
	
	
	$sql="select page_name from static_pages where id='".$_REQUEST['id'] ."' limit 1";	
	$result=$db->fetch_all_array($sql);
	
	@unlink($path_depth . $result[0]['page_name']);
	
	$db->query_delete("banners","page_id='".$_REQUEST['id'] ."'");		
	$db->query_delete("static_pages","id='".$_REQUEST['id'] ."'");
	
	$_SESSION['msg']="Your selected static page deleted!";
	$general_func->header_redirect($_REQUEST['url']);
}
 
?>

<script language="JavaScript">

function validate_search(){
	if(!validate_text(document.frmsearch.cd,1,"Enter page title.")){
		return false;
	}
}

function del(id,url,name){
	var a=confirm("Are you sure, you want to delete page: " + name +"?")
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
          <td align="left" valign="middle" class="body_tab-middilebg">Static Pages</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
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
                      <td align="center" valign="top">
                       
                        <table width="501" border="0" align="center" cellpadding="0" cellspacing="0">
                          <form name="frmsearch"  method="post" action="<?=$_SERVER['PHP_SELF']?>" onsubmit="return validate_search();">
                           <input type="hidden" name="enter" value="3" />
                          
                            <tr>
                              <td width="123" align="right" valign="middle" class="content_employee" style="padding-right: 5px;">  Page  Name:</td>
                              <td width="240" align="left" valign="middle"><input type="text" name="cd"  value="<?=$_REQUEST['cd']?>" autocomplete="OFF" size="35" class="inputbox_employee-listing" /></td>
                              <td width="138" align="left" valign="middle">
                              <table border="0" align="left" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="button" type="submit" class="submit1" value="Search" /></td>
                              <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                            </tr>
                        </table></td>
                            </tr>
                          </form>
                        </table>
                        <p style="text-align:center;"><font class="text_numbering"><?=$general_func->A_to_Z($_SERVER['PHP_SELF'])?></font></p>
                       </td>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
        </tr>        
         <?php
				//**************************************************************************************//
				$url=$_SERVER['PHP_SELF']."?".(isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:'cc=cc');
				
				$recperpage=$general_func->admin_recoed_per_page;
				$order_by=" display_order + 0 ASC";
				$query="where 1";				
				
				if(isset($_REQUEST['key']) && trim($_REQUEST['key']) != NULL)
					$query .=" and page_heading LIKE '" .trim($_REQUEST['key']). "%'";
				else if(isset($_REQUEST['enter']) && (int)$_REQUEST['enter']==3)
					$query .=" and page_heading LIKE '" .trim($_REQUEST['cd']). "%'";				
				
				 $sql="select id,page_heading,title,display_order,parent_id,page_position from static_pages $query order by $order_by";
				
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
          <td align="left" valign="top"><table width="750" align="center" border="0" 
cellpadding="5" cellspacing="1">
              <tr>
                <td  class="text_numbering" colspan="2" >               
               <table border="0"  cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="button" type="button" class="submit1" value="Add New" onClick="location.href='<?=$general_func->admin_url?>static-pages/custom-static-pages-new.php'" /></td>
                              <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                            </tr>
                   </table>                   
                   </td>
                
                <td class="text_numbering" colspan="4" align="right"><?=$total_count?> pages found.&nbsp;</td>
              </tr>
              <tr>
                <td width="150"  align="left" valign="middle" class="table_heading">Page Name</td>
                <td width="150"  align="left" valign="middle" class="table_heading">Page Title</td>
                <td width="150"  align="left" valign="middle" class="table_heading">Parent Page</td>
                <td width="100"  align="center" valign="middle" class="table_heading">Page Position</td>
                <td width="100"  align="center" valign="middle" class="table_heading">Display Order</td>
                <td width="100"  align="center" valign="middle" class="table_heading">Action</td>
              </tr>
			
			<?php if(count($result) == 0){?>
                	<tr>
                		<td colspan="6" align="center" bgcolor="#f5f7fa" valign="middle" height="50" class="message_error">Sorry, no pages found!</td>
       		  </tr>
				<?php }else{
					for($j=0;$j<count($result);$j++){?>
                     <tr bgcolor="<?=$j%2==0?$general_func->color2:$general_func->color1;?>">
                       
                        <td align="left" valign="middle" class="table_content-blue"><?=$result[$j]['page_heading']?></td>
                       <td align="left" valign="middle" class="table_content-blue"><?=$result[$j]['title']?></td> 
                       
                       <td align="left" valign="middle" class="table_content-blue"><?php
                                             
                       	if($result[$j]['parent_id'] > 0){
                      		echo $general_func->parent_static_page($result[$j]['parent_id']);						
                       	}else{
                       		echo "&nbsp;";
						}
                       ?></td> 
                       <td align="left" valign="middle" class="table_content-blue"><?php
                       $page_position=array();					   
					   $page_position=explode(",",$result[$j]['page_position']);
                       
                       $show_position="";
					   
                      	if(in_array(1,$page_position))
					   		$show_position .="Header, ";
					   	if(in_array(2,$page_position))
					   		$show_position .= "Left Sidebar, ";
					   	if(in_array(3,$page_position))
					   		$show_position .="Right Sidebar, ";
						if(in_array(4,$page_position))
					   		$show_position .= "Footer, ";
					 	if(in_array(5,$page_position))
                       		$show_position .= "Do not show on menu, ";
						
						echo substr($show_position,0,-2);
						
						
                       ?></td> 
                       <td align="center" valign="middle" class="table_content-blue"><?=$result[$j]['display_order']?></td> 
                       
                       <td  align="center" valign="middle" class="table_content-blue">
                      
                      <img src="images/edit.png" onclick="location.href='<?=$general_func->admin_url?>static-pages/custom-static-pages-new.php?id=<?=$result[$j]['id']?>&action=EDIT&return_url=<?=urlencode($url)?>'" style="cursor:pointer;"  title="EDIT" alt="EDIT" />
                      
          				 <?php if($result[$j]['id'] != 1){?>
          				 	 &nbsp;&nbsp;&nbsp;&nbsp;          				
          	        <img src="images/delete.png" title="DELETE" alt="DELETE" onclick="del('<?=$result[$j]['id']?>','<?=urlencode($url)?>','<?=$result[$j]['title']?>')" style="cursor:pointer;" />         
			<?php } ?>
          </td>
            </tr>
					<?php }
				}
			
			
	  		?>
            <tr>
                <td colspan="9" align="center" valign="middle" height="4"></td>
            </tr> 
            <tr>
                <td colspan="9" align="center" valign="middle" height="30" class="table_content-blue">
                  <?php 
		if ($total_count>$recperpage) {
		?>
		<table width="795" height="20" border="0"  cellpadding="0" cellspacing="0">
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
		  <td width="467" align="right" valign="bottom" class="htext"><?php echo " ".$showing." ".$prev." ".$next." &nbsp;";?></td>
		  </tr>
	  </table>

    <!-- / show category -->
		<?php  }?></td>
              </tr>
              <tr>
                <td colspan="9" align="center" valign="middle" height="30" class="table_content-blue"></td>
              </tr>
          </table></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<?php
include("../foot.htm");
?>