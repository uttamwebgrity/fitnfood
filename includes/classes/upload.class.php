<?php
class uploadclass {
	
	//************************  upload image without resize *****************************************//
	function image_upload_without_resize($auto_increment_id,$original='',$file_info){
		$return_data=array();
		$img =$original.$auto_increment_id."_".$file_info['userfile_name']; //original path
		move_uploaded_file($file_info['userfile_tmp'], $img) or die(); //image upload
		$return_data['upload']=$img;
		return ($return_data);						
	}
	//************************************************************************************************//
	
	function image_upload_with_resize($register_id,$width_small=0,$height_small=0,$original_path='',$small_path='',$file_info){
		$small_w =$width_small;
		$small_h =$height_small;
		$photo_path=$register_id."_".$file_info['userfile_name']; 
									
		$img_full_path =$original_path . $photo_path;//original path
		$img_small_path = $small_path. $photo_path; // small path
									
		move_uploaded_file($file_info['userfile_tmp'], $img_full_path); //image upload
		$sizes = getimagesize($img_full_path);
				
				
											
		//*****************************//
		list($width, $height) = getimagesize($img_full_path);
		$target_ratio = $small_w / $small_h;
		$image_ratio = $width / $height;
		
		if ($target_ratio > $image_ratio) {
			$new_height = $small_h;
			$new_width = $image_ratio * $small_h;
		} else {
			$new_height = $small_w / $image_ratio;
			$new_width = $small_w;
		}
				
		if ($new_height > $small_h) {
			$new_height = $small_h;
		}
		if ($new_width > $small_w) {
			$new_height = $small_w;
		}
		
		//***************************//	
		$dest = imagecreatetruecolor($new_width,$new_height);
											
		switch($sizes[2]){
			case 1:
				$src = imagecreatefromgif($img_full_path);
				break;
			case 2:
				$src = imagecreatefromjpeg($img_full_path);
				break;
			case 3:
				$src = imagecreatefrompng($img_full_path);
				break;
		}
									
		if(function_exists('imagecopyresampled')){
			imagecopyresampled($dest,$src,0,0,0,0,$new_width,$new_height,imagesx($src),imagesy($src));
		}else{
			Imagecopyresized($dest,$src,0,0,0,0,$new_width,$new_height,imagesx($src),imagesy($src));
		}
					
		imagejpeg($dest,$img_small_path,90)	or die('Problem In saving');
		imagedestroy($dest);
				
		$return_data['photo_path']=$photo_path;
		return ($return_data);						
	}
	function uploaded_image_resize($width_small=0,$height_small=0,$original_path='',$small_path='',$path){
		$small_w =$width_small;
		$small_h =$height_small;
		$photo_path=$path; 
									
		$img_full_path =$original_path . $photo_path;//original path
		$img_small_path = $small_path. $photo_path; // small path
									
		$sizes = getimagesize($img_full_path);
				
				
		//*****************************//
		list($width, $height) = getimagesize($img_full_path);
		$target_ratio = $small_w / $small_h;
		$image_ratio = $width / $height;
		
		if ($target_ratio > $image_ratio) {
			$new_height = $small_h;
			$new_width = $image_ratio * $small_h;
		} else {
			$new_height = $small_w / $image_ratio;
			$new_width = $small_w;
		}
				
		if ($new_height > $small_h) {
			$new_height = $small_h;
		}
		if ($new_width > $small_w) {
			$new_height = $small_w;
		}
		
		//***************************//	
		$dest = imagecreatetruecolor($new_width,$new_height);
											
		switch($sizes[2]){
			case 1:
				$src = imagecreatefromgif($img_full_path);
				break;
			case 2:
				$src = imagecreatefromjpeg($img_full_path);
				break;
			case 3:
				$src = imagecreatefrompng($img_full_path);
				break;
		}
									
		if(function_exists('imagecopyresampled')){
			imagecopyresampled($dest,$src,0,0,0,0,$new_width,$new_height,imagesx($src),imagesy($src));
		}else{
			Imagecopyresized($dest,$src,0,0,0,0,$new_width,$new_height,imagesx($src),imagesy($src));
		}
					
		imagejpeg($dest,$img_small_path,90)	or die('Problem In saving');
		imagedestroy($dest);
				
		$return_data['photo_path']=$photo_path;
		return ($return_data);						
	}
	
}

?>
