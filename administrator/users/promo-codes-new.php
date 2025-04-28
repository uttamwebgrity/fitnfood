<?php
$path_depth="../../";

include_once("../head.htm");
$link_name = "Welcome";

$data=array();
$return_url=$_REQUEST['return_url'];


if(isset($_REQUEST['action']) && $_REQUEST['action']=="EDIT"){		
	$sql="select * from promo_codes where id=" . intval(mysql_real_escape_string($_REQUEST['id']))  . " limit 1";
	$result=$db->fetch_all_array($sql);			
	$promo_code=$result[0]['promo_code']; 	
	$discount_type=$result[0]['discount_type']; 	
	$discount_amount=$result[0]['discount_amount'];
	$gift_items=$result[0]['gift_items']; 	
	$how_many_week=$result[0]['how_many_week']; 	
	$only_for_any_member=$result[0]['only_for_any_member']; 
	$user_id=$result[0]['user_id']; 	
		
	list($yy,$mm,$dd)=@explode("-",trim($result[0]['start_date']));//yyyy/mm/dd to mm/dd/yyyy		
  	$start_date=$mm."/".$dd."/".$yy;
	
	list($yy,$mm,$dd)=@explode("-",trim($result[0]['end_date']));//yyyy/mm/dd to mm/dd/yyyy			
  	$end_date=$mm."/".$dd."/".$yy;
	
	$button="Update";
}else{		
	$promo_code="";
	$discount_type=""; 	
	$discount_amount="";
	$gift_items="";
	$how_many_week=0;
	$only_for_any_member="";
	$user_id="";
  	$start_date="";
  	$end_date="";
  	
	$button="Add New";
}

if(isset($_POST['enter']) && $_POST['enter']=="yes" && trim($_POST['login_form_id'])==$_SESSION['login_form_id']){
	
	$promo_code=filter_var(trim($_REQUEST['promo_code']), FILTER_SANITIZE_STRING);	 
	$discount_type=intval($_REQUEST['discount_type']);	
	$discount_amount=trim($_REQUEST['discount_amount']);	
	$gift_items=trim($_REQUEST['gift_items']);
	
	$how_many_week=intval($_REQUEST['how_many_week']);	
	$only_for_any_member=intval($_REQUEST['only_for_any_member']);	
	$user_id=intval($_REQUEST['user_id']);
			 
	$start_date_array=explode("/",trim($_POST['start_date']));//mm/dd/yyyy	
	$end_date_array=explode("/",trim($_POST['end_date']));//mm/dd/yyyy	
	
	
	if($_POST['submit']=="Add New"){
		if($db->already_exist_inset("promo_codes","promo_code",$promo_code)){
			$_SESSION['msg']="Sorry, your specified promo code is already taken!";		
		}else{			
			$data['promo_code']=$promo_code;	
			$data['discount_type']=$discount_type;
				
			if($discount_type == 3){
				$data['discount_amount']="";
				$data['gift_items']=$gift_items;			
			}else{
				$data['discount_amount']=$discount_amount;
				$data['gift_items']="";	
			}			
			
			$data['how_many_week']=$how_many_week;			
			$data['only_for_any_member']=$only_for_any_member;
			
			if($only_for_any_member == 2){
				$data['user_id']=$user_id;					
			}else{
				$data['user_id']="";	
			}
			
			$data['start_date']=$start_date_array[2]."-".$start_date_array[0]."-".$start_date_array[1];			
			$data['end_date']=$end_date_array[2]."-".$end_date_array[0]."-".$end_date_array[1];				
			$data['date_added']=$current_date_time;
			
			$db->query_insert("promo_codes",$data);
						
			$_SESSION['msg']="Promo code successfully created!";
			$general_func->header_redirect($_SERVER['PHP_SELF']);
		}	

	}else{
		if($db->already_exist_update("promo_codes","id",$_REQUEST['id'],"promo_code",$promo_code)){
			$_SESSION['msg']="Sorry, your specified promo code is already taken!";		
		}else{
			$data['promo_code']=$promo_code;	
			$data['discount_type']=$discount_type;
				
			if($discount_type == 3){
				$data['discount_amount']="";
				$data['gift_items']=$gift_items;			
			}else{
				$data['discount_amount']=$discount_amount;
				$data['gift_items']="";	
			}			
			
			$data['how_many_week']=$how_many_week;			
			$data['only_for_any_member']=$only_for_any_member;
			
			if($only_for_any_member == 2){
				$data['user_id']=$user_id;					
			}else{
				$data['user_id']="";	
			}
			
			$data['start_date']=$start_date_array[2]."-".$start_date_array[0]."-".$start_date_array[1];			
			$data['end_date']=$end_date_array[2]."-".$end_date_array[0]."-".$end_date_array[1];	
			$data['date_modified']=$current_date_time;			
			
			$db->query_update("promo_codes",$data,"id='".$_REQUEST['id'] ."'");
					
						
			if($db->affected_rows > 0)
				$_SESSION['msg']="Promo code successfully updated!";
			
			$general_func->header_redirect($return_url);
		}
	}
}	

?>      

<script type="text/javascript">

function show_discount_type(val){	
	if(parseInt(val) == 3){	
		$("#discount_type_gift").show("slow");	
		$("#discount_type_amount").hide("slow");	
	}else{
		if(parseInt(val) == 1)
			$("#lbl_per_doll").html("$");
		else
			$("#lbl_per_doll").html("%");
			
		$("#discount_type_gift").hide("slow");	
		$("#discount_type_amount").show("slow");		
	}	
}


function show_only_for_any_member(val){	
	if(parseInt(val) == 2){	
		$("#div_only_for_any_member").show("slow");		
	}else{
		$("#div_only_for_any_member").hide("slow");				
	}	
}


		
function validate(){
			
	 if(!validate_text(document.ff.promo_code,1,"Enter promo code"))
		return false;
		
	if(document.ff.discount_type.selectedIndex == 0){
		alert("Please select discount type");
		document.ff.discount_type.focus();
		return false;
	}	
	
	if(document.ff.discount_type.value == 3){		
		if(!validate_text(document.ff.gift_items,1,"Enter gift items."))
			return false;		
	}else{
		if(!validate_text(document.ff.discount_amount,1,"Enter discount amount."))
			return false;
			
		if(!validate_price(document.ff.discount_amount,1,"Enter a valid discount amount."))
			return false;		
	}
	
	
	if (! $.isNumeric($("#how_many_week").val())  || parseInt($("#how_many_week ").val()) < 0){
		alert("Please enter how many weeks user can use it, either zero or greater than zero.");
		document.ff.how_many_week.focus();	
	}	
		
	
	if(document.ff.only_for_any_member.selectedIndex == 0){
		alert("Please select whether promo code will be applicable to any specific member or not.");
		document.ff.only_for_any_member.focus();
		return false;
	}	
		
	
	if(document.ff.only_for_any_member.value == 2){
		if(document.ff.user_id.selectedIndex == 0){
			alert("Please select a member.");
			document.ff.user_id.focus();
			return false;
		}
	}		
	
	
	if(!validate_text(document.ff.start_date,1,"Enter start date."))
		return false;
	
	if(!validate_text(document.ff.end_date,1,"Enter end date."))
			return false;		
	
	var startDate = new Date($('#datepicker').val());
	var endDate = new Date($('#datepicker1').val()); 
	
	if (endDate < startDate){
		alert("End date should not be less than start date");
		return false;
	}	
	
}	


</script>

 <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
 <script>
$(function() {
	$( "#datepicker" ).datepicker();
	$( "#datepicker1" ).datepicker();
	//$( "#datepicker" ).datepicker( "option", "dateFormat", "dd/mm/yy" );
	//$( "#datepicker1" ).datepicker( "option", "dateFormat",  "dd/mm/yy" );
});
</script>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="6" align="left" valign="top"><img src="images/tab-curve-left.jpg" alt="" width="6" height="29" /></td>
          <td align="left" valign="middle" class="body_tab-middilebg"><?=$button?>
            Promo Code</td>
          <td width="6" align="right" valign="top"><img src="images/tab-curve-right.jpg" alt="" width="6" height="29" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="body_whitebg"> 
    	<form method="post" action="<?=$_SERVER['PHP_SELF']?>"  name="ff" onsubmit="return validate()">
        <input type="hidden" name="enter" value="yes" />
        <input type="hidden" name="id" value="<?=$_REQUEST['id']?>" />
        <input type="hidden" name="return_url" value="<?php echo $_REQUEST['return_url']?>" />
        <input type="hidden" name="photo" value="<?=$photo?>" />
         <input type="hidden" name="login_form_id" value="<?=$_SESSION['login_form_id']?>" />					
				
       

        <table width="883" border="0" align="left" cellpadding="0" cellspacing="0">
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
            <td width="73" align="left" valign="top"></td>
            <td width="780" align="left" valign="top">
            	
            	<table width="100%" border="0" cellspacing="0" cellpadding="10">
                
                 <tr>
                  <td width="20%" class="body_content-form" valign="top">Promo Code:<font class="form_required-field"> *</font></td>
                  <td width="80%" valign="top"><input name="promo_code" value="<?=$promo_code?>" type="text" autocomplete="off" class="form_inputbox" size="55" /></td>
                </tr>
               
                <tr>
                  <td  class="body_content-form">Discount Type:<font class="form_required-field"> *</font></td>
                  <td >
                  	<select name="discount_type" class="inputbox_select" style="width: 300px;" onchange="show_discount_type(this.value);">
	            		<option  value="" <?=$discount_type==""?'selected="selected"':'';?>>Select One</option>
		            	<option value="1" <?=$discount_type==1?'selected="selected"':'';?>>$ Amount</option>				
		             	<option value="2" <?=$discount_type==2?'selected="selected"':'';?>>% Amount</option>	
		              	<option value="3" <?=$discount_type==3?'selected="selected"':'';?>>Gift Items</option>	
	         		</select>
                  </td>
                </tr>
               </table>
               <div id="discount_type_amount" style="display: <?=($discount_type==1 || $discount_type==2)?'block;':'none;';?>">
               <table width="100%" border="0" cellspacing="0" cellpadding="10"> 
                 <tr>
                  <td   width="20%" class="body_content-form">Discount Amount:<font class="form_required-field"> *</font></td>
                  <td  width="80%"><input name="discount_amount" value="<?=$discount_amount?>" type="text" autocomplete="off" class="form_inputbox" size="10" /><label style="font-size: 13px;" id="lbl_per_doll"></label>
                  </td>
                </tr>
                </table>
               </div> 
               <div id="discount_type_gift" style="display: <?=($discount_type==3)?'block;':'none;';?>">
               <table width="100%" border="0" cellspacing="0" cellpadding="10">
                 <tr>
                  <td width="20%" class="body_content-form">Gift Items:<font class="form_required-field"> *</font></td>
                  <td width="80%"><textarea name="gift_items" class="form_textarea" cols="70" rows="6"><?=$gift_items?></textarea></td>
                </tr>
                <tr>
               </table>
                </div> 
               <table width="100%" border="0" cellspacing="0" cellpadding="10"> 	
                <tr>
                  <td width="20%" class="body_content-form">How many weeks user can use it :<font class="form_required-field"> *</font></td>
                  <td width="80%"><input id="how_many_week" name="how_many_week" value="<?=$how_many_week?>" type="text" autocomplete="off" class="form_inputbox" size="10" /> 0 for life time use.
                  	
                  </td>
                </tr>
               <tr>
                  <td  class="body_content-form">Only for any specific Member:<font class="form_required-field"> *</font></td>
                  <td >
                  	<select name="only_for_any_member" class="inputbox_select" style="width: 300px;" onchange="show_only_for_any_member(this.value);">
	            		<option  value="" <?=$only_for_any_member==""?'selected="selected"':'';?>>Select One</option>
		            	<option value="1" <?=$only_for_any_member==1?'selected="selected"':'';?>>No</option>				
		             	<option value="2" <?=$only_for_any_member==2?'selected="selected"':'';?>>Yes</option>		              	
	         		</select>
                  </td>
                </tr>
                </table>
                <div id="div_only_for_any_member" style="display: <?=($only_for_any_member==2)?'block;':'none;';?>">
               <table width="100%" border="0" cellspacing="0" cellpadding="10"> 	
                <tr>
                  <td width="20%" class="body_content-form">Member Name:<font class="form_required-field"> *</font></td>
                  <td width="80%">
                  	<select name="user_id" class="inputbox_select" style="width: 300px;">
	            	<option  value="" <?=$user_id==""?'selected="selected"':'';?>>Select One</option>
		            <?php 
		            $sql_suburb="select id,CONCAT(fname,' ',lname) as name from users where status=1 order by fname ASC";
		            $result_suburb=$db->fetch_all_array($sql_suburb);
					$total_suburb=count($result_suburb);
		            
		            for($s=0; $s < $total_suburb; $s++){ ?>
		            	<option value="<?=$result_suburb[$s]['id']?>" <?=$user_id==$result_suburb[$s]['id']?'selected="selected"':'';?>><?=$result_suburb[$s]['name']?></option>				
		            <?php } ?>
	            	</select>
                  </td>
                </tr>
                </table>
               </div>
               <table width="100%" border="0" cellspacing="0" cellpadding="10"> 	
                 <tr>
                  <td width="20%" class="body_content-form">Start Date:<font class="form_required-field"> *</font></td>
                  <td width="80%"><input type="text" id="datepicker" name="start_date" value="<?=$start_date?>" class="inputbox_employee-listing"></td>
                </tr>    
                 
                  <tr>
                  <td  class="body_content-form">End Date:<font class="form_required-field"> *</font></td>
                  <td ><input type="text" id="datepicker1" name="end_date" value="<?=$end_date?>" class="inputbox_employee-listing">
                  </td>
                </tr>
                </tr>             
                  
              </table></td>
            <td width="8" align="left" valign="top">&nbsp;</td>
          </tr>
          
          
            <tr>
            <td colspan="4" height="30" align="center"><table width="879" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="36%"><table border="0" align="right" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                        <td align="left" valign="middle" class="body_tab-middilebg"><input name="submit" type="submit" class="submit1" value="<?=$button?>" /></td>
                        <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                      </tr>
                    </table></td>
                  <td width="3%"></td>
                  <td width="61%"><?php if($button !="Add New"){?>
                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="5" align="left" valign="top"><img src="images/button-curve-left.png" alt="" width="5" height="22" /></td>
                        <td align="left" valign="middle" class="body_tab-middilebg">
                        	<input type="button" class="submit1"  name="back" value="Back"  onclick="history.back();" />
                        	
                        	<!--<input name="back" onclick="location.href='<?=$return_url?>'"  type="button" class="submit1" value="Back" />--></td>
                        <td width="5" align="right" valign="top"><img src="images/button-curve-right.png" alt="" width="5" height="22" /></td>
                      </tr>
                    </table>
                    <?php  }else 
							echo "&nbsp;";
						 ?></td>
                </tr>
              </table></td>
          </tr>
          
          <tr>
            <td colspan="4" height="30"></td>
          </tr>
                
              </table></td>
            <td width="8" align="left" valign="top">&nbsp;</td>
          </tr>
          
          
          
          
        </table>
      </form></td>
  </tr>
</table>
<?php
include("../foot.htm");
?>
