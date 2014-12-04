<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com
  
  Information: reorders products by changing the products_sort_order table field.
  				This field does not exist in osc by default.
 
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

$cPath = (isset($_GET['cPath']) ? $_GET['cPath'] : '');
$page = (isset($_GET['page']) ? $_GET['page'] : '');
$entriesPerPage = (isset($_GET['entriesPerPage']) ? $_GET['entriesPerPage'] : '');
$idsArray = (isset($_GET['idsArray']) ? $_GET['idsArray'] : '');

	if(tep_not_null($cPath) && tep_not_null($page) && tep_not_null($idsArray) && tep_not_null($entriesPerPage) ){

		$exp=explode(',',$idsArray);
		
		$pids=array();
		$cids=array();
		
		foreach($exp as $key => $e ){
		
			$john=explode('_',$e);
			if($john[0]=='pid') $pids[]=$john[1]; else $cids[]=$john[1];
		
		}

		$res= ((int)$page * (int)$entriesPerPage)-(int)$entriesPerPage;

		//for products		
		foreach($pids as $key => $pid){
			tep_db_query("UPDATE products SET products_sort_order='".($key+1+$res)."' WHERE products_id=".$pid." ");
		}
		
		//for categories
		foreach($cids as $key => $cid){
			tep_db_query("UPDATE categories SET sort_order='".($key+1)."' WHERE categories_id=".$cid." ");
		}
		
	}

	header('Location: ../../../../'.FILENAME_AAS.'?cPath='.$cPath.'&page='.$page.'&entriesPerPage='.$entriesPerPage);

?>
