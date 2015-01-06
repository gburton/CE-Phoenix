<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com
 
*/

chdir('../../../../');
require('includes/application_top.php');

if(!isset($_SERVER['HTTP_X_AAS'])) die;
if(isset($sessionTimeout)){

	$dataType = (isset($_POST['dataType']) ? $_POST['dataType'] : 'html');
	if($dataType=='json') echo json_encode(array('response'=>'aasSessionTimeout'));
	else echo'aasSessionTimeout';
	die;
	
}
if($_SERVER['HTTP_X_AAS']!==$_SESSION['admin']['AAS']['ajaxToken']) die;
defined('AAS') or define('AAS', 1);

if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){ echo 'Error! Wrong HTTP method!'; }

if(array_key_exists('file',$_FILES) && $_FILES['file']['error'] == 0 ){
	
	$file = $_FILES['file'];
	
	$allowed_ext = array('csv','txt');

	if(!in_array(get_extension($file['name']),$allowed_ext)){echo 'error';exit;}
	
	$data_array=array();
	$file_handle = fopen($file['tmp_name'], "r");
	while (!feof($file_handle)) $data_array[]=fgets($file_handle);
	fclose($file_handle);
		
	if(count($data_array)>0){
		
		$dt=data_handler($data_array);
		echo $dt ? $dt : 'error';
	
	}else echo 'error';
		
	exit;
	
}

function data_handler($data=array()){

	if(count($data)<=0) return false;
	
	//get the first line where there are the column names
	//find delimeter
	
	$possible_delimeters=array(',',';','~','*',"\t");
	
	$fieldsline=array_shift($data);
	
	$delimeter='';
	foreach($possible_delimeters as $pdel){
	
		$fields_array=explode($pdel,$fieldsline);
		if(count($fields_array)>1){ $delimeter=$pdel; break; }
	
	}
	
	if($delimeter=='') return false;
	
	$heads=array_shift($data);
	$head_array=explode($delimeter,$heads);
	
	if(count($head_array)<=0) return false;
	
	$tbl='<table id="tbl-file-import" class="tbl-general tablesorter"><thead><tr>';
	
	for($i=0,$n=count($head_array);$i<$n;$i++) $tbl.='<th><input type="hidden" value="'.trim($fields_array[$i]).'">'.$head_array[$i].'</th>';
	
	$tbl.='</tr></thead><tbody>';
	foreach($data as $line){
	
		$values=explode($delimeter,$line);
		if(count($values)>1 ){
			$tbl.='<tr id="product_id_'.$values[0].'">';
			foreach($values as $value) $tbl.='<td>'.$value.'</td>';
			$tbl.='</tr>';
	
		}
	
	}
	$tbl.='</tbody></table>';

	return $tbl;

}

// Helper functions

function get_extension($file_name){
	$ext = explode('.', $file_name);
	$ext = array_pop($ext);
	return strtolower($ext);
}
?>
