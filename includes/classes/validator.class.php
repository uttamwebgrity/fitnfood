<?php
class Validator{
	
	private $_length="";	
		
		
	public function validate_text($text){//*** field should not be blank
		if (trim($text) != NULL)
        	return true;
    	else
       		return false;    	
	}		
		
	public function allLetter($text){//*** field must have alphabet characters only
		if (ctype_alpha(trim($text)))
        	return true;
    	else
       		return false;    	
	}	
	
	public function allLetterandNumber($text){//*** Your field is not valid. Only alphabet characters and numbers are acceptable
		if (ctype_alnum(trim($text)))
        	return true;
    	else
       		return false;
	}	
	
	public function allNumeric($text){//*** Your field is not valid. Only number is acceptable 
		if (preg_match("/^[0-9]$/",trim($text)))
        	return true;
    	else 
       		return false;    	
	}	
	
	public function passwordvalidate($text,$min_len=6,$max_len=12){//*** Password must be of length between min to max
		$this->_length=strlen(trim($text));
		
		if($this->_length >=$min_len && $this->_length <=$max_len)
			return true;
		else	
			return false;
		
	} 	
	
	public function validate_price($price){
		$pattern = '/^(?:0|[0-9]\d*)(?:\.\d{1,2})?$/';	
		if (preg_match($pattern, $price) == '0') {
			return false;			
		}else if(intval($price) == 0){
			return false;
		}else{
			return true;	
		}		
	}
	
		
		
	public function validateEmail($email_id){
		if (eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email_id))	
			return true;
		else
			return false;			
	}
} 
?>