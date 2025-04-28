<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";




$data=array();
$return_url=$_REQUEST['return_url'];


$small=$path_depth . "trainers_photo/small/";
$original=$path_depth . "trainers_photo/";


if(isset($_REQUEST['action']) && $_REQUEST['action']=="VIEW"){
	$sql="select u.*,suburb_name,suburb_postcode,suburb_state,delivery_day,payment_debit_day,order_cutoff_day,order_cutoff_time  from trainers u ";
	$sql .=" left join suburb s on u.suburb_id=s.id ";
	$sql .=" where u.id=" . (int) $_REQUEST['id'] . " limit 1";	
	$result=$db->fetch_all_array($sql);
	
}
//print_r ($outfitter_videos);

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
          <td align="left" valign="middle" class="body_tab-middilebg">View Trainer</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg">
        <table width="900" border="0" align="left" cellpadding="0" cellspacing="0">
          <tr>
            <td colspan="4" height="30"></td>
          </tr>
          
            
          
          <tr>
            <td width="20" align="left" valign="top"></td>
            <td width="440" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="10">
                
               <tr>
                  <td colspan="2"   class="body_content-form" style=" padding-bottom: 10px;"><strong>General Information</strong></td>
                 
                  </td>
                </tr>
                 <tr>
                  <td width="30%" class="body_content-form" valign="top">Name:</td>
                  <td width="70%" valign="top"><?=$result[0]['fname']." ".$result[0]['lname']?></td>
                </tr>              
                <tr>
                  <td class="body_content-form">Email Address:</td>
                  <td ><?=$result[0]['email_address']?></td>
                </tr>
                 <tr>
                  <td  class="body_content-form" valign="top">Password:</td>
                  <td valign="top" style="line-height: 18px;"><?=$EncDec->decrypt_me($result[0]['password'])?></td>
                </tr>
                 <tr>
                  <td class="body_content-form" valign="top">Mobile No.:</td>
                  <td class="body_content-form"  valign="top"><?=$result[0]['phone']?></td>
                </tr>
                
                
                <tr>
                  <td  class="body_content-form" valign="top">Refered Code:</td>
                  <td valign="top" style="line-height: 18px;"><?=$result[0]['refered_code']?></td>
                </tr>
                 
                <?php if(trim($result[0]['photo']) != NULL){?>
                <tr>
                  <td class="body_content-form" valign="top">Photo:</td>
                  <td  valign="top">
                    <a href="<?=$general_func->site_url.substr($original,6).$result[0]['photo']?>" class="highslide" onclick="return hs.expand(this)"><img src="<?=$general_func->site_url.substr($small,6).$result[0]['photo']?>" border="0" /></a>
                   
                    </td>
                </tr>
                <?php }	?>   
                
              </table></td>
               <td width="440" align="left" valign="top">
               	<table width="100%" border="0" cellspacing="0" cellpadding="10">
                  <tr>
                  <td colspan="2"   class="body_content-form"  style=" padding-bottom: 10px;" ><strong>Address Information </strong></td>
                 
                  </td>
                 <tr>
                  <td width="30%" class="body_content-form" valign="top">Street Address:</td>
                  <td width="70%" valign="top"><?=$result[0]['street_address']?></td>
                </tr>
                <tr>
                  <td class="body_content-form">Suburb:</td>
                  <td><?=ucwords(strtolower($result[0]['suburb_name']))?></td>
                </tr>
                <tr>
                  <td class="body_content-form">Post Code:</td>
                  <td><?=$result[0]['suburb_postcode']?></td>
                </tr>
                <tr>
                  <td class="body_content-form">State:</td>
                  <td><?=$result[0]['suburb_state']?></td>
                </tr>                
              </table>
               </td>
           
          </tr>
          
            <tr>
            <td colspan="4" height="30"></td>
          </tr>
         
           <tr>
            <td colspan="4" height="30" align="center"><table width="879" border="0" align="center" cellpadding="0" cellspacing="0">
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
            <td colspan="4" height="30"></td>
          </tr>
        </table>
     </td>
  </tr>
</table>
<?php
include("../foot.htm");
?>
