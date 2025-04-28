<?php
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
/*if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS']!="on"){
	header("location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
}*/
$REQUEST_URI=@explode("/",$_SERVER['REQUEST_URI']);

if(in_array("trainer",$REQUEST_URI))
	$path_depth="../";
else
	$path_depth="";



include_once($path_depth ."includes/configuration.php");


$q_string=basename($_SERVER['QUERY_STRING']);	

if(basename($_SERVER['PHP_SELF'])!="custom-page.php")
	$q_string="";


$page_name=basename($_SERVER['PHP_SELF'])=="index.php"?'home.php':basename($_SERVER['PHP_SELF']);

$dynamic_content=$db_common->static_page_content($page_name,$q_string);


$REQUEST_URI=explode("/",$_SERVER['REQUEST_URI']);



if(!isset($_SESSION['before_login_form_id'])){
	$_SESSION['before_login_form_id']=$general_func->genTicketString(10);	
}

if(!isset($_SESSION['frm_get_your_meal_plan'])){
	$_SESSION['frm_get_your_meal_plan']=$general_func->genTicketString(10);
}

if(!isset($_SESSION['frm_choose_your_meal_plan'])){
	$_SESSION['frm_choose_your_meal_plan']=$general_func->genTicketString(10);
}



if(!isset($_SESSION['frm_customize_your_meal_plan'])){
	$_SESSION['frm_customize_your_meal_plan']=$general_func->genTicketString(10);
}


if(isset($_GET['modify_user']) && intval($_GET['modify_user']) > 0 && isset($_SESSION['admin_login']) &&  trim($_SESSION['admin_login']) == "yes"){
		
	unset($_SESSION['user_id']);	
	unset($_SESSION['user_fname']);	
	unset($_SESSION['user_lname']);	
	unset($_SESSION['user_email_address']);	
	unset($_SESSION['user_seo_link']);	
	unset($_SESSION['user_login_type']);	
	unset($_SESSION['user_login']);	
	unset($_SESSION['after_login_form_id']);	
		
	//**********  Login on behalf of user *******************//
	$sql_user_front_end="select id,fname,lname,email_address,seo_link 	 from users where id='". intval($_GET['modify_user']) ."' limit 1";
	$result_user_front_end=$db->fetch_all_array($sql_user_front_end);
	
	$_SESSION['user_id']=$result_user_front_end[0]['id'];
	$_SESSION['user_fname']=$result_user_front_end[0]['fname'];
	$_SESSION['user_lname']=$result_user_front_end[0]['lname'];
	$_SESSION['user_email_address']=$result_user_front_end[0]['email_address'];
	$_SESSION['user_seo_link']=$result_user_front_end[0]['seo_link'];	
	$_SESSION['user_login_type']= "users"; 	
	$_SESSION['user_login']= "yes";
	$_SESSION['admin_login_user_behalf']= "yes";
	$_SESSION['after_login_form_id']=$general_func->genTicketString(10);	
	//*******************************************************//
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta  http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?=$dynamic_content['title']?></title>
  <meta name="keywords" content="<?=$dynamic_content['keyword']?>" />
  <meta name="description" content="<?=$dynamic_content['description']?>" />
  <meta name="viewport" content="width=device-width; initial-scale = 1.0; maximum-scale=1.0; user-scalable=no" />
  <base href="<?=$general_func->site_url?>" />
  <meta http-equiv="cache-control" content="max-age=0" />
  <meta http-equiv="cache-control" content="no-cache" />
  <meta http-equiv="expires" content="0" />
  <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
  <meta http-equiv="pragma" content="no-cache" />

  
  <!-- main css -->
  <link href="css/reset.css" rel="stylesheet" type="text/css">
  <link href="css/fonts.css" rel="stylesheet" type="text/css">
  <link href="css/style.css" rel="stylesheet" type="text/css">
  <link href="css/responsive.css" rel="stylesheet" type="text/css">
  <link href="css/font-awesome.css" rel="stylesheet" type="text/css">
<!--[if lt IE 9]>
<script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js" type="text/javascript"></script>
<script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/ie7-squish.js" type="text/javascript"></script>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js" type="text/javascript"></script>
<![endif]-->





<link rel="Shortcut Icon" href="<?=$general_func->site_url?>images/fitnfood.ico"/>


<script type="text/javascript">

  $(window).load(function(){
   window.onorientationchange = function() { 
    var orientation = window.orientation; 
    switch(orientation) { 
      case 0: window.location.reload();
      break; 
      case 90: window.location.reload(); 
      break; 
      case -90: window.location.reload(); 
      break; } 
    };
    
  </script>
	<script type="text/javascript" src="slider_js/js/jquery.js"></script> 
  <!--<script type="text/javascript" src="js/jquery.min.js"></script>-->
  <script type="text/javascript">
    $(document).ready(function(){

      $(".mnuIcon").click(function(){
        $(".top-nav-primary-ul").slideToggle(500);
        $("ul.ddMnu").slideUp(500);
      });

      $("li.aftrLgin").click(function(){
        $("ul.ddMnu").slideToggle(500);
      });

      $("li.aftrLgin").mouseleave(function(){
        $("ul.ddMnu").slideUp(500);
      });


    });

  </script>

  <?php if(basename($_SERVER['PHP_SELF']) == "index.php"){?>

  <script type="text/javascript">
    $(function() {

      var header = $(".headerPnl");
      $(window).scroll(function() {
        var scroll = $(window).scrollTop();

        if (scroll >= 200) {
          header.removeClass('headerPnl').addClass("headerPnl3");

        } else {
          header.removeClass("headerPnl3").addClass('headerPnl');
        }
      });
    });
  </script> 

  <!-- Home Page Slider -->
  <link href="slider_js/css/flexslider.css" rel="stylesheet" type="text/css" />
  <!--<script type="text/javascript" src="slider_js/js/jquery.js"></script>-->
  <script type="text/javascript" src="slider_js/js/jquery.flexslider.js"></script>
  <script type="text/javascript">	
    $(window).load(function() {				
     if (($.browser.msie) && ($.browser.version < '9.0')) { 

      jQuery('.flexslider').flexslider({
        animation: "fade",			
        slideshow: true,			
        slideshowSpeed: 7000,
        animationDuration: 600,
        prevText: "",
        nextText: "",
        controlNav: true		
      }) 								

    } else{		 

      jQuery('.flexslider').flexslider({
        animation: "fade",			
        slideshow: true,			
        slideshowSpeed: 7000,
        animationDuration: 600,
        prevText: "",
        nextText: "",
        controlNav: true		
      }) 								

    }

  });
  </script>
  <!-- Home Page Slider -->
  <?php }else{ ?>
  <script type="text/javascript">
    $(function() {

      var header = $(".headerPnl2");
      $(window).scroll(function() {
        var scroll = $(window).scrollTop();

        if (scroll >= 1) {
          header.removeClass('headerPnl2').addClass("headerPnl3");
        } else {
          header.removeClass("headerPnl3").addClass('headerPnl2');
        }
      });
    });
  </script> 


  <?php } ?>

  <!-- Tabcontent -->
  <link rel="stylesheet" type="text/css" href="tabcontent_js/tabcontent.css" />
  <script type="text/javascript" src="tabcontent_js/tabcontent.js"></script>
  <!-- Tabcontent -->
  <script type="text/javascript" src="includes/validator.js"></script>

  <!--[if gte IE 8]>
  <script type="text/javascript" src="js/ie.js"></script>
  <![endif]-->
 
 <script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
  ga('create', 'UA-44857341-1', 'auto');
  ga('send', 'pageview');
</script>



</head>
<body>
  <div class="<?=basename($_SERVER['PHP_SELF']) == "index.php"?'headerPnl':'headerPnl2';?>">
    <div class="mainDiv"> <a href="<?=$general_func->site_url?>"><img src="images/logo.png" alt="" class="logo" /></a>
      <div class="topNav"> <div class="mnuIcon"><img src="images/ddMnuIco.png" alt="" /></div>
      <ul class="top-nav-primary-ul">
        <li><a href="<?=$general_func->site_url?>" <?=basename($_SERVER['PHP_SELF']) == "index.php"?' class="active"':'';?>>Home</a>
        	<?php
				$sql_headersub_menu="select id,seo_link,page_heading,page_name,page_target,link_path from static_pages where parent_id=1  order by display_order + 0 ASC";
				$result_headersub_menu=$db->fetch_all_array($sql_headersub_menu);
				$total_headersub_menu=count($result_headersub_menu);
				if($total_headersub_menu > 0){
					echo "<ul class=\"ddMnu2\">";
						
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
        <li><a href="how-it-works/" <?=basename($_SERVER['PHP_SELF']) == "how-it-works.php"?' class="active"':'';?>>How it Works</a>
        	<?php
				$sql_headersub_menu="select id,seo_link,page_heading,page_name,page_target,link_path from static_pages where parent_id=2  order by display_order + 0 ASC";
				$result_headersub_menu=$db->fetch_all_array($sql_headersub_menu);
				$total_headersub_menu=count($result_headersub_menu);
				if($total_headersub_menu > 0){
					echo "<ul class=\"ddMnu2\">";
						
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
        <li><a href="get-started/" <?=basename($_SERVER['PHP_SELF']) == "get-started.php"?' class="active"':'';?>>Get Started</a>
        	<?php
				$sql_headersub_menu="select id,seo_link,page_heading,page_name,page_target,link_path from static_pages where parent_id=3  order by display_order + 0 ASC";
				$result_headersub_menu=$db->fetch_all_array($sql_headersub_menu);
				$total_headersub_menu=count($result_headersub_menu);
				if($total_headersub_menu > 0){
					echo "<ul class=\"ddMnu2\">";
						
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
        <li><a href="select-your-meal-plan/" <?=basename($_SERVER['PHP_SELF']) == "select-your-meal-plan.php"?' class="active"':'';?>>Meal Plans</a></li>
        <li><a href="fitness-centre/" <?=basename($_SERVER['PHP_SELF']) == "fitness-centre.php"?' class="active"':'';?>>Fitness</a>
        	
        	<?php
				$sql_headersub_menu="select id,seo_link,page_heading,page_name,page_target,link_path from static_pages where parent_id=17  order by display_order + 0 ASC";
				$result_headersub_menu=$db->fetch_all_array($sql_headersub_menu);
				$total_headersub_menu=count($result_headersub_menu);
				if($total_headersub_menu > 0){
					echo "<ul class=\"ddMnu2\">";
						
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
        <?php if(isset($_SESSION['user_login_type']) && isset($_SESSION['user_login']) && trim($_SESSION['user_login']) == "yes"){ ?>
        <li class="aftrLgin"><a style="cursor: pointer;"><span><?=strlen($_SESSION['user_fname']) <= 7?$_SESSION['user_fname']:substr($_SESSION['user_fname'],0,7)?>...</span></a>
         <ul class="ddMnu">
          <li><a href="<?=trim($_SESSION['user_login_type']) == "trainers"?'trainer/':'';?>my-account/">My Account</a></li>
          <li><a href="<?=trim($_SESSION['user_login_type']) == "trainers"?'trainer/':'';?>update-profile/">Update Profile</a></li>
          <li><a href="logout/">Logout</a></li>
        </ul>
      </li>
      <?php	}else{ ?>
      <li><a  style="cursor: pointer;" class="show_hide" data-reveal-id="popup1">SignUp / Login</a></li>
      <?php } ?>
    </ul>
  </div>
  <div class="ordNwBtn">
  	<?php
  	if(isset($_SESSION['user_id']) && $db_common -> user_has_an_active_order(intval($_SESSION['user_id']),2) > 0){
		echo '<a href="my-account/">My<br /> Account</a> ';	
	}else if((isset($_SESSION['fill_the_questionnaire']) && is_array($_SESSION['fill_the_questionnaire'])) || (isset($_SESSION['choose_your_meal_plan']) && is_array($_SESSION['choose_your_meal_plan'])) || (isset($_SESSION['customize_your_meal_plan']) && is_array($_SESSION['customize_your_meal_plan']))){
  		echo '<a href="order-review/">Order<br /> Review</a> ';	
    }else{  		
    	echo '<a href="get-started/">Order<br /> Now</a>';		
   	} ?>	
      </div>
    </div>
  </div>

