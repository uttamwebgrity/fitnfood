<?php
error_reporting(0);
include_once("includes/configuration.php");

$queryString_array=array();
$query="";

echo '<ul>';

if(isset($_POST['queryString'])) {
	$queryString = mysql_real_escape_string(trim($_POST['queryString']));
	
	$queryString_array=explode(",",$queryString);
	$array_size=sizeof($queryString_array);
	
	if(isset($queryString_array[0]) && trim($queryString_array[0]) != NULL){
		if($array_size > 1){	
			$query .=" suburb_name LIKE '" . trim($queryString_array[0]) . "' ";
		}else {
			$query .=" suburb_name LIKE '" . trim($queryString_array[0]) . "%'";
		}
	}
		
	
	if(isset($queryString_array[1]) && trim($queryString_array[1]) != NULL){
		if($array_size > 2){	
			$query .=" and suburb_state LIKE '" . trim($queryString_array[1]) . "'";
		}else {
			$query .=" and suburb_state LIKE '" . trim($queryString_array[1]) . "%'";
		}
	}
			
	if(isset($queryString_array[2]) && trim($queryString_array[2]) != NULL)
		$query .=" and suburb_postcode  LIKE '" . trim($queryString_array[2]) . "%'";
	
	
	if(strlen($queryString) >0) {		
		
		$sql="SELECT id,suburb_name,suburb_postcode,suburb_state FROM suburb WHERE status=1 and $query";
		$result=mysql_query($sql);
		if((int)mysql_num_rows($result) > 0) {
			while($row=mysql_fetch_object($result)){
				echo '<li style="list-style:none; line-height:20px; padding-left:0px; margin-left:0px;cursor:pointer;" onClick="fill(\''. ucwords(strtolower($row->suburb_name)) .", ". $row->suburb_state .", ". $row->suburb_postcode .'\');genereate_prefilled_value(\''.$row->id.'\');">'. ucwords(strtolower($row->suburb_name)) .", ". $row->suburb_state .", ". $row->suburb_postcode .'</li>';
			}//for	
		}else{
			echo '<li style="list-style:none; line-height:20px; padding-left:0px; margin-left:0px;cursor:pointer;" onClick="fill();genereate_prefilled_value(0);">Sorry, no suburb found!</li>';
			
		}//IF
	}//IF 
}//IF
echo '</ul>';
?>