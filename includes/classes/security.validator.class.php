<?php
class Security_validator{
	
	public function validate($val,$type){	
		switch($type){
			case 'email':
				return filter_var($val, FILTER_VALIDATE_EMAIL);	
			case 'integer':
				return filter_var($val, FILTER_VALIDATE_INT);		
			case 'ip':				
				return filter_var($val, FILTER_VALIDATE_IP);
			case 'url':
				return filter_var($val, FILTER_VALIDATE_URL);	
			case 'float':
				return filter_var($val, FILTER_VALIDATE_FLOAT);								
			default:
				return true;	
		}
	}
	
	public function replace_data_before_inserted($str){		
		//************* encode_php_tags 
		$str=str_replace(array('<?php', '<?PHP', '<?', '?>'),  array('&lt;?php', '&lt;?PHP', '&lt;?', '?&gt;'), $str);		
		return $str;
	}
	
	
	public function sanitize_filename($str, $relative_path = FALSE){
		$bad = array(
			"../",
			"<!--",
			"-->",
			"<",
			">",
			"'",
			'"',
			'&',
			'$',
			'#',
			'{',
			'}',
			'[',
			']',
			'=',
			';',
			'?',
			"%20",
			"%22",
			"%3c",		// <
			"%253c",	// <
			"%3e",		// >
			"%0e",		// >
			"%28",		// (
			"%29",		// )
			"%2528",	// (
			"%26",		// &
			"%24",		// $
			"%3f",		// ?
			"%3b",		// ;
			"%3d"		// =
		);

		if ( ! $relative_path)
		{
			$bad[] = './';
			$bad[] = '/';
		}

		$str = $this->remove_invisible_characters($str, FALSE);
		return stripslashes(str_replace($bad, '', $str));
	}
	
	
	public function remove_invisible_characters($str, $url_encoded = TRUE){
		$non_displayables = array();
		
		// every control character except newline (dec 10)
		// carriage return (dec 13), and horizontal tab (dec 09)
		
		if ($url_encoded){
			$non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
		}
		
		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

		do{
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		}while ($count);

		return $str;
	}
	
	private	function clean_input($input) {
		$search = array(
		    '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
		    '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
		    '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
		    '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
		  );
		
		$output = preg_replace($search, '', $input);
		return $output;
	}

	public function sanitize($input) {
    	if (is_array($input)) {
       		foreach($input as $var=>$val) {
            	$output[$var] = sanitize($val);
        	}
    	}else {
        	if (get_magic_quotes_gpc()) {
            	$input = stripslashes($input);
        	}
        	$input  = $this->clean_input($input);
        	$output = mysql_real_escape_string($input);
    	}
    return $output;	
	}	
}
?>