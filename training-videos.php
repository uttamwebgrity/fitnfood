<?php
include_once("includes/header.php");

if (!isset($_SESSION['user_login_type']) || trim($_SESSION['user_login_type']) != "users" || !isset($_SESSION['user_login_type'])) {
	$_SESSION['user_message'] = "Please login to access our training video page!";
	$_SESSION['return_to_front_end']= $general_func -> site_url. "training-videos/";
	$general_func -> header_redirect($general_func -> site_url);
}
 

$url=substr(trim($_SERVER['PHP_SELF']),0,-4);				
$recperpage=10;			
$access_to="1,";

if($db_common->user_has_a_paid_week(intval($_SESSION['user_id']),1) > 0){
	$access_to .="2,";        
}else {
	$access_to .="3,";
} 	
	
$access_to=substr($access_to,0,-1);				
			
$sql="select video_name,video_details,video_code from videos where video_type_id IN(select  id from video_types where access_to IN($access_to) UNION select video_type_id from video_types_access_permission where user_id='" . intval($_SESSION['user_id']) . "') order by date_added DESC";
				
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
	<h3>Our Training Videos</h3>
  	<ul class="tstmnls">
  		<?php for($j=0;$j<$total_articles;$j++){?>
  		<li>  		
    		<div class="tstmVdo">
    	  		<div id="mediaplayer<?=$j?>">Loading the player ...</div>
              	<script type="text/javascript" src="jwplayer.js"></script>
                <script type="text/javascript">
             		jwplayer("mediaplayer<?=$j?>").setup({
                    	flashplayer: "player.swf",
                        file: "http://fitnfood.com.au/video_files/<?=trim($result[$j]['video_code'])?>",
                        image: "video_js/myPoster.png"
                    });
                </script>
		 </div>  			
    	  <p><?php echo nl2br($result[$j]['video_details']) ?> <span>-- <?php echo $result[$j]['video_name'] ?></span></p>
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