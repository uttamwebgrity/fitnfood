<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";



?>

<style>
ul {
	padding:0px;
	margin: 0px;
}
#response {
	padding:10px;
	background-color:#ffffff;
	border:2px solid #f3f9ff;
	margin-bottom:20px;
}
#list li {
	margin: 0 0 3px;
	padding:8px;
	background-color:#f3f9ff;
	color:#666666;
	font-size: 13px;
	font-weight: bold;
	list-style: none;
}

#response1 {
	padding:10px;
	background-color:#ffffff;
	border:2px solid #f3f9ff;
	margin-bottom:20px;
}
#list1 li {
	margin: 0 0 3px;
	padding:8px;
	background-color:#f3f9ff;
	color:#666666;
	font-size: 13px;
	font-weight: bold;
	list-style: none;
}

</style>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg">Navigation Menus</td>
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
              
             
            </table></td>
        </tr>
        
        <tr>
          <td align="left" valign="top"><table width="550" align="center" border="0" 
cellpadding="5" cellspacing="1">
              
              <tr>
                <td width="550"  align="left" valign="middle" class="table_heading">Navigation Menus [Front end Header]</td>
               
              </tr>
              <tr>
              		<td>
              			<div id="list">
						    <div id="response"> </div>
						    <ul>
						      <?php
						       $sql_header_menu="select id,page_heading from static_pages where parent_id=0 and page_position LIKE '%1%'   order by display_order + 0 ASC";
								$result_header_menu=$db->fetch_all_array($sql_header_menu);
								$total_header_menu=count($result_header_menu);
								for($header=0; $header < $total_header_menu; $header++ ){
								?>
						      <li id="arrayorder_<?php echo $result_header_menu[$header]['id'] ?>"><?php echo  $result_header_menu[$header]['page_heading'];  ?>
						        <div class="clear"></div>
						      </li>
						      <?php } ?>
						    </ul>
						  </div>
              		</td>	
              	
              </tr>
              <tr>
            	<td align="left" valign="middle" class="table_content-blue">Drag and Drop the menu and set the position</td></tr>
             <tr>
			
			<tr>
                <td  height="30px;" ></td>
               
              </tr>
			
			<tr>
                <td width="550"  align="left" valign="middle" class="table_heading">Navigation Menus [Front end Footer]</td>
               
              </tr>
              <tr>
              		<td>
              			<div id="list1">
						    <div id="response1"> </div>
						    <ul>
						      <?php
						       $sql_header_menu="select id,page_heading from static_pages where parent_id=0 and page_position LIKE '%4%'  order by display_order + 0 ASC";
								$result_header_menu=$db->fetch_all_array($sql_header_menu);
								$total_header_menu=count($result_header_menu);
								for($header=0; $header < $total_header_menu; $header++ ){
								?>
						      <li id="arrayorder_<?php echo $result_header_menu[$header]['id'] ?>"><?php echo  $result_header_menu[$header]['page_heading'];  ?>
						        <div class="clear"></div>
						      </li>
						      <?php } ?>
						    </ul>
						  </div>
              		</td>	
              	
              </tr>
              <tr>
            	<td align="left" valign="middle" class="table_content-blue">Drag and Drop the menu and set the position</td></tr>
             <tr>
			
			
            <tr>
                <td colspan="9" align="center" valign="middle" height="4"></td>
            </tr> 
            
              <tr>
                <td colspan="9" align="center" valign="middle" height="30" class="table_content-blue"></td>
              </tr>
          </table></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<?php
include("../foot.htm");
?>