<?php
include_once("includes/header.php");


$url=substr(trim($_SERVER['PHP_SELF']),0,-4);
				
$recperpage=10;				
				
$sql="select * from testimonials  order by date_added DESC";
				
//-	----------------------------------/Pagination------------------------------
if(isset($_GET['in_page'])&& $_GET['in_page']!="")
	$page=$_GET['in_page'];
else
	$page=1;
		
$total_count=$db->num_rows($sql);
$sql=$sql." limit ".(($page-1)*$recperpage).", $recperpage";
			
if($page>1){
	$url_prev=stristr($url,"/page/".$page)==FALSE?$url."/page/".($page-1):str_replace("/page/".$page,"/page/".($page-1),$url);
	$prev="<a href='$url_prev'>&laquo; Prev</a>";
}else
	$prev="&nbsp;Prev";
						
if((($page)*$recperpage)<$total_count){
	$url_next=stristr($url,"/page/".$page)==FALSE?$url."/page/".($page+1):str_replace("/page/".$page,"/page/".($page+1),$url);
	$next="&nbsp;<a href='$url_next'>Next &raquo;</a> ";
}else
	$next="&nbsp;Next";
						
$page_temp=(($page)*$recperpage);
$page_temp=$page_temp<$total_count?$page_temp:$total_count;
$showing=" Showing ".(($page-1)*$recperpage+1)." - ".$page_temp." of ".$total_count." | ";
				 
//-----------------------------------/Pagination------------------------------
//*************************************************************************************************//
$result=$db->fetch_all_array($sql);
//*******************************************************************************************************************//

$total_articles=count($result);

?>
<div class="inrBnr">
	<?php $db_common->static_page_banner($dynamic_content['page_id']);?>									
</div>
<div class="bodyContent">
<div class="mainDiv2">
	<h3>Success Stories from our members</h3>
  	<ul class="tstmnls">
  		<?php for($j=0;$j<$total_articles;$j++){?>
  		<li>
  		<?php if(trim($result[$j]['embedded_video_link']) != NULL){ ?>
    	  			<div class="tstmVdo"><iframe width="475" height="372" src="http://www.youtube.com/embed/<?=str_replace("http://youtu.be/","", trim($result[$j]['embedded_video_link'])) ?>" frameborder="0" allowfullscreen></iframe></div>	
		<?php }	 ?>		
  			
    	  <p><?php echo nl2br($result[$j]['details']) ?> <span>-- <?php echo $result[$j]['name'] ?></span></p>
    	</li>
  		<?php } ?>	
    </ul>
    <?php if ($total_count>$recperpage) { ?>
	    <div class="peginationPnl">
	   	  <div class="jumpToPnl">
	      	<ul>
	            <li>Jump to Page</li>
	            <li><select name="in_page"  onChange="javascript:location.href='<?php echo str_replace("/page/".$page,"",$url);?>/page/'+this.value;">
                    <?php for($m=1; $m<=ceil($total_count/$recperpage); $m++) {?>
                    <option value="<?php echo $m;?>" <?php echo $page==$m?'selected':''; ?>><?php echo $m;?></option>
                    <?php }?>
                  </select></li>
	            <li>of <?php echo ceil($total_count/$recperpage); ?> </li>
	        </ul>
	      </div>
	      <div class="showingPnl">
	      	<ul>
	            <li>            	
	            	<?php echo $showing;?> </li>
	            <li><?php echo $prev." ".$next." &nbsp;";?></li>
	        </ul>
	      </div>
	    </div>
	    <?php } ?>
  </div>
</div>
<?php
include_once("includes/footer.php");
?>