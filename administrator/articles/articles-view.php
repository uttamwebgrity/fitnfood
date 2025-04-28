<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";


$data=array();
$return_url=$_REQUEST['return_url'];


if(isset($_REQUEST['action']) && $_REQUEST['action']=="VIEW"){
	$sql="select * from articles where id=" . intval($_REQUEST['id']) . " limit 1";	
	$result=$db->fetch_all_array($sql);	
}
?>



<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg">View Article</td>
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
                  <td width="20%" class="body_content-form" valign="top">Article Name:</td>
                  <td width="80%" valign="top"><?php echo $result[0]['article_name'] ?></td>
                </tr>
                <tr>
                  <td class="body_content-form">Article Details:</td>
                  <td><?php echo nl2br($result[0]['article_details']) ?></td>
                </tr>
                 <tr>
                  <td class="body_content-form" valign="top">Date:</td>
                  <td class="body_content-form"  valign="top"><?=date("dS M, Y",strtotime($result[0]['article_date']))?></td>
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
