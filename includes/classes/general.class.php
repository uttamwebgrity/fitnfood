<?php
class General{
	private $_redirect_to="";
	private $_A_to_Z="";
	private $_A="65";
	private $_Z="91";
	
	public $seo_link ="";
	public $month ="";
	public $show ="";
	
	
	//******************* Color code and height for admin section ******************//
	public $color1="#f9fcfc";
	public $color2="#eff9f9";
	public $deep_bg="#ba3836";
	public $lt_bg="#f9f9f9";
	public $hgt="93%";
	//*****************************************************************************//
	public $user_color1="#e4eed8";
	public $user_color2="#cedabe";		
	public $free_shipping_over="100";	
	//*********************  Global variables *************************************//
	
	public $admin_email_id="";
	public $no_reply="";
	
	public $site_title=""; 
	public $site_url="";
	public $admin_url="";
	public $date_format=""; 
	public $time_format=""; 
	public $admin_recoed_per_page=""; 
	
	
	public $security_questions=array(); 
	
	public $physical_endurances=array(); 
	
	
	
	public function A_to_Z($page_name,$class_name="text_numbering"){
		$this->_A_to_Z = "<a class=\"" . $class_name . "\" href=\"" .$page_name. "?key=\">ALL</a> <font class=\"" . $class_name . "\">|</font>";
		
		for($i=$this->_A; $i < $this->_Z; $i++){
			if($i == $this->_Z - 1){
				$this->_A_to_Z .= " <a class=\"" . $class_name . "\" href=\"" .$page_name. "?key=" .chr($i + 32). "\">" . chr($i) ."</a>";
			}else{
				$this->_A_to_Z .= " <a class=\"" . $class_name . "\" href=\"" .$page_name. "?key=" .chr($i +32). "\">" . chr($i) ."</a> <font class=\"" . $class_name . "\"> |</font>";
			}	
		}
		
		return ($this->_A_to_Z);
	
	}
	
	public function get_ip() {
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	
		}
		elseif(isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ip  = $_SERVER['HTTP_CLIENT_IP'];
		
		}
		else {
			$ip = $_SERVER['REMOTE_ADDR'];
		
		}
		return $ip; 
	}
	
	public function parent_static_page($parent_id){
		return(mysql_result(mysql_query("select page_heading from static_pages where id='" . $parent_id . "'"),0,0));
	} 
	
	
	
	
	public function order_status_heading($status){
		$show="";
		if($status==1)
			$show="<strong>Current order</strong>";
	   	else if($status==2)
			$show="<strong>Hold order</strong>";
		else if($status==3)
			$show="<strong>Cancelled order</strong>";	
		else if($status==4)
			$show="<strong>Deleted order</strong>";	
		else
			$show="<strong> Next week order order</strong>";
		return ($show);	
	}
	
	
	public function order_status($status){
		$show="";
		if($status==0)
			$show="Not yet started";
		else if($status==1)
			$show="<strong>Active/Running</strong>";
	   	else if($status==2)
			$show="Hold";
		else if($status==3)
			$show="Cancelled";	
		else if($status==4)
			$show="Deleted";	
		else
			$show="Next week order";
		return ($show);	
	}
	
	public function day_name($pm){
		$day="";
		if($pm=="1")
			$day="Monday";
		else if($pm=="2")
			$day="Tuesday";
	   	else if($pm=="3")
			$day="Wednesday";	
	   	else if($pm=="4")
			$day="Thursday";	
	    else if($pm=="5")
			$day="Friday";	
		else if($pm=="6")
			$day="Saturday";		  
		else
			$day="Sunday";
		return ($day);	
	}
	
	
	
	
	
	
	public function display_date($date,$date_format){
		$this->show	="";
		/*  date_format
		1 - May 26, 2011
		2 - 2011/05/26 
		3 - 05/26/2011 
		4 -  26/05/2011 */
		
		
		if(trim($date) == NULL || trim($date)=="0000-00-00 00:00:00")
			return ($this->show);
		
		if(trim($date_format) != NULL )
			$this->date_format = $date_format;
			
		
		if(trim($this->date_format) != NULL){
			if((int) $this->date_format ==1)
				$this->show	=date("M d, Y",strtotime($date));
				
			else if((int) $this->date_format ==2)
				$this->show	=date("Y/m/d",strtotime($date));

			else if((int) $this->date_format ==3)
				$this->show	=date("m/d/Y",strtotime($date));

			else if((int) $this->date_format ==4)
				$this->show	=date("m/d/Y",strtotime($date));
			else if((int)$this->date_format ==6)
				$this->show	=date("Y-m-d",strtotime($date));	
			else if((int) $this->date_format ==7){
				$date_disp=array();
				$date_disp=@explode("-",$date);
				$this->show	=$date_disp[2]."-".$date_disp[0]."-".$date_disp[1];
			}	
			else if((int) $this->date_format ==8)
				$this->show	=date("d/m/Y h:i A",strtotime($date));
			else if((int) $this->date_format ==9)
				$this->show	=date("jS M Y",strtotime($date));	
			else if((int) $this->date_format ==10)
				$this->show	=date("jS M Y h:i A",strtotime($date));	
			else if((int) $this->date_format ==11)
				$this->show	=date("d:m:Y",strtotime($date));	
			else if((int) $this->date_format ==12)
				$this->show	=date("m/d/Y h:i:s A",strtotime($date));	
									
			else 	
				$this->show	=$date;
		}
		return ($this->show);
	}
	
	
	public function display_time($date,$time_format){
		/*  time_format
		1 - 6:04 am
		2 - 06:04 am
		3 - 6:15 AM 
		4 -  06:15 AM  
		5 - 23:15  */
		$this->show	="";
		
		if(trim($date) != NULL){
			if((int) $date_format ==1)
				$this->show	=date("g:i a",strtotime($date));
				
			else if((int) $date_format ==2)
				$this->show	=date("h:i a",strtotime($date));

			else if((int) $date_format ==3)
				$this->show	=date("g:i A",strtotime($date));

			else if((int) $date_format ==4)
				$this->show	=date("h:i A",strtotime($date));
			else if((int) $date_format ==5)
				$this->show	=date("H:i",strtotime($date));	
			else 	
				$this->show	="";
		}
		return ($this->show);
	}

	
	
	
	public function show_gender($gender){
		$this->display="";
		
		if($gender =='c')
			$this->display="Couple";
		else if($gender == 'm')
			$this->display="Man";	
		else
			$this->display="Woman";
		
		return ($this->display);
	}
	
	public function show_status($status){
		$this->display="";
		
		if($status ==1)
			$this->display="Active";
		else
			$this->display="Inactive";
		
		return ($this->display);
	}
	
	public function show_draft($status){
		$this->display="";
		
		if($status ==1)
			$this->display="Yes";
		else
			$this->display="";
		
		return ($this->display);
	}
	
	
	
	
	public function date_convert($date,$flag=0){
		$this->display="";
		
		if($flag ==1){//***  mm/dd/yyyy to yyyy-mm-dd
			list($mm,$dd,$yy)=@split("/",($date));
			$this->display=$yy . "-". $mm . "-". $dd;
		}else if($flag ==2){//***   yyyy-mm-dd to mm/dd/yyyy
			
			list($yy,$mm,$dd)=@split("-",trim($date));
			$this->display=$mm . "/". $dd . "/". $yy;
			
		}else{
			$this->display="";
		}
		
		return ($this->display);
	}
	


	public function makeclickablelinks($text,$show_text="Click here"){
		$this->show = html_entity_decode($text);
		$this->show = " ".$this->show;
		$this->show = @eregi_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)',
				'<a href="\\1" class=htext style=font-weight:normal  target=_blank>' . $show_text.' </a>', $this->show);
		$this->show = @eregi_replace('(((f|ht){1}tps://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)',
				'<a href="\\1" class=htext style=font-weight:normal target=_blank>' . $show_text.'</a>', $this->show);
		$this->show = @eregi_replace('([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&//=]+)',
				'\\1<a href="https://\\2" class=htext style=font-weight:normal  target=_blank>' . $show_text.'</a>', $this->show);
		$this->show = @eregi_replace('([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})',
									'<a href="mailto:\\1" class=htext style=font-weight:normal  target=_blank>' . $show_text.'</a>', $this->show);
		return $this->show;
	}
	
	
	public function month_long_name($mon){
		$month="";
		switch($mon){
			case 1:
			case 01:
			  $month="January";
			  break;
			case 2:
			case 02:
			  $month="February";
			  break;
			case 3:
			case 03:
			  $month="March";
			  break;
			case 4:
			case 04:
			  $month="April";
			  break;
			case 5:
			case 05:
			  $month="May";
			  break;
			case 6:
			case 06:
			  $month="June";
			  break;
			case 7:
			case 07:
			  $month="July";
			  break;
			case 8:
			case 08:
			  $month="August";
			  break;
			case 9:
			case 09:
			  $month="September";
			  break;
			case 10:
			  $month="October";
			  break;
			case 11:
			 $month="November";
			 break;
			case 12:
			 $month="December";
			 break;
		}
  	return($month);
	}
	
	public function user_name($user_display_name,$user_first_name,$user_last_name){
		
		if(trim($user_display_name) != NULL)
			return($user_display_name);	
		else
			return($user_first_name ." ". $user_last_name);	
	
	}
	
	public function random_num($n=5){
		return rand(0, pow(10, $n));
	}
	
	public function document_rating($document_id){
		$this->show ="";
		$result=mysql_query("select SUM(rate) as rate,count(*) as total from document_rating where document_id='" . $document_id. "'");
		
		if(mysql_num_rows($result) > 0){
			$row=mysql_fetch_object($result);
			$total_rate=ceil($row->rate / $row->total);
			
			if($total_rate == 0)
				$this->show ="Not Yet Rated";
			else{
				for($i=1; $i<=$total_rate; $i++){
					$this->show .='<img src="images/full_rating.gif" alt="" />';	
				}
				for($i=$total_rate +1; $i<=5; $i++){
					$this->show .='<img src="images/no_rating.gif" alt="" />';	
				}
			
			}	
		
		}else{
			$this->show ="Not Yet Rated";
		
		}
		
		return ($this->show);
	}
	
	
	public function country_name($id){
		$this->show ="";
		$this->show=mysql_result(mysql_query("select name from countries where id='" . $id . "'"),0,0);
		return ($this->show);
	}

	
	
	public function create_seo_link($text){
		$letters = array('�', '�', '"', '�', '�', '\'', '�', '�', '�', '�', '&', '�', '>', '<', '$', '/');
		$text=str_replace($letters," ",$text);
		$text=str_replace("&","and",$text);
		$text=strtolower(str_replace(" ","-",$text));
		return ($text);
	}
	
	public function remove_space_by_hypen($text){
		$letters = array('�', '�', '"', '�', '�', '\'', '�', '�', '�', '�', '&', '�', '>', '<', '$', '/');
		$text=str_replace($letters," ",$text);
		$text=str_replace("&","and",$text);
		$text=strtolower(str_replace(" ","-",$text));
		return ($text);
	}
	
	
	
	public function genTicketString($len=7){
    	$length = $len;
   		$characters = "123456123456789ABCDEFGHIJKLMNPQ45612RSTUVWXYZ789ABCDEFGHIJKLMNPQRSTUVWXYZ";
    	$string="";
		
		for ($p = 0; $p < $length; $p++) {
        	$string .= $characters[mt_rand(0, strlen($characters)-1)];
    	}
    	return $string;
	}
	
	
	public function A_to_Z_T($page_name,$fid){
		$class_name="text_numbering";
		$this->_A_to_Z = "<a class=\"" . $class_name . "\" href=\"" .$page_name. "?forum_id=".$fid."&key=\">ALL</a> <font class=\"" . $class_name . "\">|</font>";
		
		for($i=$this->_A; $i < $this->_Z; $i++){
			if($i == $this->_Z - 1){
				$this->_A_to_Z .= " <a class=\"" . $class_name . "\" href=\"" .$page_name. "?forum_id=".$fid."&key=" .chr($i + 32). "\">" . chr($i) ."</a>";
			}else{
				$this->_A_to_Z .= " <a class=\"" . $class_name . "\" href=\"" .$page_name. "?forum_id=".$fid."&key=" .chr($i +32). "\">" . chr($i) ."</a> <font class=\"" . $class_name . "\"> |</font>";
			}	
		}
		
		return ($this->_A_to_Z);
	
	}
	public function A_to_Z_Tp($page_name,$tid){
		$class_name="text_numbering";
		$this->_A_to_Z = "<a class=\"" . $class_name . "\" href=\"" .$page_name. "?topic_id=".$tid."&key=\">ALL</a> <font class=\"" . $class_name . "\">|</font>";
		
		for($i=$this->_A; $i < $this->_Z; $i++){
			if($i == $this->_Z - 1){
				$this->_A_to_Z .= " <a class=\"" . $class_name . "\" href=\"" .$page_name. "?topic_id=".$tid."&key=" .chr($i + 32). "\">" . chr($i) ."</a>";
			}else{
				$this->_A_to_Z .= " <a class=\"" . $class_name . "\" href=\"" .$page_name. "?topic_id=".$tid."&key=" .chr($i +32). "\">" . chr($i) ."</a> <font class=\"" . $class_name . "\"> |</font>";
			}	
		}
		
		return ($this->_A_to_Z);
	
	}
	
	public function A_to_Z_fp($page_name,$tid){
		$class_name="text_numbering";
		$this->_A_to_Z = "<a class=\"" . $class_name . "\" href=\"" .$page_name. "?forum_id=".$tid."&key=\">ALL</a> <font class=\"" . $class_name . "\">|</font>";
		
		for($i=$this->_A; $i < $this->_Z; $i++){
			if($i == $this->_Z - 1){
				$this->_A_to_Z .= " <a class=\"" . $class_name . "\" href=\"" .$page_name. "?forum_id=".$tid."&key=" .chr($i + 32). "\">" . chr($i) ."</a>";
			}else{
				$this->_A_to_Z .= " <a class=\"" . $class_name . "\" href=\"" .$page_name. "?forum_id=".$tid."&key=" .chr($i +32). "\">" . chr($i) ."</a> <font class=\"" . $class_name . "\"> |</font>";
			}	
		}
		
		return ($this->_A_to_Z);
	
	}

	
	public function header_redirect($location){		
		$this->_redirect_to=$location;			
		header("location: ".$this->_redirect_to);
		exit();	
	}
	
	public function calculate_age($date_of_birth) {
		$show="";	
			
		if(trim($date_of_birth) == NULL)
			return $show;
		else{
			$diff = abs(time() - strtotime($date_of_birth)); 
			$show   = "Age "  . floor($diff / (365*60*60*24)); 	
			return $show;
		}
		
	}	
	
	public function how_many_days_ago($date2,$date1) {   
	  
	  
	  	$diff = abs(strtotime($date2) - strtotime($date1)); 



		$years   = floor($diff / (365*60*60*24)); 
		$months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
		$days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

		$hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60)); 
		$minuts  = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60); 
		$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minuts*60)); 
			
			
#printf("%d years, %d months, %d days, %d hours, %d minuts\n, %d seconds\n", $years, $months, $days, $hours, $minuts, $seconds); 
		
		if($years) {
		
			  echo "&nbsp;".$years." year";
			 if($years > 1) {
				 echo "s";
				 $months=0;$days=0;$hours=0;$minuts=0;$seconds=0;	 
			 }
			  $months=0;$days=0;$hours=0;$minuts=0;$seconds=0;
			}
			
			if($months) {

			echo "&nbsp;".$months." month";
			 if($months > 1) {
				 echo "s";
			 }
			$days=0;$hours=0;$minuts=0;$seconds=0;
			}
			
			 if($days) {
			
			 		 
			 echo "&nbsp;".$days." day" ;
			 if($days > 1) {
				 echo "s";
			 }
			 $hours=0;$minuts=0;$seconds=0;
			 }
			 
			 if($hours) {
			
			 	 
				echo "&nbsp;".$hours." hour";
			 if($hours > 1) {
				 echo "s";
			 }
			 $minuts=0;$seconds=0;
			 }
			 
			 if($minuts) {
			
				echo "&nbsp;".$minuts." minute";
			 if($minuts > 1) {
				 echo "s";
			 }
			 $seconds=0;
			 }
			 
			 if($seconds) {
			  
				echo "&nbsp;".$seconds." second";
			 if($seconds > 1) {
				 echo "s";
			 }	
			  }
			echo "&nbsp;ago"; 
  }


	public function how_many_days_left($date2,$date1) {   
	  
	  	$show="";
	  	
	  
	  	$diff = abs(strtotime($date2) - strtotime($date1)); 
		
		$hours   = floor($diff / (60*60)); 
		$hour_reminder=floor($diff % (60*60)); 		
		$minuts  = floor($hour_reminder/ 60);
		$seconds=floor($hour_reminder % 60);
		
		if($hours > 0){
			$show .= strlen($hours) ==1?"0".$hours:$hours;
		}else{
			$show .= "00";				
		}
		
		if($minuts > 0){
			$show .= ":";	
			$show .= strlen($minuts) ==1?"0".$minuts:$minuts;
		}else{
			$show .= ":00";				
		}
		
		if($seconds > 0){
			$show .= ":";
			$show .= strlen($seconds) ==1?"0".$seconds:$seconds;
		}else{
			$show .= ":00";					
		}		
	
		return $show;
  }






 public function pc_passwordcheck($pass) {
			
		$lc_pass = strtolower($pass);
	
		if (strlen($pass) < 8) {
			return 'Password must be at least 8 characters in length';
		}
	
		// count how many lowercase, uppercase, and digits are in the password
		$uc = 0; $lc = 0; $num = 0; $other = 0;
		for ($i = 0, $j = strlen($pass); $i < $j; $i++) {
			$c = substr($pass,$i,1);
			
			if (preg_match('/^[[:upper:]]$/',$c)) {
				$uc++;
			}elseif (preg_match('/^[[:lower:]]$/',$c)) {
				$lc++;
			}elseif (preg_match('/^[[:digit:]]$/',$c)) {
				$num++;
			} else {
				$other++;
			}
		}
		// the password must have more than two characters of at least
		// two different kinds
		$max = $j - 2;
		if ($uc > $max) {
			return "The password has too many upper case characters.";
		}
		if ($lc > $max) {
			return "The password has too many lower case characters.";
		}
		if ($num > $max) {
			return "The password has too many numeral characters.";
		}
		if ($other > $max) {
			return "The password has too many special characters.";
		}
	
		return (0);
	}

	public function only_for_paid_employer() {
				
		if(!isset($_SESSION['user_login']) || trim($_SESSION['user_login']) !="yes" || trim($_SESSION['user_type']) != "employer"){
			$_SESSION['user_message'] =$Converter->convert_me("Please login as an employer to view this page!",$_SESSION['language_name']);	
			$this->header_redirect("index.php");
		}		
		
		if(isset($_SESSION['save_as_draft']) && intval($_SESSION['save_as_draft']) == 1){//***********  draft account
			$_SESSION['user_message'] = $Converter->convert_me("Please complete your registration to view this page!",$_SESSION['language_name']);
			$this -> header_redirect("employer-registration-completion.php");					
		}
		
		if(trim($_SESSION['employer_membership_start']) == NULL){//*** membership not been started		
			$_SESSION['user_message']=$Converter->convert_me("Please choose a subscription plans to view this page!",$_SESSION['language_name']);
			$this -> header_redirect("employer-subscription.php");			
		}				
	}
	
	public function addOrdinalNumberSuffix($num) {
    	if (!in_array(($num % 100),array(11,12,13))){
      		switch ($num % 10) {
       			// Handle 1st, 2nd, 3rd
        		case 1:  return $num.'st';
        		case 2:  return $num.'nd';
        		case 3:  return $num.'rd';
      		}
    	}
    	return $num.'th';
  	}
  	
  	public function valid_file_type($file_name,$file_type) {	
	  	$allowedExts = array("gif", "jpeg", "jpg","pdf", "png","GIF", "JPEG", "JPG", "PNG");
		$extension = end(explode(".", $file_name));
		if ((($file_type == "image/gif") || ($file_type == "image/jpeg") || ($file_type == "image/jpg") || ($file_type == "image/png") ) && in_array($extension, $allowedExts)){
			return true;	
		}else{
			return false;
		}	
	}
	
	public function valid_file_type_only($file_name,$file_type,$type="pdf") {	  	
		if ($file_type == "application/pdf" || $file_type == "application/PDF"){
			return true;	
		}else{
			return false;
		}	
	}
	
	public function valid_specified_file_type_only($file_name,$file_type,$type="video/mp4",$extension="mp4") {	  	
		if (strtolower(trim($file_type)) == trim($type)){
			$file_extension = end(explode(".", $file_name));			
			if(strtolower(trim($file_extension)) == $extension){
				return true;		
			}else{
				return false;	
			}
		}else{
			return false;
		}	
	}
	
} 
?>