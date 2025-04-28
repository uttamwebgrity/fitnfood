<?php
include_once("../../includes/configuration.php");

$location_id=intval(trim($_REQUEST['location_id']));
$trainer_id=intval(trim($_REQUEST['trainer_id']));
$which_day=intval(trim($_REQUEST['which_day']));
$start_hour=trim($_REQUEST['start_hour']);
$start_min=trim($_REQUEST['start_min']);
$end_hour=trim($_REQUEST['end_hour']);
$end_min=trim($_REQUEST['end_min']);
$id=intval($_REQUEST['id']);
$action=trim($_REQUEST['action']);

$show="";
$data=array();

if(isset($action) && trim($action) == "delete"){//*************  delete log
	$db->query("delete from location_time_slots where id='" . $id . "'");	
}else if($action == "add" && isset($_SESSION['login_form_id'])){
	$start_time=$start_hour . ":" . $start_min . ":00"; 
	$end_time=$end_hour . ":" . $end_min . ":00"; 
	
	if(strtotime($end_time) > strtotime ($start_time)){
		if($db_common->trainer_slot_exists($trainer_id,$which_day,$start_time,$end_time)){
			$show="Sorry, your specified time slot is already taken!";
		}else{
			$data['location_id']=$location_id;	
			$data['trainer_id']=$trainer_id;			
			$data['which_day']=$which_day;	
			$data['start_time']=$start_time;
			$data['end_time']=$end_time;							
			$db->query_insert("location_time_slots",$data);
		}
	}
}else if($action == "edit_me" && isset($_SESSION['login_form_id'])){
	
	$start_time=$start_hour . ":" . $start_min . ":00"; 
	$end_time=$end_hour . ":" . $end_min . ":00"; 
	
	if(strtotime($end_time) > strtotime ($start_time)){		
		if($db_common->trainer_slot_exists_update($trainer_id,$which_day,$start_time,$end_time,$id)){
			$show="Sorry, your specified time slot is already taken!";
		}else{						
			$data['location_id']=$location_id;	
			$data['trainer_id']=$trainer_id;			
			$data['which_day']=$which_day;	
			$data['start_time']=$start_time;
			$data['end_time']=$end_time;			
			$db->query_update("location_time_slots",$data,"id='". mysql_real_escape_string($id) ."'");
		}
	}	
}
?>
<table width="600" align="center"  border="0" cellpadding="6" cellspacing="1" style="padding-top: 5px;">
	<tr>
   		<td  width="250" class="table_heading">Trainer Name</td>
        <td  width="100" class="table_heading">Day</td>
        <td  width="150" class="table_heading" align="center">Time Slot</td>
        <td  width="100" class="table_heading" align="center">Action</td>             							
    </tr>
    <?php
  	$sql_payment = "select s.id as id,CONCAT(fname,' ',lname) as name,start_time,end_time,which_day from location_time_slots s left join trainers t on s.trainer_id=t.id where location_id ='" . $location_id . "' order by name,which_day,start_time ASC ";
	$result_payment = $db -> fetch_all_array($sql_payment);
	$total_payment = count($result_payment);
						
	if( $total_payment > 0){
		for($p=0; $p <$total_payment; $p++){							 	
			$slot=date("h:i A",strtotime($result_payment[$p]['start_time'])) ." - " . date("h:i A",strtotime($result_payment[$p]['end_time']));
			?>
			<tr bgcolor="<?=$p%2==0?$general_func->color2:$general_func->color1;?>">
            	<td class="table_content-blue"><?=$result_payment[$p]['name'] ?> </td>
            	<td class="table_content-blue"><?=$general_func->day_name($result_payment[$p]['which_day'])?> </td>
            	<td class="table_content-blue" align="center"> <?=$slot?></td>
            	<td class="table_content-blue"  align="center"><img src="images/edit.png" onclick="get_me('<?php echo $result_payment[$p]['id']; ?>')" style="cursor:pointer; vertical-align: middle;"  title="EDIT" alt="EDIT" />
                	&nbsp;&nbsp;<img src="images/delete.png" title="DELETE" alt="DELETE" onclick="delete_me('<?php echo $result_payment[$p]['id']; ?>','<?=$slot?>')" style="cursor:pointer; vertical-align: middle;" />
                </td>
            </tr>								 	
			<?php 
		}	
	}						
?>
</table> 
<p style="text-align: center; color: #FF0000;"><?php echo $show; ?></p>