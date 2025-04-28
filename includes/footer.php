<?php

//********************  unique alphanumeric *******************//
$nchar = 5;							// number of characters in image
for($i=1;$i<=$nchar;$i++){
	$charOnumber = rand(1,2);
	if($charOnumber == 1){
		$chars = 'ABEFHKMNRVWX';	// custom used characters
		$n = strlen($chars)-1;
		$x = rand(1,$n);
		$char = substr($chars,$x,1);
		$pass .= $char;
	} else {
		//$number = rand(3,7);
		$numbers = array(1,2,3,4,7);	// custom used numbers
		$n = count($numbers)-1;
		$number = $numbers[rand(1,$n)];
		$pass .= $number;
	}
}
//************************************************************//



 if(isset($_SESSION['user_message']) && trim($_SESSION['user_message']) != NULL){ ?>
<div class="msgShow">
	<div class="msgShowInr">
		<div class="lbHdr"></div>
		<p>
			<?=$_SESSION['user_message'] ?>
		</p>
		<br class="clear" />
		<div class="okBtn">
			<?php if(isset($_SESSION['user_open_login']) && $_SESSION['user_open_login']==1){
				echo '<a  style="cursor: pointer;" data-reveal-id="popup1" class="show_hide" >OK</a>'; 
				unset($_SESSION['user_open_login']);
			}else{
				echo "OK";	
			}
			?>			
		</div>
	</div>
</div>
<?php
$_SESSION['user_message'] = "";
unset($_SESSION['user_message']);

}
 ?>
<div class="footerTop">
	<div class="mainDiv2">
		<ul>
			<li>
				<a  href="<?=$general_func -> site_url ?>">Home</a>
			</li>
			<?php
			$sql_footer_menu="select id,seo_link,page_heading,page_name,page_target,link_path from static_pages where parent_id=0 and id!= 1 and page_position LIKE '%4%' order by display_order + 0 ASC";
			$result_footer_menu=$db->fetch_all_array($sql_footer_menu);
			$total_footer_menu=count($result_footer_menu);
			
			for($footer=0; $footer < 3; $footer++ ){
				$target=intval(trim($result_footer_menu[$footer]['page_target']))==2?'_blank':'_self';
				
				if(strlen(trim($result_footer_menu[$footer]['link_path'])) > 10){
					$link_path=trim($result_footer_menu[$footer]['link_path']);
				}else{
					$link_path=trim($result_footer_menu[$footer]['seo_link'])."/";								
				}
				
				if(strlen(trim($link_path)) < 3)
					continue;
				

			?>
			<li>
				<a  target="<?=$target ?>" href="<?=$link_path ?>"> <?=trim($result_footer_menu[$footer]['page_heading']) ?> </a>
				
				
				<?php
				$sql_headersub_menu="select id,seo_link,page_heading,page_name,page_target,link_path from static_pages where parent_id='" . $result_footer_menu[$footer]['id'] . "' and page_position LIKE '%4%' order by display_order + 0 ASC";
				$result_headersub_menu=$db->fetch_all_array($sql_headersub_menu);
				$total_headersub_menu=count($result_headersub_menu);
				if($total_headersub_menu > 0){
					echo "<ul>";
						
					for($headersub=0; $headersub < $total_headersub_menu; $headersub++ ){
						$target=intval(trim($result_headersub_menu[$headersub]['page_target']))==2?'_blank':'_self';
					
						if(strlen(trim($result_headersub_menu[$headersub]['link_path'])) > 10){
							$link_path=trim($result_headersub_menu[$headersub]['link_path']);
						}else{
							$link_path=trim($result_headersub_menu[$headersub]['seo_link']) ."/";									
						}?>
						<li><a  target="<?=$target?>" href="<?=$link_path?>"><?=trim($result_headersub_menu[$headersub]['page_heading'])?></a>
					<?php }	
					
					echo "</ul>";
				}		
				?>
			</li>
			<?php } ?>
		</ul>
		<?php if($total_footer_menu > 3){
		?>
		<ul>
			<?php
for($footer=3; $footer < 7; $footer++ ){
$target=intval(trim($result_footer_menu[$footer]['page_target']))==2?'_blank':'_self';

if(strlen(trim($result_footer_menu[$footer]['link_path'])) > 10){
$link_path=trim($result_footer_menu[$footer]['link_path']);
}else{
$link_path=trim($result_footer_menu[$footer]['seo_link'])."/";
}

if(strlen(trim($link_path)) < 3)
continue;
			?>
			<li>
				<a  target="<?=$target ?>" href="<?=$link_path ?>"> <?=trim($result_footer_menu[$footer]['page_heading']) ?> </a>
			</li>
			<?php } ?>
		</ul>

		<?php } ?>

		<?php if($total_footer_menu > 7){
		?>
		<ul>
			<?php
for($footer=7; $footer < $total_footer_menu; $footer++ ){
$target=intval(trim($result_footer_menu[$footer]['page_target']))==2?'_blank':'_self';

if(strlen(trim($result_footer_menu[$footer]['link_path'])) > 10){
$link_path=trim($result_footer_menu[$footer]['link_path']);
}else{
$link_path=trim($result_footer_menu[$footer]['seo_link'])."/";
}

if(strlen(trim($link_path)) < 3)
continue;
			?>
			<li>
				<a  target="<?=$target ?>" href="<?=$link_path ?>"> <?=trim($result_footer_menu[$footer]['page_heading']) ?> </a>
			</li>
			<?php } ?>
		</ul>

		<?php } ?>

		<div class="footer_separator"></div>

		<div class="free_training free_training_res">
			<a ><img src="images/icons/free-training.png" /></a>
		</div>

		<div class="footerTopRt">
			<p>
				Ph: <?php echo $general_func -> phone; ?>
				<br>
				<a href="mailto:<?php echo $general_func -> email; ?>"><?php echo $general_func -> email; ?></a>
				<br>
				<?php echo nl2br($general_func -> site_address); ?>
			</p>
			<div class="solMda">
				<a target="_blank" href="<?=$general_func -> facebook ?>"><img src="images/solIco1.png" alt="" /></a><a  target="_blank"  href="<?=$general_func -> twitter ?>"><img src="images/solIco2.png" alt="" /></a><a  target="_blank"  href="<?=$general_func -> google ?>"><img src="images/solIco3.png" alt="" /></a><a  target="_blank"  href="<?=$general_func -> youtube ?>"><img src="images/solIco4.png" alt="" /></a>
			</div>
		</div>

		<div class="free_training">
			<a href="fitness-centre/"><img src="images/icons/free-training.png" /></a>
		</div>

	</div>
	
</div>
<div class="footerBot">
	&copy; Copyright <?=date("Y") ?> Fit 'N' Food. All rights reserved.
</div>
<script type="text/javascript">
		function close_me () {
			document.getElementById("askQuesFrm").style.marginBottom="-350px";
		}
		
		function open_me () {
			document.getElementById("askQuesFrm").style.marginBottom="0px";
		}
		
	</script>
<div class="askQuesPnl">
	
	
	
	<script>
		

	function question_validate() {
			var error = 0;

			if(document.getElementById("question_name").value == '') {
				document.getElementById("question_name").style.border = "1px solid red";
				error++;
			} else {
				document.getElementById("question_name").style.border = "1px solid #DADCDD";
			}

		
			if(!validate_email_without_msg(document.getElementById("question_email"))) {
				document.getElementById("question_email").style.border = "1px solid red";
				error++;
			} else {
				document.getElementById("question_email").style.border = "1px solid #DADCDD";
			}
			
			if(document.getElementById("question_phone").value == '') {
				document.getElementById("question_phone").style.border = "1px solid red";
				error++;
			} else {
				document.getElementById("question_phone").style.border = "1px solid #DADCDD";
			}


			if(document.getElementById("question_message").value == '') {
				document.getElementById("question_message").style.border = "1px solid red";
				error++;
			} else {
				document.getElementById("question_message").style.border = "1px solid #DADCDD";
			}
			
			if(document.getElementById("captcha_input").value == '') {
				document.getElementById("captcha_input").style.border = "1px solid red";
				error++;
			} else {
				document.getElementById("captcha_input").style.border = "1px solid #DADCDD";
			}

			//****  check whether password exists *********//
			if(error > 0)
				return false;
			else
				return true;

		}
	</script>
		
	<h6 onclick="open_me();">Ask us a Question</h6>
    <img class="spechIcon" src="images/Speech_button.png" alt="" onclick="open_me();" />
    <div class="askQuesPnlInr" id="askQuesFrm">
    	<form method="post" name="frmquestion" action="<?=$general_func->site_url?>ask-a-question/" onsubmit="return question_validate();">
    	<input type="hidden" name="enter" value="question" />
    	 <input type="hidden" name="secreate" value="<?=$pass?>" />
		<input type="hidden" name="before_login_form_id" value="<?=$_SESSION['before_login_form_id'] ?>" />
    	<input name="question_name" id="question_name" type="text" value="" placeholder="Name"  />        
        <input name="question_email" id="question_email"  type="text" value="" placeholder="Email Address"  />
        <input name="question_phone" id="question_phone"  type="text" value="" placeholder="Phone Number"  />
        <textarea name="question_message" id="question_message"  rows="" cols="" placeholder="Message/Question"></textarea>
        <img src="captchaImage.php?secret=<?=$pass?>" alt="captcha image"/>
        <input name="captcha_input" id="captcha_input"  type="text" value="" placeholder="Enter what you see in the image"  />
        <input name="" type="submit" value="SUBMIT" />
        <input name="" type="button" value="CANCEL" onclick="close_me();" />
       </form>
    </div>
    </div>

<script>
	$(document).ready(function() {
		<?php if(!isset($_SESSION['user_open_login']) || $_SESSION['user_open_login']!=1){ ?>
			$(".okBtn").click(function() {
				$(".msgShow").hide();
			});
		<?php }	?>	

		$(".show_hide").click(function() {
			<?php if(isset($_SESSION['user_open_login']) && $_SESSION['user_open_login']==1){?>
				$(".msgShow").hide();
			<?php } ?>			
			$("#trainer_forgot_password_div").hide();
			$("#member_forgot_password_div").hide();
			$("#member_signup_div").hide();
			$("#member_trainer_login_div").show();
		});

		$(".membrYtSbmt").click(function() {
			$("#member_signup_div").slideToggle(500);
			$("#member_trainer_login_div").slideToggle(500);
		});
		
		$(".fgPass").click(function () {
			$("#member_trainer_login_div").slideToggle(500);
			$("#member_forgot_password_div").slideToggle(500);
		});
		
		$(".fgPass1").click(function () {
			$("#trainer_forgot_password_div").slideToggle(500);
			$("#member_trainer_login_div").slideToggle(500);
		});
		
		
		$("#trainer_back_to_login").click(function () {
			$("#trainer_forgot_password_div").slideToggle(500);
			$("#member_trainer_login_div").slideToggle(500);
		});

		$("#member_back_to_login").click(function () {
			$("#member_forgot_password_div").slideToggle(500);
			$("#member_trainer_login_div").slideToggle(500);
		});
		//**  Login/signup div open and close *****//

	});

</script>

<!-- Lightbox -->


<link rel="stylesheet" href="responsive_lightbox/stylesheet.css" type="text/css" charset="utf-8">
<link rel="stylesheet" href="responsive_lightbox/reveal.css" type="text/css">

<script type="text/javascript" src="responsive_lightbox/jquery.js"></script>
<div class="popBox lboxOne" style="top:130px; opacity:1; visibility:hidden;" id="popup1">
	<div class="lbHdr">
		<a class="close-reveal-modal"><img src="images/icons/closeLb.png" alt="" /></a>
	</div>

	<script>
		function user_signup_validate() {
			var error = 0;

			if(document.getElementById("fname").value == '') {
				document.getElementById("fname").style.border = "1px solid red";
				error++;
			} else {
				document.getElementById("fname").style.border = "1px solid #DADCDD";
			}

			if(document.getElementById("lname").value == '') {
				document.getElementById("lname").style.border = "1px solid red";
				error++;
			} else {
				document.getElementById("lname").style.border = "1px solid #DADCDD";
			}

			if(!validate_email_without_msg(document.getElementById("email_address"))) {
				document.getElementById("email_address").style.border = "1px solid red";
				error++;
			} else {
				document.getElementById("email_address").style.border = "1px solid #DADCDD";
			}
           
            if(document.getElementById("gender_r").value =="") {
          		document.getElementById("gender_r").style.border = "1px solid red";
				error++;  	
            }else{
            	document.getElementById("gender_r").style.border = "1px solid #DADCDD";	
            }

			 if(document.getElementById("hear_about_us").value =="") {
          		document.getElementById("hear_about_us").style.border = "1px solid red";
				error++;  	
            }else{
            	document.getElementById("hear_about_us").style.border = "1px solid #DADCDD";	
            }


			if(document.getElementById("password").value == '') {
				document.getElementById("password").style.border = "1px solid red";
				error++;
			} else {
				document.getElementById("password").style.border = "1px solid #DADCDD";
			}

			if(document.getElementById("cpassword").value == '') {
				document.getElementById("cpassword").style.border = "1px solid red";
				error++;
			} else {
				document.getElementById("cpassword").style.border = "1px solid #DADCDD";
			}

			if(document.getElementById("password").value != document.getElementById("cpassword").value) {
				$("#password_cpassword_msg").show();
				document.getElementById("password_cpassword_msg").innerHTML = "Password and confirm password must be same.";
				error++;
			} else {
				document.getElementById("password_cpassword_msg").innerHTML = "";
				$("#password_cpassword_msg").hide();
			}

			if(document.frmusersignup.term_of_use.checked == false) {
				document.getElementById("term_of_use").style.color = "red";
				error++;
			} else {
				document.getElementById("term_of_use").style.color = "";
			}

			if(error > 0)
				return false;
			else
				return true;

		}
	</script>

	<!-- Sign Up -->
	<div class="lbFrmPnl" id="member_signup_div"  style="display:none;">
		<h3><span>Sign Up,</span> It will take less than a minute</h3>
		
        <form method="post" name="frmusersignup" action="sign-up/" onsubmit="return user_signup_validate();">
			<input type="hidden" name="signup" value="users" />
			<input type="hidden" name="before_login_form_id" value="<?=$_SESSION['before_login_form_id'] ?>" />
			<ul>
				<li>
					<input name="fname" id="fname" type="text" value="" placeholder="Enter your First Name" />
				</li>
				<li>
					<input name="lname" id="lname" type="text" value="" placeholder="Enter your Last Name" />
				</li>
				<li>
					<input name="email_address" id="email_address" type="email" value="" placeholder="Enter your Email" />
				</li>
                <li>
					<label style="float:right" class="custom-select">
                        <select class="lg" id="gender_r" name="gender">
                            <option value="" selected>Gender</option>
                            <option value="1">Male</option>
                            <option value="2">Female</option>
                        </select>
                    </label>
				</li>
			</ul>
			<ul>
				<li>
					<input name="password" id="password" type="password" value="" placeholder="Password" />
				</li>
				<li>
					<input name="cpassword" id="cpassword" type="password" value=""  placeholder="Confirm Password" />
					<div class="alert_message" id="password_cpassword_msg" style="display: none;"></div>
				</li>
                <li>
					<label style="float:right" class="custom-select">
                        <select class="lg" id="hear_about_us" name="hear_about_us">
                            <option value="">How did you hear about us?</option>
                             <?php 
				            $sql_suburb="select id,name from hear_about_us where status=1 order by name ASC";
				            $result_suburb=$db->fetch_all_array($sql_suburb);
							$total_suburb=count($result_suburb);
				            
				            for($s=0; $s < $total_suburb; $s++){ ?>
				            	<option value="<?=$result_suburb[$s]['id']?>" <?=$hear_about_us==$result_suburb[$s]['id']?'selected="selected"':'';?>><?=$result_suburb[$s]['name']?></option>				
				            <?php } ?>
                        </select>
                    </label>
				</li>
				<li>
					<input name="refered_code" type="text" value="" placeholder="Referrer number if any" />
				</li>
			</ul>
			<br class="clear" />
			<p>
				<label>
					<input name="term_of_use" type="checkbox" value="" />
					<span id="term_of_use">I have read and agree to the Terms of Use</span> </label>
				<br class="clear" />
				<label>
					<input name="newsletters"  id="newsletters" type="checkbox" value="1" />
					<span>I'd like to receive "Fit 'N' Food" newsletters and special offers</span> </label>
			</p>
			<input name="submit" type="submit" value="Click to Sign Up" />
		</form>
	</div>

	<!-- LOGIN -->

	<script>
		function trainer_login_validate() {
			var error = 0;

			if(!validate_email_without_msg(document.getElementById("trainer_email"))) {
				document.getElementById("trainer_email").style.border = "1px solid red";
				error++;
			} else {
				document.getElementById("trainer_email").style.border = "1px solid #DADCDD";
			}

			if(document.getElementById("trainer_password").value == '') {
				document.getElementById("trainer_password").style.border = "1px solid red";
				error++;
			} else {
				document.getElementById("trainer_password").style.border = "1px solid #DADCDD";
			}

			if(error > 0)
				return false;
			else
				return true;

		}
		
		function member_login_validate() {
			var error = 0;

			if(!validate_email_without_msg(document.getElementById("member_email"))) {
				document.getElementById("member_email").style.border = "1px solid red";
				error++;
			} else {
				document.getElementById("member_email").style.border = "1px solid #DADCDD";
			}

			if(!validate_text_without_msg(document.getElementById("member_password"))) {
				document.getElementById("member_password").style.border = "1px solid red";
				error++;
			} else {
				document.getElementById("member_password").style.border = "1px solid #DADCDD";
			}

			//****  check whether password exists *********//
			if(error > 0)
				return false;
			else
				return true;

		}

		function trainer_forgotpassword_validate() {
			var error = 0;

			if(!validate_email_without_msg(document.getElementById("trainer_forgot_email"))) {
				document.getElementById("trainer_forgot_email").style.border = "1px solid red";
				error++;
			} else {
				document.getElementById("trainer_forgot_email").style.border = "1px solid #DADCDD";
			}
			//****  check whether password exists *********//
			if(error > 0)
				return false;
			else
				return true;

		}

		function member_forgotpassword_validate() {
			var error = 0;

			if(!validate_email_without_msg(document.getElementById("member_forgot_email"))) {
				document.getElementById("member_forgot_email").style.border = "1px solid red";
				error++;
			} else {
				document.getElementById("member_forgot_email").style.border = "1px solid #DADCDD";
			}
			//****  check whether password exists *********//
			if(error > 0)
				return false;
			else
				return true;

		}

	</script>	
	<!-- Tranier Forgot your Password -->
	<div class="lbFrmLog" id="trainer_forgot_password_div" style="display:none;">
		<form method="post" name="frmtrainerforgotpassword" action="forgot-password/" onsubmit="return trainer_forgotpassword_validate();">
			<input type="hidden" name="forgotpassword_type" value="trainers" />
			<input type="hidden" name="before_login_form_id" value="<?=$_SESSION['before_login_form_id'] ?>" />
			<h3>Forgot your <span>Password</span>?</h3>
			<input name="email" type="email" id="trainer_forgot_email" value="" placeholder="Enter Your Email Address" />

			<input name="submit" type="submit" value="Send" />
			<a  style="cursor: pointer;" class="fgPass2" id="trainer_back_to_login">&laquo; Back to login</a>
		</form>
	</div>
	<!-- Member Forgot your Password -->
	<div class="lbFrmLog" id="member_forgot_password_div" style="display:none;">
		<form method="post" name="frmmemberforgotpassword" action="forgot-password/" onsubmit="return member_forgotpassword_validate();">
			<input type="hidden" name="forgotpassword_type" value="users" />
			<input type="hidden" name="before_login_form_id" value="<?=$_SESSION['before_login_form_id'] ?>" />
			<h3>Forgot your <span>Password</span>?</h3>
			<input name="email" type="email" id="member_forgot_email" value="" placeholder="Enter Your Email Address" />
			<input name="submit" type="submit" value="Send" />
			<a  style="cursor: pointer;" class="fgPass2" id="member_back_to_login">&laquo; Back to login</a>
		</form>
	</div>

	<!-- Member Login -->

	<div id="member_trainer_login_div">
		<div class="dblColBox" >
			<div class="dblColBox1">
				<h3>Member's Login</h3>
				<form method="post" name="frmmemberlogin" action="login/" onsubmit="return member_login_validate();">
					<input type="hidden" name="login_type" value="users" />
					<input type="hidden" name="before_login_form_id" value="<?=$_SESSION['before_login_form_id'] ?>" />
					<input name="email"  type="email" id="member_email" value="" placeholder="Enter Your Email Address" />
					<input name="password" type="password" id="member_password"  value="" placeholder="Enter your Password" />
					<input name="submit" type="submit" value="Login" class="lgn" />
					<a style="cursor: pointer;" class="fgPass">Forgot your Password?</a>
					<br class="clear">
				</form>
				<div class="dblColBoxOrPnl">
					<span>OR</span>
				</div>
				<h3 style="padding-bottom:5px; padding-top:0">Login with OpenID &amp; get social</h3>
				<p>
					Select one of these third-party accounts:
				</p>
				<a href="<?=$general_func->site_url?>?login=facebook"><img src="images/facebookBtn.png" alt="" /></a>
				<a href="<?=$general_func->site_url?>?login=google"><img src="images/googleBtn.png" alt="" /></a>

			</div>
			<div class="dblColBox2">
				&nbsp;
			</div>
			<div class="dblColBox3">
				<h3>Trainer's Login</h3>
				<form method="post" name="frmtrainerlogin" action="login/" onsubmit="return trainer_login_validate();">
					<input type="hidden" name="login_type" value="trainers" />
					<input type="hidden" name="before_login_form_id" value="<?=$_SESSION['before_login_form_id'] ?>" />
					<input name="email"  type="email" id="trainer_email" value="" placeholder="Enter Your Email Address" />
					<input name="password" type="password" id="trainer_password"  value="" placeholder="Enter your Password" />
					<input name="submit" type="submit" value="Login" class="lgn" />
					<a style="cursor: pointer;" class="fgPass1">Forgot your Password?</a>
					<br class="clear">
				</form>
			</div>

		</div>
		<div class="membrYtPnl">
			Not a member yet?
			<input name="" type="submit" value="Click to Sign Up" class="membrYtSbmt" />
			<br class="clear">
		</div>
	</div>
</div>

</body></html>