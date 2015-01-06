<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com
  
*/

defined('AAS') or die;

//load AAS functions
require_once 'ext/aas/functions/general.php';

//Alternative Administration System Configuration File
require 'ext/aas/config.php';

//pagination class
require 'ext/aas/classes/pagination.php';

require DIR_WS_CLASSES.'currencies.php';
$currencies = new currencies();

$time=time();
$alerts=array();

$languages = tep_get_languages();
$languages_array = array();

for($i = 0, $n = sizeof($languages); $i < $n; $i++){

	$languages_array[] = array('id' => $languages[$i]['code'],'text' => $languages[$i]['name']);
	if($languages[$i]['directory'] == $language) $languages_selected=$languages[$i];
	
}

//create session cache array so to store various data
if(!isset($_SESSION['admin']['AAS']['cache'])) $_SESSION['admin']['AAS']['cache']=array();

//CHECK TO SEE IF table aas_settings IS INSTALLED IN DB
//unset($_SESSION['admin']['AAS']['table_columns_exist']['aas_settings']);
if((isset($_SESSION['admin']['AAS']['table_columns_exist']['aas_settings']) && !$_SESSION['admin']['AAS']['table_columns_exist']['aas_settings'] ) || !isset($_SESSION['admin']['AAS']['table_columns_exist']['aas_settings']) ){

  if(tep_db_num_rows(tep_db_query("SHOW TABLES LIKE 'aas_settings'"))==1) $_SESSION['admin']['AAS']['columns_exist']['aas_settings']=true;
  else{
   $_SESSION['admin']['AAS']['columns_exist']['aas_settings']=false;
    include 'oops.php';
   die;
  }

}

//SET DEFAULT FIELDS DISPLAY BY ADMIN

$aasDcd=array();
$aasDcd_query = tep_db_query("select * from aas_settings WHERE type='dcd' AND (skey='default_columns_display' OR skey='default_columns_display_per_admin')");
if(tep_db_num_rows($aasDcd_query)>0){

  while($aasDcd_row = tep_db_fetch_array($aasDcd_query)) $aasDcd[$aasDcd_row['skey']]=unserialize($aasDcd_row['value']);

  foreach($fieldsArray as $kf=> $f){

    $fieldsArray[$kf]['visible']=$aasDcd['default_columns_display'][$kf]['v']=='1'?true:false;
    $fieldsArray[$kf]['lockVisibility']=$aasDcd['default_columns_display'][$kf]['l']=='1'?true:false;

    //overide default columns per admin
    
    if(isset($aasDcd['default_columns_display_per_admin'][$_SESSION['admin']['id']][$kf]['o']) && $aasDcd['default_columns_display_per_admin'][$_SESSION['admin']['id']][$kf]['o']=='1'){
    
      $fieldsArray[$kf]['visible']=$aasDcd['default_columns_display_per_admin'][$_SESSION['admin']['id']][$kf]['v']=='1'?true:false;
      $fieldsArray[$kf]['lockVisibility']=$aasDcd['default_columns_display_per_admin'][$_SESSION['admin']['id']][$kf]['l']=='1'?true:false;
    
    }else{
    
      if(isset($aasDcd['default_columns_display_per_admin'][$_SESSION['admin']['id']][$kf]['v']) && $aasDcd['default_columns_display_per_admin'][$_SESSION['admin']['id']][$kf]['v']=='1') $fieldsArray[$kf]['visible']=true;
      if(isset($aasDcd['default_columns_display_per_admin'][$_SESSION['admin']['id']][$kf]['l']) && $aasDcd['default_columns_display_per_admin'][$_SESSION['admin']['id']][$kf]['l']=='1') $fieldsArray[$kf]['lockVisibility']=true;
    
    }
  
  }

}

//unset($_SESSION['admin']['AAS']['AAC']);
if(isset($_SESSION['admin']['AAS']['AAC'])){

	$aasAac=$_SESSION['admin']['AAS']['AAC'];

}else{

	//GET admin access control from db
	$aasAac_query = tep_db_query("select * from aas_settings WHERE sgroup='aac' order by id ASC");
	$aasAac=array();
	while ($aasAac_row = tep_db_fetch_array($aasAac_query)){
	
	  if($aasAac_row['skey']=='fields_disable_action'){
	  
	    $aasAac[$aasAac_row['skey']]=unserialize($aasAac_row['value']);
	  
	  }elseif($aasAac_row['skey']=='columns_sorted'){
	  
	    $aasAac[$aasAac_row['skey']][$aasAac_row['type']]=$aasAac_row['value'];
	  
	  }else{

		  $aac_temp=explode(',',$aasAac_row['value']);
		  foreach($aac_temp as $aac_temp_value){

			  if($aac_temp_value!='') $aasAac[$aasAac_row['type']][$aasAac_row['skey']][$aac_temp_value]=true;
		  //	else $aasAac[$aasAac_row['type']][$aasAac_row['key']][$aac_temp_value]=false;

		  }
		
		}

	}

	$_SESSION['admin']['AAS']['AAC']=$aasAac;

}

//CHANGE DEFAULTS BY AAC DATA
if(isset($aasAac['default'])){
  foreach($aasAac['default'] as  $aasAacDefaultKey => $aasAacDefaultValue){
	
	  if(isset($aasAacDefaultValue[$_SESSION['admin']['id']])) $defaults[$aasAacDefaultKey]=false;
	
  }
}


if(isset($_POST['radio'])){
	if($_POST['radio']=='1' || $_POST['radio']=='0' || $_POST['radio']=='2'){
		$show_products_by_status=$_POST['radio'];
		$_SESSION['admin']['AAS']['show_products_by_status']=$show_products_by_status;
	}else $show_products_by_status = (isset($_SESSION['admin']['AAS']['show_products_by_status']) ? $_SESSION['admin']['AAS']['show_products_by_status'] : $defaults['displayByStatus']);
}else $show_products_by_status = (isset($_SESSION['admin']['AAS']['show_products_by_status']) ? $_SESSION['admin']['AAS']['show_products_by_status'] : $defaults['displayByStatus']);

if(isset($_POST['entriesPerPage'])){
	if(in_array($_POST['entriesPerPage'],$perPageArray)){
		$entriesPerPage=$_POST['entriesPerPage'];
		$_SESSION['admin']['AAS']['entriesPerPage']=$entriesPerPage;
	}else $entriesPerPage = (isset($_SESSION['admin']['AAS']['entriesPerPage']) ? $_SESSION['admin']['AAS']['entriesPerPage'] : $defaults['entriesPerPage']);
}else $entriesPerPage = (isset($_SESSION['admin']['AAS']['entriesPerPage']) ? $_SESSION['admin']['AAS']['entriesPerPage'] : $defaults['entriesPerPage']);

$show_success_alert_messages = (isset($_SESSION['admin']['AAS']['show_success_alert_messages']) ? $_SESSION['admin']['AAS']['show_success_alert_messages'] : $defaults['displaySuccessAlertMessages']);
$show_error_alert_messages = (isset($_SESSION['admin']['AAS']['show_error_alert_messages']) ? $_SESSION['admin']['AAS']['show_error_alert_messages'] : $defaults['displayErrorAlertMessages']);

if(isset($_POST['orderBy'])){
	$orderBy=$_POST['orderBy'];
	$_SESSION['admin']['AAS']['orderBy']=$orderBy;
}else $orderBy = (isset($_SESSION['admin']['AAS']['orderBy']) && $_SESSION['admin']['AAS']['orderBy']!='p.products_sort_order' ? $_SESSION['admin']['AAS']['orderBy'] : $defaults['orderBy']);

if(isset($_POST['ascDesc'])){
	$ascDesc=$_POST['ascDesc'];
	$_SESSION['admin']['AAS']['ascDesc']=$ascDesc;
}else $ascDesc = (isset($_SESSION['admin']['AAS']['ascDesc']) ? $_SESSION['admin']['AAS']['ascDesc'] : $defaults['ascDesc']);

if(isset($_POST['displayCategories'])){
	$displayCategories=(int)$_POST['displayCategories'];
	$_SESSION['admin']['AAS']['displayCategories']=$displayCategories;
}else $displayCategories = (isset($_SESSION['admin']['AAS']['displayCategories']) ? (int)$_SESSION['admin']['AAS']['displayCategories'] : $defaults['displayCategories']);

if(isset($_POST['check'])){

	$columns_bool=array();

	if(isset($_POST['columncheck']) && isset($_POST['columncheckAll'])){
		
		foreach($fieldsArray as $kf => $fa){
		
		  if(isset($_POST['columncheck'][$kf])){

		    $fieldsArray[$kf]['visible'] = true;
		    $columns_bool[$kf]= (isset($fieldsArray[$kf]['visible']) ) ? true : false;
		   
		   }else{
		   
		   $fieldsArray[$kf]['visible'] = false;
		   $columns_bool[$kf]= false;
		   
		   }
		
		}

	}

	//store to session so to know which fields are
	$_SESSION['admin']['AAS']['tbl_fields']=$columns_bool;

}else{

	if(isset($_SESSION['admin']['AAS']['tbl_fields'])){

		foreach($_SESSION['admin']['AAS']['tbl_fields'] as $key => $val) $fieldsArray[$key]['visible']=$val;

	}

}

//if sort_order column is enabled and visible try to find if products_sort_order field exist in db
if($fieldsArray['sort_order']['visible']){

	if(isset($_SESSION['admin']['AAS']['orderByProductsSortOrder']) && $_SESSION['admin']['AAS']['orderByProductsSortOrder']){
	
    	$orderBy='p.products_sort_order';
			$_SESSION['admin']['AAS']['orderBy']=$orderBy;
			$_SESSION['admin']['AAS']['orderByProductsSortOrder']=true;
			
			$orderByArray=array('p.products_sort_order'=>AAS_TEXT_ORDER_BY_PRODUCTS_SORT_ORDER);	
	
	}else{

    $result_sort_order = tep_db_query("SHOW COLUMNS FROM ".TABLE_PRODUCTS." LIKE 'products_sort_order'");
    if(tep_db_num_rows($result_sort_order)){
    
    	$orderBy='p.products_sort_order';
			$_SESSION['admin']['AAS']['orderBy']=$orderBy;
			$_SESSION['admin']['AAS']['orderByProductsSortOrder']=true;
			
			$orderByArray=array('p.products_sort_order'=>AAS_TEXT_ORDER_BY_PRODUCTS_SORT_ORDER);
    
    }else{
    
    	if(isset($_SESSION['admin']['AAS']['orderByProductsSortOrder'])) unset($_SESSION['admin']['AAS']['orderByProductsSortOrder']);
    
    }
    
  }

}else{

		$_SESSION['admin']['AAS']['orderBy']=$orderBy;
		//just in case
		if(isset($_SESSION['admin']['AAS']['orderByProductsSortOrder'])) unset($_SESSION['admin']['AAS']['orderByProductsSortOrder']);

}

$counts_array=array('count_products','count_subcategories');

if(isset($_POST['ccheck'])){
	if(isset($_POST['countcheck'])){

		foreach($counts_array as $counts){
		
			if(in_array($counts,$_POST['countcheck'],true)){
							
				switch($counts){
					case 'count_products': $bool_count_products=true; break;
					case 'count_subcategories': $bool_count_subcategories=true; break;
				}
				
			}else{
		
				switch($counts){
					case 'count_products': $bool_count_products=false; break;
					case 'count_subcategories': $bool_count_subcategories=false; break;
				}
		
			}
		
		}

		//store to sessions
		$_SESSION['admin']['AAS']['count_products']=$bool_count_products;
		$_SESSION['admin']['AAS']['count_subcategories']=$bool_count_subcategories;

	}else{

		//We need bool variables for later using
		$bool_count_products= false;
		$bool_count_subcategories= false;
		$_SESSION['admin']['AAS']['count_products']=false;
		$_SESSION['admin']['AAS']['count_subcategories']=false;

	}

}else{

	$bool_count_products= isset($_SESSION['admin']['AAS']['count_products']) ? $_SESSION['admin']['AAS']['count_products'] : $defaults['countProducts'] ;
	$bool_count_subcategories= isset($_SESSION['admin']['AAS']['count_subcategories']) ? $_SESSION['admin']['AAS']['count_subcategories'] :  $defaults['countSubcategories'] ;

}

//SORT COLUMNS BASED ON: SESSION IF EXIST AND ON AAS SETTINGS STORED TO DB
//unset($_SESSION['sorted_fields_array']);
if(isset($_SESSION['admin']['AAS']['sorted_fields_array'])){

	$jexp=explode(',',$_SESSION['admin']['AAS']['sorted_fields_array']);
	$tempFieldsArray=array();
	foreach($jexp as $sfa){	$tempFieldsArray[$sfa]= $fieldsArray[$sfa]; }
	$fieldsArray=$tempFieldsArray;

}elseif(isset($aasAac['columns_sorted'][$_SESSION['admin']['id']])){

  $jexp=explode(',',$aasAac['columns_sorted'][$_SESSION['admin']['id']]);
	$tempFieldsArray=array();
  foreach($jexp as $sfa){	$tempFieldsArray[$sfa]= $fieldsArray[$sfa]; }
  $fieldsArray=$tempFieldsArray;

}else ;


//QUERIES
$categories_entries=0;

//$categories_count=0;
//$rows=0;

if(isset($_GET['search']) && tep_not_null($_GET['search']) ){

	$search = tep_db_prepare_input($_GET['search']);
	$catQuery='';
	$catLike=" AND cd.categories_name like '%" . tep_db_input($search) . "%'";

}else{

	$catQuery=" AND c.parent_id = '" . (int)$current_category_id . "'";
	$catLike='';
      
}

//IF WE DISPLAY THE CATEGORIES THEN EXECUTE QUERY
if($displayCategories){

  $catsOrderBy=isset($fieldsArray['sort_order']) && $fieldsArray['sort_order']['visible'] ? 'c.sort_order' : 'c.sort_order, cd.categories_name';

  $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' ".$catQuery.''.$catLike." order by ".$catsOrderBy." ".$ascDesc." ");
  $categories_entries=tep_db_num_rows($categories_query);

}

//IF MANUFACTURERS COLUMN IS VISIBLE THEN GET ONCE THE MANUFACTURERS LIST AND STORE THEM TO SESSION SO TO AVOID CALL TO DB UPON EVERY PAGE REFRESH
if($fieldsArray['manufacturers_name']['visible']){

	//store in SESSION manufacturers results in order to avoid continuously call db query
	if(!isset($_SESSION['admin']['AAS']['cache_manufacturers_array'])){

			$manufacturers_query=tep_db_query("SELECT manufacturers_id, manufacturers_name FROM manufacturers");
			if(tep_db_num_rows($manufacturers_query)>0){

				while($manufacturers_row=tep_db_fetch_array($manufacturers_query)) $manufacturers_array[]=array('id'=>$manufacturers_row['manufacturers_id'],'name'=>$manufacturers_row['manufacturers_name']);
				$_SESSION['admin']['AAS']['cache_manufacturers_array']=$manufacturers_array;
					
			}

	}else $manufacturers_array=$_SESSION['admin']['AAS']['cache_manufacturers_array'];

}


//SAME AS ABOVE. SEE MANUFACTURERS
if($fieldsArray['tax_class_title']['visible']){

	//store in SESSION tax classes results in order to avoid continuously call db query
	if(!isset($_SESSION['admin']['AAS']['cache_tax_class_array'])){
	
		$tax_class_query=tep_db_query("SELECT tc.tax_class_id, tc.tax_class_title, tr.tax_rate FROM tax_class tc, tax_rates tr WHERE tc.tax_class_id=tr.tax_class_id");
		if(tep_db_num_rows($tax_class_query)>0){

			while($tax_class_row=tep_db_fetch_array($tax_class_query)) $tax_class_array[]=array('id'=>$tax_class_row['tax_class_id'],'title'=>$tax_class_row['tax_class_title'],'tax_rate'=>tep_display_tax_value($tax_class_row['tax_rate']));
			$_SESSION['admin']['AAS']['cache_tax_class_array']=$tax_class_array;
		
		}
		
	}else $tax_class_array=$_SESSION['admin']['AAS']['cache_tax_class_array'];

}
//tax class

if(isset($_GET['search'])  && tep_not_null($_GET['search'])){

	$searchOnField= isset($_GET['searchOnField']) ? $_GET['searchOnField'] : 'pd.products_name';

	$searchSql=' AND '.$searchOnField." like '%" . tep_db_input($search) . "%'";
	$tblCategories='';
	$tblCategoriesWhere='';

	$tblCategories=', ' . TABLE_PRODUCTS_TO_CATEGORIES . ' p2c';
	$tblCategoriesWhere=' AND p.products_id = p2c.products_id ';

}else{

	$searchSql='';
	$tblCategories=', ' . TABLE_PRODUCTS_TO_CATEGORIES . ' p2c';
	$tblCategoriesWhere=' AND p.products_id = p2c.products_id and p2c.categories_id = '. (int)$current_category_id;

}
    
$orderBySql=isset($_GET['orderBy']) ? $_GET['orderBy'] : $orderBy;
$ascDescSql=isset($_GET['ascDesc']) ? $_GET['ascDesc'] : $ascDesc;

$cPath=isset($_GET['cPath']) && is_numeric($_GET['cPath']) ? (int)$_GET['cPath'] : 0;

$cPathString='cPath='.$cPath;
$categoryId=''.$cPath;

if(isset($_GET['search']) && tep_not_null($_GET['search']) ){

  $cPathString='search='.$_GET['search'];
  if(isset($_GET['searchOnField'])) $cPathString.='&searchOnField='.$_GET['searchOnField'];
  if(isset($_GET['ascDesc'])) $cPathString.='&ascDesc='.$_GET['ascDesc'];
  if(isset($_GET['orderBy'])) $cPathString.='&orderBy='.$_GET['orderBy'];
    
}
    
$currentPage=isset($_GET['page']) ? intval($_GET['page']) : 1;
    
if(intval($currentPage) < 1 ){ header('Location: aas.php?'.$cPathString.'&page=1' ); die; }
    
$limitLeft=$entriesPerPage*($currentPage-1);   
$limitation= ($entriesPerPage!='All') ? " LIMIT ".$limitLeft." , ".$entriesPerPage." " : '';
    
$selectQuery=array();
$fromQuery=array();
$whereQuery=array();
if(count($_EXTRA_FIELDS)>0){
	foreach($_EXTRA_FIELDS as $key => $extra_field){

		$selectQuery[]=$extra_field['SELECT'][$fieldsArray[$key]['visible']];
		$fromQuery[]=$extra_field['FROM'][$fieldsArray[$key]['visible']];
		$whereQuery[]=$extra_field['WHERE'][$fieldsArray[$key]['visible']];

	}
}

//IF WE USE TEMP PRODUCTS LIST THEN GET THOSE "PARKED" PRODUCTS
if($defaults['enableTempProductsList']){

	$parked_products_count=0;

	if(isset($_SESSION['admin']['AAS']['parkedProducts']) && is_array($_SESSION['admin']['AAS']['parkedProducts']) && count($_SESSION['admin']['AAS']['parkedProducts'])>0){

		$parked_products_query = tep_db_query('SELECT p2c.categories_id, p.*, pd.* '.implode(' ', $selectQuery).' 
		    FROM  '.implode(' ', $fromQuery).' ' . TABLE_PRODUCTS . ' p, ' . TABLE_PRODUCTS_DESCRIPTION . ' pd'.$tblCategories.' 
		    WHERE '.implode(' ', $whereQuery).' '.($show_products_by_status==2 ? '' : "p.products_status='".(int)$show_products_by_status."' AND").' p.products_id = pd.products_id AND p.products_id = p2c.products_id and pd.language_id = ' . (int)$languages_id . ' '.$searchSql.' AND p.products_id IN ('.implode(',',array_keys($_SESSION['admin']['AAS']['parkedProducts'])).')
		    ORDER BY '.$orderBySql.' '.$ascDescSql.'  ');
		
		$parked_products_count=tep_db_num_rows($parked_products_query);
		
	}

}   

//GET PRODUCTS QUERY
$products_query = tep_db_query('SELECT SQL_CALC_FOUND_ROWS p2c.categories_id, p.*, pd.* '.implode(' ', $selectQuery).' 
    FROM  '.implode(' ', $fromQuery).' ' . TABLE_PRODUCTS . ' p, ' . TABLE_PRODUCTS_DESCRIPTION . ' pd'.$tblCategories.' 
    WHERE '.implode(' ', $whereQuery).' '.($show_products_by_status==2 ? '' : "p.products_status='".(int)$show_products_by_status."' AND").' p.products_id = pd.products_id and pd.language_id = ' . (int)$languages_id . $tblCategoriesWhere.' '.$searchSql.' 
    ORDER BY '.$orderBySql.' '.$ascDescSql.'  '.$limitation.'  '); 

$totalRowsArray=tep_db_fetch_array(tep_db_query("SELECT FOUND_ROWS();"));

$totalRows=$totalRowsArray['FOUND_ROWS()'];

$entriesPerPageCalc =($entriesPerPage!='All') ? $entriesPerPage : $totalRows;

$pagination = new Pagination(
	array(

		'entriesPerPage'=>$entriesPerPageCalc,
		'currentPage'=> $currentPage,
		'queryString'=>$cPathString,
		'totalRows'=>$totalRows,
		'firstString'=>AAS_PAGINATION_FIRST,
		'lastString'=>AAS_PAGINATION_LAST,
		'nextString'=>AAS_PAGINATION_NEXT,
		'prevString'=>AAS_PAGINATION_PREVIOUS,
		'jumpString'=>AAS_PAGINATION_JUMP,
		'jumpToPageString'=>AAS_PAGINATION_JUMP_TO_PAGE,
		'file'=>FILENAME_AAS

	)
);

$totalPages=$pagination->totalPages($totalRows);

$entries=tep_db_num_rows($products_query);

if($currentPage>$totalPages && $totalPages!=0 ) { header('Location: '.FILENAME_AAS.'?'.$cPathString.'&page='.(  $totalPages-1 <= 0 ? 1 : $totalPages-1 ) ); die; }
	
if(isset($_GET['search']) && !tep_not_null($_GET['search']) ) { header('Location: '.(isset($_SESSION['admin']['AAS']['preSearchUrl']) ? $_SESSION['admin']['AAS']['preSearchUrl'] : 'aas.php?'.$cPathString )); die; }
			
//in order to remember where to return after pressing the x on search
if(!isset($_GET['search'])) $_SESSION['admin']['AAS']['preSearchUrl']= tep_href_link(FILENAME_AAS, $cPathString.'&amp;page='.$currentPage);

//currencies is need when displaying gross so display it then
if($fieldsArray['products_price_gross']['visible']){

	$currency_symbols=array();
	$default_currency_query = tep_db_query("select code, title, symbol_left, symbol_right, decimal_point, thousands_point, decimal_places, value from " . TABLE_CURRENCIES." WHERE code = '".DEFAULT_CURRENCY."' LIMIT 1");
	while ($default_currency = tep_db_fetch_array($default_currency_query)) {
	$currency_symbols[] = array('symbol_left' => $default_currency['symbol_left'],
                              'symbol_right' => $default_currency['symbol_right'],
                              'decimal_point' => $default_currency['decimal_point'],
                              'thousands_point' => $default_currency['thousands_point'],
                              'decimal_places' => $default_currency['decimal_places'],
                              'value' => $default_currency['value']);
	}

}

//GET SPECIALS DATA IF SPECIALS COLUMN IS VISIBLE
if(isset($fieldsArray['special']) && $fieldsArray['special']['visible']){

	$specials_query = tep_db_query("select p.products_id, pd.products_name, p.products_price, s.specials_id, s.specials_new_products_price, s.specials_date_added, s.specials_last_modified, s.expires_date, s.date_status_change, s.status from " . TABLE_PRODUCTS . " p, " . TABLE_SPECIALS . " s, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = s.products_id order by pd.products_name");

	$specials_products=array();

	if(tep_db_num_rows($specials_query)>0){
	 
		while($specials = tep_db_fetch_array($specials_query)){
		 
			$specials_products[$specials['products_id']]=array('specials_id'=>$specials['specials_id'],'status'=>$specials['status'],'old_price'=>$specials['products_price'],'new_price'=>$specials['specials_new_products_price'],'expires_date'=>$specials['expires_date']);
		 
		}
	 
	}

}

//NEEDED BY PRODUCTS QUANTY ORDER STATUS COLUMN
if($fieldsArray['products_order_status']['visible']){

	$os_query = tep_db_query("SELECT os.orders_status_name,os.orders_status_id FROM " . TABLE_ORDERS_STATUS . " os WHERE language_id= '" . (int)$languages_id . "' ORDER BY os.orders_status_id ASC");

	$orders_status_array=array();
	while($osr=tep_db_fetch_array($os_query)) $orders_status_array[$osr['orders_status_id']]=$osr['orders_status_name'];
	
	$pq_query = tep_db_query("SELECT op.products_id,o.orders_status, SUM(op.products_quantity) FROM " . TABLE_ORDERS . " o 
	LEFT JOIN ".TABLE_ORDERS_PRODUCTS." op  ON o.orders_id=op.orders_id
	GROUP BY op.products_id,o.orders_status ");
	
	//WHERE o.orders_status=3 
	$products_quantity_by_orders=array();
	while($pqr=tep_db_fetch_array($pq_query)) $products_quantity_by_orders[$pqr['products_id']][$pqr['orders_status']]=$pqr['SUM(op.products_quantity)'];
	
}

//store fetched products data for later use
$products_array=array();
$productsIds=array();
while ($products_fetched = tep_db_fetch_array($products_query)){ $products_array[]=$products_fetched; $productsIds[]=$products_fetched['products_id'];  }

//GET ATTRIBUTES COUNT IF ATTRIBUTES COLUMN IS VISIBLE
if(isset($fieldsArray['attributes']) && $fieldsArray['attributes']['visible']){
  
  if(count($productsIds)>0){

  $products_attributes_query = tep_db_query("SELECT products_id, COUNT(products_attributes_id) FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE products_id IN (".implode(',',$productsIds).") GROUP BY products_id ");
	$products_attributes=array();
	while ($products_attributes_row = tep_db_fetch_array($products_attributes_query)) $products_attributes[$products_attributes_row['products_id']]=$products_attributes_row['COUNT(products_attributes_id)'];
	
	}else $products_attributes=array();

}

//CHEK TO SEE IF COLUMN LINKED EXISTS

//unset($_SESSION['admin']['AAS']['table_columns_exist']['aas_settings']);
if((isset($_SESSION['admin']['AAS']['table_columns_exist']['linked']) && $_SESSION['admin']['AAS']['table_columns_exist']['linked']==false ) || !isset($_SESSION['admin']['AAS']['table_columns_exist']['linked']) ){
  $ptc_columns=tep_db_num_rows(tep_db_query("Show columns from ".TABLE_PRODUCTS_TO_CATEGORIES." like 'linked' "));
}else $ptc_columns=1;

if($ptc_columns>0){

  $_SESSION['admin']['AAS']['table_columns_exist']['linked']=true;

  $products_fetched_ids=array();
  foreach($products_array as $products) $products_fetched_ids[]=$products['products_id'];

  if($fieldsArray['products_linked']['visible']){

	  $products_fetched_ids=array();
	  foreach($products_array as $products) $products_fetched_ids[]=$products['products_id'];
	  if(count($products_fetched_ids)>0){
	  
	    $protoca_query = tep_db_query("SELECT ptc.*, cd.categories_name FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc, ".TABLE_CATEGORIES_DESCRIPTION." cd 
	    WHERE ptc.products_id IN (".implode(',',$products_fetched_ids).") AND ptc.categories_id=cd.categories_id AND cd.language_id='".(int)$languages_id."' ");

	    $protoca_array=array();
	    while($protoca=tep_db_fetch_array($protoca_query)) $protoca_array[$protoca['products_id']][]=array('cid'=>$protoca['categories_id'],'cname'=>$protoca['categories_name'],'linked'=>$protoca['linked']);
	
	  }

  }

	if(count($products_fetched_ids)>0){
	  $ptc_query = tep_db_query("SELECT ptc.* FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc 
	  WHERE ptc.products_id IN (".implode(',',$products_fetched_ids).") AND ptc.categories_id='".$categoryId."' AND ptc.linked='1' ");
	  $ptc_array=array();
	  while($ptc=tep_db_fetch_array($ptc_query)) $ptc_array[$ptc['products_id']][]=array('cid'=>$ptc['categories_id']);
	}

}else{

  $_SESSION['admin']['AAS']['table_columns_exist']['linked']=false;
  $alerts['warning']=AAS_ALERT_NO_COLUMN_LINKED_FOUND;

}

$cPath_back = '';
if(isset($cPath_array) && sizeof($cPath_array)>0){
	for ($i=0, $n=sizeof($cPath_array); $i<$n; $i++) $cPath_back .= $cPath_array[$i];
}

if($defaults['displayGoBackButton']){

	//Get the Parent Id, used from Go Back To Parent Button
	$parent_categories_query = tep_db_query("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$cPath_back . "'");
	$parent_categories = tep_db_fetch_array($parent_categories_query);
	$parent_id = $parent_categories['parent_id'];
	
}

//check to see if we have export action
if(isset($_POST['select-export-type']) && tep_not_null($_POST['select-export-type']) && isset($_POST['select-export-delimeter']) && tep_not_null($_POST['select-export-delimeter']) ) include 'ext/aas/plugins/export/index.php';

//disable sorting if we displaying one or less entries
if($entries>1 || $categories_entries>1) $enableColumnSorting = (isset($_SESSION['admin']['AAS']['enable_column_sorting']) ? $_SESSION['admin']['AAS']['enable_column_sorting'] : $defaults['tableSorting']); 
else $enableColumnSorting=false;
$sorting='';
if(isset($enableColumnSorting) && $enableColumnSorting ){
	$tt=array();
	$cnt=2;
	
	$tt[]=' 0: {sorter: false}';
	
	foreach($fieldsArray as $key => $value){

		if($value['visible']){$cnt++; if(isset($value['sortable']) && $value['sortable']==false) $tt[] =' '.$cnt.': {sorter: false}';}

	}

	$sorting=implode(',',$tt);

}

//unset($_SESSION['admin']['AAS']['modules']); //MUST BE REMOVED IN PRODUCTION
//modules loader since version 0.3
$modules=array();
$modules_count=0;
if(isset($_SESSION['admin']['AAS']['modules'])){

	$modules=$_SESSION['admin']['AAS']['modules'];
	
	foreach($modules as $km => $vm){
	
		if($vm['aac']){
		
			if(file_exists('ext/aas_modules/'.$km.'/languages/'.$language.'.php')) require 'ext/aas_modules/'.$km.'/languages/'.$language.'.php'; else require 'ext/aas_modules/'.$km.'/languages/english.php';
		
		}
	
	}
	
	$modules_count=count($modules);	

}else{

	if(file_exists(DIR_FS_ADMIN . 'ext/aas_modules')){

		if($dir = dir(DIR_FS_ADMIN . 'ext/aas_modules')){
			while($file = $dir->read()){

				if($file=='.' || $file=='..' ) continue;
				if(file_exists($dir->path.'/'.$file.'/loader.php')){
					
					$extTmp=include $dir->path.'/'.$file.'/loader.php';
					if($extTmp['enable']){

						if(isset($aasAac['modules'][$file][$_SESSION['admin']['id']])){
						
							//load language file
							if(file_exists('ext/aas_modules/'.$file.'/languages/'.$language.'.php')) require 'ext/aas_modules/'.$file.'/languages/'.$language.'.php';
							else require 'ext/aas_modules/'.$file.'/languages/english.php';
						
							$extTmp['title']=constant($extTmp['title']);
							$extTmp['aac']=true;
							$modules[$file]=$extTmp;
							$_SESSION['admin']['AAS']['modules']=$modules;
						
						}
					
					}
					
				}
				
			}
			$dir->close();
			$modules_count=count($modules);
		
		}

	}

}

//store the fields - columns array to session so it can be used from ajax calls
$_SESSION['admin']['AAS']['fieldsArray']=$fieldsArray;
$cats_fields = tep_aas_draw_categories_tree(0,'&nbsp;',$cPath_back,'selectedOptionCategoryClass',false);
?>
