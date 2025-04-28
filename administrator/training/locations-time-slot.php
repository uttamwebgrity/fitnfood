<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";
$data=array();
$return_url=$_REQUEST['return_url'];


$sql="select location_name from locations where id=" . mysql_real_escape_string(trim($_REQUEST['id'])) . " limit 1";	
$result=$db->fetch_all_array($sql);


?>
 <script>                
                function check_start_time_end_time_valid(sh,sm,eh,em){				
					if(parseInt(sh) > parseInt(eh)){
						return 1;
					}else if(parseInt(eh) == 24 &&  parseInt(em) > 0 ){	
						
						return 2;
					}else if(parseInt(sh) == parseInt(eh) && parseInt(sm) >= parseInt(em)){
						return 3;	
					}		
					return 0;
				}
                
                function add_work_log(){
                	 error=0;
                	
                	 if ($("#trainer_id").val().trim() == 0) {
						document.getElementById("trainer_id").style.border = "1px solid red";
						error++;
					} else {
						document.getElementById("trainer_id").style.border = "1px solid #ccc";
					}	
					
					   
                	 if ($("#which_day").val().trim() == 0) {
						document.getElementById("which_day").style.border = "1px solid red";
						error++;
					} else {
						document.getElementById("which_day").style.border = "1px solid #ccc";
					}	
					
					  
                	 if ($("#start_hour").val().trim() == "") {
						document.getElementById("start_hour").style.border = "1px solid red";
						error++;
					} else {
						document.getElementById("start_hour").style.border = "1px solid #ccc";
					}	
					
					    
                	if ($("#start_min").val().trim() ==  "") {
						document.getElementById("start_min").style.border = "1px solid red";
						error++;
					} else {
						document.getElementById("start_min").style.border = "1px solid #ccc";
					}	
					
					 
                	if ($("#end_hour").val().trim() == "") {
						document.getElementById("end_hour").style.border = "1px solid red";
						error++;
					} else {
						document.getElementById("end_hour").style.border = "1px solid #ccc";
					}	
					
					   
                	if ($("#end_min").val().trim() ==  "") {
						document.getElementById("end_min").style.border = "1px solid red";
						error++;
					} else {
						document.getElementById("end_min").style.border = "1px solid #ccc";
					}		         
                	                	
                	if(error == 0){
                		var return_val=check_start_time_end_time_valid($("#start_hour").val().trim(),$("#start_min").val().trim(),$("#end_hour").val().trim(),$("#end_min").val().trim());
                		
                		if( return_val > 0){
                			error++; 
                			$("#show_start_time_error").show("slow");	
                			if( return_val == 1){
                				$("#show_start_time_error").html("Start hour must be less than end hour");                				
                			}else if( return_val == 2){
                				$("#show_start_time_error").html("If end hour 24, end minute must be zero");        
                			}else{
                				$("#show_start_time_error").html("Start minute must be less than end minute");        
                			}               			
                		}else{
                			$("#show_start_time_error").hide("slow");
                		}
                	}
                	
                	if( error == 0){
                		var location_id=$("#location_id").val().trim();
                		var trainer_id=$("#trainer_id").val().trim();
                		var which_day=$("#which_day").val().trim();	
                		var start_hour=$("#start_hour").val().trim();	
                		var start_min=$("#start_min").val().trim();                		
                		var end_hour=$("#end_hour").val().trim();	
                		var end_min=$("#end_min").val().trim();                		
                		var id= $("#edited_id").val().trim();                		
                		var action=$("#now_action").val().trim();
                			
               			$.post("<?=$general_func->admin_url?>training/add-work-log.php",{id: id,action: action, location_id : location_id, trainer_id : trainer_id, which_day : which_day,start_hour : start_hour,start_min : start_min,end_hour : end_hour,end_min : end_min},
							function (data){
								$("#display_data").html(data);
								$("#start_hour option[value='" + end_hour + "']").attr('selected', 'selected');
								$("#start_min option[value='" + end_min + "']").attr('selected', 'selected');
							});
                	}			
                }
                
              	function delete_me(id,slot){ 
              		
              		var a=confirm("Are you sure you want to delete time slot:  '" + slot +"'?")
   					 if (a){
   					 	var action="delete";
						var location_id=$("#location_id").val().trim();				
						$.post("<?=$general_func->admin_url?>training/add-work-log.php",{action : action, id : id, location_id : location_id}, 
							function (data){							
							$("#display_data").html(data);
						});	   					 	
   					 }
				}
				
				function get_me(id){								
					$.post("<?=$general_func->admin_url?>training/get-me.php",{id : id},
						function (data){
							var data=data.split("~_~");	
															
							$("#edited_id").val(data[0]);			
							$("#trainer_id").val(data[1]);
							$("#which_day").val(data[2]);
							$("#start_hour").val(data[3]);
							$("#start_min").val(data[4]);
							$("#end_hour").val(data[5]);
							$("#end_min").val(data[6]);								
							$("#now_action").val("edit_me");				
					});		
				}	
                </script>     

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg">Training time slots of location '<?=$result[0]['location_name']?>' </td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg">
        <table width="900" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
			            <td colspan="2" height="30"></td>
			          </tr>
          <?php if(isset($_SESSION['msg']) && trim($_SESSION['msg']) != NULL){?>
          <tr>
            <td colspan="2" class="message_error"><?=$_SESSION['msg'];$_SESSION['msg']=""; ?></td>
          </tr>
          <tr>
            <td colspan="2" class="body_content-form" height="30"></td>
          </tr>
          <?php  } ?>
          
          <tr>
            	<td width="20" align="left"  colspan="4" valign="top">&nbsp;</td>
            </tr>
            <tr>
            	<td width="20" align="left" valign="top"></td>
            	<td width="600" align="center"  colspan="3" valign="top" style="padding-top: 10px; float: left;">            		
            		<div id="display_data">
            		<table width="600" align="center"  border="0" cellpadding="6" cellspacing="1" style="padding-top: 5px;">
            			<tr>
            				<td  width="250" class="table_heading">Trainer Name</td>
            				<td  width="100" class="table_heading">Day</td>
            				<td  width="150" class="table_heading" align="center">Time Slot</td>
            				<td  width="100" class="table_heading" align="center">Action</td>             							
            			</tr>
            			<?php
            			$sql_payment = "select s.id as id,CONCAT(fname,' ',lname) as name,start_time,end_time,which_day from location_time_slots s left join trainers t on s.trainer_id=t.id where location_id ='" . mysql_real_escape_string(trim($_REQUEST['id'])) . "' order by name,which_day,start_time ASC ";
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
            					
            					<td class="table_content-blue"  align="center">            						         	
                	<img src="images/edit.png" onclick="get_me('<?php echo $result_payment[$p]['id']; ?>')" style="cursor:pointer; vertical-align: middle;"  title="EDIT" alt="EDIT" />
                	&nbsp;&nbsp;<img src="images/delete.png" title="DELETE" alt="DELETE" onclick="delete_me('<?php echo $result_payment[$p]['id']; ?>','<?=$slot?>')" style="cursor:pointer; vertical-align: middle;" />
                	</td>
            				</tr>								 	
							<?php 
							 }	
						}						
            			?>
            		</table> 
            		</div>           		
            	</td>
            </tr>	
             <tr>
            <td colspan="4" height="30"></td>
          </tr> 
          </table>  
           <table width="900" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td width="20" align="left" valign="top"></td>
            <td width="880" align="left" valign="top"><table width="880" border="0" cellspacing="0" cellpadding="8">                      
                       		
				<form method="post" action="<?=$_SERVER['PHP_SELF']?>"  name="ff"  >
        		<input type="hidden" name="enter" value="yes" />
        		<input type="hidden" name="return_url" value="<?=$return_url?>" />
        		          		
        		<input type="hidden" name="now_action" id="now_action" value="add" />
     			<input type="hidden" name="edited_id" id="edited_id" value="" />
        		
        		      			
        		<input type="hidden" name="location_id" id="location_id" value="<?=mysql_real_escape_string($_REQUEST['id'])?>" />
        		<input type="hidden" name="action" value="<?=mysql_real_escape_string($_REQUEST['action'])?>" />
        		<input type="hidden" name="login_form_id" value="<?=$_SESSION['login_form_id']?>" />
        		<div id="show_start_time_error" class="message_error" style="display: none; float: left; padding-left: 5px; "></div>  		
             	<tr>
                  <td width="60" class="body_content-form" valign="top" align="right">Trainer: <font class="form_required-field"> *</font></td>
                  <td width="200" valign="top"><select name="trainer_id" id="trainer_id" class="inputbox_select" style="width: 185px;">
	            	<option  value="" <?=$trainer_id==""?'selected="selected"':'';?>>Select One</option>
		            <?php  		          
		            $result_suburb=$db->fetch_all_array("CALL GetTrainers()");
					$total_suburb=count($result_suburb);
		            
		            for($s=0; $s < $total_suburb; $s++){ ?>
		            	<option value="<?=$result_suburb[$s]['id']?>" <?=$trainer_id==$result_suburb[$s]['id']?'selected="selected"':'';?>><?=$result_suburb[$s]['trainer_name']?> (<?=$result_suburb[$s]['trainer_type']==1?'Gym':'Personal Trainer';?>)</option>				
		            <?php } ?>
	            	</select></td>
	            	<td width="50" class="body_content-form" valign="top" align="right">Day: <font class="form_required-field"> *</font></td>
                  <td width="100" valign="top"><select name="which_day" id="which_day" class="inputbox_select" style="width: 100px;">
                  		<option value=""> Select One</option>
                  		<?php for($i=1; $i <= 7; $i++){ ?>
                  			<option value="<?=$i?>"><?=$general_func->day_name($i)?></option>
							
                  		<?php } ?>
                  	</select></td>
                  		<td width="100" class="body_content-form" valign="top" align="right">Start Time: <font class="form_required-field"> *</font></td>
                  <td width="150" valign="top"><select name="start_hour" id="start_hour" class="inputbox_select" style="width: 50px;">
                  		<option value="">hh</option>
                  		<?php for($i=0; $i <= 23; $i++){
                  			$display=$i<10?'0'.$i:$i;
							 ?>
                  			<option value="<?=$display?>"><?=$display?></option>
							
                  		<?php } ?>
                  	</select>
                  	<select name="start_min" id="start_min"  class="inputbox_select" style="width: 50px;">
                  		<option value="">mm</option>
                  		<?php for($i=0; $i <= 59; $i++){ 
                  			$display=$i<10?'0'.$i:$i; ?>
                  			<option value="<?=$display?>"><?=$display?></option>
							
                  		<?php } ?>
                  	</select></td>
                  		<td width="80" class="body_content-form" valign="top" align="right">End Time: <font class="form_required-field"> *</font></td>
                  <td width="140" valign="top"><select name="end_hour" id="end_hour"  class="inputbox_select" style="width: 50px;">
                  		<option value="">hh</option>
                  		<?php for($i=0; $i <= 24; $i++){
                  			$display=$i<10?'0'.$i:$i;
							 ?>
                  			<option value="<?=$display?>"><?=$display?></option>
							
                  		<?php } ?>
                  	</select>
                  	<select name="end_min" id="end_min"  class="inputbox_select" style="width: 50px;">
                  		<option value="">mm</option>
                  		<?php for($i=0; $i <= 59; $i++){ 
                  			$display=$i<10?'0'.$i:$i; ?>
                  			<option value="<?=$display?>"><?=$display?></option>
							
                  		<?php } ?>
                  	</select></td>
                </tr> 
                 <tr>
                
                  <td  colspan="8"><table width="261" border="0" align="left" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="41%"><table border="0" align="left" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="submit" type="button" class="submit1" value="Submit" onclick="add_work_log();"  /></td>
                              <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                            </tr>
                          </table></td>
                        <td width="10%">&nbsp;</td>
                        <td width="49%"><!--<table border="0" align="left" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="back" onclick="location.href='<?=$general_func->admin_url?>settings/discounts.php'"  type="button" class="submit1" value="Back" /></td>
                              <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                            </tr>
                          </table>--></td>
                      </tr>
                    </table></td>
                </tr>     		
				</form>	                
              </table>
              </td>
              </tr>
             <tr>
            <td colspan="2" height="30"></td>
          </tr>        
        </table>
     </td>
  </tr>
</table>
<?php
include("../foot.htm");
?>