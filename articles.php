<?php
include_once("includes/header.php");

//**************************************************************************************//

$url=substr(trim($_SERVER['PHP_SELF']),0,-4);
				
$recperpage=10;				
				
$sql="select * from articles  order by article_date DESC";
				
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
  		<h3>Articles</h3>
  		<?php for($j=0;$j<$total_articles;$j++){?>
  		<div class="aticlPnl">
  	  		<div class="aticlDate">
      			<h6><?=date("d",strtotime($result[$j]['article_date']))?></h6>
        		<span><?=date("F, Y",strtotime($result[$j]['article_date']))?><br></b></span>
      		</div>
      		<div class="aticlCont">
      			<h5><?=$result[$j]['article_name']?></h5>
        		<p><?=nl2br($result[$j]['article_details'])?></p>
      		</div>
  		</div>
  		<?php } 
  		if ($total_count>$recperpage) {  		
  		?>
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