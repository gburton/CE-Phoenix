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

if(file_exists('ext/aas/languages/'.$language.'.php')) include 'ext/aas/languages/'.$language.'.php'; else include 'ext/aas/languages/english.php';
if(isset($sessionTimeout)){echo'aasSessionTimeout'; die;}

require('ext/aas/application_top.php');

$action = (isset($_POST['action']) ? $_POST['action'] : '');

switch($action){

	case'uploadSmallImage':
	
		$categories_id = (isset($_POST['cid']) ? $_POST['cid'] : '');
		$path = (isset($_POST['images_path']) ? $_POST['images_path'] : '');
		if($path!='') $path=$path.'/';

		$status_change='0_@_';
		if(tep_not_null($categories_id)){

			$categories_image = new upload('categories_image');
			$categories_image->set_destination(DIR_FS_CATALOG_IMAGES.$path);
		
			if($categories_image->parse()){
			
				$pif=$path.$categories_image->filename;
				
				//if image exists with same filename then append random number
				if(file_exists(DIR_FS_CATALOG_IMAGES.$pif)){
			
					$a = mt_rand(100000,999999);
					$pif=$path.'c_'.$a.'_'.$categories_image->filename;
					$categories_image->set_filename('c_'.$a.'_'.$categories_image->filename);

				}
				
				if($categories_image->save()){
				
					$sql_data_array = array('categories_image'=>tep_db_prepare_input($pif), 'last_modified' => 'now()');
					tep_db_perform(TABLE_CATEGORIES, $sql_data_array, 'update', "categories_id = '" . (int)$categories_id . "'");
					$status_change=$pif.'_@_';
				
				}else $status_change='0_@_';
			
			
			}else $status_change='0_@_';
	
			if($messageStack->size > 0) echo $status_change.$messageStack->output();

		}else echo $status_change.AAS_TEXT_NO_CATEGORY_ID_FOUND;
	
	break;
	
}

?>
