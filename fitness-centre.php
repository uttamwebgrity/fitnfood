<?php
include_once("includes/header.php");


if (!isset($_SESSION['user_login_type']) || trim($_SESSION['user_login_type']) != "users" || !isset($_SESSION['user_login_type'])) {
	$_SESSION['user_message'] = "Please login to access our training video page!";
	$_SESSION['return_to_front_end']= $general_func -> site_url. "fitness-centre/";
	$general_func -> header_redirect($general_func -> site_url);
}
 

$sql_pointers="select l.id as location_id,t.id as type_id,location_name,street_address,location_latitude,location_longitude,name,marker_name,only_for_platinum_members from locations l";
$sql_pointers .=" left join location_types t on l.location_type_id=t.id  where status=1";
$result_pointers = $db -> fetch_all_array($sql_pointers);
$total_pointers = count($result_pointers);

$all_lat_lang="";
$all_icons="";


$legends=array();
$legend_index=0;

for($marker=0; $marker < $total_pointers; $marker++){
	
	
	$legends[$result_pointers[$marker]['type_id']]['name']=trim($result_pointers[$marker]['name']);
	$legends[$result_pointers[$marker]['type_id']]['marker']=trim($result_pointers[$marker]['marker_name']);
		
	$address='<div class=info ><h4>' . trim($result_pointers[$marker]['location_name']) . '</h4><p>' . trim($result_pointers[$marker]['name']) . '<br/>' . trim($result_pointers[$marker]['street_address']) . '</p><a class="mapReg" href=' .$general_func->site_url .'fitness-center-register/' . trim($result_pointers[$marker]['location_id']) . '>Register Now</a></div>';
	$all_lat_lang .= '[\'' . $address . '\', ' .$result_pointers[$marker]['location_latitude']. ', ' .$result_pointers[$marker]['location_longitude']. ',' . ($marker+1) . ' ],';	
	$all_icons .= "'".$general_func->site_url."markers/" . trim($result_pointers[$marker]['marker_name']) . ".png',";
}

$all_lat_lang=substr($all_lat_lang,0,-1); 
$all_icons=substr($all_icons,0,-1);  


?>

<style>
.info{
	z-index: 5000;
}
.info p{
	color: #373636;
	font-size:16px;
}
.info h4{
	color#373636;
	font-size:20px;
	font-family: Arial, Helvetica, sans-serif;
}	
.info a.mapReg{ float:left; display:block; padding:5px 10px; background:#047ad6; color:#fff; border-radius:3px; }				
</style>
<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
<div class="inrBnr"><?php $db_common->static_page_banner($dynamic_content['page_id']);?></div>
<div class="bodyContent">
	<div class="mainDiv2">
		<h1><?php echo trim($dynamic_content['title']); ?></h1>
		<p><?php echo trim($dynamic_content['file_data']); ?></p>
		<div class="choose_location">
			<div class="choose_location_row">
				<div id="map" style=""></div>
			  	<script type="text/javascript">
				    var locations = [
				      <?=$all_lat_lang?>
				    ];
				
					 var icons = [<?=$all_icons?> ]
					 
					 
					 
				    var map = new google.maps.Map(document.getElementById('map'), {
				      zoom: 12,
				      center: new google.maps.LatLng(-33.865143, 151.209900),
				      mapTypeId: google.maps.MapTypeId.ROADMAP
				    });
				
				    var infowindow = new google.maps.InfoWindow();
				
				    var marker, i;
				
				    for (i = 0; i < locations.length; i++) {  
				      marker = new google.maps.Marker({
				        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
				        map: map,
				        icon : icons[i],
				        
				        
				      });
				
				      google.maps.event.addListener(marker, 'mouseover', (function(marker, i) {
				        return function() {
				          infowindow.setContent(locations[i][0]);
				          infowindow.open(map, marker);
				        }
				      })(marker, i));
				    }
			  	</script>
			</div>
			<div class="choose_location_row">
				<?php
				if(count($legends) > 0){
					foreach ($legends as $key => $value) {?>
					<div class="location_legend">
						<div class="location_leg_icon"><img src="<?php echo $general_func->site_url; ?>markers/<?php echo trim($value['marker']); ?>.png" /></div>
						<div class="location_leg_text"><?php echo $value['name']?></div>
					</div>	
					<?php }	
				}
				?>
			</div>
			<!--<div class="choose_location_row">
				<div class="choose_location_detail">			
					<div class="choose_location_left">
						<div class="location_info_row">
							<span>Training centre type :</span><span>Premium training group</span>
						</div>
						<div class="location_info_row">
							<span>Centre Name :</span><span>Keep Fit</span>
						</div>
						<div class="location_info_row">
							<span>Location Name :</span><span>West Pennant Hills</span>
						</div>
						<div class="location_info_row">
							<span>Phone No. :</span><span>9876543210</span>
						</div>
						<div class="location_info_row">
							<span>Centre email :</span><span><a href="mailto:info@pennanttraining.com">info@pennanttraining.com</a></span>
						</div>
						<div class="location_info_row">
							<span>Contact Person :</span><span>Mr. Boby Alson</span>
						</div>
					</div>					
					<div class="choose_location_right">
						<div class="join_content_block">
							<h6>Address :</h6>
							<p>
								123, West Pennant Hils
								<br />
								NSW, Australia
							</p>
						</div>
						<div class="join_content_block normal_select">
							<h6>Choose your time slot</h6>
							<label class="custom-select">
								<select>
									<option>07:00 A.M. -- 11:00 A.M.</option>
								</select> </label>
							<input type="submit" value="Join now" class="btn-default" />
						</div>
					</div>	
				</div>
			</div>-->
		</div>
	</div>
</div>
<?php
include_once("includes/footer.php");
?>