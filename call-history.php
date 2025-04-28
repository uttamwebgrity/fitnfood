<?php
include_once ("includes/header.php");
if (!isset($_SESSION['user_login_type']) || trim($_SESSION['user_login_type']) != "users" || !isset($_SESSION['user_login_type'])) {
	$_SESSION['user_message'] = "Sorry, you have no permission to access this page!";
	$general_func -> header_redirect($general_func -> site_url);
}


$sql_call = "select call_details,date_added from  call_history where user_id ='" . intval($_SESSION['user_id']) . "' order by date_added DESC";
$result_call = $db -> fetch_all_array($sql_call);
$total_call = count($result_call);

?>

<script type="text/javascript">
	$(document).ready(function() {

		$(".dayPnl1_new1 li").mouseenter(function() {
			$(this).find(".tip_box1").show();
		});

		$(".dayPnl1_new1 li").mouseleave(function() {
			$(this).find(".tip_box1").hide();
		});

		$(".close_pop").click(function() {
			$(this).parent().parent().find(".tip_box1").hide();
		});
	})
</script>

<link href="css/fonts.css" rel="stylesheet" type="text/css" />
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link href="css/responsive.css" rel="stylesheet" type="text/css" />
<div class="inrBnr">
	<?php $db_common -> static_page_banner($dynamic_content['page_id']); ?>
</div>
<div class="bodyContent">
  <div class="mainDiv2">
  <div class="order_listingBcmb">
    	<ul>
        	<li><a href="my-account/">My Account &raquo;</a></li>
            <li>Call History</li>
        </ul>
  </div>
  <br class="clear" />
  	<h5 class="orderDtlsHd" style="margin-top:0">Date Called <b>&amp;</b> <span>Call Details</span></h5>
  	<?php 	if( $total_call > 0){
			  for($j=0; $j <$total_call; $j++){ ?>
			  <div class="aticlPnl" style="background:#fff; padding:30px 2.5% 20px; width:95%">
		      <div class="aticlDate">
		        <h6><?=date("d",strtotime($result_call[$j]['date_added']))?></h6>
		        <span><?=date("F, Y",strtotime($result_call[$j]['date_added']))?><br><b>At: <?=date("h:i A",strtotime($result_call[$j]['date_added']))?></b></span>
		      </div>
		      <div class="aticlCont">
		        <p><?=nl2br($result_call[$j]['call_details'])?></p>
		      </div>
		      </div>		  	
			  	
			 <?php  }
	}	 ?>
  	
  	
    
    
    
    
  </div>
</div>
<?php
include_once ("includes/footer.php");
?>