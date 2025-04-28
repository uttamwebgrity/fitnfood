<?php
class EncryptDecrypt{	
	public function encode_me($plaintext) {
		return(base64_encode(md5(trim($plaintext))));
	} 	
	
	public function encrypt_me($plaintext) {
		return(base64_encode(trim($plaintext)));
	}
	
	public function decrypt_me($plaintext) {
		return(base64_decode(trim($plaintext)));
	}
	
}	
?>
