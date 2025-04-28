<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";


if(isset($_POST['enter']) && $_POST['enter']=="update" && trim($_POST['login_form_id'])==$_SESSION['login_form_id']){
	$ids_array=array();	
	$ids=$_POST['ids'];
	$ids_array=explode(",",$ids);
	$ids_total=sizeof($ids_array);	

	for($j=0; $j <$ids_total; $j++ ){		
		if(intval($_POST['delivery_day_'.$ids_array[$j]]) > 0 && intval($_POST['payment_debit_day_'.$ids_array[$j]]) > 0 && intval($_POST['order_cutoff_day_'.$ids_array[$j]]) > 0 && trim($_POST['order_cutoff_time_hour_'.$ids_array[$j]]) != NULL && trim($_POST['order_cutoff_time_minute_'.$ids_array[$j]])  != NULL && trim($_POST['order_cutoff_time_second_'.$ids_array[$j]])  != NULL && $validator->validate_price(trim($_POST['delivery_cost_'.$ids_array[$j]])) && intval($_POST['order_cutoff_day_'.$ids_array[$j]]) < intval($_POST['payment_debit_day_'.$ids_array[$j]]) && intval($_POST['payment_debit_day_'.$ids_array[$j]]) < intval($_POST['delivery_day_'.$ids_array[$j]])){
			$order_cutoff_time=trim($_POST['order_cutoff_time_hour_'.$ids_array[$j]]) . ":" . trim($_POST['order_cutoff_time_minute_'.$ids_array[$j]]) . ":" . trim($_POST['order_cutoff_time_second_'.$ids_array[$j]]);
			$status=isset($_POST['status_'.$ids_array[$j]])?1:0;
			
			$db->query("UPDATE suburb set 
			delivery_day='" . intval($_POST['delivery_day_'.$ids_array[$j]]) . "',
			payment_debit_day='" . intval($_POST['payment_debit_day_'.$ids_array[$j]]) . "',
			order_cutoff_day='" . intval($_POST['order_cutoff_day_'.$ids_array[$j]]) . "',
			order_cutoff_time='" . $order_cutoff_time . "',
			delivery_cost='" . trim($_POST['delivery_cost_'.$ids_array[$j]]) . "',
			status='" . $status . "' 
			where id='" . $ids_array[$j] . "' LIMIT 1; ");
		}
	}	
}	


?>
<script language="JavaScript">
function validate_search(){
	if(!validate_text(document.frmsearch.cd,1,"Enter suburb name")){
		return false
	}
}

function select_or_remove_all(val){	
	if(val == true){
		$('input[id="id_status"]').attr( "checked" ,"checked");
	}else{
		 $('input[id="id_status"]').removeAttr('checked');
	}
}
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg">Delivery Schedule</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
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
              <tr>
                <td align="left" valign="top"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                      <td align="center" valign="top">
                       
                        <table width="523" border="0" align="center" cellpadding="5" cellspacing="0">
                          <form name="frmsearch"  method="get" action="<?=$_SERVER['PHP_SELF']?>" >
                           <input type="hidden" name="enter" value="3" />
                          
                            <tr>
                              <td width="200" align="right" valign="middle" class="content_employee" style="padding-right: 5px;">Suburb Name:</td>
                              <td width="220" align="left" valign="middle"><input type="text" name="cd"  value="<?=$_REQUEST['cd']?>" autocomplete="OFF" size="35" class="inputbox_employee-listing" /></td>
                              <td width="100" align="left" valign="middle">&nbsp;</td>
                            </tr>
                             <tr>
                              <td  align="right" valign="middle" class="content_employee" style="padding-right: 5px;">Postcode starting with:</td>
                              <td align="left" valign="middle"><input type="text" name="postcode"  value="<?=$_REQUEST['postcode']?>" autocomplete="OFF" size="35" class="inputbox_employee-listing" /></td>
                              <td  align="left" valign="middle">
                              <table border="0" align="left" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="button" type="submit" class="submit1" value="Search" /></td>
                              <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                            </tr>
                        </table></td>
                            </tr>
                            
                          </form>
                        </table>
                        <p style="text-align:center;"><font class="text_numbering"><?=$general_func->A_to_Z($_SERVER['PHP_SELF'])?></font></p>
                       </td>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
        </tr>
         <tr>
          <td align="left" valign="middle" height="10"></td>
         </tr> 
            <?php
				//**************************************************************************************//
				$url=$_SERVER['PHP_SELF']."?".(isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:'cc=cc');
				
				
				
				
				$order_by="";
				$display_oder_type="ASC";
		
				if(isset($_REQUEST['display_oder']) && trim($_REQUEST['display_oder']) != NULL){
					if(trim($_REQUEST['display_oder']) == "name"){//***********name
						if(trim($_REQUEST['display_oder_type']) == "ASC"){
							$display_oder_type="DESC";
							$order_by .="suburb_name ASC";
						}else{
							$display_oder_type="ASC";
							$order_by .="suburb_name DESC";
						}
					}else if(trim($_REQUEST['display_oder']) == "suburb_postcode"){//***********name
						if(trim($_REQUEST['display_oder_type']) == "ASC"){
							$display_oder_type="DESC";
							$order_by .="suburb_postcode + 0 ASC";
						}else{
							$display_oder_type="ASC";
							$order_by .="suburb_postcode + 0 DESC";
						}	
					}else if(trim($_REQUEST['display_oder']) == "status"){//***********name
						if(trim($_REQUEST['display_oder_type']) == "ASC"){
							$display_oder_type="DESC";
							$order_by .="status + 0 ASC";
						}else{
							$display_oder_type="ASC";
							$order_by .="status + 0 DESC";
						}	
					}else{
						$order_by .="suburb_name ASC";
					} 					
				}else{
					$order_by .=" suburb_name ASC";
				}
				
				$query="where 1";
				
				
				if(isset($_REQUEST['key']) && trim($_REQUEST['key']) != NULL)
					$query .=" and  suburb_name LIKE '" .trim($_REQUEST['key']). "%'";
				else if(isset($_REQUEST['enter']) && (int)$_REQUEST['enter']==3 && trim($_REQUEST['cd']) != NULL)
					$query .=" and  suburb_name LIKE '" .trim($_REQUEST['cd']). "%'";
				
				
				if(isset($_REQUEST['postcode']) && intval($_REQUEST['postcode']) > 0 ){
					$query .=" and  suburb_postcode LIKE '" .trim($_REQUEST['postcode']). "%'";
					
				}	
				
				$recperpage=50;
				
				$sql="select * from suburb $query order by $order_by";
				
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
          <td align="left" valign="top"><table width="900" align="center" border="0" 
cellpadding="6" cellspacing="1" style="background-color: #e3e1e1;">
              <tr style="background-color: #ffffff;">
                
                <td class="text_numbering" align="right" colspan="8"><?=$total_count?> suburb found.</td>
              </tr>
              <tr>
               
                <td width="17%" align="left" valign="middle" bgcolor="#35619c" class="table_heading">
                <a href="settings/delivery-schedule.php?display_oder=name&display_oder_type=<?=$display_oder_type?>" class="header_link">Suburb</a>
                </td>
                 <td width="5%" align="left" valign="middle" bgcolor="#35619c" class="table_heading">
                <a href="settings/delivery-schedule.php?display_oder=suburb_postcode&display_oder_type=<?=$display_oder_type?>" class="header_link">Postcode</a>
                </td>
                <td width="10%" align="center" valign="middle" bgcolor="#35619c" class="table_heading">Delivery Day</td>
                <td width="10%" align="center" valign="middle" bgcolor="#35619c" class="table_heading">Payment Debit Day</td>
                <td width="36%" align="center" valign="middle" bgcolor="#35619c" class="table_heading">Cutoff Day &amp; Time</td>
                <td width="10%" align="center" valign="middle" bgcolor="#35619c" class="table_heading">Delivery Cost</td>
                <td width="6%" align="center" valign="middle" bgcolor="#35619c" class="table_heading">
                	 <input type="checkbox" name="select_or_remove_all" id="select_or_remove_all" value="1" onclick="select_or_remove_all(this.checked);"  />
                	
                	<a href="settings/delivery-schedule.php?display_oder=status&display_oder_type=<?=$display_oder_type?>" class="header_link">Active?</a></td>
               <td width="6%" align="center" valign="middle" bgcolor="#35619c" class="table_heading">Action</td>
              </tr>
			
			<?php if(count($result) == 0){?>
                	<tr>
                		<td colspan="8" align="center" bgcolor="#f5f7fa" valign="middle" height="50" class="message_error">Sorry, no categories added yet!</td>
              		</tr>
				<?php }else{
					?>
					<form name="frm_delivery_schedule" method="post" action="<?=$_SERVER['PHP_SELF']?>">
					  <input type="hidden" name="enter" value="update" />
					      <input type="hidden" name="login_form_id" value="<?=$_SESSION['login_form_id']?>" />					
					<?php
					$ids="";
					for($j=0;$j<count($result);$j++){
						$ids .= $result[$j]['id'].",";
						?>
                     <tr bgcolor="<?=$j%2==0?$general_func->color2:$general_func->color1;?>">
                        <td align="left" valign="middle" class="table_content-blue"><?=ucwords(strtolower($result[$j]['suburb_name'])).", ".$result[$j]['suburb_state']?></td>
                        <td align="left" valign="middle" class="table_content-blue"><?=$result[$j]['suburb_postcode']?></td>
                       
                       <td  align="center" valign="middle" class="table_content-blue">
                       	<select name="delivery_day_<?=$result[$j]['id']?>" class="inputbox_select" style="width: 90px;">
                  		<option value="" <?=$result[$j]['delivery_day']==""?'selected="selected"':'';?>> Select One</option>
                  		<?php for($i=3; $i <=7; $i++){ ?>
                  			<option value="<?=$i?>" <?=$result[$j]['delivery_day']==$i?'selected="selected"':'';?>><?=$general_func->day_name($i)?></option>
							
                  		<?php } ?>
                  	</select></td>
                        <td  align="center" valign="middle" class="table_content-blue">
                        	<select name="payment_debit_day_<?=$result[$j]['id']?>" class="inputbox_select" style="width: 90px;">
                  		<option value="" <?=$result[$j]['payment_debit_day']==""?'selected="selected"':'';?>> Select One</option>
                  		<?php for($i=1; $i <=2; $i++){ ?>
                  			<option value="<?=$i?>" <?=$result[$j]['payment_debit_day']==$i?'selected="selected"':'';?>><?=$general_func->day_name($i)?></option>
							
                  		<?php } ?>
                  	</select></td>
                         <td  align="center" valign="middle" class="table_content-blue">
                         	
                         	<select name="order_cutoff_day_<?=$result[$j]['id']?>" class="inputbox_select" style="width: 90px;">
                  		<option value="" <?=$result[$j]['order_cutoff_day']==""?'selected="selected"':'';?>> Select One</option>
                  		<?php for($i=1; $i <=2; $i++){ ?>
                  			<option value="<?=$i?>" <?=$result[$j]['order_cutoff_day']==$i?'selected="selected"':'';?>><?=$general_func->day_name($i)?></option>
							
                  		<?php }
						list($order_cutoff_time_hour, $order_cutoff_time_minute, $order_cutoff_time_second)=explode(":",$result[$j]['order_cutoff_time']);
						
						 ?>
                  	</select>
                  	
                  	<select name="order_cutoff_time_hour_<?=$result[$j]['id']?>" class="inputbox_select" style="width: 50px;">
                  		<option value="" <?=$order_cutoff_time_hour==""?'selected="selected"':'';?>>hh</option>
                  		<?php for($i=0; $i <=23; $i++){ 
                  			$disp=$i <10? '0'.$i:$i;
                  			?>
                  			<option value="<?=$i?>" <?=$order_cutoff_time_hour==$i?'selected="selected"':'';?>><?=$disp?></option>
							
                  		<?php } ?>
                  	</select>
                  	&nbsp;&nbsp;
                  	<select name="order_cutoff_time_minute_<?=$result[$j]['id']?>" class="inputbox_select" style="width: 50px;">
                  		<option value="" <?=$order_cutoff_time_minute==""?'selected="selected"':'';?>> mm</option>
                  		<?php for($i=0; $i <=59; $i++){ 
                  			$disp=$i <10? '0'.$i:$i;
                  			?>
                  			<option value="<?=$i?>" <?=$order_cutoff_time_minute==$i?'selected="selected"':'';?>><?=$disp?></option>
							
                  		<?php } ?>
                  	</select>
                  	&nbsp;&nbsp;
                  	<select name="order_cutoff_time_second_<?=$result[$j]['id']?>" class="inputbox_select" style="width: 50px;">
                  		<option value="" <?=$order_cutoff_time_second==""?'selected="selected"':'';?>> ss</option>
                  		<?php for($i=0; $i <=59; $i++){ 
                  			$disp=$i <10? '0'.$i:$i;
                  			?>
                  			<option value="<?=$i?>" <?=$order_cutoff_time_second==$i?'selected="selected"':'';?>><?=$disp?></option>
							
                  		<?php } ?>
                  	</select>
                         	
                         	</td>
                           <td  align="center" valign="middle" class="table_content-blue">
                           	
                           	$<input name="delivery_cost_<?=$result[$j]['id']?>" value="<?=$result[$j]['delivery_cost']?>" type="text" autocomplete="off" class="form_inputbox" size="5" />
                           	</td>
                               <td  align="center" valign="middle" class="table_content-blue">
                               <input type="checkbox" name="status_<?=$result[$j]['id']?>" id="id_status" value="1" <?=$result[$j]['status']==1?'checked="checked"':'';?>  />
                               </td>
                       <td  align="center" valign="middle" class="table_content-blue" >                     
                       <img src="images/edit.png" onclick="location.href='<?=$general_func->admin_url?>settings/delivery-schedule-new.php?id=<?=$result[$j]['id']?>&action=EDIT&return_url=<?=urlencode($url)?>'" style="cursor:pointer;" />
                      </td>
            </tr>
			<?php } 
				$ids=substr($ids,0,-1);
			?>
			<tr>
                <td colspan="8" align="center" valign="middle" height="4" style="background-color: #ffffff; padding: 10px;">
                	<table border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                              <td align="left" valign="middle" class="body_tab-middilebg"><input name="submit" type="submit" class="submit1" value="Update" /></td>
                              <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                            </tr>
                          </table>
                	
                </td>
            </tr>
            <input type="hidden" value="<?=$ids?>" name="ids" /> 
			</form>
			<?php
				}
	  		?>
            
             
          </table></td>
        </tr>
                    <tr>
                <td colspan="8" align="center" valign="middle" height="30" class="table_content-blue">
                  <?php 
		if ($total_count>$recperpage) {
		?>
		<table width="821" height="20" border="0"  cellpadding="0" cellspacing="0">
<tr>
				<td width="300" align="left" valign="bottom" class="htext">
						&nbsp;Jump to page 
				<select name="in_page" style="width:45px;" onChange="javascript:location.href='<?php echo str_replace("&in_page=".$page,"",$url);?>&in_page='+this.value;">
				  <?php for($m=1; $m<=ceil($total_count/$recperpage); $m++) {?>
				  <option value="<?php echo $m;?>" <?php echo $page==$m?'selected':''; ?>><?php echo $m;?></option>
				  <?php }?>
				</select>
				of 
		  <?php echo ceil($total_count/$recperpage); ?>	  </td>
		  <td width="521"  valign="bottom" class="htext"  style="text-align:right;"><?php echo " ".$showing." ".$prev." ".$next." &nbsp;";?></td>
		  </tr>
	  </table>

    <!-- / show category -->
		<?php  }?>                </td>
              </tr>
               <tr>
                <td colspan="8" align="center" valign="middle" height="20">&nbsp;</td>
            </tr> 

      </table>
    </td>
  </tr>
</table>
<?php
include("../foot.htm");
?>