<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com
  
  Information: Some usefull functions needed by AAS

*/

defined('AAS') or die;

function tep_aas_draw_categories_tree($root_id=0, $spacing='&nbsp;', $selected=0, $selectedClass='', $drawSelectTag=true, $selectParams='', $selectClass=''){
    global $languages_id;
        
    //GET ALL CATEGORIES
    $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id='" . (int)$languages_id ."' order by sort_order, cd.categories_name");
    
    $items = array();
    while ($categories = tep_db_fetch_array($categories_query)) $items[$categories['categories_id']] = array('name' => $categories['categories_name'], 'parent_id' => $categories['parent_id'], 'id' => $categories['categories_id']);
    
    $citems=count($items);
    
    if($citems<=0) return '';
    elseif($citems==1) $children[] = $items; //in case we have one category item without subcategories, rare but possible
    else foreach( $items as $item ) $children[$item['parent_id']][] = $item;

		// loop will be false if the root has no children (i.e., an empty categories!)
		$loop = !empty( $children[$root_id] );

		$parent = $root_id;
		$parent_stack = array();
    $html=array();//store html code
    
		if($drawSelectTag) $html[]='<select class="'.$selectClass.'" '.$selectParams.'>';
		
		if($root_id==0) $html[]='<option value="0" '.($selected==0?'selected="selected" class="'.$selectedClass.'"':'').'>'.TEXT_TOP.'</option>';
		else{
		
		  if(isset($items[$root_id])) $html[]='<option value="'.$items[$root_id]['id'].'" '.($items[$root_id]['id']==$selected?'selected="selected" class="'.$selectedClass.'"':'').'>'.stripslashes($items[$root_id]['name']).'</option>';
		
		}
		
		while ( $loop && ( ( $option = each( $children[$parent] ) ) || ( $parent > $root_id ) ) ){

			if ( $option === false ){

				$parent = array_pop( $parent_stack );
				
			}elseif ( !empty( $children[$option['value']['id']] ) ){
                   
        $html[]='<option value="'.$option['value']['id'].'" '.($option['value']['id']==$selected?'selected="selected" class="'.$selectedClass.'"':'').'>'.str_repeat( $spacing, ( count( $parent_stack ) + 1 ) * 2 - 1 ).stripslashes($option['value']['name']).'</option>';

				$parent_stack[]=$option['value']['parent_id'];
				$parent = $option['value']['id'];

			}else{
				
				$html[]='<option value="'.$option['value']['id'].'" '.($option['value']['id']==$selected?'selected="selected" class="'.$selectedClass.'"':'').'>'.str_repeat( $spacing, ( count( $parent_stack ) + 1 ) * 2 - 1 ).stripslashes($option['value']['name']).'</option>';
				
			}
				
		}
    if($drawSelectTag) $html[]='</select>';
		if($drawSelectTag) echo implode( "", $html );
		else return implode( "", $html );

}


function tep_aas_get_category_ids($root_id = '0') {

    //GET ALL CATEGORIES
    $categories_query = tep_db_query("select c.categories_id, c.parent_id from " . TABLE_CATEGORIES . " c order by c.sort_order");
    
    $items = array();
    while ($categories = tep_db_fetch_array($categories_query)) $items[$categories['categories_id']] = array('parent_id' => $categories['parent_id'], 'id' => $categories['categories_id']);
    $citems=count($items);
    
    if($citems<=0) return '';
    elseif($citems==1) $children[] = $items; //in case we have one category item without subcategories, rare but possible
    else foreach( $items as $item ) $children[$item['parent_id']][] = $item;

		// loop will be false if the root has no children (i.e., an empty categories!)
		$loop = !empty( $children[$root_id] );

		$parent = $root_id;
		$parent_stack = array();
    $html=array();//store html code

		if($root_id>=0){
		
		  if(isset($items[$root_id])) $html[]=$items[$root_id]['id'];
		
		}
		
		while ( $loop && ( ( $option = each( $children[$parent] ) ) || ( $parent > $root_id ) ) ){

			if ( $option === false ){

				$parent = array_pop( $parent_stack );
				
			}elseif ( !empty( $children[$option['value']['id']] ) ){
                   
        $html[]=$option['value']['id'];
				$parent_stack[]=$option['value']['parent_id'];
				$parent = $option['value']['id'];

			}else{
				
				$html[]=$option['value']['id'];
				
			}
				
		}

		return $html;

}

//DEPRECATED in favor of tep_aas_get_category_ids which is not recursive
function tep_get_category_ids($parent_id = '0', $exclude = '', $category_tree_array = '') {
    global $languages_id;

    if (!is_array($category_tree_array)) $category_tree_array = array();
    if ( (sizeof($category_tree_array) < 1) && ($exclude != $parent_id) ) $category_tree_array[] = (int)$parent_id;//array('id' => $parent_id);

    $categories_query = tep_db_query("select c.categories_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' and c.parent_id = '" . (int)$parent_id . "' order by c.sort_order, cd.categories_name");
    
    while ($categories = tep_db_fetch_array($categories_query)) {
      if ($exclude != $categories['categories_id']) $category_tree_array[] = (int)$categories['categories_id']; //array('id' => (int)$categories['categories_id']);
      $category_tree_array = tep_get_category_ids($categories['categories_id'], $exclude, $category_tree_array);
    }

    return $category_tree_array;
}


function tep_get_products_parent_categories($products_id=0){
	
	global $languages_id;
	if((int)$products_id!=0){
	
		 $categories_query = tep_db_query("select cd.categories_name, cd.categories_id from " . TABLE_CATEGORIES_DESCRIPTION . " cd, ".TABLE_PRODUCTS_TO_CATEGORIES." ptc 
		 where cd.categories_id = ptc.categories_id AND ptc.products_id='".(int)$products_id."' and cd.language_id = '" . (int)$languages_id . "' limit 1");
	 	
	 	$parents=array();
	 	
	 	while ($categories = tep_db_fetch_array($categories_query)) $parents[]= tep_get_category_parent_by_category($categories['categories_id']);

	 	return $parents;
	
	}

}

function tep_get_category_parent_by_category($categories_id=0,$category_tree_array = ''){

	global $languages_id;
		
		if(!is_array($category_tree_array)) $category_tree_array = array();
	
		 $categories_query = tep_db_query("select cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c,  " . TABLE_CATEGORIES_DESCRIPTION . " cd 
		 where c.categories_id='".(int)$categories_id."' and c.categories_id=cd.categories_id and cd.language_id = '" . (int)$languages_id . "' ");
		
	 	while ($categories = tep_db_fetch_array($categories_query)) { 

	 	 	$category_tree_array[$categories['parent_id']] = $categories['categories_name'];

	 		if($categories['parent_id']!='0') $category_tree_array = tep_get_category_parent_by_category($categories['parent_id'],$category_tree_array);
	 	
	 	}
	 	 	
	 	return $category_tree_array;
	
}


function tep_get_category_parents($categories_id=0,$category_tree_array = ''){

	global $languages_id;
		
		if(!is_array($category_tree_array)) $category_tree_array = array();
	
		 $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c,  " . TABLE_CATEGORIES_DESCRIPTION . " cd 
		 where c.categories_id='".(int)$categories_id."' and c.categories_id=cd.categories_id and cd.language_id = '" . (int)$languages_id . "' ");
		
	 	while ($categories = tep_db_fetch_array($categories_query)) { 

	 	 	$category_tree_array[$categories['categories_id']] = $categories['categories_name'];

	 		if($categories['parent_id']!='0') $category_tree_array = tep_get_category_parents($categories['parent_id'],$category_tree_array);
	 	
	 	}
	 	 	
	 	return $category_tree_array;
	
}

function tep_get_price_with_tax($products_price,$products_tax,$default_currency,$calculate_currency_value=true){
	
	$price = tep_round(tep_add_tax($products_price, $products_tax,true), $default_currency['decimal_places']);
	
	if ($calculate_currency_value){
		$format_string = number_format($price * $default_currency['value'], $default_currency['decimal_places'], $default_currency['decimal_point'], $default_currency['thousands_point']);
	}else{
		$format_string = number_format($price, $default_currency['decimal_places'], $default_currency['decimal_point'], $default_currency['thousands_point']);
	}
	
	return $format_string;

}
  
function tep_aas_link($section = 'admin', $page = '', $parameters = '', $search_engine_safe = true) {
    
    $page = tep_output_string($page);

    if($section!=='admin'){

      if(defined('ENABLE_SSL')){
      
        $link = ENABLE_SSL == true ? HTTPS_CATALOG_SERVER . DIR_WS_HTTPS_CATALOG : HTTP_CATALOG_SERVER . DIR_WS_CATALOG;
              
      }elseif(defined('ENABLE_SSL_CATALOG')){ //for old version of osc
      
        if(ENABLE_SSL_CATALOG == true){
        
          if(defined('DIR_WS_HTTPS_CATALOG')) $link=HTTPS_CATALOG_SERVER . DIR_WS_HTTPS_CATALOG;
          else $link = HTTPS_CATALOG_SERVER . DIR_WS_CATALOG;
                
        }else $link = HTTP_CATALOG_SERVER . DIR_WS_CATALOG;
      
      }else $link='#';

    }else{
    
      if(defined('ENABLE_SSL')){
      
        if(defined('DIR_WS_HTTPS_ADMIN')) $link = ENABLE_SSL == true ? HTTPS_SERVER . DIR_WS_HTTPS_ADMIN : HTTP_SERVER . DIR_WS_ADMIN;
        else $link = ENABLE_SSL == true ? HTTPS_SERVER . DIR_WS_ADMIN : HTTP_SERVER . DIR_WS_ADMIN;
                      
      }elseif(defined('ENABLE_SSL_CATALOG')){ //for old version of osc
      
        $link = ENABLE_SSL_CATALOG == true ? HTTPS_CATALOG_SERVER . DIR_WS_ADMIN : HTTP_SERVER . DIR_WS_ADMIN;
      
      }else $link='#';
    
    }

    if (tep_not_null($parameters)) {
      $link .= $page . '?' . tep_output_string($parameters);
      $separator = '&';
    } else {
      $link .= $page;
      $separator = '?';
    }

    while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);

    while (strstr($link, '&&')) $link = str_replace('&&', '&', $link);

    if($section!=='admin'){
    
      if ( (SEARCH_ENGINE_FRIENDLY_URLS == 'true') && ($search_engine_safe == true) ) {
        $link = str_replace('?', '/', $link);
        $link = str_replace('&', '/', $link);
        $link = str_replace('=', '/', $link);
      } else {
        $link = str_replace('&', '&amp;', $link);
      }
    
    }

    return $link;
}

function listFiles($dir){
	global $aas_cpath;
	
    $ffs = scandir($dir);
	$downloads_folder=substr(DIR_FS_DOWNLOAD, strlen(DIR_FS_CATALOG));
    echo '<ol style="margin-left:5px;">';
    foreach($ffs as $ff){

        if($ff != '.' && $ff != '..' && $ff!='.htaccess'){
        	echo '<li class="downloadable_filename_li">';
        	
        	if(is_file($dir.'/'.$ff)){

				$fs=substr($dir.'/'.$ff, strlen(DIR_FS_DOWNLOAD));
		        echo '<label><input type="radio" name="dowloadable_filename" class="dowloadable_filename" value="'.$fs.'">'.$downloads_folder.'<strong style="color:green">'.$fs.'</strong></label>';
		     
		     }elseif(is_dir($dir.'/'.$ff)){
		        
		        listFiles($dir.'/'.$ff);
		        echo '</li>';
		        
		     }
		     
        }
    }
    echo '</ol>';
}

function listFolderFiles($dir){
	global $aas_cpath;
	
    $ffs = scandir($dir);

    echo '<ol>';
    foreach($ffs as $ff){
        if($ff != '.' && $ff != '..' && is_dir($dir.'/'.$ff)){
        	$aas_cpath=$dir.'/'.$ff;
            if(!tep_is_writable($dir.'/'.$ff)) echo '<li><label style="color:lightGray"><input disabled="disabled" type="radio" name="image_folder" value="'.$aas_cpath.'">'.substr($aas_cpath, strlen(DIR_FS_CATALOG)).'&nbsp;<span class="no-writable">'.AAS_DIALOG_TEXT_NO_WRITABLE.'</span></label>';
            else{
            	echo '<li><label><input type="radio" name="image_folder" value="'.$aas_cpath.'">'.substr($aas_cpath, strlen(DIR_FS_CATALOG)).'</label>';
            	
            }
            
            listFolderFiles($dir.'/'.$ff);
            echo '</li>';
        }
    }
    echo '</ol>';
}

function listFolders($dir,$name='image_folder',$class='',$echo=true,$defaultPath='.'){
	global $aas_cpath;
	if(!$echo) global $html;
	
    $ffs = scandir($dir);

		if($echo){

    echo '<ol>';
    foreach($ffs as $ff){
        if($ff != '.' && $ff != '..' && is_dir($dir.'/'.$ff)){
        	$aas_cpath=$dir.'/'.$ff;
        	$aas_cpath_sub_img=substr($aas_cpath, strlen(DIR_FS_CATALOG_IMAGES));
        	$aas_cpath_sub_cat=substr($aas_cpath, strlen(DIR_FS_CATALOG));
            if(!tep_is_writable($dir.'/'.$ff)) echo '<li><label style="color:lightGray"><input disabled="disabled" type="radio" name="'.$name.'" value="'.$aas_cpath_sub_img.'">'.$aas_cpath_sub_cat.'&nbsp;<span class="no-writable">'.AAS_DIALOG_TEXT_NO_WRITABLE.'</span></label>';
            else echo '<li><label><input class="'.$class.'" '.($defaultPath==$aas_cpath_sub_img?'checked="checked"':'').' type="radio" name="'.$name.'" value="'.$aas_cpath_sub_img.'">'.$aas_cpath_sub_cat.'</label>';
            
            listFolders($dir.'/'.$ff,$name,$class,$echo,$defaultPath);
            echo '</li>';
        }
    }
    echo '</ol>';
    
    }else{
    
    $html.='<ol>';
    foreach($ffs as $ff){
        if($ff != '.' && $ff != '..' && is_dir($dir.'/'.$ff)){
        	$aas_cpath=$dir.'/'.$ff;
        	$aas_cpath_sub_img=substr($aas_cpath, strlen(DIR_FS_CATALOG_IMAGES));
        	$aas_cpath_sub_cat=substr($aas_cpath, strlen(DIR_FS_CATALOG));
            if(!tep_is_writable($dir.'/'.$ff)) $html.='<li><label style="color:lightGray"><input disabled="disabled" type="radio" name="'.$name.'" value="'.$aas_cpath_sub_img.'">'.$aas_cpath_sub_cat.'&nbsp;<span class="no-writable">'.AAS_DIALOG_TEXT_NO_WRITABLE.'</span></label>';
            else $html.='<li><label><input class="'.$class.'" '.($defaultPath==$aas_cpath_sub_img?'checked="checked"':'').' type="radio" name="'.$name.'" value="'.$aas_cpath_sub_img.'">'.$aas_cpath_sub_cat.'</label>';
            
            listFolders($dir.'/'.$ff,$name,$class,$echo,$defaultPath);
            $html.= '</li>';
        }
    }
    $html.= '</ol>';
    
    return $html;
    
    
    }
}

function recursiveRemoveDirectory($directory){
  foreach(glob("{$directory}/*") as $file)
  {
      if(is_dir($file)) { 
          recursiveRemoveDirectory($file);
      } else {
          unlink($file);
      }
  }
  rmdir($directory);
}

function atleastOneFolderWritable($dir){
	global $writable_folders_found;
	
    $ffs = scandir($dir);

    foreach($ffs as $ff){
        if($ff != '.' && $ff != '..' && is_dir($dir.'/'.$ff)){
        
            if(tep_is_writable($dir.'/'.$ff)){
            
            	$writable_folders_found=true;
            
            	break;
            }
            
            atleastOneFolderWritable($dir.'/'.$ff);

        }
    }
    
    return $writable_folders_found;

}

function tep_formatDate($val, $format='d-m-Y h:i:s'){
	return date($format, $val);
}

function tep_dump($value){

	echo '<pre>';
	var_dump($value);
	echo '</pre>';
	
}

function tep_formatSeconds($secs,$display=array('year'=>1,'month'=>1,'week'=>1,'day'=>1,'hour'=>1,'min'=>1,'sec'=>1)) {

    if (!$secs = (int)$secs) return '0 secs';

    $units = array('year' => 31536000,'month' => 2628000,'week' => 604800,'day' => 86400,'hour' => 3600,'min' => 60,'sec' => 1);
    $strs = array();

    foreach($units as $name=>$int){
    
	    if(!isset($display[$name]) || $display[$name]==0 )  continue;
    		    
		if($secs < $int)
	    	continue;
		$num = (int) ($secs / $int);
		$secs = $secs % $int;

		$strs[$name] = "$num $name".(($num == 1) ? '' : 's');

    }

     $strs=array_slice($strs,0,3);
     
    return implode(', ', $strs);
}

function function_disabled($function_name) {
  $disabled = explode(',', ini_get('disable_functions'));
  return in_array($function_name, $disabled);
}

function generateToken($length = 24) {

  if(function_exists('openssl_random_pseudo_bytes')) {
      $token = base64_encode(openssl_random_pseudo_bytes($length, $strong));
      if($strong == TRUE) return strtr(substr($token, 0, $length), '+/=', '-_,'); //base64 is about 33% longer, so we need to truncate the result
  }

  //fallback to mt_rand if php < 5.3 or no openssl available
  $characters = '0123456789';
  $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; 
  $charactersLength = strlen($characters)-1;
  $token = '';

  //select some random characters
  for ($i = 0; $i < $length; $i++) {
      $token .= $characters[mt_rand(0, $charactersLength)];
  }        

  return $token;

}

?>
