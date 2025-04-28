<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg"> Eating Sehedule PDFs</td>
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
              
            </table></td>
        </tr>
         <tr>
          <td align="left" valign="middle" height="10"></td>
         </tr> 
            <?php
				//**************************************************************************************//
				$url=$_SERVER['PHP_SELF']."?".(isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:'cc=cc');				
				$recperpage=$general_func->admin_recoed_per_page;
				
				$sql="select * from meal_schedule_pdf order by eating_schedule ASC";
				$result=$db->fetch_all_array($sql);
			//*******************************************************************************************************************//
        ?> 
        <tr>
          <td align="left" valign="top"><table width="621" align="center" border="0" 
cellpadding="5" cellspacing="1">
             
              <tr>
               
                <td width="40%" align="left" valign="middle" bgcolor="#35619c" class="table_heading">Timing of Training</td>
                 <td width="30%" align="center" valign="middle" bgcolor="#35619c" class="table_heading">PDF File</td>                
                <td width="30%" align="center" valign="middle" bgcolor="#35619c" class="table_heading">Action</td>
              </tr>
			
			<?php if(count($result) == 0){?>
                	<tr>
                		<td colspan="3" align="center" bgcolor="#f5f7fa" valign="middle" height="50" class="message_error">Sorry, no PDF added yet!</td>
              		</tr>
				<?php }else{
					for($j=0;$j<count($result);$j++){?>
                     <tr bgcolor="<?=$j%2==0?$general_func->color2:$general_func->color1;?>">
                        <td  align="left" valign="middle" class="table_content-blue"><?php                        
                        if($result[$j]['eating_schedule'] == 1){
                        	echo "Morning";                        	
                        }else if($result[$j]['eating_schedule'] == 2){
                        	echo "Lunch Time";      
                        }else if($result[$j]['eating_schedule'] == 3){
                        	echo "After Work";      
                        }else if($result[$j]['eating_schedule'] == 4){
                        	echo "Evening";      
                        }else {
							echo "No Training";      
						}                       
                        
                        ?></td>
                        <td align="center" valign="middle" class="table_content-blue">
                        	<?php if(trim($result[$j]['pdf_file_name']) != NULL){ ?>
                        		<a target="_blank" href="<?=$general_func->site_url?>eating_schedule/<?=trim($result[$j]['pdf_file_name'])?>"><img src="images/pdf.png" alt="" /></a>
							<?php } ?>                        	
                        </td>                        
                       <td  align="center" valign="middle" class="table_content-blue" style="padding-left: 15px;">                     
                       <img src="images/edit.png" onclick="location.href='<?=$general_func->admin_url?>settings/eating-sehedule-file-new.php?id=<?=$result[$j]['id']?>&action=EDIT&return_url=<?=urlencode($url)?>'" style="cursor:pointer;" />
                      </td>
            </tr>
			<?php }
				}
	  		?>
            <tr>
                <td colspan="4" align="center" valign="middle" height="4"></td>
            </tr> 
              <tr>
                <td colspan="4" align="center" valign="middle" height="30" class="table_content-blue"></td>
              </tr>
          </table></td>
        </tr>
                    <tr>
                <td colspan="4" align="center" valign="middle" height="30" class="table_content-blue">
                  <?php 
		if ($total_count>$recperpage) {
		?>
		<table width="715" height="20" border="0"  cellpadding="0" cellspacing="0">
<tr>
				<td width="295" align="left" valign="bottom" class="htext">
						&nbsp;Jump to page 
				<select name="in_page" style="width:45px;" onChange="javascript:location.href='<?php echo str_replace("&in_page=".$page,"",$url);?>&in_page='+this.value;">
				  <?php for($m=1; $m<=ceil($total_count/$recperpage); $m++) {?>
				  <option value="<?php echo $m;?>" <?php echo $page==$m?'selected':''; ?>><?php echo $m;?></option>
				  <?php }?>
				</select>
				of 
		  <?php echo ceil($total_count/$recperpage); ?>	  </td>
		  <td width="420" align="right" valign="bottom" class="htext"><?php echo " ".$showing." ".$prev." ".$next." &nbsp;";?></td>
		  </tr>
	  </table>

    <!-- / show category -->
		<?php  }?>                </td>
              </tr>

      </table>
    </td>
  </tr>
</table>
<?php
include("../foot.htm");
?>