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
		<h3>Choose Your Training Location</h3>


		<div class="choose_location">


			<div class="choose_location_row">
				<div class="location_map"><img src="images/location_map.jpg"></div>
			</div>

			<div class="choose_location_row">
				<div class="location_legend"><div class="location_leg_icon"><img src="images/free_training_icon.png" /></div><div class="location_leg_text">Free training group</div></div>
				<div class="location_legend"><div class="location_leg_icon"><img src="images/prm_training_icon.png" /></div><div class="location_leg_text">Premium training group</div></div>
				<div class="location_legend"><div class="location_leg_icon"><img src="images/gym_training_icon.png" /></div><div class="location_leg_text">Gym</div></div>
			</div>

			<div class="choose_location_row">

				<div class="choose_location_detail">

					<!-- left -->
					<div class="choose_location_left">
						<div class="location_info_row"><span>Training centre type :</span><span>Premium training group</span></div>
						<div class="location_info_row"><span>Centre Name :</span><span>Keep Fit</span></div>
						<div class="location_info_row"><span>Location Name :</span><span>West Pennant Hills</span></div>
						<div class="location_info_row"><span>Phone No. :</span><span>9876543210</span></div>
						<div class="location_info_row"><span>Centre email :</span><span><a href="mailto:info@pennanttraining.com">info@pennanttraining.com</a></span></div>
						<div class="location_info_row"><span>Contact Person :</span><span>Mr. Boby Alson</span></div>
					</div>
					<!-- left -->


					<!-- right -->
					<div class="choose_location_right">

						<div class="join_content_block">
							<h6>Address :</h6>
							<p>123, West Pennant Hils<br />NSW, Australia</p>
						</div>

						<div class="join_content_block normal_select">
							<h6>Choose your time slot</h6>
							<label class="custom-select">
								<select>
									<option>07:00 A.M. -- 11:00 A.M.</option>
								</select>
							</label>
							<input type="submit" value="Join now" class="btn-default" />
						</div>

					</div>
					<!-- right -->




				</div>

			</div>







		</div>


	</div>
</div>
<?php
include_once("includes/footer.php");
?>