<?php
error_reporting(0);
function getExtension($str){
	$i = strrpos($str,".");
	if (!$i) { return ""; }
	$l = strlen($str) - $i;
	$ext = substr($str,$i+1,$l);
	return $ext;
}

mysql_connect("localhost",'root','');
mysql_select_db('fitnfood');



$csvfile="data.csv";

$sql = "INSERT INTO `suburb` (`suburb_name`, `suburb_postcode`,`suburb_state`) VALUES ";	

if ($csvfile) {
	$filename = stripslashes($csvfile);
	$extension = getExtension($filename);
	$extension = strtolower($extension);
	$ext=$extension;
	
	if($ext=="csv"){
		$fieldseparator = ",";
        $lineseparator = "\n";
		
		$newname="data".'.'.$extension;
		
		
		$file = fopen($newname,"r");
		$size = filesize($newname);
		if(!$size) {
			echo "File is empty.\n";
			exit;
		}
		
		$csvcontent = fread($file,$size);
		fclose($file);
		
		$lines = 0;
		$queries = "";
		$linearray = array();
		$lines = 0;
		$queries = "";
		$linearray = array();
		
		foreach(@split($lineseparator,$csvcontent) as $line) {
			$lines++;

			$line = trim($line," ,");
	
			$line = str_replace("\r","",$line);
	
			/************************************
			This line escapes the special character. remove it if entries are already escaped in the csv file
			************************************/
			$line = str_replace("'","\'",$line);
			/*************************************/
	
	
			$linearray = @explode($fieldseparator,$line);
			
			$data=array();
			$SuburbName =trim($linearray[0],'""');
			$ZipCodeNumber =trim($linearray[1],'""');			
			$StateCode =trim($linearray[2],'""');
			 	
		
		    if(trim($linearray[0]) != "SuburbName"){
		    	$sql .="('" . $SuburbName ."', '" . $ZipCodeNumber ."','" . $StateCode ."'), ";							    
			}
			
		}
		
		$sql = substr($sql,0,-2);
		$sql .=";";
		echo $sql;
		exit;
		mysql_query($sql);
		
		echo "done";
		exit;
			
	}elseif($ext=="xls"){
		$fieldseparator = "\t";
        $lineseparator = "\n";
		
		$csv_name="data".'.'.$extension;
		$newname=$csv_name;
		$copied = copy($_FILES['file']['tmp_name'], $newname);
		if (!$copied) {
		$_SESSION['errmessage']="Copy unsuccessfull";
		$errors=1;
		}
		$file = fopen($newname,"r");
		$size = filesize($newname);
		if(!$size) {
			$_SESSION['errmessage']= "File is empty.\n";
			exit;
		}
		require_once 'Excel/reader.php';

		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('CP1251');
	    
		$data->read($newname);
		
		
			for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {					
				for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
					echo $j."=\"".$data->sheets[0]['cells'][$i][$j]."\",";					
				}
			
				$file_data=array();
			    $file_data['first_name']=trim($data->sheets[0]['cells'][$i][2],'""');
				$file_data['last_name']=trim($data->sheets[0]['cells'][$i][1],'""');
				$file_data['email']=trim($data->sheets[0]['cells'][$i][7],'""');
				$file_data['city']=trim($data->sheets[0]['cells'][$i][3],'""');
				$file_data['zip_code']=trim($data->sheets[0]['cells'][$i][5],'""');
				$file_data['state']=trim($data->sheets[0]['cells'][$i][4],'""');
				$file_data['discipline']=trim($data->sheets[0]['cells'][$i][6],'""');
				$file_data['expiration_date']=date("Y-m-d",strtotime($data->sheets[0]['cells'][$i][8]));
		
			    if(trim($file_data['first_name']) != NULL){
			    	if(mysql_num_rows(mysql_query("select email from certificants where email ='" . trim($data->sheets[0]['cells'][$i][7],'""') . "'"))==0 && $file_data['city']!="City"){
						$db->query_insert("certificants",$file_data);	
						$counter++;
				    }
				 }
		    }
			
			if($db->affected_rows > 0){
				$_SESSION['message']="Successfully imported ".$counter." new certificants";
			}
		
			unlink($csv_name);
		}
		
	else{
		$_SESSION['errmessage']="Unknown file extention: your uploaded file should be csv and xls only";		
	}
}

header("Location:import-certificant.php");
exit();

?>
