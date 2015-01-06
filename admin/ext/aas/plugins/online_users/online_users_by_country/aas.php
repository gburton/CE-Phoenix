<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com
  
  Information: returns online users countries numbers in json, aas_ip2 table must be installed to db
 
*/

chdir('../../../../../');
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

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json;  charset=utf-8');
header("access-control-allow-origin: *");

if(isset($sessionTimeout)){$json=json_encode(array('aasSessionTimeout'));}
else{
	$ips_query = tep_db_query("SELECT ip_address FROM ". TABLE_WHOS_ONLINE);
	$ret=array();
	if(tep_db_num_rows($ips_query)>0){

		$a=array();
		while($ip=tep_db_fetch_array($ips_query)){
		
			$squery=tep_db_query("SELECT country_code,country_name FROM aas_ip2c WHERE ". sprintf("%u",ip2long($ip['ip_address'])) ." BETWEEN begin_ip_num AND end_ip_num");
			while($sq=tep_db_fetch_array($squery)){
		
				$a[]=$sq['country_name'];
		
			}

		}

		$n = array_count_values($a);
		arsort($n);
		foreach($n as $key => $nnn) $ret[$key]=$nnn;

	}

	$json = json_encode($ret);
}

# JSON if no callback
if( ! isset($_GET['callback'])) exit($json);

# JSONP if valid callback
if(is_valid_callback($_GET['callback'])) exit("{$_GET['callback']}($json)");

# Otherwise, bad request
header('status: 400 Bad Request', true, 400);

function is_valid_callback($subject){
    $identifier_syntax
      = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';

    $reserved_words = array('break', 'do', 'instanceof', 'typeof', 'case',
      'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue', 
      'for', 'switch', 'while', 'debugger', 'function', 'this', 'with', 
      'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum', 
      'extends', 'super', 'const', 'export', 'import', 'implements', 'let', 
      'private', 'public', 'yield', 'interface', 'package', 'protected', 
      'static', 'null', 'true', 'false');

    return preg_match($identifier_syntax, $subject)
        && ! in_array(mb_strtolower($subject, 'UTF-8'), $reserved_words);
}

?>
