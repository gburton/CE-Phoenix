<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com
  
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

if(file_exists('ext/aas/languages/'.$language.'.php')) include 'ext/aas/languages/'.$language.'.php'; else include 'ext/aas/languages/english.php';
if(isset($sessionTimeout)){echo'aasSessionTimeout'; die;}

if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){ echo 'Error! Wrong HTTP method!'; }

$products_id = (isset($_POST['pid']) ? $_POST['pid'] : '');
$path = (isset($_POST['images_path']) ? $_POST['images_path'] : DIR_FS_CATALOG_IMAGES);
if($path!=DIR_FS_CATALOG_IMAGES) $path=$path.'/';

$status_change='0_@_';
if(tep_not_null($products_id)){

	$products_image = new upload('products_image');
	$products_image->set_destination($path);
	if($products_image->parse() && $products_image->save()){

		$pif=substr($path, strlen(DIR_FS_CATALOG_IMAGES)).$products_image->filename;
		$sql_data_array['products_image'] = tep_db_prepare_input($pif);
		$update_sql_data = array('products_last_modified' => 'now()');
		$sql_data_array = array_merge($sql_data_array, $update_sql_data);
		tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$products_id . "'");
		$status_change=$pif.'_@_';
	  
	}else $status_change='0_@_';
	
	if($messageStack->size > 0) echo $status_change.$messageStack->output();

}else echo $status_change.AAS_TEXT_NO_PRODUCT_ID_FOUND;

?>
