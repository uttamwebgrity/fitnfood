<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";




$data=array();
$return_url=$_REQUEST['return_url'];


if(isset($_REQUEST['action']) && $_REQUEST['action']=="VIEW"){
	
	$_SESSION['updated_user_id']=intval($_REQUEST['id']);
	
	
	$sql = "select * from users where id=" . intval($_REQUEST['id']) . " limit 1";
	$result = $db -> fetch_all_array($sql);

	$returnURL=$general_func->admin_url."users/cc-dd-updation-success.php";
	$update_URL="https://www.edebit.com.au/IS/". trim($result[0]['cc_or_dd']) ."Info.aspx?cd_crn=" . $edNo . "-" . (intval($_REQUEST['id']) + 3000)."&returnURL=".$returnURL;

}


?>
<script type="text/javascript" src="<?=$general_func->site_url?>highslide/highslide.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$general_func->site_url?>highslide/highslide.css" />
<script type="text/javascript">
	hs.graphicsDir = '<?=$general_func->site_url?>highslide/graphics/';
	hs.wrapperClassName = 'wide-border';
</script>


<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg"><?=$result[0]['fname']." ".$result[0]['lname']?> - <?=trim($result[0]['cc_or_dd'])=="DD"?'Bank Info.':'Credit Card Info.'; ?> Update</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table>
 
      </td>
      
  </tr>
  <tr>
    <td align="center" valign="top" class="body_whitebg">
    	<br/><br/><br/>
        <table width="900" border="0" align="left" cellpadding="0" cellspacing="0"  >
        	<tr>
        		<td width="300px;"></td>
        		<td  width="600px;" style="height: 600px;"> <iframe width="100%" height="100%" frameborder="0"   src="<?=$update_URL?>" ></iframe> </td>
        		
        	</tr>
        </table>	
         
     </td>
  </tr>
</table>
<?php
include("../foot.htm");
?>
