<?php
class Database {	

	private $_server   = "localhost";
	private $_user     = "root";
	private $_pass     = "";
	private $_database = "FitNFood";
	
	/*private $_server   = "localhost";
	private $_user     = "fitnfood_orderme";
	private $_pass     = "Order@$12meals";
	private $_database = "fitnfood_ordermeals";*/
	

	private $_pre      = "";
	private $_link_id = 0;
	
	 
	
	public $record = array();
	public $error = "";
	public $errno = 0;
	public $field_table= "";
	public $affected_rows = 0;
	public $query_id = 0;	
	
	

	public function __construct() {
		$this->_link_id=@mysql_connect($this->_server,$this->_user,$this->_pass);
	
		if (!$this->_link_id) {
			$this->oops("Could not connect to server: <b>$this->server</b>.");
		}
	
		if(!@mysql_select_db($this->_database, $this->_link_id)) {
			$this->oops("Could not open database: <b>$this->database</b>.");
		}
	
		$this->_server='';
		$this->_user='';
		$this->_pass='';
		$this->_database='';
	}
	
	
	function fetch_all_array($sql) {
		$query_id = $this->query($sql);
		$out = array();
	
		while ($row = $this->fetch_array($query_id, $sql)){
			$out[] = $row;
		}
	
		$this->free_result($query_id);
		return $out;
	}
	
	public function already_exist_inset($tbl_name="",$field1="",$data1="",$field2="",$data2="",$field3="",$data3="",$field4="",$data4=""){
		$sql="select $field1 from $tbl_name where 1";
		
		if(trim($field1) != NULL)
			$sql .=" and $field1='". $this->escape($data1) ."'";
		
		if(trim($field2) != NULL)
			$sql .=" and $field2='". $this->escape($data2) ."'";
			
		
		if(trim($field3) != NULL)
			$sql .=" and $field3='". $this->escape($data3) ."'";	
			
			
		if(trim($field4) != NULL)
			$sql .=" and $field4='". $this->escape($data4)."'";	
			
					
			
		 $result=$this->fetch_all_array($sql);
		 
		 return (count($result));
	}
	
	public function already_exist_update($tbl_name="",$primary_key,$primary_key_value,$field1="",$data1="",$field2="",$data2="",$field3="",$data3="",$field4="",$data4=""){
		$sql="select $field1 from $tbl_name where 1";
		
		if(trim($field1) != NULL)
			$sql .=" and $field1='". $this->escape($data1) ."'";
		
		if(trim($field2) != NULL)
			$sql .=" and $field2='". $this->escape($data2) ."'";
			
		
		if(trim($field3) != NULL)
			$sql .=" and $field3='". $this->escape($data3) ."'";	
			
			
		if(trim($field4) != NULL)
			$sql .=" and $field4='". $this->escape($data4) ."'";	
			
		$sql .=" and $primary_key !='". $this->escape($primary_key_value) ."'";					
			
		 $result=$this->fetch_all_array($sql);
		 
		 return (count($result));
	}

	public function close() {
		if(!mysql_close()){
			$this->oops("Connection close failed.");
		}
	}
	
	public function escape($string) {
		if(get_magic_quotes_gpc())
			$string = stripslashes($string);
		
		return mysql_real_escape_string($string);
	}
	
	public function query($sql){
		$this->query_id = @mysql_query($sql, $this->_link_id);

		if (!$this->query_id) {
			$this->oops("<b>MySQL Query fail:</b> $sql");
		}
	
		$this->affected_rows = @mysql_affected_rows();
		return $this->query_id;
	}
	
	public function query_delete($table,$where='1'){
		$q="DELETE from `".$this->pre.$table."`";
		$q .= ' WHERE '.$where.';';
		return $this->query($q);
	}
	
	
	public function query_delete_bulk($table,$field_id,$all_ids){
		$q="DELETE from `".$this->pre.$table."`";
		$q .= ' WHERE '.$field_id.' IN ('.$all_ids.');';
		return $this->query($q);
	}
	
	public function fetch_array($query_id=-1) {
		if ($query_id!=-1) {
			$this->query_id=$query_id;
		}

		if (isset($this->query_id)) {
			$this->record = @mysql_fetch_assoc($this->query_id);
		}else{
			$this->oops("Invalid query_id: <b>$this->query_id</b>. Records could not be fetched.");
		}

	
		if($this->record){
			$this->record=array_map("stripslashes", $this->record);
		
		}
		return $this->record;
	}
	
	public function fetch_result($query_id){
		return(mysql_result($query_id,0,0));
	}
	
	public function fetch_array_object($sql) {
		$query_id = $this->query($sql);
		
		$out = $this->fetch_result($query_id);
	
		$this->free_result($query_id);
		return $out;
	}
	
	public function fetch_single_result($query_id){
		return(mysql_fetch_object($query_id));
	}
	
	public function fetch_single_object($sql) {
		$query_id = $this->query($sql);
		
		$out = $this->fetch_single_result($query_id);
	
		$this->free_result($query_id);
		return $out;
	}

	public function free_result($query_id=-1) {
		if ($query_id!=-1) {
			$this->query_id=$query_id;
		}
		if(!@mysql_free_result($this->query_id)) {
			$this->oops("Result ID: <b>$this->query_id</b> could not be freed.");
		}
	}
	
	public function query_first($query_string) {
		$query_id = $this->query($query_string);
		$out = $this->fetch_array($query_id);
		$this->free_result($query_id);
		return $out;
	}



	public function max_id($table,$field_name, $where="") {
		$q="select max($field_name) from `".$this->pre.$table."`";
		if(trim($where) != NULL)
			$q .=$where;		
		$out = $this->fetch_array_object($q);
		return $out;
	}  
	


#-#############################################
# desc: does a query, fetches the first row only, frees resultset
# param: (MySQL query) the query to run on server
# returns: array of fetched results

function query_update($table, $data, $where='1') {
	$q="UPDATE `".$this->pre.$table."` SET ";

	foreach($data as $key=>$val) {
		if(strtolower($val)=='null') $q.= "`$key` = NULL, ";
		elseif(strtolower($val)=='now()') $q.= "`$key` = NOW(), ";
		else $q.= "`$key`='".$this->escape($val)."', ";
	}

	$q = rtrim($q, ', ') . ' WHERE '.$where.';';
	/*print $q;
	exit;*/
	return $this->query($q);
}#-#query_update()



function query_insert($table, $data) {
	$q="INSERT INTO `".$this->pre.$table."` ";
	$v=''; $n='';

	foreach($data as $key=>$val) {
		$n.="`$key`, ";
		if(strtolower($val)=='null') $v.="NULL, ";
		elseif(strtolower($val)=='now()') $v.="NOW(), ";
		else $v.= "'".$this->escape($val)."', ";
	}

	$q .= "(". rtrim($n, ', ') .") VALUES (". rtrim($v, ', ') .");";
	//echo $q; exit;

	if($this->query($q)){
		$this->free_result();
		return mysql_insert_id();
	}
	else return false;

}#-#query_insert()
function num_rows($sql){
	return(mysql_num_rows(mysql_query($sql)));
}



//*******************************************************************************************************//





#-#############################################
# desc: throw an error message
# param: [optional] any custom error to display
function oops($msg='') {
	if($this->_link_id>0){
		$this->error=mysql_error($this->_link_id);
		$this->errno=mysql_errno($this->_link_id);
	}

	$this->error=mysql_error();
	$this->errno=mysql_errno();
	?>
		
	<?php
}#-#oops()

//**** Alter table tbl_category rename to tbl_page_category **/

}//CLASS Database
###################################################################################################

?>