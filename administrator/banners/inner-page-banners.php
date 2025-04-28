<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";


$original=$path_depth . "banner_images/";


if(isset($_GET['action']) && $_GET['action']=='delete'){
	/****************  delete all relevant tables and files ***********************/
	
	$sql="select banner_path from banners where id=" . (int) $_REQUEST['id'] . " limit 1";
	$result=$db->fetch_all_array($sql);
	
	if(count($result) > 0){
		@unlink($original.$result[0]['banner_path']);
	}
	
	/****************  / delete all relevant tables and files  ********************/
	$db->query_delete("banners","id='".$_REQUEST['id'] ."'");
	
	$_SESSION['msg']="Your selected banner deleted!";
	$general_func->header_redirect($_REQUEST['url']);
} 

?>
<script language="JavaScript">


function del(id,url){
	var a=confirm("Are you sure, you want to delete this banner?")
    if (a){
    	location.href="<?=$_SERVER['PHP_SELF']?>?id="+id+"&action=delete&url="+url;
    }  
} 
</script>
<script type="text/javascript" src="<?=$general_func->site_url?>highslide/highslide-with-html.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$general_func->site_url?>highslide/highslide.css" />
<script type="text/javascript">
    hs.graphicsDir = '<?=$general_func->site_url?>highslide/graphics/';
    hs.outlineType = 'rounded-white';
</script>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg">Inner Page Banners</td>
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
                <td class="message_error"><?=$_SESSION['msg']; $_SESSION['msg']="";?></td>
              </tr>
              <tr>
                <td  class="body_content-form" height="10"></td>
              </tr>
              <?php  } ?>
              
            </table></td>
        </tr>
        <tr>
          <td align="left" valign="middle" height="10"></td>
        </tr>
        <?php
				//**************************************************************************************//
				$url=$_SERVER['PHP_SELF']."?".(isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:'cc=cc');
				
				$recperpage=$general_func->admin_recoed_per_page;
				
				
				
				$order_by="b.display_order + 0 ASC";
				$query="where banner_type=2";
				
				$sql="select b.*,link_name from banners b left join static_pages s on b.page_id=s.id  $query order by $order_by";
				
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
          <td align="left" valign="top"><table width="774" align="center" border="0" 
cellpadding="5" cellspacing="1">
              <tr>
                <td  class="text_numbering"  >                	
                	<table border="0"  cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                      <td align="left" valign="middle" class="body_tab-middilebg"><input name="button" type="button" class="submit1" value="Add New" onclick="location.href='<?=$general_func->admin_url?>banners/inner-page-banners-new.php'" /></td>
                      <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                    </tr>
                  </table>                 
                  </td>
                <td class="text_numbering" align="right" colspan="2" ><?=$total_count?>
                  banners found.</td>
              </tr>
              <tr>               
               
               <td width="25%" align="center" valign="middle" bgcolor="#35619c" class="table_heading">Page Name</td>              
                <td width="60%" align="center" valign="middle" bgcolor="#35619c" class="table_heading">Image</td>
              
                <td width="15%" align="center" valign="middle" bgcolor="#35619c" class="table_heading">Action</td>
              </tr>
              <?php if(count($result) == 0){?>
              <tr>
                <td colspan="3" align="center" bgcolor="#f5f7fa" valign="middle" height="50" class="message_error">Sorry, no banners found</td>
              </tr>
              <?php }else{
					for($j=0;$j<count($result);$j++){?>
              <tr bgcolor="<?=$j%2==0?$general_func->color2:$general_func->color1;?>">
                <td  align="center" valign="top" class="table_content-blue"><?=$result[$j]['link_name']?></td>                
                <td  align="center" valign="top" class="table_content-blue"><a href="<?=$general_func->site_url.substr($original,6).$result[$j]['banner_path']?>" class="highslide" onclick="return hs.expand(this)"><img src="<?=$general_func->site_url.substr($original,6).$result[$j]['banner_path']?>" border="0"  width="430" /></a></td>
                <td  align="center" valign="top" class="table_content-blue">                	
                	<img src="images/edit.png" onclick="location.href='<?=$general_func->admin_url?>banners//inner-page-banners-new.php?id=<?=$result[$j]['id']?>&action=EDIT&return_url=<?=urlencode($url)?>'" style="cursor:pointer;" />&nbsp;&nbsp;&nbsp;&nbsp; 
                	<img src="images/delete.png" onclick="del('<?=$result[$j]['id']?>','<?=urlencode($url)?>')" style="cursor:pointer;" /> 
                	
                	</td>
              </tr>
              <?php }
				}
	  		?>
              <tr>
                <td colspan="3" align="center" valign="middle" height="4"></td>
              </tr>
              <tr>
                <td colspan="3" align="center" valign="middle" height="10" class="table_content-blue"></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td colspan="3" align="center" valign="middle" height="10" class="table_content-blue"><?php 
		if ($total_count>$recperpage) {
		?>
            <table width="715" height="20" border="0"  cellpadding="0" cellspacing="0">
              <tr>
                <td width="295" align="left" valign="bottom" class="htext">&nbsp;Jump to page
                  <select name="in_page" style="width:45px;" onChange="javascript:location.href='<?php echo str_replace("&in_page=".$page,"",$url);?>&in_page='+this.value;">
                    <?php for($m=1; $m<=ceil($total_count/$recperpage); $m++) {?>
                    <option value="<?php echo $m;?>" <?php echo $page==$m?'selected':''; ?>><?php echo $m;?></option>
                    <?php }?>
                  </select>
                  of <?php echo ceil($total_count/$recperpage); ?> </td>
                <td width="420" align="right" valign="bottom" class="htext"><?php echo " ".$showing." ".$prev." ".$next." &nbsp;";?></td>
              </tr>
              <tr>
                <td colspan="2" align="center" valign="middle" height="30" class="table_content-blue"></td>
              </tr>
            </table>
            <p>
              <!-- / show category -->
            </p>
            <p>
              <?php  }?>
            </p></td>
        </tr>
      </table></td>
  </tr>
</table>
<?php
include("../foot.htm");
?>
