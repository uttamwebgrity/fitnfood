<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";


$data=array();
$return_url=$_REQUEST['return_url'];


if(isset($_REQUEST['action']) && $_REQUEST['action']=="VIEW"){
	$sql="select * from testimonials where id=" . intval($_REQUEST['id']) . " limit 1";	
	$result=$db->fetch_all_array($sql);	
}
?>



<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg">View Testimonial</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg">
        <table width="883" border="0" align="left" cellpadding="0" cellspacing="0">
          <tr>
            <td colspan="2" height="30"></td>
          </tr>
          
           
          
          <tr>
            <td width="73" align="left" valign="top"></td>
            <td width="780" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="10">
                
                 <tr>
                  <td width="20%" class="body_content-form" valign="top">Name:</td>
                  <td width="80%" valign="top"><?php echo $result[0]['name'] ?></td>
                </tr>
                <tr>
                  <td class="body_content-form">Details:</td>
                  <td><?php echo nl2br($result[0]['details']) ?></td>
                </tr>
                 <tr>
                  <td class="body_content-form" valign="top">YouTube Video:</td>
                  <td class="body_content-form"  valign="top">
                  	<img width="100" src="http://img.youtube.com/vi/<?=str_replace("http://youtu.be/","", trim($result[0]['embedded_video_link'])) ?>/default.jpg"   /><br/><br/>
                  	
                  	<?php
                  if(trim($result[0]['embedded_video_link']) != NULL){
                  	echo 	"<iframe width=\"475\" height=\"372\" src=\"http://www.youtube.com/embed/" . str_replace("http://youtu.be/","", trim($result[0]['embedded_video_link'])) . "\" frameborder=\"0\" allowfullscreen></iframe>";		
                  } ?></td>
                </tr>
               
              </table></td>
            <td width="8" align="left" valign="top">&nbsp;</td>
          </tr>
         
           <tr>
            <td colspan="3" height="30" align="center"><table width="879" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="31%"></td>
                  <td width="10%">&nbsp;</td>
                  <td width="5%"><table border="0" align="left" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                        <td align="left" valign="middle" class="body_tab-middilebg"><input name="back" onClick="location.href='<?=$return_url?>'"  type="button" class="submit1" value="Back" /></td>
                        <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                      </tr>
                    </table></td>
                  <td width="40%"></td>
                </tr>
              </table></td>
          </tr>
         
          <tr>
            <td colspan="3" height="30"></td>
          </tr>
          
          
                    
                 
              </table></td>
            <td width="8" align="left" valign="top">&nbsp;</td>
          </tr>
          
          
          
         
          
          
         
        </table>
     </td>
  </tr>
</table>
<?php
include("../foot.htm");
?>
