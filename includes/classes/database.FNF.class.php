<?php
class FNF extends Database{
	
	public $href=""; 
	public $dynamic_content=array(); 
	public $query_data=array(); 
	public $query_data_value=array(); 
	

	public function plan_category($meal_id,$type=1){	
		
		if($type == 1)
			$result_category=$this->fetch_all_array("select name from meal_plan_category_meals c INNER JOIN meal_plan_category p on c.meal_plan_category_id=p.id and meal_id='" . $this->escape($meal_id) . "' order by name");
		else
			$result_category=$this->fetch_all_array("select name from meal_plan_category_snacks c INNER JOIN meal_plan_category p on c.meal_plan_category_id=p.id and snack_id='" . $this->escape($meal_id) . "' order by name");
		
		$total=count($result_category);
		$show="";		
		if($total > 0){				
			for($c=0; $c < $total; $c++){
				$show .= $result_category[$c]['name'] .", ";
			}
			$show = substr($show,0,-2);
		}
		return $show; 		
	}
	
	
	public function refered_code_present($refered_code){
		if($refered_code == NULL){
			return 0; 	
		}else{			
			$sql="select refered_code from trainers where refered_code='" . $this->escape($refered_code) . "' limit 1";
			$result=$this->fetch_all_array($sql);						
			if(count($result) == 1)
				return 1; 	
			else
				return 0;						
		}
	}
	
	public function user_has_an_order($user_id){
		if($user_id == NULL){
			return 0; 	
		}else{			
			$sql="select id from orders where user_id='" . $this->escape($user_id) . "' limit 1";
			$result=$this->fetch_all_array($sql);						
			if(count($result) == 1)
				return $result[0]['id']; 	
			else
				return 0;						
		}
	}
	
	public function user_has_an_active_order($user_id,$state=1){//1=active,2=active or not yet started or hold
		if($user_id == NULL){
			return 0; 	
		}else{
			if($state == 1 )
				$query="status=1";
			else if($state == 2 )	
				$query="(status=0 or status=1 or status=2)";
			else if($state == 3 )	
				$query="(status=0 or status=1)";
					
			$sql="select id from orders where user_id='" . $this->escape($user_id) . "' and $query limit 1";
			$result=$this->fetch_all_array($sql);
									
			if(count($result) == 1)
				return $result[0]['id']; 	
			else
				return 0;						
		}
	}

	public function user_has_a_paid_week($user_id,$state=1,$start_date,$end_date){//1=active,2=active or not yet started or hold
		if($user_id == NULL){
			return 0; 	
		}else{
			if($state == 1 )
				$query="order_status=1";//ordered for any week
			else if($state == 2 )//***** paid for last week	
				$query="order_status=1 and  DATE(payment_date) >= '". $this->escape($start_date) ."' and DATE(payment_date) <= '". $this->escape($end_date) ."' ";
			else if($state == 3 )//***** paid for current week		
				$query="order_status=1 and  DATE(payment_date) >= '". $this->escape($start_date) ."' and DATE(payment_date) <= '". $this->escape($end_date) ."' ";
				
					
			$sql="select id from payment where user_id='" . $user_id . "' and $query limit 1";
			$result=$this->fetch_all_array($sql);
									
			if(count($result) == 1)
				return $result[0]['id']; 	
			else
				return 0;						
		}
	}
	
	public function order_can_be_modified($user_id,$order_id){
		if($order_id == NULL){
			return 0; 	
		}else{
			$sql="select meal_plan_category_id from orders where user_id='" . $this->escape($user_id) . "' and id='" . $this->escape($order_id) . "' and (status=0 or status=1 or status=5) limit 1";
			$result=$this->fetch_all_array($sql);
									
			if(count($result) == 1)
				return $result[0]['meal_plan_category_id']; 	
			else
				return 0;						
		}
	}	
	
	
	public function order_is_for_next_week($user_id,$order_id){
		if($order_id == NULL){
			return 0; 	
		}else{
			$sql="select status from orders where user_id='" . $this->escape($user_id) . "' and id='" . $this->escape($order_id) . "' limit 1";
			$result=$this->fetch_all_array($sql);
									
			if(count($result) == 1)
				return $result[0]['status']; 	
			else
				return 0;						
		}
	}	
			
		
		
		
		
	public function order_day_length($order_id){
		if(trim($order_id) == NULL){
			return 0; 	
		}else{			
			$sql="select MAX(which_day) as day_length from order_meals where order_id='" . $this->escape($order_id) . "' limit 1";			
			$result=$this->fetch_all_array($sql);
						
			if(count($result) > 0){
				return $result[0]['day_length']; 	
			}else{
				return 0; 
			}			
		}
	}
	
	
	
	public function product_exists($product_id){
		if(trim($product_id) == NULL){
			return false; 	
		}else{			
			$sql="select product_id from tbl_product where product_id='" . $this->escape($product_id) . "' limit 1";			
			$result=$this->fetch_all_array($sql);
						
			if(count($result) > 0){
				return true; 	
			}else{
				return false; 
			}			
		}
	}
	
		
	public function outfitter_name($id){
		$sql="select name from outfitters where id='" .$this->escape($id) . "'";
		$result=$this->fetch_all_array($sql);
		return ($result[0]['name']);
	}
	
	public function country_name($id){
		$country_name="";	
			
		if(trim($id) == NULL)
			return ($country_name);	
		else {
			$sql="select name from countries where id='" .$this->escape($id) . "'";
			$result=$this->fetch_all_array($sql);
			$country_name=$result[0]['name'];
			return ($country_name);
		}	
	}
	
	
	public function taxidermy_name($id){
		$sql="select name from taxidermy where id='" . $this->escape($id) . "'";
		$result=$this->fetch_all_array($sql);
		return ($result[0]['name']);
	}
	
	public function static_page_banner($page_id){
		$sql="select banner_path,banner_link,banner_target from  banners where page_id='" . $page_id. "' and banner_path <> '' limit 1";
		$result=$this->fetch_all_array($sql);		
		
		if(count($result) == 1){
			if(trim($result[0]['banner_link']) != NULL){ ?>
				<a  target="<?=(int)trim($result[0]['banner_target'])==1?'_self':'_blank'?>" href="<?=trim($result[0]['banner_link'])?>"><img src="banner_images/<?=trim($result[0]['banner_path'])?>" ></a>
			<?php }else{ ?>
				<img src="banner_images/<?=trim($result[0]['banner_path'])?>" >				
			<?php }
		 }else {
			return "&nbsp;"; 	
		}			 
	}
	
		
	
	
	public function static_page_content($page_name,$query_string){
		
		$sql="select id,link_name,page_name,page_heading,file_data,title,description,keyword from static_pages where page_name='" . $this->escape($page_name) . "' limit 1";
		$result=$this->fetch_all_array($sql);
		
		if(count($result) == 1){	
			$this->dynamic_content['page_id']=$result[0]['id'];
			$this->dynamic_content['link_name']=$result[0]['link_name'];
			$this->dynamic_content['page_name']=$result[0]['page_name'];
			$this->dynamic_content['page_heading']=$result[0]['page_heading'];
			$this->dynamic_content['file_data']=$result[0]['file_data'];
			$this->dynamic_content['title']=$result[0]['title'];
			$this->dynamic_content['description']=$result[0]['description'];
			$this->dynamic_content['keyword']=$result[0]['keyword'];	
			
		
			
		}else if($page_name=="custom-page.php" || in_array($page_name, $do_not_show_array) ){	
			$sql_global="select * from static_pages  where seo_link='" . $this->escape($query_string) . "' limit 1";
			
			$result_global=$this->fetch_all_array($sql_global);
			
			if(count($result_global) == 1){				
				$this->dynamic_content['page_id']=$result_global[0]['id'];
				$this->dynamic_content['page_heading']=$result_global[0]['page_heading'];
				$this->dynamic_content['page_title']=$result_global[0]['title'];
				$this->dynamic_content['page_keywords']=$result_global[0]['keyword'];
				$this->dynamic_content['page_description']=$result_global[0]['description'];
				$this->dynamic_content['file_data']=$result_global[0]['file_data'];								
			}			
		}else{
			$sql_global="select option_name,option_value from tbl_options where  admin_admin_id =1 and (option_name='global_meta_title' or option_name='global_meta_keywords'  or option_name='global_meta_description')";
			 
			$result_global=$this->fetch_all_array($sql_global);
			
			
			if(count($result_global) > 0){
				for($i=0; $i <count($result_global); $i++){
					$$result_global[$i]['option_name']=trim($result_global[$i]['option_value']);
				}
			}
			
			$this->dynamic_content['page_title']=$global_meta_title;
			$this->dynamic_content['page_keywords']=$global_meta_keywords;
			$this->dynamic_content['page_description']=$global_meta_description;
			
		}	
		return ($this->dynamic_content);
	
		
	}
	
	public function meal_time($time){
		
	 	if(trim($time) == NULL){
			return ($time);	
		}else{
			if(intval($time) == 1)
				return "Breakfast";	
			else if(intval($time) == 2)
				return "Lunch";	
			else if(intval($time) == 3)
				return "Dinner";	
			else if(intval($time) == 4)	
				return "Snacks 1";			
			else
				return "Snacks 2";	
		}
	}
	
	//******************* training module **************************//
		
	public function trainer_slot_exists($trainer_id,$which_day,$start_time,$end_time){
		if(trim($trainer_id) == NULL || trim($which_day) == NULL  || trim($start_time) == NULL || trim($end_time) == NULL){
			return true; 	
		}else{			
			$sql="select id from location_time_slots where trainer_id='" . $this->escape($trainer_id) . "' and which_day='" . $this->escape($which_day) . "' and (('" . $this->escape($start_time) . "' >= start_time and '" . $this->escape($start_time) . "' < end_time)  or ('" . $this->escape($end_time) . "' > start_time and '" . $this->escape($end_time) . "' <= end_time) or ('" . $this->escape($start_time) . "' <= start_time and '" . $this->escape($end_time) . "' >= end_time))  limit 1";			
			$result=$this->fetch_all_array($sql);				
			
			if(count($result) > 0){
				return true; 	
			}else{
				return false; 
			}			
		}
	}
	public function trainer_slot_exists_update($trainer_id,$which_day,$start_time,$end_time,$edited_time_slot){
		
		if(trim($trainer_id) == NULL || trim($which_day) == NULL  || trim($start_time) == NULL || trim($end_time) == NULL || trim($edited_time_slot) == NULL){
			return true; 	
		}else{			
			$sql="select id from location_time_slots where trainer_id='" . $this->escape($trainer_id) . "' and which_day='" . $this->escape($which_day) . "' and (('" . $this->escape($start_time) . "' >= start_time and '" . $this->escape($start_time) . "' < end_time)  or ('" . $this->escape($end_time) . "' > start_time and '" . $this->escape($end_time) . "' <= end_time) or ('" . $this->escape($start_time) . "' <= start_time and '" . $this->escape($end_time) . "' >= end_time)) and  id != '" . $this->escape($edited_time_slot) . "'  limit 1";			
			$result=$this->fetch_all_array($sql);				
			
			if(count($result) > 0){
				return true; 	
			}else{
				return false; 
			}			
		}
	}
	
	public function nutritional_value($value,$qty){
		if(intval($qty) == 150){
			return round((floatval($value) * 1.5),2);
		}else if(intval($qty) == 200){
			return round((floatval($value) * 2),2);
		}else{
			return $value;
		}	
	}
}
?>