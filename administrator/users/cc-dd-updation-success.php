<?php
$path_depth = "../../";

include_once ($path_depth . "includes/configuration.php");

if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] != "yes") {
	$_SESSION['redirect_to'] = substr($_SERVER['PHP_SELF'], strpos($_SERVER['PHP_SELF'], "administrator/") + 14);
	$_SESSION['redirect_to_query_string'] = $_SERVER['QUERY_STRING'];

	$_SESSION['message'] = "Please login to view this page!";
	$general_func -> header_redirect($general_func -> admin_url . "index.php");
}

$sql_user = "select * from users where id=" . intval($_SESSION['updated_user_id']) . " limit 1";
$result_user = $db -> fetch_all_array($sql_user);

$clNo = 3000 + $_SESSION['updated_user_id'];

$payment_status = 0;

if (strtolower(trim($_REQUEST['Result'])) == "s" && intval($result_user[0]['cc_or_dd_created']) == 1) {
	//**************************  collect cc_or_dd token *************************//
	$edTKI_url = "https://www.edebit.com.au/IS/edTKI.ashx?cd_crn=" . $edNo . "-" . $clNo;
	$ch = curl_init($edTKI_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$edTKI_data = curl_exec($ch);
	curl_close($ch);

	$return_data = array();
	$return_data = @explode("&", $edTKI_data);

	$token_data = array();
	$token_data = @explode("=", $return_data[0]);

	if ($token_data[1] != NULL) {
		$update_query .= "debit_token='" . trim($token_data[1]) . "' ";
		$db -> query("update users set $update_query where id='" . intval($_SESSION['updated_user_id']) . "'");
	}
}
?>
<link href="<?=$general_func -> admin_url?>css/style.css" rel="stylesheet" type="text/css" />
<table width="100%" border="0" cellspacing="0" cellpadding="0">

	<tr>
		<td align="center" valign="top" class="body_whitebg" style="border: none;">
		<br/>
		<br/>
		<br/>
		<table width="900" border="0" align="left" cellpadding="0" cellspacing="0"  >
			<tr>
				<td width="50px;"></td>
				<td  width="850px;" <?php if(strtolower(trim($_REQUEST['Result']))=="s"){
				?>
				<p class="htext"><?=$result_user[0]['fname'] . " " . $result_user[0]['lname'] ?> <?=trim($result_user[0]['cc_or_dd'])=="DD"?'bank info.':'credit card info.'; ?> has been updated!</p>
		
		
			<input  type="button" class="submit_button"  value="Users" name="myaccount" onclick="window.open('<?=$general_func -> admin_url?>users/users.php', '_top');" > 
		
				<?php
				}else{
				?>

				<p class="htext">
					Sorry, <?=$result[0]['fname'] . " " . $result[0]['lname'] ?>
					<?=trim($result_user[0]['cc_or_dd']) == "DD" ? 'bank info.' : 'credit card info.'; ?>
					has not been updated. please try again.
				</p>
				<input class="submit_button"  type="button" value="Try Again!" name="continue" onclick="window.open('<?=$general_func -> admin_url ?>users/update-cc-dd-info.php?id=<?=$_SESSION['updated_user_id']?>&action=VIEW', '_top');" >
				<?php } ?></td>

			</tr>
		</table></td>
	</tr>
</table>

